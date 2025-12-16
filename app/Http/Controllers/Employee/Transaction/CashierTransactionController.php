<?php

namespace App\Http\Controllers\Employee\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentGateway\Xendit\InvoiceController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction\OrderPayment;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Product\Promotion;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Transaction\BookingOrder;
use App\Models\Transaction\OrderDetail;
use App\Models\Transaction\OrderDetailOption;
use App\Models\Xendit\SplitRule;
use App\Models\Xendit\XenditSubAccount;
use App\Services\XenditService;
use App\Models\Product\Specification;
use App\Models\Admin\Product\Category;
use App\Models\Owner;
use App\Models\User;
use App\Models\Store\Table;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use App\Events\OrderCreated;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\SendReceiptEmailJob;
use App\Models\Partner\Products\PartnerProductOptionsRecipe;
use App\Models\Partner\Products\PartnerProductRecipe;
use App\Models\Store\Stock;
use App\Models\Store\StockMovement;
use App\Services\StockRecalculationService;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CashierTransactionController extends Controller
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

    public function orderDetail($id)
    {
        $order = BookingOrder::with([
            'table',
            'customer',
            'payment',
            'order_details.partnerProduct',
            'order_details.order_detail_options.option.parent'
        ])->findOrFail($id);
        return response()->json($order);
    }
    public function cashPayment(Request $request, $id)
    {
        // dd($request->all());
        $cashier = Auth::user();
        $booking_order = BookingOrder::with('order_details.order_detail_options.option', 'order_details.partnerProduct')
            ->findOrFail($id);
        $payment = OrderPayment::where('booking_order_id', $id)
            ->where('payment_status', 'PAID')
            ->first();
        if ($payment) {

            $booking_order->order_status = 'PAID';
            $booking_order->payment_flag = true;
            $booking_order->save();

            DB::commit();
            return redirect()->back()->with('error', 'Order ini sudah dibayar! Coba Refresh halaman.');
        }

        if (!$booking_order) {
            return redirect()->back()->with('error', 'Order tidak ditemukan');
        }

        if ($cashier->partner_id !== $booking_order->partner_id) {
            return redirect()->back()->with('error', 'Anda tidak bisa membayar order outlet lain');
        }

        DB::beginTransaction();
        try {


            $order_payment = OrderPayment::create([
                'booking_order_id' => $id,
                'employee_id' => $cashier->id,
                'customer_id' => $booking_order->customer_id ?? null,
                'customer_name' => $booking_order->customer_name ?? 'guest',
                'payment_type' => $booking_order->payment_method,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $request->change_amount,
                'payment_status' => 'PAID',
                'note' => $request->note ?? null
            ]);

            $booking_order->order_status = 'PAID';
            $booking_order->payment_method = 'CASH';
            $booking_order->payment_id = $order_payment->id;
            $booking_order->payment_flag = true;
            $booking_order->save();

            DB::commit();

            SendReceiptEmailJob::dispatch($booking_order->id, $request->input('email'))
                ->onQueue('email')
                ->afterCommit();

            return redirect()->back()->with('success', 'Pembayaran berhasil diproses!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // kalau booking_order tidak ketemu
            return redirect()->back()->with('error', 'Order tidak ditemukan!');
        } catch (\Exception $e) {
            // general error lainnya
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function finishOrder(Request $request, $id)
    {
        $cashier = Auth::user();
        $partner = User::findOrFail($cashier->partner_id);

        DB::beginTransaction();
        try {

            $booking_order = BookingOrder::with([
                'order_details.partnerProduct.stock',
                'order_details.partnerProduct.recipes',
                'order_details.order_detail_options.option.stock',
                'order_details.order_detail_options.option.recipes',
            ])->findOrFail($id);

            if (!$booking_order) {
                return redirect()->back()->with('error', 'Order tidak ditemukan');
            }

            if ($cashier->partner_id !== $booking_order->partner_id) {
                return redirect()->back()->with('error', 'Anda tidak bisa menyelesaikan order outlet lain');
            }

            $masterMovement = StockMovement::create([
                'owner_id'   => $partner->owner_id,
                'partner_id' => $partner->id,
                'type'       => 'out',
                'category'   => 'sale',
            ]);

            // 2. PENGURANGAN FISIK & PENCATATAN MOVEMENT ITEM
            foreach ($booking_order->order_details as $detail) {
                $qty = $detail->quantity;
                $product = $detail->partnerProduct;
                if ($product) {
                    // A. Pengurangan Produk Utama
                    if ($product->stock_type === 'direct' && $product->always_available_flag === 0 && $product->stock) {
                        // Kurangi fisik (quantity) dan hapus reservasi (quantity_reserved)
                        $this->processStockConsumption($product->stock, $qty, $masterMovement);
                    } elseif ($product->stock_type === 'linked') {
                        // Kurangi bahan baku (ingredients)
                        $this->processRecipeConsumption($product->recipes, $qty, $masterMovement);
                    }

                    // B. Pengurangan Opsi Produk
                    foreach ($detail->order_detail_options as $detailOption) {
                        $opt = $detailOption->option;
                        if (!$opt) continue;

                        if ($opt->stock_type === 'direct' && $opt->always_available_flag === 0 && $opt->stock) {
                            $this->processStockConsumption($opt->stock, $qty, $masterMovement);
                        } elseif ($opt->stock_type === 'linked') {
                            $this->processRecipeConsumption($opt->recipes, $qty, $masterMovement);
                        }
                    }
                }
            }

            $booking_order->order_status = 'SERVED';
            $booking_order->employee_order_note = trim(
                ($booking_order->employee_order_note ?? '') . '|| ' . 'CASHIER ' . $cashier->name . ': ' . $request->note
            );
            $booking_order->save();

            DB::commit();
            return redirect()->back()->with('success', 'Order Berhasil diselesaikan!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // kalau booking_order tidak ketemu
            return redirect()->back()->with('error', 'Order tidak ditemukan!');
        } catch (\Exception $e) {
            // general error lainnya
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function checkout(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $employee = Auth::guard('employee')->user();
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
                    "invoice_duration" => 600,
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
                    "success_redirect_url" => url("employee/cashier/open-order/" . $booking_order->id),
                    "failure_redirect_url" => url("employee/cashier/open-order/" . $booking_order->id),
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
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal membuat pesanan. ' . $errorMessage]);
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
        // dd($id);
        $cashier = Auth::user();
        $data = BookingOrder::with(
            'order_details.order_detail_options.option',
            'order_details.partnerProduct',
            'payment'
        )
            ->findOrFail($id);
        $payment = $data->payment;
        if ($cashier->partner_id !== $data->partner_id) {
            return redirect()->back()->with('error', 'Tidak bisa print order toko lain');
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

        // Lebar thermal: 80mm (≈227pt), tinggi auto (0)
        $customPaper = [0, 0, 227, 600];
        // kalau mau lebih panjang lagi, naikkan angka terakhir (tinggi)

        $pdf = Pdf::loadView('pages.employee.cashier.pdf.receipt', [
            'data'    => $data,
            'partner' => $partner,
            'cashier' => $cashier,
            'payment' => $payment
        ])
            ->setPaper($customPaper, 'portrait');

        return $pdf->stream("receipt-{$data->booking_order_code}.pdf");
    }

    // Di dalam Controller
    public function processOrder($id)
    {
        $cashier = Auth::user();
        $booking_order = BookingOrder::with('order_details')->findOrFail($id);

        // 1. Verifikasi Kepemilikan (Tetap)
        if ($booking_order->partner_id !== $cashier->partner_id) {
            return response()->json(['status' => 'error', 'message' => 'Tidak bisa proses order toko lain!'], 403);
        }

        DB::beginTransaction();
        try {
            // 2. VERIFIKASI STATUS KRITIS: Cek apakah order sudah PROCESSED atau SERVED
            if (in_array($booking_order->order_status, ['PROCESSED', 'SERVED'])) {
                DB::rollBack(); // Tidak ada perubahan, jadi rollback aman.

                // Berikan respon sukses SEMU (TAPI status khusus)
                return response()->json([
                    'status' => 'warning', // Status baru untuk frontend
                    'message' => 'Order ini sudah diproses oleh tim lain (Kitchen). Order akan di-refresh.',
                    'already_processed' => true // Flag khusus
                ]);
            }

            // 3. Jika status masih UNPAID/PENDING, lanjutkan proses
            $booking_order->order_status = 'PROCESSED';
            $booking_order->save();

            foreach ($booking_order->order_details as $detail) {
                $detail->status = 'PROCESSED BY CASHIER';
                $detail->save();
            }

            DB::commit();

            return response()->json(['status' => 'ok', 'message' => 'Order berhasil diproses.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
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

    /**
     * Akumulasi total kebutuhan bahan baku untuk semua item linked.
     */
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
     * Melakukan pengurangan fisik dan menghapus reservasi untuk Direct Stock.
     */
    private function processStockConsumption(Stock $stock, int $qty, StockMovement $masterMovement): void
    {
        $reservedColumnExists = Schema::hasColumn('stocks', 'quantity_reserved');

        $updateData = [
            // Mengurangi kolom quantity fisik
            'quantity' => DB::raw('quantity - ' . $qty)
        ];

        if ($reservedColumnExists) {
            // Mengurangi kolom quantity_reserved (membersihkan reservasi)
            $updateData['quantity_reserved'] = DB::raw('quantity_reserved - ' . $qty);
        }

        $stock->update($updateData);

        $masterMovement->items()->create([
            'stock_id' => $stock->id,
            'quantity' => $qty,
            'unit_price' => $stock->last_price_per_unit ?? 0,
        ]);
    }

    /**
     * Mengurangi fisik dan mencatat konsumsi untuk Linked Stock (saat served).
     */
    private function processRecipeConsumption($recipes, int $orderedQuantity, StockMovement $masterMovement): void
    {
        foreach ($recipes as $recipe) {
            $ingredientStock = Stock::find($recipe->stock_id);

            if (!$ingredientStock) {
                continue;
            }

            $quantityPerUnit = $recipe->quantity_used;
            $totalQuantityToConsume = $quantityPerUnit * $orderedQuantity;

            // 1. Pengurangan Stok Fisik (quantity)
            $ingredientStock->decrement('quantity', $totalQuantityToConsume);

            // 2. Pengurangan Reservasi (quantity_reserved)
            if (Schema::hasColumn('stocks', 'quantity_reserved')) {
                $ingredientStock->decrement('quantity_reserved', $totalQuantityToConsume);
            }

            // 3. Pencatatan Movement Item
            $masterMovement->items()->create([
                'stock_id' => $ingredientStock->id,
                'quantity' => $totalQuantityToConsume,
                'unit_price' => $ingredientStock->last_price_per_unit ?? 0,
            ]);

            // PANGGIL RECALCULATION SERVICE
            $this->recalculationService->recalculateLinkedProducts($ingredientStock);
        }
    }

    /**
     * Check stock availability in real-time sebelum checkout
     */
    public function checkStockRealtime(Request $request)
    {
        try {
            Log::info('Stock check request received', [
                'items' => $request->input('items', [])
            ]);

            $items = $request->input('items', []);

            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada item untuk dicek',
                    'unavailable_items' => []
                ], 400);
            }

            $unavailable = [];

            foreach ($items as $item) {
                $productId = data_get($item, 'product_id');
                $optionIds = data_get($item, 'option_ids', []);
                $qty = (int) data_get($item, 'qty', 1);

                if (!$productId) {
                    Log::warning('Product ID missing in item', ['item' => $item]);
                    continue;
                }

                // ===== CEK PRODUK UTAMA =====
                try {
                    $product = PartnerProduct::with(['stock', 'recipes.stock'])
                        ->find($productId);

                    if (!$product) {
                        Log::warning('Product not found', ['product_id' => $productId]);
                        continue;
                    }

                    // Skip jika always available
                    if ($product->always_available_flag == 1) {
                        continue;
                    }

                    if ($product->stock_type === 'direct') {
                        $stock = $product->stock;

                        if ($stock) {
                            $available = (int)($stock->quantity ?? 0) - (int)($stock->quantity_reserved ?? 0);

                            if ($available < $qty) {
                                $unavailable[] = [
                                    'name' => $product->name,
                                    'type' => 'Produk',
                                    'requested' => $qty,
                                    'available' => max(0, $available)
                                ];
                            }
                        }
                    } elseif ($product->stock_type === 'linked') {
                        // Hitung manual untuk linked stock
                        $recipes = PartnerProductRecipe::where('partner_product_id', $productId)->get();

                        if ($recipes->isEmpty()) {
                            continue;
                        }

                        $minAvailable = PHP_INT_MAX;

                        foreach ($recipes as $recipe) {
                            $ingredientStock = Stock::find($recipe->stock_id);

                            if (!$ingredientStock) {
                                $minAvailable = 0;
                                break;
                            }

                            $stockAvailable = (int)($ingredientStock->quantity ?? 0) - (int)($ingredientStock->quantity_reserved ?? 0);
                            $quantityPerUnit = (float)$recipe->quantity_used;

                            if ($quantityPerUnit > 0) {
                                $canMake = floor($stockAvailable / $quantityPerUnit);
                                $minAvailable = min($minAvailable, $canMake);
                            } else {
                                $minAvailable = 0;
                                break;
                            }
                        }

                        $available = ($minAvailable === PHP_INT_MAX) ? 0 : $minAvailable;

                        if ($available < $qty) {
                            $unavailable[] = [
                                'name' => $product->name,
                                'type' => 'Produk (Linked)',
                                'requested' => $qty,
                                'available' => max(0, $available)
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error checking product stock', [
                        'product_id' => $productId,
                        'error' => $e->getMessage()
                    ]);
                }

                // ===== CEK OPSI PRODUK =====
                if (!empty($optionIds)) {
                    try {
                        $options = PartnerProductOption::with(['stock', 'recipes.stock'])
                            ->whereIn('id', (array)$optionIds)
                            ->get();

                        foreach ($options as $opt) {
                            // Skip jika always available
                            if ($opt->always_available_flag == 1) {
                                continue;
                            }

                            if ($opt->stock_type === 'direct') {
                                $stock = $opt->stock;

                                if ($stock) {
                                    $available = (int)($stock->quantity ?? 0) - (int)($stock->quantity_reserved ?? 0);

                                    if ($available < $qty) {
                                        $unavailable[] = [
                                            'name' => $opt->name,
                                            'type' => 'Opsi',
                                            'requested' => $qty,
                                            'available' => max(0, $available)
                                        ];
                                    }
                                }
                            } elseif ($opt->stock_type === 'linked') {
                                $recipes = PartnerProductOptionsRecipe::where('partner_product_option_id', $opt->id)->get();

                                if ($recipes->isEmpty()) {
                                    continue;
                                }

                                $minAvailable = PHP_INT_MAX;

                                foreach ($recipes as $recipe) {
                                    $ingredientStock = Stock::find($recipe->stock_id);

                                    if (!$ingredientStock) {
                                        $minAvailable = 0;
                                        break;
                                    }

                                    $stockAvailable = (int)($ingredientStock->quantity ?? 0) - (int)($ingredientStock->quantity_reserved ?? 0);
                                    $quantityPerUnit = (float)$recipe->quantity_used;

                                    if ($quantityPerUnit > 0) {
                                        $canMake = floor($stockAvailable / $quantityPerUnit);
                                        $minAvailable = min($minAvailable, $canMake);
                                    } else {
                                        $minAvailable = 0;
                                        break;
                                    }
                                }

                                $available = ($minAvailable === PHP_INT_MAX) ? 0 : $minAvailable;

                                if ($available < $qty) {
                                    $unavailable[] = [
                                        'name' => $opt->name,
                                        'type' => 'Opsi (Linked)',
                                        'requested' => $qty,
                                        'available' => max(0, $available)
                                    ];
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Error checking option stock', [
                            'option_ids' => $optionIds,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            Log::info('Stock check completed', [
                'unavailable_count' => count($unavailable),
                'unavailable_items' => $unavailable
            ]);

            return response()->json([
                'success' => empty($unavailable),
                'unavailable_items' => $unavailable,
                'message' => empty($unavailable)
                    ? 'Semua stok tersedia'
                    : 'Beberapa item tidak tersedia'
            ]);
        } catch (\Exception $e) {
            Log::error('Stock check fatal error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa stok: ' . $e->getMessage(),
                'unavailable_items' => []
            ], 500);
        }
    }

    public function softDeleteUnpaidOrder($id)
    {
        $order = BookingOrder::findOrFail($id);

        // Kalau mau dibatasi hanya order UNPAID, bisa aktifkan ini:
        if (!in_array($order->order_status, ['UNPAID', 'EXPIRED'])) {
            return back()->with('error', 'Order ini tidak dapat dihapus.');
        }

        if (method_exists($order, 'trashed') && $order->trashed()) {
            return back()->with('info', 'Order ini sudah dihapus sebelumnya.');
        }

        $order_details = OrderDetail::where('booking_order_id', $order->id)->get();
        foreach ($order_details as $detail) {
            $partner_product = PartnerProduct::findOrFail($detail->partner_product_id);
            if ($partner_product && $partner_product->always_available_flag === 0) {
                if ($partner_product->stock_type === 'direct') {
                    $stock = Stock::where('partner_product_id', $partner_product->id)
                        ->whereNull('partner_product_option_id')
                        ->first();
                    $stock->quantity_reserved -= ($detail->quantity);
                    $stock->save();
                } else {
                    $partner_recipes = PartnerProductRecipe::where('partner_product_id', $partner_product->id)->get();
                    foreach ($partner_recipes as $pr) {
                        $stock = Stock::findOrFail($pr->stock_id);
                        $stock->quantity_reserved -= ($detail->quantity * $pr->quantity_used);
                        $stock->save();
                    }
                }
            }

            $order_detail_options = OrderDetailOption::where('order_detail_id', $detail->id)->get();
            if ($order_detail_options) {
                foreach ($order_detail_options as $option) {
                    $partner_option = PartnerProductOption::findOrFail($option->option_id);
                    if ($partner_option && $partner_option->always_available_flag === 0) {
                        if ($partner_option->stock_type === 'direct') {
                            $stockOption = Stock::where('partner_product_option_id', $partner_option->id)->first();
                            $stockOption->quantity_reserved -= $detail->quantity;
                            $stockOption->save();
                        } else {
                            $partner_option_recipes = PartnerProductOptionsRecipe::where('partner_product_option_id', $partner_option->id)->get();
                            foreach ($partner_option_recipes as $por) {
                                $stockOption = Stock::findOrFail($por->stock_id);
                                $stockOption->quantity_reserved -= ($detail->quantity * $por->quantity_used);
                                $stockOption->save();
                            }
                        }
                    }
                }
            }
        }

        $order->delete(); // soft delete (set deleted_at)

        return back()->with('success', 'Order berhasil dihapus.');
    }
}
