<div class="table-responsive">
    <table class="table table-striped table-hover" style="width:100%" id="xendit-invoice-table">
        <thead class="thead-dark">
        <tr>
            <th>No</th>
            <th>Date(GMT +7)</th>
            <th>External ID</th>
            <th>Payer email</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @forelse($data['invoices'] as $index => $item)
            <tr class="invoice-clickable-row"
                data-invoice-id="{{ $item['id'] ?? '' }}"
                data-business-id="{{ $item['user_id'] ?? '' }}"
                style="cursor: pointer;">
                <td></td>
                <td>
                    <span class="font-weight-bold">{{ \Carbon\Carbon::parse($item['created'])->timezone('Asia/Jakarta')->format('d M Y') }}</span><br>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($item['created'])->timezone('Asia/Jakarta')->format('h:i A') }}</small>
                </td>
                <td>{{$item['external_id']}}</td>
                <td>{{$item['customer']['email'] ?? '-'}}</td>
                <td>{{$item['description']}}</td>
                <td>{{ $item['currency'] }} {{ number_format($item['amount']) }}</td>
                <td>
                    @switch($item['status'])
                        @case('PENDING')
                            <span class="badge bg-warning badge-pill">{{ $item['status'] }}</span>
                            @break

                        @case('PAID')
                            <span class="badge bg-info badge-pill">{{ $item['status'] }}</span>
                            @break

                        @case('SETTLED')
                            <span class="badge bg-success badge-pill">{{ $item['status'] }}</span>
                            @break

                        @case('EXPIRED')
                            <span class="badge bg-secondary badge-pill">{{ $item['status'] }}</span>
                            @break

                        @default
                            <span class="badge bg-dark badge-pill">{{ $item['status'] ?? 'UNKNOWN' }}</span>
                    @endswitch
                </td>
                <td class="text-center">
                    <div class="dropdown">
                        <span class="fas fa-ellipsis-v fa-lg font-medium-3 nav-hide-arrow cursor-pointer"
                              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item copy-btn" data-copy-value="{{ $item['external_id'] ?? '' }}">
                                <i class="bx bx-copy-alt mr-1"></i> Copy Reference
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">
                    <i class="fas fa-info-circle"></i> Tidak ada data invoice yang ditemukan.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@php
    $meta = $data['meta'] ?? [];
    $afterId = $meta['after_id'] ?? null;
    $limit = $meta['limit'] ?? 10;
    $count = count($data['invoices'] ?? []);
    $isDisabled =  empty($afterId) || $count < $limit;
@endphp

<div id="xendit-invoice-pagination">
    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center border-top pt-1">
        <nav aria-label="Navigasi halaman transaksi">
            <ul class="pagination mb-0 pagination">
                <li class="page-item {{ $isDisabled ? 'disabled' : '' }} mt-2 mb-2">
                    <a class="page-link fw-medium rounded-pill" href="#"
                       data-cursor="{{ $afterId }}"
                       data-direction="after">
                        <i class="fas fa-arrow-down mr-1"></i> Load More
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
