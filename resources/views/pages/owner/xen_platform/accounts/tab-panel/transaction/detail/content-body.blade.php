 <div class="row">
    {{-- Main Content --}}
    <div class="col-lg-8">
        
        {{-- Transaction ID & Status Card --}}
        <div class="modern-card">
            <div class="detail-hero-header">

                {{-- Transaction Info --}}
                <div class="detail-hero-info">
                    <h3 class="detail-hero-name">{{ $transaction['id'] ?? 'N/A' }}</h3>
                    <div class="detail-hero-badges">
                        @php
                            $status = $transaction['status'] ?? 'UNKNOWN';
                            $statusBadge = [
                                'SUCCESS' => 'badge-success',
                                'PENDING' => 'badge-warning',
                                'FAILED' => 'badge-danger',
                                'VOIDED' => 'badge-secondary',
                                'UNKNOWN' => 'badge-info',
                            ];
                            $badgeClass = $statusBadge[$status] ?? 'badge-info';
                        @endphp
                        <span class="badge-modern {{ $badgeClass }}">
                            {{ $status }}
                        </span>
                        
                        <span class="badge-modern badge-{{ ($transaction['cashflow'] ?? '') === 'MONEY_IN' ? 'success' : 'danger' }}">
                            {{ ($transaction['cashflow'] ?? '') === 'MONEY_IN' ? 'MONEY IN' : 'MONEY OUT' }}
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
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.transaction_amount') }}</h3>
                </div>
                
                <div class="detail-info-item">
                    <div class="detail-info-value">
                        <h2 class="font-weight-bold text-{{ ($transaction['cashflow'] ?? '') === 'MONEY_IN' ? 'success' : 'danger' }} mb-0">
                            {{ $transaction['currency'] ?? 'IDR' }}
                            {{ number_format($transaction['amount'] ?? 0, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

{{-- Transaction Details Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">info</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.transaction_details') }}</h3>
                </div>

                <div class="data-table-wrapper">
                    <table class="table-details">
                        <tbody>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.type') }}</td>
                                <td class="value-col">: {{ $transaction['type'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.channel_category') }}</td>
                                <td class="value-col">: {{ $transaction['channel_category'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.channel_name') }}</td>
                                <td class="value-col">: {{ $transaction['channel_code'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.account_number') }}</td>
                                <td class="value-col">: {{ $transaction['account_identifier'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.reference') }}</td>
                                <td class="value-col">: {{ $transaction['reference_id'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.payment_request_id') }}</td>
                                <td class="value-col">: {{ $transaction['product_data']['payment_request_id'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.product_id') }}</td>
                                <td class="value-col">: {{ $transaction['product_id'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.capture_id') }}</td>
                                <td class="value-col">: {{ $transaction['product_data']['capture_id'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.channel_reference') }}</td>
                                <td class="value-col">: -</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.settlement_status') }}</td>
                                <td class="value-col">: 
                                    @php
                                        $settlementStatus = $transaction['settlement_status'] ?? '-';
                                        $settlementBadge = 'badge-secondary';
                                        if($settlementStatus === 'SETTLED') {
                                            $settlementBadge = 'badge-success';
                                        } elseif($settlementStatus === 'PENDING') {
                                            $settlementBadge = 'badge-warning';
                                        }
                                    @endphp
                                    <span class="badge-modern {{ $settlementBadge }}">
                                        {{ $settlementStatus }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Fee Details Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">receipt</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.fee_details') }}</h3>
                </div>

                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('messages.owner.xen_platform.accounts.description') }}</th>
                                <th class="text-end">{{ __('messages.owner.xen_platform.accounts.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-row">
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>{{ __('messages.owner.xen_platform.accounts.transaction_fee_status') }}</span>
                                        @php
                                            $feeStatus = strtoupper($transaction['fee']['status'] ?? 'N/A');
                                            $feeStatusClass = 'badge-info';

                                            switch ($feeStatus) {
                                                case 'COMPLETED':
                                                    $feeStatusClass = 'badge-success';
                                                    break;
                                                case 'PENDING':
                                                    $feeStatusClass = 'badge-warning';
                                                    break;
                                                case 'REVERSED':
                                                    $feeStatusClass = 'badge-primary';
                                                    break;
                                                case 'CANCELED':
                                                    $feeStatusClass = 'badge-danger';
                                                    break;
                                                case 'NOT_APPLICABLE':
                                                default:
                                                    $feeStatusClass = 'badge-secondary';
                                                    break;
                                            }
                                        @endphp

                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="badge-modern {{ $feeStatusClass }}">
                                        {{ str_replace('_', ' ', $feeStatus) }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="table-row">
                                <td>{{ __('messages.owner.xen_platform.accounts.transaction_fee') }}</td>
                                <td class="text-end">
                                    {{ $transaction['currency'] ?? 'IDR' }}
                                    {{ number_format($transaction['fee']['xendit_fee'] ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="table-row">
                                <td>{{ __('messages.owner.xen_platform.accounts.transaction_vat') }}</td>
                                <td class="text-end">
                                    {{ $transaction['currency'] ?? 'IDR' }}
                                    {{ number_format($transaction['fee']['value_added_tax'] ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="table-row" style="background-color: #f8f9fa; font-weight: 600;">
                                <td>{{ __('messages.owner.xen_platform.accounts.net_amount') }}</td>
                                <td class="text-end">
                                    {{ $transaction['net_amount_currency'] ?? 'IDR' }}
                                    {{ number_format($transaction['net_amount'] ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Timeline Information Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">schedule</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.timeline_information') }}</h3>
                </div>

                <div class="data-table-wrapper">
                    <table class="table-details">
                        <tbody>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.estimated_settlement_date') }}</td>
                                <td class="value-col">: 
                                    @if ($transaction['estimated_settlement_time'] ?? null)
                                        {{ \Carbon\Carbon::parse($transaction['estimated_settlement_time'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} (GMT +7)
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.date_settled') }}</td>
                                <td class="value-col">: 
                                    @if ($transaction['updated'] ?? null)
                                        {{ \Carbon\Carbon::parse($transaction['updated'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} (GMT +7)
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Event History Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">history</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.event_history') }}</h3>
                </div>

                @php
                    $history = [];
                    $statusClasses = [
                        'SUCCESS' => 'badge-success',
                        'SETTLED' => 'badge-success',
                        'PENDING' => 'badge-warning',
                        'FAILED' => 'badge-danger',
                        'UNKNOWN' => 'badge-info',
                        'CREATED' => 'badge-primary',
                        'EST. SETTLEMENT' => 'badge-secondary',
                    ];

                    $statusIcons = [
                        'SUCCESS' => 'fas fa-check',
                        'SETTLED' => 'fas fa-check-circle',
                        'PENDING' => 'fas fa-clock',
                        'FAILED' => 'fas fa-times',
                        'UNKNOWN' => 'fas fa-question',
                        'CREATED' => 'fas fa-plus-circle',
                        'EST. SETTLEMENT' => 'fas fa-calendar-alt',
                    ];

                    // Event Created
                    if (!empty($transaction['created'])) {
                        $history[] = [
                            'status' => 'CREATED',
                            'timestamp' => \Carbon\Carbon::parse($transaction['created'])->setTimezone('Asia/Jakarta'),
                            'details' => __('messages.owner.xen_platform.accounts.event_created'),
                            'sort_key' => $transaction['created'],
                        ];
                    }

                    // Event Updated/Status Change
                    if (!empty($transaction['updated']) && ($transaction['status'] !== 'PENDING')) {
                        $history[] = [
                            'status' => $transaction['status'],
                            'timestamp' => \Carbon\Carbon::parse($transaction['updated'])->setTimezone('Asia/Jakarta'),
                            'details' => __('messages.owner.xen_platform.accounts.event_status'),
                            'sort_key' => $transaction['updated'],
                        ];
                    }

                    // Settlement Events
                    if (!empty($transaction['estimated_settlement_time'])) {
                        if ($transaction['settlement_status'] === 'SETTLED') {
                            $settledTime = $transaction['updated'] ?? $transaction['estimated_settlement_time'];
                            $history[] = [
                                'status' => 'SETTLED',
                                'timestamp' => \Carbon\Carbon::parse($settledTime)->setTimezone('Asia/Jakarta'),
                                'details' => __('messages.owner.xen_platform.accounts.event_settled'),
                                'sort_key' => $settledTime,
                            ];
                        } else {
                            $history[] = [
                                'status' => 'EST. SETTLEMENT',
                                'timestamp' => \Carbon\Carbon::parse($transaction['estimated_settlement_time'])->setTimezone('Asia/Jakarta'),
                                'details' => __('messages.owner.xen_platform.accounts.event_est'),
                                'sort_key' => $transaction['estimated_settlement_time'],
                            ];
                        }
                    }

                    // Sort events by timestamp
                    $eventHistory = collect($history)->sortByDesc('sort_key')->unique('status')->values()->all();
                @endphp

                @if(count($eventHistory) > 0)
                    <div class="timeline">
                        @foreach($eventHistory as $event)
                            @php
                                $status = strtoupper($event['status']);
                                $class = $statusClasses[$status] ?? $statusClasses['UNKNOWN'];
                                $icon = $statusIcons[$status] ?? $statusIcons['UNKNOWN'];
                                $time = $event['timestamp']->format('d M Y, H:i');
                            @endphp

                            <div class="timeline-item">
                                <div class="timeline-icon {{ $class }}">
                                    <i class="{{ $icon }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <span class="time">
                                        <i class="fas fa-clock me-1"></i> {{ $time }}
                                    </span>
                                    <h3 class="timeline-header">
                                        <span class="badge-modern badge-sm badge-pill-custom {{ $class }}">
                                            {{ $status }}
                                        </span>
                                        
                                        <span class="timeline-details">
                                            {{ $event['details'] }}
                                        </span>
                                    </h3>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ __('messages.owner.xen_platform.accounts.show') }} {{ count($eventHistory) }} {{ __('messages.owner.xen_platform.accounts.events') }}
                        </small>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">{{ __('messages.owner.xen_platform.accounts.no_history') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>