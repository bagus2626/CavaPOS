<section class="invoice-view-wrapper">
    <div class="row">
        <!-- invoice view page -->
        <div class="col-xl-7 col-md-8 col-12">
            <div class="card invoice-print-area border">
                <div class="card-content">
                    <div class="card-body pb-0 mx-25">
                        <div class="row">
                            <div class="col-12">
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mb-2">
                                    <i class="bx bx-arrow-back me-1"></i> Kembali
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h6 class="invoice-from">Transaction ID#</h6>
                                <h5 class="text-primary">{{ $transaction['id'] ?? 'N/A' }}</h5>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-6">
                                <h6 class="invoice-from">Transaction Amount</h6>
                                <h2 class="text-bold-700 text-{{ ($transaction['cashflow'] ?? '') === 'MONEY_IN' ? 'success' : 'danger' }}">
                                    <span class="text-light-secondary me-1">
                                        {{ $transaction['currency'] ?? 'IDR' }}
                                    </span>
                                    {{ number_format($transaction['amount'] ?? 0, 0, ',', '.') }}
                                </h2>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">

                                <div class="d-flex align-items-center">
                                    @php
                                        $status = $transaction['status'] ?? 'UNKNOWN';
                                        $statusData = [
                                            'SUCCESS' => ['class' => 'badge-success', 'icon' => 'bx-check-circle'],
                                            'PENDING' => ['class' => 'badge-warning', 'icon' => 'bx-time'],
                                            'FAILED' => ['class' => 'badge-danger', 'icon' => 'bx-x-circle'],
                                            'VOIDED' => ['class' => 'badge-secondary', 'icon' => 'bx-minus-circle'],
                                            'UNKNOWN' => ['class' => 'badge-info', 'icon' => 'bx-help-circle'],
                                        ];

                                        $data = $statusData[$status] ?? $statusData['UNKNOWN'];
                                    @endphp

                                    <div class="badge badge-pill {{ $data['class'] }} badge-glow d-inline-flex align-items-center text-uppercase p-1">
                                        <i class="bx {{ $data['icon'] }} font-medium-1 mr-25"></i>
                                        <span class="fw-bold">{{ $status }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <!-- product details table-->
                    <div class="invoice-product-details table-responsive mx-md-25 px-md-3">
                        <table class="table mb-5">
                            <tbody>
                            <tr>
                                <td>Type</td>
                                <td>: {{ $transaction['type'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Channel</td>
                                <td>: {{ $transaction['channel_category'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Channel Name</td>
                                <td>: {{ $transaction['channel_code'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Account Number</td>
                                <td>: {{ $transaction['account_identifier'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Transaction Fee Status</td>
                                <td>:
                                    @php
                                        $feeStatus = strtoupper($transaction['fee']['status'] ?? 'N/A');
                                        $feeStatusClass = 'bg-info';

                                        switch ($feeStatus) {
                                            case 'COMPLETED':
                                                $feeStatusClass = 'bg-success';
                                                break;
                                            case 'PENDING':
                                                $feeStatusClass = 'bg-warning text-dark';
                                                break;
                                            case 'REVERSED':
                                                $feeStatusClass = 'bg-primary';
                                                break;
                                            case 'CANCELED':
                                                $feeStatusClass = 'bg-danger';
                                                break;
                                            case 'NOT_APPLICABLE':
                                            default:
                                                $feeStatusClass = 'bg-secondary';
                                                break;
                                        }
                                    @endphp
                                    <div class="badge badge-pill {{ $feeStatusClass }} badge-glow d-inline-flex align-items-center text-uppercase">
                                        <span class="fw-bold"> {{ str_replace('_', ' ', $feeStatus) }}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Transaction Fee</td>
                                <td>
                                    <div class="invoice-calc d-flex justify-content-between">
                                        <span class="invoice-title">: {{ $transaction['currency'] ?? 'IDR' }}</span>
                                        <span class="invoice-value text-bold-500">{{ number_format($transaction['fee']['xendit_fee']) ?? '-' }}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Transaction VAT</td>
                                <td>
                                    <div class="invoice-calc d-flex justify-content-between">
                                        <span class="invoice-title">: {{ $transaction['currency'] ?? 'IDR' }}</span>
                                        <span class="invoice-value text-bold-500">{{ number_format($transaction['fee']['value_added_tax']) ?? '-' }}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Net Amount</td>
                                <td>
                                    <div class="invoice-calc d-flex justify-content-between">
                                        <span class="invoice-title">: {{ $transaction['net_amount_currency'] ?? 'IDR' }}</span>
                                        <span class="invoice-value text-bold-500">{{ number_format($transaction['net_amount']) ?? '-' }}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Reference</td>
                                <td>: {{ $transaction['reference_id'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Payment Request ID</td>
                                <td>: {{ $transaction['product_data']['payment_request_id'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Product ID</td>
                                <td>: {{ $transaction['product_id'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Capture ID</td>
                                <td>: {{ $transaction['product_data']['capture_id'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Channel Reference</td>
                                <td>: {{ '-' }}</td>
                            </tr>
                            <tr>
                                <td>Settlement Status</td>
                                <td>: {{ $transaction['settlement_status'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Estimated Settlement Date (GMT +7)</td>
                                <td>
                                    @if ($transaction['estimated_settlement_time'] ?? null)
                                        : {{ \Carbon\Carbon::parse($transaction['estimated_settlement_time'])->setTimezone('Asia/Jakarta')->format('d M Y, h:i A') }}
                                    @else
                                        : -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Date Settled (GMT +7)</td>
                                <td>
                                    @if ($transaction['updated'] ?? null)
                                        : {{ \Carbon\Carbon::parse($transaction['updated'])->setTimezone('Asia/Jakarta')->format('d M Y, h:i A') }}
                                    @else
                                        : -
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- invoice action  -->
        <div class="col-xl-4 col-md-4 col-12">
            <div class="card border">
                <div class="card-body">
                    <h5 class="text-bold-500 mb-1">Event History</h5>

                    @php
                        $history = [];
                        $statusClasses = [
                            'SUCCESS' => 'timeline-icon-success',
                            'SETTLED' => 'timeline-icon-success',
                            'PENDING' => 'timeline-icon-warning',
                            'FAILED' => 'timeline-icon-danger',
                            'UNKNOWN' => 'timeline-icon-info',
                        ];

                        if (!empty($transaction['created'])) {
                            $history[] = [
                                'status' => 'CREATED',
                                'timestamp' => \Carbon\Carbon::parse($transaction['created'])->setTimezone('Asia/Jakarta'),
                                'details' => 'Transaksi dibuat dan pembayaran dimulai.',
                                'sort_key' => $transaction['created'],
                            ];
                        }

                        if (!empty($transaction['updated']) && ($transaction['status'] !== 'PENDING')) {
                            $history[] = [
                                'status' => $transaction['status'],
                                'timestamp' => \Carbon\Carbon::parse($transaction['updated'])->setTimezone('Asia/Jakarta'),
                                'details' => 'Status akhir transaksi.',
                                'sort_key' => $transaction['updated'],
                            ];
                        }

                        if (!empty($transaction['estimated_settlement_time']) && $transaction['settlement_status'] === 'SETTLED') {
                            $settledTime = $transaction['updated'];

                            $history[] = [
                                'status' => 'SETTLED',
                                'timestamp' => \Carbon\Carbon::parse($settledTime)->setTimezone('Asia/Jakarta'),
                                'details' => 'Dana telah diselesaikan ke akun Anda.',
                                'sort_key' => $settledTime,
                            ];
                        }
                        else if (!empty($transaction['estimated_settlement_time']) && $transaction['settlement_status'] !== 'SETTLED') {
                            $history[] = [
                                'status' => 'EST. SETTLEMENT',
                                'timestamp' => \Carbon\Carbon::parse($transaction['estimated_settlement_time'])->setTimezone('Asia/Jakarta'),
                                'details' => 'Estimasi waktu dana akan diselesaikan.',
                                'sort_key' => $transaction['estimated_settlement_time'],
                            ];
                        }

                        $eventHistory = collect($history)->sortByDesc('sort_key')->unique('status')->values()->all();
                    @endphp

                    <ul class="widget-timeline">
                        @foreach($eventHistory as $event)
                            @php
                                $status = strtoupper($event['status']);
                                $statusKey = ($status === 'CREATED' || $status === 'EST. SETTLEMENT') ? 'UNKNOWN' : $transaction['status'];
                                $class = $statusClasses[$statusKey] ?? $statusClasses['UNKNOWN'];
                                $time = $event['timestamp']->format('d M Y, H:i A');
                                $iconClass = ($status === 'SUCCESS' || $status === 'SETTLED') ? 'timeline-icon-success' : $class;
                            @endphp

                            <li class="timeline-items {{ $iconClass }} active">

                                <div class="timeline-time ms-auto fw-bold">
                                    {{ $time }} (GMT +7)
                                    <i class="bx bx-info-circle ms-1 text-muted cursor-pointer" data-toggle="tooltip" data-placement="top" title="{{ $event['details'] }}"></i>
                                </div>

                                <h6 class="timeline-title d-flex align-items-center text-bold-500 mb-0">
                                    {{ $status }}
                                </h6>

                                <p class="timeline-text text-muted mb-1">Timestamp</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
