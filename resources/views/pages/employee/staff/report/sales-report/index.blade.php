@extends('layouts.staff')

@section('title', __('messages.owner.sales_report.business_performance_dashboard') ?? 'Sales Report Dashboard')
@section('page_title', __('messages.owner.sales_report.business_performance_dashboard') ?? 'Sales Report Dashboard')

@section('content')
<div class="modern-container">
    <div class="container-modern">
        
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">{{ __('messages.owner.sales_report.business_performance_dashboard') ?? 'Business Performance' }}</h1>
                <p class="page-subtitle">{{ $indicatorText ?? __('messages.owner.sales_report.sales_report') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('employee.' . $routePrefix . '.report.sales.export', request()->query()) }}" 
                   class="btn-modern btn-sm-modern btn-success-modern" >
                    <span class="material-symbols-outlined">file_download</span>
                    {{ __('messages.owner.sales_report.export_excel') ?? 'Export Excel' }}
                </a>
            </div>
        </div>

        <div class="modern-card">
            <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                <form method="GET" action="{{ route('employee.' . $routePrefix . '.report.sales.index') }}">
                    <div class="row">
                        
                        {{-- FILTER OUTLET DIHAPUS KARENA STAFF HANYA 1 OUTLET --}}

                        <div class="col-xl-3">
                            <div class="form-group">
                                <label for="period-select" class="form-label-modern">
                                    {{ __('messages.owner.sales_report.report_type') ?? 'Report Type' }}
                                </label>
                                <div class="select-wrapper">
                                    <select name="period" id="period-select" class="form-control-modern" onchange="toggleFilterInputs()">
                                        <option value="daily" {{ ($filters['period'] ?? 'daily') == 'daily' ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.daily_report') ?? 'Daily' }}
                                        </option>
                                        <option value="monthly" {{ ($filters['period'] ?? 'daily') == 'monthly' ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.monthly_report') ?? 'Monthly' }}
                                        </option>
                                        <option value="yearly" {{ ($filters['period'] ?? 'daily') == 'yearly' ? 'selected' : '' }}>
                                            {{ __('messages.owner.sales_report.yearly_report') ?? 'Yearly' }}
                                        </option>
                                    </select>
                                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            
                            <div id="filter-daily" class="filter-group {{ ($filters['period'] ?? 'daily') == 'daily' ? '' : 'd-none' }}">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label-modern small">{{ __('messages.owner.sales_report.from_date') ?? 'From Date' }}</label>
                                            <input type="date" name="from" value="{{ $filters['from'] ?? date('Y-m-d') }}" class="form-control-modern">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label-modern small">{{ __('messages.owner.sales_report.to_date') ?? 'To Date' }}</label>
                                            <input type="date" name="to" value="{{ $filters['to'] ?? date('Y-m-d') }}" class="form-control-modern">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="filter-monthly" class="filter-group {{ ($filters['period'] ?? 'daily') == 'monthly' ? '' : 'd-none' }}">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label-modern small">{{ __('messages.owner.sales_report.from_month') ?? 'From Month' }}</label>
                                            <input type="month" 
                                                name="month_from" 
                                                value="{{ $filters['month_from'] ?? date('Y-m') }}" 
                                                class="form-control-modern">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label-modern small">{{ __('messages.owner.sales_report.to_month') ?? 'To Month' }}</label>
                                            <input type="month" 
                                                name="month_to" 
                                                value="{{ $filters['month_to'] ?? date('Y-m') }}" 
                                                class="form-control-modern">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="filter-yearly" class="filter-group {{ ($filters['period'] ?? 'daily') == 'yearly' ? '' : 'd-none' }}">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label-modern small">{{ __('messages.owner.sales_report.from_year') ?? 'From Year' }}</label>
                                            <input type="number" 
                                                name="year_from" 
                                                value="{{ $filters['year_from'] ?? date('Y') }}" 
                                                min="2020" 
                                                max="{{ date('Y') }}" 
                                                class="form-control-modern">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label-modern small">{{ __('messages.owner.sales_report.to_year') ?? 'To Year' }}</label>
                                            <input type="number" 
                                                name="year_to" 
                                                value="{{ $filters['year_to'] ?? date('Y') }}" 
                                                min="2020" 
                                                max="{{ date('Y') }}" 
                                                class="form-control-modern">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-xl-3 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="submit" class="btn-modern btn-primary-modern w-100 justify-content-center">
                                    {{ __('messages.owner.sales_report.apply') ?? 'Apply Filter' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12 col-sm-6 col-lg-4 mb-3">
                <div class="modern-card stats-card">
                    <div class="stats-icon">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">{{ __('messages.owner.sales_report.total_sales') ?? 'Total Sales' }}</div>
                        <div class="stats-value">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4 mb-3">
                <div class="modern-card stats-card">
                    <div class="stats-icon">
                        <span class="material-symbols-outlined">restaurant_menu</span>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">{{ __('messages.owner.sales_report.total_menu_sold') ?? 'Total Items Sold' }}</div>
                        <div class="stats-value">{{ number_format($totalOrders ?? 0, 0, ',', '.') }} {{ __('messages.owner.sales_report.items_unit') ?? 'items' }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4 mb-3">
                <div class="modern-card stats-card">
                    <div class="stats-icon">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">{{ __('messages.owner.sales_report.total_booking_orders') ?? 'Total Orders' }}</div>
                        <div class="stats-value">{{ number_format($totalBookingOrders ?? 0, 0, ',', '.') }} {{ __('messages.owner.sales_report.orders_unit') ?? 'orders' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-8 mb-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">trending_up</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.sales_report.revenue_trend_chart_title') ?? 'Revenue Trend' }}</h3>
                        </div>
                    </div>
                    <div class="card-body-modern" style="position: relative; height: 350px;">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4 mb-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">pie_chart</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.sales_report.category_chart_title') ?? 'Sales by Category' }}</h3>
                        </div>
                    </div>
                    <div class="card-body-modern" style="position: relative; height: 350px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6 mb-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">account_balance_wallet</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.sales_report.payment_method_chart_title') ?? 'Payment Methods' }}</h3>
                        </div>
                    </div>
                    <div class="card-body-modern" style="position: relative; height: 350px;">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 mb-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">monetization_on</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.sales_report.payment_revenue_chart_title') ?? 'Revenue by Payment' }}</h3>
                        </div>
                    </div>
                    <div class="card-body-modern" style="position: relative; height: 350px;">
                        <canvas id="paymentRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">bar_chart</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.sales_report.top_products_chart_title') ?? 'Top 5 Products' }}</h3>
                        </div>
                    </div>
                    <div class="card-body-modern" style="position: relative; height: 350px;">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="modern-card">
                    <div class="card-header-modern d-flex justify-content-between align-items-center">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">star</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.sales_report.products_by_quantity') ?? 'Product Sales Details' }}</h3>
                        </div>
                    </div>

                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 60px;">#</th>
                                    <th>{{ __('messages.owner.products.master_products.product_name') ?? 'Product Name' }}</th>
                                    
                                    <th class="text-center sortable-header" 
                                        data-sort="quantity" 
                                        onclick="sortProductTable('quantity')" 
                                        style="cursor: pointer;">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <span>{{ __('messages.owner.sales_report.sold') ?? 'Sold' }}</span>
                                            <span class="material-symbols-outlined sort-icon" style="font-size: 18px;">unfold_more</span>
                                        </div>
                                    </th>
                                    
                                    <th class="sortable-header" 
                                        data-sort="revenue" 
                                        onclick="sortProductTable('revenue')" 
                                        style="cursor: pointer; text-align: left !important;">
                                        <div class="d-flex align-items-center gap-1">
                                            <span>{{ __('messages.owner.sales_report.total_sales') ?? 'Total Revenue' }}</span>
                                            <span class="material-symbols-outlined sort-icon" style="font-size: 18px;">unfold_more</span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="top-products-list">
                                @forelse ($topProducts ?? [] as $index => $product)
                                    <tr class="table-row">
                                        <td class="text-center text-muted">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>
                                            <span class="data-name">{{ $product->name }}</span>
                                        </td>

                                        <td class="text-center" data-quantity="{{ $product->total_quantity }}">
                                            <span>
                                                {{ $product->total_quantity }} 
                                            </span>
                                        </td>

                                        <td data-revenue="{{ $product->total_sales }}" style="text-align: left !important;">
                                            <span class="fw-bold text-success">
                                                Rp {{ number_format($product->total_sales, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <div class="table-empty-state">
                                                <span class="material-symbols-outlined">inventory</span>
                                                <h4>{{ __('messages.owner.sales_report.no_product_data_found') ?? 'No product sales data found' }}</h4>
                                                <p>{{ __('messages.owner.sales_report.no_data_found') ?? 'No data found for this period' }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($topProducts->hasPages())
                        <div class="table-pagination">
                            {{ $topProducts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <script>
        window.salesReportLang = {
            revenue_trend_no_data: "{{ __('messages.owner.sales_report.revenue_trend_no_data') ?? 'No Data' }}",
            category_chart_no_data: "{{ __('messages.owner.sales_report.category_chart_no_data') ?? 'No Data' }}",
            top_products_no_data: "{{ __('messages.owner.sales_report.top_products_no_data') ?? 'No Data' }}",
            payment_method_no_data: "{{ __('messages.owner.sales_report.payment_method_no_data') ?? 'No Data' }}",
            payment_revenue_no_data: "{{ __('messages.owner.sales_report.payment_revenue_no_data') ?? 'No Data' }}",
            transactions_unit: "{{ __('messages.owner.sales_report.transactions_unit') ?? 'trx' }}",
            items_unit: "{{ __('messages.owner.sales_report.items_unit') ?? 'items' }}",
            sold_label: "{{ __('messages.owner.sales_report.sold_label') ?? 'Sold' }}",
            revenue_label: "{{ __('messages.owner.sales_report.revenue_label') ?? 'Revenue' }}",
            sales_label: "{{ __('messages.owner.sales_report.sales_label') ?? 'Sales' }}"
        };
    </script>

    {{-- Sesuaikan URL asset ini jika Anda membuat file JS terpisah untuk staf, atau gunakan milik owner jika tidak ada AJAX URL spesifik yang berbeda di dalam scriptnya --}}
    <script src="{{ asset('js/owner/reports/sales.js') }}"></script>

    <script>
        // Data chart dari Controller
        const revenueChartData = @json($revenueChartData ?? ['labels' => [], 'data' => []]);
        const categoryChartData = @json($categoryChartData ?? ['labels' => [], 'data' => []]);
        const topProductsChartData = @json($topProductsChart ?? ['labels' => [], 'data' => []]);
        const paymentMethodChartData = @json($paymentMethodChart ?? ['labels' => [], 'data' => []]);
        const paymentRevenueChartData = @json($paymentRevenueChart ?? ['labels' => [], 'data' => []]);

        // Logic Switch Tampilan Filter (Javascript Murni)
        function toggleFilterInputs() {
            const period = document.getElementById('period-select').value;
            
            document.querySelectorAll('.filter-group').forEach(el => el.classList.add('d-none'));
            
            if (period === 'daily') {
                document.getElementById('filter-daily').classList.remove('d-none');
            } else if (period === 'monthly') {
                document.getElementById('filter-monthly').classList.remove('d-none');
            } else if (period === 'yearly') {
                document.getElementById('filter-yearly').classList.remove('d-none');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleFilterInputs();
        });
    </script>

    <script>
        // Fungsi Sort untuk Tabel Produk
        let currentSortColumn = 'quantity';
        let currentSortDirection = 'desc';

        function sortProductTable(column) {
            // Toggle direction jika klik column yang sama
            if (currentSortColumn === column) {
                currentSortDirection = currentSortDirection === 'desc' ? 'asc' : 'desc';
            } else {
                currentSortColumn = column;
                currentSortDirection = 'desc';
            }
            
            // Update icon pada header
            updateSortIcons();
            
            // Get table body
            const tbody = document.getElementById('top-products-list');
            const rows = Array.from(tbody.querySelectorAll('tr.table-row'));
            
            // Sort rows
            rows.sort((a, b) => {
                let aValue, bValue;
                
                if (column === 'quantity') {
                    aValue = parseInt(a.querySelector('[data-quantity]').getAttribute('data-quantity'));
                    bValue = parseInt(b.querySelector('[data-quantity]').getAttribute('data-quantity'));
                } else if (column === 'revenue') {
                    aValue = parseFloat(a.querySelector('[data-revenue]').getAttribute('data-revenue'));
                    bValue = parseFloat(b.querySelector('[data-revenue]').getAttribute('data-revenue'));
                }
                
                if (currentSortDirection === 'asc') {
                    return aValue - bValue;
                } else {
                    return bValue - aValue;
                }
            });
            
            // Update nomor urut dan append kembali ke tbody
            rows.forEach((row, index) => {
                row.querySelector('.text-center.text-muted').textContent = index + 1;
                tbody.appendChild(row);
            });
        }

        function updateSortIcons() {
            // Reset semua icon
            document.querySelectorAll('.sort-icon').forEach(icon => {
                icon.textContent = 'unfold_more';
                icon.classList.remove('active');
            });
            
            // Set icon untuk column yang aktif
            const activeIcon = document.querySelector(`[data-sort="${currentSortColumn}"] .sort-icon`);
            if (activeIcon) {
                activeIcon.textContent = currentSortDirection === 'asc' ? 'arrow_upward' : 'arrow_downward';
                activeIcon.classList.add('active');
            }
        }

        // Initialize saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            updateSortIcons();
        });       
    </script>
@endpush