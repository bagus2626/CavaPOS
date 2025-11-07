<?php

namespace App\Models\Xendit;

use Illuminate\Database\Eloquent\Model;

class SplitRule extends Model
{
    protected $table = 'split_rules';

    protected $fillable = [
        'partner_id',
        'split_rule_id',
        'name',
        'description',
        'routes',
        'raw_response',
    ];

    protected $casts = [
        'routes' => 'array',
        'raw_response' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(\App\Models\Owner::class, 'partner_id');
    }

    public function splitTransactions()
    {
        return $this->hasMany(SplitTransaction::class, 'split_rule_id');
    }
}
