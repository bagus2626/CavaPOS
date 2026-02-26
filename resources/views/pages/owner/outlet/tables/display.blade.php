@php use Illuminate\Support\Str; @endphp

<link rel="stylesheet" href="{{ asset('css/mobile-owner.css') }}">

<div>

    {{-- ======================= DESKTOP: TABLE ======================= --}}
    <div class="modern-card data-table-wrapper only-desktop">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width:60px;">#</th>
                    <th>{{ __('messages.partner.outlet.table_management.tables.table_no') }}</th>
                    <th>{{ __('messages.owner.layout.outlets') }}</th>
                    <th>{{ __('messages.partner.outlet.table_management.tables.class_type') }}</th>
                    <th>{{ __('messages.partner.outlet.table_management.tables.description') }}</th>
                    <th class="text-center">{{ __('messages.partner.outlet.table_management.tables.status') }}</th>
                    <th class="text-center">{{ __('messages.partner.outlet.table_management.tables.picture') }}</th>
                    <th class="text-center">
                        {{ __('messages.partner.outlet.table_management.tables.table_barcode') }}
                        <a href="{{ route('owner.user-owner.tables.generate-all-barcode') }}" target="_blank"
                            class="table-link" title="Download All Barcodes">
                            <span class="material-symbols-outlined"
                                style="font-size:1.25rem;vertical-align:middle;">picture_as_pdf</span>
                        </a>
                    </th>
                    <th class="text-center" style="width:160px;">{{ __('messages.partner.outlet.table_management.tables.actions') }}</th>
                </tr>
            </thead>
            <tbody id="tableTableBody">
                @forelse ($tables as $index => $table)
                    <tr data-category="{{ $table->table_class }}" class="table-row">
                        <td class="text-center text-muted">{{ $tables->firstItem() + $index }}</td>
                        <td><span class="fw-600">{{ $table->table_no }}</span></td>
                        <td><span class="text-secondary">{{ $table->partner->name ?? '-' }}</span></td>
                        <td><span class="text-secondary">{{ $table->table_class }}</span></td>
                        <td><span class="text-secondary">{{ $table->description ?: '-' }}</span></td>
                        <td class="text-center">
                            @if ($table->status === 'available')
                                <span class="badge-modern badge-success">{{ __('messages.partner.outlet.table_management.tables.available') }}</span>
                            @elseif ($table->status === 'occupied')
                                <span class="badge-modern badge-warning">{{ __('messages.partner.outlet.table_management.tables.occupied') }}</span>
                            @elseif ($table->status === 'reserved')
                                <span class="badge-modern badge-info">{{ __('messages.partner.outlet.table_management.tables.reserved') }}</span>
                            @elseif ($table->status === 'not_available')
                                <span class="badge-modern badge-danger">{{ __('messages.partner.outlet.table_management.tables.not_available') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $images = is_array($table->images) ? $table->images : [];
                                $images = array_filter($images, fn($img) => is_array($img) && isset($img['path']));
                            @endphp
                            @if (count($images) > 0)
                                <div class="table-images-cell">
                                    @foreach ($images as $image)
                                        <a href="{{ asset($image['path']) }}" target="_blank" class="table-image-link">
                                            <img src="{{ asset($image['path']) }}" class="table-thumbnail"
                                                loading="lazy">
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">{{ __('messages.partner.outlet.table_management.tables.no_images') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button onclick="generateBarcode({{ $table->id }})" class="btn-table-action primary"
                                title="{{ __('messages.partner.outlet.table_management.tables.table_barcode') }}">
                                <span class="material-symbols-outlined">qr_code</span>
                            </button>
                        </td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="{{ route('owner.user-owner.tables.show', $table->id) }}"
                                    class="btn-table-action view"
                                    title="{{ __('messages.partner.outlet.table_management.tables.view_complete_information') ?? 'View' }}">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>
                                <a href="{{ route('owner.user-owner.tables.edit', $table->id) }}"
                                    class="btn-table-action edit"
                                    title="{{ __('messages.partner.outlet.table_management.tables.edit') }}">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                <button onclick="deleteTable({{ $table->id }})" class="btn-table-action delete"
                                    title="{{ __('messages.partner.outlet.table_management.tables.delete') }}">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">table_restaurant</span>
                                <h4>{{ __('messages.partner.outlet.table_management.tables.no_tables') ?? 'No tables found' }}</h4>
                                <p>{{ __('messages.partner.outlet.table_management.tables.add_first_table') ?? 'Add your first table to get started' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ======================= MOBILE: HEADER + SEARCH + CARDS ======================= --}}
    <div class="only-mobile">

        {{-- Mobile Header with Search --}}
        <div class="mobile-header-section">
            <div class="mobile-header-card">

                <div class="mobile-header-content">
                    <div class="mobile-header-left">
                        <h2 class="mobile-header-title">{{ __('messages.owner.outlet.tables.page_title') }}</h2>
                        <p class="mobile-header-subtitle">{{ $tables->total() }} {{ __('messages.partner.outlet.table_management.tables.table_list') }}</p>
                    </div>
                    <div class="mobile-header-right">
                        <div class="mobile-header-avatar-placeholder">
                            <span class="material-symbols-outlined">table_restaurant</span>
                        </div>
                    </div>
                </div>

                {{-- Mobile Search Box --}}
                <div class="mobile-search-wrapper">

                    <div class="mobile-search-box">
                        <span class="mobile-search-icon">
                            <span class="material-symbols-outlined">search</span>
                        </span>
                        <input type="text" class="mobile-search-input" id="mobileTableSearchInput"
                            placeholder="{{ __('messages.partner.outlet.table_management.tables.search_tables') }}">
                    </div>

                    {{-- Dropdown Filter Row --}}
                    <div class="mobile-filter-row">
                        <div class="mobile-select-wrapper">
                            <span class="material-symbols-outlined mobile-select-icon">store</span>
                            <select id="mobileOutletFilter" class="mobile-select-input"
                                onchange="onMobileOutletChange(this.value)">
                                <option value="">{{ __('messages.owner.user_management.employees.all_outlets') }}</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined mobile-select-arrow">expand_more</span>
                        </div>

                        <div class="mobile-select-wrapper">
                            <span class="material-symbols-outlined mobile-select-icon">category</span>
                            <select id="mobileTableClassFilter" class="mobile-select-input"
                                onchange="onMobileClassChange(this.value)">
                                <option value="">{{ __('messages.partner.outlet.table_management.tables.all_table_classes') }}</option>
                                @foreach ($table_classes as $class)
                                    <option value="{{ $class }}">{{ $class }}</option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined mobile-select-arrow">expand_more</span>
                        </div>
                    </div>

                </div>{{-- end mobile-search-wrapper --}}

            </div>{{-- end mobile-header-card --}}
        </div>{{-- end mobile-header-section --}}

        {{-- Mobile Table List --}}
        <div class="mobile-table-list" id="mobileTableList">
            @forelse ($tables as $table)
                @php
                    $images = is_array($table->images) ? $table->images : [];
                    $images = array_filter($images, fn($img) => is_array($img) && isset($img['path']));
                    $firstImage = count($images) > 0 ? asset(array_values($images)[0]['path']) : null;
                @endphp
                <div class="outlet-card-wrapper">
                    <div class="swipe-actions">
                        <button onclick="generateBarcode({{ $table->id }})" class="swipe-action"
                            style="background:#8c1000;">
                            <span class="material-symbols-outlined">qr_code</span>
                        </button>
                        <a href="{{ route('owner.user-owner.tables.edit', $table->id) }}" class="swipe-action edit">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                        <button type="button" onclick="deleteTable({{ $table->id }})"
                            class="swipe-action delete">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>

                    <a href="{{ route('owner.user-owner.tables.show', $table->id) }}" class="outlet-card-link">
                        <div class="outlet-card-clickable">
                            <div class="outlet-card__left">
                                <div class="outlet-card__avatar">
                                    @if ($firstImage)
                                        <img src="{{ $firstImage }}" alt="Table {{ $table->table_no }}"
                                            loading="lazy">
                                    @else
                                        <div class="user-avatar-placeholder">
                                            <span class="material-symbols-outlined">table_restaurant</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="outlet-card__info">
                                    <div class="outlet-card__name">{{ __('messages.partner.outlet.table_management.tables.table_no') }} {{ $table->table_no }}</div>
                                    <div class="outlet-card__details">
                                        <span class="detail-text">{{ $table->table_class ?? '-' }}</span>
                                        <span class="detail-separator">•</span>
                                        <span class="detail-text">{{ $table->partner->name ?? '-' }}</span>
                                    </div>
                                    <div class="mt-1">
                                        @if ($table->status === 'available')
                                            <span class="badge-modern badge-success"
                                                style="font-size:0.7rem;">{{ __('messages.partner.outlet.table_management.tables.available') }}</span>
                                        @elseif ($table->status === 'occupied')
                                            <span class="badge-modern badge-warning"
                                                style="font-size:0.7rem;">{{ __('messages.partner.outlet.table_management.tables.occupied') }}</span>
                                        @elseif ($table->status === 'reserved')
                                            <span class="badge-modern badge-info"
                                                style="font-size:0.7rem;">{{ __('messages.partner.outlet.table_management.tables.reserved') }}</span>
                                        @elseif ($table->status === 'not_available')
                                            <span class="badge-modern badge-danger"
                                                style="font-size:0.7rem;">{{ __('messages.partner.outlet.table_management.tables.not_available') }}</span>
                                        @endif
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
                    <span class="material-symbols-outlined">table_restaurant</span>
                    <h4>{{ __('messages.partner.outlet.table_management.tables.no_tables') ?? 'No tables found' }}</h4>
                    <p>{{ __('messages.partner.outlet.table_management.tables.add_first_table') ?? 'Add your first table to get started' }}</p>
                </div>
            @endforelse
        </div>{{-- end mobile-table-list --}}

    </div>{{-- end only-mobile --}}

    {{-- ======================= PAGINATION ======================= --}}
    @if ($tables->hasPages())
        <div class="table-pagination">{{ $tables->withQueryString()->links() }}</div>
    @endif

</div>

{{-- Floating Add Button (Mobile Only) --}}
<a href="{{ route('owner.user-owner.tables.create') }}" class="btn-add-outlet-mobile">
    <span class="material-symbols-outlined">add</span>
</a>

{{-- Mobile Filter Modal --}}
<div id="mobileTableFilterModal" class="mobile-filter-modal">
    <div class="filter-modal-backdrop" onclick="closeMobileTableFilter()"></div>
    <div class="filter-modal-content">
        <div class="filter-modal-header">
            <div class="filter-header-left">
                <span class="material-symbols-outlined filter-header-icon">tune</span>
                <h3>{{ __('messages.partner.outlet.table_management.tables.all_table_classes') }}</h3>
            </div>
            <button type="button" class="filter-close-btn" onclick="closeMobileTableFilter()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="filter-modal-body">
            {{-- Filter by Table Class --}}
            <div class="filter-divider"><span>{{ __('messages.partner.outlet.table_management.tables.class_type') }}</span></div>
            <div class="modal-filter-pills">
                <a href="javascript:void(0)" onclick="setMobileTableClassFilter('')" class="modal-pill active"
                    id="mobilePillClass-all">
                    <div class="pill-left">
                        <div class="pill-icon-wrapper active">
                            <span class="material-symbols-outlined">table_restaurant</span>
                        </div>
                        <div class="pill-info">
                            <span class="pill-text">{{ __('messages.partner.outlet.table_management.tables.all_table_classes') }}</span>
                            <span class="pill-subtext">{{ __('messages.partner.outlet.table_management.tables.all_tables') }}</span>
                        </div>
                    </div>
                    <div class="pill-right">
                        <span class="material-symbols-outlined pill-check">check_circle</span>
                    </div>
                </a>
                @foreach ($table_classes as $class)
                    <a href="javascript:void(0)" onclick="setMobileTableClassFilter('{{ $class }}')"
                        class="modal-pill" id="mobilePillClass-{{ Str::slug($class) }}">
                        <div class="pill-left">
                            <div class="pill-icon-wrapper">
                                <span class="material-symbols-outlined">category</span>
                            </div>
                            <div class="pill-info">
                                <span class="pill-text">{{ $class }}</span>
                            </div>
                        </div>
                        <div class="pill-right"></div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="filter-modal-footer">
            <button type="button" class="btn-clear-filter"
                onclick="setMobileTableClassFilter(''); document.getElementById('mobileTableSearchInput').value = '';">
                <span class="material-symbols-outlined">restart_alt</span>
                {{ __('messages.partner.outlet.table_management.tables.delete_confirm_4') }}
            </button>
        </div>
    </div>
</div>

<style>
    .table-images-cell {
        display: flex;
        gap: .5rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .table-image-link {
        display: block;
        transition: transform .15s ease;
    }

    .table-image-link:hover {
        transform: scale(1.05);
    }

    .table-thumbnail {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
    }

    .btn-table-action.primary {
        background: rgba(140, 16, 0, .1);
        color: #8c1000;
    }

    .btn-table-action.primary:hover {
        background: #8c1000;
        color: #fff;
    }

    .mobile-table-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding: 0.75rem 0 0 0;
    }

    .btn-add-outlet-mobile {
        display: none;
    }

    @media (max-width: 768px) {
        .only-desktop {
            display: none !important;
        }

        .only-mobile {
            display: block !important;
        }

        .btn-add-outlet-mobile {
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #8c1000;
            color: #fff;
            box-shadow: 0 4px 16px rgba(140, 16, 0, 0.35);
            z-index: 100;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-add-outlet-mobile:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(140, 16, 0, 0.45);
        }

        .btn-add-outlet-mobile .material-symbols-outlined {
            font-size: 1.75rem;
        }
    }

    .mobile-filter-row {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .mobile-select-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        flex: 1;
        background: #fff;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        padding: 0 0.5rem;
        min-width: 0;
    }

    .mobile-select-icon {
        font-size: 1.1rem;
        color: #9ca3af;
        flex-shrink: 0;
        margin-right: 0.25rem;
    }

    .mobile-select-input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        font-size: 0.82rem;
        color: #374151;
        padding: 0.55rem 1.5rem 0.55rem 0;
        appearance: none;
        -webkit-appearance: none;
        min-width: 0;
        cursor: pointer;
    }

    .mobile-select-arrow {
        position: absolute;
        right: 0.4rem;
        font-size: 1rem;
        color: #9ca3af;
        pointer-events: none;
        flex-shrink: 0;
    }

    @media (min-width: 769px) {
        .only-mobile {
            display: none !important;
        }
    }
</style>