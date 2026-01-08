@extends('layouts.owner')

@section('title', __('messages.owner.outlet.all_outlets.outlet_list'))
@section('page_title', __('messages.owner.outlet.all_outlets.all_outlets'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.outlet.all_outlets.all_outlets') }}</h1>
          <p class="page-subtitle">Manage your outlets and their configurations</p>
        </div>
      </div>

      <!-- Success/Error Messages -->
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
                  placeholder="Search outlets...">
              </div>

              <!-- Filter by Status -->
              <div class="select-wrapper" style="min-width: 200px;">
                <select id="statusFilter" class="form-control-modern">
                  <option value="">All Status</option>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
                <span class="material-symbols-outlined select-arrow">expand_more</span>
              </div>
            </div>

            <!-- Add Outlet Button -->
            <a href="{{ route('owner.user-owner.outlets.create') }}" class="btn-modern btn-primary-modern">
              <span class="material-symbols-outlined">add</span>
              {{ __('messages.owner.outlet.all_outlets.add_outlet') ?? 'Add Outlet' }}
            </a>
          </div>
        </div>
      </div>

      <!-- Table Display -->
      @include('pages.owner.outlet.display')

    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // ==========================================
    // OUTLET INDEX - SEARCH & FILTER
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const statusFilter = document.getElementById('statusFilter');
      const tableBody = document.getElementById('outletTableBody');

      if (!tableBody) return;

      const rows = tableBody.querySelectorAll('tr.table-row');

      // ==========================================
      // FILTER FUNCTION
      // ==========================================
      function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedStatus = statusFilter ? statusFilter.value : '';

        let visibleCount = 0;

        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const status = row.dataset.status;

          const matchesSearch = !searchTerm || text.includes(searchTerm);
          const matchesStatus = !selectedStatus || status === selectedStatus;

          if (matchesSearch && matchesStatus) {
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
        // Remove existing empty row
        const existingEmptyRow = tableBody.querySelector('.empty-filter-row');
        if (existingEmptyRow) {
          existingEmptyRow.remove();
        }

        // Add empty row if no results
        if (visibleCount === 0 && rows.length > 0) {
          const emptyRow = document.createElement('tr');
          emptyRow.classList.add('empty-filter-row');
          emptyRow.innerHTML = `
                  <td colspan="9" class="td-center">
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

      if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
      }
    });

    // ==========================================
    // DELETE OUTLET FUNCTION
    // ==========================================
    function deleteOutlet(outletId) {
      Swal.fire({
        title: '{{ __('messages.owner.outlet.all_outlets.delete_confirmation_1') }}',
        text: '{{ __('messages.owner.outlet.all_outlets.delete_confirmation_2') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ae1504',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('messages.owner.outlet.all_outlets.delete_confirmation_3') }}',
        cancelButtonText: '{{ __('messages.owner.outlet.all_outlets.cancel') }}'
      }).then((result) => {
        if (result.isConfirmed) {
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = `/owner/user-owner/outlets/${outletId}`;
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