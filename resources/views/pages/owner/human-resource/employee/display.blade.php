@php
    use Illuminate\Support\Str;
@endphp

<link rel="stylesheet" href="{{ asset('css/mobile-owner.css') }}">
<div class="modern-card">

    {{-- =======================
    DESKTOP: TABLE
  ======================= --}}
    <div class="data-table-wrapper only-desktop">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">#</th>
                    <th>{{ __('messages.owner.user_management.employees.employee_name') }}</th>
                    <th>{{ __('messages.owner.user_management.employees.outlet') }}</th>
                    <th>{{ __('messages.owner.user_management.employees.username') }}</th>
                    <th>{{ __('messages.owner.user_management.employees.email') }}</th>
                    <th>{{ __('messages.owner.user_management.employees.role') }}</th>
                    <th class="text-center">{{ __('messages.owner.user_management.employees.status') }}</th>
                    <th class="text-center" style="width: 180px;">
                        {{ __('messages.owner.user_management.employees.actions') }}
                    </th>
                </tr>
            </thead>

            <tbody id="employeeTableBody">
                @forelse ($employees as $index => $employee)
                    @php
                        $img = $employee->image
                            ? (Str::startsWith($employee->image, ['http://', 'https://'])
                                ? $employee->image
                                : asset('storage/' . $employee->image))
                            : null;
                    @endphp

                    <tr data-outlet="{{ $employee->partner_id }}" class="table-row">
                        <td class="text-center text-muted">{{ $employees->firstItem() + $index }}</td>

                        <td>
                            <div class="user-info-cell">
                                @if ($img)
                                    <img src="{{ $img }}" alt="{{ $employee->name }}" class="user-avatar"
                                        loading="lazy">
                                @else
                                    <div class="user-avatar-placeholder">
                                        <span class="material-symbols-outlined">person</span>
                                    </div>
                                @endif
                                <span class="data-name">{{ $employee->name }}</span>
                            </div>
                        </td>

                        <td>
                            <div class="cell-with-icon">
                                <span class="fw-600">{{ $employee->partner->name ?? '-' }}</span>
                            </div>
                        </td>

                        <td><span class="text-secondary">{{ $employee->user_name }}</span></td>

                        <td>
                            <a href="mailto:{{ $employee->email }}" class="table-link">
                                {{ $employee->email }}
                            </a>
                        </td>

                        <td>
                            <span class="badge-modern badge-info">{{ $employee->role }}</span>
                        </td>

                        <td class="text-center">
                            @if ((int) $employee->is_active === 1)
                                <span class="badge-modern badge-success">
                                    {{ __('messages.owner.user_management.employees.active') }}
                                </span>
                            @else
                                <span class="badge-modern badge-danger">
                                    {{ __('messages.owner.user_management.employees.non_active') }}
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            <div class="table-actions">
                                <a href="{{ route('owner.user-owner.employees.show', $employee->id) }}"
                                    class="btn-table-action view"
                                    title="{{ __('messages.owner.user_management.employees.view_details') ?? 'View Details' }}">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>

                                <a href="{{ route('owner.user-owner.employees.edit', $employee->id) }}"
                                    class="btn-table-action edit"
                                    title="{{ __('messages.owner.user_management.employees.edit') }}">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>

                                <button type="button" onclick="deleteEmployee({{ $employee->id }})"
                                    class="btn-table-action delete"
                                    title="{{ __('messages.owner.user_management.employees.delete') }}">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">person_off</span>
                                <h4>{{ __('messages.owner.user_management.employees.no_employees') ?? 'No employees found' }}
                                </h4>
                                <p>{{ __('messages.owner.user_management.employees.add_first_employee') ?? 'Add your first employee to get started' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

   {{-- =======================
  MOBILE: HEADER + SEARCH + FILTER + CARDS
======================= --}}
<div class="only-mobile">
    {{-- Wrapper Card untuk semua konten mobile --}}
    <div class="mobile-unified-card">
        {{-- Mobile Header with Avatar & Search --}}
        <div class="mobile-header-section">
            <div class="mobile-header-card">
                <div class="mobile-header-content">
                    <div class="mobile-header-left">
                        <h2 class="mobile-header-title">Employee Directory</h2>
                        <p class="mobile-header-subtitle">{{ $employees->total() }} Total Staff Members</p>
                    </div>
                    <div class="mobile-header-right">
                      <div class="mobile-header-avatar-placeholder">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                    </div>
                </div>

                {{-- Mobile Search Form --}}
                <div class="mobile-search-wrapper">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="mobile-search-box">
                            <span class="mobile-search-icon">
                                <span class="material-symbols-outlined">search</span>
                            </span>
                            <input type="text" name="q" value="{{ request('q') }}"
                                class="mobile-search-input"
                                placeholder="{{ __('messages.owner.user_management.employees.search_placeholder') }}"
                                oninput="searchFilter(this, 600)">
                            @if (request('partner_id'))
                                <input type="hidden" name="partner_id" value="{{ request('partner_id') }}">
                            @endif
                            <button type="button" class="mobile-filter-btn" onclick="toggleMobileFilter()">
                                <span class="material-symbols-outlined">tune</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Filter Pills --}}
        <div class="mobile-filter-pills">
            <div class="filter-pills-container">
                {{-- All Staff --}}
                <a href="{{ route('owner.user-owner.employees.index', array_filter(['partner_id' => $currentPartnerId, 'q' => $q])) }}"
                    class="filter-pill {{ !$currentRole && !$currentStatus ? 'active' : '' }}">
                    All Staff
                </a>
                
                {{-- Off Duty --}}
                @if($inactiveCount > 0)
                <a href="{{ route('owner.user-owner.employees.index', array_filter(['status' => 'off', 'partner_id' => $currentPartnerId, 'q' => $q])) }}"
                    class="filter-pill {{ $currentStatus === 'off' ? 'active' : '' }}">
                    Off ({{ $inactiveCount }})
                </a>
                @endif
                
                {{-- Dynamic Roles --}}
                @foreach($availableRoles as $role)
                <a href="{{ route('owner.user-owner.employees.index', array_filter(['role' => $role, 'partner_id' => $currentPartnerId, 'q' => $q])) }}"
                    class="filter-pill {{ $currentRole === $role ? 'active' : '' }}">
                    {{ ucfirst(strtolower($role)) }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Mobile Employee List --}}
        <div class="mobile-employee-list">
            @forelse ($employees as $employee)
                @php
                    $img = $employee->image
                        ? (Str::startsWith($employee->image, ['http://', 'https://'])
                            ? $employee->image
                            : asset('storage/' . $employee->image))
                        : null;
                @endphp

                <div class="employee-card-wrapper">
                    {{-- Swipe Actions Background --}}
                    <div class="swipe-actions">
                        <a href="{{ route('owner.user-owner.employees.edit', $employee->id) }}"
                            class="swipe-action edit">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                        <button type="button" onclick="deleteEmployee({{ $employee->id }})"
                            class="swipe-action delete">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>

                    {{-- Card Content --}}
                    <a href="{{ route('owner.user-owner.employees.show', $employee->id) }}"
                        class="employee-card-link">
                        <div class="employee-card-clickable">
                            <div class="employee-card__left">
                                <div class="employee-card__avatar">
                                    @if ($img)
                                        <img src="{{ $img }}" alt="{{ $employee->name }}" loading="lazy">
                                    @else
                                        <div class="user-avatar-placeholder">
                                            <span class="material-symbols-outlined">person</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="employee-card__info">
                                    <div class="employee-card__name">{{ $employee->name }}</div>
                                    <div class="employee-card__details">
                                        <span class="detail-text">{{ $employee->role ?? '-' }}</span>
                                        <span class="detail-separator">•</span>
                                        <span class="detail-text">{{ $employee->partner->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="employee-card__right">
                                <span class="material-symbols-outlined chevron">chevron_right</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="table-empty-state">
                    <span class="material-symbols-outlined">person_off</span>
                    <h4>{{ __('messages.owner.user_management.employees.no_employees') ?? 'No employees found' }}</h4>
                    <p>{{ __('messages.owner.user_management.employees.add_first_employee') ?? 'Add your first employee to get started' }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>

    {{-- =======================
    PAGINATION
  ======================= --}}
    @if ($employees->hasPages())
        <div class="table-pagination">
            {{ $employees->links() }}
        </div>
    @endif

</div>

{{-- Floating Add Button (Mobile Only) --}}
<a href="{{ route('owner.user-owner.employees.create') }}" class="btn-add-employee-mobile">
    <span class="material-symbols-outlined">add</span>
</a>

{{-- Mobile Filter Modal (Advanced Filter) --}}
<div id="mobileFilterModal" class="mobile-filter-modal">
    <div class="filter-modal-backdrop" onclick="closeMobileFilter()"></div>
    <div class="filter-modal-content">
        <div class="filter-modal-header">
            <div class="filter-header-left">
                <span class="material-symbols-outlined filter-header-icon">tune</span>
                <h3>Filter by Outlet</h3>
            </div>
            <button type="button" class="filter-close-btn" onclick="closeMobileFilter()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <div class="filter-modal-body">
            {{-- Pill Filter untuk Outlet --}}
            <div class="modal-filter-pills">
                {{-- All Outlets --}}
                <a href="{{ route('owner.user-owner.employees.index', array_filter(['q' => request('q'), 'role' => request('role'), 'status' => request('status')])) }}"
                    class="modal-pill {{ !request('partner_id') ? 'active' : '' }}"
                    onclick="closeMobileFilter()">
                    <div class="pill-left">
                        <div class="pill-icon-wrapper {{ !request('partner_id') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">storefront</span>
                        </div>
                        <div class="pill-info">
                            <span class="pill-text">All Outlets</span>
                            <span class="pill-subtext">View all employees</span>
                        </div>
                    </div>
                    <div class="pill-right">
                        @if(!request('partner_id'))
                        <span class="material-symbols-outlined pill-check">check_circle</span>
                        @endif
                    </div>
                </a>
                
                {{-- Divider --}}
                <div class="filter-divider">
                    <span>Outlets</span>
                </div>
                
                {{-- Dynamic Outlets --}}
                @foreach ($partners as $partner)
                <a href="{{ route('owner.user-owner.employees.index', array_filter(['partner_id' => $partner->id, 'q' => request('q'), 'role' => request('role'), 'status' => request('status')])) }}"
                    class="modal-pill {{ (string)request('partner_id') === (string)$partner->id ? 'active' : '' }}"
                    onclick="closeMobileFilter()">
                    <div class="pill-left">
                        <div class="pill-icon-wrapper {{ (string)request('partner_id') === (string)$partner->id ? 'active' : '' }}">
                            <span class="material-symbols-outlined">store</span>
                        </div>
                        <div class="pill-info">
                            <span class="pill-text">{{ $partner->name }}</span>
                            <span class="pill-subtext">{{ $partner->address ?? 'Location not set' }}</span>
                        </div>
                    </div>
                    <div class="pill-right">
                        @if((string)request('partner_id') === (string)$partner->id)
                        <span class="material-symbols-outlined pill-check">check_circle</span>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        
        {{-- Modal Footer --}}
        <div class="filter-modal-footer">
            <button type="button" class="btn-clear-filter" 
                onclick="window.location.href='{{ route('owner.user-owner.employees.index', array_filter(['q' => request('q'), 'role' => request('role'), 'status' => request('status')])) }}'">
                <span class="material-symbols-outlined">restart_alt</span>
                Clear Filter
            </button>
        </div>
    </div>
</div>

