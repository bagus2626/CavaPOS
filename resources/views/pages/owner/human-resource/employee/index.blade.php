@extends('layouts.owner')

@section('title', __('messages.owner.user_management.employees.employee_list'))
@section('page_title', __('messages.owner.user_management.employees.all_employees'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.user_management.employees.all_employees') }}</h1>
          <p class="page-subtitle">{{ __('messages.owner.user_management.employees.manage_team_subtitle') }}</p>
        </div>
      </div>

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

      <div class="modern-card mb-4">
        <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
          <form method="GET" action="{{ url()->current() }}" id="employeeFilterForm">
            <div class="table-controls">
              <div class="search-filter-group">
                <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                  <span class="input-icon">
                    <span class="material-symbols-outlined">search</span>
                  </span>
                  <input
                    type="text"
                    name="q"
                    id="employeeSearchInput"
                    value="{{ $q ?? request('q') }}"
                    class="form-control-modern with-icon"
                    placeholder="{{ __('messages.owner.user_management.employees.search_placeholder') }}"
                    oninput="searchFilter(this, 600)"
                  >
                </div>

                <div class="select-wrapper" style="min-width: 200px;">
                  <select name="partner_id" class="form-control-modern" onchange="this.form.submit()">
                    <option value="">{{ __('messages.owner.user_management.employees.all_outlets') }}</option>
                    @foreach($partners as $partner)
                      <option value="{{ $partner->id }}" @selected((string)request('partner_id') === (string)$partner->id)>
                        {{ $partner->name }}
                      </option>
                    @endforeach
                  </select>
                  <span class="material-symbols-outlined select-arrow">expand_more</span>
                </div>
              </div>

              <a href="{{ route('owner.user-owner.employees.create') }}" class="btn-modern btn-primary-modern">
                <span class="material-symbols-outlined">add</span>
                {{ __('messages.owner.user_management.employees.add_employee') }}
              </a>
            </div>
          </form>
        </div>
      </div>

      @include('pages.owner.human-resource.employee.display')

    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function searchFilter(el, delay = 400) {
      const form = document.getElementById('employeeFilterForm');
      if (!form) return;

      // simpan timer per-element (biar aman kalau nanti ada lebih dari 1 input)
      if (el._searchDebounceTimer) {
        clearTimeout(el._searchDebounceTimer);
      }

      // Enter = langsung submit
      // event tersedia di window.event karena dipanggil inline dari HTML
      const e = window.event;
      if (e && e.key === 'Enter') {
        e.preventDefault();
        form.submit();
        return;
      }

      el._searchDebounceTimer = setTimeout(() => {
        form.submit();
      }, delay);
    }
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