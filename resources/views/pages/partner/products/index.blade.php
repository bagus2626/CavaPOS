@extends('layouts.partner')

@section('title', __('messages.partner.product.all_product.product_list'))
@section('page_title', __('messages.partner.product.all_product.all_products'))

@section('content')
@vite(['resources/css/app.css'])

<section class="content product-index">
  <div class="container-fluid">

    {{-- <a href="{{ route('partner.products.create') }}" class="btn btn-choco mb-3">
      <i class="fas fa-plus-circle mr-2"></i>Add Product
    </a> --}}

    <div class="filter-group mb-3">
      {{-- tombol "Semua" --}}
      <a
        href="{{ route('partner.products.index') }}"
        class="btn btn-outline-choco btn-sm filter-btn rounded-pill {{ !$categoryId ? 'active' : '' }}"
      >
        {{ __('messages.partner.product.all_product.all') }}
      </a>

      @foreach($categories as $category)
        <a
          href="{{ route('partner.products.index', ['category' => $category->id]) }}"
          class="btn btn-outline-choco btn-sm filter-btn rounded-pill
                {{ (string)$categoryId === (string)$category->id ? 'active' : '' }}"
        >
          {{ $category->category_name }}
        </a>
      @endforeach
    </div>


    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
      <div class="card-body product-table-wrapper">
        @include('pages.partner.products.display')
      </div>
    </div>

    {{-- pagination --}}
    @if($products->hasPages())
      <div class="mt-3">
        {{ $products->links() }}
      </div>
    @endif

  </div>
</section>

<style>
    /* ==== Product List (page scope) ==== */
:root{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* container */
.product-index .card{ border:0; border-radius: var(--radius); box-shadow: var(--shadow); }
.product-index .card-body{ padding: 1rem 1rem; }

/* alerts seragam */
.product-index .alert{ border-left:4px solid var(--choco); border-radius:10px; }
.product-index .alert-success{ background:#f0fdf4; border-color:#dcfce7; color:#166534; }

/* filter pills */
.product-index .filter-group{ display:flex; flex-wrap:wrap; gap:.5rem; }
.btn-outline-choco{
  color: var(--choco);
  border-color: var(--choco);
}
.btn-outline-choco:hover{
  color:#fff; background: var(--choco); border-color: var(--choco);
}
.btn-choco{ background: var(--choco); border-color: var(--choco); color:#fff; }
.btn-choco:hover{ background: var(--soft-choco); border-color: var(--soft-choco); }

.product-index .filter-btn.active{
  color:#fff; background: var(--choco); border-color: var(--choco);
}

/* table polish (berlaku ke tabel di dalam wrapper) */
.product-table-wrapper .table{
  border-collapse: separate;
  border-spacing: 0;
  background:#fff;
  overflow:hidden;
  border-radius: 10px;
}
.product-table-wrapper .table thead th{
  background:#fff;
  border-bottom: 2px solid #eef1f4 !important;
  color:#374151; font-weight:700;
}
.product-table-wrapper .table tbody td{
  vertical-align: middle;
}
.product-table-wrapper .table tbody tr{
  transition: background-color .12s ease;
}
.product-table-wrapper .table tbody tr:hover{
  background: rgba(140,16,0,.04);
}

/* thumb images (kalau partial menampilkan gambar) */
.product-table-wrapper .img-thumbnail{
  border:0; border-radius: 10px; box-shadow: var(--shadow);
}

/* badge fallback */
.product-table-wrapper .badge{
  padding: .38rem .6rem; border-radius: 999px; font-weight:600;
}

/* kosong */
.product-table-wrapper .empty-row td{
  background: #fafafa;
  border-bottom: 0 !important;
}

/* util */
.rounded-pill{ border-radius: 999px; }

/* === Custom Choco Pagination === */
.pagination {
    display: flex;
    gap: 6px;
}

.pagination .page-item .page-link {
    border-radius: 8px;
    padding: 6px 12px;
    color: var(--choco) !important;
    border: 1px solid var(--choco);
    background: transparent;
    font-weight: 600;
    transition: .15s ease;
}

.pagination .page-item .page-link:hover {
    background: var(--choco);
    color: #fff !important;
}

.pagination .page-item.active .page-link {
    background: var(--choco) !important;
    border-color: var(--choco) !important;
    color: #fff !important;
}

.pagination .page-item.disabled .page-link {
    opacity: .4;
    cursor: not-allowed;
    border-color: #ddd;
    color: #aaa !important;
}

.pagination .page-item .page-link {
    border-radius: 999px;
}


</style>
@endsection

@push('scripts')
@vite(['resources/js/app.js'])

  {{-- SweetAlert2 sudah ada di layout; kalau belum, tetap aman --}}
  <script>
    function deleteProduct(productId) {
      const swal = window.$swal || window.Swal;
      swal.fire({
        title: '{{ __('messages.partner.product.all_product.delete_confirmation_1') }}',
        text: '{{ __('messages.partner.product.all_product.delete_confirmation_2') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '{{ __('messages.partner.product.all_product.delete_confirmation_3') }}',
        cancelButtonText: '{{ __('messages.partner.product.all_product.delete_confirmation_4') }}'
      }).then((result) => {
        if (!result.isConfirmed) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/partner/products/${productId}`;
        form.style.display = 'none';

        form.innerHTML = `
          @csrf
          <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
      });
    }

    document.addEventListener('DOMContentLoaded', function() {
      const filterButtons = document.querySelectorAll('.filter-btn');
      const table = document.querySelector('.product-table-wrapper table');
      const tableBody = table ? table.querySelector('tbody') : null;
      const tableRows = table ? table.querySelectorAll('tbody tr') : [];
      const colCount = table ? (table.querySelector('thead tr')?.children.length || 1) : 1;

      filterButtons.forEach(button => {
        button.addEventListener('click', function() {
          const categoryId = this.getAttribute('data-category');

          // state aktif
          filterButtons.forEach(btn => btn.classList.remove('active'));
          this.classList.add('active');

          if (!table) return;

          let visibleCount = 0;

          tableRows.forEach(row => {
            if (categoryId === 'all' || row.getAttribute('data-category') === categoryId) {
              row.style.display = '';
              visibleCount++;
              // update nomor urut di kolom pertama
              const firstCell = row.querySelector('td');
              if (firstCell) firstCell.textContent = visibleCount;
            } else {
              row.style.display = 'none';
            }
          });

          // bersihkan row kosong lama
          const emptyRow = tableBody?.querySelector('.empty-row');
          if (emptyRow) emptyRow.remove();

          // jika kosong, tampilkan pesan
          if (tableBody && visibleCount === 0) {
            const tr = document.createElement('tr');
            tr.classList.add('empty-row');
            tr.innerHTML = `<td colspan="${colCount}" class="text-center text-muted py-4">Data tidak ditemukan</td>`;
            tableBody.appendChild(tr);
          }
        });
      });
    });
  </script>
@endpush
