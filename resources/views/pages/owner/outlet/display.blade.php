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
                    <th>{{ __('messages.owner.outlet.all_outlets.outlet_name') }}</th>
                    <th>{{ __('messages.owner.outlet.all_outlets.username') }}</th>
                    <th>{{ __('messages.owner.outlet.all_outlets.email') }}</th>
                    <th>Address</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 200px;">
                        {{ __('messages.owner.outlet.all_outlets.actions') }}
                    </th>
                </tr>
            </thead>

            <tbody id="outletTableBody">
                @forelse ($outlets as $index => $outlet)
                    @php
                        $img = $outlet->logo
                            ? (Str::startsWith($outlet->logo, ['http://', 'https://'])
                                ? $outlet->logo
                                : asset('storage/' . $outlet->logo))
                            : null;
                        $isActive = (int) $outlet->is_active === 1;
                    @endphp

                    <tr data-status="{{ $isActive ? 'active' : 'inactive' }}" class="table-row">
                        <td class="text-center text-muted">{{ $outlets->firstItem() + $index }}</td>

                        <td>
                            <div class="user-info-cell">
                                @if ($img)
                                    <img src="{{ $img }}" alt="{{ $outlet->name }}" class="user-avatar"
                                        loading="lazy">
                                @else
                                    <div class="user-avatar-placeholder">
                                        <span class="material-symbols-outlined">store</span>
                                    </div>
                                @endif
                                <span class="data-name">{{ $outlet->name }}</span>
                            </div>
                        </td>

                        <td><span class="text-secondary">{{ $outlet->username }}</span></td>

                        <td>
                            <a href="mailto:{{ $outlet->email }}" class="table-link">
                                {{ $outlet->email }}
                            </a>
                        </td>

                        <td><span class="text-secondary">{{ $outlet->city }}</span></td>

                        <td class="text-center">
                            @if ($isActive)
                                <span class="badge-modern badge-success badge-sm">
                                    {{ __('messages.owner.outlet.all_outlets.active') }}
                                </span>
                            @else
                                <span class="badge-modern badge-danger badge-sm">
                                    {{ __('messages.owner.outlet.all_outlets.inactive') }}
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            <div class="table-actions">
                                {{-- Tombol Tables --}}
                                <a href="{{ route('owner.user-owner.tables.index') }}" class="btn-table-action primary"
                                    title="Tables">
                                    <span class="material-symbols-outlined">table_restaurant</span>
                                </a>

                                <a href="{{ route('owner.user-owner.outlets.show', $outlet->id) }}"
                                    class="btn-table-action view"
                                    title="{{ __('messages.owner.outlet.all_outlets.view_details') ?? 'View Details' }}">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>

                                <a href="{{ route('owner.user-owner.outlets.edit', $outlet->id) }}"
                                    class="btn-table-action edit"
                                    title="{{ __('messages.owner.outlet.all_outlets.edit') }}">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>

                                <button type="button" onclick="deleteOutlet({{ $outlet->id }})"
                                    class="btn-table-action delete"
                                    title="{{ __('messages.owner.outlet.all_outlets.delete') }}">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">store</span>
                                <h4>{{ __('messages.owner.outlet.all_outlets.no_outlets') ?? 'No outlets found' }}</h4>
                                <p>{{ __('messages.owner.outlet.all_outlets.add_first_outlet') ?? 'Add your first outlet to get started' }}
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
        {{-- Mobile Header with Avatar & Search --}}
        <div class="mobile-header-section">
            <div class="mobile-header-card">
                <div class="mobile-header-content">
                    <div class="mobile-header-left">
                        <h2 class="mobile-header-title">Outlet Directory</h2>
                        <p class="mobile-header-subtitle">{{ $outlets->total() }} Total Outlets</p>
                    </div>
                    <div class="mobile-header-right">
                        <div class="mobile-header-avatar-placeholder">
                            <span class="material-symbols-outlined">store</span>
                        </div>
                    </div>
                </div>

                {{-- Mobile Search Box --}}
                <div class="mobile-search-wrapper">
                    <div class="mobile-search-box">
                        <span class="mobile-search-icon">
                            <span class="material-symbols-outlined">search</span>
                        </span>
                        <input type="text" class="mobile-search-input" placeholder="Search outlets...">
                        <button type="button" class="mobile-filter-btn" onclick="toggleMobileFilter()">
                            <span class="material-symbols-outlined">tune</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile Outlet List --}}
        <div class="mobile-outlet-list">
            @forelse ($outlets as $outlet)
                @php
                    $img = $outlet->logo
                        ? (Str::startsWith($outlet->logo, ['http://', 'https://'])
                            ? $outlet->logo
                            : asset('storage/' . $outlet->logo))
                        : null;
                    $isActive = (int) $outlet->is_active === 1;
                @endphp

                <div class="outlet-card-wrapper">
                    {{-- Swipe Actions Background --}}
                    <div class="swipe-actions">
                        {{-- Tombol Tables --}}
                        <a href="{{ route('owner.user-owner.tables.index') }}" class="swipe-action"
                            style="background:#8c1000;">
                            <span class="material-symbols-outlined">table_restaurant</span>
                        </a>

                        <a href="{{ route('owner.user-owner.outlets.edit', $outlet->id) }}" class="swipe-action edit">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                        <button type="button" onclick="deleteOutlet({{ $outlet->id }})" class="swipe-action delete">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>

                    {{-- Card Content --}}
                    <a href="{{ route('owner.user-owner.outlets.show', $outlet->id) }}" class="outlet-card-link">
                        <div class="outlet-card-clickable">
                            <div class="outlet-card__left">
                                <div class="outlet-card__avatar">
                                    @if ($img)
                                        <img src="{{ $img }}" alt="{{ $outlet->name }}" loading="lazy">
                                    @else
                                        <div class="user-avatar-placeholder">
                                            <span class="material-symbols-outlined">store</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="outlet-card__info">
                                    <div class="outlet-card__name">{{ $outlet->name }}</div>
                                    <div class="outlet-card__details">
                                        <span class="detail-text">{{ $outlet->username ?? '-' }}</span>
                                        <span class="detail-separator">•</span>
                                        <span class="detail-text">{{ $outlet->city ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="outlet-card__right">
                                <span class="material-symbols-outlined chevron">chevron_right</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="table-empty-state">
                    <span class="material-symbols-outlined">store</span>
                    <h4>{{ __('messages.owner.outlet.all_outlets.no_outlets') ?? 'No outlets found' }}</h4>
                    <p>{{ __('messages.owner.outlet.all_outlets.add_first_outlet') ?? 'Add your first outlet to get started' }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- =======================
    PAGINATION
  ======================= --}}
    @if ($outlets->hasPages())
        <div class="table-pagination">
            {{ $outlets->links() }}
        </div>
    @endif

</div>

{{-- Floating Add Button (Mobile Only) --}}
<a href="{{ route('owner.user-owner.outlets.create') }}" class="btn-add-outlet-mobile">
    <span class="material-symbols-outlined">add</span>
</a>

{{-- Mobile Filter Modal --}}
<div id="mobileFilterModal" class="mobile-filter-modal">
    <div class="filter-modal-backdrop" onclick="closeMobileFilter()"></div>
    <div class="filter-modal-content">
        <div class="filter-modal-header">
            <div class="filter-header-left">
                <span class="material-symbols-outlined filter-header-icon">tune</span>
                <h3>Filter Outlets</h3>
            </div>
            <button type="button" class="filter-close-btn" onclick="closeMobileFilter()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="filter-modal-body">
            {{-- Pill Filter untuk Status --}}
            <div class="modal-filter-pills">
                {{-- All Status --}}
                <a href="javascript:void(0)" onclick="setStatusFilter('')"
                    class="modal-pill {{ !request('status') ? 'active' : '' }}">
                    <div class="pill-left">
                        <div class="pill-icon-wrapper {{ !request('status') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">store</span>
                        </div>
                        <div class="pill-info">
                            <span class="pill-text">All Outlets</span>
                            <span class="pill-subtext">View all outlets</span>
                        </div>
                    </div>
                    <div class="pill-right">
                        @if (!request('status'))
                            <span class="material-symbols-outlined pill-check">check_circle</span>
                        @endif
                    </div>
                </a>

                {{-- Divider --}}
                <div class="filter-divider">
                    <span>Status</span>
                </div>

                {{-- Active Status --}}
                <a href="javascript:void(0)" onclick="setStatusFilter('active')"
                    class="modal-pill {{ request('status') === 'active' ? 'active' : '' }}">
                    <div class="pill-left">
                        <div class="pill-icon-wrapper {{ request('status') === 'active' ? 'active' : '' }}">
                            <span class="material-symbols-outlined">check_circle</span>
                        </div>
                        <div class="pill-info">
                            <span class="pill-text">Active Outlets</span>
                            <span class="pill-subtext">Currently operational</span>
                        </div>
                    </div>
                    <div class="pill-right">
                        @if (request('status') === 'active')
                            <span class="material-symbols-outlined pill-check">check_circle</span>
                        @endif
                    </div>
                </a>

                {{-- Inactive Status --}}
                <a href="javascript:void(0)" onclick="setStatusFilter('inactive')"
                    class="modal-pill {{ request('status') === 'inactive' ? 'active' : '' }}">
                    <div class="pill-left">
                        <div class="pill-icon-wrapper {{ request('status') === 'inactive' ? 'active' : '' }}">
                            <span class="material-symbols-outlined">cancel</span>
                        </div>
                        <div class="pill-info">
                            <span class="pill-text">Inactive Outlets</span>
                            <span class="pill-subtext">Not operational</span>
                        </div>
                    </div>
                    <div class="pill-right">
                        @if (request('status') === 'inactive')
                            <span class="material-symbols-outlined pill-check">check_circle</span>
                        @endif
                    </div>
                </a>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="filter-modal-footer">
            <button type="button" class="btn-clear-filter"
                onclick="setStatusFilter(''); if(document.querySelector('.mobile-search-input')) document.querySelector('.mobile-search-input').value = ''; if(document.getElementById('searchInput')) document.getElementById('searchInput').value = '';">
                <span class="material-symbols-outlined">restart_alt</span>
                Clear Filter
            </button>
        </div>
    </div>
</div>
