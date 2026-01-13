@extends('layouts.partner')

@section('title', __('messages.partner.product.all_product.product_detail'))
@section('page_title', __('messages.partner.product.all_product.product_detail'))

@section('content')
  @php
    use Illuminate\Support\Str;

    // fleksibel: dukung $product atau $data
    $prod = $product ?? $data ?? null;

    // kategori (opsional)
    $catName = optional($prod->category)->category_name ?? __('messages.partner.product.all_product.uncategorized');

    // gambar utama (ambil dari pictures[0] jika ada)
    $firstImg = null;
    if (!empty($prod->pictures) && is_array($prod->pictures)) {
      $first = $prod->pictures[0] ?? null;
      if ($first && !empty($first['path'])) {
        $firstImg = Str::startsWith($first['path'], ['http://', 'https://'])
          ? $first['path']
          : asset($first['path']);
      }
    } elseif (!empty($prod->image)) {
      $firstImg = Str::startsWith($prod->image, ['http://', 'https://'])
        ? $prod->image
        : asset('storage/' . $prod->image);
    }

    // promo (dukung relasi / field berbeda)
    $promo = $prod->promotion ?? $prod->promo ?? null;
    $promoType = $promo->promotion_type ?? null;
    $promoValue = $promo->promotion_value ?? null;
    $promoName = $promo->promotion_name ?? null;

    // harga final setelah promo
    $basePrice = (float) ($prod->price ?? 0);
    $finalPrice = $basePrice;
    if ($promoType && $promoValue !== null) {
      if ($promoType === 'percentage') {
        $finalPrice = max(0, $basePrice - ($basePrice * ((float) $promoValue / 100)));
      } else {
        $finalPrice = max(0, $basePrice - (float) $promoValue);
      }
    }
  @endphp

  <div class="modern-container">
    <div class="container-modern">
      
      {{-- Page Header --}}
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.partner.product.all_product.product_detail') }}</h1>
          <p class="page-subtitle">{{ __('messages.partner.product.all_product.view_information') }}</p>
        </div>
      </div>

      {{-- Success Message --}}
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

      {{-- Error Message --}}
      @if (session('error'))
        <div class="alert alert-danger alert-modern">
          <div class="alert-icon">
            <span class="material-symbols-outlined">error</span>
          </div>
          <div class="alert-content">
            {{ session('error') }}
          </div>
        </div>
      @endif

      {{-- Hero Card --}}
      <div class="modern-card">
        <div class="detail-hero-header">
          {{-- Product Image --}}
          <div class="detail-avatar">
            @if($firstImg)
              <img src="{{ $firstImg }}" alt="{{ $prod->name }}" class="detail-avatar-image">
            @else
              <div class="detail-avatar-placeholder">
                <span class="material-symbols-outlined">inventory_2</span>
              </div>
            @endif
          </div>

          {{-- Hero Info --}}
          <div class="detail-hero-info">
            <h3 class="detail-hero-name">{{ $prod->name }}</h3>
            <p class="detail-hero-subtitle">
              {{ $prod->product_code }}
            </p>
            <div class="detail-hero-badges">
              <span class="badge-modern badge-info">
                {{ $catName }}
              </span>
              @if($prod->stock_type === 'linked')
                <span class="badge-modern badge-primary">
                  <span class="material-symbols-outlined">link</span>
                  Linked Stock
                </span>
              @endif
              @if($promo)
                <span class="badge-modern badge-warning">
                  <span class="material-symbols-outlined">local_offer</span>
                  {{ $promoName ?? 'Promo' }}
                </span>
              @endif
            </div>
          </div>
        </div>

        {{-- Gallery Thumbnails --}}
        @if(!empty($prod->pictures) && is_array($prod->pictures) && count($prod->pictures) > 1)
          <div class="detail-gallery">
            @foreach($prod->pictures as $p)
              @php
                $src = !empty($p['path'])
                  ? (Str::startsWith($p['path'], ['http://', 'https://']) ? $p['path'] : asset($p['path']))
                  : null;
              @endphp
              @if($src)
                <a href="{{ $src }}" target="_blank" rel="noopener" class="gallery-item">
                  <img src="{{ $src }}" alt="{{ $p['filename'] ?? 'Product Image' }}">
                </a>
              @endif
            @endforeach
          </div>
        @endif
      </div>

      {{-- Body Card --}}
      <div class="modern-card">
        <div class="card-body-modern">
          
          {{-- Product Information Section --}}
          <div class="section-header">
            <div class="section-icon section-icon-red">
              <span class="material-symbols-outlined">shopping_bag</span>
            </div>
            <h3 class="section-title">Product Information</h3>
          </div>
          
          <div class="detail-info-grid">
            <div class="detail-info-group">
              {{-- Product Name --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.product.all_product.product_name') }}
                </div>
                <div class="detail-info-value">{{ $prod->name ?? '—' }}</div>
              </div>

              {{-- Product Code --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.product.all_product.product_code') ?? 'Product Code' }}
                </div>
                <div class="detail-info-value">{{ $prod->product_code ?? '—' }}</div>
              </div>

              {{-- Quantity --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.product.all_product.quantity') }}
                </div>
                <div class="detail-info-value">
                  @if((int) $prod->always_available_flag === 1)
                    {{ __('messages.partner.product.all_product.always_available') }}
                  @else
                    @php
                      $stockUnit = $prod->stock_type === 'direct' 
                        ? optional(optional($prod->stock)->displayUnit)->unit_name 
                        : 'pcs';
                    @endphp
                    {{ number_format($prod->quantity_available ?? 0, 0) }} {{ $stockUnit }}
                  @endif
                </div>
              </div>
            </div>

            <div class="detail-info-group">
              {{-- Category --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.product.all_product.category') ?? 'Category' }}
                </div>
                <div class="detail-info-value">{{ $catName }}</div>
              </div>

              {{-- Stock Type --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.product.all_product.stock_type') ?? 'Stock Type' }}
                </div>
                <div class="detail-info-value">
                    {{ strtoupper($prod->stock_type) }}
                </div>
              </div>

              {{-- Base Price --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.product.all_product.base_price') ?? 'Base Price' }}
                </div>
                <div class="detail-info-value">Rp {{ number_format($basePrice, 0, ',', '.') }}</div>
              </div>
            </div>
          </div>

          {{-- Final Price Row --}}
          <div class="detail-info-grid" style="margin-top: 1rem;">
            <div class="detail-info-group">
              {{-- Final Price --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.product.all_product.final_price') ?? 'Final Price' }}
                </div>
                <div class="detail-info-value">
                  <span class="text-success">Rp {{ number_format($finalPrice, 0, ',', '.') }}</span>
                </div>
              </div>
            </div>

            <div class="detail-info-group">
              {{-- Empty space for alignment --}}
            </div>
          </div>

          {{-- Description Section --}}
          @if(!empty($prod->description))
            <div class="section-divider"></div>
            
            <div class="section-header">
              <div class="section-icon section-icon-red">
                <span class="material-symbols-outlined">description</span>
              </div>
              <h3 class="section-title">{{ __('messages.partner.product.all_product.description') }}</h3>
            </div>
            
            <div class="detail-info-item">
              <div class="detail-info-value">
                {!! $prod->description !!}
              </div>
            </div>
          @endif

          {{-- Recipe Section (jika linked stock) --}}
          @if($prod->stock_type === 'linked' && $prod->recipes->isNotEmpty())
            <div class="section-divider"></div>
            
            <div class="section-header">
              <div class="section-icon section-icon-red">
                <span class="material-symbols-outlined">restaurant_menu</span>
              </div>
              <h3 class="section-title">{{ __('messages.partner.product.all_product.recipe') ?? 'Recipe' }}</h3>
            </div>

            <div class="data-table-wrapper">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>{{ __('messages.partner.product.all_product.ingredient') ?? 'Ingredient' }}</th>
                    <th class="text-end">{{ __('messages.partner.product.all_product.quantity_needed') ?? 'Quantity Needed' }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($prod->recipes as $recipe)
                    <tr class="table-row">
                      <td>{{ $recipe->stock->stock_name ?? '—' }}</td>
                      <td class="text-end">
                        @php
                          $chosenUnit = $recipe->displayUnit;
                          $conversionValue = $chosenUnit->base_unit_conversion_value;
                          $displayQty = $recipe->quantity_used / $conversionValue;
                          $unitName = $chosenUnit->unit_name;
                        @endphp
                        {{ number_format($displayQty, 2, ',', '.') }} {{ $unitName }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

      {{-- OPTIONS SECTION --}}
      @php
        $parents = $prod->parent_options ?? collect();
      @endphp

      @if($parents && count($parents))
        @foreach($parents as $parent)
          <div class="modern-card">
            <div class="card-body-modern">
              
              <div class="section-header">
                <div class="section-icon section-icon-red">
                  <span class="material-symbols-outlined">tune</span>
                </div>
                <h3 class="section-title">
                  {{ $parent->name }}
                  <span class="text-secondary body-sm fw-normal">
                    ({{ $parent->provision ?? 'OPTIONAL' }}
                    @if(!empty($parent->provision_value) && $parent->provision !== 'OPTIONAL')
                      : {{ $parent->provision_value }}
                    @endif)
                  </span>
                </h3>
              </div>

              @if(!empty($parent->description))
                <p>{{ $parent->description }}</p>
              @endif

              @if(!empty($parent->options) && count($parent->options))
                <div class="data-table-wrapper">
                  <table class="data-table">
                    <thead>
                      <tr>
                        <th>option</th>
                        <th class="text-center">{{ __('messages.partner.product.all_product.stock_type') ?? 'Stock Type' }}</th>
                        <th class="text-end">price</th>
                        {{-- <th class="text-end">{{ __('messages.partner.product.all_product.additional_price') ?? 'Additional Price' }}</th> --}}
                        <th class="text-center">{{ __('messages.partner.product.all_product.quantity') }}</th>
                        <th>{{ __('messages.partner.product.all_product.description') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($parent->options as $opt)
                        <tr class="table-row">
                          <td>
                            <span class="fw-600">{{ $opt->name }}</span>
                          </td>
                          <td class="text-center">
                            <span class="badge-modern {{ $opt->stock_type === 'linked' ? 'badge-primary' : 'badge-secondary' }}">
                              {{ Str::title($opt->stock_type) }}
                            </span>
                          </td>
                          <td class="text-end">
                            @if($opt->price > 0)
                              <span class="text-success fw-600">
                                +Rp {{ number_format((float) $opt->price, 0, ',', '.') }}
                              </span>
                            @else
                              <span class="text-muted">—</span>
                            @endif
                          </td>
                          <td class="text-center">
                            @if((int) $opt->always_available_flag === 1)
                              <span class="badge-modern badge-success">UNLIMITED</span>
                            @else
                              @php
                                $optStockQty = $opt->available_linked_quantity ?? optional($opt->stock)->quantity_available ?? 0;
                                $optStockUnit = $opt->stock_type === 'direct' 
                                  ? optional(optional($opt->stock)->displayUnit)->unit_name 
                                  : 'pcs';
                              @endphp
                              <span class="{{ $optStockQty <= 0 ? 'text-danger' : '' }}">
                                {{ number_format($optStockQty, 0) }} {{ $optStockUnit }}
                              </span>
                            @endif
                          </td>
                          <td>
                            @if($opt->description)
                              <span>{{ $opt->description }}</span>
                            @else
                              <span>—</span>
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <p>{{ __('messages.partner.product.all_product.no_option') }}</p>
              @endif

            </div>
          </div>
        @endforeach
      @endif

    </div>
  </div>
@endsection