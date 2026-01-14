@extends('layouts.partner')

@section('title', __('messages.partner.product.all_product.product_list'))
@section('page_title', __('messages.partner.product.all_product.all_products'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.partner.product.all_product.all_products') }}</h1>
          <p class="page-subtitle">{{ __('messages.partner.product.all_product.manage_your_product_catalog') }}</p>
        </div>
      </div>

      <!-- Success/Error Messages -->
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
                  placeholder="{{ __('messages.partner.product.all_product.search_products') }}">
              </div>

              <!-- Filter by Category -->
              <div class="select-wrapper" style="min-width: 200px;">
                <select id="categoryFilter" class="form-control-modern">
                  <option value="">{{ __('messages.partner.product.all_product.all_categories') }}</option>
                  @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (string) ($categoryId ?? null) === (string) $category->id ? 'selected' : '' }}>
                      {{ $category->category_name }}
                    </option>
                  @endforeach
                </select>
                <span class="material-symbols-outlined select-arrow">expand_more</span>
              </div>
            </div>

            <!-- Add Product Button (uncomment jika diperlukan) -->
            {{-- <a href="{{ route('partner.products.create') }}" class="btn-modern btn-primary-modern">
              <span class="material-symbols-outlined">add</span>
              {{ __('messages.partner.product.all_product.add_product') ?? 'Add Product' }}
            </a> --}}
          </div>
        </div>
      </div>

      <!-- Table Display -->
      @include('pages.partner.products.display')

    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // ==========================================
    // PRODUCTS INDEX - SEARCH & FILTER (NO RELOAD)
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const categoryFilter = document.getElementById('categoryFilter');
      const tableBody = document.getElementById('productTableBody');
      const paginationWrapper = document.querySelector('.table-pagination');

      if (!tableBody) return;

      // Ambil semua data dari Blade (yang sudah di-pass dari controller)
      const allProductsData = @json($allProductsFormatted ?? []);
      
      let filteredProducts = [...allProductsData];
      const itemsPerPage = 10;
      let currentPage = 1;

      // ==========================================
      // FILTER FUNCTION
      // ==========================================
      function filterProducts() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedCategory = categoryFilter ? categoryFilter.value : '';

        filteredProducts = allProductsData.filter(product => {
          // Search: cari di nama, category, options, dll
          const searchText = `
            ${product.name || ''} 
            ${product.category?.category_name || ''} 
            ${product.parent_options?.map(o => o.name).join(' ') || ''}
          `.toLowerCase();
          
          const matchesSearch = !searchTerm || searchText.includes(searchTerm);

          // Category filter
          const matchesCategory = !selectedCategory || product.category_id == selectedCategory;

          return matchesSearch && matchesCategory;
        });

        currentPage = 1; // Reset ke halaman pertama
        renderTable();
      }

      // ==========================================
      // RENDER TABLE
      // ==========================================
      function renderTable() {
        // Hitung pagination
        const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentProducts = filteredProducts.slice(startIndex, endIndex);

        // Clear table
        tableBody.innerHTML = '';

        // Render rows
        if (currentProducts.length === 0) {
          tableBody.innerHTML = `
            <tr>
              <td colspan="6" class="text-center">
                <div class="table-empty-state">
                  <span class="material-symbols-outlined">search_off</span>
                  <h4>No results found</h4>
                  <p>Try adjusting your search or filter</p>
                </div>
              </td>
            </tr>
          `;
        } else {
          currentProducts.forEach((product, index) => {
            const rowNumber = startIndex + index + 1;
            const row = createProductRow(product, rowNumber);
            tableBody.appendChild(row);
          });
        }

        // Handle pagination visibility
        if (paginationWrapper) {
          // Tampilkan pagination jika data lebih dari itemsPerPage
          if (filteredProducts.length <= itemsPerPage) {
            paginationWrapper.style.display = 'none';
          } else {
            paginationWrapper.style.display = '';
            renderPagination(totalPages);
          }
        }
      }

      // ==========================================
      // CREATE PRODUCT ROW
      // ==========================================
      function createProductRow(product, rowNumber) {
        const tr = document.createElement('tr');
        tr.className = 'table-row';
        tr.dataset.category = product.category_id;

        // Format image
        let imageSrc = '';
        if (product.pictures && Array.isArray(product.pictures) && product.pictures.length > 0) {
          imageSrc = `{{ asset('') }}${product.pictures[0].path}`;
        }

        // Format options
        let optionsDisplay = '{{ __("messages.partner.product.all_product.no_options_product") }}';
        if (product.parent_options && product.parent_options.length > 0) {
          optionsDisplay = product.parent_options.map(opt => opt.name).join(', ');
        }

        // Format quantity
        let quantityDisplay = '<span class="text-muted">0</span>';
        const qtyAvailable = Math.round(product.quantity_available || 0);
        
        if (product.stock_type === 'linked') {
          quantityDisplay = `<span class="fw-600">${qtyAvailable.toLocaleString('id-ID')}</span>`;
        } else if (parseInt(product.always_available_flag) === 1) {
          quantityDisplay = '<span class="text-muted">{{ __("messages.partner.product.all_product.always_available") }}</span>';
        } else if (product.stock) {
          quantityDisplay = `<span class="fw-600">${qtyAvailable.toLocaleString('id-ID')}</span>`;
        }

        // Format price
        const priceDisplay = `Rp ${parseInt(product.price || 0).toLocaleString('id-ID')}`;

        // URLs
        const showUrl = `/partner/products/${product.id}`;
        const editUrl = `/partner/products/${product.id}/edit`;

        tr.innerHTML = `
          <td class="text-center text-muted">${rowNumber}</td>
          <td>
            <div class="user-info-cell">
              ${imageSrc ? 
                `<img src="${imageSrc}" alt="${product.name}" class="user-avatar" loading="lazy">` :
                `<div class="user-avatar-placeholder">
                  <span class="material-symbols-outlined">inventory_2</span>
                </div>`
              }
              <span class="data-name">${product.name}</span>
            </div>
          </td>
          <td>
            <span class="text-secondary">${optionsDisplay}</span>
          </td>
          <td>
            ${quantityDisplay}
          </td>
          <td>
            <span class="fw-600">${priceDisplay}</span>
          </td>
          <td class="text-center">
            <div class="table-actions">
              <a href="${showUrl}" class="btn-table-action view" title="{{ __('messages.partner.product.all_product.detail') }}">
                <span class="material-symbols-outlined">visibility</span>
              </a>
              <a href="${editUrl}" class="btn-table-action edit" title="{{ __('messages.partner.product.all_product.edit') }}">
                <span class="material-symbols-outlined">edit</span>
              </a>
            </div>
          </td>
        `;

        return tr;
      }

      // ==========================================
      // RENDER PAGINATION (MATCH LARAVEL STYLE)
      // ==========================================
      function renderPagination(totalPages) {
        if (!paginationWrapper) return;

        // Hapus konten lama
        paginationWrapper.innerHTML = '';

        // Buat struktur pagination yang sama dengan Laravel
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
          // Tampilkan: halaman pertama, terakhir, dan 2 halaman sekitar current page
          if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
            
            if (i === currentPage) {
              pageLi.innerHTML = `
                <span class="page-link" aria-current="page">${i}</span>
              `;
            } else {
              pageLi.innerHTML = `
                <a href="#" class="page-link" data-page="${i}">${i}</a>
              `;
            }
            
            ul.appendChild(pageLi);
          } else if (i === currentPage - 2 || i === currentPage + 2) {
            // Dots untuk gap
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

        // Add click handlers untuk semua link
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
        searchInput.addEventListener('input', filterProducts);
      }

      if (categoryFilter) {
        categoryFilter.addEventListener('change', filterProducts);
      }

      // ==========================================
      // INITIALIZE
      // ==========================================
      renderTable();
    });

    // ==========================================
    // DELETE PRODUCT FUNCTION
    // ==========================================
    function deleteProduct(productId) {
      Swal.fire({
        title: '{{ __("messages.partner.product.all_product.delete_confirmation_1") }}',
        text: '{{ __("messages.partner.product.all_product.delete_confirmation_2") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ae1504',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __("messages.partner.product.all_product.delete_confirmation_3") }}',
        cancelButtonText: '{{ __("messages.partner.product.all_product.delete_confirmation_4") }}'
      }).then((result) => {
        if (result.isConfirmed) {
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = `/partner/products/${productId}`;
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