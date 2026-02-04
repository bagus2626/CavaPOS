<!-- Filter Navigation Tabs -->
<div class="mb-4">
  <ul class="nav nav-tabs nav-tabs-modern" role="tablist" id="stockFilterTabs">
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
<div class="modern-card stock-responsive">

  {{-- =======================
    DESKTOP: TABLE
  ======================= --}}
  <div class="data-table-wrapper only-desktop">
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
        {{-- 
        @forelse ($stocks as $index => $stock)
            <tr class="table-row"
                data-type="{{ $stock->type }}"
                data-stock_type="{{ $stock->stock_type }}"
                data-partner-type="{{ $stock->partner_product_id && !$stock->partner_product_option_id ? 'product' : ($stock->partner_product_id && $stock->partner_product_option_id ? 'option' : 'none') }}">
              <td class="text-center text-muted">
                  {{ $stocks->firstItem() + $index }}
              </td>
              <td class="mono fw-600">
                  {{ $stock->stock_code }}
              </td>
              <td>
                  <span class="fw-600">{{ $stock->stock_name }}</span>
              </td>
              <td>
                  {{ number_format($stock->display_quantity, 2, ',', '.') }}
              </td>
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
              <td>
                  <span class="fw-600">
                      {{ $stock->last_price_per_unit }}
                  </span>
              </td>
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
        --}}
      </tbody>
    </table>
  </div>

  {{-- =======================
    MOBILE: CARDS
  ======================= --}}
  <div class="only-mobile mobile-stock-list" id="stockMobileList">
    {{-- di-render via JS --}}
  </div>

  {{-- Pagination (JS) --}}
  <div class="table-pagination" id="stockPagination"></div>
</div>

<style>
  .stock-responsive .only-desktop{ display:block; }
  .stock-responsive .only-mobile{ display:none; }

  @media (max-width: 768px){
    .stock-responsive .only-desktop{ display:none; }
    .stock-responsive .only-mobile{ display:block; }
  }

  /* MOBILE CARDS */
  .mobile-stock-list{
    padding: 14px;
    display: grid;
    gap: 12px;
  }
  .stock-card{
    border: 1px solid rgba(0,0,0,.08);
    background: #fff;
    border-radius: 16px;
    padding: 14px;
    box-shadow: 0 10px 24px rgba(0,0,0,.06);
    margin-bottom: 5px;
  }
  .stock-card__top{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 12px;
  }
  .stock-card__title{
    min-width:0;
  }
  .stock-card__code{
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-weight: 800;
    font-size: 12px;
    opacity: .9;
  }
  .stock-card__name{
    font-weight: 900;
    font-size: 14px;
    margin-top: 4px;
    line-height: 1.25;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .stock-card__bottom{
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px dashed rgba(0,0,0,.10);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 10px;
    flex-wrap: wrap;
  }
  .stock-chip{
    display:inline-flex;
    align-items:center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(0,0,0,.04);
    font-size: 12px;
    color: #555;
  }
  .stock-chip .material-symbols-outlined{ font-size: 16px; opacity:.85; }
  .stock-actions{
    display:flex;
    gap: 8px;
  }
  .btn-card-action{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap: 6px;
    padding: 10px 12px;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,.10);
    background:#fff;
    font-size: 12px;
    font-weight: 800;
    white-space: nowrap;
  }
  .btn-card-action.danger{
    border-color: rgba(174,21,4,.25);
    color: #ae1504;
  }
</style>

