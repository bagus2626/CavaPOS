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
use App\Models\Store\Stock;
use App\Models\Store\StockMovement;
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
        $productsByOutlet = PartnerProduct::with('parent_options.options', 'category', 'promotion', 'stock.displayUnit')
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
            'outlet_id'   => 'required|integer|exists:users,id',
        ]);

        $existing_outlet_products = PartnerProduct::where('partner_id', $request->outlet_id)
            ->whereNotNull('master_product_id')
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

                $mp->pictures  = $pictures;
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
            'stock'
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
        DB::beginTransaction();
        try {
            $product = PartnerProduct::with(['parent_options.options.stock', 'stock'])->findOrFail($id);
            $owner = Auth::user();

            $hasOptions = $product->parent_options
                ->flatMap(fn($parent) => $parent->options ?? collect())
                ->isNotEmpty();

            // ===== Validasi =====
            $rules = [
                'always_available' => ['nullable', 'in:0,1'],
                'quantity'         => ['nullable', 'integer', 'min:0'],
                'is_active'        => ['required', 'in:0,1'],
                'promotion_id'     => ['nullable', 'integer', 'exists:promotions,id'],
                'options'                     => [$hasOptions ? 'required' : 'sometimes', 'array'],
                'options.*.stock_type'        => ['required', 'in:direct,linked'],
                'options.*.always_available'  => ['nullable', 'in:0,1'],
                'options.*.quantity'          => ['nullable', 'integer', 'min:0'],
            ];

            $validator = Validator::make($request->all(), $rules);

            $validator->after(function ($v) use ($request, $product, $hasOptions) {
                if ($product->stock_type === 'direct') {
                    $prodAA = (int)$request->input('always_available', 0) === 1;
                    if (!$prodAA) {
                        $q = $request->input('quantity', null);
                        if ($q === null || $q === '') {
                            $v->errors()->add('quantity', 'Quantity is required unless product is set to always available.');
                        }
                    }
                }

                if ($hasOptions) {
                    foreach ((array)$request->input('options', []) as $oid => $opt) {
                        $optStockType = $opt['stock_type'] ?? 'direct';

                        // Hanya validasi quantity jika stock_type = direct
                        if ($optStockType === 'direct') {
                            $oa = (int)($opt['always_available'] ?? 0) === 1;
                            if (!$oa) {
                                if (!array_key_exists('quantity', $opt) || $opt['quantity'] === '' || $opt['quantity'] === null) {
                                    $v->errors()->add("options.$oid.quantity", 'Quantity is required unless this option is set to always available.');
                                }
                            }
                        }
                    }
                }
            });

            $validated = $validator->validate();

            $productAlways = (int)($request->input('always_available', 0)) === 1;
            $newIsActive   = (int) ($validated['is_active'] ?? 0);
            $promotionId   = $request->filled('promotion_id') ? (int) $validated['promotion_id'] : null;
            $newQuantity   = $productAlways ? 0 : (int) ($validated['quantity'] ?? 0);

            // Get PCS unit ID
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

            // ===== Update kolom di 'partner_products' =====
            $product->is_active = $newIsActive;
            $product->promo_id  = $promotionId;
            $product->always_available_flag = $productAlways ? 1 : 0;
            $product->save();

            // ===== Update tabel 'stocks' untuk Product (Hanya jika 'direct') =====
            if ($product->stock_type === 'direct') {
                if ($product->stock) {
                    $product->stock->quantity = $newQuantity;
                    $product->stock->save();
                } else if (!$productAlways) {
                    $product->stock()->create([
                        'stock_code' => $this->generateUniqueStockCode(),
                        'owner_id'   => $product->owner_id,
                        'partner_id' => $product->partner_id,
                        'type'       => 'partner',
                        'stock_name' => $product->name,
                        'quantity'   => $newQuantity,
                        'display_unit_id' => $pcsUnitId,
                        'owner_master_product_id' => $product->master_product_id,
                        'partner_product_id' => $product->id,
                        'last_price_per_unit' => $product->price,
                        'description' => $product->description,
                    ]);
                }
            }

            // ===== Update tiap Option  =====
            $optionInputs = $request->input('options', []);
            if (!empty($optionInputs)) {
                $optionIds = array_map('intval', array_keys($optionInputs));
                $options = PartnerProductOption::with('stock')
                    ->whereIn('id', $optionIds)
                    ->whereHas('parent', function ($q) use ($product) {
                        $q->where('partner_product_id', $product->id);
                    })
                    ->get()
                    ->keyBy('id');

                foreach ($optionInputs as $optId => $payload) {
                    $optId = (int)$optId;
                    if (!$options->has($optId)) continue;

                    $optModel = $options[$optId];
                    $optStockType = $payload['stock_type'] ?? 'direct';
                    $optAlways = (int)($payload['always_available'] ?? 0) === 1;
                    $optQty = ($optStockType === 'direct' && !$optAlways) ? (int)($payload['quantity'] ?? 0) : 0;

                    // Update option model
                    $optModel->stock_type = $optStockType;
                    $optModel->always_available_flag = $optAlways ? 1 : 0;
                    $optModel->save();

                    // ===== Handle Stock untuk Option =====
                    if ($optStockType === 'direct') {
                        // Jika direct, kelola stock
                        if ($optModel->stock) {
                            // Update existing stock
                            $optModel->stock->quantity = $optQty;
                            $optModel->stock->save();
                        } else if (!$optAlways) {
                            // Create new stock jika belum ada dan bukan always available
                            Stock::create([
                                'stock_code' => $this->generateUniqueStockCode(),
                                'owner_id'   => $product->owner_id,
                                'partner_id' => $product->partner_id,
                                'type'       => 'partner',
                                'stock_name' => $product->name . ' - ' . $optModel->name,
                                'quantity'   => $optQty,
                                'display_unit_id' => $pcsUnitId,
                                'owner_master_product_id' => $product->master_product_id,
                                'partner_product_id' => $product->id,
                                'partner_product_option_id' => $optModel->id,
                                'last_price_per_unit' => $optModel->price,
                                'description' => $optModel->description,
                            ]);
                        }
                    } else {
                        // Jika linked, hapus stock yang ada (jika ada)
                        if ($optModel->stock) {
                            $optModel->stock->delete();
                        }
                    }
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
