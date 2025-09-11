<?php

namespace App\Models\Admin\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Partner\Products\PartnerProduct;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'description',
        'images',
        'partner_id',
    ];

    protected $casts = [
        'images' => 'array', // otomatis decode JSON ke array saat diambil
    ];

    public function partner_products()
    {
        return $this->hasMany(PartnerProduct::class,'category_id', 'id');
    }
}
