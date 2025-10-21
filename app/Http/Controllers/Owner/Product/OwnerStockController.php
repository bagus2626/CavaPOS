<?php

namespace App\Http\Controllers\Owner\Product;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Product\MasterProduct;
use App\Models\Product\MasterProductParentOption;
use App\Models\Product\MasterProductOption;
use App\Models\Admin\Product\Category;
use App\Models\Store\Stock;
use App\Models\Product\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class OwnerStockController extends Controller
{
    public function index()
    {
        $owner = Auth::user();
        $stocks = Stock::where('owner_id', $owner->id)->get();
        return view('pages.owner.products.stock.index', compact('stocks'));
    }

    public function create()
    {
        $owner = Auth::user();
        $existing_master_product_ids = Stock::where('owner_id', $owner->id)->pluck('owner_master_product_id');
        $master_products = MasterProduct::where('owner_id', $owner->id)
            ->whereNotIn('id', $existing_master_product_ids)
            ->get();
        // dd($master_products);
        return view('pages.owner.products.stock.create', compact('master_products'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $owner = Auth::user();

        DB::beginTransaction();
        try {
            $request->validate([
                'stock_name'          => ['required','string','max:150'],
                'unit'                => ['required','string','max:30'],
                'custom_unit'         => ['nullable','string','max:30'],
                'description'         => ['nullable','string','max:1000'],
            ]);

            $unit = $request->unit === 'other' ? ($request->custom_unit ?: 'unit') : $request->unit;
            $code = $this->generateUniqueStockCode();

            Stock::create([
                'stock_code' => $code,
                'owner_id' => $owner->id,
                'owner_master_product_id' => $request->product_id ?? null,
                'type' => 'master',
                'unit' => $unit,
                'stock_name' => $request->stock_name,
                'description' => $request->description
            ]);

            DB::commit();

            return redirect()
                ->route('owner.user-owner.stocks.index')
                ->with('success', 'Product added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    private function generateUniqueStockCode(): string
    {
        $date = Carbon::now()->format('ymd');

        for ($i = 0; $i < 10; $i++) { // batasi percobaan collision
            // 6 char alfanumerik uppercase
            $suffix = strtoupper(Str::random(6)); // [A-Za-z0-9], nanti di-upper
            $code = "STK-{$date}-{$suffix}";

            if (!Stock::where('stock_code', $code)->exists()) {
                return $code;
            }
        }

        // fallback super kuat kalau (sangat jarang) masih tabrakan
        return 'STK-'.Str::ulid()->toBase32(); // ~26 char, sangat unik
    }


    public function show(Promotion $promotion)
    {
        $data = Promotion::where('id', $promotion->id)->first();
        return view('pages.owner.products.stock.show', compact('data'));
    }

    public function edit(Promotion $promotion)
    {
        $owner = Auth::user();
        // dd($owner);
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

            // Opsional: guard kepemilikan
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
                'active_days'     => $request->active_days ?: null, // model cast ke array
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
        // Hapus semua paket dan spesifikasi terkait

        $stock->delete();

        return redirect()->route('owner.user-owner.stocks.index')->with('success', 'Stock deleted successfully!');
    }
}
