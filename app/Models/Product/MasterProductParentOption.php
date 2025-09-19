<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProductParentOption extends Model
{
    use HasFactory;

    protected $table = 'master_product_parent_options';

    protected $fillable = [
        'master_product_id',
        'name',
        'description',
        'provision',
        'provision_value',
    ];

    /**
     * Relasi ke MasterProduct (parent).
     */
    public function master_product()
    {
        return $this->belongsTo(MasterProduct::class, 'master_product_id');
    }

    /**
     * Relasi ke MasterProductOption (child options).
     */
    public function options()
    {
        return $this->hasMany(MasterProductOption::class, 'master_product_parent_option_id');
    }
}
