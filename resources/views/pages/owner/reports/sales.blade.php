@extends('layouts.owner')
@section('title', __('messages.owner.sales_report.business_performance_dashboard'))
@section('page_title', __('messages.owner.sales_report.business_performance_dashboard'))

@section('content')
<div class="modern-container">
    <div class="container-modern">
        
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">{{ __('messages.owner.sales_report.business_performance_dashboard') }}</h1>
                <p class="page-subtitle">{{ $indicatorText ?? 'Laporan Penjualan' }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('owner.user-owner.report.sales.export', request()->query()) }}" 
                   class="btn-modern btn-sm-modern btn-success-modern" >
                    <span class="material-symbols-outlined">file_download</span>
                    {{ __('messages.owner.sales_report.export_excel') }}
                </a>
            </div>
        </div>

        <div class="modern-card">
            <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                <form method="GET" action="{{ route('owner.user-owner.report.sales.index') }}">
                    <div class="row">
                        <div class="col-xl-3">
                            <div class="form-group">
                                <label for="partner_id" class="form-label-modern">
                                    {{ __('messages.owner.sales_report.all_outlets') }}
                                </label>
                                <div class="select-wrapper">
                                    <select name="partner_id" id="partner_id" class="form-control-modern">
                                        <option value="">Semua Outlet</option>
                                        @foreach($partners as $partner)
                                            <option value="{{ $partner->id }}" {{ ($filters['partner_id'] ?? '') == $partner->id ? 'selected' : '' }}>
                                                {{ $partner->name }} ({{ $partner->partner_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3">
                            <div class="form-group">
                                <label for="period-select" class="form-label-modern">
                                    Tipe Laporan
                                </label>
                                <div class="select-wrapper">
                                    <select name="period" id="period-select" class="form-control-modern" onchange="toggleFilterInputs()">
                                        <option value="daily" {{ ($filters['period'] ?? 'daily') == 'daily' ? 'selected' : '' }}>Harian</option>
                                        <option value="monthly" {{ ($filters['period'] ?? 'daily') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                        <option value="yearly" {{ ($filters['period'] ?? 'daily') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                                    </select>
                                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            
                            <!-- Filter Harian -->
                            <div id="filter-daily" class="filter-group {{ ($filters['period'] ?? 'daily') == 'daily' ? '' : 'd-none' }}">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label-modern small">Dari Tanggal</label>
                                            <input type="date" name="from" value="{{ $filters['from'] ?? date('Y-m-d') }}" class="form-control-modern">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label-modern small">Sampai Tanggal</label>
                                            <input type="date" name="to" value="{{ $filters['to'] ?? date('Y-m-d') }}" class="form-control-modern">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Bulanan -->
                            <div id="filter-monthly" class="filter-group {{ ($filters['period'] ?? 'daily') == 'monthly' ? '' : 'd-none' }}">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label-modern small">Dari Bulan</label>
                                            <input type="month" 
                                                name="month_from" 
                                                value="{{ $filters['month_from'] ?? date('Y-m') }}" 
                                                class="form-control-modern">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label-modern small">Sampai Bulan</label>
                                            <input type="month" 
                                                name="month_to" 
                                                value="{{ $filters['month_to'] ?? date('Y-m') }}" 
                                                class="form-control-modern">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Tahunan -->
                            <div id="filter-yearly" class="filter-group {{ ($filters['period'] ?? 'daily') == 'yearly' ? '' : 'd-none' }}">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label-modern small">Dari Tahun</label>
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
                                            <label class="form-label-modern small">Sampai Tahun</label>
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

                        <div class="col-xl-2 d-flex align-items-end">
                            <div class="form-group">
                                <button type="submit" class="btn-modern btn-primary-modern">
                                    Terapkan
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
                        <div class="stats-label">{{ __('messages.owner.sales_report.total_sales') }}</div>
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
                        <div class="stats-label">{{ __('messages.owner.sales_report.total_menu_sold') }}</div>
                        <div class="stats-value">{{ number_format($totalOrders ?? 0, 0, ',', '.') }} Item</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4 mb-3">
                <div class="modern-card stats-card">
                    <div class="stats-icon">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">{{ __('messages.owner.sales_report.total_booking_orders') }}</div>
                        <div class="stats-value">{{ number_format($totalBookingOrders ?? 0, 0, ',', '.') }} Order</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 1: Revenue Trend & Category Chart -->
        <div class="row">
            <div class="col-12 col-lg-8 mb-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">trending_up</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.sales_report.sales_trend_analysis') }}</h3>
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
                            <h3 class="section-title">Kategori (Jumlah Terjual)</h3>
                        </div>
                    </div>
                    <div class="card-body-modern" style="position: relative; height: 350px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Payment Method & Payment Revenue -->
        <div class="row">
            <div class="col-12 col-lg-6 mb-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">account_balance_wallet</span>
                            </div>
                            <h3 class="section-title">Metode Pembayaran</h3>
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
                            <h3 class="section-title">Pendapatan per Metode Pembayaran</h3>
                        </div>
                    </div>
                    <div class="card-body-modern" style="position: relative; height: 350px;">
                        <canvas id="paymentRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Top 5 Products Chart -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">bar_chart</span>
                            </div>
                            <h3 class="section-title">Top 5 Produk Terlaris</h3>
                        </div>
                    </div>
                    <div class="card-body-modern" style="position: relative; height: 350px;">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="row">
            <div class="col-12">
                <div class="modern-card">
                    <div class="card-header-modern d-flex justify-content-between align-items-center">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">star</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.sales_report.products_by_quantity') }}</h3>
                        </div>
                    </div>

                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 60px;">#</th>
                                    <th>Product Name</th>
                                    
                                    <!-- ✅ TAMBAHKAN onclick & data-sort di header Sold -->
                                    <th class="text-center sortable-header" 
                                        data-sort="quantity" 
                                        onclick="sortProductTable('quantity')" 
                                        style="cursor: pointer;">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <span>{{ __('messages.owner.sales_report.sold') }}</span>
                                            <span class="material-symbols-outlined sort-icon" style="font-size: 18px;">unfold_more</span>
                                        </div>
                                    </th>
                                    
                                    <!-- ✅ TAMBAHKAN onclick & data-sort di header Total Penjualan -->
                                    <th class="sortable-header" 
                                        data-sort="revenue" 
                                        onclick="sortProductTable('revenue')" 
                                        style="cursor: pointer; text-align: left !important;">
                                        <div class="d-flex align-items-center gap-1">
                                            <span>Total Penjualan</span>
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

                                        <!-- ✅ TAMBAHKAN data-quantity attribute -->
                                        <td class="text-center" data-quantity="{{ $product->total_quantity }}">
                                            <span>
                                                {{ $product->total_quantity }} 
                                            </span>
                                        </td>

                                        <!-- ✅ TAMBAHKAN data-revenue attribute -->
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
                                                <h4>{{ __('messages.owner.sales_report.no_product_data_found') }}</h4>
                                                <p>Belum ada data penjualan produk untuk periode ini.</p>
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