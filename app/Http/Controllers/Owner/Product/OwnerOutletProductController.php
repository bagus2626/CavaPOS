<?php

namespace App\Http\Controllers\Owner\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Store\MasterUnit;
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
use App\Models\Partner\Products\PartnerProductOptionsRecipe;
use App\Models\Partner\Products\PartnerProductRecipe;
use App\Models\Store\Stock;
use App\Models\Store\StockMovement;
use App\Services\StockRecalculationService;
use App\Services\UnitConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;


class OwnerOutletProductController extends Controller
{

    protected StockRecalculationService $recalculationService;
    public function __construct(StockRecalculationService $recalculationService)
    {
        $this->recalculationService = $recalculationService;
    }

    public function index(Request $request)
    {
        $owner = Auth::user();

        $outlets = User::where('owner_id', $owner->id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $currentOutletId = $request->get('outlet_id') ?? ($outlets->first()->id ?? null);

        if ($currentOutletId) {
            $categories = Category::where('owner_id', $owner->id)
                ->whereHas('partnerProducts', function ($query) use ($currentOutletId) {
                    $query->where('partner_id', $currentOutletId);
                })
                ->orderBy('category_name')
                ->get();
        } else {
            $categories = collect(); // Empty collection jika tidak ada outlet
        }

        $categoryId = $request->get('category');
        $q = trim((string) $request->get('q', ''));

        $productsQuery = PartnerProduct::with(['category', 'promotion', 'stock.displayUnit'])
            ->where('owner_id', $owner->id);

        if ($currentOutletId) {
            $productsQuery->where('partner_id', $currentOutletId);
        } else {
            $products = PartnerProduct::whereRaw('1=0')->paginate(10);
            return view('pages.owner.products.outlet-product.index', compact(
                'outlets',
                'categories',
                'products',
                'currentOutletId',
                'categoryId',
                'q'
            ));
        }

        // category filter
        if (!empty($categoryId)) {
            $productsQuery->where('category_id', $categoryId);
        }

        // search filter
        if ($q !== '') {
            $productsQuery->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%");
            });
        }

