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
                                <h6 class="invoice-from">Invoice ID#</h6>
                                <h5 class="text-primary">{{ $invoice['id'] ?? 'N/A' }}</h5>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <h6 class="invoice-from">Invoice Amount</h6>
                                <h2 class="text-bold-700 text-success">
                                    <span class="text-light-secondary me-1">
                                        {{ $invoice['currency'] ?? 'IDR' }}
                                    </span>
                                    {{ number_format($invoice['amount'] ?? 0, 0, ',', '.') }}
                                </h2>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                <div class="d-flex align-items-center">
                                    @php
                                        $status = $invoice['status'] ?? 'UNKNOWN';
                                        $statusData = [
                                            'PAID' => ['class' => 'badge-success', 'icon' => 'bx-check-circle'],
                                            'PENDING' => ['class' => 'badge-warning', 'icon' => 'bx-time'],
                                            'EXPIRED' => ['class' => 'badge-danger', 'icon' => 'bx-x-circle'],
                                            'SETTLED' => ['class' => 'badge-info', 'icon' => 'bx-dollar-circle'],
                                            'UNKNOWN' => ['class' => 'badge-info', 'icon' => 'bx-help-circle'],
                                        ];

                                        $data = $statusData[$status] ?? $statusData['UNKNOWN'];
                                    @endphp

                                    <div class="badge badge-pill {{ $data['class'] }} badge-glow d-inline-flex align-items-center text-uppercase p-1">
                                        <i class="bx {{ $data['icon'] }} font-medium-1 mr-25"></i>
                                        <span class="text-bold-700">{{ $status }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <!-- Customer Information -->
                    <div class="card-body px-md-3">
                        <h5 class="text-bold-500 mb-2">Customer Information</h5>
                        <div class="row mx-md-25">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-semibold">Name</td>
                                        <td>: {{ $invoice['customer']['given_names'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Email</td>
                                        <td>: {{ $invoice['customer']['email'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Mobile</td>
                                        <td>: {{ $invoice['customer']['mobile_number'] ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-semibold">External ID</td>
                                        <td>: {{ $invoice['external_id'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">User ID</td>
                                        <td>: {{ $invoice['user_id'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Store Branch</td>
                                        <td>: {{ $invoice['metadata']['store_branch'] ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <!-- Items Details -->
                    <div class="card-body px-md-3">
                        <h5 class="text-bold-500 mb-2">Items Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($invoice['items'] as $item)
                                    <tr>
                                        <td>{{ $item['name'] ?? '-' }}</td>
                                        <td>{{ $item['category'] ?? '-' }}</td>
                                        <td>{{ $item['quantity'] ?? 0 }}</td>
                                        <td>{{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ number_format(($item['quantity'] ?? 0) * ($item['price'] ?? 0), 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-active">
                                    <td colspan="4" class="text-end text-bold-700">Total Amount</td>
                                    <td class="text-bold-700">{{ number_format($invoice['amount'] ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Invoice details table -->
                    <div class="invoice-product-details table-responsive mx-md-25 px-md-3">
                        <table class="table mb-5">
                            <tbody>
                            <tr>
                                <td class="fw-semibold" style="width: 30%;">Description</td>
                                <td>: {{ $invoice['description'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Merchant</td>
                                <td>: {{ $invoice['merchant_name'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Credit Card Excluded</td>
                                <td>: {{ $invoice['should_exclude_credit_card'] ? 'Yes' : 'No' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Email Notification</td>
                                <td>: {{ $invoice['should_send_email'] ? 'Yes' : 'No' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Customer Notification</td>
                                <td>
                                    :
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
                                <td class="fw-semibold">Invoice URL</td>
                                <td>:
                                    <a href="{{ $invoice['invoice_url'] ?? '#' }}" target="_blank" class="text-primary">
                                        {{ $invoice['invoice_url'] ?? '-' }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Success Redirect URL</td>
                                <td>:
                                    <a href="{{ $invoice['success_redirect_url'] ?? '#' }}" target="_blank" class="text-primary">
                                        {{ $invoice['success_redirect_url'] ?? '-' }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Failure Redirect URL</td>
                                <td>:
                                    <a href="{{ $invoice['failure_redirect_url'] ?? '#' }}" target="_blank" class="text-primary">
                                        {{ $invoice['failure_redirect_url'] ?? '-' }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Created Date (GMT +7)</td>
                                <td>
                                    @if ($invoice['created'] ?? null)
                                        : {{ \Carbon\Carbon::parse($invoice['created'])->setTimezone('Asia/Jakarta')->format('d M Y, h:i A') }}
                                    @else
                                        : -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Updated Date (GMT +7)</td>
                                <td>
                                    @if ($invoice['updated'] ?? null)
                                        : {{ \Carbon\Carbon::parse($invoice['updated'])->setTimezone('Asia/Jakarta')->format('d M Y, h:i A') }}
                                    @else
                                        : -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Expiry Date (GMT +7)</td>
                                <td>
                                    @if ($invoice['expiry_date'] ?? null)
                                        : {{ \Carbon\Carbon::parse($invoice['expiry_date'])->setTimezone('Asia/Jakarta')->format('d M Y, h:i A') }}
                                    @else
                                        : -
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Available Payment Methods -->
{{--                    <div class="card-body mx-md-25 px-md-3">--}}
{{--                        <h5 class="fw-bold mb-3">Available Payment Methods</h5>--}}

{{--                        <!-- Available Banks -->--}}
{{--                        @if(!empty($invoice['available_banks']))--}}
{{--                            <div class="mb-4">--}}
{{--                                <h6 class="text-primary mb-2">Virtual Account Banks</h6>--}}
{{--                                <div class="table-responsive">--}}
{{--                                    <table class="table table-bordered table-sm">--}}
{{--                                        <thead>--}}
{{--                                        <tr>--}}
{{--                                            <th>Bank Code</th>--}}
{{--                                            <th>Collection Type</th>--}}
{{--                                            <th>Transfer Amount</th>--}}
{{--                                            <th>Bank Branch</th>--}}
{{--                                            <th>Account Holder</th>--}}
{{--                                        </tr>--}}
{{--                                        </thead>--}}
{{--                                        <tbody>--}}
{{--                                        @foreach($invoice['available_banks'] as $bank)--}}
{{--                                            <tr>--}}
{{--                                                <td>{{ $bank['bank_code'] ?? '-' }}</td>--}}
{{--                                                <td>{{ $bank['collection_type'] ?? '-' }}</td>--}}
{{--                                                <td>{{ number_format($bank['transfer_amount'] ?? 0, 0, ',', '.') }}</td>--}}
{{--                                                <td>{{ $bank['bank_branch'] ?? '-' }}</td>--}}
{{--                                                <td>{{ $bank['account_holder_name'] ?? '-' }}</td>--}}
{{--                                            </tr>--}}
{{--                                        @endforeach--}}
{{--                                        </tbody>--}}
{{--                                    </table>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        <!-- Available E-Wallets -->--}}
{{--                        @if(!empty($invoice['available_ewallets']))--}}
{{--                            <div class="mb-4">--}}
{{--                                <h6 class="text-primary mb-2">E-Wallets</h6>--}}
{{--                                <div class="d-flex flex-wrap gap-2">--}}
{{--                                    @foreach($invoice['available_ewallets'] as $ewallet)--}}
{{--                                        <span class="badge badge-light-primary">{{ $ewallet['ewallet_type'] ?? '-' }}</span>--}}
{{--                                    @endforeach--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        <!-- Available QR Codes -->--}}
{{--                        @if(!empty($invoice['available_qr_codes']))--}}
{{--                            <div class="mb-4">--}}
{{--                                <h6 class="text-primary mb-2">QR Codes</h6>--}}
{{--                                <div class="d-flex flex-wrap gap-2">--}}
{{--                                    @foreach($invoice['available_qr_codes'] as $qr)--}}
{{--                                        <span class="badge badge-light-success">{{ $qr['qr_code_type'] ?? '-' }}</span>--}}
{{--                                    @endforeach--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        <!-- Available Direct Debits -->--}}
{{--                        @if(!empty($invoice['available_direct_debits']))--}}
{{--                            <div class="mb-4">--}}
{{--                                <h6 class="text-primary mb-2">Direct Debits</h6>--}}
{{--                                <div class="d-flex flex-wrap gap-2">--}}
{{--                                    @foreach($invoice['available_direct_debits'] as $debit)--}}
{{--                                        <span class="badge badge-light-info">{{ $debit['direct_debit_type'] ?? '-' }}</span>--}}
{{--                                    @endforeach--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        <!-- Available Paylaters -->--}}
{{--                        @if(!empty($invoice['available_paylaters']))--}}
{{--                            <div class="mb-4">--}}
{{--                                <h6 class="text-primary mb-2">Paylater</h6>--}}
{{--                                <div class="d-flex flex-wrap gap-2">--}}
{{--                                    @foreach($invoice['available_paylaters'] as $paylater)--}}
{{--                                        <span class="badge badge-light-warning">{{ $paylater['paylater_type'] ?? '-' }}</span>--}}
{{--                                    @endforeach--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endif--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>

        <!-- invoice action  -->
        <div class="col-xl-5 col-md-4 col-12">
            <div class="card border">
                <div class="card-body mx-md-25 px-md-3">
                    <h5 class="text-bold-700 mb-3">Available Payment Methods</h5>

                    <!-- Available Banks -->
                    @if(!empty($invoice['available_banks']))
                        <div class="mb-2">
                            <h6 class="text-secondary mb-2">Virtual Account Banks</h6>
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th class="ps-3">Bank Code</th>
                                        <th>Collection Type</th>
                                        <th>Transfer Amount</th>
                                        <th>Bank Branch</th>
                                        <th>Account Holder</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($invoice['available_banks'] as $bank)
                                        <tr class="border-bottom">
                                            <td class="ps-3 fw-medium">{{ $bank['bank_code'] ?? '-' }}</td>
                                            <td><span class="badge bg-light text-white">{{ $bank['collection_type'] ?? '-' }}</span></td>
                                            <td class="text-bold-700">{{ number_format($bank['transfer_amount'] ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ $bank['bank_branch'] ?? '-' }}</td>
                                            <td class="text-primary">{{ $bank['account_holder_name'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Available E-Wallets -->
                    @if(!empty($invoice['available_ewallets']))
                        <div class="mb-1">
                            <h6 class="text-secondary mb-1">E-Wallets</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($invoice['available_ewallets'] as $ewallet)
                                    <span class="badge badge-light-primary mr-1 mb-1">{{ $ewallet['ewallet_type'] ?? '-' }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Available QR Codes -->
                    @if(!empty($invoice['available_qr_codes']))
                        <div class="mb-1">
                            <h6 class="text-secondary mb-1">QR Codes</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($invoice['available_qr_codes'] as $qr)
                                    <span class="badge badge-light-success mr-1 mb-1">{{ $qr['qr_code_type'] ?? '-' }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Available Direct Debits -->

                    @if(!empty($invoice['available_direct_debits']))
                        <div class="mb-1">
                            <h6 class="text-secondary mb-1">Direct Debits</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($invoice['available_direct_debits'] as $debit)
                                    <span class="badge badge-light-info mr-1 mb-1">{{ $debit['direct_debit_type'] ?? '-' }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Available Paylaters -->
                    @if(!empty($invoice['available_paylaters']))
                        <div class="mb-1">
                            <h6 class="text-secondary mb-1">Paylater</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($invoice['available_paylaters'] as $paylater)
                                    <span class="badge badge-light-warning mr-1 mb-1">{{ $paylater['paylater_type'] ?? '-' }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border">
                <div class="card-body">
                    <h5 class="text-bold-700 mb-1">Event History</h5>

                    @php
                        $history = [];
                        $statusClasses = [
                            'PAID' => 'timeline-icon-success',
                            'PENDING' => 'timeline-icon-warning',
                            'EXPIRED' => 'timeline-icon-danger',
                            'SETTLED' => 'timeline-icon-info',
                            'UNKNOWN' => 'timeline-icon-info',
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

                    <ul class="widget-timeline">
                        @if(count($eventHistory) > 0)
                            @foreach($eventHistory as $event)
                                @php
                                    $status = strtoupper($event['status']);
                                    $statusKey = ($status === 'CREATED') ? 'UNKNOWN' : $invoice['status'];
                                    $class = $statusClasses[$statusKey] ?? $statusClasses['UNKNOWN'];
                                    $time = $event['timestamp']->format('d M Y, H:i A');
                                    $iconClass = ($status === 'PAID' || $status === 'SETTLED') ? 'timeline-icon-success' : $class;
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
                        @else
                            <li class="timeline-items timeline-icon-info">
                                <h6 class="timeline-title text-bold-500 mb-0 text-muted">
                                    No event history available
                                </h6>
                            </li>
                        @endif
                    </ul>

                    <!-- Action Buttons -->
                    <div class="mt-2">
                        <h5 class="fw-bold mb-1">Actions</h5>
                        <div class="d-grid gap-2">
                            <a href="{{ $invoice['invoice_url'] ?? '#' }}" target="_blank" class="btn btn-primary">
                                <i class="bx bx-link-external me-1"></i> View Invoice
                            </a>
                            @if($invoice['status'] === 'PENDING')
                                <button class="btn btn-outline-secondary">
                                    <i class="bx bx-envelope me-1"></i> Send Reminder
                                </button>
                                <button class="btn btn-outline-danger">
                                    <i class="bx bx-x-circle me-1"></i> Cancel Invoice
                                </button>
                            @elseif($invoice['status'] === 'EXPIRED')
                                <button class="btn btn-outline-info">
                                    <i class="bx bx-refresh me-1"></i> Recreate Invoice
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Merchant Profile -->
                    <div class="mt-4">
                        <h5 class="fw-bold mb-2">Merchant Profile</h5>
                        <div class="d-flex align-items-center">
                            @if($invoice['merchant_profile_picture_url'] ?? false)
                                <img src="{{ $invoice['merchant_profile_picture_url'] }}" alt="Merchant Logo" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="bx bx-store text-muted"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0">{{ $invoice['merchant_name'] ?? 'Unknown Merchant' }}</h6>
                                <small class="text-muted">Merchant</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>