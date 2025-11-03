<?php

namespace App\Models\Owner;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Businesses extends Model
{
    use HasFactory;

    protected $table = 'businesses';

    protected $fillable = [
        'owner_id',
        'business_category_id',
        'name',
        'address',
        'phone',
        'email',
        'logo_path',
        'is_active',
    ];



    /**
     * Mendapatkan data owner yang memiliki bisnis ini. (Satu Bisnis milik satu Owner)
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Mendapatkan kategori bisnis
     */
    public function category()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    /**
     * Scope untuk mengambil bisnis yang aktif saja
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter berdasarkan kategori
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('business_category_id', $categoryId);
    }

    /**
     * Accessor untuk nama kategori
     */
    public function getCategoryNameAttribute()
    {
        return $this->category ? $this->category->name : '-';
    }
}
