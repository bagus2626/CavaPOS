<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPackage extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'price', 'monthly_price', 'annual_price', 'description'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function specificationValues()
    {
        return $this->hasMany(PackageSpecificationValue::class);
    }
}
