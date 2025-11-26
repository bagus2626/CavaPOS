<tr>
    <td class="text-left">
        <span class="text-bold-500">{{ $employees->firstItem() + $index }}</span>
    </td>
    <td>
        <div class="media align-items-center">
            @if ($employee->image)
                <img src="{{ asset($employee->image) }}" alt="{{ $employee->name }}" class="rounded-circle mr-1"
                    style="width: 48px; height: 48px; object-fit: cover;">
            @else
                <div class="rounded-circle mr-1 d-flex align-items-center justify-content-center bg-light"
                    style="width: 48px; height: 48px;">
                    <i class="bx bx-user text-muted font-medium-3"></i>
                </div>
            @endif
            <div class="media-body">
                <h6 class="mb-0 text-bold-500">{{ $employee->name }}</h6>
            </div>
        </div>
    </td>
    <td>
        <span class="text-muted">{{ $employee->user_name }}</span>
    </td>
    <td>{{ $employee->email }}</td>
    <td>
        @if ($employee->role === 'CASHIER')
            <span class="badge badge-primary badge-pill">Cashier</span>
        @elseif($employee->role === 'KITCHEN')
            <span class="badge badge-warning badge-pill">Kitchen</span>
        @else
            <span class="badge badge-secondary badge-pill">{{ $employee->role }}</span>
        @endif
    </td>
    <td>
        @if ($employee->is_active_admin)
            <span class="badge badge-success badge-pill">Active</span>
        @else
            <span class="badge badge-danger badge-pill"
                @if ($employee->deactivation_reason) 
                    data-toggle="tooltip" 
                    data-placement="top"
                    title="{{ $employee->deactivation_reason }}" 
                @endif>
                Inactive
            </span>
        @endif
    </td>
    <td>
        <div class="d-flex align-items-center">
            <div class="custom-control custom-switch custom-switch-success">
                <input type="checkbox"
                    class="custom-control-input employee-status-toggle"
                    id="employeeSwitch{{ $employee->id }}"
                    data-employee-id="{{ $employee->id }}"
                    data-employee-name="{{ $employee->name }}"
                    {{ $employee->is_active_admin ? 'checked' : '' }}>
                <label class="custom-control-label"
                    for="employeeSwitch{{ $employee->id }}"></label>
            </div>
        </div>
    </td>
</tr>