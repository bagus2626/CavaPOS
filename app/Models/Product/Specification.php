<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Specification extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'created_by', 'modified_by'];

    public function values()
    {
        return $this->hasMany(PackageSpecificationValue::class);
    }
}

