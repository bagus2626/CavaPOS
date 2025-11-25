<tr>
    <td class="text-left">
        <span class="text-bold-500">{{ $outlets->firstItem() + $index }}</span>
    </td>
    <td>
        <div class="media">
            @if ($outlet->logo)
                <img src="{{ $outlet->logo }}" alt="{{ $outlet->name }}"
                    class="rounded-circle mr-1"
                    style="width: 48px; height: 48px; object-fit: cover;">
            @else
                <div class="rounded-circle mr-1 d-flex align-items-center justify-content-center bg-light"
                    style="width: 48px; height: 48px;">
                    <i class="bx bx-store text-muted font-medium-3"></i>
                </div>
            @endif
            <div class="media-body">
                <h6 class="mb-0 text-bold-500">{{ $outlet->name }}</h6>
                <small class="text-muted">{{ $outlet->email }}</small>
            </div>
        </div>
    </td>
    <td>
        <span class="text-bold-500">{{ $outlet->username ?? 'N/A' }}</span>
    </td>
    <td>
        @if ($outlet->city && $outlet->subdistrict)
            <div class="text-bold-500">{{ $outlet->city }}</div>
            <small class="text-muted">{{ $outlet->subdistrict }}</small>
        @else
            <span class="text-muted">No location</span>
        @endif
    </td>
    <td>
        <div style="max-width: 300px;">
            @if ($outlet->address)
                <span class="line-clamp-2"
                    title="{{ $outlet->address }}">{{ $outlet->address }}</span>
            @else
                <span class="text-muted">No address</span>
            @endif
        </div>
    </td>
    <td>
        @if ($outlet->is_active_admin)
            <span class="badge badge-success badge-pill">Active</span>
        @else
            <span class="badge badge-danger badge-pill"
                @if ($outlet->deactivation_reason) 
                    data-toggle="tooltip" 
                    data-placement="top"
                    title="{{ $outlet->deactivation_reason }}" 
                @endif>
                Inactive
            </span>
        @endif
    </td>
    <td>
        <div class="d-flex align-items-center">
            <div class="custom-control custom-switch custom-switch-success">
                <input type="checkbox"
                    class="custom-control-input outlet-status-toggle"
                    id="outletSwitch{{ $outlet->id }}"
                    data-outlet-id="{{ $outlet->id }}"
                    data-owner-id="{{ $owner->id }}"
                    data-outlet-name="{{ $outlet->name }}"
                    {{ $outlet->is_active_admin ? 'checked' : '' }}>
                <label class="custom-control-label"
                    for="outletSwitch{{ $outlet->id }}"></label>
            </div>
        </div>
    </td>
    <td>
        <a href="{{ route('admin.owner-list.outlet-data', ['ownerId' => $owner->id, 'outletId' => $outlet->id]) }}"
            title="View Outlets Data">
            <i class="badge-circle badge-circle-light-secondary bx bx-show font-medium-1"></i>
        </a>
    </td>
</tr>