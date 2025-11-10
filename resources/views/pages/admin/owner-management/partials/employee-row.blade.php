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
        @if ($employee->is_active)
            <span class="badge badge-success badge-pill">Active</span>
        @else
            <span class="badge badge-danger badge-pill">Inactive</span>
        @endif
    </td>
</tr>