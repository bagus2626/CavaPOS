@extends('layouts.owner')

@section('title', __('messages.owner.products.outlet_products.update_outlet_product'))
@section('page_title', __('messages.owner.products.outlet_products.update_outlet_product'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.owner.products.outlet_products.edit_outlet_product') }}</h1>
          <p class="page-subtitle">Update product information, stock, and settings.</p>
        </div>
      </div>

      <!-- Error Messages -->
      @if ($errors->any())
        <div class="alert alert-danger alert-modern">
          <div class="alert-icon">
            <span class="material-symbols-outlined">error</span>
          </div>
          <div class="alert-content">
            <strong>{{ __('messages.owner.products.outlet_products.alert_error') }}:</strong>
            <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      @if (session('success'))
        <div class="alert alert-success alert-modern">
          <div class="alert-icon">
            <span class="material-symbols-outlined">check_circle</span>
          </div>
          <div class="alert-content">
            {{ session('success') }}
          </div>
        </div>
      @endif

      <!-- Main Card -->
      <div class="modern-card">
        <form action="{{ route('owner.user-owner.outlet-products.update', $data->id) }}" method="POST" id="productForm">
          @csrf
          @method('PUT')

          <div class="card-body-modern">
            <!-- Product Overview Section -->
            <div class="profile-section">
              <!-- Product Image -->
              <div class="profile-picture-wrapper">
                <div class="profile-picture-container">
                  @php
                    $hasImage = !empty($data->pictures) && is_array($data->pictures) && count($data->pictures) > 0;
                    $imagePath = $hasImage ? asset($data->pictures[0]['path']) : '';
                  @endphp

                  <!-- Placeholder -->
                  <div class="upload-placeholder" id="uploadPlaceholder" style="{{ $hasImage ? 'display:none;' : '' }}">
                    <span class="material-symbols-outlined">image</span>
                    <span class="upload-text">No Image</span>
                  </div>

                  <!-- Image Preview -->
                  <img id="imagePreview" class="profile-preview {{ $hasImage ? 'active' : '' }}" src="{{ $imagePath }}"
                    alt="{{ $data->name }}">
                </div>
              </div>


              <!-- Product Info -->
              <div class="personal-info-fields">
                <div class="section-header">
                  <div class="section-icon section-icon-red">
                    <span class="material-symbols-outlined">inventory_2</span>
                  </div>
                  <h3 class="section-title">Product Information</h3>
                </div>

                <div class="row g-4">
                  <div class="col-md-6">
                    <div class="form-group-modern">
                      <label class="form-label-modern">
                        Product Name
                      </label>
                      <input type="text" class="form-control-modern" value="{{ $data->name }}" readonly>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group-modern">
                      <label class="form-label-modern">
                        {{ __('messages.owner.products.outlet_products.category') }}
                      </label>
                      <input type="text" class="form-control-modern"
                        value="{{ optional($data->category)->category_name ?? 'Uncategorized' }}" readonly>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group-modern">
                      <label class="form-label-modern">
                        {{ __('messages.owner.products.outlet_products.code') }}
                      </label>
                      <input type="text" class="form-control-modern" value="{{ $data->product_code ?? '-' }}" readonly>
                    </div>
                  </div>

                  @if(!empty($data->description))
                    <div class="col-md-12">
                      <div class="form-group-modern">
                        <label class="form-label-modern">
                          Description
                        </label>
                        <div class="alert alert-secondary py-2 px-3 mb-0">
                          <div style="font-size: 0.9rem;">{!! $data->description !!}</div>
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <!-- Divider -->
            <div class="section-divider"></div>

            <!-- Editable Fields Section -->
            <div class="account-section">
              <div class="section-header">
                <div class="section-icon section-icon-red">
                  <span class="material-symbols-outlined">edit</span>
                </div>
                <h3 class="section-title">{{ __('messages.owner.products.outlet_products.update_stock_status') }}</h3>
              </div>

              @php
                $prodUnlimited = old('always_available', $data->always_available_flag ?? 0);
                $prodStockType = old('stock_type', $data->stock_type ?? 'direct');
                $currentQuantity = 0;
                if ($data->stock_type === 'direct' && $data->stock) {
                  $currentQuantity = (int) $data->quantity_available;
                }
              @endphp

              <div class="row g-4">
                <!-- Price -->
                <div class="col-md-6">
                  <div class="form-group-modern">
                    <label class="form-label-modern">
                      {{ __('messages.owner.products.outlet_products.price') }}
                      <span class="text-danger">*</span>
                    </label>
                    <div class="input-wrapper">
                      <span class="input-icon">Rp</span>
                      <input type="text" name="price" id="outlet_price"
                        class="form-control-modern with-icon @error('price') is-invalid @enderror"
                        value="{{ old('price', number_format((float) ($data->price ?? 0), 0, ',', '.')) }}"
                        autocomplete="off" required>
                    </div>
                    @error('price')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Stock Type -->
                <div class="col-md-6">
                  <div class="form-group-modern">
                    <label class="form-label-modern">
                      {{ __('messages.owner.products.outlet_products.stock_type') }}
                      <span class="text-danger">*</span>
                    </label>
                    <div class="select-wrapper">
                      <select name="stock_type" id="product_stock_type" class="form-control-modern">
                        <option value="direct" {{ $prodStockType === 'direct' ? 'selected' : '' }}>
                          Direct Stock Input
                        </option>
                        <option value="linked" {{ $prodStockType === 'linked' ? 'selected' : '' }}>
                          Linked to Raw Materials
                        </option>
                      </select>
                      <span class="material-symbols-outlined select-arrow">expand_more</span>
                    </div>
                  </div>
                </div>

                <!-- Always Available Toggle -->
                <div class="col-md-6" id="product_aa_group">
                  <div class="form-group-modern">
                    <label class="form-label-modern d-block">
                      {{ __('messages.owner.products.outlet_products.always_available_product') }}
                    </label>
                    <input type="hidden" name="always_available" value="0">
                    <div class="status-switch">
                      <label class="switch-modern">
                        <input type="checkbox" id="aa_product" name="always_available" value="1" {{ $prodUnlimited ? 'checked' : '' }}>
                        <span class="slider-modern"></span>
                      </label>
                      <span class="status-label" id="aaLabel">
                        {{ $prodUnlimited ? 'Enabled' : 'Disabled' }}
                      </span>
                    </div>
                    <small class="text-muted d-block mt-1">
                      {{ __('messages.owner.products.outlet_products.if_active_stock_hidden') }}
                    </small>
                  </div>
                </div>

                <!-- Quantity Input -->
                <div class="col-md-6" id="product_qty_group">
                  <div class="form-group-modern">
                    <label class="form-label-modern">
                      {{ __('messages.owner.products.outlet_products.quantity') }}
                      <span class="text-danger">*</span>
                    </label>
                    <input type="number" id="new_quantity" name="new_quantity"
                      class="form-control-modern text-center @error('new_quantity') is-invalid @enderror" min="0" step="1"
                      value="{{ old('new_quantity', $currentQuantity) }}"
                      placeholder="{{ __('messages.owner.products.outlet_products.enter_new_stock') }}">

                    <input type="hidden" id="current_quantity" name="current_quantity" value="{{ $currentQuantity }}">

                    <div id="adjustment_info" class="alert alert-info py-2 px-3 mt-2 small" style="display: none;">
                      <strong><span id="adjustment_type">-</span>:</strong>
                      <span id="adjustment_amount">-</span>
                    </div>

                    @error('new_quantity')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Linked Info -->
                <div class="col-md-12" id="product_linked_group" style="display:none;">
                  <div class="form-group-modern">
                    <button type="button" class="btn-modern btn-sm-modern btn-primary-modern"
                      id="btn-manage-product-recipe" data-product-id="{{ $data->id }}"
                      data-product-name="{{ $data->name }}" data-partner-id="{{ $data->partner_id }}">
                      {{ __('messages.owner.products.outlet_products.manage_product_recipe') }}
                    </button>
                  </div>

                </div>

                @if($pcsUnitId)
                  <input type="hidden" name="display_unit_id" value="{{ $pcsUnitId }}">
                @else
                  <div class="col-md-12">
                    <div class="alert alert-danger alert-modern">
                      <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                      </div>
                      <div class="alert-content">
                        Error: Unit dasar 'pcs' tidak terdefinisi di Master Units.
                      </div>
                    </div>
                  </div>
                @endif

                <!-- Status Active/Inactive -->
                <div class="col-md-6">
                  <div class="form-group-modern">
                    <label class="form-label-modern d-block">
                      {{ __('messages.owner.products.outlet_products.status') }}
                    </label>
                    <input type="hidden" name="is_active" id="is_active"
                      value="{{ old('is_active', $data->is_active ?? 1) }}">
                    <div class="status-switch">
                      <label class="switch-modern">
                        <input type="checkbox" id="is_active_switch" {{ old('is_active', $data->is_active ?? 1) ? 'checked' : '' }}>
                        <span class="slider-modern"></span>
                      </label>
                      <span class="status-label" id="is_active_label">
                        {{ old('is_active', $data->is_active ?? 1) ? __('messages.owner.products.outlet_products.active') : __('messages.owner.products.outlet_products.inactive') }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Hot Product Toggle -->
                <div class="col-md-6">
                  <div class="form-group-modern">
                    <label class="form-label-modern d-block">
                      {{ __('messages.owner.products.outlet_products.hot_product') }}
                    </label>
                    <input type="hidden" name="is_hot_product" id="is_hot_product"
                      value="{{ old('is_hot_product', $data->is_hot_product ?? 0) }}">
                    <div class="status-switch">
                      <label class="switch-modern">
                        <input type="checkbox" id="is_hot_product_switch" {{ old('is_hot_product', $data->is_hot_product ?? 0) ? 'checked' : '' }}>
                        <span class="slider-modern"></span>
                      </label>
                      <span class="status-label" id="is_hot_product_label">
                        {{ old('is_hot_product', $data->is_hot_product ?? 0) ? __('messages.owner.products.outlet_products.active') : __('messages.owner.products.outlet_products.inactive') }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Promotion -->
                <div class="col-md-12">
                  <div class="form-group-modern">
                    <label class="form-label-modern">
                      {{ __('messages.owner.products.outlet_products.promotion') }}
                    </label>
                    <div class="select-wrapper">
                      @php
                        $selectedPromoId = old('promotion_id', $data->promo_id);
                      @endphp
                      <select id="promotion_id" name="promotion_id" class="form-control-modern">
                        <option value="">{{ __('messages.owner.products.outlet_products.no_promotion_dropdown') }}
                        </option>
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
                      <span class="material-symbols-outlined select-arrow">expand_more</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Product Options Section -->
            @if(($data->parent_options ?? null) && count($data->parent_options))
              <div class="section-divider"></div>
              <div class="account-section">
                <div class="section-header">
                  <div class="section-icon section-icon-red">
                    <span class="material-symbols-outlined">tune</span>
                  </div>
                  <h3 class="section-title">{{ __('messages.owner.products.outlet_products.options') }}</h3>
                </div>

                @foreach($data->parent_options as $parent)
                  <div class="modern-card mb-3" style="border: 1px solid #e5e7eb;">
                    <div class="card-body-modern" style="padding: 1.5rem;">
                      <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                          <h5 class="mb-1">{{ $parent->name }}</h5>
                          @if($parent->provision)
                            <span class="body-sm text-secondary">
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
                          <small class="text-muted">{{ $parent->description }}</small>
                        @endif
                      </div>

                      @if($parent->options && count($parent->options))
                        <div class="data-table-wrapper">
                          <table class="data-table">
                            <thead>
                              <tr>
                                <th>{{ __('messages.owner.products.outlet_products.option') }}</th>
                                <th>{{ __('messages.owner.products.outlet_products.price') }}</th>
                                <th class="text-center">{{ __('messages.owner.products.outlet_products.stock_type') }}</th>
                                <th class="text-center">{{ __('messages.owner.products.outlet_products.quantity') }}</th>
                                <th class="text-center">{{ __('messages.owner.products.outlet_products.last') }}</th>
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
                                  <td class="fw-600">{{ $opt->name }}</td>
                                  <td>Rp {{ number_format((float) ($opt->price ?? 0), 0, ',', '.') }}</td>
                                  <td class="text-center">
                                    <div class="select-wrapper" style="max-width: 150px; margin: 0 auto;">
                                      <select name="options[{{ $opt->id }}][stock_type]"
                                        class="form-control-modern opt-stock-type" data-opt-id="{{ $opt->id }}">
                                        <option value="direct" {{ $optStockType === 'direct' ? 'selected' : '' }}>Direct</option>
                                        <option value="linked" {{ $optStockType === 'linked' ? 'selected' : '' }}>Linked</option>
                                      </select>
                                      <span class="material-symbols-outlined select-arrow">expand_more</span>
                                    </div>
                                  </td>
                                  <td class="text-center">
                                    <div class="opt-aa-container-{{ $opt->id }} mb-2">
                                      <div class="status-switch" style="justify-content: center;">
                                        <input type="hidden" name="options[{{ $opt->id }}][always_available]" value="0">
                                        <label class="switch-modern" style="transform: scale(0.8);">
                                          <input type="checkbox" id="opt-aa-{{ $opt->id }}" class="opt-aa"
                                            name="options[{{ $opt->id }}][always_available]" value="1"
                                            data-opt-id="{{ $opt->id }}" {{ $optUnlimited ? 'checked' : '' }}>
                                          <span class="slider-modern"></span>
                                        </label>
                                        <span class="status-label small">Always Available</span>
                                      </div>
                                    </div>

                                    <div id="opt-qty-wrap-{{ $opt->id }}" class="opt-qty-wrapper">
                                      <input type="number" id="opt-new-qty-{{ $opt->id }}"
                                        name="options[{{ $opt->id }}][new_quantity]"
                                        class="form-control-modern text-center opt-new-qty" min="0" step="1"
                                        value="{{ old("options.{$opt->id}.new_quantity", $optCurrentQty) }}"
                                        data-opt-id="{{ $opt->id }}" style="max-width: 120px; margin: 0 auto;">

                                      <input type="hidden" id="opt-current-qty-{{ $opt->id }}"
                                        name="options[{{ $opt->id }}][current_quantity]" value="{{ $optCurrentQty }}">

                                      <div id="opt-adj-info-{{ $opt->id }}" class="alert alert-info py-1 px-2 small mt-2"
                                        style="display: none;">
                                        <strong><span class="opt-adj-type">-</span>:</strong>
                                        <span class="opt-adj-amount">-</span>
                                      </div>
                                    </div>

                                    <div id="opt-linked-info-{{ $opt->id }}" class="opt-linked-info" style="display:none;">
                                      <button type="button" class="btn-modern btn-primary-modern btn-sm-modern btn-manage-recipe"
                                        data-opt-id="{{ $opt->id }}" data-opt-name="{{ $opt->name }}"
                                        data-partner-id="{{ $data->partner_id }}">
                                        Recipe
                                      </button>
                                    </div>
                                  </td>
                                  <td class="text-center">
                                    <span class="text-muted">{{ $optCurrentQty }}</span>
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>

          <!-- Card Footer -->
          <div class="card-footer-modern">
            <a href="{{ route('owner.user-owner.outlet-products.index') }}" class="btn-cancel-modern">
              {{ __('messages.owner.products.outlet_products.cancel') }}
            </a>
            <button type="submit" class="btn-submit-modern">
              {{ __('messages.owner.products.outlet_products.update') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>


  {{-- Recipe Management Modal --}}
  <div class="modal fade" id="recipeModal" tabindex="-1" role="dialog" aria-labelledby="recipeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-choco text-white">
          <h5 class="modal-title" id="recipeModalLabel">
            <i class="fas fa-clipboard-list mr-2"></i>{{ __('messages.owner.products.outlet_products.manage_recipe') }}:
            <span id="modal-item-name"></span>
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>{{ __('messages.owner.products.outlet_products.how_it_works') }}:</strong>
            {{ __('messages.owner.products.outlet_products.add_raw_materials_info') }}
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
          <button type="button" class="btn btn-outline-secondary"
            data-dismiss="modal">{{ __('messages.owner.products.outlet_products.cancel') }}</button>
          <button type="button" class="btn btn-primary" id="save-recipe">
            <i class="fas fa-save mr-1"></i>{{ __('messages.owner.products.outlet_products.save_recipe') }}
          </button>
        </div>
      </div>
    </div>
  </div>




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
