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
                  <option value="">All Outlets</option>
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
    // EMPLOYEE INDEX - SEARCH & FILTER (NO RELOAD)
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const outletFilter = document.getElementById('outletFilter');
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
        const selectedOutlet = outletFilter ? outletFilter.value.trim() : '';

        filteredEmployees = allEmployeesData.filter(employee => {
          // Search: cari di name, username, email, role, partner
          const searchText = `
            ${employee.name || ''} 
            ${employee.user_name || ''} 
            ${employee.email || ''} 
            ${employee.role || ''}
            ${employee.partner_name || ''}
          `.toLowerCase();
          
          const matchesSearch = !searchTerm || searchText.includes(searchTerm);

          // Outlet filter
          const matchesOutlet = !selectedOutlet || employee.partner_id == selectedOutlet;

          return matchesSearch && matchesOutlet;
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
              <td colspan="8" class="text-center">
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
        tr.setAttribute('data-outlet', employee.partner_id || '');

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
        // Check berbagai format nilai is_active (boolean, int, string)
        if (employee.is_active === 1 || employee.is_active === '1' || employee.is_active === true) {
          statusBadge = '<span class="badge-modern badge-success">{{ __("messages.owner.user_management.employees.active") }}</span>';
        } else {
          statusBadge = '<span class="badge-modern badge-danger">{{ __("messages.owner.user_management.employees.non_active") }}</span>';
        }

        // URLs
        const showUrl = `/owner/user-owner/employees/${employee.id}`;
        const editUrl = `/owner/user-owner/employees/${employee.id}/edit`;

        tr.innerHTML = `
          <td class="text-center text-muted">${rowNumber}</td>
          <td>
            <div class="user-info-cell">
              ${imageHtml}
              <span class="data-name">${employee.name || '-'}</span>
            </div>
          </td>
          <td>
            <div class="cell-with-icon">
              <span class="fw-600">${employee.partner_name || '-'}</span>
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
              <a href="${showUrl}" class="btn-table-action view" title="{{ __('messages.owner.user_management.employees.view_details') ?? 'View Details' }}">
                <span class="material-symbols-outlined">visibility</span>
              </a>
              <a href="${editUrl}" class="btn-table-action edit" title="{{ __('messages.owner.user_management.employees.edit') }}">
                <span class="material-symbols-outlined">edit</span>
              </a>
              <button onclick="deleteEmployee(${employee.id})" class="btn-table-action delete" title="{{ __('messages.owner.user_management.employees.delete') }}">
                <span class="material-symbols-outlined">delete</span>
              </button>
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
        searchInput.addEventListener('input', filterEmployees);
      }

      if (outletFilter) {
        outletFilter.addEventListener('change', filterEmployees);
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