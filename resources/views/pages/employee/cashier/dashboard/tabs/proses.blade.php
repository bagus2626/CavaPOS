<div class="lg:col-span-1 h-full">
    <div class="rounded-2xl border border-choco/10 bg-white shadow-sm h-full flex flex-col">
        <!-- HEADER jadi sticky -->
        <div class="px-4 py-3 border-b border-choco/10 flex items-center justify-between sticky top-0 bg-white z-10">
            <h2 class="font-semibold text-choco">Pembayaran Cash</h2>
            <span class="text-xs text-gray-500">{{ $items->count() }} order</span>
        </div>

        <!-- SCROLL AREA -->
        <div class="p-4 overflow-y-auto max-h-[70vh]">
            @if ($items->isEmpty())
                <div class="text-center text-sm text-gray-500 h-full">
                    Tidak ada order yang diproses.
                </div>
            @else
                <ul class="space-y-3">
                    @foreach ($items as $i)
                        <li class="rounded-xl border border-choco/10 p-3 hover:bg-soft-choco/5"
                            id="order-item-{{ $i->id }}">

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                {{-- 🔹 INFO ORDER --}}
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900 leading-tight">
                                        {{ $i->booking_order_code }}
                                        &middot; Meja {{ $i->table->table_no ?? '-' }}
                                        &middot; {{ $i->payment_method }}

                                        @if ($i->order_status === 'PROCESSED')
                                            <span class="ml-1 text-blue-500">{{ $i->order_status }}</span>
                                        @elseif ($i->order_status === 'PAID')
                                            <span class="ml-1 text-choco">{{ $i->order_status }}</span>
                                        @else
                                            <span class="ml-1">{{ $i->order_status }}</span>
                                        @endif
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Rp {{ number_format($i->total_order_value, 0, ',', '.') }}
                                        &middot; {{ $i->created_at?->format('H:i') }}
                                        · <span class="font-bold">{{ $i->customer_name }}</span>
                                    </p>
                                </div>

                                {{-- 🔹 ACTION BUTTONS --}}
                                <div
                                    class="flex items-center gap-2
                                        w-full sm:w-auto
                                        justify-between sm:justify-end">

                                    {{-- Detail --}}
                                    <a href="{{ route('employee.cashier.order-detail', $i->id) }}"
                                    data-detail-btn
                                    data-order-id="{{ $i->id }}"
                                    class="h-9 flex-1 sm:flex-none flex items-center justify-center
                                            text-sm px-3 rounded-lg
                                            border border-choco/20 text-choco
                                            hover:bg-soft-choco/10
                                            focus:ring-2 focus:ring-soft-choco/30">
                                        Detail
                                    </a>

                                    {{-- Struk --}}
                                    <button type="button"
                                        data-print-receipt-process
                                        data-order-id="{{ $i->id }}"
                                        class="h-9 flex-1 sm:flex-none flex items-center justify-center
                                            text-sm px-3 rounded-lg
                                            border border-choco/20 text-choco
                                            hover:bg-soft-choco/20
                                            focus:ring-2 focus:ring-choco/30">
                                        Struk
                                    </button>

                                    {{-- Status-dependent button --}}
                                    @if ($i->order_status === 'PAID')
                                        <button type="button"
                                            data-turn-to-process-btn
                                            data-order-id="{{ $i->id }}"
                                            data-order-name="{{ $i->customer_name }}"
                                            data-order-code="{{ $i->booking_order_code }}"
                                            data-order-total="{{ $i->total_order_value }}"
                                            data-order-table="{{ $i->table->table_no ?? '-' }}"
                                            data-order-url="{{ route('employee.cashier.process-order', '__ID__') }}"
                                            class="h-9 flex-1 sm:flex-none flex items-center justify-center
                                                text-sm px-3 rounded-lg
                                                bg-blue-500 text-white
                                                hover:bg-blue-500/90
                                                focus:ring-2 focus:ring-blue-400/40">
                                            Proses
                                        </button>
                                    @elseif ($i->order_status === 'PROCESSED')
                                        <button type="button"
                                            data-turn-to-paid-btn
                                            data-order-id="{{ $i->id }}"
                                            data-order-name="{{ $i->customer_name }}"
                                            data-order-code="{{ $i->booking_order_code }}"
                                            data-order-total="{{ $i->total_order_value }}"
                                            data-order-table="{{ $i->table->table_no ?? '-' }}"
                                            data-order-url="{{ route('employee.cashier.cancel-process-order', '__ID__') }}"
                                            class="h-9 flex-1 sm:flex-none flex items-center justify-center
                                                text-sm px-3 rounded-lg
                                                bg-red-500 text-white
                                                hover:bg-red-500/90
                                                focus:ring-2 focus:ring-red-400/40">
                                            Batal Proses
                                        </button>
                                        <button type="button"
                                            data-process-btn
                                            data-order-id="{{ $i->id }}"
                                            data-order-name="{{ $i->customer_name }}"
                                            data-order-code="{{ $i->booking_order_code }}"
                                            data-order-total="{{ $i->total_order_value }}"
                                            data-order-table="{{ $i->table->table_no ?? '-' }}"
                                            data-order-get-url="{{ route('employee.cashier.order-detail', $i->id) }}"
                                            data-order-url="{{ route('employee.cashier.finish-order', '__ID__') }}"
                                            class="h-9 flex-1 sm:flex-none flex items-center justify-center
                                                text-sm px-3 rounded-lg
                                                bg-green-500 text-white
                                                hover:bg-green-400/90
                                                focus:ring-2 focus:ring-green-400/40">
                                            Selesaikan
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>


@include('pages.employee.cashier.dashboard.modals.cash')
@include('pages.employee.cashier.dashboard.modals.detail')
@include('pages.employee.cashier.dashboard.modals.served')

<script src="{{ asset('js/employee/cashier/dashboard/detail.js') }}"></script>
<script>
(function () {
  // Delegasi klik untuk semua tombol yang punya data-print-receipt-process
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-print-receipt-process]');
    if (!btn) return; // klik bukan pada tombol struk
    const id = btn.dataset.orderId;
    if (!id) {
      console.warn('Order ID kosong, tidak bisa cetak nota');
      return;
    }

    // Bangun URL ke route cetak
    const url = `/employee/cashier/print-receipt/${encodeURIComponent(id)}`;

    // Buka di tab baru
    window.open(url, '_blank', 'noopener,noreferrer');
  });
})();
</script>
