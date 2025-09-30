@extends('layouts.partner')

@section('title', 'Update Product Stock')
@section('page_title', 'Update Product Stock')

@push('styles')
  @vite('resources/css/pages/product-stock.css')
@endpush

@section('content')
<section class="content product-stock">
  <div class="container-fluid">

    <a href="{{ route('partner.products.index') }}" class="btn btn-outline-choco mb-3 btn-pill">
      <i class="fas fa-arrow-left mr-2"></i> Back to Products
    </a>

    @if ($errors->any())
      <div class="alert alert-danger brand-alert">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="card card-shell border-0 shadow-sm">
      <div class="card-header brand-header">
        <h3 class="card-title mb-0">Edit Stock</h3>
      </div>

      <form action="{{ route('partner.products.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body">
          <div class="row g-3">
            {{-- LEFT: Ringkasan readonly --}}
            <div class="col-lg-7">
              <div class="card border-0 mb-3 inner-card">
                <div class="card-body">
                  <div class="d-flex align-items-start">
                    <div class="me-3">
                      @php
                        $firstPic = is_array($data->pictures ?? null) && count($data->pictures) ? $data->pictures[0]['path'] : null;
                      @endphp
                      <img
                        src="{{ $firstPic ? asset($firstPic) : 'https://via.placeholder.com/120x120?text=No+Image' }}"
                        alt="{{ $data->name }}"
                        class="thumb-120"
                      >
                    </div>

                    <div class="flex-fill">
                      <h4 class="mb-1 fw-600">{{ $data->name }}</h4>

                      <div class="badges-row mb-2">
                        <span class="badge badge-soft-info">
                          {{ optional($data->category)->category_name ?? 'Uncategorized' }}
                        </span>
                        <span class="badge badge-soft-neutral">Code: {{ $data->product_code ?? '-' }}</span>
                        <span class="badge badge-soft-neutral">Outlet ID: {{ $data->partner_id }}</span>
                      </div>

                      <div class="meta small text-muted">
                        <div class="mb-1"><i class="fas fa-tags me-1"></i>
                          Price:
                          <strong>Rp {{ number_format((float)($data->price ?? 0), 0, ',', '.') }}</strong>
                        </div>

                        <div class="mb-1 d-flex flex-wrap align-items-center">
                          <span class="me-1"><i class="fas fa-percentage me-1"></i>Promotion:</span>
                          <strong class="me-2">{{ $data->promotion ? 'Applied' : '—' }}</strong>
                          @if($data->promotion)
                            <span class="badge badge-soft-neutral">
                              {{ $data->promotion->promotion_name ?? '-' }}
                              (@if($data->promotion->promotion_type === 'percentage')
                                discount {{ intval($data->promotion->promotion_value ?? 0) }}%
                              @elseif($data->promotion->promotion_type === 'amount')
                                potongan Rp {{ number_format((float)($data->promotion->promotion_value ?? 0), 0, ',', '.') }}
                              @endif)
                            </span>
                          @endif
                        </div>

                        <div><i class="fas fa-info-circle me-1"></i>
                          Master Product:
                          <strong>{{ $data->master_product_id ?? '-' }}</strong>
                        </div>
                      </div>
                    </div>
                  </div>

                  @if(!empty($data->description))
                    <hr>
                    <div>
                      <div class="text-muted mb-1 fw-600"><i class="far fa-file-alt me-1"></i>Description</div>
                      <div class="desc-box">
                        {!! $data->description !!}
                      </div>
                    </div>
                  @endif

                  @if(($data->parent_options ?? null) && count($data->parent_options))
                    <hr>
                    <div class="text-muted mb-2 fw-600"><i class="fas fa-list-ul me-1"></i>Options</div>

                    @foreach($data->parent_options as $parent)
                      <div class="mb-3 p-2 border rounded-3 option-group">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                          <div class="fw-600">
                            {{ $parent->name }}
                            @if($parent->provision)
                              <span class="badge badge-soft-neutral ms-1">
                                {{ $parent->provision }}
                                {{ $parent->provision_value ? ' : '.$parent->provision_value : '' }}
                              </span>
                            @endif
                          </div>
                          @if($parent->description)
                            <div class="text-muted small ms-3">{{ $parent->description }}</div>
                          @endif
                        </div>

                        @if($parent->options && count($parent->options))
                          <div class="table-responsive rounded-3">
                            <table class="table table-sm table-hover align-middle options-table mb-0">
                              <thead>
                                <tr>
                                  <th style="width:40%">Option</th>
                                  <th class="text-end" style="width:20%">Price</th>
                                  <th class="text-center" style="width:30%">Quantity</th>
                                  <th class="text-end" style="width:10%">Info</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($parent->options as $opt)
                                  @php
                                    $optUnlimited = old("options.{$opt->id}.always_available", $opt->always_available_flag ?? ($opt->quantity === null ? 1 : 0));
                                  @endphp
                                  <tr>
                                    <td class="fw-500">{{ $opt->name }}</td>

                                    <td class="text-end">
                                      Rp {{ number_format((float)($opt->price ?? 0), 0, ',', '.') }}
                                    </td>

                                    <td class="text-center">
                                      {{-- switch always available --}}
                                      <div class="custom-control custom-switch custom-control-sm mb-2 text-start d-inline-block">
                                        <input type="hidden" name="options[{{ $opt->id }}][always_available]" value="0">
                                        <input
                                          type="checkbox"
                                          class="custom-control-input opt-aa"
                                          id="opt-aa-{{ $opt->id }}"
                                          name="options[{{ $opt->id }}][always_available]"
                                          value="1"
                                          data-qty="#opt-qty-{{ $opt->id }}"
                                          data-wrap="#opt-qty-wrap-{{ $opt->id }}"
                                          {{ $optUnlimited ? 'checked' : '' }}
                                        >
                                        <label class="custom-control-label" for="opt-aa-{{ $opt->id }}">Always available</label>
                                      </div>

                                      {{-- qty --}}
                                      <div id="opt-qty-wrap-{{ $opt->id }}" class="mt-2">
                                        <div class="input-group input-group-sm justify-content-center qty-group">
                                          <button type="button" class="btn btn-qty btn-outline-secondary btn-opt-dec" data-target="#opt-qty-{{ $opt->id }}">
                                            <i class="fas fa-minus"></i>
                                          </button>
                                          <input
                                            type="number"
                                            id="opt-qty-{{ $opt->id }}"
                                            name="options[{{ $opt->id }}][quantity]"
                                            class="form-control text-center qty-input"
                                            min="0"
                                            step="1"
                                            value="{{ old("options.{$opt->id}.quantity", $opt->quantity ?? 0) }}"
                                          >
                                          <button type="button" class="btn btn-qty btn-outline-secondary btn-opt-inc" data-target="#opt-qty-{{ $opt->id }}">
                                            <i class="fas fa-plus"></i>
                                          </button>
                                        </div>
                                      </div>
                                    </td>

                                    <td class="text-end">
                                      <span class="text-muted small">Last: {{ (int)($opt->quantity ?? 0) }}</span>
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

            {{-- RIGHT: Quantity produk --}}
            <div class="col-lg-5">
              <div class="card border-0 inner-card">
                <div class="card-body">
                  <h5 class="mb-3 fw-600">Update Stock</h5>

                  @php
                    $prodUnlimited = old('always_available', $data->always_available_flag ?? ($data->quantity === null ? 1 : 0));
                  @endphp

                  <div class="form-group mb-3">
                    <div class="custom-control custom-switch">
                      <input type="hidden" name="always_available" value="0">
                      <input
                        type="checkbox"
                        class="custom-control-input"
                        id="aa_product"
                        name="always_available"
                        value="1"
                        {{ $prodUnlimited ? 'checked' : '' }}
                      >
                      <label class="custom-control-label" for="aa_product">
                        Produk selalu tersedia (tanpa stok)
                      </label>
                    </div>
                    <small class="text-muted">Jika aktif, kolom Quantity akan disembunyikan.</small>
                  </div>

                  <div class="form-group" id="product_qty_group">
                    <label class="mb-1 fw-600">Quantity (Product)</label>
                    <div class="input-group qty-group">
                      <button type="button" class="btn btn-qty btn-outline-secondary" id="btn-qty-dec">
                        <i class="fas fa-minus"></i>
                      </button>

                      <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        class="form-control text-center qty-input"
                        min="0"
                        step="1"
                        value="{{ old('quantity', $data->quantity ?? 0) }}"
                        required
                      >

                      <button type="button" class="btn btn-qty btn-outline-secondary" id="btn-qty-inc">
                        <i class="fas fa-plus"></i>
                      </button>
                      <button type="button" class="btn btn-outline-choco" id="btn-qty-max">Max</button>
                    </div>
                    @error('quantity')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>

                  <hr>
                  <div class="d-flex justify-content-end">
                    <a href="{{ route('partner.products.index') }}" class="btn btn-outline-choco me-2">Cancel</a>
                    <button type="submit" class="btn btn-choco">Save Changes</button>
                  </div>
                </div>
              </div>

              <div class="card border-0 inner-card mt-3">
                <div class="card-body small text-muted">
                  <div class="d-flex justify-content-between"><span>Created</span><strong>{{ optional($data->created_at)->format('d M Y, H:i') ?? '-' }}</strong></div>
                  <div class="d-flex justify-content-between"><span>Last Updated</span><strong>{{ optional($data->updated_at)->format('d M Y, H:i') ?? '-' }}</strong></div>
                  <div class="d-flex justify-content-between"><span>Owner</span><strong>{{ $data->owner->name ?? '-' }}</strong></div>
                </div>
              </div>
            </div>

          </div>{{-- /row --}}
        </div>{{-- /card-body --}}
      </form>
    </div>
  </div>