        // paginate
        $products = $productsQuery
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        return view('pages.owner.products.outlet-product.index', compact(
            'outlets',
            'categories',
            'products',
            'currentOutletId',
            'categoryId',
            'q'
        ));
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
            'outlet_id'   => 'required|integer|exists:users,id',
        ]);

        // Get existing outlet products to exclude
        $existingProducts = PartnerProduct::where('partner_id', $request->outlet_id)
            ->whereNotNull('master_product_id')
            ->pluck('master_product_id')
            ->toArray();

        $ownerId = Auth::id();

        // Query master products
        $list = MasterProduct::query()
            ->where('owner_id', $ownerId)
            ->when($request->category_id !== 'all', function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->whereNotIn('id', $existingProducts)
            ->select('id', 'name', 'pictures')
            ->orderBy('name')
            ->get()
            ->map(function ($mp) {
                // Process pictures
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
        $owner = Auth::user();

        $validated = $request->validate([
            'outlet_id'           => 'required|integer',
            'category_id'         => 'required',
            'master_product_ids'  => 'required|array|min:1',
            'master_product_ids.*' => 'integer|exists:master_products,id',
            'always_available'     => 'nullable|in:1',
            'stock_type'          => 'required|in:direct,linked',
            'quantity'            => 'nullable|required_if:stock_type,direct|required_unless:always_available,1|integer|min:0',
            'is_active'           => 'required|in:0,1',
        ]);

        $outletId   = (int) $validated['outlet_id'];
        $quantity   = (int) ($validated['quantity'] ?? 0);
        $alwaysAvailable = $request->has('always_available') && $request->input('always_available') == '1';
        $stockType  = $validated['stock_type'];
        $isActive   = (int) $validated['is_active'];
        $ids        = collect($validated['master_product_ids'])->map(fn($id) => (int)$id)->unique()->values()->all();

        // Cek Unit 'pcs'
        $defaultPcsUnit = MasterUnit::where(function ($query) use ($owner) {
            $query->whereNull('owner_id')->orWhere('owner_id', $owner->id);
        })
            ->where('is_base_unit', 1)
            ->where(function ($query) {
                $query->where('unit_name', 'pcs')
                    ->orWhere('group_label', 'pcs');
            })
            ->first();

        if (!$defaultPcsUnit) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Setup Error: Unit dasar "pcs" tidak ditemukan.']);
        }

        $defaultPcsUnitId = $defaultPcsUnit->id;
        $pcsConversionValue = (float) $defaultPcsUnit->base_unit_conversion_value;

        $masters = MasterProduct::with('parent_options.options')
            ->where('owner_id', $owner->id)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $existingIds = PartnerProduct::where('partner_id', $outletId)
            ->whereIn('master_product_id', $ids)
            ->pluck('master_product_id')
            ->all();

        $toCreate = array_values(array_diff($ids, $existingIds));
        $created  = [];
        $skipped  = $existingIds;

        DB::beginTransaction();
        try {
            foreach ($toCreate as $mid) {
                $master = $masters[$mid] ?? null;
                if (!$master) {
                    continue;
                }

                $productCode = 'PPD-' . $outletId . '-' . strtoupper(uniqid());

                $partnerProduct = PartnerProduct::create([
                    'master_product_id' => $master->id,
                    'product_code'      => $productCode,
                    'owner_id'          => $owner->id,
                    'partner_id'        => $outletId,
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
                            'master_product_option_id'           => $mOpt->id,
                            'partner_product_id'                 => $partnerProduct->id,
                            'partner_product_parent_option_id'   => $pParent->id,
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

                    $baseUnitPrice = 0;

                    $newStock = Stock::create([
                        'stock_code' => $this->generateUniqueStockCode(),
                        'owner_id'   => $owner->id,
                        'partner_id' => $outletId,
                        'owner_master_product_id' => $master->id,
                        'partner_product_id' => $partnerProduct->id,
                        'display_unit_id' => $defaultPcsUnitId,
                        'type'       => 'partner',
                        'stock_name' => $partnerProduct->name,
                        'quantity'   => $baseQuantity,
                        'last_price_per_unit' => $baseUnitPrice,
                        'description' => $partnerProduct->description,
                    ]);


                    if ($baseQuantity > 0) {
                        $movement = StockMovement::create([
                            'owner_id'   => $owner->id,
                            'partner_id' => $outletId,
                            'type'       => 'in',
                            'category'   => 'Initial Stock',
                        ]);

                        $movement->items()->create([
                            'stock_id'   => $newStock->id,
                            'quantity'   => $baseQuantity,
                            'unit_price' => $baseUnitPrice,
                        ]);
                    }
                }

                $created[] = $mid;
            }

            DB::commit();

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

        $data = PartnerProduct::with(
            'parent_options.options.stock',
            'category',
            'owner',
            'stock',
            'partner' // TAMBAHKAN: Load relasi partner
        )
            ->where('owner_id', $owner->id)
            ->where('id', $outlet_product->id)
            ->firstOrFail();

        $promotions = Promotion::where('owner_id', $owner->id)->get();

        $pcsUnit = MasterUnit::where(function ($query) use ($owner) {
            $query->where('owner_id', $owner->id)
                ->orWhereNull('owner_id');
        })
            ->where(function ($query) {
                $query->where('unit_name', 'pcs')
                    ->orWhere('group_label', 'pcs');
            })
            ->where('is_base_unit', 1)
            ->first();

        $pcsUnitId = $pcsUnit ? $pcsUnit->id : null;

        return view('pages.owner.products.outlet-product.edit', compact(
            'data',
            'categories',
            'promotions',
            'pcsUnitId'
        ));
    }

    public function update(Request $request, $id)
    {
        $owner = Auth::user();

        DB::beginTransaction();
        try {
            $product = PartnerProduct::with(['parent_options.options.stock', 'stock'])
                ->where('owner_id', $owner->id)
                ->findOrFail($id);

            $hasOptions = $product->parent_options
                ->flatMap(fn($parent) => $parent->options ?? collect())
                ->isNotEmpty();

            // ===== VALIDASI =====
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
                'options.*.new_quantity'      => ['nullable', 'integer', 'min:0'], // UBAH
                'options.*.current_quantity'  => ['nullable', 'integer', 'min:0'], // TAMBAH
            ];

            $validator = Validator::make($request->all(), $rules);

            // VALIDASI CUSTOM
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

                // Validasi Opsi 
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

            // ===== PERSIAPAN DATA =====
            $productAlways = (int)($request->input('always_available', 0)) === 1;
            $newStockType  = $validated['stock_type'];
            $newIsActive   = (int)($validated['is_active'] ?? 0);
            $newIsHotProduct = (int)($validated['is_hot_product'] ?? 0);
            $promotionId   = $request->filled('promotion_id') ? (int)$validated['promotion_id'] : null;
            $newPrice      = $price ?? 0;

            // Get PCS Unit ID
            $pcsUnit = MasterUnit::where(function ($query) use ($owner) {
                $query->where('owner_id', $owner->id)->orWhereNull('owner_id');
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

            // ===== UPDATE KOLOM DI partner_products =====
            $product->update([
                'is_active'             => $newIsActive,
                'is_hot_product'        => $newIsHotProduct,
                'promo_id'              => $promotionId,
                'stock_type'            => $newStockType,
                'always_available_flag' => $productAlways ? 1 : 0,
                'price'                 => $newPrice,
            ]);

            // ===== SYNC PRODUCT STOCK WITH ADJUSTMENT LOGIC =====
            $this->syncProductStockWithAdjustment(
                $product,
                $newStockType,
                $productAlways,
                (int)($validated['new_quantity'] ?? 0),
                (int)($validated['current_quantity'] ?? 0),
                $pcsUnitId,
                $owner
            );

            // ===== SYNC OPTION STOCKS (sama seperti sebelumnya) =====
            $this->syncOptionStocks(
                $product,
                $request->input('options', []),
                $pcsUnitId
            );

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

    /**
     * Sync product stock dengan logic adjustment (IN/OUT movement)
     */
    private function syncProductStockWithAdjustment(
        PartnerProduct $product,
        string $newStockType,
        bool $productAlways,
        int $newQuantity,
        int $currentQuantity,
        int $pcsUnitId,
        $owner
    ): void {
        // LINKED: Hapus stok direct dan trigger recalculation
        if ($newStockType === 'linked') {
            if ($product->stock) {
                $product->stock->delete();
                unset($product->stock);
                $product->load('stock');
            }
            $this->recalculationService->recalculateSingleTarget($product);
            return;
        }

        // DIRECT: Handle adjustment
        $existingStock = Stock::where('partner_product_id', $product->id)
            ->whereNull('partner_product_option_id')
            ->first();

        // Jika belum ada stok, buat baru
        if (!$existingStock) {
            Stock::create([
                'stock_code'              => $this->generateUniqueStockCode(),
                'stock_type'              => 'direct',
                'owner_id'                => $product->owner_id,
                'partner_id'              => $product->partner_id,
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

        // Jika status Always Available (Unlimited), kita HENTIKAN proses.
        if ($productAlways) {
            return;
        }

        $difference = $newQuantity - $currentQuantity;

        // Jika tidak ada perubahan, skip
        if ($difference == 0) {
            return;
        }

        // Buat stock movement berdasarkan selisih
        $category = 'stock_adjustment';

        if ($difference > 0) {
            // PENAMBAHAN STOK (IN)
            $movement = StockMovement::create([
                'owner_id' => $owner->id,
                'partner_id' => $product->partner_id,
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
            // PENGURANGAN STOK (OUT)
            // Validasi ketersediaan
            if ($existingStock->quantity < abs($difference)) {
                throw new \Exception(
                    "Stok '{$existingStock->stock_name}' tidak mencukupi. " .
                        "Tersedia: {$existingStock->quantity} pcs"
                );
            }

            $movement = StockMovement::create([
                'owner_id' => $owner->id,
                'partner_id' => $product->partner_id,
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

        // Recalculate linked products jika ada
        $this->recalculationService->recalculateLinkedProducts($existingStock);
    }

    /**
     * Menangani update/pembuatan/penghapusan stok untuk opsi produk.
     */
    private function syncOptionStocks(
        PartnerProduct $product,
        array $optionInputs,
        int $pcsUnitId
    ): void {
        if (empty($optionInputs)) return;

        $owner = Auth::user();
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

            // Update model opsi
            $optModel->stock_type = $optStockType;
            $optModel->always_available_flag = $optAlways;
            $optModel->save();

            // LINKED: Hapus stok direct dan trigger recalculation
            if ($optStockType === 'linked') {
                if ($optModel->stock) {
                    $optModel->stock->delete();
                    unset($optModel->stock);
                    $optModel->load('stock');
                }
                $this->recalculationService->recalculateSingleTarget($optModel);
                continue;
            }

            // DIRECT: Handle adjustment
            $newQuantity = (int)($payload['new_quantity'] ?? 0);
            $currentQuantity = (int)($payload['current_quantity'] ?? 0);
            $difference = $newQuantity - $currentQuantity;

            $existingStock = Stock::where('partner_product_option_id', $optModel->id)
                ->first();

            // Jika belum ada stok, buat baru
            if (!$existingStock) {
                Stock::create([
                    'stock_code'               => $this->generateUniqueStockCode(),
                    'stock_type'               => 'direct',
                    'owner_id'                 => $product->owner_id,
                    'partner_id'               => $product->partner_id,
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

            // Jika Always Available, set quantity ke reserved only
            if ($optAlways) {
                // $existingStock->quantity = $existingStock->quantity_reserved ?? 0;
                // $existingStock->save();
                continue;
            }

            // Jika tidak ada perubahan, skip
            if ($difference == 0) {
                continue;
            }

            // Buat stock movement berdasarkan selisih
            $category = 'stock_adjustment';
            $itemName = $product->name . ' - ' . $optModel->name;

            if ($difference > 0) {
                // PENAMBAHAN STOK (IN)
                $movement = StockMovement::create([
                    'owner_id' => $owner->id,
                    'partner_id' => $product->partner_id,
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
                // PENGURANGAN STOK (OUT)
                if ($existingStock->quantity < abs($difference)) {
                    throw new \Exception(
                        "Stok '{$existingStock->stock_name}' tidak mencukupi. " .
                            "Tersedia: {$existingStock->quantity} pcs"
                    );
                }

                $movement = StockMovement::create([
                    'owner_id' => $owner->id,
                    'partner_id' => $product->partner_id,
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


    public function getRecipeIngredients(Request $request)
    {
        $owner = Auth::user();
        $owner_id = $owner->id;

        // TAMBAHKAN: Validasi dan ambil partner_id dari request
        $request->validate([
            'partner_id' => 'required|integer|exists:users,id'
        ]);

        $partnerId = (int) $request->input('partner_id');

        // VERIFIKASI: Pastikan partner ini milik owner yang sedang login
        $partner = \App\Models\User::where('id', $partnerId)
            ->where('owner_id', $owner->id)
            ->firstOrFail();

        // 1. Ambil HANYA Stock (Linked) milik Partner TERTENTU
        $stocks = Stock::with('displayUnit', 'partner')
            ->where('owner_id', $owner->id)
            ->where('partner_id', $partnerId)  // FILTER BY PARTNER
            ->where('stock_type', 'linked')
            ->get();

        // 2. Ambil Semua Unit yang tersedia untuk owner ini
        $allUnits = MasterUnit::where(function ($query) use ($owner_id) {
            $query->where('owner_id', $owner_id)->orWhereNull('owner_id');
        })->get();

        // 3. Mapping data untuk JSON response
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
            $currentOwnerId = auth()->user()->id; // ID Owner yang sedang login

            DB::beginTransaction();

            if ($itemType === 'product') {
                // Verifikasi Produk: Pastikan produk ada dan dimiliki oleh Owner yang login
                $product = PartnerProduct::where('id', $itemId)
                    ->where('owner_id', $currentOwnerId)
                    ->firstOrFail();

                // Delete existing recipe
                PartnerProductRecipe::where('partner_product_id', $itemId)->delete();

                // Insert new recipe items
                foreach ($recipeItems as $item) {
                    // Convert quantity to base unit
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

                // Update product stock_type ke 'linked' dan picu recalculation
                if ($product->stock_type !== 'linked') {
                    $product->update(['stock_type' => 'linked']);
                }
                $recalculator->recalculateSingleTarget($product);
            } elseif ($itemType === 'option') {
                // Verifikasi Opsi: Ambil opsi dan verifikasi kepemilikan melalui produk induk
                $option = PartnerProductOption::findOrFail($itemId);

                $product = PartnerProduct::where('id', $option->partner_product_id)
                    ->where('owner_id', $currentOwnerId)
                    ->firstOrFail(); // Pastikan Owner memiliki produk induk

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

                // Update option stock_type ke 'linked' dan picu recalculation
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

    public function destroy(PartnerProduct $outlet_product)
    {
        $outlet_product->delete();
        return redirect()->route('owner.user-owner.outlet-products.index')->with('success', 'Product deleted successfully!');
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
