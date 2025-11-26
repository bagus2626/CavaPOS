<?php

namespace App\Models\Xendit;

use App\Models\Transaction\BookingOrder;
use Illuminate\Database\Eloquent\Model;

class XenditInvoice extends Model
{
    protected $table = 'xendit_invoices';

    protected $fillable = [
        'order_id',
        'xendit_invoice_id',
        'external_id',
        'amount',
        'status',
        'payment_method',
        'invoice_url',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(BookingOrder::class, 'order_id');
    }

    public function splitTransactions()
    {
        return $this->hasMany(SplitTransaction::class, 'xendit_invoice_id');
    }
}
