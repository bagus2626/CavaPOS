{{-- ====== MODAL ADD PRODUCT (satu modal dipakai semua outlet) ====== --}}
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="outletProductQuickAddForm" method="POST" action="{{ route('owner.user-owner.outlet-products.store') }}"
      class="modal-content">
      @csrf

      <input type="hidden" name="outlet_id" id="qp_outlet_id" value="">

      <div class="modal-header">
        <h5 class="modal-title" id="addProductModalLabel">
          {{ __('messages.owner.products.outlet_products.add_outlet_product') }}
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        {{-- Category --}}
        <div class="mb-3">
          <label for="qp_category_id" class="form-label">{{ __('messages.owner.products.outlet_products.category') }}
            <span class="text-danger">*</span></label>
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
          <label class="form-label">{{ __('messages.owner.products.outlet_products.master_products') }} <span
              class="text-danger">*</span></label>

          {{-- select all --}}
          <div class="form-check mt-2 ml-2">
            <input class="form-check-input" type="checkbox" id="qp_check_all" disabled>
            <label class="form-check-label"
              for="qp_check_all">{{ __('messages.owner.products.outlet_products.select_all') }}</label>
          </div>

          <div id="qp_master_product_box" class="border rounded p-2" style="max-height: 280px; overflow:auto;">
            <div class="text-muted small">{{ __('messages.owner.products.outlet_products.select_category_first') }}
            </div>
          </div>

          <div class="invalid-feedback d-block" id="qp_mp_error" style="display:none;">
            {{ __('messages.owner.products.outlet_products.at_least_one_master') }}
          </div>
        </div>

        {{-- ========== STOCK TYPE SELECTION (Bootstrap Style) ========== --}}
        <div class="mb-3">
          <label class="form-label">Stock Management <span class="text-danger">*</span></label>

          <div class="form-check p-3 border rounded" style="transition: all 0.2s ease;">
            <input class="form-check-input" type="radio" name="stock_type" id="stock_type_direct" value="direct" checked
              required style="cursor: pointer; margin-top: 0.5rem;">
            <label class="form-check-label w-100" for="stock_type_direct" style="cursor: pointer; margin-left: 0.5rem;">
              <div class="d-flex align-items-center" style="gap: 8px; margin-bottom: 6px;">
                <i class="fas fa-box"></i>
                <strong>Direct Stock Input</strong>
              </div>
              <small class="text-muted d-block">Enter quantity directly. Stock decreases when product is sold.</small>
            </label>
          </div>

          <div class="form-check p-3 border rounded mt-2" style="transition: all 0.2s ease;">
            <input class="form-check-input" type="radio" name="stock_type" id="stock_type_linked" value="linked"
              required style="cursor: pointer; margin-top: 0.5rem;">
            <label class="form-check-label w-100" for="stock_type_linked" style="cursor: pointer; margin-left: 0.5rem;">
              <div class="d-flex align-items-center" style="gap: 8px; margin-bottom: 6px;">
                <i class="fas fa-link"></i>
                <strong>Link to Raw Materials</strong>
              </div>
              <small class="text-muted d-block">Connect product to raw material ingredients.</small>
            </label>
          </div>
        </div>

        {{-- Quantity (only shown for direct stock) --}}
        <div class="mb-3" id="qp_quantity_group">
          <label for="qp_quantity" class="form-label">{{ __('messages.owner.products.outlet_products.stock') }} <span
              class="text-danger">*</span></label>
          <input type="number" min="0" step="1" id="qp_quantity" name="quantity" class="form-control" value="0"
            required>
          <small class="form-text text-muted">Enter initial stock quantity for this product</small>
        </div>

        {{-- Info message for linked stock --}}
        <div class="alert alert-info d-none" id="linked_stock_info" style="font-size: 0.9rem;">
          <i class="fas fa-info-circle mr-1"></i>
          <strong>Note:</strong> Product will be created with 0 stock.
          You can configure raw material links in <strong>Stock Management</strong> page.
        </div>

        {{-- Status --}}
        <div class="mb-3">
          <label for="qp_is_active"
            class="form-label">{{ __('messages.owner.products.outlet_products.status') }}</label>
          <select id="qp_is_active" name="is_active" class="form-control">
            <option value="1">{{ __('messages.owner.products.outlet_products.active') }}</option>
            <option value="0">{{ __('messages.owner.products.outlet_products.inactive') }}</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light border"
          data-dismiss="modal">{{ __('messages.owner.products.outlet_products.cancel') }}</button>
        <button type="submit" class="btn btn-primary">
          <span class="label">{{ __('messages.owner.products.outlet_products.save') }}</span>
          <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Stock Type Styles (Sedikit CSS untuk highlight) --}}
<style>
  /* Sedikit CSS untuk highlight border saat di-check */
  .form-check:has(input[type="radio"]:checked) {
    border-color: #007bff !important;
    background-color: #f0f8ff;
  }

  .form-check:hover {
    border-color: #007bff;
  }
</style>

{{-- Stock Type Toggle Logic (Tidak perlu diubah) --}}
<script>
  (function () {
    const directRadio = document.getElementById('stock_type_direct');
    const linkedRadio = document.getElementById('stock_type_linked');
    const qtyGroup = document.getElementById('qp_quantity_group');
    const qtyInput = document.getElementById('qp_quantity');
    const linkedInfo = document.getElementById('linked_stock_info');

    function syncStockTypeUI() {
      if (!directRadio || !linkedRadio || !qtyGroup || !qtyInput || !linkedInfo) return;

      if (linkedRadio.checked) {
        // Pilihan 2: Linked to Raw Materials
        qtyGroup.classList.add('d-none');
        qtyInput.required = false;
        qtyInput.value = '0';
        linkedInfo.classList.remove('d-none');
      } else {
        // Pilihan 1: Direct Stock
        qtyGroup.classList.remove('d-none');
        qtyInput.required = true;
        linkedInfo.classList.add('d-none');
      }
    }

    // Event listeners
    directRadio?.addEventListener('change', syncStockTypeUI);
    linkedRadio?.addEventListener('change', syncStockTypeUI);

    // Reset ke default saat modal dibuka
    $('#addProductModal').on('shown.bs.modal', function () {
      if (directRadio) directRadio.checked = true;
      syncStockTypeUI();
    });

    // Sinkron awal
    syncStockTypeUI();
  })();
</script>