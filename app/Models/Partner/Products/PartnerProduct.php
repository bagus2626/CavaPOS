<?php

namespace App\Models\Partner\Products;

use App\Models\Store\Stock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Owner;
use App\Models\Admin\Product\Category;
use App\Models\Product\Promotion;
use App\Services\LinkedStockCalculatorService;
use App\Services\UnitConversionService;

class PartnerProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'is_active',
        'is_hot_product',
        'master_product_id',
        'owner_id',
        'partner_id',
        'category_id',
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
        'pictures' => 'array', // Supaya JSON images otomatis jadi array
    ];

    // Relasi ke Partner
    public function partner()
    {
        return $this->belongsTo(User::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class, 'partner_product_id')->whereNull('partner_product_option_id');
    }

    // Relasi ke Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function parent_options()
    {
        return $this->hasMany(PartnerProductParentOption::class, 'partner_product_id', 'id');
    }

    // Relasi ke Addons (options)
    public function options()
    {
        return $this->hasMany(PartnerProductOption::class, 'partner_product_id', 'id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promo_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'id');
    }

    public function recipes()
    {
        return $this->hasMany(PartnerProductRecipe::class, 'partner_product_id');
    }

    public function ingredients()
    {
        return $this->belongsToMany(Stock::class, 'partner_product_recipes', 'partner_product_id', 'stock_id')
            ->withPivot('quantity_used');
    }

    public function getQuantityAvailableAttribute(): float
    {
        // Jika tidak terbatas (always_available), kembalikan nilai besar
        if ((int) $this->always_available_flag === 1) {
            return 999999999;
        }

        // Ambil service yang diperlukan
        $converter = app(UnitConversionService::class);
        $recipeCalc = app(LinkedStockCalculatorService::class);

        // 1. Logika untuk Linked Stock (Membaca Kolom Cache)
        if ($this->stock_type === 'linked') {

            // // hapus kode ini jika sudah menkadlankan StockRecalculationService
            // $cachedQty = (float) $this->available_linked_quantity;
            // if ($cachedQty > 0) {
            //     return $cachedQty;
            // }
            // return $recipeCalc->calculateLinkedQuantity($this);

            // Ambil nilai dari kolom yang sudah dihitung dan disimpan
            return (float) $this->available_linked_quantity;
        }

        // 2. Logika untuk Direct Stock (Tetap Konversi Langsung)
        elseif ($this->stock_type === 'direct') {
            if ($this->stock) {
                return $converter->convertToDisplayUnit(
                    $this->stock->quantity - ($this->stock->quantity_reserved ?? 0),
                    $this->stock->display_unit_id
                );
            }
            return 0.00;
        }

        return 0.00;
    }
}
