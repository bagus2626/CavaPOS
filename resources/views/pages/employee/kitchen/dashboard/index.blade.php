<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Kitchen Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Styles -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=info" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: '#b91c1c',
                        'primary-dark': '#991b1b',
                        'primary-hover': '#dc2626',
                        'background-light': '#f9fafb',
                        'background-dark': '#111827',
                        'card-light': '#ffffff',
                        'card-dark': '#1f2937',
                        'pink': '#FFD9D9',
                        'text-light': '#1f2937',
                        'text-dark': '#f9fafb',
                        'text-secondary-light': '#6b7280',
                        'text-secondary-dark': '#9ca3af',
                        'border-light': '#e5e7eb',
                        'border-dark': '#374151',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    fontWeight: {
                        'medium': 500,
                        'semibold': 600,
                        'bold': 700,
                    },
                    borderRadius: {
                        DEFAULT: '0.75rem',
                    },
                    boxShadow: {
                        'card': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                        'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                        'elegant': '0 8px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 10px -2px rgba(0, 0, 0, 0.04)',
                    },
                    width: {
                        '120': '28rem',
                    },
                    screens: {
                        'xs': '475px',
                        'sm': '640px',
                        'md': '768px',
                        'lg': '1024px',
                        'xl': '1280px',
                    }
                },
            },
        };
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans">
    <div class="flex flex-col lg:flex-row min-h-screen">
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 order-2 lg:order-1">
            <!-- Header -->
            @include('pages.employee.kitchen.dashboard.header-new')
            
            <!-- Mobile Floating Button -->
        <button 
        id="mobileSidebarToggle" 
        class="lg:hidden fixed bottom-6 right-6 bg-primary hover:bg-primary-hover text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg z-[60] transition-all duration-300">
            <span class="material-icons">menu</span>
            <!-- Notification Badge -->
            <span id="pendingOrdersBadge" class="hidden absolute -top-1 -right-1 bg-yellow-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center shadow-md animate-pulse">
                0
            </span>
        </button>
            
            <!-- Main Content -->
            <main class="flex-1 p-4 md:p-6 space-y-4 md:space-y-6 min-w-0">
                <!-- Status Cards -->
                <!-- Status Cards -->
                    <div class="grid grid-cols-3 gap-2 md:gap-4 lg:gap-6">
                        <div class="bg-pink dark:bg-card-dark p-3 md:p-4 lg:p-6 rounded-xl shadow-elegant border border-white/20">
                        <div>
                            <p class="text-pink text-primary font-bold font-display dark:text-text-secondary-dark text-xs md:text-sm lg:text-base tracking-wide">WAITING</p>
<p class="text-2xl md:text-3xl lg:text-4xl font-bold text-primary font-display" id="waitingCount">0</p>
                        </div>
                    </div>
                    <div class="bg-card-light dark:bg-card-dark p-4 md:p-6 rounded-xl shadow-elegant border border-border-light/50 dark:border-border-dark/50">
                        <div>
                            <p class="text-text-secondary-light dark:text-text-secondary-dark text-xs md:text-sm lg:text-base font-medium tracking-wide">COOKING</p>
<p class="text-2xl md:text-3xl lg:text-4xl font-bold text-text-light dark:text-text-dark font-display" id="cookingCount">0</p>
                        </div>
                    </div>
                    <div class="bg-card-light dark:bg-card-dark p-4 md:p-6 rounded-xl shadow-elegant border border-border-light/50 dark:border-border-dark/50">
                        <div>
                            <p class="text-text-secondary-light dark:text-text-secondary-dark text-xs md:text-sm lg:text-base font-medium tracking-wide">SERVED</p>
