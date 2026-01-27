{{-- =========================== MODALS (dipakai file ini) =========================== --}}

<!-- Floating Cart Bar -->
<div id="floatingCartBar"
    class="fixed bottom-0 left-0 right-0 z-50 px-4 md:px-10 pb-4 pointer-events-none hidden flex justify-center"
    style="padding-bottom: calc(env(safe-area-inset-bottom) + 1rem);">

    <!-- The Floating Cart Component -->
    <div
        class="pointer-events-auto w-full max-w-[960px] bg-white dark:bg-surface-dark rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-200 dark:border-white/10 flex items-center justify-between p-3 md:p-4 gap-4 animate-in slide-in-from-bottom-5 fade-in duration-300">

        <!-- Left Side: Clear & Info -->
        <div class="flex items-center gap-3 md:gap-4 flex-1 min-w-0">
            <!-- Clear Cart Button -->
            <button id="floatingCartClear"
                class="group flex items-center justify-center size-10 md:size-11 rounded-xl bg-gray-100 text-[#ae1504] hover:bg-gray-200 transition-all border border-transparent hover:border-[#ae1504]/20 shrink-0"
                aria-label="Cart">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 md:w-5 md:h-5">
                    <circle cx="9" cy="21" r="1" />
                    <circle cx="20" cy="21" r="1" />
                    <path d="M1 2h3l3.6 12.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6L23 6H6" />
                </svg>
            </button>

            <!-- Cart Summary Info -->
            <div class="flex flex-col min-w-0">
                <div class="flex items-baseline gap-1.5">
                    <span id="floatingCartCount"
                        class="inline-flex items-center justify-center bg-red-50 dark:bg-red-900/20 text-[#ae1504] text-xs md:text-sm font-bold px-2 py-0.5 rounded-xl whitespace-nowrap">
                        0 Items
                    </span>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-gray-600 text-xs md:text-sm font-medium">Total:</span>
                    <span id="floatingCartTotal"
                        class="text-gray-900 text-base md:text-lg font-bold tracking-tight truncate">
                        Rp 0
                    </span>
                </div>
            </div>
        </div>

        <!-- Right Side: Checkout Action -->
        <button id="floatingCartPay"
            class="flex items-center gap-2 md:gap-3 bg-[#ae1504] hover:bg-[#8a1103] active:bg-[#7a0e02] text-white px-6 md:px-8 py-3 rounded-full transition-all shrink-0 group disabled:opacity-50 disabled:cursor-not-allowed">
            <span class="font-bold text-sm md:text-base whitespace-nowrap">
                Checkout
            </span>
            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4 md:w-5 md:h-5 group-hover:translate-x-1 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </button>
    </div>
</div>