</section>

<style>
  /* ==== Update Product Stock (page scope) ==== */
:root{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

.product-stock .card-shell{ border-radius: var(--radius); box-shadow: var(--shadow); }
.product-stock .brand-header{
  background: linear-gradient(135deg, var(--choco), var(--soft-choco));
  color:#fff; border-bottom:0; border-radius: var(--radius) var(--radius) 0 0;
}
.product-stock .inner-card{ border-radius: var(--radius); box-shadow: var(--shadow); }
.product-stock .fw-600{ font-weight:600; }
.product-stock .fw-500{ font-weight:500; }

.btn-pill{ border-radius:999px; }
.btn-choco{ background:var(--choco); border-color:var(--choco); color:#fff; }
.btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); }
.btn-outline-choco{ color:var(--choco); border-color:var(--choco); background:#fff; }
.btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }

.brand-alert{
  border-left:4px solid var(--choco);
  border-radius:10px;
}

/* Thumb */
.thumb-120{
  width:120px; height:120px; object-fit:cover;
  border-radius:12px; border:0; box-shadow: var(--shadow);
}

/* Badges soft */
.badges-row .badge{ margin-right:.35rem; }
.badge-soft-info{
  background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; border-radius:999px; font-weight:600;
}
.badge-soft-neutral{
  background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:999px; font-weight:600;
}
.badge-soft-success{
  background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; border-radius:999px; font-weight:600;
}