<p class="text-2xl md:text-3xl lg:text-4xl font-bold text-text-light dark:text-text-dark font-display" id="completedCount">0</p>
                        </div>
                    </div>
                </div>

                <!-- Active Orders Section -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <h2 class="text-lg md:text-xl font-semibold text-text-light dark:text-text-dark">
                        Active Orders 
                        <span class="text-sm md:text-base text-text-secondary-light dark:text-text-secondary-dark ml-2" id="activeOrdersCount">
                            0 orders cooking
                        </span>
                    </h2>
                    {{-- <button class="flex items-center space-x-2 text-primary hover:text-primary-hover text-sm md:text-base" onclick="window.kitchenDashboard.loadAllData()">
                        <span class="material-icons text-lg">refresh</span>
                        <span>Refresh</span>
                    </button> --}}
                </div>

                <!-- Active Orders Grid -->
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4 lg:gap-6" id="activeOrders">
                    <div class="col-span-full flex flex-col items-center justify-center text-center py-8">
                        <div class="text-4xl mb-4">👨‍🍳</div>
                        <div class="text-texta-secondary-light dark:text-text-secondary-dark text-lg">Loading active orders...</div>
                    </div>
                </div>
            </main>
        </div>
        
        <!-- Desktop Sidebar -->
        <aside class="hidden lg:block w-120 bg-card-light dark:bg-card-dark border-l border-border-light dark:border-border-dark p-6 flex-col sticky top-0 h-screen flex-shrink-0 order-2">
            <!-- Order Queue Section -->
            <div class="flex flex-col h-1/2 min-h-0">
                @include('pages.employee.kitchen.dashboard.order-queue-new')
            </div>
            
            <!-- Divider -->
            <div class="flex-shrink-0 border-t border-border-light dark:border-border-dark my-4"></div>
            
            <!-- Served Orders Section -->
            <div class="flex flex-col h-1/2 min-h-0">
                @include('pages.employee.kitchen.dashboard.served-orders-new')
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div 
            id="mobileSidebarOverlay" 
            class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-[45] hidden transition-opacity duration-300"
        ></div>

        <!-- Mobile Sidebar -->
        <aside 
            id="mobileSidebar" 
            class="lg:hidden fixed right-0 top-0 h-full w-full sm:w-96 bg-card-light dark:bg-card-dark shadow-2xl z-[50] transform translate-x-full transition-transform duration-300 flex flex-col"
        >
            <!-- Mobile Sidebar Header -->
            <div class="flex items-center justify-between p-4 border-b border-border-light dark:border-border-dark flex-shrink-0">
                <h2 class="text-lg font-bold text-text-light dark:text-text-dark typography-heading">Orders</h2>
                <button 
                    id="closeMobileSidebar"
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
                >
                    <span class="material-icons">close</span>
                </button>
            </div>

            <!-- Tab Navigation -->
            <div class="flex border-b border-border-light dark:border-border-dark flex-shrink-0">
                <button 
                    id="queueTabBtn"
                    class="flex-1 py-3 px-4 text-sm font-semibold text-primary border-b-2 border-primary transition-colors"
                >
                    Order Queue
                    <span id="queueTabCount" class="ml-1 text-xs">(0)</span>
                </button>
                <button 
                    id="servedTabBtn"
                    class="flex-1 py-3 px-4 text-sm font-semibold text-text-secondary-light dark:text-text-secondary-dark border-b-2 border-transparent transition-colors"
                >
                    Served Orders
                    <span id="servedTabCount" class="ml-1 text-xs">(0)</span>
                </button>
            </div>

            <!-- Tab Content -->
            <div class="flex-1 overflow-hidden">
                <!-- Queue Tab -->
                <div id="queueTabContent" class="h-full p-4">
                    <div class="flex flex-col h-full">
                        <div class="relative mb-4 flex-shrink-0">
                            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary-light dark:text-text-secondary-dark text-sm">search</span>
                            <input 
                                class="w-full pl-10 pr-4 py-2 bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark rounded-xl focus:ring-primary focus:border-primary text-text-light dark:text-text-dark typography-enhanced" 
                                placeholder="Search orders" 
                                type="text" 
                                id="mobileQueueSearch"
                                autocomplete="off"
                            />
                        </div>
                        <div class="flex-1 overflow-y-auto custom-scrollbar" id="mobileOrderQueue">
                            <div class="text-center text-text-secondary-light dark:text-text-secondary-dark py-8">
                                <div class="text-lg mb-2">🔭</div>
                                <div>Loading orders...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Served Tab -->
                <div id="servedTabContent" class="h-full p-4 hidden">
                    <div class="flex flex-col h-full">
                        <div class="mb-4 flex-shrink-0">
                            <input 
                                type="date" 
                                id="mobileDatePicker" 
                                class="w-full bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark rounded-xl px-3 py-2 text-sm focus:ring-primary focus:border-primary transition-colors typography-enhanced"
                                max="<?php echo date('Y-m-d'); ?>"
                                onchange="window.kitchenDashboard.applyDateFilter(this.value)"
                            >
                        </div>
                        <div class="flex-1 overflow-y-auto custom-scrollbar" id="mobileServedOrders">
                            <div class="text-center text-text-secondary-light dark:text-text-secondary-dark py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500 mx-auto mb-2"></div>
                                <div>Loading served orders...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Notification Container -->
    <div id="notificationContainer"></div>
    
    <!-- Modals -->
    @include('pages.employee.kitchen.dashboard.modals')
    
    <!-- JavaScript -->
    @include('pages.employee.kitchen.dashboard.scripts')
</body>
</html>