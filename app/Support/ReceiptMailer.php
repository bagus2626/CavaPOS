<?php

namespace App\Support;

use App\Mail\CustomerReceiptMail;
use App\Models\Transaction\BookingOrder;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReceiptMailer
{
    /**
     * Kirim email struk ke customer.
     *
     * @param  BookingOrder  $order
     * @param  string|null   $fallbackEmail  (opsional) email jika order/customer tidak punya email
     * @return bool
     */
    public static function sendToCustomer(BookingOrder $order, ?string $fallbackEmail = null): bool
    {
        try {
            // Tentukan email tujuan
            $to = null;

            // Jika ada relasi customer model (sesuaikan dengan skema kamu)
            if ($order->customer_id && $order->customer && !empty($order->customer->email)) {
                $to = $order->customer->email;
            }

            // Kalau booking order menyimpan email customer (jika ada kolomnya)
            if (!$to && !empty($order->customer_email ?? null)) {
                $to = $order->customer_email;
            }

            // Fallback manual dari argumen
            if (!$to && $fallbackEmail) {
                $to = $fallbackEmail;
            }

            if (!$to) {
                Log::warning("ReceiptMailer: email tujuan tidak ditemukan untuk order {$order->id}");
                return false;
            }

            // Build data
            $order->loadMissing([
                'order_details.order_detail_options.option',
                'order_details.partnerProduct',
                'payment',
                'table',
            ]);

            $partner  = User::findOrFail($order->partner_id);
            $customer = Auth::guard('customer')->user() ?? session('guest_customer');

            // Buat PDF dari view thermal yang sama
            $customPaper = [0, 0, 227, 600];
            $pdf = Pdf::loadView('pages.employee.cashier.pdf.receipt', [
                'data'     => $order,
                'partner'  => $partner,
                'cashier'  => null,
                'customer' => $customer,
                'payment'  => $order->payment,
            ])->setPaper($customPaper, 'portrait');

            $binary = $pdf->output();

            // Kirim email (gunakan queue jika perlu)
            Mail::to($to)->send(new CustomerReceiptMail(
                $order,
                $partner,
                $customer,
                $order->payment,
                $binary
            ));

            return true;
        } catch (\Throwable $e) {
            Log::error("ReceiptMailer error: {$e->getMessage()}");
            return false;
        }
    }
}
