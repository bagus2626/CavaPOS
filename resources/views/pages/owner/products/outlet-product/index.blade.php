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
      const searchInput = document.getElementById('searchInput');
      const categoryFilter = document.getElementById('categoryFilter');
      const tableBody = document.getElementById('productTableBody');

      if (!tableBody) return;

      const rows = tableBody.querySelectorAll('tr.table-row');

      // ==========================================
      // FILTER TABLE
      // ==========================================
      function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedCategory = categoryFilter ? categoryFilter.value : '';

        let visibleCount = 0;

        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const category = row.dataset.category;

          const matchesSearch = !searchTerm || text.includes(searchTerm);
          const matchesCategory = !selectedCategory || category === selectedCategory;

          if (matchesSearch && matchesCategory) {
            row.style.display = '';
            visibleCount++;

            // Update row number
            const firstCell = row.querySelector('td:first-child');
            if (firstCell) {
              firstCell.textContent = visibleCount;
            }
          } else {
            row.style.display = 'none';
          }
        });

        // Handle empty state
        handleEmptyState(visibleCount);
      }

      // ==========================================
      // EMPTY STATE HANDLER
      // ==========================================
      function handleEmptyState(visibleCount) {
        const existingEmptyRow = tableBody.querySelector('.empty-filter-row');
        if (existingEmptyRow) {
          existingEmptyRow.remove();
        }

        if (visibleCount === 0 && rows.length > 0) {
          const emptyRow = document.createElement('tr');
          emptyRow.classList.add('empty-filter-row');
          emptyRow.innerHTML = `
            <td colspan="8" class="text-center">
              <div class="table-empty-state">
                <span class="material-symbols-outlined">search_off</span>
                <h4>No results found</h4>
                <p>Try adjusting your search or filter</p>
              </div>
            </td>
          `;
          tableBody.appendChild(emptyRow);
        }
      }

      // ==========================================
      // EVENT LISTENERS
      // ==========================================
      if (searchInput) {
        searchInput.addEventListener('input', filterTable);
      }

      if (categoryFilter) {
        categoryFilter.addEventListener('change', filterTable);
      }
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

      // ==========================================
      // RESET FORM FIELDS
      // ==========================================
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

      // ==========================================
      // GET DEFAULT CATEGORY
      // ==========================================
      function getDefaultCategoryId() {
        const current = categorySelect.value;
        if (current) return current;
        
        const firstOption = Array.from(categorySelect.options).find(opt => opt.value && opt.value !== '');
        return firstOption ? firstOption.value : '';
      }

      // ==========================================
      // RENDER MASTER PRODUCT CHECKBOXES
      // ==========================================
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

      // ==========================================
      // SELECT ALL TOGGLE
      // ==========================================
      mpSelectAll.addEventListener('change', function() {
        const checked = this.checked;
        mpBox.querySelectorAll('input[type="checkbox"][name="master_product_ids[]"]').forEach(cb => {
          cb.checked = checked;
        });
      });

      // ==========================================
      // FORM SUBMIT VALIDATION
      // ==========================================
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

      // ==========================================
      // OPEN MODAL - ADD PRODUCT BUTTON
      // ==========================================
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

      // ==========================================
      // MODAL HIDDEN - RESET
      // ==========================================
      if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
          hardResetFields(true);
        });
      }

      // ==========================================
      // LOAD MASTER PRODUCTS
      // ==========================================
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

      // ==========================================
      // CATEGORY CHANGE
      // ==========================================
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