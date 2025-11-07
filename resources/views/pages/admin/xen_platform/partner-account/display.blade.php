<table class="table table-striped table-hover" style="width:100%" id="table-partner-account">
    <thead class="thead-dark">
    <tr>
        <th class="w-20p">Account Name</th>
        <th class="w-15p">Account ID</th>
        <th class="w-10p">Status</th>
        <th class="w-15p">Date Created (GMT+7)</th>
        <th class="w-10p">Cash Balance</th>
        <th class="w-3p">Action</th>
    </tr>
    </thead>
    <tbody>
    @php
        $colors = [
            'bg-primary', 'bg-secondary', 'bg-success', 'bg-danger',
            'bg-warning', 'bg-info', 'bg-dark',
        ];

        function getColor($string, $colors) {
            $hash = crc32($string);
            return $colors[$hash % count($colors)];
        }
    @endphp

    @forelse($subAccounts as $item)
        @php
            $businessName = $item['public_profile']['business_name'] ?? 'N/A';
            $randomColor = getColor($businessName, $colors);
            $status = $item['status'] ?? 'UNKNOWN';
        @endphp
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar {{ $randomColor }} mr-1">
                        <span class="avatar-content">
                            {{ strtoupper(substr($businessName, 0, 1)) }}
                        </span>
                    </div>
                    <div class="d-flex flex-column text-left">
                        <span class="font-weight-bold text-dark">{{ $businessName }}</span>
                        <small class="text-muted">{{ $item['email'] ?? '-' }}</small>
                    </div>
                </div>
            </td>
            <td>
                <span class="font-weight-bold text-dark">{{ $item['id'] ?? '-' }}</span><br>
                <small class="text-muted">{{ ucfirst(strtolower($item['type'] ?? '-')) }}</small>
            </td>
            <td class="text-center">
                @if($status === 'INVITED')
                    <span class="badge badge-light-info badge-pill">{{ $status }}</span>
                @elseif ($status === 'REGISTERED')
                    <span class="badge badge-light-warning badge-pill">{{ $status }}</span>
                @elseif ($status === 'AWAITING_DOCS')
                    <span class="badge badge-light-primary badge-pill">{{ $status }}</span>
                @elseif ($status === 'LIVE')
                    <span class="badge badge-light-success badge-pill">{{ $status }}</span>
                @elseif ($status === 'SUSPENDED')
                    <span class="badge badge-light-danger badge-pill">{{ $status }}</span>
                @else
                    <span class="badge badge-secondary badge-pill">{{ $status }}</span>
                @endif
            </td>
            <td>
                {{ \Carbon\Carbon::parse($item['created'] ?? now())->timezone('Asia/Jakarta')->format('d M Y h:i A') }}
            </td>
            <td>
                IDR {{ number_format($item['balance'] ?? 0) }}
            </td>
            <td class="text-center">
                <div class="dropdown">
                    <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="{{ route('admin.xen_platform.partner-account.information', ['accountId' => $item['id'] ?? 'none', 'tab' => 'profile']) }}">
                            <i class="bx bx-user mr-1"></i> Profile
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.xen_platform.partner-account.information', ['accountId' => $item['id'] ?? 'none', 'tab' => 'activity']) }}">
                            <i class="bx bx-list-check mr-1"></i> Activity
                        </a>
                    </div>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">
                <i class="bx bx-info-circle"></i> Tidak ada data transaksi yang ditemukan.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

@php
    $beforeId = $meta['before_id'] ?? null;
    $afterId = $meta['after_id'] ?? null;
    $hasMore = $meta['has_more'] ?? false;
    $limit = $meta['limit'] ?? 10;
    $count = count($subAccounts);
@endphp

<div id="xendit-account-pagination" class="mt-1">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center border-top pt-1">

        <div class="d-flex align-items-center text-muted small">
            <i class="bx bx-info-circle me-1"></i>
            <span>Showing <strong>{{ $count }}</strong> records (Limit: <strong>{{ $limit }}</strong>)</span>
        </div>

        <nav aria-label="Navigasi halaman transaksi">
            <ul class="pagination mb-0 pagination">
                <li class="page-item {{ empty($beforeId) ? 'disabled' : '' }} mr-1">
                    <a class="page-link fw-medium px-1 rounded-pill" href="#"
                       data-before="{{ $beforeId }}"
                       data-after=""
                       data-direction="before">
                        ← Previous
                    </a>
                </li>

                <li class="page-item {{ (empty($afterId) || !$hasMore || ($count < $limit)) ? 'disabled' : '' }}">
                    <a class="page-link fw-medium px-1 rounded-pill ms-2" href="#"
                       data-after="{{ $afterId }}"
                       data-before=""
                       data-direction="after">
                        Next →
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>

{{--<div class="d-flex justify-content-between mt-3" id="pagination-controls">--}}
{{--    <button class="btn btn-outline-secondary"--}}
{{--            data-before-id="{{ $meta['before_id'] ?? '' }}"--}}
{{--            data-after-id=""--}}
{{--            @if(empty($meta['before_id'])) disabled @endif>--}}
{{--        <i class="bx bx-chevron-left"></i> Sebelumnya--}}
{{--    </button>--}}

{{--    <span class="text-muted d-flex align-items-center">--}}
{{--        Menampilkan {{ count($subAccounts) }} akun (Limit: {{ $meta['limit'] }})--}}
{{--    </span>--}}

{{--    <button class="btn btn-outline-secondary"--}}
{{--            data-after-id="{{ $meta['after_id'] ?? '' }}"--}}
{{--            data-before-id=""--}}
{{--            @if(empty($meta['after_id'])) disabled @endif>--}}
{{--        Selanjutnya <i class="bx bx-chevron-right"></i>--}}
{{--    </button>--}}
{{--</div>--}}
