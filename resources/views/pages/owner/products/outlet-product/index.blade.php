@extends('layouts.owner')

@section('title', __('messages.owner.products.outlet_products.product_list'))
@section('page_title', __('messages.owner.products.outlet_products.outlet_products'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.outlet_products.outlet_products') }}</h1>
          <p class="page-subtitle">Manage products across all your outlets</p>
        </div>
      </div>

      <!-- Success/Error Messages -->
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

      @if(session('error'))
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
              <!-- Filter by Outlet -->
              <div class="select-wrapper" style="min-width: 200px;">
                <select id="outletFilter" class="form-control-modern" onchange="window.location.href=this.value">
                  @foreach($outlets as $outlet)
                    <option value="{{ route('owner.user-owner.outlet-products.index', ['outlet_id' => $outlet->id]) }}" 
                            {{ $currentOutletId == $outlet->id ? 'selected' : '' }}>
                      {{ $outlet->name }}
                    </option>
                  @endforeach
                </select>
                <span class="material-symbols-outlined select-arrow">expand_more</span>
              </div>

              <!-- Filter by Category -->
              <div class="select-wrapper" style="min-width: 200px;">
                <select id="categoryFilter" class="form-control-modern">
                  <option value="">All Categories</option>
                  @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                  @endforeach
                </select>
                <span class="material-symbols-outlined select-arrow">expand_more</span>
              </div>
            </div>

            <!-- Add Product Button -->
            <button class="btn-modern btn-primary-modern btn-add-product" 
                    data-toggle="modal" 
                    data-target="#addProductModal"
                    data-outlet="{{ $currentOutletId }}">
              <span class="material-symbols-outlined">add</span>
              {{ __('messages.owner.products.outlet_products.add_product') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Table Display -->
      @include('pages.owner.products.outlet-product.display')

    </div>
  </div>

  @include('pages.owner.products.outlet-product.modal')
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  {{-- SEARCH & FILTER SCRIPT --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
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
        const selectedCategory = categoryFilter ? categoryFilter.value.trim() : '';

        filteredProducts = allProductsData.filter(product => {
          // Category filter
          const matchesCategory = !selectedCategory || String(product.category_id) === selectedCategory;

          return matchesCategory;
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
            <tr class="empty-filter-row">
              <td colspan="8" class="text-center">
                <div class="table-empty-state">
                  <span class="material-symbols-outlined">search_off</span>
                  <h4>No results found</h4>
                  <p>Try adjusting your filter</p>
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

        // Product image
        let imageHtml = '';
        if (product.picture_path) {
          imageHtml = `
            <img src="{{ asset('') }}${product.picture_path}"
                 alt="${product.name}"
                 class="user-avatar"
                 style="width:40px; height:40px; object-fit:cover; border-radius:6px;"
                 loading="lazy">
          `;
        } else {
          imageHtml = `
            <div class="user-avatar-placeholder">
              <span class="material-symbols-outlined">image</span>
            </div>
          `;
        }

        // Hot product badge
        let hotBadge = '';
        if (product.is_hot_product) {
          hotBadge = `
            <span style="
              position:absolute;
              top:-6px;
              right:-6px;
              background:#ff5722;
              color:white;
              padding:2px 6px;
              border-radius:8px;
              font-size:10px;
              font-weight:600;
              box-shadow:0 2px 6px rgba(0,0,0,0.2);
            ">
              HOT
            </span>
          `;
        }

        // Status badge
        const statusBadgeClass = product.is_active ? 'badge-success' : 'badge-danger';
        const statusText = product.is_active 
          ? '{{ __("messages.owner.products.outlet_products.active") }}'
          : '{{ __("messages.owner.products.outlet_products.inactive") }}';

        // Promotion
        const promoText = product.promotion_name 
          ? `<span class="badge-modern badge-warning">${product.promotion_name}</span>`
          : '<span class="text-muted">—</span>';

        // URLs
        const editUrl = `/owner/user-owner/outlet-products/${product.id}/edit`;

        tr.innerHTML = `
          <td class="text-center text-muted">${rowNumber}</td>
          <td>
            <div class="user-info-cell">
              <div class="position-relative" style="width:40px; height:40px;">
                ${imageHtml}
                ${hotBadge}
              </div>
              <span class="user-name">${product.name}</span>
            </div>
          </td>
          <td>
            <span class="badge-modern badge-info">
              ${product.category_name}
            </span>
          </td>
          <td>
            ${product.stock_display}
          </td>
          <td class="text-center">
            <span class="badge-modern ${statusBadgeClass}">
              ${statusText}
            </span>
          </td>
          <td>
            <span class="fw-600">Rp ${new Intl.NumberFormat('id-ID').format(product.price)}</span>
          </td>
          <td>
            ${promoText}
          </td>
          <td class="text-center">
            <div class="table-actions">
              <a href="${editUrl}"
                 class="btn-table-action edit"
                 title="{{ __('messages.owner.products.outlet_products.edit') }}">
                <span class="material-symbols-outlined">edit</span>
              </a>
              <button onclick="deleteProduct(${product.id})" 
                      class="btn-table-action delete"
                      title="{{ __('messages.owner.products.outlet_products.delete') }}">
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
      if (categoryFilter) {
        categoryFilter.addEventListener('change', filterProducts);
      }

      // ==========================================
      // INITIALIZE
      // ==========================================
      renderTable();
    });
  </script>

  {{-- DELETE PRODUCT SCRIPT --}}
  <script>
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
        const url = "{{ route('owner.user-owner.outlet-products.destroy', ':id') }}".replace(':id', id);
        const formData = new FormData();
        formData.append('_method', 'DELETE');

        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
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

  {{-- MODAL ADD PRODUCT SCRIPT --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const modal = document.getElementById('addProductModal');
      const form = document.getElementById('outletProductQuickAddForm');
      const outletInput = document.getElementById('qp_outlet_id');
      const categorySelect = document.getElementById('qp_category_id');
      const mpBox = document.getElementById('qp_master_product_box');
      const mpSelectAll = document.getElementById('qp_check_all');
      const mpError = document.getElementById('qp_mp_error');
      const qtyInput = document.getElementById('qp_quantity');
      const statusSelect = document.getElementById('qp_is_active');

      form.setAttribute('autocomplete', 'off');
      form.querySelectorAll('input, select').forEach(el => el.setAttribute('autocomplete', 'off'));

      function hardResetFields(keepOutlet = true) {
        form.reset();
        categorySelect.value = '';
        mpBox.innerHTML = '<div class="text-muted small text-center" style="padding: 2rem 1rem;"><span class="material-symbols-outlined" style="font-size: 2rem; opacity: 0.3; display: block; margin-bottom: 0.5rem;">inventory_2</span>Select category first</div>';
        mpSelectAll.disabled = true;
        mpSelectAll.checked = false;
        mpError.style.display = 'none';
        qtyInput.value = '0';
        statusSelect.value = '1';
        if (!keepOutlet) outletInput.value = '';
      }

      function getDefaultCategoryId() {
        const current = categorySelect.value;
        if (current) return current;
        
        const firstOption = Array.from(categorySelect.options).find(opt => opt.value && opt.value !== '');
        return firstOption ? firstOption.value : '';
      }

      function renderMasterProductCheckboxes(items) {
        mpBox.innerHTML = '';
        mpSelectAll.disabled = true;
        mpSelectAll.checked = false;

        if (!Array.isArray(items) || items.length === 0) {
          mpBox.innerHTML = '<div class="text-muted small text-center" style="padding: 2rem 1rem;">No master products available</div>';
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

      mpSelectAll.addEventListener('change', function() {
        const checked = this.checked;
        mpBox.querySelectorAll('input[type="checkbox"][name="master_product_ids[]"]').forEach(cb => {
          cb.checked = checked;
        });
      });

      form.addEventListener('submit', function(e) {
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
          e.preventDefault();
          const btn = e.target.closest('.btn-add-product');
          const outletId = btn.getAttribute('data-outlet') || '';

          hardResetFields(true);
          outletInput.value = outletId;
          
          const catId = getDefaultCategoryId();
          if (catId) {
            categorySelect.value = catId;
            loadMasterProducts(catId, outletId);
          }
        }
      });

      if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
          hardResetFields(true);
        });
      }

      async function loadMasterProducts(categoryId, outletId) {
        mpBox.innerHTML = '<div class="text-muted small text-center" style="padding: 2rem 1rem;">Loading...</div>';
        mpSelectAll.disabled = true;
        mpSelectAll.checked = false;
        mpError.style.display = 'none';

        try {
          const url = new URL("{{ route('owner.user-owner.outlet-products.get-master-products') }}", window.location.origin);
          url.searchParams.set('category_id', categoryId || 'all');
          if (outletId) url.searchParams.set('outlet_id', outletId);

          const res = await fetch(url.toString(), { 
            headers: { 'Accept': 'application/json' } 
          });
          const data = await res.json();
          renderMasterProductCheckboxes(data);
        } catch {
          mpBox.innerHTML = '<div class="text-danger small text-center" style="padding: 2rem 1rem;">Failed to load master products</div>';
        }
      }

      categorySelect.addEventListener('change', function() {
        loadMasterProducts(this.value, outletInput.value);
      });
    });
  </script>

  {{-- STOCK TYPE TOGGLE --}}
  <script>
    (function () {
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
@endpush