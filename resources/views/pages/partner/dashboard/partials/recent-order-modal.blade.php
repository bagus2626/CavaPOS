@foreach ($data['last_orders'] as $order)
    <div class="modal fade text-left" id="orderDetailModal{{ $order->id }}" tabindex="-1" role="dialog"
        aria-labelledby="orderDetailModalLabel{{ $order->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--choco), var(--soft-choco)); border: none;">
                    <h3 class="modal-title text-white" id="orderDetailModalLabel{{ $order->id }}">
                        <i class="fas fa-receipt"></i> {{ __('messages.owner.sales_report.order_details') }}
                    </h3>
                </div>
                <div class="modal-body">
                    <!-- Order Basic Info -->
                    <div class="border-bottom pb-2 mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="text-bold-600 mb-50">{{ $order->booking_order_code }}</h4>
                                <p class="text-muted mb-0">
                                    <small>{{ $order->created_at->format('d M Y, H:i') }}</small>
                                </p>
                            </div>
                            <h3 class="text-bold-700 mb-0" style="color: var(--choco);">
                                Rp {{ number_format($order->total_order_value, 0, ',', '.') }}
                            </h3>
                        </div>

                        <div class="mt-1 d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                            @php
                                $statusConfig = [
                                    'UNPAID' => ['class' => 'warning', 'text' => 'Unpaid'],
                                    'PROCESSED' => ['class' => 'primary', 'text' => 'Processed'],
                                    'SERVED' => ['class' => 'success', 'text' => 'Served'],
                                    'PAID' => ['class' => 'primary', 'text' => 'Paid'],
                                    'PENDING' => ['class' => 'warning', 'text' => 'Pending'],
                                    'CANCELLED' => ['class' => 'danger', 'text' => 'Cancelled'],
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

                            @if ($order->order_by === 'CASHIER')
                                <span class="badge badge-light-primary badge-pill"
                                    style="min-width: 85px; text-align: center;">
                                    Cashier
                                </span>
                            @elseif($order->order_by === 'CUSTOMER')
                                <span class="badge badge-light-success badge-pill"
                                    style="min-width: 85px; text-align: center;">
                                    Customer
                                </span>
                            @endif

                            @if ($order->payment_method === 'CASH')
                                <span class="badge badge-light-success badge-pill"
                                    style="min-width: 85px; text-align: center;">
                                    Cash
                                </span>
                            @elseif($order->payment_method === 'QRIS')
                                <span class="badge badge-light-primary badge-pill"
                                    style="min-width: 85px; text-align: center;">
                                    QRIS
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Order Information -->
                    <div class="border-bottom pb-2 mb-2">
                        <h5 class="text-bold-600 mb-1">Order Information</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <span class="text-muted d-block">{{ __('messages.owner.dashboard.customer') }}</span>
                                    <span class="text-bold-500">{{ $order->customer_name }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <span class="text-muted d-block">Table Number</span>
                                    @if ($order->table)
                                        <span class="badge badge-light-info badge-pill">{{ $order->table->table_no }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($order->customer_order_note)
                            <div class="mt-1">
                                <span class="text-muted d-block">Notes</span>
                                <span class="text-bold-500">{{ $order->customer_order_note }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Order Items -->
                    @if ($order->order_details && $order->order_details->count() > 0)
                        <div class="mb-0">
                            <h5 class="text-bold-600 mb-1">Order Items</h5>

                            <div class="px-1">
                                @foreach ($order->order_details as $item)
                                    <div class="py-75" style="border-bottom: 1px dashed #ddd;">
                                        <!-- Product Name and Base Price -->
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <span class="text-bold-500">
                                                    {{ $item->product_name ?? ($item->partnerProduct->name ?? 'N/A') }}
                                                </span>
                                                <span class="text-muted ml-50">x{{ $item->quantity }}</span>
                                            </div>
                                            <div class="text-right ml-1" style="min-width: 100px;">
                                                <div class="text-muted small">
                                                    Rp {{ number_format($item->base_price, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Options -->
                                        @if ($item->order_detail_options && $item->order_detail_options->count() > 0)
                                            <div class="mt-25">
                                                @foreach ($item->order_detail_options as $option)
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="text-muted small">
                                                            + {{ $option->partner_product_option_name ?? ($option->option->name ?? 'N/A') }}
                                                        </div>
                                                        <div class="text-right ml-1" style="min-width: 100px;">
                                                            @if (($option->price ?? 0) > 0)
                                                                <div class="text-success small">
                                                                    +Rp {{ number_format($option->price, 0, ',', '.') }}
                                                                </div>
                                                            @else
                                                                <div class="text-muted small">
                                                                    Rp {{ number_format($option->price, 0, ',', '.') }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Customer Note -->
                                        @if ($item->customer_note)
                                            <div class="text-warning small mt-25">
                                                Note: {{ $item->customer_note }}
                                            </div>
                                        @endif

                                        <!-- Item Total -->
                                        <div class="d-flex justify-content-between align-items-start mt-50">
                                            <div class="text-bold-500">Subtotal:</div>
                                            <div class="text-right ml-1" style="min-width: 100px;">
                                                <div class="text-bold-500">
                                                    Rp {{ number_format(($item->base_price + $item->options_price) * $item->quantity, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="text-bold-600">Total:</span>
                                    <span class="text-bold-700" style="font-size: 1.2rem; color: var(--choco);">
                                        Rp {{ number_format($order->total_order_value, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> No items found in this order.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-choco" data-dismiss="modal">
                        <i class="fas fa-times d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<style>
/* Modal styling agar selaras dengan desain dashboard */
.modal-content {
    border-radius: var(--radius);
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.modal-header {
    border-top-left-radius: var(--radius);
    border-top-right-radius: var(--radius);
    padding: 1rem 1.5rem;
}

.modal-header .close {
    opacity: 0.8;
    text-shadow: none;
    font-size: 1.5rem;
}

.modal-header .close:hover {
    opacity: 1;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: 1px solid #eef1f4;
    padding: 0.75rem 1.5rem;
}

.btn-choco {
    background: linear-gradient(135deg, var(--choco), var(--soft-choco));
    color: #ffffff;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-choco:hover {
    background: linear-gradient(135deg, var(--soft-choco), var(--choco));
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(140, 16, 0, 0.3);
}

.badge-light-primary {
    background-color: rgba(140, 16, 0, 0.1);
    color: var(--choco);
}

.badge-light-success {
    background-color: rgba(22, 163, 74, 0.1);
    color: #16a34a;
}

.badge-light-info {
    background-color: rgba(14, 165, 233, 0.1);
    color: #0ea5e9;
}

/* Hover effect untuk table row */
.table-modern tbody tr[data-toggle="modal"] {
    transition: all 0.2s ease;
}

.table-modern tbody tr[data-toggle="modal"]:hover {
    background-color: #f9fafb;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
</style>

<script>
// Prevent link default action when clicking on order code
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.order-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
        });
    });
});
</script>