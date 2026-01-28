<!-- Filter Navigation Tabs -->
<div class="mb-4">
    <ul class="nav nav-tabs nav-tabs-modern" role="tablist">
        {{-- <li class="nav-item">
            <a class="nav-link active" 
               data-filter-type="all" 
               href="#" 
               role="tab">
                {{ __('messages.owner.products.stocks.all_stock') }}
            </a>
        </li> --}}
        <li class="nav-item">
            <a class="nav-link" 
               data-filter-type="linked" 
               href="#" 
               role="tab">
                {{ __('messages.owner.products.stocks.raw_materials') }}
            </a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link" 
               data-filter-type="direct" 
               href="#" 
               role="tab">
                {{ __('messages.owner.products.stocks.products') }}
            </a>
        </li> --}}
    </ul>
</div>


<!-- Table Card -->
<div class="modern-card">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">#</th>
                    <th>{{ __('messages.owner.products.stocks.stock_code') }}</th>
                    <th>{{ __('messages.owner.products.stocks.stock_name') }}</th>
                    <th>{{ __('messages.owner.products.stocks.stock_quantity') }}</th>
                    <th>{{ __('messages.owner.products.stocks.unit') }}</th>
                    <th>{{ __('messages.owner.products.stocks.last_price_unit') }}</th>
                    <th class="text-center" style="width: 160px;">
                        {{ __('messages.owner.products.stocks.actions') }}
                    </th>
                </tr>
            </thead>

            <tbody id="stockTableBody">
                @forelse ($stocks as $index => $stock)
                    <tr class="table-row"
                        data-type="{{ $stock->type }}"
                        data-stock_type="{{ $stock->stock_type }}"
                        data-partner-type="{{ $stock->partner_product_id && !$stock->partner_product_option_id ? 'product' : ($stock->partner_product_id && $stock->partner_product_option_id ? 'option' : 'none') }}">

                        <!-- Number -->
                        <td class="text-center text-muted">
                            {{ $stocks->firstItem() + $index }}
                        </td>

                        <!-- Stock Code -->
                        <td class="mono fw-600">
                            {{ $stock->stock_code }}
                        </td>

                        <!-- Stock Name -->
                        <td>
                            <span class="fw-600">{{ $stock->stock_name }}</span>
                        </td>

                        <!-- Quantity -->
                        <td>
                            {{ number_format($stock->display_quantity, 2, ',', '.') }}
                        </td>

                        <!-- Unit -->
                        <td>
                            @if($stock->displayUnit)
                                <span class="badge-modern badge-info">
                                    {{ $stock->displayUnit->unit_name }}
                                </span>
                            @else
                                <span class="text-muted small">
                                    ({{ __('messages.owner.products.stocks.base_unit') }})
                                </span>
                            @endif
                        </td>

                        <!-- Last Price -->
                        <td>
                            <span class="fw-600">
                                {{ $stock->last_price_per_unit }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <div class="table-actions">
                                <button onclick="deleteStock({{ $stock->id }})"
                                    class="btn-table-action delete"
                                    title="{{ __('messages.owner.products.stocks.delete') }}">
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
                                <h4>{{ __('messages.owner.products.stocks.no_stock_found') }}</h4>
                                <p>{{ __('messages.owner.products.stocks.add_first_stock') ?? 'Add your first stock to get started' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($stocks->hasPages())
        <div class="table-pagination">
            {{ $stocks->links() }}
        </div>
    @endif
</div>
