<?php
namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Owner;
use App\Models\User;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_code',
        'owner_id',
        'partner_id',
        'owner_master_product_id',
        'type',
        'stock_name',
        'quantity',
        'unit',
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
}
