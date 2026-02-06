<div class="lg:col-span-1 h-full">
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm h-full flex flex-col overflow-hidden">
        <!-- HEADER - Sticky dengan shadow -->
        <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Pembayaran Cash</h2>
                    <span class="text-sm font-normal text-gray-500">({{ $items->count() }})</span>
                </div>
            </div>
        </div>


        <!-- TABLE AREA - dengan styling smooth -->
        <div class="flex-1 overflow-y-auto bg-gray-50">
            @if ($items->isEmpty())
                <div class="flex items-center justify-center h-64">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-sm text-gray-500">Tidak ada order yang diproses</p>
                    </div>
                </div>
            @else
                {{-- DESKTOP: TABLE --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order ID
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Table
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer Info
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Time
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Amount
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($items as $i)
                                <tr class="hover:bg-gray-50 transition-colors" id="order-row-{{ $i->id }}">
                                    <!-- Order ID -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $i->booking_order_code }}
                                        </div>
                                    </td>

                                    <!-- Table -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if ($i->order_status === 'PROCESSED')
                                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 font-semibold text-sm">
                                                    {{ $i->table->table_no ?? '-' }}
                                                </div>
                                            @else
                                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-orange-100 text-orange-600 font-semibold text-sm">
                                                    {{ $i->table->table_no ?? '-' }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Customer Info -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if ($i->order_status === 'PROCESSED')
                                                <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 font-semibold text-xs">
                                                    {{ strtoupper(substr($i->customer_name, 0, 2)) }}
                                                </div>
                                            @else
                                                <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-orange-100 text-orange-600 font-semibold text-xs">
                                                    {{ strtoupper(substr($i->customer_name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $i->customer_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Time -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $i->created_at?->format('H:i') }}
                                        </div>
                                    </td>

                                    <!-- Total Amount -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($i->total_order_value, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if ($i->order_status === 'PROCESSED')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-purple-600"></span>
                                                Sedang Diproses
                                            </span>
                                        @elseif ($i->order_status === 'PAID')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-orange-600"></span>
                                                Menunggu Diproses
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-gray-600"></span>
                                                {{ $i->order_status }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Detail -->
                                            <a href="{{ route('employee.cashier.order-detail', $i->id) }}"
                                            data-detail-btn
                                            data-order-id="{{ $i->id }}"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-300 text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all"
                                            title="View Details">
                                                <x-heroicon-o-eye class="w-5 h-5" />
                                            </a>

                                            <!-- Struk -->
                                            <button type="button"
                                                    data-print-receipt-process
                                                    data-order-id="{{ $i->id }}"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-300 text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all"
                                                    title="Print Receipt">
                                                <x-heroicon-o-printer class="w-5 h-5" />
                                            </button>

                                            @if ($i->order_status === 'PAID')
                                                <button type="button"
                                                        data-turn-to-process-btn
                                                        data-order-id="{{ $i->id }}"
                                                        data-order-name="{{ $i->customer_name }}"
                                                        data-order-code="{{ $i->booking_order_code }}"
                                                        data-order-total="{{ $i->total_order_value }}"
                                                        data-order-table="{{ $i->table->table_no ?? '-' }}"
                                                        data-order-url="{{ route('employee.cashier.process-order', '__ID__') }}"
                                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-orange-500 text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-400 transition-all shadow-sm">
                                                    <x-heroicon-o-clock class="w-4 h-4 mr-1.5" />
                                                    Mulai Proses
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
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-red-300 text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-200 transition-all"
                                                        title="Batalkan Proses">
                                                    <x-heroicon-o-x-mark class="w-5 h-5" />
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
                                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-purple-600 text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all shadow-sm">
                                                    <x-heroicon-o-check-circle class="w-4 h-4 mr-1.5" />
                                                    Selesaikan
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE: CARD LIST --}}
                <div class="md:hidden p-3 space-y-2">
                    @foreach ($items as $i)
                        <div id="order-card-{{ $i->id }}"
                            class="rounded-xl border border-gray-200 bg-white shadow-sm px-3 py-2.5">

                            {{-- Top: Code + Status badge --}}
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="inline-flex items-center text-[12px] font-mono font-semibold text-gray-900 bg-gray-100 px-2 py-0.5 rounded">
                                        {{ $i->booking_order_code }}
                                    </div>

                                    <div class="mt-1 text-sm font-semibold text-gray-900 truncate">
                                        {{ $i->customer_name }}
                                    </div>

                                    <div class="mt-0.5 text-[11px] text-gray-500">
                                        Meja:
                                        <span class="font-semibold
                                            @if($i->order_status === 'PROCESSED') text-purple-700 @else text-orange-700 @endif">
                                            {{ $i->table->table_no ?? '-' }}
                                        </span>
                                        <span class="mx-1.5">•</span>
                                        {{ $i->created_at?->format('H:i') }}
                                    </div>
                                </div>

                                <div class="shrink-0">
                                    @if ($i->order_status === 'PROCESSED')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-100 text-purple-800">
                                            <span class="size-1.5 rounded-full bg-purple-600"></span>
                                            Proses
                                        </span>
                                    @elseif ($i->order_status === 'PAID')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-orange-100 text-orange-800">
                                            <span class="size-1.5 rounded-full bg-orange-600"></span>
                                            Menunggu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">
                                            <span class="size-1.5 rounded-full bg-gray-500"></span>
                                            {{ $i->order_status }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Bottom: Total + Actions --}}
                            <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-[11px] text-gray-500 leading-none">Total</div>
                                    <div class="text-sm font-bold text-gray-900 tabular-nums leading-tight">
                                        Rp {{ number_format($i->total_order_value, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    {{-- Detail --}}
                                    <a href="{{ route('employee.cashier.order-detail', $i->id) }}"
                                    data-detail-btn
                                    data-order-id="{{ $i->id }}"
                                    class="p-2 rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition"
                                    title="Detail">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>

                                    {{-- Struk --}}
                                    <button type="button"
                                            data-print-receipt-process
                                            data-order-id="{{ $i->id }}"
                                            class="p-2 rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition"
                                            title="Struk">
                                        <x-heroicon-o-printer class="w-4 h-4" />
                                    </button>

                                    @if ($i->order_status === 'PAID')
                                        <button type="button"
                                                data-turn-to-process-btn
                                                data-order-id="{{ $i->id }}"
                                                data-order-name="{{ $i->customer_name }}"
                                                data-order-code="{{ $i->booking_order_code }}"
                                                data-order-total="{{ $i->total_order_value }}"
                                                data-order-table="{{ $i->table->table_no ?? '-' }}"
                                                data-order-url="{{ route('employee.cashier.process-order', '__ID__') }}"
                                                class="px-3 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-[11px] font-semibold transition">
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
                                                class="p-2 rounded-lg border border-red-200 text-red-600 bg-white hover:bg-red-50 transition"
                                                title="Batal">
                                            <x-heroicon-o-x-mark class="w-4 h-4" />
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
                                                class="px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-[11px] font-semibold transition">
                                            Selesai
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>


@include('pages.employee.cashier.dashboard.modals.cash')
@include('pages.employee.cashier.dashboard.modals.detail')
@include('pages.employee.cashier.dashboard.modals.served')


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