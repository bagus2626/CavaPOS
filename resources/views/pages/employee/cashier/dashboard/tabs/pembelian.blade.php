{{-- resources/views/pages/employee/cashier/dashboard/tabs/pembelian.blade.php --}}

{{-- Konten utama --}}
<div class="w-full px-0 relative bg-gray-50">
    {{-- Header dengan Sticky --}}
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                {{-- Category Tabs (Kiri) --}}
                <div class="flex-1 min-w-0 order-2 md:order-1">
                    <nav aria-label="Tabs"
                        class="flex space-x-8 overflow-x-auto hide-scrollbar border-b border-gray-200 -pb-1">
                        <button
                            class="filter-btn whitespace-nowrap py-3 px-1 font-medium text-sm text-gray-600 flex items-center gap-2 border-b-2 border-transparent transition-all active"
                            data-category="all">
                            All
                        </button>
                        @foreach ($categories->sortBy('category_order') as $category)
                            <button
                                class="filter-btn whitespace-nowrap py-3 px-1 font-medium text-sm text-gray-600 flex items-center gap-2 border-b-2 border-transparent transition-all"
                                data-category="{{ $category->id }}">
                                {{ $category->category_name }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                {{-- Search Bar (Kanan) --}}
                <div class="w-full md:w-96 order-1 md:order-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input id="menuSearch" type="search" placeholder="Cari menu… (nama / deskripsi)"
                            class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl leading-5 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:border-red-600 sm:text-sm shadow-sm transition-all"
                            style="--tw-ring-color: #ae1504;" autocomplete="off" />
                        <button type="button" id="menuSearchClear"
                            class="absolute right-3 top-1/2 -translate-y-1/2 hidden w-6 h-6 rounded hover:bg-gray-100 text-gray-500 flex items-center justify-center text-xl font-bold"
                            aria-label="Clear" title="Clear">×</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Menu Content --}}
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

    <div class="w-full px-4 sm:px-6 lg:px-8 pb-6 pt-6" id="menu-container">
        {{-- Hot Products Section --}}
        @if ($hotProducts->count())
            <div class="hot-products-group mb-10">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-6">
                    <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"
                            clip-rule="evenodd" />
                    </svg>
                    Hot Products
                </h2>

                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-6">
                    @foreach ($hotProducts as $product)
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
                                $promoBadge =
                                    $promo->promotion_type === 'percentage'
                                        ? '-' .
                                            rtrim(
                                                rtrim(number_format($promo->promotion_value, 2, ',', '.'), '0'),
                                                ',',
                                            ) .
                                            '%'
                                        : '-Rp ' . number_format($promo->promotion_value, 0, ',', '.');
                            }
                        @endphp

                        <div class="menu-item menu-item-hot group bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-lg hover:border-choco/20 transition-all flex flex-col h-full relative cursor-pointer
                            {{ $product->quantity_available < 1 && $product->always_available_flag == false ? 'grayscale opacity-75 cursor-not-allowed' : '' }}"
                            data-category="{{ $product->category_id }}" data-product-id="{{ $product->id }}">

                            {{-- Product Image --}}
                            <div
                                class="aspect-[4/3] bg-gradient-to-br from-orange-50 to-orange-100 overflow-hidden relative">
                                @if ($product->quantity_available > 0 && $product->quantity_available <= 3 && $product->always_available_flag == false)
                                    <div
                                        class="absolute bottom-2 left-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-md flex items-center justify-center gap-1.5 shadow-sm border border-yellow-100 z-10">
                                        <span class="text-[10px] font-bold text-yellow-700 uppercase tracking-wide">Stok
                                            Terbatas</span>
                                    </div>
                                @endif

                                @if ($hasPromo && $promoBadge)
                                    <div class="absolute top-3 left-3 z-10 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md"
                                        style="background-color: #ae1504;">
                                        {{ $promoBadge }}
                                    </div>
                                @endif

                                @if ($firstImage)
                                    <img src="{{ asset($firstImage) }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div
                                        class="absolute inset-0 flex items-center justify-center text-orange-200 group-hover:scale-105 transition-transform duration-500">
                                        <svg class="w-14 h-14" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M2 21h19v-3H2v3zM20 8H4V4h16v4zm0 10H4v-6h16v6z" />
                                        </svg>
                                    </div>
                                @endif

                                @if ($product->quantity_available < 1 && $product->always_available_flag == false)
                                    <div class="absolute inset-0 bg-white/40 flex items-center justify-center">
                                        <span
                                            class="bg-gray-800 text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-lg">
                                            Habis
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Product Info --}}
                            <div class="flex-1 flex flex-col p-3">
                                <h3
                                    class="font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-choco transition-colors text-sm sm:text-base">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-xs text-gray-500 mb-2 sm:mb-3 line-clamp-2">{{ $product->description }}
                                </p>

                                <div class="mt-auto flex items-end justify-between gap-2">
                                    {{-- Price --}}
                                    <div class="flex-shrink min-w-0 max-w-[55%] sm:max-w-none">
                                        @if ($hasPromo)
                                            <p class="text-[10px] sm:text-xs text-gray-500 line-through truncate">Rp
                                                {{ number_format($basePrice, 0, ',', '.') }}</p>
                                            <p class="font-bold text-gray-900 text-xs sm:text-base truncate">Rp
                                                {{ number_format($discountedBase, 0, ',', '.') }}</p>
                                        @else
                                            <p class="font-bold text-gray-900 text-xs sm:text-base truncate">Rp
                                                {{ number_format($basePrice, 0, ',', '.') }}</p>
                                        @endif
                                    </div>

                                    {{-- Quantity Controls --}}
                                    <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                                        <button
                                            class="minus-btn hidden w-6 h-6 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl border border-[#ae1504] text-[#ae1504] flex items-center justify-center hover:bg-gray-100 transition-all"
                                            data-id="{{ $product->id }}">
                                            <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <span
                                            class="qty hidden text-[11px] sm:text-sm font-bold text-gray-800 min-w-[1rem] sm:min-w-[1.5rem] text-center"
                                            data-id="{{ $product->id }}">0</span>
                                        @if ($product->quantity_available < 1 && $product->always_available_flag == false)
                                            <button
                                                class="h-6 w-6 sm:h-9 sm:w-9 rounded-lg sm:rounded-xl bg-gray-100 text-gray-300 flex items-center justify-center cursor-not-allowed border border-gray-200"
                                                disabled>
                                                <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        @else
                                            <button
                                                class="plus-btn h-6 w-6 sm:h-9 sm:w-9 rounded-lg sm:rounded-xl bg-[#ae1504] text-white flex items-center justify-center hover:bg-[#8a1103] shadow-md hover:shadow-lg transition-all active:scale-95"
                                                data-id="{{ $product->id }}">
                                                <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
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

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
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
                                    $promoBadge =
                                        $promo->promotion_type === 'percentage'
                                            ? '-' .
                                                rtrim(
                                                    rtrim(number_format($promo->promotion_value, 2, ',', '.'), '0'),
                                                    ',',
                                                ) .
                                                '%'
                                            : '-Rp ' . number_format($promo->promotion_value, 0, ',', '.');
                                }
                            @endphp

                            <div class="menu-item group bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-lg hover:border-choco/20 transition-all flex flex-col h-full relative cursor-pointer
                                {{ $product->quantity_available < 1 && $product->always_available_flag == false ? 'grayscale opacity-75 cursor-not-allowed' : '' }}"
                                data-category="{{ $product->category_id }}" data-product-id="{{ $product->id }}">

                                {{-- Product Image --}}
                                <div class="aspect-[4/3] bg-gray-50 overflow-hidden relative">
                                    @if ($product->quantity_available > 0 && $product->quantity_available <= 3 && $product->always_available_flag == false)
                                        <div
                                            class="absolute bottom-2 left-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-md flex items-center justify-center gap-1.5 shadow-sm border border-yellow-100 z-10">
                                            <span
                                                class="text-[10px] font-bold text-yellow-700 uppercase tracking-wide">Stok
                                                Terbatas</span>
                                        </div>
                                    @endif

                                    @if ($hasPromo && $promoBadge)
                                        <div class="absolute top-3 left-3 z-10 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md"
                                            style="background-color: #ae1504;">
                                            {{ $promoBadge }}
                                        </div>
                                    @endif

                                    @if ($firstImage)
                                        <img src="{{ asset($firstImage) }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div
                                            class="absolute inset-0 flex items-center justify-center text-gray-300 group-hover:scale-105 transition-transform duration-500">
                                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M2 21h19v-3H2v3zM20 8H4V4h16v4zm0 10H4v-6h16v6z" />
                                            </svg>
                                        </div>
                                    @endif

                                    @if ($product->quantity_available < 1 && $product->always_available_flag == false)
                                        <div class="absolute inset-0 bg-white/40 flex items-center justify-center">
                                            <span
                                                class="bg-gray-800 text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-lg">Habis</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Product Info --}}
                                <div class="flex-1 flex flex-col p-3">
                                    <h3
                                        class="font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-choco transition-colors text-sm sm:text-base">
                                        {{ $product->name }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mb-2 sm:mb-3 line-clamp-2">
                                        {{ $product->description }}</p>

                                    <div class="mt-auto flex items-end justify-between gap-2">
                                        {{-- Price --}}
                                        <div class="flex-shrink min-w-0 max-w-[55%] sm:max-w-none">
                                            @if ($hasPromo)
                                                <p class="text-[10px] sm:text-xs text-gray-500 line-through truncate">
                                                    Rp {{ number_format($basePrice, 0, ',', '.') }}</p>
                                                <p class="font-bold text-gray-900 text-xs sm:text-base truncate">Rp
                                                    {{ number_format($discountedBase, 0, ',', '.') }}</p>
                                            @else
                                                <p class="font-bold text-gray-900 text-xs sm:text-base truncate">Rp
                                                    {{ number_format($basePrice, 0, ',', '.') }}</p>
                                            @endif
                                        </div>

                                        {{-- Quantity Controls --}}
                                        <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                                            <button
                                                class="minus-btn hidden w-6 h-6 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl border border-[#ae1504] text-[#ae1504] flex items-center justify-center hover:bg-gray-100 transition-all"
                                                data-id="{{ $product->id }}">
                                                <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <span
                                                class="qty hidden text-[11px] sm:text-sm font-bold text-gray-800 min-w-[1rem] sm:min-w-[1.5rem] text-center"
                                                data-id="{{ $product->id }}">0</span>
                                            @if ($product->quantity_available < 1 && $product->always_available_flag == false)
                                                <button
                                                    class="h-6 w-6 sm:h-9 sm:w-9 rounded-lg sm:rounded-xl bg-gray-100 text-gray-300 flex items-center justify-center cursor-not-allowed border border-gray-200"
                                                    disabled>
                                                    <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            @else
                                                <button
                                                    class="plus-btn h-6 w-6 sm:h-9 sm:w-9 rounded-lg sm:rounded-xl bg-[#ae1504] text-white flex items-center justify-center hover:bg-[#8a1103] shadow-md hover:shadow-lg transition-all active:scale-95"
                                                    data-id="{{ $product->id }}">
                                                    <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
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

@include('pages.employee.cashier.dashboard.modals.pembelian-modal')

<style>
    /* Full width layout */
    #menu-container {
        max-width: 100%;
        margin: 0 auto;
    }

    /* Responsive padding untuk layar besar */
    @media (min-width: 1536px) {
        #menu-container {
            padding-left: 3rem;
            padding-right: 3rem;
        }
    }

    @media (min-width: 1920px) {
        #menu-container {
            padding-left: 4rem;
            padding-right: 4rem;
        }
    }

    /* sheet bisa di-scroll sendiri */
    #modalSheet {
        max-height: 80vh;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
    }

    /* saat modal terbuka, kunci scroll body */
    body.modal-open {
        overflow: hidden;
    }

    /* ✅ KOMPENSASI UNTUK DESKTOP - Lebih Spesifik */
    @media (min-width: 768px) {

        /* Body padding */
        body.modal-open {
            padding-right: var(--scrollbar-width, 0px) !important;
        }

        /* Floating cart bar */
        body.modal-open #floatingCartBar {
            right: var(--scrollbar-width, 0px);
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

    /* Filter button style */
    .filter-btn {
        background-color: transparent !important;
        color: #4b5563;
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

    /* Sheet modal */
    #modalSheet {
        transform: translateY(100%);
        transition: transform 0.3s ease-out;
    }

    /* Saat modal ditampilkan */
    #parentOptionsModal.show #modalSheet {
        transform: translateY(0);
    }

    #floatingCartBar {
        padding-bottom: env(safe-area-inset-bottom);
    }


    /* Hilangkan tombol clear bawaan browser */
    #menuSearch::-webkit-search-cancel-button {
        display: none;
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
</style>

