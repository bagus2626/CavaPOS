<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\Store\Table;
use App\Models\Customer;
use App\Models\Transaction\OrderPayment;
use App\Models\Xendit\XenditInvoice;
use Illuminate\Database\Eloquent\SoftDeletes;


class BookingOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_order_code',
        'partner_id',
        'partner_name',
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
        'cashier_process_id',
        'kitchen_process_id',
        'payment_id',
        'payment_flag',
    ];

    protected $dates = ['deleted_at'];

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
    public function xendit_invoices()
    {
        return $this->hasMany(XenditInvoice::class, 'order_id', 'id');
    }

    // invoice Xendit TERBARU untuk order ini
    public function last_xendit_invoice()
    {
        // pakai created_at, sesuaikan kalau mau pakai kolom lain
        return $this->hasOne(XenditInvoice::class, 'order_id', 'id')->latest('created_at');
    }
}
