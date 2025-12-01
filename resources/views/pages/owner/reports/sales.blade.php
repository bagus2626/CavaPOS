@extends('layouts.owner')
@section('title', __('messages.owner.sales_report.business_performance_dashboard'))

@section('page_title', __('messages.owner.sales_report.business_performance_dashboard'))

@section('content')
    @vite(['resources/css/app.css'])

    <section class="content mb-4">
        <div class="container-fluid">
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <form method="GET" action="{{ route('owner.user-owner.report.sales.index') }}" id="partner-filter-form">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="flex-1">

                            <select name="partner_id" id="partner-select"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-choco focus:border-transparent">
                                <option value="">{{ __('messages.owner.sales_report.all_outlets') }}</option>
                                @foreach($partners as $partner)
                                    <option value="{{ $partner->id }}" {{ ($filters['partner_id'] ?? '') == $partner->id ? 'selected' : '' }}>
                                        {{ $partner->name }} ({{ $partner->partner_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 items-end">
                            <button type="submit"
                                class="px-6 py-2 bg-choco text-white text-sm font-medium rounded-lg hover:bg-opacity-90 transition duration-200">
                                {{ __('messages.owner.sales_report.apply') }}
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="period" value="{{ $filters['period'] ?? 'daily' }}">
                    @if(isset($filters['year_from']))
                        <input type="hidden" name="year_from" value="{{ $filters['year_from'] }}">
                    @endif
                    @if(isset($filters['year_to']))
                        <input type="hidden" name="year_to" value="{{ $filters['year_to'] }}">
                    @endif
                    @if(isset($filters['month_year']))
                        <input type="hidden" name="month_year" value="{{ $filters['month_year'] }}">
                    @endif
                    @if(isset($filters['month_from']))
                        <input type="hidden" name="month_from" value="{{ $filters['month_from'] }}">
                    @endif
                    @if(isset($filters['month_to']))
                        <input type="hidden" name="month_to" value="{{ $filters['month_to'] }}">
                    @endif
                    @if(isset($filters['from']))
                        <input type="hidden" name="from" value="{{ $filters['from'] }}">
                    @endif
                    @if(isset($filters['to']))
                        <input type="hidden" name="to" value="{{ $filters['to'] }}">
                    @endif
                </form>

                @if(isset($selectedPartner))
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            <div>
                                <p class="text-sm font-medium text-blue-900">
                                    {{ __('messages.owner.sales_report.showing_data_for') }}: <span class="font-bold">{{ $selectedPartner->name }}</span>
                                </p>
                                <p class="text-xs text-blue-700 mt-1">
                                    {{ __('messages.owner.sales_report.partner_code') }}: {{ $selectedPartner->partner_code }} |
                                    {{ __('messages.owner.sales_report.location') }}: {{ $selectedPartner->city }}, {{ $selectedPartner->province }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <form method="GET" action="{{ route('owner.user-owner.report.sales.index') }}">
                @if(isset($filters['partner_id']) && $filters['partner_id'])
                    <input type="hidden" name="partner_id" value="{{ $filters['partner_id'] }}">
                @endif

                <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                    <div class="flex flex-col space-y-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div class="flex flex-wrap gap-2">
                                <div class="flex bg-gray-100 rounded-lg p-1">
                                    <button type="button" onclick="changeFilterPeriod('yearly')" id="filter-btn-yearly"
                                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200">{{ __('messages.owner.sales_report.annualy') }}</button>
                                    <button type="button" onclick="changeFilterPeriod('monthly')" id="filter-btn-monthly"
                                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200">{{ __('messages.owner.sales_report.monthly') }}</button>
                                    <button type="button" onclick="changeFilterPeriod('daily')" id="filter-btn-daily"
                                        class="px-3 py-1 text-sm font-medium rounded-md">{{ __('messages.owner.sales_report.daily') }}</button>
                                </div>
                            </div>

                            <div>
                                <a href="{{ route('owner.user-owner.report.sales.export', request()->query()) }}"
                                    class="inline-flex items-center px-4 py-2 bg-choco text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    {{ __('messages.owner.sales_report.export_excel') }}
                                </a>
                            </div>
                        </div>

                        <div id="global-period-filters">
                            {{-- YEARLY FILTER --}}
                            <div id="global-yearly-filter"
                                class="period-filter hidden grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.owner.sales_report.from_year') }}</label>
                                    <select name="year_from" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}" {{ ($filters['year_from'] ?? date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.owner.sales_report.to_year') }}</label>
                                    <select name="year_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}" {{ ($filters['year_to'] ?? date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="flex justify-start">
                                    <button type="submit" name="period" value="yearly"
                                        class="px-4 py-2 bg-choco text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                                        {{ __('messages.owner.sales_report.apply_filter') }}
                                    </button>
                                </div>
                            </div>

                            {{-- MONTHLY FILTER --}}
                            <div id="global-monthly-filter"
                                class="period-filter hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.owner.sales_report.select_year') }}</label>
                                    <select name="month_year" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}" {{ ($filters['month_year'] ?? date('Y')) == $year ? 'selected' : '' }}>{{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.owner.sales_report.from_month') }}</label>
                                    <select name="month_from" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <option value="1" {{ ($filters['month_from'] ?? 1) == 1 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.january') }}
                                        </option>
                                        <option value="2" {{ ($filters['month_from'] ?? 1) == 2 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.february') }}
                                        </option>
                                        <option value="3" {{ ($filters['month_from'] ?? 1) == 3 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.march') }}
                                        </option>
                                        <option value="4" {{ ($filters['month_from'] ?? 1) == 4 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.april') }}
                                        </option>
                                        <option value="5" {{ ($filters['month_from'] ?? 1) == 5 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.may') }}
                                        </option>
                                        <option value="6" {{ ($filters['month_from'] ?? 1) == 6 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.june') }}
                                        </option>
                                        <option value="7" {{ ($filters['month_from'] ?? 1) == 7 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.july') }}
                                        </option>
                                        <option value="8" {{ ($filters['month_from'] ?? 1) == 8 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.august') }}
                                        </option>
                                        <option value="9" {{ ($filters['month_from'] ?? 1) == 9 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.september') }}
                                        </option>
                                        <option value="10" {{ ($filters['month_from'] ?? 1) == 10 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.october') }}
                                        </option>
                                        <option value="11" {{ ($filters['month_from'] ?? 1) == 11 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.november') }}
                                        </option>
                                        <option value="12" {{ ($filters['month_from'] ?? 1) == 12 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.december') }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.owner.sales_report.to_month') }}</label>
                                    <select name="month_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <option value="1" {{ ($filters['month_to'] ?? date('n')) == 1 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.january') }}
                                        </option>
                                        <option value="2" {{ ($filters['month_to'] ?? date('n')) == 2 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.february') }}
                                        </option>
                                        <option value="3" {{ ($filters['month_to'] ?? date('n')) == 3 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.march') }}
                                        </option>
                                        <option value="4" {{ ($filters['month_to'] ?? date('n')) == 4 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.april') }}
                                        </option>
                                        <option value="5" {{ ($filters['month_to'] ?? date('n')) == 5 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.may') }}
                                        </option>
                                        <option value="6" {{ ($filters['month_to'] ?? date('n')) == 6 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.june') }}
                                        </option>
                                        <option value="7" {{ ($filters['month_to'] ?? date('n')) == 7 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.july') }}
                                        </option>
                                        <option value="8" {{ ($filters['month_to'] ?? date('n')) == 8 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.august') }}
                                        </option>
                                        <option value="9" {{ ($filters['month_to'] ?? date('n')) == 9 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.september') }}
                                        </option>
                                        <option value="10" {{ ($filters['month_to'] ?? date('n')) == 10 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.october') }}
                                        </option>
                                        <option value="11" {{ ($filters['month_to'] ?? date('n')) == 11 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.november') }}
                                        </option>
                                        <option value="12" {{ ($filters['month_to'] ?? date('n')) == 12 ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.december') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="flex justify-start">
                                    <button type="submit" name="period" value="monthly"
                                        class="px-4 py-2 bg-choco text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                                        {{ __('messages.owner.sales_report.apply_filter') }}
                                    </button>
                                </div>
                            </div>

                            <div id="global-daily-filter"
                                class="period-filter hidden grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.owner.sales_report.from_date') }}</label>
                                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.owner.sales_report.to_date') }}</label>
                                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div class="flex justify-start">
                                    <button type="submit" name="period" value="daily"
                                        class="px-4 py-2 bg-choco text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                                        {{ __('messages.owner.sales_report.apply_filter') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm font-medium text-gray-500 uppercase">{{ __('messages.owner.sales_report.total_sales') }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm font-medium text-gray-500 uppercase">{{ __('messages.owner.sales_report.total_menu_sold') }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalOrders ?? 0, 0, ',', '.') }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm font-medium text-gray-500 uppercase">{{ __('messages.owner.sales_report.total_booking_orders') }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ number_format($totalBookingOrders ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6 xl:col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">{{ __('messages.owner.sales_report.sales_trend_analysis') }}</h3>
                        <div class="text-sm text-gray-500" id="chart-period-indicator">
                            {{ $indicatorText ?? 'Tampilan Data' }}
                        </div>
                    </div>
                    <div class="h-80"><canvas id="revenueTrendChart"></canvas></div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 xl:col-span-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('messages.owner.sales_report.sales_by_category') }}</h3>
                    <div class="h-80"><canvas id="categoryChart"></canvas></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-choco flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white mb-0">{{ __('messages.owner.sales_report.products_by_quantity') }}</h3>
                        <div class="flex items-center gap-2">
                            <div class="relative inline-block">
                                <select id="sort-products-filter"
                                    class="px-2 py-1 w-24 text-sm border border-white rounded-lg bg-choco text-white focus:outline-none focus:ring-2 focus:ring-white/50  pr-8"
                                    style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                                    <option value="desc" {{ ($filters['sort_products'] ?? 'desc') == 'desc' ? 'selected' : '' }}>
                                        {{ __('messages.owner.sales_report.highest') }}
                                    </option>
                                    <option value="asc" {{ ($filters['sort_products'] ?? 'desc') == 'asc' ? 'selected' : '' }}>
                                        {{ __('messages.owner.sales_report.lowest') }}
                                    </option>
                                </select>

                                <svg class="w-4 h-4 text-white absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 max-h-[48rem] overflow-y-auto">
                        <div class="space-y-4" id="top-products-list">
                            @forelse ($topProducts ?? [] as $product)
                                <div class="flex justify-between items-center p-2 m-0 hover:bg-gray-50 rounded-xl">
                                    <div>
                                        <p class="font-medium text-gray-800 m-0">{{ $product->name }}</p>
                                        <p class="text-sm text-gray-500 m-0">{{ $product->total_quantity }} {{ __('messages.owner.sales_report.sold') }}</p>
                                    </div>
                                    <span class="font-bold text-green-600">Rp
                                        {{ number_format($product->total_sales, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center">{{ __('messages.owner.sales_report.no_product_data_found') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-choco flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white mb-0">{{ __('messages.owner.sales_report.recent_sales_transactions') }}</h3>
                    </div>
                    <div class="p-6 max-h-[48rem] overflow-y-auto">
                        <div class="space-y-4" id="recent-transactions-list">
                            @forelse ($recentTransactions ?? [] as $transaction)
                                <div class="transaction-row flex justify-between items-center p-2 m-0 hover:bg-gray-50 rounded-xl cursor-pointer"
                                    data-order-id="{{ $transaction->id }}">
                                    <div>
                                        <p class="font-medium text-gray-800 m-0">Order #{{ $transaction->booking_order_code }}
                                        </p>
                                        <p class="text-sm text-gray-500 m-0">
                                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}
                                        </p>
                                    </div>
                                    <span class="font-bold text-green-600">Rp
                                        {{ number_format($transaction->total_order_value, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center">{{ __('messages.owner.sales_report.no_recent_sales_transactions_found') }}</p>
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">{{ __('messages.owner.sales_report.order_details') }}</h3>
                    <button id="closeModalXBtn"
                        class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
                </div>
                <div id="modal-body" class="mt-2 px-2 py-3 text-sm text-gray-700" style="min-height: 100px;">
                    <p class="text-center">{{ __('messages.owner.sales_report.loading_detail') }}</p>
                </div>
                <div class="items-center px-4 py-3 border-t mt-4">
                    <button id="closeModalBtn"
                        class="px-4 py-2 bg-choco text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-soft-choco focus:outline-none focus:ring-2 focus:ring-gray-300">
                        {{ __('messages.owner.sales_report.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/app.js'])
    <script src="{{ asset('js/owner/reports/sales.js') }}"></script>

    {{-- Melewatkan data dari PHP ke JavaScript untuk Chart --}}
    <script>
        const revenueChartData = @json($revenueChartData ?? ['labels' => [], 'data' => []]);
        const categoryChartData = @json($categoryChartData ?? ['labels' => [], 'data' => []]);
    </script>

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
                        modalBody.innerHTML = '<p class="text-center">{{ __('messages.owner.sales_report.loading_detail') }}</p>';
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
                                modalBody.innerHTML = '<p class="text-center">{{ __('messages.owner.sales_report.no_product_detail_found') }}</p>';
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

            // --- BAGIAN 3: LOGIKA FILTER TOP PRODUCTS ---
            const sortFilter = document.getElementById('sort-products-filter');
            const productsList = document.getElementById('top-products-list');

            if (sortFilter && productsList) {
                sortFilter.addEventListener('change', async function () {
                    const sortValue = this.value;

                    // Show loading state
                    productsList.innerHTML = '<p class="text-gray-500 text-center py-4">Memuat data...</p>';

                    try {
                        // Get current URL parameters
                        const urlParams = new URLSearchParams(window.location.search);
                        urlParams.set('sort_products', sortValue);

                        // Fetch sorted data
                        const response = await fetch(`{{ route('owner.user-owner.report.sales.products') }}?${urlParams.toString()}`);

                        if (!response.ok) throw new Error('{{ __('messages.owner.sales_report.failed_load_product_data') }}');

                        const products = await response.json();

                        // Update the list
                        if (products.length > 0) {
                            let html = '';
                            products.forEach(product => {
                                html += `
                                            <div class="flex justify-between items-center p-2 m-0 hover:bg-gray-50 rounded-xl">
                                                <div>
                                                    <p class="font-medium text-gray-800 m-0">${product.name}</p>
                                                    <p class="text-sm text-gray-500 m-0">${product.total_quantity} Sold</p>
                                                </div>
                                                <span class="font-bold text-green-600">Rp ${Number(product.total_sales).toLocaleString('id-ID')}</span>
                                            </div>
                                        `;
                            });
                            productsList.innerHTML = html;
                        } else {
                            productsList.innerHTML = '<p class="text-gray-500 text-center">{{ __('messages.owner.sales_report.no_data_found') }}</p>';
                        }

                        // Update URL without refresh
                        const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
                        window.history.pushState({ path: newUrl }, '', newUrl);

                    } catch (error) {
                        console.error(error);
                        productsList.innerHTML = '<p class="text-red-500 text-center">{{ __('messages.owner.sales_report.failed_load_data') }}</p>';
                    }
                });
            }
        });
    </script>
@endpush