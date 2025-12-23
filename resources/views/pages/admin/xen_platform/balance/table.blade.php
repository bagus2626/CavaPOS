<div class="table-responsive">
    <div class="table-responsive">
        <table class="table table-striped table-hover" style="width:100%">
            <thead class="thead-dark">
            <tr>
                <th>No</th>
                <th>Created Date (GMT +7)</th>
                <th>Settlement</th>
                <th>Transaction Type</th>
                <th>Channel</th>
                <th>Reference</th>
                <th>Fee/VAT</th>
                <th>Cashflow</th>
                <th class="text-end">Amount</th>
                <th class="text-end">Balance</th>
            </tr>
            </thead>
            <tbody>
            @forelse($balances as $index => $tx)
                @php
                    $amountSign = '';
                    $amountClass = '';

                    if ($tx['cashflow'] === 'MONEY_OUT') {
                        $amountSign = '- ';
                        $amountClass = 'text-danger';
                    } elseif ($tx['cashflow'] === 'MONEY_IN') {
                        $amountSign = '+ ';
                        $amountClass = 'text-success';
                    }

                    $amountValue = abs($tx['amount']);

                    $createdDate = isset($tx['created']) ? \Carbon\Carbon::parse($tx['created'])->timezone('Asia/Jakarta') : null;

                    $totalFees = $tx['fee_details']['total_fees'] ?? 0;
                    $hasFees = $totalFees > 0;
                    $feePending = ($tx['fee_details']['status'] ?? '') === 'PENDING';
                @endphp
                <tr>
                    <td>
                        @if($createdDate)
                            <div class="d-flex flex-column">
                                <span class="font-weight-bold">{{ $createdDate->format('d M Y') }}</span>
                                <small class="text-muted">{{ $createdDate->format('H:i A') }}</small>
                            </div>
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        @if (!empty($tx['settlement_status']))
                            <span class="badge
                                    @if($tx['settlement_status'] === 'SETTLED') badge-light-success
                                    @elseif($tx['settlement_status'] === 'PENDING') badge-light-warning
                                    @else badge-light-secondary @endif
                                    badge-pill">
                                    {{ $tx['settlement_status'] }}
                                </span><br>
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        <div class="d-flex flex-column">
                            <span class="font-weight-bold">{{ $tx['transaction_type'] }}</span>
                            @if($feePending)
                                <small class="text-warning">(Fee Pending)</small>
                            @endif
                            @if($hasFees)
                                <small class="text-danger">
                                    (Includes Fee/VAT)
                                </small>
                            @endif
                        </div>
                    </td>

                    <td>{{ $tx['channel_code'] ?? '-' }}</td>
                    <td>{{ $tx['reference_id'] }}</td>

                    <td>
                        @if(($tx['fee_details']['total_fees'] ?? 0) > 0)
                            <small>Fee: - {{ number_format($tx['fee_details']['xendit_fee'], 0, ',', '.') }}</small><br>
                            <small>VAT: - {{ number_format($tx['fee_details']['vat_fee'], 0, ',', '.') }}</small>
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        <span class="badge
                            @if($tx['cashflow'] === 'MONEY_IN') bg-success
                            @elseif($tx['cashflow'] === 'MONEY_OUT') bg-danger
                            @else bg-secondary @endif">
                            {{ $tx['cashflow'] }}
                        </span>
                    </td>

                    <td class="text-end">
                        <div class="d-flex flex-column align-items-end">
                            <span class="{{ $amountClass }} font-weight-bold">
                                {{ $amountSign }}{{ number_format($amountValue, 0, ',', '.') }}
                            </span>
                        </div>
                    </td>

                    <td class="text-end text-primary font-weight-bold">
                        {{ number_format($tx['balance'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">
                        <i class="bx bx-info-circle"></i> Tidak ada data balance yang ditemukan.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

@php
    $beforeId = $meta['before_id'] ?? null;
    $afterId = $meta['after_id'] ?? null;
    $hasMore = $meta['has_more'] ?? false;
    $limit = $meta['limit'] ?? 10;
    $count = count($balances ?? []);

    if (empty($beforeId)) {
        $hasMore = true;
    }
@endphp

    <div id="xendit-balance-pagination" class="mt-1">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center border-top pt-1">

            <div class="d-flex align-items-center text-muted small">
                <i class="bx bx-info-circle me-1"></i>
                <span>
                Showing <strong>{{ $count }}</strong> records (Limit: <strong>{{ $limit }}</strong>)
            </span>
            </div>

            <nav aria-label="Navigasi halaman transaksi">
                <ul class="pagination mb-0">
                    {{-- Tombol Previous --}}
                    <li class="page-item {{ empty($beforeId) ? 'disabled' : '' }} mr-1">
                        <a class="page-link fw-medium px-1 rounded-pill"
                           href="#"
                           data-direction="before"
                           data-before="{{ $beforeId }}"
                           data-after="">
                            ← Previous
                        </a>
                    </li>

                    {{-- Tombol Next --}}
                    <li class="page-item {{ (!$hasMore || empty($afterId) || ($count < $limit)) ? 'disabled' : '' }}">
                        <a class="page-link fw-medium px-1 rounded-pill ms-2"
                           href="#"
                           data-direction="after"
                           data-after="{{ $afterId }}"
                           data-before="">
                            Next →
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>


