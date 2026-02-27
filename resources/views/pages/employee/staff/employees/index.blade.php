@extends('layouts.staff')
@section('title', __('messages.owner.user_management.employees.employee_list'))

@section('content')
    <style>
        @media (max-width: 768px) {
            .page-header {
                display: none !important;
            }
        }
    </style>

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
                    <div class="alert-icon"><span class="material-symbols-outlined">check_circle</span></div>
                    <div class="alert-content">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon"><span class="material-symbols-outlined">error</span></div>
                    <div class="alert-content">{{ session('error') }}</div>
                </div>
            @endif

            {{-- Desktop Search & Filter --}}
            <div class="modern-card mb-4 desktop-only-card">
                <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                    <form method="GET" action="{{ url()->current() }}" id="employeeFilterForm">
                        <div class="table-controls">
                            <div class="search-filter-group">
                                <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                                    <span class="input-icon">
                                        <span class="material-symbols-outlined">search</span>
                                    </span>
                                    <input type="text" name="q" value="{{ request('q') }}"
                                        class="form-control-modern with-icon"
                                        placeholder="{{ __('messages.owner.user_management.employees.search_placeholder') }}"
                                        oninput="searchFilter(this, 600)">
                                </div>
                            </div>
                            @php $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager'); @endphp
                            <a href="{{ route('employee.' . $empRole . '.employees.create') }}"
                                class="btn-modern btn-primary-modern">
                                <span class="material-symbols-outlined">add</span>
                                {{ __('messages.owner.user_management.employees.add_employee') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @include('pages.employee.staff.employees.display')

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function searchFilter(el, delay = 400) {
            const form = el.closest('form');
            if (!form) return;
            if (el._searchDebounceTimer) clearTimeout(el._searchDebounceTimer);
            el._searchDebounceTimer = setTimeout(() => form.submit(), delay);
        }

        function deleteEmployee(employeeId) {
            @php $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager'); @endphp
            Swal.fire({
                title: '{{ __('messages.owner.user_management.employees.delete_confirmation_1') }}',
                text: '{{ __('messages.owner.user_management.employees.delete_confirmation_2') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b3311d',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __('messages.owner.user_management.employees.delete_confirmation_3') }}',
                cancelButtonText: '{{ __('messages.owner.user_management.employees.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/employee/{{ $empRole }}/employees/${employeeId}`;
                    form.style.display = 'none';
                    form.innerHTML = `@csrf <input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function toggleMobileFilter() {
            const modal = document.getElementById('mobileFilterModal');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileFilter() {
            const modal = document.getElementById('mobileFilterModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }
    </script>
@endpush