@extends('layouts.owner')

@section('title', 'Employee List')
@section('page_title', 'All Employees')

@section('content')
<section class="content owner-emp">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <a href="{{ route('owner.user-owner.employees.create') }}" class="btn btn-choco btn-pill">
        <i class="fas fa-user-plus mr-2"></i> Add Employee
      </a>

      <div class="filter-bar mb-3">
        <button class="btn filter-btn rounded-pill active" data-category="all">All</button>
        @foreach($partners as $partner)
          <button class="btn filter-btn rounded-pill" data-category="{{ $partner->id }}">
            {{ $partner->name }}
          </button>
        @endforeach
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success brand-alert">{{ session('success') }}</div>
    @endif

    {{-- tabel karyawan --}}
    <div class="owner-emp-table">
      @include('pages.owner.human-resource.employee.display')
    </div>
  </div>
</section>

<style>
    /* ==== Owner › Employee Index (page scope) ==== */
:root{
  /* fallback jika layout belum define */
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* Util */
.btn-pill{ border-radius:999px; }
.fw-600{ font-weight:600; }

/* Brand buttons (fallback) */
.btn-choco{ background:var(--choco); border-color:var(--choco); color:#fff; }
.btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); color:#fff; }
.btn-outline-choco{ color:var(--choco); border-color:var(--choco); background:#fff; }
.btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }

/* Alert yang selaras tema */
.brand-alert{ border-left:4px solid var(--choco); border-radius:10px; }

/* ===== Filter bar ===== */
.owner-emp .filter-bar{ display:flex; flex-wrap:wrap; gap:.4rem; }
.owner-emp .filter-btn{
  border:1px solid #e5e7eb; color:#374151; background:#fff; padding:.35rem .8rem;
  box-shadow:0 1px 2px rgba(0,0,0,.04); transition:.15s ease;
}
.owner-emp .filter-btn:hover{ transform: translateY(-1px); }
.owner-emp .filter-btn.active{
  background:var(--choco); color:#fff; border-color:var(--choco);
  box-shadow:0 8px 24px rgba(140,16,0,.20);
}

/* ===== Table polish (scoped) ===== */
.owner-emp-table .table{
  border-collapse: separate;
  border-spacing: 0;
  background:#fff;
  border-radius:10px;
  overflow:hidden;
  box-shadow: var(--shadow);
}
.owner-emp-table thead th{
  background:#fff;
  border-bottom:2px solid #eef1f4 !important;
  color:#374151; font-weight:700;
  white-space:nowrap;
}
.owner-emp-table tbody td{ vertical-align: middle; }
.owner-emp-table tbody tr{ transition: background-color .12s ease; }
.owner-emp-table tbody tr:hover{ background: rgba(140,16,0,.04); }

/* Avatar/picture kecil dalam tabel (jika ada) */
.owner-emp-table td img{
  width:42px; height:42px; object-fit:cover;
  border-radius: 10px; border:0; box-shadow: 0 4px 14px rgba(0,0,0,.08);
}

/* Status badge yang lembut (optional, kalau pakai badge default tetap bagus) */
.badge-soft-success{
  background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; border-radius:999px; font-weight:600;
}
.badge-soft-secondary{
  background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:999px; font-weight:600;
}

/* Action buttons kecil di tabel */
.owner-emp-table .btn-group-sm .btn{
  border-radius:10px; padding:.25rem .55rem;
}
.owner-emp-table .btn-soft-danger{
  background:#fee2e2; color:#991b1b; border-color:#fecaca;
}
.owner-emp-table .btn-soft-danger:hover{
  background:#fecaca; color:#7f1d1d; border-color:#fca5a5;
}

/* Empty state row */
.owner-emp-table .empty-row td{
  background:#fff; color:#6b7280;
}

</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const filterButtons = document.querySelectorAll('.filter-btn');

  filterButtons.forEach(button => {
    button.addEventListener('click', function() {
      const categoryId = this.getAttribute('data-category');

      // toggle active
      filterButtons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');

      const tableBody = document.querySelector('tbody');
      const tableRows = document.querySelectorAll('tbody tr');
      let visibleCount = 0;

      tableRows.forEach(row => {
        if(categoryId === 'all' || row.getAttribute('data-category') === categoryId) {
          row.style.display = '';
          visibleCount++;
          const firstCell = row.querySelector('td');
          if (firstCell) firstCell.textContent = visibleCount;
        } else {
          row.style.display = 'none';
        }
      });

      // bersihkan row kosong lama
      const emptyRow = tableBody.querySelector('.empty-row');
      if (emptyRow) emptyRow.remove();

      if (visibleCount === 0) {
        const tr = document.createElement('tr');
        tr.classList.add('empty-row');
        tr.innerHTML = `<td colspan="9" class="text-center text-muted py-4">Data tidak ditemukan</td>`;
        tableBody.appendChild(tr);
      }
    });
  });
});
</script>
@endpush
