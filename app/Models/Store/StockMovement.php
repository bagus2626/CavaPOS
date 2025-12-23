<?php

namespace App\Models\Store;

use App\Models\Owner;
use App\Models\User; // Pastikan ini model User (Partner) Anda
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockMovement extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'stock_movements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'partner_id',
        'type',
        'category',
        'notes',
    ];

    /**
     * Get the owner that this movement belongs to.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Get the partner (outlet) that this movement belongs to (nullable).
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    /**
     * Get all of the items included in this movement.
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockMovementItem::class, 'stock_movement_id');
    }
}
