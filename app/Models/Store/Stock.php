<?php

namespace App\Models\Store;

use App\Models\Partner\Products\PartnerProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Owner;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\User;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_code',
        'stock_type',
        'owner_id',
        'partner_id',
        'partner_product_id',
        'partner_product_option_id',
        'owner_master_product_id',
        'display_unit_id',
        'type',
        'stock_name',
        'quantity',
        'quantity_reserved',
        'last_price_per_unit',
        'description',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function partnerProduct()
    {
        return $this->belongsTo(PartnerProduct::class, 'partner_product_id');
    }

    public function partnerProductOption()
    {
        return $this->belongsTo(PartnerProductOption::class, 'partner_product_option_id');
    }

    public function displayUnit()
    {
        return $this->belongsTo(MasterUnit::class, 'display_unit_id');
    }

    public function usedInProducts()
    {
        return $this->belongsToMany(PartnerProduct::class, 'partner_product_recipes', 'stock_id', 'partner_product_id');
    }

    public function usedInOptions()
    {
        return $this->belongsToMany(PartnerProductOption::class, 'partner_product_options_recipes', 'stock_id', 'partner_product_option_id');
    }
}
