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
            data-business-id="{{ optional($item->subAccount)->xendit_user_id }}"
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
                        'REQUESTED'   => 'badge-light-info',
                        'REVERSED'    => 'badge-light-primary',
                        'ACCEPTED'    => 'badge-light-warning',
                        'SUCCEEDED'   => 'badge-light-success',
                        'FAILED'      => 'badge-light-danger',
                        'CANCELLED'   => 'badge-light-secondary',
                        'UNKNOWN'     => 'badge-light-secondary',
                    ];
                @endphp
                <div class="badge {{ $badgeClasses[$status] ?? 'badge-light-secondary' }} badge-pill ml-1">{{ $status }}</div>
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

<script>
    $(document).on('click', '.disbursement-clickable-row', function (e) {
        const $row = $(this);

        if ($(e.target).closest('.dropdown, .dropdown-toggle, a').length > 0) {
            return;
        }

        const businessId = $row.data('business-id');
        console.log(businessId)
        const payoutId = $row.data('payout-id');

        if (businessId && payoutId) {
            $row.addClass('loading');

            const colCount = $row.find('td').length;

            $row.html(`
                    <td colspan="${colCount}" class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-2 overlay">
                            <div class="spinner-border" role="status" style="width:1.5rem; height:1.5rem;"></div>
                            <span class="fw-medium ml-1">Memuat detail transaksi...</span>
                        </div>
                    </td>
                `);

            setTimeout(() => {
                window.location.href = `/admin/send-payment/payout/${businessId}/detail/${payoutId}`;
            }, 250);
        } else {
            alert('Missing business_id or payout_id for row click.')
        }
    });
</script>

