<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Owner Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Bootstrap 4 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <!-- Google Font: Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">


    <!-- Google Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">


    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">


    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.4/dist/select2-bootstrap4.min.css">


    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">


    <!-- Summernote CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css">


    <!-- Loader Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/blockui-loader.css') }}">


    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">


    <!-- Date Picker CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/pickers/daterange/daterangepicker.css') }}">


    <style>
        :root {
            --primary: #d42811;
            --primary-dark: #b01f0c;
            --background-light: #f8f6f6;
            --surface-light: #ffffff;
            --choco-text: #896661;
            --choco-dark: #5c403c;
            --ink: #181211;
            --border-color: #f4f1f0;
        }


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {
            font-family: 'Inter', sans-serif;
            background: var(--background-light);
            color: var(--ink);
            overflow-x: hidden;
        }


        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }


        ::-webkit-scrollbar-track {
            background: transparent;
        }


        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }


        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }


        /* Layout Wrapper */
        .main-wrapper {
            display: flex;
            height: 100vh;
            width: 100%;
        }


        /* Sidebar */
        .main-sidebar {
            width: 280px;
            background: var(--surface-light);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: all 0.3s ease;
            z-index: 1000;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
        }


        .main-sidebar.collapsed {
            width: 80px;
        }


        /* Brand/Logo */
        .brand-link {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--surface-light);
            min-height: 80px;
        }


        .brand-logo-wrapper {
            background: rgba(212, 40, 17, 0.1);
            padding: 0.5rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }


        .brand-logo-wrapper .material-symbols-outlined {
            color: var(--primary);
            font-size: 2rem;
        }


        .brand-text-wrapper {
            display: flex;
            flex-direction: column;
            transition: opacity 0.3s;
        }


        .main-sidebar.collapsed .brand-text-wrapper {
            opacity: 0;
            display: none;
        }


        .brand-title {
            color: var(--ink);
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.2;
            margin: 0;
        }


        .brand-subtitle {
            color: var(--choco-text);
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0;
        }


        /* User Panel */
        .user-panel {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }


        .user-panel .user-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--surface-light);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }


        .user-panel .user-info {
            flex: 1;
            transition: opacity 0.3s;
        }


        .main-sidebar.collapsed .user-panel .user-info {
            opacity: 0;
            display: none;
        }


        .user-panel .user-info a {
            color: var(--ink);
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
        }


        /* Sidebar Navigation */
        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1rem 1rem;
        }


        .nav-header {
            padding: 0.75rem 0.75rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--choco-text);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }


        .main-sidebar.collapsed .nav-header {
            opacity: 0;
            height: 0;
            padding: 0;
            overflow: hidden;
        }


        .nav-item {
            margin-bottom: 0.25rem;
        }


        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            color: var(--ink);
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
        }


        .nav-link:hover {
            background: var(--border-color);
            color: var(--primary);
        }


        .nav-link.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 6px 20px rgba(212, 40, 17, 0.3);
        }


        .nav-link .material-symbols-outlined,
        .nav-link .fas,
        .nav-link .far {
            color: var(--choco-text);
            font-size: 1.25rem;
            transition: color 0.2s;
            flex-shrink: 0;
        }


        .nav-link:hover .material-symbols-outlined,
        .nav-link:hover .fas,
        .nav-link:hover .far {
            color: var(--primary);
        }


        .nav-link.active .material-symbols-outlined,
        .nav-link.active .fas,
        .nav-link.active .far {
            color: white;
        }


        .nav-link span {
            font-size: 0.875rem;
            font-weight: 500;
            transition: opacity 0.3s;
        }


        .main-sidebar.collapsed .nav-link span {
            opacity: 0;
            display: none;
        }


        .nav-link .right {
            margin-left: auto;
            font-size: 1rem;
            transition: transform 0.3s;
        }


        .nav-item.menu-open > .nav-link .right {
            transform: rotate(-90deg);
        }


        /* Submenu */
        .nav-treeview {
            padding-left: 0;
            margin-top: 0.25rem;
            display: none;
        }


        .nav-item.menu-open > .nav-treeview {
            display: block;
        }


        .nav-treeview .nav-link {
            padding-left: 3rem;
            font-size: 0.875rem;
        }


        .main-sidebar.collapsed .nav-treeview {
            display: none !important;
        }


        /* Disabled Menu State */
        .disabled-link {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
            pointer-events: auto !important;
            position: relative;
            background-color: rgba(0, 0, 0, 0.02) !important;
            filter: grayscale(80%);
        }


        .disabled-link:hover {
            background-color: rgba(0, 0, 0, 0.05) !important;
            color: inherit !important;
        }


        .disabled-header {
            opacity: 0.4;
            color: #999 !important;
        }


        /* Main Content Area */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-left: 280px;
            transition: margin-left 0.3s ease;
            height: 100vh;
        }


        .main-sidebar.collapsed ~ .main-content {
            margin-left: 80px;
        }


        /* Header/Navbar */
        .main-header {
            height: 80px;
            background: var(--surface-light);
            border-bottom: 1px solid var(--border-color);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }


        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }


        .sidebar-toggle {
            background: none;
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            color: var(--choco-text);
            font-size: 1.5rem;
            transition: color 0.2s;
        }


        .sidebar-toggle:hover {
            color: var(--primary);
        }


        .search-box {
            position: relative;
            width: 400px;
            max-width: 100%;
        }


        .search-box input {
            width: 100%;
            padding: 0.625rem 1rem 0.625rem 2.5rem;
            border-radius: 0.75rem;
            border: none;
            background: var(--border-color);
            color: var(--ink);
            font-size: 0.875rem;
            transition: all 0.2s;
        }


        .search-box input:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(212, 40, 17, 0.2);
        }


        .search-box .material-symbols-outlined {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--choco-text);
            font-size: 1.25rem;
        }


        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }


        .header-icon-btn {
            position: relative;
            padding: 0.625rem;
            border-radius: 0.75rem;
            background: var(--border-color);
            border: none;
            color: var(--ink);
            cursor: pointer;
            transition: all 0.2s;
        }


        .header-icon-btn:hover {
            background: #e5e7eb;
        }


        .header-icon-btn .material-symbols-outlined {
            font-size: 1.25rem;
        }


        .notification-badge {
            position: absolute;
            top: 0.5rem;
            right: 0.625rem;
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
            border: 2px solid var(--surface-light);
        }


        .user-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.25rem 0.75rem 0.25rem 0.25rem;
            border-radius: 9999px;
            background: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
        }


        .user-dropdown-toggle:hover {
            background: var(--border-color);
            border-color: #e5e7eb;
        }


        .user-dropdown-toggle .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }


        .user-dropdown-toggle .user-info-header {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }


        .user-dropdown-toggle .user-name {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.2;
        }


        .user-dropdown-toggle .user-role {
            font-size: 0.625rem;
            font-weight: 500;
            color: var(--choco-text);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }


        /* Content Wrapper */
        .content-wrapper {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
        }


        /* Breadcrumb */
        .content-header {
            padding: 1rem 0;
            margin-bottom: 1.5rem;
        }


        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 0.875rem;
        }


        .breadcrumb-item + .breadcrumb-item::before {
            content: "/";
            color: var(--choco-text);
        }


        .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
        }


        .breadcrumb-item.active {
            color: var(--choco-text);
        }


        /* Cards */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            background: var(--surface-light);
            margin-bottom: 1.5rem;
        }


        .card-header {
            background: var(--surface-light);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            font-weight: 600;
        }


        .card-body {
            padding: 1.5rem;
        }


        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            border-radius: 0.75rem;
            padding: 0.625rem 1.25rem;
            font-weight: 600;
            transition: all 0.2s;
        }


        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212, 40, 17, 0.3);
        }


        /* Dropdown Menu Styling */
        .dropdown-menu {
            border-radius: 1rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
        }


        .dropdown-item {
            border-radius: 0.5rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }


        .dropdown-item:hover {
            background: var(--border-color);
            color: var(--primary);
        }


        /* Language Dropdown */
        .lang-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            background: var(--surface-light);
            color: var(--ink);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }


        .lang-btn:hover {
            background: var(--border-color);
            border-color: #e5e7eb;
        }


        .lang-flag {
            width: 18px;
            height: 18px;
            object-fit: cover;
            border-radius: 2px;
        }


        /* Tables */
        .table {
            font-size: 0.875rem;
        }


        .table thead th {
            background: var(--surface-light);
            border-bottom: 2px solid var(--border-color);
            color: var(--ink);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }


        /* Footer */
        .main-footer {
            padding: 1rem 2rem;
            background: var(--surface-light);
            border-top: 1px solid var(--border-color);
            font-size: 0.875rem;
            color: var(--choco-text);
        }


        /* Responsive */
        @media (max-width: 992px) {
            .main-sidebar {
                transform: translateX(-100%);
            }


            .main-sidebar.show {
                transform: translateX(0);
            }


            .main-content {
                margin-left: 0;
            }


            .search-box {
                display: none;
            }


            .header-left .sidebar-toggle {
                display: block;
            }
        }


        @media (min-width: 993px) {
            .mobile-only {
                display: none !important;
            }
        }


        /* Utility Classes */
        .text-choco {
            color: var(--choco-text) !important;
        }


        .text-primary-custom {
            color: var(--primary) !important;
        }


        .bg-primary-custom {
            background-color: var(--primary) !important;
        }


        .rounded-xl {
            border-radius: 0.75rem !important;
        }


        .rounded-2xl {
            border-radius: 1rem !important;
        }


        /* SweetAlert2 Custom */
        .swal2-popup {
            border-radius: 1rem !important;
        }


        .swal2-confirm {
            background: var(--primary) !important;
            border-radius: 0.75rem !important;
        }


        /* Toastr Custom */
        #toast-container > .toast {
            border-radius: 0.75rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }


        #toast-container > .toast-success {
            background-color: var(--primary);
        }
    </style>


