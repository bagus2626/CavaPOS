@extends('layouts.owner')

@section('title', __('messages.owner.products.categories.categories_list'))
@section('page_title', __('messages.owner.products.categories.categories'))

@section('content')
<section class="content">
<div class="container-fluid owner-categories"> {{-- PAGE SCOPE --}}
    <a href="{{ route('owner.user-owner.categories.create') }}" class="btn btn-primary mb-3">
        + {{ __('messages.owner.products.categories.add_category') }}
    </a>
    <button class="btn btn-warning mb-3" data-toggle="modal" data-target="#orderModal">
      {{ __('messages.owner.products.categories.category_order') }}
  </button>


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-hover align-middle">
          <thead>
              <tr>
                  <th class="w-[5%]">#</th>
                  <th class="w-[20%]">{{ __('messages.owner.products.categories.category_name') }}</th>
                  <th class="w-[35%]">{{ __('messages.owner.products.categories.description') }}</th>
                  <th class="w-[20%]">{{ __('messages.owner.products.categories.picture') }}</th>
                  <th class="w-[10%]">{{ __('messages.owner.products.categories.actions') }}</th>
              </tr>
          </thead>
          <tbody>
              @foreach($categories as $category)
                  <tr>
                      <td class="text-muted">
                        {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                      </td>
                      <td class="fw-600">{{ $category->category_name }}</td>
                      <td>{{ $category->description }}</td>

                      <td>
                          @if($category->images && isset($category->images['path']))
                              <a href="#" data-toggle="modal" data-target="#imageModal{{ $category->id }}">
                                  <img src="{{ asset($category->images['path']) }}"
                                       alt="{{ $category->category_name }}"
                                       class="thumb"
                                       loading="lazy">
                              </a>
                              @include('pages.owner.products.categories.modal')
                          @else
                              <span class="text-muted">{{ __('messages.owner.products.categories.no_pictures_yet') }}</span>
                          @endif
                      </td>

                      <td class="text-nowrap">
                          <a href="{{ route('owner.user-owner.categories.edit', $category) }}" class="btn btn-sm btn-outline-choco me-1">{{ __('messages.owner.products.categories.edit') }}</a>
                          <form action="{{ route('owner.user-owner.categories.destroy', $category) }}"
                                method="POST"
                                class="d-inline js-delete-form"
                                data-name="{{ $category->category_name }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-soft-danger">{{ __('messages.owner.products.categories.delete') }}</button>
                          </form>

                      </td>
                  </tr>
              @endforeach
          </tbody>
      </table>
    </div>

  {{-- Pagination Links --}}
  <div class="d-flex justify-content-between align-items-center">
    <div class="text-muted small">
      Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} entries
    </div>
    <div>
      {{ $categories->links() }}
    </div>
  </div>
</div>

@include('pages.owner.products.categories.category-order-modal')

</section>

<style>
/* ===== Owner › Categories (page scope) ===== */
.owner-categories{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* Title */
.owner-categories .page-title{
  font-weight:500; color:var(--ink); margin-bottom:.90rem;
}

/* Brand buttons */
.owner-categories .btn-primary{
  background:var(--choco); border-color:var(--choco);
}
.owner-categories .btn-primary:hover{
  background:var(--soft-choco); border-color:var(--soft-choco);
}
.owner-categories .btn-outline-choco{
  color:var(--choco); border:1px solid var(--choco); background:#fff;
}
.owner-categories .btn-outline-choco:hover{
  color:#fff; background:var(--choco); border-color:var(--choco);
}
.owner-categories .btn-soft-danger{
  background:#fee2e2; color:#991b1b; border:1px solid #fecaca;
}
.owner-categories .btn-soft-danger:hover{
  background:#fecaca; color:#7f1d1d; border-color:#fca5a5;
}

/* Alerts */
.owner-categories .alert{
  border-left:4px solid var(--choco); border-radius:10px;
}

/* Table */
.owner-categories .table{
  background:#fff; border-collapse:separate; border-spacing:0;
  border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow);
}
.owner-categories thead th{
  background:#fff; border-bottom:2px solid #eef1f4 !important;
  color:#374151; font-weight:700; white-space:nowrap;
}
.owner-categories tbody td{ vertical-align:middle; }
.owner-categories tbody tr{ transition: background-color .12s ease; }
.owner-categories tbody tr:hover{ background: rgba(140,16,0,.04); }

