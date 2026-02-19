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
                    <th class="text-center" style="width:60px;">#</th>
                    <th>{{ __('messages.owner.products.promotions.promo_code') }}</th>
                    <th>{{ __('messages.owner.products.promotions.promo_name') }}</th>
                    <th class="text-center">{{ __('messages.owner.products.promotions.promo_type') }}</th>
                    <th>{{ __('messages.owner.products.promotions.value') }}</th>
                    <th>{{ __('messages.owner.products.promotions.active_date') }}</th>
                    <th>{{ __('messages.owner.products.promotions.active_day') }}</th>
                    <th class="text-center">{{ __('messages.owner.products.promotions.status') }}</th>
                    <th class="text-center" style="width:220px;">{{ __('messages.owner.products.promotions.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody id="promotionTableBody">
                @forelse($promotions as $index => $promotion)
                    @php
                        $daysMap = [
                            'sun' => __('messages.owner.products.promotions.sunday'),
                            'mon' => __('messages.owner.products.promotions.monday'),
                            'tue' => __('messages.owner.products.promotions.tuesday'),
                            'wed' => __('messages.owner.products.promotions.wednesday'),
                            'thu' => __('messages.owner.products.promotions.thursday'),
                            'fri' => __('messages.owner.products.promotions.friday'),
                            'sat' => __('messages.owner.products.promotions.saturday'),
                        ];
                        $activeDaysArr = is_array($promotion->active_days) ? $promotion->active_days : [];
                        $activeDays =
                            count($activeDaysArr) === 7
                                ? 'Setiap Hari'
                                : collect($activeDaysArr)->map(fn($d) => $daysMap[$d] ?? $d)->join(', ');

                        // status badge
                        if (!$promotion->is_active) {
                            $label = __('messages.owner.products.promotions.inactive');
                            $badgeClass = 'badge-danger';
                        } else {
                            $now = now();
                            $start = $promotion->start_date;
                            $end = $promotion->end_date;
                            if ($start && $now->lt($start)) {
                                $label = __('messages.owner.products.promotions.will_be_active');
                                $badgeClass = 'badge-warning';
                            } elseif ($end && $now->gt($end)) {
                                $label = __('messages.owner.products.promotions.expired');
                                $badgeClass = 'badge-danger';
                            } else {
                                $label = __('messages.owner.products.promotions.active');
                                $badgeClass = 'badge-success';
                            }
                        }
                    @endphp

                    <tr class="table-row">
                        <td class="text-center text-muted">{{ $promotions->firstItem() + $index }}</td>
                        <td><span class="mono fw-600">{{ $promotion->promotion_code }}</span></td>
                        <td><span class="fw-600">{{ $promotion->promotion_name }}</span></td>
                        <td class="text-center">
                            {{ $promotion->promotion_type === 'percentage' ? __('messages.owner.products.promotions.percentage') : __('messages.owner.products.promotions.reduced_fare') }}
                        </td>
                        <td>
                            <span class="fw-600">
                                @if ($promotion->promotion_type === 'percentage')
                                    {{ number_format($promotion->promotion_value, 0, ',', '.') }}%
                                @else
                                    Rp {{ number_format($promotion->promotion_value, 0, ',', '.') }}
                                @endif
                            </span>
                        </td>
                        <td>
                            <div class="text-secondary" style="font-size:0.875rem;">
                                @if ($promotion->start_date && $promotion->end_date)
                                    {{ $promotion->start_date->translatedFormat('d F Y') }}
                                    ({{ $promotion->start_date->format('H:i') }})
                                    –
                                    {{ $promotion->end_date->translatedFormat('d F Y') }}
                                    ({{ $promotion->end_date->format('H:i') }})
                                @elseif($promotion->start_date)
                                    {{ __('messages.owner.products.promotions.start') }}
                                    {{ $promotion->start_date->translatedFormat('d F Y') }}
                                    ({{ $promotion->start_date->format('H:i') }})
                                @elseif($promotion->end_date)
                                    {{ __('messages.owner.products.promotions.until') }}
                                    {{ $promotion->end_date->translatedFormat('d F Y') }}
                                    ({{ $promotion->end_date->format('H:i') }})
                                @else
                                    <span
                                        class="text-muted">{{ __('messages.owner.products.promotions.unlimited') }}</span>
                                @endif
                            </div>
                        </td>
                        <td><span class="text-secondary" style="font-size:0.875rem;">{{ $activeDays }}</span></td>
                        <td class="text-center"><span
                                class="badge-modern {{ $badgeClass }}">{{ $label }}</span></td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="{{ route('owner.user-owner.promotions.show', $promotion->id) }}"
                                    class="btn-table-action view"
                                    title="{{ __('messages.owner.products.promotions.detail') }}">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>
                                <a href="{{ route('owner.user-owner.promotions.edit', $promotion->id) }}"
                                    class="btn-table-action edit"
                                    title="{{ __('messages.owner.products.promotions.edit') }}">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>

                                <form action="{{ route('owner.user-owner.promotions.destroy', $promotion->id) }}"
                                    method="POST" class="d-inline js-delete-promo-form"
                                    data-name="{{ $promotion->promotion_name }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-table-action delete"
                                        title="{{ __('messages.owner.products.promotions.delete') }}">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">local_offer</span>
                                <h4>{{ __('messages.owner.products.promotions.no_promotions') ?? 'No promotions found' }}
                                </h4>
                                <p>{{ __('messages.owner.products.promotions.add_first_promotion') ?? 'Add your first promotion to get started' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- =======================
    MOBILE: HEADER + SEARCH + CARDS
  ======================= --}}
    <div class="only-mobile">
        {{-- Mobile Header with Avatar & Search --}}
        <div class="mobile-header-section">
            <div class="mobile-header-card">
                <div class="mobile-header-content">
                    <div class="mobile-header-left">
                        <h2 class="mobile-header-title">Promotions</h2>
                        <p class="mobile-header-subtitle">{{ $promotions->total() }} Total Promotions</p>
                    </div>
                    <div class="mobile-header-right">
                        <div class="mobile-header-avatar-placeholder">
                            <span class="material-symbols-outlined">local_offer</span>
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
                                placeholder="{{ __('messages.owner.products.promotions.search_placeholder') }}"
                                oninput="searchFilter(this, 600)">
                            <button type="button" class="mobile-filter-btn" onclick="toggleMobileFilter()">
                                <span class="material-symbols-outlined">tune</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Mobile Promotion List (MENGGUNAKAN CLASS DARI CSS UNIVERSAL) --}}
        <div class="mobile-employee-list">
            @forelse ($promotions as $promotion)
                @php
                    // Gunakan logic yang sama untuk badge
                    $daysMap = [
                        'sun' => __('messages.owner.products.promotions.sunday'),
                        'mon' => __('messages.owner.products.promotions.monday'),
                        'tue' => __('messages.owner.products.promotions.tuesday'),
                        'wed' => __('messages.owner.products.promotions.wednesday'),
                        'thu' => __('messages.owner.products.promotions.thursday'),
                        'fri' => __('messages.owner.products.promotions.friday'),
                        'sat' => __('messages.owner.products.promotions.saturday'),
                    ];
                    $activeDaysArr = is_array($promotion->active_days) ? $promotion->active_days : [];
                    $activeDays =
                        count($activeDaysArr) === 7
                            ? 'Setiap Hari'
                            : collect($activeDaysArr)->map(fn($d) => $daysMap[$d] ?? $d)->join(', ');

                    if (!$promotion->is_active) {
                        $label = __('messages.owner.products.promotions.inactive');
                        $badgeClass = 'badge-danger';
                    } else {
                        $now = now();
                        $start = $promotion->start_date;
                        $end = $promotion->end_date;
                        if ($start && $now->lt($start)) {
                            $label = __('messages.owner.products.promotions.will_be_active');
                            $badgeClass = 'badge-warning';
                        } elseif ($end && $now->gt($end)) {
                            $label = __('messages.owner.products.promotions.expired');
                            $badgeClass = 'badge-danger';
                        } else {
                            $label = __('messages.owner.products.promotions.active');
                            $badgeClass = 'badge-success';
                        }
                    }
                @endphp

                <div class="employee-card-wrapper">
                    {{-- Swipe Actions Background --}}
                    <div class="swipe-actions">
                        <a href="{{ route('owner.user-owner.promotions.edit', $promotion->id) }}"
                            class="swipe-action edit">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                        <button type="button"
                            onclick="deletePromotion({{ $promotion->id }}, '{{ $promotion->promotion_name }}')"
                            class="swipe-action delete">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>

                    {{-- Card Content --}}
                    <a href="{{ route('owner.user-owner.promotions.show', $promotion->id) }}"
                        class="employee-card-link">
                        <div class="employee-card-clickable">
                            <div class="employee-card__left">
                                <div class="employee-card__avatar">
                                    <div class="user-avatar-placeholder">
                                        <span class="material-symbols-outlined">local_offer</span>
                                    </div>
                                </div>

                                <div class="employee-card__info">
                                    <div class="employee-card__name">{{ $promotion->promotion_name }}</div>
                                    <div class="employee-card__details">
                                        <span class="detail-text mono">{{ $promotion->promotion_code }}</span>
                                        <span class="detail-separator">•</span>
                                        <span class="detail-text">
                                            @if ($promotion->promotion_type === 'percentage')
                                                {{ number_format($promotion->promotion_value, 0, ',', '.') }}%
                                            @else
                                                Rp {{ number_format($promotion->promotion_value, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </div>
                                    <div style="margin-top: 6px;">
                                        <span class="badge-modern {{ $badgeClass }}"
                                            style="font-size: 11px; padding: 4px 10px;">{{ $label }}</span>
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
                    <span class="material-symbols-outlined">local_offer</span>
                    <h4>{{ __('messages.owner.products.promotions.no_promotions') ?? 'No promotions found' }}</h4>
                    <p>{{ __('messages.owner.products.promotions.add_first_promotion') ?? 'Add your first promotion to get started' }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- =======================
    PAGINATION
  ======================= --}}
    @if ($promotions->hasPages())
        <div class="table-pagination">
            {{ $promotions->links() }}
        </div>
    @endif

</div>

{{-- Floating Add Button (Mobile Only) --}}
<a href="{{ route('owner.user-owner.promotions.create') }}" class="btn-add-employee-mobile">
    <span class="material-symbols-outlined">add</span>
</a>

{{-- Mobile Filter Modal --}}
<div id="mobileFilterModal" class="mobile-filter-modal">
    <div class="filter-modal-backdrop" onclick="closeMobileFilter()"></div>
    <div class="filter-modal-content">
        <div class="filter-modal-header">
            <div class="filter-header-left">
                <span class="material-symbols-outlined filter-header-icon">tune</span>
                <h3>Filter Promotions</h3>
            </div>
            <button type="button" class="filter-close-btn" onclick="closeMobileFilter()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="filter-modal-body">
            <div class="modal-filter-pills">
                <a href="{{ route('owner.user-owner.promotions.index', array_filter(['q' => request('q')])) }}"
                    class="modal-pill {{ !request('type') ? 'active' : '' }}" onclick="closeMobileFilter()">
                    <div class="pill-left">
                        <div class="pill-icon-wrapper {{ !request('type') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">local_offer</span>
                        </div>
                        <div class="pill-info">
                            <span class="pill-text">{{ __('messages.owner.products.promotions.all') }}</span>
                            <span class="pill-subtext">View all promotions</span>
                        </div>
                    </div>
                    <div class="pill-right">
                        @if (!request('type'))
                            <span class="material-symbols-outlined pill-check">check_circle</span>
                        @endif
                    </div>
                </a>

                <div class="filter-divider">
                    <span>Promotion Types</span>
                </div>

                <a href="{{ route('owner.user-owner.promotions.index', array_filter(['q' => request('q'), 'type' => 'percentage'])) }}"
                    class="modal-pill {{ request('type') === 'percentage' ? 'active' : '' }}"
                    onclick="closeMobileFilter()">
                    <div class="pill-left">
                        <div class="pill-icon-wrapper {{ request('type') === 'percentage' ? 'active' : '' }}">
                            <span class="material-symbols-outlined">percent</span>
                        </div>
                        <div class="pill-info">
                            <span class="pill-text">{{ __('messages.owner.products.promotions.percentage') }}</span>
                            <span class="pill-subtext">Filter by percentage discount</span>
                        </div>
                    </div>
                    <div class="pill-right">
                        @if (request('type') === 'percentage')
                            <span class="material-symbols-outlined pill-check">check_circle</span>
                        @endif
                    </div>
                </a>

                <a href="{{ route('owner.user-owner.promotions.index', array_filter(['q' => request('q'), 'type' => 'amount'])) }}"
                    class="modal-pill {{ request('type') === 'amount' ? 'active' : '' }}"
                    onclick="closeMobileFilter()">
                    <div class="pill-left">
                        <div class="pill-icon-wrapper {{ request('type') === 'amount' ? 'active' : '' }}">
                            <span class="material-symbols-outlined">payments</span>
                        </div>
                        <div class="pill-info">
                            <span class="pill-text">{{ __('messages.owner.products.promotions.reduced_fare') }}</span>
                            <span class="pill-subtext">Filter by amount discount</span>
                        </div>
                    </div>
                    <div class="pill-right">
                        @if (request('type') === 'amount')
                            <span class="material-symbols-outlined pill-check">check_circle</span>
                        @endif
                    </div>
                </a>
            </div>
        </div>

        <div class="filter-modal-footer">
            <button type="button" class="btn-clear-filter"
                onclick="window.location.href='{{ route('owner.user-owner.promotions.index', array_filter(['q' => request('q')])) }}'">
                <span class="material-symbols-outlined">restart_alt</span>
                Clear Filter
            </button>
        </div>
    </div>
</div>

<script>
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

    document.addEventListener('DOMContentLoaded', function() {
        const backdrop = document.querySelector('.filter-modal-backdrop');
        if (backdrop) {
            backdrop.addEventListener('click', closeMobileFilter);
        }
    });

    let searchTimeout;

    function searchFilter(input, delay) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            input.form.submit();
        }, delay);
    }

    function deletePromotion(promotionId, promotionName) {
        Swal.fire({
            title: '{{ __('messages.owner.products.promotions.delete_confirmation_1') }}',
            text: `{{ __('messages.owner.products.promotions.delete_confirmation_2') }}: "${promotionName}"`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#b3311d',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '{{ __('messages.owner.products.promotions.delete_confirmation_3') }}',
            cancelButtonText: '{{ __('messages.owner.products.promotions.cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/owner/user-owner/promotions/${promotionId}`;
                form.style.display = 'none';
                form.innerHTML = `
          @csrf
          <input type="hidden" name="_method" value="DELETE">
        `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

<style>
    .mono {
        font-family: 'Courier New', monospace;
    }
</style>
