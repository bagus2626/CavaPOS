<table class="data-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 60px;">#</th>
            <th>{{ __('messages.owner.xen_platform.accounts.created_date') }} (GMT +7)</th>
            <th>{{ __('messages.owner.xen_platform.accounts.settlement') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.transaction_type') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.channel') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.reference') }}</th>
            <th>{{ __('messages.owner.xen_platform.accounts.fee') }}/VAT</th>
            <th>{{ __('messages.owner.xen_platform.accounts.cashflow') }}</th>
            <th class="text-end">{{ __('messages.owner.xen_platform.accounts.amount') }}</th>
            <th class="text-end">{{ __('messages.owner.xen_platform.accounts.balance') }}</th>
        </tr>
    </thead>

    <tbody>
        @forelse($data['transactions'] as $index => $tx)
            @php
                $cashflow = $tx['cashflow'] ?? null;
                $amount = abs($tx['amount'] ?? 0);
                $currency = $tx['currency'] ?? 'IDR';

                $amountClass = 'text-secondary fw-600';
                $iconClass = 'text-secondary';

                if ($cashflow === 'MONEY_IN') {
                    $amountClass = 'text-success fw-600';
                    $iconClass = 'text-success';
                } elseif ($cashflow === 'MONEY_OUT') {
                    $amountClass = 'text-danger fw-600';
                    $iconClass = 'text-danger';
                }

                $createdDate = isset($tx['created'])
                    ? \Carbon\Carbon::parse($tx['created'])->timezone('Asia/Jakarta')
                    : null;

                $settlementStatus = $tx['settlement_status'] ?? null;

                $totalFees = $tx['fee_details']['total_fees'] ?? 0;
                $feePending = ($tx['fee_details']['status'] ?? '') === 'PENDING';
            @endphp

            <tr class="table-row">

                <!-- Created -->
                <td>
                    @if($createdDate)
                        <div>
                            <span class="fw-600">{{ $createdDate->format('d M Y') }}</span><br>
                            <small class="text-muted">{{ $createdDate->format('H:i A') }}</small>
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                <!-- Settlement -->
                <td>
                    @if($settlementStatus)
                        @php
                            $settlementBadge = [
                                'SETTLED' => 'badge-success',
                                'PENDING' => 'badge-warning',
                            ];
                        @endphp
                        <span class="badge-modern {{ $settlementBadge[$settlementStatus] ?? 'badge-secondary' }}">
                            {{ $settlementStatus }}
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                <!-- Transaction Type -->
                <td>
                    <div>
                        <span class="fw-600">{{ $tx['transaction_type'] ?? '-' }}</span>
                        @if($feePending)
                            <br><small class="text-warning">
                                {{ __('messages.owner.xen_platform.accounts.pending_fee') }}
                            </small>
                        @endif
                        @if($totalFees > 0)
                            <br><small class="text-danger">
                                {{ __('messages.owner.xen_platform.accounts.includes_fee_vat') }}
                            </small>
                        @endif
                    </div>
                </td>

                <!-- Channel -->
                <td>
                    <span class="badge-modern badge-info">
                        {{ $tx['channel_code'] ?? '-' }}
                    </span>
                </td>

                <!-- Reference -->
                <td>
                    <code class="text-monospace small">
                        {{ $tx['reference_id'] ?? '-' }}
                    </code>
                </td>

                <!-- Fee -->
                <td>
                    @if($totalFees > 0)
                        <small>
                            {{ __('messages.owner.xen_platform.accounts.fee') }}:
                            - {{ number_format($tx['fee_details']['xendit_fee'] ?? 0, 0, ',', '.') }}
                        </small><br>
                        <small>
                            VAT:
                            - {{ number_format($tx['fee_details']['vat_fee'] ?? 0, 0, ',', '.') }}
                        </small>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                <!-- Cashflow -->
                <td>
                    <span class="badge-modern
                        {{ $cashflow === 'MONEY_IN' ? 'badge-success' :
                            ($cashflow === 'MONEY_OUT' ? 'badge-danger' : 'badge-secondary') }}">
                        {{ $cashflow ?? '-' }}
                    </span>
                </td>

                <!-- Amount -->
                <td class="text-end">
                    <div class="cell-with-icon justify-content-end">
                        @if($cashflow === 'MONEY_IN')
                            <span class="material-symbols-outlined {{ $iconClass }}">arrow_downward</span>
                        @elseif($cashflow === 'MONEY_OUT')
                            <span class="material-symbols-outlined {{ $iconClass }}">arrow_upward</span>
                        @else
                            <span class="material-symbols-outlined {{ $iconClass }}">remove</span>
                        @endif
                        <span class="{{ $amountClass }}">
                            {{ $currency }} {{ number_format($amount, 0, ',', '.') }}
                        </span>
                    </div>
                </td>

                <!-- Balance -->
                <td class="text-end fw-600 text-primary">
                    {{ number_format($tx['balance'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">
                    <div class="table-empty-state">
                        <span class="material-symbols-outlined">account_balance_wallet</span>
                        <h4>{{ __('messages.owner.xen_platform.accounts.no_balance_data_found') }}</h4>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
{{-- Pagination --}}
@php
    $meta = $data['meta'] ?? [];
    $beforeId = $meta['before_id'] ?? null;
    $afterId = $meta['after_id'] ?? null;
    $hasMore = $meta['has_more'] ?? false;
    $limit = $meta['limit'] ?? 10;
    // Menggunakan null coalescing operator seperti pada kode target asli agar aman
    $count = count($data['transactions'] ?? []); 
@endphp

<div id="xendit-balance-pagination" class="border-top py-3 px-3">
    <div class="row align-items-center">
        
        {{-- Info Section --}}
        <div class="col-md-6 col-12 mb-2 mb-md-0">
            <div class="text-muted small">
                {{-- Mengganti Material Symbols dengan Boxicons agar sesuai style referensi --}}
                <i class="bx bx-info-circle me-1"></i>
                {{ __('messages.owner.xen_platform.accounts.showing') }} 
                <span class="fw-bold text-dark">{{ $count }}</span> 
                {{ __('messages.owner.xen_platform.accounts.records') }} 
                (Limit: <span class="fw-bold text-dark">{{ $limit }}</span>)
            </div>
        </div>

        {{-- Navigation Section --}}
        <div class="col-md-6 col-12">
            <nav aria-label="Navigasi halaman balance">
                <ul class="pagination justify-content-md-end justify-content-start mb-0">
                    
                    {{-- Previous Button --}}
                    <li class="page-item pagination-nav-btn {{ empty($beforeId) ? 'disabled' : '' }}">
                        <a class="page-link" href="#"
                           style="width: auto !important; height: auto !important;"
                           data-before="{{ $beforeId }}"
                           data-after=""
                           data-direction="before"
                           tabindex="{{ empty($beforeId) ? '-1' : '0' }}" 
                           aria-disabled="{{ empty($beforeId) ? 'true' : 'false' }}">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="d-sm-inline ml-1">{{ __('messages.owner.xen_platform.accounts.previous') }}</span>
                        </a>
                    </li>

                    {{-- Next Button --}}
                    {{-- Logika disabled tetap mengikuti kondisi data balance --}}
                    <li class="page-item pagination-nav-btn {{ (!$hasMore || empty($afterId) || ($count < $limit)) ? 'disabled' : '' }}">
                        <a class="page-link" href="#"
                           style="width: auto !important; height: auto !important;"
                           data-after="{{ $afterId }}"
                           data-before=""
                           data-direction="after"
                           tabindex="{{ (!$hasMore || empty($afterId) || ($count < $limit)) ? '-1' : '0' }}"
                           aria-disabled="{{ (!$hasMore || empty($afterId) || ($count < $limit)) ? 'true' : 'false' }}">
                            <span class="d-sm-inline mr-1">{{ __('messages.owner.xen_platform.accounts.next') }}</span>
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </div>
</div>