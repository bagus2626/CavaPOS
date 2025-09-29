<?php

namespace App\Http\Controllers\Owner\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Product\MasterProduct;
use App\Models\Product\MasterProductParentOption;
use App\Models\Product\MasterProductOption;
use App\Models\Product\Promotion;
use App\Models\Product\Specification;
use App\Models\Admin\Product\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class OwnerMasterProductController extends Controller
{
    public function index()
    {
        $categories = Category::where('owner_id', Auth::id())->get();
        $products = MasterProduct::with('parent_options.options', 'category', 'promotion')
            ->where('owner_id', Auth::id())
            ->get();
        return view('pages.owner.products.master-product.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('owner_id', Auth::id())->get();
        $promotions = Promotion::where('owner_id', Auth::id())
            ->get();
        return view('pages.owner.products.master-product.create', compact('categories', 'promotions'));
    }

    public function store(Request $request)
    {
        $owner = Auth::user();

        DB::beginTransaction();
        try {
            // dd($request->all());
            // Validasi request
            $validated = $request->validate([
                'name'             => 'required|string|max:255',
                'product_category' => 'required|exists:categories,id',
                'price'            => 'required',
                'description'      => 'nullable|string',
                'images'           => 'nullable|array|max:5',
                'promotion_id'     => 'nullable|integer|exists:promotions,id',
                'images.*'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'options'          => 'nullable|array',
            ], [
                'name.required'             => 'Nama produk wajib diisi.',
                'name.string'               => 'Nama produk harus berupa teks.',
                'product_category.required' => 'Kategori produk wajib dipilih.',
                'product_category.exists'   => 'Kategori produk tidak valid.',
                'price.required'            => 'Harga produk wajib diisi.',
                'images.array'              => 'Gambar harus dalam bentuk array.',
                'options.array'             => 'Options harus dalam bentuk array.',
            ]);


            // Konversi harga ke angka (hilangkan titik)
            $price = (int) str_replace('.', '', $validated['price']);
            // Simpan produk utama
            $productCode = 'MPD-' . $owner->id . '-' . strtoupper(uniqid());

            $storedImages = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('uploads/master-products', $filename, 'public');

                    // Optional: Compress & resize image
                    $img = Image::make(storage_path('app/public/' . $path));
                    $img->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save();

                    $storedImages[] = [
                        'path'     => 'storage/' . $path,
                        'filename' => $filename,
                        'mime'     => $image->getClientMimeType(),
                        'size'     => $image->getSize(),
                    ];
                }
            }

            $product = MasterProduct::create([
                'product_code' => $productCode,
                'owner_id'   => $owner->id, // pastikan ini valid
                'name'         => $request->name,
                'category_id'  => $request->product_category,
                'quantity'     => 0,
                'price'        => $price,
                'description'  => $request->description,
                'promo_id' => $request->promotion_id ?? null,
                'pictures'     => $storedImages ?? null,
            ]);

            if ($request->has('menu_options')) {
                foreach ($request->menu_options as $parentOption) {
                    $parent = MasterProductParentOption::create([
                        'master_product_id' => $product->id,
                        'name'               => $parentOption['name'],
                        'description'        => $parentOption['description'] ?? null,
                        'provision'          => $parentOption['provision'],
                        'provision_value'    => $parentOption['provision_value'] ?? 0,
                    ]);

                    if (isset($parentOption['options']) && is_array($parentOption['options'])) {
                        foreach ($parentOption['options'] as $option) {
                            $optionPrice = (int) str_replace('.', '', $option['price']);

                            $optionImages = null;
                            if (isset($option['image']) && $option['image'] instanceof \Illuminate\Http\UploadedFile) {
                                $filename = time() . '_' . uniqid() . '.' . $option['image']->getClientOriginalExtension();
                                $path = $option['image']->storeAs('uploads/products/master_product_options', $filename, 'public');

                                $img = Image::make(storage_path('app/public/' . $path));
                                $img->resize(800, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                })->save();

                                $optionImages = [[
                                    'path'     => 'storage/' . $path,
                                    'filename' => $filename,
                                    'mime'     => $option['image']->getClientMimeType(),
                                    'size'     => $option['image']->getSize(),
                                ]];
                            }
                            // dd($product->id, $parent->id, $option['name'], $option['quantity'], $optionPrice, $option['description'], $optionImages);

                            MasterProductOption::create([
                                'master_product_id' => $product->id,
                                'master_product_parent_option_id' => $parent->id,
                                'name'        => $option['name'],
                                'quantity'    => 0,
                                'price'       => $optionPrice,
                                'description' => $option['description'] ?? null,
                                'pictures'    => $optionImages ?? null,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('owner.user-owner.master-products.index')
                ->with('success', 'Product added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
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

    public function edit(MasterProduct $master_product)
    {
        $owner = Auth::user();
        // dd($master_product->id);
        $categories = Category::where('owner_id', $owner->id)->get();
        $data = MasterProduct::with('parent_options.options', 'category')
            ->where('owner_id', $owner->id)
            ->where('id', $master_product->id)
            ->first();
        $promotions = Promotion::where('owner_id', $owner->id)->get();

        return view('pages.owner.products.master-product.edit', compact('data', 'categories', 'promotions'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $product = MasterProduct::with(['parent_options.options'])->findOrFail($id);
            // dd($request->all());
            // Validasi
            $validated = $request->validate([
                'name'             => 'required|string|max:255',
                'product_category' => 'required|exists:categories,id',
                'price'            => 'required',
                'description'      => 'nullable|string',
                'promotion_id'     => 'nullable|integer|exists:promotions,id',
                'images'           => 'nullable|array|max:5',
                'images.*'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'existing_images'  => 'nullable|array',
                'menu_options'     => 'nullable|array',
            ]);
            // dd($validated);

            // Konversi harga ke integer
            $price = (int) str_replace('.', '', $validated['price']);

            // Handle existing images
            $storedImages = [];
            $existingFilenames = $request->input('existing_images', []); // filenames yang dipertahankan

            foreach ((array) $product->pictures as $pic) {
                $filename   = $pic['filename'] ?? null;
                $pathFromDb = $pic['path'] ?? null; // e.g. "storage/uploads/master-products/xxx.jpg"

                // Jika user memilih untuk tetap menyimpan gambar ini → keep
                if ($filename && in_array($filename, $existingFilenames, true)) {
                    $storedImages[] = $pic;
                    continue;
                }

                // Jika user menghapus gambar → hapus file-nya di storage
                if ($pathFromDb) {
                    // Ubah "storage/uploads/..." → "uploads/..." agar cocok dengan disk('public')
                    $relativePath = ltrim(str_replace('storage/', '', $pathFromDb), '/');

                    if (Storage::disk('public')->exists($relativePath)) {
                        Storage::disk('public')->delete($relativePath);
                    }
                } elseif ($filename) {
                    // Fallback kalau 'path' tidak ada: tebak dari folder standar
                    $guess = 'uploads/master-products/' . $filename;
                    if (Storage::disk('public')->exists($guess)) {
                        Storage::disk('public')->delete($guess);
                    }
                }
            }


            // Upload gambar baru
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('uploads/master-products', $filename, 'public');

                    $img = Image::make(storage_path('app/public/' . $path));
                    $img->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save();

                    $storedImages[] = [
                        'path'     => 'storage/' . $path,
                        'filename' => $filename,
                        'mime'     => $image->getClientMimeType(),
                        'size'     => $image->getSize(),
                    ];
                }
            }

            // Update product utama
            $product->update([
                'name'        => $request->name,
                'category_id' => $request->product_category,
                'price'       => $price,
                'promo_id'    => $request->promotion_id,
                'description' => $request->description,
                'pictures'    => $storedImages,
            ]);

            // Hapus semua parent & option yang sudah dihapus
            // Parent IDs (lebih aman dari versi awal)
            $existingParentIds = collect($request->input('menu_options', []))
                ->pluck('parent_id')
                ->filter(fn($id) => is_numeric($id))   // buang "", "null", null, dsb.
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            // Option IDs
            $existingOptionIds = collect($request->input('menu_options', []))
                ->flatMap(function ($parent) {
                    return collect($parent['options'] ?? [])
                        ->pluck('option_id');
                })
                ->filter(fn($id) => is_numeric($id))   // antisipasi "null" (string) dari form
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->all();


            foreach ($product->parent_options as $index => $parentOption) {
                if (!in_array($parentOption->id, $existingParentIds)) {
                    // Hapus opsi terkait
                    $parentOption->options()->delete();
                    $parentOption->delete();
                }
                foreach ($parentOption->options as $index => $option) {
                    if (!in_array($option->id, $existingOptionIds)) {
                        $option->delete();
                    }
                }
            }


            // Update atau create parent & option
            if ($request->has('menu_options')) {
                foreach ($request->menu_options as $parentIndex => $parentOption) {
                    $parent = $parentOption['parent_id'] ? (int) $parentOption['parent_id'] : null;

                    if ($parent) {
                        // Update parent
                        $parentModel = MasterProductParentOption::findOrFail($parent);
                        $parentModel->update([
                            'name'              => $parentOption['name'],
                            'description'       => $parentOption['description'] ?? null,
                            'provision'         => $parentOption['provision'],
                            'provision_value'   => $parentOption['provision_value'] ?? 0
                        ]);
                    } else {
                        // Create new parent
                        $parentModel = MasterProductParentOption::create([
                            'master_product_id' => $product->id,
                            'name'               => $parentOption['name'],
                            'description'        => $parentOption['description'] ?? null,
                            'provision'          => $parentOption['provision'],
                            'provision_value'    => $parentOption['provision_value'] ?? 0
                        ]);
                    }

                    // Update/Create options
                    if (isset($parentOption['options']) && is_array($parentOption['options'])) {
                        foreach ($parentOption['options'] as $optionIndex => $option) {
                            // $optionId = $option['option_id'] ?? null;
                            $optionId = $option['option_id'] ? (int) $option['option_id'] : null;
                            $optionPrice = (int) str_replace('.', '', $option['price']);

                            $optionImages = null;
                            if (isset($option['image']) && $option['image'] instanceof \Illuminate\Http\UploadedFile) {
                                $filename = time() . '_' . uniqid() . '.' . $option['image']->getClientOriginalExtension();
                                $path = $option['image']->storeAs('uploads/product_options', $filename, 'public');

                                $img = Image::make(storage_path('app/public/' . $path));
                                $img->resize(800, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                })->save();

                                $optionImages = [[
                                    'path'     => 'storage/' . $path,
                                    'filename' => $filename,
                                    'mime'     => $option['image']->getClientMimeType(),
                                    'size'     => $option['image']->getSize(),
                                ]];
                            }

                            if ($optionId) {
                                // Update option
                                $optionModel = MasterProductOption::find($optionId);
                                $optionModel->update([
                                    'name'        => $option['name'],
                                    'price'       => $optionPrice,
                                    'description' => $option['description'] ?? null,
                                    'pictures'    => $optionImages ?? $optionModel->pictures,
                                ]);
                            } else {
                                // Create new option
                                MasterProductOption::create([
                                    'master_product_id' => $product->id,
                                    'master_product_parent_option_id' => $parentModel->id,
                                    'name'        => $option['name'],
                                    'price'       => $optionPrice,
                                    'description' => $option['description'] ?? null,
                                    'pictures'    => $optionImages,
                                ]);
                            }
                        }
                    }
                }
            }

            $partner_products = PartnerProduct::where('master_product_id', $product->id)->get();
            if ($partner_products) {
                // fungsi untuk foreach partner_product
                $this->syncPartnerProductsFromMaster($product, $partner_products, false, true);
            }

            DB::commit();

            return redirect()
                ->route('owner.user-owner.master-products.index', $product->id)
                ->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }



    private function syncPartnerProductsFromMaster(
        MasterProduct $master,
        Collection $partnerProducts,
        bool $propagatePrice = true,
        bool $syncOptions = true
    ): void {
        // --- Peta master untuk opsi (memudahkan lookup)
        $masterParents = $master->parent_options()->with('options')->get();

        // Kumpulkan ID master parent & option (berguna untuk pruning di sisi partner)
        $masterParentIds = $masterParents->pluck('id')->all();
        $masterOptionIds = $masterParents->flatMap(fn($p) => $p->options->pluck('id'))->all();

        foreach ($partnerProducts as $pp) {
            // 1) Sinkron field level produk (aman: jangan timpa quantity stok outlet)
            $payload = [
                'name'        => $master->name,
                'category_id' => $master->category_id,
                'promo_id'    => $master->promo_id,
                'description' => $master->description,
            ];

            if ($propagatePrice) {
                $payload['price'] = $master->price;
            }

            // Jika Anda ingin juga menyalin gambar master → uncomment baris berikut
            $payload['pictures'] = $master->pictures;

            $pp->fill($payload)->save();

            if (!$syncOptions) {
                continue; // lewati sinkron opsi jika tidak diminta
            }

            // 2) Sinkron struktur PARENT OPTIONS
            $partnerParents = $pp->parent_options()->get();

            // Hapus parent partner yang tidak ada di master
            $partnerParents
                ->filter(fn($p) => !in_array($p->master_product_parent_option_id, $masterParentIds, true))
                ->each(function ($oldParent) {
                    // hapus dulu child options
                    $oldParent->options()->delete();
                    $oldParent->delete();
                });

            // Upsert parent dari master
            foreach ($masterParents as $mParent) {
                /** @var \App\Models\Partner\Products\PartnerProductParentOption $pParent */
                $pParent = $pp->parent_options()
                    ->firstOrNew(['master_product_parent_option_id' => $mParent->id]);

                $pParent->fill([
                    'name'        => $mParent->name,
                    'description' => $mParent->description,
                    'provision'   => $mParent->provision,
                    'provision_value' => $mParent->provision_value,
                ])->save();

                // 3) Sinkron CHILD OPTIONS untuk parent ini
                $partnerOptions = $pParent->options()->get();

                // Hapus option partner yang tidak ada lagi pada master
                $partnerOptions
                    ->filter(fn($opt) => !in_array($opt->master_product_option_id, $masterOptionIds, true))
                    ->each->delete();

                // Upsert option dari master
                foreach ($mParent->options as $mOpt) {
                    /** @var \App\Models\Partner\Products\PartnerProductOption $pOpt */
                    $pOpt = $pParent->options()
                        ->firstOrNew(['master_product_option_id' => $mOpt->id]);

                    // Amankan harga opsi: jika propagate price = true, ikuti master; jika tidak, pertahankan existing
                    $optionPrice = $propagatePrice
                        ? $mOpt->price
                        : ($pOpt->exists ? $pOpt->price : $mOpt->price);

                    $pOpt->fill([
                        'name'        => $mOpt->name,
                        'price'       => $optionPrice,
                        'description' => $mOpt->description,
                        'pictures'  => $mOpt->pictures,   // uncomment jika ingin menyalin gambar opsi
                        'master_product_option_id' => $mOpt->id,
                    ])->save();
                }
            }
        }
    }



    public function destroy(MasterProduct $master_product)
    {
        // Hapus semua paket terkait
        foreach ($master_product->options as $pkg) {
            $pkg->delete();
        }

        // Hapus semua gambar dari storage
        if (is_array($master_product->pictures)) {
            foreach ($master_product->pictures as $picture) {
                if (!empty($picture['path'])) {
                    // Hapus prefix 'storage/' supaya sesuai disk public
                    $filePath = str_replace('storage/', '', $picture['path']);

                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }
            }
        }

        // Hapus master product
        $master_product->delete();

        return redirect()->route('owner.user-owner.master-products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
