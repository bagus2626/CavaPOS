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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">


    <!-- Material Symbols -->
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


    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">


    <!-- Date Picker CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/pickers/daterange/daterangepicker.css') }}">


    <!-- Loader Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/blockui-loader.css') }}">


    <style>
        :root {
            --primary: #ae1504;
            --primary-light: #fdf3f2;
            --primary-dark: #7d0f03;
            --secondary-text: #8c635f;
            --background-light: #f8f6f5;
            --background-dark: #23110f;
            --card-dark: #2a1614;
            --border-color: #f0e6e6;
        }


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {
            font-family: 'Inter', 'Noto Sans', sans-serif;
            background: var(--background-light);
            color: #181111;
            overflow-x: hidden;
        }


        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #dccac8;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }


        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            font-size: 20px;
        }
        .icon-fill {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }


        /* Wrapper Layout */
        .wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }


        /* Sidebar Styles */
        .main-sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
        }


        .main-sidebar.collapsed {
            width: 80px;
        }


        /* Brand Link */
.brand-link {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid var(--border-color);
    min-height: 80px;
    background: white;
    position: relative;
}


/* Logo Images */
.brand-image {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}


.brand-image-full {
    max-height: 50px;
    width: auto;
    max-width: 350px;
    opacity: 1;
    display: block;
}


.brand-image-collapsed {
    max-height: 35px;
    width: 35px;
    height: 35px;
    opacity: 0;
    display: none;
    object-fit: contain;
    border-radius: 50%;
    position: absolute;
}


/* Sidebar Collapsed State */
.main-sidebar.collapsed .brand-image-full {
    opacity: 0;
    display: none;
}


