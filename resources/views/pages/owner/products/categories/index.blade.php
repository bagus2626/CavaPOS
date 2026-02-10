@extends('layouts.owner')

@section('title', __('messages.owner.products.categories.categories_list'))
@section('page_title', __('messages.owner.products.categories.categories'))

@section('content')
<link rel="stylesheet" href="{{ asset('css/mobile-owner.css') }}">
  <div class="modern-container">
    <div class="container-modern">
      {{-- Page Header - Desktop Only --}}
      <div class="page-header only-desktop">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.categories.categories') }}</h1>
          <p class="page-subtitle">{{ __('messages.owner.products.categories.subtitle') }}</p>
        </div>
      </div>

      {{-- Mobile Header - Mobile Only --}}
      <div class="only-mobile mobile-header-card">
        <div class="mobile-header-content">
          <div class="mobile-header-left">
            <h1 class="mobile-header-title">{{ __('messages.owner.products.categories.categories') }}</h1>
            <p class="mobile-header-subtitle">{{ __('messages.owner.products.categories.subtitle') }}</p>
          </div>
        </div>

        {{-- Mobile Search Box --}}
        <div class="mobile-search-box">
          <span class="mobile-search-icon">
            <span class="material-symbols-outlined">search</span>
          </span>
          <input 
            type="text" 
            id="searchInputMobile" 
            class="mobile-search-input"
            value="{{ request('q') }}"
            placeholder="{{ __('messages.owner.products.categories.search_placeholder') }}"
          >
          <button class="mobile-filter-btn" data-toggle="modal" data-target="#orderModal">
            <span class="material-symbols-outlined">swap_vert</span>
          </button>
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

      {{-- Search & Filter Card - Desktop Only --}}
      <div class="modern-card mb-4 only-desktop">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <div class="table-controls">
            <div class="search-filter-group">
              <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                <span class="input-icon">
                  <span class="material-symbols-outlined">search</span>
                </span>
                <input type="text" id="searchInput"
                  class="form-control-modern with-icon"
                  value="{{ request('q') }}"
                  placeholder="{{ __('messages.owner.products.categories.search_placeholder') }}">
              </div>
            </div>

            <div style="display: flex; gap: var(--spacing-sm);">
              <button class="btn-modern btn-secondary-modern" data-toggle="modal" data-target="#orderModal">
                <span class="material-symbols-outlined">swap_vert</span>
                {{ __('messages.owner.products.categories.category_order') }}
              </button>
              
              <a href="{{ route('owner.user-owner.categories.create') }}" class="btn-modern btn-primary-modern">
                <span class="material-symbols-outlined">add</span>
                {{ __('messages.owner.products.categories.add_category') }}
              </a>
            </div>
          </div>
        </div>
      </div>

      @include('pages.owner.products.categories.display')

    </div>
  </div>

  {{-- Floating Add Button - Mobile Only --}}
  <a href="{{ route('owner.user-owner.categories.create') }}" class="btn-add-outlet-mobile">
    <span class="material-symbols-outlined">add</span>
  </a>

  @include('pages.owner.products.categories.category-order-modal')

@endsection

<style>
/* Hide desktop elements on mobile */
@media (max-width: 768px) {
  .only-desktop {
    display: none !important;
  }
}

/* Hide mobile elements on desktop */
@media (min-width: 769px) {
  .only-mobile {
    display: none !important;
  }
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

{{-- Category Order Modal Script --}}
<script>
// ==========================================
// CATEGORY ORDER MODAL
// ==========================================
$(function() {
    let initialCategoryListHtml = $('#sortableCategoryList').html();

    function initSortable() {
        $("#sortableCategoryList").sortable({
            placeholder: "ui-state-highlight",
            axis: "y",
        });
        $("#sortableCategoryList").disableSelection();
    }

    $('#orderModal').on('shown.bs.modal', function () {
        initSortable();
    });

    $('#orderModal').on('hidden.bs.modal', function () {
        $('#sortableCategoryList').html(initialCategoryListHtml);
    });

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
                    title: '{{ __('messages.owner.products.categories.saved') }}',
                    text: '{{ __('messages.owner.products.categories.order_updated_success') }}',
                    confirmButtonColor: '#ae1504'
                }).then(() => {
                    location.reload();
                });
            },
            error: function() {
                Swal.fire(
                    '{{ __('messages.owner.products.categories.error_title') }}',
                    '{{ __('messages.owner.products.categories.order_updated_error') }}',
                    'error'
                );
            }
        });
    });
});
</script>

