<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\Partner\Products\PartnerProductOption;

class OrderDetailOption extends Model
{
    protected $fillable = [
        'order_detail_id',
        'parent_name',
        'partner_product_option_name',
        'option_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function option()
    {
        return $this->belongsTo(PartnerProductOption::class, 'option_id'); // ganti jika model berbeda
    }
}