@php
    $productsData = $partner_products
        ->map(function ($p) {
            $firstImage = $p->pictures[0]['path'] ?? null;
            $promo = $p->promotion;
            $base = (float) $p->price;
            $discBase = $base;

            if ($promo) {
                if ($promo->promotion_type === 'percentage') {
                    $discBase = max(0, $base * (1 - $promo->promotion_value / 100));
                } else {
                    $discBase = max(0, $base - (float) $promo->promotion_value);
                }
            }

            // Transform parent_options dengan accessor quantity_available
            $parentOptions = ($p->parent_options ?? collect())
                ->map(function ($po) {
                    return [
                        'id' => $po->id,
                        'name' => $po->name,
                        'description' => $po->description,
                        'provision' => $po->provision,
                        'provision_value' => $po->provision_value,
                        'options' => ($po->options ?? collect())
                            ->map(function ($opt) {
                                return [
                                    'id' => $opt->id,
                                    'name' => $opt->name,
                                    'price' => (float) $opt->price,
                                    'quantity_available' => $opt->quantity_available,
                                    'always_available_flag' => (bool) $opt->always_available_flag,
                                    'description' => $opt->description,
                                ];
                            })
                            ->values()
                            ->all(),
                    ];
                })
                ->values()
                ->all();

            return [
                'id' => $p->id,
                'name' => $p->name,
                'description' => strip_tags((string) $p->description),
                'price' => $base,
                'discounted_base' => $discBase,
                'promotion' => $promo
                    ? [
                        'id' => $promo->id,
                        'type' => $promo->promotion_type,
                        'value' => (float) $promo->promotion_value,
                    ]
                    : null,
                'image' => $firstImage ? asset($firstImage) : null,
                'parent_options' => $parentOptions,
                'quantity_available' => $p->quantity_available,
                'always_available_flag' => (int) $p->always_available_flag,
            ];
        })
        ->values()
        ->toArray();
@endphp

