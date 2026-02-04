@extends('layouts.owner')

@section('title', __('messages.owner.products.master_products.master_product_list'))
@section('page_title', __('messages.owner.products.master_products.master_products'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.master_products.master_products') }}</h1>
          <p class="page-subtitle">{{ __('messages.owner.products.master_products.manage_catalog_subtitle') }}</p>
        </div>
      </div>

      @if (session('success'))
        <div class="alert alert-success alert-modern">
          <div class="alert-icon">
            <span class="material-symbols-outlined">check_circle</span>
          </div>
          <div class="alert-content">
            {{ session('success') }}
          </div>
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger alert-modern">
          <div class="alert-icon">
            <span class="material-symbols-outlined">error</span>
          </div>
          <div class="alert-content">
            {{ session('error') }}
          </div>
        </div>
      @endif

      <div class="modern-card mb-4">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <form method="GET" action="{{ url()->current() }}" id="productFilterForm">
            <div class="table-controls">
              <div class="search-filter-group">

                <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                  <span class="input-icon">
                    <span class="material-symbols-outlined">search</span>
                  </span>

                  <input
                    type="text"
                    name="q"
                    id="productSearchInput"
                    value="{{ $q ?? request('q') }}"
                    class="form-control-modern with-icon"
                    placeholder="{{ __('messages.owner.products.master_products.search_placeholder') }}"
                    oninput="searchFilter(this, 500)"
                  >
                  <input type="hidden" name="page" id="pageInput" value="{{ request('page', 1) }}">
                </div>

                <div class="select-wrapper" style="min-width: 200px;">
                  <select name="category" class="form-control-modern" onchange="document.getElementById('productFilterForm').submit()">
                    <option value="all">{{ __('messages.owner.products.master_products.all') }}</option>
                    @foreach($categories as $category)
                      <option value="{{ $category->id }}" @selected((string)request('category') === (string)$category->id)>
                        {{ $category->category_name }}
                      </option>
                    @endforeach
                  </select>
                  <span class="material-symbols-outlined select-arrow">expand_more</span>
                </div>
              </div>

              <a href="{{ route('owner.user-owner.master-products.create') }}" class="btn-modern btn-primary-modern">
                <span class="material-symbols-outlined">add</span>
                {{ __('messages.owner.products.master_products.add_product') ?? 'Add Product' }}
              </a>
            </div>
          </form>
        </div>
      </div>


      @include('pages.owner.products.master-product.display')

    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function deleteProduct(productId) {
    Swal.fire({
      title: '{{ __('messages.owner.products.master_products.delete_confirmation_1') }}',
      text: '{{ __('messages.owner.products.master_products.delete_confirmation_2') }}',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ae1504',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '{{ __('messages.owner.products.master_products.delete_confirmation_3') }}',
      cancelButtonText: '{{ __('messages.owner.products.master_products.cancel') }}'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/owner/user-owner/master-products/${productId}`;
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

  function searchFilter(el, delay = 500) {
    const form = document.getElementById('productFilterForm');
    const pageInput = document.getElementById('pageInput');
    if (!form || !el) return;

    // reset ke halaman 1 setiap kali user mengubah input
    if (pageInput) pageInput.value = 1;

    // clear timer sebelumnya
    if (el._debounceTimer) clearTimeout(el._debounceTimer);

    // debounce: submit 500ms setelah user berhenti mengetik
    el._debounceTimer = setTimeout(() => {
      form.submit();
    }, delay);
  }
</script>
@endpush