/* Description box */
.desc-box{
  background:#fcfcfc; border:1px solid #eef1f4; border-radius:10px; padding:.75rem;
}

/* Table options */
.options-table thead th{
  background:#fff;
  border-bottom:2px solid #eef1f4 !important;
  color:#374151; font-weight:700;
}
.options-table tbody tr{ transition: background-color .12s ease; }
.options-table tbody tr:hover{ background: rgba(140,16,0,.04); }

/* Quantity group */
.qty-group{
  max-width: 360px;
  margin-left:auto; margin-right:auto;
  gap:.5rem;
}
.qty-input{
  max-width:140px;
  border-color:#e5e7eb;
}
.qty-input:focus{
  border-color: var(--choco);
  box-shadow: 0 0 0 .2rem rgba(140,16,0,.15);
}
.btn-qty{ min-width:40px; }

/* Switch color (Bootstrap 4 custom-switch) */
.custom-control-input:checked ~ .custom-control-label::before{
  background-color: var(--choco);
  border-color: var(--choco);
}
.custom-control-input:focus ~ .custom-control-label::before{
  box-shadow: 0 0 0 .2rem rgba(140,16,0,.18);
  border-color: var(--soft-choco);
}

/* Small polish */
.option-group{ border-color:#eef1f4 !important; background:#fff; }
.meta i{ color:#9ca3af; }

</style>
@endsection

@section('scripts')
<script>
  // Quantity product
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

  // +/- options
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

  // Toggle Always available
  (function () {
    function toggleQty(wrapperEl, inputEl, checked) {
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

    // Product
    const aaProd  = document.getElementById('aa_product');
    const prodWrap = document.getElementById('product_qty_group');
    const prodQty  = document.getElementById('quantity');
    function syncProd(){ toggleQty(prodWrap, prodQty, aaProd?.checked); }
    aaProd?.addEventListener('change', syncProd);
    syncProd();

    // Options
    function syncOneOpt(tg) {
      const qtySel = tg.getAttribute('data-qty');
      const wrapSel = tg.getAttribute('data-wrap');
      const qty = document.querySelector(qtySel);
      const wrap = document.querySelector(wrapSel);
      toggleQty(wrap, qty, tg.checked);
    }
    document.querySelectorAll('.opt-aa').forEach(tg => {
      tg.addEventListener('change', () => syncOneOpt(tg));
      syncOneOpt(tg);
    });
  })();
</script>
@endsection
