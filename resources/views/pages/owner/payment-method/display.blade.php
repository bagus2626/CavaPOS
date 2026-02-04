<div class="modern-card payment-responsive">

  {{-- DESKTOP TABLE --}}
  <div class="data-table-wrapper only-desktop">
    <table class="data-table">
      <thead>
        <tr>
          <th class="text-center" style="width: 60px;">#</th>
          <th>{{ __('messages.owner.payment_methods.payment_type') }}</th>
          <th>{{ __('messages.owner.payment_methods.provider') }}</th>
          <th class="text-center" style="width: 150px;">{{ __('messages.owner.payment_methods.picture') }}</th>
          <th>{{ __('messages.owner.payment_methods.additional_info') }}</th>
          <th>{{ __('messages.owner.payment_methods.status') }}</th>
          <th class="text-center" style="width: 180px;">{{ __('messages.owner.payment_methods.actions') }}</th>
        </tr>
      </thead>

      <tbody id="paymentMethodTableBody">
        @forelse($paymentMethods as $index => $paymentMethod)
          <tr class="table-row">
            <td class="text-center text-muted">
              {{ $paymentMethods->firstItem() + $index }}
            </td>

            <td>
              @if ($paymentMethod->payment_type === 'manual_tf')
                <span class="badge bg-primary text-white">{{ __('messages.owner.payment_methods.type_transfer') }}</span>
              @elseif ($paymentMethod->payment_type === 'manual_ewallet')
                <span class="badge bg-success text-white">{{ __('messages.owner.payment_methods.type_ewallet') }}</span>
              @elseif ($paymentMethod->payment_type === 'manual_qris')
                <span class="badge bg-info text-white">{{ __('messages.owner.payment_methods.type_qris') }}</span>
              @endif
            </td>

            <td>
              <span class="text-secondary text-ellipsis-1" title="{{ $paymentMethod->provider_name }}">
                {{ $paymentMethod->provider_name }}
                @if ($paymentMethod->provider_account_name)
                  <br><small class="text-muted">{{ $paymentMethod->provider_account_name }}</small>
                @endif
                @if ($paymentMethod->provider_account_no)
                  <br><small class="text-muted">{{ $paymentMethod->provider_account_no }}</small>
                @endif
              </span>
            </td>

            <td class="text-center">
              @if($paymentMethod->qris_image_url)
                <a href="javascript:void(0)"
                   class="js-qris-open"
                   data-modal="#imageModal{{ $paymentMethod->id }}">
                  <img src="{{ asset('storage/' . $paymentMethod->qris_image_url) }}"
                       alt="{{ $paymentMethod->provider_account_name }}"
                       class="table-image"
                       loading="lazy">
                </a>
              @else
                <span class="text-muted" style="font-size: 0.875rem;">-</span>
              @endif
            </td>

            <td>
              <span class="text-secondary text-ellipsis-1" title="{{ $paymentMethod->additional_info }}">
                {{ $paymentMethod->additional_info ?? '-' }}
              </span>
            </td>

            <td>
              <span class="status-badge-table {{ $paymentMethod->is_active ? 'status-active-soft' : 'status-inactive-soft' }}">
                {{ $paymentMethod->is_active ? __('messages.owner.payment_methods.enabled') : __('messages.owner.payment_methods.disabled') }}
              </span>
            </td>

            <td class="text-center">
              <div class="table-actions">
                <a href="{{ route('owner.user-owner.payment-methods.edit', $paymentMethod) }}"
                   class="btn-table-action edit"
                   title="{{ __('messages.owner.payment_methods.edit') }}">
                  <span class="material-symbols-outlined">edit</span>
                </a>

                <form action="{{ route('owner.user-owner.payment-methods.destroy', $paymentMethod) }}"
                      method="POST"
                      class="d-inline js-delete-form"
                      data-name="{{ $paymentMethod->provider_name }}"
                      style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-table-action delete"
                          title="{{ __('messages.owner.payment_methods.delete') }}">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center">
              <div class="table-empty-state">
                <span class="material-symbols-outlined">payment</span>
                <h4>{{ __('messages.owner.payment_methods.no_results_found') ?? 'No payment methods found' }}</h4>
                <p>{{ __('messages.owner.payment_methods.add_first_payment_method') ?? 'Add your first payment method to get started' }}</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- MOBILE CARDS --}}
  <div class="only-mobile mobile-payment-list">
    @forelse($paymentMethods as $index => $paymentMethod)
      <div class="payment-card">
        <div class="payment-card__top">
          <div class="payment-card__title">
            {{ $paymentMethod->provider_name }}
          </div>

          <div class="payment-card__badge">
            @if ($paymentMethod->payment_type === 'manual_tf')
              <span class="badge bg-primary text-white">{{ __('messages.owner.payment_methods.type_transfer') }}</span>
            @elseif ($paymentMethod->payment_type === 'manual_ewallet')
              <span class="badge bg-success text-white">{{ __('messages.owner.payment_methods.type_ewallet') }}</span>
            @elseif ($paymentMethod->payment_type === 'manual_qris')
              <span class="badge bg-info text-white">{{ __('messages.owner.payment_methods.type_qris') }}</span>
            @endif
          </div>
        </div>

        <div class="payment-card__meta">
            <div class="payment-card__row">
                <span>{{ __('messages.owner.payment_methods.provider') }}</span>
                <strong>{{ $paymentMethod->provider_account_name ?? '-' }}</strong>
            </div>

            @if($paymentMethod->provider_account_no)
                <div class="payment-card__row">
                <span>Account No</span>
                <strong class="mono">{{ $paymentMethod->provider_account_no }}</strong>
                </div>
            @endif

            <div class="payment-card__row">
                <span>{{ __('messages.owner.payment_methods.status') }}</span>
                <span class="status-badge-table {{ $paymentMethod->is_active ? 'status-active-soft' : 'status-inactive-soft' }}">
                {{ $paymentMethod->is_active ? __('messages.owner.payment_methods.enabled') : __('messages.owner.payment_methods.disabled') }}
                </span>
            </div>

            <div class="payment-card__row">
                <span>{{ __('messages.owner.payment_methods.additional_info') }}</span>
                <span class="ellipsis-mobile"
                        title="{{ $paymentMethod->additional_info ?? '' }}">
                    {{ $paymentMethod->additional_info ?? '-' }}
                </span>

            </div>
        </div>

        <div class="payment-card__actions">
          <a href="{{ route('owner.user-owner.payment-methods.edit', $paymentMethod) }}" class="btn-card-action">
            <span class="material-symbols-outlined">edit</span>
            {{ __('messages.owner.payment_methods.edit') }}
          </a>

          <form action="{{ route('owner.user-owner.payment-methods.destroy', $paymentMethod) }}"
                method="POST"
                class="js-delete-form"
                data-name="{{ $paymentMethod->provider_name }}"
                style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-card-action danger">
              <span class="material-symbols-outlined">delete</span>
              {{ __('messages.owner.payment_methods.delete') }}
            </button>
          </form>
        </div>
      </div>
    @empty
      <div class="table-empty-state" style="padding:16px;">
        <span class="material-symbols-outlined">payment</span>
        <h4>{{ __('messages.owner.payment_methods.no_results_found') ?? 'No payment methods found' }}</h4>
      </div>
    @endforelse
  </div>

  {{-- MODALS --}}
  @foreach($paymentMethods as $paymentMethod)
    @if($paymentMethod->qris_image_url)
      @include('pages.owner.payment-method.modal', ['paymentMethod' => $paymentMethod])
    @endif
  @endforeach

  {{-- PAGINATION --}}
  @if($paymentMethods->hasPages())
    <div class="table-pagination">
      {{ $paymentMethods->links() }}
    </div>
  @endif
