<?php

namespace App\Http\Controllers\Employee\Staff\Product;

use App\Http\Controllers\Controller;
use App\Models\Store\MasterUnit;
use App\Models\Store\StockMovement;
use App\Models\User;
use App\Services\UnitConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Store\Stock;
use App\Services\StockRecalculationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StaffStockMovementController extends Controller
{
    protected $unitConversionService;
    protected $recalculationService;

    public function __construct(UnitConversionService $unitConversionService, StockRecalculationService $recalculationService)
    {
        $this->unitConversionService = $unitConversionService;
        $this->recalculationService = $recalculationService;
    }

    private function getRoutePrefix(): string
    {
        return strtolower(Auth::guard('employee')->user()->role);
    }

    private function getContext()
    {
        $employee = Auth::guard('employee')->user();
        $partner = User::findOrFail($employee->partner_id);

        return (object)[
            'employee' => $employee,
            'partner_id' => $partner->id,
            'owner_id' => $partner->owner_id,
        ];
    }

    public function index(Request $request)
    {
        $context = $this->getContext();

        $query = StockMovement::where('owner_id', $context->owner_id)
            ->where('partner_id', $context->partner_id)
            ->withCount('items');

        $query->when($request->filled('filter_type'), fn($q) => $q->where('type', $request->input('filter_type')));

        if ($request->filled('filter_date')) {
            $date = Carbon::parse($request->input('filter_date'));
            $query->whereDate('created_at', $date);
        }

        $movements = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('pages.employee.staff.products.stock-movements.index', [
            'movements' => $movements,
        ]);
    }

    public function createStockIn()
    {
        $context = $this->getContext();

        // HANYA AMBIL STOK LINKED (Bahan Baku)
        $stocks = Stock::where('owner_id', $context->owner_id)
            ->where('partner_id', $context->partner_id)
            ->where('stock_type', 'linked')
            ->with('displayUnit')
            ->orderBy('stock_name')
            ->get();

        $allUnits = MasterUnit::where('owner_id', $context->owner_id)->orWhereNull('owner_id')->get();

        return view('pages.employee.staff.products.stock-movements.create-stock-in', [
            'stocks' => $stocks,
            'allUnits' => $allUnits,
            'context' => $context
        ]);
    }

    public function createAdjustment()
    {
        $context = $this->getContext();

        // HANYA AMBIL STOK LINKED (Bahan Baku)
        $stocks = Stock::where('owner_id', $context->owner_id)
            ->where('partner_id', $context->partner_id)
            ->where('stock_type', 'linked')
            ->with('displayUnit')
            ->orderBy('stock_name')
            ->get();

        $stocks->each(function ($stock) {
            $displayQty = 0;
            if ($stock->displayUnit) {
                $displayQty = $this->unitConversionService->convertToDisplayUnit(
                    $stock->quantity,
                    $stock->display_unit_id
                );
            }
            $stock->setAttribute('display_quantity', round($displayQty, 4));
        });

        $allUnits = MasterUnit::where('owner_id', $context->owner_id)->orWhereNull('owner_id')->get();

        $defaultCategories = [
            'damaged',
            'expired',
            'internal_use',
            'lost',
            'audit_adjustment'
        ];

        $customCategories = StockMovement::where('owner_id', $context->owner_id)
            ->whereNotIn('category', $defaultCategories)
            ->distinct()
            ->pluck('category');

        return view('pages.employee.staff.products.stock-movements.create-adjustment', [
            'stocks' => $stocks,
            'allUnits' => $allUnits,
            'customCategories' => $customCategories,
            'context' => $context
        ]);
    }

    public function createTransfer()
    {
        $context = $this->getContext();

        // HANYA AMBIL STOK LINKED (Bahan Baku)
        $stocks = Stock::where('owner_id', $context->owner_id)
            ->where('partner_id', $context->partner_id)
            ->where('stock_type', 'linked')
            ->with('displayUnit')
            ->orderBy('stock_name')
            ->get();

        $stocks->each(function ($stock) {
            $displayQty = 0;
            if ($stock->displayUnit) {
                $displayQty = $this->unitConversionService->convertToDisplayUnit(
                    $stock->quantity,
                    $stock->display_unit_id
                );
            }
            $stock->setAttribute('display_quantity', round($displayQty, 4));
        });

        $allUnits = MasterUnit::where('owner_id', $context->owner_id)->orWhereNull('owner_id')->get();

        // DATA PARTNER LAIN DIHAPUS KARENA TRANSFER HANYA BISA KE OWNER

        return view('pages.employee.staff.products.stock-movements.create-transfer', [
            'stocks' => $stocks,
            // 'partners' tidak lagi dikirim
            'allUnits' => $allUnits,
            'context' => $context
        ]);
    }

    public function store(Request $request)
    {
        $context = $this->getContext();
        $routePrefix = $this->getRoutePrefix();
        
        $type = $request->input('movement_type');
        $validatedData = [];

        // Validasi Rule exists diperketat agar hanya menerima stok linked milik outlet tersebut
        $stockExistsRule = Rule::exists('stocks', 'id')
            ->where('partner_id', $context->partner_id)
            ->where('stock_type', 'linked');

        if ($type === 'in') {
            $validatedData = $request->validate([
                'movement_type' => 'required|in:in',
                'category' => 'required|string|max:100',
                'notes' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.stock_id' => ['required', 'string', $stockExistsRule],
                'items.*.unit_id' => 'required|exists:master_units,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'nullable|numeric|min:0',
            ]);
        } elseif ($type === 'transfer') {
            $validatedData = $request->validate([
                'movement_type' => 'required|in:transfer',
                'location_to' => 'required|in:_owner', // KUNCI HANYA BISA KE OWNER
                'category' => 'required|string|max:100',
                'notes' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.stock_id' => ['required', 'string', $stockExistsRule],
                'items.*.unit_id' => 'required|exists:master_units,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
            ]);
        } elseif ($type === 'adjustment') {
            $validatedData = $request->validate([
                'movement_type' => 'required|in:adjustment',
                'category' => 'required|string|max:100',
                'notes' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.stock_id' => ['required', 'string', $stockExistsRule],
                'items.*.unit_id' => 'required|exists:master_units,id',
                'items.*.new_quantity' => 'required|numeric|min:0',
                'items.*.current_quantity' => 'required|numeric|min:0',
            ]);
        } else {
            return back()->with('error', 'Tipe transaksi tidak valid.')->withInput();
        }

        try {
            DB::beginTransaction();

            if ($type === 'in') {
                $this->processStockIn($request, $context, $validatedData['items']);
            } elseif ($type === 'transfer') {
                $this->processTransfer($request, $context, $validatedData['items']);
            } elseif ($type === 'adjustment') {
                $this->processAdjustment($request, $context, $validatedData['items']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = config('app.debug') ? $e->getMessage() : 'Gagal menyimpan transaksi. Terjadi kesalahan server.';
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route("employee.{$routePrefix}.stocks.movements.index")->with('success', 'Transaksi stok berhasil dicatat.');
    }

    private function processStockIn(Request $request, $context, $items)
    {
        $partnerId = $context->partner_id;

        $movement = StockMovement::create([
            'owner_id' => $context->owner_id,
            'partner_id' => $partnerId,
            'type' => 'in',
            'category' => $request->input('category'),
            'notes' => $request->input('notes'),
        ]);

        foreach ($items as $item) {
            $stock = Stock::where('owner_id', $context->owner_id)
                ->where('id', $item['stock_id'])
                ->where('partner_id', $partnerId)
                ->first();

            if (!$stock) throw new \Exception("Item tidak terdaftar di lokasi ini.");

            $inputQuantity = $item['quantity'];
            $inputUnitId = $item['unit_id'];
            $inputUnitPrice = $item['unit_price'] ?? 0;

            $quantityInBaseUnit = $this->unitConversionService->convertToBaseUnit($inputQuantity, $inputUnitId);

            $movement->items()->create([
                'stock_id' => $stock->id,
                'quantity' => $quantityInBaseUnit,
                'unit_price' => $inputUnitPrice,
            ]);

            $stock->increment('quantity', $quantityInBaseUnit);
            $this->recalculationService->recalculateLinkedProducts($stock);
        }
    }

    private function processTransfer(Request $request, $context, $items)
    {
        $locationFrom = $context->partner_id;
        $locationTo = null; // PAKSA TUJUAN MENJADI NULL (GUDANG OWNER)
        $date = now();

        $movementOut = StockMovement::create([
            'owner_id' => $context->owner_id,
            'partner_id' => $locationFrom,
            'type' => 'out',
            'category' => 'transfer_out',
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        $movementIn = StockMovement::create([
            'owner_id' => $context->owner_id,
            'partner_id' => $locationTo,
            'type' => 'in',
            'category' => 'transfer_in',
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        foreach ($items as $item) {
            $inputQuantity = $item['quantity'];
            $inputUnitId = $item['unit_id'];

            $quantityInBaseUnit = $this->unitConversionService->convertToBaseUnit($inputQuantity, $inputUnitId);

            $stockFrom = Stock::where('id', $item['stock_id'])
                ->where('owner_id', $context->owner_id)
                ->where('partner_id', $locationFrom)
                ->first();

            if (!$stockFrom) throw new \Exception("Item tidak terdaftar di lokasi asal.");

            if ($stockFrom->quantity < $quantityInBaseUnit) {
                $availableInDisplayUnit = $this->unitConversionService->convertToDisplayUnit(
                    $stockFrom->quantity,
                    $stockFrom->display_unit_id ?? 1
                );
                $displayUnit = $stockFrom->displayUnit ? $stockFrom->displayUnit->unit_name : 'unit';
                throw new \Exception("Stok '{$stockFrom->stock_name}' tidak mencukupi (Tersisa: {$availableInDisplayUnit} {$displayUnit}).");
            }

            // Catat KELUAR & Kurangi
            $movementOut->items()->create([
                'stock_id' => $stockFrom->id,
                'quantity' => $quantityInBaseUnit,
                'unit_price' => 0
            ]);
            $stockFrom->decrement('quantity', $quantityInBaseUnit);
            $this->recalculationService->recalculateLinkedProducts($stockFrom);

            // --- Validasi & Update Stok TUJUAN (Gudang Owner) ---
            $stockTo = Stock::where('owner_id', $context->owner_id)
                ->whereNull('partner_id')
                ->where('stock_name', $stockFrom->stock_name)
                ->whereNull('partner_product_id')
                ->whereNull('partner_product_option_id')
                ->first();

            if (!$stockTo) {
                $stockTo = Stock::create([
                    'owner_id'      => $context->owner_id,
                    'partner_id'    => null, // Milik Owner
                    'stock_name'    => $stockFrom->stock_name,
                    'stock_code'    => 'STK-' . Carbon::now()->format('ymd') . '-' . strtoupper(Str::random(6)),
                    'type'          => 'master',
                    'stock_type'    => $stockFrom->stock_type,
                    'partner_product_id' => null,
                    'partner_product_option_id' => null,
                    'display_unit_id' => $stockFrom->display_unit_id,
                    'last_price_per_unit' => $stockFrom->last_price_per_unit,
                    'quantity'      => 0,
                ]);
            }

            // Catat MASUK & Tambah
            $movementIn->items()->create([
                'stock_id' => $stockTo->id,
                'quantity' => $quantityInBaseUnit,
                'unit_price' => $stockFrom->last_price_per_unit
            ]);
            $stockTo->increment('quantity', $quantityInBaseUnit);
            $this->recalculationService->recalculateLinkedProducts($stockTo);
        }
    }

    private function processAdjustment(Request $request, $context, $items)
    {
        $location = $context->partner_id;
        $category = $request->input('category');
        $notes = $request->input('notes');

        $itemsIn = [];
        $itemsOut = [];

        foreach ($items as $item) {
            $inputNewQuantity = $item['new_quantity'];
            $inputCurrentQuantity = $item['current_quantity'];
            $inputUnitId = $item['unit_id'];

            $newQuantityInBaseUnit = $this->unitConversionService->convertToBaseUnit($inputNewQuantity, $inputUnitId);
            $currentQuantityInBaseUnit = $this->unitConversionService->convertToBaseUnit($inputCurrentQuantity, $inputUnitId);

            $difference = $newQuantityInBaseUnit - $currentQuantityInBaseUnit;

            if ($difference == 0) continue;

            $stock = Stock::where('id', $item['stock_id'])
                ->where('owner_id', $context->owner_id)
                ->where('partner_id', $location)
                ->first();

            if (!$stock) throw new \Exception("Item tidak terdaftar di lokasi ini.");

            if ($difference > 0) {
                $itemsIn[] = ['stock' => $stock, 'quantity' => abs($difference)];
            } else {
                if ($stock->quantity < abs($difference)) {
                    $available = $this->unitConversionService->convertToDisplayUnit($stock->quantity, $stock->display_unit_id ?? 1);
                    $unit = $stock->displayUnit ? $stock->displayUnit->unit_name : 'unit';
                    throw new \Exception("Stok '{$stock->stock_name}' tidak mencukupi. Tersisa: {$available} {$unit}.");
                }
                $itemsOut[] = ['stock' => $stock, 'quantity' => abs($difference)];
            }
        }

        if (!empty($itemsIn)) {
            $movementIn = StockMovement::create([
                'owner_id' => $context->owner_id,
                'partner_id' => $location,
                'type' => 'in',
                'category' => $category,
                'notes' => $notes,
            ]);

            foreach ($itemsIn as $itemData) {
                $movementIn->items()->create([
                    'stock_id' => $itemData['stock']->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['stock']->last_price_per_unit
                ]);
                $itemData['stock']->increment('quantity', $itemData['quantity']);
                $this->recalculationService->recalculateLinkedProducts($itemData['stock']);
            }
        }

        if (!empty($itemsOut)) {
            $movementOut = StockMovement::create([
                'owner_id' => $context->owner_id,
                'partner_id' => $location,
                'type' => 'out',
                'category' => $category,
                'notes' => $notes,
            ]);

            foreach ($itemsOut as $itemData) {
                $movementOut->items()->create([
                    'stock_id' => $itemData['stock']->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['stock']->last_price_per_unit
                ]);
                $itemData['stock']->decrement('quantity', $itemData['quantity']);
                $this->recalculationService->recalculateLinkedProducts($itemData['stock']);
            }
        }
    }

    public function getMovementItemsJson(Request $request, $id)
    {
        $context = $this->getContext();

        $movement = StockMovement::where('owner_id', $context->owner_id)
            ->where('partner_id', $context->partner_id)
            ->with('partner')
            ->findOrFail($id);

        $items = $movement->items()->with('stock.displayUnit')->get();

        $formattedItems = $items->map(function ($item) {
            $displayQty = $item->quantity;
            $unitName = 'N/A';
            $stockName = 'Item Tidak Ditemukan';

            if ($item->stock && $item->stock->displayUnit) {
                $stockName = $item->stock->stock_name;
                $unitName = $item->stock->displayUnit->unit_name;
                $displayQty = $this->unitConversionService->convertToDisplayUnit(
                    $item->quantity,
                    $item->stock->display_unit_id
                );
            }

            $unitPriceFormatted = $item->unit_price !== null
                ? 'Rp ' . number_format($item->unit_price, 2, ',', '.') . ''
                : '-';

            return [
                'stock_name' => $stockName,
                'display_quantity' => number_format($displayQty, 2, ',', '.'),
                'display_unit_name' => $unitName,
                'quantity_raw' => $item->quantity,
                'unit_price_formatted' => $unitPriceFormatted,
            ];
        });

        return response()->json([
            'movement' => $movement,
            'items' => $formattedItems,
        ]);
    }
}