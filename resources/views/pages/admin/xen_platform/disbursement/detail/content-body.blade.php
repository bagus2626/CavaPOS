<section class="invoice-view-wrapper">
    <div class="row">
        <div class="col-xl-6 col-md-8 col-12">
            <div class="card shadow border">
                <div class="card-content">
                    <div class="card-body pb-0 mx-25 mb-3">
                        <div class="row">
                            <div class="col-12">
                                <a href="{{ url()->previous() }}"
                                   class="btn btn-outline-secondary mb-2">
                                    <i class="bx bx-arrow-back me-1"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <h6 class="invoice-from">Transaction Amount</h6>
                                <h2 class="text-bold-700">
                                    {{ $data['currency'] ?? 'IDR' }}
                                    {{ number_format($data['amount'] ?? 0, 0, ',', '.') }}
                                </h2>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                <div class="d-flex align-items-center">

                                    @php
                                        $status = $data['status'] ?? 'UNKNOWN';

                                        $statusData = [
                                            'SUCCEEDED' => ['class' => 'badge-success', 'icon' => 'bx-check-circle'],
                                            'REQUESTED' => ['class' => 'badge-warning', 'icon' => 'bx-time'],
                                            'FAILED' => ['class' => 'badge-danger', 'icon' => 'bx-x-circle'],
                                            'CANCELLED' => ['class' => 'badge-secondary', 'icon' => 'bx-minus-circle'],
                                            'REVERSED' => ['class' => 'badge-secondary', 'icon' => 'bx-minus-circle'],
                                            'ACCEPTED' => ['class' => 'badge-info', 'icon' => 'bx-help-circle'],
                                        ];

                                        $statusDisplay = $statusData[$status] ?? $statusData['UNKNOWN'];
                                    @endphp

                                    <div class="badge badge-pill {{ $statusDisplay['class'] }} badge-glow d-inline-flex align-items-center text-uppercase p-1">
                                        <i class="bx {{ $statusDisplay['icon'] }} font-medium-1 mr-25"></i>
                                        <span class="fw-bold">{{ $status }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row invoice-info">
                            <div class="col-6">
                                <div class="mb-0">
                                    <span>Created Date</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <h6 class="invoice-to">{{ \Carbon\Carbon::parse($data['created'])->setTimezone('Asia/Jakarta')->format('d M Y, h:i:s A') }}
                                    (GMT +7)</h6>
                            </div>
                        </div>
                        <hr>
                        <div class="row invoice-info">
                            <div class="col-6">
                                <div class="mb-0">
                                    <span>External ID</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <h6 class="invoice-to">{{ $data['reference_id'] }}</h6>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>
            </div>
            <div class="card invoice-print-area border">
                <div class="card-content">
                    <div class="card-body pb-0 mx-25">
                        <div class="row">
                            <div class="col-6">
                                <h5>Payment Details</h5>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="invoice-product-details table-responsive px-md-3">
                        <table class="table mb-5">
                            <tbody>
                            <tr>
                                <td>Name</td>
                                <td class="text-bold-500">{{ $data['channel_properties']['account_holder_name'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Bank Code</td>
                                <td class="text-bold-500">{{ $data['channel_code'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Bank Account Number</td>
                                <td class="text-bold-500">{{ $data['channel_properties']['account_number'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Description</td>
                                <td class="text-bold-500">{{ $data['description'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Bank Reference</td>
                                <td class="text-bold-500">{{ $data['connector_reference'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Email Notification</td>
                                <td class="text-bold-500">{{ $data['receipt_notification']['email_to'][0] ?? '-' }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 col-12">
            <div class="card invoice-print-area border">
                <div class="card-body">
                    <h5 class="text-bold-700 mb-1">Payout Status</h5>

                    @php
                        $statusClasses = [
                            'ACCEPTED' => 'timeline-icon-warning',
                            'REQUESTED' => 'timeline-icon-info',
                            'SUCCEEDED' => 'timeline-icon-success',
                            'FAILED' => 'timeline-icon-danger',
                            'CANCELLED' => 'timeline-icon-danger',
                            'REVERSED' => 'timeline-icon-danger',
                        ];

                        $statusDescriptions = [
                            'ACCEPTED' => 'The payout request has been accepted and has not yet been sent on to a channel.',
                            'REQUESTED' => 'The payout has been sent to the channel. Funds have been sent to the channel for processing.',
                            'SUCCEEDED' => 'Sender bank/channel has sent out the payout.',
                            'FAILED' => 'Payout failed. Check the failure reason for details.',
                            'CANCELLED' => 'Payout has been cancelled per your request.',
                            'REVERSED' => 'Payout was rejected by the channel after the payout succeeded.',
                        ];
                    @endphp

                    <ul class="widget-timeline">
                        <!-- Status 1: CREATED -->
                        @if(!empty($data['created']))
                            <li class="timeline-items timeline-icon-info active">
                                <div class="timeline-time ms-auto fw-bold">
                                    {{ \Carbon\Carbon::parse($data['created'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i A') }} (GMT +7)
                                    <i class="bx bx-info-circle ms-1 text-muted cursor-pointer" data-toggle="tooltip" data-placement="top" title="Payout request was created."></i>
                                </div>
                                <h6 class="timeline-title d-flex align-items-center text-bold-500 mb-0">
                                    CREATED
                                </h6>
                                <p class="timeline-text text-muted mb-1">Timestamp</p>
                            </li>
                        @endif

                        @if(!empty($data['status']))
                            @php
                                $currentStatus = $data['status'];
                                $statusTime = ($currentStatus === 'SUCCEEDED' && !empty($data['estimated_arrival_time']))
                                    ? $data['estimated_arrival_time']
                                    : ($data['updated'] ?? $data['created'] ?? null);
                            @endphp

                            @if(!empty($statusTime))
                                <li class="timeline-items {{ $statusClasses[$currentStatus] ?? 'timeline-icon-info' }} active">
                                    <div class="timeline-time ms-auto fw-bold">
                                        {{ \Carbon\Carbon::parse($statusTime)->setTimezone('Asia/Jakarta')->format('d M Y, H:i A') }} (GMT +7)
                                        <i class="bx bx-info-circle ms-1 text-muted cursor-pointer" data-toggle="tooltip" data-placement="top" title="{{ $statusDescriptions[$currentStatus] ?? 'Current payout status.' }}"></i>
                                    </div>
                                    <h6 class="timeline-title d-flex align-items-center text-bold-500 mb-0">
                                        {{ $currentStatus }}
                                    </h6>
                                    <p class="timeline-text text-muted mb-1">Timestamp</p>
                                </li>
                            @endif
                        @endif

                        @if(empty($data['created']) && empty($data['status']))
                            <li class="timeline-items timeline-icon-info">
                                <h6 class="timeline-title text-bold-500 mb-0 text-muted">
                                    No payout history available
                                </h6>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
