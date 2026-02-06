<div class="row">
    {{-- Main Content --}}
    <div class="col-lg-8">
        
        {{-- Payout ID & Status Card --}}
        <div class="modern-card">
            <div class="detail-hero-header">
                {{-- Payout Info --}}
                <div class="detail-hero-info">
                    <h3 class="detail-hero-name">{{ $data['id'] ?? 'N/A' }}</h3>
                    <div class="detail-hero-badges">
                        @php
                            $status = $data['status'] ?? 'UNKNOWN';
                            $statusBadge = [
                                'SUCCEEDED' => 'badge-success',
                                'REQUESTED' => 'badge-warning',
                                'FAILED' => 'badge-danger',
                                'CANCELLED' => 'badge-secondary',
                                'REVERSED' => 'badge-secondary',
                                'ACCEPTED' => 'badge-info',
                                'UNKNOWN' => 'badge-secondary',
                            ];
                            $badgeClass = $statusBadge[$status] ?? 'badge-secondary';
                        @endphp
                        <span class="badge-modern {{ $badgeClass }}">
                            {{ $status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Amount Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.payouts.transaction_amount') }}</h3>
                </div>
                
                <div class="detail-info-item">
                    <div class="detail-info-value">
                        <h2 class="font-weight-bold text-primary" style="font-size: 2rem; margin: 0;">
                            {{ $data['currency'] ?? 'IDR' }}
                            {{ number_format($data['amount'] ?? 0, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Basic Information Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">info</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.payouts.basic_information') }}</h3>
                </div>

                <div class="data-table-wrapper">
                    <table class="table-details">
                        <tbody>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.payouts.created_date') }}</td>
                                <td class="value-col">: {{ \Carbon\Carbon::parse($data['created'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }} (GMT +7)</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.payouts.external_id') }}</td>
                                <td class="value-col">: {{ $data['reference_id'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.payouts.withdrawal_id') }}</td>
                                <td class="value-col">: {{ $data['id'] ?? '-' }}</td>
                            </tr>
                            @if(!empty($data['estimated_arrival_time']))
                                <tr>
                                    <td class="label-col">{{ __('messages.owner.xen_platform.payouts.estimated_incoming_funds') }}</td>
                                    <td class="value-col">: {{ \Carbon\Carbon::parse($data['estimated_arrival_time'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }} (GMT +7)</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Payment Details Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">credit_card</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.payouts.payment_details') }}</h3>
                </div>

                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 30%">{{ __('messages.owner.xen_platform.payouts.fields') }}</th>
                                <th>{{ __('messages.owner.xen_platform.payouts.values') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-row">
                                <td class="font-weight-bold">{{ __('messages.owner.xen_platform.payouts.account_holder_name') }}</td>
                                <td>{{ $data['channel_properties']['account_holder_name'] ?? '-' }}</td>
                            </tr>
                            <tr class="table-row">
                                <td class="font-weight-bold">{{ __('messages.owner.xen_platform.payouts.bank_code') }}</td>
                                <td>
                                    <span class="badge-modern badge-primary">
                                        {{ $data['channel_code'] ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="table-row">
                                <td class="font-weight-bold">{{ __('messages.owner.xen_platform.payouts.bank_account_number') }}</td>
                                <td>
                                    <code class="font-weight-bold">
                                        {{ $data['channel_properties']['account_number'] ?? '-' }}
                                    </code>
                                </td>
                            </tr>
                            <tr class="table-row">
                                <td class="font-weight-bold">{{ __('messages.owner.xen_platform.payouts.description') }}</td>
                                <td>{{ $data['description'] ?? '-' }}</td>
                            </tr>
                            <tr class="table-row">
                                <td class="font-weight-bold">{{ __('messages.owner.xen_platform.payouts.bank_reference') }}</td>
                                <td>
                                    @if(!empty($data['connector_reference']))
                                        <code>{{ $data['connector_reference'] }}</code>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-row">
                                <td class="font-weight-bold">{{ __('messages.owner.xen_platform.payouts.email_notification') }}</td>
                                <td>
                                    @if(!empty($data['receipt_notification']['email_to'][0]))
                                        {{ $data['receipt_notification']['email_to'][0] }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @if(!empty($data['failure_code']))
                                <tr class="table-row" style="background-color: #f8d7da;">
                                    <td class="font-weight-bold text-danger">{{ __('messages.owner.xen_platform.payouts.failure_code') }}</td>
                                    <td class="text-danger font-weight-bold">
                                        {{ $data['failure_code'] }}
                                    </td>
                                </tr>
                            @endif
                            @if(!empty($data['failure_message']))
                                <tr class="table-row" style="background-color: #f8d7da;">
                                    <td class="font-weight-bold text-danger">{{ __('messages.owner.xen_platform.payouts.failure_message') }}</td>
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

    {{-- Sidebar --}}
    <div class="col-lg-4">
        
        {{-- Payout Status Timeline Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">history</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.payouts.withdrawal_status_timeline') }}</h3>
                </div>

                @php
                    $statusClasses = [
                        'ACCEPTED' => 'badge-info',
                        'REQUESTED' => 'badge-warning',
                        'SUCCEEDED' => 'badge-success',
                        'FAILED' => 'badge-danger',
                        'CANCELLED' => 'badge-secondary',
                        'REVERSED' => 'badge-secondary',
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
                        'ACCEPTED' => __('messages.owner.xen_platform.payouts.if_accepted'),
                        'REQUESTED' => __('messages.owner.xen_platform.payouts.if_requested'),
                        'SUCCEEDED' => __('messages.owner.xen_platform.payouts.if_succeeded'),
                        'FAILED' => __('messages.owner.xen_platform.payouts.if_failed'),
                        'CANCELLED' => __('messages.owner.xen_platform.payouts.if_cancelled'),
                        'REVERSED' => __('messages.owner.xen_platform.payouts.if_reversed'),
                    ];

                    $history = [];

                    // CREATED Event
                    if (!empty($data['created'])) {
                        $history[] = [
                            'status' => 'CREATED',
                            'timestamp' => \Carbon\Carbon::parse($data['created'])->setTimezone('Asia/Jakarta'),
                            'details' => __('messages.owner.xen_platform.payouts.withdrawal_created'),
                            'sort_key' => $data['created'],
                            'icon' => 'fas fa-plus-circle',
                            'class' => 'badge-primary',
                        ];
                    }

                    // Current Status Event
                    if (!empty($data['status'])) {
                        $currentStatus = $data['status'];
                        $statusTime = ($currentStatus === 'SUCCEEDED' && !empty($data['estimated_arrival_time']))
                            ? $data['estimated_arrival_time']
                            : ($data['updated'] ?? $data['created'] ?? null);

                        if (!empty($statusTime)) {
                            $history[] = [
                                'status' => $currentStatus,
                                'timestamp' => \Carbon\Carbon::parse($statusTime)->setTimezone('Asia/Jakarta'),
                                'details' => $statusDescriptions[$currentStatus] ?? 'Current payout status.',
                                'sort_key' => $statusTime,
                                'icon' => $statusIcons[$currentStatus] ?? 'fas fa-info-circle',
                                'class' => $statusClasses[$currentStatus] ?? 'badge-info',
                            ];
                        }
                    }

                    $eventHistory = collect($history)->sortByDesc('sort_key')->values()->all();
                @endphp

                @if(count($eventHistory) > 0)
                    <div class="timeline">
                        @foreach($eventHistory as $event)
                            @php
                                $time = $event['timestamp']->format('d M Y, H:i');
                            @endphp

                            <div class="timeline-item">
                                <div class="timeline-icon {{ $event['class'] }}">
                                    <i class="{{ $event['icon'] }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <span class="time">
                                        <i class="fas fa-clock me-1"></i> {{ $time }}
                                    </span>
                                    <h3 class="timeline-header">
                                        <span class="badge-modern badge-sm badge-pill-custom {{ $event['class'] }}">
                                            {{ $event['status'] }}
                                        </span>
                                        
                                        <span class="timeline-details">
                                            {{ $event['details'] }}
                                        </span>
                                    </h3>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e9ecef;">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ __('messages.owner.xen_platform.payouts.all_times') }} GMT +7
                        </small>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">{{ __('messages.owner.xen_platform.payouts.no_withdrawal_history') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">flash_on</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.payouts.quick_actions') }}</h3>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-outline-secondary" onclick="printPayoutReceipt()">
                        <i class="fas fa-print me-2"></i> {{ __('messages.owner.xen_platform.payouts.print_receipt') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Payout Information Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">info</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.payouts.withdrawal_information') }}</h3>
                </div>

                <div class="data-table-wrapper">
                    <table class="table-details">
                        <tbody>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.payouts.payout_id') }}</td>
                                <td class="value-col">: {{ $data['id'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.payouts.channel') }}</td>
                                <td class="value-col">: {{ $data['channel_code'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.payouts.currency') }}</td>
                                <td class="value-col">: {{ $data['currency'] ?? 'IDR' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.payouts.amount') }}</td>
                                <td class="value-col">: {{ number_format($data['amount'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @if(!empty($data['estimated_arrival_time']))
                                <tr>
                                    <td class="label-col">ETA</td>
                                    <td class="value-col">: {{ \Carbon\Carbon::parse($data['estimated_arrival_time'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printPayoutReceipt() {
        window.print();
    }
</script>