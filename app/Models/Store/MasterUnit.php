<?php

namespace App\Models\Store; // Sesuaikan jika path Anda berbeda

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterUnit extends Model
{
    use HasFactory;

    protected $table = 'master_units';

    protected $fillable = [
        'owner_id',
        'unit_name',
        'is_base_unit',
        'base_unit_conversion_value',
        'group_label',
    ];

    protected $casts = [
        'is_base_unit' => 'boolean',
        'base_unit_conversion_value' => 'decimal:5',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Relasi ke Stok (Stok mana saja yang menggunakan ini sebagai unit tampilan).
     */
    public function stocksAsDisplayUnit(): HasMany
    {
        return $this->hasMany(Stock::class, 'display_unit_id');
    }
}
