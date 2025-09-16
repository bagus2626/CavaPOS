<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // jika akan dipakai untuk auth
// use Illuminate\Database\Eloquent\Model; // pakai ini jika BUKAN untuk auth

class Owner extends Authenticatable
{
    use HasFactory;

    // Jika tidak untuk auth, ganti extends ke Model dan hapus Authenticatable import.

    protected $table = 'owners';

    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        // 'romomber_token', // pakai ini jika kolom custom
        'image',
        'phone_number',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token', // atau 'romomber_token' jika pakai nama custom
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password'  => 'hashed', // Laravel 10+: otomatis di-hash saat set
    ];
}
