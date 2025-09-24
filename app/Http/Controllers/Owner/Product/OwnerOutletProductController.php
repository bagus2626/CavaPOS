<?php

namespace App\Http\Controllers\Owner\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\User;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Product\MasterProduct;
use App\Models\Product\Promotion;
use App\Models\Product\MasterProductParentOption;
use App\Models\Product\MasterProductOption;
use App\Models\Product\Specification;
use App\Models\Admin\Product\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OwnerOutletProductController extends Controller
{
    public function index()
    {
        $owner = Auth::user();
        $outlets = User::where('owner_id', $owner->id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        $categories = Category::where('owner_id', $owner->id)
            ->orderBy('category_name')
            ->get();
        $master_product = MasterProduct::where('owner_id', $owner->id)->get();
        $productsByOutlet = PartnerProduct::with('parent_options.options', 'category', 'promotion')
            ->where('owner_id', $owner->id)
            ->get()
            ->groupBy('partner_id');
        $promotions = Promotion::where('owner_id', $owner->id)->get();
        return view('pages.owner.products.outlet-product.index', compact('productsByOutlet', 'categories', 'outlets', 'master_product', 'promotions'));
    }

    public function create()
    {
        $categories = Category::where('owner_id', Auth::id())->get();
        return view('pages.owner.products.outlet-product.create', compact('categories'));
    }

    public function getMasterProducts(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'outlet_id'   => 'required|integer|exists:users,id', // sesuaikan dengan skema kamu
        ]);

        // Master product yang sudah ada di outlet tsb → dikeluarkan
        $existing_outlet_products = PartnerProduct::where('partner_id', $request->outlet_id)
            ->pluck('master_product_id')
            ->toArray();

        $ownerId = Auth::id();

        $list = MasterProduct::query()
            ->where('owner_id', $ownerId)
            ->when($request->category_id !== 'all', function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->whereNotIn('id', $existing_outlet_products)
            ->select('id', 'name', 'pictures')
            ->orderBy('name')
            ->get()
            ->map(function ($mp) {
                // Pastikan pictures berupa array
                $pictures = is_array($mp->pictures) ? $mp->pictures : [];

                // Tambahkan URL absolut untuk tiap gambar
                $pictures = collect($pictures)->map(function ($pic) {
                    // Jika sudah http/https biarkan
                    $path = is_array($pic) ? ($pic['path'] ?? null) : null;
                    if (!$path) return $pic;

                    if (preg_match('~^https?://~i', $path)) {
                        // sudah absolut
                        $pic['url'] = $path;
                    } else {
                        // normalisasi: hilangkan slash depan jika ada, lalu jadikan absolut
                        $normalized = ltrim($path, '/');               // "storage/uploads/.."
                        $pic['url'] = asset($normalized);              // "https://domain/storage/uploads/.."
                    }
                    return $pic;
                })->values()->all();

                // set kembali & siapkan thumb_url (gambar pertama)
                $mp->pictures  = $pictures;
                $mp->thumb_url = $pictures[0]['url'] ?? null;

                return $mp;
            });

        return response()->json($list);
    }

    // public function getMasterProducts(Request $request)
    // {
    //     // dd($request->all());
    //     $request->validate([
    //         'category_id' => 'required',
    //         'outlet_id'   => 'required|integer|exists:users,id',
    //     ]);

    //     // Ambil semua master_product_id yang sudah ada di outlet
    //     $existing_outlet_products = PartnerProduct::where('partner_id', $request->outlet_id)
    //         ->pluck('master_product_id') // pluck saja, tidak perlu get()
    //         ->toArray();

    //     $ownerId = Auth::id();

    //     $list = MasterProduct::query()
    //         ->where('owner_id', $ownerId)
    //         ->when($request->category_id !== 'all', function ($query) use ($request) {
    //             $query->where('category_id', $request->category_id);
    //         })
    //         ->whereNotIn('id', $existing_outlet_products)
    //         ->select('id', 'name', 'pictures')
    //         ->orderBy('name')
    //         ->get();


    //     return response()->json($list);
    // }

    public function store(Request $request)
    {
        // dd($request->all());
        $owner = Auth::user();

        // VALIDASI
        $validated = $request->validate([
            'outlet_id'           => 'required|integer', // sesuaikan jadi exists:outlets,id jika perlu
            'category_id'         => 'required',         // karena bisa 'all' dari modal
            'master_product_ids'  => 'required|array|min:1',
            'master_product_ids.*' => 'integer|exists:master_products,id',
            'quantity'            => 'nullable|integer|min:0',
            'is_active'           => 'required|in:0,1',
        ]);

        $outletId   = (int) $validated['outlet_id'];
        $quantity   = (int) ($validated['quantity'] ?? 0);
        $isActive   = (int) $validated['is_active'];
        $ids        = collect($validated['master_product_ids'])->map(fn($id) => (int)$id)->unique()->values()->all();

        // Ambil semua master product milik owner dalam 1 query
        $masters = MasterProduct::with('parent_options.options')
            ->where('owner_id', $owner->id)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id'); // agar mudah diakses: $masters[$id]

        // Cek yang sudah ada di outlet (hindari duplikat) dalam 1 query
        $existingIds = PartnerProduct::where('partner_id', $outletId)
            ->whereIn('master_product_id', $ids)
            ->pluck('master_product_id')
            ->all();

        $toCreate = array_values(array_diff($ids, $existingIds)); // hanya yang belum ada
        $created  = [];
        $skipped  = $existingIds; // informasi buat respon

        DB::beginTransaction();
        try {
            foreach ($toCreate as $mid) {
                /** @var \App\Models\Product\MasterProduct|null $master */
                $master = $masters[$mid] ?? null;
                if (!$master) {
                    // master tidak ditemukan / bukan milik owner — skip saja
                    continue;
                }

                $productCode = 'PPD-' . $outletId . '-' . strtoupper(uniqid());

                // Buat partner product
                $partnerProduct = PartnerProduct::create([
                    'master_product_id' => $master->id,
                    'product_code'      => $productCode,
                    'owner_id'          => $owner->id,
                    'partner_id'        => $outletId,         // outlet
                    'name'              => $master->name,
                    'category_id'       => $master->category_id,
                    'price'             => $master->price,
                    'quantity'          => $quantity,
                    'pictures'          => $master->pictures,
                    'description'       => $master->description,
                    'promo_id'          => $master->promo_id ?? null,
                    'is_active'         => $isActive,
                ]);

                // Copy parent options & child options dari master
                foreach ($master->parent_options as $mParent) {
                    $pParent = PartnerProductParentOption::create([
                        'master_product_parent_option_id' => $mParent->id,  // pastikan kolom ini ada; jika tidak, hapus field ini
                        'partner_product_id'              => $partnerProduct->id,
                        'name'                            => $mParent->name,
                        'description'                     => $mParent->description,
                        'provision'                       => $mParent->provision,
                        'provision_value'                 => $mParent->provision_value ?? 0,
                    ]);

                    foreach ($mParent->options as $mOpt) {
                        PartnerProductOption::create([
                            'master_product_option_id'           => $mOpt->id,  // pastikan kolom ini ada; jika tidak, hapus field ini
                            'partner_product_id'                 => $partnerProduct->id,
                            'partner_product_parent_option_id'   => $pParent->id,
                            'name'        => $mOpt->name,
                            'quantity'    => $mOpt->quantity, // kalau stok outlet beda kebijakan, ubah di sini
                            'price'       => $mOpt->price,
                            'pictures'    => $mOpt->pictures ?? null,
                            'description' => $mOpt->description,
                        ]);
                    }
                }

                $created[] = $mid;
            }

            DB::commit();

            // Respons
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'            => true,
                    'created_ids'   => $created,
                    'skipped_ids'   => $skipped,
                    'created_count' => count($created),
                    'skipped_count' => count($skipped),
                    'message'       => count($created)
                        ? 'Product(s) added successfully.'
                        : 'No new product created (all selected already exist).',
                ]);
            }

            $msg = count($created)
                ? 'Product added successfully! (created: ' . count($created) . ', skipped: ' . count($skipped) . ')'
                : 'No new product created (all selected already exist).';

            return redirect()
                ->route('owner.user-owner.outlet-products.index')
                ->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->route('owner.user-owner.outlet-products.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function show(PartnerProduct $product)
    {
        $categories = Category::where('partner_id', Auth::id())->get();
        $data = PartnerProduct::with('parent_options.options', 'category')
            ->where('partner_id', Auth::id())
            ->where('id', $product->id)
            ->first();
        return view('pages.partner.products.show', compact('data', 'categories'));
    }

    public function edit(PartnerProduct $outlet_product)
    {
        $owner = Auth::user();
        $categories = Category::where('owner_id', $owner->id)->get();
        $data = PartnerProduct::with('parent_options.options', 'category', 'owner')
            ->where('owner_id', $owner->id)
            ->where('id', $outlet_product->id)
            ->first();
        $promotions = Promotion::where('owner_id', $owner->id)->get();

        return view('pages.owner.products.outlet-product.edit', compact('data', 'categories', 'promotions'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Ambil produk + relasi
            $product = PartnerProduct::with(['parent_options.options'])->findOrFail($id);

            // Cek apakah produk ini punya option (pakai flatMap eksplisit biar aman)
            $hasOptions = $product->parent_options
                ->flatMap(function ($parent) {
                    return $parent->options ?? collect();
                })
                ->isNotEmpty();

            // Rules dasar
            $rules = [
                'quantity'      => ['required', 'integer', 'min:0'],
                'is_active'     => ['required', 'in:0,1'],
                'promotion_id'  => ['nullable', 'integer', 'exists:promotions,id'],
                // options hanya wajib bila produk punya option
                'options'       => [$hasOptions ? 'required' : 'nullable', 'array'],
            ];

            // Validasi quantity per option (hanya jika ada options)
            if ($hasOptions) {
                $rules['options.*.quantity'] = ['required', 'integer', 'min:0'];
            } else {
                // Jika tidak punya option, abaikan bila ada kiriman options
                $rules['options.*.quantity'] = ['sometimes', 'integer', 'min:0'];
            }

            $validated = $request->validate($rules);

            // Normalisasi nilai
            $newQuantity  = (int) ($validated['quantity'] ?? 0);
            $newIsActive  = (int) ($validated['is_active'] ?? 0);
            $promotionId  = $request->filled('promotion_id') ? (int) $validated['promotion_id'] : null;

            // Update field utama produk (termasuk promo)
            $product->update([
                'quantity'  => $newQuantity,
                'is_active' => $newIsActive,
                'promo_id'  => $promotionId,   // <- tambahkan ini
            ]);

            // === Update quantity per option (jika ada input options) ===
            $optionInputs = $validated['options'] ?? []; // bisa kosong
            if (!empty($optionInputs)) {
                // Struktur diasumsikan: [ optionId => ['quantity' => ...], ... ]
                $optionIds = array_map('intval', array_keys($optionInputs));

                // Pastikan hanya option milik produk ini yang diupdate
                $options = PartnerProductOption::whereIn('id', $optionIds)
                    ->whereHas('parent', function ($q) use ($product) {
                        $q->where('partner_product_id', $product->id);
                    })
                    ->get();

                foreach ($options as $opt) {
                    $newQty = (int) ($optionInputs[$opt->id]['quantity'] ?? 0);
                    // Gunakan fill+save agar event model tetap terpanggil (kalau ada)
                    $opt->fill(['quantity' => $newQty])->save();
                }
            }

            DB::commit();

            return redirect()
                ->route('owner.user-owner.outlet-products.index')
                ->with('success', 'Product updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }


    // public function update(Request $request, $id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         // Ambil produk + relasi
    //         $product = PartnerProduct::with(['parent_options.options'])->findOrFail($id);

    //         // Cek apakah produk ini punya option
    //         $hasOptions = $product->parent_options->flatMap->options->isNotEmpty();

    //         // Rules dasar
    //         $rules = [
    //             'quantity'  => ['required', 'integer', 'min:0'],
    //             'is_active' => ['required', 'in:0,1'],
    //             // options hanya wajib bila produk punya option
    //             'options'   => [$hasOptions ? 'required' : 'nullable', 'array'],
    //         ];

    //         // Validasi quantity per option hanya bila memang ada options
    //         if ($hasOptions) {
    //             $rules['options.*.quantity'] = ['required', 'integer', 'min:0'];
    //         } else {
    //             // Jika tidak punya option, abaikan bila ada kiriman options
    //             $rules['options.*.quantity'] = ['sometimes', 'integer', 'min:0'];
    //         }

    //         $validated = $request->validate($rules);

    //         // Update field utama produk
    //         $product->update([
    //             'quantity'  => (int) $validated['quantity'],
    //             'is_active' => (int) $validated['is_active'],
    //         ]);

    //         // === Update quantity per option (jika ada input options) ===
    //         $optionInputs = $validated['options'] ?? []; // bisa kosong
    //         if (!empty($optionInputs)) {
    //             // Struktur diasumsikan: [ optionId => ['quantity' => ...], ... ]
    //             $optionIds = array_map('intval', array_keys($optionInputs));

    //             // Pastikan hanya option milik produk ini yang diupdate
    //             $options = PartnerProductOption::whereIn('id', $optionIds)
    //                 ->whereHas('parent', function ($q) use ($product) {
    //                     $q->where('partner_product_id', $product->id);
    //                 })
    //                 ->get();

    //             foreach ($options as $opt) {
    //                 $newQty = (int) ($optionInputs[$opt->id]['quantity'] ?? 0);
    //                 $opt->update(['quantity' => $newQty]);
    //             }
    //         }

    //         DB::commit();

    //         return redirect()
    //             ->route('owner.user-owner.outlet-products.index')
    //             ->with('success', 'Product updated successfully!');
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return redirect()
    //             ->back()
    //             ->withInput()
    //             ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    //     }
    // }

    public function destroy(PartnerProduct $outlet_product)
    {
        $outlet_product->delete();

        return redirect()->route('owner.user-owner.outlet-products.index')->with('success', 'Product deleted successfully!');
    }
}
