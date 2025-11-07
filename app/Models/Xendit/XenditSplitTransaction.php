<?php

namespace App\Models\Xendit;

use Illuminate\Database\Eloquent\Model;

class XenditSplitTransaction extends Model
{
    protected $table = 'xendit_split_transactions';

    protected $fillable = [
        'xendit_invoice_id',
        'split_rule_id',
        'xendit_split_payment_id',
        'reference_id',
        'payment_id',
        'payment_reference_id',
        'source_account_id',
        'destination_account_id',
        'account_type',
        'amount',
        'percentage',
        'status',
        'currency',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(XenditInvoice::class, 'xendit_invoice_id', 'xendit_invoice_id');
    }

    public function splitRule()
    {
        return $this->belongsTo(SplitRule::class, 'split_rule_id', 'split_rule_id');
    }

    public function sourceAccount()
    {
        return $this->belongsTo(XenditSubAccount::class, 'source_account_id', 'xendit_user_id');
    }

    public function destinationAccount()
    {
        return $this->belongsTo(XenditSubAccount::class, 'destination_account_id', 'xendit_user_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'COMPLETED');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'FAILED');
    }
}

