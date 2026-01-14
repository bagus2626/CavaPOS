<!-- Table Card -->
<div class="modern-card">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">#</th>
                    <th>{{ __('messages.owner.products.promotions.promo_code') }}</th>
                    <th>{{ __('messages.owner.products.promotions.promo_name') }}</th>
                    <th class="text-center">{{ __('messages.owner.products.promotions.promo_type') }}</th>
                    <th>{{ __('messages.owner.products.promotions.value') }}</th>
                    <th>{{ __('messages.owner.products.promotions.active_date') }}</th>
                    <th>{{ __('messages.owner.products.promotions.active_day') }}</th>
                    <th class="text-center">{{ __('messages.owner.products.promotions.status') }}</th>
                    <th class="text-center" style="width: 220px;">{{ __('messages.owner.products.promotions.actions') }}</th>
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
                        $activeDays = count($activeDaysArr) === 7
                            ? 'Setiap Hari'
                            : collect($activeDaysArr)->map(fn($d) => $daysMap[$d] ?? $d)->join(', ');

                        // Status badge class
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

                    <tr data-category="{{ $promotion->promotion_type }}" class="table-row">
                        <!-- Number -->
                        <td class="text-center text-muted">{{ $promotions->firstItem() + $index }}</td>

                        <!-- Promo Code -->
                        <td>
                            <span class="mono fw-600">{{ $promotion->promotion_code }}</span>
                        </td>

                        <!-- Promo Name -->
                        <td>
                            <span class="fw-600">{{ $promotion->promotion_name }}</span>
                        </td>

                        <!-- Promo Type -->
                        <td class="text-center">
                            @if($promotion->promotion_type === 'percentage')
                                <span>
                                    {{ __('messages.owner.products.promotions.percentage') }}
                                </span>
                            @else
                                <span>
                                    {{ __('messages.owner.products.promotions.reduced_fare') }}
                                </span>
                            @endif
                        </td>

                        <!-- Value -->
                        <td>
                            <span class="fw-600">
                                @if($promotion->promotion_type == 'percentage')
                                    {{ number_format($promotion->promotion_value, 0, ',', '.') }}%
                                @else
                                    Rp {{ number_format($promotion->promotion_value, 0, ',', '.') }}
                                @endif
                            </span>
                        </td>

                        <!-- Active Date -->
                        <td>
                            <div class="text-secondary" style="font-size: 0.875rem;">
                                @if($promotion->start_date && $promotion->end_date)
                                    {{ $promotion->start_date->translatedFormat('d F Y') }} ({{ $promotion->start_date->format('H:i') }}) – 
                                    {{ $promotion->end_date->translatedFormat('d F Y') }} ({{ $promotion->end_date->format('H:i') }})
                                @elseif($promotion->start_date)
                                    {{ __('messages.owner.products.promotions.start') }} {{ $promotion->start_date->translatedFormat('d F Y') }} ({{ $promotion->start_date->format('H:i') }})
                                @elseif($promotion->end_date)
                                    {{ __('messages.owner.products.promotions.until') }} {{ $promotion->end_date->translatedFormat('d F Y') }} ({{ $promotion->end_date->format('H:i') }})
                                @else
                                    <span class="text-muted">{{ __('messages.owner.products.promotions.unlimited') }}</span>
                                @endif
                            </div>
                        </td>

                        <!-- Active Days -->
                        <td>
                            <span class="text-secondary" style="font-size: 0.875rem;">{{ $activeDays }}</span>
                        </td>

                        <!-- Status -->
                        <td class="text-center">
                            <span class="badge-modern {{ $badgeClass }}">{{ $label }}</span>
                        </td>

                        <!-- Actions -->
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
                                <button onclick="deletePromo({{ $promotion->id }})"
                                    class="btn-table-action delete"
                                    title="{{ __('messages.owner.products.promotions.delete') }}">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">local_offer_off</span>
                                <h4>{{ __('messages.owner.products.promotions.no_promotions') ?? 'No promotions found' }}</h4>
                                <p>{{ __('messages.owner.products.promotions.add_first_promotion') ?? 'Create your first promotion to get started' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($promotions->hasPages())
        <div class="table-pagination">
            {{ $promotions->links() }}
        </div>
    @endif
</div>