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
