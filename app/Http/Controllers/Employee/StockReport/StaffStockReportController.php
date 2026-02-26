<?php

namespace App\Http\Controllers\Employee\StockReport;

use App\Http\Controllers\Controller;
use App\Models\Store\Stock;
use App\Models\Store\StockMovementItem;
use App\Services\StockMovementExcelService;
use App\Services\UnitConversionService;
use App\Services\StockReportExcelService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffStockReportController extends Controller
{
    protected $unitConversionService;
    protected $exportService;
    protected $movementExportService;

    public function __construct(
        UnitConversionService $unitConversionService,
        StockReportExcelService $exportService,
        StockMovementExcelService $movementExportService
    ) {
        $this->unitConversionService = $unitConversionService;
        $this->exportService         = $exportService;
        $this->movementExportService = $movementExportService;
    }

    private function authEmployee()
    {
        return Auth::guard('employee')->user();
    }

    private function partnerScope(): int
    {
        return (int) $this->authEmployee()->partner_id;
    }

    private function empRole(): string
    {
        return strtolower($this->authEmployee()->role ?? 'manager');
    }

    // =========================================================
    // INDEX
    // =========================================================
    public function index(Request $request)
    {
        $partnerId = $this->partnerScope();

        if (!$request->filled('month')) {
            $request->merge(['month' => Carbon::now()->format('Y-m')]);
        }

        $startDate = null;
        $endDate   = null;

        if ($request->filled('month')) {
            [$filterYear, $filterMonth] = explode('-', $request->input('month'));
            $startDate = Carbon::createSafe((int) $filterYear, (int) $filterMonth)->startOfMonth()->startOfDay();
            $endDate   = Carbon::createSafe((int) $filterYear, (int) $filterMonth)->endOfMonth()->endOfDay();
        }

        $stocks = $this->getFilteredStocks($request, $partnerId);
        $movementsByStock = $this->getStockMovements($stocks, $partnerId, $startDate, $endDate);
        $this->processStockData($stocks, $movementsByStock);

        $monthOptions = $this->generateMonthOptions();

        return view('pages.employee.staff.reports.stock', compact(
            'stocks',
            'monthOptions'
        ));
    }

    // =========================================================
    // SHOW STOCK MOVEMENT
    // =========================================================
    public function showStockMovement(Request $request, Stock $stock)
    {
        // Pastikan stock milik partner yang login
        abort_if((int) $stock->partner_id !== $this->partnerScope(), 403);

        $movementsQuery = StockMovementItem::where('stock_id', $stock->id)
            ->with(['movement.partner', 'stock.displayUnit']);

        if ($request->filled('month')) {
            [$filterYear, $filterMonth] = explode('-', $request->input('month'));
            $startDate = Carbon::createSafe((int) $filterYear, (int) $filterMonth)->startOfMonth()->startOfDay();
            $endDate   = Carbon::createSafe((int) $filterYear, (int) $filterMonth)->endOfMonth()->endOfDay();

            $movementsQuery->whereHas('movement', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        $movements = $movementsQuery
            ->orderByDesc('stock_movement_id')
            ->paginate(20)
            ->withQueryString();

        return view('pages.employee.staff.reports.show-stock-movement', [
            'stockItem' => $stock,
            'movements' => $movements,
        ]);
    }

    // =========================================================
    // EXPORT
    // =========================================================
    public function export(Request $request)
    {
        $partnerId = $this->partnerScope();

        if (!$request->filled('month')) {
            $request->merge(['month' => Carbon::now()->format('Y-m')]);
        }

        $startDate = null;
        $endDate   = null;

        if ($request->filled('month')) {
            [$filterYear, $filterMonth] = explode('-', $request->input('month'));
            $startDate = Carbon::createSafe((int) $filterYear, (int) $filterMonth)->startOfMonth()->startOfDay();
            $endDate   = Carbon::createSafe((int) $filterYear, (int) $filterMonth)->endOfMonth()->endOfDay();
        }

        $stocks = $this->getFilteredStocksForExport($request, $partnerId);

        if ($stocks->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk di-export');
        }

        $movementsByStock = $this->getStockMovements($stocks, $partnerId, $startDate, $endDate);
        $this->processStockData($stocks, $movementsByStock);

        try {
            $filePath = $this->exportService->export($stocks, [
                'stock_type' => $request->input('stock_type'),
                'month'      => $request->input('month'),
            ]);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    // =========================================================
    // EXPORT MOVEMENT
    // =========================================================
    public function exportMovement(Request $request, Stock $stock)
    {
        abort_if((int) $stock->partner_id !== $this->partnerScope(), 403);

        $movementsQuery = StockMovementItem::where('stock_id', $stock->id)
            ->with(['movement.partner', 'stock.displayUnit']);

        if ($request->filled('month')) {
            [$filterYear, $filterMonth] = explode('-', $request->input('month'));
            $startDate = Carbon::createSafe((int) $filterYear, (int) $filterMonth)->startOfMonth()->startOfDay();
            $endDate   = Carbon::createSafe((int) $filterYear, (int) $filterMonth)->endOfMonth()->endOfDay();

            $movementsQuery->whereHas('movement', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        $movements = $movementsQuery->orderByDesc('stock_movement_id')->get();

        if ($movements->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk di-export');
        }

        try {
            $filePath = $this->movementExportService->export($movements, $stock, [
                'month' => $request->input('month'),
            ]);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================
    private function getFilteredStocks(Request $request, int $partnerId)
    {
        $query = Stock::with(['displayUnit', 'partner'])
            ->where('partner_id', $partnerId);

        if ($request->filled('stock_type')) {
            $query->where('stock_type', $request->stock_type);
        }

        return $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();
    }

    private function getFilteredStocksForExport(Request $request, int $partnerId)
    {
        $query = Stock::with(['displayUnit', 'partner'])
            ->where('partner_id', $partnerId);

        if ($request->filled('stock_type')) {
            $query->where('stock_type', $request->stock_type);
        }

        return $query->orderBy('updated_at', 'desc')->get();
    }

    private function getStockMovements($stocks, int $partnerId, $startDate, $endDate)
    {
        $stockIds = $stocks instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $stocks->pluck('id')->toArray()
            : $stocks->pluck('id')->toArray();

        $query = DB::table('stock_movement_items AS smi')
            ->select('smi.stock_id', 'sm.type', DB::raw('SUM(smi.quantity) as total_quantity'))
            ->join('stock_movements AS sm', 'smi.stock_movement_id', '=', 'sm.id')
            ->where('sm.partner_id', $partnerId)
            ->whereIn('smi.stock_id', $stockIds);

        if ($startDate && $endDate) {
            $query->whereBetween('sm.created_at', [
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s'),
            ]);
        }

        return $query->groupBy('smi.stock_id', 'sm.type')->get()
            ->groupBy('stock_id')
            ->map(fn($items) => [
                'in'  => $items->where('type', 'in')->sum('total_quantity'),
                'out' => $items->where('type', 'out')->sum('total_quantity'),
            ]);
    }

    private function processStockData($stocks, $movementsByStock)
    {
        $items = $stocks instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $stocks->getCollection()
            : $stocks;

        $items->each(function ($stock) use ($movementsByStock) {
            $movement = $movementsByStock->get($stock->id);

            $totalInBase  = $movement['in'] ?? 0;
            $totalOutBase = $movement['out'] ?? 0;

            $stock->available_physical = $stock->quantity - $stock->quantity_reserved;

            if ($stock->displayUnit) {
                $stock->lifetime_in  = $this->unitConversionService->convertToDisplayUnit($totalInBase, $stock->display_unit_id);
                $stock->lifetime_out = $this->unitConversionService->convertToDisplayUnit($totalOutBase, $stock->display_unit_id);
            } else {
                $stock->lifetime_in  = $totalInBase;
                $stock->lifetime_out = $totalOutBase;
            }
        });
    }

    private function generateMonthOptions()
    {
        $options = [];
        $date = Carbon::now();

        for ($i = 0; $i < 12; $i++) {
            $options[] = [
                'value' => $date->format('Y-m'),
                'label' => $date->translatedFormat('F Y'),
            ];
            $date->subMonth();
        }

        return $options;
    }
}