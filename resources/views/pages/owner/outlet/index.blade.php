@extends('layouts.owner')

@section('title', __('messages.owner.outlet.all_outlets.outlet_list'))
@section('page_title', __('messages.owner.outlet.all_outlets.all_outlets'))

@section('content')
    <style>
        /* Hide page header on mobile */
        @media (max-width: 768px) {
            .page-header {
                display: none !important;
            }
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

        // Set status filter dari modal mobile
        function setStatusFilter(status) {
            const statusFilter = document.getElementById('statusFilter');
            if (statusFilter) {
                statusFilter.value = status || '';

                // Update active state di modal pills
                updateModalPillsActiveState(status);

                filterOutlets();
            }
            closeMobileFilter();
        }

        // Update active state pada modal pills
        function updateModalPillsActiveState(activeStatus) {
            const pills = document.querySelectorAll('.modal-pill');
            const iconWrappers = document.querySelectorAll('.pill-icon-wrapper');
            const checkIcons = document.querySelectorAll('.pill-check');

            pills.forEach(pill => {
                pill.classList.remove('active');
            });

            iconWrappers.forEach(wrapper => {
                wrapper.classList.remove('active');
            });

            checkIcons.forEach(check => {
                check.remove();
            });

            // Set active berdasarkan status
            let activePill;
            if (!activeStatus || activeStatus === '') {
                activePill = document.querySelector('.modal-pill[onclick*="setStatusFilter(\'\')"]');
            } else if (activeStatus === 'active') {
                activePill = document.querySelector('.modal-pill[onclick*="setStatusFilter(\'active\')"]');
            } else if (activeStatus === 'inactive') {
                activePill = document.querySelector('.modal-pill[onclick*="setStatusFilter(\'inactive\')"]');
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
            const modal = document.getElementById('mobileFilterModal');
            if (modal && e.target === modal) {
                closeMobileFilter();
            }
        });

        // ==========================================
        // OUTLET INDEX - SEARCH & FILTER (NO RELOAD)
        // ==========================================
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const tableBody = document.getElementById('outletTableBody');
            const mobileList = document.querySelector('.mobile-outlet-list');
            const paginationWrapper = document.querySelector('.table-pagination');
            const mobileSearchInput = document.querySelector('.mobile-search-input');
            const mobileHeaderSubtitle = document.querySelector('.mobile-header-subtitle');

            if (!tableBody) {
                console.error('Table body not found');
                return;
            }

            // Ambil semua data dari Blade
            const allOutletsData = @json($allOutletsFormatted ?? []);

            let filteredOutlets = [...allOutletsData];
            const itemsPerPage = 10;
            let currentPage = 1;

            // ==========================================
            // FILTER FUNCTION
            // ==========================================
            window.filterOutlets = function() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const selectedStatus = statusFilter ? statusFilter.value.trim() : '';

                filteredOutlets = allOutletsData.filter(outlet => {
                    // Search: cari di name, username, email, city
                    const searchText = `
            ${outlet.name || ''} 
            ${outlet.username || ''} 
            ${outlet.email || ''} 
            ${outlet.city || ''}
          `.toLowerCase();

                    const matchesSearch = !searchTerm || searchText.includes(searchTerm);

                    // Status filter
                    const outletStatus = outlet.is_active === 1 || outlet.is_active === '1' || outlet
                        .is_active === true ? 'active' : 'inactive';
                    const matchesStatus = !selectedStatus || outletStatus === selectedStatus;

                    return matchesSearch && matchesStatus;
                });

                currentPage = 1;
                updateMobileSubtitle();
                renderTable();
            }

            // ==========================================
            // UPDATE MOBILE SUBTITLE (TOTAL COUNT)
            // ==========================================
            function updateMobileSubtitle() {
                if (mobileHeaderSubtitle) {
                    mobileHeaderSubtitle.textContent = `${filteredOutlets.length} Total Outlets`;
                }
            }

            // ==========================================
            // RENDER TABLE
            // ==========================================
            function renderTable() {
                const totalPages = Math.ceil(filteredOutlets.length / itemsPerPage);
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const currentOutlets = filteredOutlets.slice(startIndex, endIndex);

                // Clear table + mobile list
                tableBody.innerHTML = '';
                if (mobileList) mobileList.innerHTML = '';

                if (currentOutlets.length === 0) {
                    // Empty state untuk TABLE
                    tableBody.innerHTML = `
            <tr class="empty-filter-row">
              <td colspan="7" class="text-center">
                <div class="table-empty-state">
                  <span class="material-symbols-outlined">store_off</span>
                  <h4>{{ __('messages.owner.outlet.all_outlets.no_outlets') ?? 'No outlets found' }}</h4>
                  <p>{{ __('messages.owner.outlet.all_outlets.add_first_outlet') ?? 'Add your first outlet to get started' }}</p>
                </div>
              </td>
            </tr>
          `;

                    // Empty state untuk MOBILE
                    if (mobileList) {
                        mobileList.innerHTML = `
              <div class="table-empty-state">
                <span class="material-symbols-outlined">store_off</span>
                <h4>{{ __('messages.owner.outlet.all_outlets.no_outlets') ?? 'No outlets found' }}</h4>
                <p>{{ __('messages.owner.outlet.all_outlets.add_first_outlet') ?? 'Add your first outlet to get started' }}</p>
              </div>
            `;
                    }

                } else {
                    currentOutlets.forEach((outlet, index) => {
                        const rowNumber = startIndex + index + 1;
                        tableBody.appendChild(createOutletRow(outlet, rowNumber));
                        if (mobileList) mobileList.appendChild(createOutletCard(outlet));
                    });
                }

                // Handle pagination
                if (paginationWrapper) {
                    if (filteredOutlets.length <= itemsPerPage) {
                        paginationWrapper.style.display = 'none';
                    } else {
                        paginationWrapper.style.display = '';
                        renderPagination(totalPages);
                    }
                }
            }

            // ==========================================
            // CREATE OUTLET ROW (DESKTOP)
            // ==========================================
            function createOutletRow(outlet, rowNumber) {
                const tr = document.createElement('tr');
                tr.className = 'table-row';

                const isActive = outlet.is_active === 1 || outlet.is_active === '1' || outlet.is_active === true;
                tr.setAttribute('data-status', isActive ? 'active' : 'inactive');

                let imageHtml = '';
                if (outlet.logo) {
                    const imgSrc = outlet.logo.startsWith('http://') || outlet.logo.startsWith('https://') ?
                        outlet.logo :
                        `{{ asset('storage/') }}/${outlet.logo}`;
                    imageHtml = `<img src="${imgSrc}" alt="${outlet.name}" class="user-avatar" loading="lazy">`;
                } else {
                    imageHtml = `
            <div class="user-avatar-placeholder">
              <span class="material-symbols-outlined">store</span>
            </div>
          `;
                }

                const statusBadge = isActive ?
                    '<span class="badge-modern badge-success badge-sm">{{ __('messages.owner.outlet.all_outlets.active') }}</span>' :
                    '<span class="badge-modern badge-danger badge-sm">{{ __('messages.owner.outlet.all_outlets.inactive') }}</span>';

                const showUrl = `/owner/user-owner/outlets/${outlet.id}`;
                const editUrl = `/owner/user-owner/outlets/${outlet.id}/edit`;

                tr.innerHTML = `
          <td class="text-center text-muted">${rowNumber}</td>
          <td>
            <div class="user-info-cell">
              ${imageHtml}
              <span class="data-name">${outlet.name || '-'}</span>
            </div>
          </td>
          <td><span class="text-secondary">${outlet.username || '-'}</span></td>
          <td>
            <a href="mailto:${outlet.email}" class="table-link">${outlet.email || '-'}</a>
          </td>
          <td><span class="text-secondary">${outlet.city || '-'}</span></td>
          <td class="text-center">${statusBadge}</td>
          <td class="text-center">
            <div class="table-actions">
              <a href="${showUrl}" class="btn-table-action view" title="{{ __('messages.owner.outlet.all_outlets.view_details') }}">
                <span class="material-symbols-outlined">visibility</span>
              </a>
              <a href="${editUrl}" class="btn-table-action edit" title="{{ __('messages.owner.outlet.all_outlets.edit') }}">
                <span class="material-symbols-outlined">edit</span>
              </a>
              <button onclick="deleteOutlet(${outlet.id})" class="btn-table-action delete" title="{{ __('messages.owner.outlet.all_outlets.delete') }}">
                <span class="material-symbols-outlined">delete</span>
              </button>
            </div>
          </td>
        `;

                return tr;
            }

            // ==========================================
            // CREATE OUTLET CARD (MOBILE)
            // ==========================================
            function createOutletCard(outlet) {
                let imageHtml = '';
                if (outlet.logo) {
                    const imgSrc = outlet.logo.startsWith('http://') || outlet.logo.startsWith('https://') ?
                        outlet.logo :
                        `{{ asset('storage/') }}/${outlet.logo}`;
                    imageHtml = `<img src="${imgSrc}" alt="${outlet.name}" loading="lazy">`;
                } else {
                    imageHtml = `
            <div class="user-avatar-placeholder">
              <span class="material-symbols-outlined">store</span>
            </div>
          `;
                }

                const showUrl = `/owner/user-owner/outlets/${outlet.id}`;
                const editUrl = `/owner/user-owner/outlets/${outlet.id}/edit`;

                const wrapper = document.createElement('div');
                wrapper.className = 'outlet-card-wrapper';

                wrapper.innerHTML = `
          <div class="swipe-actions">
            <a href="${editUrl}" class="swipe-action edit">
              <span class="material-symbols-outlined">edit</span>
            </a>
            <button type="button" onclick="deleteOutlet(${outlet.id})" class="swipe-action delete">
              <span class="material-symbols-outlined">delete</span>
            </button>
          </div>

          <a href="${showUrl}" class="outlet-card-link">
            <div class="outlet-card-clickable">
              <div class="outlet-card__left">
                <div class="outlet-card__avatar">
                  ${imageHtml}
                </div>
                <div class="outlet-card__info">
                  <div class="outlet-card__name">${outlet.name || '-'}</div>
                  <div class="outlet-card__details">
                    <span class="detail-text">${outlet.username || '-'}</span>
                    <span class="detail-separator">•</span>
                    <span class="detail-text">${outlet.city || '-'}</span>
                  </div>
                </div>
              </div>
              <div class="outlet-card__right">
                <span class="material-symbols-outlined chevron">chevron_right</span>
              </div>
            </div>
          </a>
        `;

                return wrapper;
            }

            // ==========================================
            // RENDER PAGINATION
            // ==========================================
            function renderPagination(totalPages) {
                if (!paginationWrapper) return;

                paginationWrapper.innerHTML = '';

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
                            renderTable();
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        }
                    });
                });
            }

            // ==========================================
            // EVENT LISTENERS
            // ==========================================

            // Desktop search input
            if (searchInput) {
                searchInput.addEventListener('input', filterOutlets);
            }

            // Desktop status filter
            if (statusFilter) {
                statusFilter.addEventListener('change', filterOutlets);
            }

            // Mobile search input - sync dengan desktop
            if (mobileSearchInput) {
                mobileSearchInput.addEventListener('input', function() {
                    if (searchInput) {
                        searchInput.value = this.value;
                        filterOutlets();
                    }
                });
            }

            // ==========================================
            // INITIALIZE
            // ==========================================
            updateMobileSubtitle();
            renderTable();
        });

        // ==========================================
        // DELETE OUTLET FUNCTION
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
