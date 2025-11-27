<?php

namespace App\Models\Partner\Products;

use App\Models\Store\Stock;
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
        'stock_type',
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
        // Jika tidak terbatas (always_available), kembalikan nilai besar
        if ((int) $this->always_available_flag === 1) {
            return 999999999;
        }

        // Ambil service yang diperlukan
        $converter = app(\App\Services\UnitConversionService::class);
        $recipeCalc = app(\App\Services\LinkedStockCalculatorService::class);

        $availablePhysicalQuantity = ($this->stock)
            ? ($this->stock->quantity - ($this->stock->quantity_reserved ?? 0))
            : 0.00;

        // 1. Logika untuk Linked Stock (Perhitungan Faktor Pembatas)
        if ($this->stock_type === 'linked') {
            // Panggil service, mengirimkan instance $this (Model Option)
            return $recipeCalc->calculateLinkedQuantity($this);
        }

        // 2. Logika untuk Direct Stock (Konversi dari Base Unit)
        elseif ($this->stock_type === 'direct') {
            if ($this->stock) {
                // Gunakan kuantitas fisik yang sudah dikurangi reservasi
                return $converter->convertToDisplayUnit(
                    $availablePhysicalQuantity,
                    $this->stock->display_unit_id
                );
            }
            return 0.00;
        }

        return 0.00;
    }
}