</head>


<body>
    <div class="main-wrapper">
        <!-- Sidebar -->
        <aside class="main-sidebar" id="mainSidebar">
            <!-- Brand Logo -->
            <a href="{{ route('owner.user-owner.dashboard') }}" class="brand-link">
                <div class="brand-logo-wrapper">
                    <span class="material-symbols-outlined">local_cafe</span>
                </div>
                <div class="brand-text-wrapper">
                    <h1 class="brand-title">Owner Panel</h1>
                    <p class="brand-subtitle">ChocoAdmin Dashboard</p>
                </div>
            </a>


            <!-- User Panel -->
            <div class="user-panel">
                @if (auth('owner')->check() && auth('owner')->user()->image)
                    <img src="{{ asset('storage/' . auth('owner')->user()->image) }}" class="user-image" alt="User Image">
                @else
                    <div class="user-image" style="background-color: #9ca3af; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 22px; height: 22px; color: #ffffff;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </div>
                @endif
                <div class="user-info">
                    <a href="{{ route('owner.user-owner.settings.index') }}">
                        @auth('owner')
                            {{ auth('owner')->user()->name }}
                        @else
                            User Owner
                        @endauth
                    </a>
                </div>
            </div>


            <!-- Sidebar Menu -->
            <nav class="sidebar-menu">
                @php
                    $isVerified = auth('owner')->check() && auth('owner')->user()->verification_status === 'approved';
                    $isActive = auth('owner')->check() && auth('owner')->user()->is_active;
                @endphp


                <div class="nav-header">Main Menu</div>


                <!-- Dashboard -->
                <div class="nav-item">
                    <a href="{{ $isVerified && $isActive ? route('owner.user-owner.dashboard') : 'javascript:void(0)' }}"
                        class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} {{ Route::is('owner.user-owner.dashboard') ? 'active' : '' }}"
                        onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : '' }}">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span>{{ __('messages.owner.layout.dashboard') }}</span>
                    </a>
                </div>


                <!-- User Management -->
                @php
                    $employeeRoutes = ['owner.user-owner.employees.*'];
                @endphp
                <div class="nav-item {{ Route::is($employeeRoutes) ? 'menu-open' : '' }}">
                    <a href="javascript:void(0)"
                        class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} {{ Route::is($employeeRoutes) ? 'active' : '' }}"
                        onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : 'toggleSubmenu(this)' }}">
                        <span class="material-symbols-outlined">group</span>
                        <span>{{ __('messages.owner.layout.user_management') }}</span>
                        @if ($isVerified && $isActive)
                            <i class="fas fa-angle-left right"></i>
                        @endif
                    </a>
                    <div class="nav-treeview {{ !$isVerified || !$isActive ? 'disabled' : '' }}">
                        <div class="nav-item">
                            <a href="{{ route('owner.user-owner.employees.index') }}"
                                class="nav-link {{ Route::is('owner.user-owner.employees.*') ? 'active' : '' }}">
                                <i class="far fa-circle"></i>
                                <span>{{ __('messages.owner.layout.employees') }}</span>
                            </a>
                        </div>
                    </div>
                </div>


                <!-- XenPlatform -->
                <div class="nav-item {{ Request::segment(1) == 'owner' && Request::segment(3) == 'xen_platform' ? 'menu-open' : '' }}">
                    <a href="javascript:void(0)"
                        class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }}"
                        onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : 'toggleSubmenu(this)' }}">
                        <span class="material-symbols-outlined">credit_card</span>
                        <span>XenPlatform</span>
                        @if($isVerified && $isActive)
                            <i class="fas fa-angle-left right"></i>
                        @endif
                    </a>
                    <div class="nav-treeview {{ !$isVerified || !$isActive ? 'disabled' : '' }}">
                        <div class="nav-item">
                            <a href="{{ $isVerified ? route('owner.user-owner.xen_platform.accounts.information') : 'javascript:void(0)' }}"
                                class="nav-link {{ Request::segment(4) == 'accounts' ? 'active' : '' }}">
                                <i class="far fa-circle"></i>
                                <span>{{ __('messages.owner.layout.accounts') }}</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a href="{{ $isVerified ? route('owner.user-owner.xen_platform.split-payment.index') : 'javascript:void(0)' }}"
                                class="nav-link {{ Request::segment(4) == 'split-payment' ? 'active' : '' }}">
                                <i class="far fa-circle"></i>
                                <span>{{ __('messages.owner.layout.split_payments') }}</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a href="{{ $isVerified ? route('owner.user-owner.xen_platform.payout.index') : 'javascript:void(0)' }}"
                                class="nav-link {{ Request::segment(4) == 'payout' ? 'active' : '' }}">
                                <i class="far fa-circle"></i>
                                <span>{{ __('messages.owner.layout.withdrawal') }}</span>
                            </a>
                        </div>
                    </div>
                </div>


                <!-- Outlets -->
                @php
                    $outletRoutes = ['owner.user-owner.outlets.*'];
                @endphp
                <div class="nav-item {{ Route::is($outletRoutes) ? 'menu-open' : '' }}">
                    <a href="javascript:void(0)"
                        class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} {{ Route::is($outletRoutes) ? 'active' : '' }}"
                        onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : 'toggleSubmenu(this)' }}">
                        <span class="material-symbols-outlined">storefront</span>
                        <span>{{ __('messages.owner.layout.outlets') }}</span>
                        @if ($isVerified && $isActive)
                            <i class="fas fa-angle-left right"></i>
                        @endif
                    </a>
                    <div class="nav-treeview {{ !$isVerified || !$isActive ? 'disabled' : '' }}">
                        <div class="nav-item">
                            <a href="{{ route('owner.user-owner.outlets.index') }}"
                                class="nav-link {{ Route::is('owner.user-owner.outlets.*') ? 'active' : '' }}">
                                <i class="far fa-circle"></i>
                                <span>{{ __('messages.owner.layout.all_outlets') }}</span>
                            </a>
                        </div>
                    </div>
                </div>


                <!-- Products -->
                @php
                    $productRoutes = ['owner.user-owner.products.*'];
                    $categoryRoutes = ['owner.user-owner.categories.*'];
                    $promotionRoutes = ['owner.user-owner.promotions.*'];
                    $stockRoutes = ['owner.user-owner.stocks.*'];
                    $masterProductRoutes = ['owner.user-owner.master-products.*'];
                    $outletProductRoutes = ['owner.user-owner.outlet-products.*'];
                    $allProductRoutes = array_merge($productRoutes, $categoryRoutes, $promotionRoutes, $masterProductRoutes, $outletProductRoutes, $stockRoutes);
                @endphp
                <div class="nav-item {{ Route::is($allProductRoutes) ? 'menu-open' : '' }}">
