@extends('layouts.customer')

@section('title', __('messages.customer.orders.detail.order_detail'))

@section('content')
@php
    $payment = $order->payment ?? null;
@endphp
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow p-6 md:p-8 border-t-4 border-[#ae1504]">
        {{-- Header --}}
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">
                    {{ __('messages.customer.orders.detail.order_detail') }}
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $headline }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $subtitle }}
                </p>
            </div>
            <div class="text-right text-sm">
                <div class="font-mono text-xs uppercase text-gray-500">
                    {{ __('messages.customer.orders.detail.order_code') }}
                </div>
                <div class="font-semibold tracking-widest">
                    {{ $order->booking_order_code }}
                </div>
                <div class="mt-1 text-xs text-gray-500">
                    {{ $order->created_at?->format('d M Y H:i') }}
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="mt-4 text-xs text-green-600 bg-lime-100 rounded-md p-2">
                {{ session('success') }}
            </div>
        @endif

        {{-- Info pemesan & meja --}}
        <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-gray-500">{{ __('messages.customer.orders.detail.order_name') }}</div>
                <div class="font-semibold">
                    {{ $order->customer_name }}
                </div>
            </div>
            <div>
                <div class="text-gray-500">{{ __('messages.customer.orders.detail.table_no') }}</div>
                <div class="font-semibold">
                    {{ $table->table_no ?? '-' }}
                </div>
            </div>
        </div>

        {{-- PERINGATAN JIKA PEMBAYARAN BELUM LUNAS --}}
        @if(!$order->payment_flag && $payment && $payment->payment_status !== 'PAID' && !in_array($order->order_status, ['PAYMENT', 'PAYMENT REQUEST'], true))
            <div class="mt-4 rounded-xl border border-red-300 bg-red-100 px-4 py-3 text-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-1 space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-amber-900">
                                {{ __('messages.customer.orders.detail.your_payment_is_not_paid_yet') }}
                            </p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-white text-[11px] font-semibold border border-amber-300 text-amber-800">
                                Status: {{ $payment->payment_status }}
                            </span>
                        </div>

                        <p class="text-xs md:text-sm text-amber-800">
                            {{ __('messages.customer.orders.detail.please_come_to_the_cashier') }}
                        </p>

                        {{-- Kartu QR + Detail --}}
                        <div class="mt-2 w-full rounded-lg bg-white border border-dashed border-amber-300 px-4 py-3">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                {{-- QR CODE 2D --}}
                                @if(!empty($qrPngBase64))
                                    <div class="flex justify-center md:justify-start">
                                        <img
                                            src="data:image/png;base64,{{ $qrPngBase64 }}"
                                            alt="QR Pembayaran"
                                            class="w-24 h-24 md:w-28 md:h-28 object-contain"
                                        >
                                    </div>
                                @endif

                                {{-- Info teks pendukung --}}
                                <div class="flex-1 text-center md:text-left space-y-2">
                                    <div>
                                        <div class="text-[10px] uppercase text-gray-500 tracking-wide">
                                            {{ __('messages.customer.orders.detail.order_code') }}
                                        </div>
                                        <div class="font-mono text-base font-semibold tracking-[0.35em]">
                                            {{ $order->booking_order_code }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-[10px] uppercase text-gray-500 tracking-wide mt-1">
                                            {{ __('messages.customer.orders.detail.name') }}
                                        </div>
                                        <div class="text-xs font-medium text-gray-800">
                                            {{ $order->customer_name }}
                                        </div>
                                    </div>

                                    <p class="mt-1 text-[11px] text-gray-500">
                                        {{ __('messages.customer.orders.detail.show_this_qr') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(!$order->payment_flag && !$payment)
            <div class="mt-4 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-amber-500 text-white text-sm font-bold flex-shrink-0">
                        !
                    </span>

                    <div class="flex-1 space-y-2">
                        <p class="font-semibold text-amber-900">
                            {{ __('messages.customer.orders.detail.no_payment_found') }}
                        </p>
                        <p class="text-xs md:text-sm text-amber-800">
                            {{ __('messages.customer.orders.detail.please_come_to_the_cashier') }}
                        </p>

                        {{-- Kartu QR + Detail --}}
                        <div class="mt-2 w-full rounded-lg bg-white border border-dashed border-amber-300 px-4 py-3">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                {{-- QR CODE 2D --}}
                                @if(!empty($qrPngBase64))
                                    <div class="flex justify-center md:justify-start">
                                        <img
                                            src="data:image/png;base64,{{ $qrPngBase64 }}"
                                            alt="QR Pembayaran"
                                            class="w-24 h-24 md:w-28 md:h-28 object-contain"
                                        >
                                    </div>
                                @endif

                                {{-- Info teks pendukung --}}
                                <div class="flex-1 text-center md:text-left space-y-2">
                                    <div>
                                        <div class="text-[10px] uppercase text-gray-500 tracking-wide">
                                            {{ __('messages.customer.orders.detail.order_code') }}
                                        </div>
                                        <div class="font-mono text-base font-semibold tracking-[0.35em]">
                                            {{ $order->booking_order_code }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-[10px] uppercase text-gray-500 tracking-wide mt-1">
                                            {{ __('messages.customer.orders.detail.name') }}
                                        </div>
                                        <div class="text-xs font-medium text-gray-800">
                                            {{ $order->customer_name }}
                                        </div>
                                    </div>

                                    <p class="mt-1 text-[11px] text-gray-500">
                                        {{ __('messages.customer.orders.detail.show_this_qr') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-center md:justify-end">
                            <button
                                type="button"
                                onclick="cancelOrder({{ $order->id }}, '{{ $partner->slug }}', '{{ $table->table_code }}', '{{ $customer->id }}')"
                                class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-red-50 px-4 py-2 text-xs md:text-sm font-semibold text-red-700 hover:bg-red-100 hover:border-red-400 transition"
                            >
                                {{ __('messages.customer.orders.detail.cancel_order') ?? 'Cancel Order' }}
                            </button>
                            <form id="cancelOrderForm" method="POST" style="display:none;">
                                @csrf

                                <input type="hidden" name="order_id">
                                <input type="hidden" name="partner_slug">
                                <input type="hidden" name="table_code">
                                <input type="hidden" name="customer_id">
                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
        @elseif ($order->order_status === 'PAYMENT')
            @if ($order->last_xendit_invoice)
                <div class="mt-4 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-1 space-y-2">
                            <p class="font-semibold text-amber-900">
                                {{ __('messages.customer.orders.detail.unpaid_warning') }}
                            </p>
                            <p class="text-xs md:text-sm text-amber-800">
                                {{ __('messages.customer.orders.detail.please_continue_payment') }}
                            </p>
                            <a href="{{ $order->last_xendit_invoice->invoice_url }}"
                                class="inline-flex items-center px-4 py-2 rounded-lg bg-[#ae1504] text-sm text-white hover:bg-[#8a1103]">
                                    {{ __('messages.customer.orders.detail.continue_payment') }}
                            </a>
                        </div>
                    </div>
                </div>
            @elseif ($payment && !$order->last_xendit_invoice)
                <div class="mt-4 rounded-xl border border-red-300 bg-red-100 px-4 py-3 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-1 space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-semibold text-amber-900">
                                    {{ __('messages.customer.orders.detail.your_payment_is_not_paid_yet') }}
                                </p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-white text-[11px] font-semibold border border-amber-300 text-amber-800">
                                    Status: {{ $payment->payment_status }}
                                </span>
                            </div>

                            <p class="text-xs md:text-sm text-amber-800">
                                {{ __('messages.customer.orders.detail.please_come_to_the_cashier') }}
                            </p>

                            {{-- === TOMBOL SAYA INGIN BAYAR DI KASIR === --}}
                            <div class="pt-3">
                                <form
                                    action="{{ route('customer.orders.unpaid-order', [
                                        'partner_slug' => $partner->slug,
                                        'order_id' => $order->id
                                    ]) }}"
                                    method="POST"
                                >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition"
                                    >
                                        {{ __('messages.customer.orders.detail.pay_at_cashier') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Timeline Status --}}
        @php
            $steps = [
                [
                    'key'   => 'UNPAID',
                    'title' => __('messages.customer.orders.detail.waiting_for_payment'),
                    'desc'  => __('messages.customer.orders.detail.waiting_for_payment_desc'),
                    'icon'  => asset('icons/icon-payment-90.png'),
                ],
                [
                    'key'   => 'PAYMENT REQUEST',
                    'title' => __('messages.customer.orders.detail.payment_validation'),
                    'desc'  => __('messages.customer.orders.detail.payment_validation_desc'),
                    'icon'  => asset('icons/icon-payment-90.png'),
                ],
                [
                    'key'   => 'PAID',
                    'title' => __('messages.customer.orders.detail.waiting_to_be_processed'),
                    'desc'  => __('messages.customer.orders.detail.waiting_to_be_processed_desc'),
                    'icon'  => asset('icons/icon-time-90.png'),
                ],
                [
                    'key'   => 'PROCESSED',
                    'title' => __('messages.customer.orders.detail.being_processed'),
                    'desc'  => __('messages.customer.orders.detail.being_processed_desc'),
                    'icon'  => asset('icons/icon-process-100.png'),
                ],
                [
                    'key'   => 'SERVED',
                    'title' => __('messages.customer.orders.detail.served'),
                    'desc'  => __('messages.customer.orders.detail.served_desc'),
                    'icon'  => asset('icons/icon-done-128.png'),
                ],
            ];

            $statusIndexMap = [
                'UNPAID'    => 0,
                'PAYMENT REQUEST' => 1,
                'PAID'      => 2,
                'PROCESSED' => 3,
                'SERVED'    => 4,
            ];

            $currentIndex = $currentIndex ?? ($statusIndexMap[$order->order_status] ?? 0);

            $totalSteps = count($steps);
            $maxIndex   = max($totalSteps - 1, 1);
            $currentIndex = min(max($currentIndex, 0), $maxIndex);

            $progressMap = [
                0 => 0,    // UNPAID
                1 => 25,   // PAYMENT REQUEST
                2 => 50,   // PROCESSED
                3 => 75,   // READY / NEXT STEP
                4 => 100,  // SERVED
            ];


            $progressPercent = $progressMap[$currentIndex] ?? 0;

            $currentStep = $steps[$currentIndex] ?? $steps[0];
        @endphp

        <div class="mt-8">
            <h2 class="text-sm font-semibold text-gray-800 mb-3">{{ __('messages.customer.orders.detail.order_status') }}</h2>

            <div class="mt-4 px-4">
                <div class="relative">
                    <div class="absolute left-0 right-0 top-4 h-1 bg-gray-200 rounded-full z-0"></div>

                    <div class="absolute left-0 top-4 h-1 bg-[#ae1504] rounded-full z-0"
                        style="width: {{ $progressPercent }}%;"></div>
                    <div class="relative flex justify-between z-10">
                        @foreach ($steps as $index => $step)
                            @php
                                $isDone   = $index < $currentIndex;
                                $isActive = $index === $currentIndex;
                            @endphp

                            <div class="flex flex-col items-center w-1/4 text-center">
                                {{-- BULLET --}}
                                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center overflow-hidden
                                    @if($isDone || $isActive)
                                        bg-[#ae1504] border-[#ae1504]
                                    @else
                                        bg-white border-gray-300
                                    @endif
                                ">
                                    <img src="{{ $step['icon'] }}"
                                        class="w-5 h-5 object-contain"
                                        alt="icon">
                                </div>
                                <p class="mt-2 text-[11px] font-semibold
                                    @if($isActive)
                                        text-[#ae1504]
                                    @elseif($isDone)
                                        text-gray-700
                                    @else
                                        text-gray-400
                                    @endif
                                ">
                                    {{ $step['title'] }}
                                </p>
                                {{-- DESKRIPSI (hanya untuk status aktif) --}}
                                @if($isActive)
                                    <p class="mt-1 text-[10px] text-gray-600">
                                        {{ $step['desc'] }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Item Pesanan --}}
        <div class="mt-8">
            <h2 class="text-sm font-semibold text-gray-800 mb-3">{{ __('messages.customer.orders.detail.order_detail') }}</h2>
            <div class="space-y-3">
                @forelse ($order->order_details as $detail)
                    @php
                        $options = $detail->order_detail_options ?? collect();
                        $subtotal = ($detail->base_price + ($detail->options_price ?? 0) - ($detail->promo_amount ?? 0)) * $detail->quantity;
                    @endphp

                    <div class="border rounded-xl p-3 flex flex-col gap-1 bg-gray-50">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $detail->product_name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ __('messages.customer.orders.detail.qty') }}: {{ $detail->quantity }} ×
                                    Rp {{ number_format($detail->base_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-right text-sm font-semibold text-gray-900">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </div>
                        </div>

                        @if($options->count() > 0)
                            <div class="mt-1 text-xs text-gray-600">
                                @foreach ($options as $opt)
                                    <div class="flex items-center justify-between">
                                        <span>
                                            {{ $opt->parent_name ? $opt->parent_name.': ' : '' }}{{ $opt->partner_product_option_name }}
                                        </span>
                                        <span>
                                            @if(($opt->price ?? 0) > 0)
                                                + Rp {{ number_format($opt->price, 0, ',', '.') }}
                                            @else
                                                (Free)
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($detail->customer_note)
                            <p class="mt-1 text-xs text-gray-700 italic">
                                {{ __('messages.customer.orders.detail.note') }}: {{ $detail->customer_note }}
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">
                        {{ __('messages.customer.orders.detail.item_not_found') }}
                    </p>
                @endforelse
            </div>

            <div class="mt-4 border-t pt-3 flex items-center justify-between text-sm">
                <span class="text-gray-600">{{ __('messages.customer.orders.detail.order_total') }}</span>
                <span class="font-semibold text-gray-900">
                    Rp {{ number_format($order->total_order_value, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Detail Pembayaran --}}
        <div class="mt-8">
            <h2 class="text-sm font-semibold text-gray-800 mb-3">{{ __('messages.customer.orders.detail.payment_detail') }}</h2>

            @if($payment)
                <div class="border rounded-xl p-4 bg-slate-50 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">
                            {{ __('messages.customer.orders.detail.payment_method') }}
                        </p>
                        <p class="text-sm font-semibold text-gray-900">
                            <span class="inline-flex items-center gap-1 px-1 py-1 rounded-md text-[11px] font-medium border border-gray-200 bg-white text-gray-700">
                                @if ($payment->payment_type == 'QRIS')
                                <img src="{{ asset('icons/qris_svg.svg') }}" 
                                            alt="QRIS" 
                                            class="h-3 w-auto">
                                @elseif ($payment->payment_type === 'manual_tf')
                                    {{ __('messages.customer.orders.detail.manual_tf') }}
                                @elseif ($payment->payment_type === 'manual_ewallet')
                                    {{ __('messages.customer.orders.detail.manual_ewallet') }}
                                @elseif ($payment->payment_type === 'manual_qris')
                                    {{ __('messages.customer.orders.detail.manual_qris') }}
                                @else
                                    {{ $payment->payment_type ?? '' }}
                                @endif
                            </span>
                        </p>

                        <p class="mt-2 text-xs text-gray-500 uppercase tracking-wide">
                            {{ __('messages.customer.orders.detail.payment_status') }}
                        </p>
                        <p class="text-sm font-semibold
                            @if($payment->payment_status === 'PAID') text-emerald-700
                            @elseif($payment->payment_status === 'PENDING') text-amber-600
                            @else text-red-600 @endif">
                            {{ $payment->payment_status }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">
                            {{ __('messages.customer.orders.detail.total_paid') }}
                        </p>
                        <p class="text-lg font-semibold text-gray-900">
                            Rp {{ number_format($payment->paid_amount, 0, ',', '.') }}
                        </p>

                        @if($payment->change_amount ?? 0)
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('messages.customer.orders.detail.change') }}:
                                <span class="font-semibold">
                                    Rp {{ number_format($payment->change_amount, 0, ',', '.') }}
                                </span>
                            </p>
                        @endif

                        @if($payment->created_at)
                            <p class="mt-1 text-xs text-gray-400">
                                {{ __('messages.customer.orders.detail.paid_at') }} {{ $payment->created_at->format('d M Y H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
            @else
                <div class="border rounded-xl p-4 bg-amber-50 text-sm text-amber-800">
                    {{ __('messages.customer.orders.detail.no_payment_found_please_confirm_to_cashier') }}
                </div>
            @endif
        </div>

{{-- ============ GANTI SECTION WIFI DENGAN INI ============ --}}
@if($wifiData && $order->payment_flag === 1)
    <div class="mt-8">
        <h2 class="text-sm font-semibold text-gray-800 mb-3">
            {{ __('messages.customer.orders.detail.wifi_information') ?? 'Informasi WiFi' }}
        </h2>

        <div class="border rounded-xl p-4 bg-slate-50">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                    </svg>
                </div>
                
                <div class="flex-1">
                    <p class="text-xs text-gray-500 mb-3">
                        {{ __('messages.customer.orders.detail.wifi_description') ?? 'Nikmati koneksi internet gratis selama Anda berada di resto kami' }}
                    </p>

                    <div class="space-y-3">
                        {{-- SSID --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">
                                    {{ __('messages.customer.orders.detail.wifi_name') ?? 'Nama WiFi (SSID)' }}
                                </p>
                                <p class="text-sm font-semibold text-gray-900 mt-0.5">
                                    {{ $wifiData['ssid'] ?? '-' }}
                                </p>
                            </div>
                            <button 
                                onclick="copyWifi('{{ $wifiData['ssid'] }}', 'SSID')"
                                class="flex items-center gap-1 px-3 py-1.5 text-xs text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors"
                                title="Salin SSID"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span>Salin</span>
                            </button>
                        </div>

                        {{-- Password --}}
                        @if($wifiData['password'])
                            <div class="pt-3 border-t flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                                        {{ __('messages.customer.orders.detail.wifi_password') ?? 'Password' }}
                                    </p>
                                    <p class="text-sm font-semibold text-gray-900 mt-0.5 font-mono">
                                        {{ $wifiData['password'] }}
                                    </p>
                                </div>
                                <button 
                                    onclick="copyWifi('{{ $wifiData['password'] }}', 'Password')"
                                    class="flex items-center gap-1 px-3 py-1.5 text-xs text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors"
                                    title="Salin Password"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <span>Salin</span>
                                </button>
                            </div>
                        @else
                            <div class="pt-3 border-t">
                                <p class="text-xs text-emerald-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('messages.customer.orders.detail.wifi_open') ?? 'WiFi tanpa password (Terbuka)' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div id="wifi-toast" class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg transform translate-y-20 opacity-0 transition-all duration-300 z-50">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span id="wifi-toast-message">Tersalin!</span>
        </div>
    </div>

    <script>
        function copyWifi(text, label) {
            if (!navigator.clipboard) {
                // Fallback untuk browser lama
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                try {
                    document.execCommand('copy');
                    showWifiToast(label + ' tersalin!');
                } catch (err) {
                    console.error('Gagal menyalin:', err);
                }
                document.body.removeChild(textarea);
                return;
            }

            navigator.clipboard.writeText(text).then(() => {
                showWifiToast(label + ' tersalin!');
            }).catch(err => {
                console.error('Gagal menyalin:', err);
            });
        }

        function showWifiToast(message) {
            const toast = document.getElementById('wifi-toast');
            const messageEl = document.getElementById('wifi-toast-message');
            
            if (!toast || !messageEl) return;
            
            messageEl.textContent = message;
            
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 2000);
        }
    </script>
@endif
        
        {{-- Aksi --}}
        <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
            @if($order->payment_flag == 1 && $customer->id)
            <a href="{{ route('customer.menu.index', [
                            'partner_slug'       => $partner_slug,
                            'table_code'         => $table_code,
                            'reorder_order_id'   => $order->id,
                        ]) }}"
                class="inline-flex items-center px-4 py-2 rounded-lg border border-[#ae1504] text-sm text-[#ae1504] hover:bg-[#fee2e2]">
                    {{ __('messages.customer.orders.detail.order_again') }}
            </a>
            @endif

            <div class="flex items-center gap-2">
                @if ($order->payment_flag === 1)
                    <a href="{{ route('customer.orders.receipt', $order->id) }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">
                        {{ __('messages.customer.orders.detail.download_receipt') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Hapus cart dari localStorage jika order sudah dibayar
    @if($order->order_status !== 'UNPAID')
        localStorage.removeItem('menuCart');
    @endif
</script>
<script>
    function cancelOrder(orderId, partnerSlug, tableCode, customerId) {
        if (!orderId) return;

        Swal.fire({
            title: "{{ __('messages.customer.orders.detail.cancel_order_confirm_1') }}",
            text: "{{ __('messages.customer.orders.detail.cancel_order_confirm_2') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ __('messages.customer.orders.detail.cancel_order_confirm_yes') }}",
            cancelButtonText: "{{ __('messages.customer.orders.detail.cancel_order_confirm_no') }}",
            confirmButtonColor: '#d33',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('cancelOrderForm');
                form.action = `/customer/cancel-order/${orderId}`;
                form.querySelector('input[name="order_id"]').value = orderId;
                form.querySelector('input[name="partner_slug"]').value = partnerSlug;
                form.querySelector('input[name="table_code"]').value = tableCode;
                form.querySelector('input[name="customer_id"]').value = customerId;
                form.submit();
            }
        });
    }
</script>

@endpush
