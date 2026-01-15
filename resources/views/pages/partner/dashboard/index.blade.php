@extends('layouts.partner')

@section('title', 'Partner Dashboard')

@section('page_title', 'Dashboard Partner')

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <!-- Header Section -->
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">Dashboard Partner</h1>
                    <p class="page-subtitle">Overview penjualan dan performa outlet hari ini</p>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">check_circle</span>
                    </div>
                    <div class="alert-content">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                    </div>
                    <div class="alert-content">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Stats Cards - Hari Ini -->
            <div class="row mb-4">
                <!-- Total Penjualan Hari Ini -->
                <div class="col-12 col-sm-6 col-lg-3 mb-3">
                    <div class="modern-card stats-card">
                        <div class="stats-icon">
                            <span class="material-symbols-outlined">payments</span>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">{{ __('messages.partner.dashboard.today_sales') }}</div>
                            <div class="stats-value">Rp {{ number_format($data['today_sales'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Total Employee Aktif -->
                <div class="col-12 col-sm-6 col-lg-3 mb-3">
                    <div class="modern-card stats-card">
                        <div class="stats-icon">
                            <span class="material-symbols-outlined">group</span>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">{{ __('messages.partner.dashboard.active_employees') }}</div>
                            <div class="stats-value">{{ $data['total_employees_active'] }}</div>
                        </div>
                    </div>
                </div>

                <!-- Pesanan Hari Ini (PAID) -->
                <div class="col-12 col-sm-6 col-lg-3 mb-3">
                    <div class="modern-card stats-card">
                        <div class="stats-icon">
                            <span class="material-symbols-outlined">receipt_long</span>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">{{ __('messages.partner.dashboard.paid_orders') }}</div>
                            <div class="stats-value">{{ number_format($data['today_orders_paid']) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Total Produk -->
                <div class="col-12 col-sm-6 col-lg-3 mb-3">
                    <div class="modern-card stats-card">
                        <div class="stats-icon">
                            <span class="material-symbols-outlined">inventory_2</span>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">{{ __('messages.partner.dashboard.total_products') }}</div>
                            <div class="stats-value">{{ number_format($data['total_products']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 1: Sales Trend & Top Products -->
            <div class="row">
                <!-- Sales Trend Chart -->
                <div class="col-12 col-lg-7 mb-4">
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <div class="section-header mb-0">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">trending_up</span>
                                </div>
                                <h3 class="section-title">{{ __('messages.partner.dashboard.sales_trend') }}</h3>
                            </div>
                        </div>
                        <div class="card-body-modern">
                            <canvas id="salesTrendChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top 5 Products -->
                <div class="col-12 col-lg-5 mb-4">
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <div class="section-header mb-0">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">star</span>
                                </div>
                                <h3 class="section-title">{{ __('messages.partner.dashboard.top_products') }}</h3>
                            </div>
                        </div>
                        <div class="card-body-modern">
                            <canvas id="topProductsChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Category Performance -->
            <div class="row">
                <!-- Category Performance Chart -->
                <div class="col-12 mb-4">
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <div class="section-header mb-0">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">leaderboard</span>
                                </div>
                                <h3 class="section-title">{{ __('messages.partner.dashboard.performance_this_month') }}
                                </h3>
                            </div>
                            <div class="chart-filter-group">
                                <div class="select-wrapper">
                                    <select id="categoryFilterType" class="form-control-modern">
                                        <option value="top">{{ __('messages.partner.dashboard.best_performing') }}
                                        </option>
                                        <option value="bottom">{{ __('messages.partner.dashboard.lowest_performing') }}
                                        </option>
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

    {{-- Popup carousel kalau ada popup messages --}}
    @includeWhen(isset($data['popups']) && $data['popups']->isNotEmpty(),
        'pages.partner.dashboard.partials.popup-carousel',
        [
            'popups' => $data['popups'],
        ]
    )

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const topProductsData = @json($data['topProducts']);
            const allCategoryData = @json($data['categoryPerformance']);

            let categoryChart = null;

            // ==================== SALES TREND CHART ====================
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
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                borderRadius: 8,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                        } else if (value >= 1000) {
                                            return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                        }
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // ==================== TOP PRODUCTS CHART ====================
            const topProductsCtx = document.getElementById('topProductsChart');
            if (!topProductsCtx) {
                console.warn('Canvas topProductsChart tidak ditemukan');
            } else if (topProductsData.length === 0) {
                // Empty state - sembunyikan canvas dan tampilkan pesan
                topProductsCtx.style.display = 'none';
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'd-flex align-items-center justify-content-center';
                emptyDiv.style.height = '300px';
                emptyDiv.innerHTML =
                    '<p class="text-center" style="color: #999;">Belum ada data produk bulan ini.</p>';
                topProductsCtx.parentElement.appendChild(emptyDiv);
            } else {
                topProductsCtx.style.display = 'block';
                const productLabels = topProductsData.map(p => p.product_name);
                const productQuantities = topProductsData.map(p => p.total_quantity);

                new Chart(topProductsCtx, {
                    type: 'bar',
                    data: {
                        labels: productLabels,
                        datasets: [{
                            label: 'Jumlah Terjual',
                            data: productQuantities,
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
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                borderRadius: 8,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        const index = context.dataIndex;
                                        const quantity = topProductsData[index].total_quantity;
                                        const revenue = topProductsData[index].total_revenue;
                                        return [
                                            'Terjual: ' + quantity.toLocaleString('id-ID') +
                                            ' item',
                                            'Revenue: Rp ' + revenue.toLocaleString('id-ID')
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // ==================== CATEGORY PERFORMANCE CHART ====================
            function updateCategoryChart() {
                const categoryCtx = document.getElementById('categoryPerformanceChart');

                if (!categoryCtx) {
                    console.warn('Canvas categoryPerformanceChart tidak ditemukan');
                    return;
                }

                const filterType = document.getElementById('categoryFilterType').value;
                const filterCount = 5; // Fixed at 5 categories

                let filteredData = [...allCategoryData];
                let categoryLabels = [];
                let categoryQuantities = [];
                let colors = [];

                // Handle empty data
                if (allCategoryData.length === 0) {
                    categoryLabels = [];
                    categoryQuantities = [];
                    colors = [];
                } else {
                    // Sort and filter based on type
                    if (filterType === 'top') {
                        // Top performers: terbanyak di atas
                        filteredData = filteredData
                            .sort((a, b) => b.total_quantity - a.total_quantity)
                            .slice(0, filterCount);
                    } else {
                        // Lowest performers: tersedikit di atas
                        filteredData = filteredData
                            .sort((a, b) => a.total_quantity - b.total_quantity)
                            .slice(0, filterCount);
                    }

                    categoryLabels = filteredData.map(c => c.category_name);
                    categoryQuantities = filteredData.map(c => c.total_quantity);
                    colors = filteredData.map(() =>
                        filterType === 'top' ? '#10b981' : '#ef4444'
                    );
                }

                // Destroy existing chart
                if (categoryChart) {
                    categoryChart.destroy();
                }

                // Create new chart
                categoryChart = new Chart(categoryCtx, {
                    type: 'bar',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            label: 'Total Item Terjual',
                            data: categoryQuantities,
                            backgroundColor: colors,
                            borderRadius: 6,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                borderRadius: 8,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        if (filteredData.length === 0) return '';
                                        const index = context.dataIndex;
                                        const quantity = filteredData[index].total_quantity;
                                        return 'Total Item: ' + quantity.toLocaleString('id-ID') +
                                            ' terjual';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    callback: function(value) {
                                        return value.toLocaleString('id-ID');
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Initial chart render
            updateCategoryChart();

            // Add event listener to filter
            document.getElementById('categoryFilterType').addEventListener('change', updateCategoryChart);
        });
    </script>
@endpush
