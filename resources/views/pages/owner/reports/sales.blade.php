@extends('layouts.owner')
@section('title', 'Business Performance Dashboard')

@section('page_title', 'Business Performance Dashboard')

@section('content')
    <section class="content">
        <div class="container-fluid">
            {{-- Form Filter --}}
            <form method="GET" action="{{ route('owner.user-owner.report.sales.index') }}">
                <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                    <div class="flex flex-col space-y-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            {{-- Bagian Kiri: Tombol Filter Periode --}}
                            <div class="flex flex-wrap gap-2">
                                <div class="flex bg-gray-100 rounded-lg p-1">
                                    <button type="button" onclick="changeFilterPeriod('yearly')" id="filter-btn-yearly"
                                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200">Tahunan</button>
                                    <button type="button" onclick="changeFilterPeriod('monthly')" id="filter-btn-monthly"
                                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200">Bulanan</button>
                                    <button type="button" onclick="changeFilterPeriod('daily')" id="filter-btn-daily"
                                        class="px-3 py-1 text-sm font-medium rounded-md">Harian</button>
                                </div>
                            </div>

                            {{-- Bagian Kanan: Tombol Export Excel --}}
                            <div>
                                <a href="{{ route('owner.user-owner.report.sales.export', request()->query()) }}"
                                    class="inline-flex items-center px-4 py-2 bg-choco text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Export Excel
                                </a>
                            </div>
                        </div>

                        {{-- Input Filter --}}
                        <div id="global-period-filters">
                            <div id="global-yearly-filter"
                                class="period-filter hidden grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">From Year</label>
                                    <select name="year_from" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}" {{ ($filters['year_from'] ?? date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">To Year</label>
                                    <select name="year_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}" {{ ($filters['year_to'] ?? date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="flex justify-start">
                                    <button type="submit" name="period" value="yearly"
                                        class="px-4 py-2 bg-choco text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Apply
                                        Filters</button>
                                </div>
                            </div>
                            <div id="global-monthly-filter"
                                class="period-filter hidden grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Year</label>
                                    <select name="month_year" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}" {{ ($filters['month_year'] ?? date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="flex justify-start">
                                    <button type="submit" name="period" value="monthly"
                                        class="px-4 py-2 bg-choco text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Apply
                                        Filters</button>
                                </div>
                            </div>
                            <div id="global-daily-filter"
                                class="period-filter hidden grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div class="flex justify-start">
                                    <button type="submit" name="period" value="daily"
                                        class="px-4 py-2 bg-choco text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Apply
                                        Filters</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Menu Sold</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalOrders ?? 0, 0, ',', '.') }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Booking order</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ number_format($totalBookingOrders ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6 xl:col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Revenue Trend Analysis</h3>
                        <div class="text-sm text-gray-500" id="chart-period-indicator">
                            {{ $indicatorText ?? 'Tampilan Data' }}
                        </div>
                    </div>
                    <div class="h-80"><canvas id="revenueTrendChart"></canvas></div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 xl:col-span-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Revenue by Category</h3>
                    <div class="h-80"><canvas id="categoryChart"></canvas></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-choco flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white mb-0">Top Products by Quantity</h3>
                    </div>
                    <div class="p-6 max-h-[48rem] overflow-y-auto">
                        <div class="space-y-4" id="top-products-list">
                            @forelse ($topProducts ?? [] as $product)
                                <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-xl">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $product->total_quantity }} Terjual</p>
                                    </div>
                                    <span class="font-bold text-green-600">Rp
                                        {{ number_format($product->total_sales, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center">Tidak ada data produk.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-choco flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white mb-0">Recent Transactions</h3>
                    </div>
                    <div class="p-6 max-h-[48rem] overflow-y-auto">
                        <div class="space-y-4" id="recent-transactions-list">
                            @forelse ($recentTransactions ?? [] as $transaction)
                                <div class="transaction-row flex justify-between items-center p-3 hover:bg-gray-50 rounded-xl cursor-pointer"
                                    data-order-id="{{ $transaction->id }}">
                                    <div>
                                        <p class="font-medium text-gray-800">Order #{{ $transaction->booking_order_code }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}
                                        </p>
                                    </div>
                                    <span class="font-bold text-green-600">Rp
                                        {{ number_format($transaction->total_order_value, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center">Tidak ada transaksi terkini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="transactionDetailModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Detail Pesanan</h3>
                    <button id="closeModalXBtn"
                        class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
                </div>
                <div id="modal-body" class="mt-2 px-2 py-3 text-sm text-gray-700" style="min-height: 100px;">
                    <p class="text-center">Memuat detail...</p>
                </div>
                <div class="items-center px-4 py-3 border-t mt-4">
                    <button id="closeModalBtn"
                        class="px-4 py-2 bg-choco text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-soft-choco focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Melewatkan data dari PHP ke JavaScript untuk Chart --}}
    <script>
        const revenueChartData = @json($revenueChartData ?? ['labels' => [], 'data' => []]);
        const categoryChartData = @json($categoryChartData ?? ['labels' => [], 'data' => []]);
    </script>

    {{-- Memuat file JavaScript utama Anda (untuk render chart) --}}
    <script src="{{ asset('js/sales.js') }}"></script>

    {{-- Menggabungkan semua script UI dan Modal ke dalam satu blok --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // --- BAGIAN 1: LOGIKA UI FILTER ---
            let currentFilterPeriod = '{{ $filters["period"] ?? "daily" }}';

            window.changeFilterPeriod = function (period) {
                currentFilterPeriod = period;
                document.querySelectorAll('[id^="filter-btn-"]').forEach(btn => {
                    btn.classList.remove('bg-choco', 'text-white');
                    btn.classList.add('text-gray-600');
                });
                const activeBtn = document.getElementById(`filter-btn-${period}`);
                if (activeBtn) {
                    activeBtn.classList.add('bg-choco', 'text-white');
                    activeBtn.classList.remove('text-gray-600');
                }
                document.querySelectorAll('.period-filter').forEach(filter => filter.classList.add('hidden'));
                const activeFilterDiv = document.getElementById(`global-${period}-filter`);
                if (activeFilterDiv) activeFilterDiv.classList.remove('hidden');
            }

            changeFilterPeriod(currentFilterPeriod);

            // --- BAGIAN 2: LOGIKA FUNGSIONALITAS MODAL ---
            const modal = document.getElementById('transactionDetailModal');
            if (modal) {
                const closeModalBtns = [document.getElementById('closeModalBtn'), document.getElementById('closeModalXBtn')];
                const modalBody = document.getElementById('modal-body');
                const modalTitle = document.getElementById('modal-title');

                document.querySelectorAll('.transaction-row').forEach(row => {
                    row.addEventListener('click', async function () {
                        const orderId = this.dataset.orderId;
                        const orderCode = this.querySelector('.font-medium').textContent;

                        modalTitle.textContent = `Detail ${orderCode}`;
                        modalBody.innerHTML = '<p class="text-center">Memuat detail...</p>';
                        modal.classList.remove('hidden');

                        try {
                            const response = await fetch(`{{ route('owner.user-owner.report.order-details', ['id' => '__ORDER_ID__']) }}`.replace('__ORDER_ID__', orderId));
                            if (!response.ok) throw new Error('Gagal mengambil data detail pesanan.');

                            const details = await response.json();

                            if (details.length > 0) {
                                let grandTotal = 0;
                                let html = '<ul class="space-y-4">';
                                details.forEach(item => {
                                    const totalItemPrice = (parseFloat(item.base_price) + parseFloat(item.options_price)) * item.quantity;
                                    grandTotal += totalItemPrice;
                                    html += `<li class="border-b pb-3">
                                                        <div class="flex justify-between font-semibold">
                                                            <span>${item.quantity}x ${item.name}</span>
                                                            <span>Rp ${Number(totalItemPrice).toLocaleString('id-ID')}</span>
                                                        </div>`;
                                    if (item.options && item.options.length > 0) {
                                        html += '<ul class="mt-2 pl-4 text-gray-600 text-xs space-y-1">';
                                        item.options.forEach(option => {
                                            html += `<li class="flex justify-between">
                                                                <span>+ ${option.name}</span>
                                                                <span>Rp ${Number(option.price).toLocaleString('id-ID')}</span>
                                                             </li>`;
                                        });
                                        html += '</ul>';
                                    }
                                    html += '</li>';
                                });
                                html += `<li class="flex justify-between font-bold text-base pt-3">
                                                    <span>Grand Total</span>
                                                    <span>Rp ${Number(grandTotal).toLocaleString('id-ID')}</span>
                                                 </li>`;
                                html += '</ul>';
                                modalBody.innerHTML = html;
                            } else {
                                modalBody.innerHTML = '<p class="text-center">Tidak ada detail produk untuk pesanan ini.</p>';
                            }
                        } catch (error) {
                            console.error(error);
                            modalBody.innerHTML = `<p class="text-center text-red-500">${error.message}</p>`;
                        }
                    });
                });
                const hideModal = () => modal.classList.add('hidden');
                closeModalBtns.forEach(btn => btn.addEventListener('click', hideModal));
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) hideModal();
                });
            }
        });
    </script>
@endpush