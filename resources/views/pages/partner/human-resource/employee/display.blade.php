@php
    use Illuminate\Support\Str;
@endphp

<!-- Table Card -->
<div class="modern-card">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">#</th>
                    <th>{{ __('messages.partner.user_management.employees.employee_name') }}</th>
                    <th>{{ __('messages.partner.user_management.employees.username') }}</th>
                    <th>{{ __('messages.partner.user_management.employees.email') }}</th>
                    <th>{{ __('messages.partner.user_management.employees.role') }}</th>
                    <th class="text-center">{{ __('messages.partner.user_management.employees.status') }}</th>
                    <th class="text-center" style="width: 180px;">
                        {{ __('messages.partner.user_management.employees.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody id="employeeTableBody">
                @forelse ($employees as $index => $employee)
                    <tr data-role="{{ $employee->role }}" class="table-row">
                        <!-- Number -->
                        <td class="text-center text-muted">{{ $employees->firstItem() + $index }}</td>

                        <!-- Employee Name with Avatar -->
                        <td>
                            <div class="user-info-cell">
                                @php
                                    $img = $employee->image
                                        ? (Str::startsWith($employee->image, ['http://', 'https://'])
                                            ? $employee->image
                                            : asset('storage/' . $employee->image))
                                        : null;
                                @endphp

                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $employee->name }}" class="user-avatar" loading="lazy">
                                @else
                                    <div class="user-avatar-placeholder">
                                        <span class="material-symbols-outlined">person</span>
                                    </div>
                                @endif
                                <span class="data-name">{{ $employee->name }}</span>
                            </div>
                        </td>

                        <!-- Username -->
                        <td>
                            <span class="text-secondary">{{ $employee->user_name }}</span>
                        </td>

                        <!-- Email -->
                        <td>
                            <a href="mailto:{{ $employee->email }}" class="table-link">
                                {{ $employee->email }}
                            </a>
                        </td>

                        <!-- Role -->
                        <td>
                            <span class="badge-modern badge-info">
                                {{ $employee->role }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="text-center">
                            @if((int) $employee->is_active === 1)
                                <span class="badge-modern badge-success">
                                    {{ __('messages.partner.user_management.employees.active') }}
                                </span>
                            @else
                                <span class="badge-modern badge-danger">
                                    {{ __('messages.partner.user_management.employees.non_active') }}
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="{{ route('partner.user-management.employees.show', $employee->id) }}"
                                    class="btn-table-action view"
                                    title="{{ __('messages.partner.user_management.employees.view_details') ?? 'View Details' }}">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>
                                {{-- Uncomment jika edit & delete diperlukan --}}
                                {{-- <a href="{{ route('partner.user-management.employees.edit', $employee->id) }}"
                                    class="btn-table-action edit"
                                    title="{{ __('messages.partner.user_management.employees.edit') }}">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                <button onclick="deleteEmployee({{ $employee->id }})" 
                                    class="btn-table-action delete"
                                    title="{{ __('messages.partner.user_management.employees.delete') }}">
                                    <span class="material-symbols-outlined">delete</span>
                                </button> --}}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">person_off</span>
                                <h4>{{ __('messages.partner.user_management.employees.no_employees') ?? 'No employees found' }}</h4>
                                <p>{{ __('messages.partner.user_management.employees.add_first_employee') ?? 'Add your first employee to get started' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($employees->hasPages())
        <div class="table-pagination">
            {{ $employees->withQueryString()->links() }}
        </div>
    @endif
</div>