<table class="table table-striped table-hover" id="table-split-payments">
    <thead class="thead-dark">
    <tr>
        <th class="w-2p">No</th>
        <th class="w-20p">{{ __('messages.owner.xen_platform.split_payments.split_id') }}</th>
        <th class="w-15p">{{ __('messages.owner.xen_platform.split_payments.reference') }}</th>
        <th class="w-15p">{{ __('messages.owner.xen_platform.split_payments.date_created') }} <br> <span>(GMT +7)</span></th>
        <th class="w-20p">{{ __('messages.owner.xen_platform.split_payments.source_account_id') }}</th>
        <th class="w-10p">{{ __('messages.owner.xen_platform.split_payments.transaction_amount') }}</th>
        <th class="w-20p">{{ __('messages.owner.xen_platform.split_payments.destination_account_id') }}</th>
        <th class="w-10p">{{ __('messages.owner.xen_platform.split_payments.total_split') }}</th>
        <th class="w-10p">{{ __('messages.owner.xen_platform.split_payments.settled') }} (GMT+7)</th>
        <th class="w-10p">Status</th>
    </tr>
    </thead>
    <tbody>
        @forelse($splitTransactions as $item)
        <tr>
            <td>{{ $splitTransactions->firstItem() + $loop->index }}</td>
            <td>{{ $item['xendit_split_payment_id'] }}</td>
            <td>{{ $item['reference_id'] }}</td>
            <td>
                <span class="font-weight-bold">{{ \Carbon\Carbon::parse($item['date_created'])->timezone('Asia/Jakarta')->format('d M Y') }}</span><br>
                <small class="text-muted">{{ \Carbon\Carbon::parse($item['date_created'])->timezone('Asia/Jakarta')->format('h:i A') }}</small>
            </td>
            <td>
                <span class="font-weight-bold">{{ $item['source_account_id'] }}</span><br>
                <small class="text-muted">{{ $item['source_account_name'] }}</small>
            </td>
            <td>
                IDR {{number_format($item['transaction_amount'])}}
            </td>
            <td>
                <span class="font-weight-bold">{{ $item['destination_account_id'] }}</span><br>
                <small class="text-muted">{{ $item['destination_account_name'] }}</small>
            </td>
            <td>
                IDR {{number_format($item['total_split'])}}
            </td>
            <td>
                <span class="font-weight-bold">{{ \Carbon\Carbon::parse($item['date_settled'])->timezone('Asia/Jakarta')->format('d M Y') }}</span><br>
                <small class="text-muted">{{ \Carbon\Carbon::parse($item['date_settled'])->timezone('Asia/Jakarta')->format('h:i A') }}</small>
            </td>
            <td class="text-center">
                @if ($item['status'] === 'COMPLETED')
                    <span class="badge bg-success badge-pill">{{ $item['status'] }}</span>
                @elseif ($item['status'] === 'FAILED')
                    <span class="badge bg-danger badge-pill">{{ $item['status'] }}</span>
                @else
                    <span class="badge bg-secondary badge-pill">{{ $item['status'] }}</span>
                @endif
            </td>
        </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">
                    <i class="bx bx-info-circle"></i> {{ __('messages.owner.xen_platform.split_payments.no_split_data_found') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="d-flex justify-content-between align-items-center mt-1">
    @if ($splitTransactions->total() > 0)
        <div class="pagination-summary text-muted">
            {{ __('messages.owner.xen_platform.split_payments.showing') }} 
            {{ $splitTransactions->firstItem() }} - {{ $splitTransactions->lastItem() }} 
            {{ __('messages.owner.xen_platform.split_payments.from') }} {{ $splitTransactions->total() }} 
            {{ __('messages.owner.xen_platform.split_payments.split_payments_s') }}
        </div>
    @endif

    <div class="pagination-links">
        {{ $splitTransactions->appends(request()->query())->links('vendor.pagination.custom-limited') }}
    </div>
</div>
