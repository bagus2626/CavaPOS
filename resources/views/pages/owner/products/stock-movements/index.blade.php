@extends('layouts.owner')

@section('title', 'Stock Movement')
@section('page_title', 'Riwayat Pergerakan Stok')

@section('content')
  <section class="content">
    <div class="container-fluid owner-stock-movements">

      {{-- FILTER --}}
      <div class="card mb-4">
        <div class="card-body">
          <form action="{{ route('owner.user-owner.stock-movements.index')}}" method="GET">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="filter_location">Lokasi (Outlet/Gudang)</label>
                  <select class="form-control" id="filter_location" name="filter_location">
                    <option value="owner" {{ request('filter_location') == 'owner' ? 'selected' : '' }}>
                      Gudang Owner
                    </option>
                    @foreach ($partners as $partner)
                      <option value="{{ $partner->id }}" {{ request('filter_location') == $partner->id ? 'selected' : '' }}>
                        {{ $partner->name }} (Outlet)
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="filter_type">Tipe Pergerakan</label>
                  <select class="form-control" id="filter_type" name="filter_type">
                    <option value="">Semua Tipe</option>
                    <option value="in" {{ request('filter_type') == 'in' ? 'selected' : '' }}>
                      Stok Masuk (In)
                    </option>
                    <option value="out" {{ request('filter_type') == 'out' ? 'selected' : '' }}>
                      Stok Keluar (Out)
                    </option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="filter_date">Tanggal</label>
                  <input type="date" class="form-control" id="filter_date" name="filter_date"
                    value="{{ request('filter_date') }}">
                </div>
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <div class="form-group w-100">
                  <button type="submit" class="btn btn-primary w-100">
                    Filter
                  </button>
                  <a href="{{ route('owner.user-owner.stock-movements.index')}}" class="btn btn-secondary w-100 mt-2">
                    Reset
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      {{-- E FORM FILTER --}}


      {{-- TABEL DATA --}}
      <div class="card">
        <div class="card-body px-0 py-0">
          <div class="table-responsive owner-stock-movements-table">
            <table class="table table-hover align-middle bg-white">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Tanggal</th>
                  <th>Tipe</th>
                  <th>Kategori</th>
                  <th>Outlet/Gudang</th>
                  <th>Catatan</th>
                  <th>Total Item</th>
                  <th class="text-nowrap">Aksi</th>
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
                        <span class="badge badge-success">Masuk</span>
                      @elseif ($movement->type == 'out')
                        <span class="badge badge-danger">Keluar</span>
                      @else
                        <span class="badge badge-warning">Penyesuaian</span>
                      @endif
                    </td>
                    <td>
                      <span class="">
                        {{ Str::title(str_replace('_', ' ', $movement->category)) }}
                      </span>
                    </td>
                    <td class="fw-600">
                      {{ $movement->partner->name ?? 'Gudang Owner' }}
                    </td>
                    <td class="text-truncate" style="max-width: 200px;" title="{{ $movement->notes }}">
                      {{ $movement->notes ?? '-' }}
                    </td>
                    <td>
                      {{ $movement->items_count }} Item
                    </td>
                    <td class="text-nowrap">
                      <button type="button" class="btn btn-sm btn-primary btn-show-detail roundedlg" style=""
                        data-toggle="modal" data-target="#detailMovementModal"
                        data-url="{{ route('owner.user-owner.stock-movements.items.json', $movement->id) }}">
                        <i class="fas fa-eye"></i>
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                      <p class="fw-bold mb-0">Belum ada riwayat pergerakan stok.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- PAGINAtion --}}
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
          <h5 class="modal-title" id="detailMovementModalLabel">Detail Pergerakan Stok</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          {{-- Info Header --}}
          <div class="mb-3">
            <h6 class="text-muted mb-1">Catatan Transaksi:</h6>
            <p id="modal_notes" class="fw-600">-</p>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <h6 class="text-muted mb-1">Kategori:</h6>
              <p id="modal_category" class="fw-600">-</p>
            </div>
            <div class="col-md-6">
              <h6 class="text-muted mb-1">Lokasi:</h6>
              <p id="modal_location" class="fw-600">-</p>
            </div>
          </div>

          {{-- Tabel Item --}}
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead>
                <tr>
                  <th>Nama Barang</th>
                  <th class="text-right">Kuantitas</th>
                  <th>Unit</th>
                  <th class="text-right">Harga Beli</th>
                </tr>
              </thead>
              <tbody id="movementDetailTableBody">
                <tr>
                  <td colspan="4" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                    <span class="ml-2">Memuat data item...</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
                      <span class="sr-only">Loading...</span>
                    </div>
                    <span class="ml-2">Memuat data item...</span>
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
            // Update info header modal
            modal.find('#modal_notes').text(response.movement.notes || '-');
            modal.find('#modal_category').text(
              response.movement.category.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
            );
            modal.find('#modal_location').text(response.movement.partner ? response.movement.partner.name : 'Gudang Owner');

            // Ambil tipe pergerakan dari response (in / out / transfer)
            var movementType = response.movement.type;

            tableBody.empty();

            if (response.items && response.items.length > 0) {
              $.each(response.items, function (index, item) {

                var qtyClass = '';
                var qtySign = '';

                // Cek tipe pergerakan dari parent (movement) atau item itu sendiri
                // Jika movement 'out', atau item spesifik ini tipenya 'out'
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
                          Tidak ada item dalam transaksi ini.
                        </td>
                      </tr>
                    `);
            }
          },
          error: function (xhr) {
            tableBody.html(`
                    <tr>
                      <td colspan="4" class="text-center py-4 text-danger">
                        Gagal memuat data. Silakan coba lagi.
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