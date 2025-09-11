<?php

namespace App\Http\Controllers\Partner\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Product\Specification;
use App\Models\Admin\Product\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PartnerProductController extends Controller
{
    public function index()
    {
        $categories = Category::where('partner_id', Auth::id())->get();
        $products = PartnerProduct::with('parent_options.options', 'category')
            ->where('partner_id', Auth::id())
            ->get();
        return view('pages.partner.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('partner_id', Auth::id())->get();
        return view('pages.partner.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // dd($request->all());
            // Validasi request
            $validated = $request->validate([
                'name'             => 'required|string|max:255',
                'product_category' => 'required|exists:categories,id',
                'quantity'         => 'required',
                'price'            => 'required',
                'description'      => 'nullable|string',
                'images'           => 'nullable|array|max:5',
                'images.*'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'options'          => 'nullable|array',
            ], [
                'name.required'             => 'Nama produk wajib diisi.',
                'name.string'               => 'Nama produk harus berupa teks.',
                'product_category.required' => 'Kategori produk wajib dipilih.',
                'product_category.exists'   => 'Kategori produk tidak valid.',
                'quantity.required'         => 'Jumlah produk wajib diisi.',
                'price.required'            => 'Harga produk wajib diisi.',
                'images.array'              => 'Gambar harus dalam bentuk array.',
                'images.*.string'           => 'Setiap gambar harus berupa string path/url.',
                'options.array'             => 'Options harus dalam bentuk array.',
            ]);

            // Konversi harga ke angka (hilangkan titik)
            $price = (int) str_replace('.', '', $validated['price']);
            // Simpan produk utama
            $productCode = 'PRD-' . Auth::id() . '-' . strtoupper(uniqid());

            $storedImages = [];
            if($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('uploads/products', $filename, 'public');

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

            $product = PartnerProduct::create([
                'product_code' => $productCode,
                'partner_id'   => Auth::id(), // pastikan ini valid
                'name'         => $request->name,
                'category_id'  => $request->product_category,
                'quantity'     => $request->quantity,
                'price'        => $price,
                'description'  => $request->description,
                'pictures'     => $storedImages ?? null,
            ]);

            if ($request->has('menu_options')) {
                foreach ($request->menu_options as $parentOption) {
                    $parent = PartnerProductParentOption::create([
                        'partner_product_id' => $product->id,
                        'name'               => $parentOption['name'],
                        'description'        => $parentOption['description'] ?? null,
                        'provision'          => $parentOption['provision'],
                        'provision_value'    => $parentOption['provision_value'] ?? null,
                    ]);

                    if (isset($parentOption['options']) && is_array($parentOption['options'])) {
                        foreach ($parentOption['options'] as $option) {
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

                            PartnerProductOption::create([
                                'partner_product_id' => $product->id,
                                'partner_product_parent_option_id' => $parent->id,
                                'name'        => $option['name'],
                                'quantity'    => $option['quantity'],
                                'price'       => $optionPrice,
                                'description' => $option['description'] ?? null,
                                'pictures'    => $optionImages,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('partner.products.index')
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

    public function edit(PartnerProduct $product)
    {
        $categories = Category::where('partner_id', Auth::id())->get();
        $data = PartnerProduct::with('parent_options.options', 'category')
            ->where('partner_id', Auth::id())
            ->where('id', $product->id)
            ->first();

        return view('pages.partner.products.edit', compact('data', 'categories'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $product = PartnerProduct::with(['parent_options.options'])->findOrFail($id);
            // dd($request->all());
            // Validasi
            $validated = $request->validate([
                'name'             => 'required|string|max:255',
                'product_category' => 'required|exists:categories,id',
                'quantity'         => 'required',
                'price'            => 'required',
                'description'      => 'nullable|string',
                'images'           => 'nullable|array|max:5',
                'images.*'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'existing_images'  => 'nullable|array',
                'menu_options'     => 'nullable|array',
            ]);

            // Konversi harga ke integer
            $price = (int) str_replace('.', '', $validated['price']);

            // Handle existing images
            $storedImages = [];
            $existingFilenames = $request->existing_images ?? [];

            // Hapus gambar yang sudah dihapus
            foreach ($product->pictures as $pic) {
                if (!in_array($pic['filename'], $existingFilenames)) {
                    if (Storage::disk('public')->exists('uploads/products/' . $pic['filename'])) {
                        Storage::disk('public')->delete('uploads/products/' . $pic['filename']);
                    }
                } else {
                    $storedImages[] = $pic;
                }
            }

            // Upload gambar baru
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('uploads/products', $filename, 'public');

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
                'quantity'    => $request->quantity,
                'price'       => $price,
                'description' => $request->description,
                'pictures'    => $storedImages,
            ]);

            // Hapus semua parent & option yang sudah dihapus
            // $existingParentIds = $request->has('menu_options')
            //     ? collect($request->menu_options)->pluck('parent_id')->filter()->map(fn($id) => (int) $id)->toArray()
            //     : [];
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
                        $parentModel = PartnerProductParentOption::findOrFail($parent);
                        $parentModel->update([
                            'name'              => $parentOption['name'],
                            'description'       => $parentOption['description'] ?? null,
                            'provision'         => $parentOption['provision'],
                            'provision_value'   => $parentOption['provision_value'] ?? null
                        ]);
                    } else {
                        // Create new parent
                        $parentModel = PartnerProductParentOption::create([
                            'partner_product_id' => $product->id,
                            'name'               => $parentOption['name'],
                            'description'        => $parentOption['description'] ?? null,
                            'provision'          => $parentOption['provision'],
                            'provision_value'    => $parentOption['provision_value'] ?? null
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
                                $optionModel = PartnerProductOption::find($optionId);
                                $optionModel->update([
                                    'name'        => $option['name'],
                                    'quantity'    => $option['quantity'],
                                    'price'       => $optionPrice,
                                    'description' => $option['description'] ?? null,
                                    'pictures'    => $optionImages ?? $optionModel->pictures,
                                ]);
                            } else {
                                // Create new option
                                PartnerProductOption::create([
                                    'partner_product_id' => $product->id,
                                    'partner_product_parent_option_id' => $parentModel->id,
                                    'name'        => $option['name'],
                                    'quantity'    => $option['quantity'],
                                    'price'       => $optionPrice,
                                    'description' => $option['description'] ?? null,
                                    'pictures'    => $optionImages,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('partner.products.index')->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }


    public function destroy(PartnerProduct $product)
    {
        // Hapus semua paket dan spesifikasi terkait
        foreach ($product->options as $pkg) {
            $pkg->delete();
        }

        $product->delete();

        return redirect()->route('partner.products.index')->with('success', 'Product deleted successfully!');
    }

}
