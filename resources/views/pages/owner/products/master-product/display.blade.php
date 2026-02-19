@php
    use Illuminate\Support\Str;
@endphp

<link rel="stylesheet" href="{{ asset('css/mobile-owner.css') }}">
<div class="modern-card">

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
                                @if ($img)
                                    <img src="{{ asset($img) }}" alt="{{ $product->name }}" class="user-avatar"
                                        loading="lazy">
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
                            @if ($product->parent_options->isEmpty())
                                <span
                                    class="text-muted">{{ __('messages.owner.products.master_products.no_options') }}</span>
                            @else
                                <span class="text-secondary">{{ $optionsText }}</span>
                            @endif
                        </td>

                        <td><span class="fw-600">{{ $product->quantity }}</span></td>

                        <td><span class="fw-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span></td>

                        <td>
                            @if ($product->promotion)
                                <span
                                    class="badge-modern badge-warning">{{ $product->promotion->promotion_name }}</span>
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
                                <button type="button" onclick="deleteProduct({{ $product->id }})"
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
                                <h4>{{ __('messages.owner.products.master_products.no_products') ?? 'No products found' }}
                                </h4>
                                <p>{{ __('messages.owner.products.master_products.add_first_product') ?? 'Add your first product to get started' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- =======================
    MOBILE: HEADER + SEARCH + FILTER + CARDS
  ======================= --}}
    <div class="only-mobile">
        {{-- Mobile Header with Avatar & Search --}}
        <div class="mobile-header-section">
            <div class="mobile-header-card">
                <div class="mobile-header-content">
                    <div class="mobile-header-left">
                        <h2 class="mobile-header-title">Product Catalog</h2>
                        <p class="mobile-header-subtitle">{{ $products->total() }} Total Products</p>
                    </div>
                    <div class="mobile-header-right">
                        <div class="mobile-header-avatar-placeholder">
                            <span class="material-symbols-outlined">shopping_bag</span>
                        </div>
                    </div>
                </div>

                {{-- Mobile Search Form --}}
                <div class="mobile-search-wrapper">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="mobile-search-box">
                            <span class="mobile-search-icon">
                                <span class="material-symbols-outlined">search</span>
                            </span>
                            <input type="text" name="q" value="{{ request('q') }}"
                                class="mobile-search-input" placeholder="Search products..."
                                oninput="searchFilter(this, 600)">
                            <button type="button" class="mobile-filter-btn" onclick="toggleMobileFilter()">
                                <span class="material-symbols-outlined">tune</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Filter Pills --}}
        {{-- <div class="mobile-filter-pills">
  <div class="filter-pills-container">
    <a href="{{ route('owner.user-owner.master-products.index', array_filter(['q' => request('q')])) }}"
      class="filter-pill {{ !request('category') || request('category') == 'all' ? 'active' : '' }}">
      {{ __('messages.owner.products.master_products.all') }}
    </a>
    @foreach ($categories as $category)
      <a href="{{ route('owner.user-owner.master-products.index', array_filter(['q' => request('q'), 'category' => $category->id])) }}"
        class="filter-pill {{ (string)request('category') === (string)$category->id ? 'active' : '' }}">
        {{ $category->category_name }}
      </a>
    @endforeach
  </div>
</div> --}}

        {{-- Mobile Product List (MENGGUNAKAN CLASS DARI CSS UNIVERSAL) --}}
        <div class="mobile-employee-list">
            @forelse ($products as $product)
                @php
                    $img = null;
                    if (!empty($product->pictures) && is_array($product->pictures)) {
                        $img = $product->pictures[0]['path'] ?? null;
                    }
                @endphp

                <div class="employee-card-wrapper">
                    {{-- Swipe Actions Background --}}
                    <div class="swipe-actions">
                        <a href="{{ route('owner.user-owner.master-products.edit', $product->id) }}"
                            class="swipe-action edit">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                        <button type="button" onclick="deleteProduct({{ $product->id }})"
                            class="swipe-action delete">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>

                    {{-- Card Content --}}
                    <a href="{{ route('owner.user-owner.master-products.show', $product->id) }}"
                        class="employee-card-link">
                        <div class="employee-card-clickable">
                            <div class="employee-card__left">
                                <div class="employee-card__avatar">
                                    @if ($img)
                                        <img src="{{ asset($img) }}" alt="{{ $product->name }}" loading="lazy">
                                    @else
                                        <div class="user-avatar-placeholder">
                                            <span class="material-symbols-outlined">inventory_2</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="employee-card__info">
                                    <div class="employee-card__name">{{ $product->name }}</div>
                                    <div class="employee-card__details">
                                        <span class="detail-text">{{ $product->category->category_name ?? '-' }}</span>
                                        <span class="detail-separator">•</span>
                                        <span class="detail-text">Qty: {{ $product->quantity }}</span>
                                    </div>
                                    <div style="font-weight: 700; font-size: 15px; color: #b3311d; margin-top: 6px;">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <div class="employee-card__right">
                                <span class="material-symbols-outlined chevron">chevron_right</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="table-empty-state">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <h4>{{ __('messages.owner.products.master_products.no_products') ?? 'No products found' }}</h4>
                    <p>{{ __('messages.owner.products.master_products.add_first_product') ?? 'Add your first product to get started' }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- =======================
    PAGINATION
  ======================= --}}
    @if ($products->hasPages())
        <div class="table-pagination">
            {{ $products->links() }}
        </div>
    @endif

