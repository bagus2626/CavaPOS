{{-- =========================== MODALS (dipakai file ini) =========================== --}}

<!-- Floating Cart Bar -->
<div id="floatingCartBar"
     class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 shadow-[0_-6px_20px_rgba(0,0,0,0.12)] hidden"
     style="padding-bottom: env(safe-area-inset-bottom);">
  <div class="max-w-screen-md mx-auto px-4 py-3 flex items-center gap-3">
    <div class="flex-1">
      <p class="text-xs text-gray-500 leading-none">Total</p>
      <p id="floatingCartTotal" class="text-lg font-extrabold text-gray-900">Rp 0</p>
    </div>

    <button id="floatingCartClear"
          class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50"
          aria-label="Buka keranjang"
          title="Keranjang">
      <!-- SVG cart -->
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-choco" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M6 6h15l-1.5 9h-12L6 6Zm0 0L5 3H2m6 19a1 1 0 100-2 1 1 0 000 2Zm10 0a1 1 0 100-2 1 1 0 000 2Z" />
      </svg>
  </button>


    <button id="floatingCartPay" class="px-4 py-2 rounded-lg bg-choco text-white font-semibold hover:bg-soft-choco">
      Checkout <span id="floatingCartCount" class="ml-1 text-white/90 text-sm"></span>
    </button>
  </div>
</div>

<!-- Cart Manager Modal -->
<div id="cartManagerModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40"></div>
  <div id="cartManagerSheet"
       class="absolute left-0 right-0 bottom-0 bg-white rounded-t-2xl shadow-xl max-h-[80vh] overflow-y-auto transform translate-y-full transition-transform duration-300">
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
      <h3 class="text-lg font-semibold">Keranjang</h3>
      <button id="closeCartManager" class="text-2xl leading-none px-2">&times;</button>
    </div>
    <div id="cartManagerBody" class="divide-y divide-gray-100"></div>
    <div class="p-4 border-t border-gray-100 flex items-center justify-between">
      <div>
        <p class="text-xs text-gray-500">Total</p>
        <p id="cartManagerTotal" class="text-xl font-extrabold">Rp 0</p>
      </div>
      <button id="cartManagerDone" class="px-4 py-2 rounded-lg bg-choco text-white font-semibold hover:bg-soft-choco">Selesai</button>
    </div>
  </div>
</div>

{{-- Modal Parent Options --}}
<div id="parentOptionsModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden">
  <div id="modalSheet"
       class="bg-white w-full max-h-[80vh] rounded-t-2xl p-4 absolute bottom-0 left-0
              transform translate-y-full transition-transform duration-300
              overflow-hidden flex flex-col">
    <button id="closeModalBtn" class="absolute top-2 right-4 text-xl font-bold">&times;</button>

    {{-- Header Produk --}}
    <div id="modalHeader" class="shrink-0 flex flex-col gap-4"></div>

    <h3 class="shrink-0 text-lg font-semibold mt-2">Pilih Opsi</h3>

    {{-- Scrollable area --}}
    <div id="modalScrollArea" class="min-h-0 flex-1 overflow-y-auto pr-1 -mr-1" style="-webkit-overflow-scrolling: touch;">
      <div id="modalContent" class="flex flex-col gap-4 pb-4"></div>
    </div>

    {{-- Qty control --}}
    <div class="shrink-0 flex items-center justify-center gap-4 mt-2">
      <button id="modalQtyMinus" class="w-9 h-9 flex items-center justify-center border border-choco rounded-lg font-bold text-choco hover:bg-gray-100">-</button>
      <span id="modalQtyValue" class="text-lg font-semibold text-gray-800">1</span>
      <button id="modalQtyPlus" class="w-9 h-9 flex items-center justify-center border border-choco rounded-lg font-bold text-white bg-choco hover:bg-soft-choco">+</button>
    </div>

    {{-- Save --}}
    <div class="shrink-0 flex justify-end mt-4">
      <button id="saveModalBtn"
              class="px-4 py-1 w-full rounded-md bg-choco text-white hover:bg-soft-choco font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
              disabled>
        Simpan <span id="modalTotalPrice" class="modalTotalPrice font-light text-white">Rp 0</span>
      </button>
    </div>
  </div>
</div>

