<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // penting agar bisa auth
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'slug',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
// Auth::guard('customer')->attempt(['email'=>$email, 'password'=>$password]);
