<!-- Table Card -->
<div class="modern-card">
    <div class="data-table-wrapper">
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
            <tbody id="productTableBody">
                @forelse ($products as $index => $product)
                    <tr data-category="{{ $product->category_id }}" class="table-row">
                        <!-- Number -->
                        <td class="text-center text-muted">{{ $products->firstItem() + $index }}</td>

                        <!-- Product Name with Image -->
                        <td>
                            <div class="user-info-cell">
                                @if(!empty($product->pictures) && is_array($product->pictures))
                                    @php 
                                        $firstPicture = $product->pictures[0];
                                        $src = asset($firstPicture['path']); 
                                    @endphp
                                    <img src="{{ $src }}" alt="{{ $product->name }}" class="user-avatar" loading="lazy">
                                @else
                                    <div class="user-avatar-placeholder">
                                        <span class="material-symbols-outlined">inventory_2</span>
                                    </div>
                                @endif
                                <span class="data-name">{{ $product->name }}</span>
                            </div>
                        </td>

                        <!-- Options -->
                        <td>
                            @if($product->parent_options->isEmpty())
                                <span class="text-muted">{{ __('messages.owner.products.master_products.no_options') }}</span>
                            @else
                                <span class="text-secondary">{{ $product->parent_options->pluck('name')->implode(', ') }}</span>
                            @endif
                        </td>

                        <!-- Quantity -->
                        <td>
                            <span class="fw-600">{{ $product->quantity }}</span>
                        </td>

                        <!-- Price -->
                        <td>
                            <span class="fw-600">Rp {{ number_format($product->price) }}</span>
                        </td>

                        <!-- Promo -->
                        <td>
                            @if($product->promotion)
                                <span class="badge-modern badge-warning">
                                    {{ $product->promotion->promotion_name }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <!-- Actions -->
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
                                <button onclick="deleteProduct({{ $product->id }})" 
                                    class="btn-table-action delete"
                                    title="{{ __('messages.owner.products.master_products.delete') }}">
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
                                <h4>{{ __('messages.owner.products.master_products.no_products') ?? 'No products found' }}</h4>
                                <p>{{ __('messages.owner.products.master_products.add_first_product') ?? 'Add your first product to get started' }}</p>
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
            {{ $products->links() }}
        </div>
    @endif
</div>