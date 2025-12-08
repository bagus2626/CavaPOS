<?php

namespace App\Http\Controllers\Customer\Menu;

use App\Http\Controllers\PaymentGateway\Xendit\InvoiceController;
use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Transaction\BookingOrder;
use App\Models\Transaction\OrderDetail;
use App\Models\Transaction\OrderDetailOption;
use App\Models\Owner;
use App\Models\Product\Promotion;
use App\Models\Product\Specification;
use App\Models\Admin\Product\Category;
use App\Models\User;
use App\Models\Store\Table;
use App\Models\Xendit\SplitRule;
use App\Models\Xendit\XenditSubAccount;
use App\Services\XenditService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Events\OrderCreated;
use App\Models\Transaction\OrderPayment;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\SendReceiptEmailJob;
use App\Models\Partner\Products\PartnerProductOptionsRecipe;
use App\Models\Partner\Products\PartnerProductRecipe;
use App\Models\Store\Stock;
use Milon\Barcode\Facades\DNS2DFacade as DNS2D;
use App\Services\StockRecalculationService;
use Illuminate\Support\Facades\Schema;

class CustomerMenuController extends Controller
{
    protected $xenditInvoice;
    protected $recalculationService;

    public function __construct(XenditService $xendit, StockRecalculationService $recalculationService)
    {
        $this->xenditInvoice = new InvoiceController($xendit);
        $this->recalculationService = $recalculationService;
    }

