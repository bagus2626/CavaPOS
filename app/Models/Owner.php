<?php

namespace App\Models;

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
}
