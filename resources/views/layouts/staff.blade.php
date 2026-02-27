<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Management Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.4/dist/select2-bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">

    <link rel="stylesheet" href="{{ asset('css/message.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/add-product-options.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owner.css') }}">

    @stack('styles')
</head>

@php
    // Dapatkan role employee (manager atau supervisor) yang sedang login dalam format huruf kecil
    $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager');
@endphp

<body>
    <div class="wrapper">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <aside class="main-sidebar" id="mainSidebar">
            <a href="{{ route("employee.{$empRole}.dashboard") }}" class="brand-link">
                <img src="{{ asset('images/cava-logo2-gradient.png') }}" alt="Cavaa Logo"
                    class="brand-image brand-image-full">
                <img src="{{ asset('icons/cava-logo-red-gradient.png') }}" alt="Cavaa Logo"
                    class="brand-image brand-image-collapsed">
            </a>

            <nav class="sidebar-nav">
                @php
                    // Dapatkan employee yang sedang login
                    $employee = auth('employee')->user();
                    $isActive = $employee && $employee->is_active;
                    $empRole = strtolower($employee->role ?? 'manager');

                    // Helper untuk mengecek grup akses
                    $hasAnyProductAccess = $employee->hasMenuAccess('products') ||
                        $employee->hasMenuAccess('stocks') ||
                        $employee->hasMenuAccess('categories') ||
                        $employee->hasMenuAccess('promotions');

                    $hasAnyReportAccess = $employee->hasMenuAccess('report_sales') ||
                        $employee->hasMenuAccess('report_stocks');
                @endphp

                {{-- DASHBOARD  --}}
                <div class="nav-section-title">{{ __('messages.owner.layout.dashboard') ?? 'Dashboard' }}</div>
                <div class="nav-item">
                    <a href="{{ $isActive ? route("employee.{$empRole}.dashboard") : 'javascript:void(0)' }}"
                        class="nav-link {{ !$isActive ? 'disabled-link' : '' }} @if (Route::is("employee.{$empRole}.dashboard")) active @endif">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span>{{ __('messages.owner.layout.dashboard') ?? 'Dashboard' }}</span>
                    </a>
                </div>

                {{-- USER MANAGEMENT (EMPLOYEES) --}}
                @if($employee->hasMenuAccess('employees'))
                    <div class="nav-section-title">{{ __('messages.owner.layout.user_management') ?? 'User Management' }}
                    </div>

                    @php $employeeRoutes = ["employee.{$empRole}.employees.*"]; @endphp
                    <div class="nav-item {{ Route::is($employeeRoutes) ? 'menu-open' : '' }}">
                        <a href="javascript:void(0)"
                            class="nav-link {{ !$isActive ? 'disabled-link' : '' }} {{ Route::is($employeeRoutes) ? 'active' : '' }}"
                            onclick="{{ !$isActive ? '' : 'toggleSubmenu(this)' }}">
                            <span class="material-symbols-outlined">group</span>
                            <span>{{ __('messages.owner.layout.employees') ?? 'Employees' }}</span>
                            <span class="material-symbols-outlined expand-icon">expand_more</span>
                        </a>
                        <div class="nav-treeview {{ !$isActive ? 'disabled' : '' }}">
                            <div class="nav-item">
                                <a href="{{ $isActive ? route('employee.' . $empRole . '.employees.index') : 'javascript:void(0)' }}"
                                    class="nav-link {{ Route::is("employee.{$empRole}.employees.*") ? 'active' : '' }}">
                                    <span>{{ __('messages.owner.layout.employees') ?? 'All Employees' }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- PRODUCTS GROUP --}}
                @if($hasAnyProductAccess)
                    @php
                        $allProductRoutes = [
                            "employee.{$empRole}.products.*",
                            "employee.{$empRole}.categories.*",
                            "employee.{$empRole}.promotions.*",
                            "employee.{$empRole}.stocks.*",
                        ];
                    @endphp

                    <div class="nav-item {{ Route::is($allProductRoutes) ? 'menu-open' : '' }}">
                        <a href="javascript:void(0)"
                            class="nav-link {{ !$isActive ? 'disabled-link' : '' }} {{ Route::is($allProductRoutes) ? 'active' : '' }}"
                            onclick="{{ !$isActive ? '' : 'toggleSubmenu(this)' }}">
                            <span class="material-symbols-outlined">inventory_2</span>
                            <span>{{ __('messages.owner.layout.products') ?? 'Products' }}</span>
                            <span class="material-symbols-outlined expand-icon">expand_more</span>
                        </a>
                        <div class="nav-treeview {{ !$isActive ? 'disabled' : '' }}">

                            @if($employee->hasMenuAccess('products'))
                                <div class="nav-item">
                                    <a href="{{ route("employee.{$empRole}.products.index")}}"
                                        class="nav-link {{ Route::is("employee.{$empRole}.products.*") ? 'active' : '' }}">
                                        <span>Products</span>
                                    </a>
                                </div>
                            @endif

                            @if($employee->hasMenuAccess('stocks'))
                                <div class="nav-item">
                                    <a href="{{ route("employee.{$empRole}.stocks.index") }}"
                                        class="nav-link {{ Route::is("employee.{$empRole}.stocks.*") ? 'active' : '' }}">
                                        <span>{{ __('messages.owner.layout.stocks') ?? 'Stocks' }}</span>
                                    </a>
                                </div>
                            @endif

                            @if($employee->hasMenuAccess('categories'))
                                <div class="nav-item">
                                    <a href="{{route("employee.{$empRole}.categories.index")}}"
                                        class="nav-link {{ Route::is("employee.{$empRole}.categories.*") ? 'active' : '' }}">
                                        <span>{{ __('messages.owner.layout.categories') ?? 'Categories' }}</span>
                                    </a>
                                </div>
                            @endif

                            @if($employee->hasMenuAccess('promotions'))
                                <div class="nav-item">
                                    <a href="{{route("employee.{$empRole}.promotions.index")}}"
                                        class="nav-link {{ Route::is("employee.{$empRole}.promotions.*") ? 'active' : '' }}">
                                        <span>{{ __('messages.owner.layout.promotions') ?? 'Promotions' }}</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- SETTINGS --}}
                @if($employee->hasMenuAccess('settings'))
                    @php $settingRoutes = ["employee.{$empRole}.settings.*"]; @endphp
                    <div class="nav-item">
                        <a href="{{ route('employee.' . $empRole . '.settings.index') }}"
                            class="nav-link {{ !$isActive ? 'disabled-link' : '' }} {{ Route::is($settingRoutes) ? 'active' : '' }}">
                            <span class="material-symbols-outlined">settings</span>
                            <span>{{ __('messages.owner.layout.settings') ?? 'Settings' }}</span>
                        </a>
                    </div>
                @endif

                {{-- REPORTS GROUP --}}
                @if($hasAnyReportAccess)
                    <div class="nav-section-title">{{ __('messages.owner.layout.reports') ?? 'Reports' }}</div>

                    @if($employee->hasMenuAccess('report_sales'))
                        <div class="nav-item">
                            <a href="{{ $isActive ? route("employee.{$empRole}.report.sales.index") : 'javascript:void(0)' }}"
                                class="nav-link {{ !$isActive ? 'disabled-link' : '' }} {{ Route::is("employee.{$empRole}.report.sales.*") ? 'active' : '' }}">
                                <span class="material-symbols-outlined">payments</span>
                                <span>{{ __('messages.owner.layout.sales_report') ?? 'Sales Report' }}</span>
                            </a>
                        </div>
                    @endif

                    @if($employee->hasMenuAccess('report_stocks'))
                        <div class="nav-item">
                            <a href="{{ $isActive ? route('employee.' . $empRole . '.report.stocks.index') : 'javascript:void(0)' }}"
                                class="nav-link {{ !$isActive ? 'disabled-link' : '' }} {{ Route::is("employee.{$empRole}.report.stocks.*") ? 'active' : '' }}">
                                <span class="material-symbols-outlined">receipt_long</span>
                                <span>{{ __('messages.owner.layout.stock_report') ?? 'Stock Report' }}</span>
                            </a>
                        </div>
                    @endif
                @endif

            </nav>
        </aside>

        <div class="content-wrapper">
            <header class="main-header">
                <div class="navbar-left">
                    <button class="sidebar-toggle-btn d-md-none" onclick="toggleSidebar()"
                        style="color: var(--primary); display: none;">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                </div>

                <div class="navbar-right">
                    <div class="dropdown">
                        <button class="lang-btn dropdown-toggle" type="button" data-toggle="dropdown">
                            <img src="{{ asset('icons/icon-globe-50.png') }}" alt="Language" class="lang-globe">
                            <span>{{ app()->getLocale() === 'id' ? 'Bahasa' : 'English' }}</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('language.set.get', ['locale' => 'id']) }}">
                                <img src="{{ asset('icons/icon-indonesia-96.png') }}" alt="Indonesia"
                                    class="lang-flag mr-2">
                                Bahasa
                                @if (app()->getLocale() === 'id')
                                    <i class="fas fa-check ml-auto" style="color: var(--primary)"></i>
                                @endif
                            </a>
                            <a class="dropdown-item" href="{{ route('language.set.get', ['locale' => 'en']) }}">
                                <img src="{{ asset('icons/icon-english-96.png') }}" alt="English"
                                    class="lang-flag mr-2">
                                English
                                @if (app()->getLocale() === 'en')
                                    <i class="fas fa-check ml-auto" style="color: var(--primary)"></i>
                                @endif
                            </a>
                        </div>
                    </div>

                    <div class="navbar-divider"></div>

                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="navbar-icon-btn" type="button" data-toggle="dropdown" id="notificationBtn">
                            <span class="material-symbols-outlined">notifications</span>
                            <span class="badge" id="notificationBadge" style="display: none;"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right notification-dropdown"
                            style="min-width: 380px; max-height: 500px; overflow-y: auto;">
                            <div class="dropdown-header d-flex justify-content-between align-items-center">
                                <span id="notificationTitle">Pesan</span>
                                <button class="btn btn-sm btn-link text-primary p-0" id="markAllReadBtn"
                                    style="display: none;">
                                    Tandai telah dibaca
                                </button>
                            </div>
                            <div class="dropdown-divider m-0"></div>

                            <div id="notificationContent">
                                <div class="text-center py-4" id="notificationLoading">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-divider m-0" id="notificationDivider" style="display: none;"></div>
                            <div class="dropdown-footer text-center" id="notificationFooter" style="display: none;">
                                <a href="{{ route("employee.{$empRole}.messages.index") }}"
                                    class="btn btn-sm btn-link text-primary">
                                    Lihat semua
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="user-dropdown-btn" type="button" data-toggle="dropdown">
                            <div class="user-avatar">
                                <svg style="width: 18px; height: 18px; color: #ffffff;" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </div>
                            <span class="user-name" style="text-transform: capitalize;">
                                {{ Auth::guard('employee')->user()->name ?? 'Employee' }}
                                <small class="d-block text-muted"
                                    style="font-size: 0.75rem; text-transform: uppercase;">
                                    {{ Auth::guard('employee')->user()->role ?? '' }}
                                </small>
                            </span>
                            <span class="material-symbols-outlined" style="font-size: 16px">expand_more</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user mr-2"></i>
                                Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('employee.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <section class="content">
                @yield('content')
            </section>

            <footer class="main-footer">
                <div class="float-right d-none d-sm-block">
                    <b>Version</b> 3.2.0
                </div>
                <strong>Copyright &copy; 2024-{{ date('Y') }} <a
                        href="https://vastech.co.id">Vastech.co.id</a>.</strong>
                All rights reserved.
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script src="{{ asset('js/image-cropper.js') }}"></script>
    <script src="{{ asset('js/remove-image.js') }}"></script>

    <script>
        // Toggle Sidebar (Desktop)
        function toggleSidebarCollapse() {
            document.getElementById('mainSidebar').classList.toggle('collapsed');
        }

        // Toggle Sidebar (Mobile)
        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Close sidebar when clicking overlay
        document.getElementById('sidebarOverlay').addEventListener('click', function () {
            toggleSidebar();
        });

        // Toggle Submenu
        function toggleSubmenu(element) {
            const navItem = element.closest('.nav-item');
            navItem.classList.toggle('menu-open');
            event.preventDefault();
        }

        // Initialize plugins
        $(function () {
            // DataTables
            $('.datatable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });

            // Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            // Summernote
            $('.summernote').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Toastr notifications
            @if (session('success'))
                toastr.success({!! json_encode(session('success')) !!});
            @endif

            @if (session('error'))
                toastr.error({!! json_encode(session('error')) !!});
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $err)
                    toastr.error({!! json_encode($err) !!});
                @endforeach
            @endif
        });
    </script>

    <!-- NOTIFICATION SCRIPT -->
    <script>
        $(document).ready(function () {
            let currentPage = 1;
            let lastPage = 1;
            let isLoading = false;

            $('#notificationBtn').on('click', function () {
                if ($('#notificationContent').children('.notification-item').length === 0) {
                    loadNotifications(1);
                }
            });

            function loadNotifications(page = 1) {
                if (isLoading) return;
                isLoading = true;
                $('#notificationLoading').show();

                $.ajax({
                    url: '{{ route("employee.{$empRole}.messages.notifications") }}',
                    method: 'GET',
                    data: { page: page },
                    success: function (response) {
                        if (response.success) {
                            currentPage = response.pagination.current_page;
                            lastPage = response.pagination.last_page;

                            updateBadge(response.unread_count);
                            $('#notificationLoading').hide();

                            if (response.messages.length === 0 && page === 1) {
                                showEmptyState();
                            } else {
                                if (page === 1) $('#notificationContent').empty();
                                renderNotifications(response.messages);
                                $('#notificationDivider').show();
                                $('#notificationFooter').show();
                            }
                        }
                    },
                    error: function () {
                        $('#notificationLoading').hide();
                        toastr.error('Gagal memuat notifikasi');
                    },
                    complete: function () { isLoading = false; }
                });
            }

            function renderNotifications(messages) {
                messages.forEach(function (message) {
                    const isRead = message.recipients && message.recipients.length > 0 && message.recipients[0].is_read;
                    const timeAgo = formatTimeAgo(message.created_at);
                    const msgUrl = '{{ route("employee.{$empRole}.messages.show", ":id") }}'.replace(':id', message.id);

                    const html = `
                    <a href="${msgUrl}" class="notification-item ${!isRead ? 'unread' : ''}"
                        data-id="${message.id}" style="text-decoration:none;color:inherit;display:block;">
                        <div class="d-flex">
                            <div class="notification-icon mr-3">
                                <span class="material-symbols-outlined">mail</span>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">${message.title || 'No Title'}</div>
                                <div class="notification-time">${timeAgo}</div>
                            </div>
                        </div>
                    </a>`;
                    $('#notificationContent').append(html);
                });
            }

            function showEmptyState() {
                $('#notificationContent').html(`
                <div class="notification-empty">
                    <span class="material-symbols-outlined">notifications_off</span>
                    <div style="font-size:14px;font-weight:500;">Tidak ada notifikasi</div>
                    <div style="font-size:12px;margin-top:4px;">Semua sudah terbaca!</div>
                </div>`);
                $('#notificationDivider').hide();
                $('#notificationFooter').hide();
            }

            function updateBadge(count) {
                if (count > 0) {
                    $('#notificationBadge').text(count > 99 ? '99+' : count).show();
                    $('#markAllReadBtn').show();
                    $('#notificationTitle').text(`${count} Pesan belum terbaca`);
                } else {
                    $('#notificationBadge').hide();
                    $('#markAllReadBtn').hide();
                    $('#notificationTitle').text('Pesan');
                }
            }

            $(document).on('click', '.notification-item', function (e) {
                const id = $(this).data('id');
                const $item = $(this);
                const url = $(this).attr('href');

                if ($item.hasClass('unread')) {
                    e.preventDefault();
                    $.ajax({
                        url: '{{ route("employee.{$empRole}.messages.mark-read", ":id") }}'.replace(':id', id),
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            if (response.success) {
                                $item.removeClass('unread');
                                updateBadge(response.unread_count);
                                window.location.href = url;
                            }
                        },
                        error: function () { window.location.href = url; }
                    });
                }
            });

            $('#markAllReadBtn').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $.ajax({
                    url: '{{ route("employee.{$empRole}.messages.mark-all-read") }}',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (response) {
                        if (response.success) {
                            $('.notification-item').removeClass('unread');
                            updateBadge(0);
                            toastr.success('Semua pesan telah ditandai terbaca');
                        }
                    },
                    error: function () { toastr.error('Gagal menandai pesan'); }
                });
            });

            $('.notification-dropdown').on('scroll', function () {
                if (isLoading || currentPage >= lastPage) return;
                const scrollTop = $(this).scrollTop();
                const scrollHeight = $(this)[0].scrollHeight;
                const clientHeight = $(this).height();
                if (scrollTop + clientHeight >= scrollHeight - 50) {
                    loadNotifications(currentPage + 1);
                }
            });

            function formatTimeAgo(datetime) {
                const now = new Date();
                const past = new Date(datetime);
                const diff = Math.floor((now - past) / 1000);
                if (diff < 60) return 'Baru saja';
                if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
                if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
                if (diff < 604800) return Math.floor(diff / 86400) + ' hari lalu';
                return past.toLocaleDateString();
            }

            // Load awal
            loadNotifications(1);
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>