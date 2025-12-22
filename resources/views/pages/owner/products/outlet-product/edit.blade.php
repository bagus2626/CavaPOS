@extends('layouts.owner')

@section('title', __('messages.owner.products.outlet_products.update_outlet_product'))
@section('page_title', __('messages.owner.products.outlet_products.update_outlet_product'))

@section('content')
<section class="content">
    <div class="container-fluid owner-op-edit">
      <a href="{{ route('owner.user-owner.outlet-products.index') }}" class="btn btn-primary mb-3">
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
                      <div class="mr-3 position-relative" style="width:120px; height:120px;">
                          @php
                              $firstPic = is_array($data->pictures ?? null) && count($data->pictures)
                                  ? $data->pictures[0]['path']
                                  : null;
                          @endphp

                          {{-- Tampilkan gambar jika ada --}}
                          @if($firstPic)
                              <img src="{{ asset($firstPic) }}"
                                  alt="{{ $data->name }}"
                                  class="rounded"
                                  style="width:120px;height:120px;object-fit:cover;">
                          @else
                              {{-- Placeholder --}}
                              <div style="
                                  width:120px;height:120px;
                                  background:#f3f4f6;
                                  border-radius:6px;
                                  display:flex;
                                  align-items:center;
                                  justify-content:center;
                                  font-size:18px;
                                  color:#9ca3af;
                              ">
                                  <i class="fas fa-image"></i>
                              </div>
                          @endif

                          {{-- HOT BADGE --}}
                          @if($data->is_hot_product)
                              <span style="
                                  position:absolute;
                                  top:-8px;
                                  right:-8px;
                                  background:#ff5722;
                                  color:white;
                                  padding:4px 8px;
                                  border-radius:10px;
                                  font-size:12px;
                                  font-weight:600;
                                  box-shadow:0 2px 6px rgba(0,0,0,0.2);
                              ">
                                  HOT
                              </span>
                          @endif

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
                          <div class="d-flex justify-content-between align-items-start mb-2">
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
                              <div class="text-muted small ml-3">{{ $parent->description }}</div>
                            @endif
                          </div>

                          @if($parent->options && count($parent->options))
                            <div class="table-responsive">
                              <table class="table table-sm mb-0">
                                <thead class="thead-light">
                                  <tr>
                                    <th style="">{{ __('messages.owner.products.outlet_products.option') }}</th>
                                    <th class="" style="">{{ __('messages.owner.products.outlet_products.price') }}</th>
                                    <th class="text-center" style="">{{ __('messages.owner.products.outlet_products.stock_type') }}</th>
                                    <th class="text-center" style="">{{ __('messages.owner.products.outlet_products.quantity') }}</th>
                                    <th class="text-center" style="">{{ __('messages.owner.products.outlet_products.last') }}</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach($parent->options as $opt)
                                    @php
                                      $optStockType = old("options.{$opt->id}.stock_type", $opt->stock_type ?? 'direct');
                                      $optUnlimited = old("options.{$opt->id}.always_available", $opt->always_available_flag ?? 0);
                                      $optCurrentQty = (int) ($opt->quantity_available ?? 0);
                                    @endphp
                                    <tr>
                                      <td class="font-weight-bold">{{ $opt->name }}</td>
                                      <td class="">
                                        Rp {{ number_format((float) ($opt->price ?? 0), 0, ',', '.') }}
                                      </td>
                                      <td class="text-center">
                                        <select 
                                          name="options[{{ $opt->id }}][stock_type]" 
                                          class="form-control form-control-sm opt-stock-type"
                                          data-opt-id="{{ $opt->id }}"
                                        >
                                          <option value="direct" {{ $optStockType === 'direct' ? 'selected' : '' }}>Direct</option>
                                          <option value="linked" {{ $optStockType === 'linked' ? 'selected' : '' }}>Linked</option>
                                        </select>
                                      </td>
                                      <td>
                                        {{-- Always Available Toggle --}}
                                        <div class="opt-aa-container-{{ $opt->id }} mb-2" style="display:inline-block;">
                                          <div class="custom-control custom-switch custom-control-sm">
                                            <input type="hidden" name="options[{{ $opt->id }}][always_available]" value="0">
                                            <input
                                              type="checkbox"
                                              class="custom-control-input opt-aa"
                                              id="opt-aa-{{ $opt->id }}"
                                              name="options[{{ $opt->id }}][always_available]"
                                              value="1"
                                              data-opt-id="{{ $opt->id }}"
                                              {{ $optUnlimited ? 'checked' : '' }}
                                            >
                                            <label class="custom-control-label" for="opt-aa-{{ $opt->id }}">
                                              <small>{{ __('messages.owner.products.outlet_products.always_available') }}</small>
                                            </label>
                                          </div>
                                        </div>

                                        {{-- Direct Stock Input (Adjustment Style) --}}
                                        <div id="opt-qty-wrap-{{ $opt->id }}" class="opt-qty-wrapper">
                                          {{-- New Stock Input --}}
                                          <input
                                            type="number"
                                            id="opt-new-qty-{{ $opt->id }}"
                                            name="options[{{ $opt->id }}][new_quantity]"
                                            class="form-control form-control-sm text-center mb-1 opt-new-qty"
                                            min="0"
                                            step="1"
                                            value="{{ old("options.{$opt->id}.new_quantity", $optCurrentQty) }}"
                                            placeholder="{{ __('messages.owner.products.outlet_products.enter_new_stock') }}"
                                            data-opt-id="{{ $opt->id }}"
                                          >

                                          {{-- Hidden: Current Quantity --}}
                                          <input 
                                            type="hidden" 
                                            id="opt-current-qty-{{ $opt->id }}" 
                                            name="options[{{ $opt->id }}][current_quantity]" 
                                            value="{{ $optCurrentQty }}"
                                          >

                                          {{-- Adjustment Info --}}
                                          <div id="opt-adj-info-{{ $opt->id }}" class="alert alert-secondary py-1 px-2 small" style="display: none; margin: 0;">
                                            <strong><span class="opt-adj-type">-</span> :</strong> 
                                            <span class="opt-adj-amount">-</span>
                                          </div>
                                        </div>

                                        {{-- Linked Info + Button --}}
                                        <div id="opt-linked-info-{{ $opt->id }}" class="opt-linked-info" style="display:none;">
                                          <div class="alert alert-info py-2 px-2 mb-1 small">
                                            <i class="fas fa-link mr-1"></i>{{ __('messages.owner.products.outlet_products.stock_controlled_by_recipe') }}
                                          </div>
                                          <button type="button" class="btn btn-sm btn-outline-choco btn-block btn-manage-recipe" data-opt-id="{{ $opt->id }}" data-opt-name="{{ $opt->name }}" data-partner-id="{{ $data->partner_id }}">
                                            {{ __('messages.owner.products.outlet_products.manage_recipe') }}
                                          </button>
                                        </div>
                                      </td>
                                      <td class="text-center">
                                        <span class="text-muted small">{{ $optCurrentQty }}</span>
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
                      $prodUnlimited = old('always_available', $data->always_available_flag ?? 0);
                      $prodStockType = old('stock_type', $data->stock_type ?? 'direct');
                      $currentQuantity = 0;
                      if ($data->stock_type === 'direct' && $data->stock) {
                        $currentQuantity = (int) $data->quantity_available;
                      }
                    @endphp

                    {{-- === Price === --}}
                    <div class="form-group mb-3">
                      <label class="mb-1 font-weight-bold">
                        {{ __('messages.owner.products.outlet_products.price') }}
                      </label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">Rp.</span>
                        </div>
                        <input
                          type="text"
                          name="price"
                          id="outlet_price"
                          class="form-control"
                          value="{{ old('price', number_format((float) ($data->price ?? 0), 0, ',', '.')) }}"
                          autocomplete="off"
                          required
                        >
                      </div>
                      @error('price')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                      @enderror
                    </div>

                    {{-- Stock Type Product --}}
                    <div class="form-group mb-3">
                      <label class="mb-2 font-weight-bold">{{ __('messages.owner.products.outlet_products.stock_type') }}</label>
                      <select name="stock_type" id="product_stock_type" class="form-control">
                        <option value="direct" {{ $prodStockType === 'direct' ? 'selected' : '' }}>Direct</option>
                        <option value="linked" {{ $prodStockType === 'linked' ? 'selected' : '' }}>Linked</option>
                      </select>
                    </div>

                    {{-- Toggle always available (product) --}}
                    <div class="form-group mb-3" id="product_aa_group">
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
                          {{ __('messages.owner.products.outlet_products.always_available_product') }}
                        </label>
                      </div>
                      <small class="text-muted">{{ __('messages.owner.products.outlet_products.if_active_stock_hidden') }}</small>
                    </div>

                    {{-- ===== ADJUSTMENT STYLE STOCK INPUT ===== --}}
                    <div class="form-group mb-3" id="product_qty_group">
                      <label class="mb-1 font-weight-bold">{{ __('messages.owner.products.outlet_products.quantity') }}</label>

                      <input
                        type="number"
                        id="new_quantity"
                        name="new_quantity"
                        class="form-control text-center mb-2"
                        min="0"
                        step="1"
                        value="{{ old('new_quantity', $currentQuantity) }}"
                        placeholder="{{ __('messages.owner.products.outlet_products.enter_new_stock') }}"
                      >

                      {{-- Hidden: Current Stock for calculation --}}
                      <input type="hidden" id="current_quantity" name="current_quantity" value="{{ $currentQuantity }}">

                      {{-- Adjustment Info --}}
                      <div id="adjustment_info" class="alert alert-secondary py-2" style="display: none;">
                        <small>
                          <strong><span id="adjustment_type">-</span> :</strong> 
                          <span id="adjustment_amount">-</span>
                        </small>
                      </div>

                      @error('new_quantity')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                      @enderror
                    </div>

                    {{-- Linked Info + Button (untuk linked) --}}
                    <div class="form-group mb-3" id="product_linked_group" style="display:none;">
                      <label class="mb-2 font-weight-bold">{{ __('messages.owner.products.outlet_products.stock_product') }}</label>
                      <div class="alert alert-info py-2 mb-2">
                        <i class="fas fa-link mr-2"></i>
                        <span>{{ __('messages.owner.products.outlet_products.stock_controlled_by_recipe') }}</span>
                      </div>
                      <button type="button" class="btn btn-outline-choco btn-block" id="btn-manage-product-recipe" data-product-id="{{ $data->id }}" data-product-name="{{ $data->name }}" data-partner-id={{ $data->partner_id }}>
                        {{ __('messages.owner.products.outlet_products.manage_product_recipe') }}
                      </button>
                    </div>

                    @if($pcsUnitId)
                      <input type="hidden" name="display_unit_id" value="{{ $pcsUnitId }}">
                    @else
                      <div class="alert alert-danger small py-2">
                        Error: Unit dasar 'pcs' tidak terdefinisi di Master Units.
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
                    </div>

                    {{-- Hot Product --}}
                    <div class="form-group">
                      <label class="mb-1">{{ __('messages.owner.products.outlet_products.hot_product') }}</label>
                      <div class="custom-control custom-switch">
                        <input
                          type="checkbox"
                          class="custom-control-input"
                          id="is_hot_product_switch"
                          {{ old('is_hot_product', $data->is_hot_product ?? 0) ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="is_hot_product_switch">
                          <span id="is_hot_product_label">{{ old('is_hot_product', $data->is_hot_product ?? 0) ? __('messages.owner.products.outlet_products.active') : __('messages.owner.products.outlet_products.inactive') }}</span>
                        </label>
                      </div>
                      <input type="hidden" name="is_hot_product" id="is_hot_product" value="{{ old('is_hot_product', $data->is_hot_product ?? 0) }}">
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
                    </div>

                    {{-- Submit --}}
                    <hr>
                    <div class="d-flex justify-content-end">
                      <a href="{{ route('owner.user-owner.outlet-products.index') }}" class="btn btn-light border mr-2">{{ __('messages.owner.products.outlet_products.cancel') }}</a>
                      <button type="submit" class="btn btn-primary">{{ __('messages.owner.products.outlet_products.save_changes') }}</button>
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

  {{-- Recipe Management Modal --}}
  <div class="modal fade" id="recipeModal" tabindex="-1" role="dialog" aria-labelledby="recipeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-choco text-white">
          <h5 class="modal-title" id="recipeModalLabel">
            <i class="fas fa-clipboard-list mr-2"></i>{{ __('messages.owner.products.outlet_products.manage_recipe') }}: <span id="modal-item-name"></span>
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>{{ __('messages.owner.products.outlet_products.how_it_works') }}:</strong> {{ __('messages.owner.products.outlet_products.add_raw_materials_info') }}
          </div>

          {{-- Recipe Items List --}}
          <div id="recipe-items-container">
            {{-- Will be populated via JavaScript --}}
          </div>

          <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="add-recipe-item">
            <i class="fas fa-plus mr-1"></i>{{ __('messages.owner.products.outlet_products.add_ingredient') }}
          </button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('messages.owner.products.outlet_products.cancel') }}</button>
          <button type="button" class="btn btn-primary" id="save-recipe">
            <i class="fas fa-save mr-1"></i>{{ __('messages.owner.products.outlet_products.save_recipe') }}
          </button>
        </div>
      </div>
    </div>
  </div>


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

  .btn-outline-choco{ color:var(--choco); border-color:var(--choco); }
  .btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }

  .bg-choco{ background: linear-gradient(135deg, var(--choco), var(--soft-choco)); }

  @media (max-width: 768px) {
  .owner-op-edit .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  .owner-op-edit .table {
    min-width: 600px; /* Force horizontal scroll */
    font-size: 12px;
  }
  
  .owner-op-edit .table th,
  .owner-op-edit .table td {
    padding: 8px 4px;
    font-size: 12px;
  }
  
  .owner-op-edit .form-control-sm {
    font-size: 12px;
    padding: 4px 6px;
  }
  
  .owner-op-edit .btn-sm {
    font-size: 11px;
    padding: 4px 8px;
  }
}
  </style>

@endsection
@push('scripts')
  {{-- 1. Definisikan object Translation Global DULUAN --}}
  <script>
    window.outletProductLang = {
      type_increase: "{{ __('messages.owner.products.outlet_products.type_increase') }}",
      type_decrease: "{{ __('messages.owner.products.outlet_products.type_decrease') }}",
      type_no_change: "{{ __('messages.owner.products.outlet_products.type_no_change') }}"
    };
  </script>

  {{-- 2. Panggil file JS Eksternal setelahnya --}}
  <script src="{{ asset('js/owner/outlet-product/edit.js') }}"></script>

  {{-- 3. Script tambahan (formatting harga) tetap di sini --}}
  <script>
  (function () {
    const priceInput = document.getElementById('outlet_price');
    if (!priceInput) return;

    priceInput.addEventListener('input', function () {
      let raw = this.value.replace(/[^0-9]/g, '');
      if (!raw) {
        this.value = '';
        return;
      }
      this.value = new Intl.NumberFormat('id-ID').format(parseInt(raw, 10));
    });
  })();
  </script>
@endpush