{{-- Checkout Confirmation Modal --}}
<div id="checkoutModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="checkoutTitle" aria-hidden="true">
  <div class="absolute inset-0 bg-black/40"></div>
  <div class="absolute inset-x-0 bottom-0 sm:inset-0 sm:flex sm:items-center sm:justify-center">
    <div id="checkoutSheet" class="bg-white rounded-t-2xl sm:rounded-2xl shadow-xl w-full sm:max-w-lg translate-y-full sm:translate-y-0 transition-transform duration-300 flex flex-col max-h[85vh] sm:max-h-[85vh] overflow-hidden">
      <div class="p-4 border-b flex items-center justify-between">
        <h3 id="checkoutTitle" class="text-lg font-semibold">Konfirmasi Pesanan</h3>
        <button id="checkoutCloseBtn" type="button" class="p-2 rounded-md hover:bg-gray-100" aria-label="Tutup">✕</button>
      </div>

      <div id="checkoutScroll" class="flex-1 overflow-y-auto overscroll-contain" style="-webkit-overflow-scrolling: touch;">
        <div id="checkoutBody" class="p-4 space-y-3"></div>

        <div class="p-4 border-t space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Total</span>
            <span id="checkoutGrandTotal" class="text-xl font-extrabold">Rp 0</span>
          </div>

          <div>
            <label for="orderName" class="block text-sm font-medium mb-1">Nama Pemesan</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.1a7.5 7.5 0 0115 0V21H4.5v-.9z"/></svg>
              </span>
              <input id="orderName" name="customer_name" type="text" inputmode="text"
                     placeholder="Contoh: Budi Setiawan"
                     value="{{ old('customer_name', $autofillName ?? '') }}"
                     data-default-name="{{ $autofillName ?? '' }}"
                     autocomplete="name" autocapitalize="words" maxlength="60"
                     @if(!empty($lockName)) readonly aria-readonly="true" @endif
                     class="w-full rounded-lg border border-gray-300 pl-10 pr-10 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-choco/40 focus:border-choco" required />
              @empty($lockName)
                <button type="button" id="clearOrderName" class="absolute inset-y-0 right-2 px-2 text-gray-400 hover:text-gray-600" aria-label="Hapus nama">✕</button>
              @endempty
            </div>
            @if(!empty($lockName))
              <p class="mt-1 text-xs text-gray-500">Nama diambil dari akun Anda.</p>
            @else
              <p class="mt-1 text-xs text-gray-500">Isi nama agar pesanan mudah dipanggil.</p>
            @endif
          </div>

          {{-- Pilih Meja --}}
            <div>
              <label for="orderTable" class="block text-sm font-medium mb-1">Pilih Meja</label>
              <select id="orderTable"
                      class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-choco/40"
                      required>
                  <option value="" selected disabled>Pilih meja</option>
                  @foreach($tables as $table)
                      @php
                          $isDisabled = $table->status === 'not_available';
                          $statusLabel = '';
                          
                          switch($table->status) {
                              case 'occupied':
                                  $statusLabel = ' (Sedang Sibuk)';
                                  break;
                              case 'reserved':
                                  $statusLabel = ' (Sudah Dipesan)';
                                  break;
                              case 'not_available':
                                  $statusLabel = ' (Tidak Tersedia)';
                                  break;
                          }
                      @endphp
                      
                      <option value="{{ $table->id }}"
                              data-table-no="{{ $table->table_no }}"
                              data-table-class="{{ $table->table_class }}"
                              data-table-status="{{ $table->status }}"
                              @if($isDisabled) disabled @endif
                              @if($isDisabled) class="text-gray-400" @endif>
                          Meja {{ $table->table_no }} — {{ $table->table_class }}{{ $statusLabel }}
                      </option>
                  @endforeach
              </select>
              <p class="mt-1 text-xs text-gray-500">
                  Meja yang tidak tersedia tidak dapat dipilih
              </p>
          </div>
          <div>
            <label for="paymentMethod" class="block text-sm font-medium mb-1">Metode Pembayaran</label>
            <select id="paymentMethod" class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-choco/40" required>
              <option value="" selected disabled>Pilih metode</option>
              <option value="CASH">Cash</option>
              <option value="QRIS">QRIS</option>
            </select>
          </div>
        </div>
      </div>

      <div class="p-4 border-t bg-white">
        <div class="flex gap-2">
          <button id="checkoutCancelBtn" type="button" class="flex-1 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
          <button id="checkoutPayBtn" type="button" class="flex-1 py-2 rounded-lg bg-choco text-white font-semibold disabled:opacity-50 disabled:cursor-not-allowed">Pembayaran</button>
        </div>
      </div>
    </div>
  </div>
</div>
