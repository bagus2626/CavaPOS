<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Owner\OwnerManualPayment;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_order_id',
        'employee_id',
        'customer_id',
        'customer_name',
        'payment_type',
        'owner_manual_payment_id',
        'manual_provider_name',
        'manual_provider_account_name',
        'manual_provider_account_no',
        'manual_payment_image',
        'paid_amount',
        'change_amount',
        'payment_status',
        'note',
    ];

    protected static function booted()
    {
        static::deleting(function ($payment) {
            if (!$payment->manual_payment_image) {
                return;
            }

            $relativePath = str_replace(asset('storage/'), '', $payment->manual_payment_image);

            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        });
    }

    //relasi ke owner_manual_payments
    public function ownerManualPayment(): BelongsTo
    {
        return $this->belongsTo(OwnerManualPayment::class, 'owner_manual_payment_id');
    }
}
