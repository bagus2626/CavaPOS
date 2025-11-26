<div class="table-responsive">
    <table class="table table-striped table-hover" style="width:100%">
        <thead class="thead-dark">
        <tr>
            <th>No</th>
            <th>Status</th>
            <th>{{ __('messages.owner.xen_platform.accounts.type') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.channel') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.account') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.reference_id') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.amount') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.settlement') }} <br> Status</th>
            <th>{{ __('messages.owner.xen_platform.accounts.date_created') }} (GMT +7)</th>
            <th>{{ __('messages.owner.xen_platform.accounts.action') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($data['transactions'] as $item)
            <tr class="transaction-clickable-row row-ov"
                data-transaction-id="{{ $item['id'] ?? '' }}"
                data-business-id="{{ $item['business_id'] ?? '' }}"
                style="cursor: pointer;">
                <td>
                    @php
                        $status = $item['status'] ?? 'UNKNOWN';
                        $badgeClasses = [
                            'PENDING'   => 'bg-warning',
                            'SUCCESS'   => 'bg-success',
                            'FAILED'    => 'bg-danger',
                            'VOIDED'    => 'bg-secondary',
                            'REVERSED'  => 'bg-info',
                            'UNKNOWN'   => 'bg-dark',
                        ];
                    @endphp
                    <span class="badge {{ $badgeClasses[$status] ?? 'bg-dark' }} badge-pill">{{ $status }}</span>
                </td>
                <td class="text-bold-500">{{ $item['type'] ?? '-' }}</td>
                <td>{{ $item['channel_category'] ?? '-' }}</td>
                <td>{{ $item['channel_code'] ?? '-' }}</td>
                <td>{{ $item['reference_id'] ?? ($item['id'] ?? '-') }}</td>
                <td>
                    @php
                        $cashflow = $item['cashflow'] ?? null;
                        $amount = $item['amount'] ?? 0;
                        $currency = $item['currency'] ?? 'IDR';

                        $iconClass = 'fas fa-minus text-secondary';

                        if ($cashflow === 'MONEY_IN') {
                            $iconClass = 'fas fa-arrow-down text-success';
                        } elseif ($cashflow === 'MONEY_OUT') {
                            $iconClass = 'fas fa-arrow-up text-danger';
                        }
                    @endphp

                    <i class="{{ $iconClass }}"></i>
                    {{ $currency }} {{ number_format($amount, 0, ',', '.') }}
                </td>
                <td>
                    @if(isset($item['estimated_settlement_time']))
                        @php
                            $settlementTime = \Carbon\Carbon::parse($item['estimated_settlement_time'])->timezone('Asia/Jakarta');
                        @endphp
                        <span class="font-weight-bold">{{ $item['settlement_status'] ?? '-' }}</span><br>
                        <small class="text-muted">{{ $settlementTime->format('d M Y, H:i A') }}</small>
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if(isset($item['created']))
                        @php
                            $createdDate = \Carbon\Carbon::parse($item['created'])->timezone('Asia/Jakarta');
                        @endphp
                        <span class="font-weight-bold">{{ $createdDate->format('d M Y') }}</span><br>
                        <small class="text-muted">{{ $createdDate->format('H:i A') }}</small>
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if(isset($item['id']))
                        <div class="dropdown">
                            <span class="fas fa-ellipsis-v fa-lg font-medium-3 nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item copy-btn" data-copy-value="{{ $item['id'] }}"><i class="bx bx-copy-alt mr-1"></i> {{ __('messages.owner.xen_platform.accounts.copy_transaction_id') }}</a>
                                <a class="dropdown-item copy-btn" data-copy-value="{{ $item['reference_id'] ?? '' }}"><i class="bx bx-copy-alt mr-1"></i> {{ __('messages.owner.xen_platform.accounts.copy_reference') }}</a>
                                <a class="dropdown-item copy-btn" data-copy-value="{{ $item['product_id'] ?? '' }}"><i class="bx bx-copy-alt mr-1"></i> {{ __('messages.owner.xen_platform.accounts.copy_product_id') }}</a>
                            </div>
                        </div>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">
                    <i class="fas fa-info-circle"></i> {{ __('messages.owner.xen_platform.accounts.no_transaction_data_found') }}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@php
    $meta = $data['meta'] ?? [];
    $beforeId = $meta['before_id'] ?? null;
    $afterId = $meta['after_id'] ?? null;
    $hasMore = $meta['has_more'] ?? false;
    $limit = $meta['limit'] ?? 10;
    $count = count($data['transactions']);
@endphp

<div id="xendit-pagination" class="mt-1">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center border-top pt-1">

        <div class="d-flex align-items-center text-muted small">
            <i class="bx bx-info-circle me-1"></i>
            <span>{{ __('messages.owner.xen_platform.accounts.showing') }} <strong>{{ $count }}</strong> {{ __('messages.owner.xen_platform.accounts.records') }} (Limit: <strong>{{ $limit }}</strong>)</span>
        </div>

        <nav aria-label="Navigasi halaman transaksi" class="py-2 px-2">
            <ul class="pagination mb-0 pagination">
                <li class="page-item {{ empty($beforeId) ? 'disabled' : '' }} mr-1">
                    <a class="page-link fw-mediu rounded-pill" href="#"
                       data-before="{{ $beforeId }}"
                       data-after=""
                       data-direction="before">
                        &laquo; {{ __('messages.owner.xen_platform.accounts.previous') }}
                    </a>
                </li>

                <li class="page-item {{ (empty($afterId) || !$hasMore || ($count < $limit)) ? 'disabled' : '' }}">
                    <a class="page-link fw-medium rounded-pill ms-2" href="#"
                       data-after="{{ $afterId }}"
                       data-before=""
                       data-direction="after">
                        {{ __('messages.owner.xen_platform.accounts.next') }} &raquo;
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
