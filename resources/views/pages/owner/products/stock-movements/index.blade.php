@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.movements_index.title'))
@section('page_title', __('messages.owner.products.stocks.movements_index.page_title'))

@section('content')
  <section class="content">
    <div class="container-fluid owner-stock-movements">
      <a href="{{ route('owner.user-owner.stocks.index') }}" class="btn btn-primary mb-3">
        <i class="fas fa-arrow-left mr-2"></i>
        {{ __('messages.owner.products.stocks.movements_index.back_to_list') }}
      </a>

      {{-- FILTER --}}
      <div class="card mb-4">
        <div class="card-body">
          <form action="{{ route('owner.user-owner.stocks.movements.index')}}" method="GET">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="filter_location">
                    {{ __('messages.owner.products.stocks.movements_index.filter_location_label') }}
                  </label>
                  <select class="form-control" id="filter_location" name="filter_location">
                    <option value="owner" {{ request('filter_location') == 'owner' ? 'selected' : '' }}>
                      {{ __('messages.owner.products.stocks.movements_index.filter_location_owner') }}
                    </option>
                    @foreach ($partners as $partner)
                      <option value="{{ $partner->id }}" {{ request('filter_location') == $partner->id ? 'selected' : '' }}>
                        {{ $partner->name }} ({{ __('messages.owner.products.stocks.movements_index.filter_location_outlet_suffix') }})
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="filter_type">
                    {{ __('messages.owner.products.stocks.movements_index.filter_type_label') }}
                  </label>
                  <select class="form-control" id="filter_type" name="filter_type">
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
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="filter_date">
                    {{ __('messages.owner.products.stocks.movements_index.filter_date_label') }}
                  </label>
                  <input type="date" class="form-control" id="filter_date" name="filter_date"
                    value="{{ request('filter_date') }}">
                </div>
              </div>

              <div class="col-md-2 d-flex align-items-end">
                <div class="form-group w-100">
                  <button type="submit" class="btn btn-primary w-100">
                    {{ __('messages.owner.products.stocks.movements_index.filter_button') }}
                  </button>
                  <a href="{{ route('owner.user-owner.stocks.movements.index')}}" class="btn btn-secondary w-100 mt-2">
                    {{ __('messages.owner.products.stocks.movements_index.reset_button') }}
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      {{-- /FILTER --}}

      {{-- TABEL DATA --}}
      <div class="card">
        <div class="card-body px-0 py-0">
          <div class="table-responsive owner-stock-movements-table">
            <table class="table table-hover align-middle bg-white">
              <thead>
                <tr>
                  <th>#</th>
                  <th>{{ __('messages.owner.products.stocks.movements_index.table_header_date') }}</th>
                  <th>{{ __('messages.owner.products.stocks.movements_index.table_header_type') }}</th>
                  <th>{{ __('messages.owner.products.stocks.movements_index.table_header_category') }}</th>
                  <th>{{ __('messages.owner.products.stocks.movements_index.table_header_location') }}</th>
                  <th>{{ __('messages.owner.products.stocks.movements_index.table_header_notes') }}</th>
                  <th>{{ __('messages.owner.products.stocks.movements_index.table_header_total_items') }}</th>
                  <th class="text-nowrap">{{ __('messages.owner.products.stocks.movements_index.table_header_actions') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($movements as $index => $movement)
                  <tr>
                    <td class="text-muted">{{ $movements->firstItem() + $index }}</td>
                    <td>
                      <div class="fw-600">{{ $movement->created_at->format('d M Y') }}</div>
                      <div class="text-muted small">{{ $movement->created_at->format('H:i') }}</div>
                    </td>
                    <td>
                      @if ($movement->type == 'in')
                        <span class="badge badge-success">
                          {{ __('messages.owner.products.stocks.movements_index.type_in') }}
                        </span>
                      @elseif ($movement->type == 'out')
                        <span class="badge badge-danger">
                          {{ __('messages.owner.products.stocks.movements_index.type_out') }}
                        </span>
                      @else
                        <span class="badge badge-warning">
                          {{ __('messages.owner.products.stocks.movements_index.type_adjustment') }}
                        </span>
                      @endif
                    </td>
                    <td>
                      <span>
                        {{ Str::title(str_replace('_', ' ', $movement->category)) }}
                      </span>
                    </td>
                    <td class="fw-600">
                      {{ $movement->partner->name ?? __('messages.owner.products.stocks.movements_index.filter_location_owner') }}
                    </td>
                    <td class="text-truncate" style="max-width: 200px;" title="{{ $movement->notes }}">
                      {{ $movement->notes ?? '-' }}
                    </td>
                    <td>
                      {{ $movement->items_count }}
                      {{ __('messages.owner.products.stocks.movements_index.items_count_suffix') }}
                    </td>
                    <td class="text-nowrap">
                      <button type="button" class="btn btn-sm btn-primary btn-show-detail roundedlg"
                        data-toggle="modal" data-target="#detailMovementModal"
                        data-url="{{ route('owner.user-owner.stocks.movements.items.json', $movement->id) }}">
                        <i class="fas fa-eye"></i>
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                      <p class="fw-bold mb-0">
                        {{ __('messages.owner.products.stocks.movements_index.no_movements_message') }}
                      </p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- PAGINATION --}}
      <div class="d-flex justify-content-center mt-4">
        {{ $movements->links() }}
      </div>

    </div>
  </section>

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
