<!-- Table Card -->
<div class="modern-card">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">#</th>
                    <th>{{ __('messages.partner.product.all_product.product_name') }}</th>
                    {{-- <th>{{ __('messages.partner.product.all_product.description') }}</th> --}}
                    <th>{{ __('messages.partner.product.all_product.options') }}</th>
                    <th>{{ __('messages.partner.product.all_product.quantity') }}</th>
                    <th>{{ __('messages.partner.product.all_product.price') }}</th>
                    <th class="text-center" style="width: 180px;">
                        {{ __('messages.partner.product.all_product.actions') }}
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
                                <span class="user-name">{{ $product->name }}</span>
                            </div>
                        </td>

                        <!-- Description -->
                        {{-- <td>
                            <span class="text-secondary text-truncate" style="max-width: 200px; display: inline-block;">
                                {{ $product->description ?? '—' }}
                            </span>
                        </td> --}}

                        <!-- Options -->
                        <td>
                            @if($product->parent_options->isEmpty())
                                <span class="text-muted">{{ __('messages.partner.product.all_product.no_options_product') }}</span>
                            @else
                                <span class="text-secondary">
                                    @foreach($product->parent_options as $index => $opt)
                                        {{ $opt->name }}{{ $index < $product->parent_options->count() - 1 ? ', ' : '' }}
                                    @endforeach
                                </span>
                            @endif
                        </td>

                        <!-- Quantity -->
                        <td>
                            @php
                                $qtyAvailable = round($product->quantity_available, 0);
                            @endphp

                            @if($product->stock_type == 'linked')
                                <span class="fw-600">{{ number_format($qtyAvailable, 0) }}</span>
                            @elseif((int) $product->always_available_flag === 1)
                                <span class="text-muted">
                                    {{ __('messages.partner.product.all_product.always_available') }}
                                </span>     
                            @elseif($product->stock)
                                <span class="fw-600">{{ number_format($qtyAvailable, 0) }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>

                        <!-- Price -->
                        <td>
                            <span class="fw-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="{{ route('partner.products.show', $product->id) }}"
                                    class="btn-table-action view"
                                    title="{{ __('messages.partner.product.all_product.detail') }}">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>
                                <a href="{{ route('partner.products.edit', $product->id) }}"
                                    class="btn-table-action edit"
                                    title="{{ __('messages.partner.product.all_product.edit') }}">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                {{-- Uncomment jika delete diperlukan --}}
                                {{-- <button onclick="deleteProduct({{ $product->id }})" 
                                    class="btn-table-action delete"
                                    title="{{ __('messages.partner.product.all_product.delete') }}">
                                    <span class="material-symbols-outlined">delete</span>
                                </button> --}}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">inventory_2</span>
                                <h4>{{ __('messages.partner.product.all_product.no_products') ?? 'No products found' }}</h4>
                                <p>{{ __('messages.partner.product.all_product.add_first_product') ?? 'Add your first product to get started' }}</p>
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