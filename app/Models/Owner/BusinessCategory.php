<?php

namespace App\Models\Owner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessCategory extends Model
{
    use HasFactory;

    protected $table = 'business_categories';

    protected $fillable = [
        'code',
        'name',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot method untuk auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });
    }

    /**
     * Scope untuk mengambil kategori yang aktif saja
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


    /**
     * Scope untuk mencari berdasarkan kode KBLI
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Mendapatkan semua bisnis dengan kategori ini
     */
    public function businesses()
    {
        return $this->hasMany(Businesses::class, 'business_category_id');
    }

    /**
     * Mendapatkan jumlah bisnis aktif dalam kategori ini
     */
    public function getActiveBusinessesCountAttribute()
    {
        return $this->businesses()->where('is_active', true)->count();
    }

    /**
     * Accessor untuk menampilkan kode dan nama
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} ({$this->code})";
    }
}