.main-sidebar.collapsed .brand-image-collapsed {
    opacity: 1;
    display: block;
}


        /* User Panel */
        .user-panel {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }


        .user-panel .image {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
        }


        .user-panel img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }


        .user-panel .user-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }


        .user-panel .info {
            flex: 1;
            overflow: hidden;
            transition: all 0.3s ease;
        }


        .user-panel .info a {
            color: var(--primary);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }


        .main-sidebar.collapsed .user-panel .info {
            opacity: 0;
            width: 0;
        }


        /* Sidebar Navigation */
        .sidebar-nav {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }


        .nav-section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--secondary-text);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }


        .main-sidebar.collapsed .nav-section-title {
            opacity: 0;
            height: 0;
            padding: 0;
            margin: 0;
        }


        .nav-item {
            margin: 0.25rem 1rem;
        }


        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.75rem;
            border-radius: 0.5rem;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            font-weight: 500;
            position: relative;
        }


        .nav-link:hover {
            background: var(--background-light);
            color: #374151;
        }


        .nav-link.active {
            background: rgba(174, 21, 4, 0.1);
            color: var(--primary);
        }


        .nav-link .material-symbols-outlined {
            flex-shrink: 0;
        }


        .nav-link.active .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }


        .nav-link span:not(.material-symbols-outlined) {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: all 0.3s ease;
        }


        .main-sidebar.collapsed .nav-link span:not(.material-symbols-outlined) {
            opacity: 0;
            width: 0;
        }


        .nav-link .expand-icon {
            margin-left: auto;
            font-size: 16px;
            transition: transform 0.3s ease;
        }


        .main-sidebar.collapsed .nav-link .expand-icon {
            display: none;
        }


        .nav-item.menu-open > .nav-link .expand-icon {
            transform: rotate(180deg);
        }


        /* Submenu */
        .nav-treeview {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }


        .nav-item.menu-open > .nav-treeview {
            max-height: 1000px;
        }


        .nav-treeview .nav-item {
            margin-left: 0;
            padding-left: 2.75rem;
        }


        .nav-treeview .nav-link {
            font-size: 0.8125rem;
            padding: 0.5rem 0.75rem;
        }


        .main-sidebar.collapsed .nav-treeview {
            display: none;
        }


        /* Disabled Menu State */
        .nav-link.disabled-link {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: auto;
            background-color: rgba(0, 0, 0, 0.02);
            filter: grayscale(80%);
        }


        .nav-link.disabled-link:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }


        /* Content Wrapper */
        .content-wrapper {
            flex: 1;
            margin-left: 280px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }


        .main-sidebar.collapsed ~ .content-wrapper {
            margin-left: 80px;
        }


        /* Top Navbar */
        .main-header {
            height: 64px;
            background: white;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }


        .navbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
            max-width: 600px;
        }


        .search-wrapper {
            position: relative;
            flex: 1;
        }


        .search-wrapper .material-symbols-outlined {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-text);
            font-size: 18px;
        }


        .search-input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: none;
            background: var(--background-light);
            border-radius: 0.5rem;
            font-size: 0.875rem;
            outline: none;
            transition: all 0.2s ease;
        }


        .search-input:focus {
            box-shadow: 0 0 0 2px rgba(174, 21, 4, 0.2);
        }


        .navbar-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }


        .navbar-icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 0.5rem;
            border: none;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }


        .navbar-icon-btn:hover {
            background: var(--background-light);
        }


        .navbar-icon-btn .badge {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
            border: 2px solid white;
        }


        .navbar-divider {
            width: 1px;
            height: 24px;
            background: #e5e7eb;
            margin: 0 0.5rem;
        }


        .user-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem 0.25rem 0.25rem;
            border: none;
            background: transparent;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }


        .user-dropdown-btn:hover {
            background: var(--background-light);
        }


        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(174, 21, 4, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 0.75rem;
            font-weight: 700;
        }


        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }


        .user-name {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
        }


        /* Content */
        .content {
            flex: 1;
            padding: 2rem;
        }


        .content-header {
            margin-bottom: 2rem;
        }


        .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }


        .breadcrumb-item {
            color: var(--secondary-text);
        }


        .breadcrumb-item.active {
            color: #374151;
            font-weight: 500;
        }


        .breadcrumb-item + .breadcrumb-item::before {
            content: "/";
            color: var(--secondary-text);
        }


        .breadcrumb-item a {
            color: var(--secondary-text);
            text-decoration: none;
            transition: color 0.2s ease;
        }


        .breadcrumb-item a:hover {
            color: var(--primary);
        }


        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            color: #181111;
        }


        /* Footer */
        .main-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--border-color);
            background: white;
            margin-top: auto;
        }


        /* Cards */
        .card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }


        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: white;
        }


        .card-body {
            padding: 1.5rem;
        }


        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            border-radius: 0.5rem;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }


        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            color: white;
        }


        /* Dropdown Menu */
        .dropdown-menu {
            border-radius: 0.75rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
        }


        .dropdown-item {
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }


        .dropdown-item:hover {
            background: var(--background-light);
        }


        .dropdown-divider {
            margin: 0.5rem 0;
            border-color: var(--border-color);
        }


        /* Toggle Button */
        .sidebar-toggle-btn {
            width: 36px;
            height: 36px;
            border-radius: 0.5rem;
            border: none;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }


        .sidebar-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }


        /* Language Dropdown */
        .lang-btn {
            background: transparent;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }


        .lang-btn:hover {
            background: var(--background-light);
            border-color: #d1d5db;
        }


        .lang-globe {
            width: 16px;
            height: 16px;
        }


        .lang-flag {
            width: 18px;
            height: 18px;
            object-fit: cover;
        }


        /* Responsive */
        @media (max-width: 768px) {
            .main-sidebar {
                transform: translateX(-100%);
            }


            .main-sidebar.show {
                transform: translateX(0);
            }


            .content-wrapper {
                margin-left: 0;
            }


            .main-header {
                padding: 0 1rem;
            }


            .content {
                padding: 1rem;
            }


            .user-name {
                display: none;
            }


            .search-wrapper {
                max-width: 200px;
            }
        }


        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }


        .sidebar-overlay.show {
            display: block;
        }


        @media (max-width: 768px) {
            .main-sidebar {
                z-index: 1001;
            }
        }
    </style>
</head>


<body>
    <div class="wrapper">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>


        <!-- Sidebar -->
        <aside class="main-sidebar" id="mainSidebar">
            <!-- Brand Logo -->
            <!-- Brand Logo -->
<a href="{{ route('owner.user-owner.dashboard') }}" class="brand-link">
    <!-- Logo untuk sidebar terbuka (horizontal) -->
    <img src="{{ asset('images/cava-logo2-gradient.png') }}"
         alt="Cavaa Logo"
         class="brand-image brand-image-full">


    <!-- Logo circular untuk sidebar collapse -->
    <img src="{{ asset('icons/cava-logo-red-gradient.png') }}"
         alt="Cavaa Logo"
         class="brand-image brand-image-collapsed">
