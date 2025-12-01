<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovementItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'stock_movement_items';

    /**
     * Menunjukkan bahwa model tidak menggunakan timestamps (created_at/updated_at).
     * Sesuai file migrasi kita, hanya tabel header yang punya timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stock_movement_id',
        'stock_id',
        'quantity',
        'unit_price',
    ];

    /**
     * Get the movement header that this item belongs to.
     */
    public function movement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class, 'stock_movement_id');
    }

    /**
     * Get the actual stock item (material/product) that this record refers to.
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }
}
