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
use Illuminate\Support\Facades\Validator;


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
            if ($request->hasFile('images')) {
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
        $data = PartnerProduct::with('parent_options.options', 'category', 'promotion')
            ->where('partner_id', Auth::id())
            ->where('id', $product->id)
            ->first();

        return view('pages.partner.products.edit', compact('data', 'categories'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Ambil produk beserta options yang dimiliki partner ini
            $product = PartnerProduct::with(['parent_options.options'])->findOrFail($id);

            // ===== Validasi =====
            $rules = [
                // Produk
                'always_available' => ['nullable', 'in:0,1'],
                'quantity'         => ['nullable', 'integer', 'min:0'], // kewajiban dicek di after()

                // Opsi
                'options'                  => ['nullable', 'array'],
                'options.*.always_available' => ['nullable', 'in:0,1'],
                'options.*.quantity'         => ['nullable', 'integer', 'min:0'], // kewajiban dicek di after()
            ];

            $validator = Validator::make($request->all(), $rules);

            // Required-unless untuk product & setiap option
            $validator->after(function ($v) use ($request) {
                // Produk
                $prodAA = (int)$request->input('always_available', 0) === 1;
                if (!$prodAA) {
                    $q = $request->input('quantity', null);
                    if ($q === null || $q === '') {
                        $v->errors()->add('quantity', 'Quantity is required unless product is set to always available.');
                    }
                }

                // Opsi
                foreach ((array)$request->input('options', []) as $oid => $opt) {
                    $oa = (int)($opt['always_available'] ?? 0) === 1;
                    if (!$oa) {
                        if (!array_key_exists('quantity', $opt) || $opt['quantity'] === '' || $opt['quantity'] === null) {
                            $v->errors()->add("options.$oid.quantity", 'Quantity is required unless this option is set to always available.');
                        }
                    }
                }
            });

            $validated = $validator->validate();

            // ===== Normalisasi & Update Produk =====
            $productAlways = (int)$request->input('always_available', 0) === 1;
            $newQuantity   = $productAlways ? 0 : (int)($validated['quantity'] ?? 0);

            // Hanya update stok; kolom lain dibiarkan
            $product->update([
                'quantity' => $newQuantity,
                // Jika punya kolom always_available_flag, kamu bisa aktifkan baris ini:
                'always_available_flag' => $productAlways ? 1 : 0,
            ]);

            // ===== Update tiap Option (jika ada input options) =====
            $optionInputs = $request->input('options', []);
            if (!empty($optionInputs)) {
                // Ambil hanya option milik produk ini
                $optionIds = array_map('intval', array_keys($optionInputs));

                $options = PartnerProductOption::whereIn('id', $optionIds)
                    ->where('partner_product_id', $product->id)
                    ->get()
                    ->keyBy('id');

                foreach ($optionInputs as $optId => $payload) {
                    $optId = (int)$optId;
                    if (!isset($options[$optId])) continue;

                    $optAlways = (int)($payload['always_available'] ?? 0) === 1;
                    $optQty    = $optAlways ? 0 : (int)($payload['quantity'] ?? 0);

                    $options[$optId]->update([
                        'quantity' => $optQty,
                        // Jika punya kolom always_available_flag, aktifkan baris ini:
                        'always_available_flag' => $optAlways ? 1 : 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('partner.products.index')->with('success', 'Stock updated successfully!');
        } catch (\Throwable $e) {
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
