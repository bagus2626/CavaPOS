<?php

namespace App\Http\Controllers\Employee\Staff\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\MasterProduct;
use App\Models\Store\MasterUnit;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Store\Stock;
use App\Models\User;
use App\Services\UnitConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffStockController extends Controller
{
    protected $unitConversionService;

    public function __construct(UnitConversionService $unitConversionService)
    {
        $this->unitConversionService = $unitConversionService;
    }

    /**
     * Mendapatkan prefix route dinamis (manager atau supervisor)
     */
    private function getRoutePrefix(): string
    {
        $role = Auth::guard('employee')->user()->role;
        return strtolower($role);
    }

    /**
     * Mendapatkan Data Konteks (Employee, Partner, Owner)
     */
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

        // 1. Ambil HANYA Stock milik Partner (Outlet) ini saja.
        // Tipe stock biasanya 'partner' atau bisa dicari berdasarkan partner_id.
        $query = Stock::with('displayUnit')
            ->where('owner_id', $context->owner_id)
            ->where('partner_id', $context->partner_id);

        $allStocks = $query->get();

        // 2. Konversi quantity untuk tampilan dan format data untuk JavaScript
        $allStocksFormatted = $allStocks->map(function ($stock) {
            if ($stock->displayUnit) {
                $displayQuantity = $this->unitConversionService->convertToDisplayUnit(
                    $stock->quantity,
                    $stock->display_unit_id
                );
            } else {
                $displayQuantity = $stock->quantity;
            }

            return [
                'id' => $stock->id,
                'stock_code' => $stock->stock_code,
                'stock_name' => $stock->stock_name,
                'quantity' => $stock->quantity,
                'display_quantity' => $displayQuantity,
                'display_unit_id' => $stock->display_unit_id,
                'display_unit_name' => $stock->displayUnit ? $stock->displayUnit->unit_name : null,
                'last_price_per_unit' => $stock->last_price_per_unit,
                'type' => $stock->type,
                'stock_type' => $stock->stock_type,
                'partner_product_id' => $stock->partner_product_id,
                'partner_product_option_id' => $stock->partner_product_option_id,
            ];
        });

        // 3. Simulasi pagination untuk compatibility dengan view
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        // Transform collection untuk tampilan default
        $stocksForDisplay = $allStocks->map(function ($stock) {
            if ($stock->displayUnit) {
                $stock->display_quantity = $this->unitConversionService->convertToDisplayUnit(
                    $stock->quantity,
                    $stock->display_unit_id
                );
            } else {
                $stock->display_quantity = $stock->quantity;
            }
            return $stock;
        });

        $stocks = new \Illuminate\Pagination\LengthAwarePaginator(
            $stocksForDisplay->slice($offset, $perPage)->values(),
            $stocksForDisplay->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // View dipindahkan ke folder staff
        return view('pages.employee.staff.products.stocks.index', compact(
            'stocks',
            'allStocksFormatted'
        ));
    }

    public function create()
    {
        $context = $this->getContext();

        // Ambil semua unit yang tersedia
        $master_units = MasterUnit::where('owner_id', $context->owner_id)
            ->orWhereNull('owner_id')
            ->get();

        // Ambil semua Master Product milik owner ini untuk dropdown "Use Existing Product"
        $master_products = MasterProduct::where('owner_id', $context->owner_id)
            ->orderBy('name')
            ->get();

        return view('pages.employee.staff.products.stocks.create', compact('master_units', 'master_products'));
    }

    public function store(Request $request)
    {
        $context = $this->getContext();
        $routePrefix = $this->getRoutePrefix();

        // Validasi disesuaikan dengan form baru
        $request->validate([
            'stock_name' => 'required|string|max:150',
            'unit_id'    => 'required|exists:master_units,id',
            'product_id' => 'nullable|exists:master_products,id',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $stockName = $request->input('stock_name');
            $unitId    = $request->input('unit_id');
            $desc      = $request->input('description');

            // Note: form baru TIDAK memiliki field "quantity" & "price", 
            // jadi kita anggap penambahan awal adalah 0
            $price     = 0;

            // Opsional: jika pakai product_id, Anda bisa mengambil ID master productnya jika diperlukan untuk logika spesifik

            // 1. CEK & BUAT MASTER STOCK DI OWNER (Gudang Pusat)
            $ownerStock = Stock::where('owner_id', $context->owner_id)
                ->whereNull('partner_id')
                ->where('stock_name', $stockName)
                ->whereNull('partner_product_id')
                ->whereNull('partner_product_option_id')
                ->first();

            if (!$ownerStock) {
                $ownerStock = Stock::create([
                    'stock_code'          => $this->generateUniqueStockCode(),
                    'owner_id'            => $context->owner_id,
                    'partner_id'          => null,
                    'type'                => 'master',
                    'stock_type'          => 'linked',
                    'stock_name'          => $stockName,
                    'display_unit_id'     => $unitId,
                    'quantity'            => 0,
                    'last_price_per_unit' => $price,
                    'description'         => $desc,
                ]);
            }

            // 2. CEK & BUAT STOK DI OUTLET (Staff)
            $partnerStock = Stock::where('owner_id', $context->owner_id)
                ->where('partner_id', $context->partner_id)
                ->where('stock_name', $stockName)
                ->whereNull('partner_product_id')
                ->whereNull('partner_product_option_id')
                ->first();

            if (!$partnerStock) {
                $partnerStock = Stock::create([
                    'stock_code'          => $this->generateUniqueStockCode(),
                    'owner_id'            => $context->owner_id,
                    'partner_id'          => $context->partner_id,
                    'type'                => 'partner',
                    'stock_type'          => 'linked',
                    'stock_name'          => $stockName,
                    'display_unit_id'     => $unitId,
                    'quantity'            => 0,
                    'last_price_per_unit' => $price,
                    'description'         => $desc,
                ]);
            } else {
                return back()->with('error', 'Item stok dengan nama tersebut sudah ada di outlet Anda! Gunakan menu "Stock In" jika ingin menambah jumlahnya.')->withInput();
            }

            DB::commit();
            return redirect()->route('employee.' . $routePrefix . '.stocks.index')
                ->with('success', 'Bahan baku baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan stok: ' . $e->getMessage())->withInput();
        }
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

    public function show($id)
    {
        // Fitur show detail stock jika diperlukan. (Seringnya tidak terpakai jika hanya ada index)
        abort(404);
    }

    public function edit($id)
    {
        // Edit nama/deskripsi stok adalah hak owner
        abort(403, 'Akses ditolak.');
    }

    public function update(Request $request, $id)
    {
        abort(403, 'Akses ditolak.');
    }

    public function destroy($id)
    {
        return $this->deleteStock(request(), $id);
    }

    public function deleteStock(Request $request, $id)
    {
        $context = $this->getContext();
        $routePrefix = $this->getRoutePrefix();

        $stock = Stock::where('owner_id', $context->owner_id)
            ->where('partner_id', $context->partner_id)
            ->findOrFail($id);

        $stock->delete();

        return redirect()->route('employee.' . $routePrefix . '.stocks.index')
            ->with('success', 'Stok outlet berhasil dihapus!');
    }
}
