<?php

namespace App\Http\Controllers\Owner\Product;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Product\MasterProduct;
use App\Models\Store\Stock;
use App\Models\Product\Promotion;
use App\Models\Store\MasterUnit;
use App\Models\User;
use App\Services\UnitConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OwnerStockController extends Controller
{
    protected $unitConversionService;

    public function __construct(UnitConversionService $unitConversionService)
    {
        $this->unitConversionService = $unitConversionService;
    }

    public function index(Request $request)
    {
        $owner = Auth::user();

        // Ambil daftar Partner/Outlet untuk dropdown
        $partners = User::where('owner_id', $owner->id)
            ->where('role', 'partner')
            ->orderBy('name')
            ->get();

        // Menentukan lokasi filter default (Gudang Owner)
        $filterLocation = $request->input('filter_location', 'owner');

        $query = Stock::with('displayUnit')->where('owner_id', $owner->id);

        if ($filterLocation === 'owner') {
            $query->whereNull('partner_id');
        } else {
            // Cari partner berdasarkan username
            $partner = User::where('owner_id', $owner->id)
                ->where('role', 'partner')
                ->where('username', $filterLocation)
                ->first();

            if ($partner) {
                $query->where('partner_id', $partner->id);
            } else {
                // Jika username tidak ditemukan, fallback ke owner
                $query->whereNull('partner_id');
                $filterLocation = 'owner';
            }
        }

        // Ambil SEMUA stocks (tanpa pagination di backend)
        $allStocks = $query->get();

        // Konversi quantity untuk tampilan dan format data untuk JavaScript
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

        // Simulasi pagination untuk compatibility dengan view
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

        return view('pages.owner.products.stock.index', compact(
            'stocks',
            'partners',
            'filterLocation',
            'allStocksFormatted'
        ));
    }

    public function create()
    {
        $owner = Auth::user();

        $existing_master_product_ids = Stock::where('owner_id', $owner->id)
            ->whereNotNull('owner_master_product_id')
            ->pluck('owner_master_product_id');

        $master_products = MasterProduct::where('owner_id', $owner->id)
            ->whereNotIn('id', $existing_master_product_ids)
            ->orderBy('name')
            ->get();

        $master_units = MasterUnit::where('owner_id', $owner->id)
            ->orWhereNull('owner_id')
            ->orderBy('group_label')
            ->orderBy('unit_name')
            ->get();

        return view('pages.owner.products.stock.create', compact(
            'master_products',
            'master_units'
        ));
    }

    public function store(Request $request)
    {
        $owner = Auth::user();

        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'stock_name'         => ['required', 'string', 'max:150'],
                'description'        => ['nullable', 'string', 'max:1000'],
                'product_id'         => ['nullable', 'integer', 'exists:master_products,id'],
                'unit_id'            => [
                    'required',
                    'integer',
                    Rule::exists('master_units', 'id')->where(function ($query) use ($owner) {
                        $query->where('owner_id', $owner->id)
                            ->orWhereNull('owner_id');
                    }),
                ],
            ]);

            // Ambil semua ID Partner (Outlet) yang dimiliki oleh Owner
            $partnerIds = User::where('owner_id', $owner->id)
                ->where('role', 'partner')
                ->pluck('id');

            // Tambahkan ID Owner sebagai "gudang utama" (master stock)
            $recipients = collect([
                ['id' => $owner->id, 'partner_id' => null, 'type' => 'master']
            ]);

            // Tambahkan semua ID Partner
            $partnerRecipients = $partnerIds->map(function ($id) {
                return ['id' => $id, 'partner_id' => $id, 'type' => 'partner'];
            });

            // Gabungkan list penerima
            $recipients = $recipients->concat($partnerRecipients);


            // Looping untuk membuat Stock di Owner dan setiap Partner
            foreach ($recipients as $recipient) {
                $partnerId = $recipient['partner_id'];
                $stockType = $recipient['type'];

                $ownerIdForStock = ($stockType === 'master') ? $owner->id : $owner->id;

                $stockName = $validated['stock_name'];

                $code = $this->generateUniqueStockCode();

                $newStock = Stock::create([
                    'stock_code'              => $code,
                    'stock_type'              => 'linked',
                    'owner_id'                => $ownerIdForStock,
                    'partner_id'              => $partnerId,
                    'owner_master_product_id' => $validated['product_id'] ?? null,
                    'type'                    => $stockType,
                    'stock_name'              => ($stockType === 'partner') ? $stockName  : $stockName,
                    'quantity'                => 0,
                    'last_price_per_unit'     => 0,
                    'description'             => $validated['description'],
                    'display_unit_id'         => $validated['unit_id'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('owner.user-owner.stocks.index')
                ->with('success', 'Stock item and ' . count($partnerIds) . ' partner stocks added successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
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

    public function show(Promotion $promotion)
    {
        $data = Promotion::where('id', $promotion->id)->first();
        return view('pages.owner.products.stock.show', compact('data'));
    }

    public function edit(Promotion $promotion)
    {
        $owner = Auth::user();
        $data = Promotion::where('owner_id', $owner->id)
            ->where('id', $promotion->id)
            ->first();

        return view('pages.owner.products.stock.edit', compact('data'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $owner = Auth::user();

        DB::beginTransaction();
        try {
            $request->validate([
                'promotion_name'  => ['required', 'string', 'max:150'],
                'promotion_type'  => ['required', 'in:percentage,amount'],
                'promotion_value' => [
                    'required',
                    'numeric',
                    function ($attr, $val, $fail) use ($request) {
                        if ($request->promotion_type === 'percentage' && ($val < 1 || $val > 100)) {
                            $fail('Persentase harus 1–100.');
                        }
                        if ($request->promotion_type === 'amount' && $val < 0) {
                            $fail('Nominal tidak boleh negatif.');
                        }
                    }
                ],
                'uses_expiry'     => ['nullable', 'boolean'],
                'start_date'      => ['required_if:uses_expiry,1', 'nullable', 'date'],
                'end_date'        => ['required_if:uses_expiry,1', 'nullable', 'date', 'after:start_date'],
                'is_active'       => ['nullable', 'boolean'],
                'description'     => ['nullable', 'string'],
                'active_days'     => ['nullable', 'array'],
                'active_days.*'   => ['in:mon,tue,wed,thu,fri,sat,sun'],
            ]);

            $uses_expiry = $request->has('uses_expiry') ? (bool)$request->uses_expiry : false;

            if ((int)$promotion->owner_id !== (int)$owner->id) {
                abort(403);
            }

            $promotion->update([
                'promotion_name'  => $request->promotion_name,
                'promotion_type'  => $request->promotion_type,
                'promotion_value' => $request->promotion_value,
                'uses_expiry'     => $uses_expiry,
                'start_date'      => $request->uses_expiry ? $request->start_date : null,
                'end_date'        => $request->uses_expiry ? $request->end_date : null,
                'active_days'     => $request->active_days ?: null,
                'is_active'       => $request->has('is_active') ? (bool)$request->is_active : false,
                'description'     => $request->description,
            ]);

            DB::commit();

            return redirect()
                ->route('owner.user-owner.promotions.index')
                ->with('success', 'Promotion updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();

        return redirect()->route('owner.user-owner.stocks.index')->with('success', 'Stock deleted successfully!');
    }

    public function deleteStock(Request $request, $id)
    {
        $stock = Stock::findOrFail($id);
        $partner_stocks = Stock::where('owner_id', $stock->owner_id)
            ->where('type', 'partner')
            ->where('stock_name', $stock->stock_name)
            ->get();
        foreach ($partner_stocks as $ps) {
            $ps->delete();
        }

        $stock->delete();

        return redirect()->route('owner.user-owner.stocks.index')->with('success', 'Stock deleted successfully!');
    }
}
