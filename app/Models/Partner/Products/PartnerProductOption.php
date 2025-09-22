<?php

namespace App\Models\Partner\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerProductOption extends Model
{

    use HasFactory;

    protected $fillable = [
        'partner_product_id',
        'master_product_option_id',
        'partner_product_parent_option_id',
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

    // Relasi ke PartnerProduct
    public function product()
    {
        return $this->belongsTo(PartnerProduct::class, 'partner_product_id');
    }
    public function parent()
    {
        return $this->belongsTo(PartnerProductParentOption::class, 'partner_product_parent_option_id');
    }
}
