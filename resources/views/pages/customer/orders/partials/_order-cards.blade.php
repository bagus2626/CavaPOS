@forelse($orderHistory as $order)
    @php
        $payment = $order->payment;

        $orderStatusColor = match($order->order_status) {
            'UNPAID'    => 'bg-red-100 text-red-700 border-red-200',
            'PAID'      => 'bg-amber-100 text-amber-800 border-amber-200',
            'PROCESSED' => 'bg-blue-100 text-blue-800 border-blue-200',
            'SERVED'    => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            default     => 'bg-gray-100 text-gray-700 border-gray-200',
        };

        $paymentStatus = $payment->payment_status ?? 'UNRECORDED';
        $paymentStatusColor = match($paymentStatus) {
            'PAID'      => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'PENDING'   => 'bg-amber-100 text-amber-800 border-amber-200',
            'FAILED'    => 'bg-red-100 text-red-700 border-red-200',
            default     => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    @endphp

    {{-- 🔹 seluruh card bisa di-klik --}}
    <div class="border rounded-xl p-4 bg-gray-50 flex flex-col gap-3 order-card cursor-pointer hover:bg-gray-100 transition"
         data-detail-url="{{ route('customer.orders.order-detail', [
                'partner_slug' => $partner_slug,
                'table_code'   => $table_code,
                'order_id'     => $order->id,
          ]) }}"
         tabindex="0"
         role="button">
        {{-- Baris atas: Kode, tanggal, total --}}
        <div class="flex items-start justify-between gap-3">
            <div class="space-y-1">
                <div class="text-xs text-gray-500 uppercase tracking-wide">
                    {{ __('messages.customer.orders.histories.order_code') }}
                </div>
                <div class="font-mono text-sm font-semibold">
                    {{ $order->booking_order_code }}
                </div>

                <div class="text-xs text-gray-500 mt-1">
                    {{ __('messages.customer.orders.histories.date') }}:
                    <span class="font-medium text-gray-800">
                        {{ $order->created_at?->format('d M Y H:i') }}
                    </span>
                </div>

                <div class="text-xs text-gray-500">
                    {{ __('messages.customer.orders.histories.table') }}:
                    <span class="font-medium text-gray-800">
                        {{ $order->table->table_no ?? '-' }}
                    </span>
                </div>
            </div>

            <div class="text-right">
                <div class="text-xs text-gray-500 uppercase tracking-wide">
                    Total
                </div>
                <div class="text-lg font-semibold text-gray-900">
                    Rp {{ number_format($order->total_order_value, 0, ',', '.') }}
                </div>

                @if($order->payment_method)
                    <div class="mt-1 text-xs text-gray-500">
                        {{ __('messages.customer.orders.histories.method') }}:
                        <span class="font-medium text-gray-800">
                            {{ $order->payment_method }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Baris status --}}
        <div class="flex flex-wrap items-center gap-2 mt-1">
            {{-- Status pesanan --}}
            @if ($order->order_status === 'PAYMENT' && !$order->last_xendit_invoice && $payment)
                <span class="inline-flex items-center px-1 py-1 rounded-md text-[11px] font-semibold border bg-red-400 text-white {{ $orderStatusColor }}">
                    {{ __('messages.customer.orders.histories.failed') }}
                </span>
            @else
                <span class="inline-flex items-center px-1 py-1 rounded-md text-[11px] font-semibold border {{ $orderStatusColor }}">
                    {{ $order->order_status }}
                </span>
            @endif

            {{-- Status pembayaran --}}
            <span class="inline-flex items-center px-1 py-1 rounded-md text-[11px] font-semibold border {{ $paymentStatusColor }}">
                @if($payment)
                    {{ $payment->payment_status }}
                @else
                    {{ __('messages.customer.orders.histories.payment_not_found') }}
                @endif
            </span>

            {{-- Metode bayar badge --}}
            @if($payment && $payment->payment_type)
                <span class="inline-flex items-center gap-1 px-1 py-1 rounded-md text-[11px] font-medium border border-gray-200 bg-white text-gray-700">
                    @if(strtoupper($payment->payment_type) === 'QRIS')
                        <img src="{{ asset('icons/qris_svg.svg') }}"
                             alt="QRIS"
                             class="h-3 w-auto">
                    @else
                        {{ $payment->payment_type }}
                    @endif
                </span>
            @endif
        </div>

        {{-- List item singkat --}}
        <div class="mt-2 text-xs text-gray-600">
            @php
                $names = $order->order_details->pluck('product_name')->toArray();
                $preview = implode(', ', array_slice($names, 0, 3));
                $extra   = count($names) > 3 ? ' +' . (count($names) - 3) . ' item' : '';
            @endphp
            <span class="font-medium">Item:</span>
            <span>{{ $preview }}{{ $extra }}</span>
        </div>

        {{-- Aksi --}}
        <div class="flex flex-wrap items-center justify-between gap-2 mt-2 pt-2 border-t border-dashed border-gray-200">
            <div class="text-[11px] text-gray-500">
                @if($payment && $payment->payment_status === 'PAID' && $payment->created_at)
                    {{ __('messages.customer.orders.histories.created') }}: {{ $order->created_at?->format('d M Y H:i') }} •
                    {{ __('messages.customer.orders.histories.paid') }}: {{ $payment->created_at->format('d M Y H:i') }}
                @else
                    {{ __('messages.customer.orders.histories.created') }}: {{ $order->created_at?->format('d M Y H:i') }}
                @endif
            </div>

            <div class="flex items-center gap-2">
                {{-- 🔻 TOMBOL DETAIL DIHAPUS --}}

                @if($order->payment_flag == 1)
                    <a href="{{ route('customer.orders.receipt', $order->id) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-[#ae1504] text-xs md:text-sm text-[#ae1504] hover:bg-[#8a1103] hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"
                             class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 8.25h6m-6 3h3.75M4.5 9.75v8.25a2.25 2.25 0 002.25 2.25h10.5A2.25 2.25 0 0019.5 18V9.75M16.5 6h-9A1.5 1.5 0 006 7.5v.75h12V7.5A1.5 1.5 0 0016.5 6z" />
                        </svg>
                        <span>{{ __('messages.customer.orders.histories.receipt') }}</span>
                    </a>

                    {{-- PESAN LAGI --}}
                    <a href="{{ route('customer.menu.index', [
                            'partner_slug'       => $partner_slug,
                            'table_code'         => $table_code,
                            'reorder_order_id'   => $order->id,
                        ]) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-emerald-500 text-xs md:text-sm text-emerald-700 hover:bg-emerald-50">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"
                             class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4.5 4.5h10.125M4.5 9h6.75M4.5 13.5h10.125M4.5 18h6.75M16.5 7.5l3 3-3 3" />
                        </svg>
                        <span>{{ __('messages.customer.orders.histories.order_again') }}</span>
                    </a>
                @endif
                @if ($order->order_status === 'PAYMENT')
                    @if ($order->last_xendit_invoice)
                        <a href="{{ $order->last_xendit_invoice->invoice_url }}"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs bg-[#ae1504] md:text-sm text-white hover:bg-[#8a1103]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" 
                                viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"
                                class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 7.5h18M3 10.5h18M4.5 6A1.5 1.5 0 003 7.5v9A1.5 1.5 0 004.5 18h15a1.5 1.5 0 001.5-1.5v-9A1.5 1.5 0 0019.5 6h-15z" />
                            </svg>

                            <span>{{ __('messages.customer.orders.histories.continue_payment') }}</span>
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
@empty
    {{-- ... --}}
@endforelse
