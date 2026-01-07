@php
    use Illuminate\Support\Str;
@endphp

<!-- Table Card -->
<div class="modern-card">
    <div class="data-table-wrapper">
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
                    <th class="text-center" style="width: 180px;">
                        {{ __('messages.owner.products.outlet_products.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                @forelse ($products as $index => $p)
                    <tr data-category="{{ $p->category_id }}" class="table-row">
                        <!-- Number -->
                        <td class="text-center text-muted">{{ $products->firstItem() + $index }}</td>

                        <!-- Product with Image -->
                        <td>
                            <div class="user-info-cell">
                                <div class="position-relative" style="width:40px; height:40px;">
                                    @if(!empty($p->pictures) && isset($p->pictures[0]['path']))
                                        <img src="{{ asset($p->pictures[0]['path']) }}"
                                             alt="{{ $p->name ?? $p->product_name }}"
                                             class="user-avatar"
                                             style="width:40px; height:40px; object-fit:cover; border-radius:6px;"
                                             loading="lazy">
                                    @else
                                        <div class="user-avatar-placeholder">
                                            <span class="material-symbols-outlined">image</span>
                                        </div>
                                    @endif

                                    @if($p->is_hot_product)
                                        <span style="
                                            position:absolute;
                                            top:-6px;
                                            right:-6px;
                                            background:#ff5722;
                                            color:white;
                                            padding:2px 6px;
                                            border-radius:8px;
                                            font-size:10px;
                                            font-weight:600;
                                            box-shadow:0 2px 6px rgba(0,0,0,0.2);
                                        ">
                                            HOT
                                        </span>
                                    @endif
                                </div>
                                <span class="user-name">{{ $p->name ?? $p->product_name }}</span>
                            </div>
                        </td>

                        <!-- Category -->
                        <td>
                            <span class="badge-modern badge-info">
                                {{ $p->category->category_name ?? '-' }}
                            </span>
                        </td>

                        <!-- Stock -->
                        <td>
                            @php
                                $qtyAvailable = $p->quantity_available;
                                $isQtyZero = $qtyAvailable < 1 && $qtyAvailable !== 999999999;
                            @endphp

                            @if($p->stock_type == 'linked')
                                @if ($qtyAvailable === 999999999)
                                    <span class="text-muted" style="font-style: italic;">
                                        {{ __('messages.owner.products.outlet_products.always_available') }}
                                    </span>
                                @elseif ($isQtyZero)
                                    <span class="badge-modern badge-danger">Out of Stock</span>
                                @else
                                    <strong>{{ number_format(floor($qtyAvailable), 0) }}</strong>
                                    <span class="text-muted small">pcs</span>
                                @endif

                            @elseif((int) $p->always_available_flag === 1)
                                <span class="text-muted">
                                    {{ __('messages.owner.products.outlet_products.always_available') }}
                                </span>

                            @elseif($p->stock)
                                @if ($isQtyZero)
                                    <span class="badge-modern badge-danger">0</span>
                                @else
                                    <strong>
                                        {{ rtrim(rtrim(number_format($qtyAvailable, 2, ',', '.'), '0'), ',') }}
                                    </strong>
                                    <span class="text-muted small">{{ $p->stock->displayUnit->unit_name ?? 'unit' }}</span>
                                @endif

                            @else
                                <span class="badge-modern badge-danger">0</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="text-center">
                            @php
                                $active = (int) ($p->is_active ?? 1);
                            @endphp
                            <span class="badge-modern badge-{{ $active ? 'success' : 'danger' }}">
                                {{ $active ? __('messages.owner.products.outlet_products.active') : __('messages.owner.products.outlet_products.inactive') }}
                            </span>
                        </td>

                        <!-- Price -->
                        <td>
                            <span class="fw-600">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                        </td>

                        <!-- Promo -->
                        <td>
                            @if($p->promotion)
                                <span class="badge-modern badge-warning">
                                    {{ $p->promotion->promotion_name }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <!-- Actions -->
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

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="table-pagination">
            {{ $products->appends(['outlet_id' => $currentOutletId])->links() }}
        </div>
    @endif
</div>