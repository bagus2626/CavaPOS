<table class="data-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 60px;">#</th>
            <th>status</th>
            <th>{{ __('messages.owner.xen_platform.accounts.type') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.channel') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.account') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.reference_id') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.amount') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.settlement') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.date_created') }}</th>
            <th class="text-center" style="width: 120px;">
                {{ __('messages.owner.xen_platform.accounts.action') }}
            </th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['transactions'] as $index => $item)
            <tr class="table-row transaction-clickable-row" data-transaction-id="{{ $item['id'] ?? '' }}"
                data-business-id="{{ $item['business_id'] ?? '' }}" style="cursor: pointer;">

                <!-- Status -->
                <td>
                    @php
                        $status = $item['status'] ?? 'UNKNOWN';
                        $badgeClasses = [
                            'PENDING' => 'badge-warning',
                            'SUCCESS' => 'badge-success',
                            'FAILED' => 'badge-danger',
                            'VOIDED' => 'badge-secondary',
                            'REVERSED' => 'badge-info',
                            'UNKNOWN' => 'badge-secondary',
                        ];
                    @endphp
                    <span class="badge-modern {{ $badgeClasses[$status] ?? 'badge-secondary' }}">
                        {{ $status }}
                    </span>
                </td>

                <!-- Type -->
                <td>
                    <span class="fw-600">{{ $item['type'] ?? '-' }}</span>
                </td>

                <!-- Channel Category -->
                <td>
                    <span class="text-secondary">{{ $item['channel_category'] ?? '-' }}</span>
                </td>

                <!-- Channel Code -->
                <td>
                    <span class="badge-modern badge-info">
                        {{ $item['channel_code'] ?? '-' }}
                    </span>
                </td>

                <!-- Reference ID -->
                <td>
                    <code class="text-monospace small">{{ $item['reference_id'] ?? ($item['id'] ?? '-') }}</code>
                </td>

                <!-- Amount -->
                <td>
                    @php
                        $cashflow = $item['cashflow'] ?? null;
                        $amount = $item['amount'] ?? 0;
                        $currency = $item['currency'] ?? 'IDR';

                        $iconClass = 'text-secondary';
                        $amountClass = 'text-secondary';

                        if ($cashflow === 'MONEY_IN') {
                            $iconClass = 'text-success';
                            $amountClass = 'text-success fw-600';
                        } elseif ($cashflow === 'MONEY_OUT') {
                            $iconClass = 'text-danger';
                            $amountClass = 'text-danger fw-600';
                        }
                    @endphp

                    <div class="cell-with-icon">
                        @if($cashflow === 'MONEY_IN')
                            <span class="material-symbols-outlined {{ $iconClass }}"
                                style="font-size: 18px;">arrow_downward</span>
                        @elseif($cashflow === 'MONEY_OUT')
                            <span class="material-symbols-outlined {{ $iconClass }}"
                                style="font-size: 18px;">arrow_upward</span>
                        @else
                            <span class="material-symbols-outlined {{ $iconClass }}" style="font-size: 18px;">remove</span>
                        @endif
                        <span class="{{ $amountClass }}">{{ $currency }} {{ number_format($amount, 0, ',', '.') }}</span>
                    </div>
                </td>

                <!-- Settlement -->
                <td>
                    @if(isset($item['estimated_settlement_time']))
                        @php
                            $settlementTime = \Carbon\Carbon::parse($item['estimated_settlement_time'])->timezone('Asia/Jakarta');
                        @endphp
                        <div>
                            <span class="fw-600">{{ $item['settlement_status'] ?? '-' }}</span><br>
                            <small class="text-muted">{{ $settlementTime->format('d M Y, H:i A') }}</small>
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                <!-- Created Date -->
                <td>
                    @if(isset($item['created']))
                        @php
                            $createdDate = \Carbon\Carbon::parse($item['created'])->timezone('Asia/Jakarta');
                        @endphp
                        <div>
                            <span class="fw-600">{{ $createdDate->format('d M Y') }}</span><br>
                            <small class="text-muted">{{ $createdDate->format('H:i A') }}</small>
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                <!-- Actions -->
                <td class="text-center">
                    @if(isset($item['id']))
                        <div class="dropdown">
                            <span class="fas fa-ellipsis-v fa-lg font-medium-3 nav-hide-arrow cursor-pointer"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item copy-btn" data-copy-value="{{ $item['id'] }}">
                                    <i class="bx bx-copy-alt mr-1"></i>
                                    {{ __('messages.owner.xen_platform.accounts.copy_transaction_id') }}
                                </a>
                                <a class="dropdown-item copy-btn" data-copy-value="{{ $item['reference_id'] ?? '' }}">
                                    <i class="bx bx-copy-alt mr-1"></i>
                                    {{ __('messages.owner.xen_platform.accounts.copy_reference') }}
                                </a>
                                <a class="dropdown-item copy-btn" data-copy-value="{{ $item['product_id'] ?? '' }}">
                                    <i class="bx bx-copy-alt mr-1"></i>
                                    {{ __('messages.owner.xen_platform.accounts.copy_product_id') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">
                    <div class="table-empty-state">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <h4>{{ __('messages.owner.xen_platform.accounts.no_transaction_data_found') }}</h4>
                        <p>{{ __('messages.owner.xen_platform.accounts.no_transaction_description') ?? 'No transactions available at the moment' }}
                        </p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Pagination -->

@php
    $meta = $data['meta'] ?? [];
    $beforeId = $meta['before_id'] ?? null;
    $afterId = $meta['after_id'] ?? null;
    $hasMore = $meta['has_more'] ?? false;
    $limit = $meta['limit'] ?? 10;
    $count = count($data['transactions']);
@endphp

<div id="xendit-pagination" class="border-top py-3 px-3">
    <div class="row align-items-center">

        <div class="col-md-6 col-12 mb-2 mb-md-0">
            <div class="text-muted small">
                <i class="bx bx-info-circle me-1"></i>
                {{ __('messages.owner.xen_platform.accounts.showing') }}
                <span class="fw-bold text-dark">{{ $count }}</span>
                {{ __('messages.owner.xen_platform.accounts.records') }}
                (Limit: <span class="fw-bold text-dark">{{ $limit }}</span>)
            </div>
        </div>

        <div class="col-md-6 col-12">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-md-end justify-content-start mb-0">
                    <li class="page-item pagination-nav-btn {{ empty($beforeId) ? 'disabled' : '' }}">
                        <a class="page-link" href="#" style="width: auto !important; height: auto !important;"
                            data-before="{{ $beforeId }}" data-after="" data-direction="before"
                            tabindex="{{ empty($beforeId) ? '-1' : '0' }}"
                            aria-disabled="{{ empty($beforeId) ? 'true' : 'false' }}">
                            <span aria-hidden="true">&laquo;</span>
                            <span
                                class=" d-sm-inline ml-1">{{ __('messages.owner.xen_platform.accounts.previous') }}</span>
                        </a>
                    </li>

                    <li
                        class="page-item pagination-nav-btn {{ (empty($afterId) || !$hasMore || ($count < $limit)) ? 'disabled' : '' }}">
                        <a class="page-link" href="#" style="width: auto !important; height: auto !important;"
                            data-after="{{ $afterId }}" data-before="" data-direction="after"
                            tabindex="{{ (empty($afterId) || !$hasMore || ($count < $limit)) ? '-1' : '0' }}"
                            aria-disabled="{{ (empty($afterId) || !$hasMore || ($count < $limit)) ? 'true' : 'false' }}">
                            <span class=" d-sm-inline mr-1">{{ __('messages.owner.xen_platform.accounts.next') }}</span>
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </div>
</div>