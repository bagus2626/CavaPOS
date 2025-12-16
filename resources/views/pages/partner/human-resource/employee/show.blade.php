@extends('layouts.partner')

@section('title', __('messages.partner.user_management.employees.employee_detail'))
@section('page_title', __('messages.partner.user_management.employees.employee_detail'))

@section('content')
  @php
    use Illuminate\Support\Str;

    // siapkan URL gambar (relatif → storage url)
    $img = $data->image
      ? (Str::startsWith($data->image, ['http://', 'https://'])
        ? $data->image
        : asset('storage/' . $data->image))
      : null;

    $isActive = (int) $data->is_active === 1;
  @endphp

  <section class="content">

    <div class="container-fluid employee-show-page"> {{-- tambahkan class page scope --}}

      <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
        <a href="{{ route('partner.user-management.employees.index') }}" class="btn btn-outline-choco">
          <i class="fas fa-arrow-left mr-2"></i> Back to Employees
        </a>

        <div class="btn-group">
          {{-- rafi --}}
          {{-- <a href="{{ route('partner.user-management.employees.edit', $data->id) }}" class="btn btn-choco">
            <i class="fas fa-pen mr-1"></i> Edit
          </a>
          <button class="btn btn-soft-danger"
            onclick="confirmDeletion(`{{ route('partner.user-management.employees.destroy', $data->id) }}`)">
            <i class="fas fa-trash-alt mr-1"></i> Delete
          </button> --}}
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
              <span class="badge badge-status badge-status--inactive"><i
                  class="fas fa-minus-circle mr-1"></i>Nonaktif</span>
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
  </section>

  <style>
    /* ===== Partner › Employee Show (page scope) ===== */
    .employee-show-page {
      --choco: #8c1000;
      --soft-choco: #c12814;
      --ink: #22272b;
      --muted: #6b7280;
      --paper: #fff;
      --radius: 14px;
      --shadow: 0 6px 20px rgba(0, 0, 0, .08);
    }

    /* Card */
    .employee-show-page .employee-show.card {
      border: 0;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
      background: var(--paper);
    }

    /* Hero */
    .employee-show-page .employee-hero {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1.1rem 1.25rem;
      background: linear-gradient(90deg, rgba(140, 16, 0, .05), rgba(140, 16, 0, .02));
      border-bottom: 1px solid #eef1f4;
    }

    .employee-show-page .employee-avatar {
      --avatar-size: 200px;
      /* default desktop */
      width: var(--avatar-size);
      height: var(--avatar-size);
      border-radius: 999px;
      overflow: hidden;
      flex: 0 0 auto;
      position: relative;
      box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
      border: 4px solid #fff;
    }

    .employee-show-page .employee-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .employee-show-page .employee-avatar__placeholder {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f3f4f6;
      color: #111827;
      font-weight: 800;
      font-size: 3rem;
    }

    .employee-show-page .employee-hero__meta {
      min-width: 0;
    }

    .employee-show-page .employee-name {
      margin: 0;
      font-weight: 700;
      color: var(--ink);
      font-size: 1.35rem;
      line-height: 1.2;
    }

    /* Badges */
    .employee-show-page .badge-status {
      border-radius: 999px;
      padding: .42rem .7rem;
      font-weight: 600;
      font-size: .85rem;
      vertical-align: middle;
    }

    .employee-show-page .badge-status--active {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }

    .employee-show-page .badge-status--inactive {
      background: #f3f4f6;
      color: #374151;
      border: 1px solid #e5e7eb;
    }

    /* Buttons (brand) */
    .employee-show-page .btn-choco {
      background: var(--choco);
      border-color: var(--choco);
      color: #fff;
    }

    .employee-show-page .btn-choco:hover {
      background: var(--soft-choco);
      border-color: var(--soft-choco);
      color: #fff;
    }

    .employee-show-page .btn-outline-choco {
      color: var(--choco);
      border: 1px solid var(--choco);
      background: transparent;
    }

    .employee-show-page .btn-outline-choco:hover {
      color: #fff;
      background: var(--choco);
      border-color: var(--choco);
    }

    .employee-show-page .btn-soft-danger {
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    .employee-show-page .btn-soft-danger:hover {
      background: #fecaca;
      color: #7f1d1d;
      border-color: #fca5a5;
    }

    /* Meta list */
    .employee-show-page .meta-list {
      margin: 0 0 .5rem 0;
    }

    .employee-show-page .meta-list dt {
      color: #374151;
      font-weight: 600;
      width: 40%;
      float: left;
      clear: left;
      padding: .45rem 0;
      border-bottom: 1px dashed #eef1f4;
    }

    .employee-show-page .meta-list dd {
      margin-left: 40%;
      color: #111827;
      padding: .45rem 0;
      border-bottom: 1px dashed #eef1f4;
    }

    .employee-show-page .meta-list dt:last-of-type,
    .employee-show-page .meta-list dd:last-of-type {
      border-bottom: 0;
    }

    /* Links */
    .employee-show-page .link-ink {
      color: var(--choco);
      text-decoration: none;
    }

    .employee-show-page .link-ink:hover {
      color: var(--soft-choco);
      text-decoration: underline;
    }

    /* Alerts */
    .employee-show-page .alert {
      border-left: 4px solid var(--choco);
      border-radius: 10px;
    }

    /* Responsiveness */
    @media (max-width: 992px) {
      .employee-show-page .employee-avatar {
        --avatar-size: 170px;
      }
    }

    @media (max-width: 768px) {
      .employee-show-page .employee-hero {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .employee-show-page .employee-avatar {
        --avatar-size: 150px;
      }

      .employee-show-page .meta-list dt {
        width: 48%;
      }

      .employee-show-page .meta-list dd {
        margin-left: 48%;
      }
    }

    @media (max-width: 576px) {
      .employee-show-page .employee-avatar {
        --avatar-size: 120px;
      }

      .employee-show-page .employee-name {
        font-size: 1.15rem;
      }

      .employee-show-page .btn-group {
        width: 100%;
      }

      .employee-show-page .btn-group .btn {
        width: 50%;
      }
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