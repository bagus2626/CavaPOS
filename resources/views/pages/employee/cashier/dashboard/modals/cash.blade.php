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
