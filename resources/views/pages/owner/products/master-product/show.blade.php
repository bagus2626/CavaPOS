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
    $promoType = $promo->promotion_type ?? null;   // 'percentage' atau 'nominal'
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

  <section class="content product-show">
    <div class="container-fluid">

      {{-- Toolbar --}}
      <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('owner.user-owner.master-products.index') }}" class="btn btn-choco mb-3">
          <i class="fas fa-arrow-left mr-2"></i>{{ __('messages.owner.products.master_products.back_to_products') }}
        </a>

        <div class="btn-group">
          <a href="{{ route('owner.user-owner.master-products.edit', $prod->id) }}" class="btn btn-choco btn-pill">
            <i class="fas fa-pen mr-1"></i> {{ __('messages.owner.products.master_products.edit') }}
          </a>
          <button class="btn btn-soft-danger btn-pill"
            onclick="ownerConfirmDeletion(`{{ route('owner.user-owner.master-products.destroy', $prod->id) }}`)">
            <i class="fas fa-trash-alt mr-1"></i> {{ __('messages.owner.products.master_products.delete') }}
          </button>
        </div>
      </div>

      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header brand-header rounded-top-4">
          <h3 class="card-title fw-bold mb-0">{{ $prod->product_code }}</h3>
        </div>

        <div class="card-body">
          <div class="row mb-5">

            {{-- KIRI: GAMBAR UTAMA --}}
            <div class="col-md-3">
              <div class="main-image-container rounded-3 shadow-lg">
                @if($firstImg)
                  <img src="{{ $firstImg }}" alt="{{ $prod->name }}" class="main-image rounded overflow-hidden">
                @else
                  <div class="main-image placeholder-image">
                    <i class="fas fa-image fa-3x text-muted"></i>
                    <p class="text-muted mt-2">No Image</p>
                  </div>
                @endif
              </div>

              {{-- Gallery Thumbnail --}}
              @if(!empty($prod->pictures) && is_array($prod->pictures) && count($prod->pictures) > 1)
                <div class="thumb-grid mt-3">
                  @foreach($prod->pictures as $p)
                    @php
                      $src = !empty($p['path'])
                        ? (Str::startsWith($p['path'], ['http://', 'https://']) ? $p['path'] : asset($p['path']))
                        : null;
                    @endphp
                    @if($src)
                      <a href="{{ $src }}" target="_blank" rel="noopener" class="thumb-item">
                        <img src="{{ $src }}" alt="{{ $p['filename'] ?? 'Product Image' }}">
                      </a>
                    @endif
                  @endforeach
                </div>
              @endif
            </div>

            {{-- KANAN: DETAIL PRODUK --}}
            <div class="col-md-9 mt-4 mt-md-0 d-flex flex-column justify-content-center">

              <h4 class="mb-3 fw-bold text-choco">{{ $prod->name }}</h4>
              {{-- Layout Kategori & Promo --}}
              <div class="row g-3 mb-4">
                <div class="col-sm-6">
                  <div class="info-card">
                    <div class="info-label">{{ __('messages.owner.products.master_products.category') }}</div>
                    <div class="info-value">{{ $catName }}</div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="info-card">
                    <div class="info-label">{{ __('messages.owner.products.master_products.promotion') }}</div>
                    <div class="info-value">
                      @if($promo)
                        <span class="badge badge-promo">
                          <i class="fas fa-bolt mr-1"></i>{{ $promoName ?? 'Promo' }}
                        </span>
                      @else
                        <span
                          class="badge badge-soft-secondary">{{ __('messages.owner.products.master_products.none') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
              </div>

              {{-- Layout Harga --}}
              <div class="row g-3">
                <div class="col-sm-6">
                  <div class="info-card">
                    <div class="info-label">{{ __('messages.owner.products.master_products.base_price') }}</div>
                    <div class="info-value">Rp {{ number_format($basePrice, 0, ',', '.') }}</div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="info-card highlight-card">
                    <div class="info-label">{{ __('messages.owner.products.master_products.final_price') }}</div>
                    <div class="info-value d-flex align-items-baseline gap-2 flex-wrap">
                      <span class="price-final">Rp {{ number_format($finalPrice, 0, ',', '.') }}</span>
                      @if($promo && $finalPrice < $basePrice)
                        <span class="badge badge-saving">
                          @if($promoType === 'percentage')
                            -{{ number_format($promoValue, 0, ',', '.') }}%
                          @else
                            -Rp {{ number_format($promoValue, 0, ',', '.') }}
                          @endif
                        </span>
                      @endif
                    </div>
                  </div>
                </div>
              </div>

              {{-- Detail Promo (jika ada) --}}
              @if($promo)
                <div class="card mt-3 shadow-sm border-0">
                  <div class="card-body">
                    <h6 class="fw-bold text-muted mb-2">
                      <i class="fas fa-tag mr-2"></i>{{ __('messages.owner.products.master_products.promotion') }} Detail
                    </h6>
                    <div class="row g-2">
                      <div class="col-6">
                        <small class="text-muted d-block">{{ __('messages.owner.products.master_products.type') }}</small>
                        <span class="text-capitalize fw-600">{{ $promoType }}</span>
                      </div>
                      <div class="col-6">
                        <small class="text-muted d-block">{{ __('messages.owner.products.master_products.value') }}</small>
                        <span class="fw-600">
                          @if($promoType === 'percentage')
                            {{ number_format($promoValue, 0, ',', '.') }}%
                          @else
                            Rp {{ number_format($promoValue, 0, ',', '.') }}
                          @endif
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>

          {{-- Deskripsi --}}
          @if(!empty($prod->description))
            <div class="card shadow-sm border-0 mt-3">
              <div class="card-body">
                <h6 class="fw-bold text-muted mb-2">
                  <i class="fas fa-info-circle mr-2"></i>{{ __('messages.owner.products.master_products.description') }}
                </h6>
                <div class="desc-body">
                  {!! $prod->description !!}
                </div>
              </div>
            </div>
          @endif

          {{-- OPTIONS --}}
          @php
            $parents = $prod->parent_options ?? collect();
          @endphp

          @if($parents && count($parents))
            @foreach($parents as $parent)
              <div class="card mt-4 shadow-sm border-0 rounded-4">
                <div class="card-header sub-header rounded-top-4">
                  <h4 class="card-title mb-0 fw-semibold">
                    <i class="fas fa-list-ul mr-2"></i>{{ $parent->name }}
                    <span class="small text-muted fw-normal ms-2">
                      ({{ $parent->provision ?? 'OPTIONAL' }}
                      @if(!empty($parent->provision_value) && $parent->provision !== 'OPTIONAL')
                        : {{ $parent->provision_value }}
                      @endif)
                    </span>
                  </h4>
                </div>
                <div class="card-body">
                  @if(!empty($parent->description))
                    <p class="text-muted mb-3">{{ $parent->description }}</p>
                  @endif

                  @if(!empty($parent->options) && count($parent->options))
                    <div class="table-responsive rounded-3">
                      <table class="table table-hover align-middle product-options-table mb-0">
                        <thead>
                          <tr>
                            <th>{{ __('messages.owner.products.master_products.options') }}</th>
                            <th class="text-end">{{ __('messages.owner.products.master_products.price') }}</th>
                            <th>{{ __('messages.owner.products.master_products.description') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($parent->options as $opt)
                            <tr>
                              <td>
                                <div class="fw-600">{{ $opt->name }}</div>
                              </td>
                              <td class="text-end">
                                @if($opt->price > 0)
                                  <span class="text-success fw-semibold">+Rp
                                    {{ number_format((float) $opt->price, 0, ',', '.') }}</span>
                                @else
                                  <span class="text-muted">—</span>
                                @endif
                              </td>
                              <td>
                                @if($opt->description)
                                  <small class="text-muted">{{ $opt->description }}</small>
                                @else
                                  <span class="text-muted">—</span>
                                @endif
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  @else
                    <p class="text-muted mb-0">{{ __('messages.owner.products.master_products.no_child_options') }}</p>
                  @endif
                </div>
              </div>
            @endforeach
          @endif

        </div>
      </div>
    </div>
  </section>

  <style>
    /* ==== Owner Product Show - New Layout ==== */
    :root {
      --choco: #8c1000;
      --soft-choco: #c12814;
      --ink: #22272b;
      --paper: #f7f7f8;
      --radius: 12px;
      --shadow: 0 6px 20px rgba(0, 0, 0, .08);
    }

    .product-show .card {
      border-radius: var(--radius);
      box-shadow: var(--shadow);
    }

    .product-show .brand-header {
      background: linear-gradient(135deg, var(--choco), var(--soft-choco));
      color: #fff;
      border-bottom: 0;
    }

    .product-show .sub-header {
      background: #fff;
      border-bottom: 1px solid #eef1f4;
    }

    /* Main Image Styling */
    .main-image-container {
      aspect-ratio: 1/1;
      overflow: hidden;
    }

    .main-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .placeholder-image {
      width: 100%;
      height: 100%;
      background: #f3f4f6;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #9ca3af;
    }

    /* Gallery Thumbnail */
    .thumb-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
      gap: 0.5rem;
    }

    .thumb-item img {
      width: 100%;
      aspect-ratio: 1/1;
      object-fit: cover;
      display: block;
      border-radius: 8px;
      border: 2px solid #e5e7eb;
      transition: all 0.2s ease;
    }

    .thumb-item img:hover {
      border-color: var(--choco);
      transform: scale(1.05);
    }

    /* Info Cards */
    .info-card {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 1rem;
      transition: all .2s ease;
      height: 100%;
    }

    .info-card:hover {
      border-color: var(--choco);
      box-shadow: 0 4px 12px rgba(140, 16, 0, .08);
    }

    .info-card.highlight-card {
      background: linear-gradient(135deg, #fff5f5, #fff);
      border-color: var(--choco);
    }

    .info-label {
      font-size: .85rem;
      font-weight: 600;
      color: #6b7280;
      text-transform: uppercase;
      letter-spacing: .5px;
      margin-bottom: .5rem;
    }

    .info-value {
      font-size: 1.1rem;
      font-weight: 600;
      color: #1f2937;
    }

    .price-final {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--choco);
    }

    /* Badges */
    .badge-saving {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
      border-radius: 999px;
      padding: .32rem .55rem;
      font-weight: 700;
      font-size: 0.85rem;
    }

    .badge-promo {
      background: #fef3c7;
      color: #92400e;
      border: 1px solid #fde68a;
      border-radius: 999px;
      padding: .32rem .6rem;
      font-weight: 700;
    }

    .badge-soft-secondary {
      background: #f3f4f6;
      color: #374151;
      border: 1px solid #e5e7eb;
      border-radius: 999px;
      padding: .32rem .6rem;
      font-weight: 600;
    }

    /* Description */
    .desc-body :where(p, ul, ol) {
      margin-bottom: .5rem;
    }

    .desc-body img {
      max-width: 100%;
      height: auto;
      border-radius: 10px;
    }

    /* Options Table */
    .product-options-table {
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
    }

    .product-options-table thead th {
      background: #fff;
      border-bottom: 2px solid #eef1f4 !important;
      color: #374151;
      font-weight: 700;
    }

    .product-options-table tbody tr:hover {
      background: #f9fafb;
    }

    /* Buttons */
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
      color: #fff;
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

    .btn-soft-danger {
      background: #fee2e2;
      color: #991b1b;
      border-color: #fecaca;
    }

    .btn-soft-danger:hover {
      background: #fecaca;
      color: #7f1d1d;
      border-color: #fca5a5;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .price-final {
        font-size: 1.25rem;
      }

      .info-card {
        padding: 0.75rem;
      }

      .info-value {
        font-size: 1rem;
      }
    }
  </style>
@endsection

@push('scripts')
  <script>
    function ownerConfirmDeletion(url, opts = {}) {
      const base = {
        title: '{{ __('messages.owner.products.master_products.delete_confirmation_1') }}',
        text: '{{ __('messages.owner.products.master_products.delete_confirmation_2') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#8c1000',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('messages.owner.products.master_products.delete_confirmation_3') }}',
        cancelButtonText: '{{ __('messages.owner.products.master_products.cancel') }}',
        reverseButtons: true
      };
      const swal = window.$swal || window.Swal;
      if (!swal) {
        if (confirm(base.title + '\n' + base.text)) ownerPostDelete(url);
        return;
      }
      swal.fire(Object.assign(base, opts)).then(r => { if (r.isConfirmed) ownerPostDelete(url); });
    }

    function ownerPostDelete(url) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = url;
      form.style.display = 'none';
      form.innerHTML = `
        @csrf
        <input type="hidden" name="_method" value="DELETE">
      `;
      document.body.appendChild(form);
      form.submit();
    }
  </script>
@endpush