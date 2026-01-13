@extends('layouts.partner')

@section('title', __('messages.partner.user_management.employees.employee_list'))
@section('page_title', __('messages.partner.user_management.employees.all_employees'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.partner.user_management.employees.employees') }}</h1>
          <p class="page-subtitle">{{ __('messages.partner.user_management.employees.manage_your_team') }}</p>
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
                  placeholder="{{ __('messages.partner.user_management.employees.search_employees') }}">
              </div>

              <!-- Filter by Role -->
              <div class="select-wrapper" style="min-width: 200px;">
                <select id="roleFilter" class="form-control-modern">
                  <option value="">{{ __('messages.partner.user_management.employees.all_roles') }}</option>
                  @foreach($roles as $role)
                    <option value="{{ $role }}" {{ (string) ($roleFilter ?? '') === (string) $role ? 'selected' : '' }}>
                      {{ $role }}
                    </option>
                  @endforeach
                </select>
                <span class="material-symbols-outlined select-arrow">expand_more</span>
              </div>
            </div>

            <!-- Add Employee Button (uncomment jika diperlukan) -->
            {{-- <a href="{{ route('partner.user-management.employees.create') }}" class="btn-modern btn-primary-modern">
              <span class="material-symbols-outlined">add</span>
              {{ __('messages.partner.user_management.employees.add_employee') ?? 'Add Employee' }}
            </a> --}}
          </div>
        </div>
      </div>

      <!-- Table Display -->
      @include('pages.partner.human-resource.employee.display')

    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // ==========================================
    // EMPLOYEE INDEX - SEARCH & FILTER (NO RELOAD)
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const roleFilter = document.getElementById('roleFilter');
      const tableBody = document.getElementById('employeeTableBody');
      const paginationWrapper = document.querySelector('.table-pagination');

      if (!tableBody) {
        console.error('Table body not found');
        return;
      }

      // Ambil semua data dari Blade
      const allEmployeesData = @json($allEmployeesFormatted ?? []);

      let filteredEmployees = [...allEmployeesData];
      const itemsPerPage = 10;
      let currentPage = 1;

      // ==========================================
      // FILTER FUNCTION
      // ==========================================
      function filterEmployees() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedRole = roleFilter ? roleFilter.value.trim() : '';

        filteredEmployees = allEmployeesData.filter(employee => {
          // Search: cari di name, username, email, role
          const searchText = `
              ${employee.name || ''} 
              ${employee.user_name || ''} 
              ${employee.email || ''} 
              ${employee.role || ''}
            `.toLowerCase();

          const matchesSearch = !searchTerm || searchText.includes(searchTerm);

          // Role filter
          const matchesRole = !selectedRole || employee.role === selectedRole;

          return matchesSearch && matchesRole;
        });

        currentPage = 1; // Reset ke halaman pertama
        renderTable();
      }

      // ==========================================
      // RENDER TABLE
      // ==========================================
      function renderTable() {
        // Hitung pagination
        const totalPages = Math.ceil(filteredEmployees.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentEmployees = filteredEmployees.slice(startIndex, endIndex);

        // Clear table
        tableBody.innerHTML = '';

        // Render rows
        if (currentEmployees.length === 0) {
          tableBody.innerHTML = `
              <tr class="empty-filter-row">
                <td colspan="7" class="text-center">
                  <div class="table-empty-state">
                    <span class="material-symbols-outlined" style="font-size: 4rem; color: #ccc; display: block; margin-bottom: 1rem;">search_off</span>
                    <h4 style="margin: 0 0 0.5rem 0; color: #666; font-size: 1.25rem;">No results found</h4>
                    <p style="margin: 0; color: #999;">Try adjusting your search or filter</p>
                  </div>
                </td>
              </tr>
            `;
        } else {
          currentEmployees.forEach((employee, index) => {
            const rowNumber = startIndex + index + 1;
            const row = createEmployeeRow(employee, rowNumber);
            tableBody.appendChild(row);
          });
        }

        // Handle pagination visibility
        if (paginationWrapper) {
          if (filteredEmployees.length <= itemsPerPage) {
            paginationWrapper.style.display = 'none';
          } else {
            paginationWrapper.style.display = '';
            renderPagination(totalPages);
          }
        }
      }

      // ==========================================
      // CREATE EMPLOYEE ROW
      // ==========================================
      function createEmployeeRow(employee, rowNumber) {
        const tr = document.createElement('tr');
        tr.className = 'table-row';
        tr.setAttribute('data-role', employee.role || '');

        // Format image
        let imageHtml = '';
        if (employee.image) {
          const imgSrc = employee.image.startsWith('http://') || employee.image.startsWith('https://')
            ? employee.image
            : `{{ asset('storage/') }}/${employee.image}`;
          imageHtml = `<img src="${imgSrc}" alt="${employee.name}" class="user-avatar" loading="lazy">`;
        } else {
          imageHtml = `
              <div class="user-avatar-placeholder">
                <span class="material-symbols-outlined">person</span>
              </div>
            `;
        }

        // Format status badge
        let statusBadge = '';
        if (employee.is_active == 1 || employee.is_active === true || employee.is_active === "1") {
          statusBadge = '<span class="badge-modern badge-success">{{ __("messages.partner.user_management.employees.active") }}</span>';
        } else {
          statusBadge = '<span class="badge-modern badge-danger">{{ __("messages.partner.user_management.employees.non_active") }}</span>';
        }

        // URLs
        const showUrl = `/partner/user-management/employees/${employee.id}`;

        tr.innerHTML = `
            <td class="text-center text-muted">${rowNumber}</td>
            <td>
              <div class="user-info-cell">
                ${imageHtml}
                <span class="data-name">${employee.name || '-'}</span>
              </div>
            </td>
            <td>
              <span class="text-secondary">${employee.user_name || '-'}</span>
            </td>
            <td>
              <a href="mailto:${employee.email}" class="table-link">
                ${employee.email || '-'}
              </a>
            </td>
            <td>
              <span class="badge-modern badge-info">
                ${employee.role || '-'}
              </span>
            </td>
            <td class="text-center">
              ${statusBadge}
            </td>
            <td class="text-center">
              <div class="table-actions">
                <a href="${showUrl}" class="btn-table-action view" title="{{ __('messages.partner.user_management.employees.view_details') ?? 'View Details' }}">
                  <span class="material-symbols-outlined">visibility</span>
                </a>
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
          link.addEventListener('click', function (e) {
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
        searchInput.addEventListener('input', filterEmployees);
      }

      if (roleFilter) {
        roleFilter.addEventListener('change', filterEmployees);
      }

      // ==========================================
      // INITIALIZE
      // ==========================================
      renderTable();
    });

    // ==========================================
    // DELETE EMPLOYEE FUNCTION
    // ==========================================
    function deleteEmployee(employeeId) {
      Swal.fire({
        title: '{{ __('messages.partner.user_management.employees.delete_confirmation_1') ?? 'Are you sure?' }}',
        text: '{{ __('messages.partner.user_management.employees.delete_confirmation_2') ?? 'You won\'t be able to revert this!' }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ae1504',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('messages.partner.user_management.employees.delete_confirmation_3') ?? 'Yes, delete it!' }}',
        cancelButtonText: '{{ __('messages.partner.user_management.employees.cancel') ?? 'Cancel' }}'
      }).then((result) => {
        if (result.isConfirmed) {
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = `/partner/user-management/employees/${employeeId}`;
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