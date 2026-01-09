@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.movements_index.title'))
@section('page_title', __('messages.owner.products.stocks.movements_index.page_title'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.stocks.movements_index.page_title') }}</h1>
          <p class="page-subtitle">Track and monitor all stock movements</p>
        </div>
      </div>

      <!-- Filters Card -->
      <div class="modern-card mb-4">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <div class="row">
            <!-- Filter Location -->
            <div class="col-md-4">
              <div class="form-group">
                <label for="filter_location" class="form-label-modern">
                  {{ __('messages.owner.products.stocks.movements_index.filter_location_label') }}
                </label>
                <div class="select-wrapper">
                  <select class="form-control-modern" id="filter_location" name="filter_location">
                    <option value="owner" {{ request('filter_location') == 'owner' ? 'selected' : '' }}>
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

            <!-- Filter Type -->
            <div class="col-md-4">
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

            <!-- Filter Date -->
            <div class="col-md-4">
              <div class="form-group">
                <label for="filter_date" class="form-label-modern">
                  {{ __('messages.owner.products.stocks.movements_index.filter_date_label') }}
                </label>
                <input type="date" class="form-control-modern" id="filter_date" name="filter_date"
                  value="{{ request('filter_date') }}">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Table Display -->
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
                <tr class="table-row" data-location="{{ $movement->partner_id ?? 'owner' }}"
                  data-type="{{ $movement->type }}" data-date="{{ $movement->created_at->format('Y-m-d') }}"
                  data-search="{{ strtolower($movement->created_at->format('d M Y') . ' ' . $movement->type . ' ' . str_replace('_', ' ', $movement->category) . ' ' . ($movement->partner->name ?? __('messages.owner.products.stocks.movements_index.filter_location_owner')) . ' ' . ($movement->notes ?? '')) }}">
                  <!-- Number -->
                  <td class="text-center text-muted">
                    {{ $movements->firstItem() + $index }}
                  </td>

                  <!-- Date & Time -->
                  <td>
                    <div class="fw-600">{{ $movement->created_at->format('d M Y') }}</div>
                    <div class="text-muted small">{{ $movement->created_at->format('H:i') }}</div>
                  </td>

                  <!-- Type Badge -->
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

                  <!-- Category -->
                  <td>
                    <span>{{ Str::title(str_replace('_', ' ', $movement->category)) }}</span>
                  </td>

                  <!-- Location -->
                  <td class="fw-600">
                    {{ $movement->partner->name ?? __('messages.owner.products.stocks.movements_index.filter_location_owner') }}
                  </td>

                  <!-- Notes -->
                  <td class="text-truncate" style="max-width: 200px;" title="{{ $movement->notes }}">
                    {{ $movement->notes ?? '-' }}
                  </td>

                  <!-- Total Items -->
                  <td class="text-center">
                    <span class="badge-modern badge-info">
                      {{ $movement->items_count }}
                      {{ __('messages.owner.products.stocks.movements_index.items_count_suffix') }}
                    </span>
                  </td>

                  <!-- Actions -->
                  <td class="text-center">
                    <div class="table-actions">
                      <button type="button" class="btn-table-action view btn-show-detail" data-toggle="modal"
                        data-target="#detailMovementModal"
                        data-url="{{ route('owner.user-owner.stocks.movements.items.json', $movement->id) }}"
                        title="View Details">
                        <span class="material-symbols-outlined">visibility</span>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center">
                    <div class="table-empty-state">
                      <span class="material-symbols-outlined">inventory_2</span>
                      <h4>{{ __('messages.owner.products.stocks.movements_index.no_movements_message') }}</h4>
                      <p>No stock movements found</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
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
      // REALTIME FILTER FUNCTION
      // ==========================================
      const filterLocation = $('#filter_location');
      const filterType = $('#filter_type');
      const filterDate = $('#filter_date');
      const tableBody = $('#movementTableBody');
      const rows = tableBody.find('tr.table-row');

      function applyFilters() {
        const selectedLocation = filterLocation.val();
        const selectedType = filterType.val();
        const selectedDate = filterDate.val();

        let visibleCount = 0;

        rows.each(function () {
          const row = $(this);
          const rowLocation = row.data('location');
          const rowType = row.data('type');
          const rowDate = row.data('date');

          // Check all filter conditions
          const matchesLocation = !selectedLocation || selectedLocation === '' ||
            rowLocation == selectedLocation;
          const matchesType = !selectedType || selectedType === '' ||
            rowType === selectedType;
          const matchesDate = !selectedDate || selectedDate === '' ||
            rowDate === selectedDate;

          // Show/hide row based on all conditions
          if (matchesLocation && matchesType && matchesDate) {
            row.show();
            visibleCount++;
            // Update row number
            row.find('td:first-child').text(visibleCount);
          } else {
            row.hide();
          }
        });

        // Handle empty state
        handleEmptyState(visibleCount);
      }

      // ==========================================
      // EMPTY STATE HANDLER
      // ==========================================
      function handleEmptyState(visibleCount) {
        const existingEmptyRow = tableBody.find('.empty-filter-row');
        existingEmptyRow.remove();

        if (visibleCount === 0 && rows.length > 0) {
          const emptyRow = `
              <tr class="empty-filter-row">
                <td colspan="8" class="text-center">
                  <div class="table-empty-state">
                    <span class="material-symbols-outlined">search_off</span>
                    <h4>No results found</h4>
                    <p>Try adjusting your filters</p>
                  </div>
                </td>
              </tr>
            `;
          tableBody.append(emptyRow);
        }
      }

      // ==========================================
      // EVENT LISTENERS - Realtime Filter
      // ==========================================
      filterLocation.on('change', applyFilters);
      filterType.on('change', applyFilters);
      filterDate.on('change', applyFilters);

      // Apply initial filters if any
      if (filterLocation.val() || filterType.val() || filterDate.val()) {
        applyFilters();
      }

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