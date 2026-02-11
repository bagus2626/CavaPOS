@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.stock_list'))
@section('page_title', __('messages.owner.products.stocks.all_stock'))

@section('content')
    <link rel="stylesheet" href="{{ asset('css/mobile-owner.css') }}">

    <div class="modern-container">
        <div class="container-modern">
            {{-- PAGE HEADER - DESKTOP ONLY --}}
            <div class="page-header only-desktop">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.stocks.all_stock') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.products.stocks.manage_inventory_subtitle') }}</p>
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



            {{-- DESKTOP SEARCH & FILTER --}}
            <div class="modern-card mb-4 only-desktop">
                <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                    <div class="table-controls">
                        <div class="search-filter-group">
                            <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                                <span class="input-icon">
                                    <span class="material-symbols-outlined">search</span>
                                </span>
                                <input type="text" id="searchInput" class="form-control-modern with-icon"
                                    placeholder="{{ __('messages.owner.products.stocks.search_placeholder') }}">
                            </div>

                            <div class="select-wrapper" style="min-width: 200px;">
                                <form action="{{ route('owner.user-owner.stocks.index') }}" method="GET"
                                    id="locationFilterForm">
                                    <select id="locationFilter" name="filter_location" class="form-control-modern"
                                        onchange="this.form.submit()">
                                        <option value="owner" {{ $filterLocation == 'owner' ? 'selected' : '' }}>
                                            {{ __('messages.owner.products.stocks.owner_warehouse') }}
                                        </option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->username }}"
                                                {{ $filterLocation == $partner->username ? 'selected' : '' }}>
                                                {{ $partner->name }} (Outlet)
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                                </form>
                            </div>
                        </div>

                        <a href="{{ route('owner.user-owner.stocks.create') }}" class="btn-modern btn-primary-modern">
                            <span class="material-symbols-outlined">add</span>
                            {{ __('messages.owner.products.stocks.add_stock_item') }}
                        </a>
                    </div>
                </div>
            </div>

            @include('pages.owner.products.stock.display')

        </div>
    </div>

    {{-- MOBILE FLOATING ADD BUTTON --}}
    <a href="{{ route('owner.user-owner.stocks.create') }}" class="btn-add-outlet-mobile">
        <span class="material-symbols-outlined">add</span>
    </a>



    <div class="filter-modal-body">



    </div>
@endsection

<style>
    /* Hide desktop elements on mobile */
    @media (max-width: 768px) {
        .only-desktop {
            display: none !important;
        }
    }

    /* Hide mobile elements on desktop */
    @media (min-width: 769px) {
        .only-mobile {
            display: none !important;
        }
    }

    /* Mobile Category/Location Dropdown */
    .mobile-category-dropdown {
        margin-top: 12px;
    }

    .select-wrapper-mobile {
        position: relative;
        width: 100%;
    }

    .form-control-mobile {
        width: 100%;
        padding: 12px 40px 12px 16px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        background-color: #fff;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        cursor: pointer;
    }

    .form-control-mobile:focus {
        outline: none;
        border-color: #ae1504;
    }

    .select-arrow-mobile {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #666;
        font-size: 20px;
    }
