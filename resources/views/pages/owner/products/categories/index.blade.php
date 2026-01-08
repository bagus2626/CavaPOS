@extends('layouts.owner')

@section('title', __('messages.owner.products.categories.categories_list'))
@section('page_title', __('messages.owner.products.categories.categories'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.categories.categories') }}</h1>
          <p class="page-subtitle">Manage your product categories</p>
        </div>
      </div>

      <!-- Success/Error Messages -->
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

      <!-- Filters & Actions -->
      <div class="modern-card mb-4">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <div class="table-controls">
            <!-- Search & Filter -->
            <div class="search-filter-group">
              <!-- Search -->
              <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                <span class="input-icon">
                  <span class="material-symbols-outlined">search</span>
                </span>
                <input type="text" id="searchInput" class="form-control-modern with-icon"
                  placeholder="Search categories...">
              </div>
            </div>

            <!-- Action Buttons -->
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

      <!-- Table Display -->
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
// CATEGORY INDEX - SEARCH & FILTER
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('searchInput');
  const tableBody = document.getElementById('categoryTableBody');

  if (!tableBody) return;

  const rows = tableBody.querySelectorAll('tr.table-row');

  // ==========================================
  // FILTER FUNCTION
  // ==========================================
  function filterTable() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';

    let visibleCount = 0;

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      const matchesSearch = !searchTerm || text.includes(searchTerm);

      if (matchesSearch) {
        row.style.display = '';
        visibleCount++;

        // Update row number
        const firstCell = row.querySelector('td:first-child');
        if (firstCell) {
          firstCell.textContent = visibleCount;
        }
      } else {
        row.style.display = 'none';
      }
    });

    // Handle empty state
    handleEmptyState(visibleCount);
  }

  // ==========================================
  // EMPTY STATE HANDLER
  // ==========================================
  function handleEmptyState(visibleCount) {
    const existingEmptyRow = tableBody.querySelector('.empty-filter-row');
    if (existingEmptyRow) {
      existingEmptyRow.remove();
    }

    if (visibleCount === 0 && rows.length > 0) {
      const emptyRow = document.createElement('tr');
      emptyRow.classList.add('empty-filter-row');
      emptyRow.innerHTML = `
        <td colspan="4" class="text-center">
          <div class="table-empty-state">
            <span class="material-symbols-outlined">search_off</span>
            <h4>No results found</h4>
            <p>Try adjusting your search</p>
          </div>
        </td>
      `;
      tableBody.appendChild(emptyRow);
    }
  }

  // ==========================================
  // EVENT LISTENERS
  // ==========================================
  if (searchInput) {
    searchInput.addEventListener('input', filterTable);
  }

  // ==========================================
  // DELETE CONFIRMATION
  // ==========================================
  document.querySelectorAll('.js-delete-form').forEach(function(form){
    form.addEventListener('submit', function(e){
      e.preventDefault();

      const name = form.dataset.name || 'kategori ini';
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
                    title: 'Saved',
                    text: 'Category order updated successfully',
                    confirmButtonColor: '#ae1504'
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