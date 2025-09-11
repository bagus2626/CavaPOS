@extends('layouts.customer')

@section('title', 'Menu ' . $partner->name)

@section('content')
{{-- Hero Background (logo tetap di belakang) --}}
<div class="fixed top-0 left-0 w-full h-72 sm:h-72 -z-10">
    @if($partner->logo)
        <img src="{{ asset($partner->logo) }}"
             alt="{{ $partner->name }}"
             class="w-full h-full object-cover">
    @else
        <div class="absolute inset-0 bg-gradient-to-r from-blue-100 to-blue-200"></div>
    @endif
    <div class="absolute inset-0 bg-white/30"></div>
</div>

{{-- Konten utama --}}
<div class="w-full px-0 pt-48 relative z-10">
    {{-- Info Partner --}}
    <div class="bg-white rounded-t-2xl shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-gray-900 mb-1">
            {{ $partner->name }}
        </h2>
        <p class="inline-flex items-center gap-2 rounded-full bg-soft-choco/10 px-4 py-1 text-sm font-medium text-choco">
            <span class="font-semibold">Table {{ $table->table_no }}</span>
            <span class="text-gray-400">•</span>
            <span>{{ $table->table_class }}</span>
        </p>
    </div>

    {{-- Category Filter --}}
    <div class="relative pb-6 bg-white">
        <div id="categoryWrapper"
            class="flex gap-2 overflow-x-auto whitespace-nowrap scrollbar-hide px-2 sm:px-8">

            <div class="filter-btn px-4 py-2 text-sm rounded-md active cursor-pointer" data-category="all">
                All
            </div>

            @foreach($categories as $category)
                <div class="filter-btn px-4 py-2 text-sm rounded-md cursor-pointer"
                    data-category="{{ $category->id }}">
                    {{ $category->category_name }}
                </div>
            @endforeach
        </div>
    </div>

    {{-- menu list --}}
    @php
        // Kelompokkan produk berdasarkan category_id
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
                    {{-- Nama kategori --}}
                    <p class="font-semibold text-gray-800 px-4 pt-2 my-0 bg-white">{{ $categoryName }} <span class="font-extralight text-gray-500">({{$productsOnCategory}})</span> </p>

                    {{-- List produk --}}
                    @foreach($products as $product)
                        @php
                            $firstImage = $product->pictures[0]['path'] ?? null;
                        @endphp
                        <div
                            @class([
                                'menu-item bg-white flex flex-row transition hover:shadow-lg px-4 border-b border-gray-200',
                                // tailwind grayscale
                                'grayscale' => $product->quantity < 1,
                            ])
                            data-category="{{ $product->category_id }}"
                            >

                            {{-- Gambar Produk --}}
                            @if($firstImage)
                                <div class="w-28 h-28 flex-shrink-0 rounded-lg m-2 rounded-bl-lg overflow-hidden">
                                    <img src="{{ asset($firstImage) }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @endif

                            {{-- Info Produk --}}
                            <div class="ml-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <h5 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h5>
                                    <p class="text-gray-500 text-sm mb-1 line-clamp-1">{{ $product->description }}</p>
                                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                </div>

                                {{-- Tombol Qty --}}
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
@include('pages.customer.menu.modal')

@endsection

<style>
     /* sheet bisa di-scroll sendiri */
    #modalSheet{
        max-height: 80vh;            /* atau pakai class Tailwind max-h-[80vh] */
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain; /* cegah "scroll chaining" ke belakang */
    }

    /* saat modal terbuka, kunci scroll body */
    body.modal-open {
        overflow: hidden;
        height: 100%;
    }

    /* Hide scrollbar for category filter */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Filter button style */
    .filter-btn {
        background-color: white;
        color: #000000;
        transition: all 0.2s;
    }
    .filter-btn:hover {
        background-color: #eff6ff; /* Tailwind blue-50 */
    }
    .filter-btn.active {
        background-color: #9A3F3F; /* choco */
        color: white;
    }

    /* animasi slide up modal */
    /* Modal overlay hitam */
    #parentOptionsModal {
        background-color: rgba(0,0,0,0.4);
    }

    /* Sheet modal */
    #modalSheet {
        /* awalnya di bawah layar */
        transform: translateY(100%);
        transition: transform 0.3s ease-out;
    }

    /* Saat modal ditampilkan */
    #parentOptionsModal.show #modalSheet {
        transform: translateY(0);
    }

    #floatingCartBar { padding-bottom: env(safe-area-inset-bottom); }
    /* animasi slide-up cart manager */
    #cartManagerModal.show #cartManagerSheet { transform: translateY(0); }

</style>

@push('scripts')
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

