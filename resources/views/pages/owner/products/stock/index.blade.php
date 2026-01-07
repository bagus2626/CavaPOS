@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.stock_list'))
@section('page_title', __('messages.owner.products.stocks.all_stock'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.stocks.all_stock') }}</h1>
          <p class="page-subtitle">Manage your inventory and stock levels</p>
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
                <input type="text" id="searchInput" class="form-control-modern with-icon" placeholder="Search stocks...">
              </div>

              <!-- Filter by Location -->
              <div class="select-wrapper" style="min-width: 200px;">
                <form action="{{ route('owner.user-owner.stocks.index') }}" method="GET" id="locationFilterForm">
                  <select id="locationFilter" name="filter_location" class="form-control-modern"
                    onchange="this.form.submit()">
                    <option value="owner" {{ $filterLocation == 'owner' ? 'selected' : '' }}>
                      {{ __('messages.owner.products.stocks.owner_warehouse') }}
                    </option>
                    @foreach ($partners as $partner)
                      <option value="{{ $partner->id }}" {{ $filterLocation == $partner->id ? 'selected' : '' }}>
                        {{ $partner->name }} (Outlet)
                      </option>
                    @endforeach
                  </select>
                  <span class="material-symbols-outlined select-arrow">expand_more</span>
                </form>
              </div>
            </div>

            <!-- Add Stock Button -->
            <a href="{{ route('owner.user-owner.stocks.create') }}" class="btn-modern btn-primary-modern">
              <span class="material-symbols-outlined">add</span>
              {{ __('messages.owner.products.stocks.add_stock_item') }}
            </a>
          </div>
        </div>
      </div>

      <!-- Stock Actions Card -->
      <div class="modern-card mb-4">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">

          <div class="stock-actions-container">

            <!-- ROW 1 : ACTION BUTTONS -->
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

            <!-- ROW 2 : HISTORY -->
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


      <!-- Table Display -->
      @include('pages.owner.products.stock.display')

    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // ==========================================
    // STOCK INDEX - SEARCH & FILTER
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const tableBody = document.getElementById('stockTableBody');

      if (!tableBody) return;

      const rows = tableBody.querySelectorAll('tr.table-row');

      // ==========================================
      // SEARCH FUNCTION
      // ==========================================
      function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';

        let visibleCount = 0;

        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const matchesSearch = !searchTerm || text.includes(searchTerm);

          if (matchesSearch) {
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
              <td colspan="7" class="text-center">
                <div class="table-empty-state">
                  <span class="material-symbols-outlined">search_off</span>
                  <h4>No results found</h4>
                  <p>Try adjusting your search</p>
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
    });

    // ==========================================
    // DELETE STOCK FUNCTION
    // ==========================================
    function deleteStock(stockId) {
      Swal.fire({
        title: '{{ __('messages.owner.products.promotions.delete_confirmation_1') }}',
        text: '{{ __('messages.owner.products.promotions.delete_confirmation_2') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ae1504',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('messages.owner.products.promotions.delete_confirmation_3') }}',
        cancelButtonText: '{{ __('messages.owner.products.promotions.cancel') }}'
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