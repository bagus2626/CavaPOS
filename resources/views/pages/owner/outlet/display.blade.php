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
                    <th>{{ __('messages.owner.outlet.all_outlets.outlet_name') }}</th>
                    <th>{{ __('messages.owner.outlet.all_outlets.username') }}</th>
                    <th>{{ __('messages.owner.outlet.all_outlets.email') }}</th>
                    <th>Address</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 180px;">
                        {{ __('messages.owner.outlet.all_outlets.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody id="outletTableBody">
                @forelse ($outlets as $index => $outlet)
                    <tr data-status="{{ (int) $outlet->is_active === 1 ? 'active' : 'inactive' }}" class="table-row">
                        <!-- Number -->
                        <td class="text-center text-muted">{{ $outlets->firstItem() + $index }}</td>

                        <!-- Outlet Name with Avatar -->
                        <td>
                            <div class="user-info-cell">
                                @php
                                    $img = $outlet->logo
                                        ? (Str::startsWith($outlet->logo, ['http://', 'https://'])
                                            ? $outlet->logo
                                            : asset('storage/' . $outlet->logo))
                                        : null;
                                @endphp

                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $outlet->name }}" class="user-avatar" loading="lazy">
                                @else
                                    <div class="user-avatar-placeholder">
                                        <span class="material-symbols-outlined">store</span>
                                    </div>
                                @endif
                                <span class="data-name">{{ $outlet->name }}</span>
                            </div>
                        </td>

                        <!-- Username -->
                        <td>
                            <span class="text-secondary">{{ $outlet->username }}</span>
                        </td>

                        <!-- Email -->
                        <td>
                            <a href="mailto:{{ $outlet->email }}" class="table-link">
                                {{ $outlet->email }}
                            </a>
                        </td>

                        <!-- Address -->
                        <td>
                            <span class="text-secondary">{{ $outlet->city }}</span>
                        </td>

                        <!-- Status -->
                        <td class="text-center">
                                <!-- Outlet Status -->
                                @if((int) $outlet->is_active === 1)
                                    <span class="badge-modern badge-success badge-sm">
                                        {{ __('messages.owner.outlet.all_outlets.active') }}
                                    </span>
                                @else
                                    <span class="badge-modern badge-danger badge-sm">
                                        {{ __('messages.owner.outlet.all_outlets.inactive') }}
                                    </span>
                                @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="{{ route('owner.user-owner.outlets.show', $outlet->id) }}"
                                    class="btn-table-action view"
                                    title="{{ __('messages.owner.user_management.employees.view_details') ?? 'View Details' }}">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>
                                <a href="{{ route('owner.user-owner.outlets.edit', $outlet->id) }}"
                                    class="btn-table-action edit"
                                    title="{{ __('messages.owner.outlet.all_outlets.edit') }}">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                <button onclick="deleteOutlet({{ $outlet->id }})" 
                                    class="btn-table-action delete"
                                    title="{{ __('messages.owner.outlet.all_outlets.delete') }}">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">store_off</span>
                                <h4>{{ __('messages.owner.outlet.all_outlets.no_outlets') ?? 'No outlets found' }}</h4>
                                <p>{{ __('messages.owner.outlet.all_outlets.add_first_outlet') ?? 'Add your first outlet to get started' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($outlets->hasPages())
        <div class="table-pagination">
            {{ $outlets->links() }}
        </div>
    @endif
</div>