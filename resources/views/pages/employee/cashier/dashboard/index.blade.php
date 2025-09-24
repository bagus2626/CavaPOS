{{-- resources/views/employee/cashier/dashboard.blade.php --}}
@extends('layouts.employee-cashier')

@section('title', 'Employee Dashboard')
<meta name="csrf-token" content="{{ csrf_token() }}">


@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="mr-3">
            <h1 class="text-2xl font-extrabold text-soft-choco">Dashboard Kasir <span class="text-choco">{{ $partner->name }}</span></h1>
            <p class="text-sm text-gray-500">Pantau pesanan, proses pembayaran cash, dan cek transaksi QRIS.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employee.cashier.dashboard') }}"
               class="inline-flex items-center rounded-xl border border-choco/20 px-3 py-2 text-sm font-semibold text-choco hover:bg-soft-choco/10 focus:ring-2 focus:ring-soft-choco/30">
                Refresh
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('employee.cashier.dashboard') }}" class="mb-5 mt-10">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Metode</label>
                <select name="payment" class="w-full rounded-xl border-gray-300 focus:border-soft-choco focus:ring-soft-choco/40">
                    <option value="">Semua</option>
                    <option value="CASH" {{ request('payment')=='CASH'?'selected':'' }}>Cash</option>
                    <option value="QRIS" {{ request('payment')=='QRIS'?'selected':'' }}>QRIS</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full rounded-xl border-gray-300 focus:border-soft-choco focus:ring-soft-choco/40">
                    <option value="">Semua</option>
                    <option value="0" {{ request('status')=='0'?'selected':'' }}>Belum Bayar</option>
                    <option value="1" {{ request('status')=='1'?'selected':'' }}>Sudah Bayar</option>
                    <option value="PROCESSED" {{ request('status')=='PROCESSED'?'selected':'' }}>Diproses</option>
                    <option value="SERVED" {{ request('status')=='SERVED'?'selected':'' }}>Selesai</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Dari</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border-gray-300 focus:border-soft-choco focus:ring-soft-choco/40">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sampai</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-xl border-gray-300 focus:border-soft-choco focus:ring-soft-choco/40">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari (Kode/Meja/Nama)</label>
                <div class="flex">
                    <input type="text" name="q"
                            class="w-full rounded-l-xl border-gray-300 focus:border-soft-choco focus:ring-soft-choco/40"
                            placeholder="CTH: ORD123 / Meja 5"
                            value="{{ request('q') }}">
                    <button type="submit"
                            class="shrink-0 bg-choco text-white px-4 rounded-r-xl hover:bg-choco/90 focus:ring-2 focus:ring-soft-choco/40">
                        Cari
                    </button>
                </div>
            </div>
        </div>

        {{-- Tombol reset filter --}}
        @if(request()->anyFilled(['payment','status','from','to','q']))
            <div class="mt-3">
                <a href="{{ route('employee.cashier.dashboard') }}"
                class="inline-block px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-600 hover:bg-soft-choco/50 hover:text-white hover:border-none">
                    Hapus Filter
                </a>
            </div>
        @endif
    </form>

    <div class="text-choco">Data Order ({{ $periodLabel }})</div>

    {{-- Metrics --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        {{-- Total orders (gradient card) --}}
        <div class="rounded-2xl border border-choco/10 p-4 shadow-sm bg-gradient-to-br from-soft-choco to-choco">
            <p class="metric-label text-xs text-white/90">Total Order</p>
            <p class="mt-2 text-3xl font-extrabold text-white drop-shadow">{{ number_format($ordersToday->count() ?? 0) }}</p>
        </div>

        {{-- Unpaid cash --}}
        <div class="rounded-2xl border border-choco/10 p-4 bg-white shadow-sm">
            <p class="metric-label text-xs text-gray-500">Belum Bayar (Cash)</p>
            <p class="mt-2 text-3xl font-extrabold text-soft-choco">{{ number_format($ordersToday->where('payment_method', 'CASH')->where('payment_flag', 0)->count() ?? 0) }}</p>
        </div>

        {{-- paid cash --}}
        <div class="rounded-2xl border border-choco/10 p-4 bg-white shadow-sm">
            <p class="metric-label text-xs text-gray-500">Sudah Bayar (Cash)</p>
            <p class="mt-2 text-3xl font-extrabold text-soft-choco">{{ number_format($ordersToday->where('payment_method', 'CASH')->where('payment_flag', 1)->count() ?? 0) }}</p>
        </div>

        {{-- QRIS paid --}}
        <div class="rounded-2xl border border-choco/10 p-4 bg-white shadow-sm">
            <p class="metric-label text-xs text-gray-500">QRIS Berhasil</p>
            <p class="mt-2 text-3xl font-extrabold text-teal-700">{{ number_format($ordersToday->where('payment_method', 'QRIS')->where('payment_flag', 1)->count() ?? 0) }}</p>
        </div>

        {{-- On Process Order --}}
        <div class="rounded-2xl border border-choco/10 p-4 bg-white shadow-sm">
            <p class="metric-label text-xs text-gray-500">Order Diproses</p>
            <p class="mt-2 text-3xl font-extrabold text-cyan-700">{{ number_format($ordersToday->whereIn('order_status', ['PROCESSED', 'PAID'])->count() ?? 0) }}</p>
        </div>

        {{-- Revenue --}}
        <div class="rounded-2xl border border-choco/10 p-4 bg-white shadow-sm">
            <p class="metric-label text-xs text-gray-500">Pendapatan</p>
            <p class="mt-2 text-2xl font-extrabold text-choco">Rp {{ number_format($ordersToday->where('payment_flag', 1)->sum('total_order_value') ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- TABS --}}
    <div class="mb-6">
        <div class="inline-flex rounded-xl border border-choco/20 bg-white overflow-hidden">
            @php
                // Label tab
                $tabs = [
                    'pembelian'  => 'Pembelian',
                    'pembayaran' => 'Pembayaran',
                    'proses'     => 'Proses',
                    'selesai'    => 'Selesai',
                ];

                // Hitung jumlah untuk badge
                $tabCounts = [
                    // Pembayaran: Cash & belum bayar
                    'pembayaran' => number_format(
                        $ordersToday->where('payment_method', 'CASH')->where('payment_flag', 0)->count() ?? 0
                    ),
                    // Proses: status PROCESSED
                    'proses' => number_format(
                        $ordersToday->whereIn('order_status', ['PROCESSED', 'PAID'])->count() ?? 0
                    ),
                    // Selesai (opsional): status SERVED
                    'selesai' => number_format(
                        $ordersToday->where('order_status', 'SERVED')->count() ?? 0
                    ),
                ];

            @endphp


            @foreach ($tabs as $key => $label)
                <button type="button"
                        class="tab-btn px-4 py-2 text-sm font-semibold hover:bg-soft-choco/10 focus:ring-2 focus:ring-soft-choco/30
                            {{ $loop->first ? 'bg-soft-choco/10 text-choco' : 'text-gray-700' }}"
                        data-tab="{{ $key }}">
                    <span class="inline-flex items-center gap-2">
                        <span>{{ $label }}</span>
                        @if(isset($tabCounts[$key]))
                            <span class="ml-1 inline-flex items-center justify-center rounded-full
                                        bg-choco/10 text-choco text-[11px] font-bold px-2 py-0.5">
                                {{ $tabCounts[$key] }}
                            </span>
                        @endif
                    </span>
                </button>
                @if (! $loop->last)
                    <span class="w-px bg-choco/10"></span>
                @endif
            @endforeach

        </div>
    </div>

    {{-- TAB CONTENT CONTAINER --}}
    <div id="tabContent"
        class="relative rounded-2xl border border-choco/10 bg-white shadow-sm overflow-hidden overflow-y-auto mb-7 h-[70vh] [scrollbar-gutter:stable]">
        <div id="tabLoading" class="hidden p-10 text-center text-gray-500">Memuat…</div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(120vh-120px)] lg:h-[calc(80vh-120px)]">
        {{-- Pending Cash --}}
        <div class="lg:col-span-1 flex flex-col rounded-2xl border border-choco/10 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-choco/10 flex items-center justify-between sticky top-0 bg-white z-10">
                <h2 class="font-semibold text-choco">Butuh Proses Cash</h2>
                <span class="text-xs text-gray-500">{{ $pendingCashOrders->count() }} order</span>
            </div>
            <div class="p-4 overflow-y-auto flex-1">
                @if ($pendingCashOrders->isEmpty())
                    <div class="text-center text-sm text-gray-500 py-8">
                        Tidak ada order cash yang menunggu pembayaran.
                    </div>
                @else
                    <ul class="space-y-3">
                        @foreach ($pendingCashOrders as $o)
                            <li class="rounded-xl border border-choco/10 p-3 hover:bg-soft-choco/5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $o->booking_order_code }} &middot; Meja {{ $o->table->table_no }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Rp {{ number_format($o->total_order_value, 0, ',', '.') }} &middot; 
                                            {{ $o->created_at?->format('H:i') }} &middot; 
                                            <span class="font-bold">{{ $o->customer_name }}</span>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('employee.cashier.order-detail', $o->id) }}"
                                            data-detail-btn
                                            data-order-id="{{ $o->id }}"
                                            class="text-sm px-3 py-1.5 rounded-lg border border-choco/20 text-choco hover:bg-soft-choco/10 focus:ring-2 focus:ring-soft-choco/30">
                                            Detail
                                        </a>
                                        <button type="button"
                                                class="text-sm px-3 py-1.5 rounded-lg bg-soft-choco text-white hover:bg-soft-choco/90"
                                                data-cash-btn
                                                data-order-id="{{ $o->id }}"
                                                data-order-code="{{ $o->booking_order_code }}"
                                                data-order-name="{{ $o->customer_name }}"
                                                data-order-total="{{ $o->total_order_value }}"
                                                data-cash-url="{{ route('employee.cashier.cash-payment', '__ID__') }}">
                                            Proses
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Orders Today --}}
        <div class="lg:col-span-2 flex flex-col rounded-2xl border border-choco/10 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-choco/10 flex items-center justify-between sticky top-0 bg-white z-10">
                <h2 class="font-semibold text-choco">Data Order ({{ $periodLabel }})</h2>
                <span class="text-xs text-gray-500">{{ $ordersToday->count() }} order</span>
            </div>
            <div class="overflow-x-auto overflow-y-auto flex-1">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#fcecec] text-gray-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left">Waktu</th>
                            <th class="px-4 py-3 text-left">Kode/Name</th>
                            <th class="px-4 py-3 text-left">Meja</th>
                            <th class="px-4 py-3 text-left">Metode</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-choco/10">
                        @forelse ($ordersToday as $o)
                            <tr class="hover:bg-soft-choco/5">
                                <td class="px-4 py-3">{{ $o->created_at?->format('H:i') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $o->booking_order_code }}</div>
                                    <div class="text-xs text-gray-500 mt-0 truncate">{{ $o->customer_name ?? '—' }}</div>
                                </td>
                                <td class="px-4 py-3">Meja {{ $o->table->table_no }}</td>
                                <td class="px-4 py-3">
                                    @if ($o->payment_method === 'QRIS')
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs">QRIS</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs">CASH</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($o->order_status === 'PAID')
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs">PAID</span>
                                    @elseif ($o->order_status === 'UNPAID')
                                        <span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs">UNPAID</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 px-2 py-0.5 text-xs">{{ $o->order_status }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($o->total_order_value, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex gap-2">
                                        <a href="{{ route('employee.cashier.order-detail', $o->id) }}"
                                            data-detail-btn
                                            data-order-id="{{ $o->id }}"
                                            class="text-sm px-3 py-1.5 rounded-lg border border-choco/20 text-choco hover:bg-soft-choco/10 focus:ring-2 focus:ring-soft-choco/30">
                                            Detail
                                        </a>
                                        <a href="#"
                                        class="px-3 py-1.5 rounded-lg border border-choco/20 text-choco hover:bg-soft-choco/10"
                                        target="_blank">Cetak</a>
                                        @if ($o->payment_method === 'CASH' && $o->status === 'UNPAID')
                                            <button type="button"
                                                    class="px-3 py-1.5 rounded-lg bg-choco text-white hover:bg-choco/90"
                                                    data-cash-btn
                                                    data-order-id="{{ $o->id }}"
                                                    data-order-code="{{ $o->booking_order_code }}"
                                                    data-order-total="{{ $o->total_order_value }}">
                                                Bayar
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">Belum ada order hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($ordersToday, 'links'))
                <div class="px-4 py-3 border-t border-choco/10 sticky bottom-0 bg-white">
                    {{ $ordersToday->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@include('pages.employee.cashier.dashboard.modals.cash')
@include('pages.employee.cashier.dashboard.modals.detail')
@endsection

@push('scripts')
<script>
(function() {
    const tabBtns    = document.querySelectorAll('.tab-btn');
    const tabContent = document.getElementById('tabContent');
    const tabLoading = document.getElementById('tabLoading');

    function setActive(btn) {
        tabBtns.forEach(b => b.classList.remove('bg-soft-choco/10','text-choco'));
        btn.classList.add('bg-soft-choco/10','text-choco');
    }
    function setActiveByKey(key) {
        const btn = document.querySelector(`.tab-btn[data-tab="${key}"]`);
        if (btn) setActive(btn);
    }

    async function loadTab(tab, afterLoaded) {
        tabLoading.classList.remove('hidden');
        [...tabContent.children].forEach(el => { if (el.id !== 'tabLoading') el.remove(); });

        try {
            const qs = new URLSearchParams(window.location.search);
            qs.set('_', Date.now());
            const url = "{{ route('employee.cashier.tab', '__TAB__') }}".replace('__TAB__', tab) + '?' + qs.toString();

            const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();
            const frag = document.createRange().createContextualFragment(html);
            tabContent.appendChild(frag);

            // panggil callback setelah DOM tab terpasang
            if (typeof afterLoaded === 'function') afterLoaded();
        } catch (e) {
            tabContent.appendChild(Object.assign(document.createElement('div'), {
                className: 'p-6 text-rose-600',
                textContent: 'Gagal memuat data. Coba lagi.'
            }));
        } finally {
            tabLoading.classList.add('hidden');
        }
    }

    // initial load
    if (tabBtns.length) {
    const savedTab = localStorage.getItem("activeTab");
    let initialBtn = tabBtns[0];
    if (savedTab) {
        const foundBtn = document.querySelector(`.tab-btn[data-tab="${savedTab}"]`);
        if (foundBtn) initialBtn = foundBtn;
    }
    setActive(initialBtn);
    loadTab(initialBtn.dataset.tab, () => {
        if (initialBtn.dataset.tab === 'pembelian' && typeof window.initPembelianTab === 'function') {
        window.initPembelianTab();
        }
    });
    }

    tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        setActive(btn);
        loadTab(btn.dataset.tab, () => {
        if (btn.dataset.tab === 'pembelian' && typeof window.initPembelianTab === 'function') {
            window.initPembelianTab();
        }
        });
        localStorage.setItem("activeTab", btn.dataset.tab);
    });
    });


    // 🔸 Ekspor ke window biar file JS eksternal bisa panggil
    window.CASHIER = {
        setActiveTab: (key) => setActiveByKey(key),
        loadTab: (key, afterLoaded) => loadTab(key, afterLoaded)
    };
})();

</script>

@endpush
