{{-- resources/views/pages/employee/cashier/activity.blade.php --}}
@extends('layouts.employee-cashier')


@section('title', 'Activity - Track Orders')


@section('content')
<!-- Tambahkan Material Icons CDN -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">


<meta name="csrf-token" content="{{ csrf_token() }}">


<div class="flex-1 flex flex-col h-full overflow-hidden">
    {{-- Header --}}
    <header class="flex items-center justify-between px-6 py-4 bg-gray-100 z-10 shrink-0">
        <div class="flex items-center space-x-4">
            <div>
                <h1 class="text-2xl font-bold" style="color: #ae1504;">Track Order Activity</h1>
                <p class="text-xs text-gray-500 mt-0.5">{{ now()->isoFormat('dddd, DD MMMM YYYY') }}</p>
            </div>
        </div>
        <div
            </div>
        </div>
    </header>


    {{-- Content --}}
    <div class="flex-1 overflow-y-auto px-6 py-6">
        {{-- Metrics Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            {{-- Total orders --}}
            <div class="rounded-2xl p-4 shadow-sm" style="background: linear-gradient(to bottom right, #ae1504, #8a1003);">
                <p class="text-xs text-white/90">Total Order</p>
                <p class="mt-2 text-3xl font-extrabold text-white drop-shadow">
                    {{ number_format($metrics['total_order'] ?? 0) }}
                </p>
            </div>


            {{-- Unpaid cash --}}
            <div class="rounded-2xl p-4 bg-white shadow-sm">
                <p class="text-xs text-gray-500">Belum Bayar</p>
                <p class="mt-2 text-3xl font-extrabold text-orange-600">
                    {{ number_format($metrics['unpaid'] ?? 0) }}
                </p>
            </div>


            {{-- Paid cash --}}
            <div class="rounded-2xl p-4 bg-white shadow-sm">
                <p class="text-xs text-gray-500">Sudah Bayar</p>
                <p class="mt-2 text-3xl font-extrabold text-teal-600">
                    {{ number_format($metrics['paid_cash'] ?? 0) }}
                </p>
            </div>


            {{-- QRIS paid --}}
            <div class="rounded-2xl p-4 bg-white shadow-sm">
                <p class="text-xs text-gray-500">QRIS Berhasil</p>
                <p class="mt-2 text-3xl font-extrabold text-green-600">
                    {{ number_format($metrics['qris_paid'] ?? 0) }}
                </p>
            </div>


            {{-- On Process --}}
            <div class="rounded-2xl p-4 bg-white shadow-sm">
                <p class="text-xs text-gray-500">Diproses</p>
                <p class="mt-2 text-3xl font-extrabold text-cyan-600">
                    {{ number_format($metrics['processed'] ?? 0) }}
                </p>
            </div>


            {{-- Revenue --}}
            <div class="rounded-2xl p-4 bg-white shadow-sm">
                <p class="text-xs text-gray-500">Pendapatan</p>
                <p class="mt-2 text-xl font-extrabold" style="color: #ae1504;">
                    Rp {{ number_format($metrics['revenue'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>


        {{-- Search Bar --}}
        <form method="GET" action="{{ route('employee.cashier.activity') }}" class="mb-6">
            <div class="relative">
                <input
                    name="q"
                    id="searchInput"
                    class="w-full bg-white border-none rounded-2xl py-4 pl-6 pr-16 shadow-sm text-gray-600 placeholder-gray-400 focus:ring-2 outline-none search-input-red"
                    placeholder="Cari order (kode/meja/nama)..."
                    type="text"
                    value="{{ request('q') }}"
                />
                <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-white rounded-xl px-4 py-2 transition-colors" style="background-color: #ae1504;" onmouseover="this.style.backgroundColor='#8a1003'" onmouseout="this.style.backgroundColor='#ae1504'">
                    <span class="material-icons-round">search</span>
                </button>
            </div>
        </form>


        {{-- Advanced Filters - Always Visible --}}
        <div class="mb-6">
            <form method="GET" action="{{ route('employee.cashier.activity') }}" class="bg-white rounded-2xl p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons-round" style="color: #ae1504;">filter_list</span>
                    <h3 class="text-sm font-semibold text-gray-800">Filter Pencarian</h3>
                </div>
               
                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Metode Pembayaran</label>
                        <select name="payment" class="w-full h-[42px] rounded-xl border-gray-300 text-sm select-red">
                            <option value="">Semua</option>
                            <option value="CASH" {{ request('payment') == 'CASH' ? 'selected' : '' }}>Cash</option>
                            <option value="QRIS" {{ request('payment') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                        </select>
                    </div>


                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status Order</label>
                        <select name="status" class="w-full h-[42px] rounded-xl border-gray-300 text-sm select-red">
                            <option value="">Semua</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Sudah Bayar</option>
                            <option value="PROCESSED" {{ request('status') == 'PROCESSED' ? 'selected' : '' }}>Diproses</option>
                            <option value="SERVED" {{ request('status') == 'SERVED' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>


                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="from" value="{{ request('from', $from->format('Y-m-d')) }}" class="w-full h-[42px] rounded-xl border-gray-300 text-sm input-red">
                    </div>


                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="to" value="{{ request('to', $to->format('Y-m-d')) }}" class="w-full h-[42px] rounded-xl border-gray-300 text-sm input-red">
                    </div>


                    <div class="flex gap-3">
                        <button type="submit" class="h-[42px] px-6 text-white rounded-xl transition-colors text-sm font-medium flex items-center gap-2 whitespace-nowrap" style="background-color: #ae1504;" onmouseover="this.style.backgroundColor='#8a1003'" onmouseout="this.style.backgroundColor='#ae1504'">
                            <span class="material-icons-round text-lg">search</span>
                            <span>Terapkan</span>
                        </button>


                        @if(request()->anyFilled(['payment', 'status', 'from', 'to', 'q']))
                            <a href="{{ route('employee.cashier.activity') }}" class="h-[42px] px-6 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors text-sm font-medium flex items-center gap-2 whitespace-nowrap">
                                <span class="material-icons-round text-lg">refresh</span>
                                <span>Reset</span>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>


        {{-- Tabs --}}
        <div class="mb-6">
            <div class="flex justify-start gap-2">
                @php
                    $tabs = [
                        'pembayaran' => 'Pembayaran',
                        'proses'     => 'Proses',
                        'selesai'    => 'Selesai',
                    ];
                    $activeTab = request('tab', 'pembayaran');
                @endphp


                @foreach ($tabs as $key => $label)
                    <a href="{{ route('employee.cashier.activity', array_merge(request()->except('tab'), ['tab' => $key])) }}"
                       class="px-4 py-2 rounded-xl font-medium text-sm transition-colors flex items-center gap-2
                       {{ $activeTab == $key ? 'text-white' : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm' }}"
                       @if($activeTab == $key) style="background-color: #ae1504;" @endif>
                        <span>{{ $label }}</span>
                        @if(isset($tabCounts[$key]))
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $activeTab == $key ? '' : 'bg-gray-200' }}"
                                @if($activeTab == $key) style="background-color: #8a1003;" @endif>
                                {{ number_format($tabCounts[$key]) }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>


        {{-- Orders Table berdasarkan tab aktif --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- HEADER - Sticky -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">
                    {{ ucfirst(request('tab', 'pembayaran')) }}
                </h2>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border" style="background-color: #fef2f2; color: #ae1504; border-color: #fecaca;">
                    @php
    $activeTab = request('tab', 'pembayaran');
   
    // Filter orders berdasarkan tab dari paginated collection
    $filteredOrders = $ordersToday->filter(function($o) use ($activeTab) {
        return match($activeTab) {
            'pembayaran' => in_array($o->payment_method, ['CASH', 'QRIS'])
                && in_array($o->order_status, ['UNPAID', 'EXPIRED']),
            'proses' => in_array($o->order_status, ['PROCESSED', 'PAID']),
            'selesai' => $o->order_status === 'SERVED',
            default => true
        };
    });
@endphp
                    {{ $filteredOrders->count() }} order
                </span>
            </div>


            <!-- SCROLL AREA -->
            <div class="overflow-y-auto">
                @if ($filteredOrders->isEmpty())
                    <div class="flex flex-col items-center justify-center text-center h-full py-12 px-4">
                        <div class="size-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Belum ada order untuk tab {{ ucfirst($activeTab) }}</p>
                        @if(request()->anyFilled(['payment', 'status', 'from', 'to', 'q']))
                            <a href="{{ route('employee.cashier.activity') }}" class="inline-block mt-4 px-4 py-2 text-white rounded-xl transition-colors text-sm font-medium" style="background-color: #ae1504;" onmouseover="this.style.backgroundColor='#8a1003'" onmouseout="this.style.backgroundColor='#ae1504'">
                                Reset Filter
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Table</th>
                                    <th class="px-14 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer Info</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Payment</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Time</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Total Amount</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="order-list">
                                @foreach ($filteredOrders as $order)
                                    <tr class="group hover:bg-red-50/30 transition-colors duration-150" id="order-item-{{ $order->id }}">
                                        {{-- ORDER ID --}}
                                        <td class="px-4 py-4 align-middle">
                                            <span class="inline-flex items-center text-sm font-medium text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">
                                                {{ $order->booking_order_code }}
                                            </span>
                                        </td>


                                        {{-- TABLE --}}
                                        <td class="px-4 py-4 align-middle">
                                            <span class="inline-flex items-center justify-center size-8 rounded-full text-sm font-bold bg-white text-gray-700 border border-gray-200 shadow-sm">
                                                {{ $order->table?->table_no ?? '-' }}
                                            </span>
                                        </td>


                                        {{-- CUSTOMER INFO --}}
                                        <td class="px-4 py-4 align-middle">
                                            <div class="flex items-center gap-3">
                                                <div class="size-9 rounded-full flex items-center justify-center text-xs font-bold" style="background-color: #fef2f2; color: #ae1504;">
                                                    {{ strtoupper(substr($order->customer_name ?? 'G', 0, 2)) }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-semibold text-gray-900">{{ $order->customer_name ?? 'Guest' }}</span>
                                                    <span class="text-xs text-gray-500">Walk-in Customer</span>
                                                </div>
                                            </div>
                                        </td>


                                        {{-- PAYMENT METHOD --}}
                                        <td class="px-4 py-4 align-middle text-center">
                                            @if($order->payment_method === 'QRIS')
                                                <div class="inline-flex items-center gap-1.5">
                                                    <img src="{{ asset('icons/qris_svg.svg') }}" alt="QRIS" class="h-4 w-auto">
                                                    <span class="text-sm text-gray-700 font-medium">QRIS</span>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-700 font-medium">CASH</span>
                                            @endif
                                        </td>


                                        {{-- TIME --}}
                                        <td class="px-4 py-4 align-middle text-center">
                                            <span class="text-sm text-gray-600 font-medium tabular-nums">
                                                {{ $order->created_at?->format('H:i') }}
                                            </span>
                                        </td>


                                        {{-- TOTAL AMOUNT --}}
                                        <td class="px-4 py-4 align-middle text-center">
                                            <span class="text-sm font-bold text-gray-900 tabular-nums">
                                                Rp {{ number_format($order->total_order_value, 0, ',', '.') }}
                                            </span>
                                        </td>


                                        {{-- STATUS --}}
                                        <td class="px-4 py-4 align-middle text-center">
                                            @if(in_array($order->order_status, ['UNPAID', 'EXPIRED']))
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                                    <span class="size-1.5 rounded-full bg-rose-500"></span>
                                                    {{ $order->order_status }}
                                                </span>
                                            @elseif($order->order_status === 'PAID')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                                    PAID
                                                </span>
                                            @elseif($order->order_status === 'PROCESSED')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border" style="background-color: #fef2f2; color: #ae1504; border-color: #fecaca;">
                                                    <span class="size-1.5 rounded-full" style="background-color: #ae1504;"></span>
                                                    PROCESSED
                                                </span>
                                            @elseif($order->order_status === 'SERVED')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                    <span class="size-1.5 rounded-full bg-green-500"></span>
                                                    SERVED
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="size-1.5 rounded-full bg-amber-500"></span>
                                                    {{ $order->order_status }}
                                                </span>
                                            @endif
                                        </td>


                                        {{-- ACTIONS --}}
                                        <td class="px-4 py-4 align-middle">
                                            <div class="flex items-center justify-end gap-2">
                                                {{-- Detail Button --}}
                                                <button type="button"
                                                    data-detail-btn
                                                    data-order-id="{{ $order->id }}"
                                                    class="p-2 rounded-lg text-gray-400 transition-colors hover-red-custom"
                                                    title="View Details">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
         {{-- Pagination --}}
        @if ($ordersToday->hasPages())
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Menampilkan
                    <span class="font-semibold">{{ $ordersToday->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-semibold">{{ $ordersToday->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-semibold">{{ $ordersToday->total() }}</span>
                    order
                </div>
               
                <div class="flex gap-2">
                    {{-- Previous Button --}}
                    @if ($ordersToday->onFirstPage())
                        <span class="px-3 py-2 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed inline-flex items-center">
                            <span class="material-icons-round text-base">chevron_left</span>
                        </span>
                    @else
                        <a href="{{ $ordersToday->previousPageUrl() }}"
                           class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 transition-colors inline-flex items-center hover-pagination-red">
                            <span class="material-icons-round text-base">chevron_left</span>
                        </a>
                    @endif


                    {{-- Page Numbers --}}
                    @php
                        $start = max(1, $ordersToday->currentPage() - 2);
                        $end = min($ordersToday->lastPage(), $ordersToday->currentPage() + 2);
                    @endphp
                   
                    @if($start > 1)
                        <a href="{{ $ordersToday->url(1) }}"
                           class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 transition-colors hover-pagination-red">
                            1
                        </a>
                        @if($start > 2)
                            <span class="px-3 py-2 text-gray-400">...</span>
                        @endif
                    @endif


                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $ordersToday->currentPage())
                            <span class="px-3 py-2 rounded-lg text-white font-medium min-w-[40px] text-center" style="background-color: #ae1504;">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $ordersToday->url($page) }}"
                               class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 transition-colors min-w-[40px] text-center hover-pagination-red">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor


                    @if($end < $ordersToday->lastPage())
                        @if($end < $ordersToday->lastPage() - 1)
                            <span class="px-3 py-2 text-gray-400">...</span>
                        @endif
                        <a href="{{ $ordersToday->url($ordersToday->lastPage()) }}"
                           class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 transition-colors hover-pagination-red">
                            {{ $ordersToday->lastPage() }}
                        </a>
                    @endif


                    {{-- Next Button --}}
                    @if ($ordersToday->hasMorePages())
                        <a href="{{ $ordersToday->nextPageUrl() }}"
                           class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 transition-colors inline-flex items-center hover-pagination-red">
                            <span class="material-icons-round text-base">chevron_right</span>
                        </a>
                    @else
                        <span class="px-3 py-2 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed inline-flex items-center">
                            <span class="material-icons-round text-base">chevron_right</span>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
    </div>
   
</div>


@include('pages.employee.cashier.dashboard.modals.detail')
@endsection


@push('styles')
<style>
    /* Pastikan Material Icons terlihat dengan baik */
    .material-icons-round {
        font-family: 'Material Icons Round';
        font-weight: normal;
        font-style: normal;
        font-size: 24px;
        line-height: 1;
        letter-spacing: normal;
        text-transform: none;
        display: inline-block;
        white-space: nowrap;
        word-wrap: normal;
        direction: ltr;
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
        -moz-osx-font-smoothing: grayscale;
        font-feature-settings: 'liga';
    }
</style>
@endpush


@push('scripts')
<script src="{{ asset('js/employee/cashier/dashboard/detail.js') }}"></script>
<script>
    // Handle detail modal - Sesuai dengan halaman pembayaran
    document.addEventListener('click', function(e) {
        const detailBtn = e.target.closest('[data-detail-btn]');
        if (detailBtn) {
            e.preventDefault();
            const orderId = detailBtn.dataset.orderId;
            showOrderDetail(orderId);
        }
    });


    function showOrderDetail(orderId) {
        const detailUrl = "{{ route('employee.cashier.order-detail', '__ID__') }}".replace('__ID__', orderId);
       
        // Fetch order detail
        fetch(detailUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate modal dengan data order
                populateDetailModal(data.order);
               
                // Show modal
                const modal = document.getElementById('detailModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat detail order');
        });
    }


    function populateDetailModal(order) {
        // Update order info
        document.getElementById('detail-order-code').textContent = order.booking_order_code || '-';
        document.getElementById('detail-customer-name').textContent = order.customer_name || 'Guest';
        document.getElementById('detail-table-no').textContent = order.table?.table_no || '-';
        document.getElementById('detail-payment-method').textContent = order.payment_method || '-';
        document.getElementById('detail-order-status').textContent = order.order_status || '-';
       
        // Update items list
        const itemsList = document.getElementById('detail-items-list');
        if (itemsList && order.order_items) {
            itemsList.innerHTML = order.order_items.map(item => `
                <div class="flex justify-between items-start py-2 border-b border-gray-100 last:border-0">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">${item.menu?.name || '-'}</p>
                        <p class="text-xs text-gray-500">Qty: ${item.quantity} × Rp ${parseInt(item.price).toLocaleString('id-ID')}</p>
                    </div>
                    <p class="text-sm font-bold text-gray-900">
                        Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}
                    </p>
                </div>
            `).join('');
        }
       
        // Update total
        document.getElementById('detail-total').textContent = 'Rp ' + parseInt(order.total_order_value).toLocaleString('id-ID');
    }


    // Close modal
    function closeDetailModal() {
        const modal = document.getElementById('detailModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }


    // Close on backdrop click
    document.getElementById('detailModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeDetailModal();
        }
    });
</script>
@endpush