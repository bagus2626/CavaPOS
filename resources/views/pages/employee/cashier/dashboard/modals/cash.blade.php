{{-- MODAL: Proses Pembayaran Cash --}}
<div id="cashModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative max-w-md mx-auto mt-24 bg-white rounded-2xl shadow-xl border border-choco/10 overflow-hidden">
        
        {{-- Header --}}
        <div class="px-5 py-4 border-b border-choco/10 flex items-center justify-between bg-soft-choco/10">
            <h3 class="font-semibold text-choco text-lg">💵 Proses Pembayaran Cash</h3>
            <button type="button" class="p-2 rounded-lg hover:bg-soft-choco/20" data-cash-close>&times;</button>
        </div>
        <div class="max-h-[80vh] overflow-y-auto">
            {{-- Form --}}
            <form id="cashForm" method="POST" action="">
                @csrf
                <div class="px-5 py-6 space-y-5">
                    <input type="hidden" name="order_id" id="cashOrderId">

                    {{-- Informasi Order --}}
                    <div class="bg-soft-choco/5 rounded-xl p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Kode Order</span>
                            <span id="cashOrderCode" class="font-semibold text-gray-800">—</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Nama Order</span>
                            <span id="cashOrderName" class="font-medium text-gray-800">—</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200 pt-2">
                            <span class="text-xs text-gray-500">Total Tagihan</span>
                            <span id="cashOrderTotal" class="font-bold text-choco text-lg">Rp 0</span>
                        </div>
                        <input type="hidden" id="cashOrderTotalRaw" value="0">
                    </div>

                    <div id="detailItemCash" class="text-sm text-gray-700">
                        <!-- Isi order akan dimuat lewat JS -->
                    </div>

                    <div id="paymentInfoBox" class="hidden bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs text-amber-700 uppercase tracking-wide">Pembayaran Manual Terdeteksi</p>
                                <p id="payInfoType" class="text-sm font-semibold text-amber-900">—</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-white text-[11px] font-semibold border border-amber-300 text-amber-800">
                                    PAYMENT REQUEST
                                </span>

                                <div class="mt-2 text-sm text-gray-800 space-y-1">
                                    <div class="flex justify-between gap-3">
                                    <span class="text-gray-500">Provider</span>
                                    <span id="payInfoProvider" class="font-medium text-right">—</span>
                                    </div>
                                    <div class="flex justify-between gap-3">
                                    <span class="text-gray-500">Nama Akun</span>
                                    <span id="payInfoAccName" class="font-medium text-right">—</span>
                                    </div>

                                    <div id="payInfoAccNoWrap" class="flex justify-between gap-3">
                                    <span class="text-gray-500">No Akun</span>
                                    <div class="flex items-center gap-2">
                                        <span id="payInfoAccNo" class="font-mono font-semibold">—</span>
                                    </div>
                                    </div>

                                    <div id="payInfoProofWrap" class="pt-3 border-t border-amber-200">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-xs text-gray-500">Bukti bayar</p>

                                            <a id="payInfoProofLink" href="#" target="_blank"
                                            class="px-3 py-1.5 text-xs rounded-lg border bg-white hover:bg-gray-50 whitespace-nowrap">
                                            Lihat Bukti
                                            </a>
                                        </div>

                                        <!-- Preview besar (mirip QRIS customer) -->
                                        <div id="payInfoProofPreview" class="mt-3 hidden">
                                            <div class="w-full max-w-[420px] mx-auto">
                                            <div class="bg-white rounded-2xl border-2 border-amber-200 shadow-sm p-3 md:p-4">
                                                <img
                                                id="payInfoProofImg"
                                                src=""
                                                alt="Bukti bayar"
                                                class="w-full h-auto object-contain mx-auto rounded-xl"
                                                loading="lazy"
                                                >
                                            </div>
                                            <p class="mt-2 text-[11px] text-gray-500 text-center">
                                                Klik “Lihat Bukti” untuk membuka ukuran penuh.
                                            </p>
                                            </div>
                                        </div>

                                        <!-- Kalau bukti PDF -->
                                        <p id="payInfoProofPdfHint" class="mt-2 text-[11px] text-gray-500 hidden">
                                            Bukti berbentuk PDF. Klik “Lihat Bukti” untuk membuka.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Input Uang Diterima --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Uang Diterima</label>
                        <input type="number" name="paid_amount" id="paidAmount"
                            class="w-full rounded-xl border-gray-300 focus:border-choco focus:ring-choco/40"
                            min="0" step="100" placeholder="cth: 100000">
                        <p id="paidHint" class="mt-1 text-xs text-gray-500">Masukkan nominal uang dari pelanggan.</p>
                    </div>

                    {{-- Output Kembalian --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Kembalian</label>
                        <input type="text" id="changeDisplay"
                            class="w-full rounded-xl border-gray-200 bg-soft-choco/10 font-semibold text-choco"
                            value="Rp 0" readonly>
                        <input type="hidden" name="change_amount" id="changeAmount" value="0">
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label for="cashNote" class="block text-sm font-medium mb-1">Catatan</label>
                        <textarea name="note" id="cashNote" rows="2"
                                class="w-full rounded-xl border-gray-300 focus:border-choco focus:ring-choco/40"
                                placeholder="Contoh: pembayaran dengan uang pas, atau catatan lain..."></textarea>
                    </div>

                    {{-- Error --}}
                    <div id="cashError" class="hidden text-sm text-rose-600"></div>
                </div>

                {{-- Footer --}}
                <div class="px-5 pt-4 pb-28 border-t border-choco/10 flex items-center justify-end gap-2 bg-white">
                    <button type="button"
                            class="px-4 py-2 rounded-lg border border-choco/20 text-choco hover:bg-soft-choco/20 focus:ring-2 focus:ring-choco/30"
                            data-cash-close>Batal</button>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg bg-choco text-white hover:bg-choco/90 focus:ring-2 focus:ring-choco/40">
                        Konfirmasi & Cetak Struk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