</div>

{{-- Floating Add Button (Mobile Only) --}}
<a href="{{ route('owner.user-owner.master-products.create') }}" class="btn-add-employee-mobile">
    <span class="material-symbols-outlined">add</span>
</a>

{{-- Mobile Filter Modal --}}
<div id="mobileFilterModal" class="mobile-filter-modal">
    <div class="filter-modal-backdrop" onclick="closeMobileFilter()"></div>
    <div class="filter-modal-content">
        <div class="filter-modal-header">
            <div class="filter-header-left">
                <span class="material-symbols-outlined filter-header-icon">tune</span>
                <h3>Filter by Category</h3>
            </div>
            <button type="button" class="filter-close-btn" onclick="closeMobileFilter()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="filter-modal-body">
            <div class="modal-filter-pills">
                <a href="{{ route('owner.user-owner.master-products.index', array_filter(['q' => request('q')])) }}"
                    class="modal-pill {{ !request('category') || request('category') == 'all' ? 'active' : '' }}"
                    onclick="closeMobileFilter()">
                    <div class="pill-left">
                        <div
                            class="pill-icon-wrapper {{ !request('category') || request('category') == 'all' ? 'active' : '' }}">
                            <span class="material-symbols-outlined">inventory_2</span>
                        </div>
                        <div class="pill-info">
                            <span class="pill-text">{{ __('messages.owner.products.master_products.all') }}</span>
                            <span class="pill-subtext">View all products</span>
                        </div>
                    </div>
                    <div class="pill-right">
                        @if (!request('category') || request('category') == 'all')
                            <span class="material-symbols-outlined pill-check">check_circle</span>
                        @endif
                    </div>
                </a>

                <div class="filter-divider">
                    <span>Categories</span>
                </div>

                @foreach ($categories as $category)
                    <a href="{{ route('owner.user-owner.master-products.index', array_filter(['q' => request('q'), 'category' => $category->id])) }}"
                        class="modal-pill {{ (string) request('category') === (string) $category->id ? 'active' : '' }}"
                        onclick="closeMobileFilter()">
                        <div class="pill-left">
                            <div
                                class="pill-icon-wrapper {{ (string) request('category') === (string) $category->id ? 'active' : '' }}">
                                <span class="material-symbols-outlined">category</span>
                            </div>
                            <div class="pill-info">
                                <span class="pill-text">{{ $category->category_name }}</span>
                                <span class="pill-subtext">Filter by {{ strtolower($category->category_name) }}</span>
                            </div>
                        </div>
                        <div class="pill-right">
                            @if ((string) request('category') === (string) $category->id)
                                <span class="material-symbols-outlined pill-check">check_circle</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="filter-modal-footer">
            <button type="button" class="btn-clear-filter"
                onclick="window.location.href='{{ route('owner.user-owner.master-products.index', array_filter(['q' => request('q')])) }}'">
                <span class="material-symbols-outlined">restart_alt</span>
                Clear Filter
            </button>
        </div>
    </div>
</div>

<script>
    function toggleMobileFilter() {
        const modal = document.getElementById('mobileFilterModal');
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeMobileFilter() {
        const modal = document.getElementById('mobileFilterModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const backdrop = document.querySelector('.filter-modal-backdrop');
        if (backdrop) {
            backdrop.addEventListener('click', closeMobileFilter);
        }
    });

    let searchTimeout;

    function searchFilter(input, delay) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            input.form.submit();
        }, delay);
    }
</script>
