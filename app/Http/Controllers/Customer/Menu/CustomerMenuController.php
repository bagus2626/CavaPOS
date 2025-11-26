<?php

namespace App\Http\Controllers\Customer\Menu;

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

class CustomerMenuController extends Controller
{
    public function index($partner_slug, $table_code)
    {
        if (!Auth::guard('customer')->check() && !session()->has('guest_customer')) {
            // Belum login, tampilkan pilihan login
            return view('pages.customer.auth.login_choice', compact('partner_slug', 'table_code'));
        }

        // Sudah login / guest
        $customer = Auth::guard('customer')->user() ?? session('guest_customer');

        // Ambil menu dari partner/table
        $table = Table::where('table_code', $table_code)
            ->whereHas('partner', fn($q) => $q->where('slug', $partner_slug))
            ->firstOrFail();
        $partner = User::where('slug', $partner_slug)->first();

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

        $owner = Owner::where('id', $partner->owner_id)->first();
        $categories = Category::whereIn('id', $partner_products->pluck('category_id'))->get();

        return view('pages.customer.menu.index', compact('table', 'customer', 'partner', 'partner_products', 'categories'));
    }

    public function checkout(Request $request, $partner_slug, $table_code)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            // dd($request->all());
            $customer = Auth::guard('customer')->user();
            // dd($user->name ?? 'guest', $partner_slug, $table_code, $request->all());
            $partner = User::where('slug', $partner_slug)->first();
            $table = Table::where('table_code', $table_code)->first();
            $orders = $request->input('items', []);

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


            foreach ($orders as $order) {
                // dd($order);
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
                    $product->stock->decrement('quantity', $qty);
                }

                if ($product->stock_type === 'linked') {
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
                    if ($opt->stock_type === 'direct' && $opt->always_available_flag === 0 && $opt->stock) {
                        $opt->stock->decrement('quantity', $qty);
                    }
                    if ($opt->stock_type === 'linked') {
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

                $payment = OrderPayment::create([
                    'booking_order_id' => $booking_order->id,
                    'customer_id' => $customer ? $customer->id : null,
                    'customer_name' => $customer ? $customer->name : 'guest-' . $request->order_name,
                    'payment_type' => 'QRIS',
                    'paid_amount' => $request->total_amount,
                    'change_amount' => 0,
                    'payment_status' => 'PAID'
                ]);

                $booking_order->payment_id = $payment->id;
                $booking_order->save();

                DB::commit();
                DB::afterCommit(function () use ($booking_order) {
                    event(new OrderCreated($booking_order));
                });
                SendReceiptEmailJob::dispatch($booking_order->id, $request->input('email'))
                    ->onQueue('email')
                    ->afterCommit();
                // return redirect()->route('customer.orders.receipt', $booking_order->id);
                return redirect()->back()->with('success', 'Product updated successfully!');
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

        // Validasi kepemilikan hanya jika order memang punya customer_id
        if ($data->customer_id) {
            if (!$customer || ($customer->id ?? null) !== $data->customer_id) {
                // jangan redirect/HTML ke login di sini — kirim 403 murni
                abort(403, 'Tidak bisa print order pelanggan lain');
            }
        }

        $partner = User::findOrFail($data->partner_id);

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

        Storage::put("receipts/debug-{$data->booking_order_code}.pdf", $pdf->output());

        // download file hasil simpan
        return Storage::download("receipts/debug-{$data->booking_order_code}.pdf");
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
