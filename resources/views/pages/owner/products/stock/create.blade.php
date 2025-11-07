@extends('layouts.owner')

@section('title', 'Create Stock Item')
@section('page_title', 'Create New Stock')

@section('content')
<section class="content">
  <div class="container-fluid owner-stock-create">
    <div class="row">
      <div class="col-12">

        <a href="{{ route('owner.user-owner.stocks.index') }}" class="btn btn-secondary mb-3">
          <i class="fas fa-arrow-left mr-2"></i>Back to Stock List
        </a>

        @if ($errors->any())
          <div class="alert alert-danger">
            <strong>Please re-check your input</strong>
            <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Stock Information</h3>
          </div>

          <form action="{{ route('owner.user-owner.stocks.store') }}" method="POST" id="stockForm" novalidate>
            @csrf

            <div class="card-body">

              {{-- ====== NEW: Link to existing product from master_products ====== --}}
              <div class="form-group mb-2">
                <div class="custom-control custom-switch">
                  <input type="checkbox"
                         class="custom-control-input"
                         id="use_product_name_switch">
                  <label class="custom-control-label" for="use_product_name_switch">
                    Use existing product name (from Product Master)
                  </label>
                </div>
                <small class="text-muted d-block">
                  If enabled, choose a product below. The stock name will follow the product name and the product ID will be submitted.
                </small>
              </div>

              <div id="productPickerWrap" class="row d-none">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="product_picker" class="required">Choose Product</label>
                    <select id="product_picker" class="form-control">
                      <option value="">-- Select Product --</option>
                      @foreach(($master_products ?? []) as $p)
                        {{-- asumsi: $p->id dan $p->name tersedia --}}
                        <option value="{{ $p->id }}"
                                data-name="{{ $p->name }}"
                                {{ old('product_id') == $p->id ? 'selected' : '' }}>
                          {{ $p->name }}
                        </option>
                      @endforeach
                    </select>
                    <small class="text-muted">Changing the product will update the Stock Name below (you can still edit it).</small>
                  </div>
                </div>
              </div>

              {{-- hidden product_id; disabled by default supaya tidak terkirim saat tidak dipakai --}}
              <input type="hidden" id="product_id" name="product_id" value="{{ old('product_id') }}" disabled>

              {{-- ====== /NEW ====== --}}

              {{-- Stock Name --}}
              <div class="form-group">
                <label for="stock_name" class="required">Stock Name</label>
                <input type="text"
                       class="form-control @error('stock_name') is-invalid @enderror"
                       id="stock_name"
                       name="stock_name"
                       value="{{ old('stock_name') }}"
                       placeholder="e.g., Sugar / Flour / Paper Cup"
                       required
                       maxlength="150">
                @error('stock_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="row">
                {{-- Unit --}}
                <div class="col-md-6">
                  <div class="form-group mb-0">
                    <label for="unit" class="required">Unit</label>
                    <select id="unit"
                            name="unit"
                            class="form-control @error('unit') is-invalid @enderror"
                            required>
                      <option value="">-- Select Unit --</option>
                      @php
                        $units = ['pcs','box','pack','kg','g','liter','ml','meter','cm','dozen','tray','sachet','other'];
                        $oldUnit = old('unit');
                      @endphp
                      @foreach($units as $u)
                        <option value="{{ $u }}" {{ $oldUnit === $u ? 'selected' : '' }}>{{ $u }}</option>
                      @endforeach
                    </select>
                    @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>

                {{-- Custom Unit (shown only if "other") --}}
                <div class="col-md-6" id="customUnitWrap" style="display:none;">
                  <div class="form-group mb-0">
                    <label for="custom_unit" class="required">Custom Unit</label>
                    <input type="text"
                           class="form-control @error('custom_unit') is-invalid @enderror"
                           id="custom_unit"
                           name="custom_unit"
                           value="{{ old('custom_unit') }}"
                           placeholder="e.g., roll, sheet, bundle"
                           maxlength="30">
                    @error('custom_unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>
              </div>

              <hr>

              {{-- Description --}}
              <div class="form-group">
                <label for="description">Description (optional)</label>
                <textarea id="description"
                          name="description"
                          class="form-control @error('description') is-invalid @enderror"
                          rows="3"
                          placeholder="Notes about supplier, quality, size, or other details">{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="card-footer text-right">
              <button type="reset" class="btn btn-outline-secondary mr-2">
                <i class="fas fa-undo mr-1"></i>Reset
              </button>
              <button type="submit" class="btn btn-success">
                <i class="fas fa-save mr-1"></i>Create Stock
              </button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</section>

<style>
/* ===== Owner › Stock Create (page scope) ===== */
.owner-stock-create{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#fff;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}
.owner-stock-create .card{ border:0; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; background:var(--paper); }
.owner-stock-create .card-header{ background:#fff; border-bottom:1px solid #eef1f4; }
.owner-stock-create .card-title{ color:var(--ink); font-weight:700; }
.owner-stock-create .alert{ border-left:4px solid var(--choco); border-radius:10px; }
.btn-secondary{ background:var(--choco); border-color:var(--choco); }
.btn-secondary:hover{ background:var(--soft-choco); border-color:var(--soft-choco); }
.owner-stock-create .btn-success{ background:var(--choco); border-color:var(--choco); }
.owner-stock-create .btn-success:hover{ background:var(--soft-choco); border-color:var(--soft-choco); }
.owner-stock-create .btn-outline-secondary{ border-color:#cbd5e1; color:#374151; background:#fff; }
.owner-stock-create .btn-outline-secondary:hover{ color:#fff; background:#6b7280; border-color:#6b7280; }
.owner-stock-create .form-group label{ font-weight:600; color:#374151; }
.owner-stock-create .required::after{ content:" *"; color:#dc3545; }
.owner-stock-create .form-control:focus{ border-color:var(--choco); box-shadow:0 0 0 .2rem rgba(140,16,0,.15); }
.owner-stock-create .input-group-text{ background:rgba(140,16,0,.08); color:var(--choco); border-color:rgba(140,16,0,.25); }
.owner-stock-create .card-footer .btn{ border-radius:10px; }
.owner-stock-create .text-muted{ color:#6b7280 !important; }
</style>
@endsection

@section('scripts')
<script>
(function(){
  // ===== use existing product toggle =====
  const useSwitch  = document.getElementById('use_product_name_switch');
  const pickerWrap = document.getElementById('productPickerWrap');
  const picker     = document.getElementById('product_picker');
  const productId  = document.getElementById('product_id');
  const stockName  = document.getElementById('stock_name');

  // ===== unit "other" toggle =====
  const unitSel    = document.getElementById('unit');
  const customWrap = document.getElementById('customUnitWrap');
  const customInput= document.getElementById('custom_unit');

  function syncCustomUnit(){
    const isOther = unitSel.value === 'other';
    customWrap.style.display = isOther ? '' : 'none';
    if(isOther){
      customInput.setAttribute('required', 'required');
    } else {
      customInput.removeAttribute('required');
      customInput.value = '';
    }
  }

  // === NEW helper: set unit to 'pcs' ===
  function forceUnitToPCS(){
    if(!unitSel) return;
    // Kalau opsi pcs ada, set ke pcs lalu sinkronkan tampilan custom unit
    const hasPCS = Array.from(unitSel.options).some(o => o.value === 'pcs');
    if(hasPCS){
      unitSel.value = 'pcs';
      syncCustomUnit();
    }
  }

  function setUseProductUI(on){
    pickerWrap.classList.toggle('d-none', !on);
    if(on){
      productId.removeAttribute('disabled');
      // jika ada pilihan aktif, sinkronkan
      if(picker && picker.value){
        productId.value = picker.value;
        const opt = picker.selectedOptions[0];
        if(opt && opt.dataset.name){
          stockName.value = opt.dataset.name;
        }
        // === NEW: tiap kali aktif + ada produk terpilih, paksa unit -> pcs
        forceUnitToPCS();
      }
    } else {
      productId.setAttribute('disabled', 'disabled');
      productId.value = '';
    }
  }

  if(useSwitch){
    useSwitch.addEventListener('change', () => setUseProductUI(useSwitch.checked));
    // initial state (restore dari old('product_id') jika ada)
    const hasOldProduct = !!productId.value;
    useSwitch.checked = hasOldProduct;
    setUseProductUI(useSwitch.checked);
    // === NEW: kalau saat load sudah ada product_id lama & picker berisi nilai, paksa unit pcs
    if(hasOldProduct && picker && picker.value){
      forceUnitToPCS();
    }
  }

  if(picker){
    picker.addEventListener('change', () => {
      const id  = picker.value;
      const opt = picker.selectedOptions[0];
      if(id){
        productId.value = id;
        if(opt && opt.dataset.name){
          // isi stock_name; masih bisa diedit manual
          stockName.value = opt.dataset.name;
        }
        // === NEW: setiap kali user memilih produk, paksa unit -> pcs
        forceUnitToPCS();
      } else {
        productId.value = '';
      }
    });
  }

  if(unitSel){
    unitSel.addEventListener('change', syncCustomUnit);
    syncCustomUnit();
  }

  // ===== safety guard =====
  const form = document.getElementById('stockForm');
  if(form){
    form.addEventListener('submit', function(e){
      // tidak ada tambahan khusus; server-side validation akan handle.
    });
  }
})();
</script>
@endsection

