{{-- resources/views/pages/employee/cashier/dashboard/tabs/pembelian.blade.php --}}

{{-- Konten utama --}}
<div class="px-0 pt-0 relative z-10">
  {{-- Tempelkan blok ini DI DALAM container yang di-scroll (mis. di awal tab "Pembelian") --}}
  <div class="sticky top-0 z-30 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 border-b border-gray-200">
    <div class="w-full pt-7">
      <div class="px-2 flex flex-col md:flex-row md:items-center md:gap-3">

        {{-- Kategori: scroll horizontal, berhenti sebelum search --}}
        <div class="category-bar md:flex-1 md:min-w-0 overflow-x-auto overflow-y-hidden pr-1 mb-4">
          <div id="categoryWrapper" class="category-track inline-flex items-center gap-2 min-w-max">
            <button type="button"
                    class="filter-btn px-4 py-2 text-sm rounded-md active cursor-pointer shrink-0"
                    data-category="all">
              All
            </button>
            @foreach($categories->sortBy('category_order') as $category)
              <button type="button"
                      class="filter-btn px-4 py-2 text-sm rounded-md cursor-pointer shrink-0"
                      data-category="{{ $category->id }}">
                {{ $category->category_name }}
              </button>
            @endforeach
          </div>
        </div>

        {{-- Searchbar: fixed di kanan (desktop), turun ke bawah (mobile) --}}
        <div class="mt-2 md:mt-0 md:flex-none md:w-80 md:mr-3 mb-4">
          <div class="relative">
            <input
              id="menuSearch"
              type="search"
              placeholder="Cari menu… (nama / deskripsi)"
              class="w-full h-10 rounded-md border border-gray-300 bg-white px-3 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-choco/40"
              autocomplete="off"
            />
            <button
              type="button"
              id="menuSearchClear"
              class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center justify-center w-6 h-6 rounded hover:bg-gray-100 text-gray-500"
              aria-label="Clear"
              title="Clear"
              style="display:none"
            >
              ×
            </button>
          </div>
        </div>
        <div class="pointer-events-none absolute inset-y-0 right-[calc(theme(spacing.2)+theme(spacing.80))] hidden md:block w-6 bg-gradient-to-l from-white to-transparent">
        </div>

      </div>
    </div>
  </div>

  @php
      $productsByCategory = $partner_products
          ->sortBy(function ($product) {
              return $product->category->category_order ?? 99999;
          })
          ->groupBy('category_id');

      $hotProducts = $partner_products->filter(function ($p) {
          return $p->is_hot_product;
      });
  @endphp

  <div class="flex flex-col" id="menu-container">
    {{-- Segmen Hot Products --}}
    @if($hotProducts->count())
        <div class="hot-products-group bg-amber-50 border-b border-amber-200 pb-2">
            <p class="px-4 pt-3 mb-2 text-sm font-semibold text-amber-800 flex items-center gap-2">
                🔥 <span>Hot Products</span>
            </p>

            <div class="flex flex-col">
                @foreach($hotProducts as $product)
                    @php
                        $firstImage = $product->pictures[0]['path'] ?? null;

                        $promo = $product->promotion;
                        $basePrice = (float) $product->price;
                        $discountedBase = $basePrice;
                        $hasPromo = false;

                        if ($promo) {
                            if ($promo->promotion_type === 'percentage') {
                                $discountedBase = max(0, $basePrice * (1 - $promo->promotion_value / 100));
                            } else {
                                $discountedBase = max(0, $basePrice - (float) $promo->promotion_value);
                            }
                            $hasPromo = $discountedBase < $basePrice;
                        }

                        $promoBadge = null;
                        if ($promo) {
                            $promoBadge = $promo->promotion_type === 'percentage'
                                ? '-' . rtrim(rtrim(number_format($promo->promotion_value, 2, ',', '.'), '0'), ',') . '%'
                                : '-Rp ' . number_format($promo->promotion_value, 0, ',', '.');
                        }
                    @endphp

                    <div
                        @class([
                            'menu-item menu-item-hot bg-white flex flex-row transition hover:shadow-lg border border-amber-100 rounded-xl px-3',
                            'grayscale' => $product->quantity_available < 1 && $product->always_available_flag === 0,
                        ])
                        data-category="{{ $product->category_id }}"
                    >
                        {{-- Gambar + badge PROMO + badge HOT --}}
                        <div class="w-24 h-24 flex-shrink-0 rounded-lg m-2 overflow-hidden relative">
                            @if($firstImage)
                                <img src="{{ asset($firstImage) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif

                            @if($hasPromo && $promoBadge)
                                <span class="absolute top-1 left-1 bg-red-600 text-white text-[11px] font-semibold px-2 py-0.5 rounded">
                                    {{ $promoBadge }}
                                </span>
                            @endif

                            <span class="absolute top-1 right-1 bg-orange-600 text-white text-[10px] font-semibold px-2 py-0.5 rounded-full shadow">
                                HOT
                            </span>
                        </div>

                        {{-- Info produk --}}
                        <div class="ml-4 flex-1 flex flex-col justify-between py-2">
                            <div>
                                <h5 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                                    {{ $product->name }}
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-medium">
                                        {{ $product->category->category_name ?? '' }}
                                    </span>
                                </h5>
                                <p class="text-gray-500 text-xs mb-1 line-clamp-2">
                                    {{ $product->description }}
                                </p>

                                @if($hasPromo)
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-xs text-gray-500 line-through">
                                            Rp {{ number_format($basePrice, 0, ',', '.') }}
                                        </span>
                                        <span class="text-sm font-bold text-gray-900">
                                            Rp {{ number_format($discountedBase, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @else
                                    <p class="text-sm font-bold text-gray-900">
                                        Rp {{ number_format($basePrice, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>

                            {{-- Tombol Qty (LINKED ke list biasa lewat data-id) --}}
                            <div class="mt-1 mb-2 flex items-center ml-auto space-x-4">
                                <button
                                    class="minus-btn w-8 h-8 flex items-center justify-center border border-choco rounded-lg font-bold text-choco hover:bg-gray-100 hidden"
                                    data-id="{{ $product->id }}"
                                >-</button>
                                <span class="qty text-base font-semibold text-gray-800 hidden" data-id="{{ $product->id }}">0</span>
                                @if ($product->quantity_available < 1 && $product->always_available_flag === 0)
                                    <p class="text-gray-700 text-xs">Habis</p>
                                @else
                                    <button
                                        class="plus-btn w-8 h-8 flex items-center justify-center border rounded-lg font-bold text-white bg-choco hover:bg-soft-choco"
                                        data-id="{{ $product->id }}"
                                    >+</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Daftar produk per kategori --}}
    @foreach($productsByCategory as $categoryId => $products)
      @if($products->count() > 0)
            @php
              $categoryName = $categories->firstWhere('id', $categoryId)->category_name ?? 'Uncategorized';
              $productsOnCategory = App\Models\Partner\Products\PartnerProduct::where('category_id', $categoryId)->count();
            @endphp

            <div class="category-group" data-category="{{ $categoryId }}">
              <p class="font-semibold text-gray-800 pl-4 pt-2 my-0 bg-white">
                {{ $categoryName }} <span class="font-extralight text-gray-500">({{ $productsOnCategory }})</span>
              </p>

              @foreach($products as $product)
                      @php
                        $firstImage = $product->pictures[0]['path'] ?? null;

                        // --- PROMO (sama seperti Customer) ---
                        $promo = $product->promotion; // null kalau tidak ada/ tidak aktif hari ini
                        $basePrice = (float) $product->price;

                        $hasPromo = false;
                        $discountedBase = $basePrice;

                        if ($promo) {
                          if ($promo->promotion_type === 'percentage') {
                            $discountedBase = max(0, $basePrice * (1 - ($promo->promotion_value / 100)));
                          } else { // amount
                            $discountedBase = max(0, $basePrice - (float) $promo->promotion_value);
                          }
                          $hasPromo = $discountedBase < $basePrice;
                        }

                        $promoBadge = null;
                        if ($promo) {
                          $promoBadge = $promo->promotion_type === 'percentage'
                            ? '-' . rtrim(rtrim(number_format($promo->promotion_value, 2, ',', '.'), '0'), ',') . '%'
                            : '-Rp ' . number_format($promo->promotion_value, 0, ',', '.');
                        }
                      @endphp
                      <div
                        @class([
                          'menu-item bg-white flex flex-row transition hover:shadow-lg px-4 border-b border-gray-200',
                          'grayscale' => $product->quantity_available < 1 && $product->always_available_flag === 0, // <-- Diubah
                        ])
                                          data-category="{{ $product->category_id }}"
                      >
                        @if($firstImage)
                          <div class="w-28 h-28 flex-shrink-0 rounded-lg m-2 rounded-bl-lg overflow-hidden relative">
                            <img src="{{ asset($firstImage) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @if($hasPromo && $promoBadge)
                              <span class="absolute top-1 left-1 bg-red-600 text-white text-[11px] font-semibold px-2 py-0.5 rounded">
                                {{ $promoBadge }}
                              </span>
                            @endif
                          </div>
                        @endif
                        <div class="ml-4 flex-1 flex flex-col justify-between">
                          <div>
                            <h5 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h5>
                            <p class="text-gray-500 text-sm mb-1 line-clamp-1">{{ $product->description }}</p>
                            @if($hasPromo)
                              <div class="flex items-baseline gap-2">
                                <span class="text-sm text-gray-500 line-through">Rp {{ number_format($basePrice, 0, ',', '.') }}</span>
                                <span class="text-lg font-bold text-gray-900">Rp {{ number_format($discountedBase, 0, ',', '.') }}</span>
                              </div>
                            @else
                              <p class="text-lg font-bold text-gray-900">Rp {{ number_format($basePrice, 0, ',', '.') }}</p>
                            @endif
                          </div>

                          <div class="mb-2 flex items-center ml-auto space-x-4">
                            <button
                              class="minus-btn w-9 h-9 flex items-center justify-center border border-choco rounded-lg font-bold text-choco hover:bg-gray-100 hidden"
                              data-id="{{ $product->id }}">-</button>
                            <span class="qty text-lg font-semibold text-gray-800 hidden" data-id="{{ $product->id }}">0</span>

                            @if ($product->quantity_available < 1 && $product->always_available_flag === 0)
                              <p class="text-gray-700">Habis</p>
                            @else
                              <button
                                class="plus-btn w-9 h-9 flex items-center justify-center border rounded-lg font-bold text-white bg-choco hover:bg-soft-choco"
                                data-id="{{ $product->id }}">+</button>
                            @endif
                          </div>
                        </div>
                      </div>
              @endforeach
            </div>
      @endif
    @endforeach
  </div>
</div>

@include('pages.employee.cashier.dashboard.modals.pembelian-modal')

<style>
  /* sheet bisa di-scroll sendiri */
  #modalSheet { max-height: 80vh; overflow-y: auto; -webkit-overflow-scrolling: touch; overscroll-behavior: contain; }
  body.modal-open { overflow: hidden; height: 100%; }

  /* Hide scrollbar for category filter */
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

  /* Filter button style */
  .filter-btn { background-color: white; color: #000000; transition: all 0.2s; }
  .filter-btn:hover { background-color: #eff6ff; } /* blue-50 */
  .filter-btn.active { background-color: #CF1A02; color: white; } /* choco */

  /* Modal overlay */
  #parentOptionsModal { background-color: rgba(0,0,0,0.4); }
  /* Sheet modal anim */
  #modalSheet { transform: translateY(100%); transition: transform 0.3s ease-out; }
  #parentOptionsModal.show #modalSheet { transform: translateY(0); }

  #floatingCartBar { padding-bottom: env(safe-area-inset-bottom); }
  #cartManagerModal.show #cartManagerSheet { transform: translateY(0); }

  /* ===== Kategori: scroll horizontal tanpa melebarkan halaman ===== */
  .category-bar{
    overflow-x:auto;                /* scroller mandiri */
    overflow-y:hidden;
    -webkit-overflow-scrolling:touch;/* iOS momentum scroll */
    scrollbar-gutter:stable;        /* cegah layout shift saat scrollbar muncul */
    contain:inline-size;            /* PENTING: cegah lebar “bocor” ke parent */
  }

  .category-bar::-webkit-scrollbar{ /* opsional: sembunyikan scrollbar */
    height:0;
  }

  .category-track{
    display:inline-flex;            /* memanjang sesuai isi */
    align-items:center;
    gap:0.5rem;                     /* = gap-2 */
    padding:0 0.5rem;               /* = px-2 */
    min-width:max-content;          /* biarkan konten memanjang (bukan paksa parent melebar) */
  }

  .category-track .filter-btn{
    flex:0 0 auto;                  /* jangan shrink */
    white-space:nowrap;             /* satu baris */
  }

  #menuSearch::-webkit-search-cancel-button { display: none; }
  #menuSearchClear { display: none; } /* hanya tampil saat ada teks */

</style>

@php
  $productsData = $partner_products->map(function ($p) {
    $firstImage = $p->pictures[0]['path'] ?? null;
    $promo = $p->promotion;
    $base = (float) $p->price;
    $discBase = $base;

    if ($promo) {
      if ($promo->promotion_type === 'percentage') {
        $discBase = max(0, $base * (1 - ($promo->promotion_value / 100)));
      } else {
        $discBase = max(0, $base - (float) $promo->promotion_value);
      }
    }

    $parentOptions = ($p->parent_options ?? [])->map(function ($po) {
      return [
        'id' => $po->id,
        'name' => $po->name,
        'provision' => $po->provision,
        'provision_value' => $po->provision_value,
        'options' => ($po->options ?? [])->map(function ($opt) {
          return [
            'id' => $opt->id,
            'name' => $opt->name,
            'price' => (float) $opt->price,
            'quantity_available' => $opt->quantity_available,
            'always_available_flag' => (int) $opt->always_available_flag,
          ];
        })->values()->toArray(),
      ];
    })->values()->toArray();

    return [
      'id' => $p->id,
      'name' => $p->name,
      'description' => strip_tags((string) $p->description),
      'price' => $base,
      'discounted_base' => $discBase,
      'promotion' => $promo ? [
        'id' => $promo->id,
        'type' => $promo->promotion_type,
        'value' => (float) $promo->promotion_value,
      ] : null,
      'image' => $firstImage ? asset($firstImage) : null,
      'parent_options' => $parentOptions,
      'quantity_available' => $p->quantity_available, 
      'always_available_flag' => (int) $p->always_available_flag,
    ];
  })->values()->toArray();
@endphp


<script>
window.initPembelianTab = function initPembelianTab() {
  // ======== ELEMENTS (Modal Parent Options) ========
  const modal            = document.getElementById('parentOptionsModal');
  const modalContent     = document.getElementById('modalContent');
  const modalHeader      = document.getElementById('modalHeader');
  const closeModalBtn    = document.getElementById('closeModalBtn');
  const saveModalBtn     = document.getElementById('saveModalBtn');
  const modalQtyMinus    = document.getElementById('modalQtyMinus');
  const modalQtyPlus     = document.getElementById('modalQtyPlus');
  const modalQtyValue    = document.getElementById('modalQtyValue');

  // ======== ELEMENTS (Floating Cart & Manager & Checkout) ========
  const floatingBar      = document.getElementById('floatingCartBar');
  const floatingTotal    = document.getElementById('floatingCartTotal');
  const floatingCount    = document.getElementById('floatingCartCount');
  const btnCartClear     = document.getElementById('floatingCartClear');
  const btnCartPay       = document.getElementById('floatingCartPay');

  const cartManagerModal = document.getElementById('cartManagerModal');
  const cartManagerSheet = document.getElementById('cartManagerSheet');
  const cartManagerBody  = document.getElementById('cartManagerBody');
  const cartManagerTotal = document.getElementById('cartManagerTotal');
  const closeCartManager = document.getElementById('closeCartManager');
  const cartManagerDone  = document.getElementById('cartManagerDone');

  const checkoutModal      = document.getElementById('checkoutModal');
  const checkoutSheet      = document.getElementById('checkoutSheet');
  const checkoutBody       = document.getElementById('checkoutBody');
  const checkoutGrandTotal = document.getElementById('checkoutGrandTotal');
  const checkoutCloseBtn   = document.getElementById('checkoutCloseBtn');
  const checkoutCancelBtn  = document.getElementById('checkoutCancelBtn');
  const checkoutPayBtn     = document.getElementById('checkoutPayBtn');
  const paymentMethodSel   = document.getElementById('paymentMethod');
  const orderNameInput     = document.getElementById('orderName');
  const orderTableInput    = document.getElementById('orderTable');

  // ======== DATA ========
  const productsData = @json($productsData);

  // ======== SCROLL LOCK ========
  let __savedScrollY = 0;
  let __prevScrollBehavior = '';
  function lockBodyScroll() {
    __savedScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
    const html = document.documentElement;
    __prevScrollBehavior = html.style.scrollBehavior;
    html.style.scrollBehavior = 'auto';
    document.body.classList.add('modal-open');
    document.body.style.position = 'fixed';
    document.body.style.top = `-${__savedScrollY}px`;
    document.body.style.left = '0';
    document.body.style.right = '0';
    document.body.style.width = '100%';
  }
  function unlockBodyScroll() {
    const html = document.documentElement;
    document.body.classList.remove('modal-open');
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.left = '';
    document.body.style.right = '';
    document.body.style.width = '';
    requestAnimationFrame(() => {
      window.scrollTo({ top: __savedScrollY || 0, left: 0, behavior: 'auto' });
      requestAnimationFrame(() => { html.style.scrollBehavior = __prevScrollBehavior || ''; });
    });
  }

  // ======== STATE ========
  let currentProductId = null;
  let selectedOptions  = [];
  let modalQty         = 1;
  let modalNote        = '';

  // cart: { key: { productId, options:number[], qty, unitPrice, lineTotal, note } }
  let cart = {};
  // track last key per product for minus shortcut
  let lastKeyPerProduct = {};

  const rupiahFmt = new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', minimumFractionDigits:0 });

  // ======== HELPERS (PRODUCT & PRICE) ========
  function getProductDataById(pid) {
    return productsData.find(p => p.id === pid) || {};
  }
  function keyOf(productId, optionsArr) {
    const opts = (optionsArr || []).slice().sort((a,b)=>a-b).join('-');
    return `${productId}::${opts}`;
  }
  function computeUnitPrice(productId, optionsArr) {
    const pd = getProductDataById(productId);
    const base = Number(pd.discounted_base ?? pd.price) || 0; // <— PAKAI DISCOUNTED BASE
    let optSum = 0;
    (pd.parent_options || []).forEach(po => {
      (po.options || []).forEach(opt => {
        if ((optionsArr || []).includes(opt.id)) optSum += Number(opt.price) || 0;
      });
    });
    return base + optSum;
  }

  function recomputeLineTotal(key) {
    const row = cart[key];
    if (!row) return;
    row.unitPrice = computeUnitPrice(row.productId, row.options);
    row.lineTotal = row.unitPrice * row.qty;
  }
  function addToCart(productId, optionsArr, qty = 1, note = '') {
    const key = keyOf(productId, optionsArr);
    const pd  = getProductDataById(productId);
    const promoId = pd.promotion?.id ?? null; // <— ambil id promo

    if (!cart[key]) {
      cart[key] = { productId, options: (optionsArr || []).slice(), qty: 0, unitPrice: 0, lineTotal: 0, note: '', promo_id: promoId };
    } else if (cart[key].promo_id == null) {
      cart[key].promo_id = promoId; // jaga-jaga
    }

    cart[key].qty += qty;
    if (note && note.trim().length > 0) cart[key].note = note.trim();
    recomputeLineTotal(key);
    lastKeyPerProduct[productId] = key;
    updateFloatingCartBar();
    return key;
  }

  function minusFromCart(productId, optionsArr) {
    const key = keyOf(productId, optionsArr);
    if (!cart[key]) return;
    cart[key].qty = Math.max(0, cart[key].qty - 1);
    if (cart[key].qty === 0) delete cart[key];
    else recomputeLineTotal(key);
    updateFloatingCartBar();
  }
  function sumQtyByProduct(productId) {
    let total = 0;
    for (const k in cart) if (cart[k].productId === productId) total += cart[k].qty;
    return total;
  }
  function updateProductBadge(productId) {
      const qtySpans  = document.querySelectorAll('.qty[data-id="' + productId + '"]');
      const minusBtns = document.querySelectorAll('.minus-btn[data-id="' + productId + '"]');
      const total     = sumQtyByProduct(productId);

      qtySpans.forEach(qtySpan => {
          qtySpan.innerText = total;
          if (total > 0) {
              qtySpan.classList.remove('hidden');
          } else {
              qtySpan.classList.add('hidden');
          }
      });

      minusBtns.forEach(minusBtn => {
          if (total > 0) {
              minusBtn.classList.remove('hidden');
          } else {
              minusBtn.classList.add('hidden');
          }
      });
  }

  function printCart(label='Cart') {
    const rows = Object.entries(cart).map(([key, v]) => ({
      key, productId: v.productId, options: (v.options || []).join(','), qty: v.qty,
      unitPrice: v.unitPrice, lineTotal: v.lineTotal, Note: v.note
    }));
    console.log(label, cart); if (rows.length) console.table(rows);
  }

  // ======== MODAL RENDERING ========
  function provisionInfoText(provision, value) {
    const prov = String(provision || '').toUpperCase();
    const val  = Number(value); const hasN = Number.isFinite(val);
    switch (prov) {
      case 'EXACT':        return hasN ? `Pilih ${val}` : 'Pilih';
      case 'MAX':          return hasN ? `Maksimal ${val}` : 'Maksimal';
      case 'MIN':          return hasN ? `Minimal ${val}` : 'Minimal';
      case 'OPTIONAL MAX': return hasN ? `Opsional, maksimal ${val}` : 'Opsional, maksimal';
      case 'OPTIONAL':     return 'Opsional';
      default:             return '';
    }
  }
  function enforceProvision(poDiv, provision, value) {
    const checkboxes = Array.from(poDiv.querySelectorAll('input[type="checkbox"]'));
    const prov = String(provision || '').toUpperCase();
    const val = Number(value);

    function updateState() {
      const checked = checkboxes.filter(c => c.checked);
      if (prov === 'EXACT') {
        if (checked.length >= val) checkboxes.forEach(c => { if (!c.checked) c.disabled = true; });
        else checkboxes.forEach(c => c.disabled = false);
      }
      if (prov === 'MAX' || prov === 'OPTIONAL MAX') {
        if (checked.length >= val) checkboxes.forEach(c => { if (!c.checked) c.disabled = true; });
        else checkboxes.forEach(c => c.disabled = false);
      }
      if (prov === 'MIN') {
        if (checked.length <= val) {
          checkboxes.forEach(c => {
            if (c.checked) {
              c.onchange = (e) => {
                if (checked.length <= val) e.target.checked = true;
                updateState();
              };
            }
          });
        }
      }

      selectedOptions = Array.from(
        modalContent.querySelectorAll('input[type="checkbox"]:checked')
      ).map(c => parseInt(c.value, 10));

      if (currentProductId) {
        const pd = getProductDataById(currentProductId);
        calcModalTotal(pd);
      }
      validateAllProvisions();
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateState));
    updateState();
  }
  function validateAllProvisions() {
    const groups = Array.from(modalContent.querySelectorAll('[data-provision-group]'));
    let allValid = true;
    groups.forEach(group => {
      const prov = String(group.dataset.provision || '').toUpperCase();
      const val  = Number(group.dataset.value);
      const checked = group.querySelectorAll('input[type="checkbox"]:checked').length;
      if (prov === 'EXACT' && checked !== val) allValid = false;
      if (prov === 'MAX' && (checked < 1 || checked > val)) allValid = false;
      if (prov === 'MIN' && checked < val) allValid = false;
      if (prov === 'OPTIONAL MAX' && checked > val) allValid = false;
    });
    saveModalBtn.disabled = !allValid;
    saveModalBtn.classList.toggle('opacity-50', !allValid);
    saveModalBtn.classList.toggle('cursor-not-allowed', !allValid);
  }
  function calcModalTotal(productData) {
    const baseDisc = Number(productData.discounted_base ?? productData.price) || 0; // <— PAKAI DISCOUNTED
    const optSum = (productData.parent_options || []).reduce((sum, po) => {
      (po.options || []).forEach(opt => {
        if (selectedOptions.includes(opt.id)) sum += Number(opt.price) || 0;
      });
      return sum;
    }, 0);
    const total = (baseDisc + optSum) * modalQty;
    const priceEl = document.getElementById('modalTotalPrice');
    if (priceEl) priceEl.innerText = `(${rupiahFmt.format(total)})`;
  }

  function showModal(productData) {
    // reset
    modalContent.innerHTML = '';
    modalHeader.innerHTML = '';
    modalQty = 1;
    modalNote = '';
    selectedOptions = [];

    // header
    const headerWrapper = document.createElement('div');
    headerWrapper.className = 'flex gap-4 items-start mb-4';
    
    if (productData.image) {
      const img = document.createElement('img');
      img.src = productData.image;
      img.alt = productData.name || 'Product Image';
      img.className = 'w-20 h-20 rounded-md object-cover flex-shrink-0';
      headerWrapper.appendChild(img);
    }
    
    const infoDiv = document.createElement('div');
    
    const nameEl = document.createElement('h3');
    nameEl.className = 'text-lg font-semibold';
    nameEl.textContent = productData.name || '';
    
    const descEl = document.createElement('p');
    descEl.className = 'text-sm text-gray-500 line-clamp-2';
    descEl.textContent = productData.description || '';
    
    // ===== TAMBAHAN: Info Stok =====
    const stockEl = document.createElement('p');
    stockEl.className = 'text-xs mt-1 font-medium';
    stockEl.id = 'productStockInfo';
    const stockQty = Number(productData.quantity_available) || 0;
    const alwaysAvailable = Boolean(productData.always_available_flag);

    if (alwaysAvailable) {
      stockEl.innerHTML = '<span class="text-green-600">✓ Selalu Tersedia</span>';
    } else if (stockQty > 10) {
      stockEl.innerHTML = `<span class="text-green-600">Stok: ${stockQty}</span>`;
    } else if (stockQty > 0) {
      stockEl.innerHTML = `<span class="text-orange-600">⚠ Stok Terbatas: ${stockQty}</span>`;
    } else {
      stockEl.innerHTML = '<span class="text-red-600">✕ Stok Habis</span>';
    }
    
    infoDiv.appendChild(nameEl);
    infoDiv.appendChild(descEl);
    infoDiv.appendChild(stockEl);
    headerWrapper.appendChild(infoDiv);
    modalHeader.appendChild(headerWrapper);

    // parent options
    const parentOptions = productData.parent_options || [];
    parentOptions.forEach(po => {
      const poDiv = document.createElement('div');
      poDiv.className = 'mb-2';
      poDiv.dataset.provision = po.provision;
      poDiv.dataset.value = po.provision_value;
      poDiv.setAttribute('data-provision-group', '');

      const title = document.createElement('p');
      title.className = 'font-semibold mb-2 bg-gray-100 py-1';
      title.innerText = po.name;
      const info = provisionInfoText(po.provision, po.provision_value);
      if (info) {
        const infoSpan = document.createElement('span');
        infoSpan.className = 'ml-2 text-gray-500 font-normal';
        infoSpan.textContent = '(' + info + ')';
        title.appendChild(infoSpan);
      }
      poDiv.appendChild(title);

      (po.options || []).forEach(opt => {
        const label = document.createElement('label');
        label.className = 'flex items-center gap-2 w-full py-1';

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.value = opt.id;
        checkbox.className = 'h-5 w-5 rounded-md border border-gray-500 transition focus:outline-none focus:ring-2 disabled:opacity-60 disabled:cursor-not-allowed';

        const nameSpan = document.createElement('span');
        nameSpan.className = 'flex-1';
        nameSpan.textContent = opt.name;

        const priceSpan = document.createElement('span');
        priceSpan.className = 'ml-auto text-sm font-medium';

        const qty = Number(opt.quantity_available) || 0;
        const alwaysAvailable = Boolean(opt.always_available_flag);
        const priceNum = Number(opt.price) || 0;

        if (qty < 1 && !alwaysAvailable) {
          priceSpan.textContent = 'Habis';
          priceSpan.classList.add('text-red-600');
          checkbox.disabled = true;
          label.classList.add('line-through', 'opacity-60', 'cursor-not-allowed');
          const val = parseInt(checkbox.value, 10);
          selectedOptions = selectedOptions.filter(v => v !== val);
        } else {
          priceSpan.textContent = (priceNum === 0) ? 'Free' : rupiahFmt.format(priceNum);
          const val = parseInt(checkbox.value, 10);
          checkbox.checked = selectedOptions.includes(val);
          checkbox.addEventListener('change', function() {
            const v = parseInt(this.value, 10);
            if (this.checked) {
              if (!selectedOptions.includes(v)) selectedOptions.push(v);
            } else {
              selectedOptions = selectedOptions.filter(x => x !== v);
            }
            const pd = getProductDataById(currentProductId);
            calcModalTotal(pd);
            validateAllProvisions();
          });
        }

        label.appendChild(checkbox);
        label.appendChild(nameSpan);
        label.appendChild(priceSpan);
        poDiv.appendChild(label);
      });

      modalContent.appendChild(poDiv);
      // enforce
      enforceProvision(poDiv, po.provision, po.provision_value);
    });

    // Catatan
    const noteWrap = document.createElement('div');
    noteWrap.className = 'mt-4';
    const noteLabel = document.createElement('label');
    noteLabel.className = 'block text-sm font-semibold mb-1';
    noteLabel.textContent = 'Catatan (opsional)';
    const noteArea = document.createElement('textarea');
    noteArea.id = 'modalNote';
    noteArea.className = 'w-full min-h-[72px] p-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-choco/40';
    noteArea.placeholder = 'Contoh: "Pedas level 2, saus terpisah, tanpa bawang."';
    noteArea.maxLength = 200;
    noteArea.addEventListener('input', (e) => {
      modalNote = e.target.value;
    });
    const noteHint = document.createElement('p');
    noteHint.className = 'text-xs text-gray-500 mt-1';
    noteHint.textContent = 'Maks. 200 karakter.';

    noteWrap.appendChild(noteLabel);
    noteWrap.appendChild(noteArea);
    noteWrap.appendChild(noteHint);
    modalContent.appendChild(noteWrap);

    // tampilkan
    modal.classList.add('show');
    lockBodyScroll();
    modal.classList.remove('hidden');
    
    // init qty & total
    modalQty = 1;
    calcModalTotal(productData);
    updateModalQtyDisplay(); // ← Dipanggil terakhir agar validasi stok berjalan
  }

 function updateModalQtyDisplay() {
      modalQtyValue.innerText = modalQty;
      modalQtyMinus.disabled = modalQty <= 1;
      
      if (currentProductId) {
        const pd = getProductDataById(currentProductId);
        const stockInfo = document.getElementById('productStockInfo');
        const stockQty = Number(pd.quantity_available) || 0;
        const alwaysAvailable = Boolean(pd.always_available_flag);
        
        if (stockInfo && !alwaysAvailable) {
          if (modalQty >= stockQty) {
            stockInfo.innerHTML = `<span class="text-red-600">Stok: ${stockQty}</span>`;
            modalQtyPlus.disabled = true;
          } else {
            stockInfo.innerHTML = `<span class="text-green-600">Stok: ${stockQty}</span>`;
            modalQtyPlus.disabled = false;
          }
        } else if (alwaysAvailable) {
          modalQtyPlus.disabled = false;
        }
      }
  }

  modalQtyMinus.addEventListener('click', () => {
    if (modalQty > 1) {
      modalQty--; updateModalQtyDisplay();
      if (currentProductId) calcModalTotal(getProductDataById(currentProductId));
    }
  });
  modalQtyPlus.addEventListener('click', () => {
      const pd = getProductDataById(currentProductId);
      if (!pd) return;

      const productStock = Number(pd.quantity_available) || 0;
      const alwaysAvailable = Boolean(pd.always_available_flag);
      
      // ===== TAMBAHAN: Validasi Stok =====
      if (!alwaysAvailable && modalQty >= productStock) {
        // Jangan tambah qty jika sudah mencapai stok maksimal
        return;
      }

      modalQty++; 
      updateModalQtyDisplay();
      calcModalTotal(pd);
  });

  // CLOSE modal
  closeModalBtn.addEventListener('click', () => {
    modal.classList.remove('show');
    setTimeout(() => { modal.classList.add('hidden'); unlockBodyScroll(); }, 300);
  });

  // SAVE modal (SATU handler saja)
  saveModalBtn.addEventListener('click', function () {
    if (!currentProductId) return;
    const pd = getProductDataById(currentProductId);
    const noteEl = document.getElementById('modalNote');
    const noteVal = (noteEl ? noteEl.value : modalNote || '').trim();
    addToCart(currentProductId, selectedOptions, modalQty, noteVal);
    updateProductBadge(currentProductId);
    printCart('Cart (saved with options):');

    // reset state + tutup modal
    currentProductId = null; selectedOptions = []; modalQty = 1; modalNote = '';
    updateModalQtyDisplay();
    modal.classList.remove('show');
    setTimeout(() => { modal.classList.add('hidden'); unlockBodyScroll(); }, 300);
  });

  // ======== UI: PLUS/MINUS PRODUCT CARD ========
  document.querySelectorAll('.plus-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const productId = parseInt(this.dataset.id, 10);
      const pd = getProductDataById(productId);
      if (pd && (pd.parent_options || []).length > 0) {
        currentProductId = productId; selectedOptions = []; modalQty = 1; modalNote = '';
        showModal(pd);
      } else {
        addToCart(productId, []);
        updateProductBadge(productId);
        printCart('Cart (no-options +):');
      }
    });
  });
  document.querySelectorAll('.minus-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const productId = parseInt(this.dataset.id, 10);
      let key = lastKeyPerProduct[productId];
      if (!key || !cart[key] || cart[key].qty === 0) {
        key = Object.keys(cart).find(k => cart[k].productId === productId && cart[k].qty > 0);
      }
      if (!key) { updateProductBadge(productId); printCart('Cart (minus noop):'); return; }
      const line = cart[key];
      minusFromCart(productId, line.options);
      updateProductBadge(productId);
      printCart('Cart (minus):');
    });
  });

  // ======== FLOATING CART ========
  function cartGrandTotal() {
    let total = 0;
    for (const k in cart) {
      const row = cart[k]; if (!row) continue;
      const unit = (typeof row.unitPrice === 'number' && row.unitPrice > 0) ? row.unitPrice : computeUnitPrice(row.productId, row.options);
      total += unit * row.qty;
    }
    return total;
  }
  function cartTotalQty() {
    let total = 0; for (const k in cart) total += cart[k].qty || 0; return total;
  }
  function updateFloatingCartBar() {
    const total = cartGrandTotal(); const count = cartTotalQty();
    if (count > 0) {
      floatingTotal.textContent = rupiahFmt.format(total);
      floatingCount.textContent = `(${count})`;
      floatingBar.classList.remove('hidden');
    } else {
      floatingBar.classList.add('hidden');
    }
  }

  // Buka Cart Manager ketika klik ikon trash (untuk melihat/atur isi)
  btnCartClear.addEventListener('click', (e) => {
    e.preventDefault(); openCartManager();
  });

  // Buka Checkout
  btnCartPay.addEventListener('click', () => { openCheckoutModal(); });

  // ======== CART MANAGER ========
  function optionNameById(productData, optId) {
    for (const po of (productData.parent_options || [])) {
      for (const opt of (po.options || [])) if (opt.id === optId) return opt.name;
    }
    return null;
  }
  function cartRows() {
    const arr = [];
    for (const k in cart) {
      const row = cart[k]; if (!row || row.qty <= 0) continue;
      const pd = getProductDataById(row.productId) || {};
      const optNames = (row.options || []).map(id => optionNameById(pd, id)).filter(Boolean);
      const unit = (typeof row.unitPrice === 'number' && row.unitPrice > 0) ? row.unitPrice : computeUnitPrice(row.productId, row.options);
      arr.push({
        key: k, productId: row.productId, productName: pd.name || 'Produk', image: pd.image || null,
        optNames, qty: row.qty, unit, line: unit * row.qty, options: row.options || [], note: row.note || ''
      });
    }
    return arr;
  }
  function renderCartManager() {
    const rows = cartRows();
    if (rows.length === 0) {
      cartManagerBody.innerHTML = `<div class="p-6 text-center text-gray-500">Keranjang masih kosong.</div>`;
      cartManagerTotal.textContent = rupiahFmt.format(0);
      return;
    }
    cartManagerBody.innerHTML = rows.map(r => {
      const opts = r.optNames.length ? `<p class="text-xs text-gray-500 line-clamp-1">${r.optNames.join(', ')}</p>` : '';
      const note = r.note ? `<p class="text-xs text-gray-600 mt-1 italic line-clamp-2">Catatan: ${r.note}</p>` : '';
      const img  = r.image ? `<img src="${r.image}" class="w-16 h-16 rounded-md object-cover flex-shrink-0" alt="">` : '';
      return `
        <div class="p-3 flex items-center gap-3" data-key="${r.key}" data-product-id="${r.productId}">
          ${img}
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-800 line-clamp-1">${r.productName}</p>
            ${opts}
            ${note}
            <p class="text-xs text-gray-400 mt-0.5">Harga: ${rupiahFmt.format(r.unit)}</p>
          </div>
          <div class="flex items-center gap-2">
            <button class="cm-minus w-8 h-8 flex items-center justify-center border rounded-lg">-</button>
            <span class="cm-qty w-6 text-center font-semibold">${r.qty}</span>
            <button class="cm-plus w-8 h-8 flex items-center justify-center border rounded-lg bg-choco text-white">+</button>
          </div>
          <div class="ml-3 text-right">
            <p class="text-sm font-bold">${rupiahFmt.format(r.line)}</p>
          </div>
        </div>
      `;
    }).join('');
    const grand = rows.reduce((s, r) => s + r.line, 0);
    cartManagerTotal.textContent = rupiahFmt.format(grand);
  }
  function openCartManager() {
    renderCartManager();
    cartManagerModal.classList.add('show'); cartManagerModal.classList.remove('hidden'); lockBodyScroll();
  }
  function closeCartManagerModal() {
    cartManagerModal.classList.remove('show');
    setTimeout(() => { cartManagerModal.classList.add('hidden'); unlockBodyScroll(); }, 300);
  }
  cartManagerBody.addEventListener('click', (e) => {
    const plusBtn  = e.target.closest('.cm-plus');
    const minusBtn = e.target.closest('.cm-minus');
    if (!plusBtn && !minusBtn) return;
    const rowEl = e.target.closest('[data-key]'); if (!rowEl) return;
    const key = rowEl.getAttribute('data-key'); const row = cart[key]; if (!row) return;
    if (plusBtn) addToCart(row.productId, row.options);
    else if (minusBtn) minusFromCart(row.productId, row.options);
    updateProductBadge(row.productId); updateFloatingCartBar(); renderCartManager();
  });
  closeCartManager.addEventListener('click', closeCartManagerModal);
  cartManagerDone.addEventListener('click', closeCartManagerModal);
  cartManagerModal.addEventListener('click', (e) => { if (e.target === cartManagerModal) closeCartManagerModal(); });

  // ======== CHECKOUT ========
  function getOptionDetail(productId, optId) {
    const pd = getProductDataById(productId);
    if (!pd) return { name:null, price:0, parentName:null };
    for (const po of (pd.parent_options || [])) {
      for (const opt of (po.options || [])) {
        if (opt.id === optId) return { name: opt.name, price: Number(opt.price) || 0, parentName: po.name || null };
      }
    }
    return { name:null, price:0, parentName:null };
  }
  function checkoutRows() {
    const rows = [];
    for (const k in cart) {
      const r = cart[k]; if (!r || r.qty <= 0) continue;
      const pd = getProductDataById(r.productId) || {};
      const unit = (typeof r.unitPrice === 'number' && r.unitPrice > 0) ? r.unitPrice : computeUnitPrice(r.productId, r.options);
      const optionsDetail = (r.options || []).map(oid => getOptionDetail(r.productId, oid));
      rows.push({
        key: k, productId: r.productId, name: pd.name || 'Produk', image: pd.image || null,
        note: r.note || '', qty: r.qty, unit, line: unit * r.qty, basePrice: Number(pd.price) || 0, optionsDetail
      });
    }
    return rows;
  }
  function rupiah(n) { return rupiahFmt.format(n || 0); }
  function renderCheckoutModal() {
    const rows = checkoutRows();
    if (rows.length === 0) {
      checkoutBody.innerHTML = `<div class="text-center text-gray-500 py-8">Keranjang masih kosong.</div>`;
      checkoutGrandTotal.textContent = rupiah(0);
      return;
    }

    checkoutBody.innerHTML = rows.map(r => {
      // 1) Baris opsi (tidak berubah)
      const opts = (r.optionsDetail || []).map(od => {
        const label = od.parentName ? `${od.parentName}: ${od.name}` : od.name;
        return `
          <div class="w-full flex items-center justify-between text-xs text-gray-600">
            <span class="truncate">${label}</span>
            <span class="shrink-0">${od.price === 0 ? '(Free)' : rupiah(od.price)}</span>
          </div>
        `;
      }).join('');

      // 2) Catatan (opsional)
      const note = r.note ? `<div class="mt-2 text-xs italic text-gray-700">Catatan: ${r.note}</div>` : '';

      // 3) HITUNG baseDisc dari unit - sumOpts
      //    (karena unit = discounted_base + total harga opsi)
      const sumOpts  = (r.optionsDetail || []).reduce((s, od) => s + (Number(od.price) || 0), 0);
      const rawBase  = Number(r.basePrice) || 0;                           // harga dasar asli (sebelum promo)
      const baseDisc = Math.max(0, (Number(r.unit) || 0) - sumOpts);       // harga dasar setelah promo

      const baseRow = (baseDisc < rawBase)
        ? `
            <div class="w-full flex items-center justify-between text-xs text-gray-600">
              <span>Harga dasar</span>
              <span class="shrink-0">
                <span class="line-through mr-1">${rupiah(rawBase)}</span>
                <span class="font-medium">${rupiah(baseDisc)}</span>
              </span>
            </div>
          `
        : `
            <div class="w-full flex items-center justify-between text-xs text-gray-600">
              <span>Harga dasar</span>
              <span class="shrink-0">${rupiah(rawBase)}</span>
            </div>
          `;

      // 4) RETURN card: pakai ${baseRow} (ganti blok "Harga dasar" lama)
      return `
        <div class="border rounded-lg p-3">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
              ${r.image ? `<img src="${r.image}" alt="${r.name}" class="w-20 h-20 rounded-md object-cover flex-shrink-0">` : ''}
              <p class="text-sm font-semibold truncate">${r.name}</p>
            </div>
            <div class="text-right shrink-0">
              <span class="text-xs text-gray-500 align-middle mr-1">Qty</span>
              <span class="font-semibold align-middle">${r.qty}</span>
            </div>
          </div>

          <div class="mt-1 space-y-0.5">
            ${baseRow}   <!-- <<< ini menggantikan baris harga dasar lama -->
            ${opts}
          </div>

          ${note}

          <div class="mt-2 w-full flex items-center justify-between">
            <span class="text-xs text-gray-500">Subtotal</span>
            <span class="font-semibold">${rupiah(r.line)}</span>
          </div>
        </div>
      `;
    }).join('');

    const grand = rows.reduce((s, r) => s + r.line, 0);
    checkoutGrandTotal.textContent = rupiah(grand);
  }

  function openCheckoutModal() {
    renderCheckoutModal();
    paymentMethodSel.value = '';
    orderTableInput.value = ';'
    checkoutPayBtn.disabled = true;
    checkoutModal.classList.remove('hidden');
    requestAnimationFrame(() => { checkoutSheet.classList.remove('translate-y-full'); });
    lockBodyScroll();
    orderNameInput && orderNameInput.focus({ preventScroll: true });
  }
  function closeCheckoutModal() {
    checkoutSheet.classList.add('translate-y-full');
    setTimeout(() => { checkoutModal.classList.add('hidden'); unlockBodyScroll(); }, 250);
  }
  function updatePayBtnState() {
    const hasMethod = !!paymentMethodSel.value;
    const hasName   = !!(orderNameInput.value || '').trim();
    const hasTable  = !!(orderTableInput.value || '').trim();
    checkoutPayBtn.disabled = !(hasMethod && hasName && hasTable);
  }
  paymentMethodSel.addEventListener('change', updatePayBtnState);
  orderTableInput.addEventListener('change', updatePayBtnState);
  orderNameInput.addEventListener('input', updatePayBtnState);

  checkoutCloseBtn.addEventListener('click', closeCheckoutModal);
  checkoutCancelBtn.addEventListener('click', closeCheckoutModal);
  checkoutModal.addEventListener('click', (e) => { if (e.target === checkoutModal) closeCheckoutModal(); });

  checkoutPayBtn.addEventListener('click', async () => {
    const paymentMethod = paymentMethodSel.value; // 'CASH' | 'QRIS'
    const orderTable = orderTableInput.value;
    const orderName     = orderNameInput.value;
    const payload = Object.entries(cart).map(([key, r]) => ({
      product_id: r.productId,
      option_ids: r.options,
      qty: r.qty,
      unit_price: r.unitPrice ?? computeUnitPrice(r.productId, r.options),
      note: r.note || '',
      promo_id: (r.promo_id != null)
        ? r.promo_id
        : (getProductDataById(r.productId)?.promotion?.id ?? null), // fallback
    }));

    const grandTotal = payload.reduce((s, it) => s + (it.unit_price * it.qty), 0);
    if (!paymentMethod) { Swal && Swal.fire({ icon:'warning', title:'Metode belum dipilih' }); return; }
    if (!orderTable) { Swal && Swal.fire({ icon:'warning', title:'Meja belum dipilih' }); return; }
    if (!orderName)     { Swal && Swal.fire({ icon:'warning', title:'Nama belum diisi' }); return; }
    if (grandTotal <= 0){ Swal && Swal.fire({ icon:'info', title:'Keranjang kosong' }); return; }

    // ===== VALIDASI STOK REAL-TIME =====
                    checkoutPayBtn.disabled = true;
                    Swal.fire({ 
                        title:'Memeriksa ketersediaan stok…', 
                        allowOutsideClick:false, 
                        didOpen:() => Swal.showLoading() 
                    });

                    try {
                        const tokenEl = document.querySelector('meta[name="csrf-token"]');
                        const csrf = tokenEl ? tokenEl.content : null;
                        
                        const checkStockUrl = `/employee/cashier/check-stock`;                        
                        const stockCheckResponse = await fetch(checkStockUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({ items: payload })
                        });
                        
                        if (!stockCheckResponse.ok) {
                            throw new Error(`HTTP ${stockCheckResponse.status}`);
                        }
                        
                        const stockResult = await stockCheckResponse.json();
                        
                        if (!stockResult.success) {
                            Swal.close();
                            
                            const result = await Swal.fire({
                                icon: 'error',
                                title: 'Stok Tidak Mencukupi',
                                html: `
                                    <div class="text-center">
                                        <p class="mt-3 text-sm text-gray-600">Silakan refresh halaman untuk melihat stok terbaru.</p>
                                    </div>
                                `,
                                showCancelButton: true,
                                confirmButtonText: 'Refresh Halaman',
                                cancelButtonText: 'Batal',
                                confirmButtonColor: '#3085d6',
                            });
                            
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                            
                            checkoutPayBtn.disabled = false;
                            return;
                        }
                        
                        Swal.close();
                        
                    } catch (stockCheckError) {
        console.error('Stock check error:', stockCheckError);
        Swal.close();
        
        // ✅ PERBAIKAN: Tampilkan toast + return (hentikan)
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });
        
        await Toast.fire({
            icon: 'warning',
            title: 'Gagal memverifikasi stok'
        });
        
        checkoutPayBtn.disabled = false;
        return; // ✅ PENTING: Hentikan eksekusi, jangan lanjut checkout
    }
    
    const confirm = await Swal.fire({
      icon:'question',
      title:'Konfirmasi Checkout',
      html: `<div style="text-align:left">
              <div>Nama Pemesan: <b>${orderName}</b></div>
              <div>Metode: <b>${paymentMethod.toUpperCase()}</b></div>
              <div>Meja: <b>${orderTable.toUpperCase()}</b></div>
              <div>Total: <b>${rupiahFmt.format(grandTotal)}</b></div>
            </div>`,
      showCancelButton:true, confirmButtonText:'Ya, bayar', cancelButtonText:'Batal'
    });
    if (!confirm.isConfirmed) return;

    const PARTNER_SLUG = @json($partner_slug);
    const TABLE_CODE   = @json($table_code);
    const checkoutUrl  = `/employee/cashier/checkout-order`;
    const tokenEl = document.querySelector('meta[name="csrf-token"]'); const csrf = tokenEl ? tokenEl.content : null;

    checkoutPayBtn.disabled = true;
    Swal.fire({ title:'Memproses pembayaran…', allowOutsideClick:false, didOpen:() => Swal.showLoading() });
    try {
      const r = await fetch(checkoutUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json', 'Accept': 'application/json',
          ...(csrf ? { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' } : {})
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          items: payload,
          payment_method: paymentMethod,
          order_table: orderTable,
          order_name: orderName,
          total_amount: grandTotal
        })
      });
      if (r.redirected && r.url) { window.location.assign(r.url); return; }
      let res; const ct = r.headers.get('content-type') || '';
      if (ct.includes('application/json')) res = await r.json();
      else { const text = await r.text(); try { res = JSON.parse(text); } catch { throw new Error('Respons tidak valid dari server.'); } }
      if (!r.ok) throw new Error(res?.message || `Request failed: ${r.status}`);
      if (res?.redirect) { window.location.assign(res.redirect); return; }
      // Tutup modal checkout
        closeCheckoutModal();

        // Bersihkan keranjang & UI terkait
        cart = {};
        lastKeyPerProduct = {};
        updateFloatingCartBar();
        // reset badge qty di daftar produk
        // reset badge qty di semua tampilan (Hot & kategori)
        document.querySelectorAll('.qty').forEach(el => {
            el.textContent = '0';
            el.classList.add('hidden');
        });
        document.querySelectorAll('.minus-btn').forEach(btn => btn.classList.add('hidden'));


        // Notifikasi
        Swal.close();
        await Swal.fire({
        icon:'success',
        title:'Berhasil',
        text:'Checkout berhasil diproses.',
        timer:1400,
        showConfirmButton:false
        });
        
        window.location.reload();


    } catch (err) {
      Swal.close();
      const msg = (err?.message || '').toLowerCase().includes('csrf')
        ? 'Sesi keamanan kedaluwarsa. Silakan muat ulang halaman dan coba lagi.'
        : (err?.message || 'Checkout gagal.');
      await Swal.fire({ icon:'error', title:'Gagal', text: msg });
    } finally {
      checkoutPayBtn.disabled = false;
    }
  });

    // ======== FILTER KATEGORI + SEARCH ========
  (function setupCategoryAndSearch(){
    if (window.__CATSEARCH_INITED__) return;
    window.__CATSEARCH_INITED__ = true;
    const filterButtons  = document.querySelectorAll('.filter-btn');
    const categoryGroups = document.querySelectorAll('.category-group');
    const items          = document.querySelectorAll('.menu-item');

    const searchInput = document.getElementById('menuSearch');
    const searchClear = document.getElementById('menuSearchClear');

    let activeCategory = 'all';
    let query = '';

    function norm(s){
      return (s || '')
        .toString()
        .toLowerCase()
        .normalize('NFD')              // hapus diakritik
        .replace(/[\u0300-\u036f]/g,'')
        .trim();
    }

    function itemMatches(item, cat, q){
      const catOk = (cat === 'all' || item.dataset.category === cat);
      if (!catOk) return false;
      if (!q) return true;

      const nameEl = item.querySelector('h5');
      const descEl = item.querySelector('p.text-gray-500');

      const name = norm(nameEl ? nameEl.textContent : '');
      const desc = norm(descEl ? descEl.textContent : '');
      return name.includes(q) || desc.includes(q);
    }

    function applyFilters(){
      const nq = norm(query);
      // tombol active
      filterButtons.forEach(b => b.classList.toggle('active', b.dataset.category === activeCategory || (activeCategory === 'all' && b.dataset.category === 'all')));

      // tampilkan item sesuai kombinasi
      let anyShown = false;
      items.forEach(item => {
        const show = itemMatches(item, activeCategory, nq);
        item.style.display = show ? 'flex' : 'none';
        if (show) anyShown = true;
      });

      // group heading: tampil kalau minimal satu item di group yang lolos
      categoryGroups.forEach(group => {
        if (activeCategory === 'all') {
          // cek ada anak visible?
          const visibleChild = Array.from(group.querySelectorAll('.menu-item')).some(it => it.style.display !== 'none');
          group.style.display = visibleChild ? 'block' : 'none';
        } else {
          group.style.display = (group.dataset.category === activeCategory)
            ? (Array.from(group.querySelectorAll('.menu-item')).some(it => it.style.display !== 'none') ? 'block' : 'none')
            : 'none';
        }
      });

      // pesan "tidak ada hasil"
      ensureNoResultBanner(!anyShown, nq);
      // tombol clear
      if (searchClear) searchClear.style.display = nq ? 'inline-flex' : 'none';
    }

    // Banner "no result" ringan
    let noResEl = null;
    function ensureNoResultBanner(show, nq){
      // Reuse kalau sudah ada di DOM
      if (!noResEl) {
        noResEl = document.getElementById('noResultBanner');
      }
      // Kalau memang belum ada, buat baru
      if (!noResEl) {
        noResEl = document.createElement('div');
        noResEl.id = 'noResultBanner';
        noResEl.className = 'p-6 text-center text-gray-500';
        noResEl.style.display = 'none';
        const menuContainer = document.getElementById('menu-container');
        if (menuContainer && menuContainer.parentNode) {
          menuContainer.parentNode.insertBefore(noResEl, menuContainer);
        }
      }
      if (!noResEl) return;

      if (show) {
        noResEl.innerHTML = `Tidak ada menu yang cocok untuk <b>"${nq}"</b>${activeCategory!=='all' ? ` pada kategori terpilih` : ''}.`;
        noResEl.style.display = 'block';
      } else {
        noResEl.style.display = 'none';
      }
    }


    // Debounce input
    let t = null;
    function debounce(fn, wait=200){
      return function(...args){
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), wait);
      }
    }

    // Listeners
    filterButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        activeCategory = btn.dataset.category;
        applyFilters();
      });
    });

    if (searchInput) {
      searchInput.addEventListener('input', debounce((e) => {
        query = e.target.value || '';
        applyFilters();
      }, 150));
    }
    if (searchClear) {
      searchClear.addEventListener('click', () => {
        if (!searchInput) return;
        searchInput.value = '';
        query = '';
        applyFilters();
        searchInput.focus({ preventScroll: true });
      });
    }

    // inisialisasi awal
    applyFilters();
  })();


  // ======== EXPOSED (optional) ========
  window.PEMBELIAN = {
    cart,
    addToCart,
    minusFromCart,
    updateFloatingCartBar,
  };
};
// opsional: kalau tab ini juga dipakai non-AJAX, kamu boleh auto-init jika elemen kunci ada:
if (document.getElementById('parentOptionsModal')) {
  // cegah double init: hanya jalankan bila tab pembelian saat ini ada di DOM utama
  window.initPembelianTab();
}
  
</script>