</a>


            {{-- <!-- User Panel -->
            <div class="user-panel">
                <div class="image">
                    @if(auth('owner')->check() && auth('owner')->user()->image)
                        <img src="{{ asset('storage/' . auth('owner')->user()->image) }}" alt="User Image">
                    @else
                        <div class="user-placeholder">
                            <svg style="width: 22px; height: 22px;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="info">
                    <a href="{{ route('owner.user-owner.settings.index') }}">
                        @auth('owner')
                            {{ auth('owner')->user()->name }}
                        @else
                            User Owner
                        @endauth
                    </a>
                </div>
            </div> --}}


            <!-- Sidebar Navigation -->
            <nav class="sidebar-nav">
                @php
                    $isVerified = auth('owner')->check() && auth('owner')->user()->verification_status === 'approved';
                    $isActive = auth('owner')->check() && auth('owner')->user()->is_active;
                @endphp


                <!-- Main Section -->
                <div class="nav-section-title">{{ __('messages.owner.layout.dashboard') }}</div>
               
                <div class="nav-item">
                    <a href="{{ $isVerified && $isActive ? route('owner.user-owner.dashboard') : 'javascript:void(0)' }}"
                       class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} @if(Route::is('owner.user-owner.dashboard')) active @endif"
                       onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : '' }}">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span>{{ __('messages.owner.layout.dashboard') }}</span>
                    </a>
                </div>




                <!-- Management Section -->
                <div class="nav-section-title">{{ __('messages.owner.layout.user_management') }}</div>


                @php
                    $employeeRoutes = ['owner.user-owner.employees.*'];
                @endphp


                <div class="nav-item {{ Route::is($employeeRoutes) ? 'menu-open' : '' }}">
                    <a href="javascript:void(0)"
                       class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} {{ Route::is($employeeRoutes) ? 'active' : '' }}"
                       onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : 'toggleSubmenu(this)' }}">
                        <span class="material-symbols-outlined">group</span>
                        <span>{{ __('messages.owner.layout.user_management') }}</span>
                        @if($isVerified)
                            <span class="material-symbols-outlined expand-icon">expand_more</span>
                        @endif
                    </a>
                    <div class="nav-treeview {{ !$isVerified || !$isActive ? 'disabled' : '' }}">
                        <div class="nav-item">
                            <a href="{{ route('owner.user-owner.employees.index') }}"
                               class="nav-link {{ Route::is('owner.user-owner.employees.*') ? 'active' : '' }}">
                                <span>{{ __('messages.owner.layout.employees') }}</span>
                            </a>
                        </div>
                    </div>
                </div>


                <div class="nav-item {{ Request::segment(3) == 'xen_platform' ? 'menu-open' : '' }}">
                    <a href="javascript:void(0)"
                       class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }}"
                       onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : 'toggleSubmenu(this)' }}">
                        <span class="material-symbols-outlined">dns</span>
                        <span>XenPlatform</span>
                        @if($isVerified)
                            <span class="material-symbols-outlined expand-icon">expand_more</span>
                        @endif
                    </a>
                    <div class="nav-treeview {{ !$isVerified || !$isActive ? 'disabled' : '' }}">
                        <div class="nav-item">
                            <a href="{{ $isVerified ? route('owner.user-owner.xen_platform.accounts.information') : 'javascript:void(0)' }}"
                               class="nav-link {{ Request::segment(4) == 'accounts' ? 'active' : '' }}">
                                <span>{{ __('messages.owner.layout.accounts') }}</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a href="{{ $isVerified ? route('owner.user-owner.xen_platform.split-payment.index') : 'javascript:void(0)' }}"
                               class="nav-link {{ Request::segment(4) == 'split-payment' ? 'active' : '' }}">
                                <span>{{ __('messages.owner.layout.split_payments') }}</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a href="{{ $isVerified ? route('owner.user-owner.xen_platform.payout.index') : 'javascript:void(0)' }}"
                               class="nav-link {{ Request::segment(4) == 'payout' ? 'active' : '' }}">
                                <span>{{ __('messages.owner.layout.withdrawal') }}</span>
                            </a>
                        </div>
                    </div>
                </div>


                @php
                    $outletRoutes = ['owner.user-owner.outlets.*'];
                @endphp


                <div class="nav-item {{ Route::is($outletRoutes) ? 'menu-open' : '' }}">
                    <a href="javascript:void(0)"
                       class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} {{ Route::is($outletRoutes) ? 'active' : '' }}"
                       onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : 'toggleSubmenu(this)' }}">
                        <span class="material-symbols-outlined">storefront</span>
                        <span>{{ __('messages.owner.layout.outlets') }}</span>
                        @if($isVerified)
                            <span class="material-symbols-outlined expand-icon">expand_more</span>
                        @endif
                    </a>
                    <div class="nav-treeview {{ !$isVerified || !$isActive ? 'disabled' : '' }}">
                        <div class="nav-item">
                            <a href="{{ route('owner.user-owner.outlets.index') }}"
                               class="nav-link {{ Route::is('owner.user-owner.outlets.*') ? 'active' : '' }}">
