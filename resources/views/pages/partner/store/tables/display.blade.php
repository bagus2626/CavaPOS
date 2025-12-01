<div class="tables-index__table table-responsive rounded-xl">
  <table class="table table-bordered table-hover align-middle mb-0">
    <thead class="thead-light">
      <tr>
        <th style="width:56px">#</th>
        <th>{{ __('messages.partner.outlet.table_management.tables.table_no') }}</th>
        <th>{{ __('messages.partner.outlet.table_management.tables.class_type') }}</th>
        <th>{{ __('messages.partner.outlet.table_management.tables.description') }}</th>
        <th style="width:120px">{{ __('messages.partner.outlet.table_management.tables.status') }}</th>
        <th style="width:140px">{{ __('messages.partner.outlet.table_management.tables.picture') }}</th>
        <th style="width:140px">
            Barcode
            <a href="{{ route('partner.store.tables.generate-all-barcode') }}" 
              target="_blank"
              class="text-danger ms-1"
              title="Download Barcode (PDF)">
                <i class="fas fa-file-pdf text-danger fs-5"></i>
            </a>
        </th>

        <th style="width:220px">{{ __('messages.partner.outlet.table_management.tables.actions') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($tables as $index => $table)
        @php
          // mapping badge status
          $status = strtoupper((string) $table->status);
          $statusClass = match ($status) {
            'ACTIVE', 'AVAILABLE', 'OPEN' => 'badge-status badge-status--active',
            'MAINTENANCE', 'PENDING'     => 'badge-status badge-status--warn',
            default                      => 'badge-status badge-status--inactive',
          };
        @endphp
        <tr data-category="{{ $table->table_class }}">
          <td>{{ $index + 1 }}</td>
          <td>{{ $table->table_no }}</td>
          <td>{{ $table->table_class }}</td>
          <td>{{ $table->description }}</td>

          <td>
            <span class="{{ $statusClass }}">
              @if ($table->status === 'available')
                {{ __('messages.partner.outlet.table_management.tables.available') }}
              @elseif ($table->status === 'occupied')
                {{ __('messages.partner.outlet.table_management.tables.occupied') }}
              @elseif ($table->status === 'reserved')
                {{ __('messages.partner.outlet.table_management.tables.reserved') }}
              @else
                -
              @endif
            </span>
          </td>

          <td>
            @if(!empty($table->images) && is_array($table->images))
              <div class="thumb-list d-flex flex-wrap">
                @foreach($table->images as $image)
                  @php $src = asset($image['path']); @endphp
                  <a href="{{ $src }}" target="_blank" rel="noopener" class="thumb-item">
                    <img src="{{ $src }}" alt="{{ $image['filename'] ?? 'Table Image' }}" class="thumb-img">
                  </a>
                @endforeach
              </div>
            @else
              <span class="text-muted">{{ __('messages.partner.outlet.table_management.tables.no_images') }}</span>
            @endif
          </td>

          <td>
            <button onclick="generateBarcode({{ $table->id }})" class="btn btn-sm btn-outline-choco">
              <i class="fas fa-qrcode mr-1"></i> {{ __('messages.partner.outlet.table_management.tables.table_barcode') }}
            </button>
          </td>

          <td>
            <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
              <a href="{{ route('partner.store.tables.show', $table->id) }}" class="btn btn-outline-choco">
                <i class="fas fa-eye mr-1"></i> Detail
              </a>
              <a href="{{ route('partner.store.tables.edit', $table->id) }}" class="btn btn-choco">
                <i class="fas fa-pen mr-1"></i> {{ __('messages.partner.outlet.table_management.tables.edit') }}
              </a>
              <button onclick="deleteTable({{ $table->id }})" class="btn btn-soft-danger">
                <i class="fas fa-trash-alt mr-1"></i> {{ __('messages.partner.outlet.table_management.tables.delete') }}
              </button>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<style>
    :root{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* Table shell */
.tables-index__table .table{
  background:#fff; border-color:#eef1f4;
  border-radius: var(--radius); overflow:hidden;
}
.tables-index__table .table thead th{
  background:#fff; border-bottom:2px solid #eef1f4;
  color:#374151; font-weight:600;
}
.tables-index__table .table-hover tbody tr:hover{
  background: rgba(193,40,20,.06);
}

/* Status badges */
.badge-status{
  display:inline-flex; align-items:center;
  padding:.35rem .6rem; border-radius:999px;
  font-weight:600; font-size:.78rem;
}
.badge-status--active{ background:var(--choco); color:#fff; }
.badge-status--inactive{ background:#e5e7eb; color:#374151; }
.badge-status--warn{ background:#fff7ed; color:#9a3412; border:1px solid #fed7aa; }

/* Thumbnails */
.thumb-list{ margin:-.25rem; }         /* faux gap */
.thumb-item{ margin:.25rem; display:block; }
.thumb-img{
  width:80px; height:80px; object-fit:cover;
  border-radius:12px; border:0; box-shadow:var(--shadow);
  transition: transform .15s ease, box-shadow .15s ease;
}
.thumb-item:hover .thumb-img{
  transform: scale(1.03);
  box-shadow: 0 10px 24px rgba(0,0,0,.12);
}

/* Brand buttons (fallback) */
.btn-choco{ background:var(--choco); border-color:var(--choco); color:#fff; }
.btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); color:#fff; }
.btn-outline-choco{ color:var(--choco); border-color:var(--choco); background:#fff; }
.btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }
.btn-soft-danger{ background:#fee2e2; color:#991b1b; border-color:#fecaca; }
.btn-soft-danger:hover{ background:#fecaca; color:#7f1d1d; border-color:#fca5a5; }

</style>

@push('scripts')
<script>
function deleteTable(tableId) {
  const swal = window.$swal || window.Swal;
  swal.fire({
    title: 'Apakah Anda yakin?',
    text: 'Anda tidak dapat mengembalikan data tersebut!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batalkan'
  }).then((result) => {
    if (!result.isConfirmed) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/partner/store/tables/${tableId}`;
    form.style.display = 'none';
    form.innerHTML = `
      @csrf
      <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
  });
}
</script>
<script>
function generateBarcode(tableId) {
  axios.get(`/partner/store/tables/generate-barcode/${tableId}`, { responseType: 'blob' })
  .then(res => {
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `barcode-table-${tableId}.png`);
    document.body.appendChild(link);
    link.click();
  })
  .catch(err => console.error('Gagal generate barcode:', err));
}
</script>
@endpush