<!-- Cart Manager Modal -->
<div id="cartManagerModal" class="fixed inset-0 z-50 hidden">
    <!-- backdrop -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>

    <!-- sheet -->
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2
                bg-white rounded-2xl shadow-2xl
                w-[95%] sm:max-w-[600px] max-h-[90vh] overflow-hidden
                transform scale-95 opacity-0
                transition-all duration-300 flex flex-col"
        id="cartManagerSheet">

        <!-- Header -->
        <div class="shrink-0 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-white z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 sm:gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="h-6 w-6 sm:h-7 sm:w-7 text-[#ae1504]">
                        <circle cx="9" cy="21" r="1" />
                        <circle cx="20" cy="21" r="1" />
                        <path d="M1 2h3l3.6 12.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6L23 6H6" />
                    </svg>
                    <h3 class="text-gray-900 text-lg sm:text-xl font-bold leading-tight">
                        Keranjang
                    </h3>
                </div>
                <button id="closeCartManager"
                    class="group flex h-9 w-9 sm:h-10 sm:w-10 cursor-pointer items-center justify-center rounded-full 
                           bg-gray-100 hover:bg-gray-200 transition-colors text-gray-700"
                    aria-label="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Scrollable Body -->
        <div id="cartManagerBody"
            class="flex-1 overflow-y-auto overscroll-contain scrollbar-thin scrollbar-thumb-gray-200 p-4 space-y-3 bg-white">
            <!-- Baris item cart dirender via JS -->
            <!-- Empty state (akan di-replace via JS) -->
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <svg class="w-20 h-20 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="text-gray-500 text-lg font-medium">
                    Keranjang masih kosong
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="shrink-0 bg-white border-t border-gray-200 p-3 sm:p-4 z-10 space-y-2.5 sm:space-y-3">
            <!-- Summary -->
            <div class="flex justify-between items-center">
                <span class="text-gray-600 text-sm">Subtotal</span>
                <span id="cartManagerTotal" class="text-gray-900 font-bold text-lg sm:text-xl">Rp 0</span>
            </div>

            <!-- Action Button -->
            <button id="cartManagerDone"
                class="w-full bg-[#ae1504] hover:bg-[#8a1103] text-white font-bold text-sm sm:text-base py-3 sm:py-3.5 rounded-full 
                transition-all transform active:scale-[0.98]">
                Selesai
            </button>
        </div>
    </div>
</div>

{{-- Modal Parent Options --}}
<div id="parentOptionsModal"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 sm:p-6 transition-all duration-300">
    <div id="modalSheet"
        class="bg-white dark:bg-surface-dark w-full max-w-[640px] max-h-[90vh] rounded-xl shadow-2xl
              transform translate-y-full transition-transform duration-300
              overflow-hidden flex flex-col">

        {{-- Sticky Header --}}
        <div class="flex-none p-5 sm:p-6 border-b border-gray-200 bg-white dark:bg-surface-dark z-10">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1" id="modalHeader">
                    {{-- Diisi via JS: gambar + nama + deskripsi --}}
                </div>
                <button id="closeModalBtn"
                    class="group flex h-9 w-9 sm:h-10 sm:w-10 cursor-pointer items-center justify-center rounded-full 
                           bg-gray-100 hover:bg-gray-200 transition-colors text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Scrollable Options Area --}}
        <div id="modalScrollArea" class="flex-1 overflow-y-auto bg-gray-50 dark:bg-background-dark custom-scrollbar"
            style="-webkit-overflow-scrolling: touch;">

            <div id="modalContent" class="flex flex-col">
                {{-- Parent options diisi via JS --}}
            </div>

            {{-- Section: Special Instructions (akan di-append via JS jika perlu) --}}
        </div>

        {{-- Sticky Footer --}}
        <div class="flex-none p-5 sm:p-6 border-t border-gray-200 bg-white dark:bg-surface-dark">
            <div class="flex flex-col sm:flex-row items-center gap-4">
                {{-- Quantity Stepper --}}
                <div
                    class="flex items-center justify-between w-full sm:w-auto rounded-full border border-gray-300 bg-white dark:bg-surface-dark p-1 transition-colors">
                    <button id="modalQtyMinus"
                        class="flex h-10 w-10 items-center justify-center rounded-full text-gray-500 hover:bg-gray-200 hover:text-[#ae1504] transition-colors disabled:opacity-50 dark:hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                    <span id="modalQtyValue" class="w-8 text-center text-lg font-bold text-gray-900">1</span>
                    <button id="modalQtyPlus"
                        class="flex h-10 w-10 items-center justify-center rounded-full text-gray-500 hover:bg-gray-200 hover:text-[#ae1504] transition-colors dark:hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>

                {{-- Add Button --}}
                <button id="saveModalBtn"
                    class="flex-1 w-full flex items-center justify-between rounded-full bg-[#ae1504] px-6 py-3.5 text-base font-bold text-white 
                    hover:bg-[#8a1103] hover:-translate-y-0.5 
                    transition-all active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0"
                    disabled>
                    <span>Simpan</span>
                    <span id="modalTotalPrice" class="modalTotalPrice">Rp 0</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Checkout Confirmation Modal --}}
