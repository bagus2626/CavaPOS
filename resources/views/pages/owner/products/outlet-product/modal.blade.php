{{-- ====== MODAL ADD PRODUCT (satu modal dipakai semua outlet) ====== --}}
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="outletProductQuickAddForm"
          method="POST"
          action="{{ route('owner.user-owner.outlet-products.store') }}"
          class="modal-content">
      @csrf

      <input type="hidden" name="outlet_id" id="qp_outlet_id" value="">

      <div class="modal-header">
        <h5 class="modal-title" id="addProductModalLabel">Add Outlet Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        {{-- Category --}}
        <div class="mb-3">
          <label for="qp_category_id" class="form-label">Category <span class="text-danger">*</span></label>
          <select id="qp_category_id" name="category_id" class="form-control" required>
            <option value="all">— All Category —</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
            @endforeach
          </select>
          <div class="invalid-feedback">Please choose a category.</div>
        </div>

        {{-- Master Product (multi select via checkbox) --}}
        <div class="mb-3">
          <label class="form-label">Master Products <span class="text-danger">*</span></label>

          {{-- select all --}}
          <div class="form-check mt-2 ml-2">
            <input class="form-check-input" type="checkbox" id="qp_check_all" disabled>
            <label class="form-check-label" for="qp_check_all">Select all</label>
          </div>
          {{-- kotak daftar checkbox --}}
          <div id="qp_master_product_box" class="border rounded p-2" style="max-height: 280px; overflow:auto;">
            <div class="text-muted small">Select a category first.</div>
          </div>

          {{-- error helper (frontend) --}}
          <div class="invalid-feedback d-block" id="qp_mp_error" style="display:none;">
            Please pick at least one master product.
          </div>
        </div>


        {{-- Quantity --}}
        <div class="mb-3">
          <label for="qp_quantity" class="form-label">Quantity</label>
          <input type="number" min="0" step="1" id="qp_quantity" name="quantity" class="form-control" value="0">
        </div>

        {{-- Status --}}
        <div class="mb-3">
          <label for="qp_is_active" class="form-label">Status</label>
          <select id="qp_is_active" name="is_active" class="form-control">
            <option value="1">Active</option>
            <option value="0">Not Active</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">
          <span class="label">Save</span>
          <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
      </div>
    </form>
  </div>
</div>
