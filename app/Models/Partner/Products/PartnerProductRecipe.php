<?php

namespace App\Models\Partner\Products;

use App\Models\Store\MasterUnit;
use App\Models\Store\Stock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerProductRecipe extends Model
{
    use HasFactory;

    protected $table = 'partner_product_recipes';


    protected $fillable = [
        'partner_product_id',
        'stock_id',
        'quantity_used',
        'display_unit_id'
    ];


    protected $casts = [
        'quantity_used' => 'decimal:2',
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(PartnerProduct::class, 'partner_product_id');
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function displayUnit()
    {
        return $this->belongsTo(MasterUnit::class, 'display_unit_id');
    }
}
