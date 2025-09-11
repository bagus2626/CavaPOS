<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'slug', 'created_by', 'modified_by'];

    public function packages()
    {
        return $this->hasMany(ProductPackage::class);
    }
}

