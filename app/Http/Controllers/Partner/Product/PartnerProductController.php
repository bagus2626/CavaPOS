<?php

namespace App\Http\Controllers\Partner\Product;

use App\Http\Controllers\Controller;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Admin\Product\Category;
use App\Models\Partner\Products\PartnerProductOptionsRecipe;
use App\Models\Partner\Products\PartnerProductRecipe;
use App\Models\Store\MasterUnit;
use App\Models\Store\Stock;
use App\Models\Store\StockMovement;
use App\Services\StockRecalculationService;
use App\Services\UnitConversionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PartnerProductController extends Controller
{

    protected $recalculationService;

    public function __construct(StockRecalculationService $recalculationService)
    {
        $this->recalculationService = $recalculationService;
    }

    public function index(Request $request)
    {
        $partnerId = Auth::id();
        $ownerId   = Auth::user()->owner_id;

        $categories = Category::where('owner_id', $ownerId)->get();

        $categoryId = $request->query('category');

        $productsQuery = PartnerProduct::with('parent_options.options', 'category', 'stock')
            ->where('partner_id', $partnerId);

        if ($categoryId && $categoryId !== 'all') {
            $productsQuery->where('category_id', $categoryId);
        }

        // get() untuk load semua data tanpa pagination
        $allProducts = $productsQuery->orderBy('name')->get();

        // Format data untuk JavaScript dengan computed properties
        $allProductsFormatted = $allProducts->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'category' => $product->category,
                'parent_options' => $product->parent_options,
                'stock_type' => $product->stock_type,
                'always_available_flag' => $product->always_available_flag,
                'quantity_available' => $product->quantity_available, // accessor akan dipanggil
                'price' => $product->price,
                'pictures' => $product->pictures,
                'stock' => $product->stock,
            ];
        });

        // Simulasi pagination object untuk compatibility dengan view
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $allProducts->slice($offset, $perPage)->values(),
            $allProducts->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Kirim semua data untuk JavaScript filter
        return view('pages.partner.products.index', compact(
            'products',
            'categories',
            'categoryId',
            'allProductsFormatted'
        ));
    }

    public function create()
    {
        abort(404); // disable for now
        $categories = Category::where('partner_id', Auth::id())->get();
        return view('pages.partner.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
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
        $partner = Auth::user();

        // Eager Load: Menerapkan filter stok dan memuat resep
        $data = PartnerProduct::with([
            'parent_options.options.stock' => fn($q) => $q->where('partner_id', $partner->id),
            'parent_options.options.recipes.stock.displayUnit',
            'category',
            // Stock utama produk
            'stock' => fn($q) => $q->where('partner_id', $partner->id),
            // Resep utama produk
            'recipes.stock.displayUnit',
        ])
            ->where('partner_id', $partner->id)
            ->where('id', $product->id)
            ->firstOrFail(); // Gunakan firstOrFail untuk konsistensi

        // Memuat ulang data opsi setelah filter (penting)
        $data->load(['parent_options.options.stock', 'parent_options.options.recipes']);

        $categories = Category::where('partner_id', $partner->id)->get();

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

    public function getRecipeIngredients()
    {
        $partner = Auth::user();
        $owner_id = $partner->owner_id;

        // 1. Ambil Stock (Linked & milik partner)
        $stocks = Stock::with('displayUnit')
            ->where('partner_id', $partner->id)
            ->where('stock_type', 'linked') // Sesuai permintaan
            ->get();

        // 2. Ambil Semua Unit yang tersedia untuk owner ini
        $allUnits = MasterUnit::where(function ($query) use ($owner_id) {
            $query->where('owner_id', $owner_id)->orWhereNull('owner_id');
        })->get();

        // 3. Mapping data untuk JSON response
        $data = $stocks->map(function ($stock) use ($allUnits) {
            // Ambil group label dari unit stock saat ini
            $currentGroup = $stock->displayUnit->group_label ?? null;

            // Filter unit yang satu grup (jika group label ada)
            $compatibleUnits = [];
            if ($currentGroup) {
                $compatibleUnits = $allUnits->where('group_label', $currentGroup)
                    ->values()
                    ->map(function ($u) {
                        return [
                            'id' => $u->id,
                            'name' => $u->unit_name,
                            'is_base' => $u->is_base_unit
                        ];
                    });
            }

            return [
                'id' => $stock->id,
                'name' => $stock->stock_name,
                'current_unit_id' => $stock->display_unit_id,
                'current_unit_name' => $stock->displayUnit->unit_name ?? '-',
                'available_units' => $compatibleUnits
            ];
        });

        return response()->json($data);
    }

    public function loadRecipe(Request $request, UnitConversionService $converter)
    {
        try {
            $itemType = $request->input('item_type'); // 'product' or 'option'
            $itemId = $request->input('item_id');

            $recipe = [];

            if ($itemType === 'product') {
                $recipe = PartnerProductRecipe::where('partner_product_id', $itemId)
                    ->with(['stock'])
                    ->get()
                    ->map(function ($item) use ($converter) {

                        $baseQuantity = $item->quantity_used;
                        $displayUnitId = $item->display_unit_id;

                        $displayQuantity = $converter->convertToDisplayUnit(
                            (float) $baseQuantity,
                            (int) $displayUnitId
                        );

                        return [
                            'stock_id' => $item->stock_id,
                            'quantity_used' => number_format($displayQuantity, 2, '.', ''),
                            'unit_id' => $displayUnitId,
                            'stock_name' => $item->stock->stock_name,
                        ];
                    });
            } elseif ($itemType === 'option') {
                $recipe = PartnerProductOptionsRecipe::where('partner_product_option_id', $itemId)
                    ->with(['stock'])
                    ->get()
                    ->map(function ($item) use ($converter) {

                        $baseQuantity = $item->quantity_used;
                        $displayUnitId = $item->display_unit_id;

                        $displayQuantity = $converter->convertToDisplayUnit(
                            (float) $baseQuantity,
                            (int) $displayUnitId
                        );

                        return [
                            'stock_id' => $item->stock_id,
                            'quantity_used' => number_format($displayQuantity, 2, '.', ''),
                            'unit_id' => $displayUnitId,
                            'stock_name' => $item->stock->stock_name,
                        ];
                    });
            }

            return response()->json([
                'success' => true,
                'recipe' => $recipe
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load recipe'], 500);
        }
    }

    public function saveRecipe(Request $request, UnitConversionService $converter, StockRecalculationService $recalculator)
    {
        try {
            $request->validate([
                'item_type' => 'required|in:product,option',
                'item_id' => 'required|integer',
                'recipe_items' => 'required|array|min:1',
                'recipe_items.*.stock_id' => 'required|integer|exists:stocks,id',
                'recipe_items.*.quantity' => 'required|numeric|min:0.01',
                'recipe_items.*.unit_id' => 'required|integer|exists:master_units,id',
            ]);

            $itemType = $request->input('item_type');
            $itemId = $request->input('item_id');
            $recipeItems = $request->input('recipe_items');

            DB::beginTransaction();

            if ($itemType === 'product') {
                // Verify product exists and belongs to user
                $product = PartnerProduct::where('id', $itemId)
                    ->where('owner_id', auth()->user()->owner_id)
                    ->firstOrFail();

                // Delete existing recipe
                PartnerProductRecipe::where('partner_product_id', $itemId)->delete();

                // Insert new recipe items
                foreach ($recipeItems as $item) {
                    // Convert quantity to base unit if needed
                    $convertedQuantity = $converter->convertToBaseUnit(
                        $item['quantity'],
                        $item['unit_id']
                    );

                    PartnerProductRecipe::create([
                        'partner_product_id' => $itemId,
                        'stock_id' => $item['stock_id'],
                        'quantity_used' => $convertedQuantity,
                        'display_unit_id' => $item['unit_id'],
                    ]);
                }

                // Update product stock_type to 'linked' if not already
                if ($product->stock_type !== 'linked') {
                    $product->update(['stock_type' => 'linked']);
                }
                $recalculator->recalculateSingleTarget($product);
            } elseif ($itemType === 'option') {
                // Verify option exists
                $option = PartnerProductOption::findOrFail($itemId);

                // Verify the option belongs to a product owned by the user
                $product = PartnerProduct::where('id', $option->partner_product_id)
                    ->where('owner_id', auth()->user()->owner_id)
                    ->firstOrFail();

                // Delete existing recipe
                PartnerProductOptionsRecipe::where('partner_product_option_id', $itemId)->delete();

                // Insert new recipe items
                foreach ($recipeItems as $item) {
                    $convertedQuantity = $converter->convertToBaseUnit(
                        $item['quantity'],
                        $item['unit_id']
                    );

                    PartnerProductOptionsRecipe::create([
                        'partner_product_option_id' => $itemId,
                        'stock_id' => $item['stock_id'],
                        'quantity_used' => $convertedQuantity,
                        'display_unit_id' => $item['unit_id'],
                    ]);
                }

                // Update option stock_type to 'linked'
                if ($option->stock_type !== 'linked') {
                    $option->update(['stock_type' => 'linked']);
                }

                $recalculator->recalculateSingleTarget($option);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Resep berhasil disimpan'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan resep: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $partner = Auth::user();
        $owner_id = $partner->owner_id;

        DB::beginTransaction();
        try {
            $product = PartnerProduct::with(['stock', 'parent_options.options.stock'])
                ->where('partner_id', $partner->id)
                ->findOrFail($id);

            $hasOptions = $product->parent_options
                ->flatMap(fn($parent) => $parent->options ?? collect())
                ->isNotEmpty();

            // VALIDASI
            $rules = [
                'stock_type'       => ['required', 'in:direct,linked'],
                'always_available' => ['nullable', 'in:0,1'],
                'new_quantity'     => ['nullable', 'integer', 'min:0'], // UBAH: dari quantity
                'current_quantity' => ['nullable', 'integer', 'min:0'], // TAMBAH
                'options'          => [$hasOptions ? 'required' : 'sometimes', 'array'],
                'options.*.stock_type'        => ['required', 'in:direct,linked'],
                'options.*.always_available'  => ['nullable', 'in:0,1'],
                'options.*.new_quantity'      => ['nullable', 'integer', 'min:0'], // UBAH
                'options.*.current_quantity'  => ['nullable', 'integer', 'min:0'], // TAMBAH
                'is_active'    => ['nullable', 'in:0,1'],
                'promotion_id' => ['nullable', 'integer'],
            ];

            $validator = Validator::make($request->all(), $rules);

            // VALIDASI CUSTOM
            $validator->after(function ($v) use ($request, $hasOptions) {
                // Validasi Product
                $prodStockType = $request->input('stock_type');
                if ($prodStockType === 'direct') {
                    $prodAA = (int)$request->input('always_available', 0) === 1;
                    if (!$prodAA) {
                        $q = $request->input('new_quantity', null);
                        if ($q === null || $q === '') {
                            $v->errors()->add('new_quantity', 'New quantity is required unless product is set to always available.');
                        }
                    }
                }

                // Validasi Option
                if ($hasOptions) {
                    foreach ((array)$request->input('options', []) as $oid => $opt) {
                        $optStockType = $opt['stock_type'] ?? 'direct';
                        if ($optStockType === 'direct') {
                            $oa = (int)($opt['always_available'] ?? 0) === 1;
                            if (!$oa) {
                                if (!array_key_exists('new_quantity', $opt) || $opt['new_quantity'] === '' || $opt['new_quantity'] === null) {
                                    $v->errors()->add("options.$oid.new_quantity", 'New quantity is required unless this option is set to always available.');
                                }
                            }
                        }
                    }
                }
            });

            $validated = $validator->validate();

            $pcsUnitId = $this->getPcsUnitId($owner_id);
            $productAlways = (int)($request->input('always_available', 0)) === 1;
            $newStockType  = $request->input('stock_type');

            // Update Product
            $product->update([
                'promo_id'              => $request->filled('promotion_id') ? (int) $validated['promotion_id'] : null,
                'stock_type'            => $newStockType,
                'always_available_flag' => $productAlways,
            ]);

            // Sync Stocks with Adjustment Logic
            $this->syncProductStockWithAdjustment(
                $product,
                $partner,
                $newStockType,
                $productAlways,
                (int)($validated['new_quantity'] ?? 0),
                (int)($validated['current_quantity'] ?? 0),
                $pcsUnitId
            );

            $this->syncOptionStocksWithAdjustment(
                $product,
                $partner,
                $request->input('options', []),
                $pcsUnitId
            );

            DB::commit();

            return redirect()
                ->route('partner.products.index')
                ->with('success', 'Product updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // ===== SYNC PRODUCT STOCK WITH ADJUSTMENT =====
    private function syncProductStockWithAdjustment(
        PartnerProduct $product,
        $partner,
        string $newStockType,
        bool $productAlways,
        int $newQuantity,
        int $currentQuantity,
        int $pcsUnitId
    ): void {
        // LINKED
        if ($newStockType === 'linked') {
            if ($product->stock) {
                $product->stock->delete();
                unset($product->stock);
                $product->load('stock');
            }
            $this->recalculationService->recalculateSingleTarget($product);
            return;
        }

        // DIRECT with Adjustment
        $existingStock = Stock::where('partner_product_id', $product->id)
            ->whereNull('partner_product_option_id')
            ->first();

        $difference = $newQuantity - $currentQuantity;

        // Jika belum ada stok, buat baru
        if (!$existingStock) {
            Stock::create([
                'stock_code'              => $this->generateUniqueStockCode(),
                'stock_type'              => 'direct',
                'owner_id'                => $partner->owner_id,
                'partner_id'              => $partner->id,
                'type'                    => 'partner',
                'stock_name'              => $product->name,
                'quantity'                => $productAlways ? 0 : $newQuantity,
                'display_unit_id'         => $pcsUnitId,
                'partner_product_id'      => $product->id,
                'last_price_per_unit'     => $product->price,
            ]);
            return;
        }

        // Always Available
        if ($productAlways) {
            $existingStock->quantity = $existingStock->quantity_reserved ?? 0;
            $existingStock->save();
            return;
        }

        // Tidak ada perubahan
        if ($difference == 0) {
            return;
        }

        // Buat stock movement
        $category = 'stock_adjustment';

        if ($difference > 0) {
            // PENAMBAHAN (IN)
            $movement = StockMovement::create([
                'owner_id' => $partner->owner_id,
                'partner_id' => $partner->id,
                'type' => 'in',
                'category' => $category,
            ]);

            $movement->items()->create([
                'stock_id' => $existingStock->id,
                'quantity' => abs($difference),
                'unit_price' => $existingStock->last_price_per_unit
            ]);

            $existingStock->increment('quantity', abs($difference));
        } else {
            // PENGURANGAN (OUT)
            if ($existingStock->quantity < abs($difference)) {
                throw new \Exception(
                    "Stok '{$existingStock->stock_name}' tidak mencukupi. " .
                        "Tersedia: {$existingStock->quantity} pcs"
                );
            }

            $movement = StockMovement::create([
                'owner_id' => $partner->owner_id,
                'partner_id' => $partner->id,
                'type' => 'out',
                'category' => $category,
            ]);

            $movement->items()->create([
                'stock_id' => $existingStock->id,
                'quantity' => abs($difference),
                'unit_price' => $existingStock->last_price_per_unit
            ]);

            $existingStock->decrement('quantity', abs($difference));
        }

        $this->recalculationService->recalculateLinkedProducts($existingStock);
    }

    private function syncOptionStocksWithAdjustment(
        PartnerProduct $product,
        $partner,
        array $optionInputs,
        int $pcsUnitId
    ): void {
        if (empty($optionInputs)) return;

        $optionIds = array_map('intval', array_keys($optionInputs));
        $options = PartnerProductOption::with('stock')
            ->whereIn('id', $optionIds)
            ->where('partner_product_id', $product->id)
            ->get()
            ->keyBy('id');

        foreach ($optionInputs as $optId => $payload) {
            $optId = (int)$optId;
            if (!$options->has($optId)) continue;

            $optModel = $options[$optId];
            $optStockType = $payload['stock_type'] ?? 'direct';
            $optAlways = (int)($payload['always_available'] ?? 0) === 1;

            // Update model
            $optModel->stock_type = $optStockType;
            $optModel->always_available_flag = $optAlways;
            $optModel->save();

            // LINKED
            if ($optStockType === 'linked') {
                if ($optModel->stock) {
                    $optModel->stock->delete();
                    unset($optModel->stock);
                    $optModel->load('stock');
                }
                $this->recalculationService->recalculateSingleTarget($optModel);
                continue;
            }

            // DIRECT with Adjustment
            $newQuantity = (int)($payload['new_quantity'] ?? 0);
            $currentQuantity = (int)($payload['current_quantity'] ?? 0);
            $difference = $newQuantity - $currentQuantity;

            $existingStock = Stock::where('partner_product_option_id', $optModel->id)->first();

            // Belum ada stok
            if (!$existingStock) {
                Stock::create([
                    'stock_code' => $this->generateUniqueStockCode(),
                    'stock_type' => 'direct',
                    'owner_id'   => $partner->owner_id,
                    'partner_id' => $partner->id,
                    'type'       => 'partner',
                    'stock_name' => $product->name . ' - ' . $optModel->name,
                    'quantity'   => $optAlways ? 0 : $newQuantity,
                    'display_unit_id' => $pcsUnitId,
                    'partner_product_id' => $product->id,
                    'partner_product_option_id' => $optModel->id,
                    'last_price_per_unit' => $optModel->price,
                ]);
                continue;
            }

            // Always Available
            if ($optAlways) {
                $existingStock->quantity = $existingStock->quantity_reserved ?? 0;
                $existingStock->save();
                continue;
            }

            // Tidak ada perubahan
            if ($difference == 0) {
                continue;
            }

            // Buat movement
            $category = 'stock_adjustment';
            $itemName = $product->name . ' - ' . $optModel->name;

            if ($difference > 0) {
                // PENAMBAHAN
                $movement = StockMovement::create([
                    'owner_id' => $partner->owner_id,
                    'partner_id' => $partner->id,
                    'type' => 'in',
                    'category' => $category,

                ]);

                $movement->items()->create([
                    'stock_id' => $existingStock->id,
                    'quantity' => abs($difference),
                    'unit_price' => $existingStock->last_price_per_unit
                ]);

                $existingStock->increment('quantity', abs($difference));
            } else {
                // PENGURANGAN
                if ($existingStock->quantity < abs($difference)) {
                    throw new \Exception(
                        "Stok '{$existingStock->stock_name}' tidak mencukupi. " .
                            "Tersedia: {$existingStock->quantity} pcs"
                    );
                }

                $movement = StockMovement::create([
                    'owner_id' => $partner->owner_id,
                    'partner_id' => $partner->id,
                    'type' => 'out',
                    'category' => $category,
                ]);

                $movement->items()->create([
                    'stock_id' => $existingStock->id,
                    'quantity' => abs($difference),
                    'unit_price' => $existingStock->last_price_per_unit
                ]);

                $existingStock->decrement('quantity', abs($difference));
            }

            $this->recalculationService->recalculateLinkedProducts($existingStock);
        }
    }

    private function getPcsUnitId(int $ownerId): int
    {
        $pcsUnit = MasterUnit::where(function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId)->orWhereNull('owner_id');
        })
            ->where(function ($query) {
                $query->where('unit_name', 'pcs')->orWhere('group_label', 'pcs');
            })
            ->where('is_base_unit', 1)
            ->first();

        if (!$pcsUnit) {
            throw new \Exception('Setup Error: Unit dasar "pcs" tidak ditemukan.');
        }

        return $pcsUnit->id;
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
