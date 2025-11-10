<div class="modal fade text-left" id="productDetailModal{{ $product->id }}" tabindex="-1" role="dialog" 
     aria-labelledby="productDetailModalLabel{{ $product->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h3 class="modal-title text-white" id="productDetailModalLabel{{ $product->id }}">
                    <i class="bx bx-package"></i> Product Details
                </h3>
            </div>
            <div class="modal-body">
                <!-- Product Basic Info -->
                <div class="border-bottom pb-2 mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="text-bold-600 mb-50">{{ $product->name }}</h4>
                            <p class="text-muted mb-0">
                                <small>{{ $product->product_code }}</small>
                            </p>
                        </div>
                        <h3 class="text-primary text-bold-700 mb-0">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </h3>
                    </div>
                    
                    <div class="mt-1">
                        <span class="badge badge-primary badge-pill">
                            {{ $product->category->category_name ?? 'N/A' }}
                        </span>
                        @if ($product->is_active)
                            <span class="badge badge-success badge-pill">Active</span>
                        @else
                            <span class="badge badge-danger badge-pill">Inactive</span>
                        @endif
                        
                        @if ($product->always_available_flag)
                            <span class="badge badge-light-primary badge-pill">
                                <i class="bx bx-infinite"></i> Always Available
                            </span>
                        @else
                            @php
                                $stockClass = '';
                                if ($product->quantity <= 0) {
                                    $stockClass = 'badge-danger';
                                } elseif ($product->quantity < 10) {
                                    $stockClass = 'badge-warning';
                                } else {
                                    $stockClass = 'badge-success';
                                }
                            @endphp
                            <span class="badge {{ $stockClass }} badge-pill">
                                Stock: {{ number_format($product->quantity) }}
                            </span>
                        @endif
                    </div>

                    @if ($product->description)
                        <p class="text-muted mt-1 mb-0">{{ $product->description }}</p>
                    @endif
                </div>

                <!-- Product Options -->
                @if ($product->parent_options && $product->parent_options->count() > 0)
                    <div class="mb-0">
                        <h5 class="text-bold-600 mb-1">Product Options</h5>
                        
                        @foreach ($product->parent_options as $parentOption)
                            <div class="mb-2">
                                <!-- Parent Option Header -->
                                <div class="mb-1">
                                    <span class="text-bold-500">{{ $parentOption->name }}</span>
                                </div>
                                
                                <!-- Child Options List -->
                                @if ($parentOption->options && $parentOption->options->count() > 0)
                                    <div class="ml-3">
                                        @foreach ($parentOption->options as $option)
                                            <div class="d-flex justify-content-between align-items-center py-25">
                                                <span class="text-muted">
                                                    + {{ $option->name }}
                                                </span>
                                                <span class="text-bold-500">
                                                    Rp {{ number_format($option->price, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bx bx-info-circle"></i> This product has no customization options.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Close</span>
                </button>
            </div>
        </div>
    </div>
</div>