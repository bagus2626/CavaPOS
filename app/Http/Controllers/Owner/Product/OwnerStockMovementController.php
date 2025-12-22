<?php

namespace App\Http\Controllers\Owner\Product;

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

class OwnerStockMovementController extends Controller
{
    protected $unitConversionService;
    protected $recalculationService;

    public function __construct(UnitConversionService $unitConversionService, StockRecalculationService $recalculationService) // TAMBAHAN: Injection
    {
        $this->unitConversionService = $unitConversionService;
        $this->recalculationService = $recalculationService;
    }

    public function index(Request $request)
    {
        $owner = Auth::user();
        $partners = User::where('owner_id', $owner->id)->where('role', 'partner')->orderBy('name')->get();

        $query = StockMovement::where('owner_id', $owner->id)->with('partner')->withCount('items');

        // Filter tipe
        $query->when($request->filled('filter_type'), fn($q) => $q->where('type', $request->input('filter_type')));

        // Filter lokasi
        $location = $request->input('filter_location', 'owner');
        if ($location === 'owner') {
            $query->whereNull('partner_id');
        } else {
            $query->where('partner_id', $location);
        }

        // Filter tanggal
        if ($request->filled('filter_date')) {
            $date = Carbon::parse($request->input('filter_date'));
            $query->whereDate('created_at', $date);
        }

        $movements = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('pages.owner.products.stock-movements.index', [
            'movements' => $movements,
            'partners' => $partners,
        ]);
    }

    public function createStockIn()
    {
        $owner = Auth::user();

        $stocks = Stock::where('owner_id', $owner->id)
            ->with('partner', 'displayUnit')
            ->orderBy('stock_name')
            ->get();

        $partners = User::where('owner_id', $owner->id)
            ->where('role', 'partner')
            ->orderBy('name')
            ->get();

        $allUnits = MasterUnit::all();

        return view('pages.owner.products.stock-movements.create-stock-in', [
            'stocks' => $stocks,
            'partners' => $partners,
            'allUnits' => $allUnits,
        ]);
    }


   public function createAdjustment()
    {
        $owner = Auth::user();

        $stocks = Stock::where('owner_id', $owner->id)
            ->with('partner', 'displayUnit')
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

        $partners = User::where('owner_id', $owner->id)
            ->where('role', 'partner')
            ->orderBy('name')
            ->get();

        $allUnits = MasterUnit::all();

        // Daftar kategori default yang sudah ada di hardcode view
        $defaultCategories = [
            'damaged', 
            'expired', 
            'internal_use', 
            'lost', 
            'audit_adjustment'
        ];

        // Ambil kategori kustom yang pernah disimpan sebelumnya
        $customCategories = StockMovement::where('owner_id', $owner->id)
            ->whereNotIn('category', $defaultCategories)
            ->distinct()
            ->pluck('category');
        // -----------------------------

        return view('pages.owner.products.stock-movements.create-adjustment', [
            'stocks' => $stocks,
            'partners' => $partners,
            'allUnits' => $allUnits,
            'customCategories' => $customCategories, // <-- Jangan lupa dikirim ke view
        ]);
    }

