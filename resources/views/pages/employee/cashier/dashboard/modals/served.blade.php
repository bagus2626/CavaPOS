{{-- MODAL: Proses Pembayaran Cash --}}
<div id="servedModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative max-w-md mx-auto mt-24 bg-white rounded-2xl shadow-xl border border-choco/10">
        <div class="px-5 py-4 border-b border-choco/10 flex items-center justify-between">
            <h3 class="font-semibold text-choco">Penyelesaian Order</h3>
            <button type="button" class="p-2 rounded-lg hover:bg-soft-choco/10" data-served-close>&times;</button>
        </div>

        <div class="max-h-[80vh] overflow-y-auto">
            <form id="servedForm" method="POST" action="">
                @csrf
                <div class="px-5 py-5 space-y-4">
                    <input type="hidden" name="order_id" id="servedOrderId">

                    {{-- Informasi Order --}}
                    <div class="bg-soft-choco/5 rounded-xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Kode Order</span>
                            <span id="servedOrderCode" class="font-semibold text-choco text-sm">—</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Nama Order</span>
                            <span id="servedOrderName" class="font-medium text-gray-800 text-sm">—</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Meja</span>
                            <span id="servedOrderTable" class="font-medium text-gray-800 text-sm">—</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-dashed border-choco/20 pt-3">
                            <span class="text-xs text-gray-500">Total Tagihan</span>
                            <span id="servedOrderTotal" class="font-bold text-lg text-choco">Rp 0</span>
                        </div>
                        <input type="hidden" id="servedOrderTotalRaw" value="0">
                    </div>
                    <div id="detailItem" class="text-sm text-gray-700">
                        <!-- Isi order akan dimuat lewat JS -->
                    </div>

                    {{-- Input Catatan --}}
                    <div>
                        <label for="servedNote" class="block text-sm font-medium mb-1">Catatan</label>
                        <textarea name="note" id="servedNote" rows="2"
                                class="w-full rounded-xl border-gray-300 focus:border-soft-choco focus:ring-soft-choco/40 text-sm"
                                placeholder="Contoh: tomat habis, sudah konfirmasi ke customer..."></textarea>
                    </div>

                    <div id="servedError" class="hidden text-sm text-rose-600"></div>
                </div>

                <div class="px-5 py-4 border-t border-choco/10 flex items-center justify-end gap-2">
                    <button type="button"
                            class="px-4 py-2 rounded-lg border border-choco/20 text-choco hover:bg-soft-choco/10 focus:ring-2 focus:ring-soft-choco/30"
                            data-served-close>Batal</button>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg bg-choco text-white hover:bg-choco/90 focus:ring-2 focus:ring-soft-choco/40">
                        Konfirmasi & Selesaikan Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
