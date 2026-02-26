@extends('layouts.owner')

@section('title', __('messages.owner.outlet.all_outlets.outlet_list'))
@section('page_title', __('messages.owner.outlet.all_outlets.all_outlets'))

@section('content')
    <style>
        @media (max-width: 768px) {
            .page-header {
                display: none !important;
            }
        }

        /* Tab Styles */
        .tabs-wrapper {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #6b7280;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
        }

        .tab-btn:hover {
            color: #374151;
        }

        .tab-btn.active {
            color: var(--primary, #b3311d);
            border-bottom-color: var(--primary, #b3311d);
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }
    </style>

    <div class="modern-container">
        <div class="container-modern">
            {{-- PAGE HEADER - DESKTOP ONLY --}}
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.outlet.all_outlets.all_outlets') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.outlet.all_outlets.subtitle') }}</p>
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

            {{-- Langsung tampilkan konten outlets tanpa tab wrapper --}}
            {{-- DESKTOP SEARCH & FILTER --}}
            <div class="modern-card mb-4 desktop-only-card">
                <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                    <div class="table-controls">
                        <div class="search-filter-group">
                            <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                                <span class="input-icon">
                                    <span class="material-symbols-outlined">search</span>
                                </span>
                                <input type="text" id="searchInput" class="form-control-modern with-icon"
                                    placeholder="{{ __('messages.owner.outlet.all_outlets.search_placeholder') }}">
                            </div>
                            <div class="select-wrapper" style="min-width: 200px;">
                                <select id="statusFilter" class="form-control-modern">
                                    <option value="">{{ __('messages.owner.outlet.all_outlets.filter_all_status') }}
                                    </option>
                                    <option value="active">{{ __('messages.owner.outlet.all_outlets.active') }}</option>
                                    <option value="inactive">{{ __('messages.owner.outlet.all_outlets.inactive') }}</option>
                                </select>
                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                            </div>
                        </div>
                        <a href="{{ route('owner.user-owner.outlets.create') }}" class="btn-modern btn-primary-modern">
                            <span class="material-symbols-outlined">add</span>
                            {{ __('messages.owner.outlet.all_outlets.add_outlet') }}
                        </a>
                    </div>
                </div>
            </div>

            @include('pages.owner.outlet.display')

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ==========================================
        // TAB SWITCHING
        // ==========================================
        function switchTab(tab, btn) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
            btn.classList.add('active');
        }

        // ==========================================
        // MOBILE FILTER MODAL FUNCTIONS
        // ==========================================
        function toggleMobileFilter() {
            const modal = document.getElementById('mobileFilterModal');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileFilter() {
            const modal = document.getElementById('mobileFilterModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        function setStatusFilter(status) {
            const statusFilter = document.getElementById('statusFilter');
            if (statusFilter) {
                statusFilter.value = status || '';
                filterOutlets();
            }
            closeMobileFilter();
        }
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('mobileFilterModal');
            if (modal && e.target === modal) closeMobileFilter();
        });

        // ==========================================
        // OUTLET SEARCH & FILTER
        // ==========================================
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const tableBody = document.getElementById('outletTableBody');
            const mobileList = document.querySelector('.mobile-outlet-list');
            const paginationWrapper = document.querySelector('.table-pagination');
            const mobileSearchInput = document.querySelector('.mobile-search-input');
            const mobileHeaderSubtitle = document.querySelector('.mobile-header-subtitle');

            const allOutletsData = @json($allOutletsFormatted ?? []);
            let filteredOutlets = [...allOutletsData];
            const itemsPerPage = 10;
            let currentPage = 1;

            window.filterOutlets = function() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const selectedStatus = statusFilter ? statusFilter.value.trim() : '';
                filteredOutlets = allOutletsData.filter(outlet => {
                    const searchText =
                        `${outlet.name||''} ${outlet.username||''} ${outlet.email||''} ${outlet.city||''}`
                        .toLowerCase();
                    const matchesSearch = !searchTerm || searchText.includes(searchTerm);
                    const outletStatus = (outlet.is_active === 1 || outlet.is_active === '1' || outlet
                        .is_active === true) ? 'active' : 'inactive';
                    const matchesStatus = !selectedStatus || outletStatus === selectedStatus;
                    return matchesSearch && matchesStatus;
                });
                currentPage = 1;
                if (mobileHeaderSubtitle) mobileHeaderSubtitle.textContent =
                    `${filteredOutlets.length} Total Outlets`;
                renderOutletTable();
            }

            function renderOutletTable() {
                const totalPages = Math.ceil(filteredOutlets.length / itemsPerPage);
                const startIndex = (currentPage - 1) * itemsPerPage;
                const currentOutlets = filteredOutlets.slice(startIndex, startIndex + itemsPerPage);

                if (tableBody) tableBody.innerHTML = '';
                if (mobileList) mobileList.innerHTML = '';

                if (currentOutlets.length === 0) {
                    if (tableBody) tableBody.innerHTML = `
                        <tr><td colspan="7" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">store</span>
                                <h4>No outlets found</h4>
                                <p>Add your first outlet to get started</p>
                            </div>
                        </td></tr>`;
                } else {
                    currentOutlets.forEach((outlet, index) => {
                        const rowNumber = startIndex + index + 1;
                        if (tableBody) tableBody.appendChild(createOutletRow(outlet, rowNumber));
                        if (mobileList) mobileList.appendChild(createOutletCard(outlet));
                    });
                }

                if (paginationWrapper) {
                    paginationWrapper.style.display = filteredOutlets.length <= itemsPerPage ? 'none' : '';
                    if (filteredOutlets.length > itemsPerPage) renderOutletPagination(totalPages,
                        paginationWrapper);
                }
            }

            function createOutletRow(outlet, rowNumber) {
                const tr = document.createElement('tr');
                tr.className = 'table-row';
                const isActive = outlet.is_active === 1 || outlet.is_active === '1' || outlet.is_active === true;
                tr.setAttribute('data-status', isActive ? 'active' : 'inactive');

                let imageHtml = outlet.logo ?
                    `<img src="${outlet.logo.startsWith('http')?outlet.logo:`{{ asset('storage/') }}/${outlet.logo}`}" alt="${outlet.name}" class="user-avatar" loading="lazy">` :
                    `<div class="user-avatar-placeholder"><span class="material-symbols-outlined">store</span></div>`;

                const statusBadge = isActive ?
                    `<span class="badge-modern badge-success badge-sm">{{ __('messages.owner.outlet.all_outlets.active') }}</span>` :
                    `<span class="badge-modern badge-danger badge-sm">{{ __('messages.owner.outlet.all_outlets.inactive') }}</span>`;

                tr.innerHTML = `
                    <td class="text-center text-muted">${rowNumber}</td>
                    <td><div class="user-info-cell">${imageHtml}<span class="data-name">${outlet.name||'-'}</span></div></td>
                    <td><span class="text-secondary">${outlet.username||'-'}</span></td>
                    <td><a href="mailto:${outlet.email}" class="table-link">${outlet.email||'-'}</a></td>
                    <td><span class="text-secondary">${outlet.city||'-'}</span></td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="text-center">
                        <div class="table-actions">
                            <a href="/owner/user-owner/outlets/${outlet.id}" class="btn-table-action view" title="View"><span class="material-symbols-outlined">visibility</span></a>
                            <a href="/owner/user-owner/outlets/${outlet.id}/edit" class="btn-table-action edit" title="Edit"><span class="material-symbols-outlined">edit</span></a>
                            <button onclick="deleteOutlet(${outlet.id})" class="btn-table-action delete" title="Delete"><span class="material-symbols-outlined">delete</span></button>
                        </div>
                    </td>`;
                return tr;
            }

            function createOutletCard(outlet) {
                const showUrl = `/owner/user-owner/outlets/${outlet.id}`;
                const editUrl = `/owner/user-owner/outlets/${outlet.id}/edit`;
                let imageHtml = outlet.logo ?
                    `<img src="${outlet.logo.startsWith('http')?outlet.logo:`{{ asset('storage/') }}/${outlet.logo}`}" alt="${outlet.name}" loading="lazy">` :
                    `<div class="user-avatar-placeholder"><span class="material-symbols-outlined">store</span></div>`;

                const wrapper = document.createElement('div');
                wrapper.className = 'outlet-card-wrapper';
                wrapper.innerHTML = `
                    <div class="swipe-actions">
                        <a href="${editUrl}" class="swipe-action edit"><span class="material-symbols-outlined">edit</span></a>
                        <button type="button" onclick="deleteOutlet(${outlet.id})" class="swipe-action delete"><span class="material-symbols-outlined">delete</span></button>
                    </div>
                    <a href="${showUrl}" class="outlet-card-link">
                        <div class="outlet-card-clickable">
                            <div class="outlet-card__left">
                                <div class="outlet-card__avatar">${imageHtml}</div>
                                <div class="outlet-card__info">
                                    <div class="outlet-card__name">${outlet.name||'-'}</div>
                                    <div class="outlet-card__details">
                                        <span class="detail-text">${outlet.username||'-'}</span>
                                        <span class="detail-separator">•</span>
                                        <span class="detail-text">${outlet.city||'-'}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="outlet-card__right"><span class="material-symbols-outlined chevron">chevron_right</span></div>
                        </div>
                    </a>`;
                return wrapper;
            }

            function renderOutletPagination(totalPages, wrapper) {
                renderPaginationBase(totalPages, wrapper, () => {
                    renderOutletTable();
                });
            }

            if (searchInput) searchInput.addEventListener('input', filterOutlets);
            if (statusFilter) statusFilter.addEventListener('change', filterOutlets);
            if (mobileSearchInput) {
                mobileSearchInput.addEventListener('input', function() {
                    if (searchInput) {
                        searchInput.value = this.value;
                        filterOutlets();
                    }
                });
            }

            if (mobileHeaderSubtitle) mobileHeaderSubtitle.textContent = `${filteredOutlets.length} Total Outlets`;
            renderOutletTable();

            // ==========================================
            // OWNER TABLE (restaurant tables) TAB
            // ==========================================
            const tableSearchInput = document.getElementById('tableSearchInput');
            const tableClassFilter = document.getElementById('tableClassFilter');
            const tableOutletFilter = document.getElementById('tableOutletFilter');
            const ownerTableBody = document.getElementById('ownerTableTableBody');
            const ownerTablePagination = document.getElementById('ownerTablePagination');

            const allTablesData = @json($allTablesFormatted ?? []);
            let filteredTables = [...allTablesData];
            let tableCurrentPage = 1;
            const tableItemsPerPage = 10;

            function filterOwnerTables() {
                const search = tableSearchInput ? tableSearchInput.value.toLowerCase().trim() : '';
                const cls = tableClassFilter ? tableClassFilter.value.trim() : '';
                const outletId = tableOutletFilter ? tableOutletFilter.value.trim() : '';

                filteredTables = allTablesData.filter(t => {
                    const text = `${t.table_no||''} ${t.table_class||''} ${t.description||''}`
                        .toLowerCase();
                    const matchSearch = !search || text.includes(search);
                    const matchClass = !cls || t.table_class === cls;
                    const matchOutlet = !outletId || String(t.outlet_id) === outletId;
                    return matchSearch && matchClass && matchOutlet;
                });
                tableCurrentPage = 1;
                renderOwnerTables();
            }

            function renderOwnerTables() {
                if (!ownerTableBody) return;
                const totalPages = Math.ceil(filteredTables.length / tableItemsPerPage);
                const startIndex = (tableCurrentPage - 1) * tableItemsPerPage;
                const current = filteredTables.slice(startIndex, startIndex + tableItemsPerPage);

                ownerTableBody.innerHTML = '';

                if (current.length === 0) {
                    ownerTableBody.innerHTML = `
            <tr><td colspan="9" class="text-center">
                <div class="table-empty-state">
                    <span class="material-symbols-outlined">table_restaurant</span>
                    <h4>No tables found</h4>
                    <p>Try adjusting your search or filter</p>
                </div>
            </td></tr>`;
                } else {
                    current.forEach((t, idx) => {
                        const tr = document.createElement('tr');
                        tr.className = 'table-row';
                        const rowNumber = startIndex + idx + 1;

                        // Status badge
                        const statusMap = {
                            available: '<span class="badge-modern badge-success">Available</span>',
                            occupied: '<span class="badge-modern badge-warning">Occupied</span>',
                            reserved: '<span class="badge-modern badge-info">Reserved</span>',
                            not_available: '<span class="badge-modern badge-danger">Not Available</span>',
                        };
                        const statusBadge = statusMap[t.status] || '<span class="text-muted">-</span>';

                        // Images
                        let imagesHtml = '<span class="text-muted">No image</span>';
                        if (t.images && Array.isArray(t.images) && t.images.length > 0) {
                            const valid = t.images.filter(img => img && img.path);
                            if (valid.length > 0) {
                                imagesHtml = '<div class="table-images-cell">';
                                valid.forEach(img => {
                                    const src = img.path.startsWith('http') ? img.path :
                                        `{{ asset('') }}${img.path}`;
                                    imagesHtml +=
                                        `<a href="${src}" target="_blank" class="table-image-link"><img src="${src}" class="table-thumbnail" loading="lazy"></a>`;
                                });
                                imagesHtml += '</div>';
                            }
                        }

                        const showUrl = `/owner/user-owner/tables/${t.id}`;
                        const editUrl = `/owner/user-owner/tables/${t.id}/edit`;

                        tr.innerHTML = `
                <td class="text-center text-muted">${rowNumber}</td>
                <td><span class="fw-600">${t.table_no||'-'}</span></td>
                <td><span class="text-secondary">${t.table_class||'-'}</span></td>
                <td><span class="text-secondary">${t.outlet_name||'-'}</span></td>
                <td><span class="text-secondary">${t.description||'-'}</span></td>
                <td class="text-center">${statusBadge}</td>
                <td class="text-center">${imagesHtml}</td>
                <td class="text-center">
                    <button onclick="generateOwnerBarcode(${t.id})" class="btn-table-action primary" title="Generate Barcode">
                        <span class="material-symbols-outlined">qr_code</span>
                    </button>
                </td>
                <td class="text-center">
                    <div class="table-actions">
                        <a href="${showUrl}" class="btn-table-action view" title="View"><span class="material-symbols-outlined">visibility</span></a>
                        <a href="${editUrl}" class="btn-table-action edit" title="Edit"><span class="material-symbols-outlined">edit</span></a>
                        <button onclick="deleteOwnerTable(${t.id})" class="btn-table-action delete" title="Delete"><span class="material-symbols-outlined">delete</span></button>
                    </div>
                </td>`;
                        ownerTableBody.appendChild(tr);
                    });
                }

                if (ownerTablePagination) {
                    ownerTablePagination.style.display = filteredTables.length <= tableItemsPerPage ? 'none' : '';
                    if (filteredTables.length > tableItemsPerPage) {
                        renderPaginationBase(
                            totalPages,
                            ownerTablePagination,
                            () => renderOwnerTables(),
                            () => tableCurrentPage,
                            (p) => {
                                tableCurrentPage = p;
                            }
                        );
                    }
                }
            }

            if (tableSearchInput) tableSearchInput.addEventListener('input', filterOwnerTables);
            if (tableClassFilter) tableClassFilter.addEventListener('change', filterOwnerTables);
            if (tableOutletFilter) tableOutletFilter.addEventListener('change', filterOwnerTables);

            renderOwnerTables();

            // ==========================================
            // SHARED PAGINATION BUILDER
            // ==========================================
            function renderPaginationBase(totalPages, wrapper, onRender, getPage, setPage) {
                wrapper.innerHTML = '';
                const nav = document.createElement('nav');
                nav.setAttribute('role', 'navigation');
                const ul = document.createElement('ul');
                ul.className = 'pagination';

                // Untuk outlet, currentPage sudah di-closure; untuk employee, pakai getter/setter
                const getCurrent = getPage ? getPage : () => currentPage;
                const setCurrent = setPage ? setPage : (p) => {
                    currentPage = p;
                };

                const cp = getCurrent();

                // Prev
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${cp === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = cp === 1 ?
                    `<span class="page-link"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg></span>` :
                    `<a href="#" class="page-link" data-page="${cp - 1}"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg></a>`;
                ul.appendChild(prevLi);

                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= cp - 1 && i <= cp + 1)) {
                        const li = document.createElement('li');
                        li.className = `page-item ${i === cp ? 'active' : ''}`;
                        li.innerHTML = i === cp ?
                            `<span class="page-link" aria-current="page">${i}</span>` :
                            `<a href="#" class="page-link" data-page="${i}">${i}</a>`;
                        ul.appendChild(li);
                    } else if (i === cp - 2 || i === cp + 2) {
                        const li = document.createElement('li');
                        li.className = 'page-item disabled';
                        li.innerHTML = `<span class="page-link">...</span>`;
                        ul.appendChild(li);
                    }
                }

                // Next
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${cp === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = cp === totalPages ?
                    `<span class="page-link"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg></span>` :
                    `<a href="#" class="page-link" data-page="${cp + 1}"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg></a>`;
                ul.appendChild(nextLi);

                nav.appendChild(ul);
                wrapper.appendChild(nav);

                nav.querySelectorAll('a.page-link[data-page]').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = parseInt(this.dataset.page);
                        if (page > 0 && page <= totalPages && page !== getCurrent()) {
                            setCurrent(page);
                            onRender();
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        }
                    });
                });
            }
        });

        // ==========================================
        // DELETE FUNCTIONS
        // ==========================================
        function deleteOutlet(outletId) {
            Swal.fire({
                title: '{{ __('messages.owner.outlet.all_outlets.delete_confirmation_1') }}',
                text: '{{ __('messages.owner.outlet.all_outlets.delete_confirmation_2') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b3311d',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __('messages.owner.outlet.all_outlets.delete_confirmation_3') }}',
                cancelButtonText: '{{ __('messages.owner.outlet.all_outlets.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/owner/user-owner/outlets/${outletId}`;
                    form.style.display = 'none';
                    form.innerHTML = `@csrf<input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function deleteOwnerTable(tableId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b3311d',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/owner/user-owner/tables/${tableId}`;
                    form.style.display = 'none';
                    form.innerHTML = `@csrf<input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function generateOwnerBarcode(tableId) {
            window.open(`/owner/user-owner/tables/generate-barcode/${tableId}`, '_blank');
        }
    </script>
@endpush
