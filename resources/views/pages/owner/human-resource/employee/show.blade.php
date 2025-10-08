@extends('layouts.owner')

@section('title',  __('messages.owner.user_management.employees.employee_detail'))
@section('page_title',  __('messages.owner.user_management.employees.employee_detail'))

@section('content')
@php
  use Illuminate\Support\Str;

  // fleksibel: dukung $data atau $employee dari controller
  $emp = $data ?? $employee ?? null;

  // gambar (relatif → storage)
  $img = $emp && $emp->image
      ? (Str::startsWith($emp->image, ['http://','https://'])
          ? $emp->image
          : asset('storage/'.$emp->image))
      : null;

  $isActive = (int) ($emp->is_active ?? 0) === 1;
@endphp

<div class="container owner-emp-show">
  {{-- Toolbar --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('owner.user-owner.employees.index') }}" class="btn btn-outline-choco">
      <i class="fas fa-arrow-left mr-2"></i> {{ __('messages.owner.user_management.employees.back_to_employees') }}
    </a>

    <div class="btn-group">
      <a href="{{ route('owner.user-owner.employees.edit', $emp->id) }}" class="btn btn-choco">
        <i class="fas fa-pen mr-1"></i> {{ __('messages.owner.user_management.employees.edit') }}
      </a>
      <button class="btn btn-soft-danger"
              onclick="ownerConfirmDeletion(`{{ route('owner.user-owner.employees.destroy', $emp->id) }}`)">
        <i class="fas fa-trash-alt mr-1"></i> {{ __('messages.owner.user_management.employees.delete') }}
      </button>
    </div>
  </div>

  {{-- Card --}}
  <div class="card shadow-sm employee-card">
    {{-- Hero --}}
    <div class="employee-hero">
      <div class="employee-avatar">
        @if($img)
          <img src="{{ $img }}" alt="{{ $emp->name }}">
        @else
          <div class="employee-avatar__placeholder">
            {{ Str::upper(Str::substr($emp->name ?? 'U', 0, 1)) }}
          </div>
        @endif
      </div>

      <div class="employee-hero__meta">
        <h3 class="employee-name mb-1">{{ $emp->name }}</h3>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <span class="badge badge-role">{{ $emp->role ?? '—' }}</span>
          @if($isActive)
            <span class="badge badge-status badge-status--active">
              <i class="fas fa-check-circle mr-1"></i>{{ __('messages.owner.user_management.employees.active') }}
            </span>
          @else
            <span class="badge badge-status badge-status--inactive">
              <i class="fas fa-minus-circle mr-1"></i>{{ __('messages.owner.user_management.employees.non_active') }}
            </span>
          @endif
        </div>
      </div>
    </div>

    {{-- Body --}}
    <div class="card-body">
      <div class="row gy-3">
        <div class="col-md-6">
          <dl class="meta-list">
            <dt>{{ __('messages.owner.user_management.employees.outlet') }}</dt>
            <dd>{{ optional($emp->partner)->name ?? '—' }}</dd>

            <dt>{{ __('messages.owner.user_management.employees.username') }}</dt>
            <dd>{{ $emp->user_name ?? '—' }}</dd>

            <dt>{{ __('messages.owner.user_management.employees.email') }}</dt>
            <dd>
              @if(!empty($emp->email))
                <a class="link-ink" href="mailto:{{ $emp->email }}">{{ $emp->email }}</a>
              @else
                —
              @endif
            </dd>
          </dl>
        </div>
        <div class="col-md-6">
          <dl class="meta-list">
            <dt>Status</dt>
            <dd>
              @if($isActive)
                <span class="badge badge-status badge-status--active">{{ __('messages.owner.user_management.employees.active') }}</span>
              @else
                <span class="badge badge-status badge-status--inactive">{{ __('messages.owner.user_management.employees.non_active') }}</span>
              @endif
            </dd>

            <dt>{{ __('messages.owner.user_management.employees.created') }}</dt>
            <dd>{{ optional($emp->created_at)->format('d M Y, H:i') ?? '—' }}</dd>

            <dt>{{ __('messages.owner.user_management.employees.updated') }}</dt>
            <dd>{{ optional($emp->updated_at)->format('d M Y, H:i') ?? '—' }}</dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

<style>
/* =========================================================
   Owner › Employee Show (page scope)
   Scope semua styling di bawah .owner-emp-show
   ========================================================= */