<script>
    window.initPembelianTab = function initPembelianTab() {
        // if (window.__PEMBELIAN_INITED__) return;   // ✅ cegah double init
        // window.__PEMBELIAN_INITED__ = true;
        // Cleanup event listener
        const oldPayBtn = document.getElementById('checkoutPayBtn');
        if (oldPayBtn) {
            const newPayBtn = oldPayBtn.cloneNode(true);
            oldPayBtn.parentNode.replaceChild(newPayBtn, oldPayBtn);
        }
        // ======== ELEMENTS (Modal Parent Options) ========
        const modal = document.getElementById('parentOptionsModal');
        const modalContent = document.getElementById('modalContent');
        const modalHeader = document.getElementById('modalHeader');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const saveModalBtn = document.getElementById('saveModalBtn');
        const modalQtyMinus = document.getElementById('modalQtyMinus');
        const modalQtyPlus = document.getElementById('modalQtyPlus');
        const modalQtyValue = document.getElementById('modalQtyValue');

        // ======== ELEMENTS (Floating Cart & Manager & Checkout) ========
        const floatingBar = document.getElementById('floatingCartBar');
        const floatingTotal = document.getElementById('floatingCartTotal');
        const floatingCount = document.getElementById('floatingCartCount');
        const btnCartClear = document.getElementById('floatingCartClear');
        const btnCartPay = document.getElementById('floatingCartPay');

        const cartManagerModal = document.getElementById('cartManagerModal');
        const cartManagerSheet = document.getElementById('cartManagerSheet');
        const cartManagerBody = document.getElementById('cartManagerBody');
        const cartManagerTotal = document.getElementById('cartManagerTotal');
        const closeCartManager = document.getElementById('closeCartManager');
        const cartManagerDone = document.getElementById('cartManagerDone');

        const checkoutModal = document.getElementById('checkoutModal');
        const checkoutSheet = document.getElementById('checkoutSheet');
        const checkoutBody = document.getElementById('checkoutBody');
        const checkoutGrandTotal = document.getElementById('checkoutGrandTotal');
        const checkoutCloseBtn = document.getElementById('checkoutCloseBtn');
        const checkoutCancelBtn = document.getElementById('checkoutCancelBtn');
        const checkoutPayBtn = document.getElementById('checkoutPayBtn');
        const orderNameInput = document.getElementById('orderName');
        const orderTableInput = document.getElementById('orderTable');

        // ======== DATA ========
        const productsData = @json($productsData);

        // ======== SCROLL LOCK MANAGER (ANTI MACET, ANTI LOMPAT) ========
        const __SCROLLER = document.getElementById('pembelianScroll') || document.scrollingElement;

        // simpan state global (biar aman kalau beberapa modal pakai lock)
        let __LOCK_COUNT = 0;
        let __savedScrollTop = 0;

        let __savedBodyOverflow = '';
        let __savedHtmlOverflow = '';
        let __savedBodyPaddingRight = '';

        function getScrollbarWidth() {
            return window.innerWidth - document.documentElement.clientWidth;
        }

        function lockBodyScroll() {
            if (__LOCK_COUNT === 0) {
                // simpan posisi scroll dari scroller aktif
                __savedScrollTop = (__SCROLLER === document.scrollingElement) ?
                    (window.pageYOffset || document.documentElement.scrollTop || 0) :
                    (__SCROLLER.scrollTop || 0);

                // simpan style awal
                __savedBodyOverflow = document.body.style.overflow;
                __savedHtmlOverflow = document.documentElement.style.overflow;
                __savedBodyPaddingRight = document.body.style.paddingRight;

                // kunci scroll
                const sbw = getScrollbarWidth();
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden'; // ✅ penting (Safari/iOS/Chrome tertentu)
                if (sbw > 0) document.body.style.paddingRight = `${sbw}px`;
            }

            __LOCK_COUNT++;
        }

        function unlockBodyScroll() {
            __LOCK_COUNT = Math.max(0, __LOCK_COUNT - 1);
            if (__LOCK_COUNT > 0) return; // masih ada modal lain yang membuka

            // Force remove class modal-open
            document.body.classList.remove('modal-open');

            // restore style
            document.body.style.overflow = __savedBodyOverflow || '';
            document.documentElement.style.overflow = __savedHtmlOverflow || '';
            document.body.style.paddingRight = __savedBodyPaddingRight || '';

            // restore posisi scroll
            if (__SCROLLER === document.scrollingElement) {
                window.scrollTo(0, __savedScrollTop || 0);
            } else {
                __SCROLLER.scrollTop = __savedScrollTop || 0;
            }
        }

        function forceUnlockAll() {
            // Paksa reset semua lock
            __LOCK_COUNT = 0;

            // Hapus class modal-open
            document.body.classList.remove('modal-open');

            // Force reset overflow
            document.body.style.overflow = '';
            document.body.style.removeProperty('overflow');
            document.documentElement.style.overflow = '';
            document.documentElement.style.removeProperty('overflow');

            // Reset padding
            document.body.style.paddingRight = '';
            document.body.style.removeProperty('padding-right');

            // Hapus aria-hidden jika ada
            document.body.removeAttribute('aria-hidden');

            console.log('🔓 Force unlock executed');
        }

        // ======== STATE ========
        let currentProductId = null;
        let selectedOptions = [];
        let modalQty = 1;
        let modalNote = '';

        // cart: { key: { productId, options:number[], qty, unitPrice, lineTotal, note } }
        let cart = {};
        // track last key per product for minus shortcut
        let lastKeyPerProduct = {};

        const rupiahFmt = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        });

        // ======== HELPERS (PRODUCT & PRICE) ========
        function getProductDataById(pid) {
            return productsData.find(p => p.id === pid) || {};
        }

        function keyOf(productId, optionsArr) {
            const opts = (optionsArr || []).slice().sort((a, b) => a - b).join('-');
            return `${productId}::${opts}`;
        }

        function computeUnitPrice(productId, optionsArr) {
            const pd = getProductDataById(productId);
            const base = Number(pd.discounted_base ?? pd.price) || 0; // <— PAKAI DISCOUNTED BASE
            let optSum = 0;
            (pd.parent_options || []).forEach(po => {
                (po.options || []).forEach(opt => {
                    if ((optionsArr || []).includes(opt.id)) optSum += Number(opt.price) || 0;
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

        function addToCart(productId, optionsArr, qty = 1, note = '') {
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
                    promo_id: promoId
                };
            } else if (cart[key].promo_id == null) {
                cart[key].promo_id = promoId;
            }

            // batas maksimal untuk kombinasi produk+opsi ini (dikurangi yang sudah ada di cart)
            const cap = maxQtyForSelection(pd, optionsArr);
            const existingLineQty = (cart[key]?.qty || 0);

            // sisa qty yang masih boleh ditambah untuk LINE ini
            const canAdd = Number.isFinite(cap) ? Math.max(0, cap - existingLineQty) : qty;

            if (Number.isFinite(cap) && canAdd <= 0) {
                Swal && Swal.fire({
                    toast: true,
                    position: 'top-end',
                    timer: 1800,
                    showConfirmButton: false,
                    icon: 'warning',
                    title: 'Stok tidak mencukupi'
                });
                return key;
            }

            const addQty = Number.isFinite(cap) ? Math.min(qty, canAdd) : qty;

            if (addQty < qty) {
                Swal && Swal.fire({
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false,
                    icon: 'info',
                    title: `Qty disesuaikan (maks ${existingLineQty + addQty})`
                });
            }

            // ✅ hanya tambah SEKALI
            cart[key].qty += addQty;

            if (note && note.trim().length > 0) cart[key].note = note.trim();
            recomputeLineTotal(key);
            lastKeyPerProduct[productId] = key;

            updateFloatingCartBar();
            return key;
        }

        function minusFromCart(productId, optionsArr) {
            const key = keyOf(productId, optionsArr);
            if (!cart[key]) return;
            cart[key].qty = Math.max(0, cart[key].qty - 1);
            if (cart[key].qty === 0) delete cart[key];
            else recomputeLineTotal(key);
            updateFloatingCartBar();
        }

        function sumQtyByProduct(productId) {
            let total = 0;
            for (const k in cart)
                if (cart[k].productId === productId) total += cart[k].qty;
            return total;
        }

        function sumQtyUsingOption(optionId) {
            let total = 0;
            for (const k in cart) {
                const row = cart[k];
                if (!row || row.qty <= 0) continue;
                if ((row.options || []).includes(optionId)) total += row.qty;
            }
            return total;
        }

        function getOptionObj(productData, optId) {
            for (const po of (productData.parent_options || [])) {
                for (const opt of (po.options || [])) {
                    if (opt.id === optId) return opt;
                }
            }
            return null;
        }

        // remaining stok produk (dikurangi qty di cart)
        function remainingProductQty(productData) {
            const always = Boolean(productData.always_available_flag);
            if (always) return Number.POSITIVE_INFINITY;
            const stock = Math.max(0, Math.floor(Number(productData.quantity_available) || 0));
            const used = sumQtyByProduct(productData.id);
            return Math.max(0, stock - used);
        }

        // remaining stok option (dikurangi qty di cart)
        function remainingOptionQty(productData, optId) {
            const opt = getOptionObj(productData, optId);
            if (!opt) return 0;
            const always = Boolean(opt.always_available_flag);
            if (always) return Number.POSITIVE_INFINITY;
            const stock = Math.max(0, Math.floor(Number(opt.quantity_available) || 0));
            const used = sumQtyUsingOption(optId);
            return Math.max(0, stock - used);
        }

        // batas maksimal qty untuk kombinasi produk+opsi tsb
        function maxQtyForSelection(productData, optionIds) {
            let cap = remainingProductQty(productData);
            (optionIds || []).forEach(oid => {
                cap = Math.min(cap, remainingOptionQty(productData, oid));
            });
            if (!Number.isFinite(cap)) cap = Number.POSITIVE_INFINITY;
            return cap;
        }

        function updateProductBadge(productId) {
            const qtySpans = document.querySelectorAll('.qty[data-id="' + productId + '"]');
            const minusBtns = document.querySelectorAll('.minus-btn[data-id="' + productId + '"]');
            const total = sumQtyByProduct(productId);

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

        // ======== MODAL RENDERING ========
        function provisionInfoText(provision, value) {
            const prov = String(provision || '').toUpperCase();
            const val = Number(value);
            const hasN = Number.isFinite(val);
            switch (prov) {
                case 'EXACT':
                    return hasN ? `Pilih ${val}` : 'Pilih';
                case 'MAX':
                    return hasN ? `Maksimal ${val}` : 'Maksimal';
                case 'MIN':
                    return hasN ? `Minimal ${val}` : 'Minimal';
                case 'OPTIONAL MAX':
                    return hasN ? `Opsional, maksimal ${val}` : 'Opsional, maksimal';
                case 'OPTIONAL':
                    return 'Opsional';
                default:
                    return '';
            }
        }

        function enforceProvision(poDiv, provision, value) {
            const checkboxes = Array.from(poDiv.querySelectorAll('input[type="checkbox"], input[type="radio"]'));
            const prov = String(provision || '').toUpperCase();
            const val = Number(value);

            function updateState() {
                const checked = checkboxes.filter(c => c.checked && !c.disabled);

                // ✅ Update selectedOptions berdasarkan semua checkbox yang checked
                selectedOptions = Array.from(
                    modalContent.querySelectorAll(
                        'input[type="checkbox"]:checked:not([disabled]), input[type="radio"]:checked:not([disabled])'
                    )
                ).map(c => parseInt(c.value, 10));

                // Logic provision enforcement
                if (prov === 'EXACT' && val > 1) {
                    if (checked.length >= val) {
                        checkboxes.forEach(c => {
                            if (!c.checked && !c.dataset.sold) {
                                c.disabled = true;
                                const label = c.closest('label');
                                if (label) {
                                    label.classList.add('opacity-60', 'cursor-not-allowed');
                                }
                            }
                        });
                    } else {
                        checkboxes.forEach(c => {
                            if (!c.dataset.sold) {
                                c.disabled = false;
                                const label = c.closest('label');
                                if (label) {
                                    label.classList.remove('opacity-60', 'cursor-not-allowed');
                                }
                            }
                        });
                    }
                }

                if (prov === 'MAX' || prov === 'OPTIONAL MAX') {
                    if (checked.length >= val) {
                        checkboxes.forEach(c => {
                            if (!c.checked && !c.dataset.sold) {
                                c.disabled = true;
                                const label = c.closest('label');
                                if (label) {
                                    label.classList.add('opacity-60', 'cursor-not-allowed');
                                }
                            }
                        });
                    } else {
                        checkboxes.forEach(c => {
                            if (!c.dataset.sold) {
                                c.disabled = false;
                                const label = c.closest('label');
                                if (label) {
                                    label.classList.remove('opacity-60', 'cursor-not-allowed');
                                }
                            }
                        });
                    }
                }

                if (prov === 'MIN') {
                    // Untuk MIN, tidak disable checkbox saat sudah mencapai minimum
                    checkboxes.forEach(c => {
                        if (!c.dataset.sold) {
                            c.disabled = false;
                            const label = c.closest('label');
                            if (label) {
                                label.classList.remove('opacity-60', 'cursor-not-allowed');
                            }
                        }
                    });
                }

                if (currentProductId) {
                    const pd = getProductDataById(currentProductId);
                    calcModalTotal(pd);
                }

                // ✅ Validasi setelah state update
                validateAllProvisions();
            }

            // ✅ Attach event listener ke semua checkbox/radio
            checkboxes.forEach(cb => {
                // Remove existing listener jika ada
                cb.removeEventListener('change', updateState);
                // Add new listener
                cb.addEventListener('change', updateState);
            });

            // Initial state
            updateState();
        }

        function validateAllProvisions() {
            const groups = Array.from(modalContent.querySelectorAll('[data-provision-group]'));
            let allValid = true;

            groups.forEach(group => {
                const prov = String(group.dataset.provision || '').toUpperCase();
                const val = Number(group.dataset.value);
                const checked = group.querySelectorAll(
                    'input[type="checkbox"]:checked:not([disabled]), input[type="radio"]:checked:not([disabled])'
                ).length;

                let groupValid = true;
                let msg = '';

                if (prov === 'EXACT' && val > 0) {
                    if (checked !== val) {
                        groupValid = false;
                        msg = `Harus memilih tepat ${val} opsi.`;
                    }
                } else if (prov === 'MAX') {
                    if (checked < 1) {
                        groupValid = false;
                        msg = 'Minimal pilih 1 opsi.';
                    } else if (checked > val) {
                        groupValid = false;
                        msg = `Maksimal memilih ${val} opsi.`;
                    }
                } else if (prov === 'MIN') {
                    if (checked < val) {
                        groupValid = false;
                        msg = `Minimal memilih ${val} opsi.`;
                    }
                } else if (prov === 'OPTIONAL MAX') {
                    // ✅ Optional MAX tidak wajib pilih, tapi kalau pilih maksimal sesuai val
                    if (checked > val) {
                        groupValid = false;
                        msg = `Maksimal memilih ${val} opsi.`;
                    }
                    // Jika tidak ada yang dipilih, tetap valid karena optional
                } else if (prov === 'OPTIONAL') {
                    // ✅ Optional selalu valid
                    groupValid = true;
                }

                if (!groupValid) {
                    allValid = false;
                }

                // Kelola error text kecil di bawah group
                let errEl = group.querySelector('.provision-error');
                if (!errEl && !groupValid) {
                    errEl = document.createElement('p');
                    errEl.className = 'provision-error text-xs text-red-500 mt-2 px-1';
                    group.appendChild(errEl);
                }

                if (errEl) {
                    if (groupValid) {
                        errEl.remove();
                    } else {
                        errEl.textContent = msg;
                    }
                }
            });

            // ✅ Update tombol simpan
            if (saveModalBtn) {
                saveModalBtn.disabled = !allValid;
                if (!allValid) {
                    saveModalBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    saveModalBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            return allValid;
        }

        function calcModalTotal(productData) {
            const baseDisc = Number(productData.discounted_base ?? productData.price) || 0; // <— PAKAI DISCOUNTED
            const optSum = (productData.parent_options || []).reduce((sum, po) => {
                (po.options || []).forEach(opt => {
                    if (selectedOptions.includes(opt.id)) sum += Number(opt.price) || 0;
                });
                return sum;
            }, 0);
            const total = (baseDisc + optSum) * modalQty;
            const priceEl = document.getElementById('modalTotalPrice');
            if (priceEl) priceEl.innerText = `(${rupiahFmt.format(total)})`;
        }

        function showModal(productData) {
            // reset
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

            // === HEADER PRODUK (Style Baru seperti Customer) ===
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
                badge.className =
                    'inline-flex items-center rounded-md bg-orange-100 px-2 py-1 text-xs font-medium text-orange-600 ring-1 ring-inset ring-orange-600/20';
                badge.textContent = 'Popular';
                titleContainer.appendChild(badge);
            }

            infoDiv.appendChild(titleContainer);

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
            const remainProd = remainingProductQty(productData);

            if (alwaysAvailable) {
                stockEl.innerHTML = '<span class="text-green-600">✓ Selalu Tersedia</span>';
            } else if (remainProd > 5) {
                stockEl.innerHTML = `<span class="text-green-600">Stok: ${remainProd}</span>`;
            } else if (remainProd > 0) {
                stockEl.innerHTML = `<span class="text-orange-600">⚠ Sisa: ${remainProd}</span>`;
            } else {
                stockEl.innerHTML = '<span class="text-red-600">✕ Stok Habis</span>';
            }
            infoDiv.appendChild(stockEl);

            // Gambar produk
            if (productData.image) {
                const imgContainer = document.createElement('div');
                imgContainer.className =
                    'relative flex-none w-20 h-20 sm:w-22 sm:h-22 rounded-lg overflow-hidden bg-gray-100';

                const imgDiv = document.createElement('div');
                imgDiv.className = 'w-full h-full bg-center bg-cover';
                imgDiv.style.backgroundImage = `url("${productData.image}")`;
                imgDiv.setAttribute('data-alt', productData.name || 'Product Image');

                imgContainer.appendChild(imgDiv);
                headerWrapper.appendChild(imgContainer);
            }

            modalHeader.appendChild(headerWrapper);

            // === PARENT OPTIONS (Style Baru seperti Customer) ===
            const parentOptions = productData.parent_options || [];

            if (parentOptions.length === 0) {
                saveModalBtn.disabled = false;
                saveModalBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            parentOptions.forEach((po) => {
                const section = document.createElement('div');
                section.className =
                    'px-5 py-2 sm:px-6 sm:py-3 border-b border-gray-200/50 dark:border-white/10';
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

                // Options container dengan DESAIN BARU
                const optionsContainer = document.createElement('div');
                optionsContainer.className = 'flex flex-col gap-3';

                const val = Number(po.provision_value);
                const isRadioMode = val === 1 && (provision === 'EXACT' || provision === 'MAX' ||
                    provision === 'OPTIONAL MAX');

                (po.options || []).forEach(opt => {
                    const label = document.createElement('label');
                    label.className =
                        'group relative flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 cursor-pointer hover:border-[#ae1504]/30 hover:bg-[#ae1504]/5 transition-all';

                    const leftDiv = document.createElement('div');
                    leftDiv.className = 'flex items-center gap-3';

                    // Custom indicator (bulat seperti customer)
                    const indicator = document.createElement('div');
                    indicator.className =
                        'flex h-5 w-5 items-center justify-center rounded-full border-2 border-gray-300 transition-all flex-shrink-0';

                    const dot = document.createElement('div');
                    dot.className =
                        'h-2.5 w-2.5 rounded-full bg-choco opacity-0 transition-opacity';
                    indicator.appendChild(dot);

                    // Hidden input
                    const input = document.createElement('input');
                    input.type = isRadioMode ? 'radio' : 'checkbox';
                    input.value = opt.id;
                    input.className = 'sr-only'; // ✅ Gunakan sr-only, bukan hidden
                    if (isRadioMode) input.name = `radio_${po.id}`;

                    const nameSpan = document.createElement('span');
                    nameSpan.className = 'text-sm font-medium text-gray-900 transition-colors';
                    nameSpan.textContent = opt.name;

                    leftDiv.appendChild(indicator);
                    leftDiv.appendChild(input);
                    leftDiv.appendChild(nameSpan);
                    label.appendChild(leftDiv);

                    // Price & Stock
                    const priceSpan = document.createElement('span');
                    priceSpan.className = 'text-sm text-gray-500 font-medium flex-shrink-0';

                    const remOpt = remainingOptionQty(productData, opt.id);
                    const alwaysOpt = (opt.always_available_flag === 1 || opt
                        .always_available_flag === true);
                    const priceNum = Number(opt.price) || 0;

                    if (!alwaysOpt && remOpt < 1) {
                        priceSpan.textContent = 'Habis';
                        priceSpan.className = 'text-sm text-red-600 font-medium flex-shrink-0';
                        input.disabled = true;
                        input.dataset.sold = '1';
                        label.classList.add('opacity-60', 'cursor-not-allowed', 'bg-gray-50');
                        label.classList.remove('hover:border-[#ae1504]/30', 'hover:bg-[#ae1504]/5');
                    } else {
                        priceSpan.textContent = (priceNum === 0) ? 'Free' : rupiahFmt.format(
                            priceNum);

                        if (!alwaysOpt && remOpt <= 5) {
                            const stockSpan = document.createElement('span');
                            stockSpan.className = 'text-orange-600 ml-1 text-xs';
                            stockSpan.textContent = `(sisa ${remOpt})`;
                            priceSpan.appendChild(stockSpan);
                        }

                        // ✅ PERBAIKAN: Event listener yang lebih sederhana dan efektif
                        input.addEventListener('change', function() {
                            // Update visual indicator
                            if (this.checked) {
                                dot.classList.remove('opacity-0');
                                dot.classList.add('opacity-100');
                                indicator.classList.remove('border-gray-300');
                                indicator.classList.add('border-choco');
                                label.classList.add('border-[#ae1504]', 'bg-[#ae1504]/10');
                                label.classList.remove('border-gray-200');

                                if (isRadioMode) {
                                    // Reset visual semua radio lain di group ini
                                    section.querySelectorAll(`input[name="radio_${po.id}"]`)
                                        .forEach(r => {
                                            if (r !== this && !r.disabled) {
                                                const parentLabel = r.closest('label');
                                                const ind = parentLabel?.querySelector(
                                                    '.flex.h-5');
                                                const d = parentLabel?.querySelector(
                                                    '.h-2\\.5');
                                                if (ind) {
                                                    ind.classList.remove(
                                                        'border-choco');
                                                    ind.classList.add(
                                                        'border-gray-300');
                                                }
                                                if (d) {
                                                    d.classList.remove('opacity-100');
                                                    d.classList.add('opacity-0');
                                                }
                                                if (parentLabel) {
                                                    parentLabel.classList.remove(
                                                        'border-[#ae1504]',
                                                        'bg-[#ae1504]/10');
                                                    parentLabel.classList.add(
                                                        'border-gray-200');
                                                }
                                            }
                                        });
                                }
                            } else {
                                // Uncheck
                                dot.classList.remove('opacity-100');
                                dot.classList.add('opacity-0');
                                indicator.classList.remove('border-choco');
                                indicator.classList.add('border-gray-300');
                                label.classList.remove('border-[#ae1504]',
                                    'bg-[#ae1504]/10');
                                label.classList.add('border-gray-200');
                            }

                            // ✅ Update selectedOptions SETELAH visual update
                            // Ini akan di-handle oleh enforceProvision
                        });
                    }

                    label.appendChild(priceSpan);
                    optionsContainer.appendChild(label);
                });

                section.appendChild(optionsContainer);
                modalContent.appendChild(section);

                // ✅ Panggil enforceProvision SETELAH semua option di-render
                enforceProvision(section, po.provision, po.provision_value);
            });

            // Catatan
            const noteWrap = document.createElement('div');
            noteWrap.className = 'px-5 py-2 sm:px-6 sm:py-3';

            const noteLabel = document.createElement('h3');
            noteLabel.className = 'text-lg font-bold text-gray-900 mb-2';
            noteLabel.innerHTML =
                `Catatan <span class="text-xs font-normal uppercase tracking-wider text-gray-500 bg-gray-100 dark:bg-white/10 pl-0.5 pr-2 py-1 rounded">(Opsional)</span>`;

            const noteInputWrap = document.createElement('div');
            noteInputWrap.className = 'relative';

            const noteArea = document.createElement('textarea');
            noteArea.id = 'modalNote';
            noteArea.className =
                'w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:border-[#ae1504] focus:ring-1 focus:ring-[#ae1504] outline-none transition-all';
            noteArea.placeholder = 'Contoh: "Pedas level 2, saus terpisah, tanpa bawang."';
            noteArea.rows = 3;
            noteArea.maxLength = 200;
            noteArea.addEventListener('input', (e) => {
                modalNote = e.target.value;
            });

            const noteHint = document.createElement('div');
            noteHint.className = 'absolute bottom-3 right-3 text-xs text-gray-400 pointer-events-none';
            noteHint.textContent = 'Maks. 200 karakter';

            noteInputWrap.appendChild(noteArea);
            noteInputWrap.appendChild(noteHint);
            noteWrap.appendChild(noteLabel);
            noteWrap.appendChild(noteInputWrap);
            modalContent.appendChild(noteWrap);

            calcModalTotal(productData);

            // Tampilkan modal
            while (__LOCK_COUNT > 0) unlockBodyScroll();
            modal.classList.add('show');
            lockBodyScroll();
            modal.classList.remove('hidden');

            // init qty & total
            modalQty = 1;
            calcModalTotal(productData);
            updateModalQtyDisplay();
        }

        function updateModalQtyDisplay() {
            if (!modalQtyValue) return;
            modalQtyValue.innerText = modalQty;
            modalQtyMinus.disabled = modalQty <= 1;

            if (!currentProductId) return;

            const pd = getProductDataById(currentProductId);
            const cap = maxQtyForSelection(pd, selectedOptions); // ✅ stok produk & option yg dipilih
            const stockInfo = document.getElementById('productStockInfo');

            if (!Number.isFinite(cap)) {
                // unlimited (selalu tersedia)
                modalQtyPlus.disabled = false;
                if (stockInfo) stockInfo.innerHTML = `<span class="text-green-600">✓ Selalu Tersedia</span>`;
                return;
            }

            if (cap <= 0) {
                modalQtyPlus.disabled = true;
                if (stockInfo) stockInfo.innerHTML =
                    `<span class="text-red-600">✕ Stok tidak cukup untuk pilihan ini</span>`;
                return;
            }

            // jaga qty tidak melewati cap
            if (modalQty > cap) modalQty = cap;
            modalQtyValue.innerText = modalQty;

            modalQtyPlus.disabled = modalQty >= cap;

            // info cap
            if (stockInfo) {
                if (cap <= 5) stockInfo.innerHTML =
                    `<span class="text-orange-600">⚠ Maksimum untuk pilihan ini: ${cap}</span>`;
                else stockInfo.innerHTML = `<span class="text-green-600">Stock: ${cap}</span>`;
            }
        }


        modalQtyMinus.addEventListener('click', () => {
            if (modalQty > 1) {
                modalQty--;
                updateModalQtyDisplay();
                if (currentProductId) calcModalTotal(getProductDataById(currentProductId));
            }
        });
        modalQtyPlus.addEventListener('click', () => {
            const pd = getProductDataById(currentProductId);
            if (!pd) return;

            const productStock = Number(pd.quantity_available) || 0;
            const alwaysAvailable = Boolean(pd.always_available_flag);

            // ===== TAMBAHAN: Validasi Stok =====
            if (!alwaysAvailable && modalQty >= productStock) {
                // Jangan tambah qty jika sudah mencapai stok maksimal
                return;
            }

            modalQty++;
            updateModalQtyDisplay();
            calcModalTotal(pd);
        });

        // CLOSE modal
        // ===== CLOSE Parent Options Modal (rapi + pasti unlock) =====
        function closeParentModal() {
            modal.classList.remove('show');

            setTimeout(() => {
                modal.classList.add('hidden');

                forceUnlockAll();

                // ✅ safety net (kalau ada yang nyangkut dari library lain)
                document.body.style.overflow = '';
                document.documentElement.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 300);
        }
        closeModalBtn.addEventListener('click', closeParentModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeParentModal();
        });


        // SAVE modal (SATU handler saja)
        saveModalBtn.addEventListener('click', function() {
            if (!currentProductId) return;
            const pd = getProductDataById(currentProductId);
            const noteEl = document.getElementById('modalNote');
            const noteVal = (noteEl ? noteEl.value : modalNote || '').trim();
            addToCart(currentProductId, selectedOptions, modalQty, noteVal);
            updateProductBadge(currentProductId);
            printCart('Cart (saved with options):');

            // reset state + tutup modal
            currentProductId = null;
            selectedOptions = [];
            modalQty = 1;
            modalNote = '';
            updateModalQtyDisplay();
            modal.classList.remove('show');
            setTimeout(() => {
                modal.classList.add('hidden');
                forceUnlockAll();
            }, 300);
        });

        // ======== UI: PLUS/MINUS PRODUCT CARD ========
        document.querySelectorAll('.plus-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = parseInt(this.dataset.id, 10);
                const pd = getProductDataById(productId);
                if (pd && (pd.parent_options || []).length > 0) {
                    currentProductId = productId;
                    selectedOptions = [];
                    modalQty = 1;
                    modalNote = '';
                    showModal(pd);
                } else {
                    addToCart(productId, []);
                    updateProductBadge(productId);
                    printCart('Cart (no-options +):');
                }
            });
        });
        document.querySelectorAll('.minus-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = parseInt(this.dataset.id, 10);
                let key = lastKeyPerProduct[productId];
                if (!key || !cart[key] || cart[key].qty === 0) {
                    key = Object.keys(cart).find(k => cart[k].productId === productId && cart[k]
                        .qty > 0);
                }
                if (!key) {
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

        // ======== FLOATING CART ========
        function cartGrandTotal() {
            let total = 0;
            for (const k in cart) {
                const row = cart[k];
                if (!row) continue;
                const unit = (typeof row.unitPrice === 'number' && row.unitPrice > 0) ? row.unitPrice :
                    computeUnitPrice(row.productId, row.options);
                total += unit * row.qty;
            }
            return total;
        }

        function cartTotalQty() {
            let total = 0;
            for (const k in cart) total += cart[k].qty || 0;
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
                // Format: "1 Item" untuk singular, "2 Items" untuk plural
                countEl.textContent = count === 1 ? '1 Item' : `${count} Items`;
                bar.classList.remove('hidden');
            } else {
                bar.classList.add('hidden');
            }
        }

        // Buka Cart Manager ketika klik ikon trash (untuk melihat/atur isi)
        btnCartClear.addEventListener('click', (e) => {
            e.preventDefault();
            openCartManager();
        });

        // Buka Checkout
        btnCartPay.addEventListener('click', () => {
            openCheckoutModal();
        });

        // ======== CART MANAGER ========
        function optionNameById(productData, optId) {
            for (const po of (productData.parent_options || [])) {
                for (const opt of (po.options || []))
                    if (opt.id === optId) return opt.name;
            }
            return null;
        }

        function cartRows() {
            const arr = [];
            for (const k in cart) {
                const row = cart[k];
                if (!row || row.qty <= 0) continue;
                const pd = getProductDataById(row.productId) || {};
                const optNames = (row.options || []).map(id => optionNameById(pd, id)).filter(Boolean);
                const unit = (typeof row.unitPrice === 'number' && row.unitPrice > 0) ? row.unitPrice :
                    computeUnitPrice(row.productId, row.options);
                arr.push({
                    key: k,
                    productId: row.productId,
                    productName: pd.name || 'Produk',
                    image: pd.image || null,
                    desc: pd.description || '',
                    optNames,
                    qty: row.qty,
                    unit,
                    line: unit * row.qty,
                    options: row.options || [],
                    note: row.note || ''
                });
            }
            return arr;
        }

        // ✅ TAMBAHKAN 2 HELPER FUNCTIONS INI
        function computeMaxQtyAllowedForLine(productData, optionsArr) {
            const maxByProduct = remainingProductQty(productData);

            let maxByOptions = Number.POSITIVE_INFINITY;
            const chosen = new Set((optionsArr || []).map(Number));

            (productData.parent_options || []).forEach(po => {
                (po.options || []).forEach(opt => {
                    if (chosen.has(Number(opt.id))) {
                        const remOpt = remainingOptionQty(productData, opt.id);
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
            const remProduct = remainingProductQty(productData);

            if (!alwaysProduct && remProduct === maxAllowed) {
                labels.push(`${productData.name || 'Produk'} (stok produk)`);
            }

            const chosen = new Set((optionsArr || []).map(Number));
            (productData.parent_options || []).forEach(po => {
                (po.options || []).forEach(opt => {
                    if (!chosen.has(Number(opt.id))) return;

                    const alwaysOpt = (opt.always_available_flag === 1 || opt
                        .always_available_flag === true);
                    if (alwaysOpt) return;

                    const remOpt = remainingOptionQty(productData, opt.id);
                    if (remOpt === maxAllowed) {
                        labels.push(`${opt.name || 'Option'} (stok opsi)`);
                    }
                });
            });

            return [...new Set(labels)];
        }

        function renderCartManager() {
            const rows = cartRows();

            if (rows.length === 0) {
                cartManagerBody.innerHTML = `
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <svg class="w-20 h-20 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-gray-500 text-lg font-medium">
                    Keranjang masih kosong
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
                ${reachedMax ? `Tidak dapat menambah "${who}" lagi (stok habis)` : `Tersisa ${remainingAdd} lagi untuk "${who}"`}
            </p>`;
                }

                const optsText = r.optNames.length ?
                    `<p class="text-gray-600 text-xs sm:text-sm mt-1 leading-relaxed">${r.optNames.join(', ')}</p>` :
                    '';
                const noteText = r.note ?
                    `<p class="text-gray-700 text-xs mt-1.5 italic leading-relaxed">
                <span class="font-semibold">Catatan:</span> ${r.note}
            </p>` : '';

                const img = r.image ?
                    `<div class="shrink-0">
                <img src="${r.image}" alt="${r.productName}" 
                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg object-cover border border-gray-200">
            </div>` : '';

                const descText = r.desc ?
                    `<p class="text-gray-500 text-xs sm:text-sm mt-0.5 leading-relaxed">${r.desc}</p>` :
                    '';

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

        function openCartManager() {
            // Reset lock sebelum buka modal
            while (__LOCK_COUNT > 0) unlockBodyScroll();

            renderCartManager();
            cartManagerModal.classList.add('show');
            cartManagerModal.classList.remove('hidden');
            lockBodyScroll();
        }

        function closeCartManagerModal() {
            cartManagerModal.classList.remove('show');
            setTimeout(() => {
                cartManagerModal.classList.add('hidden');
                forceUnlockAll();
            }, 300);
        }
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
                const cap = maxQtyForSelection(pd, row.options);
                if (Number.isFinite(cap) && row.qty >= cap) {
                    Swal && Swal.fire({
                        toast: true,
                        position: 'top-end',
                        timer: 1800,
                        showConfirmButton: false,
                        icon: 'warning',
                        title: 'Stok tidak mencukupi'
                    });
                } else {
                    addToCart(row.productId, row.options);
                }
            } else if (minusBtn) minusFromCart(row.productId, row.options);
            updateProductBadge(row.productId);
            updateFloatingCartBar();
            renderCartManager();
        });
        closeCartManager.addEventListener('click', closeCartManagerModal);
        cartManagerDone.addEventListener('click', closeCartManagerModal);
        cartManagerModal.addEventListener('click', (e) => {
            if (e.target === cartManagerModal) closeCartManagerModal();
        });

        // ======== CHECKOUT ========
        function getOptionDetail(productId, optId) {
            const pd = getProductDataById(productId);
            if (!pd) return {
                name: null,
                price: 0,
                parentName: null
            };
            for (const po of (pd.parent_options || [])) {
                for (const opt of (po.options || [])) {
                    if (opt.id === optId) return {
                        name: opt.name,
                        price: Number(opt.price) || 0,
                        parentName: po.name || null
                    };
                }
            }
            return {
                name: null,
                price: 0,
                parentName: null
            };
        }

        function checkoutRows() {
            const rows = [];
            for (const k in cart) {
                const r = cart[k];
                if (!r || r.qty <= 0) continue;
                const pd = getProductDataById(r.productId) || {};
                const unit = (typeof r.unitPrice === 'number' && r.unitPrice > 0) ? r.unitPrice : computeUnitPrice(r
                    .productId, r.options);
                const optionsDetail = (r.options || []).map(oid => getOptionDetail(r.productId, oid));
                rows.push({
                    key: k,
                    productId: r.productId,
                    name: pd.name || 'Produk',
                    image: pd.image || null,
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

        function renderCheckoutModal() {
            const rows = checkoutRows();
            if (rows.length === 0) {
                checkoutBody.innerHTML =
                    `<div class="text-center text-gray-500 py-8">Keranjang masih kosong.</div>`;
                checkoutGrandTotal.textContent = rupiah(0);
                return;
            }

            checkoutBody.innerHTML = rows.map(r => {
                // 1) Baris opsi
                const opts = (r.optionsDetail || []).map(od => {
                    const label = od.parentName ? `${od.parentName}: ${od.name}` : od.name;
                    return `
                <div class="w-full flex items-center justify-between text-xs text-gray-600">
                    <span class="truncate">${label}</span>
                    <span class="shrink-0">${od.price === 0 ? '(Free)' : rupiah(od.price)}</span>
                </div>
            `;
                }).join('');

                // 2) Catatan
                const note = r.note ?
                    `<div class="mt-2 text-xs italic text-gray-700"><span class="font-semibold">Catatan:</span> ${r.note}</div>` :
                    '';

                // 3) Hitung base price dengan promo
                const sumOpts = (r.optionsDetail || []).reduce((s, od) => s + (Number(od.price) || 0), 0);
                const rawBase = Number(r.basePrice) || 0;
                const baseDisc = Math.max(0, (Number(r.unit) || 0) - sumOpts);

                const baseRow = (baseDisc < rawBase) ?
                    `<div class="w-full flex items-center justify-between text-xs text-gray-600">
                <span>Harga dasar</span>
                <span class="shrink-0">
                    <span class="line-through mr-1">${rupiah(rawBase)}</span>
                    <span class="font-medium text-[#ae1504]">${rupiah(baseDisc)}</span>
                </span>
            </div>` :
                    `<div class="w-full flex items-center justify-between text-xs text-gray-600">
                <span>Harga dasar</span>
                <span class="shrink-0">${rupiah(rawBase)}</span>
            </div>`;

                // 4) Return card dengan desain baru
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
            checkoutGrandTotal.textContent = rupiah(grand);
        }

        function openCheckoutModal() {
            while (__LOCK_COUNT > 0) unlockBodyScroll();

            renderCheckoutModal();
            // Reset payment method
            document.querySelectorAll('.payment-method-radio').forEach(radio => {
                radio.checked = false;
            })
            orderTableInput.value = '';
            checkoutPayBtn.disabled = true;

            checkoutModal.classList.remove('hidden');
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    checkoutSheet.classList.remove('scale-95', 'opacity-0');
                    checkoutSheet.classList.add('scale-100', 'opacity-100');
                });
            });

            lockBodyScroll();
            orderNameInput && orderNameInput.focus({
                preventScroll: true
            });
        }

        function closeCheckoutModal() {
            checkoutSheet.classList.remove('scale-100', 'opacity-100');
            checkoutSheet.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                checkoutModal.classList.add('hidden');
                forceUnlockAll();
            }, 300);
        }

        function updatePayBtnState() {
            const selectedPayment = document.querySelector('.payment-method-radio:checked');
            const hasMethod = !!selectedPayment?.value;
            const hasName = !!(orderNameInput.value || '').trim();
            const hasTable = !!(orderTableInput.value || '').trim();

            checkoutPayBtn.disabled = !(hasMethod && hasName && hasTable);
        }

        // Event listeners untuk validation
        document.querySelectorAll('.payment-method-radio').forEach(radio => {
            radio.addEventListener('change', updatePayBtnState);
        });
        orderTableInput.addEventListener('change', updatePayBtnState);
        orderNameInput.addEventListener('input', updatePayBtnState);

        checkoutCloseBtn.addEventListener('click', closeCheckoutModal);
        checkoutCancelBtn.addEventListener('click', closeCheckoutModal);
        checkoutModal.addEventListener('click', (e) => {
            if (e.target === checkoutModal) closeCheckoutModal();
        });

        checkoutPayBtn.addEventListener('click', async () => {
            const selectedPayment = document.querySelector('.payment-method-radio:checked');
            const paymentMethod = selectedPayment?.value; // 'CASH' | 'QRIS'
            const orderTable = orderTableInput.value;
            const orderName = orderNameInput.value;
            const payload = Object.entries(cart).map(([key, r]) => ({
                product_id: r.productId,
                option_ids: r.options,
                qty: r.qty,
                unit_price: r.unitPrice ?? computeUnitPrice(r.productId, r.options),
                note: r.note || '',
                promo_id: (r.promo_id != null) ?
                    r.promo_id : (getProductDataById(r.productId)?.promotion?.id ??
                        null), // fallback
            }));

            const grandTotal = payload.reduce((s, it) => s + (it.unit_price * it.qty), 0);
            if (!paymentMethod) {
                Swal && Swal.fire({
                    icon: 'warning',
                    title: 'Metode belum dipilih'
                });
                return;
            }
            if (!orderTable) {
                Swal && Swal.fire({
                    icon: 'warning',
                    title: 'Meja belum dipilih'
                });
                return;
            }
            if (!orderName) {
                Swal && Swal.fire({
                    icon: 'warning',
                    title: 'Nama belum diisi'
                });
                return;
            }
            if (grandTotal <= 0) {
                Swal && Swal.fire({
                    icon: 'info',
                    title: 'Keranjang kosong'
                });
                return;
            }

            // ===== VALIDASI STOK REAL-TIME =====
            checkoutPayBtn.disabled = true;
            Swal.fire({
                title: 'Memeriksa ketersediaan stok…',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const tokenEl = document.querySelector('meta[name="csrf-token"]');
                const csrf = tokenEl ? tokenEl.content : null;

                const checkStockUrl = `/employee/cashier/check-stock`;
                const stockCheckResponse = await fetch(checkStockUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...(csrf ? {
                            'X-CSRF-TOKEN': csrf
                        } : {})
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        items: payload
                    })
                });

                if (!stockCheckResponse.ok) {
                    throw new Error(`HTTP ${stockCheckResponse.status}`);
                }

                const stockResult = await stockCheckResponse.json();

                if (!stockResult.success) {
                    Swal.close();

                    const result = await Swal.fire({
                        icon: 'error',
                        title: 'Stok Tidak Mencukupi',
                        html: `
                                    <div class="text-center">
                                        <p class="mt-3 text-sm text-gray-600">Silakan refresh halaman untuk melihat stok terbaru.</p>
                                    </div>
                                `,
                        showCancelButton: true,
                        confirmButtonText: 'Refresh Halaman',
                        cancelButtonText: 'Batal',
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
                console.error('Stock check error:', stockCheckError);
                Swal.close();

                // ✅ PERBAIKAN: Tampilkan toast + return (hentikan)
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                });

                await Toast.fire({
                    icon: 'warning',
                    title: 'Gagal memverifikasi stok'
                });

                checkoutPayBtn.disabled = false;
                return; // ✅ PENTING: Hentikan eksekusi, jangan lanjut checkout
            }

            const selectedTableOption = orderTableInput.options[orderTableInput.selectedIndex];
            const tableNo = selectedTableOption?.getAttribute('data-table-no') || orderTable;
            const tableClass = selectedTableOption?.getAttribute('data-table-class') || '';
            const tableDisplay = tableClass ? `Meja ${tableNo} — ${tableClass}` : `Meja ${tableNo}`;

            const confirm = await Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Checkout',
                html: `<div style="text-align:left">
      <div>Nama Pemesan: <b>${orderName}</b></div>
      <div>Metode: <b>${paymentMethod.toUpperCase()}</b></div>
      <div>Meja: <b>${tableDisplay}</b></div>
      <div>Total: <b>${rupiahFmt.format(grandTotal)}</b></div>
    </div>`,
                showCancelButton: true,
                confirmButtonText: 'Ya, bayar',
                cancelButtonText: 'Batal'
            });
            if (!confirm.isConfirmed) return;

            const PARTNER_SLUG = @json($partner_slug);
            const TABLE_CODE = @json($table_code);
            const checkoutUrl = `/employee/cashier/checkout-order`;
            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const csrf = tokenEl ? tokenEl.content : null;

            checkoutPayBtn.disabled = true;
            Swal.fire({
                title: 'Memproses pembayaran…',
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
                    body: JSON.stringify({
                        items: payload,
                        payment_method: paymentMethod,
                        order_table: orderTable,
                        order_name: orderName,
                        total_amount: grandTotal
                    })
                });
                if (r.redirected && r.url) {
                    window.location.assign(r.url);
                    return;
                }
                let res;
                const ct = r.headers.get('content-type') || '';
                if (ct.includes('application/json')) res = await r.json();
                else {
                    const text = await r.text();
                    try {
                        res = JSON.parse(text);
                    } catch {
                        throw new Error('Respons tidak valid dari server.');
                    }
                }
                if (!r.ok) throw new Error(res?.message || `Request failed: ${r.status}`);
                if (res?.redirect) {
                    window.location.assign(res.redirect);
                    return;
                }
                // Tutup modal checkout
                closeCheckoutModal();

                // Bersihkan keranjang & UI terkait
                cart = {};
                lastKeyPerProduct = {};
                updateFloatingCartBar();
                // reset badge qty di daftar produk
                // reset badge qty di semua tampilan (Hot & kategori)
                document.querySelectorAll('.qty').forEach(el => {
                    el.textContent = '0';
                    el.classList.add('hidden');
                });
                document.querySelectorAll('.minus-btn').forEach(btn => btn.classList.add('hidden'));


                // Notifikasi
                Swal.close();
                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Checkout berhasil diproses.',
                    timer: 1400,
                    showConfirmButton: false
                });

                window.location.reload();


            } catch (err) {
                Swal.close();
                const msg = (err?.message || '').toLowerCase().includes('csrf') ?
                    'Sesi keamanan kedaluwarsa. Silakan muat ulang halaman dan coba lagi.' :
                    (err?.message || 'Checkout gagal.');
                await Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: msg
                });
            } finally {
                checkoutPayBtn.disabled = false;
            }
        });

        // ======== FILTER KATEGORI + SEARCH ========
        (function setupCategoryAndSearch() {
            if (window.__CATSEARCH_INITED__) return;
            window.__CATSEARCH_INITED__ = true;
            const filterButtons = document.querySelectorAll('.filter-btn');
            const categoryGroups = document.querySelectorAll('.category-group');
            const items = document.querySelectorAll('.menu-item');
            const hotGroup = document.querySelector('.hot-products-group');
            const hotItems = document.querySelectorAll('.menu-item-hot');

            const searchInput = document.getElementById('menuSearch');
            const searchClear = document.getElementById('menuSearchClear');

            let activeCategory = 'all';
            let query = '';

            function norm(s) {
                return (s || '')
                    .toString()
                    .toLowerCase()
                    .normalize('NFD') // hapus diakritik
                    .replace(/[\u0300-\u036f]/g, '')
                    .trim();
            }

            function itemMatches(item, cat, q) {
                const catOk = (cat === 'all' || item.dataset.category === cat);
                if (!catOk) return false;
                if (!q) return true;

                // ✅ Perbaikan selector - sesuaikan dengan struktur HTML
                const nameEl = item.querySelector('h3'); // bukan h5
                const descEl = item.querySelector('p.text-xs.text-gray-500'); // lebih spesifik

                const name = norm(nameEl ? nameEl.textContent : '');
                const desc = norm(descEl ? descEl.textContent : '');
                return name.includes(q) || desc.includes(q);
            }

            function applyFilters() {
                const nq = norm(query);
                // tombol active
                filterButtons.forEach(b => b.classList.toggle('active', b.dataset.category === activeCategory ||
                    (activeCategory === 'all' && b.dataset.category === 'all')));

                // tampilkan item sesuai kombinasi
                let anyShown = false;
                items.forEach(item => {
                    const show = itemMatches(item, activeCategory, nq);
                    item.style.display = show ? 'flex' : 'none';
                    if (show) anyShown = true;
                });

                if (hotGroup) {
                    let hotShown = 0;
                    hotItems.forEach(item => {
                        // menu-item-hot juga sudah ikut di-set display oleh items.forEach
                        if (item.style.display !== 'none') hotShown++;
                    });
                    hotGroup.style.display = (hotShown > 0) ? 'block' : 'none';
                }

                // group heading: tampil kalau minimal satu item di group yang lolos
                categoryGroups.forEach(group => {
                    if (activeCategory === 'all') {
                        // cek ada anak visible?
                        const visibleChild = Array.from(group.querySelectorAll('.menu-item')).some(it =>
                            it.style.display !== 'none');
                        group.style.display = visibleChild ? 'block' : 'none';
                    } else {
                        group.style.display = (group.dataset.category === activeCategory) ?
                            (Array.from(group.querySelectorAll('.menu-item')).some(it => it.style
                                .display !== 'none') ? 'block' : 'none') :
                            'none';
                    }
                });

                // pesan "tidak ada hasil"
                ensureNoResultBanner(!anyShown, nq);
                // tombol clear
                if (searchClear) searchClear.style.display = nq ? 'inline-flex' : 'none';
            }

            // Banner "no result" ringan
            let noResEl = null;

            function ensureNoResultBanner(show, nq) {
                // Reuse kalau sudah ada di DOM
                if (!noResEl) {
                    noResEl = document.getElementById('noResultBanner');
                }
                // Kalau memang belum ada, buat baru
                if (!noResEl) {
                    noResEl = document.createElement('div');
                    noResEl.id = 'noResultBanner';
                    noResEl.className = 'p-6 text-center text-gray-500';
                    noResEl.style.display = 'none';
                    const menuContainer = document.getElementById('menu-container');
                    if (menuContainer && menuContainer.parentNode) {
                        menuContainer.parentNode.insertBefore(noResEl, menuContainer);
                    }
                }
                if (!noResEl) return;

                if (show) {
                    noResEl.innerHTML =
                        `Tidak ada menu yang cocok untuk <b>"${nq}"</b>${activeCategory!=='all' ? ` pada kategori terpilih` : ''}.`;
                    noResEl.style.display = 'block';
                } else {
                    noResEl.style.display = 'none';
                }
            }


            // Debounce input
            let t = null;

            function debounce(fn, wait = 200) {
                return function(...args) {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), wait);
                }
            }

            // Listeners
            filterButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    activeCategory = btn.dataset.category;
                    applyFilters();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', debounce((e) => {
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

            // inisialisasi awal
            applyFilters();
        })();


        // ======== EXPOSED (optional) ========
        window.PEMBELIAN = {
            cart,
            addToCart,
            minusFromCart,
            updateFloatingCartBar,
        };
    };
    // opsional: kalau tab ini juga dipakai non-AJAX, kamu boleh auto-init jika elemen kunci ada:
    if (document.getElementById('parentOptionsModal')) {
        // cegah double init: hanya jalankan bila tab pembelian saat ini ada di DOM utama
        window.initPembelianTab();
    }
</script>
