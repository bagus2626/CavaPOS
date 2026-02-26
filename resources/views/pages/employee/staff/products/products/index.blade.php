@extends('layouts.staff')

@section('title', __('messages.owner.products.outlet_products.product_list'))
@section('page_title', __('messages.owner.products.outlet_products.outlet_products'))

@section('content')
    <link rel="stylesheet" href="{{ asset('css/mobile-owner.css') }}">
    <div class="modern-container">
        <div class="container-modern">
            <div class="page-header only-desktop">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.outlet_products.outlet_products') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.products.outlet_products.manage_products_subtitle') }}</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-modern">
                    <div class="alert-icon"><span class="material-symbols-outlined">check_circle</span></div>
                    <div class="alert-content">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon"><span class="material-symbols-outlined">error</span></div>
                    <div class="alert-content">{{ session('error') }}</div>
                </div>
            @endif

            <div class="modern-card mb-4 only-desktop">
                <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                    <form method="GET" action="{{ url()->current() }}" id="outletProductFilterForm">
                        <div class="table-controls">
                            <div class="search-filter-group">
                                
                                <div class="input-wrapper" style="flex: 1; max-width: 420px;">
                                    <span class="input-icon"><span class="material-symbols-outlined">search</span></span>
                                    <input type="text" name="q" id="productSearchInput"
                                        value="{{ $q ?? request('q') }}" class="form-control-modern with-icon"
                                        placeholder="{{ __('messages.owner.products.outlet_products.search_placeholder') ?? 'Search product...' }}"
                                        oninput="debouncedSubmit(this, 500)">
                                </div>

                                <div class="select-wrapper" style="min-width: 220px;">
                                    <select name="category" id="categoryFilter" class="form-control-modern"
                                        onchange="submitFilterResetPage()">
                                        <option value="">
                                            {{ __('messages.owner.products.outlet_products.all_category_dropdown') }}
                                        </option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                                </div>

                                <input type="hidden" name="page" id="pageInput" value="{{ request('page', 1) }}">
                            </div>

                            <button class="btn-modern btn-primary-modern btn-add-product" type="button" data-toggle="modal"
                                data-target="#addProductModal">
                                <span class="material-symbols-outlined">add</span>
                                {{ __('messages.owner.products.outlet_products.add_product') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @include('pages.employee.staff.products.products.display')

        </div>
    </div>

    @include('pages.employee.staff.products.products.modal')
@endsection

<style>
    @media (max-width: 768px) {
        .only-desktop {
            display: none !important;
        }
    }

    @media (min-width: 769px) {
        .only-mobile {
            display: none !important;
        }
    }

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
        // Logika Mobile Filter Disesuaikan (Tanpa Outlet)
        document.getElementById('openFilterModalBtn')?.addEventListener('click', function() {
            const modal = document.getElementById('mobileFilterModal');
            if(modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        });

        function closeFilterModal() {
            const modal = document.getElementById('mobileFilterModal');
            if(modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        document.getElementById('closeFilterModalBtn')?.addEventListener('click', closeFilterModal);
        document.getElementById('filterModalBackdrop')?.addEventListener('click', closeFilterModal);

        function clearAllFilters() {
            const params = new URLSearchParams(window.location.search);
            params.delete('category');
            params.delete('q');
            params.delete('page');
            window.location.search = params.toString();
        }

        function changeCategoryMobile(selectEl) {
            const categoryId = selectEl.value;
            const params = new URLSearchParams(window.location.search);
            if (categoryId) params.set('category', categoryId);
            else params.delete('category');
            params.delete('page');
            window.location.search = params.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInputMobile = document.getElementById('productSearchInputMobile');
            if (searchInputMobile) {
                let timer;
                searchInputMobile.addEventListener('input', function() {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        const params = new URLSearchParams(window.location.search);
                        const q = (searchInputMobile.value || '').trim();
                        if (q) params.set('q', q);
                        else params.delete('q');
                        params.delete('page');
                        window.location.search = params.toString();
                    }, 500);
                });
            }
        });
    </script>

    <script>
        // Prefix dinamis berdasarkan role user (manager / supervisor)
        const staffRole = "{{ strtolower(Auth::guard('employee')->user()->role) }}";

        async function deleteProduct(id) {
            const result = await Swal.fire({
                title: '{{ __('messages.owner.products.outlet_products.delete_confirmation_1') }}',
                text: "{{ __('messages.owner.products.outlet_products.delete_confirmation_2') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ae1504',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __('messages.owner.products.outlet_products.delete_confirmation_3') }}',
                cancelButtonText: '{{ __('messages.owner.products.outlet_products.cancel') }}'
            });

            if (!result.isConfirmed) return;

            try {
                // Route dinamis menyesuaikan role
                const routeName = staffRole + '.products.destroy';
                let url = "{{ route('employee.manager.products.index', ':id') }}"; // Placeholder
                
                // Gunakan URL statis agar aman jika ada perbedaan nama route
                url = `/${staffRole}/products/${id}`; 

                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                if (res.ok) {
                    await Swal.fire({
                        title: '{{ __('messages.owner.products.outlet_products.success') }}',
                        text: '{{ __('messages.owner.products.outlet_products.delete_success') }}',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    location.reload();
                } else {
                    const data = await res.json();
                    Swal.fire({
                        title: '{{ __('messages.owner.products.outlet_products.failed') }}',
                        text: data.message || res.statusText,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    title: '{{ __('messages.owner.products.outlet_products.error') }}',
                    text: '{{ __('messages.owner.products.outlet_products.delete_error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('addProductModal');
            const form = document.getElementById('outletProductQuickAddForm');
            const categorySelect = document.getElementById('qp_category_id');
            const mpBox = document.getElementById('qp_master_product_box');
            const mpSelectAll = document.getElementById('qp_check_all');
            const mpError = document.getElementById('qp_mp_error');
            const qtyInput = document.getElementById('qp_quantity');
            const statusSelect = document.getElementById('qp_is_active');

            form?.setAttribute('autocomplete', 'off');
            form?.querySelectorAll('input, select').forEach(el => el.setAttribute('autocomplete', 'off'));

            function hardResetFields() {
                if(!form) return;
                form.reset();
                categorySelect.value = '';
                mpBox.innerHTML =
                    '<div class="text-muted small text-center" style="padding: 2rem 1rem;"><span class="material-symbols-outlined" style="font-size: 2rem; opacity: 0.3; display: block; margin-bottom: 0.5rem;">inventory_2</span>{{ __('messages.owner.products.outlet_products.select_category_first') }}</div>';
                mpSelectAll.disabled = true;
                mpSelectAll.checked = false;
                mpError.style.display = 'none';
                if(qtyInput) qtyInput.value = '0';
                if(statusSelect) statusSelect.value = '1';
            }

            function getDefaultCategoryId() {
                const current = categorySelect?.value;
                if (current) return current;
                const firstOption = Array.from(categorySelect?.options || []).find(opt => opt.value && opt.value !== '');
                return firstOption ? firstOption.value : '';
            }

            function renderMasterProductCheckboxes(items) {
                mpBox.innerHTML = '';
                mpSelectAll.disabled = true;
                mpSelectAll.checked = false;
                if (!Array.isArray(items) || items.length === 0) {
                    mpBox.innerHTML =
                        '<div class="text-muted small text-center" style="padding: 2rem 1rem;">{{ __('messages.owner.products.outlet_products.no_master_product_filter') }}</div>';
                    return;
                }
                items.forEach(item => {
                    const id = String(item.id);
                    const label = item.name || ('#' + id);
                    const div = document.createElement('div');
                    div.className = 'form-check';
                    div.innerHTML = `
                        <input class="form-check-input" type="checkbox" name="master_product_ids[]" value="${id}" id="mp_${id}">
                        <label class="form-check-label" for="mp_${id}">${label}</label>
                    `;
                    mpBox.appendChild(div);
                });
                mpSelectAll.disabled = false;
                mpSelectAll.checked = false;
            }

            mpSelectAll?.addEventListener('change', function() {
                const checked = this.checked;
                mpBox.querySelectorAll('input[type="checkbox"][name="master_product_ids[]"]').forEach(
                cb => {
                    cb.checked = checked;
                });
            });

            form?.addEventListener('submit', function(e) {
                const anyChecked = mpBox.querySelectorAll('input[name="master_product_ids[]"]:checked').length > 0;
                if (!anyChecked) {
                    e.preventDefault();
                    mpError.style.display = 'block';
                    mpBox.classList.add('border-danger');
                    setTimeout(() => mpBox.classList.remove('border-danger'), 1500);
                } else {
                    mpError.style.display = 'none';
                }
            });

            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-add-product')) {
                    const sidebar = document.querySelector('#sidebar, .sidebar, #sidenav-main, .sidenav');
                    if (sidebar) {
                        const rect = sidebar.getBoundingClientRect();
                        const sidebarOpen = rect.right > 0 && rect.left < window.innerWidth && rect.width > 100;
                        if (sidebarOpen) return;
                    }

                    e.preventDefault();
                    hardResetFields();
                    const catId = getDefaultCategoryId();
                    if (catId) {
                        categorySelect.value = catId;
                        loadMasterProducts(catId);
                    }
                }
            });

            if (modal) {
                modal.addEventListener('hidden.bs.modal', function() {
                    hardResetFields();
                });
            }

            async function loadMasterProducts(categoryId) {
                mpBox.innerHTML =
                    '<div class="text-muted small text-center" style="padding: 2rem 1rem;">{{ __('messages.owner.products.outlet_products.loading') }}</div>';
                mpSelectAll.disabled = true;
                mpSelectAll.checked = false;
                mpError.style.display = 'none';
                try {
                    // URL dinamis menyesuaikan tipe role staff
                    const endpoint = "{{ route('employee.' . strtolower(auth('employee')->user()->role) . '.products.get-master-products') }}";

                    const url = new URL(endpoint);
                    url.searchParams.set('category_id', categoryId || 'all');
                    
                    const res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    renderMasterProductCheckboxes(data);
                } catch {
                    mpBox.innerHTML =
                        '<div class="text-danger small text-center" style="padding: 2rem 1rem;">{{ __('messages.owner.products.outlet_products.failed_load_master_products') }}</div>';
                }
            }

            categorySelect?.addEventListener('change', function() {
                loadMasterProducts(this.value);
            });
        });
    </script>

    <script>
        (function() {
            const directRadio = document.getElementById('stock_type_direct');
            const linkedRadio = document.getElementById('stock_type_linked');
            const qtyGroup = document.getElementById('qp_quantity_group');
            const qtyInput = document.getElementById('qp_quantity');
            const linkedInfo = document.getElementById('linked_stock_info');

            function syncStockTypeUI() {
                if (!directRadio || !linkedRadio || !qtyGroup || !qtyInput || !linkedInfo) return;
                if (linkedRadio.checked) {
                    qtyGroup.classList.add('d-none');
                    qtyInput.required = false;
                    qtyInput.value = '0';
                    linkedInfo.classList.remove('d-none');
                } else {
                    qtyGroup.classList.remove('d-none');
                    qtyInput.required = true;
                    linkedInfo.classList.add('d-none');
                }
            }

            directRadio?.addEventListener('change', syncStockTypeUI);
            linkedRadio?.addEventListener('change', syncStockTypeUI);

            const modal = document.getElementById('addProductModal');
            if (modal) {
                modal.addEventListener('shown.bs.modal', function() {
                    if (directRadio) directRadio.checked = true;
                    syncStockTypeUI();
                });
            }
            syncStockTypeUI();
        })();
    </script>

    <script>
        function submitFilterResetPage() {
            const form = document.getElementById('outletProductFilterForm');
            const pageInput = document.getElementById('pageInput');
            if (pageInput) pageInput.value = 1;
            form?.submit();
        }

        function debouncedSubmit(el, delay = 500) {
            const form = document.getElementById('outletProductFilterForm');
            const pageInput = document.getElementById('pageInput');
            if (!form || !el) return;
            if (pageInput) pageInput.value = 1;
            if (el._debounceTimer) clearTimeout(el._debounceTimer);
            el._debounceTimer = setTimeout(() => form.submit(), delay);
        }
    </script>
@endpush