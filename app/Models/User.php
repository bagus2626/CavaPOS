<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'owner_id',
        'username',
        'email',
        'password',
        'role',
        'slug',
        'partner_code',
        'email_verified_at',
        'remember_token',
        'logo',
        'background_picture',
        'province',
        'province_id',
        'city',
        'city_id',
        'subdistrict',
        'subdistrict_id',
        'urban_village',
        'urban_village_id',
        'address',
        'pic_name',
        'pic_email',
        'pic_phone_number',
        'pic_role',
        'is_active',
        'is_qr_active',
        'is_cashier_active',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profileOutlet()
    {
        return $this->hasOne(ProfileOutlet::class);
    }
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'id');
    }
}
