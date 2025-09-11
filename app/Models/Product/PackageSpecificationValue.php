<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackageSpecificationValue extends Model
{
    use HasFactory;

    protected $fillable = ['product_package_id', 'specification_id', 'value', 'created_by', 'modified_by'];

    public function package()
    {
        return $this->belongsTo(ProductPackage::class, 'product_package_id');
    }

    public function specification()
    {
        return $this->belongsTo(Specification::class);
    }
}
