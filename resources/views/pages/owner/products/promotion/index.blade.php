@extends('layouts.owner')

@section('title', __('messages.owner.products.promotions.promotion_list'))
@section('page_title', __('messages.owner.products.promotions.all_promotions'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.promotions.all_promotions') }}</h1>
          <p class="page-subtitle">{{ __('messages.owner.products.promotions.manage_promotions_subtitle') }}</p>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-modern">
          <div class="alert-icon">
            <span class="material-symbols-outlined">check_circle</span>
          </div>
          <div class="alert-content">
            {{ session('success') }}
          </div>
        </div>
      @endif

      @if(session('error'))
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
          <div class="table-controls">
            <div class="search-filter-group">
              <form method="GET" action="{{ route('owner.user-owner.promotions.index') }}"
                    style="display:flex; gap: var(--spacing-sm); align-items:center; flex-wrap:wrap;">

                <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                  <span class="input-icon">
                    <span class="material-symbols-outlined">search</span>
                  </span>
                  <input type="text"
                        id="searchInput"
                        name="q"
                        class="form-control-modern with-icon"
                        value="{{ request('q') }}"
                        placeholder="{{ __('messages.owner.products.promotions.search_placeholder') }}">
                </div>

                <div class="select-wrapper" style="min-width: 200px;">
                  <select id="typeFilter" name="type" class="form-control-modern">
                    <option value="">{{ __('messages.owner.products.promotions.all') }}</option>
                    <option value="percentage" @selected(request('type')==='percentage')>
                      {{ __('messages.owner.products.promotions.percentage') }}
                    </option>
                    <option value="amount" @selected(request('type')==='amount')>
                      {{ __('messages.owner.products.promotions.reduced_fare') }}
                    </option>
                  </select>
                  <span class="material-symbols-outlined select-arrow">expand_more</span>
                </div>

                <noscript>
                  <button type="submit" class="btn-modern btn-secondary-modern">
                    {{ __('messages.owner.products.promotions.filter') ?? 'Filter' }}
                  </button>
                </noscript>
              </form>
            </div>

            <a href="{{ route('owner.user-owner.promotions.create') }}" class="btn-modern btn-primary-modern">
              <span class="material-symbols-outlined">add</span>
              {{ __('messages.owner.products.promotions.add_promotion') }}
            </a>
          </div>
        </div>
      </div>

      @include('pages.owner.products.promotion.display')

    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const typeFilter  = document.getElementById('typeFilter');

      // auto submit (debounce)
      let timer;
      function submitWithParams(){
        const params = new URLSearchParams(window.location.search);
        const q = (searchInput?.value || '').trim();
        const t = (typeFilter?.value || '').trim();

        if (q) params.set('q', q); else params.delete('q');
        if (t) params.set('type', t); else params.delete('type');

        // reset page ketika filter berubah
        params.delete('page');

        window.location.search = params.toString();
      }

      if (searchInput) {
        searchInput.addEventListener('input', function(){
          clearTimeout(timer);
          timer = setTimeout(submitWithParams, 350);
        });
      }

      if (typeFilter) {
        typeFilter.addEventListener('change', submitWithParams);
      }

      // DELETE confirmation (untuk semua form delete)
      document.querySelectorAll('.js-delete-promo-form').forEach(form => {
        form.addEventListener('submit', function(e){
          e.preventDefault();
          const name = form.dataset.name || 'Promotion';

          Swal.fire({
            title: '{{ __('messages.owner.products.promotions.delete_confirmation_1') }}',
            text: `{{ __('messages.owner.products.promotions.delete_confirmation_2') }}: "${name}"`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ae1504',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '{{ __('messages.owner.products.promotions.delete_confirmation_3') }}',
            cancelButtonText: '{{ __('messages.owner.products.promotions.cancel') }}',
            reverseButtons: true
          }).then((res) => {
            if (res.isConfirmed) form.submit();
          });
        });
      });
    });
</script>
@endpush