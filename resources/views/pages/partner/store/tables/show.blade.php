@extends('layouts.partner')

@section('title', 'Table Detail')
@section('page_title', 'Table Detail')

@section('content')
<div class="container">
  <a href="{{ route('partner.store.tables.index') }}" class="btn btn-outline-choco mb-3">
    <i class="fas fa-arrow-left mr-2"></i>Back to Tables
  </a>

  <div class="card table-show shadow-sm rounded-xl">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="mb-0">Detail Table #{{ $data->table_no }}</h4>
    </div>

    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-4 meta-label">Table No</div>
        <div class="col-md-8 meta-value">{{ $data->table_no }}</div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4 meta-label">Class / Type</div>
        <div class="col-md-8 meta-value">{{ $data->table_class }}</div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4 meta-label">Description</div>
        <div class="col-md-8 meta-value">{{ $data->description ?? '—' }}</div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4 meta-label">Status</div>
        <div class="col-md-8 meta-value">
          @php $status = strtolower($data->status); @endphp
          @if($status === 'available')
            <span class="badge badge-status badge-status--available">Available</span>
          @elseif($status === 'occupied')
            <span class="badge badge-status badge-status--occupied">Occupied</span>
          @elseif($status === 'reserved')
            <span class="badge badge-status badge-status--reserved">Reserved</span>
          @else
            <span class="badge badge-status badge-status--neutral">{{ $data->status }}</span>
          @endif
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4 meta-label">Pictures</div>
        <div class="col-md-8 meta-value">
          @if(!empty($data->images) && is_array($data->images))
            <div class="thumb-list">
              @foreach($data->images as $image)
                @php $src = asset($image['path']); @endphp
                <a href="{{ $src }}" target="_blank" rel="noopener" class="thumb-item">
                  <img src="{{ $src }}" alt="{{ $image['filename'] ?? 'Table Image' }}" class="thumb-img">
                </a>
              @endforeach
            </div>
          @else
            <span class="text-muted">No Images</span>
          @endif
        </div>
      </div>
    </div>

    <div class="card-footer text-end">
      <a href="{{ route('partner.store.tables.edit', $data->id) }}" class="btn btn-choco">
        <i class="fas fa-pen mr-1"></i> Edit
      </a>
      <button onclick="deleteTable({{ $data->id }})" class="btn btn-soft-danger">
        <i class="fas fa-trash-alt mr-1"></i> Delete
      </button>
    </div>
  </div>
</div>

<style>
    /* ==== Tables Show (scoped) ==== */
:root{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* rounded-xl fallback if not present */
.rounded-xl{ border-radius: 1rem; }

/* Card polish */
.table-show{ border:0; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
.table-show .card-header{
  background: linear-gradient(135deg, var(--choco), var(--soft-choco));
  color:#fff; border-bottom:0;
}
.table-show .card-header h4{ font-weight:700; letter-spacing:.2px; }
.table-show .card-body{ background:#fff; }

/* Meta rows */
.table-show .meta-label{
  font-weight:700; color:#374151;   /* slate-700 */
}
.table-show .meta-value{
  color:#1f2937;                    /* slate-800 */
}

/* Badges - brand aligned */
.badge-status{
  display:inline-flex; align-items:center;
  padding:.38rem .7rem; border-radius:999px;
  font-weight:600; font-size:.8rem;
}
.badge-status--available{ background:var(--choco); color:#fff; }
.badge-status--reserved{ background:#fff7ed; color:#9a3412; border:1px solid #fed7aa; }
.badge-status--occupied{ background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
.badge-status--neutral{ background:#e5e7eb; color:#374151; }

/* Thumbnails */
.thumb-list{ display:flex; flex-wrap:wrap; margin:-.35rem; }
.thumb-item{ margin:.35rem; display:block; }
.thumb-img{
  width:120px; height:120px; object-fit:cover;
  border-radius:12px; border:0; box-shadow:var(--shadow);
  transition: transform .15s ease, box-shadow .15s ease;
}
.thumb-item:hover .thumb-img{
  transform: scale(1.03);
  box-shadow: 0 10px 24px rgba(0,0,0,.12);
}

/* Buttons (fallback bila belum di global theme) */
.btn-choco{ background:var(--choco); border-color:var(--choco); color:#fff; }
.btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); color:#fff; }
.btn-outline-choco{ color:var(--choco); border-color:var(--choco); }
.btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }
.btn-soft-danger{ background:#fee2e2; color:#991b1b; border-color:#fecaca; }
.btn-soft-danger:hover{ background:#fecaca; color:#7f1d1d; border-color:#fca5a5; }

</style>
@endsection

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
@endpush