</div>

<style>
    .payment-responsive .only-desktop{ display:block; }
.payment-responsive .only-mobile{ display:none; }

@media (max-width: 768px){
  .payment-responsive .only-desktop{ display:none; }
  .payment-responsive .only-mobile{ display:block; }
}

.mobile-payment-list{
  padding: 14px;
  display: grid;
  gap: 12px;
}

.payment-card{
  border: 1px solid rgba(0,0,0,.08);
  border-radius: 16px;
  padding: 14px;
  box-shadow: 0 10px 24px rgba(0,0,0,.06);
  background: #fff;
  margin-bottom: 5px;
}

.payment-card__top{
  display:flex;
  justify-content:space-between;
  gap: 10px;
  align-items:flex-start;
}

.payment-card__title{
  font-weight: 900;
  font-size: 14px;
  line-height: 1.3;
}

.payment-card__meta{
  margin-top: 10px;
  display:grid;
  gap: 8px;
}

.payment-card__row{
  display:flex;
  justify-content:space-between;
  gap: 10px;
  font-size: 12px;
  color:#555;
  align-items: center;
}

/* label kiri jangan makan space berlebihan */
.payment-card__row > span:first-child{
  flex: 0 0 auto;
  max-width: 45%;
  white-space: nowrap;
}

/* value kanan harus boleh mengecil (min-width:0 wajib) */
.payment-card__row > strong,
.payment-card__row > .ellipsis-mobile{
  flex: 1 1 auto;
  min-width: 0;
}

/* khusus additional info */

.payment-card__qris img{
  margin-top: 8px;
  width: 100%;
  max-height: 220px;
  object-fit: contain;
  border: 1px solid rgba(0,0,0,.08);
  border-radius: 12px;
  background: rgba(0,0,0,.02);
}

.payment-card__actions{
  margin-top: 12px;
  display:flex;
  gap: 8px;
  flex-wrap:wrap;
}

.btn-card-action{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap: 6px;
  padding: 10px 12px;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,.10);
  background:#fff;
  font-size: 12px;
  font-weight: 800;
  white-space: nowrap;
}

.btn-card-action.danger{
  border-color: rgba(174,21,4,.25);
  color: #ae1504;
}

.text-ellipsis-1 {
  max-width: 260px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: block;
}

.ellipsis-mobile{
  display: block;
  max-width: 140px;        /* ← KUNCI LEBAR (sesuaikan 120–160px) */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-align: right;
  color: #111;
}



.payment-card__badge .badge{
  white-space: nowrap;
  font-size: 11px;
  padding: 4px 8px;
}


</style>