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
                  <option value="percentage">{{ __('messages.owner.products.promotions.percentage') }}</option>
                  <option value="amount">{{ __('messages.owner.products.promotions.reduced_fare') }}</option>
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
    // PROMOTION INDEX - SEARCH & FILTER (NO RELOAD)
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const typeFilter = document.getElementById('typeFilter');
      const tableBody = document.getElementById('promotionTableBody');
      const paginationWrapper = document.querySelector('.table-pagination');

      if (!tableBody) {
        console.error('Table body not found');
        return;
      }

      // Ambil semua data dari Blade
      const allPromotionsData = @json($allPromotionsFormatted ?? []);
      
      let filteredPromotions = [...allPromotionsData];
      const itemsPerPage = 10;
      let currentPage = 1;

      // ==========================================
      // FILTER FUNCTION
      // ==========================================
      function filterPromotions() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedType = typeFilter ? typeFilter.value.trim() : '';

        filteredPromotions = allPromotionsData.filter(promotion => {
          // Search: cari di promotion_code, promotion_name
          const searchText = `
            ${promotion.promotion_code || ''} 
            ${promotion.promotion_name || ''}
          `.toLowerCase();
          
          const matchesSearch = !searchTerm || searchText.includes(searchTerm);

          // Type filter
          const matchesType = !selectedType || promotion.promotion_type === selectedType;

          return matchesSearch && matchesType;
        });

        currentPage = 1; // Reset ke halaman pertama
        renderTable();
      }

      // ==========================================
      // RENDER TABLE
      // ==========================================
      function renderTable() {
        // Hitung pagination
        const totalPages = Math.ceil(filteredPromotions.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentPromotions = filteredPromotions.slice(startIndex, endIndex);

        // Clear table
        tableBody.innerHTML = '';

        // Render rows
        if (currentPromotions.length === 0) {
          tableBody.innerHTML = `
            <tr class="empty-filter-row">
              <td colspan="9" class="text-center">
                <div class="table-empty-state">
                  <span class="material-symbols-outlined" style="font-size: 4rem; color: #ccc; display: block; margin-bottom: 1rem;">search_off</span>
                  <h4 style="margin: 0 0 0.5rem 0; color: #666; font-size: 1.25rem;">No results found</h4>
                  <p style="margin: 0; color: #999;">Try adjusting your search or filter</p>
                </div>
              </td>
            </tr>
          `;
        } else {
          currentPromotions.forEach((promotion, index) => {
            const rowNumber = startIndex + index + 1;
            const row = createPromotionRow(promotion, rowNumber);
            tableBody.appendChild(row);
          });
        }

        // Handle pagination visibility
        if (paginationWrapper) {
          if (filteredPromotions.length <= itemsPerPage) {
            paginationWrapper.style.display = 'none';
          } else {
            paginationWrapper.style.display = '';
            renderPagination(totalPages);
          }
        }
      }

      // ==========================================
      // CREATE PROMOTION ROW
      // ==========================================
      function createPromotionRow(promotion, rowNumber) {
        const tr = document.createElement('tr');
        tr.className = 'table-row';
        tr.setAttribute('data-category', promotion.promotion_type || '');

        // Format type
        let typeText = '';
        if (promotion.promotion_type === 'percentage') {
          typeText = '{{ __("messages.owner.products.promotions.percentage") }}';
        } else {
          typeText = '{{ __("messages.owner.products.promotions.reduced_fare") }}';
        }

        // Format value
        let valueText = '';
        if (promotion.promotion_type === 'percentage') {
          valueText = `${new Intl.NumberFormat('id-ID').format(promotion.promotion_value)}%`;
        } else {
          valueText = `Rp ${new Intl.NumberFormat('id-ID').format(promotion.promotion_value)}`;
        }

        // Format active date
        let activeDateText = '';
        if (promotion.start_date && promotion.end_date) {
          activeDateText = `${promotion.start_date} (${promotion.start_time}) – ${promotion.end_date} (${promotion.end_time})`;
        } else if (promotion.start_date) {
          activeDateText = `{{ __("messages.owner.products.promotions.start") }} ${promotion.start_date} (${promotion.start_time})`;
        } else if (promotion.end_date) {
          activeDateText = `{{ __("messages.owner.products.promotions.until") }} ${promotion.end_date} (${promotion.end_time})`;
        } else {
          activeDateText = '<span class="text-muted">{{ __("messages.owner.products.promotions.unlimited") }}</span>';
        }

        // Format status badge
        let statusBadge = '';
        let badgeClass = '';
        
        switch(promotion.status) {
          case 'active':
            badgeClass = 'badge-success';
            break;
          case 'inactive':
          case 'expired':
            badgeClass = 'badge-danger';
            break;
          case 'will_be_active':
            badgeClass = 'badge-warning';
            break;
          default:
            badgeClass = 'badge-secondary';
        }
        
        statusBadge = `<span class="badge-modern ${badgeClass}">${promotion.status_label}</span>`;

        // URLs
        const showUrl = `/owner/user-owner/promotions/${promotion.id}`;
        const editUrl = `/owner/user-owner/promotions/${promotion.id}/edit`;

        tr.innerHTML = `
          <td class="text-center text-muted">${rowNumber}</td>
          <td>
            <span class="mono fw-600">${promotion.promotion_code || '-'}</span>
          </td>
          <td>
            <span class="fw-600">${promotion.promotion_name || '-'}</span>
          </td>
          <td class="text-center">
            <span>${typeText}</span>
          </td>
          <td>
            <span class="fw-600">${valueText}</span>
          </td>
          <td>
            <div class="text-secondary" style="font-size: 0.875rem;">
              ${activeDateText}
            </div>
          </td>
          <td>
            <span class="text-secondary" style="font-size: 0.875rem;">${promotion.active_days || '-'}</span>
          </td>
          <td class="text-center">
            ${statusBadge}
          </td>
          <td class="text-center">
            <div class="table-actions">
              <a href="${showUrl}" class="btn-table-action view" title="{{ __('messages.owner.products.promotions.detail') }}">
                <span class="material-symbols-outlined">visibility</span>
              </a>
              <a href="${editUrl}" class="btn-table-action edit" title="{{ __('messages.owner.products.promotions.edit') }}">
                <span class="material-symbols-outlined">edit</span>
              </a>
              <button onclick="deletePromo(${promotion.id})" class="btn-table-action delete" title="{{ __('messages.owner.products.promotions.delete') }}">
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
        searchInput.addEventListener('input', filterPromotions);
      }

      if (typeFilter) {
        typeFilter.addEventListener('change', filterPromotions);
      }

      // ==========================================
      // INITIALIZE
      // ==========================================
      renderTable();
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