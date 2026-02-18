<?php

namespace App\Models\Partner\Products;

use App\Models\Store\Stock;
use App\Services\LinkedStockCalculatorService;
use App\Services\UnitConversionService;
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
        'stock_type',
        'available_linked_quantity',
        'always_available_flag',
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

    public function stock()
    {
        return $this->hasOne(Stock::class, 'partner_product_option_id');
    }

    // Relasi resep (jika stock_type = 'linked')
    public function recipes()
    {
        return $this->hasMany(PartnerProductOptionsRecipe::class, 'partner_product_option_id');
    }

    // Relasi bahan mentah (jika stock_type = 'linked')
    public function ingredients()
    {
        return $this->belongsToMany(Stock::class, 'partner_product_options_recipes', 'partner_product_option_id', 'stock_id')
            ->withPivot('quantity_used');
    }

    public function getQuantityAvailableAttribute(): float
    {
        if ((int) $this->always_available_flag === 1) {
            return 0.00;
        }

        $converter = app(UnitConversionService::class);

        if ($this->stock_type === 'linked') {
            return (float) $this->available_linked_quantity;
        }

        if ($this->stock_type === 'direct' && $this->stock) {
            return $converter->convertToDisplayUnit(
                $this->stock->quantity - ($this->stock->quantity_reserved ?? 0),
                $this->stock->display_unit_id
            );
        }

        return 0.00;
    }
}
