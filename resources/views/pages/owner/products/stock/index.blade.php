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
                <input
                  type="text"
                  id="searchInput"
                  class="form-control-modern with-icon"
                  placeholder="{{ __('messages.owner.products.stocks.search_placeholder') }}"
                  oninput="debouncedStockSearch(this, 400)"
                >

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
    // ==============================
    // Debounce helper untuk search
    // ==============================
    function debouncedStockSearch(el, delay = 400){
      if (!el) return;
      if (el._debounceTimer) clearTimeout(el._debounceTimer);
      el._debounceTimer = setTimeout(() => {
        window.__applyStockFilter?.(); // panggil global function
      }, delay);
    }

    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const tableBody = document.getElementById('stockTableBody');
      const mobileList = document.getElementById('stockMobileList');
      const paginationWrapper = document.getElementById('stockPagination');
      const filterTabs = document.querySelectorAll('#stockFilterTabs .nav-link');

      if (!tableBody || !mobileList || !paginationWrapper) {
        console.error('Stock UI containers not found');
        return;
      }

      const allStocksData = @json($allStocksFormatted ?? []);

      let filtered = [...allStocksData];
      let currentFilterType = 'linked'; // default sesuai kamu
      const itemsPerPage = 10;
      let currentPage = 1;

      // set active tab awal (linked)
      filterTabs.forEach(tab => {
        if (tab.getAttribute('data-filter-type') === currentFilterType) tab.classList.add('active');
        else tab.classList.remove('active');
      });

      // ==============================
      // FILTER
      // ==============================
      function applyFilter() {
        const q = (searchInput?.value || '').toLowerCase().trim();

        filtered = allStocksData.filter(s => {
          // Tab filter
          let matchesTab = true;
          if (currentFilterType === 'linked') matchesTab = s.stock_type === 'linked';
          else if (currentFilterType === 'direct') matchesTab = s.stock_type === 'direct';
          // all => true

          // Search
          const hay = `${s.stock_code || ''} ${s.stock_name || ''}`.toLowerCase();
          const matchesSearch = !q || hay.includes(q);

          return matchesTab && matchesSearch;
        });

        currentPage = 1;
        render();
      }

      // expose global supaya debouncedStockSearch bisa manggil
      window.__applyStockFilter = applyFilter;

      // ==============================
      // RENDER
      // ==============================
      function render() {
        renderDesktopTable();
        renderMobileCards();
        renderPagination();
      }

      function emptyStateHtml() {
        return `
          <div class="table-empty-state" style="padding: 20px;">
            <span class="material-symbols-outlined">search_off</span>
            <h4>{{ __('messages.owner.products.stocks.no_results_found') }}</h4>
            <p>{{ __('messages.owner.products.stocks.adjust_search_filter') }}</p>
          </div>
        `;
      }

      function getPagedItems() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        return { start, end, items: filtered.slice(start, end) };
      }

      function renderDesktopTable() {
        const { start, items } = getPagedItems();

        tableBody.innerHTML = '';

        if (items.length === 0) {
          tableBody.innerHTML = `
            <tr class="empty-filter-row">
              <td colspan="7" class="text-center">${emptyStateHtml()}</td>
            </tr>
          `;
          return;
        }

        items.forEach((s, idx) => {
          const rowNumber = start + idx + 1;

          const formattedQty = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          }).format(Number(s.display_quantity ?? 0));

          const unitDisplay = s.display_unit_name
            ? `<span class="badge-modern badge-info">${escapeHtml(s.display_unit_name)}</span>`
            : `<span class="text-muted small">({{ __('messages.owner.products.stocks.base_unit') }})</span>`;

          const tr = document.createElement('tr');
          tr.className = 'table-row';
          tr.innerHTML = `
            <td class="text-center text-muted">${rowNumber}</td>
            <td class="mono fw-600">${escapeHtml(s.stock_code ?? '')}</td>
            <td><span class="fw-600">${escapeHtml(s.stock_name ?? '')}</span></td>
            <td>${formattedQty}</td>
            <td>${unitDisplay}</td>
            <td><span class="fw-600">${escapeHtml(String(s.last_price_per_unit ?? ''))}</span></td>
            <td class="text-center">
              <div class="table-actions">
                <button onclick="deleteStock(${s.id})"
                  class="btn-table-action delete"
                  title="{{ __('messages.owner.products.stocks.delete') }}">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </td>
          `;
          tableBody.appendChild(tr);
        });
      }

      function renderMobileCards() {
        const { items } = getPagedItems();

        mobileList.innerHTML = '';

        if (items.length === 0) {
          mobileList.innerHTML = emptyStateHtml();
          return;
        }

        items.forEach(s => {
          const formattedQty = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          }).format(Number(s.display_quantity ?? 0));

          const unitName = s.display_unit_name || '{{ __('messages.owner.products.stocks.base_unit') }}';
          const code = s.stock_code ?? '';
          const name = s.stock_name ?? '';

          const card = document.createElement('div');
          card.className = 'stock-card';
          card.innerHTML = `
            <div class="stock-card__top">
              <div class="stock-card__title">
                <div class="stock-card__code">${escapeHtml(code)}</div>
                <div class="stock-card__name">${escapeHtml(name)}</div>
              </div>
            </div>

            <div class="stock-card__bottom">
              <span class="stock-chip">
                <span class="material-symbols-outlined">inventory</span>
                <span>${formattedQty}</span>
              </span>

              <span class="stock-chip">
                <span class="material-symbols-outlined">straighten</span>
                <span>${escapeHtml(unitName)}</span>
              </span>

              <span class="stock-chip">
                <span class="material-symbols-outlined">payments</span>
                <span>${escapeHtml(String(s.last_price_per_unit ?? ''))}</span>
              </span>

              <div class="stock-actions">
                <button type="button" class="btn-card-action danger" onclick="deleteStock(${s.id})">
                  <span class="material-symbols-outlined">delete</span>
                  <span>{{ __('messages.owner.products.stocks.delete') }}</span>
                </button>
              </div>
            </div>
          `;
          mobileList.appendChild(card);
        });
      }

      function renderPagination() {
        const totalPages = Math.ceil(filtered.length / itemsPerPage);

        paginationWrapper.innerHTML = '';

        if (totalPages <= 1) return;

        const nav = document.createElement('nav');
        nav.setAttribute('role', 'navigation');
        nav.setAttribute('aria-label', 'Pagination Navigation');

        const ul = document.createElement('ul');
        ul.className = 'pagination';

        // Prev
        ul.appendChild(makePageItem('prev', currentPage - 1, currentPage === 1));

        // Numbers (WAJIB langsung <li>)
        for (let i = 1; i <= totalPages; i++) {
          const show =
            i === 1 ||
            i === totalPages ||
            (i >= currentPage - 1 && i <= currentPage + 1);

          const showDots =
            i === currentPage - 2 || i === currentPage + 2;

          if (show) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = (i === currentPage)
              ? `<span class="page-link" aria-current="page">${i}</span>`
              : `<a href="#" class="page-link" data-page="${i}">${i}</a>`;
            ul.appendChild(li);
          } else if (showDots) {
            const li = document.createElement('li');
            li.className = 'page-item disabled';
            li.innerHTML = `<span class="page-link">...</span>`;
            ul.appendChild(li);
          }
        }

        // Next
        ul.appendChild(makePageItem('next', currentPage + 1, currentPage === totalPages));

        nav.appendChild(ul);
        paginationWrapper.appendChild(nav);

        // Handlers
        nav.querySelectorAll('a.page-link[data-page]').forEach(a => {
          a.addEventListener('click', (e) => {
            e.preventDefault();
            const p = parseInt(a.dataset.page, 10);
            if (!Number.isFinite(p)) return;
            if (p >= 1 && p <= totalPages && p !== currentPage) {
              currentPage = p;
              render();
              window.scrollTo({ top: 0, behavior: 'smooth' });
            }
          });
        });

        function makePageItem(type, page, disabled) {
          const li = document.createElement('li');
          li.className = `page-item ${disabled ? 'disabled' : ''}`;

          if (disabled) {
            li.innerHTML = `<span class="page-link" aria-hidden="true">${type === 'prev' ? '‹' : '›'}</span>`;
          } else {
            li.innerHTML = `<a href="#" class="page-link" data-page="${page}" aria-label="${type}">${type === 'prev' ? '‹' : '›'}</a>`;
          }
          return li;
        }
      }


      // ==============================
      // TABS HANDLER
      // ==============================
      filterTabs.forEach(tab => {
        tab.addEventListener('click', function(e){
          e.preventDefault();
          filterTabs.forEach(t => t.classList.remove('active'));
          this.classList.add('active');
          currentFilterType = this.getAttribute('data-filter-type') || 'linked';
          applyFilter();
        });
      });

      // init pertama kali
      applyFilter();

      // ==============================
      // helper: escape HTML
      // ==============================
      function escapeHtml(str){
        return String(str ?? '')
          .replaceAll('&','&amp;')
          .replaceAll('<','&lt;')
          .replaceAll('>','&gt;')
          .replaceAll('"','&quot;')
          .replaceAll("'",'&#039;');
      }
    });
  </script>
  <script>
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