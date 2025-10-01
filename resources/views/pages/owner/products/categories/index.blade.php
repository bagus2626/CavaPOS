@extends('layouts.owner')

@section('content')
<div class="container owner-categories mt-4"> {{-- PAGE SCOPE --}}
    <h1 class="page-title">Categories</h1>
    <a href="{{ route('owner.user-owner.categories.create') }}" class="btn btn-primary mb-3">
        + Add Category
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-hover align-middle">
          <thead>
              <tr>
                  <th class="w-[5%]">#</th>
                  <th class="w-[20%]">Name</th>
                  <th class="w-[35%]">Description</th>
                  <th class="w-[20%]">Picture</th>
                  <th class="w-[10%]">Actions</th>
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
                              <span class="text-muted">Belum ada gambar</span>
                          @endif
                      </td>

                      <td class="text-nowrap">
                          <a href="{{ route('owner.user-owner.categories.edit', $category) }}" class="btn btn-sm btn-outline-choco me-1">Edit</a>
                          <form action="{{ route('owner.user-owner.categories.destroy', $category) }}"
                                method="POST"
                                class="d-inline js-delete-form"
                                data-name="{{ $category->category_name }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-soft-danger">Delete</button>
                          </form>

                      </td>
                  </tr>
              @endforeach
          </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $categories->links() }}
    </div>
</div>

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

/* Pagination (Laravel) */
.owner-categories .pagination{ gap:.35rem; }
.owner-categories .page-link{
  color:var(--choco); border:1px solid #e5e7eb; border-radius:999px;
}
.owner-categories .page-item.active .page-link{
  background:var(--choco); border-color:var(--choco); color:#fff;
}
.owner-categories .page-link:hover{
  color:#fff; background:var(--choco); border-color:var(--choco);
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
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.js-delete-form').forEach(function(form){
    form.addEventListener('submit', function(e){
      e.preventDefault();

      const name = form.dataset.name || 'kategori ini';
      if (!window.Swal) { if (confirm(`Delete ${name}?`)) form.submit(); return; }

      Swal.fire({
        title: 'Hapus kategori?',
        text: `Anda akan menghapus "${name}". Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        confirmButtonColor: '#8c1000', // choco
        cancelButtonColor: '#6b7280'
      }).then((res) => {
        if (res.isConfirmed) form.submit();
      });
    });
  });
});
</script>
@endpush
