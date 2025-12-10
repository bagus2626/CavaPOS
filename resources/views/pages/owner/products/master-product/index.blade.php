@extends('layouts.owner')

@section('title', __('messages.owner.products.master_products.master_product_list'))
@section('page_title', __('messages.owner.products.master_products.master_products'))

@section('content')

{{-- ===== Delete confirm (tetap) ===== --}}
<script>
function deleteProduct(productId) {
  Swal.fire({
      title: '{{ __('messages.owner.products.master_products.delete_confirmation_1') }}',
      text: "{{ __('messages.owner.products.master_products.delete_confirmation_2') }}",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#8c1000',
      cancelButtonColor: '#9CA3AF',
      confirmButtonText: '{{ __('messages.owner.products.master_products.delete_confirmation_3') }}',
      cancelButtonText: '{{ __('messages.owner.products.master_products.cancel') }}'
  }).then((result) => {
      if (result.isConfirmed) {
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = `/owner/user-owner/master-products/${productId}`;
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
  <div class="container-fluid owner-products-index"> {{-- PAGE SCOPE --}}

    {{-- Toolbar atas --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
      @php
        $currentCategory = $categoryId ?? null; // dari controller
      @endphp

      <div class="filter-toolbar d-flex flex-wrap gap-2">
        {{-- All --}}
        <a href="{{ route('owner.user-owner.master-products.index') }}"
          class="btn btn-outline-choco btn-sm filter-btn rounded-pill {{ empty($currentCategory) || $currentCategory === 'all' ? 'active' : '' }}">
          <i class="fas fa-list-ul me-1"></i> {{ __('messages.owner.products.master_products.all') }}
        </a>

        {{-- Per kategori --}}
        @foreach($categories as $category)
          <a href="{{ route('owner.user-owner.master-products.index', ['category' => $category->id]) }}"
            class="btn btn-outline-choco btn-sm filter-btn rounded-pill {{ (string)$currentCategory === (string)$category->id ? 'active' : '' }}">
            {{ $category->category_name }}
          </a>
        @endforeach
      </div>


      <a href="{{ route('owner.user-owner.master-products.create') }}" class="btn btn-choco">
        <i class="fas fa-plus me-2"></i>{{ __('messages.owner.products.master_products.add_product') }}
      </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
      <div class="alert alert-success mb-3">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      </div>
    @endif

    {{-- Card pembungkus tabel --}}
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        @include('pages.owner.products.master-product.display')
      </div>
      <div class="card-footer bg-white border-0">
        <div class="d-flex justify-content-end">
          {{ $products->withQueryString()->links() }}
        </div>
      </div>
    </div>


  </div>
</section>

<style>
/* ===== Master Products (page scope) ===== */
.owner-products-index{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* Brand buttons (konsisten) */
.owner-products-index .btn-choco{
  background:var(--choco); border-color:var(--choco); color:#fff;
  border-radius:10px; padding:.5rem .9rem; font-weight:600;
}
.owner-products-index .btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); color:#fff; }

.owner-products-index .btn-outline-choco{
  color:var(--choco); border-color:var(--choco);
  border-radius:999px; font-weight:600;
}
.owner-products-index .btn-outline-choco:hover,
.owner-products-index .filter-btn.active{
  color:#fff; background:var(--choco); border-color:var(--choco);
}

/* Alerts tone */
.owner-products-index .alert{
  border-left:4px solid var(--choco);
  border-radius:12px; box-shadow:var(--shadow);
}
.owner-products-index .alert-success{
  background:#f0fdf4; border-color:#dcfce7; color:#166534;
}

/* Card */
.owner-products-index .card{ border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }

/* ===== Tabel di dalam page ini (apapun markup partial-nya) ===== */
.owner-products-index .table{
  margin-bottom:0; background:#fff;
  border-collapse:separate; border-spacing:0;
}
.owner-products-index .table thead th{
  background:#fff; color:#374151; font-weight:700;
  border-bottom:2px solid #eef1f4 !important;
  white-space:nowrap;
}
.owner-products-index .table tbody td{ vertical-align:middle; }
.owner-products-index .table tbody tr{ transition: background-color .12s ease; }
.owner-products-index .table tbody tr:hover{ background: rgba(140,16,0,.04); }

/* Avatar/gambar kecil di tabel (jika ada) */
.owner-products-index .avatar-48{
  width:48px; height:48px; object-fit:cover; border-radius:12px; border:0; box-shadow:var(--shadow);
}

/* Badge “soft” (aktif/nonaktif/tersedia) */
.owner-products-index .badge-soft-success{
  background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; border-radius:999px; font-weight:600; padding:.3rem .55rem;
}
.owner-products-index .badge-soft-secondary{
  background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:999px; font-weight:600; padding:.3rem .55rem;
}
.owner-products-index .badge-soft-warning{
  background:#fffbeb; color:#92400e; border:1px solid #fcd34d; border-radius:999px; font-weight:600; padding:.3rem .55rem;
}

/* Link ink */
.owner-products-index .link-ink{ color:#374151; text-decoration:none; }
.owner-products-index .link-ink:hover{ color:var(--choco); }

/* Actions: rapikan tombol di dalam tabel (baik .btn-group maupun tidak) */
.owner-products-index td .btn-group.btn-group-sm{
  display:inline-flex; gap:.4rem;
}
.owner-products-index td .btn-group.btn-group-sm > .btn{
  border-radius:10px !important; padding:.28rem .6rem; min-width:72px; line-height:1.25;
}
.owner-products-index td .btn + .btn{ margin-left:0 !important; }

/* Varian tombol tabel */
.owner-products-index .btn-outline-choco.btn-sm{
  padding:.28rem .6rem; min-width:72px;
}
.owner-products-index .btn-soft-danger{
  background:#fee2e2; color:#991b1b; border-color:#fecaca; border-radius:10px;
}
.owner-products-index .btn-soft-danger:hover{
  background:#fecaca; color:#7f1d1d; border-color:#fca5a5;
}

/* Tombol filter: space & focus ring */
.owner-products-index .filter-toolbar .filter-btn{
  padding:.3rem .7rem; box-shadow:none; transition:.15s ease;
}
.owner-products-index .filter-toolbar .filter-btn:focus{
  box-shadow:0 0 0 .2rem rgba(140,16,0,.15);
}

/* ===== Pagination Choco Style ===== */
.owner-products-index .pagination {
    margin: 0;
}

.owner-products-index .pagination .page-item .page-link {
    color: var(--choco);
    border-radius: 8px;
    padding: .45rem .8rem;
    font-weight: 600;
    border: 1px solid #e5e7eb;
    transition: all .15s ease;
}

.owner-products-index .pagination .page-item .page-link:hover {
    background-color: var(--choco);
    color: #fff;
    border-color: var(--choco);
}

.owner-products-index .pagination .page-item.active .page-link {
    background-color: var(--choco);
    border-color: var(--choco);
    color: #fff;
}

.owner-products-index .pagination .page-item.disabled .page-link {
    color: #9ca3af !important;
    background-color: #f3f4f6;
    border-color: #e5e7eb;
    cursor: not-allowed;
}

</style>
@endsection
