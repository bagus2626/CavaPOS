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

class CashierTransactionController extends Controller
{

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
            // dd($request->all());
            $employee = Auth::guard('employee')->user();
            $partner = User::findOrFail($employee->partner_id);
            $table = Table::findOrFail($request->order_table);
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
                $booking_order->save();
            }

            event(new OrderCreated($booking_order));

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Product updated successfully!',
                'redirect_tab' => 'pembelian'
            ]);
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
}
