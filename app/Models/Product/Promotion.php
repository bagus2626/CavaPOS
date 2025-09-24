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
        'start_date'   => 'date',
        'end_date'     => 'date',
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
        $today = Carbon::today();
        $dayKey = strtolower($today->format('D')); // "mon", "tue", dst.

        return $query->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->where('uses_expiry', false)
                    ->orWhere(function ($q2) use ($today) {
                        $q2->where(function ($qq) use ($today) {
                            $qq->whereNull('start_date')
                                ->orWhere('start_date', '<=', $today);
                        })->where(function ($qq) use ($today) {
                            $qq->whereNull('end_date')
                                ->orWhere('end_date', '>=', $today);
                        });
                    });
            })
            ->where(function ($q) use ($dayKey) {
                $q->whereNull('active_days')
                    ->orWhereJsonContains('active_days', $dayKey);
            });
    }
}
