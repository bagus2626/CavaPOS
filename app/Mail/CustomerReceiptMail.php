<?php

namespace App\Mail;

use App\Models\Transaction\BookingOrder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public BookingOrder $order;
    public User $partner;
    public $customer;
    public $payment;
    protected string $pdfBinary;

    public function __construct(BookingOrder $order, User $partner, $customer, $payment, string $pdfBinary)
    {
        $this->order     = $order;
        $this->partner   = $partner;
        $this->customer  = $customer;
        $this->payment   = $payment;
        $this->pdfBinary = $pdfBinary;
    }

    public function build()
    {
        return $this->subject('Struk ' . $this->partner->name)
            ->markdown('emails.customer.receipt', [   // <- gunakan template kamu sendiri (bukan stub default)
                'order'    => $this->order,
                'partner'  => $this->partner,
                'customer' => $this->customer,
                'payment'  => $this->payment,
            ])
            ->attachData(
                $this->pdfBinary,
                'receipt-' . $this->order->booking_order_code . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
