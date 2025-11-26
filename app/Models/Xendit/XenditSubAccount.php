<?php

namespace App\Models\Xendit;

use Illuminate\Database\Eloquent\Model;

class XenditSubAccount extends Model
{
    protected $table = 'xendit_sub_accounts';

    protected $fillable = [
        'partner_id',
        'xendit_user_id',
        'business_name',
        'email',
        'type',
        'status',
        'country',
        'master_acc_business_id',
        'payments_enabled',
        'created_xendit',
        'updated_xendit',
        'suspended_reason',
        'suspended_at',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(\App\Models\Owner::class, 'partner_id');
    }

    public function splitTransactions()
    {
        return $this->hasMany(SplitTransaction::class, 'sub_account_id');
    }
}
