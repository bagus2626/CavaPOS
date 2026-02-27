<?php

namespace App\Models\Partner\HumanResource;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'user_name',
        'email',
        'role',
        'partner_id',
        'password',
        'is_active',
        'image',
        'menu_permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'menu_permissions' => 'array',
    ];

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function isStaff(): bool
    {
        return in_array(strtoupper($this->role), ['MANAGER', 'SUPERVISOR']);
    }

    /**
     * Cek apakah employee bisa mengakses menu tertentu.
     */
    public function hasMenuAccess(string $menuKey): bool
    {
        if (!$this->isStaff()) return true;

        $permissions = $this->menu_permissions;

        if (is_null($permissions)) return true;

        return (bool) ($permissions[$menuKey] ?? true);
    }

    /**
     * Ambil semua permission sebagai array [key => bool].
     */
    public function getResolvedPermissions(): array
    {
        $allMenuKeys = array_keys(config('staff-menus', []));
        $saved       = $this->menu_permissions ?? [];
        $resolved    = [];

        foreach ($allMenuKeys as $key) {
            $resolved[$key] = (bool) ($saved[$key] ?? true);
        }

        return $resolved;
    }


    public function setPasswordAttribute($value)
    {
        if ($value && strlen($value) < 60) { // cek kasar: belum di-hash
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
}