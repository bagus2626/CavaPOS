<?php

namespace App\Models\Xendit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XenditPayout extends Model
{
    use HasFactory;

    protected $table = 'xendit_payouts';

    protected $fillable = [
        'payout_id',
        'reference_id',
        'business_id',
        'amount',
        'currency',
        'channel_code',
        'status',
        'failure_code',
        'account_holder_name',
        'account_number',
        'account_type',
        'email_to',
        'email_cc',
        'email_bcc',
        'metadata',
        'estimated_arrival_time',
        'created_xendit',
        'updated_xendit',
        'raw_response',
        'description',
        'idempotency_key',
        'channel_category',
        'connector_reference',
    ];

    protected $casts = [
        'email_to' => 'array',
        'email_cc' => 'array',
        'email_bcc' => 'array',
        'metadata' => 'array',
        'raw_response' => 'array',
        'estimated_arrival_time' => 'datetime',
        'created_xendit' => 'datetime',
        'updated_xendit' => 'datetime',
    ];

    public function subAccount()
    {
        return $this->belongsTo(XenditSubAccount::class, 'business_id', 'xendit_user_id');
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', strtoupper($status));
    }


    public function scopeReference($query, $referenceId)
    {
        return $query->where('reference_id', $referenceId);
    }

    public function scopeChannel($query, $channelCode)
    {
        return $query->where('channel_code', $channelCode);
    }
}
