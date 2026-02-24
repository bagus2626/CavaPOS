@extends('layouts.owner')

@section('title', 'Tables')
@section('page_title', 'Tables')

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">Table List</h1>
                    <p class="page-subtitle">Manage your restaurant tables and seating</p>
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

            <div class="modern-card mb-4">
                <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                    <div class="table-controls">
                        <div class="search-filter-group">

                            {{-- Search --}}
                            <div class="input-wrapper" style="flex: 1; max-width: 360px;">
                                <span class="input-icon">
                                    <span class="material-symbols-outlined">search</span>
                                </span>
                                <input type="text" id="searchInput" class="form-control-modern with-icon"
                                    placeholder="Search tables...">
                            </div>

                            {{-- Filter Outlet --}}
                            <div class="select-wrapper" style="min-width: 180px;">
                                <select id="outletFilter" class="form-control-modern">
                                    <option value="">All Outlets</option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                    @endforeach
                                </select>
                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                            </div>

                            {{-- Filter Table Class --}}
                            <div class="select-wrapper" style="min-width: 180px;">
                                <select id="tableClassFilter" class="form-control-modern">
                                    <option value="">All Table Classes</option>
                                    @foreach ($table_classes as $class)
                                        <option value="{{ $class }}">{{ $class }}</option>
                                    @endforeach
                                </select>
                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                            </div>

                        </div>
                        <a href="{{ route('owner.user-owner.tables.create') }}" class="btn-modern btn-primary-modern">
                            <span class="material-symbols-outlined">add</span>
                            Add Table
                        </a>
                    </div>
                </div>
            </div>

            @include('pages.owner.outlet.tables.display')

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const outletFilter = document.getElementById('outletFilter');
            const tableBody = document.getElementById('tableTableBody');
            const paginationWrapper = document.querySelector('.table-pagination');
            const tableClassFilter = document.getElementById('tableClassFilter');

            if (!tableBody) return;

            const allTablesData = @json($allTablesFormatted ?? []);
            let filteredTables = [...allTablesData];
            const itemsPerPage = 10;
            let currentPage = 1;

            function filterTables() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const selectedOutlet = outletFilter ? outletFilter.value.trim() : '';
                const selectedClass = tableClassFilter ? tableClassFilter.value.trim() : '';

                filteredTables = allTablesData.filter(table => {
                    const text =
                        `${table.table_no||''} ${table.table_class||''} ${table.description||''} ${table.outlet_name||''}`
                        .toLowerCase();
                    const matchSearch = !searchTerm || text.includes(searchTerm);
                    const matchOutlet = !selectedOutlet || String(table.partner_id) === String(
                        selectedOutlet);
                    const matchClass = !selectedClass || table.table_class === selectedClass;
                    return matchSearch && matchOutlet && matchClass;
                });
                currentPage = 1;
                renderTable();
            }

            function renderTable() {
                const totalPages = Math.ceil(filteredTables.length / itemsPerPage);
                const startIndex = (currentPage - 1) * itemsPerPage;
                const currentTables = filteredTables.slice(startIndex, startIndex + itemsPerPage);

                tableBody.innerHTML = '';

                if (currentTables.length === 0) {
                    tableBody.innerHTML = `
                        <tr><td colspan="9" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">table_restaurant</span>
                                <h4>No tables found</h4>
                                <p>Try adjusting your search or filter</p>
                            </div>
                        </td></tr>`;
                } else {
                    currentTables.forEach((table, index) => {
                        tableBody.appendChild(createTableRow(table, startIndex + index + 1));
                    });
                }

                if (paginationWrapper) {
                    paginationWrapper.style.display = filteredTables.length <= itemsPerPage ? 'none' : '';
                    if (filteredTables.length > itemsPerPage) renderPagination(totalPages);
                }
            }

            function createTableRow(table, rowNumber) {
                const tr = document.createElement('tr');
                tr.className = 'table-row';

                const statusMap = {
                    available: '<span class="badge-modern badge-success">Available</span>',
                    occupied: '<span class="badge-modern badge-warning">Occupied</span>',
                    reserved: '<span class="badge-modern badge-info">Reserved</span>',
                    not_available: '<span class="badge-modern badge-danger">Not Available</span>',
                };
                const statusBadge = statusMap[table.status] || '<span class="text-muted">-</span>';

                let imagesHtml = '<span class="text-muted">No image</span>';
                if (table.images && Array.isArray(table.images) && table.images.length > 0) {
                    const valid = table.images.filter(img => img && img.path);
                    if (valid.length > 0) {
                        imagesHtml = '<div class="table-images-cell">';
                        valid.forEach(img => {
                            const src = img.path.startsWith('http') ? img.path :
                                `{{ asset('') }}${img.path}`;
                            imagesHtml += `<a href="${src}" target="_blank" class="table-image-link">
                                <img src="${src}" class="table-thumbnail" loading="lazy"></a>`;
                        });
                        imagesHtml += '</div>';
                    }
                }

                const showUrl = `/owner/user-owner/tables/${table.id}`;
                const editUrl = `/owner/user-owner/tables/${table.id}/edit`;

                tr.innerHTML = `
                    <td class="text-center text-muted">${rowNumber}</td>
                    <td><span class="fw-600">${table.table_no || '-'}</span></td>
                    <td><span class="text-secondary">${table.outlet_name || '-'}</span></td>
                    <td><span class="text-secondary">${table.table_class || '-'}</span></td>
                    <td><span class="text-secondary">${table.description || '-'}</span></td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="text-center">${imagesHtml}</td>
                    <td class="text-center">
                        <button onclick="generateBarcode(${table.id})" class="btn-table-action primary" title="Generate Barcode">
                            <span class="material-symbols-outlined">qr_code</span>
                        </button>
                    </td>
                    <td class="text-center">
                        <div class="table-actions">
                            <a href="${showUrl}" class="btn-table-action view" title="View">
                                <span class="material-symbols-outlined">visibility</span></a>
                            <a href="${editUrl}" class="btn-table-action edit" title="Edit">
                                <span class="material-symbols-outlined">edit</span></a>
                            <button onclick="deleteTable(${table.id})" class="btn-table-action delete" title="Delete">
                                <span class="material-symbols-outlined">delete</span></button>
                        </div>
                    </td>`;
                return tr;
            }

            function renderPagination(totalPages) {
                if (!paginationWrapper) return;
                paginationWrapper.innerHTML = '';
                const nav = document.createElement('nav');
                const ul = document.createElement('ul');
                ul.className = 'pagination';
                const cp = currentPage;

                // Prev
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${cp === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = cp === 1 ?
                    `<span class="page-link"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg></span>` :
                    `<a href="#" class="page-link" data-page="${cp - 1}"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg></a>`;
                ul.appendChild(prevLi);

                // Pages
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
                paginationWrapper.appendChild(nav);

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

            if (searchInput) searchInput.addEventListener('input', filterTables);
            if (outletFilter) outletFilter.addEventListener('change', filterTables);
            if (tableClassFilter) tableClassFilter.addEventListener('change', filterTables);

            renderTable();
        });

        function deleteTable(tableId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
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

        function generateBarcode(tableId) {
            window.open(`/owner/user-owner/tables/generate-barcode/${tableId}`, '_blank');
        }
    </script>
@endpush
