<?php

namespace App\Models\Partner\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerProductParentOption extends Model
{

    use HasFactory;
    protected $table = 'partner_product_parent_options';

    protected $fillable = [
        'partner_product_id',
        'master_product_parent_option_id',
        'name',
        'description',
        'provision',
        'provision_value'
    ];

    // Relasi ke PartnerProduct
    public function product()
    {
        return $this->belongsTo(PartnerProduct::class, 'partner_product_id');
    }

    public function options()
    {
        return $this->hasMany(PartnerProductOption::class, 'partner_product_parent_option_id', 'id');
    }
}
