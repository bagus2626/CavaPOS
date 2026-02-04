@php
  use Illuminate\Support\Str;
@endphp

<div class="modern-card product-responsive">

  {{-- =======================
    DESKTOP: TABLE
  ======================= --}}
  <div class="data-table-wrapper only-desktop">
    <table class="data-table">
      <thead>
        <tr>
          <th class="text-center" style="width: 60px;">#</th>
          <th>{{ __('messages.owner.products.master_products.product_name') }}</th>
          <th>{{ __('messages.owner.products.master_products.options') }}</th>
          <th>{{ __('messages.owner.products.master_products.quantity') }}</th>
          <th>{{ __('messages.owner.products.master_products.price') }}</th>
          <th>{{ __('messages.owner.products.master_products.promo') }}</th>
          <th class="text-center" style="width: 180px;">
            {{ __('messages.owner.products.master_products.actions') }}
          </th>
        </tr>
      </thead>

      <tbody>
        @forelse ($products as $index => $product)
          @php
            $img = null;
            if (!empty($product->pictures) && is_array($product->pictures)) {
              $img = $product->pictures[0]['path'] ?? null;
            }
            $optionsText = $product->parent_options->pluck('name')->implode(', ');
          @endphp

          <tr class="table-row">
            <td class="text-center text-muted">{{ $products->firstItem() + $index }}</td>

            <td>
              <div class="user-info-cell">
                @if($img)
                  <img src="{{ asset($img) }}" alt="{{ $product->name }}" class="user-avatar" loading="lazy">
                @else
                  <div class="user-avatar-placeholder">
                    <span class="material-symbols-outlined">inventory_2</span>
                  </div>
                @endif
                <span class="data-name">{{ $product->name }}</span>
              </div>
              <div class="text-muted" style="margin-left:56px;font-size:12px;">
                {{ $product->category->category_name ?? '-' }}
              </div>
            </td>

            <td>
              @if($product->parent_options->isEmpty())
                <span class="text-muted">{{ __('messages.owner.products.master_products.no_options') }}</span>
              @else
                <span class="text-secondary">{{ $optionsText }}</span>
              @endif
            </td>

            <td><span class="fw-600">{{ $product->quantity }}</span></td>

            <td><span class="fw-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span></td>

            <td>
              @if($product->promotion)
                <span class="badge-modern badge-warning">{{ $product->promotion->promotion_name }}</span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>

            <td class="text-center">
              <div class="table-actions">
                <a href="{{ route('owner.user-owner.master-products.show', $product->id) }}"
                   class="btn-table-action view"
                   title="{{ __('messages.owner.products.master_products.detail') }}">
                  <span class="material-symbols-outlined">visibility</span>
                </a>
                <a href="{{ route('owner.user-owner.master-products.edit', $product->id) }}"
                   class="btn-table-action edit"
                   title="{{ __('messages.owner.products.master_products.edit') }}">
                  <span class="material-symbols-outlined">edit</span>
                </a>
                <button type="button"
                        onclick="deleteProduct({{ $product->id }})"
                        class="btn-table-action delete"
                        title="{{ __('messages.owner.products.master_products.delete') }}">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center">
              <div class="table-empty-state">
                <span class="material-symbols-outlined">inventory_2</span>
                <h4>{{ __('messages.owner.products.master_products.no_products') ?? 'No products found' }}</h4>
                <p>{{ __('messages.owner.products.master_products.add_first_product') ?? 'Add your first product to get started' }}</p>
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
    @forelse ($products as $product)
      @php
        $img = null;
        if (!empty($product->pictures) && is_array($product->pictures)) {
          $img = $product->pictures[0]['path'] ?? null;
        }
        $optionsText = $product->parent_options->pluck('name')->implode(', ');
      @endphp

      <div class="product-card">
        <div class="product-card__top">
          <div class="product-card__avatar">
            @if($img)
              <img src="{{ asset($img) }}" alt="{{ $product->name }}" loading="lazy">
            @else
              <div class="user-avatar-placeholder">
                <span class="material-symbols-outlined">inventory_2</span>
              </div>
            @endif
          </div>

          <div class="product-card__meta">
            <div class="product-card__name">{{ $product->name }}</div>
            <div class="product-card__sub">
              <span class="chip">
                <span class="material-symbols-outlined">category</span>
                <span class="chip-text">{{ $product->category->category_name ?? '-' }}</span>
              </span>

              @if($product->promotion)
                <span class="chip chip-warn">
                  <span class="material-symbols-outlined">local_offer</span>
                  <span class="chip-text">{{ $product->promotion->promotion_name }}</span>
                </span>
              @endif
            </div>
          </div>

          <div class="product-card__price">
            <div class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
            <div class="qty">{{ __('messages.owner.products.master_products.quantity') }}: <b>{{ $product->quantity }}</b></div>
          </div>
        </div>

        <div class="product-card__info">
          <div class="info-row">
            <span class="label">{{ __('messages.owner.products.master_products.options') }}</span>
            <span class="value options">
              {{ $product->parent_options->isEmpty() ? __('messages.owner.products.master_products.no_options') : $optionsText }}
            </span>
          </div>
        </div>

        <div class="product-card__actions">
          <a href="{{ route('owner.user-owner.master-products.show', $product->id) }}" class="btn-card-action">
            <span class="material-symbols-outlined">visibility</span>
            <span>{{ __('messages.owner.products.master_products.detail') }}</span>
          </a>

          <a href="{{ route('owner.user-owner.master-products.edit', $product->id) }}" class="btn-card-action">
            <span class="material-symbols-outlined">edit</span>
            <span>{{ __('messages.owner.products.master_products.edit') }}</span>
          </a>

          <button type="button" class="btn-card-action danger" onclick="deleteProduct({{ $product->id }})">
            <span class="material-symbols-outlined">delete</span>
            <span>{{ __('messages.owner.products.master_products.delete') }}</span>
          </button>
        </div>
      </div>

    @empty
      <div class="table-empty-state" style="padding: 24px;">
        <span class="material-symbols-outlined">inventory_2</span>
        <h4>{{ __('messages.owner.products.master_products.no_products') ?? 'No products found' }}</h4>
        <p>{{ __('messages.owner.products.master_products.add_first_product') ?? 'Add your first product to get started' }}</p>
      </div>
    @endforelse
  </div>

  {{-- Pagination: real Laravel paginate --}}
  @if($products->hasPages())
    <div class="table-pagination">
      {{ $products->links() }}
    </div>
  @endif
