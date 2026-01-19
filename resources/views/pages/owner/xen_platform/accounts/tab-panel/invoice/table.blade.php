<div class="data-table-wrapper">
    <table class="data-table" id="xendit-invoice-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 60px;">#</th>
                <th>{{ __('messages.owner.xen_platform.accounts.date') }} (GMT +7)</th>
                <th>{{ __('messages.owner.xen_platform.accounts.external_id') }}</th>
                <th>{{ __('messages.owner.xen_platform.accounts.payer_email') }}</th>
                <th>{{ __('messages.owner.xen_platform.accounts.description') }}</th>
                <th>{{ __('messages.owner.xen_platform.accounts.amount') }}</th>
                <th>Status</th>
                <th class="text-center" style="width: 120px;">
                    {{ __('messages.owner.xen_platform.accounts.action') }}
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse($data['invoices'] as $index => $item)
                <tr class="table-row invoice-clickable-row" data-invoice-id="{{ $item['id'] ?? '' }}"
                    data-business-id="{{ $item['user_id'] ?? '' }}" style="cursor: pointer;">

                    <!-- No -->
                    <td class="text-center">{{ $index + 1 }}</td>

                    <!-- Created Date -->
                    <td>
                        @php
                            $createdDate = \Carbon\Carbon::parse($item['created'])->timezone('Asia/Jakarta');
                        @endphp
                        <div>
                            <span class="fw-600">{{ $createdDate->format('d M Y') }}</span><br>
                            <small class="text-muted">{{ $createdDate->format('H:i A') }}</small>
                        </div>
                    </td>

                    <!-- External ID -->
                    <td>
                        <code class="text-monospace small">
                                {{ $item['external_id'] ?? '-' }}
                            </code>
                    </td>

                    <!-- Payer Email -->
                    <td>
                        <span class="text-secondary">
                            {{ $item['customer']['email'] ?? '-' }}
                        </span>
                    </td>

                    <!-- Description -->
                    <td>
                        <span class="fw-500">{{ $item['description'] ?? '-' }}</span>
                    </td>

                    <!-- Amount -->
                    <td>
                        <span class="fw-600">
                            {{ $item['currency'] ?? 'IDR' }} {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}
                        </span>
                    </td>

                    <!-- Status -->
                    <td>
                        @php
                            $status = $item['status'] ?? 'UNKNOWN';
                            $badgeClasses = [
                                'PENDING' => 'badge-warning',
                                'PAID' => 'badge-info',
                                'SETTLED' => 'badge-success',
                                'EXPIRED' => 'badge-secondary',
                                'UNKNOWN' => 'badge-secondary',
                            ];
                        @endphp
                        <span class="badge-modern {{ $badgeClasses[$status] ?? 'badge-secondary' }}">
                            {{ $status }}
                        </span>
                    </td>

                    <!-- Action -->
                    <td class="text-center">
                        <div class="dropdown">
                            <span class="fas fa-ellipsis-v fa-lg font-medium-3 nav-hide-arrow cursor-pointer"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item copy-btn" data-copy-value="{{ $item['external_id'] ?? '' }}">
                                    <i class="bx bx-copy-alt mr-1"></i>
                                    {{ __('messages.owner.xen_platform.accounts.copy_reference') }}
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="table-empty-state">
                            <span class="material-symbols-outlined">receipt_long</span>
                            <h4>{{ __('messages.owner.xen_platform.accounts.no_invoice_found') }}</h4>
                        </div>
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
    $isDisabled = empty($afterId) || $count < $limit;
@endphp

<div id="xendit-invoice-pagination">
    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center border-top pt-1">
        <nav aria-label="Navigasi halaman transaksi">
            <ul class="pagination mb-0 pagination">
                <li class="page-item pagination-nav-btn {{ $isDisabled ? 'disabled' : '' }} mt-2 mb-2">
                    <a class="page-link fw-medium rounded-pill" href="#"
                        style="width: auto !important; height: auto !important;" data-cursor="{{ $afterId }}"
                        data-direction="after">
                        <i class="fas fa-arrow-down mr-1"></i>
                        {{ __('messages.owner.xen_platform.accounts.load_more') }}
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>