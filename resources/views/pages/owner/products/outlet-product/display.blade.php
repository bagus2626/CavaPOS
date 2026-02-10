@php
    use Illuminate\Support\Str;
@endphp

<div class="modern-card outlet-products-responsive">

    {{-- =======================
    DESKTOP: TABLE (SAMA SEPERTI SEBELUMNYA)
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
                        $img =
                            !empty($p->pictures) && isset($p->pictures[0]['path'])
                                ? asset($p->pictures[0]['path'])
                                : null;

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
                        } elseif ((int) $p->always_available_flag === 1) {
                            $stockDisplay = __('messages.owner.products.outlet_products.always_available');
                        } elseif ($p->stock) {
                            if ($isQtyZero) {
                                $stockDisplay = '0';
                            } else {
                                $unit = $p->stock->displayUnit->unit_name ?? 'unit';
                                $stockDisplay =
                                    rtrim(rtrim(number_format($qtyAvailable, 2, ',', '.'), '0'), ',') . ' ' . $unit;
                            }
                        }
                        $active = (int) ($p->is_active ?? 1);
                    @endphp

                    <tr class="table-row">
                        <td class="text-center text-muted">{{ $products->firstItem() + $index }}</td>

                        <td>
                            <div class="user-info-cell">
                                <div class="position-relative" style="width:40px;height:40px;">
                                    @if ($img)
                                        <img src="{{ $img }}" alt="{{ $name }}" class="user-avatar"
                                            style="width:40px;height:40px;object-fit:cover;border-radius:10px;"
                                            loading="lazy">
                                    @else
                                        <div class="user-avatar-placeholder">
                                            <span class="material-symbols-outlined">image</span>
                                        </div>
                                    @endif

                                    @if ((int) $p->is_hot_product === 1)
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
                            @if ($stockDisplay === __('messages.owner.products.outlet_products.always_available'))
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
                            @if ($p->promotion)
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
                                <button onclick="deleteProduct({{ $p->id }})" class="btn-table-action delete"
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
                                <p>{{ __('messages.owner.products.outlet_products.add_first_product') ?? 'Add your first product to get started' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- =======================
    MOBILE: DESAIN 2 - IMAGE FULL WIDTH (2 KOLOM)
  ======================= --}}
    <div class="only-mobile mobile-product-list-v2">
        <div class="product-grid-v2">
            @forelse ($products as $p)
                @php
                    $name = $p->name ?? $p->product_name;
                    $img =
                        !empty($p->pictures) && isset($p->pictures[0]['path']) ? asset($p->pictures[0]['path']) : null;

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
                    } elseif ((int) $p->always_available_flag === 1) {
                        $stockDisplay = __('messages.owner.products.outlet_products.always_available');
                    } elseif ($p->stock) {
                        if ($isQtyZero) {
                            $stockDisplay = '0';
                        } else {
                            $unit = $p->stock->displayUnit->unit_name ?? 'unit';
                            $stockDisplay =
                                rtrim(rtrim(number_format($qtyAvailable, 2, ',', '.'), '0'), ',') . ' ' . $unit;
                        }
                    }
                    $active = (int) ($p->is_active ?? 1);
                @endphp

                <div class="product-card-v2">
                    <!-- Image Header -->
                    <div class="card-image-header">
                        @if ($img)
                            <img src="{{ $img }}" alt="{{ $name }}" loading="lazy">
                        @else
                            <div class="image-placeholder-v2">
                                <span class="material-symbols-outlined">image</span>
                            </div>
                        @endif

                        <div class="image-overlay">
                            @if ((int) $p->is_hot_product === 1)
                                <span class="hot-tag">🔥</span>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body-v2">
                        <div class="category-status-row">
                            <div class="category-tag">{{ $p->category->category_name ?? '-' }}</div>
                            <span class="status-tag {{ $active ? 'active' : 'inactive' }}">
                                {{ $active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <h3 class="product-title-v2">{{ $name }}</h3>

                        <div class="info-row">
                            <div class="info-item-compact">
                                <span class="material-symbols-outlined">inventory</span>
                                <span>{{ $stockDisplay }}</span>
                            </div>
                        </div>

                        <div class="price-row">
                            <span class="price-label">Price</span>
                            <span class="price-value">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                        </div>

                        @if ($p->promotion)
                            <div class="promo-compact">
                                <span class="material-symbols-outlined">local_offer</span>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="action-buttons-v2">
                            <a href="{{ route('owner.user-owner.outlet-products.edit', $p->id) }}"
                                class="btn-v2 btn-edit">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                            <button onclick="deleteProduct({{ $p->id }})" class="btn-v2 btn-delete">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="table-empty-state" style="padding: 24px; grid-column: 1 / -1;">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <h4>{{ __('messages.owner.products.outlet_products.no_product_yet') }}</h4>
                    <p>{{ __('messages.owner.products.outlet_products.add_first_product') ?? 'Add your first product to get started' }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if ($products->hasPages())
        <div class="table-pagination">
            {{ $products->links() }}
        </div>
    @endif
</div>

<style>
    .outlet-products-responsive .only-desktop {
        display: block;
    }

    .outlet-products-responsive .only-mobile {
        display: none;
    }

    @media (max-width: 768px) {
        .outlet-products-responsive .only-desktop {
            display: none;
        }

        .outlet-products-responsive .only-mobile {
            display: block;
        }
    }

    /* ============================================
     DESAIN 2: IMAGE FULL WIDTH (2 KOLOM GRID)
  ============================================ */
    .mobile-product-list-v2 {
        padding: 12px;
    }

    .product-grid-v2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .product-card-v2 {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s;
    }

    .product-card-v2:active {
        transform: scale(0.98);
    }

    .card-image-header {
        position: relative;
        width: 100%;
        height: 120px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        overflow: hidden;
    }

    .card-image-header img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-placeholder-v2 {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .image-placeholder-v2 .material-symbols-outlined {
        font-size: 48px;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        padding: 8px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), transparent);
    }

    .hot-tag {
        background: #ff5722;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .status-tag {
        display: flex;
        align-items: center;
        gap: 4px;
        background: rgba(255, 255, 255, 0.95);
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
    }

    .status-tag.active {
        color: #065f46;
    }

    .status-tag.inactive {
        color: #991b1b;
    }

    .card-body-v2 {
        padding: 10px;
    }

    .category-status-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        gap: 6px;
    }

    .category-tag {
        display: inline-block;
        font-size: 9px;
        color: #6b7280;
        background: #f3f4f6;
        padding: 3px 8px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .status-tag {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        flex-shrink: 0;
    }

    .status-tag.active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-tag.inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .product-title-v2 {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 8px 0;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 34px;
    }

    .info-row {
        margin-bottom: 8px;
    }

    .info-item-compact {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: #6b7280;
        background: #f9fafb;
        padding: 6px 8px;
        border-radius: 6px;
    }

    .info-item-compact .material-symbols-outlined {
        font-size: 16px;
    }

    .price-row {
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin-bottom: 8px;
    }

    .price-label {
        font-size: 9px;
        color: #9ca3af;
        text-transform: uppercase;
    }

    .price-value {
        font-size: 13px;
        font-weight: 700;
        color: #b3311d;
    }

    .promo-compact {
        background: #fef3c7;
        color: #92400e;
        padding: 6px;
        border-radius: 6px;
        margin-bottom: 8px;
        display: inline-flex;
    }

    .promo-compact .material-symbols-outlined {
        font-size: 16px;
    }

    .action-buttons-v2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        padding-top: 8px;
        border-top: 1px solid #f3f4f6;
    }

    .btn-v2 {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-v2 .material-symbols-outlined {
        font-size: 18px;
    }

    .btn-edit {
        background: #eff6ff;
        color: #1e40af;
    }

    .btn-edit:active {
        background: #dbeafe;
    }

    .btn-delete {
        background: #fef2f2;
        color: #991b1b;
    }

    .btn-delete:active {
        background: #fee2e2;
    }
</style>
