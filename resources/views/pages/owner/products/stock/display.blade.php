@php
    use Illuminate\Support\Str;
@endphp

<link rel="stylesheet" href="{{ asset('css/mobile-owner.css') }}">

{{-- MOBILE HEADER CARD --}}
<div class="modern-card only-mobile mb-4">
    <div class="mobile-header-card">
        <div class="mobile-header-content">
            <div class="mobile-header-left">
                <h2 class="mobile-header-title">{{ __('messages.owner.products.stocks.all_stock') }}</h2>
                <p class="mobile-header-subtitle" id="mobileStockSubtitle">0
                    {{ __('messages.owner.products.stocks.stock_list') }}</p>
            </div>
            <div class="mobile-header-right">
                @if (auth()->user()->image)
                    @php
                        $userImg = Str::startsWith(auth()->user()->image, ['http://', 'https://'])
                            ? auth()->user()->image
                            : asset('storage/' . auth()->user()->image);
                    @endphp
                    <img src="{{ $userImg }}" alt="Profile" class="mobile-header-avatar">
                @else
                    <div class="mobile-header-avatar-placeholder">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="mobile-search-wrapper">
            <div class="mobile-search-box">
                <span class="mobile-search-icon">
                    <span class="material-symbols-outlined">search</span>
                </span>
                <input type="text" class="mobile-search-input" id="mobileSearchInput"
                    placeholder="{{ __('messages.owner.products.stocks.search_placeholder') }}">
                <button type="button" class="mobile-filter-btn" onclick="toggleMobileStockFilter()">
                    <span class="material-symbols-outlined">tune</span>
                </button>
            </div>
        </div>

        {{-- Mobile Location/Outlet Dropdown --}}
        <div class="mobile-category-dropdown">
            <div class="select-wrapper-mobile">
                <form action="{{ route('owner.user-owner.stocks.index') }}" method="GET"
                    id="mobileLocationFilterForm">
                    <select id="mobileLocationFilter" name="filter_location" class="form-control-mobile"
                        onchange="this.form.submit()">
                        <option value="owner" {{ $filterLocation == 'owner' ? 'selected' : '' }}>
                            {{ __('messages.owner.products.stocks.owner_warehouse') }}
                        </option>
                        @foreach ($partners as $partner)
                            <option value="{{ $partner->username }}"
                                {{ $filterLocation == $partner->username ? 'selected' : '' }}>
                                {{ $partner->name }} (Outlet)
                            </option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined select-arrow-mobile">expand_more</span>
                </form>
            </div>
        </div>
    </div>
</div>



{{-- STOCK ACTIONS CARD --}}
<div class="modern-card mb-4">
    <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
        <div class="stock-actions-container">
            <div class="stock-actions-group">
                <a href="{{ route('owner.user-owner.stocks.movements.create-stock-in') }}"
                    class="btn-modern btn-sm-modern btn-secondary-modern">
                    {{ __('messages.owner.products.stocks.stock_in') }}
                </a>

                <a href="{{ route('owner.user-owner.stocks.movements.create-transfer') }}"
                    class="btn-modern btn-sm-modern btn-secondary-modern">
                    {{ __('messages.owner.products.stocks.transfer') }}
                </a>

                <a href="{{ route('owner.user-owner.stocks.movements.create-adjustment') }}"
                    class="btn-modern btn-sm-modern btn-secondary-modern">
                    {{ __('messages.owner.products.stocks.adjustment') }}
                </a>
            </div>

            <div class="stock-history-wrapper">
                <a href="{{ route('owner.user-owner.stocks.movements.index') }}"
                    class="btn-modern btn-sm-modern btn-secondary-modern">
                    <span class="material-symbols-outlined">history</span>
                    {{ __('messages.owner.products.stocks.movement_history') }}
                </a>
            </div>
        </div>
    </div>
</div>


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

{{-- MOBILE FILTER MODAL --}}
<div class="mobile-filter-modal" id="mobileStockFilterModal">
    <div class="filter-modal-backdrop" onclick="closeMobileStockFilter()"></div>
    <div class="filter-modal-content">
        <div class="filter-modal-header">
            <div class="filter-header-left">
                <span class="material-symbols-outlined filter-header-icon">tune</span>
                <h3>Filter Stock</h3>
            </div>
            <button type="button" class="filter-close-btn" onclick="closeMobileStockFilter()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="filter-modal-body">
            {{-- Stock Type Filter --}}
            <div class="filter-divider">
                <span>Stock Type</span>
            </div>
            <div class="modal-filter-pills">
                <div class="modal-pill" onclick="setStockTypeFilter('all')">
                    <div class="pill-left">
                        <div class="pill-icon-wrapper">
                            <span class="material-symbols-outlined">select_all</span>
                        </div>
                        <div class="pill-info">
                            <div class="pill-text">All Stock</div>
                            <div class="pill-subtext">Show everything</div>
                        </div>
                    </div>
                    <div class="pill-right"></div>
                </div>

                <div class="modal-pill active" onclick="setStockTypeFilter('linked')">
                    <div class="pill-left">
                        <div class="pill-icon-wrapper active">
                            <span class="material-symbols-outlined">link</span>
                        </div>
                        <div class="pill-info">
                            <div class="pill-text">Linked Stock</div>
                            <div class="pill-subtext">Connected to products</div>
                        </div>
                    </div>
                    <div class="pill-right">
                        <span class="material-symbols-outlined pill-check">check_circle</span>
                    </div>
                </div>

                <div class="modal-pill" onclick="setStockTypeFilter('direct')">
                    <div class="pill-left">
                        <div class="pill-icon-wrapper">
                            <span class="material-symbols-outlined">inventory</span>
                        </div>
                        <div class="pill-info">
                            <div class="pill-text">Direct Stock</div>
                            <div class="pill-subtext">Standalone items</div>
                        </div>
                    </div>
                    <div class="pill-right"></div>
                </div>
            </div>
        </div>

        <div class="filter-modal-footer">
            <button type="button" class="btn-clear-filter" onclick="clearStockFilters()">
                <span class="material-symbols-outlined">filter_alt_off</span>
                <span>Clear Filters</span>
            </button>
        </div>
    </div>
</div>

<style>
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

  /* Mobile Category/Location Dropdown */
  .mobile-category-dropdown {
      margin-top: 12px;
  }

  .select-wrapper-mobile {
      position: relative;
      width: 100%;
  }

  .form-control-mobile {
      width: 100%;
      padding: 12px 40px 12px 16px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      font-size: 14px;
      background-color: #fff;
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      cursor: pointer;
  }

  .form-control-mobile:focus {
      outline: none;
      border-color: #ae1504;
  }

  .select-arrow-mobile {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      pointer-events: none;
      color: #666;
      font-size: 20px;
  }
  /* Hide floating button when modal is open */
body:has(.mobile-filter-modal.show) .btn-add-outlet-mobile {
    opacity: 0;
    pointer-events: none;
    transform: scale(0.8);
}

.btn-add-outlet-mobile {
    transition: opacity 0.3s, transform 0.3s;
}
</style>

