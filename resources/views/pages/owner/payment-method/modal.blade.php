<div class="qris-modal" id="imageModal{{ $paymentMethod->id }}" aria-hidden="true">
  <div class="qris-modal__backdrop" data-close></div>

  <div class="qris-modal__dialog" role="dialog" aria-modal="true" aria-label="QRIS Preview">
    <div class="qris-modal__header">
      <h5 class="qris-modal__title">
        {{ $paymentMethod->provider_name }}
      </h5>

      <div class="qris-modal__actions">
        {{-- Download button --}}
        <a
          href="{{ asset('storage/' . $paymentMethod->qris_image_url) }}"
          download
          class="qris-modal__download"
          title="Download QRIS">
          <span class="material-symbols-outlined">download</span>
        </a>

        {{-- Close button --}}
        <button type="button"
          class="qris-modal__close"
          data-close
          aria-label="Close">
          ×
        </button>
      </div>
    </div>

    <div class="qris-modal__body">
      <img
        src="{{ asset('storage/' . $paymentMethod->qris_image_url) }}"
        alt="{{ $paymentMethod->provider_account_name }}"
        class="qris-modal__img">
    </div>
  </div>
</div>
