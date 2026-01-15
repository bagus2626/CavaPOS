<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Partner Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 4 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Font: Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.4/dist/select2-bootstrap4.min.css">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Summernote CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css">

    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">

    <link rel="stylesheet" href="{{ asset('css/message.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/add-product-options.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-global.css') }}">
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="main-sidebar" id="mainSidebar">
            <!-- Brand Logo -->
            <a href="{{ route('partner.dashboard') }}" class="brand-link">
                <!-- Logo untuk sidebar terbuka (horizontal) -->
                <img src="{{ asset('images/cava-logo2-gradient.png') }}" alt="Cavaa Logo"
                    class="brand-image brand-image-full">

                <!-- Logo circular untuk sidebar collapse -->
                <img src="{{ asset('icons/cava-logo-red-gradient.png') }}" alt="Cavaa Logo"
                    class="brand-image brand-image-collapsed">
            </a>

            <!-- Sidebar Navigation -->
            <nav class="sidebar-nav">
                <!-- Main Section -->
                <div class="nav-section-title">{{ __('messages.partner.layout.dashboard') }}</div>

                <div class="nav-item">
                    <a href="{{ route('partner.dashboard') }}"
                        class="nav-link @if(Route::is('partner.dashboard')) active @endif">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span>{{ __('messages.partner.layout.dashboard') }}</span>
                    </a>
                </div>

                <!-- Management Section -->
                <div class="nav-section-title">{{ __('messages.partner.layout.user_management') }}</div>

                @php
                    $employeeRoutes = ['partner.user-management.*'];
                @endphp

                <div class="nav-item {{ Route::is($employeeRoutes) ? 'menu-open' : '' }}">
                    <a href="javascript:void(0)"
                        class="nav-link {{ Route::is($employeeRoutes) ? 'active' : '' }}"
                        onclick="toggleSubmenu(this)">
                        <span class="material-symbols-outlined">group</span>
                        <span>{{ __('messages.partner.layout.user_management') }}</span>
                        <span class="material-symbols-outlined expand-icon">expand_more</span>
                    </a>
                    <div class="nav-treeview">
                        <div class="nav-item">
                            <a href="{{ route('partner.user-management.employees.index') }}"
                                class="nav-link {{ Route::is('partner.user-management.employees.*') ? 'active' : '' }}">
                                <span>{{ __('messages.partner.layout.employees') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                @php
                    $storeRoutes = ['partner.store.*'];
                @endphp

                <div class="nav-item {{ Route::is($storeRoutes) ? 'menu-open' : '' }}">
                    <a href="javascript:void(0)"
                        class="nav-link {{ Route::is($storeRoutes) ? 'active' : '' }}"
                        onclick="toggleSubmenu(this)">
                        <span class="material-symbols-outlined">storefront</span>
                        <span>{{ __('messages.partner.layout.outlet') }}</span>
                        <span class="material-symbols-outlined expand-icon">expand_more</span>
                    </a>
                    <div class="nav-treeview">
                        <!-- Table Management -->
                        <div class="nav-item {{ Route::is('partner.store.tables.*') || Route::is('partner.store.seat-layouts.*') ? 'menu-open' : '' }}">
                            <a href="javascript:void(0)"
                                class="nav-link {{ Route::is('partner.store.tables.*') || Route::is('partner.store.seat-layouts.*') ? 'active' : '' }}"
                                onclick="toggleSubmenu(this)">
                                <span class="material-symbols-outlined">table_restaurant</span>
                                <span>{{ __('messages.partner.layout.table_management') }}</span>
                                <span class="material-symbols-outlined expand-icon">expand_more</span>
                            </a>
                            <div class="nav-treeview">
                                <div class="nav-item">
                                    <a href="{{ route('partner.store.tables.index') }}"
                                        class="nav-link {{ Route::is('partner.store.tables.*') ? 'active' : '' }}">
                                        <span>{{ __('messages.partner.layout.tables') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $productRoutes = ['partner.products.*'];
                    $categoryRoutes = ['partner.categories.*'];
                    $allProductRoutes = array_merge($productRoutes, $categoryRoutes);
                @endphp

                <div class="nav-item {{ Route::is($allProductRoutes) ? 'menu-open' : '' }}">
                    <a href="javascript:void(0)"
                        class="nav-link {{ Route::is($allProductRoutes) ? 'active' : '' }}"
                        onclick="toggleSubmenu(this)">
                        <span class="material-symbols-outlined">inventory_2</span>
                        <span>{{ __('messages.partner.layout.products') }}</span>
                        <span class="material-symbols-outlined expand-icon">expand_more</span>
                    </a>
                    <div class="nav-treeview">
                        <div class="nav-item">
                            <a href="{{ route('partner.products.index') }}"
                                class="nav-link {{ Route::is('partner.products.*') ? 'active' : '' }}">
                                <span>{{ __('messages.partner.layout.all_products') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Top Navbar -->
            <header class="main-header">
                <div class="navbar-left">
                    <!-- Mobile Sidebar Toggle Button -->
                    <button class="sidebar-toggle-btn d-md-none" onclick="toggleSidebar()"
                        style="color: var(--primary); display: none;">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                </div>

                <div class="navbar-right">
                    <!-- Language Switcher -->
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
                                @if(app()->getLocale() === 'id')
                                    <i class="fas fa-check ml-auto" style="color: var(--primary)"></i>
                                @endif
                            </a>
                            <a class="dropdown-item" href="{{ route('language.set.get', ['locale' => 'en']) }}">
                                <img src="{{ asset('icons/icon-english-96.png') }}" alt="English"
                                    class="lang-flag mr-2">
                                English
                                @if(app()->getLocale() === 'en')
                                    <i class="fas fa-check ml-auto" style="color: var(--primary)"></i>
                                @endif
                            </a>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="navbar-icon-btn" type="button" data-toggle="dropdown" id="notificationBtn">
                            <span class="material-symbols-outlined">notifications</span>
                            <span class="badge" id="notificationBadge" style="display: none;"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right notification-dropdown" style="min-width: 380px; max-height: 500px; overflow-y: auto;">
                            <div class="dropdown-header d-flex justify-content-between align-items-center">
                                <span id="notificationTitle">{{ __('messages.partner.layout.notifications') }}</span>
                                <button class="btn btn-sm btn-link text-primary p-0" id="markAllReadBtn" style="display: none;">
                                    Tandain telah dibaca
                                </button>
                            </div>
                            <div class="dropdown-divider m-0"></div>
                            
                            <div id="notificationContent">
                                <!-- Loading state -->
                                <div class="text-center py-4" id="notificationLoading">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-divider m-0" id="notificationDivider" style="display: none;"></div>
                            <div class="dropdown-footer text-center" id="notificationFooter" style="display: none;">
                                <a href="{{ route('partner.messages.index') }}" class="btn btn-sm btn-link text-primary">
                                    Lihat semua
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="navbar-divider"></div>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="user-dropdown-btn" type="button" data-toggle="dropdown">
                            <div class="user-avatar">
                                @if(auth()->user()->logo)
                                    <img src="{{ asset('storage/' . auth()->user()->logo) }}" alt="User">
                                @else
                                    <svg style="width: 18px; height: 18px; color: #ffffff;" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                    </svg>
                                @endif
                            </div>
                            <span class="user-name">
                                @auth {{ auth()->user()->name }}
                                @else User Partner @endauth
                            </span>
                            <span class="material-symbols-outlined" style="font-size: 16px">expand_more</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user mr-2"></i>
                                {{ __('messages.partner.layout.profile') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    {{ __('messages.partner.layout.sign_out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <section class="content">
                @yield('content')
            </section>

            <!-- Footer -->
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

    <!-- REQUIRED SCRIPTS -->
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

        // Fullscreen Toggle
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        // Initialize plugins
        $(function() {
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
            @if(session('success'))
                toastr.success({!! json_encode(session('success')) !!});
            @endif

            @if(session('error'))
                toastr.error({!! json_encode(session('error')) !!});
            @endif

            @if($errors->any())
                @foreach($errors->all() as $err)
                    toastr.error({!! json_encode($err) !!});
                @endforeach
            @endif

            // Example chart (if needed)
            var chartElement = document.getElementById('myChart');
            if (chartElement) {
                var ctx = chartElement.getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                        datasets: [{
                            label: 'Sales 2023',
                            data: [12, 19, 3, 5, 2, 3],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>

    <!-- NOTIFICATION SCRIPT -->
    <script>
    $(document).ready(function() {
        let currentPage = 1;
        let lastPage = 1;
        let isLoading = false;

        // Load notifications on dropdown open
        $('#notificationBtn').on('click', function() {
            if ($('#notificationContent').children('.notification-item').length === 0) {
                loadNotifications(1);
            }
        });

        // Load notifications function
        function loadNotifications(page = 1) {
            if (isLoading) return;
            
            isLoading = true;
            $('#notificationLoading').show();

            $.ajax({
                url: '{{ route("partner.messages.notifications") }}',
                method: 'GET',
                data: { page: page },
                success: function(response) {
                    if (response.success) {
                        currentPage = response.pagination.current_page;
                        lastPage = response.pagination.last_page;

                        // Update badge
                        updateBadge(response.unread_count);

                        // Clear loading
                        $('#notificationLoading').hide();

                        if (response.messages.length === 0 && page === 1) {
                            showEmptyState();
                        } else {
                            if (page === 1) {
                                $('#notificationContent').empty();
                            }
                            renderNotifications(response.messages);
                            
                            // Show footer if there are messages
                            $('#notificationDivider').show();
                            $('#notificationFooter').show();
                        }
                    }
                },
                error: function() {
                    $('#notificationLoading').hide();
                    toastr.error('Failed to load notifications');
                },
                complete: function() {
                    isLoading = false;
                }
            });
        }

        // Render notifications
        function renderNotifications(messages) {
            messages.forEach(function(message) {
                const isRead = message.recipients && message.recipients.length > 0 && message.recipients[0].is_read;
                const timeAgo = formatTimeAgo(message.created_at);
                const messageUrl = '{{ route("partner.messages.show", ":id") }}'.replace(':id', message.id);
                
                const notificationHtml = `
                    <a href="${messageUrl}" class="notification-item ${!isRead ? 'unread' : ''}" data-id="${message.id}" style="text-decoration: none; color: inherit; display: block;">
                        <div class="d-flex">
                            <div class="notification-icon mr-3">
                                <span class="material-symbols-outlined">mail</span>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">${message.title || 'No Title'}</div>
                                <div class="notification-time">${timeAgo}</div>
                            </div>
                        </div>
                    </a>
                `;
                
                $('#notificationContent').append(notificationHtml);
            });
        }

        // Show empty state
        function showEmptyState() {
            const emptyHtml = `
                <div class="notification-empty">
                    <span class="material-symbols-outlined">notifications_off</span>
                    <div style="font-size: 14px; font-weight: 500;">No notifications</div>
                    <div style="font-size: 12px; margin-top: 4px;">You're all caught up!</div>
                </div>
            `;
            $('#notificationContent').html(emptyHtml);
            $('#notificationDivider').hide();
            $('#notificationFooter').hide();
        }

        // Update badge
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

        // Mark as read on click
        $(document).on('click', '.notification-item', function(e) {
            const id = $(this).data('id');
            const $item = $(this);
            const url = $(this).attr('href');

            if ($item.hasClass('unread')) {
                e.preventDefault();
                
                $.ajax({
                    url: '{{ route("partner.messages.mark-read", ":id") }}'.replace(':id', id),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $item.removeClass('unread');
                            updateBadge(response.unread_count);
                            window.location.href = url;
                        }
                    },
                    error: function() {
                        window.location.href = url;
                    }
                });
            }
        });

        // Mark all as read
        $('#markAllReadBtn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            $.ajax({
                url: '{{ route("partner.messages.mark-all-read") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('.notification-item').removeClass('unread');
                        updateBadge(0);
                        toastr.success('All messages marked as read');
                    }
                },
                error: function() {
                    toastr.error('Failed to mark messages as read');
                }
            });
        });

        // Infinite scroll
        $('.notification-dropdown').on('scroll', function() {
            if (isLoading || currentPage >= lastPage) return;

            const scrollTop = $(this).scrollTop();
            const scrollHeight = $(this)[0].scrollHeight;
            const clientHeight = $(this).height();

            if (scrollTop + clientHeight >= scrollHeight - 50) {
                loadNotifications(currentPage + 1);
            }
        });

        // Helper function to format time ago
        function formatTimeAgo(datetime) {
            const now = new Date();
            const past = new Date(datetime);
            const diffInSeconds = Math.floor((now - past) / 1000);

            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' minutes ago';
            if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
            if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + ' days ago';
            
            return past.toLocaleDateString();
        }

        // Initial load badge count
        loadNotifications(1);
    });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>