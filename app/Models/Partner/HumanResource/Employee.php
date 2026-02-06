<?php

namespace App\Models\Partner\HumanResource;

use Illuminate\Foundation\Auth\User as Authenticatable; // penting
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use Laravel\Sanctum\HasApiTokens;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Authenticatable
{
    use HasApiTokens, Notifiable; //, HasFactory;

    // Kalau nama tabel default "employees", baris ini bisa dihapus
    // protected $table = 'employees';

    protected $fillable = [
        'name',
        'user_name',
        'email',
        'role',
        'partner_id',
        'password',
        'is_active',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        // 'email_verified_at' => 'datetime',
    ];

    // Relasi: sesuaikan namespace model Partner-mu yang sebenarnya
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    // Opsional: auto-hash password jika diberi plaintext
    public function setPasswordAttribute($value)
    {
        if ($value && strlen($value) < 60) { // cek kasar: belum di-hash
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
}
