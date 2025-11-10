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
            <tr>
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
                            <span class="badge badge-light-warning badge-pill">{{ $item['status'] }}</span>
                            @break

                        @case('PAID')
                            <span class="badge badge-light-info badge-pill">{{ $item['status'] }}</span>
                            @break

                        @case('SETTLED')
                            <span class="badge badge-light-success badge-pill">{{ $item['status'] }}</span>
                            @break

                        @case('EXPIRED')
                            <span class="badge badge-light-secondary badge-pill">{{ $item['status'] }}</span>
                            @break

                        @default
                            <span class="badge badge-light-dark badge-pill">{{ $item['status'] ?? 'UNKNOWN' }}</span>
                    @endswitch
                </td>
                <td class="text-center">
                    <div class="dropdown">
                        <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer"
                              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#">
                                <i class="bx bx-copy-alt mr-1"></i> Copy Transaction ID
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="bx bx-copy-alt mr-1"></i> Copy Reference
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="bx bx-send mr-1"></i> Resend Webhook
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="bx bx-copy-alt mr-1"></i> Copy Invoice ID
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">
                    <i class="bx bx-info-circle"></i> Tidak ada data invoice yang ditemukan.
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

<div id="xendit-invoice-pagination" class="mt-1">
    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center border-top pt-1">
        <nav aria-label="Navigasi halaman transaksi">
            <ul class="pagination mb-0 pagination">
                <li class="page-item {{ $isDisabled ? 'disabled' : '' }} mr-1">
                    <a class="page-link fw-medium px-1 rounded-pill" href="#"
                       data-cursor="{{ $afterId }}"
                       data-direction="after">
                        More
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
