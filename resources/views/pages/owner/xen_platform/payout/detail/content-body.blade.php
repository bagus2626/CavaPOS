<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Header Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-money-bill-transfer mr-2"></i>
                            Payout Details
                        </h3>
                        <div class="card-tools">
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary mr-2">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Amount & Status -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Transaction Amount</h6>
                                <h2 class="font-weight-bold text-primary">
                                    {{ $data['currency'] ?? 'IDR' }}
                                    {{ number_format($data['amount'] ?? 0, 0, ',', '.') }}
                                </h2>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <h6 class="text-muted">Status</h6>
                                @php
                                    $status = $data['status'] ?? 'UNKNOWN';
                                    $statusData = [
                                        'SUCCEEDED' => ['class' => 'badge-success', 'icon' => 'fas fa-check-circle'],
                                        'REQUESTED' => ['class' => 'badge-warning', 'icon' => 'fas fa-clock'],
                                        'FAILED' => ['class' => 'badge-danger', 'icon' => 'fas fa-times-circle'],
                                        'CANCELLED' => ['class' => 'badge-secondary', 'icon' => 'fas fa-ban'],
                                        'REVERSED' => ['class' => 'badge-secondary', 'icon' => 'fas fa-undo'],
                                        'ACCEPTED' => ['class' => 'badge-info', 'icon' => 'fas fa-hourglass-half'],
                                    ];
                                    $statusDisplay = $statusData[$status] ?? $statusData['ACCEPTED'];
                                @endphp
                                <span class="badge {{ $statusDisplay['class'] }} badge-lg">
                                    <i class="{{ $statusDisplay['icon'] }} mr-1"></i>
                                    {{ $status }}
                                </span>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="font-weight-bold mb-3 border-bottom pb-2">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Basic Information
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="font-weight-bold" style="width: 150px;">Created Date</td>
                                            <td>: {{ \Carbon\Carbon::parse($data['created'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }} (GMT +7)</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">External ID</td>
                                            <td>: {{ $data['reference_id'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Payout ID</td>
                                            <td>: {{ $data['id'] ?? '-' }}</td>
                                        </tr>
                                        @if(!empty($data['estimated_arrival_time']))
                                            <tr>
                                                <td class="font-weight-bold">Estimated Arrival</td>
                                                <td>: {{ \Carbon\Carbon::parse($data['estimated_arrival_time'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }} (GMT +7)</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-credit-card mr-2"></i>
                            Payment Details
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                <tr>
                                    <th style="width: 30%">Field</th>
                                    <th>Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="font-weight-bold">Account Holder Name</td>
                                    <td>{{ $data['channel_properties']['account_holder_name'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Bank Code</td>
                                    <td>
                                            <span class="badge badge-primary">
                                                {{ $data['channel_code'] ?? '-' }}
                                            </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Bank Account Number</td>
                                    <td>
                                        <code class="font-weight-bold">
                                            {{ $data['channel_properties']['account_number'] ?? '-' }}
                                        </code>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Description</td>
                                    <td>{{ $data['description'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Bank Reference</td>
                                    <td>
                                        @if(!empty($data['connector_reference']))
                                            <code>{{ $data['connector_reference'] }}</code>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Email Notification</td>
                                    <td>
                                        @if(!empty($data['receipt_notification']['email_to'][0]))
                                            <i class="fas fa-envelope text-primary mr-1"></i>
                                            {{ $data['receipt_notification']['email_to'][0] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @if(!empty($data['failure_code']))
                                    <tr class="table-danger">
                                        <td class="font-weight-bold">Failure Code</td>
                                        <td class="text-danger font-weight-bold">
                                            {{ $data['failure_code'] }}
                                        </td>
                                    </tr>
                                @endif
                                @if(!empty($data['failure_message']))
                                    <tr class="table-danger">
                                        <td class="font-weight-bold">Failure Message</td>
                                        <td class="text-danger">
                                            {{ $data['failure_message'] }}
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Payout Status Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-2"></i>
                            Payout Status Timeline
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $statusClasses = [
                                'ACCEPTED' => 'bg-info',
                                'REQUESTED' => 'bg-warning',
                                'SUCCEEDED' => 'bg-success',
                                'FAILED' => 'bg-danger',
                                'CANCELLED' => 'bg-secondary',
                                'REVERSED' => 'bg-secondary',
                            ];

                            $statusIcons = [
                                'ACCEPTED' => 'fas fa-hourglass-half',
                                'REQUESTED' => 'fas fa-paper-plane',
                                'SUCCEEDED' => 'fas fa-check-circle',
                                'FAILED' => 'fas fa-times-circle',
                                'CANCELLED' => 'fas fa-ban',
                                'REVERSED' => 'fas fa-undo',
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

                        @if(!empty($data['created']) || !empty($data['status']))
                            <div class="timeline">
                                <!-- CREATED Event -->
                                @if(!empty($data['created']))
                                    <div>
                                        <i class="fas fa-plus-circle bg-primary"></i>
                                        <div class="timeline-item">
                                        <span class="time">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ \Carbon\Carbon::parse($data['created'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                        </span>
                                            <h3 class="timeline-header mt-2">
                                                <span class="badge bg-primary mr-2">CREATED</span>
                                                Payout request was created
                                            </h3>
                                        </div>
                                    </div>
                                @endif

                                <!-- Current Status Event -->
                                @if(!empty($data['status']))
                                    @php
                                        $currentStatus = $data['status'];
                                        $statusTime = ($currentStatus === 'SUCCEEDED' && !empty($data['estimated_arrival_time']))
                                            ? $data['estimated_arrival_time']
                                            : ($data['updated'] ?? $data['created'] ?? null);
                                    @endphp

                                    @if(!empty($statusTime))
                                        <div>
                                            <i class="{{ $statusIcons[$currentStatus] ?? 'fas fa-info-circle' }} {{ $statusClasses[$currentStatus] ?? 'bg-info' }}"></i>
                                            <div class="timeline-item">
                                            <span class="time">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ \Carbon\Carbon::parse($statusTime)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                            </span>
                                                <h3 class="timeline-header">
                                                <span class="badge {{ $statusClasses[$currentStatus] ?? 'bg-info' }} mr-2">
                                                    {{ $currentStatus }}
                                                </span>
                                                    {{ $statusDescriptions[$currentStatus] ?? 'Current payout status.' }}
                                                </h3>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <!-- Timeline End -->
                                <div>
                                    <i class="fas fa-clock bg-gray"></i>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No payout history available</p>
                            </div>
                        @endif
                    </div>
                    @if(!empty($data['created']) || !empty($data['status']))
                        <div class="card-footer">
                            <small class="text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                All times in GMT +7
                            </small>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-2"></i>
                            Quick Actions
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
{{--                            @if($data['status'] === 'REQUESTED' || $data['status'] === 'ACCEPTED')--}}
{{--                                <button class="btn btn-outline-warning btn-sm" onclick="refreshPayoutStatus()">--}}
{{--                                    <i class="fas fa-sync-alt mr-1"></i> Refresh Status--}}
{{--                                </button>--}}
{{--                            @endif--}}

{{--                            @if($data['status'] === 'ACCEPTED')--}}
{{--                                <button class="btn btn-outline-danger btn-sm" onclick="cancelPayout()">--}}
{{--                                    <i class="fas fa-times-circle mr-1"></i> Cancel Payout--}}
{{--                                </button>--}}
{{--                            @endif--}}

{{--                            <button class="btn btn-outline-primary btn-sm" onclick="exportPayoutDetails()">--}}
{{--                                <i class="fas fa-download mr-1"></i> Export Details--}}
{{--                            </button>--}}

                            <button class="btn btn-outline-secondary btn-sm" onclick="printPayoutReceipt()">
                                <i class="fas fa-print mr-1"></i> Print Receipt
                            </button>

{{--                            @if($data['status'] === 'FAILED' && !empty($data['reference_id']))--}}
{{--                                <button class="btn btn-outline-info btn-sm" onclick="retryPayout()">--}}
{{--                                    <i class="fas fa-redo mr-1"></i> Retry Payout--}}
{{--                                </button>--}}
{{--                            @endif--}}
                        </div>
                    </div>
                </div>

                <!-- Payout Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-2"></i>
                            Payout Information
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="small text-muted">
                            <p><strong>Payout ID:</strong> {{ $data['id'] ?? 'N/A' }}</p>
                            <p><strong>Channel:</strong> {{ $data['channel_code'] ?? 'N/A' }}</p>
                            <p><strong>Currency:</strong> {{ $data['currency'] ?? 'IDR' }}</p>
                            <p><strong>Amount:</strong> {{ number_format($data['amount'] ?? 0, 0, ',', '.') }}</p>
                            @if(!empty($data['estimated_arrival_time']))
                                <p><strong>ETA:</strong> {{ \Carbon\Carbon::parse($data['estimated_arrival_time'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function printPayoutReceipt() {
        window.print();
    }
</script>
