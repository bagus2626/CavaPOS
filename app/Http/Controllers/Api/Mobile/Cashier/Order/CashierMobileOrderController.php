<?php

namespace App\Http\Controllers\Api\Mobile\Cashier\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Product;
use App\Models\Partner\Products\PartnerProduct;

use App\Http\Controllers\PaymentGateway\Xendit\InvoiceController;
use Carbon\Carbon;
use App\Models\Transaction\OrderPayment;
use App\Models\Product\Promotion;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Transaction\BookingOrder;
use App\Models\Transaction\OrderDetail;
use App\Models\Transaction\OrderDetailOption;
use App\Models\Xendit\SplitRule;
use App\Models\Xendit\XenditSubAccount;
use App\Services\XenditService;
use App\Models\Admin\Product\Category;
use App\Models\Owner;
use App\Models\User;
use App\Models\Store\Table;
use Illuminate\Support\Facades\DB;
use App\Events\OrderCreated;
use Illuminate\Support\Str;
use App\Jobs\SendReceiptEmailJob;
use App\Models\Partner\Products\PartnerProductOptionsRecipe;
use App\Models\Partner\Products\PartnerProductRecipe;
use App\Models\Store\Stock;
use App\Services\StockRecalculationService;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\Schema;


class CashierMobileOrderController extends Controller
{

    protected $unitConversionService;
    protected $xenditInvoice;
    protected $recalculationService;

    public function __construct(UnitConversionService $unitConversionService, XenditService $xendit, StockRecalculationService $recalculationService)
    {
        $this->unitConversionService = $unitConversionService;
        $this->xenditInvoice = new InvoiceController($xendit);
        $this->recalculationService = $recalculationService;
    }

    public function getProducts()
    {
        $partnerId = auth('employee_api')->user()->partner_id;
        $employeeId = auth('employee_api')->id();

        $partner = User::findOrFail($partnerId);
        $partner_products = PartnerProduct::with([
            'category',
            'promotion' => function ($q) {
                $q->activeToday();
            },
            'stock',
            'parent_options.options.stock',
        ])
            ->where('partner_id', $partner->id)
            ->where('is_active', 1)
            ->get();

        $categories = Category::whereIn('id', $partner_products->pluck('category_id'))->get();
        $tables = Table::where('partner_id', $partner->id)->orderBy('table_no', 'ASC')->get();

        return response()->json([
            'debug' => true,
            'partner' => $partnerId,
            'partner_products' => $partner_products,
            'categories' => $categories,
            'tables' => $tables,
        ]);
    }

