@extends('layouts.partner')

@section('title', 'Employee Detail')
@section('page_title', 'Employee Detail')

@push('styles')
  @vite('resources/css/pages/employee-show.css')
@endpush

@section('content')
@php
    use Illuminate\Support\Str;

    // siapkan URL gambar (relatif → storage url)
    $img = $data->image
        ? (Str::startsWith($data->image, ['http://','https://'])
            ? $data->image
            : asset('storage/'.$data->image))
        : null;

    $isActive = (int) $data->is_active === 1;
@endphp

<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('partner.user-management.employees.index') }}" class="btn btn-outline-choco">
      <i class="fas fa-arrow-left mr-2"></i> Back to Employees
    </a>

    <div class="btn-group">
      <a href="{{ route('partner.user-management.employees.edit', $data->id) }}" class="btn btn-choco">
        <i class="fas fa-pen mr-1"></i> Edit
      </a>
      <button class="btn btn-soft-danger"
              onclick="confirmDeletion(`{{ route('partner.user-management.employees.destroy', $data->id) }}`)">
        <i class="fas fa-trash-alt mr-1"></i> Delete
      </button>
    </div>
  </div>

  <div class="card employee-show shadow-sm">
    <!-- Hero -->
    <div class="employee-hero">
      <div class="employee-avatar">
        @if($img)
          <img src="{{ $img }}" alt="{{ $data->name }}">
        @else
          <div class="employee-avatar__placeholder">
            {{ Str::upper(Str::substr($data->name ?? 'U', 0, 1)) }}
          </div>
        @endif
      </div>

      <div class="employee-hero__meta">
        <h3 class="employee-name mb-1">{{ $data->name }}</h3>
        @if($isActive)
          <span class="badge badge-status badge-status--active"><i class="fas fa-check-circle mr-1"></i>Aktif</span>
        @else
          <span class="badge badge-status badge-status--inactive"><i class="fas fa-minus-circle mr-1"></i>Nonaktif</span>
        @endif
      </div>
    </div>

    <!-- Body -->
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <dl class="meta-list">
            <dt>Username</dt>
            <dd>{{ $data->user_name }}</dd>

            <dt>Email</dt>
            <dd><a class="link-ink" href="mailto:{{ $data->email }}">{{ $data->email }}</a></dd>

            <dt>Role</dt>
            <dd>{{ $data->role }}</dd>
          </dl>
        </div>
        <div class="col-md-6">
          <dl class="meta-list">
            <dt>Status</dt>
            <dd>
              @if($isActive)
                <span class="badge badge-status badge-status--active">Aktif</span>
              @else
                <span class="badge badge-status badge-status--inactive">Nonaktif</span>
              @endif
            </dd>

            <dt>Dibuat</dt>
            <dd>{{ optional($data->created_at)->format('d M Y, H:i') ?? '—' }}</dd>

            <dt>Diperbarui</dt>
            <dd>{{ optional($data->updated_at)->format('d M Y, H:i') ?? '—' }}</dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
    /* ==== Avatar size tuning ==== */
.employee-show{
  --avatar-size: 200px; /* dulunya 88px */
}

.employee-show .employee-avatar{
  width: var(--avatar-size);
  height: var(--avatar-size);
}

/* hero lebih ringkas biar proporsional */
.employee-show .employee-hero{
  padding: .9rem 1rem;   /* sebelumnya ~1.25rem */
  gap: .75rem;           /* lebih rapat */
}

.employee-show .employee-name{
  font-size: 1.1rem;     /* kecilkan judul agar balance dengan avatar */
  line-height: 1.2;
}

/* di layar kecil, avatar makin kecil */
@media (max-width: 576px){
  .employee-show{ --avatar-size: 150px; }
}

</style>
@endsection

@push('scripts')
<script>
  // Helper hapus global: gunakan $swal kalau sudah di-layout, fallback ke Swal
  function confirmDeletion(url, opts = {}) {
    const base = {
      title: 'Apakah Anda yakin?',
      text: 'Anda tidak dapat mengembalikan data tersebut!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batalkan'
    };
    const swal = window.$swal || window.Swal;
    if (!swal) { // fallback paling sederhana
      if (confirm(base.title + '\n' + base.text)) {
        postDelete(url);
      }
      return;
    }
    swal.fire(Object.assign(base, opts)).then(r => { if (r.isConfirmed) postDelete(url); });
  }

  function postDelete(url) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.style.display = 'none';
    form.innerHTML = `
      @csrf
      <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
  }
</script>
@endpush
