@extends('layouts.owner')

@section('title', __('messages.owner.user_management.employees.employee_list'))
@section('page_title', __('messages.owner.user_management.employees.all_employees'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.user_management.employees.all_employees') }}</h1>
          <p class="page-subtitle">Manage your team members and their roles</p>
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
                  placeholder="Search employees...">
              </div>

              <!-- Filter by Outlet -->
              <div class="select-wrapper" style="min-width: 200px;">
                <select id="outletFilter" class="form-control-modern">
                  <option value="">All Outlets
                  </option>
                  @foreach($employees->pluck('partner')->unique('id') as $partner)
                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                  @endforeach
                </select>
                <span class="material-symbols-outlined select-arrow">expand_more</span>
              </div>
            </div>

            <!-- Add Employee Button -->
            <a href="{{ route('owner.user-owner.employees.create') }}" class="btn-modern btn-primary-modern">
              <span class="material-symbols-outlined">add</span>
              {{ __('messages.owner.user_management.employees.add_employee') ?? 'Add Employee' }}
            </a>
          </div>
        </div>
      </div>

      <!-- Table Display -->
      @include('pages.owner.human-resource.employee.display')

    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // ==========================================
    // EMPLOYEE INDEX - SEARCH & FILTER
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const outletFilter = document.getElementById('outletFilter');
      const tableBody = document.getElementById('employeeTableBody');

      if (!tableBody) return;

      const rows = tableBody.querySelectorAll('tr.table-row');

      // ==========================================
      // FILTER FUNCTION
      // ==========================================
      function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedOutlet = outletFilter ? outletFilter.value : '';

        let visibleCount = 0;

        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const outlet = row.dataset.outlet;

          const matchesSearch = !searchTerm || text.includes(searchTerm);
          const matchesOutlet = !selectedOutlet || outlet === selectedOutlet;

          if (matchesSearch && matchesOutlet) {
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
                  <td colspan="8" class="td-center">
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

      if (outletFilter) {
        outletFilter.addEventListener('change', filterTable);
      }
    });

    // ==========================================
    // DELETE EMPLOYEE FUNCTION
    // ==========================================
    function deleteEmployee(employeeId) {
      Swal.fire({
        title: '{{ __('messages.owner.user_management.employees.delete_confirmation_1') }}',
        text: '{{ __('messages.owner.user_management.employees.delete_confirmation_2') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ae1504',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('messages.owner.user_management.employees.delete_confirmation_3') }}',
        cancelButtonText: '{{ __('messages.owner.user_management.employees.cancel') }}'
      }).then((result) => {
        if (result.isConfirmed) {
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = `/owner/user-owner/employees/${employeeId}`;
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