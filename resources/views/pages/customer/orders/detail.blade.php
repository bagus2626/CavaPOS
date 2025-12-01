@extends('layouts.customer')

@section('title', 'Detail Pesanan')

@section('content')
@php
    $payment = $order->payment ?? null;
@endphp
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow p-6 md:p-8 border-t-4 border-choco">
        {{-- Header --}}
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">
                    Detail Pesanan
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
                    Kode Pesanan
                </div>
                <div class="font-semibold tracking-widest">
                    {{ $order->booking_order_code }}
                </div>
                <div class="mt-1 text-xs text-gray-500">
                    {{ $order->created_at?->format('d M Y H:i') }}
                </div>
            </div>
        </div>

        {{-- Info pemesan & meja --}}
        <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-gray-500">Nama pemesan</div>
                <div class="font-semibold">
                    {{ $order->customer_name }}
                </div>
            </div>
            <div>
                <div class="text-gray-500">Nomor meja</div>
                <div class="font-semibold">
                    {{ $table->table_no ?? '-' }}
                </div>
            </div>
        </div>

        {{-- PERINGATAN JIKA PEMBAYARAN BELUM LUNAS --}}
        @if(!$order->payment_flag && $payment && $payment->payment_status !== 'PAID')
            <div class="mt-4 rounded-xl border border-red-300 bg-red-100 px-4 py-3 text-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-1 space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-amber-900">
                                Pembayaran kamu belum dinyatakan lunas.
                            </p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-white text-[11px] font-semibold border border-amber-300 text-amber-800">
                                Status: {{ $payment->payment_status }}
                            </span>
                        </div>

                        <p class="text-xs md:text-sm text-amber-800">
                            Silakan datang ke kasir dan selesaikan pembayaran dengan menunjukkan kode berikut.
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
                                            Booking Code
                                        </div>
                                        <div class="font-mono text-base font-semibold tracking-[0.35em]">
                                            {{ $order->booking_order_code }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-[10px] uppercase text-gray-500 tracking-wide mt-1">
                                            Nama
                                        </div>
                                        <div class="text-xs font-medium text-gray-800">
                                            {{ $order->customer_name }}
                                        </div>
                                    </div>

                                    <p class="mt-1 text-[11px] text-gray-500">
                                        Tunjukkan QR / kode ini ke kasir untuk menyelesaikan pembayaran.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(!$order->payment_flag && !$payment)
            {{-- Opsional: kalau belum ada record pembayaran sama sekali tapi payment_flag sudah false --}}
            <div class="mt-4 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-amber-500 text-white text-sm font-bold flex-shrink-0">
                        !
                    </span>

                    <div class="flex-1 space-y-2">
                        <p class="font-semibold text-amber-900">
                            Pembayaran untuk pesanan ini belum tercatat.
                        </p>
                        <p class="text-xs md:text-sm text-amber-800">
                            Silakan datang ke kasir dan selesaikan pembayaran dengan menunjukkan kode berikut.
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
                                            Booking Code
                                        </div>
                                        <div class="font-mono text-base font-semibold tracking-[0.35em]">
                                            {{ $order->booking_order_code }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-[10px] uppercase text-gray-500 tracking-wide mt-1">
                                            Nama
                                        </div>
                                        <div class="text-xs font-medium text-gray-800">
                                            {{ $order->customer_name }}
                                        </div>
                                    </div>

                                    <p class="mt-1 text-[11px] text-gray-500">
                                        Tunjukkan QR / kode ini ke kasir untuk menyelesaikan pembayaran.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif




        {{-- Timeline Status --}}
        @php
            $steps = [
                [
                    'key'   => 'UNPAID',
                    'title' => 'Menunggu pembayaran',
                    'desc'  => 'Pesanan sudah tercatat. Silakan lakukan pembayaran.',
                    'icon'  => asset('icons/icon-payment-90.png'),
                ],
                [
                    'key'   => 'PAID',
                    'title' => 'Menunggu diproses',
                    'desc'  => 'Pembayaran diterima. Pesanan menunggu diproses oleh pegawai.',
                    'icon'  => asset('icons/icon-time-90.png'),
                ],
                [
                    'key'   => 'PROCESSED',
                    'title' => 'Sedang diproses',
                    'desc'  => 'Pesanan sedang disiapkan.',
                    'icon'  => asset('icons/icon-process-100.png'),
                ],
                [
                    'key'   => 'SERVED',
                    'title' => 'Selesai',
                    'desc'  => 'Pesanan sudah selesai dan disajikan.',
                    'icon'  => asset('icons/icon-done-128.png'),
                ],
            ];

            $statusIndexMap = [
                'UNPAID'    => 0,
                'PAID'      => 1,
                'PROCESSED' => 2,
                'SERVED'    => 3,
            ];

            $currentIndex = $currentIndex ?? ($statusIndexMap[$order->order_status] ?? 0);

            $totalSteps = count($steps);
            $maxIndex   = max($totalSteps - 1, 1);
            $currentIndex = min(max($currentIndex, 0), $maxIndex);

            $progressPercent = ($currentIndex / $maxIndex) * 100;

            $currentStep = $steps[$currentIndex] ?? $steps[0];
        @endphp

        <div class="mt-8">
            <h2 class="text-sm font-semibold text-gray-800 mb-3">Status Pesanan</h2>

            <div class="mt-4 px-4">
                {{-- BARIS 1: BULLET + GARIS --}}
                <div class="relative flex items-center justify-between">
                    <div class="absolute left-0 right-0 h-1 bg-gray-200 rounded-full z-0"></div>

                    <div class="absolute left-0 h-1 bg-choco rounded-full z-0"
                         style="width: {{ $progressPercent }}%;"></div>

                    @foreach ($steps as $index => $step)
                        @php
                            $isDone   = $index < $currentIndex;
                            $isActive = $index === $currentIndex;
                        @endphp

                        <div class="flex items-center justify-center w-1/4 relative z-10">
                            <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center overflow-hidden
                                @if($isDone || $isActive)
                                    bg-choco border-choco
                                @else
                                    bg-white border-gray-300
                                @endif
                            ">
                                <img src="{{ $step['icon'] }}"
                                     class="w-5 h-5 object-contain"
                                     alt="icon">
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- INFO STATUS SAAT INI --}}
                <div class="mt-4 rounded-xl bg-slate-50 px-4 py-3">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold text-choco">
                            {{ $currentStep['title'] }}
                        </p>
                    </div>
                    <p class="mt-1 text-xs text-gray-600">
                        {{ $currentStep['desc'] }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Detail Item Pesanan --}}
        <div class="mt-8">
            <h2 class="text-sm font-semibold text-gray-800 mb-3">Rincian Pesanan</h2>
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
                                    Qty: {{ $detail->quantity }} ×
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
                                Catatan: {{ $detail->customer_note }}
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">
                        Tidak ada item pada pesanan ini.
                    </p>
                @endforelse
            </div>

            <div class="mt-4 border-t pt-3 flex items-center justify-between text-sm">
                <span class="text-gray-600">Total pesanan</span>
                <span class="font-semibold text-gray-900">
                    Rp {{ number_format($order->total_order_value, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Detail Pembayaran --}}
        <div class="mt-8">
            <h2 class="text-sm font-semibold text-gray-800 mb-3">Detail Pembayaran</h2>

            @if($payment)
                <div class="border rounded-xl p-4 bg-slate-50 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">
                            Metode Pembayaran
                        </p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $payment->payment_type ?? $order->payment_method }}
                        </p>

                        <p class="mt-2 text-xs text-gray-500 uppercase tracking-wide">
                            Status Pembayaran
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
                            Total dibayar
                        </p>
                        <p class="text-lg font-semibold text-gray-900">
                            Rp {{ number_format($payment->paid_amount, 0, ',', '.') }}
                        </p>

                        @if($payment->change_amount ?? 0)
                            <p class="mt-1 text-xs text-gray-500">
                                Kembalian:
                                <span class="font-semibold">
                                    Rp {{ number_format($payment->change_amount, 0, ',', '.') }}
                                </span>
                            </p>
                        @endif

                        @if($payment->created_at)
                            <p class="mt-1 text-xs text-gray-400">
                                Dibayar pada {{ $payment->created_at->format('d M Y H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
            @else
                <div class="border rounded-xl p-4 bg-amber-50 text-sm text-amber-800">
                    Belum ada data pembayaran tercatat. Jika kamu sudah membayar, silakan konfirmasi ke kasir.
                </div>
            @endif
        </div>

        {{-- Aksi --}}
        <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('customer.menu.index', [$partner->slug, $table->table_code]) }}"
                class="inline-flex items-center px-4 py-2 rounded-lg border border-choco text-sm text-choco hover:bg-[#fee2e2]">
                    Kembali ke menu
            </a>

            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">
                    Cetak halaman
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="mt-4 text-xs text-green-600">
                {{ session('success') }}
            </div>
        @endif
    </div>
</div>
@endsection
