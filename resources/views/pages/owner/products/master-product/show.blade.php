@extends('layouts.owner')

@section('title', __('messages.owner.products.master_products.product_detail'))
@section('page_title', __('messages.owner.products.master_products.master_product_detail'))

@section('content')
  @php
    use Illuminate\Support\Str;

    // fleksibel: dukung $product atau $data
    $prod = $product ?? $data ?? null;

    // kategori (opsional)
    $catName = optional($prod->category)->category_name ?? __('messages.owner.products.master_products.uncategorized');

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
          <h1 class="page-title">{{ __('messages.owner.products.master_products.master_product_detail') }}</h1>
          <p class="page-subtitle">{{ __('messages.owner.products.master_products.view_complete_info') }}</p>
        </div>
          <a href="{{ route('owner.user-owner.master-products.index') }}" class="back-button">
              <span class="material-symbols-outlined">arrow_back</span>
              {{ __('messages.owner.products.master_products.back') }}
          </a>
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
              @if($promo)
                <span class="badge-modern badge-warning">
                  <span class="material-symbols-outlined">local_offer</span>
                  {{ $promoName ?? __('messages.owner.products.master_products.promo_label') }}
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
                  <img src="{{ $src }}" alt="{{ $p['filename'] ?? __('messages.owner.products.master_products.gallery_image_alt') }}">
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
            <h3 class="section-title">{{ __('messages.owner.products.master_products.product_information') }}</h3>
          </div>
          
          <div class="detail-info-grid">
            <div class="detail-info-group">
              {{-- Product Name --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.owner.products.master_products.product_name') }}
                </div>
                <div class="detail-info-value">{{ $prod->name ?? '—' }}</div>
              </div>

              {{-- Product Code --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.owner.products.master_products.product_code') }}
                </div>
                <div class="detail-info-value">{{ $prod->product_code ?? '—' }}</div>
              </div>

              {{-- Quantity --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.owner.products.master_products.quantity') }}
                </div>
                <div class="detail-info-value">{{ $prod->quantity ?? 0 }}</div>
              </div>
            </div>

            <div class="detail-info-group">
              {{-- Category --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.owner.products.master_products.category') }}
                </div>
                <div class="detail-info-value">{{ $catName }}</div>
              </div>

              {{-- Base Price --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.owner.products.master_products.base_price') }}
                </div>
                <div class="detail-info-value">Rp {{ number_format($basePrice, 0, ',', '.') }}</div>
              </div>

              {{-- Final Price --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.owner.products.master_products.final_price') }}
                </div>
                <div class="detail-info-value">
                  <span class="text-success">Rp {{ number_format($finalPrice, 0, ',', '.') }}</span>
                </div>
              </div>
            </div>
          </div>

          {{-- Action Buttons --}}
<div class="action-buttons-group">
    <a href="{{ route('owner.user-owner.master-products.edit', $prod->id) }}" class="btn-action btn-action-edit">
        <span class="material-symbols-outlined">edit</span>
        {{ __('messages.owner.products.master_products.edit') }}
    </a>
    
    <form action="{{ route('owner.user-owner.master-products.destroy', $prod->id) }}" method="POST" class="d-inline-block" id="deleteForm">
        @csrf
        @method('DELETE')
        <button type="button" class="btn-action btn-action-delete" onclick="confirmDelete()">
            <span class="material-symbols-outlined">delete</span>
            {{ __('messages.owner.products.master_products.delete') }}
        </button>
    </form>
</div>

          {{-- Description Section --}}
          @if(!empty($prod->description))
            <div class="section-divider"></div>
            
            <div class="section-header">
              <div class="section-icon section-icon-red">
                <span class="material-symbols-outlined">description</span>
              </div>
              <h3 class="section-title">{{ __('messages.owner.products.master_products.description') }}</h3>
            </div>
            
            <div class="detail-info-item">
              <div class="detail-info-value">
                {!! $prod->description !!}
              </div>
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
                        <th>{{ __('messages.owner.products.master_products.options') }}</th>
                        <th class="text-end">{{ __('messages.owner.products.master_products.price') }}</th>
                        <th>{{ __('messages.owner.products.master_products.description') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($parent->options as $opt)
                        <tr class="table-row">
                          <td>
                            <span>{{ $opt->name }}</span>
                          </td>
                          <td class="text-end">
                            @if($opt->price > 0)
                              <span class="text-success">
                                +Rp {{ number_format((float) $opt->price, 0, ',', '.') }}
                              </span>
                            @else
                              <span>—</span>
                            @endif
                          </td>
                          <td>
                            @if($opt->description)
                              <span >{{ $opt->description }}</span>
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
                <p>{{ __('messages.owner.products.master_products.no_child_options') }}</p>
              @endif

            </div>
          </div>
        @endforeach
      @endif

      

    </div>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete() {
    Swal.fire({
        title: '{{ __('messages.owner.products.master_products.delete_confirmation_1') }}',
        text: '{{ __('messages.owner.products.master_products.delete_confirmation_2') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ae1504',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('messages.owner.products.master_products.delete_confirmation_3') }}',
        cancelButtonText: '{{ __('messages.owner.products.master_products.cancel') }}'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
}
</script>
@endpush

<style>
/* Action Buttons */
.action-buttons-group {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
  flex-wrap: wrap;
}

.btn-action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
  border: 1px solid rgba(0,0,0,.10);
  cursor: pointer;
  font-size: 0.95rem;
  background: #fff;
}

.btn-action .material-symbols-outlined {
  font-size: 1.25rem;
}

.btn-action-edit {
  color: #333;
  border-color: rgba(0,0,0,.10);
}

.btn-action-edit:hover {
  background: #f8f9fa;
  color: #333;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,.08);
}

.btn-action-delete {
  border-color: rgba(174,21,4,.25);
  color: #ae1504;
}

.btn-action-delete:hover {
  background: rgba(174,21,4,.05);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(174,21,4,.15);
}

@media (max-width: 576px) {
  .action-buttons-group {
    flex-direction: column;
  }
  
  .btn-action {
    width: 100%;
  }
}
</style>