    public function getOrdersData(Request $request, string $tab)
    {
        $partnerId = auth('employee_api')->user()->partner_id;
        $employeeId = auth('employee_api')->id();

        $payment = $request->string('payment')->toString();
        $status  = $request->string('status')->toString();
        $from = $request->date('from');
        $to   = $request->date('to');
        $q       = $request->string('q')->toString();

        $base = BookingOrder::query()
            ->with('table')
            ->where('partner_id', $partnerId)
            ->whereNotIn('order_status', ['PAYMENT'])
            ->when($from || $to, function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [
                    Carbon::parse($from ?? $to)->startOfDay(),
                    Carbon::parse($to ?? $from)->endOfDay(),
                ]);
            })
            ->when($payment, fn($q2) => $q2->where('payment_method', $payment))
            ->when($status !== null && $status !== '', function ($q2) use ($status) {
                if ($status === '0' || $status === '1') {
                    $q2->where('payment_flag', (int) $status);
                } elseif ($status === 'PROCESSED') {
                    $q2->whereIn('order_status', ['PROCESSED', 'PAID']);
                } else {
                    $q2->where('order_status', $status);
                }
            })
            ->when($q, function ($q2) use ($q) {
                $q2->where(function ($qq) use ($q) {
                    $qq->where('booking_order_code', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%")
                        ->orWhereHas('table', fn($t) => $t->where('table_no', 'like', "%{$q}%"));
                });
            })
            ->orderBy('created_at', 'ASC');
        switch ($tab) {
            case 'pembayaran':
                $items = (clone $base)
                ->whereIn('payment_method', ['CASH','QRIS','manual_tf','manual_ewallet','manual_qris'])
                ->whereIn('order_status', ['UNPAID','EXPIRED','PAYMENT REQUEST'])
                ->latest()
                ->get();
                break;

            case 'proses':
                $items = (clone $base)
                ->whereIn('order_status', ['PROCESSED','PAID'])
                ->where(function ($query) use ($employeeId) {
                    $query->where('cashier_process_id', $employeeId)
                        ->orWhereNull('cashier_process_id');
                })
                ->latest()
                ->get();
                break;

            case 'selesai':
                $items = (clone $base)
                ->where('order_status', 'SERVED')
                ->whereDate('updated_at', Carbon::today())
                ->latest()
                ->get();
                break;

            default:
                abort(404);
        }


        return response()->json([
            'debug' => true,
            'items' => $items,
        ]);
    }
    
    public function checkout(Request $request)
    {
        DB::beginTransaction();
        try {
            $employee = Auth::guard('employee_api')->user();
            $partner = User::findOrFail($employee->partner_id);
            $table = Table::findOrFail($request->order_table);
            $orders = $request->input('items', []);
            $owner = Owner::findOrFail($partner->owner_id);
            $validRegistrationStatuses = ['LIVE', 'LIVE_TESTMODE'];

            // Jika stok tidak cukup, exception akan dilempar dan transaksi akan Rollback.
            $this->checkStockAvailability($orders, $partner);

            if ($request->payment_method === 'QRIS') {
                if (!in_array($owner->xendit_registration_status, $validRegistrationStatuses)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun belum diaktifkan. Silakan hubungi pengelola untuk menyelesaikan proses aktivasi.',
                    ], 422);
                }

                if ($owner->xendit_split_rule_status !== 'created') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pengaturan pembayaran belum lengkap. Silakan hubungi pengelola.',
                    ], 422);
                }
            }

            $booking_order_code = $this->generateBookingOrderCode($partner->partner_code);

            do {
                $suffix = strtoupper(substr((string) Str::ulid(), -8));
                $booking_order_code = "{$partner->partner_code}-{$suffix}";
            } while (
                BookingOrder::where('booking_order_code', $booking_order_code)->exists()
            );

            $booking_order = BookingOrder::create([
                'booking_order_code' => $booking_order_code,
                'partner_id' => $partner->id,
                'partner_name' => $partner->name,
                'table_id' => $table->id,
                'customer_id' => null,
                'employee_order_id' => $employee->id,
                'order_by' => 'CASHIER',
                'customer_name' => 'guest-' . $request->order_name,
                'order_status' => 'UNPAID',
                'payment_method' => $request->payment_method,
                'total_order_value' => $request->total_amount,
            ]);

            $partnerProductIds = [];
            foreach ($orders as $order) {
                $partnerProductIds[] = data_get($order, 'product_id');
                $productId   = data_get($order, 'product_id');
                $optionIds   = data_get($order, 'option_ids', []);
                $qty         = (int) data_get($order, 'qty', 1);
                $note        = data_get($order, 'note', '');
                $promoId    = data_get($order, 'promo_id', null);

                $product = PartnerProduct::with('stock')->findOrFail($productId);
                $options = PartnerProductOption::with('parent', 'stock')->whereIn('id', (array)$optionIds)->get();
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
                    'booking_order_id'  => $booking_order->id,
                    'product_code'  => $product->product_code,
                    'product_name'  => $product->name,
                    'partner_product_id'  => $productId,
                    'base_price'    => $product->price,
                    'promo_id'      => $promoId,
                    'promo_amount'  => $promoAmount,
                    'promo_type'    => $promoType,
                    'options_price' => $optionsPrice ?? 0,
                    'quantity'  => $qty,
                    'customer_note' => $note,
                ]);


                if ($product->stock_type === 'direct' && $product->always_available_flag === 0 && $product->stock) {
                    if (Schema::hasColumn('stocks', 'quantity_reserved')) {
                        $product->stock->increment('quantity_reserved', $qty);
                    } else {
                        $product->stock->decrement('quantity', $qty);
                    }
                } elseif ($product->stock_type === 'linked') {
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
                        if (Schema::hasColumn('stocks', 'quantity_reserved')) {
                            $opt->stock->increment('quantity_reserved', $qty);
                        } else {
                            $opt->stock->decrement('quantity', $qty);
                        }
                    } elseif ($opt->stock_type === 'linked') {
                        $recipes = PartnerProductOptionsRecipe::where('partner_product_option_id', $opt->id)->get();
                        $this->processRecipeReservation($recipes, $qty);
                    }
                }
            }

            if ($request->payment_method === 'QRIS') {
                $booking_order->order_status = 'UNPAID';
                $booking_order->payment_method = 'QRIS';

                $payment = OrderPayment::create([
                    'booking_order_id'  => $booking_order->id,
                    'customer_id'       => null,
                    'customer_name'     => 'guest-' . $request->order_name,
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

                $payload = [
                    "external_id" => $booking_order->booking_order_code ?? "invoice-" . time(),
                    "amount" => $payment->paid_amount ?? 0,
                    "given_names" => $request->order_name ?? "unknow",
                    "description" => "Invoice QRIS",
                    "invoice_duration" => 100,
                    "customer" => [
                        "given_names" => $request->order_name ?? "unknow",
                        "email" => "example@example.com",
                        "mobile_number" => "0",
                    ],
                    "customer_notification_preference" => [
                        "invoice_created" => ["whatsapp", "email"],
                        "invoice_reminder" => ["whatsapp", "email"],
                        "invoice_paid" => ["whatsapp", "email"]
                    ],
                    // "success_redirect_url" => url("employee/cashier/open-order/" . $booking_order->id),
                    // "failure_redirect_url" => url("employee/cashier/open-order/" . $booking_order->id),
                    "success_redirect_url" => "cavapos://payment/success?order_id={$booking_order->id}&code={$booking_order->booking_order_code}", //buat mobile
                    "failure_redirect_url" => "cavapos://payment/failed?order_id={$booking_order->id}&code={$booking_order->booking_order_code}",

                    "currency" => "IDR",
                    "items" => $items,
                    //                    "payment_methods" => ["QRIS"],
                    "metadata" => [
                        "store_branch" => $partner->name
                    ]
                ];

                $invoiceResponse = $this->xenditInvoice->createInvoice($booking_order->id, $xenditSubAccount->xendit_user_id, $xenditSplitRule->split_rule_id, $payload);
                $invoice = $invoiceResponse->getData(true);
                $invoiceData = $invoice['data'] ?? null;
                DB::commit();

                if ($invoice['success']) {
                    return response()->json([
                        'redirect' => $invoiceData['invoice_url']
                    ]);
                }
            }

            DB::commit();

            DB::afterCommit(function () use ($booking_order) {
                event(new OrderCreated($booking_order));
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Product updated successfully!',
                'redirect_tab' => 'pembelian'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Menampilkan pesan error dari checkStockAvailability
            $errorMessage = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan. ' . $errorMessage
            ], 422);
        }
    }

    private function checkStockAvailability(array $orders, $partner): void
    {
        // Menggunakan array untuk mengakumulasi total kebutuhan bahan baku (linked)
        $requiredStock = [];

        foreach ($orders as $order) {
            $productId = data_get($order, 'product_id');
            $optionIds = data_get($order, 'option_ids', []);
            $qty = (int) data_get($order, 'qty', 1);

            // Cek Ketersediaan Produk Utama (Direct/Linked)
            $product = PartnerProduct::with('stock')->find($productId);

            if ($product->always_available_flag === 0) {
                if ($product->stock_type === 'direct') {
                    $productStock = $product->stock;

                    // Cek Direct Stock: Stok harus lebih besar atau sama dengan jumlah yang dipesan
                    if (!$productStock || $productStock->quantity < $qty) {
                        throw new \Exception("Stok {$product->name} (Produk) tidak mencukupi.");
                    }
                } elseif ($product->stock_type === 'linked') {
                    $recipes = PartnerProductRecipe::where('partner_product_id', $productId)->get();
                    $this->accumulateLinkedRequirements($recipes, $qty, $requiredStock, $product->name);
                }
            }

            // Cek Ketersediaan Opsi (Direct/Linked) 
            $options = PartnerProductOption::with('stock')->whereIn('id', (array)$optionIds)->get();
            foreach ($options as $opt) {
                if ($opt->always_available_flag === 0) {
                    if ($opt->stock_type === 'direct') {
                        $optStock = $opt->stock;

                        // Opsi direct stock dikurangi sebesar $qty produk
                        if (!$optStock || $optStock->quantity < $qty) {
                            throw new \Exception("Stok {$opt->name} (Opsi) tidak mencukupi.");
                        }
                    } elseif ($opt->stock_type === 'linked') {
                        $recipes = PartnerProductOptionsRecipe::where('partner_product_option_id', $opt->id)->get();
                        $this->accumulateLinkedRequirements($recipes, $qty, $requiredStock, $opt->name);
                    }
                }
            }
        }

        // Final Check untuk Linked Stock (Cek Akumulasi Total)
        foreach ($requiredStock as $stockId => $totalRequired) {
            $ingredient = Stock::find($stockId);

            if (!$ingredient || $ingredient->quantity < $totalRequired) {
                $name = $ingredient ? $ingredient->stock_name : 'Bahan Baku Tidak Ditemukan';
                throw new \Exception("Bahan Baku '{$name}' tidak mencukupi untuk memenuhi total pesanan.");
            }
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

    private function accumulateLinkedRequirements($recipes, $orderedQuantity, array &$requiredStock, $itemName)
    {
        foreach ($recipes as $recipe) {
            $stockId = $recipe->stock_id;
            $quantityPerUnit = $recipe->quantity_used;
            $totalNeeded = $quantityPerUnit * $orderedQuantity;

            // Akumulasi total kebutuhan untuk stock_id ini
            $requiredStock[$stockId] = ($requiredStock[$stockId] ?? 0) + $totalNeeded;
        }
    }

    public function orderDetail($id)
    {
        $employee = auth('employee_api')->user();

        $order = BookingOrder::with([
            'table',
            'customer',
            'payment',
            'latestPayment',
            'order_details.partnerProduct',
            'order_details.order_detail_options.option.parent',
            'partner',
        ])->findOrFail($id);

        if ($employee->partner_id !== $order->partner_id) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak bisa mengakses order outlet lain',
            ], 403);
        }

        $paymentRequest = null;
        if ($order->order_status === 'PAYMENT REQUEST' && $order->latestPayment) {
            $p = $order->latestPayment;

            $paymentTypeLabel = match ($p->payment_type) {
                'manual_tf'      => 'Transfer Manual',
                'manual_ewallet' => 'E-Wallet Manual',
                'manual_qris'    => 'QRIS Manual/Statis',
                default          => strtoupper((string) $p->payment_type),
            };

            $paymentRequest = [
                'payment_type'                 => $p->payment_type,
                'payment_type_label'           => $paymentTypeLabel,
                'manual_provider_name'         => $p->manual_provider_name,
                'manual_provider_account_name' => $p->manual_provider_account_name,
                'manual_provider_account_no'   => $p->manual_provider_account_no,
                'manual_payment_image'         => $p->manual_payment_image,
            ];
        }

        $storeName = optional($order->partner)->name ?? ($order->username ?? '-');

        $employeeName = $employee->name ?? $employee->user_name ?? '-';

        // Inject ke response
        $order->setAttribute('payment_request', $paymentRequest);
        $order->setAttribute('store_name', $storeName);
        $order->setAttribute('employee_name', $employeeName);

        return response()->json($order);
    }


    public function softDeleteUnpaidOrder($id)
    {
        $order = BookingOrder::findOrFail($id);

        // Filter status agar hanya UNPAID atau EXPIRED yang bisa dihapus
        if (!in_array($order->order_status, ['UNPAID', 'EXPIRED', 'PAYMENT REQUEST'])) {
            return back()->with('error', 'Order ini tidak dapat dihapus.');
        }

        if (method_exists($order, 'trashed') && $order->trashed()) {
            return back()->with('info', 'Order ini sudah dihapus sebelumnya.');
        }

        DB::beginTransaction();
        try {
            $order_details = OrderDetail::where('booking_order_id', $order->id)->get();

            foreach ($order_details as $detail) {
                $partner_product = PartnerProduct::findOrFail($detail->partner_product_id);

                // 1. Kembalikan Reserved Quantity Produk Utama
                if ($partner_product && $partner_product->always_available_flag === 0) {
                    if ($partner_product->stock_type === 'direct') {
                        $stock = Stock::where('partner_product_id', $partner_product->id)
                            ->whereNull('partner_product_option_id')
                            ->first();

                        if ($stock) {
                            $stock->quantity_reserved -= $detail->quantity;
                            $stock->save();

                            // PENTING: Hitung ulang semua produk linked yang menggunakan stok ini
                            $this->recalculationService->recalculateLinkedProducts($stock);
                        }
                    } else {
                        $partner_recipes = PartnerProductRecipe::where('partner_product_id', $partner_product->id)->get();
                        foreach ($partner_recipes as $pr) {
                            $stock = Stock::findOrFail($pr->stock_id);
                            $stock->quantity_reserved -= ($detail->quantity * $pr->quantity_used);
                            $stock->save();

                            // PENTING: Hitung ulang karena stok bahan baku berubah reserved-nya
                            $this->recalculationService->recalculateLinkedProducts($stock);
                        }
                    }
                }

                // 2. Kembalikan Reserved Quantity Opsi Produk
                $order_detail_options = OrderDetailOption::where('order_detail_id', $detail->id)->get();
                foreach ($order_detail_options as $option) {
                    $partner_option = PartnerProductOption::findOrFail($option->option_id);

                    if ($partner_option && $partner_option->always_available_flag === 0) {
                        if ($partner_option->stock_type === 'direct') {
                            $stockOption = Stock::where('partner_product_option_id', $partner_option->id)->first();
                            if ($stockOption) {
                                $stockOption->quantity_reserved -= $detail->quantity;
                                $stockOption->save();

                                $this->recalculationService->recalculateLinkedProducts($stockOption);
                            }
                        } else {
                            $partner_option_recipes = PartnerProductOptionsRecipe::where('partner_product_option_id', $partner_option->id)->get();
                            foreach ($partner_option_recipes as $por) {
                                $stockOption = Stock::findOrFail($por->stock_id);
                                $stockOption->quantity_reserved -= ($detail->quantity * $por->quantity_used);
                                $stockOption->save();

                                $this->recalculationService->recalculateLinkedProducts($stockOption);
                            }
                        }
                    }
                }
            }

            $order->delete(); // Soft Delete
            DB::commit();

            return response()->json([
                'status' => 'ok',
                'message' => 'Order berhasil dihapus dan stok telah diperbarui.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function paymentOrder(Request $request, $id)
    {
        // ✅ auth dari mobile guard
        $cashier = auth('employee_api')->user();

        // ✅ validasi input minimal
        $validated = $request->validate([
            'paid_amount'   => 'required|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
            'note'          => 'nullable|string',
            'email'         => 'nullable|email',
        ]);

        DB::beginTransaction();
        try {
            $booking_order = BookingOrder::with('order_details.order_detail_options.option', 'order_details.partnerProduct')
                ->findOrFail($id);

            // ✅ pastikan order milik outlet yang sama
            if ($cashier->partner_id !== $booking_order->partner_id) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Anda tidak bisa membayar order outlet lain',
                ], 403);
            }

            // ✅ kalau sudah ada yang PAID → update order lalu balik JSON
            $paid_payment = OrderPayment::where('booking_order_id', $id)
                ->where('payment_status', 'PAID')
                ->first();

            if ($paid_payment) {
                $booking_order->order_status = 'PAID';
                $booking_order->payment_flag = true;
                $booking_order->save();

                DB::commit();
                return response()->json([
                    'status' => false,
                    'message' => 'Order ini sudah dibayar',
                    'data' => [
                        'order_id' => $booking_order->id,
                        'order_status' => $booking_order->order_status,
                    ],
                ], 409);
            }

            // ✅ proses payment request (manual / request)
            if ($booking_order->order_status === 'PAYMENT REQUEST') {
                $payment_request = OrderPayment::where('booking_order_id', $id)
                    ->where('payment_status', 'PAYMENT REQUEST')
                    ->first();

                if (!$payment_request) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Payment request tidak ditemukan',
                    ], 404);
                }

                $payment_request->employee_id    = $cashier->id;
                $payment_request->paid_amount    = $validated['paid_amount'];
                $payment_request->change_amount  = $validated['change_amount'] ?? 0;
                $payment_request->payment_status = 'PAID';
                $payment_request->note           = ($payment_request->note ?? '') . '||' . ($validated['note'] ?? '');
                $payment_request->save();

                $booking_order->order_status = 'PAID';
                $booking_order->payment_flag = true;
                $booking_order->save();

                $paymentId = $payment_request->id;
            } else {
                // ✅ payment normal
                $order_payment = OrderPayment::create([
                    'booking_order_id' => $id,
                    'employee_id'      => $cashier->id,
                    'customer_id'      => $booking_order->customer_id ?? null,
                    'customer_name'    => $booking_order->customer_name ?? 'guest',
                    'payment_type'     => $booking_order->payment_method,
                    'paid_amount'      => $validated['paid_amount'],
                    'change_amount'    => $validated['change_amount'] ?? 0,
                    'payment_status'   => 'PAID',
                    'note'             => $validated['note'] ?? null,
                ]);

                $booking_order->order_status   = 'PAID';
                $booking_order->payment_method = 'CASH';
                $booking_order->payment_id     = $order_payment->id;
                $booking_order->payment_flag   = true;
                $booking_order->save();

                $paymentId = $order_payment->id;
            }

            DB::commit();

            // optional
            // SendReceiptEmailJob::dispatch($booking_order->id, $request->input('email'))
            //     ->onQueue('email')->afterCommit();

            return response()->json([
                'status' => true,
                'message' => 'Pembayaran berhasil diproses',
                'data' => [
                    'order_id' => $booking_order->id,
                    'order_status' => $booking_order->order_status,
                    'payment_id' => $paymentId,
                ],
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Order tidak ditemukan',
            ], 404);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
