<?php

namespace App\Models\Xendit;

use Illuminate\Database\Eloquent\Model;

class XenditWebhookLog extends Model
{
    protected $table = 'xendit_webhook_logs';

    protected $fillable = [
        'event',
        'xendit_id',
        'payload',
        'status',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(XenditInvoice::class, 'xendit_invoice_id', 'xendit_invoice_id');
    }
}
