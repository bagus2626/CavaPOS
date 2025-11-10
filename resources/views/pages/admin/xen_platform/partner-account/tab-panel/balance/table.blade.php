<div class="table-responsive">
    <table class="table table-striped table-hover" style="width:100%">
        <thead class="thead-dark">
        <tr>
            <th>No</th>
            <th>Completed Date (GMT +7)</th>
            <th>Transaction Type</th>
            <th>Channel</th>
            <th>Reference</th>
            <th>Details/Fee</th>
            <th>Cashflow</th>
            <th class="text-end">Amount</th>
            <th class="text-end">Balance</th>
        </tr>
        </thead>
        <tbody>
        @forelse($data['transactions'] as $index => $tx)
            @php
                $amountSign = '';
                $amountClass = '';

                if ($tx['cashflow'] === 'MONEY_OUT') {
                    $amountSign = '- ';
                    $amountClass = 'text-danger';
                }
                elseif ($tx['cashflow'] === 'MONEY_IN') {
                    $amountSign = '+ ';
                    $amountClass = 'text-success';
                }
                else {
                    $amountClass = '';
                }

                $amountValue = abs($tx['amount']);
            @endphp
            <tr>
                <td>{{ $tx['created'] }}</td>
                <td>
                    {{ $tx['transaction_type'] }}
                    @if(($tx['fee_details']['total_fees'] ?? 0) > 0)
                        <br><small class="text-info fw-normal">(Includes Fee/VAT)</small>
                    @endif
                </td>
                <td>{{ $tx['channel_code'] }}</td>
                <td>{{ $tx['reference_id'] }}</td>

                <td>
                    @if(($tx['fee_details']['total_fees'] ?? 0) > 0)
                        <small>Fee: - {{ number_format($tx['fee_details']['xendit_fee'], 0, ',', '.') }}</small><br>
                        <small>VAT: - {{ number_format($tx['fee_details']['vat_fee'], 0, ',', '.') }}</small>
                    @else
                        -
                    @endif
                </td>

                <td>{{ $tx['cashflow'] }}</td>
                <td class="text-end {{ $amountClass }}">
                    {{ $amountSign }}{{ number_format($amountValue, 0, ',', '.') }}
                </td>

                <td class="text-end text-primary fw-bold">
                    {{ number_format($tx['balance'], 0, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">
                    <i class="bx bx-info-circle"></i> Tidak ada data balance yang ditemukan.
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
    $count = count($data['transactions'] ?? []);

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