<a href="javascript:void(0)"
                     class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} {{ Route::is($allProductRoutes) ? 'active' : '' }}"
                     onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : 'toggleSubmenu(this)' }}">
<span class="material-symbols-outlined">inventory_2</span>
<span>{{ __('messages.owner.layout.products') }}</span>
@if ($isVerified && $isActive)
<i class="fas fa-angle-left right"></i>
@endif
</a>
<div class="nav-treeview {{ !$isVerified || !$isActive ? 'disabled' : '' }}">
<div class="nav-item">
<a href="{{ route('owner.user-owner.master-products.index') }}"
                             class="nav-link {{ Route::is('owner.user-owner.master-products.*') ? 'active' : '' }}">
<i class="far fa-circle"></i>
<span>{{ __('messages.owner.layout.master_products') }}</span>
</a>
</div>
<div class="nav-item">
<a href="{{ route('owner.user-owner.outlet-products.index') }}"
                             class="nav-link {{ Route::is('owner.user-owner.outlet-products.*') ? 'active' : '' }}">
<i class="far fa-circle"></i>
<span>{{ __('messages.owner.layout.outlet_products') }}</span>
</a>
</div>
<div class="nav-item">
<a href="{{ route('owner.user-owner.stocks.index') }}"
                             class="nav-link {{ Route::is('owner.user-owner.stocks.*') ? 'active' : '' }}">
