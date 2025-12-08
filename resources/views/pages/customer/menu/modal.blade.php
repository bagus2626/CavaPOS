<!-- Floating Cart Bar -->
<div id="floatingCartBar"
    class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 shadow-[0_-6px_20px_rgba(0,0,0,0.12)] hidden"
    style="padding-bottom: env(safe-area-inset-bottom);">
    <div class="max-w-screen-md mx-auto px-4 py-3 flex items-center gap-3">
        <div class="flex-1">
            <p class="text-xs text-gray-500 leading-none">Total</p>
            <p id="floatingCartTotal" class="text-lg font-extrabold text-gray-900">Rp 0</p>
        </div>

        <!-- Clear / Trash -->
        <button id="floatingCartClear" class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50"
            aria-label="Hapus keranjang">
            <!-- simple trash icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M6 7h12M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m1 0v12a2 2 0 01-2 2H8a2 2 0 01-2-2V7m3 4v6m4-6v6" />
            </svg>
        </button>

        <!-- Pay -->
        <button id="floatingCartPay" class="px-4 py-2 rounded-lg bg-choco text-white font-semibold hover:bg-soft-choco">
            {{ __('messages.customer.menu.checkout') }} <span id="floatingCartCount"
                class="ml-1 text-white/90 text-sm"></span>
        </button>
    </div>
</div>

<!-- Cart Manager Modal -->
<div id="cartManagerModal" class="fixed inset-0 z-50 hidden">
    <!-- backdrop -->
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- sheet -->
    <div class="absolute left-0 right-0 bottom-0 bg-white rounded-t-2xl shadow-xl
              max-h-[80vh] overflow-y-auto transform translate-y-full
              transition-transform duration-300"
        id="cartManagerSheet">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold">{{ __('messages.customer.menu.cart') }}</h3>
            <button id="closeCartManager" class="text-2xl leading-none px-2">&times;</button>
        </div>

        <div id="cartManagerBody" class="divide-y divide-gray-100">
            <!-- Baris item cart dirender via JS -->
        </div>

        <div class="p-4 border-t border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Total</p>
                <p id="cartManagerTotal" class="text-xl font-extrabold">Rp 0</p>
            </div>
            <button id="cartManagerDone"
                class="px-4 py-2 rounded-lg bg-choco text-white font-semibold hover:bg-soft-choco">
                {{ __('messages.customer.menu.finish') }}
            </button>
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

        {{-- Header Produk (tetap) --}}
        <div id="modalHeader" class="shrink-0 flex flex-col gap-4">
            {{-- Diisi via JS: gambar + nama + deskripsi --}}
        </div>

        <h3 class="shrink-0 text-lg font-semibold mt-2" id="choose-option">{{ __('messages.customer.menu.pilih_opsi') }}</h3>

        {{-- AREA SCROLLABLE: hanya parent options + note yang bisa di-scroll --}}
        <div id="modalScrollArea" class="min-h-0 flex-1 overflow-y-auto pr-1 -mr-1"
            style="-webkit-overflow-scrolling: touch;">
            <div id="modalContent" class="flex flex-col gap-4 pb-4">
                {{-- Parent options & textarea catatan diisi via JS --}}
            </div>
        </div>

        {{-- Kontrol Qty (tetap) --}}
        <div class="shrink-0 flex items-center justify-center gap-4 mt-2">
            <button id="modalQtyMinus"
                class="w-9 h-9 flex items-center justify-center border border-choco rounded-lg font-bold text-choco hover:bg-gray-100">-</button>
            <span id="modalQtyValue" class="text-lg font-semibold text-gray-800">1</span>
            <button id="modalQtyPlus"
                class="w-9 h-9 flex items-center justify-center border border-choco rounded-lg font-bold text-white bg-choco hover:bg-soft-choco">+</button>
        </div>

        {{-- Tombol Simpan (tetap) --}}
        <div class="shrink-0 flex justify-end mt-4">
            <button id="saveModalBtn"
                class="px-4 py-1 w-full rounded-md bg-choco text-white hover:bg-soft-choco font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                disabled>
                {{ __('messages.customer.menu.simpan') }} <span id="modalTotalPrice"
                    class="modalTotalPrice font-light text-white">Rp 0</span>
            </button>
        </div>
    </div>
</div>



