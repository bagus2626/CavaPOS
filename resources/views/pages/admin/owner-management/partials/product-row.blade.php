<tr>
    <td class="text-left">
        <span class="text-bold-500">{{ $products->firstItem() + $index }}</span>
    </td>
    <td>
        <div class="media">
            @if ($product->pictures)
                @php
                    $pictures = [];
                    if (is_string($product->pictures)) {
                        $pictures = json_decode($product->pictures, true);
                    } elseif (is_array($product->pictures)) {
                        $pictures = $product->pictures;
                    }

                    $firstImage = is_array($pictures) && !empty($pictures) ? $pictures[0] : null;
                @endphp

                @if ($firstImage && isset($firstImage['path']))
                    <img src="{{ asset($firstImage['path']) }}" alt="{{ $product->name }}" class="rounded-circle mr-1"
                        style="width: 48px; height: 48px; object-fit: cover;">
                @else
                    <div class="rounded mr-1 d-flex align-items-center justify-content-center bg-light"
                        style="width: 48px; height: 48px;">
                        <i class="bx bx-image text-muted font-medium-3"></i>
                    </div>
                @endif
            @else
                <div class="rounded mr-1 d-flex align-items-center justify-content-center bg-light"
                    style="width: 48px; height: 48px;">
                    <i class="bx bx-image text-muted font-medium-3"></i>
                </div>
            @endif
            <div class="media-body">
                <h6 class="mb-0 text-bold-500">{{ $product->name }}</h6>
                <small class="text-muted">{{ $product->product_code }}</small>
            </div>
        </div>
    </td>
    <td>
        <span class="badge badge-primary badge-pill">
            {{ $product->category->category_name ?? 'N/A' }}
        </span>
    </td>
    <td class="text-bold-500">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
    <td>
        @if ($product->always_available_flag)
            <span class="badge badge-light-primary badge-pill">
                <i class="bx bx-infinite"></i> Always
            </span>
        @else
            @php
                $stockQuantity = 0;
                $stockType = $product->stock_type ?? 'direct';
                
                // Calculate stock based on stock_type
                if ($stockType === 'linked') {
                    // For linked stock, use available_linked_quantity
                    $stockQuantity = (float) ($product->available_linked_quantity ?? 0);
                } else {
                    // For direct stock, get from stocks relation
                    if ($product->stock) {
                        $stockQuantity = (float) ($product->stock->quantity ?? 0);
                    }
                }
                
                // Determine CSS class based on stock level
                $stockClass = '';
                if ($stockQuantity <= 0) {
                    $stockClass = 'text-danger font-weight-bold';
                } elseif ($stockQuantity < 10) {
                    $stockClass = 'text-warning font-weight-bold';
                }
            @endphp
            <span class="{{ $stockClass }}">
                @if ($stockQuantity == floor($stockQuantity))
                    {{ number_format($stockQuantity, 0) }}
                @else
                    {{ number_format($stockQuantity, 2) }}
                @endif
            </span>
        @endif
    </td>
    <td>
        @if ($product->is_active)
            <span class="badge badge-success badge-pill">Active</span>
        @else
            <span class="badge badge-danger badge-pill">Inactive</span>
        @endif
    </td>
    <td>
        <a href="#" data-toggle="modal" data-target="#productDetailModal{{ $product->id }}"
            title="View Product Details">
            <i class="badge-circle badge-circle-light-secondary bx bx-show font-medium-1"></i>
        </a>
    </td>
</tr>