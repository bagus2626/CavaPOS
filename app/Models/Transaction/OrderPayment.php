<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_order_id',
        'employee_id',
        'customer_id',
        'customer_name',
        'payment_type',
        'paid_amount',
        'change_amount',
        'payment_status',
        'note',
    ];
}