@php
  $pageCategories = $categories->map(function($c){
    return [
      'id' => $c->id,
      'category_name' => $c->category_name,
      'description' => $c->description,
      'has_image' => $c->images && isset($c->images['path']),
      'image_path' => ($c->images && isset($c->images['path'])) ? $c->images['path'] : null,
    ];
  })->values();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
  applyDesktopSearch('');
  const searchInput = document.getElementById('searchInput');
  const searchInputMobile = document.getElementById('searchInputMobile');
  const mobileList = document.getElementById('categoryMobileList');

  const pageCategories = @json($pageCategories);

  // Fungsi search yang sama untuk desktop dan mobile
  function handleSearch(input) {
    if (!input) return;

    let timer;
    input.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(() => {
        const params = new URLSearchParams(window.location.search);

        const q = (input.value || '').trim();
        if (q) params.set('q', q);
        else params.delete('q');

        // reset page ke 1 saat keyword berubah
        params.delete('page');

        window.location.search = params.toString();
      }, 300);
    });
  }

  // Attach event ke kedua input
  handleSearch(searchInput);
  handleSearch(searchInputMobile);

  function escapeHtml(str){
    return String(str ?? '')
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'",'&#039;');
  }

  function emptyStateHtml(){
    return `
      <div class="table-empty-state" style="padding: 20px;">
        <span class="material-symbols-outlined">search_off</span>
        <h4>{{ __('messages.owner.products.categories.no_results') }}</h4>
        <p>{{ __('messages.owner.products.categories.adjust_search') }}</p>
      </div>
    `;
  }

  function attachDeleteHandlers(selector) {
    document.querySelectorAll(selector).forEach(form => {
      if (form._bound) return;
      form._bound = true;

      form.addEventListener('submit', function(e){
        e.preventDefault();

        const name =
          form.dataset.name ||
          '{{ __('messages.owner.products.categories.categories') }}';

        if (!window.Swal) {
          if (confirm(`Delete "${name}" ?`)) form.submit();
          return;
        }

        Swal.fire({
          title: '{{ __('messages.owner.products.categories.delete_confirmation_1') }}',
          text: `{{ __('messages.owner.products.categories.delete_confirmation_2') }} "${name}". {{ __('messages.owner.products.categories.delete_confirmation_3') }}`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: '{{ __('messages.owner.products.categories.delete_confirmation_4') }}',
          cancelButtonText: '{{ __('messages.owner.products.categories.cancel') }}',
          reverseButtons: true,
          confirmButtonColor: '#ae1504',
          cancelButtonColor: '#6c757d'
        }).then((res) => {
          if (res.isConfirmed) form.submit();
        });
      });
    });
  }
  attachDeleteHandlers('.js-delete-form');

  function renderMobile(list){
    if (!mobileList) return;
    mobileList.innerHTML = '';

    if (!list.length){
      mobileList.innerHTML = emptyStateHtml();
      return;
    }

    list.forEach(c => {
      const imgHtml = (c.has_image && c.image_path)
        ? `<img src="{{ asset('') }}${escapeHtml(c.image_path)}" alt="${escapeHtml(c.category_name)}" loading="lazy">`
        : `<span class="material-symbols-outlined" style="opacity:.4;">image</span>`;

      const editUrl = `/owner/user-owner/categories/${c.id}/edit`;
      const deleteUrl = `/owner/user-owner/categories/${c.id}`;

      const card = document.createElement('div');
      card.className = 'category-card';
      card.innerHTML = `
        <div class="category-card__top">
          <div class="category-card__thumb">${imgHtml}</div>
          <div class="category-card__meta">
            <div class="category-card__name">${escapeHtml(c.category_name || '')}</div>
            <div class="category-card__desc">${escapeHtml(c.description || '')}</div>
          </div>
        </div>

        <div class="category-card__bottom">
          <span class="category-card__badge">
            <span class="material-symbols-outlined">category</span>
            <span>${escapeHtml(c.category_name || '')}</span>
          </span>

          <div class="category-card__actions">
            <a href="${editUrl}" class="btn-card-action">
              <span class="material-symbols-outlined">edit</span>
              <span>{{ __('messages.owner.products.categories.edit') }}</span>
            </a>

            <form action="${deleteUrl}" method="POST"
              class="js-delete-form-mobile"
              data-name="${escapeHtml(c.category_name)}"
              style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn-card-action danger">
                <span class="material-symbols-outlined">delete</span>
                <span>{{ __('messages.owner.products.categories.delete') }}</span>
              </button>
            </form>
          </div>
        </div>
      `;
      mobileList.appendChild(card);
    });

    attachDeleteHandlers('.js-delete-form-mobile');
  }

  // Render awal
  renderMobile(pageCategories);

  // Search hanya untuk data halaman ini (client-side untuk instant feedback)
  function handleInstantSearch(input) {
    if (!input) return;
    
    input.addEventListener('input', function(){
      const q = (input.value || '').toLowerCase().trim();

      // mobile (render ulang)
      const filtered = pageCategories.filter(c => {
        const hay = `${c.category_name || ''} ${c.description || ''}`.toLowerCase();
        return !q || hay.includes(q);
      });
      renderMobile(filtered);

      // desktop (filter tr tanpa render ulang)
      applyDesktopSearch(q);
    });
  }

  handleInstantSearch(searchInput);
  handleInstantSearch(searchInputMobile);
});

function applyDesktopSearch(q){
  const rows = document.querySelectorAll('#categoryTableBody tr.table-row');
  if (!rows.length) return;

  const keyword = (q || '').toLowerCase().trim();

  rows.forEach(row => {
    const name = row.querySelector('td:nth-child(2)')?.innerText || '';
    const desc = row.querySelector('td:nth-child(4)')?.innerText || '';
    const hay = (name + ' ' + desc).toLowerCase();

    row.style.display = (!keyword || hay.includes(keyword)) ? '' : 'none';
  });
}
</script>

@endpush