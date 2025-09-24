<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Owner;
use Carbon\Carbon;


class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_code',
        'owner_id',
        'partner_id',
        'promotion_name',
        'promotion_type',
        'promotion_value',
        'start_date',
        'end_date',
        'is_active',
        'uses_expiry',
        'active_days',
        'description',
    ];

    protected $casts = [
        'start_date'   => 'datetime',
        'end_date'     => 'datetime',
        'is_active'    => 'boolean',
        'active_days'  => 'array',
        'uses_expiry'  => 'boolean',
        'promotion_value' => 'decimal:2',
    ];

    /*
     |-------------------------------------------------------
     | Relationships
     |-------------------------------------------------------
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function partner()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActiveToday($query)
    {
        $now   = Carbon::now();
        $dayKey = strtolower($now->format('D')); // mon, tue, ...

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->where('uses_expiry', false)
                    ->orWhere(function ($q2) use ($now) {
                        $q2->where(function ($qq) use ($now) {
                            $qq->whereNull('start_date')
                                ->orWhere('start_date', '<=', $now);
                        })
                            ->where(function ($qq) use ($now) {
                                $qq->whereNull('end_date')
                                    ->orWhere('end_date', '>=', $now);
                            });
                    });
            })
            ->where(function ($q) use ($dayKey) {
                $q->whereNull('active_days')
                    ->orWhereJsonContains('active_days', $dayKey);
            });
    }
}
