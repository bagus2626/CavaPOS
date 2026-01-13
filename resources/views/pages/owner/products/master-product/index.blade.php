@extends('layouts.owner')

@section('title', __('messages.owner.products.master_products.master_product_list'))
@section('page_title', __('messages.owner.products.master_products.master_products'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.master_products.master_products') }}</h1>
          <p class="page-subtitle">{{ __('messages.owner.products.master_products.manage_catalog_subtitle') }}</p>
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

      <div class="modern-card mb-4">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <div class="table-controls">
            <div class="search-filter-group">
              <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                <span class="input-icon">
                  <span class="material-symbols-outlined">search</span>
                </span>
                <input type="text" id="searchInput" class="form-control-modern with-icon"
                  placeholder="{{ __('messages.owner.products.master_products.search_placeholder') }}">
              </div>

              <div class="select-wrapper" style="min-width: 200px;">
                <select id="categoryFilter" class="form-control-modern">
                  <option value="">{{ __('messages.owner.products.master_products.all') }}</option>
                  @foreach($categories as $category)
                    <option value="{{ $category->id }}">
                      {{ $category->category_name }}
                    </option>
                  @endforeach
                </select>
                <span class="material-symbols-outlined select-arrow">expand_more</span>
              </div>
            </div>

            <a href="{{ route('owner.user-owner.master-products.create') }}" class="btn-modern btn-primary-modern">
              <span class="material-symbols-outlined">add</span>
              {{ __('messages.owner.products.master_products.add_product') ?? 'Add Product' }}
            </a>
          </div>
        </div>
      </div>

      @include('pages.owner.products.master-product.display')

    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // ==========================================
    // MASTER PRODUCTS INDEX - SEARCH & FILTER (NO RELOAD)
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const categoryFilter = document.getElementById('categoryFilter');
      const tableBody = document.getElementById('productTableBody');
      const paginationWrapper = document.querySelector('.table-pagination');

      if (!tableBody) {
        console.error('Table body not found');
        return;
      }

      // Ambil semua data dari Blade
      const allProductsData = @json($allProductsFormatted ?? []);
      
      let filteredProducts = [...allProductsData];
      const itemsPerPage = 10;
      let currentPage = 1;

      // ==========================================
      // FILTER FUNCTION
      // ==========================================
      function filterProducts() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedCategory = categoryFilter ? categoryFilter.value.trim() : '';

        filteredProducts = allProductsData.filter(product => {
          // Search: cari di name, category, options
          const searchText = `
            ${product.name || ''} 
            ${product.category_name || ''} 
            ${product.parent_options || ''}
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
          // UPDATE: Menggunakan key no_results_found & adjust_search_filter
          tableBody.innerHTML = `
            <tr class="empty-filter-row">
              <td colspan="7" class="text-center">
                <div class="table-empty-state">
                  <span class="material-symbols-outlined" style="font-size: 4rem; color: #ccc; display: block; margin-bottom: 1rem;">search_off</span>
                  <h4 style="margin: 0 0 0.5rem 0; color: #666; font-size: 1.25rem;">{{ __('messages.owner.products.master_products.no_results_found') }}</h4>
                  <p style="margin: 0; color: #999;">{{ __('messages.owner.products.master_products.adjust_search_filter') }}</p>
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
        tr.setAttribute('data-category', product.category_id || '');

        // Format image
        let imageHtml = '';
        if (product.picture) {
          const imgSrc = product.picture.startsWith('http://') || product.picture.startsWith('https://')
            ? product.picture
            : `{{ asset('') }}${product.picture}`;
          imageHtml = `<img src="${imgSrc}" alt="${product.name}" class="user-avatar" loading="lazy">`;
        } else {
          imageHtml = `
            <div class="user-avatar-placeholder">
              <span class="material-symbols-outlined">inventory_2</span>
            </div>
          `;
        }

        // Format options
        let optionsHtml = '';
        if (product.has_options && product.parent_options) {
          optionsHtml = `<span class="text-secondary">${product.parent_options}</span>`;
        } else {
          optionsHtml = '<span class="text-muted">{{ __("messages.owner.products.master_products.no_options") }}</span>';
        }

        // Format promo
        let promoHtml = '';
        if (product.promotion_name) {
          promoHtml = `<span class="badge-modern badge-warning">${product.promotion_name}</span>`;
        } else {
          promoHtml = '<span class="text-muted">—</span>';
        }

        // URLs
        const showUrl = `/owner/user-owner/master-products/${product.id}`;
        const editUrl = `/owner/user-owner/master-products/${product.id}/edit`;

        tr.innerHTML = `
          <td class="text-center text-muted">${rowNumber}</td>
          <td>
            <div class="user-info-cell">
              ${imageHtml}
              <span class="data-name">${product.name || '-'}</span>
            </div>
          </td>
          <td>
            ${optionsHtml}
          </td>
          <td>
            <span class="fw-600">${product.quantity || 0}</span>
          </td>
          <td>
            <span class="fw-600">Rp ${new Intl.NumberFormat('id-ID').format(product.price || 0)}</span>
          </td>
          <td>
            ${promoHtml}
          </td>
          <td class="text-center">
            <div class="table-actions">
              <a href="${showUrl}" class="btn-table-action view" title="{{ __('messages.owner.products.master_products.detail') }}">
                <span class="material-symbols-outlined">visibility</span>
              </a>
              <a href="${editUrl}" class="btn-table-action edit" title="{{ __('messages.owner.products.master_products.edit') }}">
                <span class="material-symbols-outlined">edit</span>
              </a>
              <button onclick="deleteProduct(${product.id})" class="btn-table-action delete" title="{{ __('messages.owner.products.master_products.delete') }}">
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
        // UPDATE: Menggunakan key pagination_navigation
        nav.setAttribute('aria-label', '{{ __('messages.owner.products.master_products.pagination_navigation') }}');
        
        const ul = document.createElement('ul');
        ul.className = 'pagination';

        // Previous Button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        
        // UPDATE: Menggunakan key pagination_previous
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
            <a href="#" class="page-link" data-page="${currentPage - 1}" aria-label="{{ __('messages.owner.products.master_products.pagination_previous') }}">
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
        
        // UPDATE: Menggunakan key pagination_next
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
            <a href="#" class="page-link" data-page="${currentPage + 1}" aria-label="{{ __('messages.owner.products.master_products.pagination_next') }}">
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
        title: '{{ __('messages.owner.products.master_products.delete_confirmation_1') }}',
        text: '{{ __('messages.owner.products.master_products.delete_confirmation_2') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ae1504',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('messages.owner.products.master_products.delete_confirmation_3') }}',
        cancelButtonText: '{{ __('messages.owner.products.master_products.cancel') }}'
      }).then((result) => {
        if (result.isConfirmed) {
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = `/owner/user-owner/master-products/${productId}`;
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