@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.stock_list'))
@section('page_title', __('messages.owner.products.stocks.all_stock'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <div class="page-header">
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

      <div class="modern-card mb-4">
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
                <form action="{{ route('owner.user-owner.stocks.index') }}" method="GET" id="locationFilterForm">
                  <select id="locationFilter" name="filter_location" class="form-control-modern"
                    onchange="this.form.submit()">
                    <option value="owner" {{ $filterLocation == 'owner' ? 'selected' : '' }}>
                      {{ __('messages.owner.products.stocks.owner_warehouse') }}
                    </option>
                    @foreach ($partners as $partner)
                      <option value="{{ $partner->username }}" {{ $filterLocation == $partner->username ? 'selected' : '' }}>
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

      <div class="modern-card mb-4">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">

          <div class="stock-actions-container">

            <div class="stock-actions-group">
              <a href="{{ route('owner.user-owner.stocks.movements.create-stock-in') }}"
                class="btn-modern btn-sm-modern btn-secondary-modern">
                {{ __('messages.owner.products.stocks.stock_in') }}
              </a>

              <a href="{{ route('owner.user-owner.stocks.movements.create-transfer') }}"
                class="btn-modern btn-sm-modern btn-secondary-modern">
                {{ __('messages.owner.products.stocks.transfer') }}
              </a>

              <a href="{{ route('owner.user-owner.stocks.movements.create-adjustment') }}"
                class="btn-modern btn-sm-modern btn-secondary-modern">
                {{ __('messages.owner.products.stocks.adjustment') }}
              </a>
            </div>

            <div class="stock-history-wrapper">
              <a href="{{ route('owner.user-owner.stocks.movements.index') }}"
                class="btn-modern btn-sm-modern btn-secondary-modern">
                <span class="material-symbols-outlined">history</span>
                {{ __('messages.owner.products.stocks.movement_history') }}
              </a>
            </div>

          </div>

        </div>
      </div>


      @include('pages.owner.products.stock.display')

    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // ==========================================
    // STOCK INDEX - TAB FILTER, SEARCH & PAGINATION
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const tableBody = document.getElementById('stockTableBody');
      const paginationWrapper = document.querySelector('.table-pagination');
      const filterTabs = document.querySelectorAll('.nav-tabs-modern .nav-link');
      const locationFilter = document.getElementById('locationFilter');

      if (!tableBody) {
        console.error('Table body not found');
        return;
      }

      // Ambil semua data dari Blade
      const allStocksData = @json($allStocksFormatted ?? []);

      let filteredStocks = [...allStocksData];
      let currentFilterType = 'all'; // all, linked, direct
      let currentLocation = locationFilter ? locationFilter.value : 'owner';
      const itemsPerPage = 10;
      let currentPage = 1;

      // ==========================================
      // TAB FILTER CLICK HANDLER
      // ==========================================
      filterTabs.forEach(tab => {
        tab.addEventListener('click', function (e) {
          e.preventDefault();

          // Update active tab
          filterTabs.forEach(t => t.classList.remove('active'));
          this.classList.add('active');

          // Get filter type
          currentFilterType = this.getAttribute('data-filter-type');

          // Apply filter
          filterStocks();
        });
      });

      // ==========================================
      // FILTER FUNCTION
      // ==========================================
      function filterStocks() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

        filteredStocks = allStocksData.filter(stock => {
          // Tab Filter: berdasarkan stock_type
          let matchesTab = true;
          if (currentFilterType === 'linked') {
            matchesTab = stock.stock_type === 'linked';
          } else if (currentFilterType === 'direct') {
            matchesTab = stock.stock_type === 'direct';
          }
          // 'all' tidak perlu filter

          // Search: cari di stock_code, stock_name
          const searchText = `
          ${stock.stock_code || ''} 
          ${stock.stock_name || ''}
        `.toLowerCase();

          const matchesSearch = !searchTerm || searchText.includes(searchTerm);

          return matchesTab && matchesSearch;
        });

        currentPage = 1; // Reset ke halaman pertama
        renderTable();
      }

      // ==========================================
      // RENDER TABLE
      // ==========================================
      function renderTable() {
        // Hitung pagination
        const totalPages = Math.ceil(filteredStocks.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentStocks = filteredStocks.slice(startIndex, endIndex);

        // Clear table
        tableBody.innerHTML = '';

        // Render rows
        if (currentStocks.length === 0) {
          tableBody.innerHTML = `
          <tr class="empty-filter-row">
            <td colspan="7" class="text-center">
              <div class="table-empty-state">
                <span class="material-symbols-outlined">search_off</span>
                <h4>{{ __('messages.owner.products.stocks.no_results_found') }}</h4>
                <p>{{ __('messages.owner.products.stocks.adjust_search_filter') }}</p>
              </div>
            </td>
          </tr>
        `;
        } else {
          currentStocks.forEach((stock, index) => {
            const rowNumber = startIndex + index + 1;
            const row = createStockRow(stock, rowNumber);
            tableBody.appendChild(row);
          });
        }

        // Handle pagination visibility
        if (paginationWrapper) {
          if (filteredStocks.length <= itemsPerPage) {
            paginationWrapper.style.display = 'none';
          } else {
            paginationWrapper.style.display = '';
            renderPagination(totalPages, startIndex, endIndex);
          }
        }
      }

      // ==========================================
      // CREATE STOCK ROW
      // ==========================================
      function createStockRow(stock, rowNumber) {
        const tr = document.createElement('tr');
        tr.className = 'table-row';
        tr.setAttribute('data-type', stock.type || '');
        tr.setAttribute('data-stock_type', stock.stock_type || '');

        const partnerType = stock.partner_product_id && !stock.partner_product_option_id
          ? 'product'
          : (stock.partner_product_id && stock.partner_product_option_id ? 'option' : 'none');
        tr.setAttribute('data-partner-type', partnerType);

        // Format quantity
        const formattedQuantity = new Intl.NumberFormat('id-ID', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        }).format(stock.display_quantity);

        // Unit display
        let unitDisplay = '';
        if (stock.display_unit_name) {
          unitDisplay = `<span class="badge-modern badge-info">${stock.display_unit_name}</span>`;
        } else {
          unitDisplay = `<span class="text-muted small">({{ __('messages.owner.products.stocks.base_unit') }})</span>`;
        }

        tr.innerHTML = `
        <td class="text-center text-muted">${rowNumber}</td>
        <td class="mono fw-600">${stock.stock_code}</td>
        <td><span class="fw-600">${stock.stock_name}</span></td>
        <td>${formattedQuantity}</td>
        <td>${unitDisplay}</td>
        <td><span class="fw-600">${stock.last_price_per_unit}</span></td>
        <td class="text-center">
          <div class="table-actions">
            <button onclick="deleteStock(${stock.id})"
              class="btn-table-action delete"
              title="{{ __('messages.owner.products.stocks.delete') }}">
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
      function renderPagination(totalPages, startIndex, endIndex) {
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
          link.addEventListener('click', function (e) {
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
        searchInput.addEventListener('input', filterStocks);
      }

      // ==========================================
      // INITIALIZE
      // ==========================================
      renderTable();
    });

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