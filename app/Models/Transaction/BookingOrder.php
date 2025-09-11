<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\Store\Table;
use App\Models\Customer;
use App\Models\Transaction\OrderPayment;

class BookingOrder extends Model
{
    protected $fillable = [
        'booking_order_code',
        'partner_id',
        'table_id',
        'customer_id',
        'employee_order_id',
        'order_by',
        'customer_name',
        'order_status',
        'payment_method',
        'discount_id',
        'discount_value',
        'total_order_value',
        'customer_order_note',
        'payment_id',
        'payment_flag',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function payment()
    {
        return $this->belongsTo(OrderPayment::class, 'payment_id');
    }
    public function order_details()
    {
        return $this->hasMany(OrderDetail::class, 'booking_order_id');
    }
}
