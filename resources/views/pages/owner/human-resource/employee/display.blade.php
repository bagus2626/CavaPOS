@php
  use Illuminate\Support\Str;
@endphp

<div class="table-responsive rounded-xl owner-emp-table">
  <table class="table table-hover align-middle">
    <thead>
      <tr>
        <th>#</th>
        <th>Outlet</th>
        <th>Nama Pegawai</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Picture</th>
        <th>Status</th>
        <th class="text-nowrap">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($employees as $index => $employee)
        <tr data-category="{{ $employee->partner_id }}">
          <td class="text-muted">{{ $index + 1 }}</td>

          <td class="fw-600">{{ $employee->partner->name }}</td>
          <td>{{ $employee->name }}</td>
          <td>{{ $employee->user_name }}</td>
          <td><a href="mailto:{{ $employee->email }}" class="link-ink">{{ $employee->email }}</a></td>
          <td>{{ $employee->role }}</td>

          <td class="col-photo">
            @php
              $img = $employee->image
                ? (Str::startsWith($employee->image, ['http://','https://'])
                    ? $employee->image
                    : asset('storage/'.$employee->image))
                : null;
            @endphp

            @if($img)
              <a href="{{ $img }}" target="_blank" rel="noopener">
                <img src="{{ $img }}" alt="{{ $employee->name }}" class="avatar-48" loading="lazy">
              </a>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>

          <td class="col-status">
            @if((int) $employee->is_active === 1)
              <span class="badge badge-soft-success d-inline-flex align-items-center gap-1">
                <i class="fas fa-check-circle mr-1"></i> Aktif
              </span>
            @else
              <span class="badge badge-soft-secondary d-inline-flex align-items-center gap-1">
                <i class="fas fa-minus-circle mr-1"></i> Nonaktif
              </span>
            @endif
          </td>

          <td class="col-actions">
                <div class="btn-group btn-group-sm action-group">
                    <a href="{{ route('owner.user-owner.employees.show', $employee->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-eye"></i><span>Detail</span>
                    </a>
                    <a href="{{ route('owner.user-owner.employees.edit', $employee->id) }}" class="btn btn-outline-choco">
                    <i class="fas fa-pen"></i><span>Edit</span>
                    </a>
                    <button onclick="deleteEmployee({{ $employee->id }})" class="btn btn-soft-danger">
                    <i class="fas fa-trash"></i><span>Delete</span>
                    </button>
                </div>
          </td>

        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<style>
    /* ===== Lanjutan polish untuk tabel karyawan ===== */

/* header & row hover sudah di-setup sebelumnya untuk .owner-emp-table */

/* Foto avatar */
.owner-emp-table .avatar-48{
  width:48px; height:48px; object-fit:cover;
  border-radius:12px; border:0; box-shadow:0 6px 20px rgba(0,0,0,.08);
}

/* Kolom actions jangan wrap */
.owner-emp-table .col-actions{ white-space: nowrap; }

/* Link email */
.owner-emp-table .link-ink{ color:#374151; text-decoration:none; }
.owner-emp-table .link-ink:hover{ color: var(--choco); }

/* Soft badges (kalau belum ada di file ini) */
.badge-soft-success{
  background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; border-radius:999px; font-weight:600;
  padding:.32rem .55rem;
}
.badge-soft-secondary{
  background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:999px; font-weight:600;
  padding:.32rem .55rem;
}

/* Tombol actions yang seragam */
.owner-emp-table .btn-group-sm .btn{
  border-radius:10px; padding:.25rem .6rem;
}
.btn-outline-choco{
  color: var(--choco); border-color: var(--choco); background:#fff;
}
.btn-outline-choco:hover{
  color:#fff; background:var(--choco); border-color:var(--choco);
}
.btn-soft-danger{
  background:#fee2e2; color:#991b1b; border-color:#fecaca;
}
.btn-soft-danger:hover{
  background:#fecaca; color:#7f1d1d; border-color:#fca5a5;
}

/* ==== Aksi: bikin simetris & konsisten ==== */
.owner-emp-table .action-group{
  display:inline-flex;          /* kasih gap antar tombol (tidak dempet) */
  gap:.4rem;
}

.owner-emp-table .action-group .btn{
  /* bentuk & ukuran konsisten */
  height:36px;
  padding:0 .85rem;
  border-radius:999px !important;
  line-height:1;
  display:inline-flex;
  align-items:center;
  font-weight:600;
  border-width:1px;
}

/* ikon rata tengah dan ukuran sama */
.owner-emp-table .action-group .btn i{
  font-size:.9rem;
  margin-right:.4rem;
  line-height:1;
}

/* varian warna (seragam dengan tema choco) */
.btn-outline-choco{
  color: var(--choco);
  border-color: var(--choco);
  background:#fff;
}
.btn-outline-choco:hover{
  color:#fff;
  background: var(--choco);
  border-color: var(--choco);
}
.btn-soft-danger{
  background:#fee2e2;
  color:#991b1b;
  border-color:#fecaca;
}
.btn-soft-danger:hover{
  background:#fecaca;
  color:#7f1d1d;
  border-color:#fca5a5;
}


</style>

@push('scripts')
<script>
function deleteEmployee(employeeId) {
  Swal.fire({
    title: 'Apakah Anda yakin?',
    text: "Anda tidak dapat mengembalikan data tersebut!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batalkan'
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/owner/user-owner/employees/${employeeId}`;
      form.style.display = 'none';

      form.innerHTML = `
        @csrf
        <input type="hidden" name="_method" value="DELETE">
      `;
      document.body.appendChild(form);
      form.submit();
    }
  });
}
</script>
@endpush
