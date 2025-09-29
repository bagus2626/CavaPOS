@component('mail::message')
# Terima kasih, {{ $customer->name ?? $order->customer_name }}

Pesanan kamu telah kami terima.

@component('mail::panel')
**Kode:** {{ $order->booking_order_code }}  
**Total:** Rp {{ number_format($order->total_order_value, 0, ',', '.') }}  
**Metode:** {{ $order->payment_method }}
@endcomponent

@component('mail::button', ['url' => route('customer.orders.receipt', $order->id)])
Lihat struk online
@endcomponent

Terima kasih,<br>
{{ $partner->name }}
@endcomponent
