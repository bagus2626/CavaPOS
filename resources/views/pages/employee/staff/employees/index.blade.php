@extends('layouts.staff')
@section('title', 'Employee List')

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
                    <h1 class="page-title">All Employees</h1>
                    <p class="page-subtitle">Manage your team members</p>
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
                                        class="form-control-modern with-icon" placeholder="Search employees..."
                                        oninput="searchFilter(this, 600)">
                                </div>
                            </div>
                            {{-- Hanya tampilkan tombol Add jika role adalah manager --}}
                            @php $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager'); @endphp
                            @if ($empRole === 'manager')
                                <a href="{{ route('employee.' . $empRole . '.employees.create') }}"
                                    class="btn-modern btn-primary-modern">
                                    <span class="material-symbols-outlined">add</span>
                                    Add Employee
                                </a>
                            @endif
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
                title: 'Delete this employee?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b3311d',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
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
