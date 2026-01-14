@extends('layouts.owner')

@section('title', __('messages.owner.products.categories.categories_list'))
@section('page_title', __('messages.owner.products.categories.categories'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.categories.categories') }}</h1>
          <p class="page-subtitle">{{ __('messages.owner.products.categories.subtitle') }}</p>
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
              <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                <span class="input-icon">
                  <span class="material-symbols-outlined">search</span>
                </span>
                <input type="text" id="searchInput" class="form-control-modern with-icon"
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

  @include('pages.owner.products.categories.category-order-modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

<script>
// ==========================================
// CATEGORY INDEX - SEARCH & PAGINATION
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('searchInput');
  const tableBody = document.getElementById('categoryTableBody');
  const paginationWrapper = document.querySelector('.table-pagination');

  if (!tableBody) {
    console.error('Table body not found');
    return;
  }

  // Ambil semua data dari Blade
  const allCategoriesData = @json($allCategoriesFormatted ?? []);
  
  let filteredCategories = [...allCategoriesData];
  const itemsPerPage = 10;
  let currentPage = 1;

  // ==========================================
  // FILTER FUNCTION
  // ==========================================
  function filterCategories() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

    filteredCategories = allCategoriesData.filter(category => {
      // Search: cari di category_name, description
      const searchText = `
        ${category.category_name || ''} 
        ${category.description || ''}
      `.toLowerCase();
      
      const matchesSearch = !searchTerm || searchText.includes(searchTerm);

      return matchesSearch;
    });

    currentPage = 1; // Reset ke halaman pertama
    renderTable();
  }

  // ==========================================
  // RENDER TABLE
  // ==========================================
  function renderTable() {
    // Hitung pagination
    const totalPages = Math.ceil(filteredCategories.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const currentCategories = filteredCategories.slice(startIndex, endIndex);

    // Clear table
    tableBody.innerHTML = '';

    // Render rows
    if (currentCategories.length === 0) {
      tableBody.innerHTML = `
        <tr class="empty-filter-row">
          <td colspan="5" class="text-center">
            <div class="table-empty-state">
              <span class="material-symbols-outlined">search_off</span>
              <h4>{{ __('messages.owner.products.categories.no_results') }}</h4>
              <p>{{ __('messages.owner.products.categories.adjust_search') }}</p>
            </div>
          </td>
        </tr>
      `;
    } else {
      currentCategories.forEach((category, index) => {
        const rowNumber = startIndex + index + 1;
        const row = createCategoryRow(category, rowNumber);
        tableBody.appendChild(row);
      });
    }

    // Handle pagination visibility
    if (paginationWrapper) {
      if (filteredCategories.length <= itemsPerPage) {
        paginationWrapper.style.display = 'none';
      } else {
        paginationWrapper.style.display = '';
        renderPagination(totalPages);
      }
    }
  }

  // ==========================================
  // CREATE CATEGORY ROW
  // ==========================================
  function createCategoryRow(category, rowNumber) {
    const tr = document.createElement('tr');
    tr.className = 'table-row';

    // Image display
    let imageHtml = '';
    if (category.has_image && category.image_path) {
      imageHtml = `
        <a href="#" data-toggle="modal" data-target="#imageModal${category.id}">
          <img src="{{ asset('') }}${category.image_path}" 
               alt="${category.category_name}"
               class="table-image" 
               loading="lazy">
        </a>
      `;
    } else {
      imageHtml = `
        <span class="text-muted" style="font-size: 0.875rem;">
          {{ __('messages.owner.products.categories.no_pictures_yet') }}
        </span>
      `;
    }

    // URLs
    const editUrl = `/owner/user-owner/categories/${category.id}/edit`;
    const deleteUrl = `/owner/user-owner/categories/${category.id}`;

    tr.innerHTML = `
      <td class="text-center text-muted">${rowNumber}</td>
      <td><span class="fw-600">${category.category_name}</span></td>
      <td class="text-center">${imageHtml}</td>
      <td><span class="text-secondary">${category.description || ''}</span></td>
      <td class="text-center">
        <div class="table-actions">
          <a href="${editUrl}"
             class="btn-table-action edit"
             title="{{ __('messages.owner.products.categories.edit') }}">
            <span class="material-symbols-outlined">edit</span>
          </a>
          <form action="${deleteUrl}" method="POST"
                class="d-inline js-delete-form" 
                data-name="${category.category_name}"
                style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-table-action delete"
                    title="{{ __('messages.owner.products.categories.delete') }}">
              <span class="material-symbols-outlined">delete</span>
            </button>
          </form>
        </div>
      </td>
    `;

    return tr;
  }

  // ==========================================
  // RENDER PAGINATION
  // ==========================================
  function renderPagination(totalPages) {
    if (!paginationWrapper) return;

    paginationWrapper.innerHTML = '';

    const nav = document.createElement('nav');
    nav.setAttribute('role', 'navigation');
    nav.setAttribute('aria-label', 'Pagination Navigation');
    
    const ul = document.createElement('ul');
    ul.className = 'pagination';

    // Previous Button
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    
    if (currentPage === 1) {
      prevLi.innerHTML = `
        <span class="page-link" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
            <path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/>
          </svg>
        </span>
      `;
    } else {
      prevLi.innerHTML = `
        <a href="#" class="page-link" data-page="${currentPage - 1}" aria-label="Previous">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
            <path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/>
          </svg>
        </a>
      `;
    }
    ul.appendChild(prevLi);

    // Page Numbers
    for (let i = 1; i <= totalPages; i++) {
      if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
        const pageLi = document.createElement('li');
        pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
        
        if (i === currentPage) {
          pageLi.innerHTML = `<span class="page-link" aria-current="page">${i}</span>`;
        } else {
          pageLi.innerHTML = `<a href="#" class="page-link" data-page="${i}">${i}</a>`;
        }
        
        ul.appendChild(pageLi);
      } else if (i === currentPage - 2 || i === currentPage + 2) {
        const dotsLi = document.createElement('li');
        dotsLi.className = 'page-item disabled';
        dotsLi.innerHTML = `<span class="page-link">...</span>`;
        ul.appendChild(dotsLi);
      }
    }

    // Next Button
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    
    if (currentPage === totalPages) {
      nextLi.innerHTML = `
        <span class="page-link" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
            <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
          </svg>
        </span>
      `;
    } else {
      nextLi.innerHTML = `
        <a href="#" class="page-link" data-page="${currentPage + 1}" aria-label="Next">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
            <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
          </svg>
        </a>
      `;
    }
    ul.appendChild(nextLi);

    nav.appendChild(ul);
    paginationWrapper.appendChild(nav);

    // Add click handlers
    nav.querySelectorAll('a.page-link[data-page]').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const page = parseInt(this.dataset.page);
        if (page > 0 && page <= totalPages && page !== currentPage) {
          currentPage = page;
          renderTable();
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      });
    });
  }

  // ==========================================
  // EVENT LISTENERS
  // ==========================================
  if (searchInput) {
    searchInput.addEventListener('input', filterCategories);
  }

  // ==========================================
  // DELETE CONFIRMATION (Re-attach after render)
  // ==========================================
  function attachDeleteHandlers() {
    document.querySelectorAll('.js-delete-form').forEach(function(form){
      form.addEventListener('submit', function(e){
        e.preventDefault();

        const name = form.dataset.name || '{{ __('messages.owner.products.categories.categories') }}';
        if (!window.Swal) { 
          if (confirm(`Delete ${name}?`)) form.submit(); 
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

  // Observer untuk re-attach delete handlers setelah render
  const observer = new MutationObserver(function() {
    attachDeleteHandlers();
  });

  observer.observe(tableBody, { childList: true });

  // ==========================================
  // INITIALIZE
  // ==========================================
  renderTable();
  attachDeleteHandlers();
});
</script>

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
@endpush