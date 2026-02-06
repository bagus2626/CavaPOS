@extends('layouts.customer')

@section('title', 'Menu ' . $partner->name)

@section('main-class', 'pt-0')

@section('content')
    {{-- Hero Background --}}
    <div class="relative w-full h-64 sm:h-72 overflow-hidden">
        @if ($partner->background_picture)
            <img src="{{ asset('storage/' . $partner->background_picture) }}" alt="{{ $partner->name }}"
                class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-purple-400 via-pink-400 to-orange-400"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-white to-transparent opacity-80 sm:opacity-40"></div>
    </div>

    {{-- Main Content --}}
    <div class="w-full px-0 relative bg-gray-50 min-h-screen">
        {{-- Partner Info Card --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-10 pb-6">
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    {{-- Logo & Info Section --}}
                    <div class="flex items-center gap-4">
                        {{-- Logo --}}
                        @if ($partner->logo)
                            <div class="flex-shrink-0">
                                <img src="{{ asset('storage/' . $partner->logo) }}" 
                                    alt="{{ $partner->name }}"
                                    class="h-16 w-16 md:h-20 md:w-20 object-contain rounded-md">
                            </div>
                        @endif
                        
                        {{-- Partner Name & Table Info --}}
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-[#ae1504] mb-2">
                                {{ $partner->name }}
                            </h1>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border" style="background-color: #fef2f2; color: #ae1504; border-color: #fecaca;">
                                    {{ __('messages.customer.menu.table') }} {{ $table->table_no }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                    {{ $table->table_class }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Search Bar --}}
                    <div class="w-full md:w-96">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input id="menuSearch" type="search"
                                placeholder="{{ __('messages.customer.menu.search_placeholder') }}"
                                class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl leading-5 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:border-red-600 sm:text-sm shadow-sm transition-shadow"
                                style="--tw-ring-color: #ae1504;"
                                autocomplete="off" />
                            <button type="button" id="menuSearchClear"
                                class="absolute right-3 top-1/2 -translate-y-1/2 hidden w-6 h-6 rounded hover:bg-gray-100 text-gray-500 flex items-center justify-center text-xl font-bold"
                                aria-label="Clear" title="Clear">×</button>
                        </div>
                    </div>
                </div>

                {{-- Category Tabs --}}
                <div class="mt-8">
                    <nav aria-label="Tabs" class="flex space-x-8 overflow-x-auto hide-scrollbar border-b border-gray-200">
                        <button class="filter-btn whitespace-nowrap py-4 px-1 font-medium text-sm text-gray-600 flex items-center gap-2 border-b-2 border-transparent transition-all active" data-category="all">
                            {{ __('messages.customer.menu.all') }}
                        </button>
                        @foreach ($categories as $category)
                            <button class="filter-btn whitespace-nowrap py-4 px-1 font-medium text-sm text-gray-600 flex items-center gap-2 border-b-2 border-transparent transition-all"
                                data-category="{{ $category->id }}">
                                {{ $category->category_name }}
                            </button>
                        @endforeach
                    </nav>
                </div>
            </div>
        </div>

        {{-- Menu List --}}
        @php
            $productsByCategory = $partner_products
                ->sortBy(function ($product) {
                    return $product->category->category_order ?? 99999;
                })
                ->groupBy('category_id');
            $hotProducts = $partner_products->filter(function ($p) {
                return $p->is_hot_product;
            });
        @endphp

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20" id="menu-container">
            {{-- Hot Products Section --}}
            @if($hotProducts->count())
                <div class="hot-products-group mb-10">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-6">
                        <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('messages.customer.menu.hot_products') }}
                    </h2>

                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($hotProducts as $product)
                            @php
                                $firstImage = $product->pictures[0]['path'] ?? null;
                                $promo = $product->promotion;
                                $basePrice = (float) $product->price;
                                $discountedBase = $basePrice;
                                $hasPromo = false;

                                if ($promo) {
                                    if ($promo->promotion_type === 'percentage') {
                                        $discountedBase = max(0, $basePrice * (1 - $promo->promotion_value / 100));
                                    } else {
                                        $discountedBase = max(0, $basePrice - (float) $promo->promotion_value);
                                    }
                                    $hasPromo = $discountedBase < $basePrice;
                                }

                                $promoBadge = null;
                                if ($promo) {
                                    if ($promo->promotion_type === 'percentage') {
                                        $value = rtrim(rtrim(number_format($promo->promotion_value, 2, ',', '.'), '0'), ',') . '%';
                                    } else {
                                        $value = 'Rp' . number_format($promo->promotion_value, 0, ',', '.');
                                    }

                                    $promoBadge = __('messages.customer.menu.promo.off', ['value' => $value]);
                                }
                            @endphp

                            <div class="menu-item menu-item-hot group bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-lg hover:border-choco/20 transition-all flex flex-col h-full relative cursor-pointer
                                {{ $product->quantity_available < 1 && $product->always_available_flag == false ? 'grayscale opacity-75 cursor-not-allowed' : '' }}"
                                data-category="{{ $product->category_id }}"
                                data-product-id="{{ $product->id }}">
                                
                                {{-- Product Image --}}
                                <div class="aspect-[4/3] bg-gradient-to-br from-orange-50 to-orange-100 overflow-hidden relative">
                                    @if($product->quantity_available > 0 && $product->quantity_available <= 3 && $product->always_available_flag == false)
                                        <div class="absolute bottom-2 left-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-md flex items-center justify-center gap-1.5 shadow-sm border border-yellow-100 z-10">
                                            {{-- <div class="h-1.5 w-1.5 rounded-full bg-yellow-500 animate-pulse"></div> --}}
                                            <span class="text-[10px] font-bold text-yellow-700 uppercase tracking-wide">{{ __('messages.customer.menu.low_stock') }} 
                                                {{-- ({{ $product->quantity_available }}) --}}
                                            </span>
                                        </div>
                                    @endif

                                    @if($hasPromo && $promoBadge)
                                        <div class="promo-badge">
                                            {{ $promoBadge }}
                                        </div>
                                    @endif
                                    
                                    @if($firstImage)
                                        <img src="{{ asset($firstImage) }}" alt="{{ $product->name }}" 
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center text-orange-200 group-hover:scale-105 transition-transform duration-500">
                                            <svg class="w-14 h-14" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M2 21h19v-3H2v3zM20 8H4V4h16v4zm0 10H4v-6h16v6z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    @if($product->quantity_available < 1 && $product->always_available_flag == false)
                                        <div class="absolute inset-0 bg-white/40 flex items-center justify-center">
                                            <span class="bg-gray-800 text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-lg">
                                                {{ __('messages.customer.menu.sold_out') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Product Info --}}
                                <div class="flex-1 flex flex-col p-3">
                                    <h3 class="font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-choco transition-colors text-sm sm:text-base">
                                        {{ $product->name }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mb-2 sm:mb-3 line-clamp-2">{{ $product->description }}</p>
                                    
                                    <div class="mt-auto flex items-end justify-between gap-2">
                                        {{-- Price dengan max-width agar tidak terlalu panjang --}}
                                        <div class="flex-shrink min-w-0 max-w-[55%] sm:max-w-none">
                                            @if($hasPromo)
                                                <p class="text-[10px] sm:text-xs text-gray-500 line-through truncate">Rp {{ number_format($basePrice, 0, ',', '.') }}</p>
                                                <p class="font-bold text-gray-900 text-xs sm:text-base truncate">Rp {{ number_format($discountedBase, 0, ',', '.') }}</p>
                                            @else
                                                <p class="font-bold text-gray-900 text-xs sm:text-base truncate">Rp {{ number_format($basePrice, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                        
                                        {{-- Quantity Controls --}}
                                        <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                                            <button class="minus-btn hidden w-6 h-6 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl border border-[#ae1504] text-[#ae1504] flex items-center justify-center hover:bg-gray-100 transition-all"
                                                data-id="{{ $product->id }}">
                                                <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </button>
                                            <span class="qty hidden text-[11px] sm:text-sm font-bold text-gray-800 min-w-[1rem] sm:min-w-[1.5rem] text-center" data-id="{{ $product->id }}">0</span>
                                            @if ($product->quantity_available < 1 && $product->always_available_flag == false)
                                                <button class="h-6 w-6 sm:h-9 sm:w-9 rounded-lg sm:rounded-xl bg-gray-100 text-gray-300 flex items-center justify-center cursor-not-allowed border border-gray-200" disabled>
                                                    <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <button class="plus-btn h-6 w-6 sm:h-9 sm:w-9 rounded-lg sm:rounded-xl bg-[#ae1504] text-white flex items-center justify-center hover:bg-[#8a1103] shadow-md hover:shadow-lg transition-all active:scale-95"
                                                    data-id="{{ $product->id }}">
                                                    <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Category Products --}}
            @foreach ($productsByCategory as $categoryId => $products)
                @if ($products->count() > 0)
                    @php
                        $categoryName = $categories->firstWhere('id', $categoryId)->category_name ?? 'Uncategorized';
                        $productsOnCategory = $products->count();
                    @endphp

                    <div class="category-group mb-10" data-category="{{ $categoryId }}">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900">
                                {{ $categoryName }} 
                                <span class="text-sm font-normal text-gray-500">({{ $productsOnCategory }})</span>
                            </h2>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach ($products as $product)
                                @php
                                    $firstImage = $product->pictures[0]['path'] ?? null;
                                    $promo = $product->promotion;
                                    $basePrice = (float) $product->price;
                                    $hasPromo = false;
                                    $discountedBase = $basePrice;

                                    if ($promo) {
                                        if ($promo->promotion_type === 'percentage') {
                                            $discountedBase = max(0, $basePrice * (1 - $promo->promotion_value / 100));
                                        } else {
                                            $discountedBase = max(0, $basePrice - (float) $promo->promotion_value);
                                        }
                                        $hasPromo = $discountedBase < $basePrice;
                                    }

                                    $promoBadge = null;
                                    if ($promo) {
                                        if ($promo->promotion_type === 'percentage') {
                                            $value = rtrim(rtrim(number_format($promo->promotion_value, 2, ',', '.'), '0'), ',') . '%';
                                        } else {
                                            $value = 'Rp' . number_format($promo->promotion_value, 0, ',', '.');
                                        }

                                        $promoBadge = __('messages.customer.menu.promo.off', ['value' => $value]);
                                    }
                                @endphp

                                <div class="menu-item group bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-lg hover:border-choco/20 transition-all flex flex-col h-full relative cursor-pointer
                                    {{ $product->quantity_available < 1 && $product->always_available_flag == false ? 'grayscale opacity-75 cursor-not-allowed' : '' }}"
                                    data-category="{{ $product->category_id }}"
                                    data-product-id="{{ $product->id }}">
                                    
                                    {{-- Product Image --}}
                                    <div class="aspect-[4/3] bg-gray-50 overflow-hidden relative">
                                        @if($product->quantity_available > 0 && $product->quantity_available <= 3 && $product->always_available_flag == false)
                                            <div class="absolute bottom-2 left-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-md flex items-center justify-center gap-1.5 shadow-sm border border-yellow-100 z-10">
                                                {{-- <div class="h-1.5 w-1.5 rounded-full bg-yellow-500 animate-pulse"></div> --}}
                                                <span class="text-[10px] font-bold text-yellow-700 uppercase tracking-wide">{{ __('messages.customer.menu.low_stock') }} 
                                                    {{-- ({{ $product->quantity_available }}) --}}
                                                </span>
                                            </div>
                                        @endif

                                        @if($hasPromo && $promoBadge)
                                            <div class="promo-badge">
                                                {{ $promoBadge }}
                                            </div>
                                        @endif
                                        
                                        @if($firstImage)
                                            <img src="{{ asset($firstImage) }}" alt="{{ $product->name }}" 
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center text-gray-300 group-hover:scale-105 transition-transform duration-500">
                                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M2 21h19v-3H2v3zM20 8H4V4h16v4zm0 10H4v-6h16v6z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        @if($product->quantity_available < 1 && $product->always_available_flag == false)
                                            <div class="absolute inset-0 bg-white/40 flex items-center justify-center">
                                                <span class="bg-gray-800 text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-lg">Sold Out</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Product Info --}}
                                    <div class="flex-1 flex flex-col p-3">
                                        <h3 class="font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-choco transition-colors text-sm sm:text-base">
                                            {{ $product->name }}
                                        </h3>
                                        <p class="text-xs text-gray-500 mb-2 sm:mb-3 line-clamp-2">{{ $product->description }}</p>
                                        
                                        <div class="mt-auto flex items-end justify-between gap-2">
                                            {{-- Price dengan max-width agar tidak terlalu panjang --}}
                                            <div class="flex-shrink min-w-0 max-w-[55%] sm:max-w-none">
                                                @if($hasPromo)
                                                    <p class="text-[10px] sm:text-xs text-gray-500 line-through truncate">Rp {{ number_format($basePrice, 0, ',', '.') }}</p>
                                                    <p class="font-bold text-gray-900 text-xs sm:text-base truncate">Rp {{ number_format($discountedBase, 0, ',', '.') }}</p>
                                                @else
                                                    <p class="font-bold text-gray-900 text-xs sm:text-base truncate">Rp {{ number_format($basePrice, 0, ',', '.') }}</p>
                                                @endif
                                            </div>
                                            
                                            {{-- Quantity Controls --}}
                                            <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                                                <button class="minus-btn hidden w-6 h-6 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl border border-[#ae1504] text-[#ae1504] flex items-center justify-center hover:bg-gray-100 transition-all"
                                                    data-id="{{ $product->id }}">
                                                    <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                </button>
                                                <span class="qty hidden text-[11px] sm:text-sm font-bold text-gray-800 min-w-[1rem] sm:min-w-[1.5rem] text-center" data-id="{{ $product->id }}">0</span>
                                                @if ($product->quantity_available < 1 && $product->always_available_flag == false)
                                                    <button class="h-6 w-6 sm:h-9 sm:w-9 rounded-lg sm:rounded-xl bg-gray-100 text-gray-300 flex items-center justify-center cursor-not-allowed border border-gray-200" disabled>
                                                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                    </button>
                                                @else
                                                    <button class="plus-btn h-6 w-6 sm:h-9 sm:w-9 rounded-lg sm:rounded-xl bg-[#ae1504] text-white flex items-center justify-center hover:bg-[#8a1103] shadow-md hover:shadow-lg transition-all active:scale-95"
                                                        data-id="{{ $product->id }}">
                                                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    @include('pages.customer.menu.modal')
@endsection


    <style>
        /* sheet bisa di-scroll sendiri */
        #modalSheet {
            max-height: 80vh;
            /* atau pakai class Tailwind max-h-[80vh] */
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            /* cegah "scroll chaining" ke belakang */
        }

        /* saat modal terbuka, kunci scroll body */
        body.modal-open {
            overflow: hidden !important;
        }

        /* KOMPENSASI UNTUK DESKTOP - Lebih Spesifik */
        @media (min-width: 768px) {
            /* Body padding */
            body.modal-open {
                padding-right: var(--scrollbar-width, 0px) !important;
            }
            
            /* Floating cart bar */
            body.modal-open #floatingCartBar {
                right: var(--scrollbar-width, 0px);
            }
            
            /* Navbar - kompensasi dengan transform */
            body.modal-open #customer-navbar {
                transform: translateX(calc(var(--scrollbar-width, 0px) / -2));
            }
        }

        /* Hide scrollbar untuk category tabs */
        .hide-scrollbar {
            -ms-overflow-style: none;  
            scrollbar-width: none;  
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;  
        }

        /* Hide scrollbar for category filter */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Filter button style */
        .filter-btn {
            background-color: transparent !important;
            color: #4b5563; /* gray-600 */
            transition: all 0.2s;
            border-bottom: 2px solid transparent;
            border-radius: 0;
        }

        .filter-btn:hover {
            background-color: transparent !important;
            color: #ae1504;
        }

        .filter-btn.active {
            background-color: transparent !important;
            color: #ae1504; 
            border-bottom-color: #ae1504; 
        }

        /* animasi slide up modal */
        /* Modal overlay hitam */
        #parentOptionsModal {
            background-color: rgba(0, 0, 0, 0.4);
        }

        /* Sheet modal - Scale Animation (sama seperti checkout modal) */
        #modalSheet {
            transform: scale(0.95);
            opacity: 0;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
                        opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Saat modal ditampilkan */
        #parentOptionsModal.show #modalSheet {
            transform: scale(1);
            opacity: 1;
        }

        #floatingCartBar {
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* Cart Manager Modal Animation - Scale */
        #cartManagerSheet {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) scale(0.95);
            opacity: 0;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #cartManagerModal.show #cartManagerSheet {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        /* Hilangkan tombol clear bawaan browser */
        #menuSearch::-webkit-search-cancel-button {
            display: none;
        }

        /* (opsional) smooth look untuk area kategori */
        .category-bar::-webkit-scrollbar {
            height: 0;
        }

        /* Track kategori biar item tetap di satu baris */
        .category-track .filter-btn {
            flex: 0 0 auto;
            white-space: nowrap;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out forwards;
        }

       /* Promo Badge - Enhanced Design with Rounded Style */
        .promo-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 10;
            
            /* Gradient background untuk lebih eye-catching */
            background: linear-gradient(135deg, #ae1504 0%, #d41f0a 100%);
            
            /* Shadow yang lebih kuat untuk standout dari gambar */
            box-shadow: 
                0 2px 8px rgba(174, 21, 4, 0.4),
                0 0 0 2px rgba(255, 255, 255, 0.3);
            
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 6px 10px;
            border-radius: 20px; /* Changed from 8px to 20px untuk rounded penuh */
            
            /* Border putih untuk kontras */
            border: 1.5px solid rgba(255, 255, 255, 0.9);
            
            /* Backdrop blur untuk keterbacaan */
            backdrop-filter: blur(4px);
            
            /* Animation */
            animation: promo-pulse 2s ease-in-out infinite;
        }

        /* Pulse animation untuk menarik perhatian */
        @keyframes promo-pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 
                    0 2px 8px rgba(174, 21, 4, 0.4),
                    0 0 0 2px rgba(255, 255, 255, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 
                    0 4px 12px rgba(174, 21, 4, 0.6),
                    0 0 0 2px rgba(255, 255, 255, 0.5);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .promo-badge,{
                font-size: 9px;
                padding: 5px 8px;
            }
        }

    </style>

    @push('scripts')
        <script>
            window.__REORDER_ITEMS__    = @json($reorderItems ?? []);
            window.__REORDER_MESSAGES__ = @json($reorderMessages ?? []);
            window.__PARTNER_SLUG__     = @json($partner_slug ?? null);
            window.__TABLE_CODE__       = @json($table_code ?? null);
        </script>
        @php
            $productsData = $partner_products->map(function ($p) {
            $firstImage = $p->pictures[0]['path'] ?? null;
            $promo = $p->promotion;
            $base = (float) $p->price;
            $discBase = $base;

            if ($promo) {
                if ($promo->promotion_type === 'percentage') {
                $discBase = max(0, $base * (1 - ($promo->promotion_value / 100)));
                } else {
                $discBase = max(0, $base - (float) $promo->promotion_value);
                }
            }

            // Transform parent_options dengan accessor quantity_available
            $parentOptions = ($p->parent_options ?? collect())->map(function ($po) {
                return [
                'id' => $po->id,
                'name' => $po->name,
                'description' => $po->description,
                'provision' => $po->provision,
                'provision_value' => $po->provision_value,
                'options' => ($po->options ?? collect())->map(function ($opt) {
                    return [
                    'id' => $opt->id,
                    'name' => $opt->name,
                    'price' => (float) $opt->price,
                    'quantity_available' => $opt->quantity_available, // ← ACCESSOR dipanggil di sini
                    'always_available_flag' => (bool) $opt->always_available_flag,
                    'description' => $opt->description,
                    ];
                })->values()->all(),
                ];
            })->values()->all();

            return [
                'id' => $p->id,
                'name' => $p->name,
                'description' => strip_tags((string) $p->description),
                'price' => $base,
                'discounted_base' => $discBase,
                'promotion' => $promo ? [
                'id' => $promo->id,
                'type' => $promo->promotion_type,
                'value' => (float) $promo->promotion_value,
                ] : null,
                'image' => $firstImage ? asset($firstImage) : null,
                'parent_options' => $parentOptions, // ← Gunakan transformed data
                'quantity_available' => $p->quantity_available, 
                'always_available_flag' => (int) $p->always_available_flag,
            ];
            })->values()->toArray();
            @endphp

        <script src="{{ asset('js/customer/menu/index.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('parentOptionsModal');
                const modalContent = document.getElementById('modalContent');
                const modalHeader = document.getElementById('modalHeader');
                const closeModalBtn = document.getElementById('closeModalBtn');
                const saveModalBtn = document.getElementById('saveModalBtn');
                const productsData = @json($productsData);
                const noteTextarea = document.getElementById('modalNote');
                if (noteTextarea) {
                    noteTextarea.addEventListener('input', function() {
                        modalNote = this.value;
                    });
                }

                // Cart persistence configuration 
                const CART_EXPIRY_MINUTES = {{ config('session.lifetime', 120) }}; // Ubah sesuai kebutuhan

                function saveCartToStorage() {
                    try {
                        const cartData = {
                            items: cart,
                            lastKeyPerProduct: lastKeyPerProduct,
                            timestamp: Date.now()
                        };
                        localStorage.setItem('menuCart', JSON.stringify(cartData));
                    } catch (error) {
                        console.error('Error saving cart:', error);
                    }
                }

                function loadCartFromStorage() {
                    try {
                        const stored = localStorage.getItem('menuCart');
                        if (!stored) return null;

                        const cartData = JSON.parse(stored);
                        const now = Date.now();
                        const expiry = CART_EXPIRY_MINUTES * 60 * 1000;
                        
                        if (now - cartData.timestamp > expiry) {
                            localStorage.removeItem('menuCart');
                            return null;
                        }

                        return cartData;
                    } catch (error) {
                        console.error('Error loading cart:', error);
                        localStorage.removeItem('menuCart');
                        return null;
                    }
                }

                function clearCartFromStorage() {
                    try {
                        localStorage.removeItem('menuCart');
                    } catch (error) {
                        console.error('Error clearing cart:', error);
                    }
                }

                // === Body scroll lock (aman untuk iOS & tanpa animasi saat restore) ===
                let __savedScrollY = 0;
                let __prevScrollBehavior = '';
                let __scrollbarWidth = 0;

                // Hitung lebar scrollbar sekali saja
                function calculateScrollbarWidth() {
                    const outer = document.createElement('div');
                    outer.style.visibility = 'hidden';
                    outer.style.overflow = 'scroll';
                    outer.style.width = '100px';
                    document.body.appendChild(outer);
                    
                    const inner = document.createElement('div');
                    inner.style.width = '100%';
                    outer.appendChild(inner);
                    
                    __scrollbarWidth = outer.offsetWidth - inner.offsetWidth;
                    document.body.removeChild(outer);
                    
                    // Set CSS variable untuk digunakan di style
                    document.documentElement.style.setProperty('--scrollbar-width', `${__scrollbarWidth}px`);
                }

                function lockBodyScroll() {
                    __savedScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;

                    // Matikan smooth scroll sementara (kalau ada)
                    const html = document.documentElement;
                    __prevScrollBehavior = html.style.scrollBehavior;
                    html.style.scrollBehavior = 'auto';

                    // Tambahkan padding-right sebesar lebar scrollbar untuk mencegah layout shift
                    if (window.innerWidth >= 768 && __scrollbarWidth > 0) {
                        document.body.style.paddingRight = `${__scrollbarWidth}px`;
                    }
                    
                    document.body.classList.add('modal-open');
                    // Teknik "fixed body" supaya benar2 terkunci
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${__savedScrollY}px`;
                    document.body.style.left = '0';
                    document.body.style.right = '0';
                    document.body.style.width = '100%';
                }

                function unlockBodyScroll() {
                    const html = document.documentElement;

                    document.body.classList.remove('modal-open');
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.left = '';
                    document.body.style.right = '';
                    document.body.style.width = '';
                    document.body.style.paddingRight = '';

                    // Pastikan restore TANPA animasi
                    // Tunggu satu frame agar layout settle dulu
                    requestAnimationFrame(() => {
                        const y = __savedScrollY || 0;
                        window.scrollTo({
                            top: y,
                            left: 0,
                            behavior: 'auto'
                        });

                        // Kembalikan scroll-behavior di frame berikutnya
                        requestAnimationFrame(() => {
                            html.style.scrollBehavior = __prevScrollBehavior || '';
                        });
                    });
                }

                // Hitung scrollbar width saat load
                calculateScrollbarWidth();

                // Hitung ulang saat resize dengan debounce
                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        // Hanya hitung ulang jika tidak ada modal yang terbuka
                        if (!document.body.classList.contains('modal-open')) {
                            calculateScrollbarWidth();
                        }
                    }, 250);
                });

                // ===== STATE =====
                let currentProductId = null;
                let selectedOptions = [];
                let modalQty = 1; // <<=== jumlah qty di modal
                let modalNote = '';

                // Cart per kombinasi opsi:
                // key: `${productId}::${opt1-opt2-...}` (opsi diurut agar konsisten)
                // value: { productId: number, options: number[], qty: number }
                let cart = {};

                // Simpan "line item terakhir" per produk (agar tombol MINUS mengurangi yang paling baru ditambahkan)
                let lastKeyPerProduct = {};

                // ===== HELPERS =====
                const rupiahFmt = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                });

                function getProductDataById(pid) {
                    return productsData.find(p => p.id === pid) || {};
                }

                function addToCart(productId, optionsArr) {
                    const key = keyOf(productId, optionsArr);
                    const pd = getProductDataById(productId);
                    const promoId = pd.promotion?.id ?? null;

                    if (!cart[key]) {
                        cart[key] = {
                            productId,
                            options: (optionsArr || []).slice(),
                            qty: 0,
                            unitPrice: 0,
                            lineTotal: 0,
                            note: '',
                            promo_id: promoId, // <-- WAJIB
                        };
                    } else if (cart[key].promo_id == null) {
                        cart[key].promo_id = promoId;
                    }

                    cart[key].qty += 1;
                    recomputeLineTotal(key);
                    lastKeyPerProduct[productId] = key;
                    updateFloatingCartBar();
                    saveCartToStorage();
                    return key;
                }



                function computeUnitPrice(productId, optionsArr) {
                    const pd = getProductDataById(productId);
                    // pakai discounted_base kalau ada; fallback ke price
                    const base = Number(pd.discounted_base ?? pd.price) || 0;

                    let optSum = 0;
                    (pd.parent_options || []).forEach(po => {
                        (po.options || []).forEach(opt => {
                            if ((optionsArr || []).includes(opt.id)) {
                                optSum += Number(opt.price) || 0; // opsi tidak didiskon
                            }
                        });
                    });

                    return base + optSum;
                }


                function recomputeLineTotal(key) {
                    const row = cart[key];
                    if (!row) return;
                    row.unitPrice = computeUnitPrice(row.productId, row.options);
                    row.lineTotal = row.unitPrice * row.qty;
                }


                function provisionInfoText(provision, value) {
                    const prov = String(provision || '').toUpperCase();
                    const val = Number(value);
                    const hasN = Number.isFinite(val);
                    switch (prov) {
                        case 'EXACT':
                            return hasN ? `{{ __('messages.customer.menu.exact_provision') }} ${val}` :
                                '{{ __('messages.customer.menu.exact_provision') }}';
                        case 'MAX':
                            return hasN ? `{{ __('messages.customer.menu.max_provision') }} ${val}` :
                                '{{ __('messages.customer.menu.max_provision') }}';
                        case 'MIN':
                            return hasN ? `{{ __('messages.customer.menu.min_provision') }} ${val}` :
                                '{{ __('messages.customer.menu.min_provision') }}';
                        case 'OPTIONAL MAX':
                            return hasN ? `{{ __('messages.customer.menu.optional_max') }} ${val}` :
                                '{{ __('messages.customer.menu.optional_max') }}';
                        case 'OPTIONAL':
                            return '{{ __('messages.customer.menu.optional') }}';
                        default:
                            return '';
                    }
                }

                // Buat key kombinasi yang stabil (opsi diurut)
                function keyOf(productId, optionsArr) {
                    const opts = (optionsArr || []).slice().sort((a, b) => a - b).join('-'); // ex: "138-141"
                    return `${productId}::${opts}`; // ex: "14::138-141"
                }

                // Tambah 1 ke cart untuk kombinasi tertentu
                //   function addToCart(productId, optionsArr) {
                //     const key = keyOf(productId, optionsArr);
                //     if (!cart[key]) {
                //         cart[key] = {
                //         productId,
                //         options: (optionsArr || []).slice(),
                //         qty: 0,
                //         unitPrice: 0,
                //         lineTotal: 0,
                //         note: ''
                //         };
                //     }
                //     cart[key].qty += 1;
                //     recomputeLineTotal(key);
                //     lastKeyPerProduct[productId] = key;
                //     updateFloatingCartBar();
                //     return key;
                //     }

                function minusFromCart(productId, optionsArr) {
                    const key = keyOf(productId, optionsArr);
                    if (!cart[key]) return;
                    cart[key].qty = Math.max(0, cart[key].qty - 1);
                    if (cart[key].qty === 0) {
                        delete cart[key];

                        // GUARD: jika cart sekarang kosong setelah delete 
                        const totalItems = Object.keys(cart).length;
                        if (totalItems === 0) {
                            // Tutup cart manager modal jika terbuka
                            if (cartManagerModal && !cartManagerModal.classList.contains('hidden')) {
                                closeCartManagerModal();
                            }
                            // Tutup checkout modal jika terbuka
                            if (checkoutModal && !checkoutModal.classList.contains('hidden')) {
                                closeCheckoutModal();
                            }
                        }
                    } else {
                        recomputeLineTotal(key);
                    }
                    updateFloatingCartBar();
                
                    saveCartToStorage();

                }


                // Total qty agregat untuk sebuah productId (menjumlah seluruh line item produk tsb)
                function sumQtyByProduct(productId) {
                    let total = 0;
                    for (const k in cart) {
                        if (cart[k].productId === productId) total += cart[k].qty;
                    }
                    return total;
                }

                // total qty di cart yang menggunakan option tertentu (untuk produk tertentu)
                function sumQtyByOption(productId, optId) {
                    let total = 0;
                    for (const k in cart) {
                        const row = cart[k];
                        if (!row) continue;
                        if (row.productId === productId && (row.options || []).includes(optId)) {
                        total += row.qty || 0;
                        }
                    }
                    return total;
                }

                // remaining stok produk setelah dikurangi cart
                function remainingProductStock(productData) {
                    const stock = Math.max(0, Math.floor(Number(productData.quantity_available) || 0));
                    const always = Boolean(productData.always_available_flag);
                    if (always) return Number.POSITIVE_INFINITY;

                    const used = sumQtyByProduct(productData.id);
                    return Math.max(0, stock - used);
                }

                // remaining stok option setelah dikurangi cart
                function remainingOptionStock(productData, opt) {
                    const alwaysOpt = (opt.always_available_flag === 1 || opt.always_available_flag === true);
                    if (alwaysOpt) return Number.POSITIVE_INFINITY;

                    const stock = Math.max(0, Math.floor(Number(opt.quantity_available) || 0));
                    const used  = sumQtyByOption(productData.id, opt.id);
                    return Math.max(0, stock - used);
                }


                // Update badge qty + visibility tombol minus di kartu produk
                function updateProductBadge(productId) {
                    const qtySpans  = document.querySelectorAll('.qty[data-id="' + productId + '"]');
                    const minusBtns = document.querySelectorAll('.minus-btn[data-id="' + productId + '"]');
                    const total     = sumQtyByProduct(productId);

                    qtySpans.forEach(qtySpan => {
                        qtySpan.innerText = total;
                        if (total > 0) {
                            qtySpan.classList.remove('hidden');
                        } else {
                            qtySpan.classList.add('hidden');
                        }
                    });

                    minusBtns.forEach(minusBtn => {
                        if (total > 0) {
                            minusBtn.classList.remove('hidden');
                        } else {
                            minusBtn.classList.add('hidden');
                        }
                    });
                }

                // Debug print
                function printCart(label = 'Cart') {
                    const rows = Object.entries(cart).map(([key, v]) => ({
                        key,
                        productId: v.productId,
                        options: (v.options || []).join(','),
                        qty: v.qty,
                        unitPrice: v.unitPrice,
                        lineTotal: v.lineTotal,
                        Note: v.note
                    }));
                    console.log(label, cart);
                    if (rows.length) console.table(rows);
                }


                function showModal(productData) {
                    // reset isi modal
                    modalContent.innerHTML = '';
                    modalHeader.innerHTML = '';
                    modalQty = 1;
                    modalNote = '';
                    selectedOptions = [];

                    // RESET textarea yang sudah ada di HTML
                    const noteTextarea = document.getElementById('modalNote');
                    if (noteTextarea) {
                        noteTextarea.value = '';
                    }

                    // === HEADER PRODUK ===
                    const headerWrapper = document.createElement('div');
                    headerWrapper.classList.add('flex', 'items-start', 'gap-4');

                    const infoDiv = document.createElement('div');
                    infoDiv.classList.add('flex-1');

                    // Nama + Badge
                    const titleContainer = document.createElement('div');
                    titleContainer.classList.add('flex', 'items-center', 'gap-2', 'mb-0');

                    const nameEl = document.createElement('h2');
                    nameEl.className = 'text-xl sm:text-2xl font-bold leading-tight tracking-tight text-gray-900';
                    nameEl.textContent = productData.name || '';
                    titleContainer.appendChild(nameEl);

                    // Badge jika hot product
                    if (productData.is_hot_product) {
                        const badge = document.createElement('span');
                        badge.className = 'inline-flex items-center rounded-md bg-orange-100 px-2 py-1 text-xs font-medium text-orange-600 ring-1 ring-inset ring-orange-600/20';
                        badge.textContent = 'Popular';
                        titleContainer.appendChild(badge);
                    }

                    infoDiv.appendChild(titleContainer);

                    // Harga
                    // const priceEl = document.createElement('p');
                    // priceEl.className = 'mt-1 text-lg font-bold text-gray-900';
                    // const basePrice = Number(productData.price) || 0;
                    // const discountedPrice = Number(productData.discounted_base ?? productData.price) || 0;
                    
                    // if (discountedPrice < basePrice) {
                    //     priceEl.innerHTML = `
                    //         <span class="line-through text-gray-500 text-base mr-2">${rupiahFmt.format(basePrice)}</span>
                    //         <span class="text-choco">${rupiahFmt.format(discountedPrice)}</span>
                    //     `;
                    // } else {
                    //     priceEl.textContent = rupiahFmt.format(basePrice);
                    // }
                    // infoDiv.appendChild(priceEl);

                    // Deskripsi
                    const descEl = document.createElement('p');
                    descEl.className = 'text-gray-600 dark:text-gray-400 text-sm leading-normal line-clamp-2 mt-0';
                    descEl.textContent = productData.description || '';
                    infoDiv.appendChild(descEl);

                    headerWrapper.appendChild(infoDiv);

                    // Info Stok
                    const stockEl = document.createElement('p');
                    stockEl.className = 'text-xs mt-1 font-medium';
                    stockEl.id = 'productStockInfo';
                    const stockQty = Number(productData.quantity_available) || 0;
                    const alwaysAvailable = Boolean(productData.always_available_flag);

                    if (alwaysAvailable) {
                        stockEl.innerHTML = '<span class="text-green-600">✓ Selalu Tersedia</span>';
                    } else if (stockQty > 10) {
                        stockEl.innerHTML = `<span class="text-green-600">Stok: ${stockQty}</span>`;
                    } else if (stockQty > 0) {
                        stockEl.innerHTML = `<span class="text-orange-600">⚠ Stok Terbatas: ${stockQty}</span>`;
                    } else {
                        stockEl.innerHTML = '<span class="text-red-600">✕ Habis</span>';
                    }
                    infoDiv.appendChild(stockEl);

                    // Gambar produk
                    if (productData.image) {
                        const imgContainer = document.createElement('div');
                        imgContainer.className = 'relative flex-none w-20 h-20 sm:w-22 sm:h-22 rounded-lg overflow-hidden bg-gray-100';
                        
                        const imgDiv = document.createElement('div');
                        imgDiv.className = 'w-full h-full bg-center bg-cover';
                        imgDiv.style.backgroundImage = `url("${productData.image}")`;
                        imgDiv.setAttribute('data-alt', productData.name || 'Product Image');
                        
                        imgContainer.appendChild(imgDiv);
                        headerWrapper.appendChild(imgContainer);
                    }

                    modalHeader.appendChild(headerWrapper);

                    // === PARENT OPTIONS ===
                    const parentOptions = productData.parent_options || [];
                    
                    if (parentOptions.length === 0) {
                        saveModalBtn.disabled = false;
                        saveModalBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }

                    parentOptions.forEach((po) => {
                        const section = document.createElement('div');
                        section.className = 'px-5 py-2 sm:px-6 sm:py-3 border-b border-gray-200/50 dark:border-white/10';
                        section.dataset.provision = po.provision;
                        section.dataset.value = po.provision_value;
                        section.setAttribute('data-provision-group', ''); 

                        // Header section
                        const headerDiv = document.createElement('div');
                        headerDiv.className = 'flex justify-between items-baseline mb-2';

                        const title = document.createElement('h3');
                        title.className = 'text-lg font-bold text-gray-900';
                        title.textContent = po.name;
                        headerDiv.appendChild(title);

                        // Badge provision
                        const provision = String(po.provision || '').toUpperCase();
                        const isRequired = provision === 'EXACT' || provision === 'MIN' || provision === 'MAX';
                        
                        const badge = document.createElement('span');
                        badge.className = `text-xs font-bold uppercase tracking-wider px-2 py-1 rounded ${
                            isRequired 
                                ? 'text-choco bg-red-50' 
                                : 'text-gray-500 bg-gray-100'
                        }`;
                        
                        const info = provisionInfoText(po.provision, po.provision_value);
                        badge.textContent = isRequired ? 'REQUIRED' : 'OPTIONAL';
                        if (info) badge.title = info;
                        
                        headerDiv.appendChild(badge);
                        section.appendChild(headerDiv);

                        // Options container
                        const optionsContainer = document.createElement('div');
                        const val = Number(po.provision_value);
                        const isRadioMode = val === 1 && (provision === 'EXACT' || provision === 'MAX' || provision === 'OPTIONAL MAX');

                        if (isRadioMode) {
                            // RADIO STYLE 
                            optionsContainer.className = 'flex flex-col gap-3';
                            
                            (po.options || []).forEach(opt => {
                                const label = document.createElement('label');
                                label.className = 'group relative flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 cursor-pointer hover:border-[#ae1504]/30 hover:bg-[#ae1504]/5 transition-all has-[:checked]:border-[#ae1504] has-[:checked]:bg-[#ae1504]/10';

                                const leftDiv = document.createElement('div');
                                leftDiv.className = 'flex items-center gap-3';

                                // Radio indicator (custom)
                                const radioIndicator = document.createElement('div');
                                radioIndicator.className = 'flex h-5 w-5 items-center justify-center rounded-full border-2 border-gray-300 transition-colors';

                                const radioDot = document.createElement('div');
                                radioDot.className = 'h-2.5 w-2.5 rounded-full bg-choco opacity-0 transition-opacity';
                                radioIndicator.appendChild(radioDot);

                                // Hidden radio input
                                const radio = document.createElement('input');
                                radio.type = 'radio';
                                radio.name = `radio_${po.id}`;
                                radio.value = opt.id;
                                radio.className = 'hidden peer';

                                const nameSpan = document.createElement('span');
                                nameSpan.className = 'text-sm font-medium text-gray-900 transition-colors';
                                nameSpan.textContent = opt.name;

                                leftDiv.appendChild(radioIndicator);
                                leftDiv.appendChild(radio);
                                leftDiv.appendChild(nameSpan);
                                label.appendChild(leftDiv);

                                // Price
                                const priceSpan = document.createElement('span');
                                priceSpan.className = 'text-sm text-gray-500 font-medium';
                                
                                const remOpt = remainingOptionStock(productData, opt);
                                const alwaysOpt = (opt.always_available_flag === 1 || opt.always_available_flag === true);
                                const priceNum = Number(opt.price) || 0;

                                if (!alwaysOpt && remOpt < 1) {
                                    priceSpan.textContent = 'Habis';
                                    priceSpan.className = 'text-sm text-red-600 font-medium';
                                    radio.disabled = true;
                                    radio.dataset.sold = '1';
                                    label.classList.add('opacity-60', 'cursor-not-allowed', 'bg-gray-50');
                                    label.classList.remove('hover:border-choco/50', 'hover:bg-gray-50');
                                } else {
                                    priceSpan.textContent = (priceNum === 0) ? 'Free' : rupiahFmt.format(priceNum);
                                    
                                    if (!alwaysOpt && remOpt <= 5) {
                                        const stockSpan = document.createElement('span');
                                        stockSpan.className = 'text-orange-600 ml-1 text-xs';
                                        stockSpan.textContent = `(sisa ${remOpt})`;
                                        priceSpan.appendChild(stockSpan);
                                    }

                                    // Event listener
                                    radio.addEventListener('change', function() {
                                        if (this.checked) {
                                            // Update visual indicator
                                            document.querySelectorAll(`input[name="radio_${po.id}"]`).forEach(r => {
                                                const parent = r.closest('label');
                                                const indicator = parent?.querySelector('.flex.h-5.w-5');
                                                const dot = parent?.querySelector('.h-2\\.5');
                                                if (indicator) {
                                                    indicator.classList.remove('border-choco');
                                                    indicator.classList.add('border-gray-300');
                                                }
                                                if (dot) {
                                                    dot.classList.remove('opacity-100');
                                                    dot.classList.add('opacity-0');
                                                }
                                            });
                                            
                                            // Set yang dipilih
                                            radioDot.classList.remove('opacity-0');
                                            radioDot.classList.add('opacity-100');
                                            radioIndicator.classList.remove('border-gray-300');
                                            radioIndicator.classList.add('border-choco');
                                            
                                            selectedOptions = [parseInt(this.value, 10)];
                                            const pd = productsData.find(p => p.id === currentProductId);
                                            if (pd) syncModalQtyAndStock(pd);
                                        }
                                    });
                                }

                                label.appendChild(priceSpan);
                                optionsContainer.appendChild(label);
                            });
                        } else {
                            // CHECKBOX STYLE 
                            optionsContainer.className = 'flex flex-col gap-3';

                            (po.options || []).forEach(opt => {
                                const label = document.createElement('label');
                                label.className = 'group relative flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 cursor-pointer hover:border-[#ae1504]/30 hover:bg-[#ae1504]/5 transition-all has-[:checked]:border-[#ae1504] has-[:checked]:bg-[#ae1504]/10';

                                const leftDiv = document.createElement('div');
                                leftDiv.className = 'flex items-center gap-3';

                                // Checkbox indicator (custom)
                                const checkboxIndicator = document.createElement('div');
                                checkboxIndicator.className = 'flex h-5 w-5 items-center justify-center rounded-full border-2 border-gray-300 transition-all';

                                const checkDot = document.createElement('div');
                                checkDot.className = 'h-2.5 w-2.5 rounded-full bg-choco opacity-0 transition-opacity';
                                checkboxIndicator.appendChild(checkDot);

                                // Hidden checkbox input
                                const checkbox = document.createElement('input');
                                checkbox.type = 'checkbox';
                                checkbox.value = opt.id;
                                checkbox.className = 'hidden peer';

                                const nameSpan = document.createElement('span');
                                nameSpan.className = 'text-sm font-medium text-gray-900 transition-colors';
                                nameSpan.textContent = opt.name;

                                leftDiv.appendChild(checkboxIndicator);
                                leftDiv.appendChild(checkbox);
                                leftDiv.appendChild(nameSpan);
                                label.appendChild(leftDiv);

                                // Price
                                const priceSpan = document.createElement('span');
                                priceSpan.className = 'text-sm text-gray-500 font-medium';

                                const remOpt = remainingOptionStock(productData, opt);
                                const alwaysOpt = (opt.always_available_flag === 1 || opt.always_available_flag === true);
                                const priceNum = Number(opt.price) || 0;

                                if (!alwaysOpt && remOpt < 1) {
                                    priceSpan.textContent = 'Habis';
                                    priceSpan.className = 'text-sm text-red-600 font-medium';
                                    checkbox.disabled = true;
                                    checkbox.dataset.sold = '1';
                                    label.classList.add('opacity-60', 'cursor-not-allowed', 'bg-gray-50');
                                    label.classList.remove('hover:border-choco/50', 'hover:bg-gray-50');
                                } else {
                                    priceSpan.textContent = (priceNum === 0) ? 'Free' : rupiahFmt.format(priceNum);
                                    
                                    if (!alwaysOpt && remOpt <= 5) {
                                        const stockSpan = document.createElement('span');
                                        stockSpan.className = 'text-orange-600 ml-1 text-xs';
                                        stockSpan.textContent = `(sisa ${remOpt})`;
                                        priceSpan.appendChild(stockSpan);
                                    }

                                    // Event listener
                                    checkbox.addEventListener('change', function() {
                                        const v = parseInt(this.value, 10);
                                        
                                        // Update visual indicator
                                        if (this.checked) {
                                            checkDot.classList.remove('opacity-0');
                                            checkDot.classList.add('opacity-100');
                                            checkboxIndicator.classList.remove('border-gray-300');
                                            checkboxIndicator.classList.add('border-choco');
                                            if (!selectedOptions.includes(v)) selectedOptions.push(v);
                                        } else {
                                            checkDot.classList.remove('opacity-100');
                                            checkDot.classList.add('opacity-0');
                                            checkboxIndicator.classList.remove('border-choco');
                                            checkboxIndicator.classList.add('border-gray-300');
                                            selectedOptions = selectedOptions.filter(x => x !== v);
                                        }
                                        
                                        const pd = productsData.find(p => p.id === currentProductId);
                                        if (pd) syncModalQtyAndStock(pd);
                                    });
                                }

                                label.appendChild(priceSpan);
                                optionsContainer.appendChild(label);
                            });
                        }

                        section.appendChild(optionsContainer);
                        modalContent.appendChild(section);

                        // Panggil enforceProvision
                        enforceProvision(section, po.provision, po.provision_value);
                    });

                    calcModalTotal(productData);

                    modal.classList.remove('hidden');
                    lockBodyScroll();
                    requestAnimationFrame(() => {
                        modal.classList.add('show');
                    });
                    
                    syncModalQtyAndStock(productData);
                }

                // ===== UI qty di modal =====
                const modalQtyMinus = document.getElementById('modalQtyMinus');
                const modalQtyPlus = document.getElementById('modalQtyPlus');
                const modalQtyValue = document.getElementById('modalQtyValue');

                function updateModalQtyDisplay() {
                    modalQtyValue.innerText = modalQty;
                    // modalQtyMinus.disabled = modalQty <= 1;
                    
                    if (currentProductId) {
                        const pd = getProductDataById(currentProductId);
                        const stockInfo = document.getElementById('productStockInfo');
                        const stockQty = Number(pd.quantity_available) || 0;
                        const alwaysAvailable = Boolean(pd.always_available_flag);
                        
                        if (stockInfo && !alwaysAvailable) {
                        if (modalQty >= stockQty) {
                            stockInfo.innerHTML = `<span class="text-red-600">{{ __('messages.customer.menu.stock') }}: ${stockQty}</span>`;
                            modalQtyPlus.disabled = true;
                        } else {
                            stockInfo.innerHTML = `<span class="text-green-600">{{ __('messages.customer.menu.stock') }}: ${stockQty}</span>`;
                            modalQtyPlus.disabled = false;
                        }
                        } else if (alwaysAvailable) {
                        modalQtyPlus.disabled = false;
                        }
                    }
                }

                modalQtyMinus.addEventListener('click', () => {
                    if (modalQty > 1) {
                        modalQty--;
                        const pd = getProductDataById(currentProductId);
                        if (pd) syncModalQtyAndStock(pd);
                    } else {
                        closeOptionsModal();
                    }
                });


                modalQtyPlus.addEventListener('click', () => {
                    const pd = getProductDataById(currentProductId);
                    if (!pd) return;

                    const maxAllowed = computeMaxQtyAllowed(pd);
                    const attemptedQty = modalQty + 1;

                    if (maxAllowed !== Number.POSITIVE_INFINITY && attemptedQty > maxAllowed) {
                        renderOptionStockWarnings(pd); // <-- pakai attemptedQty
                        return;
                    }

                    modalQty++;
                    syncModalQtyAndStock(pd);
                });

                // SAVE (modal): simpan sebagai line item per kombinasi opsi
                saveModalBtn.addEventListener('click', function() {
                    if (!currentProductId) return;

                    let key;
                    // tambahkan sebanyak modalQty
                    for (let i = 0; i < modalQty; i++) {
                        key = addToCart(currentProductId, selectedOptions);
                    }

                    // simpan catatan (optional) pada line item tsb
                    const noteTextarea = document.getElementById('modalNote');
                    const noteVal = noteTextarea ? noteTextarea.value.trim() : '';
                    if (noteVal.length > 0 && key && cart[key]) {
                        cart[key].note = noteVal;
                        saveCartToStorage();
                    }

                    updateProductBadge(currentProductId);
                    updateFloatingCartBar();

                    // Reset state & textarea
                    const noteEl = document.getElementById('modalNote');
                    if (noteEl) noteEl.value = '';

                    // reset & tutup modal
                    currentProductId = null;
                    selectedOptions = [];
                    modalQty = 1;
                    modalNote = '';
                    modal.classList.remove('show');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        unlockBodyScroll();
                    }, 300);
                });


                // ===== UI EVENTS =====

                // PLUS: jika ada parent_options → buka modal; kalau tidak → tambah line item tanpa opsi ([])
                document.querySelectorAll('.plus-btn').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.stopPropagation(); // jangan trigger click card

                        const productId = parseInt(this.dataset.id);
                        const productData = productsData.find(p => p.id === productId);
                        if (!productData) return;

                        currentProductId = productId;
                        selectedOptions = []; // reset pilihan
                        showModal(productData); // modal akan menampilkan options kalau ada
                    });
                });

                document.querySelectorAll('.menu-item').forEach(card => {
                    card.addEventListener('click', function (e) {
                        if (e.target.closest('.plus-btn') || e.target.closest('.minus-btn')) {
                            return;
                        }

                        const productId = parseInt(this.dataset.productId);
                        if (!productId) return;

                        const productData = productsData.find(p => p.id === productId);
                        if (!productData) return;

                        const stockQty = Number(productData.quantity_available) || 0;
                        const alwaysAvailable = Boolean(productData.always_available_flag);

                        if (stockQty < 1 && !alwaysAvailable) {
                            console.log('Product sold out, card click disabled:', productData.name);
                            return;
                        }

                        currentProductId = productId;
                        selectedOptions = []; // reset pilihan setiap buka modal
                        showModal(productData);
                    });
                });


                // MINUS: kurangi total agregat produk dengan mengurangi line item "terakhir"
                document.querySelectorAll('.minus-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = parseInt(this.dataset.id);

                        // Tentukan line item mana yang dikurangi:
                        // 1) coba yang terakhir
                        let key = lastKeyPerProduct[productId];
                        // 2) fallback: cari line item apapun yang qty>0
                        if (!key || !cart[key] || cart[key].qty === 0) {
                            key = Object.keys(cart).find(k => cart[k].productId === productId && cart[k]
                                .qty > 0);
                        }
                        if (!key) {
                            // tidak ada line item utk produk ini
                            updateProductBadge(productId);
                            printCart('Cart (minus noop):');
                            return;
                        }

                        const line = cart[key];
                        minusFromCart(productId, line.options);
                        updateProductBadge(productId);
                        printCart('Cart (minus):');
                    });
                });

                closeModalBtn.addEventListener('click', function() {
                    closeOptionsModal();
                });

                function closeOptionsModal() {
                    // Reset textarea sebelum tutup
                    const noteTextarea = document.getElementById('modalNote');
                    if (noteTextarea) {
                        noteTextarea.value = '';
                    }

                    modal.classList.remove('show');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        unlockBodyScroll();
                        
                        // reset state modal
                        currentProductId = null;
                        selectedOptions = [];
                        modalQty = 1;
                        modalNote = '';
                    }, 300);
                }

                function enforceProvision(poDiv, provision, value) {
                    const prov = String(provision || '').toUpperCase();
                    const val = Number(value);
                    const isRadioMode = val === 1 && (prov === 'EXACT' || prov === 'MAX' || prov === 'OPTIONAL MAX');

                    if (isRadioMode) {
                        // MODE RADIO: pakai radio button (type="radio")
                        const radios = Array.from(poDiv.querySelectorAll('input[type="radio"]:not([data-sold="1"])'));
                        
                        function updateStateRadio() {
                            // Cari radio yang checked
                            const checked = radios.find(r => r.checked);
                            
                            if (checked) {
                                selectedOptions = [parseInt(checked.value, 10)];
                            } else {
                                selectedOptions = [];
                            }

                            // Refresh validasi & hitung total
                            if (currentProductId) {
                                const pd = productsData.find(p => p.id === currentProductId);
                                if (pd) syncModalQtyAndStock(pd);
                            }

                            validateAllProvisions();
                        }

                        // Attach event listener ke semua radio
                        radios.forEach(radio => {
                            radio.addEventListener('change', updateStateRadio);
                        });

                        updateStateRadio(); // Init pertama kali
                        return;
                    }

                    // MODE CHECKBOX: pakai checkbox (type="checkbox") untuk multi-select
                    const checkboxes = Array.from(poDiv.querySelectorAll('input[type="checkbox"]:not([data-sold="1"])'));
                    
                    function updateState() {
                        const checked = checkboxes.filter(c => c.checked);

                        // Atur disable/enable berdasarkan provision rules
                        if (prov === 'EXACT') {
                            if (checked.length >= val) {
                                checkboxes.forEach(c => {
                                    if (!c.checked) c.disabled = true;
                                });
                            } else {
                                checkboxes.forEach(c => c.disabled = false);
                            }
                        }

                        if (prov === 'MAX' || prov === 'OPTIONAL MAX') {
                            if (checked.length >= val) {
                                checkboxes.forEach(c => {
                                    if (!c.checked) c.disabled = true;
                                });
                            } else {
                                checkboxes.forEach(c => c.disabled = false);
                            }
                        }

                        if (prov === 'MIN') {
                            // Untuk MIN, user harus pilih minimal 'val' items
                            // Logic bisa ditambahkan jika perlu mencegah uncheck di bawah minimum
                        }

                        // Update selectedOptions
                        selectedOptions = Array.from(
                            modalContent.querySelectorAll('input[type="checkbox"]:checked, input[type="radio"]:checked')
                        ).map(c => parseInt(c.value, 10));

                        // Refresh validasi & hitung total
                        if (currentProductId) {
                            const pd = productsData.find(p => p.id === currentProductId);
                            if (pd) {
                                calcModalTotal(pd);
                                // Opsional: juga panggil syncModalQtyAndStock jika perlu update stok info
                                // syncModalQtyAndStock(pd);
                            }
                        }

                        validateAllProvisions();
                    }

                    checkboxes.forEach(cb => cb.addEventListener('change', updateState));
                    updateState(); // Init pertama kali
                }

                function validateAllProvisions() {
                    const poGroups = Array.from(modalContent.querySelectorAll('[data-provision-group]'));
                    let allValid = true;

                    poGroups.forEach(group => {
                        const prov = String(group.dataset.provision || '').toUpperCase();
                        const val = Number(group.dataset.value);
                        
                        // PENTING: Cek KEDUA jenis input (checkbox DAN radio)
                        const allInputs = Array.from(group.querySelectorAll('input[type="checkbox"], input[type="radio"]'));
                        const checked = allInputs.filter(inp => inp.checked).length;

                        // Validasi berdasarkan provision type
                        if (prov === 'EXACT' && checked !== val) {
                            allValid = false;
                        }
                        if (prov === 'MAX' && (checked < 1 || checked > val)) {
                            allValid = false;
                        }
                        if (prov === 'MIN' && checked < val) {
                            allValid = false;
                        }
                        if (prov === 'OPTIONAL MAX' && checked > val) {
                            allValid = false;
                        }
                        // OPTIONAL selalu valid (tidak ada validasi)
                    });

                    // Update tombol Save
                    saveModalBtn.disabled = !allValid;
                    saveModalBtn.classList.toggle('opacity-50', !allValid);
                    saveModalBtn.classList.toggle('cursor-not-allowed', !allValid);
                }

                // Hitung total harga di modal
                function calcModalTotal(productData) {
                    const base = Number(productData.price) || 0;
                    const baseDisc = Number(productData.discounted_base ?? productData.price) || 0;

                    const optionPrice = (productData.parent_options || []).reduce((sum, po) => {
                        (po.options || []).forEach(opt => {
                            if (selectedOptions.includes(opt.id)) sum += Number(opt.price) || 0;
                        });
                        return sum;
                    }, 0);

                    const unit = baseDisc + optionPrice;
                    const total = unit * modalQty;

                    // update label total
                    document.getElementById('modalTotalPrice').innerText =
                        `(${new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', minimumFractionDigits:0 }).format(total)})`;

                    // (opsional) tampilkan ringkas harga dasar dicoret bila promo
                    // Misal Anda punya placeholder <span id="modalBasePrice"></span>
                    const el = document.getElementById('modalBasePrice');
                    if (el) {
                        if (baseDisc < base) {
                            el.innerHTML =
                                `<span class="line-through text-gray-500 mr-1">` +
                                new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0
                                }).format(base) +
                                `</span>` +
                                `<span class="font-semibold">` +
                                new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0
                                }).format(baseDisc) +
                                `</span>`;
                        } else {
                            el.textContent = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(base);
                        }
                    }
                }


                // === Floating Cart helpers ===
                function cartGrandTotal() {
                    let total = 0;
                    for (const k in cart) {
                        const row = cart[k];
                        if (!row) continue;
                        const unit = (typeof row.unitPrice === 'number' && row.unitPrice > 0) ?
                            row.unitPrice :
                            computeUnitPrice(row.productId, row.options);
                        total += unit * row.qty;
                    }
                    return total;
                }

                function cartTotalQty() {
                    let total = 0;
                    for (const k in cart) {
                        total += cart[k].qty || 0;
                    }
                    return total;
                }

                function updateFloatingCartBar() {
                    const bar = document.getElementById('floatingCartBar');
                    const totalEl = document.getElementById('floatingCartTotal');
                    const countEl = document.getElementById('floatingCartCount');

                    const total = cartGrandTotal();
                    const count = cartTotalQty();

                    if (count > 0) {
                        totalEl.textContent = rupiahFmt.format(total);
                        countEl.textContent = count === 1 ? '1 Item' : `${count} Items`;
                        bar.classList.remove('hidden');
                    } else {
                        bar.classList.add('hidden');
                    }
                }


                // ===== Cart Manager Modal =====
                const cartManagerModal = document.getElementById('cartManagerModal');
                const cartManagerSheet = document.getElementById('cartManagerSheet');
                const cartManagerBody = document.getElementById('cartManagerBody');
                const cartManagerTotal = document.getElementById('cartManagerTotal');
                const closeCartManager = document.getElementById('closeCartManager');
                const cartManagerDone = document.getElementById('cartManagerDone');

                // helper: ambil nama opsi dari id
                function optionNameById(productData, optId) {
                    for (const po of (productData.parent_options || [])) {
                        for (const opt of (po.options || [])) {
                            if (opt.id === optId) return opt.name;
                        }
                    }
                    return null;
                }

                // kumpulkan isi cart jadi array untuk dirender
                function cartRows() {
                    const arr = [];
                    for (const k in cart) {
                        const row = cart[k];
                        if (!row || row.qty <= 0) continue;
                        const pd = productsData.find(p => p.id === row.productId) || {};
                        const optNames = (row.options || [])
                            .map(id => optionNameById(pd, id))
                            .filter(Boolean);
                        const unit = (typeof row.unitPrice === 'number' && row.unitPrice > 0) ?
                            row.unitPrice :
                            computeUnitPrice(row.productId, row.options);
                        const line = unit * row.qty;
                        arr.push({
                            key: k,
                            productId: row.productId,
                            productName: pd.name || 'Produk',
                            image: pd.image || null,
                            desc: pd.description || '',
                            optNames,
                            qty: row.qty,
                            unit,
                            line,
                            options: row.options || [],
                            note: row.note || ''
                        });
                    }
                    return arr;
                }

                function openCartManager() {
                    const rows = cartRows();

                    // GUARD: jika cart kosong, jangan buka modal 
                    if (rows.length === 0) {
                        return;
                    }

                    // render konten
                    renderCartManager();
                    cartManagerModal.classList.remove('hidden');
                    if (typeof lockBodyScroll === 'function') lockBodyScroll();
                    requestAnimationFrame(() => {
                        cartManagerModal.classList.add('show');
                    });
                }

                function closeCartManagerModal() {
                    cartManagerModal.classList.remove('show');
                    setTimeout(() => {
                        cartManagerModal.classList.add('hidden');
                        if (typeof unlockBodyScroll === 'function') unlockBodyScroll();
                    }, 300);
                }

                function renderCartManager() {
                    const rows = cartRows();

                    if (rows.length === 0) {
                        cartManagerBody.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <svg class="w-20 h-20 mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">
                                {{ __('messages.customer.menu.cart_empty') }}
                            </p>
                        </div>`;
                        cartManagerTotal.textContent = rupiahFmt.format(0);
                        return;
                    }

                    cartManagerBody.innerHTML = rows.map(r => {
                        const pd = getProductDataById(r.productId);
                        const remainingAdd = computeMaxQtyAllowedForLine(pd, r.options);
                        const reachedMax = (remainingAdd !== Number.POSITIVE_INFINITY) && (remainingAdd < 1);

                        let limitInfo = '';
                        if (remainingAdd !== Number.POSITIVE_INFINITY) {
                            const limiters = getLimiterLabelsForLine(pd, r.options, remainingAdd);
                            const who = limiters.length ? limiters.join(', ') : (pd.name || 'Item');

                            limitInfo = `<p class="text-xs ${reachedMax ? 'text-red-600' : 'text-orange-600'} mt-1 font-medium">
                                ${reachedMax ? `{{ __("messages.customer.menu.cannot_add_stock", ["item" => '${who}']) }}` : `{{ __("messages.customer.menu.remaining_can_add", ["item" => '${who}', "qty" => '${remainingAdd}']) }}`}
                            </p>`;
                        }

                        const optsText = r.optNames.length ?
                            `<p class="text-gray-600 text-xs sm:text-sm mt-1 leading-relaxed">${r.optNames.join(', ')}</p>` : '';
                        const noteText = r.note ?
                            `<p class="text-gray-700 text-xs mt-1.5 italic leading-relaxed">
                                <span class="font-semibold">{{ __('messages.customer.menu.note') }}:</span> ${r.note}
                            </p>` : '';

                        const img = r.image ?
                            `<div class="shrink-0">
                                <img src="${r.image}" alt="${r.productName}" 
                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg object-cover border border-gray-200">
                            </div>` : '';

                        const descText = r.desc
                            ? `<p class="text-gray-500 text-xs sm:text-sm mt-0.5 leading-relaxed">${r.desc}</p>`
                            : '';

                        return `
                        <div class="flex gap-2.5 sm:gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200"
                            data-key="${r.key}" data-product-id="${r.productId}">
                            
                            ${img}
                            
                            <!-- Info & Controls -->
                            <div class="flex-1 min-w-0 flex flex-col">
                                <!-- Product Info Section -->
                                <div class="mb-3">
                                    <div class="flex justify-between items-start gap-2 mb-1">
                                        <h4 class="text-gray-900 text-sm sm:text-base font-bold leading-tight">
                                            ${r.productName}
                                        </h4>
                                        <span class="text-gray-600 text-sm sm:text-base whitespace-nowrap ml-2">
                                            ${rupiahFmt.format(r.unit)}
                                        </span>
                                    </div>
                                    ${descText}
                                    ${optsText}
                                    ${noteText}
                                </div>
                                
                                <!-- Controls Section (Always at bottom) -->
                                <div class="flex items-center justify-between mt-auto">
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center gap-1.5 sm:gap-2">
                                        <button class="cm-minus flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-lg 
                                                    bg-white border border-[#ae1504] text-[#ae1504]
                                                    hover:bg-gray-100 transition-colors"
                                                aria-label="Kurangi">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <span class="cm-qty text-gray-900 font-semibold w-6 sm:w-8 text-center text-sm sm:text-base">
                                            ${r.qty}
                                        </span>
                                        <button class="cm-plus flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-lg 
                                                    bg-[#ae1504] text-white hover:bg-[#8a1103] transition-colors 
                                                    shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                                ${reachedMax ? 'disabled' : ''}
                                                aria-label="Tambah">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Line Total -->
                                    <p class="text-gray-900 font-bold text-sm sm:text-base">
                                        ${rupiahFmt.format(r.line)}
                                    </p>
                                </div>
                            </div>
                        </div>
                        `;
                    }).join('');

                    // Update total
                    const grand = rows.reduce((s, r) => s + r.line, 0);
                    cartManagerTotal.textContent = rupiahFmt.format(grand);
                }

                // delegasi event +/− dalam modal
                cartManagerBody.addEventListener('click', (e) => {
                    const plusBtn = e.target.closest('.cm-plus');
                    const minusBtn = e.target.closest('.cm-minus');
                    if (!plusBtn && !minusBtn) return;

                    const rowEl = e.target.closest('[data-key]');
                    if (!rowEl) return;

                    const key = rowEl.getAttribute('data-key');
                    const row = cart[key];
                    if (!row) return;

                    if (plusBtn) {
                        const pd = getProductDataById(row.productId);
                        const maxAllowed = computeMaxQtyAllowedForLine(pd, row.options);

                        // Kalau qty saat ini sudah >= maxAllowed -> mentok
                        const remainingAdd = computeMaxQtyAllowedForLine(pd, row.options);

                        if (remainingAdd !== Number.POSITIVE_INFINITY && remainingAdd < 1) {
                            const limiters = getLimiterLabelsForLine(pd, row.options, remainingAdd);
                            const who = limiters.length ? limiters.join(', ') : (pd.name || 'Item');

                            Swal.fire({
                                icon: 'warning',
                                title: '{{ __("messages.customer.menu.stock_max_reached_title") }}',
                                text: `{{ __("messages.customer.menu.stock_max_reached_text", ["item" => '${who}']) }}`,
                            });
                            return;
                        }

                        addToCart(row.productId, row.options); // aman tambah
                    } else if (minusBtn) {
                        minusFromCart(row.productId, row.options); // kurang 1
                    }

                    // sinkron UI lain
                    updateProductBadge(row.productId);
                    updateFloatingCartBar();
                    // re-render modal
                    renderCartManager();
                });

                // buka modal saat klik ikon trash
                document.getElementById('floatingCartClear').addEventListener('click', (ev) => {
                    ev.preventDefault();
                    // kalau cart kosong, tetap buka modal agar user tahu kosong
                    openCartManager();
                });

                // tutup modal (X atau Selesai atau klik backdrop)
                closeCartManager.addEventListener('click', closeCartManagerModal);
                cartManagerDone.addEventListener('click', closeCartManagerModal);
                cartManagerModal.addEventListener('click', (e) => {
                    if (e.target === cartManagerModal) closeCartManagerModal();
                });


                // --- Helpers untuk opsi (nama & harga) ---
                function getOptionDetail(productId, optId) {
                    const pd = productsData.find(p => p.id === productId);
                    if (!pd) return {
                        name: null,
                        price: 0
                    };
                    for (const po of (pd.parent_options || [])) {
                        for (const opt of (po.options || [])) {
                            if (opt.id === optId) {
                                return {
                                    name: opt.name,
                                    price: Number(opt.price) || 0,
                                    parentName: po.name || null
                                };
                            }
                        }
                    }
                    return {
                        name: null,
                        price: 0
                    };
                }

                // Build baris checkout (dengan rincian opsi & harga)
                function checkoutRows() {
                    const rows = [];
                    for (const k in cart) {
                        const r = cart[k];
                        if (!r || r.qty <= 0) continue;

                        const pd = productsData.find(p => p.id === r.productId) || {};
                        const unit = (typeof r.unitPrice === 'number' && r.unitPrice > 0) ?
                            r.unitPrice :
                            computeUnitPrice(r.productId, r.options);

                        const optionsDetail = (r.options || []).map(oid => getOptionDetail(r.productId, oid));

                        rows.push({
                            key: k,
                            productId: r.productId,
                            name: pd.name || 'Produk',
                            image: pd.image || null, // <<< NEW
                            note: r.note || '',
                            qty: r.qty,
                            unit,
                            line: unit * r.qty,
                            basePrice: Number(pd.price) || 0,
                            optionsDetail
                        });
                    }
                    return rows;
                }


                function rupiah(n) {
                    return rupiahFmt.format(n || 0);
                }

                // --- Render isi modal checkout --- 
                function renderCheckoutModal() {
                    const body = document.getElementById('checkoutBody');
                    const totalEl = document.getElementById('checkoutGrandTotal');

                    const rows = checkoutRows();
                    if (rows.length === 0) {
                        body.innerHTML = `
                            <div class="text-center text-gray-500 py-8">
                                {{ __('messages.customer.menu.empty_cart') }}
                            </div>`;
                        totalEl.textContent = rupiah(0);
                        return;
                    }

                    // item cards 
                    body.innerHTML = rows.map(r => {
                        const opts = (r.optionsDetail || []).map(od => {
                            const label = od.parentName ? `${od.parentName}: ${od.name}` : od.name;
                            return `
                                <div class="w-full flex items-center justify-between text-xs text-gray-600">
                                    <span class="truncate">${label}</span>
                                    <span class="shrink-0">${od.price === 0 ? '(Free)' : rupiah(od.price)}</span>
                                </div>
                            `;
                        }).join('');

                        const note = r.note ?
                            `<div class="mt-2 text-xs italic text-gray-700">
                                <span class="font-semibold">{{ __('messages.customer.menu.note') }}:</span> ${r.note}
                            </div>` : '';

                        const sumOpts = (r.optionsDetail || []).reduce((s, od) => s + (Number(od.price) || 0), 0);
                        const rawBase = Number(r.basePrice) || 0;
                        const baseDisc = Math.max(0, (Number(r.unit) || 0) - sumOpts);
                        
                        const baseRow = (baseDisc < rawBase) ?
                            `<div class="w-full flex items-center justify-between text-xs text-gray-600">
                                <span>{{ __('messages.customer.menu.base_price') }}</span>
                                <span class="shrink-0">
                                    <span class="line-through mr-1">${rupiah(rawBase)}</span>
                                    <span class="font-medium text-choco">${rupiah(baseDisc)}</span>
                                </span>
                            </div>` :
                            `<div class="w-full flex items-center justify-between text-xs text-gray-600">
                                <span>{{ __('messages.customer.menu.base_price') }}</span>
                                <span class="shrink-0">${rupiah(rawBase)}</span>
                            </div>`;

                        return `
                            <div class="flex gap-2.5 sm:gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                ${r.image ? `
                                <div class="shrink-0">
                                    <img src="${r.image}" alt="${r.name}" 
                                        class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg object-cover border border-gray-200">
                                </div>
                                ` : ''}
                                
                                <div class="flex-1 min-w-0 flex flex-col">
                                    <div class="mb-3">
                                        <div class="flex justify-between items-start gap-2 mb-1">
                                            <div class="flex items-center gap-2">
                                                <span class="bg-red-50 text-[#ae1504] text-xs font-bold px-2 py-0.5 rounded">${r.qty}x</span>
                                                <h4 class="text-gray-900 text-sm sm:text-base font-bold leading-tight">
                                                    ${r.name}
                                                </h4>
                                            </div>
                                            <span class="text-gray-900 text-base font-semibold shrink-0">
                                                ${rupiah(r.line)}
                                            </span>
                                        </div>
                                        
                                        <div class="space-y-0.5 mt-2">
                                            ${baseRow}
                                            ${opts}
                                        </div>
                                        
                                        ${note}
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');

                    const grand = rows.reduce((s, r) => s + r.line, 0);
                    totalEl.textContent = rupiah(grand);
                }


                // --- Buka/Tutup modal checkout ---
                const checkoutModal = document.getElementById('checkoutModal');
                const checkoutSheet = document.getElementById('checkoutSheet');
                const checkoutCloseBtn = document.getElementById('checkoutCloseBtn');
                const checkoutCancelBtn = document.getElementById('checkoutCancelBtn');
                const checkoutPayBtn = document.getElementById('checkoutPayBtn');
                const paymentMethodSel = document.getElementById('paymentMethod');
                const orderNameInput = document.getElementById('orderName');

                document.getElementById('floatingCartPay').addEventListener('click', () => {
                    // buka modal konfirmasi
                    openCheckoutModal();
                });

                // Saat user menekan "Pembayaran"
                checkoutPayBtn.addEventListener('click', async () => {
                    const paymentRadio = document.querySelector('input[name="payment"]:checked');
                    const paymentMethod = paymentRadio ? paymentRadio.value : ''; // 'CASH' | 'QRIS'
                    const orderName = orderNameInput.value;

                    const payload = Object.entries(cart).map(([key, r]) => ({
                        product_id: r.productId,
                        option_ids: r.options,
                        qty: r.qty,
                        unit_price: r.unitPrice ?? computeUnitPrice(r.productId, r.options),
                        note: r.note || '',
                        promo_id: (r.promo_id != null) ?
                            r.promo_id : (productsData.find(p => p.id === r.productId)
                                ?.promotion?.id ??
                                null), // fallback
                    }));


                    const grandTotal = payload.reduce((s, it) => s + (it.unit_price * it.qty), 0);

                    // Validasi ringan
                    if (!paymentMethod) {
                        await Swal.fire({
                            icon: 'warning',
                            title: '{{ __('messages.customer.menu.payment_method_not_selected') }}',
                            text: '{{ __('messages.customer.menu.select_payment_first') }}'
                        });
                        return;
                    }
                    if (!orderName) {
                        await Swal.fire({
                            icon: 'warning',
                            title: '{{ __('messages.customer.menu.order_name_not_inputted') }}',
                            text: '{{ __('messages.customer.menu.input_order_name_first') }}'
                        });
                        return;
                    }
                    if (grandTotal <= 0) {
                        await Swal.fire({
                            icon: 'info',
                            title: '{{ __('messages.customer.menu.empty_cart') }}',
                            text: '{{ __('messages.customer.menu.input_item_first') }}'
                        });
                        return;
                    }

                    // ===== VALIDASI STOK REAL-TIME =====
                    checkoutPayBtn.disabled = true;
                    Swal.fire({ 
                        title:'{{ __('messages.customer.menu.checking_stock') }}', 
                        allowOutsideClick:false, 
                        didOpen:() => Swal.showLoading() 
                    });

                    try {
                        const tokenEl = document.querySelector('meta[name="csrf-token"]');
                        const csrf = tokenEl ? tokenEl.content : null;
                        
                        const checkStockUrl = "{{ route('customer.menu.check-stock', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}";
                        
                        const stockCheckResponse = await fetch(checkStockUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({ items: payload })
                        });
                        
                        if (!stockCheckResponse.ok) {
                            throw new Error(`HTTP ${stockCheckResponse.status}`);
                        }
                        
                        const stockResult = await stockCheckResponse.json();
                        
                        if (!stockResult.success) {
                            Swal.close();
                            
                            const result = await Swal.fire({
                                icon: 'error',
                                title: '{{ __('messages.customer.menu.stock_not_enough_title') }}',
                                html: `
                                    <div class="text-center">
                                        <p class="mt-3 text-sm text-gray-600">{{ __('messages.customer.menu.please_refresh_page_for_qty') }}</p>
                                    </div>
                                `,
                                showCancelButton: true,
                                confirmButtonText: '{{ __('messages.customer.menu.refresh_page') }}',
                                cancelButtonText: '{{ __('messages.customer.menu.cancel') }}',
                                confirmButtonColor: '#3085d6',
                            });
                            
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                            
                            checkoutPayBtn.disabled = false;
                            return;
                        }
                        
                        Swal.close();
                        
                    } catch (stockCheckError) {
                        Swal.close();
                    }

                    // Konfirmasi ringkas
                    const confirm = await Swal.fire({
                        icon: 'question',
                        title: '{{ __('messages.customer.menu.checkout_confirmation') }}',
                        html: `
                <div style="text-align:left">
                    <div>{{ __('messages.customer.menu.nama_pemesan') }}: <b>${orderName}</b></div>
                    <div>{{ __('messages.customer.menu.method') }}: <b>${paymentMethod.toUpperCase()}</b></div>
                    <div>Total: <b>${rupiahFmt.format(grandTotal)}</b></div>
                </div>`,
                        showCancelButton: true,
                        confirmButtonText: '{{ __('messages.customer.menu.yes_pay') }}',
                        cancelButtonText: '{{ __('messages.customer.menu.batal') }}'
                    });
                    if (!confirm.isConfirmed) return;

                    // Siapkan body & URL
                    const body = {
                        items: payload,
                        payment_method: paymentMethod,
                        order_name: orderName,
                        total_amount: grandTotal
                    };

                    const PARTNER_SLUG = @json($partner_slug);
                    const TABLE_CODE = @json($table_code);
                    const checkoutUrl =
                        `/customer/${encodeURIComponent(PARTNER_SLUG)}/checkout/${encodeURIComponent(TABLE_CODE)}`;

                    // CSRF
                    const tokenEl = document.querySelector('meta[name="csrf-token"]');
                    const csrf = tokenEl ? tokenEl.content : null;

                    // Loading
                    checkoutPayBtn.disabled = true;
                    Swal.fire({
                        title: '{{ __('messages.customer.menu.processing_payment') }}',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    try {
                        const r = await fetch(checkoutUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                ...(csrf ? {
                                    'X-CSRF-TOKEN': csrf,
                                    'X-Requested-With': 'XMLHttpRequest'
                                } : {})
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify(body)
                        });

                        // Jika server melakukan redirect 302 dan fetch mengikutinya:
                        if (r.redirected && r.url) {
                            window.location.assign(r.url);
                            return; // stop di sini
                        }

                        // Coba parse JSON
                        let res;
                        const ct = r.headers.get('content-type') || '';
                        if (ct.includes('application/json')) {
                            res = await r.json();
                        } else {
                            const text = await r.text();
                            try {
                                res = JSON.parse(text);
                            } catch {
                                throw new Error('Respons not valid from server.');
                            }
                        }

                        if (paymentMethod === 'QRIS') {
                            if (res.success) {
                                clearCartFromStorage();
                                return window.location.href = res.redirect_url;
                            } else {
                                return Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message
                                });
                            }
                        }

                        if (!r.ok) {
                            throw new Error(res?.message || `Request failed: ${r.status}`);
                        }

                        // Ekspektasi terbaik: controller mengembalikan { redirect: "..." }
                        if (res?.redirect) {
                            clearCartFromStorage();
                            window.location.assign(res.redirect);
                            return;
                        }

                        // Fallback (kalau tidak ada redirect di JSON)
                        Swal.close();
                        await Swal.fire({
                            icon: 'success',
                            title: '{{ __('messages.customer.menu.success') }}',
                            text: '{{ __('messages.customer.menu.checkout_success') }}',
                            timer: 1400,
                            showConfirmButton: false
                        });
                        clearCartFromStorage(); // Clear cart
                        
                    } catch (err) {
                        Swal.close();
                        const msg = (err?.message || '').toLowerCase().includes('csrf') ?
                            '{{ __('messages.customer.menu.session_expired') }}' :
                            (err?.message || '{{ __('messages.customer.menu.checkout_failed') }}');
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __('messages.customer.menu.failed') }}',
                            text: msg
                        });
                    } finally {
                        checkoutPayBtn.disabled = false;
                    }
                });

                function openCheckoutModal() {
                    const rows = checkoutRows();
                    if (rows.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: '{{ __('messages.customer.menu.empty_cart') }}',
                            text: '{{ __('messages.customer.menu.input_item_first') }}'
                        });
                        return;
                    }

                    renderCheckoutModal();
                    document.querySelectorAll('input[name="payment"]').forEach(r => r.checked = false);
                    checkoutPayBtn.disabled = true;

                    checkoutModal.classList.remove('hidden');
                    requestAnimationFrame(() => {
                        checkoutSheet.classList.remove('scale-95', 'opacity-0');
                        checkoutSheet.classList.add('scale-100', 'opacity-100');
                    });
                    if (typeof lockBodyScroll === 'function') lockBodyScroll();
                    orderNameInput.focus({ preventScroll: true });
                }

                function closeCheckoutModal() {
                    checkoutSheet.classList.remove('scale-100', 'opacity-100');
                    checkoutSheet.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        checkoutModal.classList.add('hidden');
                        if (typeof unlockBodyScroll === 'function') unlockBodyScroll();
                    }, 250);
                }

                // helper: tombol aktif kalau metode terpilih & nama terisi
                function updatePayBtnState() {
                    const paymentRadio = document.querySelector('input[name="payment"]:checked');
                    const hasMethod = !!paymentRadio;
                    const hasName = !!(orderNameInput.value || '').trim();
                    checkoutPayBtn.disabled = !(hasMethod && hasName);
                }

                // Event listeners untuk radio buttons
                document.addEventListener('change', function(e) {
                    if (e.target.name === 'payment') {
                        updatePayBtnState();
                    }
                });
                orderNameInput.addEventListener('input', updatePayBtnState);



                // Tutup modal handlers
                checkoutCloseBtn.addEventListener('click', closeCheckoutModal);
                checkoutCancelBtn.addEventListener('click', closeCheckoutModal);
                checkoutModal.addEventListener('click', (e) => {
                    if (e.target === checkoutModal) closeCheckoutModal();
                });

                // Restore cart dari storage 
                function restoreCartOnLoad() {
                    const savedCart = loadCartFromStorage();
                    
                    if (savedCart && savedCart.items) {
                        cart = savedCart.items;
                        lastKeyPerProduct = savedCart.lastKeyPerProduct || {};
                        
                        for (const key in cart) {
                            const row = cart[key];
                            if (row && row.productId) {
                                updateProductBadge(row.productId);
                            }
                        }
                        
                        updateFloatingCartBar();
                        console.log('Cart restored:', cart);
                    }
                }
                
                // Panggil restore cart
                restoreCartOnLoad();

                //reorder
                (function applyReorderOnLoad() {
                    const items = window.__REORDER_ITEMS__ || [];

                    // JIKA ADA REORDER → RESET CART TOTAL
                    if (Array.isArray(items) && items.length > 0) {
                        cart = {};
                        lastKeyPerProduct = {};
                        clearCartFromStorage();
                    }
                    
                    // HAPUS PARAMETER REORDER DARI URL SEGERA SETELAH DATA DIBACA
                    const url = new URL(window.location.href);
                    if (url.searchParams.has('reorder_order_id')) {
                        url.searchParams.delete('reorder_order_id');
                        window.history.replaceState({}, '', url.toString());
                    }

                    if (Array.isArray(items) && items.length > 0) {
                        items.forEach(item => {
                            const productId = parseInt(item.product_id, 10);
                            const optionIds = Array.isArray(item.option_ids) ? item.option_ids : [];
                            const qty       = item.qty ? parseInt(item.qty, 10) : 1;
                            const note      = item.note || '';

                            if (!productId || qty <= 0) return;

                            for (let i = 0; i < qty; i++) {
                                const key = addToCart(productId, optionIds);
                                // kalau ada catatan, simpan di line item terakhir
                                if (note && key && cart[key]) {
                                    cart[key].note = note;
                                }
                            }

                            updateProductBadge(productId);
                        });

                        updateFloatingCartBar();

                        // HAPUS PARAMETER REORDER DARI URL SETELAH BERHASIL DIMUAT
                        const url = new URL(window.location.href);
                        if (url.searchParams.has('reorder_order_id')) {
                            url.searchParams.delete('reorder_order_id');
                            window.history.replaceState({}, '', url.toString());
                        }
                    }

                    const msgs = window.__REORDER_MESSAGES__ || [];
                    if (Array.isArray(msgs) && msgs.length > 0 && window.Swal) {
                        Swal.fire({
                            icon: 'info',
                            title: '{{ __('messages.customer.menu.reorder_loading') }}',
                            html: `<div style="text-align:left;font-size:13px;">
                                <p class="mb-1">{{ __('messages.customer.menu.reorder_information') }}</p>
                                <ul class="mt-2 list-disc pl-5 space-y-1">
                                    ${msgs.map(m => `<li>${m}</li>`).join('')}
                                </ul>
                            </div>`,
                            confirmButtonText: "{{ __('messages.customer.menu.understand') }}",
                        }).then(() => {
                            // setelah user klik "Mengerti", langsung buka modal checkout
                            openCheckoutModal();
                        });
                    } else if (Array.isArray(items) && items.length > 0) {
                        // kalau tidak ada pesan tambahan, tapi ada item reorder
                        // tetap langsung buka modal checkout
                        openCheckoutModal();
                    }
                })();

                function getSelectedOptionObjects(productData) {
                    const chosen = new Set((selectedOptions || []).map(Number));
                    const opts = [];

                    (productData.parent_options || []).forEach(po => {
                        (po.options || []).forEach(opt => {
                        if (chosen.has(Number(opt.id))) opts.push(opt);
                        });
                    });

                    return opts;
                    }

                    function computeMaxQtyAllowed(productData) {
                        const maxByProduct = remainingProductStock(productData);

                        let maxByOptions = Number.POSITIVE_INFINITY;
                        const selectedOptObjs = getSelectedOptionObjects(productData);

                        selectedOptObjs.forEach(opt => {
                            const remOpt = remainingOptionStock(productData, opt);
                            maxByOptions = Math.min(maxByOptions, remOpt);
                        });

                        const maxAllowed = Math.min(maxByProduct, maxByOptions);
                        return Number.isFinite(maxAllowed) ? maxAllowed : Number.POSITIVE_INFINITY;
                    }

                    function computeMaxQtyAllowedForLine(productData, optionsArr) {
                        const maxByProduct = remainingProductStock(productData);

                        let maxByOptions = Number.POSITIVE_INFINITY;
                        const chosen = new Set((optionsArr || []).map(Number));

                        (productData.parent_options || []).forEach(po => {
                            (po.options || []).forEach(opt => {
                            if (chosen.has(Number(opt.id))) {
                                const remOpt = remainingOptionStock(productData, opt);
                                maxByOptions = Math.min(maxByOptions, remOpt);
                            }
                            });
                        });

                        const maxAllowed = Math.min(maxByProduct, maxByOptions);
                        return Number.isFinite(maxAllowed) ? maxAllowed : Number.POSITIVE_INFINITY;
                    }

                    function getLimiterLabelsForLine(productData, optionsArr, maxAllowed) {
                        const labels = [];

                        const alwaysProduct = Boolean(productData.always_available_flag);
                        const remProduct    = remainingProductStock(productData); // 

                        if (!alwaysProduct && remProduct === maxAllowed) {
                            labels.push(`${productData.name || 'Produk'} (stok produk)`);
                        }

                        const chosen = new Set((optionsArr || []).map(Number));
                        (productData.parent_options || []).forEach(po => {
                            (po.options || []).forEach(opt => {
                            if (!chosen.has(Number(opt.id))) return;

                            const alwaysOpt = (opt.always_available_flag === 1 || opt.always_available_flag === true);
                            if (alwaysOpt) return;

                            const remOpt = remainingOptionStock(productData, opt); 
                            if (remOpt === maxAllowed) labels.push(`${opt.name || 'Option'} (stok opsi)`);
                            });
                        });

                        return [...new Set(labels)];
                    }

                    function getLimiterLabels(productData, maxAllowed) {
                        const labels = [];

                        const alwaysProduct = Boolean(productData.always_available_flag);
                        const remProduct    = remainingProductStock(productData); // sisa setelah cart

                        if (!alwaysProduct && remProduct === maxAllowed) {
                            labels.push(`${productData.name || 'Produk'} (stok produk)`);
                        }

                        const selectedOptObjs = getSelectedOptionObjects(productData);
                        selectedOptObjs.forEach(opt => {
                            const alwaysOpt = (opt.always_available_flag === 1 || opt.always_available_flag === true);
                            if (alwaysOpt) return;

                            const remOpt = remainingOptionStock(productData, opt); // sisa setelah cart
                            if (remOpt === maxAllowed) labels.push(`${opt.name || 'Option'} (stok opsi)`);
                        });

                        return [...new Set(labels)];
                    }

                    function renderOptionStockWarnings(productData, desiredQty = modalQty) {
                        const el = document.getElementById('optionStockWarnings');
                        if (!el) return;

                        el.innerHTML = '';

                        const selectedOptObjs = getSelectedOptionObjects(productData);
                        if (!selectedOptObjs.length) return;

                        selectedOptObjs.forEach(opt => {
                            const alwaysOpt = (opt.always_available_flag === 1 || opt.always_available_flag === true);
                            if (alwaysOpt) return;

                            const optStock = remainingOptionStock(productData, opt);

                            // tampilkan jika stok opsi tinggal 5 ke bawah
                            if (optStock > 0 && optStock <= 5) {
                                const div = document.createElement('div');

                                // kalau user minta qty melebihi stok opsi -> merah, kalau tidak -> orange (low stock)
                                div.className = (desiredQty > optStock)
                                    ? 'text-red-600 font-medium'
                                    : 'text-orange-600 font-medium';

                                div.textContent =
                                    (desiredQty > optStock)
                                        ? `Pilihan "${opt.name}" hanya tersisa ${optStock}`
                                        : `Pilihan "${opt.name}" hanya tersisa ${optStock}`;

                                el.appendChild(div);
                            }

                        });
                    }



                    /**
                     * SATU PINTU: sinkron qty + stok + warning + tombol + + total
                     */
                    function syncModalQtyAndStock(productData) {
                        if (!productData) return;

                        // 1) hitung batas qty maksimal berdasarkan stok product & opsi terpilih
                        const maxAllowed = computeMaxQtyAllowed(productData);

                        const warnEl = document.getElementById('optionStockWarnings');
                        if (warnEl) {
                            const remP = remainingProductStock(productData);
                            const selectedOptObjs = getSelectedOptionObjects(productData);

                            const optsInfo = selectedOptObjs
                                .map(opt => {
                                const remO = remainingOptionStock(productData, opt);
                                return `• ${opt.name}: sisa ${remO}`;
                                })
                                .join('<br>');

                            warnEl.innerHTML = `
                                <div class="text-gray-600">
                                <div>Stok produk tersisa: <b>${remP === Number.POSITIVE_INFINITY ? '∞' : remP}</b></div>
                                ${optsInfo ? `<div class="mt-1">${optsInfo}</div>` : ''}
                                </div>
                            ` + warnEl.innerHTML; // biar pesan merah "maks" tetap ada
                        }

                        // 2) kalau modalQty melebihi batas, clamp
                        if (maxAllowed !== Number.POSITIVE_INFINITY) {
                            if (maxAllowed < 1) {
                            modalQty = 1; // tetap minimal 1, tapi tombol + akan mati
                            } else if (modalQty > maxAllowed) {
                            modalQty = maxAllowed;
                            }
                        }
                        if (modalQty < 1) modalQty = 1;

                        // 3) update angka qty
                        if (modalQtyValue) modalQtyValue.innerText = modalQty;

                        // 4) update info stok produk (opsional, pakai elemen yang sudah kamu buat)
                        const stockInfo = document.getElementById('productStockInfo');
                        const stockQty = remainingProductStock(productData);
                        const alwaysAvailable = Boolean(productData.always_available_flag);

                        if (stockInfo) {
                            if (alwaysAvailable) {
                            stockInfo.innerHTML = '<span class="text-green-600">✓ {{ __("messages.customer.menu.always_available") }}</span>';
                            } else if (stockQty > 10) {
                            stockInfo.innerHTML = `<span class="text-green-600">{{ __("messages.customer.menu.stock") }}: ${stockQty}</span>`;
                            } else if (stockQty > 0) {
                            stockInfo.innerHTML = `<span class="text-orange-600">⚠ {{ __("messages.customer.menu.limited_stock") }}: ${stockQty}</span>`;
                            } else {
                            stockInfo.innerHTML = '<span class="text-red-600">✕ {{ __("messages.customer.menu.sold") }}</span>';
                            }
                        }

                        // 5) disable/enable tombol plus berdasarkan maxAllowed
                        if (modalQtyPlus) {
                            if (maxAllowed === Number.POSITIVE_INFINITY) {
                            modalQtyPlus.disabled = false;
                            } else {
                            modalQtyPlus.disabled = (modalQty >= maxAllowed) || (maxAllowed < 1);
                            }
                        }

                        renderOptionStockWarnings(productData, modalQty);

                            if (warnEl) {
                            warnEl.dataset.maxReached = '0';

                            // Hapus pesan max sebelumnya biar tidak numpuk
                            warnEl.querySelectorAll('[data-max-msg="1"]').forEach(n => n.remove());

                            if (maxAllowed !== Number.POSITIVE_INFINITY && maxAllowed > 0 && modalQty >= maxAllowed) {
                                warnEl.dataset.maxReached = '1';

                                const limiters = getLimiterLabels(productData, maxAllowed);
                                const who = limiters.length ? limiters.join(', ') : 'Item';

                                const maxMsg = document.createElement('div');
                                maxMsg.dataset.maxMsg = '1';
                                maxMsg.className = 'text-red-600 font-medium';
                                maxMsg.textContent = `"${who}" telah mencapai stok maksimum (${maxAllowed})`;
                                warnEl.prepend(maxMsg);
                            }
                        }


                        // 6) tampilkan warning opsi yang tidak cukup
                        
                        // 7) hitung ulang total harga modal
                        calcModalTotal(productData);
                    }
            });
        </script>

        <script>
            (function setupCustomerCategoryAndSearch() {
                // GUARD: cegah init ganda saat partial re-render
                if (window.__CATSEARCH_INITED_CUSTOMER__) return;
                window.__CATSEARCH_INITED_CUSTOMER__ = true;

                const filterButtons = document.querySelectorAll('.filter-btn');
                const categoryGroups = document.querySelectorAll('.category-group');
                const items = document.querySelectorAll('.menu-item');

                const searchInput = document.getElementById('menuSearch');
                const searchClear = document.getElementById('menuSearchClear');

                let activeCategory = 'all';
                let query = '';

                function norm(s) {
                    return (s || '').toString().toLowerCase()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();
                }

                function itemMatches(item, cat, q) {
                    const catOk = (cat === 'all' || item.dataset.category === cat);
                    if (!catOk) return false;
                    if (!q) return true;

                    const nameEl = item.querySelector('h3');
                    const descEl = item.querySelector('p.text-sm.text-gray-600');
                    const name = norm(nameEl ? nameEl.textContent : '');
                    const desc = norm(descEl ? descEl.textContent : '');
                    return name.includes(q) || desc.includes(q);
                }

                let noResEl = document.getElementById('noResultBanner');

                function ensureNoResultBanner(show, nq) {
                    if (!noResEl) {
                        noResEl = document.createElement('div');
                        noResEl.id = 'noResultBanner';
                        noResEl.className = 'hidden'; 
                        noResEl.style.display = 'none';
                        const menuContainer = document.getElementById('menu-container');
                        if (menuContainer && menuContainer.parentNode) {
                            menuContainer.parentNode.insertBefore(noResEl, menuContainer);
                        }
                    }
                    
                    if (show) {
                        // Buat konten dengan desain yang lebih menarik
                        noResEl.innerHTML = `
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                                <div class="p-8 sm:p-12 text-center">
                                    <!-- Icon Search -->
                                    <div class="flex justify-center mb-4">
                                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Title -->
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                                        {{ __('messages.customer.menu.no_results_found') }}
                                    </h3>
                                    
                                    <!-- Message -->
                                    <p class="text-gray-600 text-sm sm:text-base mb-2">
                                        {{ __('messages.customer.menu.no_match_menu') }} <span class="font-bold text-[#ae1504]">"${nq}"</span>${activeCategory !== 'all' ? ' {{ __('messages.customer.menu.in_selected_category') }}' : ''}
                                    </p>
                                </div>
                            </div>
                        `;
                        
                        noResEl.style.display = 'block';
                        // Trigger fade in animation
                        requestAnimationFrame(() => {
                            noResEl.classList.remove('hidden');
                            noResEl.classList.add('animate-fadeIn');
                        });
                    } else {
                        noResEl.style.display = 'none';
                        noResEl.classList.add('hidden');
                        noResEl.classList.remove('animate-fadeIn');
                    }
                }

                function applyFilters() {
                    const nq = norm(query);

                    // toggle active pada tombol
                    filterButtons.forEach(b => {
                        const isActive = (b.dataset.category === activeCategory) || (activeCategory === 'all' && b
                            .dataset.category === 'all');
                        b.classList.toggle('active', isActive);
                    });

                    // tampilkan item sesuai kombinasi
                    let anyShown = false;
                    items.forEach(item => {
                        const show = itemMatches(item, activeCategory, nq);
                        item.style.display = show ? 'flex' : 'none';
                        if (show) anyShown = true;
                    });

                    // tampilkan/semmbunyikan heading group
                    categoryGroups.forEach(group => {
                        if (activeCategory === 'all') {
                            const hasVisible = Array.from(group.querySelectorAll('.menu-item')).some(it => it.style
                                .display !== 'none');
                            group.style.display = hasVisible ? 'block' : 'none';
                        } else {
                            const sameCat = (group.dataset.category === activeCategory);
                            if (!sameCat) {
                                group.style.display = 'none';
                                return;
                            }
                            const hasVisible = Array.from(group.querySelectorAll('.menu-item')).some(it => it.style
                                .display !== 'none');
                            group.style.display = hasVisible ? 'block' : 'none';
                        }
                    });

                    const hotGroup = document.querySelector('.hot-products-group');
                    if (hotGroup) {
                        const hotItems = hotGroup.querySelectorAll('.menu-item'); // menu-item-hot juga masuk
                        const hasVisibleHot = Array.from(hotItems).some(it => it.style.display !== 'none');
                        hotGroup.style.display = hasVisibleHot ? 'block' : 'none';
                    }

                    // banner & tombol clear
                    ensureNoResultBanner(!anyShown, nq);
                    if (searchClear) searchClear.classList.toggle('hidden', !nq);
                }

                // Debounce helper
                let t = null;

                function debounce(fn, wait = 150) {
                    return (...args) => {
                        clearTimeout(t);
                        t = setTimeout(() => fn.apply(null, args), wait);
                    };
                }

                // Listeners
                filterButtons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        activeCategory = btn.dataset.category;
                        applyFilters();
                    });
                });

                if (searchInput) {
                    searchInput.addEventListener('input', debounce(e => {
                        query = e.target.value || '';
                        applyFilters();
                    }, 150));
                }
                if (searchClear) {
                    searchClear.addEventListener('click', () => {
                        if (!searchInput) return;
                        searchInput.value = '';
                        query = '';
                        applyFilters();
                        searchInput.focus({
                            preventScroll: true
                        });
                    });
                }

                // init pertama
                applyFilters();
            })();
        </script>
        <script>
            // Tutup Swal kalau user balik ke halaman ini (bfcache / back)
            window.addEventListener('pageshow', function () {
                if (window.Swal) Swal.close();
            });

            // Tutup Swal kalau tab balik fokus
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden && window.Swal) Swal.close();
            });
        </script>
    @endpush
