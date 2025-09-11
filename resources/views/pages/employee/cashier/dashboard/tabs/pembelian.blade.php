{{-- resources/views/pages/employee/cashier/dashboard/tabs/pembelian.blade.php --}}

{{-- Konten utama --}}
<div class="px-0 pt-0 relative z-10">
  {{-- Tempelkan blok ini DI DALAM container yang di-scroll (mis. di awal tab "Pembelian") --}}
  <div class="sticky top-0 z-30 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 border-b border-gray-200">
    <div class="category-bar w-full pt-7">
      <div id="categoryWrapper" class="category-track">
        <button type="button"
                class="filter-btn px-4 py-2 text-sm rounded-md active cursor-pointer shrink-0"
                data-category="all">
          All
        </button>
        @foreach($categories as $category)
          <button type="button"
                  class="filter-btn px-4 py-2 text-sm rounded-md cursor-pointer shrink-0"
                  data-category="{{ $category->id }}">
            {{ $category->category_name }}
          </button>
        @endforeach
      </div>
    </div>
  </div>



  @php
    $productsByCategory = $partner_products->groupBy('category_id');
  @endphp

  <div class="flex flex-col" id="menu-container">
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
            @endphp
            <div
              @class([
                'menu-item bg-white flex flex-row transition hover:shadow-lg px-4 border-b border-gray-200',
                'grayscale' => $product->quantity < 1,
              ])
              data-category="{{ $product->category_id }}"
            >
              @if($firstImage)
                <div class="w-28 h-28 flex-shrink-0 rounded-lg m-2 rounded-bl-lg overflow-hidden">
                  <img src="{{ asset($firstImage) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
              @endif

              <div class="ml-4 flex-1 flex flex-col justify-between">
                <div>
                  <h5 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h5>
                  <p class="text-gray-500 text-sm mb-1 line-clamp-1">{{ $product->description }}</p>
                  <p class="text-lg font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>

                <div class="mb-2 flex items-center ml-auto space-x-4">
                  <button class="minus-btn w-9 h-9 flex items-center justify-center border border-choco rounded-lg font-bold text-choco hover:bg-gray-100 hidden"
                          data-id="{{ $product->id }}">-</button>
                  <span class="qty text-lg font-semibold text-gray-800 hidden" id="qty-{{ $product->id }}">0</span>
                  @if ($product->quantity < 1)
                    <p class="text-gray-700">Habis</p>
                  @else
                    <button class="plus-btn w-9 h-9 flex items-center justify-center border rounded-lg font-bold text-white bg-choco hover:bg-soft-choco"
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
  .filter-btn.active { background-color: #9A3F3F; color: white; } /* choco */

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

</style>

@php
  $productsData = $partner_products->map(function($p){
    $firstImage = $p->pictures[0]['path'] ?? null;
    return [
      'id'             => $p->id,
      'name'           => $p->name,
      'description'    => $p->description,
      'price'          => $p->price,
      'image'          => $firstImage ? asset($firstImage) : null,
      'parent_options' => $p->parent_options ?? [],
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
    const base = Number(pd.price) || 0;
    const opts = pd.parent_options || [];
    let optSum = 0;
    opts.forEach(po => {
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
    if (!cart[key]) {
      cart[key] = { productId, options: (optionsArr || []).slice(), qty: 0, unitPrice: 0, lineTotal: 0, note: '' };
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
    const qtySpan = document.getElementById('qty-' + productId);
    const minusBtn = document.querySelector('.minus-btn[data-id="' + productId + '"]');
    const total = sumQtyByProduct(productId);
    qtySpan.innerText = total;
    if (total > 0) {
      qtySpan.classList.remove('hidden'); minusBtn && minusBtn.classList.remove('hidden');
    } else {
      qtySpan.classList.add('hidden'); minusBtn && minusBtn.classList.add('hidden');
    }
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
    const base = Number(productData.price) || 0;
    const optSum = (productData.parent_options || []).reduce((sum, po) => {
      (po.options || []).forEach(opt => {
        if (selectedOptions.includes(opt.id)) sum += Number(opt.price) || 0;
      });
      return sum;
    }, 0);
    const total = (base + optSum) * modalQty;
    const priceEl = document.getElementById('modalTotalPrice');
    if (priceEl) priceEl.innerText = `(${rupiahFmt.format(total)})`;
  }
  function showModal(productData) {
    // reset
    modalContent.innerHTML = ''; modalHeader.innerHTML = '';
    modalQty = 1; modalNote = ''; selectedOptions = [];

    // header
    const headerWrapper = document.createElement('div');
    headerWrapper.className = 'flex gap-4 items-start mb-4';
    if (productData.image) {
      const img = document.createElement('img');
      img.src = productData.image; img.alt = productData.name || 'Product Image';
      img.className = 'w-20 h-20 rounded-md object-cover flex-shrink-0';
      headerWrapper.appendChild(img);
    }
    const infoDiv = document.createElement('div');
    const nameEl = document.createElement('h3'); nameEl.className = 'text-lg font-semibold'; nameEl.textContent = productData.name || '';
    const descEl = document.createElement('p');  descEl.className  = 'text-sm text-gray-500 line-clamp-2'; descEl.textContent = productData.description || '';
    infoDiv.appendChild(nameEl); infoDiv.appendChild(descEl);
    headerWrapper.appendChild(infoDiv);
    modalHeader.appendChild(headerWrapper);

    // parent options
    const parentOptions = productData.parent_options || [];
    parentOptions.forEach(po => {
      const poDiv = document.createElement('div');
      poDiv.className = 'mb-2'; poDiv.dataset.provision = po.provision; poDiv.dataset.value = po.provision_value;
      poDiv.setAttribute('data-provision-group','');

      const title = document.createElement('p');
      title.className = 'font-semibold mb-2 bg-gray-100 py-1'; title.innerText = po.name;
      const info = provisionInfoText(po.provision, po.provision_value);
      if (info) {
        const infoSpan = document.createElement('span');
        infoSpan.className = 'ml-2 text-gray-500 font-normal'; infoSpan.textContent = '(' + info + ')';
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
        nameSpan.className = 'flex-1'; nameSpan.textContent = opt.name;

        const priceSpan = document.createElement('span');
        priceSpan.className = 'ml-auto text-sm font-medium';

        const qty = Number(opt.quantity) || 0;
        const priceNum = Number(opt.price) || 0;

        if (qty < 1) {
          priceSpan.textContent = 'Habis'; priceSpan.classList.add('text-red-600');
          checkbox.disabled = true; label.classList.add('line-through','opacity-60','cursor-not-allowed');
          const val = parseInt(checkbox.value,10);
          selectedOptions = selectedOptions.filter(v => v !== val);
        } else {
          priceSpan.textContent = (priceNum === 0) ? 'Free' : rupiahFmt.format(priceNum);
          const val = parseInt(checkbox.value,10);
          checkbox.checked = selectedOptions.includes(val);
          checkbox.addEventListener('change', function () {
            const v = parseInt(this.value,10);
            if (this.checked) { if (!selectedOptions.includes(v)) selectedOptions.push(v); }
            else { selectedOptions = selectedOptions.filter(x => x !== v); }
            const pd = getProductDataById(currentProductId);
            calcModalTotal(pd);
            validateAllProvisions();
          });
        }

        label.appendChild(checkbox); label.appendChild(nameSpan); label.appendChild(priceSpan);
        poDiv.appendChild(label);
      });

      modalContent.appendChild(poDiv);
      // enforce
      enforceProvision(poDiv, po.provision, po.provision_value);
    });

    // Catatan
    const noteWrap = document.createElement('div'); noteWrap.className = 'mt-4';
    const noteLabel = document.createElement('label'); noteLabel.className = 'block text-sm font-semibold mb-1'; noteLabel.textContent = 'Catatan (opsional)';
    const noteArea = document.createElement('textarea'); noteArea.id = 'modalNote';
    noteArea.className = 'w-full min-h-[72px] p-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-choco/40';
    noteArea.placeholder = 'Contoh: “Pedas level 2, saus terpisah, tanpa bawang.”';
    noteArea.maxLength = 200; noteArea.addEventListener('input', (e) => { modalNote = e.target.value; });
    const noteHint = document.createElement('p'); noteHint.className = 'text-xs text-gray-500 mt-1'; noteHint.textContent = 'Maks. 200 karakter.';

    noteWrap.appendChild(noteLabel); noteWrap.appendChild(noteArea); noteWrap.appendChild(noteHint);
    modalContent.appendChild(noteWrap);

    // tampilkan
    modal.classList.add('show'); lockBodyScroll(); modal.classList.remove('hidden');
    // init qty & total
    modalQty = 1; updateModalQtyDisplay();
    calcModalTotal(productData);
  }

  function updateModalQtyDisplay() {
    modalQtyValue.innerText = modalQty;
    modalQtyMinus.disabled = modalQty <= 1;
  }

  modalQtyMinus.addEventListener('click', () => {
    if (modalQty > 1) {
      modalQty--; updateModalQtyDisplay();
      if (currentProductId) calcModalTotal(getProductDataById(currentProductId));
    }
  });
  modalQtyPlus.addEventListener('click', () => {
    modalQty++; updateModalQtyDisplay();
    if (currentProductId) calcModalTotal(getProductDataById(currentProductId));
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
      checkoutGrandTotal.textContent = rupiah(0); return;
    }
    checkoutBody.innerHTML = rows.map(r => {
      const opts = r.optionsDetail.map(od => {
        const label = od.parentName ? `${od.parentName}: ${od.name}` : od.name;
        return `
          <div class="w-full flex items-center justify-between text-xs text-gray-600">
            <span class="truncate">${label}</span>
            <span class="shrink-0">${od.price === 0 ? '(Free)' : rupiah(od.price)}</span>
          </div>
        `;
      }).join('');
      const note = r.note ? `<div class="mt-2 text-xs italic text-gray-700">Catatan: ${r.note}</div>` : '';
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
            <div class="w-full flex items-center justify-between text-xs text-gray-600">
              <span>Harga dasar</span><span class="shrink-0">${rupiah(r.basePrice)}</span>
            </div>
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
      note: r.note || ''
    }));
    const grandTotal = payload.reduce((s, it) => s + (it.unit_price * it.qty), 0);
    if (!paymentMethod) { Swal && Swal.fire({ icon:'warning', title:'Metode belum dipilih' }); return; }
    if (!orderTable) { Swal && Swal.fire({ icon:'warning', title:'Meja belum dipilih' }); return; }
    if (!orderName)     { Swal && Swal.fire({ icon:'warning', title:'Nama belum diisi' }); return; }
    if (grandTotal <= 0){ Swal && Swal.fire({ icon:'info', title:'Keranjang kosong' }); return; }

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
        document.querySelectorAll('[id^="qty-"]').forEach(el => el.classList.add('hidden'));
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

  // ======== FILTER KATEGORI ========
  (function setupCategoryFilter(){
    const filterButtons  = document.querySelectorAll('.filter-btn');
    const categoryGroups = document.querySelectorAll('.category-group');
    const items          = document.querySelectorAll('.menu-item');

    function applyFilter(category) {
      // tombol active
      filterButtons.forEach(b => b.classList.remove('active'));
      const btn = document.querySelector(`.filter-btn[data-category="${category}"]`);
      if (btn) btn.classList.add('active');

      // tampilkan group heading sesuai pilihan
      categoryGroups.forEach(group => {
        if (category === 'all') group.style.display = 'block';
        else group.style.display = (group.dataset.category === category) ? 'block' : 'none';
      });

      // tampilkan item
      items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) item.style.display = 'flex';
        else item.style.display = 'none';
      });
    }

    filterButtons.forEach(btn => {
      btn.addEventListener('click', () => applyFilter(btn.dataset.category));
    });
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


