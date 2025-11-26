<div class="table-responsive">
    <table class="table table-striped table-hover" style="width:100%">
        <thead class="thead-dark">
        <tr>
            <th>No</th>
            <th>Status</th>
            <th>Type</th>
            <th>Channel</th>
            <th>Account</th>
            <th>Reference ID</th>
            <th>Amount</th>
            <th>Settlement <br> Status</th>
            <th>Date Created (GMT +7)</th>
            <th>Action</th>
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
                            'PENDING'   => 'badge-light-warning',
                            'SUCCESS'   => 'badge-light-success',
                            'FAILED'    => 'badge-light-danger',
                            'VOIDED'    => 'badge-light-secondary',
                            'REVERSED'  => 'badge-light-info',
                            'UNKNOWN'   => 'badge-light-dark',
                        ];
                    @endphp
                    <span class="badge {{ $badgeClasses[$status] ?? 'badge-light-dark' }} badge-pill">{{ $status }}</span>
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

                        $iconClass = 'bx bx-minus text-secondary';
                        if ($cashflow === 'MONEY_IN') {
                            $iconClass = 'bx bx-down-arrow-alt text-success';
                        } elseif ($cashflow === 'MONEY_OUT') {
                            $iconClass = 'bx bx-up-arrow-alt text-danger';
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
                            <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item copy-btn" data-copy-value="{{ $item['id'] }}"><i class="bx bx-copy-alt mr-1"></i> Copy Transaction ID</a>
                                <a class="dropdown-item copy-btn" data-copy-value="{{ $item['reference_id'] ?? '' }}"><i class="bx bx-copy-alt mr-1"></i> Copy Reference</a>
                                <a class="dropdown-item copy-btn" data-copy-value="{{ $item['product_id'] ?? '' }}"><i class="bx bx-copy-alt mr-1"></i> Copy Product ID</a>
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
                    <i class="bx bx-info-circle"></i> Tidak ada data transaksi yang ditemukan.
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
            <span>Showing <strong>{{ $count }}</strong> records (Limit: <strong>{{ $limit }}</strong>)</span>
        </div>

        <nav aria-label="Navigasi halaman transaksi">
            <ul class="pagination mb-0 pagination">
                <li class="page-item {{ empty($beforeId) ? 'disabled' : '' }} mr-1">
                    <a class="page-link fw-medium px-1 rounded-pill" href="#"
                       data-before="{{ $beforeId }}"
                       data-after=""
                       data-direction="before">
                        ← Previous
                    </a>
                </li>

                <li class="page-item {{ (empty($afterId) || !$hasMore || ($count < $limit)) ? 'disabled' : '' }}">
                    <a class="page-link fw-medium px-1 rounded-pill ms-2" href="#"
                       data-after="{{ $afterId }}"
                       data-before=""
                       data-direction="after">
                        Next →
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>

