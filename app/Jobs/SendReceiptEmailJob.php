<?php

namespace App\Jobs;

use App\Mail\CustomerReceiptMail;
use App\Models\Transaction\BookingOrder;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReceiptEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // retry & backoff opsional
    public $tries = 3;
    public $backoff = [60, 300, 900];

    // public string $queue = 'mail';

    protected int $orderId;
    protected ?string $fallbackEmail;

    public function __construct(int $orderId, ?string $fallbackEmail = null)
    {
        $this->orderId = $orderId;
        $this->fallbackEmail = $fallbackEmail;

        // taruh di queue khusus (opsional)
        $this->onQueue('mail');
    }

    public function handle(): void
    {
        // Ambil data segar dari DB (jangan serialisasikan model besar di payload job)
        $order = BookingOrder::with([
            'order_details.order_detail_options.option',
            'order_details.partnerProduct',
            'payment',
            'table',
            'customer', // kalau ada relasi customer()
        ])->findOrFail($this->orderId);

        // Tentukan email tujuan
        $to = $order->customer->email
            ?? ($order->customer_email ?? null)
            ?? $this->fallbackEmail;

        if (!$to) {
            // Tidak ada tujuan email — cukup keluar; biar nggak fail job
            return;
        }

        $partner  = User::findOrFail($order->partner_id);
        $customer = $order->customer ?? null;

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

        // Kirim email (pakai mailable yang sudah dibuat)
        Mail::to($to)->send(new CustomerReceiptMail(
            $order,
            $partner,
            $customer,
            $order->payment,
            $binary
        ));
    }
}
