<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\Partner\Products\PartnerProduct;

class OrderDetail extends Model
{
    protected $fillable = [
        'booking_order_id',
        'partner_product_id',
        'quantity',
        'base_price',
        'options_price',
        'customer_note',
    ];

    protected $casts = [
        'base_price'    => 'decimal:2',
        'options_price' => 'decimal:2',
    ];

    public function bookingOrder()
    {
        return $this->belongsTo(BookingOrder::class);
    }

    public function partnerProduct()
    {
        return $this->belongsTo(PartnerProduct::class);
    }
    public function order_detail_options()
    {
        return $this->hasMany(OrderDetailOption::class, 'order_detail_id');
    }
}
