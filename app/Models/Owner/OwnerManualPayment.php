<?php

namespace App\Models\Owner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Owner;

class OwnerManualPayment extends Model
{
    use HasFactory;

    protected $table = 'owner_manual_payments';

    protected $fillable = [
        'owner_id',
        'payment_type',
        'provider_name',
        'provider_account_name',
        'provider_account_no',
        'qris_image_url',
        'additional_info',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Optional: relasi ke owner (User / Partner)
     * Sesuaikan model & foreign key jika perlu
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Helper: cek apakah QRIS
     */
    public function isQris(): bool
    {
        return $this->payment_type === 'manual_qris';
    }
}
