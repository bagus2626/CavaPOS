@extends('layouts.partner')

@section('title', __('messages.partner.outlet.table_management.tables.table_list'))
@section('page_title', __('messages.partner.outlet.table_management.tables.table_list'))

@section('content')
<div class="modern-container">
  <div class="container-modern">
    <!-- Header Section -->
    <div class="page-header">
      <div class="header-content">
        <h1 class="page-title">{{ __('messages.partner.outlet.table_management.tables.table_list') }}</h1>
        <p class="page-subtitle">Manage your restaurant tables and seating</p>
      </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
      <div class="alert alert-success alert-modern">
        <div class="alert-icon">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div class="alert-content">
          {{ session('success') }}
        </div>
      </div>
    @endif

    <!-- Filters & Actions -->
    <div class="modern-card mb-4">
      <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
        <div class="table-controls">
          <!-- Search & Filter -->
          <div class="search-filter-group">
            <!-- Search -->
            <div class="input-wrapper" style="flex: 1; max-width: 400px;">
              <span class="input-icon">
                <span class="material-symbols-outlined">search</span>
              </span>
              <input type="text" id="searchInput" class="form-control-modern with-icon"
                placeholder="{{ __('messages.partner.outlet.table_management.tables.search_tables') }}">
            </div>

            <!-- Filter by Table Class -->
            <div class="select-wrapper" style="min-width: 200px;">
              <select id="tableClassFilter" class="form-control-modern">
                <option value="">{{ __('messages.partner.outlet.table_management.tables.all_table_classes') }}</option>
                @foreach($table_classes as $table_class)
                  <option value="{{ $table_class }}" {{ (string)($tableClass ?? '') === (string)$table_class ? 'selected' : '' }}>
                    {{ $table_class }}
                  </option>
                @endforeach
              </select>
              <span class="material-symbols-outlined select-arrow">expand_more</span>
            </div>
          </div>

          <!-- Add Table Button -->
          <a href="{{ route('partner.store.tables.create') }}" class="btn-modern btn-primary-modern">
            <span class="material-symbols-outlined">add</span>
            {{ __('messages.partner.outlet.table_management.tables.add_table') }}
          </a>
        </div>
      </div>
    </div>

    <!-- Table Display -->
    @include('pages.partner.store.tables.display')

  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // ==========================================
  // TABLE INDEX - SEARCH & FILTER (NO RELOAD)
  // ==========================================
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableClassFilter = document.getElementById('tableClassFilter');
    const tableBody = document.getElementById('tableTableBody');
    const paginationWrapper = document.querySelector('.table-pagination');

    if (!tableBody) {
      console.error('Table body not found');
      return;
    }

    // Ambil semua data dari Blade
    const allTablesData = @json($allTablesFormatted ?? []);
    
    let filteredTables = [...allTablesData];
    const itemsPerPage = 10;
    let currentPage = 1;

    // ==========================================
    // FILTER FUNCTION
    // ==========================================
    function filterTables() {
      const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
      const selectedClass = tableClassFilter ? tableClassFilter.value.trim() : '';

      filteredTables = allTablesData.filter(table => {
        // Search: cari di table_no, table_class, description
        const searchText = `
          ${table.table_no || ''} 
          ${table.table_class || ''} 
          ${table.description || ''}
        `.toLowerCase();
        
        const matchesSearch = !searchTerm || searchText.includes(searchTerm);

        // Table class filter
        const matchesClass = !selectedClass || table.table_class === selectedClass;

        return matchesSearch && matchesClass;
      });

      currentPage = 1; // Reset ke halaman pertama
      renderTable();
    }

    // ==========================================
    // RENDER TABLE
    // ==========================================
    function renderTable() {
      // Hitung pagination
      const totalPages = Math.ceil(filteredTables.length / itemsPerPage);
      const startIndex = (currentPage - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      const currentTables = filteredTables.slice(startIndex, endIndex);

      // Clear table
      tableBody.innerHTML = '';

      // Render rows
      if (currentTables.length === 0) {
        tableBody.innerHTML = `
          <tr class="empty-filter-row">
            <td colspan="8" style="text-align: center; padding: 3rem;">
              <div class="table-empty-state">
                <span class="material-symbols-outlined" style="font-size: 4rem; color: #ccc; display: block; margin-bottom: 1rem;">search_off</span>
                <h4 style="margin: 0 0 0.5rem 0; color: #666; font-size: 1.25rem;">No results found</h4>
                <p style="margin: 0; color: #999;">Try adjusting your search or filter</p>
              </div>
            </td>
          </tr>
        `;
      } else {
        currentTables.forEach((table, index) => {
          const rowNumber = startIndex + index + 1;
          const row = createTableRow(table, rowNumber);
          tableBody.appendChild(row);
        });
      }

      // Handle pagination visibility
      if (paginationWrapper) {
        if (filteredTables.length <= itemsPerPage) {
          paginationWrapper.style.display = 'none';
        } else {
          paginationWrapper.style.display = '';
          renderPagination(totalPages);
        }
      }
    }

    // ==========================================
    // CREATE TABLE ROW
    // ==========================================
    function createTableRow(table, rowNumber) {
      const tr = document.createElement('tr');
      tr.className = 'table-row';
      tr.setAttribute('data-category', table.table_class || '');

      // Format status badge
      let statusBadge = '<span class="text-muted">-</span>';
      if (table.status === 'available') {
        statusBadge = '<span class="badge-modern badge-success">{{ __("messages.partner.outlet.table_management.tables.available") }}</span>';
      } else if (table.status === 'occupied') {
        statusBadge = '<span class="badge-modern badge-warning">{{ __("messages.partner.outlet.table_management.tables.occupied") }}</span>';
      } else if (table.status === 'reserved') {
        statusBadge = '<span class="badge-modern badge-info">{{ __("messages.partner.outlet.table_management.tables.reserved") }}</span>';
      } else if (table.status === 'not_available') {
        statusBadge = '<span class="badge-modern badge-danger">{{ __("messages.partner.outlet.table_management.tables.not_available") }}</span>';
      }

      // Format images
      let imagesHtml = '<span class="text-muted">{{ __("messages.partner.outlet.table_management.tables.no_images") }}</span>';
      if (table.images && Array.isArray(table.images) && table.images.length > 0) {
        const validImages = table.images.filter(img => img && img.path);
        if (validImages.length > 0) {
          imagesHtml = '<div class="table-images-cell">';
          validImages.forEach(image => {
            const src = `{{ asset('') }}${image.path}`;
            imagesHtml += `
              <a href="${src}" target="_blank" rel="noopener" class="table-image-link">
                <img src="${src}" alt="${image.filename || 'Table'}" class="table-thumbnail" loading="lazy">
              </a>
            `;
          });
          imagesHtml += '</div>';
        }
      }

      // URLs
      const showUrl = `/partner/store/tables/${table.id}`;
      const editUrl = `/partner/store/tables/${table.id}/edit`;

      tr.innerHTML = `
        <td class="text-center text-muted">${rowNumber}</td>
        <td>
          <div class="cell-with-icon">
            <span class="fw-600">${table.table_no || '-'}</span>
          </div>
        </td>
        <td>
          <span class="text-secondary">${table.table_class || '-'}</span>
        </td>
        <td>
          <span class="text-secondary">${table.description || '-'}</span>
        </td>
        <td class="text-center">
          ${statusBadge}
        </td>
        <td class="text-center">
          ${imagesHtml}
        </td>
        <td class="text-center">
          <button onclick="generateBarcode(${table.id})" 
            class="btn-table-action primary" 
            title="{{ __('messages.partner.outlet.table_management.tables.table_barcode') }}">
            <span class="material-symbols-outlined">qr_code</span>
          </button>
        </td>
        <td class="text-center">
          <div class="table-actions">
            <a href="${showUrl}" class="btn-table-action view" title="Detail">
              <span class="material-symbols-outlined">visibility</span>
            </a>
            <a href="${editUrl}" class="btn-table-action edit" title="{{ __('messages.partner.outlet.table_management.tables.edit') }}">
              <span class="material-symbols-outlined">edit</span>
            </a>
            <button onclick="deleteTable(${table.id})" 
              class="btn-table-action delete"
              title="{{ __('messages.partner.outlet.table_management.tables.delete') }}">
              <span class="material-symbols-outlined">delete</span>
            </button>
          </div>
        </td>
      `;

      return tr;
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
            window.scrollTo({ top: 0, behavior: 'smooth' });
          }
        });
      });
    }

    // ==========================================
    // EVENT LISTENERS
    // ==========================================
    if (searchInput) {
      searchInput.addEventListener('input', filterTables);
    }

    if (tableClassFilter) {
      tableClassFilter.addEventListener('change', filterTables);
    }

    // ==========================================
    // INITIALIZE
    // ==========================================
    renderTable();
  });
</script>
@endpush