.owner-emp-show{
  /* Brand vars (aman jika global belum ter-load) */
  --choco:#8c1000;
  --soft-choco:#c12814;
  --ink:#22272b;
  --paper:#f7f7f8;

  --radius:12px;
  --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* -----------------------------
   Card container
--------------------------------*/
.owner-emp-show .employee-card{
  border:0;
  border-radius:var(--radius);
  box-shadow:var(--shadow);
  overflow:hidden;
  background:#fff;
}

/* -----------------------------
   Hero (header profil)
--------------------------------*/
.owner-emp-show .employee-hero{
  display:flex;
  align-items:center;
  gap:10px;               /* kompak */
  padding:10px 14px;      /* kompak */
  background:#fff;
  border-bottom:1px solid #eef1f4;
}

/* Avatar KECIL (default 150px) */
.owner-emp-show .employee-avatar{
  width:150px;
  height:150px;
  border-radius:10px;
  overflow:hidden;
  flex:0 0 auto;
  box-shadow:var(--shadow);
  background:#fff;
}
.owner-emp-show .employee-avatar img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
}
.owner-emp-show .employee-avatar__placeholder{
  width:100%;
  height:100%;
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:700;
  font-size:18px;
  color:#fff;
  background:linear-gradient(135deg, var(--choco), var(--soft-choco));
}

/* Meta di hero */
.owner-emp-show .employee-hero__meta{ min-width:0; }
.owner-emp-show .employee-name{
  margin:0;
  font-weight:700;
  color:var(--ink);
  line-height:1.2;
}

/* -----------------------------
   Badges (role & status)
--------------------------------*/
.owner-emp-show .badge-role{
  display:inline-flex;
  align-items:center;
  padding:.28rem .6rem;
  border-radius:999px;
  font-weight:600;
  background:#fff1ef;
  color:#8c1000;
  border:1px solid #f7c9c2;
}

.owner-emp-show .badge-status{
  display:inline-flex;
  align-items:center;
  padding:.28rem .6rem;
  border-radius:999px;
  font-weight:600;
}
.owner-emp-show .badge-status--active{
  background:#ecfdf5;
  color:#065f46;
  border:1px solid #a7f3d0;
}
.owner-emp-show .badge-status--inactive{
  background:#f3f4f6;
  color:#374151;
  border:1px solid #e5e7eb;
}

/* -----------------------------
   Detail list (body)
--------------------------------*/
.owner-emp-show .meta-list{
  margin:0 0 1rem;
}
.owner-emp-show .meta-list dt{
  margin-top:.35rem;
  margin-bottom:.15rem;
  font-weight:700;
  color:#374151; /* slate-700 */
}
.owner-emp-show .meta-list dd{
  margin-bottom:.75rem;
  color:#4b5563; /* slate-600 */
}

/* -----------------------------
   Links
--------------------------------*/
.owner-emp-show .link-ink{
  color:#374151;
  text-decoration:none;
}
.owner-emp-show .link-ink:hover{
  color:var(--choco);
}

/* -----------------------------
   Buttons (brand harmonized)
--------------------------------*/
.owner-emp-show .btn-choco{
  background:var(--choco);
  border-color:var(--choco);
  color:#fff;
}
.owner-emp-show .btn-choco:hover{
  background:var(--soft-choco);
  border-color:var(--soft-choco);
}

.owner-emp-show .btn-outline-choco{
  color:var(--choco);
  border-color:var(--choco);
}
.owner-emp-show .btn-outline-choco:hover{
  color:#fff;
  background:var(--choco);
  border-color:var(--choco);
}

/* Soft danger untuk Delete */
.owner-emp-show .btn-soft-danger{
  background:#fee2e2;
  color:#991b1b;
  border-color:#fecaca;
}
.owner-emp-show .btn-soft-danger:hover{
  background:#fecaca;
  color:#7f1d1d;
  border-color:#fca5a5;
}

/* Samakan tinggi/radius antar tombol di toolbar */
.owner-emp-show .btn-group .btn,
.owner-emp-show .btn{
  border-radius:10px;
}

/* -----------------------------
   Responsive: avatar lebih kecil
--------------------------------*/
@media (max-width: 480px){
  .owner-emp-show .employee-avatar{
    width:100px;
    height:100px;
    border-radius:8px;
  }
  .owner-emp-show .employee-avatar__placeholder{
    font-size:16px;
  }
  .owner-emp-show .employee-hero{
    gap:8px;
    padding:8px 12px;
  }
}

</style>

@push('scripts')
<script>
  function ownerConfirmDeletion(url, opts = {}) {
    const base = {
      title: 'Apakah Anda yakin?',
      text: 'Anda tidak dapat mengembalikan data tersebut!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batalkan'
    };
    const swal = window.$swal || window.Swal;
    if (!swal) {
      if (confirm(base.title + '\n' + base.text)) ownerPostDelete(url);
      return;
    }
    swal.fire(Object.assign(base, opts)).then(r => { if (r.isConfirmed) ownerPostDelete(url); });
  }

  function ownerPostDelete(url) {
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
