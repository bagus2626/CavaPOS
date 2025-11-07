<?php

namespace App\Models\Owner;

use App\Models\Owner;
use App\Models\Xendit\XenditSubAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerVerification extends Model
{
    use HasFactory;

    protected $table = 'owner_verifications';

    protected $fillable = [
        'owner_id',
        'owner_name',
        'owner_phone',
        'owner_email',
        'ktp_number',
        'ktp_photo_path',
        'business_name',
        'business_category_id',
        'business_address',
        'business_phone',
        'business_email',
        'business_logo_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Mendapatkan data owner yang mengirim pengajuan ini. (Satu Verifikasi milik satu Owner)
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
    public function businessCategory()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }
}