<span>{{ __('messages.owner.layout.all_outlets') }}</span>
</a>
</div>
</div>
</div>
@php
                $allProductRoutes = [
                    'owner.user-owner.products.*',
                    'owner.user-owner.categories.*',
                    'owner.user-owner.promotions.*',
                    'owner.user-owner.stocks.*',
                    'owner.user-owner.master-products.*',
                    'owner.user-owner.outlet-products.*'
                ];
            @endphp


            <div class="nav-item {{ Route::is($allProductRoutes) ? 'menu-open' : '' }}">
                <a href="javascript:void(0)"
                   class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} {{ Route::is($allProductRoutes) ? 'active' : '' }}"
                   onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : 'toggleSubmenu(this)' }}">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <span>{{ __('messages.owner.layout.products') }}</span>
                    @if($isVerified)
                        <span class="material-symbols-outlined expand-icon">expand_more</span>
                    @endif
                </a>
                <div class="nav-treeview {{ !$isVerified || !$isActive ? 'disabled' : '' }}">
                    <div class="nav-item">
                        <a href="{{ route('owner.user-owner.master-products.index') }}"
                           class="nav-link {{ Route::is('owner.user-owner.master-products.*') ? 'active' : '' }}">
                            <span>{{ __('messages.owner.layout.master_products') }}</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('owner.user-owner.outlet-products.index') }}"
                           class="nav-link {{ Route::is('owner.user-owner.outlet-products.*') ? 'active' : '' }}">
                            <span>{{ __('messages.owner.layout.outlet_products') }}</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('owner.user-owner.stocks.index') }}"
                           class="nav-link {{ Route::is('owner.user-owner.stocks.*') ? 'active' : '' }}">
                            <span>{{ __('messages.owner.layout.stocks') }}</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('owner.user-owner.categories.index') }}"
                           class="nav-link {{ Route::is('owner.user-owner.categories.*') ? 'active' : '' }}">
                            <span>{{ __('messages.owner.layout.categories') }}</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('owner.user-owner.promotions.index') }}"
                           class="nav-link {{ Route::is('owner.user-owner.promotions.*') ? 'active' : '' }}">
                            <span>{{ __('messages.owner.layout.promotions') }}</span>
                        </a>
                    </div>
                </div>
            </div>


            @php
                $settingRoutes = ['owner.user-owner.settings.*'];
            @endphp


            <div class="nav-item">
                <a href="{{ route('owner.user-owner.settings.index') }}"
                   class="nav-link {{ Route::is('owner.user-owner.settings.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined">settings</span>
                    <span>{{ __('messages.owner.layout.settings') }}</span>
                </a>
            </div>


            {{-- <div class="nav-item">
                <a href="#" class="nav-link">
                    <span class="material-symbols-outlined">help</span>
                    <span>{{ __('messages.owner.layout.support') }}</span>
                </a>
            </div> --}}


            <!-- Reports Section -->
            <div class="nav-section-title">{{ __('messages.owner.layout.reports') }}</div>


            <div class="nav-item">
                <a href="{{ $isVerified && $isActive ? route('owner.user-owner.report.sales.index') : 'javascript:void(0)' }}"
                   class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} {{ Route::is('owner.user-owner.report.sales.*') ? 'active' : '' }}"
                   onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : '' }}">
                    <span class="material-symbols-outlined">payments</span>
                    <span>{{ __('messages.owner.layout.sales_report') }}</span>
                </a>
            </div>


            <div class="nav-item">
                <a href="{{ $isVerified && $isActive ? route('owner.user-owner.report.stocks.index') : 'javascript:void(0)' }}"
                   class="nav-link {{ !$isVerified || !$isActive ? 'disabled-link' : '' }} {{ Route::is('owner.user-owner.report.stocks.*') ? 'active' : '' }}"
                   onclick="{{ !$isVerified ? 'showVerificationAlert(event)' : '' }}">
                    <span class="material-symbols-outlined">receipt_long</span>
                    <span>{{ __('messages.owner.layout.stock_report') }}</span>
                </a>
            </div>
        </nav>
    </aside>


    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Top Navbar -->
        <header class="main-header">
            <div class="navbar-left">
                {{-- <button class="sidebar-toggle-btn d-md-none" onclick="toggleSidebar()" style="color: var(--primary)">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <div class="search-wrapper">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" class="search-input" placeholder="Search orders, products, or customers...">
                </div> --}}
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
                            <img src="{{ asset('icons/icon-indonesia-96.png') }}" alt="Indonesia" class="lang-flag mr-2">
                            Bahasa
                            @if(app()->getLocale() === 'id')
                                <i class="fas fa-check ml-auto" style="color: var(--primary)"></i>
                            @endif
                        </a>
                        <a class="dropdown-item" href="{{ route('language.set.get', ['locale' => 'en']) }}">
                            <img src="{{ asset('icons/icon-english-96.png') }}" alt="English" class="lang-flag mr-2">
                            English
                            @if(app()->getLocale() === 'en')
                                <i class="fas fa-check ml-auto" style="color: var(--primary)"></i>
                            @endif
                        </a>
                    </div>
                </div>


                <!-- Notifications -->
                <div class="dropdown">
                    <button class="navbar-icon-btn" type="button" data-toggle="dropdown">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="badge"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" style="min-width: 300px;">
                        <div class="dropdown-header">15 Notifications</div>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> 4 new messages
                            <span class="float-right text-muted text-sm">3 mins</span>
                        </a>
                    </div>
                </div>


                <button class="navbar-icon-btn" onclick="toggleFullscreen()">
                    <span class="material-symbols-outlined">fullscreen</span>
                </button>


                <div class="navbar-divider"></div>


                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="user-dropdown-btn" type="button" data-toggle="dropdown">
                        <div class="user-avatar">
                            @if(auth('owner')->check() && auth('owner')->user()->image)
                                <img src="{{ asset('storage/' . auth('owner')->user()->image) }}" alt="User">
                            @else
                                OP
                            @endif
                        </div>
                        <span class="user-name">
                            @auth('owner')
                                {{ auth('owner')->user()->name }}
                            @else
                                Owner Panel
                            @endauth
                        </span>
                        <span class="material-symbols-outlined" style="font-size: 16px">expand_more</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="{{ route('owner.user-owner.settings.index') }}">
                            <i class="fas fa-user mr-2"></i>
                            {{ __('messages.owner.layout.profile') }}
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('owner.logout') }}">
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


        {{-- <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('owner.user-owner.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">@yield('page_title', 'Dashboard')</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section> --}}


        <!-- Main Content -->
        <section class="content">
            @yield('content')
        </section>


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
<script src="{{ asset('admin/app-assets/vendors/js/pickers/daterange/moment.min.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/pickers/daterange/daterangepicker.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
<script src="{{ asset('admin/assets/js/blockui-loader.js') }}"></script>


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
    document.getElementById('sidebarOverlay').addEventListener('click', function() {
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


    // Verification Alert
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
            confirmButtonColor: '#ae1504',
            allowOutsideClick: true,
        });


        return false;
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


        @if(session('swal_error'))
            Swal.fire({
                icon: 'warning',
                title: @json(session('swal_error.title')),
                text: @json(session('swal_error.text')),
                confirmButtonColor: '#ae1504',
            });
        @endif
    });
</script>


@yield('scripts')
@yield('modal')
@stack('scripts')