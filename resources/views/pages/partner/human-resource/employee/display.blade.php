<div class="employee-table table-responsive rounded-xl">
  <table class="table table-bordered table-hover align-middle mb-0">
    <thead class="thead-light">
      <tr>
        <th style="width:56px">#</th>
        <th>Nama Pegawai</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th style="width:80px">Picture</th>
        <th style="width:110px">Status</th>
        <th style="width:220px">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($employees as $index => $employee)
        <tr data-category="{{ $employee->role }}">
          <td>{{ $index + 1 }}</td>
          <td>{{ $employee->name }}</td>
          <td>{{ $employee->user_name }}</td>
          <td>{{ $employee->email }}</td>
          <td>{{ $employee->role }}</td>
          <td>
            @php
              $img = $employee->image
                ? (Str::startsWith($employee->image, ['http://','https://'])
                    ? $employee->image
                    : asset('storage/'.$employee->image))
                : null;
            @endphp

            @if($img)
              <a href="{{ $img }}" target="_blank" rel="noopener" class="thumb-link">
                <img
                  src="{{ $img }}"
                  alt="{{ $employee->name }}"
                  loading="lazy"
                  class="thumb-img"
                >
              </a>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>
            @if((int) $employee->is_active === 1)
              <span class="badge badge-status badge-status--active">
                <i class="fas fa-check-circle mr-1"></i> Aktif
              </span>
            @else
              <span class="badge badge-status badge-status--inactive">
                <i class="fas fa-minus-circle mr-1"></i> Nonaktif
              </span>
            @endif
          </td>

          <td>
            <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
              <a href="{{ route('partner.user-management.employees.show', $employee->id) }}" class="btn btn-outline-choco">
                <i class="fas fa-eye mr-1"></i> Detail
              </a>
              <a href="{{ route('partner.user-management.employees.edit', $employee->id) }}" class="btn btn-choco">
                <i class="fas fa-pen mr-1"></i> Edit
              </a>
              <button onclick="deleteEmployee({{ $employee->id }})" class="btn btn-soft-danger">
                <i class="fas fa-trash-alt mr-1"></i> Delete
              </button>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<style>
/* ==== Employee Index (scoped) ==== */
:root{
  /* fallback kalau theme/partner.css belum ke-load */
  --choco:#8c1000;
  --soft-choco:#c12814;
  --ink:#22272b;
  --paper:#f7f7f8;
  --radius:12px;
  --shadow:0 6px 20px rgba(0,0,0,.08);
}

.employee-table .table{
  background:#fff;
  border-color:#eef1f4;
  border-radius: var(--radius);
  overflow: hidden;
}

.employee-table .table thead th{
  background: #fff;
  border-bottom:2px solid #eef1f4;
  color:#374151;
  font-weight:600;
}

.employee-table .table tbody td{
  vertical-align: middle;
  color:#374151;
}

.employee-table .table-hover tbody tr:hover{
  background: rgba(193,40,20,.06); /* soft-choco 6% */
}

/* Thumbnail */
.employee-table .thumb-img{
  width:56px; height:56px;
  object-fit:cover;
  border-radius: 12px;
  border:0;
  box-shadow: var(--shadow);
  transition: transform .15s ease, box-shadow .15s ease;
}
.employee-table .thumb-link:hover .thumb-img{
  transform: scale(1.03);
  box-shadow: 0 10px 24px rgba(0,0,0,.12);
}

/* Badge status selaras tema */
.employee-table .badge-status{
  display:inline-flex; align-items:center;
  gap:.35rem;
  padding:.35rem .55rem;
  border-radius:999px;
  font-weight:600;
  font-size:.78rem;
}
.employee-table .badge-status--active{
  background: var(--choco); color:#fff;
}
.employee-table .badge-status--inactive{
  background:#e5e7eb; color:#374151;
}

/* Buttons brand (reuse util dari theme) */
.btn-choco{ background:var(--choco); border-color:var(--choco); color:#fff; }
.btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); color:#fff; }
.btn-outline-choco{ color:var(--choco); border-color:var(--choco); background:#fff; }
.btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }

/* Soft danger buat destructive yang tetap “lembut” */
.btn-soft-danger{
  background:#fee2e2; color:#991b1b; border-color:#fecaca;
}
.btn-soft-danger:hover{
  background:#fecaca; color:#7f1d1d; border-color:#fca5a5;
}

/* Table rounded container */
.employee-table.rounded-xl{
  border-radius: 1rem;
  box-shadow: var(--shadow);
}

/* Teks muted di table */
.employee-table .text-muted{ color:#6b7280 !important; }

</style>


@push('scripts')
<script>
function deleteEmployee(employeeId) {
  Swal.fire({
    title: 'Apakah Anda yakin?',
    text: 'Anda tidak dapat mengembalikan data tersebut!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batalkan',
    customClass: {
      confirmButton: 'btn btn-choco',
      cancelButton: 'btn btn-outline-choco'
    },
    buttonsStyling: false
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/partner/user-management/employees/${employeeId}`;
      form.style.display = 'none';

      const csrf = document.createElement('input');
      csrf.type = 'hidden';
      csrf.name = '_token';
      csrf.value = '{{ csrf_token() }}';
      form.appendChild(csrf);

      const method = document.createElement('input');
      method.type = 'hidden';
      method.name = '_method';
      method.value = 'DELETE';
      form.appendChild(method);

      document.body.appendChild(form);
      form.submit();
    }
  });
}
</script>

<script>
function generateBarcode(tableId) {
  axios.get(`/partner/store/tables/generate-barcode/${tableId}`, { responseType: 'blob' })
  .then(response => {
    const url = window.URL.createObjectURL(new Blob([response.data]));
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

