@extends('layouts.partner')

@section('title', 'Product List')
@section('page_title', 'All Products')

@section('content')
<script>
function deleteProduct(productId) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda tidak dapat mengembalikan data tersebut!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batalkan'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/partner/products/${productId}`;
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

<section class="content">
  <div class="container-fluid tables-index">
    <a href="{{ route('partner.store.tables.create') }}" class="btn btn-choco mb-3">
      <i class="fas fa-plus mr-1"></i> Add Table
    </a>

    <div class="mb-3">
      <button class="btn btn-outline-choco btn-sm filter-btn rounded-pill active" data-category="all">All</button>
      @foreach($table_classes as $table_class)
        <button class="btn btn-outline-choco btn-sm filter-btn rounded-pill" data-category="{{ $table_class }}">
          {{ $table_class }}
        </button>
      @endforeach
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- bungkus display biar CSS page-scope --}}
    <div class="tables-index__table">
      @include('pages.partner.store.tables.display')
    </div>
  </div>
</section>

<style>
    /* ==== Tables Index (page scope) ==== */
:root{
  /* fallback kalau theme belum ke-load */
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* tombol filter (outline brand) */
.tables-index .filter-btn{
  border-width:1.5px; letter-spacing:.2px; transition:.15s ease;
}
.tables-index .filter-btn.active{
  background:var(--choco); border-color:var(--choco); color:#fff;
  box-shadow:0 6px 14px rgba(140,16,0,.18);
}
.tables-index .filter-btn:not(.active){
  color:var(--choco); border-color:var(--choco); background:#fff;
}
.tables-index .filter-btn:not(.active):hover{
  background: rgba(140,16,0,.08);
}

/* tabel tampil rapi dan nyambung tema */
.tables-index__table .table{
  background:#fff; border-color:#eef1f4;
  border-radius: var(--radius); overflow:hidden;
}
.tables-index__table .table thead th{
  background:#fff; border-bottom:2px solid #eef1f4;
  color:#374151; font-weight:600;
}
.tables-index__table .table-hover tbody tr:hover{
  background: rgba(193,40,20,.06); /* soft-choco 6% */
}
.tables-index__table .text-muted{ color:#6b7280 !important; }

/* badge status seragam */
.badge-status{
  display:inline-flex; align-items:center; gap:.35rem;
  padding:.35rem .55rem; border-radius:999px;
  font-weight:600; font-size:.78rem;
}
.badge-status--active{ background:var(--choco); color:#fff; }
.badge-status--inactive{ background:#e5e7eb; color:#374151; }

/* thumbnail gambar (kalau ada foto meja) */
.tables-index__table .thumb-img{
  width:56px; height:56px; object-fit:cover;
  border-radius:12px; border:0; box-shadow:var(--shadow);
  transition: transform .15s ease, box-shadow .15s ease;
}
.tables-index__table a:hover .thumb-img{
  transform: scale(1.03); box-shadow:0 10px 24px rgba(0,0,0,.12);
}

/* empty row state */
.tables-index__table tr.empty-row td{
  color:#6b7280; background: #fafafa;
}

/* buttons brand (fallback kalau belum ada di theme) */
.btn-choco{ background:var(--choco); border-color:var(--choco); color:#fff; }
.btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); color:#fff; }
.btn-outline-choco{ color:var(--choco); border-color:var(--choco); background:#fff; }
.btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }

/* Danger lembut utk delete */
.btn-soft-danger{
  background:#fee2e2; color:#991b1b; border-color:#fecaca;
}
.btn-soft-danger:hover{
  background:#fecaca; color:#7f1d1d; border-color:#fca5a5;
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
            console.log('Tombol diklik:', this.textContent);
            const categoryId = this.getAttribute('data-category');

            // hapus class active dari semua tombol
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const tableBody = document.querySelector('tbody');
            const tableRows = document.querySelectorAll('tbody tr');

            let visibleCount = 0; // hitung row yang tampil

            tableRows.forEach((row, index) => {
                if(categoryId === 'all' || row.getAttribute('data-category') === categoryId) {
                    row.style.display = '';
                    visibleCount++;
                    row.querySelector('td').textContent = visibleCount; // update nomor urut di kolom pertama
                } else {
                    row.style.display = 'none';
                }
            });

            // hapus row "data tidak ditemukan" dulu kalau ada
            const emptyRow = tableBody.querySelector('.empty-row');
            if(emptyRow) emptyRow.remove();

            // jika tidak ada row yang tampil, tampilkan pesan
            if(visibleCount === 0) {
                const tr = document.createElement('tr');
                tr.classList.add('empty-row');
                tr.innerHTML = `<td colspan="5" class="text-center">Data tidak ditemukan</td>`;
                tableBody.appendChild(tr);
            }
        });
    });
});
</script>
@endpush
