<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Owner;
use App\Models\Admin\Product\Category;
use App\Models\Product\Promotion;

class MasterProduct extends Model
{
    use HasFactory;

    protected $table = 'master_products';

    protected $fillable = [
        'product_code',
        'owner_id',
        'name',
        'category_id',
        'price',
        'quantity',
        'pictures',
        'description',
        'promo_id',
    ];

    protected $casts = [
        'pictures' => 'array',   // otomatis casting JSON ke array
        'price'    => 'decimal:2',
    ];

    // === RELATIONS ===
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function parent_options()
    {
        return $this->hasMany(MasterProductParentOption::class, 'master_product_id', 'id');
    }

    public function options()
    {
        return $this->hasMany(MasterProductOption::class, 'master_product_id', 'id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promo_id', 'id');
    }
}
