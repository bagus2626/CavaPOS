@extends('layouts.partner')

@section('title', __('messages.partner.product.all_product.product_detail'))
@section('page_title', $data->name)

@section('content')
  <section class="content product-show">
    <div class="container-fluid">

      {{-- Tombol kembali --}}
      <a href="{{ route('partner.products.index') }}" class="btn btn-outline-choco mb-3 btn-pill">
        <i class="fas fa-arrow-left mr-2"></i> {{ __('messages.partner.product.all_product.back_to_products') }}
      </a>

      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header brand-header rounded-top-4">
          <h3 class="card-title fw-bold mb-0">{{ $data->product_code }}</h3>
        </div>

        <div class="card-body">
          <div class="row mb-5">

            {{-- KIRI: GAMBAR UTAMA --}}
            <div class="col-md-3">
              @php
                $mainPicture = optional(collect($data->pictures)->first())['path'] ?? null;
              @endphp
              <div class="main-image-container rounded-3 shadow-lg">
                @if($mainPicture)
                  <img src="{{ asset($mainPicture) }}" alt="{{ $data->name }}" class="main-image rounded overflow-hidden">
                @else
                  <div class="main-image placeholder-image">
                    <i class="fas fa-image fa-3x text-muted"></i>
                    <p class="text-muted mt-2">No Image</p>
                  </div>
                @endif
              </div>
            </div>

            {{-- KANAN: DETAIL KRITIS (HARGA, STOK, TIPE) --}}
            <div class="col-md-9 mt-4 mt-md-0 d-flex flex-column justify-content-center">

              <h4 class="mb-3 fw-bold text-choco">{{ $data->name }}</h4>

              {{-- Layout Harga & Kategori --}}
              <div class="row g-3 mb-4">
                <div class="col-sm-6">
                  <div class="info-card">
                    <div class="info-label">Kategori</div>
                    <div class="info-value">{{ optional($data->category)->category_name ?? '—' }}</div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="info-card">
                    <div class="info-label">{{ __('messages.partner.product.all_product.price') }}</div>
                    <div class="info-value">Rp {{ number_format($data->price, 0, ',', '.') }}</div>
                  </div>
                </div>
              </div>

              {{-- Layout Stok & Tipe --}}
              <div class="row g-3">

                <div class="col-sm-6">
                  <div class="info-card">
                    <div class="info-label">Tipe Stok</div>
                    <div class="info-value d-flex align-items-center justify-content-between">
                      <span
                        class="badge {{ $data->stock_type === 'linked' ? 'badge-soft-info' : 'badge-soft-secondary' }}">
                        {{ $data->stock_type === 'linked' ? 'LINKED' : 'DIRECT' }}
                      </span>
                      @if($data->stock_type === 'linked' && $data->recipes->isNotEmpty())
                        <button type="button" class="btn btn-sm btn-outline-info btn-pill" data-toggle="modal"
                          data-target="#recipeModal">
                          Resep
                        </button>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="info-card">
                    <div class="info-label">{{ __('messages.partner.product.all_product.quantity') }}</div>
                    <div class="info-value">
                      @if((int) $data->always_available_flag === 1)
                        <span class="badge badge-soft-success">UNLIMITED</span>
                      @else
                        @php
                          $stockUnit = $data->stock_type === 'direct' ? optional(optional($data->stock)->displayUnit)->unit_name : 'pcs';
                        @endphp
                        <span class="fw-bold {{ $data->quantity_available <= 0 ? 'text-danger' : 'text-success' }}">
                          {{ number_format($data->quantity_available, 0) }} {{ $stockUnit }}
                        </span>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Deskripsi --}}
          @if($data->description)
            <div class="card shadow-sm border-0 mt-3">
              <div class="card-body">
                <h6 class="fw-bold text-muted mb-2"><i class="fas fa-info-circle" style="margin-right: 0.5rem"></i>Deskripsi
                  Produk</h6>
                <p>{{ $data->description }}</p>
              </div>
            </div>
          @endif

          {{-- PARENT OPTIONS & RESEP OPSI --}}
          @foreach($data->parent_options as $parentOption)
            <div class="card mt-4 shadow-sm border-0 rounded-4">
              <div class="card-header sub-header rounded-top-4">
                <h4 class="card-title mb-0 fw-semibold">
                  <i class="fas fa-list-ul" style="margin-right: 0.5rem"></i>{{ $parentOption->name }}
                  <span class="small text-muted fw-normal ml-2">({{ Str::title($parentOption->provision) }}
                    @if($parentOption->provision_value > 0) : {{ $parentOption->provision_value }} @endif)</span>
                </h4>
              </div>
              <div class="card-body">
                @if(!empty($parentOption->description))
                  <p class="text-muted mb-3">{{ $parentOption->description }}</p>
                @endif

                @if($parentOption->options->isNotEmpty())
                  <div class="table-responsive rounded-3">
                    <table class="table table-hover align-middle product-options-table mb-0">
                      <thead>
                        <tr>
                          <th>Opsi</th>
                          <th class="text-center">Tipe Stok</th>
                          <th class="text-end">Harga Tambahan</th>
                          <th class="text-center">Stok Tersedia</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($parentOption->options as $option)
                          <tr>
                            <td>
                              <div class="fw-600">{{ $option->name }}</div>
                              @if($option->description)
                                <small class="text-muted">{{ $option->description }}</small>
                              @endif
                            </td>

                            <td class="text-center">
                              <div class="d-flex flex-column align-items-center">
                                <span
                                  class="badge {{ $option->stock_type === 'linked' ? 'badge-soft-info' : 'badge-soft-secondary' }} mb-1">
                                  {{ Str::title($option->stock_type) }}
                                </span>
                                @if($option->stock_type === 'linked' && $option->recipes->isNotEmpty())
                                  <button type="button" class="btn btn-sm btn-outline-info btn-pill mt-1" data-toggle="modal"
                                    data-target="#optionRecipeModal{{ $option->id }}">
                                    Resep
                                  </button>
                                @endif
                              </div>
                            </td>

                            <td class="text-end">
                              @if($option->price > 0)
                                <span class="text-success fw-semibold">+Rp {{ number_format($option->price, 0, ',', '.') }}</span>
                              @else
                                <span class="text-muted">—</span>
                              @endif
                            </td>

                            <td class="text-center">
                              @if((int) $option->always_available_flag === 1)
                                <span class="badge badge-soft-success">UNLIMITED</span>
                              @else
                                @php
                                  $optStockQty = $option->available_linked_quantity ?? optional($option->stock)->quantity_available ?? 0;
                                  $optStockUnit = $option->stock_type === 'direct' ? optional(optional($option->stock)->displayUnit)->unit_name : 'pcs';
                                @endphp
                                <span class="fw-bold {{ $optStockQty <= 0 ? 'text-danger' : '' }}">
                                  {{ number_format($optStockQty, 0) }} {{ $optStockUnit }}
                                </span>
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @else
                  <p class="text-muted mb-0">{{ __('messages.partner.product.all_product.no_option') }}</p>
                @endif
              </div>
            </div>
          @endforeach

        </div>
      </div>
    </div>
  </section>

  {{-- Modal Resep Produk Utama --}}
  @if($data->stock_type === 'linked' && $data->recipes->isNotEmpty())
    <div class="modal fade" id="recipeModal" tabindex="-1" role="dialog" aria-labelledby="recipeModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header brand-header">
            <h5 class="modal-title" id="recipeModalLabel">
              <i class="fas fa-clipboard-list" style="margin-right: 0.5rem"></i>Resep Produk: {{ $data->name }}
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-sm product-options-table mb-0">
                <thead>
                  <tr>
                    <th>Bahan Baku</th>
                    <th class="text-center">Jumlah Kebutuhan</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($data->recipes as $recipe)
                    <tr>
                      <td class="fw-600">{{ $recipe->stock->stock_name ?? '—' }}</td>
                      @php
                        $chosenUnit = $recipe->displayUnit;
                        $conversionValue = $chosenUnit->base_unit_conversion_value;
                        $displayQty = $recipe->quantity_used / $conversionValue;
                        $unitName = $chosenUnit->unit_name;
                      @endphp
                      <td class="text-center">{{ number_format($displayQty, 2, ',', '.') }} {{ $unitName }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-pill" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  @endif

  {{-- Modal Resep untuk Setiap Option --}}
  @foreach($data->parent_options as $parentOption)
    @foreach($parentOption->options as $option)
      @if($option->stock_type === 'linked' && $option->recipes->isNotEmpty())
        <div class="modal fade" id="optionRecipeModal{{ $option->id }}" tabindex="-1" role="dialog"
          aria-labelledby="optionRecipeModalLabel{{ $option->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header brand-header">
                <h5 class="modal-title" id="optionRecipeModalLabel{{ $option->id }}">
                  <i class="fas fa-clipboard-list" style="margin-right: 0.5rem"></i>Resep Opsi: {{ $option->name }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="table-responsive">
                  <table class="table table-sm product-options-table mb-0">
                    <thead>
                      <tr>
                        <th>Bahan Baku</th>
                        <th class="text-center">Jumlah Kebutuhan</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($option->recipes as $recipe)
                        <tr>
                          <td class="fw-600">{{ $recipe->stock->stock_name ?? '—' }}</td>
                          @php
                            $chosenUnit = $recipe->displayUnit;
                            $conversionValue = $chosenUnit->base_unit_conversion_value;
                            $displayQty = $recipe->quantity_used / $conversionValue;
                            $unitName = $chosenUnit->unit_name;
                          @endphp
                          <td class="text-center">{{ number_format($displayQty, 2, ',', '.') }} {{ $unitName }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-pill" data-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>
      @endif
    @endforeach
  @endforeach

  <style>
    /* ==== Style Modifications for New Layout ==== */
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
      background: #f3f4f6;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #9ca3af;
      font-size: 24px;
    }

    /* Info Cards */
    .info-card {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 1rem;
      transition: all .2s ease;
    }

    .info-card:hover {
      border-color: var(--choco);
      box-shadow: 0 4px 12px rgba(140, 16, 0, .08);
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

    /* Badges */
    .badge-soft-success {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
      padding: .32rem .55rem;
      border-radius: 999px;
      font-weight: 600;
    }

    .badge-soft-info {
      background: #e0f2fe;
      color: #075985;
      border: 1px solid #7dd3fc;
      padding: .32rem .55rem;
      border-radius: 999px;
      font-weight: 600;
    }

    .badge-soft-secondary {
      background: #f1f5f9;
      color: #475569;
      border: 1px solid #cbd5e1;
      padding: .32rem .55rem;
      border-radius: 999px;
      font-weight: 600;
    }

    /* Tabel options */
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

    /* Tombol */
    .btn-pill {
      border-radius: 999px;
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

    .btn-outline-info {
      color: #0284c7;
      border-color: #0284c7;
    }

    .btn-outline-info:hover {
      color: #fff;
      background: #0284c7;
      border-color: #0284c7;
    }

    /* Modal Styling */
    .modal-header.brand-header {
      background: linear-gradient(135deg, var(--choco), var(--soft-choco));
      color: #fff;
    }

    .modal-header .close {
      color: #fff;
      opacity: 1;
    }

    .modal-header .close:hover {
      opacity: 0.8;
    }
  </style>
@endsection