</div>

<style>
  /* Toggle desktop vs mobile */
  .product-responsive .only-desktop { display: block !important; }
  .product-responsive .only-mobile  { display: none !important; }

  @media (max-width: 768px) {
    .product-responsive .only-desktop { display: none !important; }
    .product-responsive .only-mobile  { display: block !important; }
  }

  /* Mobile cards */
  .mobile-product-list{
    padding: 14px;
    display: grid;
    gap: 12px;
  }

  .product-card{
    border: 1px solid rgba(0,0,0,.08);
    border-radius: 16px;
    background: #fff;
    padding: 14px;
    box-shadow: 0 8px 22px rgba(0,0,0,.06);
    margin-bottom: 5px;
  }

  .product-card__top{
    display: flex;
    gap: 12px;
    align-items: flex-start;
  }

  .product-card__avatar img{
    width: 52px;
    height: 52px;
    border-radius: 14px;
    object-fit: cover;
  }

  .product-card__avatar .user-avatar-placeholder{
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    background: rgba(0,0,0,.05);
  }

  .product-card__meta{ flex: 1; min-width: 0; }

  .product-card__name{
    font-weight: 800;
    font-size: 15px;
    line-height: 1.2;
    margin-bottom: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .product-card__sub{
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .chip{
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(0,0,0,.04);
    font-size: 12px;
    color: #555;
    max-width: 100%;
  }

  .chip .material-symbols-outlined{ font-size: 16px; opacity: .85; }
  .chip .chip-text{
    max-width: 210px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .chip-warn{
    background: rgba(255,193,7,.18);
    color: #7a5b00;
  }

  .product-card__price{
    flex: 0 0 auto;
    text-align: right;
    min-width: 120px;
  }
  .product-card__price .price{
    font-weight: 900;
    font-size: 14px;
    line-height: 1.1;
  }
  .product-card__price .qty{
    margin-top: 6px;
    font-size: 12px;
    color: #777;
  }

  .product-card__info{
    margin-top: 12px;
    border-top: 1px dashed rgba(0,0,0,.08);
    padding-top: 12px;
  }

  .info-row{
    display: flex;
    justify-content: space-between;
    gap: 12px;
  }
  .info-row .label{
    color:#9aa0a6;
    font-size:12px;
    font-weight:600;
    flex: 0 0 auto;
  }
  .info-row .value{
    font-size:12px;
    color:#333;
    font-weight:600;
    text-align: right;
    max-width: 70%;
  }
  .info-row .value.options{
    display: -webkit-box;
    -webkit-line-clamp: 2; /* options bisa panjang */
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
  }

  .product-card__actions{
    margin-top: 12px;
    display: flex;
    gap: 8px;
  }

  .btn-card-action{
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,.10);
    background: #fff;
    font-size: 12px;
    font-weight: 700;
  }

  .btn-card-action .material-symbols-outlined{ font-size: 18px; }
  .btn-card-action.danger{ border-color: rgba(174,21,4,.25); color:#ae1504; }
</style>
