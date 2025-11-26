@extends('layouts.owner')

@section('title', 'Owner Dashboard')

@section('page_title', 'Dashboard Owner')

@section('content')
<!-- Main Content -->
<section class="content">
    <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row mb-3">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.owner.dashboard.total_users') }}</span>
                        <span class="info-box-number">{{ $data['total_accounts'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.owner.dashboard.total_orders') }} ({{ now()->year }})</span>
                        <span class="info-box-number">{{ $data['total_orders'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.owner.dashboard.total_sales') }} ({{ now()->year }})</span>
                        <span class="info-box-number">Rp. {{ number_format($data['orders_gross_income']) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-star"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.owner.dashboard.products') }}</span>
                        <span class="info-box-number">{{ $data['total_products'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- @yield('content') --}}

        <!-- Example content -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.owner.dashboard.recent_orders') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-modern">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.owner.dashboard.order_id') }}</th>
                                        <th>Outlet</th>
                                        <th>{{ __('messages.owner.dashboard.customer') }}</th>
                                        <th>Status</th>
                                        <th>{{ __('messages.owner.dashboard.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['last_orders'] as $order)
                                        <tr>
                                            <td><a href="#">{{ $order->booking_order_code ?? '-' }}</a></td>
                                            <td>{{ $order->partner_name ?? '-' }}</td>
                                            <td>{{ $order->customer_name ?? '-' }}</td>
                                            <td>
                                                <span class="badge 
                                                    @if ($order->order_status === 'PAID') badge-primary
                                                    @elseif ($order->order_status === 'SERVED') badge-success
                                                    @elseif ($order->order_status === 'PENDING') badge-warning
                                                    @elseif ($order->order_status === 'UNPAID') badge-warning
                                                    @elseif ($order->order_status === 'PROCESSED') badge-primary
                                                    @elseif ($order->order_status === 'CANCELLED') badge-danger
                                                    @else badge-secondary
                                                    @endif
                                                ">
                                                    {{ $order->order_status }}
                                                </span>
                                            </td>
                                            <td>Rp. {{ number_format($order->total_order_value ?? 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.owner.dashboard.my_timeline') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @php
                                use Illuminate\Support\Facades\Storage;
                                use Illuminate\Support\Str;
                            @endphp

                            {{-- Page pertama (5 pesan pertama) --}}
                            @include('pages.owner.dashboard.partials.timeline-items', ['messages' => $data['messages']])

                            {{-- Penutup timeline --}}
                            <div class="timeline-end">
                                <i class="fas fa-clock bg-gray"></i>
                            </div>

                            @if ($data['messages']->hasMorePages())
                                <div class="text-center mt-2">
                                    <button class="btn btn-sm btn-outline-primary"
                                            id="loadMoreTimeline"
                                            data-next-page="{{ $data['messages']->currentPage() + 1 }}"
                                            data-last-page="{{ $data['messages']->lastPage() }}">
                                        Load more
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- Popup carousel kalau ada popup messages --}}
@includeWhen(isset($data['popups']) && $data['popups']->isNotEmpty(), 'pages.owner.dashboard.partials.popup-carousel', [
    'popups' => $data['popups'],
])


<style>
    /* ====== INFO-BOX DASHBOARD (Total Users / Orders / Sales / Products) ====== */
    .info-box {
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid #eef1f4;
        background: #ffffff;
        padding-right: 0.85rem;
    }

    .info-box .info-box-icon {
        border-radius: 16px;
        width: 54px;
        height: 54px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, .08);
    }

    .info-box .info-box-text {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .info-box .info-box-number {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--ink);
    }

    /* warna icon pakai tone sedikit lebih lembut, tetap beda-beda */
    .info-box-icon.bg-info {
        background: linear-gradient(135deg, #0ea5e9, #0284c7) !important;
        color: #fff;
    }

    .info-box-icon.bg-danger {
        background: linear-gradient(135deg, var(--choco), var(--soft-choco)) !important;
        color: #fff;
    }

    .info-box-icon.bg-success {
        background: linear-gradient(135deg, #16a34a, #22c55e) !important;
        color: #fff;
    }

    .info-box-icon.bg-warning {
        background: linear-gradient(135deg, #f97316, #f59e0b) !important;
        color: #fff;
    }

    /* ====== TABEL RECENT ORDERS ====== */
    .table-modern {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-modern thead th {
        border-bottom: 1px solid #e5e7eb !important;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6b7280;
        background: #ffffff;
        font-weight: 600;
        padding-top: 0.55rem;
        padding-bottom: 0.55rem;
    }

    .table-modern tbody td {
        vertical-align: middle;
        font-size: 0.88rem;
        color: #374151;
        border-top: 1px solid #f3f4f6;
    }

    .table-modern tbody tr {
        transition: background-color .15s ease, transform .08s ease, box-shadow .15s ease;
    }

    .table-modern tbody tr:hover {
        background-color: #f9fafb;
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(0, 0, 0, .03);
    }

    .table-modern .order-link {
        color: var(--choco);
        font-weight: 600;
        text-decoration: none;
    }

    .table-modern .order-link:hover {
        text-decoration: underline;
    }

    /* ====== BADGE STATUS ORDER ====== */
    .badge-status {
        border-radius: 999px;
        padding: 0.25rem 0.65rem;
        font-size: 0.7rem;
        letter-spacing: .04em;
        text-transform: uppercase;
        font-weight: 600;
    }

    .card .badge-primary {
        background: linear-gradient(135deg, var(--choco), var(--soft-choco));
        border: none;
    }

    .card .badge-success {
        background: linear-gradient(135deg, #16a34a, #22c55e);
        border: none;
        color: #fff;
    }

    .card .badge-warning {
        background: linear-gradient(135deg, #facc15, #f97316);
        border: none;
        color: #111827;
    }

    .card .badge-danger {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        border: none;
        color: #fff;
    }

    .card .badge-secondary {
        background: #e5e7eb;
        color: #4b5563;
        border: none;
    }

    /* ====== KARTU DASHBOARD (Recent Orders & Timeline) ====== */
    .card {
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 0;
        overflow: hidden;
    }

    .card-header {
        border-bottom: 1px solid #eef1f4;
        background: #ffffff;
        padding: 0.75rem 1rem;
    }

    .card-header .card-title {
        font-weight: 600;
        color: var(--ink);
        font-size: 0.95rem;
    }

    .card-header .btn-tool {
        color: #9ca3af;
    }

    .card-header .btn-tool:hover {
        color: var(--choco);
    }

    /* ====== Timeline (yang sudah kamu pakai) – dibiarkan seperti sekarang ====== */
    .card .timeline {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }

    .timeline .time-label span {
        background: linear-gradient(135deg, var(--choco), var(--soft-choco)) !important;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .12);
    }

    .timeline .timeline-item {
        border-radius: var(--radius);
        border: 1px solid #eef1f4;
        background: #ffffff;
        box-shadow: var(--shadow);
        margin: 0.5rem 0 0.75rem 0;
        padding: 0.75rem 1rem;
        position: relative;
        overflow: hidden;
    }

    .timeline .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 12px;
        bottom: 12px;
        width: 3px;
        background: linear-gradient(180deg, rgba(140, 16, 0, 0.1), rgba(193, 40, 20, 0.4));
        border-radius: 999px;
    }

    .timeline .timeline-item .time {
        color: #6b7280;
        font-size: 0.8rem;
    }

    .timeline .timeline-item .time i {
        color: var(--choco);
        margin-right: 4px;
    }

    .timeline .timeline-header {
        font-size: 0.95rem;
        font-weight: 600;
        margin-top: 0.25rem;
        color: var(--ink);
    }

    .timeline .timeline-header a {
        color: var(--choco);
        text-decoration: none;
    }

    .timeline .timeline-header a:hover {
        text-decoration: underline;
    }

    .timeline .timeline-body {
        margin-top: 0.35rem;
        color: #4b5563;
        font-size: 0.9rem;
    }

    .timeline > div > i {
        box-shadow: 0 4px 12px rgba(0, 0, 0, .12);
    }

    .timeline i.bg-blue {
        background: linear-gradient(135deg, var(--choco), var(--soft-choco)) !important;
        color: #fff !important;
    }

    .timeline i.bg-green {
        background: linear-gradient(135deg, #059669, #10b981) !important;
        color: #fff !important;
    }

    .timeline i.bg-gray {
        background: #e5e7eb !important;
        color: #4b5563 !important;
    }

    .timeline-end i {
        background: #e5e7eb !important;
        color: #6b7280 !important;
        box-shadow: none !important;
    }

    .attachment-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        margin: 3px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--ink);
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
    }

    .attachment-badge:hover {
        background: #eef2ff;
        border-color: #c4b5fd;
        text-decoration: none;
    }

    .attachment-badge i {
        margin-right: 6px;
        font-size: 0.9rem;
        color: var(--choco);
    }

    .attachment-image-thumb {
        width: 120px;
        height: 120px;
        object-fit: cover;
        object-position: center;
        border-radius: 10px;
        margin: 4px 8px 4px 0;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .attachment-image-thumb:hover {
        transform: scale(1.03);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .timeline .timeline-footer {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 6px;
    }

    /* ===== Gambar dari Quill di dalam timeline-body ===== */
    .timeline .timeline-body img {
        max-width: 100%;      /* supaya tidak melebar melewati card */
        height: auto;         /* jaga rasio gambar */
        display: block;       /* biar bisa dikasih margin lebih rapi */
        border-radius: 10px;  /* selaras dengan style card & attachment */
        margin: 6px 0;        /* jarak atas-bawah */
    }

    /* Tambahan sedikit padding di kanan-kiri konten supaya tidak mepet */
    .timeline .timeline-item {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    /* Support alignment bawaan Quill */
    .timeline .timeline-body .ql-align-center img {
        margin-left: auto;
        margin-right: auto;
    }

    .timeline .timeline-body .ql-align-right img {
        margin-left: auto;
        margin-right: 0;
    }

</style>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('loadMoreTimeline');
    if (!btn) return;

    const timeline  = document.querySelector('.timeline');
    const endMarker = document.querySelector('.timeline-end');

    let nextPage = parseInt(btn.dataset.nextPage, 10);
    const lastPage = parseInt(btn.dataset.lastPage, 10);

    btn.addEventListener('click', function () {
        if (nextPage > lastPage) return;

        btn.disabled = true;
        btn.innerText = 'Loading...';

        const url = "{{ route('owner.user-owner.timeline.messages') }}" + "?page=" + nextPage;

        fetch(url)
            .then(res => res.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                // --- Cek label tanggal terakhir yang sudah ada di timeline ---
                const existingLabelSpans = timeline.querySelectorAll('.time-label span');
                const lastExistingLabelText = existingLabelSpans.length
                    ? existingLabelSpans[existingLabelSpans.length - 1].textContent.trim()
                    : null;

                // --- Cek label tanggal pertama di hasil baru ---
                const firstNewLabelDiv  = temp.querySelector('.time-label');
                const firstNewLabelSpan = firstNewLabelDiv ? firstNewLabelDiv.querySelector('span') : null;
                const firstNewLabelText = firstNewLabelSpan ? firstNewLabelSpan.textContent.trim() : null;

                // Kalau label terakhir di timeline == label pertama di hasil baru -> hapus label baru
                if (lastExistingLabelText && firstNewLabelText &&
                    lastExistingLabelText === firstNewLabelText &&
                    firstNewLabelDiv
                ) {
                    firstNewLabelDiv.remove();
                }

                // Ambil semua elemen anak langsung dari partial
                const items = Array.from(temp.children);

                // Sisipkan sebelum penutup timeline-end (jam abu-abu)
                items.forEach(el => {
                    timeline.insertBefore(el, endMarker);
                });

                nextPage++;

                if (nextPage > lastPage || items.length === 0) {
                    // Tidak ada page berikutnya -> hapus tombol
                    btn.remove();
                } else {
                    btn.dataset.nextPage = nextPage;
                    btn.disabled = false;
                    btn.innerText = 'Load more';
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerText = 'Load more';
            });
    });
});
</script>



@endsection


