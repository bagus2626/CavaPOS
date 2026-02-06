@php
  use Illuminate\Support\Str;
@endphp

<div class="modern-card outlet-products-responsive">

  {{-- =======================
    DESKTOP: TABLE
  ======================= --}}
  <div class="data-table-wrapper only-desktop">
    <table class="data-table">
      <thead>
        <tr>
          <th class="text-center" style="width: 60px;">#</th>
          <th>{{ __('messages.owner.products.outlet_products.product') }}</th>
          <th>{{ __('messages.owner.products.outlet_products.category') }}</th>
          <th>{{ __('messages.owner.products.outlet_products.stock') }}</th>
          <th class="text-center">{{ __('messages.owner.products.outlet_products.status') }}</th>
          <th>{{ __('messages.owner.products.outlet_products.price') }}</th>
          <th>{{ __('messages.owner.products.outlet_products.promo') }}</th>
          <th class="text-center" style="width: 160px;">
            {{ __('messages.owner.products.outlet_products.actions') }}
          </th>
        </tr>
      </thead>

      <tbody id="productTableBody">
        @forelse ($products as $index => $p)
          @php
            $name = $p->name ?? $p->product_name;
            $img = (!empty($p->pictures) && isset($p->pictures[0]['path'])) ? asset($p->pictures[0]['path']) : null;

            $qtyAvailable = $p->quantity_available;
            $isQtyZero = $qtyAvailable < 1 && $qtyAvailable !== 999999999;

            // Stock display (copy logic kamu)
            $stockDisplay = '0';
            if ($p->stock_type == 'linked') {
              if ($qtyAvailable === 999999999) {
                $stockDisplay = __('messages.owner.products.outlet_products.always_available');
              } elseif ($isQtyZero) {
                $stockDisplay = 'Out of Stock';
              } else {
                $stockDisplay = number_format(floor($qtyAvailable), 0) . ' pcs';
              }
            } elseif ((int)$p->always_available_flag === 1) {
              $stockDisplay = __('messages.owner.products.outlet_products.always_available');
            } elseif ($p->stock) {
              if ($isQtyZero) {
                $stockDisplay = '0';
              } else {
                $unit = $p->stock->displayUnit->unit_name ?? 'unit';
                $stockDisplay = rtrim(rtrim(number_format($qtyAvailable, 2, ',', '.'), '0'), ',') . ' ' . $unit;
              }
            }
            $active = (int) ($p->is_active ?? 1);
          @endphp

          <tr class="table-row">
            <td class="text-center text-muted">{{ $products->firstItem() + $index }}</td>

            <td>
              <div class="user-info-cell">
                <div class="position-relative" style="width:40px;height:40px;">
                  @if($img)
                    <img src="{{ $img }}" alt="{{ $name }}"
                      class="user-avatar" style="width:40px;height:40px;object-fit:cover;border-radius:10px;" loading="lazy">
                  @else
                    <div class="user-avatar-placeholder">
                      <span class="material-symbols-outlined">image</span>
                    </div>
                  @endif

                  @if((int)$p->is_hot_product === 1)
                    <span class="hot-dot" title="HOT">HOT</span>
                  @endif
                </div>

                <div class="product-title">
                  <div class="data-name">{{ $name }}</div>
                  <div class="subtle">{{ $p->category->category_name ?? '-' }}</div>
                </div>
              </div>
            </td>

            <td>
              <span class="badge-modern badge-info">
                {{ $p->category->category_name ?? '-' }}
              </span>
            </td>

            <td>
              @if($stockDisplay === __('messages.owner.products.outlet_products.always_available'))
                <span class="text-muted" style="font-style: italic;">{{ $stockDisplay }}</span>
              @elseif($stockDisplay === 'Out of Stock' || $stockDisplay === '0')
                <span class="badge-modern badge-danger">{{ $stockDisplay }}</span>
              @else
                <span class="fw-700">{{ $stockDisplay }}</span>
              @endif
            </td>

            <td class="text-center">
              <span class="badge-modern badge-{{ $active ? 'success' : 'danger' }}">
                {{ $active ? __('messages.owner.products.outlet_products.active') : __('messages.owner.products.outlet_products.inactive') }}
              </span>
            </td>

            <td><span class="fw-700">Rp {{ number_format($p->price, 0, ',', '.') }}</span></td>

            <td>
              @if($p->promotion)
                <span class="badge-modern badge-warning">{{ $p->promotion->promotion_name }}</span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>

            <td class="text-center">
              <div class="table-actions">
                <a href="{{ route('owner.user-owner.outlet-products.edit', $p->id) }}"
                   class="btn-table-action edit"
                   title="{{ __('messages.owner.products.outlet_products.edit') }}">
                  <span class="material-symbols-outlined">edit</span>
                </a>
                <button onclick="deleteProduct({{ $p->id }})"
                        class="btn-table-action delete"
                        title="{{ __('messages.owner.products.outlet_products.delete') }}">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center">
              <div class="table-empty-state">
                <span class="material-symbols-outlined">inventory_2</span>
                <h4>{{ __('messages.owner.products.outlet_products.no_product_yet') }}</h4>
                <p>{{ __('messages.owner.products.outlet_products.add_first_product') ?? 'Add your first product to get started' }}</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- =======================
    MOBILE: CARDS
  ======================= --}}
  <div class="only-mobile mobile-product-list">
    @forelse ($products as $p)
      @php
        $name = $p->name ?? $p->product_name;
        $img = (!empty($p->pictures) && isset($p->pictures[0]['path'])) ? asset($p->pictures[0]['path']) : null;

        $qtyAvailable = $p->quantity_available;
        $isQtyZero = $qtyAvailable < 1 && $qtyAvailable !== 999999999;

        $stockDisplay = '0';
        if ($p->stock_type == 'linked') {
          if ($qtyAvailable === 999999999) {
            $stockDisplay = __('messages.owner.products.outlet_products.always_available');
          } elseif ($isQtyZero) {
            $stockDisplay = 'Out of Stock';
          } else {
            $stockDisplay = number_format(floor($qtyAvailable), 0) . ' pcs';
          }
        } elseif ((int)$p->always_available_flag === 1) {
          $stockDisplay = __('messages.owner.products.outlet_products.always_available');
        } elseif ($p->stock) {
          if ($isQtyZero) {
            $stockDisplay = '0';
          } else {
            $unit = $p->stock->displayUnit->unit_name ?? 'unit';
            $stockDisplay = rtrim(rtrim(number_format($qtyAvailable, 2, ',', '.'), '0'), ',') . ' ' . $unit;
          }
        }
        $active = (int) ($p->is_active ?? 1);
      @endphp

      <div class="product-card">
        <div class="product-card__top">
          <div class="avatar-wrap">
            <div class="product-card__avatar">
              @if($img)
                <img src="{{ $img }}" alt="{{ $name }}" loading="lazy">
              @else
                <div class="user-avatar-placeholder">
                  <span class="material-symbols-outlined">image</span>
                </div>
              @endif
            </div>

            @if((int)$p->is_hot_product === 1)
              <span class="hot-dot">HOT</span>
            @endif
          </div>


          <div class="product-card__meta">
            <div class="product-card__name">{{ $name }}</div>

            <div class="product-card__chips">
              <span class="chip">
                <span class="material-symbols-outlined">category</span>
                <span class="chip-text">{{ $p->category->category_name ?? '-' }}</span>
              </span>

              <span class="chip chip-muted">
                <span class="material-symbols-outlined">inventory</span>
                <span class="chip-text stock-wrap">{{ $stockDisplay }}</span>
              </span>
            </div>
          </div>

          <div class="product-card__right">
            <div class="price">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
            <div class="status">
              <span class="badge-modern badge-{{ $active ? 'success' : 'danger' }}">
                {{ $active ? __('messages.owner.products.outlet_products.active') : __('messages.owner.products.outlet_products.inactive') }}
              </span>
            </div>
          </div>
        </div>

        <div class="product-card__bottom">
          <div class="promo">
            @if($p->promotion)
              <span class="badge-modern badge-warning">{{ $p->promotion->promotion_name }}</span>
            @else
              <span class="text-muted"></span>
            @endif
          </div>

          <div class="actions">
            <a href="{{ route('owner.user-owner.outlet-products.edit', $p->id) }}" class="btn-card-action">
              <span class="material-symbols-outlined">edit</span>
              <span>{{ __('messages.owner.products.outlet_products.edit') }}</span>
            </a>

            <button type="button" class="btn-card-action danger" onclick="deleteProduct({{ $p->id }})">
              <span class="material-symbols-outlined">delete</span>
              <span>{{ __('messages.owner.products.outlet_products.delete') }}</span>
            </button>
          </div>
        </div>
      </div>
    @empty
      <div class="table-empty-state" style="padding: 24px;">
        <span class="material-symbols-outlined">inventory_2</span>
        <h4>{{ __('messages.owner.products.outlet_products.no_product_yet') }}</h4>
        <p>{{ __('messages.owner.products.outlet_products.add_first_product') ?? 'Add your first product to get started' }}</p>
      </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  @if($products->hasPages())
    <div class="table-pagination">
      {{ $products->links() }}
    </div>
  @endif
