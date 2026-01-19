<table class="data-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 60px;">#</th>
            <th style="width: 180px;">{{ __('messages.owner.xen_platform.split_payments.split_id') }}</th>
            <th style="width: 150px;">{{ __('messages.owner.xen_platform.split_payments.reference') }}</th>
            <th style="width: 140px;">
                {{ __('messages.owner.xen_platform.split_payments.date_created') }}<br>
                <span class="text-muted" style="font-weight: 400; font-size: 0.85em;">(GMT +7)</span>
            </th>
            <th style="width: 180px;">{{ __('messages.owner.xen_platform.split_payments.source_account_id') }}</th>
            <th style="width: 120px;">{{ __('messages.owner.xen_platform.split_payments.transaction_amount') }}</th>
            <th style="width: 180px;">{{ __('messages.owner.xen_platform.split_payments.destination_account_id') }}</th>
            <th style="width: 120px;">{{ __('messages.owner.xen_platform.split_payments.total_split') }}</th>
            <th style="width: 140px;">
                {{ __('messages.owner.xen_platform.split_payments.settled') }}<br>
                <span class="text-muted" style="font-weight: 400; font-size: 0.85em;">(GMT +7)</span>
            </th>
            <th class="text-center" style="width: 100px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($splitTransactions as $item)
        <tr class="table-row">
            <td class="text-center text-muted">{{ $splitTransactions->firstItem() + $loop->index }}</td>
            
            <td>
                <span class="fw-600">{{ $item['xendit_split_payment_id'] }}</span>
            </td>
            
            <td>
                <span class="data-name">{{ $item['reference_id'] }}</span>
            </td>
            
            <td>
                <span class="fw-600">{{ \Carbon\Carbon::parse($item['date_created'])->timezone('Asia/Jakarta')->format('d M Y') }}</span><br>
                <small class="text-muted">{{ \Carbon\Carbon::parse($item['date_created'])->timezone('Asia/Jakarta')->format('h:i A') }}</small>
            </td>
            
            <td>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <span class="fw-600">{{ $item['source_account_id'] }}</span>
                    <small class="text-muted">{{ $item['source_account_name'] }}</small>
                </div>
            </td>
            
            <td>
                <span class="fw-600">IDR {{ number_format($item['transaction_amount']) }}</span>
            </td>
            
            <td>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <span class="fw-600">{{ $item['destination_account_id'] }}</span>
                    <small class="text-muted">{{ $item['destination_account_name'] }}</small>
                </div>
            </td>
            
            <td>
                <span class="fw-600">IDR {{ number_format($item['total_split']) }}</span>
            </td>
            
            <td>
                <span class="fw-600">{{ \Carbon\Carbon::parse($item['date_settled'])->timezone('Asia/Jakarta')->format('d M Y') }}</span><br>
                <small class="text-muted">{{ \Carbon\Carbon::parse($item['date_settled'])->timezone('Asia/Jakarta')->format('h:i A') }}</small>
            </td>
            
            <td class="text-center">
                @if ($item['status'] === 'COMPLETED')
                    <span class="badge-modern badge-success">{{ $item['status'] }}</span>
                @elseif ($item['status'] === 'FAILED')
                    <span class="badge-modern badge-danger">{{ $item['status'] }}</span>
                @else
                    <span class="badge-modern badge-secondary">{{ $item['status'] }}</span>
                @endif
            </td>
        </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">
                    <div class="table-empty-state">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <h4>{{ __('messages.owner.xen_platform.split_payments.no_split_data_found') }}</h4>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if($splitTransactions->hasPages())
    <div class="table-pagination">
        <div class="pagination-info">
            @if ($splitTransactions->total() > 0)
                <span class="text-muted">
                    {{ __('messages.owner.xen_platform.split_payments.showing') }} 
                    {{ $splitTransactions->firstItem() }} - {{ $splitTransactions->lastItem() }} 
                    {{ __('messages.owner.xen_platform.split_payments.from') }} {{ $splitTransactions->total() }} 
                    {{ __('messages.owner.xen_platform.split_payments.split_payments_s') }}
                </span>
            @endif
        </div>
        <div class="pagination-links">
            {{ $splitTransactions->appends(request()->query())->links('vendor.pagination.custom-limited') }}
        </div>
    </div>
@endif