<?php

namespace App\Models\Partner\PaymentMethod;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Owner\OwnerManualPayment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerManualPayment extends Model
{
    protected $fillable = [
        'partner_id',
        'owner_manual_payment_id',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function ownerManualPayment(): BelongsTo
    {
        return $this->belongsTo(OwnerManualPayment::class, 'owner_manual_payment_id');
    }
}
