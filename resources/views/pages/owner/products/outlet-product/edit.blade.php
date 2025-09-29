@extends('layouts.owner')

@section('title', 'Update Outlet Product')
@section('page_title', 'Update Outlet Product')

@section('content')
<section class="content">
  <div class="container-fluid">
    <a href="{{ route('owner.user-owner.outlet-products.index') }}" class="btn btn-secondary mb-3">
      <i class="fas fa-arrow-left mr-2"></i>Back to Outlet Products
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
        <h3 class="card-title mb-0">Edit Outlet Product</h3>
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
                        alt="{{ $data->name }}"
                        class="rounded"
                        style="width:120px;height:120px;object-fit:cover;"
                      >
                    </div>
                    <div class="flex-fill">
                      <h4 class="mb-1">{{ $data->name }}</h4>
                      <div class="mb-2">
                        <span class="badge badge-info">
                          {{ optional($data->category)->category_name ?? 'Uncategorized' }}
                        </span>
                        <span class="badge badge-light border ml-1">Code: {{ $data->product_code ?? '-' }}</span>
                        <span class="badge badge-light border ml-1">Outlet ID: {{ $data->partner_id }}</span>
                      </div>

                      <div class="d-flex flex-wrap text-muted small">
                        <div class="mr-4">
                          <i class="fas fa-tags mr-1"></i>
                          Price:
                          <strong>
                            Rp {{ number_format((float)($data->price ?? 0), 0, ',', '.') }}
                          </strong>
                        </div>
                        <div class="mr-4">
                          <i class="fas fa-barcode mr-1"></i>
                          Master Product:
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
                        <img
                          src="{{ asset($pic['path']) }}"
                          class="rounded mr-2 mb-2"
                          style="width:72px;height:72px;object-fit:cover;"
                          alt="pic-{{ $idx }}"
                        >
                      @endforeach
                    </div>
                  @endif

                  {{-- Deskripsi (readonly) --}}
                  @if(!empty($data->description))
                    <hr>
                    <div>
                      <div class="text-muted mb-1"><i class="far fa-file-alt mr-1"></i>Description</div>
                      <div class="border rounded p-2" style="background:#fcfcfc;">
                        {!! $data->description !!}
                      </div>
                    </div>
                  @endif

                  {{-- Opsi: quantity tiap option bisa diedit + toggle always available --}}
                  @if(($data->parent_options ?? null) && count($data->parent_options))
                    <hr>
                    <div class="text-muted mb-2"><i class="fas fa-list-ul mr-1"></i>Options</div>

                    @foreach($data->parent_options as $parent)
                      <div class="mb-3 p-2 border rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div class="font-weight-bold">
                            {{ $parent->name }}
                            @if($parent->provision)
                              <span class="badge badge-light border ml-1">
                                @if ($parent->provision === 'OPTIONAL')
                                    Opsional
                                @elseif ($parent->provision === 'OPTIONAL MAX')
                                    Opsional, Maksimal Pilih
                                @elseif ($parent->provision === 'MAX')
                                    Wajib, Maksimal Pilih
                                @elseif ($parent->provision === 'EXACT')
                                    Wajib, Pilih
                                @elseif ($parent->provision === 'MIN')
                                    Wajib, Minimal Pilih
                                @endif
                                {{ $parent->provision_value ? ' : '.$parent->provision_value : '' }}
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
                                  <th style="width:40%">Option</th>
                                  <th class="text-right" style="width:20%">Price</th>
                                  <th class="text-center" style="width:30%">Quantity</th>
                                  <th class="text-right" style="width:10%">Stock</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($parent->options as $opt)
                                  <tr>
                                    <td>{{ $opt->name }}</td>
                                    <td class="text-right">
                                      Rp {{ number_format((float)($opt->price ?? 0), 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                      {{-- NEW: toggle always available per-option --}}
                                      <div class="custom-control custom-switch custom-control-sm mb-2 text-left" style="display:inline-block;">
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
                                        <label class="custom-control-label" for="opt-aa-{{ $opt->id }}">Always available</label>
                                      </div>

                                      {{-- NEW: bungkus quantity input agar bisa di-hide --}}
                                      <div id="opt-qty-wrap-{{ $opt->id }}" class="mt-2">
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
                                            value="{{ old("options.{$opt->id}.quantity", $opt->quantity ?? 0) }}"
                                            style="max-width:120px"
                                          >
                                          <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary btn-opt-inc" data-target="#opt-qty-{{ $opt->id }}">
                                              <i class="fas fa-plus"></i>
                                            </button>
                                          </div>
                                        </div>
                                      </div>
                                    </td>
                                    <td class="text-right">
                                      <span class="text-muted small">{{ old("options.{$opt->id}.quantity", $opt->quantity ?? 0) }}</span>
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
                  <h5 class="mb-3">Update Stock & Status</h5>

                  {{-- NEW: toggle always available (product) --}}
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
                        Produk selalu tersedia (tanpa stok)
                      </label>
                    </div>
                    <small class="text-muted">Jika aktif, kolom Quantity akan disembunyikan.</small>
                  </div>

                  {{-- Quantity produk --}}
                  <div class="form-group" id="product_qty_group">
                    <label class="mb-1">Quantity (Product)</label>
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
                        value="{{ old('quantity', $data->quantity ?? 0) }}"
                        required
                      >
                      <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary" id="btn-qty-inc">
                          <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btn-qty-max">Max</button>
                      </div>
                    </div>
                    @error('quantity')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>

                  {{-- Status (is_active) --}}
                  <div class="form-group">
                    <label class="mb-1">Status</label>
                    <div class="custom-control custom-switch">
                      <input
                        type="checkbox"
                        class="custom-control-input"
                        id="is_active_switch"
                        {{ old('is_active', $data->is_active ?? 1) ? 'checked' : '' }}
                      >
                      <label class="custom-control-label" for="is_active_switch">
                        <span id="is_active_label">{{ old('is_active', $data->is_active ?? 1) ? 'Active' : 'Inactive' }}</span>
                      </label>
                    </div>
                    {{-- hidden value yang dikirim --}}
                    <input type="hidden" name="is_active" id="is_active" value="{{ old('is_active', $data->is_active ?? 1) }}">
                    @error('is_active')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                  
                  {{-- Promotion (owner punya hak; biarkan ada) --}}
                  <div class="form-group">
                    <label class="mb-1" for="promotion_id">Promotion</label>
                    <select id="promotion_id" name="promotion_id" class="form-control">
                      @php
                        $selectedPromoId = old('promotion_id', $data->promo_id);
                      @endphp
                      <option value="">— No Promotion —</option>
                      @foreach($promotions as $promo)
                        <option value="{{ $promo->id }}" {{ (string)$selectedPromoId === (string)$promo->id ? 'selected' : '' }}>
                          {{ $promo->promotion_name }}
                          (
                          @if($promo->promotion_type === 'percentage')
                            {{ number_format($promo->promotion_value, 0, ',', '.') }}% Off
                          @else
                            Rp.
                            @if(fmod($promo->promotion_value, 1) == 0)
                              {{ number_format($promo->promotion_value, 0, ',', '.') }} Off
                            @else
                              {{ number_format($promo->promotion_value, 2, ',', '.') }} Off
                            @endif
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
                    <a href="{{ route('owner.user-owner.outlet-products.index') }}" class="btn btn-light border mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                  </div>

                </div>
              </div>

              {{-- Compact meta info (readonly) --}}
              <div class="card border-0 mt-3">
                <div class="card-body small text-muted">
                  <div class="d-flex justify-content-between">
                    <span>Created</span>
                    <strong>{{ optional($data->created_at)->format('d M Y, H:i') ?? '-' }}</strong>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span>Last Updated</span>
                    <strong>{{ optional($data->updated_at)->format('d M Y, H:i') ?? '-' }}</strong>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span>Owner </span>
                    <strong>{{ $data->owner->name ?? '-' }}</strong>
                  </div>
                </div>
              </div>
            </div>

          </div> {{-- /row --}}
        </div> {{-- /card-body --}}
      </form>
    </div>
  </div>
</section>
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
    syncProd(); // initial

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
      // initial
      syncOneOpt(tg);
    });
  })();
</script>
@endsection