/* Thumbnail */
.owner-categories .thumb{
  width:72px; height:72px; object-fit:cover;
  border-radius:12px; border:0; box-shadow:var(--shadow);
  cursor: zoom-in;
}

/* Text utils */
.owner-categories .fw-600{ font-weight:600; }

/* Action buttons sizing */
.owner-categories td .btn.btn-sm{
  border-radius:10px; min-width:72px; padding:.28rem .6rem;
}


/* Custom Pagination Style */
  .pagination {
    margin-bottom: 1rem;
  }

  .page-link {
    color: var(--choco);
    border-color: #dee2e6;
  }

  .page-link:hover {
    color: #6b0d00;
    background-color: #f8f9fa;
    border-color: #dee2e6;
  }

  .page-item.active .page-link {
    background-color: var(--choco);
    border-color: var(--choco);
    color: white;
  }

  .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
  }

/* Support Tailwind-like width classes used in markup */
.owner-categories .w-\[5\%\]{ width:5% !important; }
.owner-categories .w-\[10\%\]{ width:10% !important; }
.owner-categories .w-\[20\%\]{ width:20% !important; }
.owner-categories .w-\[35\%\]{ width:35% !important; }

/* Small helpers */
.owner-categories .text-muted{ color:#6b7280 !important; }

/* Responsive tweak */
@media (max-width: 576px){
  .owner-categories .thumb{ width:56px; height:56px; }
}

.ui-state-highlight {
    height: 45px;
    background: #fde6e6;
    border: 2px dashed #c12814;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.js-delete-form').forEach(function(form){
    form.addEventListener('submit', function(e){
      e.preventDefault();

      const name = form.dataset.name || 'kategori ini';
      if (!window.Swal) { if (confirm(`Delete ${name}?`)) form.submit(); return; }

      Swal.fire({
        title: '{{ __('messages.owner.products.categories.delete_confirmation_1') }}',
        text: `{{ __('messages.owner.products.categories.delete_confirmation_2') }} "${name}". {{ __('messages.owner.products.categories.delete_confirmation_3') }}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '{{ __('messages.owner.products.categories.delete_confirmation_4') }}',
        cancelButtonText: '{{ __('messages.owner.products.categories.cancel') }}',
        reverseButtons: true,
        confirmButtonColor: '#8c1000',
        cancelButtonColor: '#6b7280'
      }).then((res) => {
        if (res.isConfirmed) form.submit();
      });
    });
  });
});
</script>

<script>
$(function() {

    // Simpan HTML awal list category (urutan dari server)
    let initialCategoryListHtml = $('#sortableCategoryList').html();

    function initSortable() {
        $("#sortableCategoryList").sortable({
            placeholder: "ui-state-highlight",
            axis: "y",
            // handle: ".sort-handle", // drag dari icon bars saja
        });
        $("#sortableCategoryList").disableSelection();
    }

    // Inisialisasi saat modal pertama kali ditampilkan
    $('#orderModal').on('shown.bs.modal', function () {
        initSortable();
    });

    // RESET ke urutan awal kalau modal ditutup tanpa save
    $('#orderModal').on('hidden.bs.modal', function () {
        $('#sortableCategoryList').html(initialCategoryListHtml);
    });

    // Save order
    $("#saveOrderBtn").on('click', function() {
        let orderedIDs = [];

        $("#sortableCategoryList li").each(function(index, el) {
            orderedIDs.push({
                id: $(el).data("id"),
                order: index + 1
            });
        });

        $.ajax({
            url: "{{ route('owner.user-owner.categories.reorder') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                orders: orderedIDs
            },
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved',
                    text: 'Category order updated successfully',
                    confirmButtonColor: '#8c1000'
                }).then(() => {
                    location.reload();
                });
            },
            error: function() {
                Swal.fire('Error', 'Failed to update category order!', 'error');
            }
        });

    });

});
</script>
@endpush
