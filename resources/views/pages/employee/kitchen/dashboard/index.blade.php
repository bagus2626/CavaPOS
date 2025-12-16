<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Kitchen Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Performance Optimization - Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="{{ url('/') }}">
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="{{ url('/') }}">

    <!-- Styles with display=swap for better performance -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=info&display=swap" />
    
    <!-- Critical CSS Inline -->
    <style>
        /* Critical CSS untuk First Paint - Mobile Optimized */
        body { 
            margin: 0; 
            padding: 0; 
            font-family: Inter, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .bg-background-light { background-color: #f5f5f5; }
        .bg-background-dark { background-color: #111827; }
        .bg-card-light { background-color: #ffffff; }
        .bg-card-dark { background-color: #1f2937; }
        .flex { display: flex; }
        .h-screen { height: 100vh; }
        .overflow-hidden { overflow: hidden; }
        
        /* Scrollbar styles */
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
            -webkit-overflow-scrolling: touch;
        }
        
        .scrollbar-thin::-webkit-scrollbar { width: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { 
            background: #f1f1f1;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb { 
            background: #c1c1c1;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Reduce animations on mobile for performance */
        @media (max-width: 768px) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            
            /* Reduce expensive CSS on mobile */
            .shadow-sm, .shadow, .shadow-md, .shadow-lg, .shadow-xl {
                box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
            }
        }
    </style>

    <!-- Preload Tailwind -->
    <link rel="preload" href="https://cdn.tailwindcss.com?plugins=forms,typography" as="script">
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
                        'background-light': '#f5f5f5',
                        'background-dark': '#111827',
                        'card-light': '#ffffff',
                        'card-dark': '#1f2937',
                        'text-light': '#1f2937',
                        'text-dark': '#f9fafb',
                        'text-secondary-light': '#6b7280',
                        'text-secondary-dark': '#9ca3af',
                        'border-light': '#e5e7eb',
                        'border-dark': '#374151',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    borderRadius: {
                        DEFAULT: '0.75rem',
                    },
                    width: {
                        '120': '28rem',
                    }
                },
            },
        };
    </script>
</head>

<body class="bg-background-light dark:bg-background-dark font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            @include('pages.employee.kitchen.dashboard.header-new')

            <!-- Mobile Floating Button -->
            <button id="mobileSidebarToggle"
                class="lg:hidden fixed bottom-4 right-4 md:bottom-6 md:right-6 bg-primary hover:bg-primary-hover text-white rounded-full w-12 h-12 md:w-14 md:h-14 flex items-center justify-center shadow-lg z-[60] transition-all">
                <span class="material-icons text-xl md:text-2xl">menu</span>
                <span id="pendingOrdersBadge" class="hidden absolute -top-1 -right-1 bg-yellow-500 text-white text-xs font-bold rounded-full w-5 h-5 md:w-6 md:h-6 flex items-center justify-center">0</span>
            </button>

            <!-- Main Content -->
            <main class="flex-1 p-3 md:p-4 lg:p-6 overflow-auto pb-20 lg:pb-6">
                
                <!-- Status Cards -->
                <div class="grid grid-cols-3 gap-2 md:gap-3 lg:gap-4 mb-4 md:mb-5 lg:mb-6">
                    
                    <!-- Status cards  -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg lg:rounded-xl shadow border border-gray-200 dark:border-gray-700 p-2.5 md:p-3 lg:p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white" id="waitingCount">0</p>
                                <p class="text-xs font-semibold text-red-500 uppercase">Waiting</p>
                            </div>
                            <span class="material-icons text-red-500 text-2xl">schedule</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg lg:rounded-xl shadow border border-gray-200 dark:border-gray-700 p-2.5 md:p-3 lg:p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white" id="cookingCount">0</p>
                                <p class="text-xs font-semibold text-blue-500 uppercase">Cooking</p>
                            </div>
                            <span class="material-icons text-blue-500 text-2xl">restaurant</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg lg:rounded-xl shadow border border-gray-200 dark:border-gray-700 p-2.5 md:p-3 lg:p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white" id="completedCount">0</p>
                                <p class="text-xs font-semibold text-green-500 uppercase">Served</p>
                            </div>
                            <span class="material-icons text-green-500 text-2xl">check_circle</span>
                        </div>
                    </div>
                </div>

                <!-- Active Orders --> 
                <div class="mb-4 md:mb-5 lg:mb-6">
                    <h2 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 dark:text-white mb-3 md:mb-4">
                        Active Orders
                    </h2> 

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 lg:gap-5" id="activeOrders">
                        <!-- Container kosong, akan diisi oleh JavaScript -->
                    </div>
                </div>
            </main>
        </div>

        <!-- Desktop Sidebar -->
        <aside class="hidden lg:flex w-120 bg-card-light dark:bg-card-dark border-l border-border-light dark:border-border-dark flex-col h-screen sticky top-0">
            <div class="flex-1 min-h-0 border-b border-border-light dark:border-border-dark">
                @include('pages.employee.kitchen.dashboard.order-queue-new')
            </div>
            <div class="flex-1 min-h-0">
                @include('pages.employee.kitchen.dashboard.served-orders-new')
            </div>
        </aside>

       <!-- Mobile Sidebar -->
<div id="mobileSidebarOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-[45] hidden"></div>

<aside id="mobileSidebar" class="lg:hidden fixed right-0 top-0 h-full w-full max-w-sm bg-card-light dark:bg-card-dark shadow-xl z-[50] transform translate-x-full transition-transform flex flex-col">
    <!-- Header -->
    <div class="p-4 border-b border-border-light dark:border-border-dark bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between mb-3">
            {{-- <h2 class="text-base font-bold text-text-light dark:text-text-dark">Orders</h2> --}}
            <button id="closeMobileSidebar" class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <span class="material-icons text-xl">close</span>
            </button>
        </div>
        
        <!-- Search Bar -->
        <div class="relative">
            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary-light dark:text-text-secondary-dark text-sm">search</span>
            <input 
                class="w-full pl-10 pr-4 py-2.5 bg-background-light dark:bg-gray-700 border border-border-light dark:border-border-dark rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm placeholder-gray-400 dark:placeholder-gray-500" 
                placeholder="Search by name or code" 
                type="text" 
                id="mobileQueueSearch"
                autocomplete="off"
            />
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-border-light dark:border-border-dark bg-white dark:bg-gray-800">
        <button id="queueTabBtn" class="flex-1 py-3 px-2 text-sm font-semibold text-primary border-b-2 border-primary transition-colors">
            Order Queue <span id="queueTabCount" class="ml-1 text-xs">(0)</span>
        </button>
        <button id="servedTabBtn" class="flex-1 py-3 px-2 text-sm font-semibold text-text-secondary-light dark:text-text-secondary-dark border-b-2 border-transparent transition-colors">
            Served Orders <span id="servedTabCount" class="ml-1 text-xs">(0)</span>
        </button>
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-hidden bg-background-light dark:bg-background-dark">
        <!-- Queue Tab Content Rafi -->
        <div id="queueTabContent" class="h-full p-3">
            <div class="flex flex-col h-full">
                <div class="flex-1 overflow-y-auto scrollbar-thin" id="mobileOrderQueue">
                    <!-- Container kosong, akan diisi oleh JavaScript -->
                </div>
            </div>
        </div>

        <!-- Served Tab Content Rafi -->
        <div id="servedTabContent" class="h-full p-3 hidden">
            <div class="flex flex-col h-full">
                <div class="mb-3">
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base">calendar_today</span>
                        <input 
                            type="date" 
                            id="mobileDatePicker" 
                            class="w-full pl-10 pr-3 py-2.5 bg-white dark:bg-gray-800 border border-border-light dark:border-border-dark rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary" 
                            max="<?php echo date('Y-m-d'); ?>"
                        />
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto scrollbar-thin" id="mobileServedOrders">
                    <!-- Container kosong, akan diisi oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>
</aside>

    <!-- Modals & Scripts -->
    @include('pages.employee.kitchen.dashboard.modals')
    @include('pages.employee.kitchen.dashboard.scripts')
    
    <!-- Load Alpine.js dengan defer untuk mobile -->
    <script>
        if (window.innerWidth < 768) {
            setTimeout(() => {
                const script = document.createElement('script');
                script.src = '//unpkg.com/alpinejs';
                script.defer = true;
                document.body.appendChild(script);
            }, 1000);
        } else {
            document.write('<script src="//unpkg.com/alpinejs" defer><\/script>');
        }
    </script>

    <!-- Service Worker Registration for Mobile -->
    <script>
        if ('serviceWorker' in navigator && window.innerWidth < 768) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .catch(err => console.log('SW registration failed'));
            });
        }
    </script>
</body>
</html>