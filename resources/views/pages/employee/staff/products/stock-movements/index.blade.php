@extends('layouts.staff')

@section('title', __('messages.owner.products.stocks.movements_index.title') ?? 'Movement History')
@section('page_title', __('messages.owner.products.stocks.movements_index.page_title') ?? 'Stock Movements')

@php
    // Dapatkan role employee (manager atau supervisor)
    $staffRoutePrefix = strtolower(auth('employee')->user()->role);
@endphp

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.stocks.movements_index.page_title') ?? 'Movement History' }}</h1>
          <p class="page-subtitle">{{ __('messages.owner.products.stocks.movements_index.subtitle') ?? 'Track your inventory in and out records' }}</p>
        </div>
        <a href="{{ route('employee.' . $staffRoutePrefix . '.stocks.index') }}" class="back-button">
            <span class="material-symbols-outlined">arrow_back</span>
            {{ __('messages.owner.products.stocks.back') ?? 'Back to Stock' }}
        </a>
      </div>

      <div class="modern-card mb-4">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <form method="GET" action="{{ route('employee.' . $staffRoutePrefix . '.stocks.movements.index') }}">
            <div class="row">
              
              {{-- FILTER LOCATION DIHAPUS KARENA STAFF HANYA 1 OUTLET --}}

              <div class="col-md-4">
                <div class="form-group">
                  <label for="filter_type" class="form-label-modern">
                    {{ __('messages.owner.products.stocks.movements_index.filter_type_label') ?? 'Movement Type' }}
                  </label>
                  <div class="select-wrapper">
                    <select class="form-control-modern" id="filter_type" name="filter_type">
                      <option value="">
                        {{ __('messages.owner.products.stocks.movements_index.filter_type_all') ?? 'All Types' }}
                      </option>
                      <option value="in" {{ request('filter_type') == 'in' ? 'selected' : '' }}>
                        {{ __('messages.owner.products.stocks.movements_index.filter_type_in') ?? 'Stock In' }}
                      </option>
                      <option value="out" {{ request('filter_type') == 'out' ? 'selected' : '' }}>
                        {{ __('messages.owner.products.stocks.movements_index.filter_type_out') ?? 'Stock Out' }}
                      </option>
                    </select>
                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="filter_date" class="form-label-modern">
                    {{ __('messages.owner.products.stocks.movements_index.filter_date_label') ?? 'Date' }}
                  </label>
                  <input type="date" 
                         class="form-control-modern" 
                         id="filter_date" 
                         name="filter_date"
                         value="{{ request('filter_date', date('Y-m-d')) }}"
                         max="{{ date('Y-m-d') }}">
                </div>
              </div>

              <div class="col-md-4 d-flex align-items-end">
                <div class="form-group w-100">
                  <button type="submit" class="btn-modern btn-primary-modern w-100 justify-content-center">
                    {{ __('messages.owner.products.stocks.movements_index.filter_button') ?? 'Apply Filter' }}
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="modern-card">
        <div class="data-table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th class="text-center" style="width: 60px;">#</th>
                <th>{{ __('messages.owner.products.stocks.movements_index.table_header_date') ?? 'Date & Time' }}</th>
                <th>{{ __('messages.owner.products.stocks.movements_index.table_header_type') ?? 'Type' }}</th>
                <th>{{ __('messages.owner.products.stocks.movements_index.table_header_category') ?? 'Category' }}</th>
                
                {{-- KOLOM LOCATION DIHAPUS --}}

                <th>{{ __('messages.owner.products.stocks.movements_index.table_header_notes') ?? 'Notes' }}</th>
                <th class="text-center">
                  {{ __('messages.owner.products.stocks.movements_index.table_header_total_items') ?? 'Total Items' }}</th>
                <th class="text-center" style="width: 120px;">
                  {{ __('messages.owner.products.stocks.movements_index.table_header_actions') ?? 'Actions' }}
                </th>
              </tr>
            </thead>
            <tbody id="movementTableBody">
              @forelse ($movements as $index => $movement)
                <tr class="table-row">
                  
                  <td class="text-center text-muted">
                    {{ $movements->firstItem() + $index }}
                  </td>

                  <td>
                    <div class="fw-600">{{ $movement->created_at->format('d M Y') }}</div>
                    <div class="text-muted small">{{ $movement->created_at->format('H:i') }}</div>
                  </td>

                  <td>
                    @if ($movement->type == 'in')
                      <span class="badge-modern badge-success">
                        {{ __('messages.owner.products.stocks.movements_index.type_in') ?? 'Stock In' }}
                      </span>
                    @elseif ($movement->type == 'out')
                      <span class="badge-modern badge-danger">
                        {{ __('messages.owner.products.stocks.movements_index.type_out') ?? 'Stock Out' }}
                      </span>
                    @else
                      <span class="badge-modern badge-warning">
                        {{ __('messages.owner.products.stocks.movements_index.type_adjustment') ?? 'Adjustment' }}
                      </span>
                    @endif
                  </td>

                  <td>
                    <span>{{ Str::title(str_replace('_', ' ', $movement->category)) }}</span>
                  </td>

                  {{-- DATA LOCATION DIHAPUS --}}

                  <td class="text-truncate" style="max-width: 200px;" title="{{ $movement->notes }}">
                    {{ $movement->notes ?? '-' }}
                  </td>

                  <td class="text-center">
                    <span class="badge-modern badge-info">
                      {{ $movement->items_count }}
                      {{ __('messages.owner.products.stocks.movements_index.items_count_suffix') ?? 'Items' }}
                    </span>
                  </td>

                  <td class="text-center">
                    <div class="table-actions">
                      <button type="button" class="btn-table-action view btn-show-detail" data-toggle="modal"
                        data-target="#detailMovementModal"
                        data-url="{{ route('employee.' . $staffRoutePrefix . '.stocks.movements.items.json', $movement->id) }}"
                        title="{{ __('messages.owner.products.stocks.movements_index.view_details_tooltip') ?? 'View Details' }}">
                        <span class="material-symbols-outlined">visibility</span>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr id="emptyRow">
                  <td colspan="7" class="text-center">
                    <div class="table-empty-state">
                      <span class="material-symbols-outlined">inventory_2</span>
                      <h4>{{ __('messages.owner.products.stocks.movements_index.no_movements_message') ?? 'No Stock Movements Found' }}</h4>
                      <p>{{ __('messages.owner.products.stocks.adjust_search_filter') ?? 'Try adjusting your filters' }}</p>
                    </div>
                  </td>
                </tr>
              @endforelse

              <tr id="noResultRow" style="display: none;">
                <td colspan="7" class="text-center">
                  <div class="table-empty-state">
                    <span class="material-symbols-outlined">search_off</span>
                    <h4>{{ __('messages.owner.products.stocks.movements_index.no_result') ?? 'No Results' }}</h4>
                    <p>No results found for "<span id="searchKeyword"></span>"</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        @if($movements->hasPages())
          <div class="table-pagination">
            <div class="text-muted small">
              Showing {{ $movements->firstItem() ?? 0 }} to {{ $movements->lastItem() ?? 0 }}
              of {{ $movements->total() }} entries
            </div>
            {{ $movements->links() }}
          </div>
        @endif
      </div>

    </div>
  </div>

  {{-- MODAL DETAIL --}}
  <div class="modal fade" id="detailMovementModal" tabindex="-1" aria-labelledby="detailMovementModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailMovementModalLabel">
            {{ __('messages.owner.products.stocks.movements_index.modal_title') ?? 'Movement Details' }}
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          {{-- Info Header (Tanpa Location) --}}
          <div class="row mb-3">
            <div class="col-md-6">
              <h6 class="text-muted mb-1">
                {{ __('messages.owner.products.stocks.movements_index.modal_category_label') ?? 'Category' }}
              </h6>
              <p id="modal_category" class="fw-600">-</p>
            </div>
            <div class="col-md-6">
              <h6 class="text-muted mb-1">
                {{ __('messages.owner.products.stocks.movements_index.modal_notes_label') ?? 'Notes' }}
              </h6>
              <p id="modal_notes" class="fw-600">-</p>
            </div>
          </div>

          {{-- Tabel Item --}}
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead>
                <tr>
                  <th>{{ __('messages.owner.products.stocks.movements_index.modal_table_header_name') ?? 'Item Name' }}</th>
                  <th class="text-right">
                    {{ __('messages.owner.products.stocks.movements_index.modal_table_header_quantity') ?? 'Qty' }}
                  </th>
                  <th>{{ __('messages.owner.products.stocks.movements_index.modal_table_header_unit') ?? 'Unit' }}</th>
                  <th class="text-right">
                    {{ __('messages.owner.products.stocks.movements_index.modal_table_header_price') ?? 'Price' }}
                  </th>
                </tr>
              </thead>
              <tbody id="movementDetailTableBody">
                <tr>
                  <td colspan="4" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm" role="status">
                      <span class="sr-only">
                        {{ __('messages.owner.products.stocks.movements_index.modal_loading_aria') ?? 'Loading...' }}
                      </span>
                    </div>
                    <span class="ml-2">
                      {{ __('messages.owner.products.stocks.movements_index.modal_loading_text') ?? 'Loading details...' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            {{ __('messages.owner.products.stocks.movements_index.modal_close_button') ?? 'Close' }}
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    $(document).ready(function () {
      
      // ==========================================
      // MODAL DETAIL HANDLER
      // ==========================================
      $(document).on('click', '.btn-show-detail', function () {
        var detailUrl = $(this).data('url');
        var modal = $('#detailMovementModal');
        var tableBody = $('#movementDetailTableBody');

        // Reset modal ke status loading
        tableBody.html(`
            <tr>
              <td colspan="4" class="text-center py-4">
                <div class="spinner-border spinner-border-sm" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <span class="ml-2">
                  {{ __('messages.owner.products.stocks.movements_index.modal_loading_text') ?? 'Loading details...' }}
                </span>
              </td>
            </tr>
          `);
        modal.find('#modal_notes').text('-');
        modal.find('#modal_category').text('-');

        // AJAX call
        $.ajax({
          url: detailUrl,
          type: 'GET',
          dataType: 'json',
          success: function (response) {
            modal.find('#modal_notes').text(response.movement.notes || '-');
            modal.find('#modal_category').text(
              response.movement.category.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
            );
            
            var movementType = response.movement.type;
            tableBody.empty();

            if (response.items && response.items.length > 0) {
              $.each(response.items, function (index, item) {
                var qtyClass = '';
                var qtySign = '';

                if (movementType === 'out' || item.type === 'out') {
                  qtyClass = 'text-danger';
                  qtySign = '-';
                } else {
                  qtyClass = 'text-success';
                  qtySign = '+';
                }

                var row = `
                    <tr>
                      <td>${item.stock_name}</td>
                      <td class="text-right fw-600 ${qtyClass}">
                        ${qtySign}${item.display_quantity}
                      </td>
                      <td>${item.display_unit_name}</td>
                      <td class="text-right">${item.unit_price_formatted}</td>
                    </tr>
                  `;
                tableBody.append(row);
              });
            } else {
              tableBody.html(`
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                      {{ __('messages.owner.products.stocks.movements_index.modal_table_empty') ?? 'No items found.' }}
                    </td>
                  </tr>
                `);
            }
          },
          error: function (xhr) {
            tableBody.html(`
                <tr>
                  <td colspan="4" class="text-center py-4 text-danger">
                    {{ __('messages.owner.products.stocks.movements_index.ajax_error_message') ?? 'Failed to load data.' }}
                    <div class="text-muted small">${xhr.statusText || 'Error'}</div>
                  </td>
                </tr>
              `);
          }
        });
      });
    });
  </script>
@endpush