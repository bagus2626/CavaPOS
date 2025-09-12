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
        $partner_products = PartnerProduct::with('category', 'parent_options.options')->where('partner_id', $partner->id)->get();
        $categories = Category::where('partner_id', $partner->id)->get();

        return view('pages.customer.menu.index', compact('table', 'customer', 'partner', 'partner_products', 'categories'));
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
                'table_id' => $table->id,
                'customer_id' => $customer ? $customer->id : null,
                'customer_name' => $customer ? $customer->name : 'guest-' . $request->order_name,
                'order_status' => 'UNPAID',
                'payment_method' => $request->payment_method,
                'total_order_value' => $request->total_amount,
            ]);


            foreach ($orders as $order) {
                $productId   = data_get($order, 'product_id');
                $optionIds   = data_get($order, 'option_ids', []);
                $qty         = (int) data_get($order, 'qty', 1);
                $note        = data_get($order, 'note', '');

                $product = PartnerProduct::findOrFail($productId);
                $options = PartnerProductOption::whereIn('id', (array)$optionIds)->get();
                $optionsPrice = $options->sum('price');

                $order_detail = OrderDetail::create([
                    'booking_order_id'    => $booking_order->id,
                    'partner_product_id'  => $productId,
                    'base_price'          => $product->price,
                    'options_price'     => $optionsPrice ?? 0,   // isi jika ingin simpan
                    'quantity'          => $qty,
                    'customer_note'       => $note,
                ]);

                foreach ($options as $opt) {
                    OrderDetailOption::create([
                        'order_detail_id' => $order_detail->id,
                        'option_id' => $opt->id,
                        'price' => $opt->price
                    ]);
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
            }

            event(new OrderCreated($booking_order));

            DB::commit();

            $token = Crypt::encrypt([
                'p' => $partner_slug,
                't' => $table_code,
                'o' => $booking_order->id,
            ]);

            if ($request->payment_method === 'QRIS') {
                return redirect()->route('menu.index')->with('success', 'Product updated successfully!');
            } else {
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
            }
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
}
