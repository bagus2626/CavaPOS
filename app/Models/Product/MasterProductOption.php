<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProductOption extends Model
{
    use HasFactory;

    protected $table = 'master_product_options';

    protected $fillable = [
        'master_product_id',
        'master_product_parent_option_id',
        'name',
        'quantity',
        'price',
        'pictures',
        'description',
        'promo_id',
    ];

    protected $casts = [
        'pictures' => 'array',
    ];

    /**
     * Relasi ke MasterProduct.
     */
    public function master_product()
    {
        return $this->belongsTo(MasterProduct::class, 'master_product_id');
    }

    /**
     * Relasi ke MasterProductParentOption.
     */
    public function parent_option()
    {
        return $this->belongsTo(MasterProductParentOption::class, 'master_product_parent_option_id');
    }
}
