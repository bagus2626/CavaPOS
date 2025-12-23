<tr>
    <td class="text-left">
        <span class="text-bold-500">{{ $bookingOrders->firstItem() + $index }}</span>
    </td>
    <td>
        <span class="font-weight-bold">{{ $order->booking_order_code }}</span>
    </td>
    <td>{{ $order->customer_name }}</td>
    <td>
        @if ($order->payment_method === 'CASH')
            <span class="badge badge-light-success badge-pill">Cash</span>
        @elseif($order->payment_method === 'QRIS')
            <span class="badge badge-light-primary badge-pill">QRIS</span>
        @else
            <span class="text-muted">{{ $order->payment_method ?? '-' }}</span>
        @endif
    </td>
    <td class="text-bold-500">
        Rp {{ number_format($order->total_order_value, 0, ',', '.') }}
    </td>
    <td>
        <span class="text-muted d-block">{{ $order->created_at->format('d M Y') }}</span>
        <span class="text-muted">{{ $order->created_at->format('H:i') }}</span>
    </td>
    <td>
        @php
            $statusConfig = [
                'UNPAID' => ['class' => 'danger', 'text' => 'Unpaid'],
                'PROCESSED' => ['class' => 'info', 'text' => 'Processed'],
                'SERVED' => ['class' => 'primary', 'text' => 'Served'],
                'PAID' => ['class' => 'success', 'text' => 'Paid'],
            ];
            $status = $statusConfig[$order->order_status] ?? [
                'class' => 'secondary',
                'text' => $order->order_status,
            ];
        @endphp
        <span class="badge badge-{{ $status['class'] }} badge-pill"
            style="min-width: 85px; text-align: center;">
            {{ $status['text'] }}
        </span>
    </td>
    <td>
        <a href="#" data-toggle="modal" data-target="#orderDetailModal{{ $order->id }}"
            title="View Product Details">
            <i class="badge-circle badge-circle-light-secondary bx bx-show font-medium-1"></i>
        </a>
    </td>
</tr>