</style>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ==========================================
        // MOBILE FILTER MODAL FUNCTIONS
        // ==========================================
        function toggleMobileStockFilter() {
            const modal = document.getElementById('mobileStockFilterModal');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileStockFilter() {
            const modal = document.getElementById('mobileStockFilterModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        function setStockTypeFilter(type) {
            // Update modal pills
            updateModalPillsActiveState(type);

            // Set currentFilterType dan trigger filter
            if (window.currentStockFilterType !== undefined) {
                window.currentStockFilterType = type;
            }

            if (typeof window.__applyStockFilter === 'function') {
                window.__applyStockFilter(type);
            }

            closeMobileStockFilter();
        }

        function updateModalPillsActiveState(activeType) {
            const pills = document.querySelectorAll('.mobile-filter-modal .modal-pill');
            const iconWrappers = document.querySelectorAll('.mobile-filter-modal .pill-icon-wrapper');
            const checkIcons = document.querySelectorAll('.mobile-filter-modal .pill-check');

            pills.forEach(pill => pill.classList.remove('active'));
            iconWrappers.forEach(wrapper => wrapper.classList.remove('active'));
            checkIcons.forEach(check => check.remove());

            let activePill;
            if (activeType === 'linked') {
                activePill = document.querySelector('.modal-pill[onclick*="setStockTypeFilter(\'linked\')"]');
            } else if (activeType === 'direct') {
                activePill = document.querySelector('.modal-pill[onclick*="setStockTypeFilter(\'direct\')"]');
            } else if (activeType === 'all') {
                activePill = document.querySelector('.modal-pill[onclick*="setStockTypeFilter(\'all\')"]');
            }

            if (activePill) {
                activePill.classList.add('active');
                const iconWrapper = activePill.querySelector('.pill-icon-wrapper');
                if (iconWrapper) {
                    iconWrapper.classList.add('active');
                }
                const pillRight = activePill.querySelector('.pill-right');
                if (pillRight) {
                    pillRight.innerHTML = '<span class="material-symbols-outlined pill-check">check_circle</span>';
                }
            }
        }

        // Close modal when clicking backdrop
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('mobileStockFilterModal');
            if (modal && e.target === modal) {
                closeMobileStockFilter();
            }
        });

        // ==============================
        // Debounce helper untuk search
        // ==============================
        function debouncedStockSearch(el, delay = 400) {
            if (!el) return;
            if (el._debounceTimer) clearTimeout(el._debounceTimer);
            el._debounceTimer = setTimeout(() => {
                window.__applyStockFilter?.();
            }, delay);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const mobileSearchInput = document.getElementById('mobileSearchInput');
            const mobileStockSubtitle = document.getElementById('mobileStockSubtitle');
            const tableBody = document.getElementById('stockTableBody');
            const mobileList = document.getElementById('stockMobileList');
            const paginationWrapper = document.getElementById('stockPagination');

            if (!tableBody || !mobileList || !paginationWrapper) {
                console.error('Stock UI containers not found');
                return;
            }

            const allStocksData = @json($allStocksFormatted ?? []);

            let filtered = [...allStocksData];
            let currentFilterType = 'linked';
            window.currentStockFilterType = currentFilterType;
            const itemsPerPage = 10;
            let currentPage = 1;

            // ==============================
            // UPDATE MOBILE SUBTITLE
            // ==============================
            function updateMobileSubtitle() {
                if (mobileStockSubtitle) {
                    mobileStockSubtitle.textContent =
                        `${filtered.length} {{ __('messages.owner.products.stocks.stock_list') }}`;
                }
            }

            // ==============================
            // FILTER FUNCTION
            // ==============================
            function applyFilter(filterType) {
                // Jika ada parameter filterType, gunakan itu
                if (filterType !== undefined) {
                    currentFilterType = filterType;
                    window.currentStockFilterType = filterType;
                }

                const q = (searchInput?.value || '').toLowerCase().trim();

                filtered = allStocksData.filter(s => {
                    // Tab filter
                    let matchesTab = true;
                    if (currentFilterType === 'linked') matchesTab = s.stock_type === 'linked';
                    else if (currentFilterType === 'direct') matchesTab = s.stock_type === 'direct';
                    // 'all' = tidak ada filter, semua lewat

                    // Search
                    const hay = `${s.stock_code || ''} ${s.stock_name || ''}`.toLowerCase();
                    const matchesSearch = !q || hay.includes(q);

                    return matchesTab && matchesSearch;
                });

                currentPage = 1;
                updateMobileSubtitle();
                render();
            }

            window.__applyStockFilter = applyFilter;

            // ==============================
            // RENDER
            // ==============================
            function render() {
                renderDesktopTable();
                renderMobileCards();
                renderPagination();
            }

            function emptyStateHtml() {
                return `
                    <div class="table-empty-state" style="padding: 20px;">
                        <span class="material-symbols-outlined">search_off</span>
                        <h4>{{ __('messages.owner.products.stocks.no_results_found') }}</h4>
                        <p>{{ __('messages.owner.products.stocks.adjust_search_filter') }}</p>
                    </div>
                `;
            }

            function getPagedItems() {
                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                return {
                    start,
                    end,
                    items: filtered.slice(start, end)
                };
            }

            function renderDesktopTable() {
                const {
                    start,
                    items
                } = getPagedItems();

                tableBody.innerHTML = '';

                if (items.length === 0) {
                    tableBody.innerHTML = `
                        <tr class="empty-filter-row">
                            <td colspan="7" class="text-center">${emptyStateHtml()}</td>
                        </tr>
                    `;
                    return;
                }

                items.forEach((s, idx) => {
                    const rowNumber = start + idx + 1;

                    const formattedQty = new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(Number(s.display_quantity ?? 0));

                    const unitDisplay = s.display_unit_name ?
                        `<span class="badge-modern badge-info">${escapeHtml(s.display_unit_name)}</span>` :
                        `<span class="text-muted small">({{ __('messages.owner.products.stocks.base_unit') }})</span>`;

                    const tr = document.createElement('tr');
                    tr.className = 'table-row';
                    tr.innerHTML = `
                        <td class="text-center text-muted">${rowNumber}</td>
                        <td class="mono fw-600">${escapeHtml(s.stock_code ?? '')}</td>
                        <td><span class="fw-600">${escapeHtml(s.stock_name ?? '')}</span></td>
                        <td>${formattedQty}</td>
                        <td>${unitDisplay}</td>
                        <td><span class="fw-600">${escapeHtml(String(s.last_price_per_unit ?? ''))}</span></td>
                        <td class="text-center">
                            <div class="table-actions">
                                <button onclick="deleteStock(${s.id})"
                                    class="btn-table-action delete"
                                    title="{{ __('messages.owner.products.stocks.delete') }}">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });
            }

            function renderMobileCards() {
                const {
                    items
                } = getPagedItems();

                mobileList.innerHTML = '';

                if (items.length === 0) {
                    mobileList.innerHTML = emptyStateHtml();
                    return;
                }

                items.forEach(s => {
                    const formattedQty = new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(Number(s.display_quantity ?? 0));

                    const unitName = s.display_unit_name ||
                        '{{ __('messages.owner.products.stocks.base_unit') }}';
                    const code = s.stock_code ?? '';
                    const name = s.stock_name ?? '';

                    const card = document.createElement('div');
                    card.className = 'stock-card';
                    card.innerHTML = `
                        <div class="stock-card__top">
                            <div class="stock-card__title">
                                <div class="stock-card__code">${escapeHtml(code)}</div>
                                <div class="stock-card__name">${escapeHtml(name)}</div>
                            </div>
                        </div>

                        <div class="stock-card__bottom">
                            <span class="stock-chip">
                                <span class="material-symbols-outlined">inventory</span>
                                <span>${formattedQty}</span>
                            </span>

                            <span class="stock-chip">
                                <span class="material-symbols-outlined">straighten</span>
                                <span>${escapeHtml(unitName)}</span>
                            </span>

                            <span class="stock-chip">
                                <span class="material-symbols-outlined">payments</span>
                                <span>${escapeHtml(String(s.last_price_per_unit ?? ''))}</span>
                            </span>

                            <div class="stock-actions">
                                <button type="button" class="btn-card-action danger" onclick="deleteStock(${s.id})">
                                    <span class="material-symbols-outlined">delete</span>
                                    <span>{{ __('messages.owner.products.stocks.delete') }}</span>
                                </button>
                            </div>
                        </div>
                    `;
                    mobileList.appendChild(card);
                });
            }

            // Select location filter
            function selectLocationFilter(location) {
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = '{{ route('owner.user-owner.stocks.index') }}';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'filter_location';
                input.value = location;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }

            // Clear all filters
            function clearStockFilters() {
                window.location.href = '{{ route('owner.user-owner.stocks.index') }}?filter_location=owner';
            }

            function renderPagination() {
                const totalPages = Math.ceil(filtered.length / itemsPerPage);

                paginationWrapper.innerHTML = '';

                if (totalPages <= 1) return;

                const nav = document.createElement('nav');
                nav.setAttribute('role', 'navigation');
                nav.setAttribute('aria-label', 'Pagination Navigation');

                const ul = document.createElement('ul');
                ul.className = 'pagination';

                // Previous Button
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;

                if (currentPage === 1) {
                    prevLi.innerHTML = `
                        <span class="page-link" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/>
                            </svg>
                        </span>
                    `;
                } else {
                    prevLi.innerHTML = `
                        <a href="#" class="page-link" data-page="${currentPage - 1}" aria-label="Previous">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/>
                            </svg>
                        </a>
                    `;
                }
                ul.appendChild(prevLi);

                // Page Numbers
                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                        const pageLi = document.createElement('li');
                        pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;

                        if (i === currentPage) {
                            pageLi.innerHTML = `<span class="page-link" aria-current="page">${i}</span>`;
                        } else {
                            pageLi.innerHTML = `<a href="#" class="page-link" data-page="${i}">${i}</a>`;
                        }

                        ul.appendChild(pageLi);
                    } else if (i === currentPage - 2 || i === currentPage + 2) {
                        const dotsLi = document.createElement('li');
                        dotsLi.className = 'page-item disabled';
                        dotsLi.innerHTML = `<span class="page-link">...</span>`;
                        ul.appendChild(dotsLi);
                    }
                }

                // Next Button
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;

                if (currentPage === totalPages) {
                    nextLi.innerHTML = `
                        <span class="page-link" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
                            </svg>
                        </span>
                    `;
                } else {
                    nextLi.innerHTML = `
                        <a href="#" class="page-link" data-page="${currentPage + 1}" aria-label="Next">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
                            </svg>
                        </a>
                    `;
                }
                ul.appendChild(nextLi);

                nav.appendChild(ul);
                paginationWrapper.appendChild(nav);

                // Add click handlers
                nav.querySelectorAll('a.page-link[data-page]').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = parseInt(this.dataset.page);
                        if (page > 0 && page <= totalPages && page !== currentPage) {
                            currentPage = page;
                            render();
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        }
                    });
                });
            }

            // ==============================
            // EVENT LISTENERS
            // ==============================
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    applyFilter();
                });
            }

            // Mobile search input - sync dengan desktop
            if (mobileSearchInput) {
                mobileSearchInput.addEventListener('input', function() {
                    if (searchInput) {
                        searchInput.value = this.value;
                    }
                    applyFilter();
                });
            }

            // ==============================
            // INITIALIZE
            // ==============================
            updateMobileSubtitle();
            updateModalPillsActiveState(currentFilterType);
            applyFilter();

            // ==============================
            // helper: escape HTML
            // ==============================
            function escapeHtml(str) {
                return String(str ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }
        });
    </script>
    <script>
        // ==========================================
        // DELETE STOCK FUNCTION
        // ==========================================
        function deleteStock(stockId) {
            Swal.fire({
                title: '{{ __('messages.owner.products.stocks.delete_confirmation_1') }}',
                text: '{{ __('messages.owner.products.stocks.delete_confirmation_2') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ae1504',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __('messages.owner.products.stocks.delete_confirmation_3') }}',
                cancelButtonText: '{{ __('messages.owner.products.stocks.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/owner/user-owner/stocks/delete-stock/${stockId}`;
                    form.style.display = 'none';

                    form.innerHTML = `
                        @csrf
                        <input type="hidden" name="_method" value="DELETE">
                    `;

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush
