@extends('layouts.owner')

@section('title', __('messages.owner.products.outlet_products.update_outlet_product'))
@section('page_title', __('messages.owner.products.outlet_products.update_outlet_product'))

@section('content')
  <section class="content">
    <div class="container-fluid owner-op-edit">
      <a href="{{ route('owner.user-owner.outlet-products.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('messages.owner.products.outlet_products.back_to_outlet_products') }}
      </a>

      {{-- Alerts (fallback; toastr sudah di layout) --}}
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
          <h3 class="card-title mb-0">{{ __('messages.owner.products.outlet_products.edit_outlet_product') }}</h3>
        </div>

        <form action="{{ route('owner.user-owner.outlet-products.update', $data->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="card-body">
            <div class="row">
              {{-- LEFT: Readonly Summary --}}
              <div class="col-lg-7">
                <div class="card border-0 mb-3">
                  <div class="card-body">
                    <div class="d-flex align-items-start">
                      {{-- Thumbnail utama --}}
                      <div class="mr-3">
                        @php
                          $firstPic = is_array($data->pictures ?? null) && count($data->pictures) ? $data->pictures[0]['path'] : null;
                        @endphp
                        <img
                          src="{{ $firstPic ? asset($firstPic) : 'https://via.placeholder.com/120x120?text=No+Image' }}"
                          alt="{{ $data->name }}" class="rounded" style="width:120px;height:120px;object-fit:cover;">
                      </div>
                      <div class="flex-fill">
                        <h4 class="mb-1">{{ $data->name }}</h4>
                        <div class="mb-2">
                          <span class="badge badge-info">
                            {{ optional($data->category)->category_name ?? 'Uncategorized' }}
                          </span>
                          <span
                            class="badge badge-light border ml-1">{{ __('messages.owner.products.outlet_products.code') }}:
                            {{ $data->product_code ?? '-' }}</span>
                          <span
                            class="badge badge-light border ml-1">{{ __('messages.owner.products.outlet_products.outlet_id') }}:
                            {{ $data->partner_id }}</span>
                        </div>

                        <div class="d-flex flex-wrap text-muted small">
                          <div class="mr-4">
                            <i class="fas fa-tags mr-1"></i>
                            {{ __('messages.owner.products.outlet_products.price') }}:
                            <strong>
                              Rp {{ number_format((float) ($data->price ?? 0), 0, ',', '.') }}
                            </strong>
                          </div>
                          <div class="mr-4">
                            <i class="fas fa-barcode mr-1"></i>
                            {{ __('messages.owner.products.outlet_products.master_product') }}:
                            <strong>{{ $data->master_product_id ?? '-' }}</strong>
                          </div>
                        </div>
                      </div>
                    </div>

                    {{-- Galeri kecil (readonly) --}}
                    @if(is_array($data->pictures ?? null) && count($data->pictures) > 1)
                      <div class="mt-3 d-flex flex-wrap">
                        @foreach($data->pictures as $idx => $pic)
                          @continue($idx === 0)
                          <img src="{{ asset($pic['path']) }}" class="rounded mr-2 mb-2"
                            style="width:72px;height:72px;object-fit:cover;" alt="pic-{{ $idx }}">
                        @endforeach
                      </div>
                    @endif

                    {{-- Deskripsi (readonly) --}}
                    @if(!empty($data->description))
                      <hr>
                      <div>
                        <div class="text-muted mb-1"><i
                            class="far fa-file-alt mr-1"></i>{{ __('messages.owner.products.outlet_products.description') }}
                        </div>
                        <div class="border rounded p-2" style="background:#fcfcfc;">
                          {!! $data->description !!}
                        </div>
                      </div>
                    @endif

                    {{-- Opsi: quantity tiap option bisa diedit + toggle always available + stock_type --}}
                    @if(($data->parent_options ?? null) && count($data->parent_options))
                      <hr>
                      <div class="text-muted mb-2"><i class="fas fa-list-ul mr-1"></i>{{ __('messages.owner.products.outlet_products.options') }}</div>

                      @foreach($data->parent_options as $parent)
                        <div class="mb-3 p-2 border rounded">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="font-weight-bold">
                              {{ $parent->name }}
                              @if($parent->provision)
                                <span class="badge badge-light border ml-1">
                                  @if ($parent->provision === 'OPTIONAL')
                                    {{ __('messages.owner.products.outlet_products.optional') }}
                                  @elseif ($parent->provision === 'OPTIONAL MAX')
                                    {{ __('messages.owner.products.outlet_products.optional_max') }}
                                  @elseif ($parent->provision === 'MAX')
                                    {{ __('messages.owner.products.outlet_products.max_provision') }}
                                  @elseif ($parent->provision === 'EXACT')
                                    {{ __('messages.owner.products.outlet_products.exact_provision') }}
                                  @elseif ($parent->provision === 'MIN')
                                    {{ __('messages.owner.products.outlet_products.min_provision') }}
                                  @endif
                                  {{ $parent->provision_value ? ' : ' . $parent->provision_value : '' }}
                                </span>
                              @endif
                            </div>
                            @if($parent->description)
                              <div class="text-muted small">{{ $parent->description }}</div>
                            @endif
                          </div>

                          @if($parent->options && count($parent->options))
                            <div class="table-responsive">
                              <table class="table table-sm mb-0">
                                <thead class="thead-light">
                                  <tr>
                                    <th style="width:25%">{{ __('messages.owner.products.outlet_products.option') }}</th>
                                    <th class="text-right" style="width:15%">{{ __('messages.owner.products.outlet_products.price') }}</th>
                                    <th class="text-center" style="width:15%">Stock Type</th>
                                    <th class="text-center" style="width:30%">{{ __('messages.owner.products.outlet_products.new_stock') }}</th>
                                    <th class="text-right" style="width:15%">{{ __('messages.owner.products.outlet_products.old_stock') }}</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach($parent->options as $opt)
                                    @php
                                      // Get stock_type dari option atau default ke 'direct'
                                      $optStockType = old("options.{$opt->id}.stock_type", $opt->stock_type ?? 'direct');
                                    @endphp
                                    <tr>
                                      <td>{{ $opt->name }}</td>
                                      <td class="text-right">
                                        Rp {{ number_format((float) ($opt->price ?? 0), 0, ',', '.') }}
                                      </td>
                                      <td class="text-center">
                                        {{-- Stock Type Selector --}}
                                        <select 
                                          name="options[{{ $opt->id }}][stock_type]" 
                                          class="form-control form-control-sm opt-stock-type"
                                          data-opt-id="{{ $opt->id }}"
                                        >
                                          <option value="direct" {{ $optStockType === 'direct' ? 'selected' : '' }}>Direct</option>
                                          <option value="linked" {{ $optStockType === 'linked' ? 'selected' : '' }}>Linked</option>
                                        </select>
                                      </td>
                                      <td class="text-center">
                                        {{-- NEW: toggle always available per-option --}}
                                        <div class="custom-control custom-switch custom-control-sm mb-2 text-left opt-aa-container-{{ $opt->id }}" style="display:inline-block;">
                                          <input type="hidden" name="options[{{ $opt->id }}][always_available]" value="0">
                                          <input
                                            type="checkbox"
                                            class="custom-control-input opt-aa"
                                            id="opt-aa-{{ $opt->id }}"
                                            name="options[{{ $opt->id }}][always_available]"
                                            value="1"
                                            data-qty="#opt-qty-{{ $opt->id }}"
                                            data-wrap="#opt-qty-wrap-{{ $opt->id }}"
                                            {{ old("options.{$opt->id}.always_available", $opt->always_available_flag ?? 0) ? 'checked' : '' }}
                                          >
                                          <label class="custom-control-label" for="opt-aa-{{ $opt->id }}">{{ __('messages.owner.products.outlet_products.always_available') }}</label>
                                        </div>

                                        {{-- NEW: bungkus quantity input agar bisa di-hide --}}
                                        <div id="opt-qty-wrap-{{ $opt->id }}" class="mt-2 opt-qty-wrapper">
                                          <div class="input-group input-group-sm justify-content-center" style="max-width:220px;margin:0 auto;">
                                            <div class="input-group-prepend">
                                              <button type="button" class="btn btn-outline-secondary btn-opt-dec" data-target="#opt-qty-{{ $opt->id }}">
                                                <i class="fas fa-minus"></i>
                                              </button>
                                            </div>
                                            <input
                                              type="number"
                                              id="opt-qty-{{ $opt->id }}"
                                              name="options[{{ $opt->id }}][quantity]"
                                              class="form-control text-center"
                                              min="0"
                                              step="1"
                                              value="{{ old("options.{$opt->id}.quantity", $opt->quantity_available ?? 0) }}"
                                              style="max-width:120px"
                                            >
                                            <div class="input-group-append">
                                              <button type="button" class="btn btn-outline-secondary btn-opt-inc" data-target="#opt-qty-{{ $opt->id }}">
                                                <i class="fas fa-plus"></i>
                                              </button>
                                            </div>
                                          </div>
                                        </div>

                                        {{-- Info untuk linked stock --}}
                                        <div id="opt-linked-info-{{ $opt->id }}" class="mt-2 alert alert-info py-2 small opt-linked-info" style="display:none;">
                                          <i class="fas fa-link mr-1"></i>
                                          Stok diatur oleh resep (Bahan Mentah).
                                        </div>
                                      </td>
                                      <td class="text-right">
                                        <span class="text-muted small">{{ old("options.{$opt->id}.quantity", $opt->quantity_available ?? 0) }}</span>
                                      </td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                          @endif
                        </div>
                      @endforeach
                    @endif
                  </div>
                </div>
              </div>

              {{-- RIGHT: Editable fields only (Quantity & Status untuk PRODUK) --}}
              <div class="col-lg-5">
                <div class="card border-0">
                  <div class="card-body">
                    <h5 class="mb-3">{{ __('messages.owner.products.outlet_products.update_stock_status') }}</h5>

                    @php
                      $initialQuantity = 0;

                      if ($data->stock_type === 'direct' && $data->stock) {
                        $initialQuantity = (int) $data->quantity_available;
                      }
                    @endphp

                    {{-- Toggle always available (product) --}}
                    <div class="form-group">
                      <div class="custom-control custom-switch">
                        <input type="hidden" name="always_available" value="0">
                        <input
                          type="checkbox"
                          class="custom-control-input"
                          id="aa_product"
                          name="always_available"
                          value="1"
                          {{ old('always_available', $data->always_available_flag ?? 0) ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="aa_product">
                          {{ __('messages.owner.products.outlet_products.always_available_product') }}
                        </label>
                      </div>
                      <small class="text-muted">{{ __('messages.owner.products.outlet_products.if_active_stock_hidden') }}</small>
                    </div>

                    {{-- Blok Stok Utama (Input 'quantity') --}}
                    @if($data->stock_type === 'direct')
                      <div class="form-group" id="product_qty_group">
                        <label class="mb-1">{{ __('messages.owner.products.outlet_products.stock_product') }} (pcs)</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <button type="button" class="btn btn-outline-secondary" id="btn-qty-dec">
                              <i class="fas fa-minus"></i>
                            </button>
                          </div>
                          <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            class="form-control text-center"
                            min="0"
                            step="1"
                            value="{{ old('quantity', $initialQuantity) }}" 
                          >
                          <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="btn-qty-inc">
                              <i class="fas fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btn-qty-max">{{ __('messages.owner.products.outlet_products.max') }}</button>
                          </div>
                        </div>
                        @error('quantity')
                          <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                      </div>

                      @if($pcsUnitId)
                        <input type="hidden" name="display_unit_id" value="{{ $pcsUnitId }}">
                      @else
                        <div class="alert alert-danger small py-2">
                          Error: Unit dasar 'pcs' tidak terdefinisi di Master Units.
                        </div>
                      @endif

                    @else
                      <div class="form-group">
                        <label class="mb-1">{{ __('messages.owner.products.outlet_products.stock_product') }}</label>
                        <div class="alert alert-info py-2 small">
                            <i class="fas fa-link mr-1"></i>
                            Stok diatur oleh resep (Bahan Mentah).
                        </div>
                        <input type="hidden" name="quantity" value="0">
                      </div>
                    @endif

                    {{-- Status (is_active) --}}
                    <div class="form-group">
                      <label class="mb-1">{{ __('messages.owner.products.outlet_products.status') }}</label>
                      <div class="custom-control custom-switch">
                        <input
                          type="checkbox"
                          class="custom-control-input"
                          id="is_active_switch"
                          {{ old('is_active', $data->is_active ?? 1) ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="is_active_switch">
                          <span id="is_active_label">{{ old('is_active', $data->is_active ?? 1) ? __('messages.owner.products.outlet_products.active') : __('messages.owner.products.outlet_products.inactive') }}</span>
                        </label>
                      </div>
                      <input type="hidden" name="is_active" id="is_active" value="{{ old('is_active', $data->is_active ?? 1) }}">
                      @error('is_active')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                      @enderror
                    </div>

                    {{-- Promotion --}}
                    <div class="form-group">
                      <label class="mb-1" for="promotion_id">{{ __('messages.owner.products.outlet_products.promotion') }}</label>
                      <select id="promotion_id" name="promotion_id" class="form-control">
                        @php
                          $selectedPromoId = old('promotion_id', $data->promo_id);
                        @endphp
                        <option value="">{{ __('messages.owner.products.outlet_products.no_promotion_dropdown') }}</option>
                        @foreach($promotions as $promo)
                          <option value="{{ $promo->id }}" {{ (string) $selectedPromoId === (string) $promo->id ? 'selected' : '' }}>
                            {{ $promo->promotion_name }}
                            (
                            @if($promo->promotion_type === 'percentage')
                              {{ number_format($promo->promotion_value, 0, ',', '.') }}% Off
                            @else
                              Rp. {{ number_format($promo->promotion_value, 0, ',', '.') }} Off
                            @endif
                            )
                          </option>
                        @endforeach
                      </select>
                      @error('promotion_id')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                      @enderror
                    </div>

                    {{-- Submit --}}
                    <hr>
                    <div class="d-flex justify-content-end">
                      <a href="{{ route('owner.user-owner.outlet-products.index') }}" class="btn btn-light border mr-2">{{ __('messages.owner.products.outlet_products.cancel') }}</a>
                      <button type="submit" class="btn btn-primary">{{ __('messages.owner.products.outlet_products.save_changes') }}</button>
                    </div>

                  </div>
                </div>

                {{-- Compact meta info (readonly) --}}
                <div class="card border-0 mt-3">
                  <div class="card-body small text-muted">
                    <div class="d-flex justify-content-between">
                      <span>{{ __('messages.owner.products.outlet_products.created') }}</span>
                      <strong>{{ optional($data->created_at)->format('d M Y, H:i') ?? '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                      <span>{{ __('messages.owner.products.outlet_products.last_updated') }}</span>
                      <strong>{{ optional($data->updated_at)->format('d M Y, H:i') ?? '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                      <span>{{ __('messages.owner.products.outlet_products.owner') }} </span>
                      <strong>{{ $data->owner->name ?? '-' }}</strong>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </form>
      </div>
    </div>
  </section>

  <style>
  /* ===== Owner › Outlet Product Edit (page scope) ===== */
  .owner-op-edit{
    --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b;
    --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
  }

  .owner-op-edit .card{
    border:0; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden;
  }
  .owner-op-edit .card-header{
    background:#fff; border-bottom:1px solid #eef1f4;
  }
  .owner-op-edit .card-title{ color:var(--ink); font-weight:700; }

  .owner-op-edit .btn.btn-secondary{
    background:#ffffff00; color:var(--choco); border:1px solid var(--choco);
  }
  .owner-op-edit .btn.btn-secondary:hover{
    background:var(--choco); color:#fff; border-color:var(--choco);
  }

  .owner-op-edit .btn-primary{ background:var(--choco); border-color:var(--choco); }
  .owner-op-edit .btn-primary:hover{ background:var(--soft-choco); border-color:var(--soft-choco); }
  .owner-op-edit .btn-light.border{ color:var(--choco); border-color:var(--choco); background:#fff; }
  .owner-op-edit .btn-light.border:hover{ color:#fff; background:var(--choco); }

  .owner-op-edit .badge-info{
    background:#eff6ff; color:#1d4ed8; border:1px solid #dbeafe;
  }
  .owner-op-edit .badge-light.border{
    background:#fff; color:#374151; border:1px solid #e5e7eb;
  }

  .owner-op-edit img.rounded{ border-radius:12px !important; box-shadow:var(--shadow); }

  .owner-op-edit .table{ background:#fff; margin-bottom:0; }
  .owner-op-edit .table thead th{
    background:#fff; border-bottom:2px solid #eef1f4 !important;
    color:#374151; font-weight:700; white-space:nowrap;
  }
  .owner-op-edit .table tbody tr{ transition:background-color .12s ease; }
  .owner-op-edit .table tbody tr:hover{ background:rgba(140,16,0,.04); }

  .owner-op-edit .input-group .btn-outline-secondary{ border-color:#d1d5db; }
  .owner-op-edit .input-group .btn-outline-secondary:hover{ background:#f3f4f6; }

  .owner-op-edit .custom-control{ min-height:1.75rem; }
  .owner-op-edit .custom-switch{ padding-left:2.6rem; }
  .owner-op-edit .custom-switch .custom-control-label{ cursor:pointer; padding-left:.25rem; }
  .owner-op-edit .custom-switch .custom-control-input:focus ~ .custom-control-label::before{
    border-color:var(--choco); box-shadow:0 0 0 .2rem rgba(140,16,0,.15);
  }
  .owner-op-edit .custom-switch .custom-control-input:checked ~ .custom-control-label::before{
    background-color:var(--choco); border-color:var(--choco);
  }

  .owner-op-edit #product_qty_group,
  .owner-op-edit [id^="opt-qty-wrap-"]{ transition:opacity .15s ease, transform .15s ease; }
  .owner-op-edit #product_qty_group.d-none,
  .owner-op-edit [id^="opt-qty-wrap-"].d-none{ opacity:0; transform:translateY(-4px); }

  .owner-op-edit .text-muted{ color:#6b7280 !important; }
  </style>

@endsection

@section('scripts')
  <script>
    // Quantity helpers (produk)
    (function () {
      const qty = document.getElementById('quantity');
      const dec = document.getElementById('btn-qty-dec');
      const inc = document.getElementById('btn-qty-inc');
      const max = document.getElementById('btn-qty-max');
      const toInt = (v) => { const n = parseInt(v, 10); return isNaN(n) ? 0 : n; };

      dec?.addEventListener('click', () => { if (qty.disabled) return; qty.value = Math.max(0, toInt(qty.value) - 1); });
      inc?.addEventListener('click', () => { if (qty.disabled) return; qty.value = toInt(qty.value) + 1; });
      max?.addEventListener('click', () => { if (qty.disabled) return; qty.value = 999999999; });
    })();

    // is_active switch <-> hidden input
    (function () {
      const sw = document.getElementById('is_active_switch');
      const hid = document.getElementById('is_active');
      const lab = document.getElementById('is_active_label');
      function sync(){
        hid.value = sw.checked ? 1 : 0;
        if (lab) lab.textContent = sw.checked ? 'Active' : 'Inactive';
      }
      sw?.addEventListener('change', sync);
    })();

    // +/- untuk option quantity (hindari update saat disabled)
    (function () {
      const toInt = (v) => { const n = parseInt(v, 10); return isNaN(n) ? 0 : n; };
      document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-opt-dec')) {
          const target = e.target.closest('.btn-opt-dec').dataset.target;
          const input = document.querySelector(target);
          if (!input || input.disabled) return;
          input.value = Math.max(0, toInt(input.value) - 1);
        }
        if (e.target.closest('.btn-opt-inc')) {
          const target = e.target.closest('.btn-opt-inc').dataset.target;
          const input = document.querySelector(target);
          if (!input || input.disabled) return;
          input.value = toInt(input.value) + 1;
        }
      });
    })();

    // NEW: toggle logic product & options (always available)
    (function () {
      function hideQty(wrapperEl, inputEl, checked) {
        if (!wrapperEl || !inputEl) return;
        if (checked) {
          if (!inputEl.dataset.prev) inputEl.dataset.prev = inputEl.value || '0';
          wrapperEl.classList.add('d-none');
          inputEl.disabled = true;
        } else {
          wrapperEl.classList.remove('d-none');
          inputEl.disabled = false;
          if (inputEl.dataset.prev) inputEl.value = inputEl.dataset.prev;
        }
      }

      // Product toggle
      const aaProd = document.getElementById('aa_product');
      const prodWrap = document.getElementById('product_qty_group');
      const prodQty = document.getElementById('quantity');
      function syncProd() { hideQty(prodWrap, prodQty, aaProd?.checked); }
      aaProd?.addEventListener('change', syncProd);
      syncProd();

      // Option toggles
      function syncOneOpt(toggle) {
        const qtySel = toggle.getAttribute('data-qty');
        const wrapSel = toggle.getAttribute('data-wrap');
        const qty = document.querySelector(qtySel);
        const wrap = document.querySelector(wrapSel);
        hideQty(wrap, qty, toggle.checked);
      }
      document.querySelectorAll('.opt-aa').forEach(tg => {
        tg.addEventListener('change', () => syncOneOpt(tg));
        syncOneOpt(tg);
      });
    })();

    // NEW: Stock Type Toggle Logic for Options
    (function () {
      function handleStockTypeChange(selectEl) {
        const optId = selectEl.dataset.optId;
        const stockType = selectEl.value;

        const aaContainer = document.querySelector(`.opt-aa-container-${optId}`);
        const qtyWrapper = document.getElementById(`opt-qty-wrap-${optId}`);
        const linkedInfo = document.getElementById(`opt-linked-info-${optId}`);
        const aaCheckbox = document.getElementById(`opt-aa-${optId}`);

        if (stockType === 'linked') {
          // Hide always available & quantity, show linked info
          if (aaContainer) aaContainer.style.display = 'none';
          if (qtyWrapper) qtyWrapper.style.display = 'none';
          if (linkedInfo) linkedInfo.style.display = 'block';
          // Uncheck always available jika sedang aktif
          if (aaCheckbox) aaCheckbox.checked = false;
        } else {
          // Show always available & quantity, hide linked info
          if (aaContainer) aaContainer.style.display = 'inline-block';
          if (qtyWrapper) qtyWrapper.style.display = 'block';
          if (linkedInfo) linkedInfo.style.display = 'none';
        }
      }

      // Initialize all stock type selectors
      document.querySelectorAll('.opt-stock-type').forEach(select => {
        // Handle initial state
        handleStockTypeChange(select);

        // Add change listener
        select.addEventListener('change', function() {
          handleStockTypeChange(this);
        });
      });
    })();
  </script>
@endsection