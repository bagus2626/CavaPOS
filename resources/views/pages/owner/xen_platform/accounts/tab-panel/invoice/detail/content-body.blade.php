<div class="row">
    {{-- Main Content --}}
    <div class="col-lg-8">
        
        {{-- Invoice ID & Status Card --}}
        <div class="modern-card">
            <div class="detail-hero-header">
                {{-- Invoice Info --}}
                <div class="detail-hero-info">
                    <h3 class="detail-hero-name">{{ $invoice['id'] ?? 'N/A' }}</h3>
                    <div class="detail-hero-badges">
                        @php
                            $status = $invoice['status'] ?? 'UNKNOWN';
                            $statusBadge = [
                                'PAID' => 'badge-success',
                                'PENDING' => 'badge-warning',
                                'EXPIRED' => 'badge-danger',
                                'SETTLED' => 'badge-info',
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
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.invoice_amount') }}</h3>
                </div>
                
                <div class="detail-info-item">
                    <div class="detail-info-value">
                        <h2 class="font-weight-bold text-success mb-0">
                            {{ $invoice['currency'] ?? 'IDR' }}
                            {{ number_format($invoice['amount'] ?? 0, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Information Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.customer_information') }}</h3>
                </div>

                <div class="data-table-wrapper">
                    <table class="table-details">
                        <tbody>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.name') }}</td>
                                <td class="value-col">: {{ $invoice['customer']['given_names'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.email') }}</td>
                                <td class="value-col">: {{ $invoice['customer']['email'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.mobile_number') }}</td>
                                <td class="value-col">: {{ $invoice['customer']['mobile_number'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.external_id') }}</td>
                                <td class="value-col">: {{ $invoice['external_id'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.user_id') }}</td>
                                <td class="value-col">: {{ $invoice['user_id'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.store_branch') }}</td>
                                <td class="value-col">: {{ $invoice['metadata']['store_branch'] ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Items Details Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">shopping_cart</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.item_details') }}</h3>
                </div>

                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('messages.owner.xen_platform.accounts.item_name') }}</th>
                                <th>{{ __('messages.owner.xen_platform.accounts.category') }}</th>
                                <th class="text-center">{{ __('messages.owner.xen_platform.accounts.quantity') }}</th>
                                <th class="text-end">{{ __('messages.owner.xen_platform.accounts.price') }}</th>
                                <th class="text-end">{{ __('messages.owner.xen_platform.accounts.subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice['items'] as $item)
                                <tr class="table-row">
                                    <td>{{ $item['name'] ?? '-' }}</td>
                                    <td>{{ $item['category'] ?? '-' }}</td>
                                    <td class="text-center">{{ $item['quantity'] ?? 0 }}</td>
                                    <td class="text-end">{{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format(($item['quantity'] ?? 0) * ($item['price'] ?? 0), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="table-row" style="background-color: #f8f9fa; font-weight: 600;">
                                <td colspan="4">{{ __('messages.owner.xen_platform.accounts.total_amount') }}</td>
                                <td class="text-end">{{ number_format($invoice['amount'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Invoice Details Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">info</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.invoice_details') }}</h3>
                </div>

                <div class="data-table-wrapper">
                    <table class="table-details">
                        <tbody>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.description') }}</td>
                                <td class="value-col">: {{ $invoice['description'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.business') }}</td>
                                <td class="value-col">: {{ $invoice['merchant_name'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.credit_card_excluded') }}</td>
                                <td class="value-col">: 
                                    <span class="badge-modern {{ $invoice['should_exclude_credit_card'] ? 'badge-warning' : 'badge-success' }}">
                                        {{ $invoice['should_exclude_credit_card'] ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.email_notification') }}</td>
                                <td class="value-col">: 
                                    <span class="badge-modern {{ $invoice['should_send_email'] ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $invoice['should_send_email'] ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.customer_notification') }}</td>
                                <td class="value-col">: 
                                    @php
                                        $preferences = $invoice['customer_notification_preference'] ?? [];
                                        $methods = [];
                                        foreach($preferences as $type => $channels) {
                                            $methods = array_merge($methods, $channels);
                                        }
                                        $methods = array_unique($methods);
                                        echo implode(', ', $methods) ?: 'None';
                                    @endphp
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.invoice_url') }}</td>
                                <td class="value-col">: 
                                    <a href="{{ $invoice['invoice_url'] ?? '#' }}" target="_blank" class="text-primary">
                                        {{ Str::limit($invoice['invoice_url'] ?? '-', 50) }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.success_redirect_url') }}</td>
                                <td class="value-col">: 
                                    <a href="{{ $invoice['success_redirect_url'] ?? '#' }}" target="_blank" class="text-primary">
                                        {{ Str::limit($invoice['success_redirect_url'] ?? '-', 50) }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.failure_redirect_url') }}</td>
                                <td class="value-col">: 
                                    <a href="{{ $invoice['failure_redirect_url'] ?? '#' }}" target="_blank" class="text-primary">
                                        {{ Str::limit($invoice['failure_redirect_url'] ?? '-', 50) }}
                                    </a>
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
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.created_date') }}</td>
                                <td class="value-col">: 
                                    @if ($invoice['created'] ?? null)
                                        {{ \Carbon\Carbon::parse($invoice['created'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} (GMT +7)
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.updated_date') }}</td>
                                <td class="value-col">: 
                                    @if ($invoice['updated'] ?? null)
                                        {{ \Carbon\Carbon::parse($invoice['updated'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} (GMT +7)
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.expiry_date') }}</td>
                                <td class="value-col">: 
                                    @if ($invoice['expiry_date'] ?? null)
                                        {{ \Carbon\Carbon::parse($invoice['expiry_date'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} (GMT +7)
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
        
        {{-- Payment Methods Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">credit_card</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.payment_methods') }}</h3>
                </div>

                {{-- Virtual Account Banks --}}
                @if(!empty($invoice['available_banks']))
                    <div class="mb-4">
                        <div class="data-table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.owner.xen_platform.accounts.bank') }}</th>
                                        <th class="text-end">{{ __('messages.owner.xen_platform.accounts.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice['available_banks'] as $bank)
                                        <tr class="table-row">
                                            <td>
                                                <div class="font-weight-bold" >{{ $bank['bank_code'] ?? '-' }}</div>
                                                <div class="text-muted">{{ $bank['account_holder_name'] ?? '-' }}</div>
                                            </td>
                                            <td class="text-end">
                                                <div class="font-weight-bold">{{ number_format($bank['transfer_amount'] ?? 0, 0, ',', '.') }}</div>
                                                <span class="badge-modern badge-sm badge-success">{{ $bank['collection_type'] ?? '-' }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- E-Wallets --}}
                @if(!empty($invoice['available_ewallets']))
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fas fa-mobile-alt mr-2"></i>
                            {{ __('messages.owner.xen_platform.accounts.e_wallets') }}
                        </h6>
                        <div class="d-flex flex-wrap ">
                            @foreach($invoice['available_ewallets'] as $ewallet)
                                <span class="badge-modern badge-sm badge-secondary mr-2 mb-2">{{ $ewallet['ewallet_type'] ?? '-' }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- QR Codes --}}
                @if(!empty($invoice['available_qr_codes']))
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-primary mb-3" >
                            <i class="fas fa-qrcode mr-2"></i>
                            {{ __('messages.owner.xen_platform.accounts.qr_code') }}
                        </h6>
                        <div class="d-flex flex-wrap">
                            @foreach($invoice['available_qr_codes'] as $qr)
                                <span class="badge-modern badge-sm badge-secondary mr-2 mb-2">{{ $qr['qr_code_type'] ?? '-' }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Direct Debits --}}
                @if(!empty($invoice['available_direct_debits']))
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fas fa-exchange-alt mr-2"></i>
                            {{ __('messages.owner.xen_platform.accounts.direct_debit') }}
                        </h6>
                        <div class="d-flex flex-wrap">
                            @foreach($invoice['available_direct_debits'] as $debit)
                                <span class="badge-modern badge-sm badge-secondary mr-2 mb-2">{{ $debit['direct_debit_type'] ?? '-' }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Paylater --}}
                @if(!empty($invoice['available_paylaters']))
                    <div>
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fas fa-hand-holding-usd mr-2"></i>
                            {{ __('messages.owner.xen_platform.accounts.pay_later') }}
                        </h6>
                        <div class="d-flex flex-wrap">
                            @foreach($invoice['available_paylaters'] as $paylater)
                                <span class="badge-modern badge-sm badge-secondary mr-2 mb-2">{{ $paylater['paylater_type'] ?? '-' }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

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
                        'PAID' => 'badge-success',
                        'PENDING' => 'badge-warning',
                        'EXPIRED' => 'badge-danger',
                        'SETTLED' => 'badge-info',
                        'CREATED' => 'badge-primary',
                        'UNKNOWN' => 'badge-secondary',
                    ];

                    $statusIcons = [
                        'PAID' => 'fas fa-check-circle',
                        'PENDING' => 'fas fa-clock',
                        'EXPIRED' => 'fas fa-calendar-times',
                        'SETTLED' => 'fas fa-money-check',
                        'CREATED' => 'fas fa-plus-circle',
                        'UNKNOWN' => 'fas fa-question-circle',
                    ];

                    if (!empty($invoice['created'])) {
                        $history[] = [
                            'status' => 'CREATED',
                            'timestamp' => \Carbon\Carbon::parse($invoice['created'])->setTimezone('Asia/Jakarta'),
                            'details' => __('messages.owner.xen_platform.accounts.event_created'),
                            'sort_key' => $invoice['created'],
                        ];
                    }

                    if (!empty($invoice['updated']) && ($invoice['status'] !== 'PENDING')) {
                        $history[] = [
                            'status' => $invoice['status'],
                            'timestamp' => \Carbon\Carbon::parse($invoice['updated'])->setTimezone('Asia/Jakarta'),
                            'details' => __('messages.owner.xen_platform.accounts.event_status'),
                            'sort_key' => $invoice['updated'],
                        ];
                    }

                    if (!empty($invoice['expiry_date'])) {
                        $expiryTime = \Carbon\Carbon::parse($invoice['expiry_date'])->setTimezone('Asia/Jakarta');
                        if ($expiryTime->isPast() && $invoice['status'] === 'EXPIRED') {
                            $history[] = [
                                'status' => 'EXPIRED',
                                'timestamp' => $expiryTime,
                                'details' => __('messages.owner.xen_platform.accounts.event_expired'),
                                'sort_key' => $invoice['expiry_date'],
                            ];
                        }
                    }

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

                    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e9ecef;">
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

        {{-- Actions Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">touch_app</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.actions') }}</h3>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ $invoice['invoice_url'] ?? '#' }}" target="_blank" class="btn-modern btn-primary-modern">
                        <i class="fas fa-external-link-alt mr-2"></i> {{ __('messages.owner.xen_platform.accounts.view_invoice') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Business Profile Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">store</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.business_profile') }}</h3>
                </div>

                <div class="d-flex align-items-center p-3 bg-light rounded">
                    @if($invoice['merchant_profile_picture_url'] ?? false)
                        <img src="{{ $invoice['merchant_profile_picture_url'] }}"
                             alt="Merchant Logo"
                             class="rounded me-3"
                             style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                        <div class="bg-white rounded d-flex align-items-center justify-content-center me-3"
                             style="width: 50px; height: 50px; border: 1px solid #dee2e6;">
                            <i class="fas fa-store text-muted"></i>
                        </div>
                    @endif
                    <div>
                        <h6 class="mb-0 font-weight-bold">{{ $invoice['merchant_name'] ?? 'Unknown Merchant' }}</h6>
                        <small class="text-muted">{{ __('messages.owner.xen_platform.accounts.business_account') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>