<?php

namespace App\Models\Partner\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Admin\Product\Category;

class PartnerProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'master_product_id',
        'owner_id',
        'partner_id',
        'category_id',
        'name',
        'quantity',
        'price',
        'pictures',
        'description',
        'promo_id',
    ];

    protected $casts = [
        'pictures' => 'array', // Supaya JSON images otomatis jadi array
    ];

    // Relasi ke Partner
    public function partner()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function parent_options()
    {
        return $this->hasMany(PartnerProductParentOption::class, 'partner_product_id', 'id');
    }

    // Relasi ke Addons (options)
    public function options()
    {
        return $this->hasMany(PartnerProductOption::class, 'partner_product_id', 'id');
    }
}
