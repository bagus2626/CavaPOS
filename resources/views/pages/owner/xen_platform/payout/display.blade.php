<table class="data-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 60px;">#</th>
            <th style="width: 140px;">{{ __('messages.owner.xen_platform.payouts.date_created') }}</th>
            <th style="width: 200px;">{{ __('messages.owner.xen_platform.payouts.business_name') }}</th>
            <th style="width: 150px;">{{ __('messages.owner.xen_platform.payouts.reference') }}</th>
            <th style="width: 100px;">{{ __('messages.owner.xen_platform.payouts.channel') }}</th>
            <th style="width: 150px;">{{ __('messages.owner.xen_platform.payouts.amount') }}</th>
            <th class="text-center" style="width: 120px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $item)
        <tr class="table-row disbursement-clickable-row cursor-pointer"
            data-business-id="{{ optional($item->subAccount)->xendit_user_id ?? $item->business_id }}"
            data-payout-id="{{ $item->payout_id }}">
            <td class="text-center text-muted">{{ $data->firstItem() + $loop->index }}</td>
            
            <td>
                <span class="fw-600">{{ \Carbon\Carbon::parse($item->created_xendit)->timezone('Asia/Jakarta')->format('d M Y') }}</span><br>
                <small class="text-muted">{{ \Carbon\Carbon::parse($item->created_xendit)->timezone('Asia/Jakarta')->format('h:i A') }}</small>
            </td>
            
            <td>
                <span class="data-name">{{ optional($item->subAccount)->business_name ?? '-'}}</span>
            </td>
            
            <td>
                <span class="fw-600">{{ $item->reference_id }}</span>
            </td>
            
            <td>
                <span>{{ $item->channel_code }}</span>
            </td>
            
            <td>
                <span class="fw-600">{{ $item->currency }} {{ number_format($item->amount) }}</span>
            </td>
            
            <td class="text-center">
                @php
                    $status = $item->status ?? 'UNKNOWN';
                    $badgeClasses = [
                        'REQUESTED'   => 'badge-info',
                        'REVERSED'    => 'badge-primary',
                        'ACCEPTED'    => 'badge-warning',
                        'SUCCEEDED'   => 'badge-success',
                        'FAILED'      => 'badge-danger',
                        'CANCELLED'   => 'badge-secondary',
                        'UNKNOWN'     => 'badge-secondary',
                    ];
                @endphp
                <span class="badge-modern {{ $badgeClasses[$status] ?? 'badge-secondary' }}">{{ $status }}</span>
            </td>
        </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">
                    <div class="table-empty-state">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <h4>{{ __('messages.owner.xen_platform.payouts.no_withdrawal_data_found') }}</h4>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if($data->hasPages())
    <div class="table-pagination">
        <div class="pagination-info">
            @if ($data->total() > 0)
                <span class="text-muted">
                    {{ __('messages.owner.xen_platform.payouts.showing') }} 
                    {{ $data->firstItem() }} - {{ $data->lastItem() }} 
                    {{ __('messages.owner.xen_platform.payouts.from') }} {{ $data->total() }} 
                    {{ __('messages.owner.xen_platform.payouts.withdrawal') }}
                </span>
            @else
                <span class="text-muted">
                    {{ __('messages.owner.xen_platform.payouts.no_data_found') }}
                </span>
            @endif
        </div>
        <div class="pagination-links">
            {{ $data->appends(request()->query())->links('vendor.pagination.custom-limited') }}
        </div>
    </div>
@endif