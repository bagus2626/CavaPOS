<div class="modern-card promo-responsive">

  {{-- DESKTOP TABLE --}}
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
        <th class="text-center" style="width:220px;">{{ __('messages.owner.products.promotions.actions') }}</th>
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

          // status badge
          if (!$promotion->is_active) { $label = __('messages.owner.products.promotions.inactive'); $badgeClass='badge-danger'; }
          else {
            $now = now();
            $start = $promotion->start_date;
            $end   = $promotion->end_date;
            if ($start && $now->lt($start)) { $label = __('messages.owner.products.promotions.will_be_active'); $badgeClass='badge-warning'; }
            elseif ($end && $now->gt($end)) { $label = __('messages.owner.products.promotions.expired'); $badgeClass='badge-danger'; }
            else { $label = __('messages.owner.products.promotions.active'); $badgeClass='badge-success'; }
          }
        @endphp

        <tr class="table-row">
          <td class="text-center text-muted">{{ $promotions->firstItem() + $index }}</td>
          <td><span class="mono fw-600">{{ $promotion->promotion_code }}</span></td>
          <td><span class="fw-600">{{ $promotion->promotion_name }}</span></td>
          <td class="text-center">
            {{ $promotion->promotion_type === 'percentage'
                ? __('messages.owner.products.promotions.percentage')
                : __('messages.owner.products.promotions.reduced_fare') }}
          </td>
          <td>
            <span class="fw-600">
              @if($promotion->promotion_type === 'percentage')
                {{ number_format($promotion->promotion_value, 0, ',', '.') }}%
              @else
                Rp {{ number_format($promotion->promotion_value, 0, ',', '.') }}
              @endif
            </span>
          </td>
          <td>
            <div class="text-secondary" style="font-size:0.875rem;">
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
          <td><span class="text-secondary" style="font-size:0.875rem;">{{ $activeDays }}</span></td>
          <td class="text-center"><span class="badge-modern {{ $badgeClass }}">{{ $label }}</span></td>
          <td class="text-center">
            <div class="table-actions">
              <a href="{{ route('owner.user-owner.promotions.show', $promotion->id) }}" class="btn-table-action view">
                <span class="material-symbols-outlined">visibility</span>
              </a>
              <a href="{{ route('owner.user-owner.promotions.edit', $promotion->id) }}" class="btn-table-action edit">
                <span class="material-symbols-outlined">edit</span>
              </a>

              <form action="{{ route('owner.user-owner.promotions.destroy', $promotion->id) }}"
                    method="POST"
                    class="d-inline js-delete-promo-form"
                    data-name="{{ $promotion->promotion_name }}"
                    style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-table-action delete">
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
              <span class="material-symbols-outlined">local_offer_off</span>
              <h4>{{ __('messages.owner.products.promotions.no_promotions') ?? 'No promotions found' }}</h4>
            </div>
          </td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{-- MOBILE CARDS --}}
  <div class="only-mobile mobile-promo-list">
    @forelse($promotions as $promotion)
      @php
        // pakai hitungan yang sama (boleh extract partial biar rapi)
      @endphp

      <div class="promo-card">
        <div class="promo-card__top">
          <div class="promo-card__title">{{ $promotion->promotion_name }}</div>
          <div class="promo-card__code mono">{{ $promotion->promotion_code }}</div>
        </div>

        <div class="promo-card__meta">
          <div class="promo-card__row">
            <span>{{ __('messages.owner.products.promotions.promo_type') }}</span>
            <strong>
              {{ $promotion->promotion_type === 'percentage'
                  ? __('messages.owner.products.promotions.percentage')
                  : __('messages.owner.products.promotions.reduced_fare') }}
            </strong>
          </div>

          <div class="promo-card__row">
            <span>{{ __('messages.owner.products.promotions.value') }}</span>
            <strong>
              @if($promotion->promotion_type === 'percentage')
                {{ number_format($promotion->promotion_value,0,',','.') }}%
              @else
                Rp {{ number_format($promotion->promotion_value,0,',','.') }}
              @endif
            </strong>
          </div>

          <div class="promo-card__row">
            <span>{{ __('messages.owner.products.promotions.status') }}</span>
            <span class="badge-modern {{ $badgeClass }}">{{ $label }}</span>
          </div>
        </div>

        <div class="promo-card__actions">
          <a href="{{ route('owner.user-owner.promotions.show', $promotion->id) }}" class="btn-card-action">
            <span class="material-symbols-outlined">visibility</span>
            {{ __('messages.owner.products.promotions.detail') }}
          </a>
          <a href="{{ route('owner.user-owner.promotions.edit', $promotion->id) }}" class="btn-card-action">
            <span class="material-symbols-outlined">edit</span>
            {{ __('messages.owner.products.promotions.edit') }}
          </a>

          <form action="{{ route('owner.user-owner.promotions.destroy', $promotion->id) }}"
                method="POST"
                class="js-delete-promo-form"
                data-name="{{ $promotion->promotion_name }}"
                style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-card-action danger">
              <span class="material-symbols-outlined">delete</span>
              {{ __('messages.owner.products.promotions.delete') }}
            </button>
          </form>
        </div>
      </div>
    @empty
      <div class="table-empty-state" style="padding: 16px;">
        <span class="material-symbols-outlined">local_offer_off</span>
        <h4>{{ __('messages.owner.products.promotions.no_promotions') ?? 'No promotions found' }}</h4>
      </div>
    @endforelse
  </div>

  {{-- PAGINATION (satu saja, berlaku utk desktop & mobile) --}}
  @if($promotions->hasPages())
    <div class="table-pagination">
      {{ $promotions->links() }}
    </div>
  @endif
</div>

<style>
  .promo-responsive .only-desktop{ display:block; }
  .promo-responsive .only-mobile{ display:none; }
  @media (max-width: 768px){
    .promo-responsive .only-desktop{ display:none; }
    .promo-responsive .only-mobile{ display:block; }
  }

  .mobile-promo-list{ padding:14px; display:grid; gap:12px; }
  .promo-card{
    border:1px solid rgba(0,0,0,.08);
    border-radius:16px;
    padding:14px;
    box-shadow:0 10px 24px rgba(0,0,0,.06);
    background:#fff;
    margin-bottom: 5px;
  }
  .promo-card__top{ display:flex; justify-content:space-between; gap:10px; }
  .promo-card__title{ font-weight:900; font-size:14px; }
  .promo-card__code{ opacity:.7; font-size:12px; }
  .promo-card__meta{ margin-top:10px; display:grid; gap:6px; }
  .promo-card__row{ display:flex; justify-content:space-between; gap:10px; font-size:12px; }
  .promo-card__actions{ margin-top:12px; display:flex; gap:8px; flex-wrap:wrap; }
  .btn-card-action{
    display:inline-flex; align-items:center; gap:6px;
    padding:10px 12px; border-radius:12px;
    border:1px solid rgba(0,0,0,.10); background:#fff;
    font-size:12px; font-weight:800; white-space:nowrap;
  }
  .btn-card-action.danger{ border-color: rgba(174,21,4,.25); color:#ae1504; }
</style>