</div>

<style>
  .outlet-products-responsive .only-desktop{ display:block; }
  .outlet-products-responsive .only-mobile{ display:none; }

  @media (max-width: 768px){
    .outlet-products-responsive .only-desktop{ display:none; }
    .outlet-products-responsive .only-mobile{ display:block; }
  }

  /* wrapper luar: tidak nge-clip badge */
  .avatar-wrap{
    position: relative;
    width: 54px;
    height: 54px;
    flex: 0 0 auto;
    overflow: visible; /* penting */
  }

  /* avatar tetap clip gambar */
  .product-card__avatar{
    width: 54px;
    height: 54px;
    border-radius: 14px;
    overflow: hidden;   /* tetap, biar gambar rounded */
    position: relative; /* optional */
  }

  /* badge keluar, tapi tidak terpotong */
  .hot-dot{
    position: absolute;
    top: -7px;
    right: -7px;
    z-index: 3;         /* biar di atas gambar */
  }


  .hot-dot{
    position:absolute;
    top:-7px;
    right:-7px;
    background:#ff5722;
    color:#fff;
    padding:2px 7px;
    border-radius:999px;
    font-size:10px;
    font-weight:800;
    box-shadow:0 6px 14px rgba(0,0,0,.18);
    letter-spacing:.3px;
  }

  .product-title .subtle{
    font-size: 12px;
    color: #8a8a8a;
    margin-top: 2px;
    max-width: 240px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* MOBILE */
  .mobile-product-list{
    padding: 14px;
    display: grid;
    gap: 12px;
  }
  .product-card{
    border: 1px solid rgba(0,0,0,.08);
    background: #fff;
    border-radius: 16px;
    padding: 14px;
    box-shadow: 0 10px 24px rgba(0,0,0,.06);
    margin-bottom: 5px;
  }
  .product-card__top{
    display:flex;
    gap: 12px;
    align-items:flex-start;
  }
  .product-card__avatar{
    width: 54px;
    height: 54px;
    border-radius: 14px;
    overflow: hidden;
    position: relative;
    flex: 0 0 auto;
  }
  .product-card__avatar img{
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .product-card__meta{
    flex:1;
    min-width:0;
  }
  .product-card__name{
    font-weight: 800;
    font-size: 14px;
    line-height: 1.25;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .product-card__chips{
    display:flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  .chip{
    display:inline-flex;
    align-items:center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(0,0,0,.04);
    font-size: 12px;
    color: #555;
    max-width: 100%;
  }
  .chip-muted{
    background: rgba(0,0,0,.03);
    color: #666;
  }
  .chip .material-symbols-outlined{ font-size: 16px; opacity: .85; }
  .chip-text{
    display:inline-block;
    max-width: 180px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .stock-wrap{
    max-width: 140px;
  }

  .product-card__right{
    margin-left:auto;
    text-align:right;
    display:flex;
    flex-direction:column;
    gap: 8px;
    flex: 0 0 auto;
  }
  .product-card__right .price{
    font-weight: 900;
    font-size: 13px;
    line-height: 1.1;
  }

  .product-card__bottom{
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px dashed rgba(0,0,0,.10);
    display:grid;
    gap: 12px;
  }
  .product-card__bottom .actions{
    display:flex;
    gap: 8px;
  }
  .btn-card-action{
    flex:1;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap: 6px;
    padding: 10px;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,.10);
    background:#fff;
    font-size: 12px;
    font-weight: 800;
  }
  .btn-card-action .material-symbols-outlined{ font-size: 18px; }
  .btn-card-action.danger{
    border-color: rgba(174,21,4,.25);
    color: #ae1504;
  }
</style>
