<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Invoice Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-invoice mr-2"></i>
                            Invoice Details
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
                        <!-- Header Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Invoice ID</h6>
                                <h4 class="text-primary font-weight-bold">{{ $invoice['id'] ?? 'N/A' }}</h4>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <h6 class="text-muted">Status</h6>
                                @php
                                    $status = $invoice['status'] ?? 'UNKNOWN';
                                    $statusData = [
                                        'PAID' => ['class' => 'badge-success', 'icon' => 'fas fa-check-circle'],
                                        'PENDING' => ['class' => 'badge-warning', 'icon' => 'fas fa-clock'],
                                        'EXPIRED' => ['class' => 'badge-danger', 'icon' => 'fas fa-times-circle'],
                                        'SETTLED' => ['class' => 'badge-info', 'icon' => 'fas fa-dollar-sign'],
                                        'UNKNOWN' => ['class' => 'badge-secondary', 'icon' => 'fas fa-question-circle'],
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
                                <h6 class="text-muted">Invoice Amount</h6>
                                <h2 class="text-success font-weight-bold">
                                    {{ $invoice['currency'] ?? 'IDR' }}
                                    {{ number_format($invoice['amount'] ?? 0, 0, ',', '.') }}
                                </h2>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="font-weight-bold mb-3 border-bottom pb-2">
                                    <i class="fas fa-user mr-2"></i>
                                    Customer Information
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="font-weight-bold" style="width: 120px;">Name</td>
                                                <td>: {{ $invoice['customer']['given_names'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Email</td>
                                                <td>: {{ $invoice['customer']['email'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Mobile</td>
                                                <td>: {{ $invoice['customer']['mobile_number'] ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="font-weight-bold" style="width: 120px;">External ID</td>
                                                <td>: {{ $invoice['external_id'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">User ID</td>
                                                <td>: {{ $invoice['user_id'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Store Branch</td>
                                                <td>: {{ $invoice['metadata']['store_branch'] ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="font-weight-bold mb-3 border-bottom pb-2">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Items Details
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-light">
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Category</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-right">Price</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($invoice['items'] as $item)
                                            <tr>
                                                <td>{{ $item['name'] ?? '-' }}</td>
                                                <td>{{ $item['category'] ?? '-' }}</td>
                                                <td class="text-center">{{ $item['quantity'] ?? 0 }}</td>
                                                <td class="text-right">{{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-right">{{ number_format(($item['quantity'] ?? 0) * ($item['price'] ?? 0), 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-secondary font-weight-bold">
                                            <td colspan="4" class="text-right">Total Amount</td>
                                            <td class="text-right">{{ number_format($invoice['amount'] ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Details -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="font-weight-bold mb-3 border-bottom pb-2">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Invoice Details
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="font-weight-bold" style="width: 200px;">Description</td>
                                            <td>: {{ $invoice['description'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Merchant</td>
                                            <td>: {{ $invoice['merchant_name'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Credit Card Excluded</td>
                                            <td>:
                                                <span class="badge {{ $invoice['should_exclude_credit_card'] ? 'badge-warning' : 'badge-success' }}">
                                                    {{ $invoice['should_exclude_credit_card'] ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Email Notification</td>
                                            <td>:
                                                <span class="badge {{ $invoice['should_send_email'] ? 'badge-success' : 'badge-secondary' }}">
                                                    {{ $invoice['should_send_email'] ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Customer Notification</td>
                                            <td>:
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
                                            <td class="font-weight-bold">Invoice URL</td>
                                            <td>:
                                                <a href="{{ $invoice['invoice_url'] ?? '#' }}" target="_blank" class="text-primary">
                                                    {{ Str::limit($invoice['invoice_url'] ?? '-', 50) }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Success Redirect URL</td>
                                            <td>:
                                                <a href="{{ $invoice['success_redirect_url'] ?? '#' }}" target="_blank" class="text-primary">
                                                    {{ Str::limit($invoice['success_redirect_url'] ?? '-', 50) }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Failure Redirect URL</td>
                                            <td>:
                                                <a href="{{ $invoice['failure_redirect_url'] ?? '#' }}" target="_blank" class="text-primary">
                                                    {{ Str::limit($invoice['failure_redirect_url'] ?? '-', 50) }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Created Date</td>
                                            <td>:
                                                @if ($invoice['created'] ?? null)
                                                    {{ \Carbon\Carbon::parse($invoice['created'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} (GMT +7)
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Updated Date</td>
                                            <td>:
                                                @if ($invoice['updated'] ?? null)
                                                    {{ \Carbon\Carbon::parse($invoice['updated'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} (GMT +7)
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Expiry Date</td>
                                            <td>:
                                                @if ($invoice['expiry_date'] ?? null)
                                                    {{ \Carbon\Carbon::parse($invoice['expiry_date'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} (GMT +7)
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
                <!-- Payment Methods -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-credit-card mr-2"></i>
                            Payment Methods
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Virtual Account Banks -->
                        @if(!empty($invoice['available_banks']))
                            <div class="p-3 border-bottom">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-university mr-1"></i>
                                    Virtual Account Banks
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0">
                                        <thead>
                                        <tr class="bg-light">
                                            <th class="small font-weight-bold">Bank</th>
                                            <th class="small font-weight-bold text-right">Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($invoice['available_banks'] as $bank)
                                            <tr>
                                                <td class="small">
                                                    <div class="font-weight-bold">{{ $bank['bank_code'] ?? '-' }}</div>
                                                    <div class="text-muted">{{ $bank['account_holder_name'] ?? '-' }}</div>
                                                </td>
                                                <td class="text-right">
                                                    <div class="font-weight-bold">{{ number_format($bank['transfer_amount'] ?? 0, 0, ',', '.') }}</div>
                                                    <span class="badge badge-success badge-sm">{{ $bank['collection_type'] ?? '-' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- E-Wallets -->
                        @if(!empty($invoice['available_ewallets']))
                            <div class="p-3 border-bottom">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-mobile-alt mr-1"></i>
                                    E-Wallets
                                </h6>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($invoice['available_ewallets'] as $ewallet)
                                        <span class="badge badge-light border">{{ $ewallet['ewallet_type'] ?? '-' }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- QR Codes -->
                        @if(!empty($invoice['available_qr_codes']))
                            <div class="p-3 border-bottom">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-qrcode mr-1"></i>
                                    QR Codes
                                </h6>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($invoice['available_qr_codes'] as $qr)
                                        <span class="badge badge-light border">{{ $qr['qr_code_type'] ?? '-' }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Direct Debits -->
                        @if(!empty($invoice['available_direct_debits']))
                            <div class="p-3 border-bottom">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-exchange-alt mr-1"></i>
                                    Direct Debits
                                </h6>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($invoice['available_direct_debits'] as $debit)
                                        <span class="badge badge-light border">{{ $debit['direct_debit_type'] ?? '-' }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Paylater -->
                        @if(!empty($invoice['available_paylaters']))
                            <div class="p-3">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-hand-holding-usd mr-1"></i>
                                    Paylater
                                </h6>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($invoice['available_paylaters'] as $paylater)
                                        <span class="badge badge-light border">{{ $paylater['paylater_type'] ?? '-' }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Event History & Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-2"></i>
                            Event History & Actions
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Event History -->
                        <div class="mb-4">
                            <h6 class="font-weight-bold mb-3">Event History</h6>
                            @php
                                $history = [];
                                $statusClasses = [
                                    'PAID' => 'bg-success',
                                    'PENDING' => 'bg-warning',
                                    'EXPIRED' => 'bg-danger',
                                    'SETTLED' => 'bg-info',
                                    'CREATED' => 'bg-primary',
                                    'UNKNOWN' => 'bg-secondary',
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
                                        'details' => 'Invoice dibuat dan tersedia untuk pembayaran.',
                                        'sort_key' => $invoice['created'],
                                    ];
                                }

                                if (!empty($invoice['updated']) && ($invoice['status'] !== 'PENDING')) {
                                    $history[] = [
                                        'status' => $invoice['status'],
                                        'timestamp' => \Carbon\Carbon::parse($invoice['updated'])->setTimezone('Asia/Jakarta'),
                                        'details' => 'Status akhir invoice.',
                                        'sort_key' => $invoice['updated'],
                                    ];
                                }

                                if (!empty($invoice['expiry_date'])) {
                                    $expiryTime = \Carbon\Carbon::parse($invoice['expiry_date'])->setTimezone('Asia/Jakarta');
                                    if ($expiryTime->isPast() && $invoice['status'] === 'EXPIRED') {
                                        $history[] = [
                                            'status' => 'EXPIRED',
                                            'timestamp' => $expiryTime,
                                            'details' => 'Invoice telah kedaluwarsa.',
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

                                        <div>
                                            <i class="{{ $icon }} {{ $class }}"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="fas fa-clock mr-1"></i>{{ $time }}</span>
                                                <h3 class="timeline-header">
                                                    <span class="badge {{ $class }} mr-2">{{ $status }}</span>
                                                    {{ $event['details'] }}
                                                </h3>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div><i class="fas fa-clock bg-gray"></i></div>
                                </div>
                            @else
                                <div class="text-center py-3 text-muted">
                                    <i class="fas fa-history fa-2x mb-2"></i>
                                    <p class="mb-0">Tidak ada riwayat event</p>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="mb-4">
                            <h6 class="font-weight-bold mb-3">Actions</h6>
                            <div class="d-grid gap-2">
                                <a href="{{ $invoice['invoice_url'] ?? '#' }}" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i> View Invoice
                                </a>
{{--                                @if($invoice['status'] === 'PENDING')--}}
{{--                                    <button class="btn btn-outline-warning btn-sm">--}}
{{--                                        <i class="fas fa-envelope mr-1"></i> Send Reminder--}}
{{--                                    </button>--}}
{{--                                    <button class="btn btn-outline-danger btn-sm">--}}
{{--                                        <i class="fas fa-times-circle mr-1"></i> Cancel Invoice--}}
{{--                                    </button>--}}
{{--                                @elseif($invoice['status'] === 'EXPIRED')--}}
{{--                                    <button class="btn btn-outline-info btn-sm">--}}
{{--                                        <i class="fas fa-redo mr-1"></i> Recreate Invoice--}}
{{--                                    </button>--}}
{{--                                @endif--}}
                            </div>
                        </div>

                        <!-- Merchant Profile -->
                        <div>
                            <h6 class="font-weight-bold mb-3">Merchant Profile</h6>
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                @if($invoice['merchant_profile_picture_url'] ?? false)
                                    <img src="{{ $invoice['merchant_profile_picture_url'] }}"
                                         alt="Merchant Logo"
                                         class="rounded mr-3"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-white rounded d-flex align-items-center justify-content-center mr-3"
                                         style="width: 50px; height: 50px; border: 1px solid #dee2e6;">
                                        <i class="fas fa-store text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0 font-weight-bold">{{ $invoice['merchant_name'] ?? 'Unknown Merchant' }}</h6>
                                    <small class="text-muted">Merchant Account</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>