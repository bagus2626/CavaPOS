<div class="lg:col-span-1 h-full">
    <div class="rounded-2xl border border-choco/10 bg-white shadow-sm h-full flex flex-col">
        <!-- HEADER jadi sticky -->
        <div class="px-4 py-3 border-b border-choco/10 flex items-center justify-between sticky top-0 bg-white z-10">
            <h2 class="font-semibold text-choco">Order Selesai</h2>
            <span class="text-xs text-gray-500">{{ $items->count() }} order</span>
        </div>

        <!-- SCROLL AREA -->
        <div class="p-4 overflow-y-auto max-h-[70vh]">
            @if ($items->isEmpty())
                <div class="text-center text-sm text-gray-500 h-full">
                    Belum ada order yang selesai.
                </div>
            @else
                <ul class="space-y-3">
                    @foreach ($items as $i)
                        <li class="rounded-xl border border-choco/10 p-3 hover:bg-soft-choco/5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $i->booking_order_code }} &middot; Meja {{ $i->table->table_no ?? '-' }} . {{ $i->payment_method }}
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

