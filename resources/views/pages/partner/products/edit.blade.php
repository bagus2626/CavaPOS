@extends('layouts.partner')

@section('title', __('messages.partner.product.all_product.update_product'))
@section('page_title', __('messages.partner.product.all_product.update_product'))

@section('content')
  <section class="content product-stock">
    <div class="container-fluid">

      <a href="{{ route('partner.products.index') }}" class="btn btn-outline-choco mb-3 btn-pill">
        <i class="fas fa-arrow-left mr-2"></i> {{ __('messages.partner.product.all_product.back_to_products') }}
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
          <h3 class="card-title mb-0">{{ __('messages.partner.product.all_product.edit_stock') }}</h3>
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
                          alt="{{ $data->name }}" class="thumb-120">
                      </div>

                      <div class="flex-fill">
                        <h4 class="mb-1 fw-600">{{ $data->name }}</h4>

                        <div class="badges-row mb-2">
                          <span class="badge badge-soft-info">
                            {{ optional($data->category)->category_name ?? 'Uncategorized' }}
                          </span>
                          <span class="badge badge-soft-neutral">{{ __('messages.partner.product.all_product.code') }}:
                            {{ $data->product_code ?? '-' }}</span>
                        </div>

                        <div class="meta small text-muted">
                          <div class="mb-1"><i class="fas fa-tags mr-1"></i>
                            {{ __('messages.partner.product.all_product.price') }}:
                            <strong>Rp {{ number_format((float) ($data->price ?? 0), 0, ',', '.') }}</strong>
                          </div>

                          <div class="mb-1 d-flex flex-wrap align-items-center">
                            <span class="mr-1"><i
                                class="fas fa-percentage mr-1"></i>{{ __('messages.partner.product.all_product.promotion') }}:</span>
                            <strong class="mr-2">{{ $data->promotion ? 'Applied' : '—' }}</strong>
                            @if($data->promotion)
                              <span class="badge badge-soft-neutral">
                                {{ $data->promotion->promotion_name ?? '-' }}
                                (@if($data->promotion->promotion_type === 'percentage')
                                  {{ __('messages.partner.product.all_product.discount') }}
                                  {{ intval($data->promotion->promotion_value ?? 0) }}%
                                @elseif($data->promotion->promotion_type === 'amount')
                                  {{ __('messages.partner.product.all_product.reduced_fare') }} Rp
                                  {{ number_format((float) ($data->promotion->promotion_value ?? 0), 0, ',', '.') }}
                                @endif)
                              </span>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>

                    @if(!empty($data->description))
                      <hr>
                      <div>
                        <div class="text-muted mb-1 fw-600"><i
                            class="far fa-file-alt mr-1"></i>{{ __('messages.partner.product.all_product.description') }}
                        </div>
                        <div class="desc-box">
                          {!! $data->description !!}
                        </div>
                      </div>
                    @endif

                    @if(($data->parent_options ?? null) && count($data->parent_options))
                      <hr>
                      <div class="text-muted mb-2 fw-600"><i
                          class="fas fa-list-ul mr-1"></i>{{ __('messages.partner.product.all_product.options') }}</div>

                      @foreach($data->parent_options as $parent)
                        <div class="mb-3 p-2 border rounded-3 option-group">
                          <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="fw-600">
                              {{ $parent->name }}
                              @if($parent->provision)
                                <span class="badge badge-soft-neutral ms-1">
                                  {{ $parent->provision }}
                                  {{ $parent->provision_value ? ' : ' . $parent->provision_value : '' }}
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
                                    <th style="">{{ __('messages.partner.product.all_product.options') }}</th>
                                    <th class="text-end" style="">
                                      {{ __('messages.partner.product.all_product.price') }}
                                    </th>
                                    <th class="text-center" style="">
                                      {{ __('messages.partner.product.all_product.stock_type') }}
                                    </th>
                                    <th class="text-center" style="">
                                      {{ __('messages.partner.product.all_product.quantity') }}
                                    </th>
                                    <th class="text-center" style="">
                                      {{ __('messages.partner.product.all_product.last') }}
                                    </th>
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
                                      <td class="fw-500">{{ $opt->name }}</td>

                                      <td class="text-end">
                                        Rp {{ number_format((float) ($opt->price ?? 0), 0, ',', '.') }}
                                      </td>

                                      <td class="text-center">
                                        <select name="options[{{ $opt->id }}][stock_type]"
                                          class="form-control form-control-sm opt-stock-type" data-opt-id="{{ $opt->id }}">
                                          <option value="direct" {{ $optStockType === 'direct' ? 'selected' : '' }}>Direct</option>
                                          <option value="linked" {{ $optStockType === 'linked' ? 'selected' : '' }}>Linked</option>
                                        </select>
                                      </td>

                                      <td>
                                        {{-- Always Available --}}
                                        <div class="opt-aa-container-{{ $opt->id }} mb-2" style="display:inline-block;">
                                          <div class="custom-control custom-switch custom-control-sm">
                                            <input type="hidden" name="options[{{ $opt->id }}][always_available]" value="0">
                                            <input type="checkbox" class="custom-control-input opt-aa" id="opt-aa-{{ $opt->id }}"
                                              name="options[{{ $opt->id }}][always_available]" value="1"
                                              data-opt-id="{{ $opt->id }}" {{ $optUnlimited ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="opt-aa-{{ $opt->id }}">
                                              <small>{{ __('messages.partner.product.all_product.always_available') }}</small>
                                            </label>
                                          </div>
                                        </div>

                                        {{-- Direct Stock (Adjustment Style) --}}
                                        <div id="opt-qty-wrap-{{ $opt->id }}" class="opt-qty-wrapper">
                                          {{-- New Stock Input --}}
                                          <input type="number" id="opt-new-qty-{{ $opt->id }}"
                                            name="options[{{ $opt->id }}][new_quantity]"
                                            class="form-control form-control-sm text-center mb-1 opt-new-qty" min="0" step="1"
                                            value="{{ old("options.{$opt->id}.new_quantity", $optCurrentQty) }}"
                                            placeholder="{{ __('messages.partner.product.all_product.enter_new_stock') }}"
                                            data-opt-id="{{ $opt->id }}">

                                          {{-- Hidden: Current Quantity --}}
                                          <input type="hidden" id="opt-current-qty-{{ $opt->id }}"
                                            name="options[{{ $opt->id }}][current_quantity]" value="{{ $optCurrentQty }}">

                                          {{-- Adjustment Info --}}
                                          <div id="opt-adj-info-{{ $opt->id }}" class="alert alert-secondary py-1 px-2 small"
                                            style="display: none; margin: 0;">
                                            <strong><span class="opt-adj-type">-</span>:</strong>
                                            <span class="opt-adj-amount">-</span>
                                          </div>
                                        </div>

                                        {{-- Linked Info --}}
                                        <div id="opt-linked-info-{{ $opt->id }}" class="opt-linked-info" style="display:none;">
                                          <div class="alert alert-info py-2 px-2 mb-1 small">
                                            <i
                                              class="fas fa-link mr-2"></i>{{ __('messages.partner.product.all_product.stock_controlled_by_recipe') }}
                                          </div>
                                          <button type="button" class="btn btn-sm btn-outline-choco btn-block btn-manage-recipe"
                                            data-opt-id="{{ $opt->id }}" data-opt-name="{{ $opt->name }}">
                                            {{ __('messages.partner.product.all_product.manage_recipe') }}
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

              {{-- RIGHT: Quantity produk --}}
              <div class="col-lg-5">
                <div class="card border-0 inner-card">
                  <div class="card-body">
                    <h5 class="mb-3 fw-600">{{ __('messages.partner.product.all_product.update_stock') }}</h5>

                    @php
                      $prodUnlimited = old('always_available', $data->always_available_flag ?? 0);
                      $prodStockType = old('stock_type', $data->stock_type ?? 'direct');
                      $currentQuantity = 0;
                      if ($data->stock_type === 'direct' && $data->stock) {
                        $currentQuantity = (int) $data->quantity_available;
                      }
                    @endphp

                    {{-- Stock Type Product --}}
                    <div class="form-group mb-3">
                      <label class="mb-2 fw-600">{{ __('messages.partner.product.all_product.stock_type') }}</label>
                      <select name="stock_type" id="product_stock_type" class="form-control">
                        <option value="direct" {{ $prodStockType === 'direct' ? 'selected' : '' }}>Direct</option>
                        <option value="linked" {{ $prodStockType === 'linked' ? 'selected' : '' }}>Linked</option>
                      </select>
                    </div>

                    {{-- Always Available --}}
                    <div class="form-group mb-3" id="product_aa_group">
                      <div class="custom-control custom-switch">
                        <input type="hidden" name="always_available" value="0">
                        <input type="checkbox" class="custom-control-input" id="aa_product" name="always_available"
                          value="1" {{ $prodUnlimited ? 'checked' : '' }}>
                        <label class="custom-control-label" for="aa_product">
                          {{ __('messages.partner.product.all_product.product_always_available') }}
                        </label>
                      </div>
                    </div>

                    {{-- ===== ADJUSTMENT STYLE QUANTITY INPUT ===== --}}
                    <div class="form-group mb-3" id="product_qty_group">
                      <label class="mb-1 fw-600">{{ __('messages.partner.product.all_product.quantity') }}</label>

                      {{-- New Quantity Input --}}
                      <input type="number" id="new_quantity" name="new_quantity" class="form-control text-center mb-2"
                        min="0" step="1" value="{{ old('new_quantity', $currentQuantity) }}"
                        placeholder="{{ __('messages.partner.product.all_product.enter_new_stock') }}">

                      {{-- Hidden: Current Stock --}}
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

                    {{-- Linked Info + Button --}}
                    <div class="form-group mb-3" id="product_linked_group" style="display:none;">
                      <label class="mb-2 fw-600">{{ __('messages.partner.product.all_product.quantity_product') }}</label>
                      <div class="alert alert-info py-2 mb-2">
                        <i class="fas fa-link mr-2"></i>
                        <span>{{ __('messages.partner.product.all_product.stock_controlled_by_recipe') }}</span>
                      </div>
                      <button type="button" class="btn btn-outline-choco btn-block" id="btn-manage-product-recipe"
                        data-product-id="{{ $data->id }}" data-product-name="{{ $data->name }}">
                        {{ __('messages.partner.product.all_product.manage_product_recipe') }}
                      </button>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end">
                      <a href="{{ route('partner.products.index') }}"
                        class="btn btn-outline-choco me-2">{{ __('messages.partner.product.all_product.cancel') }}</a>
                      <button type="submit"
                        class="btn btn-choco">{{ __('messages.partner.product.all_product.save_changes') }}</button>
                    </div>
                  </div>
                </div>

                <div class="card border-0 inner-card mt-3">
                  <div class="card-body small text-muted">
                    <div class="d-flex justify-content-between">
                      <span>{{ __('messages.partner.product.all_product.created') }}</span><strong>{{ optional($data->created_at)->format('d M Y, H:i') ?? '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                      <span>{{ __('messages.partner.product.all_product.last_updated') }}</span><strong>{{ optional($data->updated_at)->format('d M Y, H:i') ?? '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                      <span>{{ __('messages.partner.product.all_product.owner') }}</span><strong>{{ $data->owner->name ?? '-' }}</strong>
                    </div>
                  </div>
                </div>
              </div>

            </div>{{-- /row --}}
          </div>{{-- /card-body --}}
        </form>
      </div>
    </div>
  </section>

  {{-- Recipe Management Modal --}}
  <div class="modal fade" id="recipeModal" tabindex="-1" role="dialog" aria-labelledby="recipeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-choco text-white">
          <h5 class="modal-title" id="recipeModalLabel">
            <i class="fas fa-clipboard-list mr-2"></i>{{ __('messages.partner.product.all_product.manage_recipe') }}:
            <span id="modal-item-name"></span>
          </h5>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>{{ __('messages.partner.product.all_product.how_it_works') }}:</strong>
            {{ __('messages.partner.product.all_product.add_raw_materials_info') }}
          </div>

          {{-- Recipe Items List --}}
          <div id="recipe-items-container">
            {{-- Will be populated via JavaScript --}}
          </div>

          <button type="button" class="btn btn-outline-choco btn-sm mt-3" id="add-recipe-item">
            <i class="fas fa-plus mr-1"></i>{{ __('messages.partner.product.all_product.add_ingredient') }}
          </button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary"
            data-dismiss="modal">{{ __('messages.partner.product.all_product.cancel') }}</button>
          <button type="button" class="btn btn-choco" id="save-recipe">
            <i class="fas fa-save mr-1"></i>{{ __('messages.partner.product.all_product.save_recipe') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <style>
    /* ==== Update Product Stock (page scope) ==== */
    :root {
      --choco: #8c1000;
      --soft-choco: #c12814;
      --ink: #22272b;
      --paper: #f7f7f8;
      --radius: 12px;
      --shadow: 0 6px 20px rgba(0, 0, 0, .08);
    }

    .product-stock .card-shell {
      border-radius: var(--radius);
      box-shadow: var(--shadow);
    }

    .product-stock .brand-header {
      background: linear-gradient(135deg, var(--choco), var(--soft-choco));
      color: #fff;
      border-bottom: 0;
      border-radius: var(--radius) var(--radius) 0 0;
    }

    .product-stock .inner-card {
      border-radius: var(--radius);
      box-shadow: var(--shadow);
    }

    .product-stock .fw-600 {
      font-weight: 600;
    }

    .product-stock .fw-500 {
      font-weight: 500;
    }

    .btn-pill {
      border-radius: 999px;
    }

    .btn-choco {
      background: var(--choco);
      border-color: var(--choco);
      color: #fff;
    }

    .btn-choco:hover {
      background: var(--soft-choco);
      border-color: var(--soft-choco);
    }

    .btn-outline-choco {
      color: var(--choco);
      border-color: var(--choco);
    }

    .btn-outline-choco:hover {
      color: #fff;
      background: var(--choco);
      border-color: var(--choco);
    }

    .brand-alert {
      border-left: 4px solid var(--choco);
      border-radius: 10px;
    }

    .thumb-120 {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 12px;
      border: 0;
      box-shadow: var(--shadow);
      margin-right: 1rem;
      /* Tambahkan ini */
    }

    /* Badges soft */
    .badges-row .badge {
      margin-right: .35rem;
    }

    .badge-soft-info {
      background: #eef2ff;
      color: #3730a3;
      border: 1px solid #c7d2fe;
      border-radius: 999px;
      font-weight: 600;
    }

    .badge-soft-neutral {
      background: #f3f4f6;
      color: #374151;
      border: 1px solid #e5e7eb;
      border-radius: 999px;
      font-weight: 600;
    }

    .badge-soft-success {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
      border-radius: 999px;
      font-weight: 600;
    }

    /* Description box */
    .desc-box {
      background: #fcfcfc;
      border: 1px solid #eef1f4;
      border-radius: 10px;
      padding: .75rem;
    }

    /* Table options */
    .options-table thead th {
      background: #fff;
      border-bottom: 2px solid #eef1f4 !important;
      color: #374151;
      font-weight: 700;
    }

    .options-table tbody tr {
      transition: background-color .12s ease;
    }

    .options-table tbody tr:hover {
      background: rgba(140, 16, 0, .04);
    }

    /* Quantity group */
    .qty-group {
      max-width: 360px;
      gap: .5rem;
    }

    .qty-input {
      max-width: 140px;
      border-color: #e5e7eb;
    }

    .qty-input:focus {
      border-color: var(--choco);
      box-shadow: 0 0 0 .2rem rgba(140, 16, 0, .15);
    }

    .btn-qty {
      min-width: 40px;
    }

    /* Switch color (Bootstrap 4 custom-switch) */
    .custom-control-input:checked~.custom-control-label::before {
      background-color: var(--choco);
      border-color: var(--choco);
    }

    .custom-control-input:focus~.custom-control-label::before {
      box-shadow: 0 0 0 .2rem rgba(140, 16, 0, .18);
      border-color: var(--soft-choco);
    }

    /* Small polish */
    .option-group {
      border-color: #eef1f4 !important;
      background: #fff;
    }

    .meta i {
      color: #9ca3af;
    }

    /* Modal */
    .bg-choco {
      background: linear-gradient(135deg, var(--choco), var(--soft-choco));
    }

    .recipe-item {
      background: #fafbfc;
      transition: all .2s ease;
      border: 1px solid #e5e7eb;
    }

    .recipe-item:hover {
      background: #f3f4f6;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
    }

    /* Spacing untuk button group di mobile */
    .product-stock .d-flex.justify-content-end {
      gap: 0.5rem;
    }

    /* Atau jika menggunakan margin */
    .product-stock .d-flex.justify-content-end .btn {
      margin-left: 0.5rem;
    }

    .product-stock .d-flex.justify-content-end .btn:first-child {
      margin-left: 0;
    }

    /* Responsive spacing */
    @media (max-width: 576px) {
      .product-stock .d-flex.justify-content-end {
        gap: 0.75rem;
      }
    }

    @media (max-width: 768px) {
      .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      .table-responsive table {
        min-width: 600px;
        font-size: 12px;
      }

      .table-responsive th,
      .table-responsive td {
        padding: 8px 4px;
        font-size: 12px;
      }

      .form-control-sm {
        font-size: 12px;
        padding: 4px 6px;
      }

      .btn-sm {
        font-size: 11px;
        padding: 4px 8px;
      }
    }
  </style>
@endsection

@push('scripts')
  <script>
    window.outletProductLang = {
      type_increase: "{{ __('messages.owner.products.outlet_products.type_increase') }}",
      type_decrease: "{{ __('messages.owner.products.outlet_products.type_decrease') }}",
      type_no_change: "{{ __('messages.owner.products.outlet_products.type_no_change') }}"
    };
  </script>
  <script src="{{ asset('js/partner/product/edit.js') }}"></script>
@endpush