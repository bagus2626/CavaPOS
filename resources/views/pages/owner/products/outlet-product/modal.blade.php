{{-- ====== MODAL ADD PRODUCT (satu modal dipakai semua outlet) ====== --}}
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="outletProductQuickAddForm"
          method="POST"
          action="{{ route('owner.user-owner.outlet-products.store') }}"
          class="modal-content">
      @csrf

      <input type="hidden" name="outlet_id" id="qp_outlet_id" value="">

      <div class="modal-header">
        <h5 class="modal-title" id="addProductModalLabel">{{ __('messages.owner.products.outlet_products.add_outlet_product') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        {{-- Category --}}
        <div class="mb-3">
          <label for="qp_category_id" class="form-label">{{ __('messages.owner.products.outlet_products.category') }} <span class="text-danger">*</span></label>
          <select id="qp_category_id" name="category_id" class="form-control" required>
            <option value="all">{{ __('messages.owner.products.outlet_products.all_category_dropdown') }}</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
            @endforeach
          </select>
          <div class="invalid-feedback">{{ __('messages.owner.products.outlet_products.select_category_first') }}</div>
        </div>

        {{-- Master Product (multi select via checkbox) --}}
        <div class="mb-3">
          <label class="form-label">{{ __('messages.owner.products.outlet_products.master_products') }} <span class="text-danger">*</span></label>

          {{-- select all --}}
          <div class="form-check mt-2 ml-2">
            <input class="form-check-input" type="checkbox" id="qp_check_all" disabled>
            <label class="form-check-label" for="qp_check_all">{{ __('messages.owner.products.outlet_products.select_all') }}</label>
          </div>

          <div id="qp_master_product_box" class="border rounded p-2" style="max-height: 280px; overflow:auto;">
            <div class="text-muted small">{{ __('messages.owner.products.outlet_products.select_category_first') }}</div>
          </div>

          <div class="invalid-feedback d-block" id="qp_mp_error" style="display:none;">
            {{ __('messages.owner.products.outlet_products.at_least_one_master') }}
          </div>
        </div>

        {{-- ==== Always available toggle (baru) ==== --}}
        <div class="mb-2 form-check">
          <input type="checkbox" class="form-check-input" id="qp_always_available" name="always_available" value="1">
          <label class="form-check-label" for="qp_always_available">{{ __('messages.owner.products.outlet_products.always_available_product') }}</label>
        </div>

        {{-- Quantity --}}
        <div class="mb-3" id="qp_quantity_group">
          <label for="qp_quantity" class="form-label">{{ __('messages.owner.products.outlet_products.stock') }}</label>
          <input type="number" min="0" step="1" id="qp_quantity" name="quantity" class="form-control" value="0">
        </div>

        {{-- Status --}}
        <div class="mb-3">
          <label for="qp_is_active" class="form-label">{{ __('messages.owner.products.outlet_products.status') }}</label>
          <select id="qp_is_active" name="is_active" class="form-control">
            <option value="1">{{ __('messages.owner.products.outlet_products.active') }}</option>
            <option value="0">{{ __('messages.owner.products.outlet_products.inactive') }}</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light border" data-dismiss="modal">{{ __('messages.owner.products.outlet_products.cancel') }}</button>
        <button type="submit" class="btn btn-primary">
          <span class="label">{{ __('messages.owner.products.outlet_products.save') }}</span>
          <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Toggle logic --}}
<script>
(function () {
  const cb   = document.getElementById('qp_always_available');
  const wrap = document.getElementById('qp_quantity_group');
  const qty  = document.getElementById('qp_quantity');

  function syncQtyVisibility() {
    if (!cb || !wrap || !qty) return;
    if (cb.checked) {
      // simpan nilai lama (opsional)
      if (!qty.dataset.prev) qty.dataset.prev = qty.value || '0';
      wrap.classList.add('d-none');
      qty.disabled = true;     // agar tidak ikut terkirim saat submit
    } else {
      wrap.classList.remove('d-none');
      qty.disabled = false;
      // pulihkan nilai lama (opsional)
      if (qty.dataset.prev) qty.value = qty.dataset.prev;
    }
  }

  cb?.addEventListener('change', syncQtyVisibility);

  // sinkron awal saat modal pertama kali dirender
  syncQtyVisibility();

  // opsional: reset saat modal dibuka ulang (butuh Bootstrap JS)
  $('#addProductModal').on('shown.bs.modal', function () {
    // cb.checked = false; // jika ingin default selalu unchecked setiap buka modal
    syncQtyVisibility();
  });
})();
</script>
