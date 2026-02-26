<?php

namespace App\Http\Controllers\Employee\Staff\Product;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Store\MasterUnit;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Product\MasterProduct;
use App\Models\Product\Promotion;
use App\Models\Admin\Product\Category;
use App\Models\Partner\Products\PartnerProductOptionsRecipe;
use App\Models\Partner\Products\PartnerProductRecipe;
use App\Models\Product\MasterProductOption;
use App\Models\Product\MasterProductParentOption;
use App\Models\Store\Stock;
use App\Models\Store\StockMovement;
use App\Services\StockRecalculationService;
use App\Services\UnitConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class StaffProductController extends Controller
{
    protected StockRecalculationService $recalculationService;

    public function __construct(StockRecalculationService $recalculationService)
    {
        $this->recalculationService = $recalculationService;
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

        $categories = Category::where('owner_id', $context->owner_id)
            ->whereHas('partnerProducts', function ($query) use ($context) {
                $query->where('partner_id', $context->partner_id);
            })
            ->orderBy('category_name')
            ->get();

        $categoryId = $request->get('category');
        $q = trim((string) $request->get('q', ''));

        $productsQuery = PartnerProduct::with(['category', 'promotion', 'stock.displayUnit'])
            ->where('partner_id', $context->partner_id);

        if (!empty($categoryId)) {
            $productsQuery->where('category_id', $categoryId);
        }

        if ($q !== '') {
            $productsQuery->where('name', 'like', "%{$q}%");
        }

        $products = $productsQuery
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        // View disesuaikan dengan struktur folder staff Anda
        return view('pages.employee.staff.products.products.index', compact(
            'categories',
            'products',
            'categoryId',
            'q'
        ));
    }

    /**
     * Menampilkan halaman form untuk membuat produk baru dari nol (Custom Product)
     */
    public function createCustom()
    {
        $context = $this->getContext();

        $categories = Category::where('owner_id', $context->owner_id)->get();
        $promotions = Promotion::where('owner_id', $context->owner_id)->get();

        // Mengembalikan view khusus untuk create custom product di area staff
        return view('pages.employee.staff.products.products.create-custom', compact('categories', 'promotions'));
    }

    /**
     * Memproses penyimpanan produk baru dari nol dan Otomatis membuat data di Master Product, lalu di-copy ke Partner Product.
     */
    public function storeCustom(Request $request)
    {
        $context = $this->getContext();
        $routePrefix = $this->getRoutePrefix();

        DB::beginTransaction();
        try {
            // 1. Validasi Input (Hanya validasi field yang ada di form Blade)
            $validated = $request->validate([
                'name'             => 'required|string|max:255',
                'product_category' => 'required|exists:categories,id', // <-- HARUS product_category (Sesuai Blade)
                'price'            => 'required',
                'description'      => 'nullable|string',
                'images'           => 'nullable|array|max:5',
                'images.*'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
                'promotion_id'     => 'nullable|integer|exists:promotions,id',
                'menu_options'     => 'nullable|array',
            ]);

            // Konversi harga ke angka (hilangkan format titik)
            $price = (int) str_replace('.', '', $validated['price']);

            // Hardcode default value untuk Outlet (Karena tidak ada di UI)
            $stockType = 'direct';
            $alwaysAvailable = false;
            $quantity = 0;
            $isActive = 1;

            // 2. Proses Upload Gambar
            $storedImages = [];
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

            // 3. CREATE MASTER PRODUCT
            $masterProductCode = 'MPD-' . $context->owner_id . '-' . strtoupper(uniqid());

            $masterProduct = MasterProduct::create([
                'product_code' => $masterProductCode,
                'owner_id'     => $context->owner_id,
                'name'         => $request->name,
                'category_id'  => $request->product_category, // <-- Ambil dari product_category
                'quantity'     => 0,
                'price'        => $price,
                'description'  => $request->description,
                'promo_id'     => $request->promotion_id ?? null,
                'pictures'     => !empty($storedImages) ? $storedImages : null,
            ]);

            // 4. CREATE PARTNER PRODUCT (Otomatis ditambahkan ke outlet)
            $partnerProductCode = 'PPD-' . $context->partner_id . '-' . strtoupper(uniqid());

            $partnerProduct = PartnerProduct::create([
                'master_product_id'     => $masterProduct->id,
                'product_code'          => $partnerProductCode,
                'owner_id'              => $context->owner_id,
                'partner_id'            => $context->partner_id,
                'name'                  => $masterProduct->name,
                'category_id'           => $masterProduct->category_id,
                'price'                 => $masterProduct->price,
                'stock_type'            => $stockType,
                'always_available_flag' => $alwaysAvailable ? 1 : 0,
                'pictures'              => $masterProduct->pictures,
                'description'           => $masterProduct->description,
                'promo_id'              => $masterProduct->promo_id,
                'is_active'             => $isActive,
                'is_hot_product'        => 0,
            ]);

            // 5. PROSES MENU OPTIONS
            if ($request->has('menu_options')) {
                foreach ($request->menu_options as $parentOption) {
                    $mParent = MasterProductParentOption::create([
                        'master_product_id'  => $masterProduct->id,
                        'name'               => $parentOption['name'],
                        'description'        => $parentOption['description'] ?? null,
                        'provision'          => $parentOption['provision'],
                        'provision_value'    => $parentOption['provision_value'] ?? 0,
                    ]);

                    $pParent = PartnerProductParentOption::create([
                        'master_product_parent_option_id' => $mParent->id,
                        'partner_product_id'              => $partnerProduct->id,
                        'name'                            => $mParent->name,
                        'description'                     => $mParent->description,
                        'provision'                       => $mParent->provision,
                        'provision_value'                 => $mParent->provision_value ?? 0,
                    ]);

                    if (isset($parentOption['options']) && is_array($parentOption['options'])) {
                        foreach ($parentOption['options'] as $option) {
                            $optionPrice = (int) str_replace('.', '', $option['price']);

                            $mOpt = MasterProductOption::create([
                                'master_product_id'               => $masterProduct->id,
                                'master_product_parent_option_id' => $mParent->id,
                                'name'                            => $option['name'],
                                'quantity'                        => 0,
                                'price'                           => $optionPrice,
                                'description'                     => $option['description'] ?? null,
                            ]);

                            PartnerProductOption::create([
                                'master_product_option_id'         => $mOpt->id,
                                'partner_product_id'               => $partnerProduct->id,
                                'partner_product_parent_option_id' => $pParent->id,
                                'name'                             => $mOpt->name,
                                'stock_type'                       => 'direct',
                                'always_available_flag'            => 0,
                                'price'                            => $mOpt->price,
                                'description'                      => $mOpt->description,
                            ]);
                        }
                    }
                }
            }

            // 6. PROSES STOK (Stock 0 Direct)
            $pcsUnit = MasterUnit::where(function ($query) use ($context) {
                $query->whereNull('owner_id')->orWhere('owner_id', $context->owner_id);
            })
                ->where('is_base_unit', 1)
                ->where(function ($query) {
                    $query->where('unit_name', 'pcs')->orWhere('group_label', 'pcs');
                })->first();

            if ($pcsUnit) {
                Stock::create([
                    'stock_code'              => $this->generateUniqueStockCode(),
                    'owner_id'                => $context->owner_id,
                    'partner_id'              => $context->partner_id,
                    'owner_master_product_id' => $masterProduct->id,
                    'partner_product_id'      => $partnerProduct->id,
                    'display_unit_id'         => $pcsUnit->id,
                    'type'                    => 'partner',
                    'stock_name'              => $partnerProduct->name,
                    'quantity'                => 0, // Default 0
                    'last_price_per_unit'     => 0,
                    'description'             => $partnerProduct->description,
                ]);
            }

            DB::commit();

            return redirect()
                ->route("employee.{$routePrefix}.products.index")
                ->with('success', 'Custom Product successfully created and added to your outlet!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
    public function getMasterProducts(Request $request)
    {
        $context = $this->getContext();

        $request->validate([
            'category_id' => 'required',
        ]);

        $existingProducts = PartnerProduct::where('partner_id', $context->partner_id)
            ->whereNotNull('master_product_id')
            ->pluck('master_product_id')
            ->toArray();

        $list = MasterProduct::query()
            ->where('owner_id', $context->owner_id)
            ->when($request->category_id !== 'all', function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->whereNotIn('id', $existingProducts)
            ->select('id', 'name', 'pictures')
            ->orderBy('name')
            ->get()
            ->map(function ($mp) {
                $pictures = is_array($mp->pictures) ? $mp->pictures : [];
                $pictures = collect($pictures)->map(function ($pic) {
                    $path = is_array($pic) ? ($pic['path'] ?? null) : null;
                    if (!$path) return $pic;

                    if (preg_match('~^https?://~i', $path)) {
                        $pic['url'] = $path;
                    } else {
                        $normalized = ltrim($path, '/');
                        $pic['url'] = asset($normalized);
                    }
                    return $pic;
                })->values()->all();

                $mp->pictures = $pictures;
                $mp->thumb_url = $pictures[0]['url'] ?? null;

                return $mp;
            });

        return response()->json($list);
    }

    public function store(Request $request)
    {
        $context = $this->getContext();

        $validated = $request->validate([
            'category_id'         => 'required',
            'master_product_ids'  => 'required|array|min:1',
            'master_product_ids.*' => 'integer|exists:master_products,id',
            'always_available'    => 'nullable|in:1',
            'stock_type'          => 'required|in:direct,linked',
            'quantity'            => 'nullable|required_if:stock_type,direct|required_unless:always_available,1|integer|min:0',
            'is_active'           => 'required|in:0,1',
        ]);

        $quantity   = (int) ($validated['quantity'] ?? 0);
        $alwaysAvailable = $request->has('always_available') && $request->input('always_available') == '1';
        $stockType  = $validated['stock_type'];
        $isActive   = (int) $validated['is_active'];
        $ids        = collect($validated['master_product_ids'])->map(fn($id) => (int)$id)->unique()->values()->all();

        $defaultPcsUnit = MasterUnit::where(function ($query) use ($context) {
            $query->whereNull('owner_id')->orWhere('owner_id', $context->owner_id);
        })
            ->where('is_base_unit', 1)
            ->where(function ($query) {
                $query->where('unit_name', 'pcs')->orWhere('group_label', 'pcs');
            })
            ->first();

        if (!$defaultPcsUnit) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Setup Error: Unit dasar "pcs" tidak ditemukan.']);
        }

        $defaultPcsUnitId = $defaultPcsUnit->id;
        $pcsConversionValue = (float) $defaultPcsUnit->base_unit_conversion_value;

        $masters = MasterProduct::with('parent_options.options')
            ->where('owner_id', $context->owner_id)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $existingIds = PartnerProduct::where('partner_id', $context->partner_id)
            ->whereIn('master_product_id', $ids)
            ->pluck('master_product_id')
            ->all();

        $toCreate = array_values(array_diff($ids, $existingIds));
        $created  = [];
        $skipped  = $existingIds;
        $routePrefix = $this->getRoutePrefix();

        DB::beginTransaction();
        try {
            foreach ($toCreate as $mid) {
                $master = $masters[$mid] ?? null;
                if (!$master) continue;

                $productCode = 'PPD-' . $context->partner_id . '-' . strtoupper(uniqid());

                $partnerProduct = PartnerProduct::create([
                    'master_product_id' => $master->id,
                    'product_code'      => $productCode,
                    'owner_id'          => $context->owner_id,
                    'partner_id'        => $context->partner_id,
                    'name'              => $master->name,
                    'category_id'       => $master->category_id,
                    'price'             => $master->price,
                    'stock_type'        => $stockType,
                    'always_available_flag' => $alwaysAvailable ? 1 : 0,
                    'pictures'          => $master->pictures,
                    'description'       => $master->description,
                    'promo_id'          => $master->promo_id ?? null,
                    'is_active'         => $isActive,
                ]);

                foreach ($master->parent_options as $mParent) {
                    $pParent = PartnerProductParentOption::create([
                        'master_product_parent_option_id' => $mParent->id,
                        'partner_product_id'              => $partnerProduct->id,
                        'name'                            => $mParent->name,
                        'description'                     => $mParent->description,
                        'provision'                       => $mParent->provision,
                        'provision_value'                 => $mParent->provision_value ?? 0,
                    ]);

                    foreach ($mParent->options as $mOpt) {
                        PartnerProductOption::create([
                            'master_product_option_id'         => $mOpt->id,
                            'partner_product_id'               => $partnerProduct->id,
                            'partner_product_parent_option_id' => $pParent->id,
                            'name'        => $mOpt->name,
                            'stock_type'  => 'direct',
                            'always_available_flag' => 0,
                            'price'       => $mOpt->price,
                            'pictures'    => $mOpt->pictures ?? null,
                            'description' => $mOpt->description,
                        ]);
                    }
                }

                if ($stockType === 'direct') {
                    $baseQuantity = $quantity * $pcsConversionValue;

                    $newStock = Stock::create([
                        'stock_code' => $this->generateUniqueStockCode(),
                        'owner_id'   => $context->owner_id,
                        'partner_id' => $context->partner_id,
                        'owner_master_product_id' => $master->id,
                        'partner_product_id' => $partnerProduct->id,
                        'display_unit_id' => $defaultPcsUnitId,
                        'type'       => 'partner',
                        'stock_name' => $partnerProduct->name,
                        'quantity'   => $baseQuantity,
                        'last_price_per_unit' => 0,
                        'description' => $partnerProduct->description,
                    ]);

                    if ($baseQuantity > 0) {
                        $movement = StockMovement::create([
                            'owner_id'   => $context->owner_id,
                            'partner_id' => $context->partner_id,
                            'type'       => 'in',
                            'category'   => 'Initial Stock',
                        ]);

                        $movement->items()->create([
                            'stock_id'   => $newStock->id,
                            'quantity'   => $baseQuantity,
                            'unit_price' => 0,
                        ]);
                    }
                }

                $created[] = $mid;
            }

            DB::commit();

            $msg = count($created)
                ? 'Product added successfully! (created: ' . count($created) . ', skipped: ' . count($skipped) . ')'
                : 'No new product created (all selected already exist).';

            return redirect()->route("employee.{$routePrefix}.products.index")->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            }

            return redirect()->route("employee.{$routePrefix}.products.index")->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $context = $this->getContext();

        $data = PartnerProduct::with('parent_options.options', 'category')
            ->where('partner_id', $context->partner_id)
            ->findOrFail($id);

        return view('pages.staff.products.show', compact('data'));
    }

    public function edit($id)
    {
        $context = $this->getContext();

        $categories = Category::where('owner_id', $context->owner_id)->get();
        $promotions = Promotion::where('owner_id', $context->owner_id)->get();

        $data = PartnerProduct::with('parent_options.options.stock', 'category', 'stock')
            ->where('partner_id', $context->partner_id)
            ->findOrFail($id);

        $pcsUnit = MasterUnit::where(function ($query) use ($context) {
            $query->where('owner_id', $context->owner_id)->orWhereNull('owner_id');
        })
            ->where(function ($query) {
                $query->where('unit_name', 'pcs')->orWhere('group_label', 'pcs');
            })
            ->where('is_base_unit', 1)
            ->first();

        $pcsUnitId = $pcsUnit ? $pcsUnit->id : null;

        return view('pages.employee.staff.products.products.edit', compact(
            'data',
            'categories',
            'promotions',
            'pcsUnitId'
        ));
    }

    public function update(Request $request, $id)
    {
        $context = $this->getContext();
        $routePrefix = $this->getRoutePrefix();

        DB::beginTransaction();
        try {
            $product = PartnerProduct::with(['parent_options.options.stock', 'stock'])
                ->where('partner_id', $context->partner_id)
                ->findOrFail($id);

            $hasOptions = $product->parent_options
                ->flatMap(fn($parent) => $parent->options ?? collect())
                ->isNotEmpty();

            $rules = [
                'stock_type'       => ['required', 'in:direct,linked'],
                'always_available' => ['nullable', 'in:0,1'],
                'new_quantity'     => ['nullable', 'integer', 'min:0'],
                'current_quantity' => ['nullable', 'integer', 'min:0'],
                'price'            => ['required'],
                'is_active'        => ['required', 'in:0,1'],
                'is_hot_product'   => ['required', 'in:0,1'],
                'promotion_id'     => ['nullable', 'integer', 'exists:promotions,id'],
                'options'          => [$hasOptions ? 'required' : 'sometimes', 'array'],
                'options.*.stock_type'        => ['required', 'in:direct,linked'],
                'options.*.always_available'  => ['nullable', 'in:0,1'],
                'options.*.new_quantity'      => ['nullable', 'integer', 'min:0'],
                'options.*.current_quantity'  => ['nullable', 'integer', 'min:0'],
            ];

            $validator = Validator::make($request->all(), $rules);

            $validator->after(function ($v) use ($request, $product, $hasOptions) {
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

            $rawPrice = preg_replace('/[^0-9]/', '', $validated['price']);
            $price    = $rawPrice !== '' ? (int) $rawPrice : 0;

            $productAlways   = (int)($request->input('always_available', 0)) === 1;
            $newStockType    = $validated['stock_type'];
            $newIsActive     = (int)($validated['is_active'] ?? 0);
            $newIsHotProduct = (int)($validated['is_hot_product'] ?? 0);
            $promotionId     = $request->filled('promotion_id') ? (int)$validated['promotion_id'] : null;
            $newPrice        = $price ?? 0;

            $pcsUnit = MasterUnit::where(function ($query) use ($context) {
                $query->where('owner_id', $context->owner_id)->orWhereNull('owner_id');
            })
                ->where(function ($query) {
                    $query->where('unit_name', 'pcs')->orWhere('group_label', 'pcs');
                })
                ->where('is_base_unit', 1)
                ->first();

            if (!$pcsUnit) {
                throw new \Exception('Setup Error: Unit dasar "pcs" tidak ditemukan.');
            }
            $pcsUnitId = $pcsUnit->id;

            $product->update([
                'is_active'             => $newIsActive,
                'is_hot_product'        => $newIsHotProduct,
                'promo_id'              => $promotionId,
                'stock_type'            => $newStockType,
                'always_available_flag' => $productAlways ? 1 : 0,
                'price'                 => $newPrice,
            ]);

            $this->syncProductStockWithAdjustment(
                $product,
                $newStockType,
                $productAlways,
                (int)($validated['new_quantity'] ?? 0),
                (int)($validated['current_quantity'] ?? 0),
                $pcsUnitId,
                $context
            );

            $this->syncOptionStocks(
                $product,
                $request->input('options', []),
                $pcsUnitId,
                $context
            );

            DB::commit();

            return redirect()->route("employee.{$routePrefix}.products.index")->with('success', 'Product updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    private function syncProductStockWithAdjustment(
        PartnerProduct $product,
        string $newStockType,
        bool $productAlways,
        int $newQuantity,
        int $currentQuantity,
        int $pcsUnitId,
        $context
    ): void {
        if ($newStockType === 'linked') {
            if ($product->stock) {
                $product->stock->delete();
                unset($product->stock);
                $product->load('stock');
            }
            $this->recalculationService->recalculateSingleTarget($product);
            return;
        }

        $existingStock = Stock::where('partner_product_id', $product->id)
            ->whereNull('partner_product_option_id')
            ->first();

        if (!$existingStock) {
            Stock::create([
                'stock_code'              => $this->generateUniqueStockCode(),
                'stock_type'              => 'direct',
                'owner_id'                => $context->owner_id,
                'partner_id'              => $context->partner_id,
                'type'                    => 'partner',
                'stock_name'              => $product->name,
                'quantity'                => $productAlways ? 0 : $newQuantity,
                'display_unit_id'         => $pcsUnitId,
                'owner_master_product_id' => $product->master_product_id,
                'partner_product_id'      => $product->id,
                'last_price_per_unit'     => $product->price,
                'description'             => $product->description,
            ]);
            return;
        }

        // PERBAIKAN: Jika Always Available, HENTIKAN proses. JANGAN reset quantity ke 0.
        if ($productAlways) {
            return;
        }

        $difference = $newQuantity - $currentQuantity;

        if ($difference == 0) {
            return;
        }

        $category = 'stock_adjustment';

        if ($difference > 0) {
            $movement = StockMovement::create([
                'owner_id'   => $context->owner_id,
                'partner_id' => $context->partner_id,
                'type'       => 'in',
                'category'   => $category,
            ]);

            $movement->items()->create([
                'stock_id'   => $existingStock->id,
                'quantity'   => abs($difference),
                'unit_price' => $existingStock->last_price_per_unit
            ]);

            $existingStock->increment('quantity', abs($difference));
        } else {
            if ($existingStock->quantity < abs($difference)) {
                throw new \Exception("Stok '{$existingStock->stock_name}' tidak mencukupi. Tersedia: {$existingStock->quantity} pcs");
            }

            $movement = StockMovement::create([
                'owner_id'   => $context->owner_id,
                'partner_id' => $context->partner_id,
                'type'       => 'out',
                'category'   => $category,
            ]);

            $movement->items()->create([
                'stock_id'   => $existingStock->id,
                'quantity'   => abs($difference),
                'unit_price' => $existingStock->last_price_per_unit
            ]);

            $existingStock->decrement('quantity', abs($difference));
        }

        $this->recalculationService->recalculateLinkedProducts($existingStock);
    }

    private function syncOptionStocks(
        PartnerProduct $product,
        array $optionInputs,
        int $pcsUnitId,
        $context
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

            $optModel->stock_type = $optStockType;
            $optModel->always_available_flag = $optAlways;
            $optModel->save();

            if ($optStockType === 'linked') {
                if ($optModel->stock) {
                    $optModel->stock->delete();
                    unset($optModel->stock);
                    $optModel->load('stock');
                }
                $this->recalculationService->recalculateSingleTarget($optModel);
                continue;
            }

            $newQuantity = (int)($payload['new_quantity'] ?? 0);
            $currentQuantity = (int)($payload['current_quantity'] ?? 0);
            $difference = $newQuantity - $currentQuantity;

            $existingStock = Stock::where('partner_product_option_id', $optModel->id)->first();

            if (!$existingStock) {
                Stock::create([
                    'stock_code'               => $this->generateUniqueStockCode(),
                    'stock_type'               => 'direct',
                    'owner_id'                 => $context->owner_id,
                    'partner_id'               => $context->partner_id,
                    'type'                     => 'partner',
                    'stock_name'               => $product->name . ' - ' . $optModel->name,
                    'quantity'                 => $optAlways ? 0 : $newQuantity,
                    'display_unit_id'          => $pcsUnitId,
                    'owner_master_product_id'  => $product->master_product_id,
                    'partner_product_id'       => $product->id,
                    'partner_product_option_id' => $optModel->id,
                    'last_price_per_unit'      => $optModel->price,
                    'description'              => $optModel->description,
                ]);
                continue;
            }

            // PERBAIKAN: Jika Always Available, HENTIKAN proses. JANGAN reset quantity ke 0.
            if ($optAlways) {
                continue;
            }

            if ($difference == 0) {
                continue;
            }

            $category = 'stock_adjustment';

            if ($difference > 0) {
                $movement = StockMovement::create([
                    'owner_id'   => $context->owner_id,
                    'partner_id' => $context->partner_id,
                    'type'       => 'in',
                    'category'   => $category,
                ]);

                $movement->items()->create([
                    'stock_id'   => $existingStock->id,
                    'quantity'   => abs($difference),
                    'unit_price' => $existingStock->last_price_per_unit
                ]);

                $existingStock->increment('quantity', abs($difference));
            } else {
                if ($existingStock->quantity < abs($difference)) {
                    throw new \Exception("Stok '{$existingStock->stock_name}' tidak mencukupi. Tersedia: {$existingStock->quantity} pcs");
                }

                $movement = StockMovement::create([
                    'owner_id'   => $context->owner_id,
                    'partner_id' => $context->partner_id,
                    'type'       => 'out',
                    'category'   => $category,
                ]);

                $movement->items()->create([
                    'stock_id'   => $existingStock->id,
                    'quantity'   => abs($difference),
                    'unit_price' => $existingStock->last_price_per_unit
                ]);

                $existingStock->decrement('quantity', abs($difference));
            }

            $this->recalculationService->recalculateLinkedProducts($existingStock);
        }
    }

    public function getRecipeIngredients(Request $request)
    {
        $context = $this->getContext();

        $stocks = Stock::with('displayUnit')
            ->where('owner_id', $context->owner_id)
            ->where('partner_id', $context->partner_id)
            ->where('stock_type', 'linked')
            ->get();

        $allUnits = MasterUnit::where(function ($query) use ($context) {
            $query->where('owner_id', $context->owner_id)->orWhereNull('owner_id');
        })->get();

        $data = $stocks->map(function ($stock) use ($allUnits) {
            $currentGroup = $stock->displayUnit->group_label ?? null;

            $compatibleUnits = [];
            if ($currentGroup) {
                $compatibleUnits = $allUnits->where('group_label', $currentGroup)
                    ->values()
                    ->map(fn($u) => [
                        'id' => $u->id,
                        'name' => $u->unit_name,
                        'is_base' => $u->is_base_unit
                    ]);
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
            $itemType = $request->input('item_type');
            $itemId = $request->input('item_id');
            // Validasi keamanannya bisa ditingkatkan dengan memastikan item_id ini milik partner yang login.

            $recipe = [];

            if ($itemType === 'product') {
                $recipe = PartnerProductRecipe::where('partner_product_id', $itemId)
                    ->with(['stock'])
                    ->get()
                    ->map(function ($item) use ($converter) {
                        $baseQuantity = $item->quantity_used;
                        $displayUnitId = $item->display_unit_id;
                        $displayQuantity = $converter->convertToDisplayUnit((float) $baseQuantity, (int) $displayUnitId);

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
                        $displayQuantity = $converter->convertToDisplayUnit((float) $baseQuantity, (int) $displayUnitId);

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
            $context = $this->getContext();

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
                $product = PartnerProduct::where('id', $itemId)
                    ->where('partner_id', $context->partner_id)
                    ->firstOrFail();

                PartnerProductRecipe::where('partner_product_id', $itemId)->delete();

                foreach ($recipeItems as $item) {
                    $convertedQuantity = $converter->convertToBaseUnit($item['quantity'], $item['unit_id']);
                    PartnerProductRecipe::create([
                        'partner_product_id' => $itemId,
                        'stock_id' => $item['stock_id'],
                        'quantity_used' => $convertedQuantity,
                        'display_unit_id' => $item['unit_id'],
                    ]);
                }

                if ($product->stock_type !== 'linked') {
                    $product->update(['stock_type' => 'linked']);
                }
                $recalculator->recalculateSingleTarget($product);
            } elseif ($itemType === 'option') {
                $option = PartnerProductOption::findOrFail($itemId);

                $product = PartnerProduct::where('id', $option->partner_product_id)
                    ->where('partner_id', $context->partner_id)
                    ->firstOrFail();

                PartnerProductOptionsRecipe::where('partner_product_option_id', $itemId)->delete();

                foreach ($recipeItems as $item) {
                    $convertedQuantity = $converter->convertToBaseUnit($item['quantity'], $item['unit_id']);
                    PartnerProductOptionsRecipe::create([
                        'partner_product_option_id' => $itemId,
                        'stock_id' => $item['stock_id'],
                        'quantity_used' => $convertedQuantity,
                        'display_unit_id' => $item['unit_id'],
                    ]);
                }

                if ($option->stock_type !== 'linked') {
                    $option->update(['stock_type' => 'linked']);
                }
                $recalculator->recalculateSingleTarget($option);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Resep berhasil disimpan']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan resep: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $context = $this->getContext();
        $routePrefix = $this->getRoutePrefix();

        $product = PartnerProduct::where('partner_id', $context->partner_id)->findOrFail($id);
        $product->delete();

        return redirect()->route("{$routePrefix}.products.index")->with('success', 'Product deleted successfully!');
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
