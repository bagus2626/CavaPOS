<?php

namespace App\Models\Owner;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerProfile extends Model
{
    use HasFactory;

    protected $table = 'owner_profiles';

    protected $fillable = [
        'owner_id',
        'ktp_number',
        'ktp_photo_path',
    ];

    /**
     * Mendapatkan data owner yang memiliki profil ini. (Satu Profil milik satu Owner)
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
