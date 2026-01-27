<div class="lg:col-span-1 h-full flex flex-col bg-white rounded-xl border border-gray-200 shadow-card overflow-hidden">
    <!-- HEADER - Sticky -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between sticky top-0 z-10">
        <h2 class="text-lg font-bold text-gray-900">Pembayaran Cash</h2>
        <span
            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
            {{ $items->count() }} order
        </span>
    </div>


    <!-- SCROLL AREA -->
    <div class="overflow-y-auto flex-1">
        @if ($items->isEmpty())
            <div class="flex flex-col items-center justify-center text-center h-full py-12 px-4">
                <div class="size-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500">Tidak ada order cash yang menunggu pembayaran.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Order ID
                            </th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Table
                            </th>
                            <th class="px-14 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider ">
                                Customer Info</th>
                            <th
                                class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                Time</th>
                            <th
                                class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                Total Amount</th>
                            <th
                                class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                Status</th>
                            <th
                                class="pr-16 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="order-list">
                        @foreach ($items as $i)
                            <tr class="group hover:bg-blue-50/30 transition-colors duration-150"
                                id="order-item-{{ $i->id }}">
                                {{-- ORDER ID --}}
                                <td class="px-4 py-4 align-middle">
                                    <span
                                        class="inline-flex items-center text-sm font-medium text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">
                                        {{ $i->booking_order_code }}
                                    </span>
                                </td>


                                {{-- TABLE --}}
                                <td class="px-4 py-4 align-middle">
                                    <span
                                        class="inline-flex items-center justify-center size-8 rounded-full text-sm font-bold bg-white text-gray-700 border border-gray-200 shadow-sm">
                                        {{ $i->table?->table_no ?? '-' }}
                                    </span>
                                </td>


                                {{-- CUSTOMER INFO --}}
                                <td class="px-4 py-4 align-middle">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="size-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($i->customer_name, 0, 2)) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-semibold text-gray-900">{{ $i->customer_name }}</span>
                                        </div>
                                    </div>
                                </td>


                                {{-- TIME --}}
                                <td class="px-4 py-4 align-middle text-center">
                                    <span class="text-sm text-gray-600 font-medium tabular-nums">
                                        {{ $i->created_at?->format('H:i') }}
                                    </span>
                                </td>


                                {{-- TOTAL AMOUNT --}}
                                <td class="px-4 py-4 align-middle text-center">
                                    <span class="text-sm font-bold text-gray-900 tabular-nums">
                                        Rp {{ number_format($i->total_order_value, 0, ',', '.') }}
                                    </span>
                                </td>


                                {{-- STATUS --}}
                                <td class="px-4 py-4 align-middle text-center">
                                    @if ($i->payment_method === 'QRIS' && $i->order_status === 'EXPIRED')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="size-1.5 rounded-full bg-rose-500"></span>
                                            Unpaid
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="size-1.5 rounded-full bg-amber-500"></span>
                                            Pending
                                        </span>
                                    @endif
                                </td>


                                {{-- ACTIONS --}}
                                <td class="px-4 py-4 align-middle">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Detail Button --}}
                                        <a href="{{ route('employee.cashier.order-detail', $i->id) }}" data-detail-btn
                                            data-order-id="{{ $i->id }}"
                                            class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                            title="View Details">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>


                                       


                                        {{-- Delete Form --}}
                                        <form action="{{ route('employee.cashier.order.soft-delete', $i->id) }}"
                                            method="POST" class="inline-block"
                                            onsubmit="return handleSoftDeleteSubmit(event, '{{ $i->booking_order_code }}')">
                                            @csrf
                                            @method('DELETE')


                                            <button type="submit" title="Hapus order"
                                                class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M8.5 3a1.5 1.5 0 011.5-1.5h0a1.5 1.5 0 011.5 1.5H15a.75.75 0 010 1.5h-.278l-.69 9.042A2.25 2.25 0 0111.79 16.75H8.21a2.25 2.25 0 01-2.242-2.208L5.278 4.5H5a.75.75 0 010-1.5h3.5z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                        {{-- Payment Button --}}
                                        <button type="button" data-cash-btn data-order-id="{{ $i->id }}"
                                            data-order-name="{{ $i->customer_name }}"
                                            data-order-code="{{ $i->booking_order_code }}"
                                            data-order-total="{{ $i->total_order_value }}"
                                            data-cash-get-url="{{ route('employee.cashier.order-detail', $i->id) }}"
                                            data-cash-url="{{ route('employee.cashier.cash-payment', '__ID__') }}"
                                            class="px-4 py-1.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors shadow-sm">
                                            Process
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>


@include('pages.employee.cashier.dashboard.modals.cash')
@include('pages.employee.cashier.dashboard.modals.detail')


<script>
    function handleSoftDeleteSubmit(event, orderCode) {
        event.preventDefault();


        const form = event.target;


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


        return false;
    }
</script>