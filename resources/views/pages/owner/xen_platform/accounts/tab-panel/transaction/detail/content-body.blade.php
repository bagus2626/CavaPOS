<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Transaction Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exchange-alt mr-2"></i>
                            {{ __('messages.owner.xen_platform.accounts.transaction_details') }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary mr-2">
                                <i class="fas fa-arrow-left mr-1"></i> {{ __('messages.owner.xen_platform.accounts.back') }}
                            </a>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Header Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">{{ __('messages.owner.xen_platform.accounts.transaction_id') }}</h6>
                                <h4 class="text-primary font-weight-bold">{{ $transaction['id'] ?? 'N/A' }}</h4>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <h6 class="text-muted">Status</h6>
                                @php
                                    $status = $transaction['status'] ?? 'UNKNOWN';
                                    $statusData = [
                                        'SUCCESS' => ['class' => 'badge-success', 'icon' => 'fas fa-check-circle'],
                                        'PENDING' => ['class' => 'badge-warning', 'icon' => 'fas fa-clock'],
                                        'FAILED' => ['class' => 'badge-danger', 'icon' => 'fas fa-times-circle'],
                                        'VOIDED' => ['class' => 'badge-secondary', 'icon' => 'fas fa-ban'],
                                        'UNKNOWN' => ['class' => 'badge-info', 'icon' => 'fas fa-question-circle'],
                                    ];
                                    $data = $statusData[$status] ?? $statusData['UNKNOWN'];
                                @endphp
                                <span class="badge {{ $data['class'] }} text-lg">
                                    <i class="{{ $data['icon'] }} mr-1"></i>
                                    {{ $status }}
                                </span>
                            </div>
                        </div>

                        <!-- Amount -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted">{{ __('messages.owner.xen_platform.accounts.transaction_amount') }}</h6>
                                <h2 class="font-weight-bold text-{{ ($transaction['cashflow'] ?? '') === 'MONEY_IN' ? 'success' : 'danger' }}">
                                    {{ $transaction['currency'] ?? 'IDR' }}
                                    {{ number_format($transaction['amount'] ?? 0, 0, ',', '.') }}
                                </h2>
                                <span class="badge badge-{{ ($transaction['cashflow'] ?? '') === 'MONEY_IN' ? 'success' : 'danger' }}">
                                    {{ ($transaction['cashflow'] ?? '') === 'MONEY_IN' ? 'MONEY IN' : 'MONEY OUT' }}
                                </span>
                            </div>
                        </div>

                        <!-- Transaction Details -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="font-weight-bold mb-3 border-bottom pb-2">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    {{ __('messages.owner.xen_platform.accounts.transaction_details') }}
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="font-weight-bold" style="width: 200px;">{{ __('messages.owner.xen_platform.accounts.type') }}</td>
                                            <td>: {{ $transaction['type'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">{{ __('messages.owner.xen_platform.accounts.channel_category') }}</td>
                                            <td>: {{ $transaction['channel_category'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">{{ __('messages.owner.xen_platform.accounts.channel_name') }}</td>
                                            <td>: {{ $transaction['channel_code'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">{{ __('messages.owner.xen_platform.accounts.account_number') }}</td>
                                            <td>: {{ $transaction['account_identifier'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">{{ __('messages.owner.xen_platform.accounts.reference') }}</td>
                                            <td>: {{ $transaction['reference_id'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">{{ __('messages.owner.xen_platform.accounts.payment_request_id') }}</td>
                                            <td>: {{ $transaction['product_data']['payment_request_id'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">{{ __('messages.owner.xen_platform.accounts.product_id') }}</td>
                                            <td>: {{ $transaction['product_id'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">{{ __('messages.owner.xen_platform.accounts.capture_id') }}</td>
                                            <td>: {{ $transaction['product_data']['capture_id'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">{{ __('messages.owner.xen_platform.accounts.channel_reference') }}</td>
                                            <td>: -</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">{{ __('messages.owner.xen_platform.accounts.settlement_status') }}</td>
                                            <td>:
                                                <span class="badge
                                                    @if($transaction['settlement_status'] === 'SETTLED') badge-success
                                                    @elseif($transaction['settlement_status'] === 'PENDING') badge-warning
                                                    @else badge-secondary @endif">
                                                    {{ $transaction['settlement_status'] ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Fee Details -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="font-weight-bold mb-3 border-bottom pb-2">
                                    <i class="fas fa-receipt mr-2"></i>
                                    {{ __('messages.owner.xen_platform.accounts.fee_details') }}
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-light">
                                        <tr>
                                            <th>{{ __('messages.owner.xen_platform.accounts.description') }}</th>
                                            <th class="text-right">{{ __('messages.owner.xen_platform.accounts.amount') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
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
                                                    <span class="badge {{ $feeStatusClass }}">
                                                            {{ str_replace('_', ' ', $feeStatus) }}
                                                        </span>
                                                </div>
                                            </td>
                                            <td class="text-right">-</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.owner.xen_platform.accounts.transaction_fee') }}</td>
                                            <td class="text-right">
                                                {{ $transaction['currency'] ?? 'IDR' }}
                                                {{ number_format($transaction['fee']['xendit_fee'] ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.owner.xen_platform.accounts.transaction_vat') }}</td>
                                            <td class="text-right">
                                                {{ $transaction['currency'] ?? 'IDR' }}
                                                {{ number_format($transaction['fee']['value_added_tax'] ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr class="table-secondary font-weight-bold">
                                            <td>{{ __('messages.owner.xen_platform.accounts.net_amount') }}</td>
                                            <td class="text-right">
                                                {{ $transaction['net_amount_currency'] ?? 'IDR' }}
                                                {{ number_format($transaction['net_amount'] ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Information -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="font-weight-bold mb-3 border-bottom pb-2">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    {{ __('messages.owner.xen_platform.accounts.timeline_information') }}
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="font-weight-bold" style="width: 250px;">{{ __('messages.owner.xen_platform.accounts.estimated_settlement_date') }}</td>
                                            <td>:
                                                @if ($transaction['estimated_settlement_time'] ?? null)
                                                    {{ \Carbon\Carbon::parse($transaction['estimated_settlement_time'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} (GMT +7)
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">{{ __('messages.owner.xen_platform.accounts.date_settled') }}</td>
                                            <td>:
                                                @if ($transaction['updated'] ?? null)
                                                    {{ \Carbon\Carbon::parse($transaction['updated'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} (GMT +7)
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Event History -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-2"></i>
                            {{ __('messages.owner.xen_platform.accounts.event_history') }}
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $history = [];
                            $statusClasses = [
                                'SUCCESS' => 'bg-success',
                                'SETTLED' => 'bg-success',
                                'PENDING' => 'bg-warning',
                                'FAILED' => 'bg-danger',
                                'UNKNOWN' => 'bg-info',
                                'CREATED' => 'bg-primary',
                                'EST. SETTLEMENT' => 'bg-secondary',
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

                                    <div>
                                        <i class="{{ $icon }} {{ $class }}"></i>
                                        <div class="timeline-item mt-2">
                                            <span class="time">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $time }}
                                            </span>
                                            <h3 class="timeline-header">
                                                <span class="badge {{ $class }} mr-2">{{ $status }}</span>
                                                {{ $event['details'] }}
                                            </h3>
                                        </div>
                                    </div>
                                @endforeach
                                <div>
                                    <i class="fas fa-clock bg-gray"></i>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">{{ __('messages.owner.xen_platform.accounts.no_history') }}</p>
                            </div>
                        @endif
                    </div>
                    @if(count($eventHistory) > 0)
                        <div class="card-footer">
                            <small class="text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ __('messages.owner.xen_platform.accounts.show') }} {{ count($eventHistory) }} {{ __('messages.owner.xen_platform.accounts.events') }}
                            </small>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
{{--                <div class="card">--}}
{{--                    <div class="card-header">--}}
{{--                        <h3 class="card-title">--}}
{{--                            <i class="fas fa-bolt mr-2"></i>--}}
{{--                            Quick Actions--}}
{{--                        </h3>--}}
{{--                        <div class="card-tools">--}}
{{--                            <button type="button" class="btn btn-tool" data-card-widget="collapse">--}}
{{--                                <i class="fas fa-minus"></i>--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="card-body">--}}
{{--                        <div class="d-grid gap-2">--}}
{{--                            @if($transaction['status'] === 'PENDING')--}}
{{--                                <button class="btn btn-outline-warning btn-sm">--}}
{{--                                    <i class="fas fa-sync-alt mr-1"></i> Refresh Status--}}
{{--                                </button>--}}
{{--                            @endif--}}

{{--                            @if($transaction['settlement_status'] === 'PENDING')--}}
{{--                                <button class="btn btn-outline-info btn-sm">--}}
{{--                                    <i class="fas fa-money-check mr-1"></i> Check Settlement--}}
{{--                                </button>--}}
{{--                            @endif--}}

{{--                            <button class="btn btn-outline-primary btn-sm">--}}
{{--                                <i class="fas fa-download mr-1"></i> Export Details--}}
{{--                            </button>--}}

{{--                            <button class="btn btn-outline-secondary btn-sm">--}}
{{--                                <i class="fas fa-print mr-1"></i> Print Receipt--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>
    </div>
</section>
