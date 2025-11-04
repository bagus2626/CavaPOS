<?php

namespace App\Models;

use App\Models\Owner\Businesses;
use App\Models\Owner\OwnerProfile;
use App\Models\Owner\OwnerVerification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // jika akan dipakai untuk auth
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Model; // pakai ini jika BUKAN untuk auth

class Owner extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;

    // Jika tidak untuk auth, ganti extends ke Model dan hapus Authenticatable import.

    protected $table = 'owners';

    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'email_verified_at',
        'image',
        'phone_number',
        'is_active',
        'verification_status',
        'approved_at',
    ];

    protected $hidden = [
        'password',
        'remember_token', // atau 'romomber_token' jika pakai nama custom
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password'  => 'hashed', // Laravel 10+: otomatis di-hash saat set
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'owner_id', 'id')
            ->where('role', 'partner');
    }
    public function profile()
    {
        return $this->hasOne(OwnerProfile::class);
    }

    /**
     * Mendapatkan bisnis inti yang terkait dengan owner. (Satu Owner punya satu Bisnis)
     */
    public function business()
    {
        return $this->hasOne(Businesses::class);
    }

    /**
     * Mendapatkan semua riwayat pengajuan verifikasi milik owner. (Satu Owner punya banyak Verifikasi)
     */
    public function verifications()
    {
        return $this->hasMany(OwnerVerification::class);
    }
}