    public function createTransfer()
    {
        $owner = Auth::user();

        $stocks = Stock::where('owner_id', $owner->id)
            ->with('partner', 'displayUnit')
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
            $stock->setAttribute('display_quantity', round($displayQty, 4)); // Membulatkan untuk tampilan
        });

        $partners = User::where('owner_id', $owner->id)
            ->where('role', 'partner')
            ->orderBy('name')
            ->get();

        $allUnits = MasterUnit::all();

        return view('pages.owner.products.stock-movements.create-transfer', [
            'stocks' => $stocks,
            'partners' => $partners,
            'allUnits' => $allUnits,
        ]);
    }

    public function store(Request $request)
    {
        $owner = Auth::user();
        $type = $request->input('movement_type');
        $validatedData = [];

        if ($type === 'in') {
            $validatedData = $request->validate([
                'movement_type' => 'required|in:in',
                'location_to' => 'required|string',
                'category' => 'required|string|max:100',
                'notes' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.stock_id' => ['required', 'string', Rule::exists('stocks', 'id')->where('owner_id', $owner->id)],
                'items.*.unit_id' => 'required|exists:master_units,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'nullable|numeric|min:0',
            ]);
        } elseif ($type === 'transfer') {
            $validatedData = $request->validate([
                'movement_type' => 'required|in:transfer',
                'location_from' => 'required|string',
                'location_to' => 'required|string|different:location_from',
                'category' => 'required|string|max:100',
                'notes' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.stock_id' => ['required', 'string', Rule::exists('stocks', 'id')->where('owner_id', $owner->id)],
                'items.*.unit_id' => 'required|exists:master_units,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
            ]);
        } elseif ($type === 'adjustment') {
            $validatedData = $request->validate([
                'movement_type' => 'required|in:adjustment',
                'location' => 'required|string',
                'category' => 'required|string|max:100',
                'notes' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.stock_id' => ['required', 'string', Rule::exists('stocks', 'id')->where('owner_id', $owner->id)],
                'items.*.unit_id' => 'required|exists:master_units,id',
                'items.*.new_quantity' => 'required|numeric|min:0',
                'items.*.current_quantity' => 'required|numeric|min:0',
            ]);
        } elseif ($type === 'out') {
            $validatedData = $request->validate([
                'movement_type' => 'required|in:out',
                'location_from' => 'required|string',
                'category' => 'required|string|max:100|not_in:transfer_out,transfer_in',
                'notes' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.stock_id' => ['required', 'string', Rule::exists('stocks', 'id')->where('owner_id', $owner->id)],
                'items.*.unit_id' => 'required|exists:master_units,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
            ]);
        } else {
            return back()->with('error', 'Tipe transaksi tidak valid.')->withInput();
        }

        try {
            DB::beginTransaction();

            if ($type === 'in') {
                $this->processStockIn($request, $owner, $validatedData['items']);
            } elseif ($type === 'transfer') {
                $this->processTransfer($request, $owner, $validatedData['items']);
            } elseif ($type === 'adjustment') {
                $this->processAdjustment($request, $owner, $validatedData['items']);
            } elseif ($type === 'out') {
                $this->processStockOut($request, $owner, $validatedData['items']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = config('app.debug') ? $e->getMessage() : 'Gagal menyimpan transaksi. Terjadi kesalahan server.';
            return back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('owner.user-owner.stocks.index')->with('success', 'Transaksi stok berhasil dicatat.');
    }


    private function processStockIn(Request $request, $owner, $items)
    {
        $locationTo = $request->input('location_to');
        $partnerId = ($locationTo == '_owner') ? null : $locationTo;

        $movement = StockMovement::create([
            'owner_id' => $owner->id,
            'partner_id' => $partnerId,
            'type' => 'in',
            'category' => $request->input('category'),
            'notes' => $request->input('notes'),
        ]);

        foreach ($items as $item) {

            $stock = Stock::where('owner_id', $owner->id)
                ->where('id', $item['stock_id'])
                ->where('partner_id', $partnerId)
                ->first();

            if (!$stock) {
                $failedStock = Stock::find($item['stock_id']);
                $failedStockName = $failedStock ? $failedStock->stock_name : "ID {$item['stock_id']}";
                $locationName = $partnerId == null ? "Gudang Owner" : User::find($partnerId)->name;
                throw new \Exception("Item '{$failedStockName}' tidak terdaftar di lokasi tujuan '{$locationName}'.");
            }

            // --- KONVERSI UNIT MENGGUNAKAN SERVICE ---
            $inputQuantity = $item['quantity'];
            $inputUnitId = $item['unit_id'];
            $inputUnitPrice = $item['unit_price'] ?? 0;

            // Konversi kuantitas ke unit dasar menggunakan service
            $quantityInBaseUnit = $this->unitConversionService->convertToBaseUnit(
                $inputQuantity,
                $inputUnitId
            );

            // Simpan data yang sudah terkonversi
            $movement->items()->create([
                'stock_id' => $stock->id,
                'quantity' => $quantityInBaseUnit,
                'unit_price' => $inputUnitPrice,
            ]);

            // Update stok dengan kuantitas unit dasar
            $stock->increment('quantity', $quantityInBaseUnit);

            $this->recalculationService->recalculateLinkedProducts($stock);
        }
    }

    private function processTransfer(Request $request, $owner, $items)
    {
        $locationFrom = $request->input('location_from') == '_owner' ? null : $request->input('location_from');
        $locationTo = $request->input('location_to') == '_owner' ? null : $request->input('location_to');
        $date = now();

        // 1. Buat Gerakan KELUAR (OUT) dari Lokasi Asal
        $movementOut = StockMovement::create([
            'owner_id' => $owner->id,
            'partner_id' => $locationFrom,
            'type' => 'out',
            'category' => 'transfer_out',
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        // 2. Buat Gerakan MASUK (IN) ke Lokasi Tujuan
        $movementIn = StockMovement::create([
            'owner_id' => $owner->id,
            'partner_id' => $locationTo,
            'type' => 'in',
            'category' => 'transfer_in',
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        foreach ($items as $item) {
            // --- KONVERSI UNIT MENGGUNAKAN SERVICE ---
            $inputQuantity = $item['quantity'];
            $inputUnitId = $item['unit_id'];

            // Konversi ke unit dasar
            $quantityInBaseUnit = $this->unitConversionService->convertToBaseUnit(
                $inputQuantity,
                $inputUnitId
            );

            // --- Validasi & Update Stok ASAL (Stock Out) ---
            $stockFrom = Stock::where('id', $item['stock_id'])
                ->where('owner_id', $owner->id)
                ->where('partner_id', $locationFrom)
                ->first();

            if (!$stockFrom) {
                $failedStockName = Stock::find($item['stock_id'])->stock_name ?? "ID {$item['stock_id']}";
                $locationName = $locationFrom == null ? "Gudang Owner" : User::find($locationFrom)->name;
                throw new \Exception("Item '{$failedStockName}' tidak terdaftar di lokasi asal '{$locationName}'.");
            }

            // Cek ketersediaan stok
            if ($stockFrom->quantity < $quantityInBaseUnit) {
                // Konversi untuk ditampilkan dalam pesan error
                $availableInDisplayUnit = $this->unitConversionService->convertToDisplayUnit(
                    $stockFrom->quantity,
                    $stockFrom->display_unit_id ?? 1
                );
                $displayUnit = $stockFrom->displayUnit ? $stockFrom->displayUnit->unit_name : 'unit';

                throw new \Exception("Stok '{$stockFrom->stock_name}' di lokasi asal tidak mencukupi (Hanya tersisa: {$availableInDisplayUnit} {$displayUnit}).");
            }

            // Catat item KELUAR
            $movementOut->items()->create([
                'stock_id' => $stockFrom->id,
                'quantity' => $quantityInBaseUnit,
                'unit_price' => 0
            ]);
            // Kurangi stok ASAL
            $stockFrom->decrement('quantity', $quantityInBaseUnit);

            $this->recalculationService->recalculateLinkedProducts($stockFrom);


            // --- Validasi & Update Stok TUJUAN (Stock In) ---
            // Cari item stok yang setara di lokasi tujuan
            $stockTo = Stock::where('owner_id', $owner->id)
                ->where('partner_id', $locationTo)
                ->where(function ($q) use ($stockFrom) {
                    if ($stockFrom->partner_product_option_id) {
                        // Jika item adalah OPSI produk, cocokkan ID opsinya
                        $q->where('partner_product_option_id', $stockFrom->partner_product_option_id);
                    } elseif ($stockFrom->partner_product_id) {
                        // Jika item adalah PRODUK utama, cocokkan ID produknya
                        $q->where('partner_product_id', $stockFrom->partner_product_id);
                    } else {
                        // Jika item adalah BAHAN BAKU (master), cocokkan nama & pastikan bukan produk/opsi
                        $q->where('stock_name', $stockFrom->stock_name)
                            ->whereNull('partner_product_id')
                            ->whereNull('partner_product_option_id');
                    }
                })->first();

            if (!$stockTo) {
                $newStockCode = $this->generateUniqueStockCode();

                $stockTo = Stock::create([
                    'owner_id'      => $owner->id,
                    'partner_id'    => $locationTo,
                    'stock_name'    => $stockFrom->stock_name,
                    'stock_code'    => $newStockCode,
                    'type'          => $stockFrom->type,
                    'stock_type'    => $stockFrom->stock_type,
                    'partner_product_id' => $stockFrom->partner_product_id,
                    'partner_product_option_id' => $stockFrom->partner_product_option_id,
                    'display_unit_id' => $stockFrom->display_unit_id,
                    'last_price_per_unit' => $stockFrom->last_price_per_unit,
                    'quantity'      => 0,
                ]);
            }

            // Catat item MASUK
            $movementIn->items()->create([
                'stock_id' => $stockTo->id,
                'quantity' => $quantityInBaseUnit,
                'unit_price' => $stockFrom->last_price_per_unit
            ]);
            // Tambah stok TUJUAN
            $stockTo->increment('quantity', $quantityInBaseUnit);

            $this->recalculationService->recalculateLinkedProducts($stockTo);
        }
    }

    private function processAdjustment(Request $request, $owner, $items)
    {
        $location = $request->input('location') == '_owner' ? null : $request->input('location');
        $category = $request->input('category');
        $notes = $request->input('notes');

        // Grouping items berdasarkan tipe movement (in atau out)
        $itemsIn = [];
        $itemsOut = [];

        foreach ($items as $item) {
            $inputNewQuantity = $item['new_quantity'];
            $inputCurrentQuantity = $item['current_quantity'];
            $inputUnitId = $item['unit_id'];

            // Konversi ke unit dasar
            $newQuantityInBaseUnit = $this->unitConversionService->convertToBaseUnit(
                $inputNewQuantity,
                $inputUnitId
            );

            $currentQuantityInBaseUnit = $this->unitConversionService->convertToBaseUnit(
                $inputCurrentQuantity,
                $inputUnitId
            );

            // Hitung selisih
            $difference = $newQuantityInBaseUnit - $currentQuantityInBaseUnit;

            // Skip jika tidak ada perubahan
            if ($difference == 0) {
                continue;
            }

            // Validasi stok
            $stock = Stock::where('id', $item['stock_id'])
                ->where('owner_id', $owner->id)
                ->where('partner_id', $location)
                ->first();

            if (!$stock) {
                $failedStockName = Stock::find($item['stock_id'])->stock_name ?? "ID {$item['stock_id']}";
                $locationName = $location == null ? "Gudang Owner" : User::find($location)->name;
                throw new \Exception("Item '{$failedStockName}' tidak terdaftar di lokasi '{$locationName}'.");
            }

            // Tentukan tipe movement dan grouping
            if ($difference > 0) {
                // PENAMBAHAN STOK (IN)
                $itemsIn[] = [
                    'stock' => $stock,
                    'quantity' => abs($difference)
                ];
            } else {
                // PENGURANGAN STOK (OUT)
                // Cek ketersediaan stok
                if ($stock->quantity < abs($difference)) {
                    $availableInDisplayUnit = $this->unitConversionService->convertToDisplayUnit(
                        $stock->quantity,
                        $stock->display_unit_id ?? 1
                    );
                    $displayUnit = $stock->displayUnit ? $stock->displayUnit->unit_name : 'unit';

                    throw new \Exception("Stok '{$stock->stock_name}' tidak mencukupi untuk adjustment (Hanya tersisa: {$availableInDisplayUnit} {$displayUnit}).");
                }

                $itemsOut[] = [
                    'stock' => $stock,
                    'quantity' => abs($difference)
                ];
            }
        }

        // Buat 1 movement IN jika ada penambahan stok
        if (!empty($itemsIn)) {
            $movementIn = StockMovement::create([
                'owner_id' => $owner->id,
                'partner_id' => $location,
                'type' => 'in',
                'category' => $category,
                'notes' => $notes,
            ]);

            foreach ($itemsIn as $itemData) {
                $stock = $itemData['stock'];
                $quantity = $itemData['quantity'];

                $movementIn->items()->create([
                    'stock_id' => $stock->id,
                    'quantity' => $quantity,
                    'unit_price' => $stock->last_price_per_unit
                ]);

                $stock->increment('quantity', $quantity);
                $this->recalculationService->recalculateLinkedProducts($stock);
            }
        }

        // Buat 1 movement OUT jika ada pengurangan stok
        if (!empty($itemsOut)) {
            $movementOut = StockMovement::create([
                'owner_id' => $owner->id,
                'partner_id' => $location,
                'type' => 'out',
                'category' => $category,
                'notes' => $notes,
            ]);

            foreach ($itemsOut as $itemData) {
                $stock = $itemData['stock'];
                $quantity = $itemData['quantity'];

                $movementOut->items()->create([
                    'stock_id' => $stock->id,
                    'quantity' => $quantity,
                    'unit_price' => $stock->last_price_per_unit
                ]);

                $stock->decrement('quantity', $quantity);
                $this->recalculationService->recalculateLinkedProducts($stock);
            }
        }
    }

    private function processStockOut(Request $request, $owner, $items)
    {
        $locationFrom = $request->input('location_from') == '_owner' ? null : $request->input('location_from');
        $category = $request->input('category');

        // Buat Gerakan KELUAR (OUT)
        $movement = StockMovement::create([
            'owner_id' => $owner->id,
            'partner_id' => $locationFrom,
            'type' => 'out',
            'category' => $category,
            'notes' => $request->input('notes'),
        ]);

        foreach ($items as $item) {
            $inputQuantity = $item['quantity'];
            $inputUnitId = $item['unit_id'];

            $quantityInBaseUnit = $this->unitConversionService->convertToBaseUnit(
                $inputQuantity,
                $inputUnitId
            );

            $stockFrom = Stock::where('id', $item['stock_id'])
                ->where('owner_id', $owner->id)
                ->where('partner_id', $locationFrom)
                ->first();

            if (!$stockFrom) {
                $failedStockName = Stock::find($item['stock_id'])->stock_name ?? "ID {$item['stock_id']}";
                $locationName = $locationFrom == null ? "Gudang Owner" : User::find($locationFrom)->name;
                throw new \Exception("Item '{$failedStockName}' tidak terdaftar di lokasi asal '{$locationName}'.");
            }

            if ($stockFrom->quantity < $quantityInBaseUnit) {
                $availableInDisplayUnit = $this->unitConversionService->convertToDisplayUnit(
                    $stockFrom->quantity,
                    $stockFrom->display_unit_id ?? 1
                );
                $displayUnit = $stockFrom->displayUnit ? $stockFrom->displayUnit->unit_name : 'unit';

                throw new \Exception("Stok '{$stockFrom->stock_name}' tidak mencukupi untuk adjustment (Hanya tersisa: {$availableInDisplayUnit} {$displayUnit}).");
            }

            $movement->items()->create([
                'stock_id' => $stockFrom->id,
                'quantity' => $quantityInBaseUnit,
                'unit_price' => $stockFrom->last_price_per_unit
            ]);

            $stockFrom->decrement('quantity', $quantityInBaseUnit);

            $this->recalculationService->recalculateLinkedProducts($stockFrom);
        }
    }

    public function getMovementItemsJson(Request $request, $id)
    {
        $owner = Auth::user();

        $movement = StockMovement::where('owner_id', $owner->id)
            ->with('partner')
            ->findOrFail($id);

        $items = $movement->items()
            ->with('stock.displayUnit')
            ->get();

        $formattedItems = $items->map(function ($item) {
            $displayQty = $item->quantity;
            $unitName = 'N/A';
            $stockName = 'Item Tidak Ditemukan';

            if ($item->stock && $item->stock->displayUnit) {
                $stockName = $item->stock->stock_name;
                $unitName = $item->stock->displayUnit->unit_name;

                // Gunakan service untuk konversi
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

    private function generateUniqueStockCode(): string
    {
        $date = Carbon::now()->format('ymd');

        for ($i = 0; $i < 10; $i++) {
            $suffix = strtoupper(Str::random(6));
            $code = "STK-{$date}-{$suffix}";

            if (!Stock::where('stock_code', $code)->exists()) {
                return $code;
            }
        }

        return 'STK-' . Str::ulid()->toBase32();
    }
}