    public function index(Request $request, $partner_slug, $table_code)
    {
        if (!Auth::guard('customer')->check() && !session()->has('guest_customer')) {
            return view('pages.customer.auth.login_choice', compact('partner_slug', 'table_code'));
        }

        $customer = Auth::guard('customer')->user() ?? session('guest_customer');

        $table = Table::where('table_code', $table_code)
            ->whereHas('partner', fn($q) => $q->where('slug', $partner_slug))
            ->firstOrFail();

        $partner = User::where('slug', $partner_slug)
            ->where('role', 'partner')
            ->firstOrFail();

        $partner_products = PartnerProduct::with([
                'category',
                'parent_options.options',
                'promotion' => function ($q) {
                    $q->activeToday();
                },
                'stock',
                'parent_options.options.stock',
            ])
            ->where('partner_id', $partner->id)
            ->where('is_active', 1)
            ->get();

        $categories = Category::whereIn('id', $partner_products->pluck('category_id'))
            ->orderBy('category_order')
            ->get();

        $reorderItems    = [];
        $reorderMessages = [];

        if ($request->filled('reorder_order_id') && Auth::guard('customer')->check()) {
            $reorderOrderId = $request->query('reorder_order_id');

            $bookingOrder = BookingOrder::with([
                    'order_details.order_detail_options.option',
                ])
                ->where('id', $reorderOrderId)
                ->where('partner_id', $partner->id)
                ->where('customer_id', $customer->id)
                ->first();

            if ($bookingOrder) {
                foreach ($bookingOrder->order_details as $detail) {
                    $product = $partner_products->firstWhere('id', $detail->partner_product_id);
                    if (!$product) {
                        $reorderMessages[] = __('messages.customer.menu.product_not_in_menu', [
                            'name' => $detail->product_name
                        ]);
                        continue;
                    }

                    if ($product->quantity_available < 1 && !$product->always_available_flag) {
                        $reorderMessages[] = __('messages.customer.menu.product_out_of_stock', [
                            'name' => $product->name
                        ]);
                        continue;
                    }

                    $requestedOptions   = $detail->order_detail_options;
                    $requestedOptionIds = $requestedOptions->pluck('option_id')->filter()->values();

                    $validOptionIds      = [];
                    $validOptions        = [];
                    $unavailableOptNames = [];

                    $allOptions = $product->parent_options
                        ->flatMap(function ($po) {
                            return $po->options;
                        });

                    foreach ($requestedOptionIds as $optId) {
                        $opt = $allOptions->firstWhere('id', $optId);

                        if (!$opt) {
                            $unavailableOptNames[] =
                                $requestedOptions->firstWhere('option_id', $optId)->option->name
                                ?? 'Opsi lama';
                            continue;
                        }

                        if ($opt->quantity_available < 1 && !$opt->always_available_flag) {
                            $unavailableOptNames[] = $opt->name;
                            continue;
                        }

                        $validOptionIds[] = $optId;
                        $validOptions[]   = $opt;
                    }

                    if ($requestedOptionIds->count() > 0 && count($validOptionIds) === 0) {
                        $reorderMessages[] = __('messages.customer.menu.options_all_unavailable', [
                            'name' => $product->name
                        ]);
                        continue;
                    }

                    if (count($unavailableOptNames) > 0 && count($validOptionIds) > 0) {
                        $reorderMessages[] = __('messages.customer.menu.options_partial_unavailable', [
                            'name'    => $product->name,
                            'options' => implode(', ', $unavailableOptNames)
                        ]);
                    }

                    $requestedQty = (int) ($detail->quantity ?? $detail->qty ?? 1);
                    if ($requestedQty < 1) {
                        $requestedQty = 1;
                    }

                    if ($product->always_available_flag) {
                        $maxQtyByProduct = PHP_INT_MAX;
                    } else {
                        $maxQtyByProduct = (int) floor($product->quantity_available);
                    }

                    $maxQtyByOptions = PHP_INT_MAX;

                    foreach ($validOptions as $opt) {
                        if ($opt->always_available_flag) {
                            continue;
                        }

                        $optAvail = (int) floor($opt->quantity_available);

                        if ($optAvail < 0) {
                            $optAvail = 0;
                        }

                        $maxQtyByOptions = min($maxQtyByOptions, $optAvail);
                    }

                    $effectiveAvailable = min($maxQtyByProduct, $maxQtyByOptions);

                    if ($effectiveAvailable < 1) {
                        $reorderMessages[] = __('messages.customer.menu.options_insufficient_stock', [
                            'name' => $product->name
                        ]);
                        continue;
                    }

                    if ($effectiveAvailable < $requestedQty) {
                        $reorderMessages[] = __('messages.customer.menu.qty_reduced', [
                            'name' => $product->name,
                            'from' => $requestedQty,
                            'to'   => $effectiveAvailable
                        ]);
                        $finalQty = $effectiveAvailable;
                    } else {
                        $finalQty = $requestedQty;
                    }

                    $reorderItems[] = [
                        'product_id' => $product->id,
                        'option_ids' => $validOptionIds,
                        'qty'        => max(1, (int) $finalQty),
                        'note'       => $detail->note ?? '',
                    ];
                }
            } else {
                // kalau order tidak ditemukan / bukan milik user ini
                $reorderMessages[] = 'Pesanan yang dipilih tidak dapat dimuat ulang.';
            }
        }

        return view('pages.customer.menu.index', [
            'table'           => $table,
            'customer'        => $customer,
            'partner'         => $partner,
            'partner_products'=> $partner_products,
            'categories'      => $categories,
            'partner_slug'    => $partner_slug,
            'table_code'      => $table_code,
            'reorderItems'    => $reorderItems,
            'reorderMessages' => $reorderMessages,
        ]);
    }