<div id="checkoutModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" role="dialog" aria-modal="true"
    aria-labelledby="checkoutTitle" aria-hidden="true">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

    <!-- Sheet wrapper -->
    <div class="relative w-full max-w-2xl">
        <div id="checkoutSheet"
            class="bg-white rounded-xl shadow-2xl w-full
                scale-95 opacity-0 transition-all duration-300
                flex flex-col max-h-[90vh] overflow-hidden">
            
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 id="checkoutTitle" class="text-xl font-bold text-gray-900 tracking-tight">
                    Konfirmasi Pesanan
                </h3>
                <button id="checkoutCloseBtn" type="button" 
                    class="group flex h-9 w-9 sm:h-10 sm:w-10 cursor-pointer items-center justify-center rounded-full 
                           bg-gray-100 hover:bg-gray-200 transition-colors text-gray-700"
                    aria-label="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Scrollable Body -->
            <div id="checkoutScroll" class="flex-1 overflow-y-auto overscroll-contain scrollbar-thin scrollbar-thumb-gray-200"
                style="-webkit-overflow-scrolling: touch;">
                
                <!-- Items Section -->
                <div class="px-6 py-4 bg-white">
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-500 mb-2">
                        Items
                    </h4>
                    <div id="checkoutBody" class="flex flex-col gap-3">
                        <!-- rows injected here via JS -->
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="px-6 pb-6 bg-white">
                    <div class="bg-gray-50 rounded-lg p-5 mb-4">
                        <div class="flex justify-between pt-2 border-gray-200">
                            <p class="text-gray-900 text-lg font-bold">Total</p>
                            <p id="checkoutGrandTotal" class="text-[#ae1504] text-xl font-bold">Rp 0</p>
                        </div>
                    </div>

                    <!-- User Inputs Section -->
                    <div class="space-y-6">
                        <!-- Nama Pemesan -->
                        <div class="flex flex-col gap-2">
                            <label for="orderName" class="text-gray-900 text-sm font-semibold flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#ae1504]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.1a7.5 7.5 0 0115 0V21H4.5v-.9z"/>
                                </svg>
                                Nama Pemesan
                            </label>
                            <input id="orderName" name="customer_name" type="text" inputmode="text"
                                placeholder="Contoh: Budi Setiawan"
                                autocomplete="name"
                                autocapitalize="words" maxlength="60"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:border-[#ae1504] focus:ring-1 focus:ring-[#ae1504] outline-none transition-all"
                                required />
                            <p class="mt-1 text-xs text-gray-500">
                                Isi nama agar pesanan mudah dipanggil
                            </p>
                        </div>

                        <!-- Pilih Meja -->
                        <div class="flex flex-col gap-2">
                            <label for="orderTable" class="text-gray-900 text-sm font-semibold flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#ae1504]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                                </svg>
                                Pilih Meja
                            </label>
                            <select id="orderTable"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-[#ae1504] focus:ring-1 focus:ring-[#ae1504] outline-none transition-all"
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

                        <!-- Metode Pembayaran -->
                        <div class="flex flex-col gap-3">
                            <label class="text-gray-900 text-sm font-semibold flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#ae1504]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Metode Pembayaran
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Cash Option -->
                                <label class="relative flex items-center justify-between cursor-pointer rounded-lg border border-gray-200 bg-white p-4 transition-all hover:border-[#ae1504]/50 hover:bg-gray-50 has-[:checked]:border-[#ae1504] has-[:checked]:bg-[#ae1504]/5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-5 w-5 items-center justify-center rounded-full border border-gray-300 has-[:checked]:border-[#ae1504]">
                                            <div class="h-2.5 w-2.5 rounded-full bg-[#ae1504] opacity-0 transition-opacity peer-checked:opacity-100"></div>
                                        </div>
                                        <span class="text-gray-900 font-medium text-sm">Cash</span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <input class="hidden peer payment-method-radio" name="payment" type="radio" value="CASH"/>
                                </label>
                                
                                <!-- QRIS Option -->
                                <label class="relative flex items-center justify-between cursor-pointer rounded-lg border border-gray-200 bg-white p-4 transition-all hover:border-[#ae1504]/50 hover:bg-gray-50 has-[:checked]:border-[#ae1504] has-[:checked]:bg-[#ae1504]/5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-5 w-5 items-center justify-center rounded-full border border-gray-300 has-[:checked]:border-[#ae1504]">
                                            <div class="h-2.5 w-2.5 rounded-full bg-[#ae1504] opacity-0 transition-opacity peer-checked:opacity-100"></div>
                                        </div>
                                        <span class="text-gray-900 font-medium text-sm">QRIS</span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    <input class="hidden peer payment-method-radio" name="payment" type="radio" value="QRIS"/>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 p-6 bg-white">
                <div class="flex gap-4">
                    <button id="checkoutCancelBtn" type="button"
                        class="flex-1 px-6 py-3.5 rounded-full border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-200 focus:outline-none">
                        Batal
                    </button>
                    <button id="checkoutPayBtn" type="button"
                        class="flex-[2] px-6 py-3.5 rounded-full bg-[#ae1504] text-white font-bold hover:bg-[#8a1003] transition-colors focus:ring-4 focus:ring-[#ae1504]/30 focus:outline-none flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span>Pembayaran</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom scrollbar styling untuk modal */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #e5e7eb;
        border-radius: 20px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #d1d5db;
    }

    /* Modal animation - Scale transition */
    #parentOptionsModal.show #modalSheet {
        transform: translateY(0);
    }

    /* Smooth transition untuk modal sheet */
    #modalSheet {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom radio and checkbox styling - konsisten dengan customer */
    :root {
        --radio-dot-svg: url('data:image/svg+xml,%3csvg viewBox=%270 0 16 16%27 fill=%27rgb(174,21,4)%27 xmlns=%27http://www.w3.org/2000/svg%27%3e%3ccircle cx=%278%27 cy=%278%27 r=%273%27/%3e%3c/svg%3e');
        --checkbox-tick-svg: url('data:image/svg+xml,%3csvg viewBox=%270 0 16 16%27 fill=%27rgb(255,255,255)%27 xmlns=%27http://www.w3.org/2000/svg%27%3e%3cpath d=%27M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z%27/%3e%3c/svg%3e');
    }

    /* Radio button dengan has-[:checked] selector */
    label:has(input[type="radio"]:checked) {
        border-color: #ae1504 !important;
        background-color: rgba(174, 21, 4, 0.05) !important;
    }

    label:has(input[type="radio"]:checked) .peer-checked\:opacity-100 {
        opacity: 1 !important;
    }

    label:has(input[type="radio"]:checked) .has-\[\:checked\]\:border-choco {
        border-color: #ae1504 !important;
    }

    /* Transition smooth untuk radio/checkbox dot */
    label .h-2\.5.w-2\.5 {
        transition: opacity 0.2s ease-in-out;
    }

    /* Checkbox checked state */
    label:has(input[type="checkbox"]:checked) {
        border-color: #ae1504 !important;
        background-color: rgba(174, 21, 4, 0.05) !important;
    }

    /* Hover effects */
    label:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Disabled state */
    label:has(input:disabled) {
        opacity: 0.6;
        cursor: not-allowed;
    }

    label:has(input:disabled):hover {
        transform: none;
        box-shadow: none;
    }

    /* Animasi slide-in untuk floating cart */
    @keyframes slide-in-from-bottom {
        from {
            transform: translateY(100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes fade-in {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .animate-in {
        animation-fill-mode: both;
    }

    .slide-in-from-bottom-5 {
        animation: slide-in-from-bottom 0.3s ease-out;
    }

    .fade-in {
        animation: fade-in 0.3s ease-out;
    }

    /* Smooth transition saat muncul */
    #floatingCartBar:not(.hidden) {
        animation: slide-in-from-bottom 0.3s ease-out, fade-in 0.3s ease-out;
    }

    /* Hover effect untuk clear button */
    #floatingCartClear:hover svg {
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: rotate(0deg);
        }

        25% {
            transform: rotate(-5deg);
        }

        75% {
            transform: rotate(5deg);
        }
    }

    /* Kompensasi scrollbar untuk desktop */
    @media (min-width: 768px) {
        body.modal-open #floatingCartBar {
            right: var(--scrollbar-width, 0px);
        }
    }

    /* Scrollbar styling untuk cart manager */
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }

    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }

    .scrollbar-thumb-gray-200::-webkit-scrollbar-thumb {
        background-color: #e5e7eb;
        border-radius: 20px;
    }

    .scrollbar-thumb-gray-200::-webkit-scrollbar-thumb:hover {
        background-color: #d1d5db;
    }

    /* Animation for modal - Scale Only */
    #cartManagerModal.show #cartManagerSheet {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    /* Smooth transitions */
    #cartManagerSheet {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Modal animation - Scale */
    #checkoutModal #checkoutSheet {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #checkoutModal #checkoutSheet.scale-100 {
        transform: scale(1);
        opacity: 1;
    }

    #checkoutModal #checkoutSheet.scale-95 {
        transform: scale(0.95);
        opacity: 0;
    }

    /* Custom scrollbar */
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }

    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }

    .scrollbar-thumb-gray-200::-webkit-scrollbar-thumb {
        background-color: #e5e7eb;
        border-radius: 20px;
    }

    /* Radio button dengan has-[:checked] selector */
    label:has(input[type="radio"]:checked) {
        border-color: #ae1504 !important;
        background-color: rgba(174, 21, 4, 0.05) !important;
    }

    label:has(input[type="radio"]:checked) .peer-checked\:opacity-100 {
        opacity: 1 !important;
    }

    label:has(input[type="radio"]:checked) .has-\[\:checked\]\:border-\[\#ae1504\] {
        border-color: #ae1504 !important;
    }

    /* Transition smooth untuk radio dot */
    label .h-2\.5.w-2\.5 {
        transition: opacity 0.2s ease-in-out;
    }

    /* Hover effects untuk label payment */
    label:has(input[type="radio"]):hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Fix: Hilangkan efek hover yang tidak diinginkan pada input */
    #orderName:hover,
    #orderTable:hover {
        outline: none !important;
        border-color: #d1d5db !important; /* border-gray-300 */
        background-color: white !important;
    }
    
    /* Pastikan tidak ada perubahan warna saat hover */
    #orderName,
    #orderTable {
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    /* Khusus untuk text label (bukan label payment option) */
    #checkoutModal label.text-gray-900.text-sm.font-semibold {
        border: none !important;
        background: transparent !important;
        padding: 0 !important;
        cursor: default !important;
        user-select: none !important;
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        pointer-events: none !important;
    }

    #checkoutModal label.text-gray-900.text-sm.font-semibold:hover {
        border: none !important;
        background: transparent !important;
        transform: none !important;
        box-shadow: none !important;
        background-color: transparent !important;
    }

    /* Allow pointer events pada icon dan teks agar tidak menghalangi */
    #checkoutModal label.text-gray-900.text-sm.font-semibold * {
        pointer-events: none !important;
    }

    /* Payment method labels tetap bisa hover */
    #checkoutModal label.relative.flex.items-center:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>
