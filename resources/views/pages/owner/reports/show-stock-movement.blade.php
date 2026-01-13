@extends('layouts.owner')

@section('title', __('messages.owner.stock_report.movement.title'))
@section('page_title', __('messages.owner.stock_report.movement.page_title'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.stock_report.movement.page_title') }}</h1>
          <p class="page-subtitle">{{ __('messages.owner.stock_report.movement.subtitle') }}</p>
        </div>
      </div>

      <!-- Stock Info Card -->
      <div class="modern-card">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <div class="row">
            <div class="col-md-3">
              <div class="info-group">
                <label class="info-label">
                  {{ __('messages.owner.stock_report.movement.info.stock_name') }}
                </label>
                <p class="info-value">{{ $stockItem->stock_name }}</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="info-group">
                <label class="info-label">
                  {{ __('messages.owner.stock_report.movement.info.stock_code') }}
                </label>
                <p class="info-value mono">{{ $stockItem->stock_code }}</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="info-group">
                <label class="info-label">
                  {{ __('messages.owner.stock_report.movement.info.location') }}
                </label>
                <p class="info-value">
                  @if(request('partner_id') === 'owner' || request('partner_id') === null)
                    {{ __('messages.owner.stock_report.filter.owner_warehouse') }}
                  @else
                    {{ $stockItem->partner->name ?? 'N/A' }}
                  @endif
                </p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="info-group">
                <label class="info-label">
                  {{ __('messages.owner.stock_report.movement.info.period') }}
                </label>
                <p class="info-value">
                  {{ request('month') ? Carbon\Carbon::parse(request('month'))->translatedFormat('F Y') : __('messages.owner.stock_report.movement.info.all_time') }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Export Button Card -->
      <div class="modern-card" style="background: transparent; box-shadow: none;">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <div class="d-flex justify-content-end">
            <a href="{{ route('owner.user-owner.report.stocks.movement.export', array_merge(['stock' => $stockItem->stock_code], request()->all())) }}"
               class="btn-modern btn-sm-modern btn-success-modern">
              <span class="material-symbols-outlined">download</span>
              {{ __('messages.owner.stock_report.movement.table.export_excel') }}
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
                  {{ __('messages.owner.stock_report.movement.table.number') }}
                </th>
                <th>{{ __('messages.owner.stock_report.movement.table.date_time') }}</th>
                <th>{{ __('messages.owner.stock_report.movement.table.category') }}</th>
                <th>{{ __('messages.owner.stock_report.movement.table.location') }}</th>
                <th class="text-center">{{ __('messages.owner.stock_report.movement.table.quantity') }}</th>
                <th>{{ __('messages.owner.stock_report.movement.table.notes') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($movements as $index => $item)
                @php
                  // Ambil kuantitas mentah (selalu positif)
                  $rawQty = $item->quantity;

                  // Tentukan arah berdasarkan tipe di Model StockMovement
                  $isOut = optional($item->movement)->type === 'out';
                  $displayQty = abs($rawQty);

                  // Konversi ke display unit
                  $conversion = optional($item->stock->displayUnit)->base_unit_conversion_value ?? 1;
                  $displayQty = $displayQty / $conversion;

                  $qtyClass = $isOut ? 'text-danger' : 'text-success';
                  $qtySign = $isOut ? '-' : '+';
                @endphp
                <tr class="table-row">
                  <!-- Number -->
                  <td class="text-center text-muted">
                    {{ $movements->firstItem() + $index }}
                  </td>

                  <!-- Date & Time -->
                  <td>
                    <div class="fw-600">
                      {{ optional($item->movement->created_at)->format('d M Y') }}
                    </div>
                    <div class="text-muted small">
                      {{ optional($item->movement->created_at)->format('H:i:s') }}
                    </div>
                  </td>

                  <!-- Category Badge -->
                  <td>
                    <span class="badge-modern {{ $isOut ? 'badge-danger' : 'badge-success' }}">
                      {{ Str::title(str_replace('_', ' ', $item->movement->category)) }}
                    </span>
                  </td>

                  <!-- Location -->
                  <td>
                    <span class="fw-600">
                      {{ optional($item->movement->partner)->name ?? __('messages.owner.stock_report.filter.owner_warehouse') }}
                    </span>
                  </td>

                  <!-- Quantity -->
                  <td class="text-center">
                    <span class="fw-600 {{ $qtyClass }}">
                      {{ $qtySign }}{{ number_format($displayQty, 2) }}
                    </span>
                    <span class="text-muted small">
                      {{ optional($item->stock->displayUnit)->unit_name ?? 'N/A' }}
                    </span>
                  </td>

                  <!-- Notes -->
                  <td>
                    <span class="text-truncate d-inline-block" style="max-width: 250px;" title="{{ $item->movement->notes }}">
                      {{ $item->movement->notes ?? '-' }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">
                    <div class="table-empty-state">
                      <span class="material-symbols-outlined">inventory_2</span>
                      <h4>{{ __('messages.owner.stock_report.movement.table.no_data') }}</h4>
                      <p>No movement records found for this stock item</p>
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
            <div>
              {{ $movements->links() }}
            </div>
          </div>
        @endif
      </div>

    </div>
  </div>
@endsection

@push('styles')
<style>
  .info-group {
    margin-bottom: 0;
  }
  
  .info-label {
    font-size: 0.875rem;
    color: var(--text-muted, #6c757d);
    margin-bottom: 0.25rem;
    display: block;
    font-weight: 500;
  }
  
  .info-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary, #212529);
    margin: 0;
  }

  @media (max-width: 768px) {
    .info-group {
      margin-bottom: 1rem;
    }
  }
</style>
@endpush