    public function checkout(Request $request, $partner_slug, $table_code)
    {
        DB::beginTransaction();
        try {
            // dd($request->all());
            $customer = Auth::guard('customer')->user();
            // dd($user->name ?? 'guest', $partner_slug, $table_code, $request->all());
            $partner = User::where('slug', $partner_slug)->first();
            $table = Table::where('table_code', $table_code)->first();
            $orders = $request->input('items', []);
            $ownerId = $partner->owner_id ?? null;
            $owner = Owner::findOrFail($ownerId);
            $validRegistrationStatuses = ['LIVE', 'LIVE_TESTMODE'];

            $this->checkStockAvailability($orders, $partner);

            if ($request->payment_method === 'QRIS') {
                if (!in_array($owner->xendit_registration_status, $validRegistrationStatuses)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun belum diaktifkan. Silakan hubungi pengelola untuk menyelesaikan proses aktivasi.',
                    ]);
                }

                if ($owner->xendit_split_rule_status !== 'created') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pengaturan pembayaran belum lengkap. Silakan hubungi pengelola.',
                    ]);
                }
            }

            $booking_order_code = $this->generateBookingOrderCode($partner->partner_code);

            do {
                $suffix = strtoupper(substr((string) Str::ulid(), -8));   // contoh: 01HZ3A9Q -> ambil 8 char terakhir
                $booking_order_code = "{$partner->partner_code}-{$suffix}";
            } while (
                BookingOrder::where('booking_order_code', $booking_order_code)->exists()
            );

            $booking_order = BookingOrder::create([
                'booking_order_code' => $booking_order_code,
                'partner_id' => $partner->id,
                'partner_name' => $partner->name,
                'table_id' => $table->id,
                'customer_id' => $customer ? $customer->id : null,
                'order_by' => 'CUSTOMER',
                'customer_name' => $customer ? $customer->name : 'guest-' . $request->order_name,
                'order_status' => 'UNPAID',
                'payment_method' => $request->payment_method,
                'total_order_value' => $request->total_amount,
            ]);


            $partnerProductIds = [];

            foreach ($orders as $order) {
                // dd($order);
                $partnerProductIds[] = data_get($order, 'product_id');
                $productId   = data_get($order, 'product_id');
                $optionIds   = data_get($order, 'option_ids', []);
                $qty         = (int) data_get($order, 'qty', 1);
                $note        = data_get($order, 'note', '');
                $promoId    = data_get($order, 'promo_id', null);

                $product = PartnerProduct::with('stock')->findOrFail($productId);
                $options = PartnerProductOption::with('parent')->whereIn('id', (array)$optionIds)->get();
                $optionsPrice = $options->sum('price');

                $promoAmount = 0;
                $promoType = null;
                if ($promoId) {
                    $promotion = Promotion::findOrFail($promoId);
                    if ($promotion->promotion_type === 'percentage') {
                        $promoAmount = $product->price * $promotion->promotion_value / 100;
                        $promoType = $promotion->promotion_type;
                    } else if ($promotion->promotion_type === 'amount') {
                        $promoAmount = $promotion->promotion_value;
                        $promoType = $promotion->promotion_type;
                    }
                }

                $order_detail = OrderDetail::create([
                    'booking_order_id'    => $booking_order->id,
                    'product_code'       => $product->product_code,
                    'product_name'       => $product->name,
                    'partner_product_id'  => $productId,
                    'base_price'          => $product->price,
                    'promo_id'           => $promoId,
                    'promo_amount'      => $promoAmount,
                    'promo_type'        => $promoType,
                    'options_price'     => $optionsPrice ?? 0,   // isi jika ingin simpan
                    'quantity'          => $qty,
                    'customer_note'       => $note,
                ]);

                if ($product->stock_type === 'direct' && $product->always_available_flag === 0 && $product->stock) {
                    // Reservasi Direct Stock
                    $product->stock->increment('quantity_reserved', $qty);
                } elseif ($product->stock_type === 'linked') {
                    // Reservasi Linked Stock (Bahan Baku)
                    $recipes = PartnerProductRecipe::where('partner_product_id', $productId)->get();
                    $this->processRecipeReservation($recipes, $qty);
                }

                foreach ($options as $opt) {
                    OrderDetailOption::create([
                        'order_detail_id' => $order_detail->id,
                        'parent_name' => $opt->parent->name ?? null,
                        'partner_product_option_name' => $opt->name,
                        'option_id' => $opt->id,
                        'price' => $opt->price
                    ]);

                    // Opsi Produk
                    if ($opt->stock_type === 'direct' && $opt->always_available_flag === 0 && $opt->stock) {
                        $opt->stock->increment('quantity_reserved', $qty);
                    } elseif ($opt->stock_type === 'linked') {
                        $recipes = PartnerProductOptionsRecipe::where('partner_product_option_id', $opt->id)->get();
                        $this->processRecipeReservation($recipes, $qty);
                    }
                }
            }

            $booking_order->saveWifiSnapshot(
                $partner->user_wifi,
                $partner->pass_wifi,
                $partner->is_wifi_shown ?? 0
            );

            // test by qris (hapus kemudian)
            if ($request->payment_method === 'QRIS') {
                $booking_order->order_status = 'PAYMENT';
                $booking_order->payment_method = 'QRIS';

                $payment = OrderPayment::create([
                    'booking_order_id'  => $booking_order->id,
                    'customer_id'       => $customer ? $customer->id : null,
                    'customer_name'     => $customer ? $customer->name : 'guest-' . $request->order_name,
                    'payment_type'      => 'QRIS',
                    'paid_amount'       => $request->total_amount,
                    'change_amount'     => 0,
                    'payment_status'    => 'PENDING'
                ]);

                $booking_order->payment_id = $payment->id;
                $booking_order->save();

                $xenditSubAccount = XenditSubAccount::where('partner_id', $partner->owner_id)->first();
                $xenditSplitRule = SplitRule::where('partner_id', $partner->owner_id)->latest()->first();

                $products = OrderDetail::with('partnerProduct.category')
                    ->where('booking_order_id', $booking_order->id)
                    ->whereIn('partner_product_id', $partnerProductIds)
                    ->get();

                $items = $products->map(function ($product) {
                    return [
                        "name"      => $product->product_name,
                        "quantity"  => $product->quantity,
                        "price"     => $product->base_price ?? 0,
                        "category"  => optional($product->partnerProduct->category)->category_name ?? "Uncategorized",
                    ];
                })->toArray();

                if (!$customer) {
                    $guestOrders = collect(session('guest_orders', []));
                    $guestOrders->push($booking_order->id);

                    session(['guest_orders' => $guestOrders->unique()->values()->all()]);
                }

                $payload = [
                    "external_id" => $booking_order->booking_order_code ?? "invoice-" . time(),
                    "amount" => $payment->paid_amount ?? 0,
                    "given_names" => $request->order_name ?? "unknow",
                    "description" => "Invoice QRIS",
                    "invoice_duration" => 600,
                    "customer" => [
                        "given_names" => $customer ? $customer->name : $request->order_name,
                        "email" => $customer ? $customer->email : "customer@example.com",
                        "mobile_number" => $customer ? $customer->phone ?? "0" : "0",
                    ],
                    "customer_notification_preference" => [
                        "invoice_created" => ["whatsapp", "email"],
                        "invoice_reminder" => ["whatsapp", "email"],
                        "invoice_paid" => ["whatsapp", "email"]
                    ],
                    "success_redirect_url" => url("customer/{$partner_slug}/order-detail/{$table_code}/{$booking_order->id}"),
                    "failure_redirect_url" => url("customer/{$partner_slug}/order-detail/{$table_code}/{$booking_order->id}"),
                    // "failure_redirect_url" => url("customer/{$partner_slug}/menu/{$table_code}"),
                    "currency" => "IDR",
                    "items" => $items,
                    //                    "payment_methods" => ["QRIS"],
                    "metadata" => [
                        "store_branch" => $partner->name
                    ]
                ];

                // dd($payload);

                $invoiceResponse = $this->xenditInvoice->createInvoice($booking_order->id, $xenditSubAccount->xendit_user_id, $xenditSplitRule->split_rule_id, $payload);
                $invoice = $invoiceResponse->getData(true);
                $invoiceData = $invoice['data'] ?? null;
                DB::commit();
                // dd($invoiceResponse, $invoice, $invoiceData);

                if ($invoice['success']) {
                    return response()->json([
                        'success' => true,
                        'redirect_url' => $invoiceData['invoice_url']
                    ]);
                }
            }

            DB::commit();

            DB::afterCommit(function () use ($booking_order) {
                event(new OrderCreated($booking_order));
            });

            $token = Crypt::encrypt([
                'p' => $partner_slug,
                't' => $table_code,
                'o' => $booking_order->id,
            ]);


            $url = URL::temporarySignedRoute(
                'customer.payment.get-payment-cash',
                now()->addMinutes(120),
                [
                    'partner_slug' => $partner_slug,
                    'table_code' => $table_code,
                    'token' => $token
                ]
            );
            return redirect()->to($url)->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    function generateBookingOrderCode(string $partnerCode): string
    {
        do {
            // Ambil 8 char terakhir ULID, lalu uppercase
            $suffix = strtoupper(substr((string) Str::ulid(), -8));
            $code   = "{$partnerCode}-{$suffix}";
        } while (BookingOrder::where('booking_order_code', $code)->exists());

        return $code;
    }

    public function printReceipt($id)
    {
        // return response("OK RECEIPT $id", 200);
        $customer = Auth::guard('customer')->user() ?? session('guest_customer');

        $data = BookingOrder::with([
            'order_details.order_detail_options.option',
            'order_details.partnerProduct',
            'payment',
            'table', // kamu pakai $data->table di view => eager load
        ])->findOrFail($id);

        if ($data->payment_flag === 0) {
            abort(403, 'Pembayaran belum terdeteksi');
        }

        // Validasi kepemilikan hanya jika order memang punya customer_id
        if ($data->customer_id) {
            if (!$customer || ($customer->id ?? null) !== $data->customer_id) {
                // jangan redirect/HTML ke login di sini — kirim 403 murni
                abort(403, 'Tidak bisa print order pelanggan lain');
            }
        }

        $partner = User::findOrFail($data->partner_id);

        if (!empty($partner->logo)) {
            $logoPath = public_path('storage/' . $partner->logo);

            if (file_exists($logoPath)) {
                $img = Image::make($logoPath);
                $img->greyscale(); // Ubah ke hitam putih

                // Encode ke base64 data URL
                $partner->logo_grayscale = $img->encode('data-url');
            }
        }

        $customPaper = [0, 0, 227, 600];
        $pdf = Pdf::loadView('pages.employee.cashier.pdf.receipt', [
            'data'     => $data,
            'partner'  => $partner,
            'cashier'  => null,
            'customer' => $customer,
            'payment'  => $data->payment,
        ])->setPaper($customPaper, 'portrait');

        // ——— KUNCI: bersihkan output buffer supaya header PDF tidak “kotor”
        if (ob_get_length()) {
            ob_end_clean();
        }

        Storage::put("receipts/receipt-{$data->booking_order_code}.pdf", $pdf->output());

        // download file hasil simpan
        return Storage::download("receipts/receipt-{$data->booking_order_code}.pdf");
    }

    private function checkStockAvailability(array $orders, $partner): void
    {
        $requiredStock = [];

        foreach ($orders as $order) {
            $productId = data_get($order, 'product_id');
            $optionIds = data_get($order, 'option_ids', []);
            $qty = (int) data_get($order, 'qty', 1);

            $product = PartnerProduct::with('stock')->find($productId);

            // 1. Cek Produk Utama
            if ($product->always_available_flag === 0) {
                if ($product->stock_type === 'direct') {
                    $productStock = $product->stock;
                    // Cek Direct Stock: Stok Total - Stok Reserved harus >= qty
                    $available = ($productStock->quantity ?? 0) - ($productStock->quantity_reserved ?? 0);

                    if (!$productStock || $available < $qty) {
                        throw new \Exception("Stok {$product->name} (Produk) tidak mencukupi.");
                    }
                } elseif ($product->stock_type === 'linked') {
                    $recipes = PartnerProductRecipe::where('partner_product_id', $productId)->get();
                    $this->accumulateLinkedRequirements($recipes, $qty, $requiredStock, $product->name);
                }
            }

            // 2. Cek Opsi
            $options = PartnerProductOption::with('stock')->whereIn('id', (array)$optionIds)->get();
            foreach ($options as $opt) {
                if ($opt->always_available_flag === 0) {
                    if ($opt->stock_type === 'direct') {
                        $optStock = $opt->stock;
                        $available = ($optStock->quantity ?? 0) - ($optStock->quantity_reserved ?? 0);

                        if (!$optStock || $available < $qty) {
                            throw new \Exception("Stok {$opt->name} (Opsi) tidak mencukupi.");
                        }
                    } elseif ($opt->stock_type === 'linked') {
                        $recipes = PartnerProductOptionsRecipe::where('partner_product_option_id', $opt->id)->get();
                        $this->accumulateLinkedRequirements($recipes, $qty, $requiredStock, $opt->name);
                    }
                }
            }
        }

        // 3. Final Check untuk Linked Stock (Cek Akumulasi Total)
        foreach ($requiredStock as $stockId => $totalRequired) {
            $ingredient = Stock::find($stockId);
            $available = ($ingredient->quantity ?? 0) - ($ingredient->quantity_reserved ?? 0);

            if (!$ingredient || $available < $totalRequired) {
                $name = $ingredient ? $ingredient->stock_name : 'Bahan Baku Tidak Ditemukan';
                throw new \Exception("Bahan Baku '{$name}' tidak mencukupi untuk memenuhi total pesanan.");
            }
        }
    }

    private function accumulateLinkedRequirements($recipes, $orderedQuantity, array &$requiredStock, $itemName)
    {
        foreach ($recipes as $recipe) {
            $stockId = $recipe->stock_id;
            $quantityPerUnit = $recipe->quantity_used;
            $totalNeeded = $quantityPerUnit * $orderedQuantity;
            $requiredStock[$stockId] = ($requiredStock[$stockId] ?? 0) + $totalNeeded;
        }
    }

    /**
     * Mengubah Logic Deduction untuk reservasi stok linked (hanya menambah quantity_reserved).
     */
    private function processRecipeReservation($recipes, int $orderedQuantity): void
    {
        foreach ($recipes as $recipe) {
            $ingredientStock = Stock::find($recipe->stock_id);

            $quantityPerUnit = $recipe->quantity_used;
            $totalQuantityToReserve = $quantityPerUnit * $orderedQuantity;

            // Reservasi Stok Bahan Mentah
            $ingredientStock->increment('quantity_reserved', $totalQuantityToReserve);

            $this->recalculationService->recalculateLinkedProducts($ingredientStock);
        }
    }

    /**
     * Check stock availability in real-time sebelum checkout
     */
    public function checkStockRealtime(Request $request, $partner_slug, $table_code)
    {
        try {
            $items = $request->input('items', []);
            $unavailable = [];

            foreach ($items as $item) {
                $productId = data_get($item, 'product_id');
                $optionIds = data_get($item, 'option_ids', []);
                $qty = (int) data_get($item, 'qty', 1);

                // Cek Produk Utama
                $product = PartnerProduct::with('stock')->find($productId);

                if (!$product) continue;

                if ($product->always_available_flag === 0) {
                    if ($product->stock_type === 'direct' && $product->stock) {
                        $available = ($product->stock->quantity ?? 0) - ($product->stock->quantity_reserved ?? 0);

                        if ($available < $qty) {
                            $unavailable[] = [
                                'name' => $product->name,
                                'type' => 'Produk',
                                'requested' => $qty,
                                'available' => max(0, $available)
                            ];
                        }
                    } elseif ($product->stock_type === 'linked') {
                        // Untuk linked stock, gunakan quantity_available dari accessor
                        $available = $product->quantity_available;

                        if ($available < $qty) {
                            $unavailable[] = [
                                'name' => $product->name,
                                'type' => 'Produk',
                                'requested' => $qty,
                                'available' => max(0, $available)
                            ];
                        }
                    }
                }

                // Cek Opsi Produk
                if (!empty($optionIds)) {
                    $options = PartnerProductOption::with('stock')->whereIn('id', (array)$optionIds)->get();

                    foreach ($options as $opt) {
                        if ($opt->always_available_flag === 0) {
                            if ($opt->stock_type === 'direct' && $opt->stock) {
                                $available = ($opt->stock->quantity ?? 0) - ($opt->stock->quantity_reserved ?? 0);

                                if ($available < $qty) {
                                    $unavailable[] = [
                                        'name' => $opt->name,
                                        'type' => 'Opsi',
                                        'requested' => $qty,
                                        'available' => max(0, $available)
                                    ];
                                }
                            } elseif ($opt->stock_type === 'linked') {
                                $available = $opt->quantity_available;

                                if ($available < $qty) {
                                    $unavailable[] = [
                                        'name' => $opt->name,
                                        'type' => 'Opsi',
                                        'requested' => $qty,
                                        'available' => max(0, $available)
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            return response()->json([
                'success' => empty($unavailable),
                'unavailable_items' => $unavailable,
                'message' => empty($unavailable)
                    ? 'Stok tersedia'
                    : 'Beberapa item tidak tersedia'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa stok: ' . $e->getMessage()
            ], 500);
        }
    }

    public function orderDetail(Request $request, $partner_slug, $table_code, $order_id)
    {
        // Customer login / guest
        $customer = Auth::guard('customer')->user();
        $guestOrders = collect(session('guest_orders', []));

        // Partner & Table
        $partner = User::where('slug', $partner_slug)->firstOrFail();
        $table = Table::where('table_code', $table_code)
            ->where('partner_id', $partner->id)
            ->firstOrFail();

        // Order + relasi yang dibutuhkan
        $order = BookingOrder::with([
            'order_details.order_detail_options.option',
            'order_details.partnerProduct',
            'payment',
            'table',
        ])->findOrFail($order_id);

        // Validasi kepemilikan
        if ($order->customer_id) {
            if (!$customer || ($customer->id ?? null) !== $order->customer_id) {
                abort(403, 'Kamu tidak bisa melihat pesanan pelanggan lain.');
            }
        } else {
            if ($customer) {
                abort(403, 'Pesanan ini dibuat tanpa login. Silakan akses dari perangkat yang sama saat memesan.');
            }

            if (!$guestOrders->contains($order->id)) {
                abort(403, 'Sesi kamu untuk melihat pesanan ini sudah tidak berlaku.');
            }

            // untuk Blade, kita bisa set $customer sebagai object sederhana
            $customer = (object)[
                'id'   => null,
                'name' => $order->customer_name,
                'email' => null,
            ];
        }

        // mapping indeks status untuk timeline
        $statusOrder = $order->order_status;
        $statusIndexMap = [
            'UNPAID'    => 0,
            'PAID'      => 1,
            'PROCESSED' => 2,
            'SERVED'    => 3,
        ];
        $currentIndex = $statusIndexMap[$statusOrder] ?? 0;

        // Label/status utama di atas timeline
        $headline = '';
        $subtitle = '';

        switch ($statusOrder) {
            case 'UNPAID':
                $headline = 'Status pembayaran';
                $subtitle = 'Pesanan kamu sudah tercatat, silakan selesaikan pembayaran di kasir atau melalui QRIS.';
                break;
            case 'PAID':
                $headline = 'Menunggu diproses';
                $subtitle = 'Pembayaran kamu sudah kami terima. Pesanan sedang menunggu diproses oleh kasir/kitchen.';
                break;
            case 'PROCESSED':
                $headline = 'Sedang diproses';
                $subtitle = 'Pesanan kamu sedang disiapkan. Mohon menunggu, pesanan akan segera disajikan.';
                break;
            case 'SERVED':
                $headline = 'Selesai';
                $subtitle = 'Pesanan kamu sudah selesai dan telah disajikan. Selamat menikmati!';
                break;
            default:
                $headline = 'Status pesanan';
                $subtitle = 'Status pesanan tidak dikenal.';
        }

        $qrPayload = $order->booking_order_code;

        $qrPngBase64 = DNS2D::getBarcodePNG($qrPayload, 'QRCODE', 6, 6);
        // =====================================

         $wifiData = null;
        if ($order->payment_flag === 1) {
            $snapshot = $order->wifi_snapshot;
            if ($snapshot && ($snapshot['wifi_shown'] ?? 0) == 1) {
                $wifiData = [
                    'ssid' => $snapshot['wifi_ssid'] ?? null,
                    'password' => $snapshot['wifi_password'] ?? null,
                ];
            }
        }

        return view('pages.customer.orders.detail', [
            'order'        => $order,
            'partner'      => $partner,
            'table'        => $table,
            'customer'     => $customer,
            'currentIndex' => $currentIndex,
            'headline'     => $headline,
            'subtitle'     => $subtitle,
            'qrPngBase64'  => $qrPngBase64, // <- kirim ke Blade
            'wifiData'     => $wifiData, // ← TAMBAHKAN BARIS INI
        ]);
    }

    public function getOrderHistory(Request $request, $partner_slug, $table_code)
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer || !$customer->id) {
            abort(403, 'Maaf, anda tidak bisa mengakses halaman ini.');
        }

        $partner = User::where('slug', $partner_slug)
            ->where('role', 'partner')
            ->firstOrFail();

        $table = Table::where('table_code', $table_code)
            ->where('partner_id', $partner->id)
            ->firstOrFail();

        $order_history = BookingOrder::with([
                'order_details.order_detail_options.option',
                'order_details.partnerProduct',
                'payment',
                'table',
                'last_xendit_invoice'
            ])
            ->where('partner_id', $partner->id)
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        if ($request->ajax()) {
            $view = view('pages.customer.orders.partials._order-cards', [
                'orderHistory' => $order_history,
                'partner'      => $partner,
                'table'        => $table,
                'partner_slug' => $partner_slug,
                'table_code'   => $table_code,
            ])->render();

            return response()->json([
                'html'          => $view,
                'next_page_url' => $order_history->nextPageUrl(),
            ]);
        }

        return view('pages.customer.orders.histories', [
            'partner'       => $partner,
            'table'         => $table,
            'customer'      => $customer,
            'orderHistory'  => $order_history,
            'partner_slug'  => $partner_slug,
            'table_code'    => $table_code,
        ]);
    }


}
