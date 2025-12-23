<?php

namespace App\Http\Controllers\Owner\Report;

use App\Http\Controllers\Controller;
use App\Models\Store\Stock;
use App\Models\Store\StockMovementItem;
use App\Models\User;
use App\Services\StockMovementExcelService;
use App\Services\UnitConversionService;
use App\Services\StockReportExcelService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockReportController extends Controller
{
    protected $unitConversionService;
    protected $exportService;
    protected $movementExportService; // TAMBAHAN BARU

    public function __construct(
        UnitConversionService $unitConversionService,
        StockReportExcelService $exportService,
        StockMovementExcelService $movementExportService // TAMBAHAN BARU
    ) {
        $this->unitConversionService = $unitConversionService;
        $this->exportService = $exportService;
        $this->movementExportService = $movementExportService; // TAMBAHAN BARU
    }

    public function index(Request $request)
    {
        $ownerId = auth()->user()->id;

        if (!$request->filled('partner_id')) {
            $request->merge(['partner_id' => 'owner']);
        }

        // Jika filter 'month' kosong, isi dengan bulan dan tahun saat ini (YYYY-MM)
        if (!$request->filled('month')) {
            $request->merge(['month' => Carbon::now()->format('Y-m')]);
        }

        $startDate = null;
        $endDate = null;

        if ($request->filled('month')) {
            $filterMonthYear = $request->input('month');
            list($filterYear, $filterMonth) = explode('-', $filterMonthYear);

            $startDate = Carbon::createSafe((int)$filterYear, (int)$filterMonth)->startOfMonth()->startOfDay();
            $endDate = Carbon::createSafe((int)$filterYear, (int)$filterMonth)->endOfMonth()->endOfDay();
        }

        // Ambil data stocks dengan filter dan pagination
        $stocks = $this->getFilteredStocks($request, $ownerId);

        // Ambil data pergerakan stok (IN/OUT) dengan rentang tanggal baru
        $movementsByStock = $this->getStockMovements($stocks, $ownerId, $startDate, $endDate);

        // Proses konversi unit dan hitung ketersediaan fisik
        $this->processStockData($stocks, $movementsByStock);

        // Hitung statistik ringkasan
        $statistics = $this->calculateStatistics($stocks);

        // Ambil data pendukung untuk view
        $partners = $this->getPartners($ownerId);
        $monthOptions = $this->generateMonthOptions();

        return view('pages.owner.reports.stock', array_merge(
            compact('stocks', 'partners', 'monthOptions'),
            $statistics
        ));
    }

    /**
     * Ambil data stocks dengan filter yang diterapkan dan pagination
     */
    private function getFilteredStocks(Request $request, int $ownerId)
    {
        $query = Stock::with([
            'displayUnit',
            'partner',
            'owner',
            'partnerProduct',
            'partnerProductOption'
        ])->where('owner_id', $ownerId);

        // Filter berdasarkan stock type
        if ($request->filled('stock_type')) {
            $query->where('stock_type', $request->stock_type);
        }

        // Filter berdasarkan partner/outlet
        if ($request->filled('partner_id')) {
            $partnerIdFilter = $request->partner_id;

            if ($partnerIdFilter === 'owner') {
                $query->whereNull('partner_id');
            } else {
                $query->where('partner_id', $partnerIdFilter);
            }
        }

        // Tambahkan pagination dengan 20 items per halaman
        return $query->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Ambil dan agregasi data pergerakan stok (IN/OUT)
     */
    private function getStockMovements($stocks, int $ownerId, $startDate, $endDate)
    {
        // Handle both Collection and Paginator
        $stockIds = ($stocks instanceof \Illuminate\Pagination\LengthAwarePaginator)
            ? $stocks->pluck('id')->toArray()
            : $stocks->pluck('id')->toArray();

        // Query pergerakan stok untuk stock IDs yang sudah difilter
        $movementsQuery = DB::table('stock_movement_items AS smi')
            ->select(
                'smi.stock_id',
                'sm.type',
                DB::raw('SUM(smi.quantity) as total_quantity')
            )
            ->join('stock_movements AS sm', 'smi.stock_movement_id', '=', 'sm.id')
            ->where('sm.owner_id', $ownerId)
            ->whereIn('smi.stock_id', $stockIds);

        // Filter bulan
        if ($startDate && $endDate) {
            $movementsQuery->whereBetween('sm.created_at', [
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s')
            ]);
        }

        $movementsData = $movementsQuery->groupBy('smi.stock_id', 'sm.type')->get();

        // Map hasil agregasi berdasarkan stock ID
        return $movementsData->groupBy('stock_id')->map(function ($items) {
            return [
                'in' => $items->where('type', 'in')->sum('total_quantity'),
                'out' => $items->where('type', 'out')->sum('total_quantity'),
            ];
        });
    }

    /**
     * Hitung statistik ringkasan untuk dashboard
     */
    private function calculateStatistics($stocks)
    {
        // Gunakan getCollection() untuk mengakses semua items dari LengthAwarePaginator
        $allStocks = $stocks->getCollection();

        $totalStockItems = $stocks->total();

        $totalStockValue = $allStocks->sum(function ($stock) {
            return $stock->available_physical * $stock->last_price_per_unit;
        });

        $outOfStockItems = $allStocks->filter(function ($stock) {
            return $stock->available_physical <= 0;
        })->count();

        $totalQuantity = $allStocks->sum('quantity');
        $totalReserved = $allStocks->sum('quantity_reserved');
        $totalAvailable = $totalQuantity - $totalReserved;

        return compact(
            'totalStockItems',
            'totalStockValue',
            'outOfStockItems',
            'totalAvailable'
        );
    }

    /**
     * Proses konversi unit dan hitung ketersediaan fisik untuk setiap stock
     */
    private function processStockData($stocks, $movementsByStock)
    {
        // Handle both Collection and Paginator
        $items = ($stocks instanceof \Illuminate\Pagination\LengthAwarePaginator)
            ? $stocks->getCollection()
            : $stocks;

        $items->each(function ($stock) use ($movementsByStock) {
            $movement = $movementsByStock->get($stock->id);

            // Ambil total IN dan OUT dalam base unit
            $totalInBase = $movement['in'] ?? 0;
            $totalOutBase = $movement['out'] ?? 0;

            // Hitung ketersediaan fisik (Available = Total - Reserved) dalam base unit
            $stock->available_physical = $stock->quantity - $stock->quantity_reserved;

            // Konversi ke display unit jika tersedia
            if ($stock->displayUnit) {
                $displayUnitId = $stock->display_unit_id;

                $stock->lifetime_in = $this->unitConversionService->convertToDisplayUnit(
                    $totalInBase,
                    $displayUnitId
                );

                $stock->lifetime_out = $this->unitConversionService->convertToDisplayUnit(
                    $totalOutBase,
                    $displayUnitId
                );
            } else {
                // Fallback jika display unit tidak ditemukan
                $stock->lifetime_in = $totalInBase;
                $stock->lifetime_out = $totalOutBase;
            }
        });
    }

    /**
     * Ambil daftar partners untuk filter dropdown
     */
    private function getPartners(int $ownerId)
    {
        return User::where('owner_id', $ownerId)
            ->where('role', 'partner')
            ->where('is_active', 1)
            ->select('id', 'name')
            ->get();
    }

    /**
     * Generate opsi bulan untuk filter (12 bulan terakhir)
     */
    private function generateMonthOptions()
    {
        $monthOptions = [];
        $date = Carbon::now();

        for ($i = 0; $i < 12; $i++) {
            $monthOptions[] = [
                'value' => $date->format('Y-m'),
                'label' => $date->translatedFormat('F Y'),
            ];
            $date->subMonth();
        }

        return $monthOptions;
    }

    public function showStockMovement(Request $request, Stock $stock)
    {
        $owner = Auth::user();

        if ($stock->owner_id !== $owner->id) {
            abort(403, 'Unauthorized access to stock history.');
        }

        // Query Detail Pergerakan
        $movementsQuery = StockMovementItem::where('stock_id', $stock->id)
            ->with([
                'movement.partner',
                'stock.displayUnit'
            ]);

        $partnerIdFilter = $request->input('partner_id');
        $filterMonthYear = $request->input('month');

        // Filter Berdasarkan Lokasi (Partner/Gudang Owner)
        if ($partnerIdFilter) {
            $movementsQuery->whereHas('movement', function ($q) use ($partnerIdFilter) {
                if ($partnerIdFilter === 'owner' || $partnerIdFilter === '0') {
                    $q->whereNull('partner_id');
                } else {
                    $q->where('partner_id', $partnerIdFilter);
                }
            });
        }

        // Filter Berdasarkan Bulan 
        if ($filterMonthYear) {
            list($filterYear, $filterMonth) = explode('-', $filterMonthYear);

            $startDate = Carbon::createSafe((int)$filterYear, (int)$filterMonth)->startOfMonth()->startOfDay();
            $endDate = Carbon::createSafe((int)$filterYear, (int)$filterMonth)->endOfMonth()->endOfDay();

            $movementsQuery->whereHas('movement', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        $movements = $movementsQuery
            ->orderByDesc('stock_movement_id')
            ->paginate(20)
            ->withQueryString();

        return view('pages.owner.reports.show-stock-movement', [
            'stockItem' => $stock,
            'movements' => $movements,
            'currentFilters' => $request->only(['partner_id', 'month', 'stock_type'])
        ]);
    }

    /**
     * Export stock report to Excel
     */
    public function export(Request $request)
    {
        $ownerId = auth()->user()->id;

        // Jika filter 'month' kosong, isi dengan bulan dan tahun saat ini (YYYY-MM)
        if (!$request->filled('month')) {
            $request->merge(['month' => Carbon::now()->format('Y-m')]);
        }

        $startDate = null;
        $endDate = null;

        if ($request->filled('month')) {
            $filterMonthYear = $request->input('month');
            list($filterYear, $filterMonth) = explode('-', $filterMonthYear);

            $startDate = Carbon::createSafe((int)$filterYear, (int)$filterMonth)->startOfMonth()->startOfDay();
            $endDate = Carbon::createSafe((int)$filterYear, (int)$filterMonth)->endOfMonth()->endOfDay();
        }

        // Ambil SEMUA data stocks tanpa pagination untuk export
        $stocks = $this->getFilteredStocksForExport($request, $ownerId);

        if ($stocks->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk di-export');
        }

        // Ambil data pergerakan stok (IN/OUT) - method sudah support Collection
        $movementsByStock = $this->getStockMovements($stocks, $ownerId, $startDate, $endDate);

        // Proses konversi unit - method sudah support Collection
        $this->processStockData($stocks, $movementsByStock);

        try {
            // Export menggunakan service
            $filePath = $this->exportService->export($stocks, [
                'stock_type' => $request->input('stock_type'),
                'partner_id' => $request->input('partner_id'),
                'month' => $request->input('month')
            ]);

            // Download file
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    /**
     * Get filtered stocks WITHOUT pagination for export
     */
    private function getFilteredStocksForExport(Request $request, int $ownerId)
    {
        $query = Stock::with([
            'displayUnit',
            'partner',
            'owner',
            'partnerProduct',
            'partnerProductOption'
        ])->where('owner_id', $ownerId);

        // Filter berdasarkan stock type
        if ($request->filled('stock_type')) {
            $query->where('stock_type', $request->stock_type);
        }

        // Filter berdasarkan partner/outlet
        if ($request->filled('partner_id')) {
            $query->where('partner_id', $request->partner_id);
        }

        return $query->orderBy('updated_at', 'desc')->get();
    }

    /**
     * Export stock movement to Excel
     */
    public function exportMovement(Request $request, Stock $stock)
    {
        $owner = Auth::user();

        if ($stock->owner_id !== $owner->id) {
            abort(403, 'Unauthorized access to stock history.');
        }

        // Query Detail Pergerakan (sama seperti showStockHistory tapi tanpa pagination)
        $movementsQuery = StockMovementItem::where('stock_id', $stock->id)
            ->with([
                'movement.partner',
                'stock.displayUnit'
            ]);

        $partnerIdFilter = $request->input('partner_id');
        $filterMonthYear = $request->input('month');

        // Filter Berdasarkan Lokasi (Partner/Gudang Owner)
        if ($partnerIdFilter) {
            $movementsQuery->whereHas('movement', function ($q) use ($partnerIdFilter) {
                if ($partnerIdFilter === 'owner' || $partnerIdFilter === '0') {
                    $q->whereNull('partner_id');
                } else {
                    $q->where('partner_id', $partnerIdFilter);
                }
            });
        }

        // Berdasarkan Bulan 
        if ($filterMonthYear) {
            list($filterYear, $filterMonth) = explode('-', $filterMonthYear);

            $startDate = Carbon::createSafe((int)$filterYear, (int)$filterMonth)->startOfMonth()->startOfDay();
            $endDate = Carbon::createSafe((int)$filterYear, (int)$filterMonth)->endOfMonth()->endOfDay();

            $movementsQuery->whereHas('movement', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        // Ambil SEMUA data tanpa pagination
        $movements = $movementsQuery
            ->orderByDesc('stock_movement_id')
            ->get();

        // Cek jika tidak ada data
        if ($movements->isEmpty()) {
            return back()->with('error', 'Tidak ada data history untuk di-export');
        }

        try {
            // Export menggunakan service
            $filePath = $this->movementExportService->export($movements, $stock, [
                'partner_id' => $request->input('partner_id'),
                'month' => $request->input('month')
            ]);

            // Download file
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }
}