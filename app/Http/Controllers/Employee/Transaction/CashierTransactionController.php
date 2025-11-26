<?php

namespace App\Http\Controllers\Employee\Transaction;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Partner\HumanResource\Employee;
use App\Models\Product\Product;
use App\Models\Transaction\OrderPayment;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Product\Promotion;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Transaction\BookingOrder;
use App\Models\Transaction\OrderDetail;
use App\Models\Transaction\OrderDetailOption;
use App\Models\Product\Specification;
use App\Models\Admin\Product\Category;
use App\Models\User;
use App\Models\Store\Table;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Events\OrderCreated;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\SendReceiptEmailJob;
use App\Models\Partner\Products\PartnerProductOptionsRecipe;
use App\Models\Partner\Products\PartnerProductRecipe;
use App\Models\Store\Stock;
use App\Services\UnitConversionService;

class CashierTransactionController extends Controller
{

    protected $unitConversionService;

    public function __construct(UnitConversionService $unitConversionService)
    {
        $this->unitConversionService = $unitConversionService;
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
        // dd($order);
        return response()->json($order);
    }
    public function cashPayment(Request $request, $id)
    {
        // dd($request->all());
        $cashier = Auth::user();
        $booking_order = BookingOrder::with('order_details.order_detail_options.option', 'order_details.partnerProduct')
            ->findOrFail($id);
        $payment = OrderPayment::where('booking_order_id', $id)->first();
        if ($payment) {
            // dd('payemtn', $payment);
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
        // dd($request->all());
        DB::beginTransaction();
        try {
            $cashier = Auth::user();
            $booking_order = BookingOrder::findOrFail($id);

            if (!$booking_order) {
                return redirect()->back()->with('error', 'Order tidak ditemukan');
            }

            if ($cashier->partner_id !== $booking_order->partner_id) {
                return redirect()->back()->with('error', 'Anda tidak bisa menyelesaikan order outlet lain');
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
        DB::beginTransaction();
        try {
            $employee = Auth::guard('employee')->user();
            $partner = User::findOrFail($employee->partner_id);
            $table = Table::findOrFail($request->order_table);
            $orders = $request->input('items', []);

            // Jika stok tidak cukup, exception akan dilempar dan transaksi akan Rollback.
            $this->checkStockAvailability($orders, $partner);

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


            foreach ($orders as $order) {
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
                    $product->stock->decrement('quantity', $qty);
                } elseif ($product->stock_type === 'linked') {
                    $recipes = PartnerProductRecipe::where('partner_product_id', $productId)->get();
                    $this->processRecipeDeduction($recipes, $qty);
                }

                foreach ($options as $opt) {
                    OrderDetailOption::create([
                        'order_detail_id' => $order_detail->id,
                        'parent_name' => $opt->parent->name ?? null,
                        'partner_product_option_name' => $opt->name,
                        'option_id' => $opt->id,
                        'price' => $opt->price
                    ]);

                    // B. Opsi Produk
                    if ($opt->stock_type === 'direct' && $opt->always_available_flag === 0 && $opt->stock) {
                        $opt->stock->decrement('quantity', $qty);
                    } elseif ($opt->stock_type === 'linked') {
                        $recipes = PartnerProductOptionsRecipe::where('partner_product_option_id', $opt->id)->get();
                        $this->processRecipeDeduction($recipes, $qty);
                    }
                }
            }

            // test by qris (hapus kemudian)
            if ($request->payment_method === 'QRIS') {
                $booking_order->order_status = 'PAID';
                $booking_order->payment_method = 'QRIS';
                $booking_order->payment_flag = true;
                $booking_order->save();
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

    public function processOrder($id)
    {
        $cashier = Auth::user();
        $booking_order = BookingOrder::with('order_details')->findOrFail($id);
        if ($booking_order->partner_id !== $cashier->partner_id) {
            return redirect()->back()->with('error', 'Tidak bisa proses order toko lain!');
        }

        DB::beginTransaction();
        try {

            $booking_order->order_status = 'PROCESSED';
            $booking_order->save();
            foreach ($booking_order->order_details as $detail) {
                $detail->status = 'PROCESSED BY CASHIER';
                $detail->save();
            }
            DB::commit();

            return response()->json(['status' => 'ok', 'message' => 'Order processed']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // kalau booking_order tidak ketemu
            return response()->json(['status' => 'error', 'message' => 'Gagal Proses Order']);
        } catch (\Exception $e) {
            // general error lainnya
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
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

    private function processRecipeDeduction($recipes, int $orderedQuantity): void
    {
        foreach ($recipes as $recipe) {

            $ingredientStock = Stock::find($recipe->stock_id);

            // Jika stok bahan mentah tidak ditemukan (misal dihapus), lewati atau log warning.
            if (!$ingredientStock) {
                continue;
            }

            $quantityPerUnit = $recipe->quantity_used;

            // Hitung Total Konsumsi
            $totalQuantityToConsume = $quantityPerUnit * $orderedQuantity;

            // Pengurangan Stok Bahan Mentah
            $ingredientStock->decrement('quantity', $totalQuantityToConsume);
        }
    }
}
