@extends('layouts.owner')

@section('title', 'Product List')
@section('page_title', 'All Products')

@section('content')
<section class="content">
  <div class="container-fluid owner-promotions"> {{-- PAGE SCOPE --}}

    <a href="{{ route('owner.user-owner.promotions.create') }}" class="btn btn-primary mb-3">
      + Add Promotion
    </a>

    {{-- Filter pills --}}
    <div class="mb-3">
      <button class="btn btn-sm filter-btn rounded-pill active" data-category="all">All</button>
      @foreach($promotions->pluck('promotion_type')->unique() as $type)
        <button class="btn btn-sm filter-btn rounded-pill" data-category="{{ $type }}">
          @if($type == 'percentage') Persentase @else Nominal Tetap @endif
        </button>
      @endforeach
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tabel dari partial --}}
    @include('pages.owner.products.promotion.display')
  </div>
</section>

{{-- Brand styling --}}
<style>
/* ===== Owner › Promotions (page scope) ===== */
.owner-promotions{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* Brand buttons */
.owner-promotions .btn-primary{
  background:var(--choco); border-color:var(--choco);
}
.owner-promotions .btn-primary:hover{
  background:var(--soft-choco); border-color:var(--soft-choco);
}

/* Filter pills: outline saat idle, filled saat active */
.owner-promotions .filter-btn{
  border:1px solid var(--choco) !important;
  color:var(--choco) !important;
  border-radius:999px;
  padding:.25rem .75rem;
  transition:all .15s ease;
}
.owner-promotions .filter-btn:hover{
  background:rgba(140,16,0,.06) !important;
  color:var(--choco) !important;
  border-color:var(--choco) !important;
}
.owner-promotions .filter-btn.active{
  background:var(--choco) !important;
  color:#fff !important;
  border-color:var(--choco) !important;
  box-shadow:0 2px 8px rgba(140,16,0,.18);
}

/* Alerts */
.owner-promotions .alert{
  border-left:4px solid var(--choco); border-radius:10px;
}

/* Table container (partial-friendly) */
.owner-promotions .table-responsive{
  border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; background:#fff;
}
.owner-promotions .table{
  margin-bottom:0; background:#fff;
  border-collapse:separate; border-spacing:0;
}
.owner-promotions thead th{
  background:#fff; border-bottom:2px solid #eef1f4 !important;
  color:#374151; font-weight:700; white-space:nowrap;
}
.owner-promotions tbody td{ vertical-align:middle; }
.owner-promotions tbody tr{ transition: background-color .12s ease; }
.owner-promotions tbody tr:hover{ background: rgba(140,16,0,.04); }

/* Soft badges (kalau partial menampilkan badge) */
.owner-promotions .badge-soft-success{
  background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;
  padding:.32rem .55rem; border-radius:999px; font-weight:600;
}
.owner-promotions .badge-soft-warning{
  background:#fef3c7; color:#92400e; border:1px solid #fde68a;
  padding:.32rem .55rem; border-radius:999px; font-weight:600;
}
.owner-promotions .badge-soft-secondary{
  background:#f3f4f6; color:#374151; border:1px solid #e5e7eb;
  padding:.32rem .55rem; border-radius:999px; font-weight:600;
}

/* Action buttons kecil */
.owner-promotions .btn-group-sm .btn,
.owner-promotions .table .btn.btn-sm{
  border-radius:10px; padding:.28rem .6rem; min-width:68px;
}
/* Soft danger untuk delete */
.owner-promotions .btn-soft-danger{
  background:#fee2e2; color:#991b1b; border:1px solid #fecaca;
}
.owner-promotions .btn-soft-danger:hover{
  background:#fecaca; color:#7f1d1d; border-color:#fca5a5;
}
</style>

{{-- Delete confirm + filter logic --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deletePromo(productId) {
  Swal.fire({
    title: 'Apakah Anda yakin?',
    text: "Anda tidak dapat mengembalikan data tersebut!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#8c1000', // brand choco
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batalkan',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/owner/user-owner/promotions/${productId}`;
      form.style.display = 'none';

      const csrf = document.createElement('input');
      csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
      form.appendChild(csrf);

      const method = document.createElement('input');
      method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
      form.appendChild(method);

      document.body.appendChild(form);
      form.submit();
    }
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const filterButtons = document.querySelectorAll('.owner-promotions .filter-btn');

  filterButtons.forEach(button => {
    button.addEventListener('click', function() {
      const categoryId = this.getAttribute('data-category');

      // toggle active pill
      filterButtons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');

      const tableBody = document.querySelector('.owner-promotions tbody');
      const tableRows = document.querySelectorAll('.owner-promotions tbody tr');

      let visibleCount = 0;

      tableRows.forEach((row) => {
        if (categoryId === 'all' || row.getAttribute('data-category') === categoryId) {
          row.style.display = '';
          const firstCell = row.querySelector('td');
          if (firstCell) firstCell.textContent = (++visibleCount);
        } else {
          row.style.display = 'none';
        }
      });

      // kosong state
      const emptyRow = tableBody?.querySelector('.empty-row');
      if (emptyRow) emptyRow.remove();

      if (visibleCount === 0 && tableBody) {
        const tr = document.createElement('tr');
        tr.classList.add('empty-row');
        tr.innerHTML = `<td colspan="8" class="text-center text-muted">Data tidak ditemukan</td>`;
        tableBody.appendChild(tr);
      }
    });
  });
});
</script>
@endsection
