@extends('pages.admin.layouts.app')

@section('content-header')
    <div class="content-header-left col-12 mb-2 mt-1">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h5 class="content-header-title float-left pr-1 mb-0">Owner Management</h5>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb p-0 mb-0">
                        <li class="breadcrumb-item"><a href="#"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.owner-list.index') }}">Owner List</a>
                        <li class="breadcrumb-item"><a href="{{ route('admin.owner-list.outlets', $owner->id) }}">Owner
                                Outlet</a></li>
                        <li class="breadcrumb-item active">Outlet Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @php
        $activeTab = request()->get('tab', 'products');
    @endphp

    <!-- Outlet Info Header -->
    <section id="outlet-header">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h2 class="text-dark font-weight-bold mb-1">{{ $outlet->name }}</h2>
                                <div class="text-dark-50">
                                    <div class="d-flex align-items-center mb-50">
                                        <i class="bx bx-envelope"></i>
                                        <span class="ml-50">{{ $outlet->email }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-50">
                                        <i class="bx bx-map"></i>
                                        <span class="ml-50">{{ $outlet->city ?? 'N/A' }},
                                            {{ $outlet->province ?? 'N/A' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-50">
                                        <i class="bx bx-user"></i>
                                        <span class="ml-50">Owner by {{ $outlet->owner->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                @if ($outlet->is_active)
                                    <span class="badge badge-success badge-pill">Active Outlet</span>
                                @else
                                    <span class="badge badge-danger badge-pill">Inactive Outlet</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Widgets Statistics for Products -->
    <section id="widgets-products-statistics" class="widget-section"
        style="display: {{ $activeTab == 'products' ? 'block' : 'none' }};">
        <div class="row">
            <div class="col-xl-3 col-md-3 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-info mx-auto my-1">
                                <i class="bx bx-package font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Total Products</p>
                            <h2 class="mb-0">{{ number_format($totalProducts ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-3 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto my-1">
                                <i class="bx bx-check-circle font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Active Products</p>
                            <h2 class="mb-0">{{ number_format($activeProducts ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-3 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-danger mx-auto my-1">
                                <i class="bx bx-error-alt font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Out of Stock</p>
                            <h2 class="mb-0">{{ number_format($outOfStockProducts ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-3 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto my-1">
                                <i class="bx bx-grid-alt font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Categories</p>
                            <h2 class="mb-0">{{ number_format($totalCategories ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Widgets Statistics for Employees -->
    <section id="widgets-employees-statistics" class="widget-section"
        style="display: {{ $activeTab == 'employees' ? 'block' : 'none' }};">
        <div class="row">
            <div class="col-xl-4 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-info mx-auto my-1">
                                <i class="bx bx-user font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Total Employees</p>
                            <h2 class="mb-0">{{ number_format($totalEmployees ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto my-1">
                                <i class="bx bx-check-circle font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Active Employees</p>
                            <h2 class="mb-0">{{ number_format($activeEmployees ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-danger mx-auto my-1">
                                <i class="bx bx-x-circle font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Inactive Employees</p>
                            <h2 class="mb-0">{{ number_format($inactiveEmployees ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Widgets Statistics for Booking Orders -->
    <section id="widgets-orders-statistics" class="widget-section"
        style="display: {{ $activeTab == 'orders' ? 'block' : 'none' }};">
        <div class="row">
            <div class="col-xl-4 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-info mx-auto my-1">
                                <i class="bx bx-receipt font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Total Orders</p>
                            <h2 class="mb-0">{{ number_format($totalOrders ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto my-1">
                                <i class="bx bx-check-circle font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Completed Orders</p>
                            <h2 class="mb-0">{{ number_format($completedOrders ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-warning mx-auto my-1">
                                <i class="bx bx-time-five font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Pending Orders</p>
                            <h2 class="mb-0">{{ number_format($pendingOrders ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tabs Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab == 'products' ? 'active' : '' }}" id="products-tab"
                                    data-toggle="tab" href="#products-content" aria-controls="products" role="tab"
                                    aria-selected="{{ $activeTab == 'products' ? 'true' : 'false' }}">
                                    <span class="align-middle">Products</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab == 'employees' ? 'active' : '' }}" id="employees-tab"
                                    data-toggle="tab" href="#employees-content" aria-controls="employees" role="tab"
                                    aria-selected="{{ $activeTab == 'employees' ? 'true' : 'false' }}">
                                    <span class="align-middle">Employees</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab == 'orders' ? 'active' : '' }}" id="orders-tab"
                                    data-toggle="tab" href="#orders-content" aria-controls="orders" role="tab"
                                    aria-selected="{{ $activeTab == 'orders' ? 'true' : 'false' }}">
                                    <span class="align-middle">Booking Orders</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Products Tab -->
                            <div class="tab-pane {{ $activeTab == 'products' ? 'active' : '' }}" id="products-content"
                                aria-labelledby="products-tab" role="tabpanel">
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h4 class="mb-0">Product List</h4>
                                        <div class="d-flex align-items-center">
                                            <label class="text-muted mb-0 mr-1">Search:</label>
                                            <input type="search" id="productSearch" class="form-control">
                                        </div>
                                    </div>
                                    <p class="text-muted">All products from {{ $outlet->name }}.</p>

                                    <div class="table-responsive">
                                        <table class="table mb-0" id="productsTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>NO</th>
                                                    <th>PRODUCT (SKU)</th>
                                                    <th>CATEGORY</th>
                                                    <th>PRICE</th>
                                                    <th>STOCK</th>
                                                    <th>STATUS</th>
                                                    <th>DETAIL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($products as $index => $product)
                                                    @include('pages.admin.owner-management.partials.product-row')
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">
                                                            No products found for this outlet
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div id="productsPaginationContainer">
                                        @if ($products->total() > 0)
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <div class="pagination-summary text-muted">
                                                    Showing {{ $products->firstItem() }} - {{ $products->lastItem() }}
                                                    from {{ $products->total() }} products
                                                </div>

                                                <div class="pagination-links">
                                                    {{ $products->appends(request()->query())->links('vendor.pagination.custom-limited') }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Employees Tab -->
                            <div class="tab-pane {{ $activeTab == 'employees' ? 'active' : '' }}" id="employees-content"
                                aria-labelledby="employees-tab" role="tabpanel">
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h4 class="mb-0">Employee List</h4>
                                        <div class="d-flex align-items-center">
                                            <label class="text-muted mb-0 mr-1">Search:</label>
                                            <input type="search" id="employeeSearch" class="form-control">
                                        </div>
                                    </div>
                                    <p class="text-muted">All employees from {{ $outlet->name }}.</p>

                                    <div class="table-responsive">
                                        <table class="table mb-0" id="employeesTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>NO</th>
                                                    <th>NAME</th>
                                                    <th>USERNAME</th>
                                                    <th>EMAIL</th>
                                                    <th>ROLE</th>
                                                    <th>STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($employees as $index => $employee)
                                                    @include('pages.admin.owner-management.partials.employee-row')
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">
                                                            No employees found for this outlet
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div id="employeesPaginationContainer">
                                        @if ($employees->total() > 0)
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <div class="pagination-summary text-muted">
                                                    Showing {{ $employees->firstItem() }} - {{ $employees->lastItem() }}
                                                    from {{ $employees->total() }} employees
                                                </div>

                                                <div class="pagination-links">
                                                    {{ $employees->appends(request()->query())->links('vendor.pagination.custom-limited') }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Booking Orders Tab -->
                            <div class="tab-pane {{ $activeTab == 'orders' ? 'active' : '' }}" id="orders-content"
                                aria-labelledby="orders-tab" role="tabpanel">
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h4 class="mb-0">Booking Orders List</h4>
                                        <div class="d-flex align-items-center">
                                            <label class="text-muted mb-0 mr-1">Search:</label>
                                            <input type="search" id="orderSearch" class="form-control">
                                        </div>
                                    </div>
                                    <p class="text-muted">All booking orders from {{ $outlet->name }}.</p>

                                    <div class="table-responsive">
                                        <table class="table mb-0" id="ordersTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>NO</th>
                                                    <th>ORDER CODE</th>
                                                    <th>CUSTOMER NAME</th>
                                                    <th>PAYMENT METHOD</th>
                                                    <th>TOTAL</th>
                                                    <th>ORDER DATE</th>
                                                    <th>DETAIL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($bookingOrders as $index => $order)
                                                    @include('pages.admin.owner-management.partials.booking-order-row')
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">
                                                            No booking orders found for this outlet
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div id="ordersPaginationContainer">
                                        @if ($bookingOrders->total() > 0)
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <div class="pagination-summary text-muted">
                                                    Showing {{ $bookingOrders->firstItem() }} -
                                                    {{ $bookingOrders->lastItem() }} from {{ $bookingOrders->total() }}
                                                    orders
                                                </div>

                                                <div class="pagination-links">
                                                    {{ $bookingOrders->appends(request()->query())->links('vendor.pagination.custom-limited') }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Render Product Modals for First Page Load --}}
    @if ($products->count() > 0)
        @foreach ($products as $product)
            @include('pages.admin.owner-management.partials.product-modal', ['product' => $product])
        @endforeach
    @endif

    {{-- Render Order Modals for First Page Load --}}
    @if ($bookingOrders->count() > 0)
        @foreach ($bookingOrders as $order)
            @include('pages.admin.owner-management.partials.booking-order-modal', ['order' => $order])
        @endforeach
    @endif
@endsection

@push('styles')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #productsTable tbody,
        #employeesTable tbody,
        #ordersTable tbody {
            transition: opacity 0.2s ease-in-out;
        }

        .table tbody tr {
            transition: background-color 0.15s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .nav-tabs .nav-link {
            cursor: pointer;
        }

        .widget-section {
            transition: opacity 0.3s ease-in-out;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            const ownerId = {{ $outlet->owner_id }};
            const outletId = {{ $outlet->id }};
            const baseUrl =
                "{{ route('admin.owner-list.outlet-data', ['ownerId' => $outlet->owner_id, 'outletId' => $outlet->id]) }}";

            // Function to toggle widgets based on active tab
            function toggleWidgets(tabName) {
                $('.widget-section').hide();

                if (tabName === 'products') {
                    $('#widgets-products-statistics').show();
                } else if (tabName === 'employees') {
                    $('#widgets-employees-statistics').show();
                } else if (tabName === 'orders') {
                    $('#widgets-orders-statistics').show();
                }
            }

            // Handle tab changes and update URL
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                let activeTab = $(e.target).attr('href').replace('-content', '').substring(1);
                let url = new URL(window.location.href);
                url.searchParams.set('tab', activeTab);
                window.history.pushState({}, '', url);

                // Toggle widgets based on active tab
                toggleWidgets(activeTab);
            });

            // PRODUCTS TABLE SEARCH
            let productSearchTimeout;
            let productCurrentRequest = null;

            function loadProducts(searchValue = '', page = 1) {
                if (productCurrentRequest) {
                    productCurrentRequest.abort();
                }

                productCurrentRequest = $.ajax({
                    url: baseUrl,
                    type: 'GET',
                    data: {
                        search_products: searchValue,
                        products_page: page,
                        table: 'products',
                        ajax: 1
                    },
                    beforeSend: function() {
                        $('.search-loading').addClass('active');
                        $('#tableContainer').addClass('loading');
                    },
                    success: function(response) {
                        $('#productsTable tbody').css('opacity', '0');

                        setTimeout(function() {
                            $('[id^="productDetailModal"]').remove();
                            $('#productsTable tbody').html(response.table);

                            if (response.modals) {
                                $('body').append(response.modals);
                            }

                            if (response.pagination) {
                                $('#productsPaginationContainer').html(response.pagination);
                            } else {
                                $('#productsPaginationContainer').html('');
                            }

                            $('#productsTable tbody').css('opacity', '1');

                            let url = new URL(window.location.href);
                            if (searchValue) {
                                url.searchParams.set('search_products', searchValue);
                            } else {
                                url.searchParams.delete('search_products');
                            }
                            if (page > 1) {
                                url.searchParams.set('products_page', page);
                            } else {
                                url.searchParams.delete('products_page');
                            }
                            window.history.pushState({}, '', url);
                        }, 150);
                    },
                    error: function(xhr) {
                        if (xhr.statusText !== 'abort') {
                            $('#productsTable tbody').html(
                                '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>'
                            );
                        }
                    },
                    complete: function() {
                        $('#productsTable tbody').css('opacity', '1');
                        productCurrentRequest = null;
                    }
                });
            }

            $('#productSearch').on('keyup', function() {
                clearTimeout(productSearchTimeout);
                let searchValue = $(this).val();

                productSearchTimeout = setTimeout(function() {
                    loadProducts(searchValue, 1);
                }, 600);
            });

            $(document).on('click', '#productsPaginationContainer .pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let page = new URL(url).searchParams.get('products_page') || 1;
                let searchValue = $('#productSearch').val();

                loadProducts(searchValue, page);
            });

            // EMPLOYEES TABLE SEARCH
            let employeeSearchTimeout;
            let employeeCurrentRequest = null;

            function loadEmployees(searchValue = '', page = 1) {
                if (employeeCurrentRequest) {
                    employeeCurrentRequest.abort();
                }

                employeeCurrentRequest = $.ajax({
                    url: baseUrl,
                    type: 'GET',
                    data: {
                        search_employees: searchValue,
                        employees_page: page,
                        table: 'employees',
                        ajax: 1
                    },
                    beforeSend: function() {
                        $('.search-loading').addClass('active');
                        $('#tableContainer').addClass('loading');
                    },
                    success: function(response) {
                        $('#employeesTable tbody').css('opacity', '0');

                        setTimeout(function() {
                            $('#employeesTable tbody').html(response.table);

                            if (response.pagination) {
                                $('#employeesPaginationContainer').html(response.pagination);
                            } else {
                                $('#employeesPaginationContainer').html('');
                            }

                            $('#employeesTable tbody').css('opacity', '1');

                            let url = new URL(window.location.href);
                            if (searchValue) {
                                url.searchParams.set('search_employees', searchValue);
                            } else {
                                url.searchParams.delete('search_employees');
                            }
                            if (page > 1) {
                                url.searchParams.set('employees_page', page);
                            } else {
                                url.searchParams.delete('employees_page');
                            }
                            window.history.pushState({}, '', url);
                        }, 150);
                    },
                    error: function(xhr) {
                        if (xhr.statusText !== 'abort') {
                            $('#employeesTable tbody').html(
                                '<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>'
                            );
                        }
                    },
                    complete: function() {
                        $('#employeesTable tbody').css('opacity', '1');
                        employeeCurrentRequest = null;
                    }
                });
            }

            $('#employeeSearch').on('keyup', function() {
                clearTimeout(employeeSearchTimeout);
                let searchValue = $(this).val();

                employeeSearchTimeout = setTimeout(function() {
                    loadEmployees(searchValue, 1);
                }, 600);
            });

            $(document).on('click', '#employeesPaginationContainer .pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let page = new URL(url).searchParams.get('employees_page') || 1;
                let searchValue = $('#employeeSearch').val();

                loadEmployees(searchValue, page);
            });

            // BOOKING ORDERS TABLE SEARCH
            let orderSearchTimeout;
            let orderCurrentRequest = null;

            function loadOrders(searchValue = '', page = 1) {
                if (orderCurrentRequest) {
                    orderCurrentRequest.abort();
                }

                orderCurrentRequest = $.ajax({
                    url: baseUrl,
                    type: 'GET',
                    data: {
                        search_orders: searchValue,
                        orders_page: page,
                        table: 'orders',
                        ajax: 1
                    },
                    beforeSend: function() {
                        $('.search-loading').addClass('active');
                        $('#tableContainer').addClass('loading');
                    },
                    success: function(response) {
                        $('#ordersTable tbody').css('opacity', '0');

                        setTimeout(function() {
                            $('[id^="orderDetailModal"]').remove();
                            $('#ordersTable tbody').html(response.table);

                            if (response.modals) {
                                $('body').append(response.modals);
                            }

                            if (response.pagination) {
                                $('#ordersPaginationContainer').html(response.pagination);
                            } else {
                                $('#ordersPaginationContainer').html('');
                            }

                            $('#ordersTable tbody').css('opacity', '1');

                            let url = new URL(window.location.href);
                            if (searchValue) {
                                url.searchParams.set('search_orders', searchValue);
                            } else {
                                url.searchParams.delete('search_orders');
                            }
                            if (page > 1) {
                                url.searchParams.set('orders_page', page);
                            } else {
                                url.searchParams.delete('orders_page');
                            }
                            window.history.pushState({}, '', url);
                        }, 150);
                    },
                    error: function(xhr) {
                        if (xhr.statusText !== 'abort') {
                            $('#ordersTable tbody').html(
                                '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>'
                            );
                        }
                    },
                    complete: function() {
                        $('#ordersTable tbody').css('opacity', '1');
                        orderCurrentRequest = null;
                    }
                });
            }

            $('#orderSearch').on('keyup', function() {
                clearTimeout(orderSearchTimeout);
                let searchValue = $(this).val();

                orderSearchTimeout = setTimeout(function() {
                    loadOrders(searchValue, 1);
                }, 600);
            });

            $(document).on('click', '#ordersPaginationContainer .pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let page = new URL(url).searchParams.get('orders_page') || 1;
                let searchValue = $('#orderSearch').val();

                loadOrders(searchValue, page);
            });

            // SET SEARCH VALUES FROM URL ON PAGE LOAD
            let urlParams = new URLSearchParams(window.location.search);
            let searchProductsParam = urlParams.get('search_products');
            if (searchProductsParam) {
                $('#productSearch').val(searchProductsParam);
            }

            let searchEmployeesParam = urlParams.get('search_employees');
            if (searchEmployeesParam) {
                $('#employeeSearch').val(searchEmployeesParam);
            }

            let searchOrdersParam = urlParams.get('search_orders');
            if (searchOrdersParam) {
                $('#orderSearch').val(searchOrdersParam);
            }
        });
    </script>
@endpush
