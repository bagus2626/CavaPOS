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
                    Tidak ada order cash yang menunggu pembayaran.
                </div>
            @else
                <ul class="space-y-3">
                    @foreach ($items as $i)
                        <li class="rounded-xl border border-choco/10 p-3 hover:bg-soft-choco/5" id="order-item-{{ $i->id }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $i->booking_order_code }} &middot; Meja {{ $i->table->table_no }}
                                        @if ($i->payment_method === 'QRIS' && $i->order_status === 'UNPAID')
                                            <span class="inline-block px-2 py-0.5 text-xs font-medium text-white bg-red-500 rounded-md">
                                                Expired/Gagal QRIS
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Rp {{ number_format($i->total_order_value, 0, ',', '.') }}
                                        &middot; {{ $i->created_at?->format('H:i') }}
                                        . <span class="font-bold">{{ $i->customer_name }}</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('employee.cashier.order-detail', $i->id) }}"
                                    data-detail-btn
                                    data-order-id="{{ $i->id }}"
                                    class="text-sm px-3 py-1.5 rounded-lg border border-choco/20 text-choco hover:bg-soft-choco/10 focus:ring-2 focus:ring-soft-choco/30">
                                        Detail
                                    </a>

                                    <button type="button"
                                            class="text-sm px-3 py-1.5 rounded-lg bg-soft-choco text-white hover:bg-soft-choco/90 focus:ring-2 focus:ring-soft-choco/40"
                                            data-cash-btn
                                            data-order-id="{{ $i->id }}"
                                            data-order-name ="{{ $i->customer_name }}"
                                            data-order-code="{{ $i->booking_order_code }}"
                                            data-order-total="{{ $i->total_order_value }}"
                                            data-cash-get-url="{{ route('employee.cashier.order-detail', $i->id) }}"
                                            data-cash-url="{{ route('employee.cashier.cash-payment', '__ID__') }}">
                                        Pembayaran
                                    </button>

                                    {{-- Tombol Soft Delete (ikon bak sampah) --}}
                                    <form action="{{ route('employee.cashier.order.soft-delete', $i->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="handleSoftDeleteClick(this)"
                                                class="inline-flex p-2 mt-4 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 focus:ring-2 focus:ring-red-200 self-center"
                                                title="Hapus order"
                                                data-order-code="{{ $i->booking_order_code }}">
                                            {{-- Ikon bak sampah (SVG) --}}
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M8.5 3a1.5 1.5 0 011.5-1.5h0a1.5 1.5 0 011.5 1.5H15a.75.75 0 010 1.5h-.278l-.69 9.042A2.25 2.25 0 0111.79 16.75H8.21a2.25 2.25 0 01-2.242-2.208L5.278 4.5H5a.75.75 0 010-1.5h3.5zm-1.72 3.53a.75.75 0 011.06 0L9 7.69l1.16-1.16a.75.75 0 111.06 1.06L10.06 8.75l1.16 1.16a.75.75 0 11-1.06 1.06L9 9.81l-1.16 1.16a.75.75 0 11-1.06-1.06l1.16-1.16-1.16-1.16a.75.75 0 010-1.06z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
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

<script src="{{ asset('js/employee/cashier/dashboard/detail.js') }}"></script>
<script>
    function handleSoftDeleteClick(button) {
        console.log('tsettt');
    const form = button.closest('form');
    if (!form) return;

    const orderCode = button.getAttribute('data-order-code') || 'order ini';

    // Kalau SweetAlert2 ada, pakai Swal
    if (window.Swal) {
        Swal.fire({
            title: 'Hapus ' + orderCode + '?',
            text: 'Order akan dihapus dari daftar pembayaran.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else {
        // Fallback: confirm biasa
        if (confirm('Yakin ingin menghapus ' + orderCode + '?')) {
            form.submit();
        }
    }
}

</script>