<script src="{{ asset('js/customer/menu/index.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('parentOptionsModal');
  const modalContent = document.getElementById('modalContent');
  const modalHeader = document.getElementById('modalHeader');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const saveModalBtn = document.getElementById('saveModalBtn');
  const productsData = @json($productsData);

  // === Body scroll lock (aman untuk iOS & tanpa animasi saat restore) ===
    let __savedScrollY = 0;
    let __prevScrollBehavior = '';

    function lockBodyScroll() {
    __savedScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;

    // Matikan smooth scroll sementara (kalau ada)
    const html = document.documentElement;
    __prevScrollBehavior = html.style.scrollBehavior;
    html.style.scrollBehavior = 'auto';

    document.body.classList.add('modal-open');
    // Teknik "fixed body" supaya benar2 terkunci
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

    // Pastikan restore TANPA animasi
    // Tunggu satu frame agar layout settle dulu
    requestAnimationFrame(() => {
        const y = __savedScrollY || 0;
        window.scrollTo({ top: y, left: 0, behavior: 'auto' });

        // Kembalikan scroll-behavior di frame berikutnya
        requestAnimationFrame(() => {
        html.style.scrollBehavior = __prevScrollBehavior || '';
        });
    });
    }

  // ===== STATE =====
  let currentProductId = null;
  let selectedOptions = [];
  let modalQty = 1; // <<=== jumlah qty di modal
  let modalNote = '';

  // Cart per kombinasi opsi:
  // key: `${productId}::${opt1-opt2-...}` (opsi diurut agar konsisten)
  // value: { productId: number, options: number[], qty: number }
  let cart = {};

  // Simpan "line item terakhir" per produk (agar tombol MINUS mengurangi yang paling baru ditambahkan)
  let lastKeyPerProduct = {};

  // ===== HELPERS =====
  const rupiahFmt = new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', minimumFractionDigits:0 });

  function getProductDataById(pid) {
  return productsData.find(p => p.id === pid) || {};
}

