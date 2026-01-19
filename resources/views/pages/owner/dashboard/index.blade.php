@extends('layouts.owner')

@section('title', __('messages.owner.dashboard.title'))

@section('page_title', __('messages.owner.dashboard.page_title'))

@section('content')
<div class="modern-container">
    <div class="container-modern">
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">{{ __('messages.owner.dashboard.page_title') }}</h1>
                <p class="page-subtitle">{{ __('messages.owner.dashboard.subtitle') }}</p>
            </div>
        </div>

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

        <div class="row mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="modern-card stats-card">
                    <div class="stats-icon">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">{{ __('messages.owner.dashboard.stats_sales_today') }}</div>
                        <div class="stats-value">Rp {{ number_format($data['today_sales'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="modern-card stats-card">
                    <div class="stats-icon">
                        <span class="material-symbols-outlined">store</span>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">{{ __('messages.owner.dashboard.stats_active_outlets') }}</div>
                        <div class="stats-value">{{ $data['total_outlets_active'] }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="modern-card stats-card">
                    <div class="stats-icon">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">{{ __('messages.owner.dashboard.stats_paid_orders') }}</div>
                        <div class="stats-value">{{ number_format($data['today_orders_paid']) }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="modern-card stats-card stats-warning">
                    <div class="stats-icon">
                        <span class="material-symbols-outlined">account_balance_wallet</span>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">{{ __('messages.owner.dashboard.stats_xendit_balance') }}</div>
                        <div class="stats-value">Rp {{ number_format($data['xendit_balance'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-7 mb-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="section-header mb-0">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">trending_up</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.dashboard.trend_sales_title') }}</h3>
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
                            <h3 class="section-title">{{ __('messages.owner.dashboard.top_products_title') }}</h3>
                        </div>
                    </div>
                    <div class="card-body-modern">
                        <canvas id="topProductsChart" style="height: 300px;"></canvas>
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
                                <span class="material-symbols-outlined">leaderboard</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.dashboard.outlet_performance_title') }}</h3>
                        </div>
                        <div class="chart-filter-group">
                            <div class="select-wrapper">
                                <select id="outletFilterType" class="form-control-modern">
                                    <option value="top">{{ __('messages.owner.dashboard.filter_best') }}</option>
                                    <option value="bottom">{{ __('messages.owner.dashboard.filter_worst') }}</option>
                                </select>
                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-modern">
                        <canvas id="outletPerformanceChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Popup carousel kalau ada popup messages --}}
@includeWhen(isset($data['popups']) && $data['popups']->isNotEmpty(), 'pages.owner.dashboard.partials.popup-carousel', [
    'popups' => $data['popups'],
])

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // All outlet performance data from backend
        const allOutletData = @json($data['outletPerformance']);
        const topProductsData = @json($data['topProducts']);
        
        let outletChart = null;

        // Sales Trend Chart (Line Chart)
        const salesCtx = document.getElementById('salesTrendChart');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: @json($data['last7Days']),
                    datasets: [{
                        label: '{{ __('messages.owner.dashboard.chart_sales_label') }}',
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

        // Top 5 Products Chart (Horizontal Bar)
        const topProductsCtx = document.getElementById('topProductsChart');
        if (!topProductsCtx) {
            console.warn('Canvas topProductsChart tidak ditemukan');
        } else if (topProductsData.length === 0) {
            // Empty state - sembunyikan canvas dan tampilkan pesan
            topProductsCtx.style.display = 'none';
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'd-flex align-items-center justify-content-center';
            emptyDiv.style.height = '300px';
            emptyDiv.innerHTML = '<p class="text-center" style="color: #999;">Tidak ada data produk untuk bulan ini.</p>';
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
                        label: '{{ __('messages.owner.dashboard.chart_quantity_label') }}',
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
                                        '{{ __('messages.owner.dashboard.chart_tooltip_sold') }}: ' + quantity.toLocaleString('id-ID') + ' {{ __('messages.owner.dashboard.chart_item') }}',
                                        '{{ __('messages.owner.dashboard.chart_tooltip_revenue') }}: Rp ' + revenue.toLocaleString('id-ID')
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

        // Function to update outlet chart
// Function to update outlet chart
        function updateOutletChart() {
            const filterType = document.getElementById('outletFilterType').value;
            const filterCount = 5; // Fixed at 5 outlets

            let filteredData = [...allOutletData];
            
            // LOGIKA SORTING DIUBAH DI SINI
            if (filterType === 'top') {
                // Filter Terbaik: Urutkan dari Besar ke Kecil
                // Index 0 (Paling Atas) = Penjualan Terbesar
                filteredData = filteredData
                    .sort((a, b) => b.total_sales - a.total_sales)
                    .slice(0, filterCount);
            } else {
                // Filter Terburuk: Urutkan dari Kecil ke Besar
                // Index 0 (Paling Atas) = Penjualan Terkecil
                filteredData = filteredData
                    .sort((a, b) => a.total_sales - b.total_sales)
                    .slice(0, filterCount);
                // Kita TIDAK menggunakan .reverse() disini agar yang paling kecil tetap di index 0 (paling atas)
            }

            const labels = filteredData.map(d => d.partner_name);
            const data = filteredData.map(d => d.total_sales);
            const colors = filteredData.map(() => 
                filterType === 'top' ? '#10b981' : '#ef4444'
            );

            // Destroy existing chart
            if (outletChart) {
                outletChart.destroy();
            }

            // Create new chart
            const outletCtx = document.getElementById('outletPerformanceChart');
            if (outletCtx) {
                outletChart = new Chart(outletCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '{{ __('messages.owner.dashboard.chart_sales_label') }}',
                            data: data,
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
                                        return 'Rp ' + context.parsed.x.toLocaleString('id-ID');
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
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                        } else if (value >= 1000) {
                                            return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                        }
                                        return 'Rp ' + value.toLocaleString('id-ID');
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
        }

        // Initial chart render
        updateOutletChart();

        // Add event listener to filter
        document.getElementById('outletFilterType').addEventListener('change', updateOutletChart);
    });
</script>
@endpush