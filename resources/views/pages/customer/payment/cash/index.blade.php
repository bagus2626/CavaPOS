@extends('layouts.customer')

@section('title', 'Pembayaran Tunai')

@section('content')
<div class="max-w-md mx-auto px-4 py-8">
  <div class="bg-white rounded-2xl shadow p-6 text-center">

    <h1 class="text-xl font-semibold">{{__('messages.customer.payment.show_order_code')}}</h1>
    <p class="text-sm text-gray-600 mt-1">
      {{__('messages.customer.payment.please_to_cashier')}}
    </p>

    {{-- QR CODE (2D only) --}}
    <div class="mt-6 flex justify-center">
      @php
        $hasQr = class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class);
      @endphp

      @if($hasQr)
        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
              ->size(220)
              ->margin(1)
              ->generate($order->booking_order_code) !!}
      @else
        <div class="text-xs text-gray-500">
          (Pasang paket <code>simplesoftwareio/simple-qrcode</code> untuk menampilkan QR code)
        </div>
      @endif
    </div>

    {{-- Kode pemesanan (teks) --}}
    <div class="mt-2 text-lg font-mono tracking-widest select-all">
      {{ $order->booking_order_code }}
    </div>

    {{-- Detail pemesan & meja --}}
    <div class="mt-6 grid grid-cols-2 gap-4 text-left text-sm">
      <div>
        <div class="text-gray-500">{{__('messages.customer.payment.orderers_name')}}</div>
        <div class="font-semibold">{{ $order->customer_name }}</div>
      </div>
      <div>
        <div class="text-gray-500">{{__('messages.customer.payment.table_no')}}</div>
        <div class="font-semibold">{{ $table->table_no }}</div>
      </div>
    </div>

    {{-- Aksi --}}
    <div class="mt-6 flex items-center justify-center gap-3">
      <a href="{{ route('customer.orders.order-detail', [$partner?->slug ?? '', $table->table_code, $order->id]) }}"
              class="px-4 py-2 rounded-lg border hover:bg-gray-50">
        {{__('messages.customer.payment.order_detail')}}
      </a>
      <a href="{{ route('customer.menu.index', [$partner?->slug ?? '', $table->table_code]) }}"
         class="px-4 py-2 rounded-lg bg-choco text-white hover:bg-soft-choco">
        {{__('messages.customer.payment.back_to_menu')}}
      </a>
    </div>

    @if (session('success'))
      <div class="mt-4 text-xs text-green-600">
        {{ session('success') }}
      </div>
    @endif

  </div>
</div>
@endsection
