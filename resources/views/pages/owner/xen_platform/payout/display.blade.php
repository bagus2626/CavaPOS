<table class="table table-striped table-hover" id="table-disbursement">
    <thead class="thead-dark">
    <tr>
        <th class="w-2p">No</th>
        <th class="w-8p">Date Created</th>
        <th class="w-15p">Nama Partner</th>
        <th class="w-10p">Reference</th>
        <th class="w-5p">Channel</th>
        <th class="w-15p">Amount</th>
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
                <i class="bx bx-info-circle"></i> Tidak ada data disbursement yang ditemukan.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
<div class="d-flex justify-content-between align-items-center mt-1">
    @if ($data->total() > 0)
        <div class="pagination-summary text-muted">
            Showing {{ $data->firstItem() }} - {{ $data->lastItem() }} from {{ $data->total() }} disbursement
        </div>
    @else
        <div class="pagination-summary text-muted">
            Tidak ada data yang ditemukan.
        </div>
    @endif


    <div class="pagination-links">
        {{ $data->appends(request()->query())->links('vendor.pagination.custom-limited') }}
    </div>
</div>

