<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Table extends Model
{
    use HasFactory;

    protected $table = 'tables';

    protected $fillable = [
        'table_no',
        'table_code',
        'partner_id',
        'table_class',
        'description',
        'seat_layout_id',
        'images',
        'table_url',
        'status',
    ];

    protected $casts = [
        'images' => 'array', // otomatis decode JSON ke array
    ];

    /**
     * Relasi ke Partner (User).
     */
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    /**
     * Relasi ke Seat Layout (jika ada model SeatLayout).
     */
    // public function seatLayout()
    // {
    //     return $this->belongsTo(SeatLayout::class, 'seat_layout_id');
    // }
}
