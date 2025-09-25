@extends('layouts.owner')
@section('title', 'Business Performance Dashboard')

@section('page_title', 'Business Performance Dashboard')

@section('content')
    <section class="content">
        <div class="container-fluid">
            {{-- Form ini akan mengirimkan data filter ke controller saat di-submit --}}
            <form method="GET" action="{{ route('owner.user-owner.report.sales.index') }}">
                <!-- Global Filter Section -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                    <div class="flex flex-col space-y-6">
                        <!-- Period Selection Buttons (Hanya untuk UI) -->
                        <div class="flex flex-wrap gap-2">
                            <div class="flex bg-gray-100 rounded-lg p-1">
                                <button type="button" onclick="changeFilterPeriod('yearly')" id="filter-btn-yearly"
                                    class="px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200">Tahunan</button>
                                <button type="button" onclick="changeFilterPeriod('monthly')" id="filter-btn-monthly"
                                    class="px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200">Bulanan</button>
                                <button type="button" onclick="changeFilterPeriod('weekly')" id="filter-btn-weekly"
                                    class="px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200">Mingguan</button>
                                <button type="button" onclick="changeFilterPeriod('daily')" id="filter-btn-daily"
                                    class="px-3 py-1 text-sm font-medium rounded-md">Harian</button>
                            </div>
                        </div>

                        <!-- Filter Inputs -->
                        <div id="global-period-filters">
                            <!-- Yearly Filter -->
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
                                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Apply
                                        Filters</button>
                                </div>
                            </div>

                            <!-- Monthly Filter -->
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
                                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Apply
                                        Filters</button>
                                </div>
                            </div>

                            <!-- Weekly Filter -->
                            <div id="global-weekly-filter"
                                class="period-filter hidden grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Month</label>
                                    <input type="month" name="week_month"
                                        value="{{ $filters['week_month'] ?? date('Y-m') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div class="flex justify-start">
                                    <button type="submit" name="period" value="weekly"
                                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Apply
                                        Filters</button>
                                </div>
                            </div>

                            <!-- Daily Filter -->
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
                                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Apply
                                        Filters</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Key Performance Indicators -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalOrders ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Booking Order</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{-- Data bisa ditambahkan di sini --}}0</p>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6 xl:col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Revenue Trend Analysis</h3>
                        <div class="text-sm text-gray-500" id="chart-period-indicator">
                            {{ $indicatorText ?? 'Tampilan Data' }}</div>
                    </div>
                    <div class="h-80"><canvas id="revenueTrendChart"></canvas></div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 xl:col-span-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Revenue by Category</h3>
                    <div class="h-80"><canvas id="categoryChart"></canvas></div>
                </div>
            </div>

            <!-- Data Tables Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-indigo-600">
                        <h3 class="text-lg font-semibold text-white">Top Revenue Products</h3>
                    </div>
                    <div class="p-6 max-h-96 overflow-y-auto">
                        <div class="space-y-4" id="top-products-list">
                            @forelse ($topProducts ?? [] as $product)
                                <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-xl">
                                    <span class="font-medium text-gray-800">{{ $product->name }}</span>
                                    <span class="font-bold text-indigo-600">Rp
                                        {{ number_format($product->total_sales, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center">Tidak ada data produk.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-green-600">
                        <h3 class="text-lg font-semibold text-white">Recent Transactions</h3>
                    </div>
                    <div class="p-6 max-h-96 overflow-y-auto">
                        <div class="space-y-4" id="recent-transactions-list">
                            @forelse ($recentTransactions ?? [] as $transaction)
                                <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-xl">
                                    <div>
                                        <p class="font-medium text-gray-800">Order #{{ $transaction->booking_order_code }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}</p>
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
@endsection

@push('scripts')
    {{-- JavaScript ini hanya untuk UI, bukan untuk mengambil data --}}
    <script>
        let currentFilterPeriod = '{{ $filters["period"] ?? "daily" }}';

        function changeFilterPeriod(period) {
            currentFilterPeriod = period;
            document.querySelectorAll('[id^="filter-btn-"]').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white');
                btn.classList.add('text-gray-600');
            });
            const activeBtn = document.getElementById(`filter-btn-${period}`);
            if (activeBtn) {
                activeBtn.classList.add('bg-indigo-600', 'text-white');
                activeBtn.classList.remove('text-gray-600');
            }
            document.querySelectorAll('.period-filter').forEach(filter => filter.classList.add('hidden'));
            const activeFilterDiv = document.getElementById(`global-${period}-filter`);
            if (activeFilterDiv) activeFilterDiv.classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => changeFilterPeriod(currentFilterPeriod));
    </script>

    {{-- Melewatkan data dari PHP (Controller) ke JavaScript untuk Chart --}}
    <script>
        const revenueChartData = @json($revenueChartData ?? ['labels' => [], 'data' => []]);
        const categoryChartData = @json($categoryChartData ?? ['labels' => [], 'data' => []]);
    </script>
@endpush