<div class="table-responsive rounded-xl product-table-wrapper">
  <table class="table table-hover align-middle product-table">
    <thead>
      <tr>
        <th>#</th>
        <th>{{ __('messages.partner.product.all_product.product_name') }}</th>
        <th>{{ __('messages.partner.product.all_product.description') }}</th>
        <th>{{ __('messages.partner.product.all_product.options') }}</th>
        <th>{{ __('messages.partner.product.all_product.quantity') }}</th>
        <th>{{ __('messages.partner.product.all_product.price') }}</th>
        <th>{{ __('messages.partner.product.all_product.pictures') }}</th>
        <th>{{ __('messages.partner.product.all_product.actions') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($products as $index => $product)
              <tr data-category="{{ $product->category_id }}">
                <td class="text-muted">{{ $index + 1 }}</td>

                <td class="fw-600">{{ $product->name }}</td>

                <td class="col-desc">
                  <div class="clamp-2">{{ $product->description ?? '—' }}</div>
                </td>

                <td class="col-options">
                  @if($product->parent_options->isEmpty())
                    <span class="text-muted">—</span>
                  @else
                    <div class="chips">
                      @foreach($product->parent_options as $opt)
                        <span class="chip">{{ $opt->name }}</span>
                      @endforeach
                    </div>
                  @endif
                </td>

                <td>

                  @php
                    $qtyAvailable = round($product->quantity_available, 0);
                  @endphp

                  @if($product->stock_type == 'linked')
                    <span>{{ number_format($qtyAvailable, 0) }}</span>

                  @elseif((int) $product->always_available_flag === 1)
                    <span
                      class="">{{ __('messages.partner.product.all_product.always_available') }}</span>
                  @elseif($product->stock)
                    {{ number_format($qtyAvailable, 0) }}
                  @else
                    <span class="" title="Stok tidak ditemukan">0</span>
                  @endif
                </td>

                <td>Rp&nbsp;{{ number_format($product->price, 0, ',', '.') }}</td>

                <td class="col-images">
                  @if(!empty($product->pictures) && is_array($product->pictures))
                    <div class="thumb-list">
                      @foreach($product->pictures as $picture)
                        @php $src = asset($picture['path']); @endphp
                        <a href="{{ $src }}" target="_blank" rel="noopener" class="thumb-item"
                          title="{{ $picture['filename'] ?? 'Product Image' }}">
                          <img src="{{ $src }}" alt="{{ $picture['filename'] ?? ($product->name . ' image') }}"
                            class="thumb-img-80">
                        </a>
                      @endforeach
                    </div>
                  @else
                    <span class="text-muted">{{ __('messages.partner.product.all_product.no_images') }}</span>
                  @endif
                </td>

                <td class="col-actions">
                  <div class="btn-group btn-group-sm">
                    <a href="{{ route('partner.products.show', $product->id) }}" class="btn btn-outline-secondary btn-pill">
                      <i class="fas fa-eye"></i> <span>Detail</span>
                    </a>
                    <a href="{{ route('partner.products.edit', $product->id) }}" class="btn btn-outline-choco btn-pill">
                      <i class="fas fa-pen"></i> <span>{{ __('messages.partner.product.all_product.edit') }}</span>
                    </a>
                    {{-- rafi --}}
                    {{-- <button onclick="deleteProduct({{ $product->id }})" class="btn btn-soft-danger btn-pill">
                      <i class="fas fa-trash-alt"></i> <span>{{ __('messages.partner.product.all_product.delete') }}</span>
                    </button> --}}
                  </div>
                </td>

              </tr>
      @endforeach
    </tbody>
  </table>
</div>

<style>
  /* ==== Products table polish ==== */
  .product-table-wrapper .table {
    border-collapse: separate;
    border-spacing: 0;
    background: #fff;
    overflow: hidden;
    border-radius: 10px;
  }

  .product-table-wrapper thead th {
    background: #fff;
    border-bottom: 2px solid #eef1f4 !important;
    color: #374151;
    font-weight: 700;
    white-space: nowrap;
  }

  .product-table-wrapper tbody td {
    vertical-align: middle;
  }

  .product-table-wrapper tbody tr {
    transition: background-color .12s ease;
  }

  .product-table-wrapper tbody tr:hover {
    background: rgba(140, 16, 0, .04);
  }

  /* Nama & deskripsi */
  .fw-600 {
    font-weight: 600;
  }

  .col-desc .clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    color: #4b5563;
    /* slate-600 */
  }

  /* Chips untuk opsi/pilihan */
  .chips {
    display: flex;
    flex-wrap: wrap;
    gap: .35rem;
  }

  .chip {
    display: inline-flex;
    align-items: center;
    padding: .18rem .5rem;
    border-radius: 999px;
    font-size: .78rem;
    font-weight: 600;
    background: #fff1ef;
    color: #8c1000;
    border: 1px solid #f7c9c2;
  }

  /* Thumbnails */
  .thumb-list {
    display: flex;
    flex-wrap: wrap;
    gap: .35rem;
  }

  .thumb-item {
    display: block;
  }

  .thumb-img-80 {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    border: 0;
    box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
    transition: transform .15s ease, box-shadow .15s ease;
  }

  .thumb-item:hover .thumb-img-80 {
    transform: scale(1.03);
    box-shadow: 0 10px 24px rgba(0, 0, 0, .12);
  }

  /* Badges & buttons (soft variants, selaras choco) */
  .badge-soft-success {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    padding: .32rem .55rem;
    border-radius: 999px;
    font-weight: 600;
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

  /* Responsive tweak untuk kolom */
  .col-actions {
    white-space: nowrap;
  }

  .col-images {
    min-width: 160px;
  }

  .col-options {
    min-width: 180px;
  }
</style>