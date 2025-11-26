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
                  <div class="form-group mb-2">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="use_product_name_switch" {{ old('product_id') ? 'checked' : '' }}>
                      <label class="custom-control-label" for="use_product_name_switch">
                        Use existing product name (from Product Master)
                      </label>
                    </div>
                    <small class="text-muted d-block">
                      If enabled, choose a product below. The stock name will follow the product name.
                    </small>
                  </div>

                  <div id="productPickerWrap" class="row {{ old('product_id') ? '' : 'd-none' }}">
                    <div class="col-md-8">
                      <div class="form-group">
                        <label for="product_picker" class="required">Choose Product</label>
                        <select id="product_picker" class="form-control">
                          <option value="">-- Select Product --</option>
                          @foreach($master_products as $p)
                            <option value="{{ $p->id }}" data-name="{{ $p->name }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                              {{ $p->name }}
                            </option>
                          @endforeach
                        </select>
                        <small class="text-muted">Changing the product will update the Stock Name below.</small>
                      </div>
                    </div>
                  </div>

                  <input type="hidden" id="product_id" name="product_id" value="{{ old('product_id') }}" {{ old('product_id') ? '' : 'disabled' }}>
                  {{-- ====== /NEW ====== --}}

                  {{-- Stock Name --}}
                  <div class="form-group">
                    <label for="stock_name" class="required">Stock Name</label>
                    <input type="text" class="form-control @error('stock_name') is-invalid @enderror" id="stock_name" name="stock_name"
                      value="{{ old('stock_name') }}" placeholder="e.g., Sugar / Flour / Paper Cup" required maxlength="150" {{ old('product_id') ? 'readonly' : '' }}>
                    @error('stock_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>

                  <div class="form-group">
                    <label for="unit_id" class="required">Display Unit</label>
                    <select id="unit_id" name="unit_id" class="form-control @error('unit_id') is-invalid @enderror" required>
                      <option value="">-- Select Unit --</option>
                      @foreach($master_units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                          {{ $unit->unit_name }} ({{ $unit->group_label }})
                        </option>
                      @endforeach
                    </select>
                    <small class="text-muted">Unit used to display stock (e.g., Kg, Pcs). Quantity will be 0.</small>
                    @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                <hr>

                {{-- Description --}}
                <div class="form-group">
                  <label for="description">Description (optional)</label>
                  <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
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
    (function () {
      // ===== Variabel untuk Product Picker =====
      const useSwitch = document.getElementById('use_product_name_switch');
      const pickerWrap = document.getElementById('productPickerWrap');
      const picker = document.getElementById('product_picker');
      const productId = document.getElementById('product_id');
      const stockName = document.getElementById('stock_name');

      // Fungsi untuk mengatur UI product picker
      function setUseProductUI(on) {
        pickerWrap.classList.toggle('d-none', !on);
        if (on) {
          productId.removeAttribute('disabled');
          stockName.setAttribute('readonly', 'readonly');

          // Sinkronkan nilai dari picker
          if (picker && picker.value) {
            productId.value = picker.value;
            const opt = picker.selectedOptions[0];
            if (opt && opt.dataset.name) {
              stockName.value = opt.dataset.name;
            }
          }
        } else {
          productId.setAttribute('disabled', 'disabled');
          productId.value = '';
          stockName.removeAttribute('readonly');
        }
      }

      // Listener untuk switch product
      if (useSwitch) {
        useSwitch.addEventListener('change', () => setUseProductUI(useSwitch.checked));

        setUseProductUI(useSwitch.checked);
      }

      // Listener untuk picker product
      if (picker) {
        picker.addEventListener('change', () => {
          const id = picker.value;
          const opt = picker.selectedOptions[0];

          productId.value = id;

          if (id && useSwitch.checked) { 
            if (opt && opt.dataset.name) {
              stockName.value = opt.dataset.name;
            }
          }
        });
      }
    })();
  </script>
@endsection

