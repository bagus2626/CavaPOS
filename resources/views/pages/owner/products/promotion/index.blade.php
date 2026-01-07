@extends('layouts.owner')

@section('title', __('messages.owner.products.promotions.promotion_list'))
@section('page_title', __('messages.owner.products.promotions.all_promotions'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.promotions.all_promotions') }}</h1>
          <p class="page-subtitle">Manage your promotional campaigns and discount codes</p>
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
                  placeholder="Search promotions...">
              </div>

              <!-- Filter by Type -->
              <div class="select-wrapper" style="min-width: 200px;">
                <select id="typeFilter" class="form-control-modern">
                  <option value="">{{ __('messages.owner.products.promotions.all') }}</option>
                  @foreach($promotions->pluck('promotion_type')->unique() as $type)
                    <option value="{{ $type }}">
                      @if($type == 'percentage')
                        {{ __('messages.owner.products.promotions.percentage') }}
                      @else
                        {{ __('messages.owner.products.promotions.reduced_fare') }}
                      @endif
                    </option>
                  @endforeach
                </select>
                <span class="material-symbols-outlined select-arrow">expand_more</span>
              </div>
            </div>

            <!-- Add Promotion Button -->
            <a href="{{ route('owner.user-owner.promotions.create') }}" class="btn-modern btn-primary-modern">
              <span class="material-symbols-outlined">add</span>
              {{ __('messages.owner.products.promotions.add_promotion') }}
            </a>
          </div>
        </div>
      </div>

      <!-- Table Display -->
      @include('pages.owner.products.promotion.display')

    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // ==========================================
    // PROMOTION INDEX - SEARCH & FILTER
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const typeFilter = document.getElementById('typeFilter');
      const tableBody = document.getElementById('promotionTableBody');

      if (!tableBody) return;

      const rows = tableBody.querySelectorAll('tr.table-row');

      // ==========================================
      // FILTER FUNCTION
      // ==========================================
      function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedType = typeFilter ? typeFilter.value : '';

        let visibleCount = 0;

        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const type = row.dataset.category;

          const matchesSearch = !searchTerm || text.includes(searchTerm);
          const matchesType = !selectedType || type === selectedType;

          if (matchesSearch && matchesType) {
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
            <td colspan="9" class="text-center">
              <div class="table-empty-state">
                <span class="material-symbols-outlined">search_off</span>
                <h4>No results found</h4>
                <p>Try adjusting your search or filter</p>
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

      if (typeFilter) {
        typeFilter.addEventListener('change', filterTable);
      }
    });

    // ==========================================
    // DELETE PROMOTION FUNCTION
    // ==========================================
    function deletePromo(promotionId) {
      Swal.fire({
        title: '{{ __('messages.owner.products.promotions.delete_confirmation_1') }}',
        text: '{{ __('messages.owner.products.promotions.delete_confirmation_2') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ae1504',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('messages.owner.products.promotions.delete_confirmation_3') }}',
        cancelButtonText: '{{ __('messages.owner.products.promotions.cancel') }}',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = `/owner/user-owner/promotions/${promotionId}`;
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
  </script>
@endpush