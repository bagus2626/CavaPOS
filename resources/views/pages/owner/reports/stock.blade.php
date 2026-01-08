@extends('layouts.owner')

@section('title', __('messages.owner.stock_report.title'))
@section('page_title', __('messages.owner.stock_report.page_title'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.stock_report.page_title') }}</h1>
          <p class="page-subtitle">Monitor and analyze stock movements and inventory levels</p>
        </div>
      </div>

      <!-- Filters Card -->
      <div class="modern-card mb-4">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <form method="GET" action="{{ route('owner.user-owner.report.stocks.index') }}">
            <div class="row">
              <!-- Stock Type Filter -->
              <div class="col-md-3">
                <div class="form-group">
                  <label for="stock_type" class="form-label-modern">
                    {{ __('messages.owner.stock_report.filter.stock_type') }}
                  </label>
                  <div class="select-wrapper">
                    <select name="stock_type" id="stock_type" class="form-control-modern">
                      <option value="">{{ __('messages.owner.stock_report.filter.all_types') }}</option>
                      <option value="direct" {{ request('stock_type') == 'direct' ? 'selected' : '' }}>
                        {{ __('messages.owner.stock_report.filter.direct') }}
                      </option>
                      <option value="linked" {{ request('stock_type') == 'linked' ? 'selected' : '' }}>
                        {{ __('messages.owner.stock_report.filter.linked') }}
                      </option>
                    </select>
                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                  </div>
                </div>
              </div>

              <!-- Partner Filter -->
              <div class="col-md-3">
                <div class="form-group">
                  <label for="partner_id" class="form-label-modern">
                    {{ __('messages.owner.stock_report.filter.partner') }}
                  </label>
                  <div class="select-wrapper">
                    <select name="partner_id" id="partner_id" class="form-control-modern">
                      <option value="owner" {{ request('partner_id', 'owner') == 'owner' ? 'selected' : '' }}>
                        {{ __('messages.owner.stock_report.filter.owner_warehouse') }}
                      </option>
                      @foreach ($partners as $partner)
                        <option value="{{ $partner->id }}" {{ request('partner_id') == $partner->id ? 'selected' : '' }}>
                          {{ $partner->name }}
                        </option>
                      @endforeach
                    </select>
                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                  </div>
                </div>
              </div>

              <!-- Month Filter -->
              <div class="col-md-3">
                <div class="form-group">
                  <label for="month" class="form-label-modern">
                    {{ __('messages.owner.stock_report.filter.month') }}
                  </label>
                  <input type="month" 
                         name="month" 
                         id="month" 
                         class="form-control-modern"
                         value="{{ request('month') }}" 
                         max="{{ date('Y-m') }}">
                </div>
              </div>

              <!-- Filter Button -->
              <div class="col-md-3 d-flex align-items-end">
                <div class="form-group">
                  <button type="submit" class="btn-modern btn-primary-modern">
                    {{ __('messages.owner.stock_report.filter.apply_filter') }}
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Table Card with Search & Export -->
      <div class="modern-card mb-4">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <div class="table-controls">
            <!-- Search -->
            <div class="search-filter-group">
              <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                <span class="input-icon">
                  <span class="material-symbols-outlined">search</span>
                </span>
                <input type="text" 
                       id="searchInput" 
                       class="form-control-modern with-icon" 
                       placeholder="{{ __('messages.owner.stock_report.table.search_placeholder') }}">
              </div>
            </div>

            <!-- Export Button -->
            <a href="{{ route('owner.user-owner.report.stocks.export', request()->all()) }}" 
               class="btn-modern btn-sm-modern btn-success-modern">
              <span class="material-symbols-outlined">download</span>
              {{ __('messages.owner.stock_report.table.export_excel') }}
            </a>
          </div>
        </div>
      </div>

      <!-- Table Display -->
      <div class="modern-card">
        <div class="data-table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th class="text-center" style="width: 60px;">
                  {{ __('messages.owner.stock_report.table.number') }}
                </th>
                <th>{{ __('messages.owner.stock_report.table.stock_name_code') }}</th>
                <th>{{ __('messages.owner.stock_report.table.location') }}</th>
                <th class="text-end">
                  <span class="text-success">{{ __('messages.owner.stock_report.table.total_in') }}</span>
                </th>
                <th class="text-end">
                  <span class="text-danger">{{ __('messages.owner.stock_report.table.total_out') }}</span>
                </th>
                <th class="text-center" style="width: 120px;">
                  {{ __('messages.owner.stock_report.table.action') }}
                </th>
              </tr>
            </thead>
            <tbody id="stockTableBody">
              @forelse ($stocks as $index => $stock)
                <tr class="table-row stock-row"
                    data-stock-name="{{ strtolower($stock->stock_name) }}"
                    data-stock-code="{{ strtolower($stock->stock_code) }}"
                    data-location="{{ strtolower($stock->partner->name ?? __('messages.owner.stock_report.filter.owner_warehouse')) }}">
                  
                  <!-- Number -->
                  <td class="text-center text-muted">
                    {{ $stocks->firstItem() + $index }}
                  </td>

                  <!-- Stock Name & Code -->
                  <td>
                    <div class="fw-600">{{ $stock->stock_name }}</div>
                    <div class="text-muted small mono">{{ $stock->stock_code }}</div>
                  </td>

                  <!-- Location -->
                  <td>
                    <span class="badge-modern badge-secondary">
                      {{ $stock->partner->name ?? __('messages.owner.stock_report.filter.owner_warehouse') }}
                    </span>
                  </td>

                  <!-- Total In -->
                  <td class="text-end">
                    <span class="fw-600 text-success">{{ number_format($stock->lifetime_in, 2) }}</span>
                    <span class="text-muted small">{{ $stock->displayUnit->unit_name ?? 'N/A' }}</span>
                  </td>

                  <!-- Total Out -->
                  <td class="text-end">
                    <span class="fw-600 text-danger">{{ number_format($stock->lifetime_out, 2) }}</span>
                    <span class="text-muted small">{{ $stock->displayUnit->unit_name ?? 'N/A' }}</span>
                  </td>

                  <!-- Action -->
                  <td class="text-center">
                    <div class="table-actions">
                      <a href="{{ 
                          route('owner.user-owner.report.stocks.movement', [
                              'stock' => $stock->stock_code, 
                              'partner_id' => request('partner_id'),
                              'month' => request('month'),
                              'stock_type' => request('stock_type'),
                          ]) 
                        }}" 
                        class="btn-table-action view"
                        title="{{ __('messages.owner.stock_report.table.detail_button') }}">
                        <span class="material-symbols-outlined">visibility</span>
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr id="emptyRow">
                  <td colspan="6" class="text-center">
                    <div class="table-empty-state">
                      <span class="material-symbols-outlined">inventory_2</span>
                      <h4>{{ __('messages.owner.stock_report.table.no_data') }}</h4>
                      <p>Try adjusting your filters to see results</p>
                    </div>
                  </td>
                </tr>
              @endforelse
              
              <!-- No Search Result Row -->
              <tr id="noResultRow" style="display: none;">
                <td colspan="6" class="text-center">
                  <div class="table-empty-state">
                    <span class="material-symbols-outlined">search_off</span>
                    <h4>{{ __('messages.owner.stock_report.table.no_result') }}</h4>
                    <p>No results found for "<span id="searchKeyword"></span>"</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        @if($stocks->hasPages())
          <div class="table-pagination" id="paginationSection">
            <div class="text-muted small" id="paginationInfo">
              <span id="showingInfo">
                Showing {{ $stocks->firstItem() ?? 0 }} to {{ $stocks->lastItem() ?? 0 }}
                of {{ $stocks->total() }} entries
              </span>
              <span id="searchResultInfo" style="display: none;">
                Found <span id="resultCount">0</span> results
              </span>
            </div>
            <div id="paginationLinks">
              {{ $stocks->links() }}
            </div>
          </div>
        @endif
      </div>

    </div>
  </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;
    const $searchInput = $('#searchInput');
    const $stockRows = $('.stock-row');
    const $noResultRow = $('#noResultRow');
    const $emptyRow = $('#emptyRow');
    const $showingInfo = $('#showingInfo');
    const $searchResultInfo = $('#searchResultInfo');
    const $paginationLinks = $('#paginationLinks');

    function performSearch() {
        const searchTerm = $searchInput.val().toLowerCase().trim();
        let visibleCount = 0;

        $stockRows.each(function() {
            const $row = $(this);
            const stockName = $row.data('stock-name');
            const stockCode = $row.data('stock-code');
            const location = $row.data('location');
            
            if (searchTerm === '' || 
                stockName.includes(searchTerm) || 
                stockCode.includes(searchTerm) || 
                location.includes(searchTerm)) {
                $row.show();
                visibleCount++;
                // Update row number
                $row.find('td:first-child').text(visibleCount);
            } else {
                $row.hide();
            }
        });

        // Handle display states
        if (searchTerm === '') {
            $noResultRow.hide();
            if ($emptyRow.length) $emptyRow.toggle($stockRows.length === 0);
            $showingInfo.show();
            $searchResultInfo.hide();
            $paginationLinks.show();
        } else {
            if (visibleCount > 0) {
                $noResultRow.hide();
                if ($emptyRow.length) $emptyRow.hide();
                $('#resultCount').text(visibleCount);
                $showingInfo.hide();
                $searchResultInfo.show();
            } else {
                $noResultRow.show();
                if ($emptyRow.length) $emptyRow.hide();
                $('#searchKeyword').text($searchInput.val());
                $showingInfo.hide();
                $searchResultInfo.hide();
            }
            $paginationLinks.hide();
        }
    }

    // Search input with debounce
    $searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 300);
    });

    // Prevent form submission on Enter
    $searchInput.on('keypress', function(e) {
        if (e.which === 13) e.preventDefault();
    });
});
</script>
@endpush