<i class="far fa-circle"></i>
<span>{{ __('messages.owner.layout.stocks') }}</span>
</a>
</div>
<div class="nav-item">
<a href="{{ route('owner.user-owner.categories.index') }}"
                             class="nav-link {{ Route::is('owner.user-owner.categories.*') ? 'active' : '' }}">
<i class="far fa-circle"></i>
<span>{{ __('messages.owner.layout.categories') }}</span>
</a>
</div>
<div class="nav-item">
<a href="{{ route('owner.user-owner.promotions.index') }}"
                             class="nav-link {{ Route::is('owner.user-owner.promotions.*') ? 'active' : '' }}">
<i class="far fa-circle"></i>
<span>{{ __('messages.owner.layout.promotions') }}</span>
</a>
</div>
</div>
</div>
            <!-- Settings -->
            @php
                $settingRoutes = ['owner.user-owner.settings.*'];
            @endphp
            <div class="nav-header">System</div>
            <div class="nav-item {{ Route::is($settingRoutes) ? 'menu-open' : '' }}">
                <a href="javascript:void(0)"
                    class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} {{ Route::is($settingRoutes) ? 'active' : '' }}"
                    onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : 'toggleSubmenu(this)' }}">
                    <span class="material-symbols-outlined">settings</span>
                    <span>{{ __('messages.owner.layout.settings') }}</span>
                    @if ($isVerified && $isActive)
                        <i class="fas fa-angle-left right"></i>
                    @endif
                </a>
                <div class="nav-treeview {{ !$isVerified || !$isActive ? 'disabled' : '' }}">
                    <div class="nav-item">
                        <a href="{{ route('owner.user-owner.settings.index') }}"
                            class="nav-link {{ Route::is('owner.user-owner.settings.index') ? 'active' : '' }}">
                            <i class="far fa-circle"></i>
                            <span>{{ __('messages.owner.layout.settings') }}</span>
                        </a>
                    </div>
                </div>
            </div>


            <div class="nav-header {{ !$isVerified || !$isActive ? 'disabled-header' : '' }}">
                {{ __('messages.owner.layout.reports') }}
            </div>


            <!-- Sales Report -->
            <div class="nav-item">
                <a href="{{ $isVerified && $isActive ? route('owner.user-owner.report.sales.index') : 'javascript:void(0)' }}"
                    class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} {{ Route::is('owner.user-owner.report.sales.*') ? 'active' : '' }}"
                    onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : '' }}">
                    <span class="material-symbols-outlined">bar_chart</span>
                    <span>{{ __('messages.owner.layout.sales_report') }}</span>
                </a>
            </div>


            <!-- Stock Report -->
            <div class="nav-item">
                <a href="{{ $isVerified && $isActive ? route('owner.user-owner.report.stocks.index') : 'javascript:void(0)' }}"
                    class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} {{ Route::is('owner.user-owner.report.stocks.*') ? 'active' : '' }}"
                    onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : '' }}">
                    <i class="fas fa-boxes-stacked"></i>
                    <span>{{ __('messages.owner.layout.stock_report') }}</span>
                </a>
            </div>
        </nav>
    </aside>


    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Header -->
        <header class="main-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="search-box">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" placeholder="Search outlets, employees, or products...">
                </div>
            </div>


            <div class="header-right">
                <!-- Language Switcher -->
                <div class="dropdown">
                    <button class="lang-btn dropdown-toggle" type="button" data-toggle="dropdown">
                        <img src="{{ asset('icons/icon-globe-50.png') }}" alt="Language" class="lang-flag" />
                        <span>{{ app()->getLocale() === 'id' ? 'Bahasa' : 'English' }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="{{ route('language.set.get', ['locale' => 'id']) }}" class="dropdown-item">
                            <img src="{{ asset('icons/icon-indonesia-96.png') }}" alt="Indonesia" class="lang-flag mr-2">
                            Bahasa
                            @if (app()->getLocale() === 'id')
                                <i class="fas fa-check ml-auto text-primary-custom"></i>
                            @endif
                        </a>
                        <a href="{{ route('language.set.get', ['locale' => 'en']) }}" class="dropdown-item">
                            <img src="{{ asset('icons/icon-english-96.png') }}" alt="English" class="lang-flag mr-2">
                            English
                            @if (app()->getLocale() === 'en')
                                <i class="fas fa-check ml-auto text-primary-custom"></i>
                            @endif
                        </a>
                    </div>
                </div>


                <!-- Notifications -->
                <button class="header-icon-btn">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="notification-badge"></span>
                </button>


                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="user-dropdown-toggle dropdown-toggle" type="button" data-toggle="dropdown">
                        @if (auth('owner')->check() && auth('owner')->user()->image)
                            <img src="{{ asset('storage/' . auth('owner')->user()->image) }}" class="user-avatar" alt="User">
                        @else
                            <div class="user-avatar" style="background-color: #9ca3af; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 20px; height: 20px; color: #ffffff;" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </div>
                        @endif
                        <div class="user-info-header d-none d-sm-flex">
                            <span class="user-name">
                                @auth('owner')
                                    {{ auth('owner')->user()->name }}
                                @else
                                    User Owner
                                @endauth
                            </span>
                            <span class="user-role">Owner</span>
                        </div>
                        <span class="material-symbols-outlined">expand_more</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="{{ route('owner.user-owner.settings.index') }}" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i>
                            {{ __('messages.owner.layout.profile') }}
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('owner.logout') }}" class="d-inline w-100">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                {{ __('messages.owner.layout.sign_out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>


        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header (Breadcrumb) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>@yield('page_title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('owner.user-owner.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active">@yield('page_title', 'Dashboard')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>


            <!-- Main Content -->
            @yield('content')
        </div>


        <!-- Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 3.2.0
            </div>
            <strong>Copyright &copy; 2024-{{ date('Y') }} <a href="https://vastech.co.id">Vastech.co.id</a>.</strong>
            All rights reserved.
        </footer>
    </div>
</div>


<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>


<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>


<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>


<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>


<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<!-- Summernote -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.js"></script>


<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<!-- Cropper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>


<!-- Moment & DatePicker -->
<script src="{{ asset('admin/app-assets/vendors/js/pickers/daterange/moment.min.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/pickers/daterange/daterangepicker.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>


<!-- BlockUI -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>


<!-- Custom loader -->
<script src="{{ asset('admin/assets/js/blockui-loader.js') }}"></script>


<script>
    // Toggle Sidebar
    function toggleSidebar() {
        document.getElementById('mainSidebar').classList.toggle('collapsed');
    }


    // Toggle Submenu
    function toggleSubmenu(element) {
        const navItem = element.closest('.nav-item');
        const isOpen = navItem.classList.contains('menu-open');
       
        // Close all other open menus
        document.querySelectorAll('.nav-item.menu-open').forEach(item => {
            if (item !== navItem) {
                item.classList.remove('menu-open');
            }
        });
       
        // Toggle current menu
        navItem.classList.toggle('menu-open');
    }


    // Show verification alert
    function showVerificationAlert(event) {
        event.preventDefault();
        event.stopPropagation();


        Swal.fire({
            icon: 'warning',
            title: '<strong>Akses Terbatas</strong>',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p style="margin-bottom: 15px; color: #666;">
                        <strong>Akun Anda belum diverifikasi oleh admin.</strong>
                    </p>
                    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #ffc107;">
                        <p style="margin: 0; color: #856404;">
                            <i class="fas fa-info-circle"></i>
                            Anda tidak dapat mengakses fitur ini sampai proses verifikasi selesai.
                        </p>
                    </div>
                </div>
            `,
            confirmButtonColor: '#d42811',
            allowOutsideClick: true,
        });


        return false;
    }


    // Initialize plugins
    $(function() {
        // DataTable
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


        // Close sidebar on mobile when clicking outside
        $(document).on('click', function(e) {
            if (window.innerWidth <= 992) {
                const sidebar = document.getElementById('mainSidebar');
                const toggle = document.querySelector('.sidebar-toggle');
               
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });


        // Mobile sidebar toggle
        $('.sidebar-toggle').on('click', function() {
            if (window.innerWidth <= 992) {
                document.getElementById('mainSidebar').classList.toggle('show');
            }
        });
    });
</script>


@yield('scripts')
@yield('modal')
@stack('scripts')


@if (session('success'))
    <script>
        $(function() {
            toastr.success({!! json_encode(session('success')) !!});
        });
    </script>
@endif


@if (session('error'))
    <script>
        $(function() {
            toastr.error({!! json_encode(session('error')) !!});
        });
    </script>
@endif


@if ($errors->any())
    <script>
        $(function() {
            @foreach ($errors->all() as $err)
                toastr.error({!! json_encode($err) !!});
            @endforeach
        });
    </script>
@endif


@if(session('swal_error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'warning',
                title: @json(session('swal_error.title')),
                text: @json(session('swal_error.text')),
                confirmButtonColor: '#d42811',
            });
        });
    </script>
@endif
</body>
</html>