<!-- Checkout Confirmation Modal -->
@php
    $customerUser = Illuminate\Support\Facades\Auth::guard('customer')->user();
    $autofillName = optional($customerUser)->name ?? (optional(session('guest_customer'))->name ?? '');
    if (!$customerUser && session()->has('guest_customer')) {
        $autofillName = '';
    }
    // Kunci nama hanya jika benar-benar login sebagai customer (bukan guest)
    $lockName = filled($autofillName) && $customerUser;
@endphp

<div id="checkoutModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true"
    aria-labelledby="checkoutTitle" aria-hidden="true">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- Sheet wrapper -->
    <div class="absolute inset-x-0 bottom-0 sm:inset-0 sm:flex sm:items-center sm:justify-center">
        <div id="checkoutSheet"
            class="bg-white rounded-t-2xl sm:rounded-2xl shadow-xl w-full sm:max-w-lg
                translate-y-full sm:translate-y-0 transition-transform duration-300
                flex flex-col max-h-[85vh] overflow-hidden">
            <!-- Header (tetap) -->
            <div class="p-4 border-b flex items-center justify-between">
                <h3 id="checkoutTitle" class="text-lg font-semibold">
                    {{ __('messages.customer.menu.order_confirmation') }}</h3>
                <button id="checkoutCloseBtn" type="button" class="p-2 rounded-md hover:bg-gray-100"
                    aria-label="Tutup">
                    ✕
                </button>
            </div>

            <!-- Scroll container: LIST PRODUK + TOTAL + FORM (semua ikut scroll) -->
            <div id="checkoutScroll" class="flex-1 overflow-y-auto overscroll-contain"
                style="-webkit-overflow-scrolling: touch;">
                <!-- List produk (diisi via JS) -->
                <div id="checkoutBody" class="p-4 space-y-3">
                    <!-- rows injected here via JS -->
                </div>

                <!-- Total & Form (ikut scroll) -->
                <div class="p-4 border-t space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total</span>
                        <span id="checkoutGrandTotal" class="text-xl font-extrabold">Rp 0</span>
                    </div>

                    <!-- Nama Pemesan -->
                    <div>
                        <label for="orderName"
                            class="block text-sm font-medium mb-1">{{ __('messages.customer.menu.nama_pemesan') }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.1a7.5 7.5 0 0115 0V21H4.5v-.9z" />
                                </svg>
                            </span>
                            <input id="orderName" name="customer_name" type="text" inputmode="text"
                                placeholder="{{ __('messages.customer.menu.customer_name_example') }}"
                                value="{{ old('customer_name', $autofillName ?? '') }}"
                                data-default-name="{{ $autofillName ?? '' }}" autocomplete="name"
                                autocapitalize="words" maxlength="60"
                                @if (!empty($lockName)) readonly aria-readonly="true" @endif
                                class="w-full rounded-lg border border-gray-300 pl-10 pr-10 py-2
                       placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-choco/40 focus:border-choco"
                                required />
                            @empty($lockName)
                                <button type="button" id="clearOrderName"
                                    class="absolute inset-y-0 right-2 px-2 text-gray-400 hover:text-gray-600"
                                    aria-label="Hapus nama">✕</button>
                            @endempty
                        </div>
                        @if (!empty($lockName))
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('messages.customer.menu.name_select_from_account') }}</p>
                        @else
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('messages.customer.menu.isi_nama_agar_pesanan_mudah_diambil') }}</p>
                        @endif
                    </div>

                    <!-- Metode Pembayaran -->
                    <div>
                        <label for="paymentMethod"
                            class="block text-sm font-medium mb-1">{{ __('messages.customer.menu.metode_pembayaran') }}</label>
                        <select id="paymentMethod"
                            class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-choco/40"
                            required>
                            <option value="" selected disabled>{{ __('messages.customer.menu.pilih_metode') }}
                            </option>
                            @if ($partner->is_cashier_active == 1)
                                <option value="CASH">{{ __('messages.customer.menu.pay_at_cashier') }}</option>
                            @endif
                            @if ($partner->is_qr_active === 1)
                                <option value="QRIS">QRIS</option>
                            @endif

                        </select>
                    </div>
                </div>
            </div>

            <!-- Footer (tetap) : hanya tombol aksi -->
            <div class="p-4 border-t bg-white">
                <div class="flex gap-2">
                    <button id="checkoutCancelBtn" type="button"
                        class="flex-1 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">
                        {{ __('messages.customer.menu.cancel') }}
                    </button>
                    <button id="checkoutPayBtn" type="button"
                        class="flex-1 py-2 rounded-lg bg-choco text-white font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __('messages.customer.menu.payment') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
