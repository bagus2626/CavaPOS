<table class="table table-striped table-hover" id="table-disbursement">
    <thead class="thead-dark">
    <tr>
        <th class="w-2p">No</th>
        <th class="w-8p">{{ __('messages.owner.xen_platform.payouts.date_created') }}</th>
        <th class="w-15p">{{ __('messages.owner.xen_platform.payouts.business_name') }}</th>
        <th class="w-10p">{{ __('messages.owner.xen_platform.payouts.reference') }}</th>
        <th class="w-5p">{{ __('messages.owner.xen_platform.payouts.channel') }}</th>
        <th class="w-15p">{{ __('messages.owner.xen_platform.payouts.amount') }}</th>
        <th class="w-10p">Status</th>
    </tr>
    </thead>
    <tbody>
    @forelse($data as $item)
        <tr class="disbursement-clickable-row cursor-pointer"
            data-business-id="{{ optional($item->subAccount)->xendit_user_id ?? $item->business_id }}"
            data-payout-id="{{ $item->payout_id }}">
            <td>{{ $data->firstItem() + $loop->index }}</td>
            <td>
                <span class="font-weight-bold">{{ \Carbon\Carbon::parse($item->created_xendit)->timezone('Asia/Jakarta')->format('d M Y') }}</span><br>
                <small class="text-muted">{{ \Carbon\Carbon::parse($item->created_xendit)->timezone('Asia/Jakarta')->format('h:i A') }}</small>
            </td>
            <td>{{ optional($item->subAccount)->business_name ?? '-'}}</td>
            <td>{{ $item->reference_id }}</td>
            <td>{{ $item->channel_code }}</td>
            <td>{{ $item->currency }} {{ number_format($item->amount) }}</td>
            <td>
                @php
                    $status = $item->status ?? 'UNKNOWN';
                    $badgeClasses = [
                        'REQUESTED'   => 'bg-info',
                        'REVERSED'    => 'bg-primary',
                        'ACCEPTED'    => 'bg-warning',
                        'SUCCEEDED'   => 'bg-success',
                        'FAILED'      => 'bg-danger',
                        'CANCELLED'   => 'bg-secondary',
                        'UNKNOWN'     => 'bg-secondary',
                    ];
                @endphp
                <div class="badge {{ $badgeClasses[$status] ?? 'bg-secondary' }} badge-pill ml-1">{{ $status }}</div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center">
                <i class="bx bx-info-circle"></i> {{ __('messages.owner.xen_platform.payouts.no_withdrawal_data_found') }}
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
<div class="d-flex justify-content-between align-items-center mt-1">
    @if ($data->total() > 0)
        <div class="pagination-summary text-muted">
            {{ __('messages.owner.xen_platform.payouts.showing') }} 
            {{ $data->firstItem() }} - {{ $data->lastItem() }} 
            {{ __('messages.owner.xen_platform.payouts.from') }} {{ $data->total() }} 
            {{ __('messages.owner.xen_platform.payouts.withdrawal') }}
        </div>
    @else
        <div class="pagination-summary text-muted">
            {{ __('messages.owner.xen_platform.payouts.no_data_found') }}
        </div>
    @endif


    <div class="pagination-links">
        {{ $data->appends(request()->query())->links('vendor.pagination.custom-limited') }}
    </div>
</div>