function computeUnitPrice(productId, optionsArr) {
  const pd = getProductDataById(productId);
  const base = Number(pd.price) || 0;

  // jumlahkan harga opsi terpilih
  const opts = (pd.parent_options || []);
  let optSum = 0;
  opts.forEach(po => {
    (po.options || []).forEach(opt => {
      if ((optionsArr || []).includes(opt.id)) {
        optSum += Number(opt.price) || 0;
      }
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


  function provisionInfoText(provision, value) {
    const prov = String(provision || '').toUpperCase();
    const val  = Number(value);
    const hasN = Number.isFinite(val);
    switch (prov) {
      case 'EXACT':        return hasN ? `Pilih ${val}` : 'Pilih';
      case 'MAX':          return hasN ? `Maksimal ${val}` : 'Maksimal';
      case 'MIN':          return hasN ? `Minimal ${val}` : 'Minimal';
      case 'OPTIONAL MAX': return hasN ? `Opsional, maksimal ${val}` : 'Opsional, maksimal';
      case 'OPTIONAL':     return 'Opsional';
      default:             return '';
    }
  }

  // Buat key kombinasi yang stabil (opsi diurut)
  function keyOf(productId, optionsArr) {
    const opts = (optionsArr || []).slice().sort((a,b)=>a-b).join('-'); // ex: "138-141"
    return `${productId}::${opts}`;                                     // ex: "14::138-141"
  }

  // Tambah 1 ke cart untuk kombinasi tertentu
  function addToCart(productId, optionsArr) {
    const key = keyOf(productId, optionsArr);
    if (!cart[key]) {
        cart[key] = {
        productId,
        options: (optionsArr || []).slice(),
        qty: 0,
        unitPrice: 0,
        lineTotal: 0,
        note: ''
        };
    }
    cart[key].qty += 1;
    recomputeLineTotal(key);
    lastKeyPerProduct[productId] = key;
    updateFloatingCartBar();
    return key;
    }

    function minusFromCart(productId, optionsArr) {
        const key = keyOf(productId, optionsArr);
        if (!cart[key]) return;
        cart[key].qty = Math.max(0, cart[key].qty - 1);
        if (cart[key].qty === 0) {
            delete cart[key];
        } else {
            recomputeLineTotal(key);
            updateFloatingCartBar();
        }
    }


  // Total qty agregat untuk sebuah productId (menjumlah seluruh line item produk tsb)
  function sumQtyByProduct(productId) {
    let total = 0;
    for (const k in cart) {
      if (cart[k].productId === productId) total += cart[k].qty;
    }
    return total;
  }

  // Update badge qty + visibility tombol minus di kartu produk
  function updateProductBadge(productId) {
    const qtySpan = document.getElementById('qty-' + productId);
    const minusBtn = document.querySelector('.minus-btn[data-id="' + productId + '"]');
    const total = sumQtyByProduct(productId);

    qtySpan.innerText = total;
    if (total > 0) {
      qtySpan.classList.remove('hidden');
      if (minusBtn) minusBtn.classList.remove('hidden');
    } else {
      qtySpan.classList.add('hidden');
      if (minusBtn) minusBtn.classList.add('hidden');
    }
  }

  // Debug print
  function printCart(label='Cart') {
    const rows = Object.entries(cart).map(([key, v]) => ({
        key,
        productId: v.productId,
        options: (v.options || []).join(','),
        qty: v.qty,
        unitPrice: v.unitPrice,
        lineTotal: v.lineTotal,
        Note: v.note
    }));
    console.log(label, cart);
    if (rows.length) console.table(rows);
}


  // ===== MODAL RENDERING =====
  function showModal(productData) {
    console.log('productData:', productData);

    // reset isi modal
    modalContent.innerHTML = '';
    modalHeader.innerHTML = '';
    modalQty = 1; // reset qty ke 1 setiap kali modal dibuka
    modalNote = '';

    // === header produk ===
    const headerWrapper = document.createElement('div');
    headerWrapper.classList.add('flex', 'gap-4', 'items-start', 'mb-4');

    // gambar produk
    if (productData.image) {
        const img = document.createElement('img');
        img.src = productData.image;
        img.alt = productData.name || 'Product Image';
        img.classList.add('w-20', 'h-20', 'rounded-md', 'object-cover', 'flex-shrink-0');
        headerWrapper.appendChild(img);
    }

    // info produk (nama & deskripsi)
    const infoDiv = document.createElement('div');

    const nameEl = document.createElement('h3');
    nameEl.classList.add('text-lg', 'font-semibold');
    nameEl.textContent = productData.name || '';
    infoDiv.appendChild(nameEl);

    const descEl = document.createElement('p');
    descEl.classList.add('text-sm', 'text-gray-500', 'line-clamp-2');
    descEl.textContent = productData.description || '';
    infoDiv.appendChild(descEl);

    headerWrapper.appendChild(infoDiv);
    modalHeader.appendChild(headerWrapper);

    // === parent options ===
    const parentOptions = productData.parent_options || [];
    parentOptions.forEach(po => {
        const poDiv = document.createElement('div');
        poDiv.classList.add('mb-2');
        poDiv.dataset.provision = po.provision;
        poDiv.dataset.value = po.provision_value;
        poDiv.setAttribute('data-provision-group','');

        const title = document.createElement('p');
        title.classList.add('font-semibold','mb-2','bg-gray-100','py-1');
        title.innerText = po.name;

        const info = provisionInfoText(po.provision, po.provision_value);
        if (info) {
        const infoSpan = document.createElement('span');
        infoSpan.classList.add('ml-2','text-gray-500','font-normal');
        infoSpan.textContent = '(' + info + ')';
        title.appendChild(infoSpan);
        }
        poDiv.appendChild(title);

        (po.options || []).forEach(opt => {
        const label = document.createElement('label');
        label.classList.add('flex','items-center','gap-2','w-full','py-1');

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.value = opt.id;
        checkbox.classList.add(
            'h-5','w-5','rounded-md','border-1','border-gray-500',
            'transition','focus:outline-none','focus:ring-2',
            'disabled:opacity-60','disabled:cursor-not-allowed'
        );

        const nameSpan = document.createElement('span');
        nameSpan.textContent = opt.name;
        nameSpan.classList.add('flex-1');

        const priceSpan = document.createElement('span');
        priceSpan.classList.add('ml-auto','text-sm','font-medium');

        const qty = Number(opt.quantity) || 0;
        const priceNum = Number(opt.price) || 0;

        if (qty < 1) {
            priceSpan.textContent = 'Habis';
            priceSpan.classList.add('text-red-600');
            checkbox.disabled = true;
            label.classList.add('line-through','opacity-60','cursor-not-allowed');
            const val = parseInt(checkbox.value,10);
            selectedOptions = selectedOptions.filter(v => v !== val);
        } else {
            priceSpan.textContent = (priceNum === 0) ? 'Free' : rupiahFmt.format(priceNum);
            const val = parseInt(checkbox.value,10);
            checkbox.checked = selectedOptions.includes(val);
            checkbox.addEventListener('change', function () {
            const v = parseInt(this.value,10);
            if (this.checked) {
                if (!selectedOptions.includes(v)) selectedOptions.push(v);
            } else {
                selectedOptions = selectedOptions.filter(x => x !== v);
            }
            });
        }

        label.appendChild(checkbox);
        label.appendChild(nameSpan);
        label.appendChild(priceSpan);
        poDiv.appendChild(label);
        });

        modalContent.appendChild(poDiv);

        // enforce UX rules for this parent group
        enforceProvision(poDiv, po.provision, po.provision_value);
        calcModalTotal(productData); // hitung pertama kali
    });

    // === Catatan (textarea) ===
    const noteWrap = document.createElement('div');
    noteWrap.classList.add('mt-4');

    const noteLabel = document.createElement('label');
    noteLabel.classList.add('block','text-sm','font-semibold','mb-1');
    noteLabel.textContent = 'Catatan (opsional)';

    const noteArea = document.createElement('textarea');
    noteArea.id = 'modalNote';                                        // for later query
    noteArea.classList.add('w-full','min-h-[72px]','p-2','rounded-md','border','border-gray-300','focus:outline-none','focus:ring-2','focus:ring-choco/40');
    noteArea.placeholder = 'Contoh: “Pedas level 2, saus terpisah, tanpa bawang.”';
    noteArea.maxLength = 200;                                         // batasi 200 karakter
    noteArea.addEventListener('input', (e) => { modalNote = e.target.value; });

    const noteHint = document.createElement('p');
    noteHint.classList.add('text-xs','text-gray-500','mt-1');
    noteHint.textContent = 'Maks. 200 karakter.';

    noteWrap.appendChild(noteLabel);
    noteWrap.appendChild(noteArea);
    noteWrap.appendChild(noteHint);
    modalContent.appendChild(noteWrap);


    // tampilkan modal
    modal.classList.add('show');
    lockBodyScroll();
    modal.classList.remove('hidden');
    }

    // ===== UI qty di modal =====
    const modalQtyMinus = document.getElementById('modalQtyMinus');
    const modalQtyPlus  = document.getElementById('modalQtyPlus');
    const modalQtyValue = document.getElementById('modalQtyValue');

    function updateModalQtyDisplay() {
        modalQtyValue.innerText = modalQty;
        modalQtyMinus.disabled = modalQty <= 1;
    }

    modalQtyMinus.addEventListener('click', () => {
        if (modalQty > 1) {
            modalQty--;
            updateModalQtyDisplay();
            if (currentProductId) {
                const pd = productsData.find(p => p.id === currentProductId);
                calcModalTotal(pd);
            }
        }
    });

    modalQtyPlus.addEventListener('click', () => {
        modalQty++;
        updateModalQtyDisplay();
        if (currentProductId) {
            const pd = productsData.find(p => p.id === currentProductId);
            calcModalTotal(pd);
        }
    });


    // SAVE (modal): simpan sebagai line item per kombinasi opsi
    saveModalBtn.addEventListener('click', function () {
        if (!currentProductId) return;

        const key = keyOf(currentProductId, selectedOptions);
        if (!cart[key]) {
            cart[key] = {
            productId: currentProductId,
            options: selectedOptions.slice(),
            qty: 0,
            unitPrice: 0,
            lineTotal: 0,
            note: '' // NEW
            };
        }

        // ambil note dari textarea (fallback ke state)
        const noteEl = document.getElementById('modalNote');
        const noteVal = (noteEl ? noteEl.value : modalNote || '').trim();

        // simpan/overwrite note untuk line item ini (tidak mempengaruhi kombinasi lain)
        if (noteVal.length > 0) {
            cart[key].note = noteVal;
        }

        cart[key].qty += modalQty;          // sesuai jumlah di modal
        recomputeLineTotal(key);
        updateFloatingCartBar();

        lastKeyPerProduct[currentProductId] = key;

        updateProductBadge(currentProductId);
        printCart('Cart (saved with options):');

        // reset state + tutup modal
        currentProductId = null;
        selectedOptions = [];
        modalQty = 1;
        modalNote = '';                      // <<< reset note
        updateModalQtyDisplay();
        modal.classList.remove('show');
        setTimeout(() => {
            modal.classList.add('hidden');
            unlockBodyScroll();
        }, 300);
    });



  // ===== UI EVENTS =====

  // PLUS: jika ada parent_options → buka modal; kalau tidak → tambah line item tanpa opsi ([])
  document.querySelectorAll('.plus-btn').forEach(button => {
    button.addEventListener('click', function () {
      const productId = parseInt(this.dataset.id);
      const productData = productsData.find(p => p.id === productId);

      if (productData && (productData.parent_options || []).length > 0) {
        currentProductId = productId;
        selectedOptions = [];
        showModal(productData);
      } else {
        // langsung tambah kombinasi tanpa opsi
        addToCart(productId, []);
        updateProductBadge(productId);
        printCart('Cart (no-options +):');
      }
    });
  });

  // MINUS: kurangi total agregat produk dengan mengurangi line item "terakhir"
  document.querySelectorAll('.minus-btn').forEach(button => {
    button.addEventListener('click', function () {
      const productId = parseInt(this.dataset.id);

      // Tentukan line item mana yang dikurangi:
      // 1) coba yang terakhir
      let key = lastKeyPerProduct[productId];
      // 2) fallback: cari line item apapun yang qty>0
      if (!key || !cart[key] || cart[key].qty === 0) {
        key = Object.keys(cart).find(k => cart[k].productId === productId && cart[k].qty > 0);
      }
      if (!key) {
        // tidak ada line item utk produk ini
        updateProductBadge(productId);
        printCart('Cart (minus noop):');
        return;
      }

      const line = cart[key];
      minusFromCart(productId, line.options);
      updateProductBadge(productId);
      printCart('Cart (minus):');
    });
  });

  closeModalBtn.addEventListener('click', function () {
    modal.classList.remove('show');
    setTimeout(() => {
        modal.classList.add('hidden');
        unlockBodyScroll(); // <<< penting
    }, 300);
    });

  // SAVE (modal): simpan sebagai line item per kombinasi opsi
  saveModalBtn.addEventListener('click', function () {
    if (!currentProductId) return;

    // tambah 1 ke kombinasi yang dipilih
    addToCart(currentProductId, selectedOptions);

    // update badge agregat di kartu produk
    updateProductBadge(currentProductId);
    printCart('Cart (saved with options):');

    // reset state + tutup modal
    currentProductId = null;
    selectedOptions = [];
    modal.classList.remove('show');
    setTimeout(() => {
        modal.classList.add('hidden');
        unlockBodyScroll(); // <<< penting
    }, 300);
  });

  function enforceProvision(poDiv, provision, value) {
        const checkboxes = Array.from(poDiv.querySelectorAll('input[type="checkbox"]'));
        const prov = String(provision || '').toUpperCase();
        const val = Number(value);

        function updateState() {
            const checked = checkboxes.filter(c => c.checked);

            // === aturan provision ===
            if (prov === 'EXACT') {
                if (checked.length >= val) {
                    checkboxes.forEach(c => { if (!c.checked) c.disabled = true; });
                } else {
                    checkboxes.forEach(c => c.disabled = false);
                }
            }

            if (prov === 'MAX' || prov === 'OPTIONAL MAX') {
                if (checked.length >= val) {
                    checkboxes.forEach(c => { if (!c.checked) c.disabled = true; });
                } else {
                    checkboxes.forEach(c => c.disabled = false);
                }
            }

            if (prov === 'MIN') {
                if (checked.length <= val) {
                    checkboxes.forEach(c => {
                        if (c.checked) {
                            c.onchange = (e) => {
                                if (checked.length <= val) {
                                    e.target.checked = true; // paksa tetap checked
                                }
                                updateState();
                            };
                        }
                    });
                }
            }

            // === sinkronkan selectedOptions global ===
            selectedOptions = Array.from(
                modalContent.querySelectorAll('input[type="checkbox"]:checked')
            ).map(c => parseInt(c.value, 10));

            console.log('selectedOptions:', selectedOptions);

            if (currentProductId) {
                const pd = productsData.find(p => p.id === currentProductId);
                calcModalTotal(pd);
            }

            validateAllProvisions();
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateState));
        updateState(); // initial check
        }


    function validateAllProvisions() {
        const poGroups = Array.from(modalContent.querySelectorAll('[data-provision-group]'));
        let allValid = true;

        poGroups.forEach(group => {
            const prov = group.dataset.provision;
            const val = Number(group.dataset.value);
            const checkboxes = Array.from(group.querySelectorAll('input[type="checkbox"]'));
            const checked = checkboxes.filter(c => c.checked).length;

            if (prov === 'EXACT' && checked !== val) allValid = false;
            if (prov === 'MAX' && (checked < 1 || checked > val)) allValid = false;
            if (prov === 'MIN' && checked < val) allValid = false;
            if (prov === 'OPTIONAL MAX' && checked > val) allValid = false;
            // OPTIONAL selalu valid
        });

        saveModalBtn.disabled = !allValid;
        saveModalBtn.classList.toggle('opacity-50', !allValid);
        saveModalBtn.classList.toggle('cursor-not-allowed', !allValid);
    }

    // Hitung total harga di modal
    function calcModalTotal(productData) {
        let basePrice = Number(productData.price) || 0;

        // harga option terpilih
        const optionPrice = (productData.parent_options || []).reduce((sum, po) => {
            (po.options || []).forEach(opt => {
                if (selectedOptions.includes(opt.id)) {
                    sum += Number(opt.price) || 0;
                }
            });
            return sum;
        }, 0);

        const total = (basePrice + optionPrice) * modalQty;
        document.getElementById('modalTotalPrice').innerText = `(${rupiahFmt.format(total)})`;
    }

    // === Floating Cart helpers ===
    function cartGrandTotal() {
    let total = 0;
    for (const k in cart) {
        const row = cart[k];
        if (!row) continue;
        const unit = (typeof row.unitPrice === 'number' && row.unitPrice > 0)
        ? row.unitPrice
        : computeUnitPrice(row.productId, row.options);
        total += unit * row.qty;
    }
    return total;
    }

    function cartTotalQty() {
    let total = 0;
    for (const k in cart) {
        total += cart[k].qty || 0;
    }
    return total;
    }

    function updateFloatingCartBar() {
        const bar     = document.getElementById('floatingCartBar');
        const totalEl = document.getElementById('floatingCartTotal');
        const countEl = document.getElementById('floatingCartCount');

        const total = cartGrandTotal();
        const count = cartTotalQty();

        if (count > 0) {
            totalEl.textContent = rupiahFmt.format(total);
            countEl.textContent = `(${count})`;
            bar.classList.remove('hidden');
        } else {
            bar.classList.add('hidden');
        }
    }


    // ===== Cart Manager Modal =====
    const cartManagerModal  = document.getElementById('cartManagerModal');
    const cartManagerSheet  = document.getElementById('cartManagerSheet');
    const cartManagerBody   = document.getElementById('cartManagerBody');
    const cartManagerTotal  = document.getElementById('cartManagerTotal');
    const closeCartManager  = document.getElementById('closeCartManager');
    const cartManagerDone   = document.getElementById('cartManagerDone');

    // helper: ambil nama opsi dari id
    function optionNameById(productData, optId) {
    for (const po of (productData.parent_options || [])) {
        for (const opt of (po.options || [])) {
        if (opt.id === optId) return opt.name;
        }
    }
    return null;
    }

    // kumpulkan isi cart jadi array untuk dirender
    function cartRows() {
    const arr = [];
    for (const k in cart) {
        const row = cart[k];
        if (!row || row.qty <= 0) continue;
        const pd = productsData.find(p => p.id === row.productId) || {};
        const optNames = (row.options || [])
        .map(id => optionNameById(pd, id))
        .filter(Boolean);
        const unit = (typeof row.unitPrice === 'number' && row.unitPrice > 0)
        ? row.unitPrice
        : computeUnitPrice(row.productId, row.options);
        const line = unit * row.qty;
        arr.push({
        key: k,
        productId: row.productId,
        productName: pd.name || 'Produk',
        image: pd.image || null,
        desc: pd.description || '',
        optNames,
        qty: row.qty,
        unit,
        line,
        options: row.options || [],
        note: row.note || ''
        });
    }
    return arr;
    }

    function openCartManager() {
    // render konten
    renderCartManager();
    cartManagerModal.classList.add('show');
    cartManagerModal.classList.remove('hidden');
    // kunci body scroll (opsional, reuse fungsi kamu)
    if (typeof lockBodyScroll === 'function') lockBodyScroll();
    }

    function closeCartManagerModal() {
    cartManagerModal.classList.remove('show');
    setTimeout(() => {
        cartManagerModal.classList.add('hidden');
        if (typeof unlockBodyScroll === 'function') unlockBodyScroll();
    }, 300);
    }

    function renderCartManager() {
    const rows = cartRows();

    if (rows.length === 0) {
        cartManagerBody.innerHTML = `
        <div class="p-6 text-center text-gray-500">
            Keranjang masih kosong.
        </div>`;
        cartManagerTotal.textContent = rupiahFmt.format(0);
        return;
    }

    cartManagerBody.innerHTML = rows.map(r => {
        const optsText  = r.optNames.length ? `<p class="text-xs text-gray-500 line-clamp-1">${r.optNames.join(', ')}</p>` : '';
        const noteText  = r.note ? `<p class="text-xs text-gray-600 mt-1 italic line-clamp-2">Catatan: ${r.note}</p>` : '';
        const img = r.image ? `<img src="${r.image}" class="w-16 h-16 rounded-md object-cover flex-shrink-0" alt="">` : '';
        return `
            <div class="p-3 flex items-center gap-3" data-key="${r.key}" data-product-id="${r.productId}">
            ${img}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 line-clamp-1">${r.productName}</p>
                ${optsText}
                ${noteText} <!-- <<< NEW -->
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


    // total
    const grand = rows.reduce((s, r) => s + r.line, 0);
    cartManagerTotal.textContent = rupiahFmt.format(grand);
    }

    // delegasi event +/− dalam modal
    cartManagerBody.addEventListener('click', (e) => {
    const plusBtn  = e.target.closest('.cm-plus');
    const minusBtn = e.target.closest('.cm-minus');
    if (!plusBtn && !minusBtn) return;

    const rowEl = e.target.closest('[data-key]');
    if (!rowEl) return;

    const key = rowEl.getAttribute('data-key');
    const row = cart[key];
    if (!row) return;

    if (plusBtn) {
        addToCart(row.productId, row.options);              // tambah 1
    } else if (minusBtn) {
        minusFromCart(row.productId, row.options);          // kurang 1
    }

    // sinkron UI lain
    updateProductBadge(row.productId);
    updateFloatingCartBar();
    // re-render modal
    renderCartManager();
    });

    // buka modal saat klik ikon trash
    document.getElementById('floatingCartClear').addEventListener('click', (ev) => {
    ev.preventDefault();
    // kalau cart kosong, tetap buka modal agar user tahu kosong
    openCartManager();
    });

    // tutup modal (X atau Selesai atau klik backdrop)
    closeCartManager.addEventListener('click', closeCartManagerModal);
    cartManagerDone.addEventListener('click', closeCartManagerModal);
    cartManagerModal.addEventListener('click', (e) => {
    if (e.target === cartManagerModal) closeCartManagerModal();
    });


    // --- Helpers untuk opsi (nama & harga) ---
    function getOptionDetail(productId, optId) {
        const pd = productsData.find(p => p.id === productId);
        if (!pd) return { name: null, price: 0 };
        for (const po of (pd.parent_options || [])) {
            for (const opt of (po.options || [])) {
            if (opt.id === optId) {
                return { name: opt.name, price: Number(opt.price) || 0, parentName: po.name || null };
            }
            }
        }
        return { name: null, price: 0 };
        }

        // Build baris checkout (dengan rincian opsi & harga)
        function checkoutRows() {
            const rows = [];
            for (const k in cart) {
                const r = cart[k];
                if (!r || r.qty <= 0) continue;

                const pd = productsData.find(p => p.id === r.productId) || {};
                const unit = (typeof r.unitPrice === 'number' && r.unitPrice > 0)
                ? r.unitPrice
                : computeUnitPrice(r.productId, r.options);

                const optionsDetail = (r.options || []).map(oid => getOptionDetail(r.productId, oid));

                rows.push({
                key: k,
                productId: r.productId,
                name: pd.name || 'Produk',
                image: pd.image || null,            // <<< NEW
                note: r.note || '',
                qty: r.qty,
                unit,
                line: unit * r.qty,
                basePrice: Number(pd.price) || 0,
                optionsDetail
                });
            }
            return rows;
        }


        function rupiah(n) { return rupiahFmt.format(n || 0); }

        // --- Render isi modal checkout ---
        function renderCheckoutModal() {
        const body = document.getElementById('checkoutBody');
        const totalEl = document.getElementById('checkoutGrandTotal');

        const rows = checkoutRows();
        if (rows.length === 0) {
            body.innerHTML = `
            <div class="text-center text-gray-500 py-8">
                Keranjang masih kosong.
            </div>`;
            totalEl.textContent = rupiah(0);
            return;
        }

        // item cards
        body.innerHTML = rows.map(r => {
            // breakdown opsi (label kiri, harga kanan)
            const opts = r.optionsDetail.map(od => {
                const label = od.parentName ? `${od.parentName}: ${od.name}` : od.name;
                return `
                <div class="w-full flex items-center justify-between text-xs text-gray-600">
                    <span class="truncate">${label}</span>
                    <span class="shrink-0">${od.price === 0 ? '(Free)' : rupiah(od.price)}</span>
                </div>
                `;
            }).join('');

            const note = r.note
                ? `<div class="mt-2 text-xs italic text-gray-700">Catatan: ${r.note}</div>`
                : '';

            return `
                <div class="border rounded-lg p-3">
                <!-- HEADER: Gambar + Nama kiri, Qty kanan (SEBARIS) -->
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

                <!-- DETAIL: harga dasar & opsi (harga rata kanan) -->
                <div class="mt-1 space-y-0.5">
                    <div class="w-full flex items-center justify-between text-xs text-gray-600">
                    <span>Harga dasar</span>
                    <span class="shrink-0">${rupiah(r.basePrice)}</span>
                    </div>
                    ${opts}
                </div>

                ${note}

                <!-- SUBTOTAL -->
                <div class="mt-2 w-full flex items-center justify-between">
                    <span class="text-xs text-gray-500">Subtotal</span>
                    <span class="font-semibold">${rupiah(r.line)}</span>
                </div>
                </div>
            `;
            }).join('');



        const grand = rows.reduce((s, r) => s + r.line, 0);
        totalEl.textContent = rupiah(grand);
        }

        // --- Buka/Tutup modal checkout ---
        const checkoutModal    = document.getElementById('checkoutModal');
        const checkoutSheet    = document.getElementById('checkoutSheet');
        const checkoutCloseBtn = document.getElementById('checkoutCloseBtn');
        const checkoutCancelBtn= document.getElementById('checkoutCancelBtn');
        const checkoutPayBtn   = document.getElementById('checkoutPayBtn');
        const paymentMethodSel = document.getElementById('paymentMethod');
        const orderNameInput = document.getElementById('orderName');

        document.getElementById('floatingCartPay').addEventListener('click', () => {
            // buka modal konfirmasi
            openCheckoutModal();
        });

        // Saat user menekan "Pembayaran"
        checkoutPayBtn.addEventListener('click', async () => {
            const paymentMethod = paymentMethodSel.value; // 'CASH' | 'QRIS'
            const orderName = orderNameInput.value;

            const payload = Object.entries(cart).map(([key, r]) => ({
                product_id: r.productId,
                option_ids: r.options,
                qty: r.qty,
                unit_price: r.unitPrice ?? computeUnitPrice(r.productId, r.options),
                note: r.note || ''
            }));

            const grandTotal = payload.reduce((s, it) => s + (it.unit_price * it.qty), 0);

            // Validasi ringan
            if (!paymentMethod) {
                await Swal.fire({ icon:'warning', title:'Metode pembayaran belum dipilih', text:'Silakan pilih Cash atau QRIS dahulu.' });
                return;
            }
            if (!orderName) {
                await Swal.fire({ icon:'warning', title:'Nama Pemesan belum diisi', text:'Silakan isi nama pemesan terlebih dahulu.' });
                return;
            }
            if (grandTotal <= 0) {
                await Swal.fire({ icon:'info', title:'Keranjang kosong', text:'Tambahkan item sebelum checkout.' });
                return;
            }

            // Konfirmasi ringkas
            const confirm = await Swal.fire({
                icon:'question',
                title:'Konfirmasi Checkout',
                html: `
                <div style="text-align:left">
                    <div>Nama Pemesan: <b>${orderName}</b></div>
                    <div>Metode: <b>${paymentMethod.toUpperCase()}</b></div>
                    <div>Total: <b>${rupiahFmt.format(grandTotal)}</b></div>
                </div>`,
                showCancelButton:true,
                confirmButtonText:'Ya, bayar',
                cancelButtonText:'Batal'
            });
            if (!confirm.isConfirmed) return;

            // Siapkan body & URL
            const body = {
                items: payload,
                payment_method: paymentMethod,
                order_name: orderName,
                total_amount: grandTotal
            };

            const PARTNER_SLUG = @json($partner_slug);
            const TABLE_CODE   = @json($table_code);
            const checkoutUrl  = `/customer/${encodeURIComponent(PARTNER_SLUG)}/checkout/${encodeURIComponent(TABLE_CODE)}`;

            // CSRF
            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const csrf    = tokenEl ? tokenEl.content : null;

            // Loading
            checkoutPayBtn.disabled = true;
            Swal.fire({ title:'Memproses pembayaran…', allowOutsideClick:false, didOpen:() => Swal.showLoading() });

            try {
                const r = await fetch(checkoutUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(csrf ? { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' } : {})
                },
                credentials: 'same-origin',
                body: JSON.stringify(body)
                });

                // Jika server melakukan redirect 302 dan fetch mengikutinya:
                if (r.redirected && r.url) {
                window.location.assign(r.url);
                return; // stop di sini
                }

                // Coba parse JSON
                let res;
                const ct = r.headers.get('content-type') || '';
                if (ct.includes('application/json')) {
                res = await r.json();
                } else {
                const text = await r.text();
                try { res = JSON.parse(text); }
                catch { throw new Error('Respons tidak valid dari server.'); }
                }

                if (!r.ok) {
                throw new Error(res?.message || `Request failed: ${r.status}`);
                }

                // Ekspektasi terbaik: controller mengembalikan { redirect: "..." }
                if (res?.redirect) {
                window.location.assign(res.redirect);
                return;
                }

                // Fallback (kalau tidak ada redirect di JSON)
                Swal.close();
                await Swal.fire({ icon:'success', title:'Berhasil', text:'Checkout berhasil diproses.', timer:1400, showConfirmButton:false });
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

        function openCheckoutModal() {
            renderCheckoutModal();
            // reset pilihan pembayaran
            paymentMethodSel.value = '';
            checkoutPayBtn.disabled = true;

            checkoutModal.classList.remove('hidden');
            // animasi slide-up di mobile
            requestAnimationFrame(() => {
                checkoutSheet.classList.remove('translate-y-full');
            });
            if (typeof lockBodyScroll === 'function') lockBodyScroll();
            // fokus tanpa "lompat" layar
            orderNameInput.focus({ preventScroll: true });
        }

        function closeCheckoutModal() {
            checkoutSheet.classList.add('translate-y-full');
            setTimeout(() => {
                checkoutModal.classList.add('hidden');
                if (typeof unlockBodyScroll === 'function') unlockBodyScroll();
            }, 250);
        }

        // helper: tombol aktif kalau metode terpilih & nama terisi
        function updatePayBtnState() {
            const hasMethod = !!paymentMethodSel.value;
            const hasName   = !!(orderNameInput.value || '').trim();
            checkoutPayBtn.disabled = !(hasMethod && hasName);
        }
        // dengarkan perubahan di keduanya
        paymentMethodSel.addEventListener('change', updatePayBtnState);
        orderNameInput.addEventListener('input', updatePayBtnState);
        // set state awal saat modal dibuka
        updatePayBtnState();



        // Tutup modal handlers
        checkoutCloseBtn.addEventListener('click', closeCheckoutModal);
        checkoutCancelBtn.addEventListener('click', closeCheckoutModal);
        checkoutModal.addEventListener('click', (e) => {
        if (e.target === checkoutModal) closeCheckoutModal();
    });


});
</script>
@endpush
