@extends('layouts.staff')

@section('title', 'Staff Dashboard')

@section('content')
<div class="modern-container">
    <div class="container-modern">

        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">{{ __('messages.staff.dashboard.page_title') }}</h1>
                <p class="page-subtitle">{{ __('messages.staff.dashboard.subtitle') }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-modern">
                <div class="alert-icon"><span class="material-symbols-outlined">check_circle</span></div>
                <div class="alert-content">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-modern">
                <div class="alert-icon"><span class="material-symbols-outlined">error</span></div>
                <div class="alert-content">{{ session('error') }}</div>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="row mb-4">
            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                <div class="modern-card stats-card">
                    <div class="stats-icon">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">{{ __('messages.staff.dashboard.stats_sales_today') }}</div>
                        <div class="stats-value">Rp {{ number_format($data['today_sales'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                <div class="modern-card stats-card">
                    <div class="stats-icon">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">{{ __('messages.staff.dashboard.active_employees') }}</div>
                        <div class="stats-value">{{ $data['total_employees_active'] }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                <div class="modern-card stats-card">
                    <div class="stats-icon">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">{{ __('messages.staff.dashboard.paid_orders') }}</div>
                        <div class="stats-value">{{ number_format($data['today_orders_paid']) }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                <div class="modern-card stats-card">
                    <div class="stats-icon">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">{{ __('messages.staff.dashboard.total_products') }}</div>
                        <div class="stats-value">{{ number_format($data['total_products']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row 1 --}}
        <div class="row">
            <div class="col-12 col-lg-7 mb-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">trending_up</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.staff.dashboard.sales_trend') }}</h3>
                        </div>
                    </div>
                    <div class="card-body-modern">
                        <canvas id="salesTrendChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5 mb-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">star</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.staff.dashboard.top_products') }}</h3>
                        </div>
                    </div>
                    <div class="card-body-modern">
                        <canvas id="topProductsChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row 2 --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">leaderboard</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.staff.dashboard.performance_this_month') }}</h3>
                        </div>
                        <div class="chart-filter-group">
                            <div class="select-wrapper">
                                <select id="categoryFilterType" class="form-control-modern">
                                    <option value="top">{{ __('messages.staff.dashboard.best_performing') }}</option>
                                    <option value="bottom">{{ __('messages.staff.dashboard.lowest_performing') }}</option>
                                </select>
                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-modern">
                        <canvas id="categoryPerformanceChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Popup carousel --}}
@includeWhen(isset($data['popups']) && $data['popups']->isNotEmpty(),
    'pages.employee.staff.dashboard.partials.popup-carousel',
    ['popups' => $data['popups']]
)
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const topProductsData    = @json($data['topProducts']);
    const allCategoryData    = @json($data['categoryPerformance']);
    let categoryChart        = null;

    // ===== SALES TREND =====
    const salesCtx = document.getElementById('salesTrendChart');
    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: @json($data['last7Days']),
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: @json($data['salesLast7Days']),
                    borderColor: 'rgb(174, 21, 4)',
                    backgroundColor: 'rgba(174, 21, 4, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(174, 21, 4)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        borderRadius: 8,
                        callbacks: {
                            label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            callback: value => {
                                if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'jt';
                                if (value >= 1000)    return 'Rp ' + (value/1000).toFixed(0) + 'rb';
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // ===== TOP PRODUCTS =====
    const topProductsCtx = document.getElementById('topProductsChart');
    if (topProductsCtx) {
        if (topProductsData.length === 0) {
            topProductsCtx.style.display = 'none';
            const empty = document.createElement('div');
            empty.className = 'd-flex align-items-center justify-content-center';
            empty.style.height = '300px';
            empty.innerHTML = '<p class="text-center" style="color:#999;">Belum ada data produk bulan ini.</p>';
            topProductsCtx.parentElement.appendChild(empty);
        } else {
            new Chart(topProductsCtx, {
                type: 'bar',
                data: {
                    labels: topProductsData.map(p => p.product_name),
                    datasets: [{
                        label: 'Jumlah Terjual',
                        data: topProductsData.map(p => p.total_quantity),
                        backgroundColor: '#ae1504',
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 12,
                            borderRadius: 8,
                            callbacks: {
                                label: ctx => [
                                    'Terjual: ' + topProductsData[ctx.dataIndex].total_quantity.toLocaleString('id-ID') + ' item',
                                    'Revenue: Rp ' + topProductsData[ctx.dataIndex].total_revenue.toLocaleString('id-ID')
                                ]
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                        y: { grid: { display: false } }
                    }
                }
            });
        }
    }

    // ===== CATEGORY PERFORMANCE =====
    function updateCategoryChart() {
        const categoryCtx  = document.getElementById('categoryPerformanceChart');
        if (!categoryCtx) return;

        const filterType = document.getElementById('categoryFilterType').value;
        let filtered = [...allCategoryData];

        if (allCategoryData.length > 0) {
            filtered = filtered
                .sort((a, b) => filterType === 'top'
                    ? b.total_quantity - a.total_quantity
                    : a.total_quantity - b.total_quantity)
                .slice(0, 5);
        }

        if (categoryChart) categoryChart.destroy();

        categoryChart = new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: filtered.map(c => c.category_name),
                datasets: [{
                    label: 'Total Item Terjual',
                    data: filtered.map(c => c.total_quantity),
                    backgroundColor: filtered.map(() => filterType === 'top' ? '#10b981' : '#ef4444'),
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        borderRadius: 8,
                        callbacks: {
                            label: ctx => filtered.length
                                ? 'Total Item: ' + filtered[ctx.dataIndex].total_quantity.toLocaleString('id-ID') + ' terjual'
                                : ''
                        }
                    }
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    updateCategoryChart();
    document.getElementById('categoryFilterType').addEventListener('change', updateCategoryChart);
});
</script>
@endpush