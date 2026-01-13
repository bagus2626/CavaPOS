@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.movements_index.title'))
@section('page_title', __('messages.owner.products.stocks.movements_index.page_title'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.stocks.movements_index.page_title') }}</h1>
          <p class="page-subtitle">{{ __('messages.owner.products.stocks.movements_index.subtitle') }}</p>
        </div>
        <a href="{{ route('owner.user-owner.stocks.index') }}" class="back-button">
            <span class="material-symbols-outlined">arrow_back</span>
            {{ __('messages.owner.products.stocks.back') }}
        </a>
      </div>

      <div class="modern-card mb-4">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <form method="GET" action="{{ route('owner.user-owner.stocks.movements.index') }}">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="filter_location" class="form-label-modern">
                    {{ __('messages.owner.products.stocks.movements_index.filter_location_label') }}
                  </label>
                  <div class="select-wrapper">
                    <select class="form-control-modern" id="filter_location" name="filter_location">
                      <option value="owner" {{ request('filter_location', 'owner') == 'owner' ? 'selected' : '' }}>
                        {{ __('messages.owner.products.stocks.movements_index.filter_location_owner') }}
                      </option>
                      @foreach ($partners as $partner)
                        <option value="{{ $partner->id }}" {{ request('filter_location') == $partner->id ? 'selected' : '' }}>
                          {{ $partner->name }}
                          ({{ __('messages.owner.products.stocks.movements_index.filter_location_outlet_suffix') }})
                        </option>
                      @endforeach
                    </select>
                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="filter_type" class="form-label-modern">
                    {{ __('messages.owner.products.stocks.movements_index.filter_type_label') }}
                  </label>
                  <div class="select-wrapper">
                    <select class="form-control-modern" id="filter_type" name="filter_type">
                      <option value="">
                        {{ __('messages.owner.products.stocks.movements_index.filter_type_all') }}
                      </option>
                      <option value="in" {{ request('filter_type') == 'in' ? 'selected' : '' }}>
                        {{ __('messages.owner.products.stocks.movements_index.filter_type_in') }}
                      </option>
                      <option value="out" {{ request('filter_type') == 'out' ? 'selected' : '' }}>
                        {{ __('messages.owner.products.stocks.movements_index.filter_type_out') }}
                      </option>
                    </select>
                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="filter_date" class="form-label-modern">
                    {{ __('messages.owner.products.stocks.movements_index.filter_date_label') }}
                  </label>
                  <input type="date" 
                         class="form-control-modern" 
                         id="filter_date" 
                         name="filter_date"
                         value="{{ request('filter_date', date('Y-m-d')) }}"
                         max="{{ date('Y-m-d') }}">
                </div>
              </div>

              <div class="col-md-3 d-flex align-items-end">
                <div class="form-group">
                  <button type="submit" class="btn-modern btn-primary-modern">
                    {{ __('messages.owner.products.stocks.movements_index.filter_button') }}
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
                <th>{{ __('messages.owner.products.stocks.movements_index.table_header_date') }}</th>
                <th>{{ __('messages.owner.products.stocks.movements_index.table_header_type') }}</th>
                <th>{{ __('messages.owner.products.stocks.movements_index.table_header_category') }}</th>
                <th>{{ __('messages.owner.products.stocks.movements_index.table_header_location') }}</th>
                <th>{{ __('messages.owner.products.stocks.movements_index.table_header_notes') }}</th>
                <th class="text-center">
                  {{ __('messages.owner.products.stocks.movements_index.table_header_total_items') }}</th>
                <th class="text-center" style="width: 120px;">
                  {{ __('messages.owner.products.stocks.movements_index.table_header_actions') }}
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
                        {{ __('messages.owner.products.stocks.movements_index.type_in') }}
                      </span>
                    @elseif ($movement->type == 'out')
                      <span class="badge-modern badge-danger">
                        {{ __('messages.owner.products.stocks.movements_index.type_out') }}
                      </span>
                    @else
                      <span class="badge-modern badge-warning">
                        {{ __('messages.owner.products.stocks.movements_index.type_adjustment') }}
                      </span>
                    @endif
                  </td>

                  <td>
                    <span>{{ Str::title(str_replace('_', ' ', $movement->category)) }}</span>
                  </td>

                  <td class="fw-600">
                    {{ $movement->partner->name ?? __('messages.owner.products.stocks.movements_index.filter_location_owner') }}
                  </td>

                  <td class="text-truncate" style="max-width: 200px;" title="{{ $movement->notes }}">
                    {{ $movement->notes ?? '-' }}
                  </td>

                  <td class="text-center">
                    <span class="badge-modern badge-info">
                      {{ $movement->items_count }}
                      {{ __('messages.owner.products.stocks.movements_index.items_count_suffix') }}
                    </span>
                  </td>

                  <td class="text-center">
                    <div class="table-actions">
                      <button type="button" class="btn-table-action view btn-show-detail" data-toggle="modal"
                        data-target="#detailMovementModal"
                        data-url="{{ route('owner.user-owner.stocks.movements.items.json', $movement->id) }}"
                        title="{{ __('messages.owner.products.stocks.movements_index.view_details_tooltip') }}">
                        <span class="material-symbols-outlined">visibility</span>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr id="emptyRow">
                  <td colspan="8" class="text-center">
                    <div class="table-empty-state">
                      <span class="material-symbols-outlined">inventory_2</span>
                      <h4>{{ __('messages.owner.products.stocks.movements_index.no_movements_message') }}</h4>
                      <p>{{ __('messages.owner.products.stocks.adjust_search_filter') }}</p>
                    </div>
                  </td>
                </tr>
              @endforelse

              <tr id="noResultRow" style="display: none;">
                <td colspan="8" class="text-center">
                  <div class="table-empty-state">
                    <span class="material-symbols-outlined">search_off</span>
                    <h4>{{ __('messages.owner.products.stocks.movements_index.no_result') }}</h4>
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
            {{ __('messages.owner.products.stocks.movements_index.modal_title') }}
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          {{-- Info Header --}}
          <div class="mb-3">
            <h6 class="text-muted mb-1">
              {{ __('messages.owner.products.stocks.movements_index.modal_notes_label') }}
            </h6>
            <p id="modal_notes" class="fw-600">-</p>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <h6 class="text-muted mb-1">
                {{ __('messages.owner.products.stocks.movements_index.modal_category_label') }}
              </h6>
              <p id="modal_category" class="fw-600">-</p>
            </div>
            <div class="col-md-6">
              <h6 class="text-muted mb-1">
                {{ __('messages.owner.products.stocks.movements_index.modal_location_label') }}
              </h6>
              <p id="modal_location" class="fw-600">-</p>
            </div>
          </div>

          {{-- Tabel Item --}}
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead>
                <tr>
                  <th>{{ __('messages.owner.products.stocks.movements_index.modal_table_header_name') }}</th>
                  <th class="text-right">
                    {{ __('messages.owner.products.stocks.movements_index.modal_table_header_quantity') }}
                  </th>
                  <th>{{ __('messages.owner.products.stocks.movements_index.modal_table_header_unit') }}</th>
                  <th class="text-right">
                    {{ __('messages.owner.products.stocks.movements_index.modal_table_header_price') }}
                  </th>
                </tr>
              </thead>
              <tbody id="movementDetailTableBody">
                <tr>
                  <td colspan="4" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm" role="status">
                      <span class="sr-only">
                        {{ __('messages.owner.products.stocks.movements_index.modal_loading_aria') }}
                      </span>
                    </div>
                    <span class="ml-2">
                      {{ __('messages.owner.products.stocks.movements_index.modal_loading_text') }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            {{ __('messages.owner.products.stocks.movements_index.modal_close_button') }}
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

        // Reset modal ke status loading (using Blade keys in JS)
        tableBody.html(`
            <tr>
              <td colspan="4" class="text-center py-4">
                <div class="spinner-border spinner-border-sm" role="status">
                  <span class="sr-only">
                    {{ __('messages.owner.products.stocks.movements_index.modal_loading_aria') }}
                  </span>
                </div>
                <span class="ml-2">
                  {{ __('messages.owner.products.stocks.movements_index.modal_loading_text') }}
                </span>
              </td>
            </tr>
          `);
        modal.find('#modal_notes').text('-');
        modal.find('#modal_category').text('-');
        modal.find('#modal_location').text('-');

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
            
            // Using Blade Key for Owner Warehouse fallback in JS
            modal.find('#modal_location').text(
              response.movement.partner
                ? response.movement.partner.name
                : '{{ __('messages.owner.products.stocks.movements_index.filter_location_owner') }}'
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
                      {{ __('messages.owner.products.stocks.movements_index.modal_table_empty') }}
                    </td>
                  </tr>
                `);
            }
          },
          error: function (xhr) {
            tableBody.html(`
                <tr>
                  <td colspan="4" class="text-center py-4 text-danger">
                    {{ __('messages.owner.products.stocks.movements_index.ajax_error_message') }}
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