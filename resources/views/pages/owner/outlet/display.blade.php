@php
    use Illuminate\Support\Str;
@endphp

<div class="table-responsive owner-outlets-table">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('messages.owner.outlet.all_outlets.outlet_name') }}</th>
                <th>{{ __('messages.owner.outlet.all_outlets.username') }}</th>
                <th>{{ __('messages.owner.outlet.all_outlets.email') }}</th>
                <th>{{ __('messages.owner.outlet.all_outlets.logo') }}</th>
                <th>{{ __('messages.owner.outlet.all_outlets.background_picture') }}</th>
                <th>Status</th>
                <th>WiFi</th>
                <th class="text-nowrap">{{ __('messages.owner.outlet.all_outlets.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($outlets as $index => $outlet)
                <tr>
                    <td class="text-muted">{{ ($outlets->currentPage() - 1) * $outlets->perPage() + $index + 1 }}</td>

                    <td class="fw-600">{{ $outlet->name }}</td>

                    <td class="mono">{{ $outlet->username }}</td>

                    <td>
                        <a href="mailto:{{ $outlet->email }}" class="link-ink">{{ $outlet->email }}</a>
                    </td>

                    <td class="col-photo">
                        @php
                            $img = $outlet->logo
                                ? (Str::startsWith($outlet->logo, ['http://', 'https://'])
                                    ? $outlet->logo
                                    : asset('storage/' . $outlet->logo))
                                : null;
                        @endphp

                        @if ($img)
                            <a href="{{ $img }}" target="_blank" rel="noopener">
                                <img src="{{ $img }}" alt="{{ $outlet->name }}" class="avatar-48"
                                    loading="lazy">
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td class="col-photo">
                        @php
                            $img = $outlet->background_picture
                                ? (Str::startsWith($outlet->background_picture, ['http://', 'https://'])
                                    ? $outlet->background_picture
                                    : asset('storage/' . $outlet->background_picture))
                                : null;
                        @endphp

                        @if ($img)
                            <a href="{{ $img }}" target="_blank" rel="noopener">
                                <img src="{{ $img }}" alt="{{ $outlet->name }}" class="avatar-48"
                                    loading="lazy">
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td class="col-status-icons">
    <div class="status-list">
        <div class="status-row">
            <span class="status-label">Outlet</span>
            <span class="status-badge {{ (int) $outlet->is_active === 1 ? 'badge-on' : 'badge-off' }}">
                {{ (int) $outlet->is_active === 1 ? 'ON' : 'OFF' }}
            </span>
        </div>
        <div class="status-row">
            <span class="status-label">QRIS</span>
            <span class="status-badge {{ (int) $outlet->is_qr_active === 1 ? 'badge-on' : 'badge-off' }}">
                {{ (int) $outlet->is_qr_active === 1 ? 'ON' : 'OFF' }}
            </span>
        </div>
        <div class="status-row">
            <span class="status-label">Cashier</span>
            <span class="status-badge {{ (int) $outlet->is_cashier_active === 1 ? 'badge-on' : 'badge-off' }}">
                {{ (int) $outlet->is_cashier_active === 1 ? 'ON' : 'OFF' }}
            </span>
        </div>
    </div>
</td>

                    <td class="col-status">
                        @if ((int) $outlet->is_wifi_shown === 1)
                            <span class="badge badge-soft-success d-inline-flex align-items-center gap-1">
                                <i class="fas fa-wifi"></i> {{ __('messages.owner.outlet.all_outlets.active') }}
                            </span>
                        @else
                            <span class="badge badge-soft-secondary d-inline-flex align-items-center gap-1">
                                <i class="fas fa-wifi-slash"></i>
                                {{ __('messages.owner.outlet.all_outlets.inactive') }}
                            </span>
                        @endif
                    </td>

                    <td class="col-actions">
                        <div class="action-btns d-inline-flex">
                            <a href="{{ route('owner.user-owner.outlets.edit', $outlet->id) }}"
                                class="btn btn-outline-choco mr-1">{{ __('messages.owner.outlet.all_outlets.edit') }}</a>
                            <button onclick="deleteOutlet({{ $outlet->id }})"
                                class="btn btn-soft-danger">{{ __('messages.owner.outlet.all_outlets.delete') }}</button>
                        </div>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="pagination-wrapper mt-4">
        {{ $outlets->onEachSide(2)->links() }}
    </div>
</div>

<style>
    /* ===== Owner › Outlets List (page scope) ===== */
    .owner-outlets-table {
        --choco: #8c1000;
        --soft-choco: #c12814;
        --ink: #22272b;
        --radius: 12px;
        --shadow: 0 6px 20px rgba(0, 0, 0, .08);
    }

    .owner-outlets-table .table {
        border-collapse: separate;
        border-spacing: 0;
        background: #fff;
        overflow: hidden;
        border-radius: var(--radius);
    }

    .owner-outlets-table thead th {
        background: #fff;
        border-bottom: 2px solid #eef1f4 !important;
        color: #374151;
        font-weight: 700;
        white-space: nowrap;
    }

    .owner-outlets-table tbody td {
        vertical-align: middle;
    }

    .owner-outlets-table tbody tr {
        transition: background-color .12s ease;
    }

    .owner-outlets-table tbody tr:hover {
        background: rgba(140, 16, 0, .04);
    }

    /* Teks & util */
    .fw-600 {
        font-weight: 600;
    }

    .mono {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        color: #374151;
    }

    .link-ink {
        color: #374151;
        text-decoration: none;
    }

    .link-ink:hover {
        color: var(--choco);
    }

    /* Avatar/logo */
    .owner-outlets-table .avatar-48 {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 12px;
        border: 0;
        box-shadow: var(--shadow);
    }

    /* Status List - Clean & Simple */
.col-status-icons {
    min-width: 130px;
}

.status-list {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.status-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.status-label {
    font-size: 11px;
    color: #6b7280;
    font-weight: 500;
    min-width: 50px;
}

.status-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    text-align: center;
    min-width: 32px;
}

.badge-on {
    background: #d1fae5;
    color: #065f46;
}

.badge-off {
    background: #f3f4f6;
    color: #6b7280;
}


    /* Badges (soft) */
    .badge-soft-success {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        padding: .32rem .55rem;
        border-radius: 999px;
        font-weight: 600;
    }

    .badge-soft-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
        padding: .32rem .55rem;
        border-radius: 999px;
        font-weight: 600;
    }

    /* Actions */
    .owner-outlets-table .col-actions {
        white-space: nowrap;
    }

    .owner-outlets-table .btn-group-sm .btn {
        border-radius: 10px;
        padding: .28rem .6rem;
        min-width: 68px;
    }

    .btn-outline-choco {
        color: var(--choco);
        border-color: var(--choco);
        background: #fff;
    }

    .btn-outline-choco:hover {
        color: #fff;
        background: var(--choco);
        border-color: var(--choco);
    }

    .btn-soft-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .btn-soft-danger:hover {
        background: #fecaca;
        color: #7f1d1d;
        border-color: #fca5a5;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        padding: 1.5rem 0;
    }

    .pagination-wrapper .pagination {
        display: flex;
        gap: 0.5rem;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination-wrapper .page-item {
        display: inline-block;
    }

    .pagination-wrapper .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0.5rem 0.75rem;
        color: var(--choco);
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .pagination-wrapper .page-link:hover {
        background: rgba(140, 16, 0, 0.04);
        border-color: var(--choco);
        color: var(--choco);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    .pagination-wrapper .page-item.active .page-link {
        background: var(--choco);
        border-color: var(--choco);
        color: #fff;
        box-shadow: 0 2px 6px rgba(140, 16, 0, 0.25);
    }

    .pagination-wrapper .page-item.disabled .page-link {
        color: #9ca3af;
        background: #f9fafb;
        border-color: #e5e7eb;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .pagination-wrapper .page-item.disabled .page-link:hover {
        background: #f9fafb;
        border-color: #e5e7eb;
        transform: none;
        box-shadow: none;
    }

    /* Dots separator */
    .pagination-wrapper .page-item .page-link[aria-disabled="true"] {
        pointer-events: none;
        background: transparent;
        border: none;
        color: #9ca3af;
    }
</style>

@push('scripts')
    <script>
        function deleteOutlet(outletId) {
            Swal.fire({
                title: '{{ __('messages.owner.outlet.all_outlets.delete_confirmation_1') }}',
                text: '{{ __('messages.owner.outlet.all_outlets.delete_confirmation_2') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('messages.owner.outlet.all_outlets.delete_confirmation_3') }}',
                cancelButtonText: '{{ __('messages.owner.outlet.all_outlets.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/owner/user-owner/outlets/${outletId}`;
                    form.style.display = 'none';
                    form.innerHTML = `
        @csrf
        <input type="hidden" name="_method" value="DELETE">
      `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
    <script>
        function generateBarcode(tableId) {
            axios.get(`/partner/store/tables/generate-barcode/${tableId}`, {
                    responseType: 'blob'
                })
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', `barcode-table-${tableId}.png`);
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(error => {
                    console.error('Generate barcode failed:', error);
                });
        }
    </script>
@endpush