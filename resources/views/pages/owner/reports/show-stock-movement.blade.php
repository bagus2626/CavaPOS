@extends('layouts.owner')

@section('title', __('messages.owner.stock_report.movement.title'))
@section('page_title', __('messages.owner.stock_report.movement.page_title'))

@section('content')
    <section class="content">
        <div class="container-fluid">
            {{-- Tombol Kembali --}}
            <a href="{{ route('owner.user-owner.report.stocks.index', $currentFilters) }}" class="btn btn-primary mb-3">
                <i class="fas fa-arrow-left mr-2"></i> {{ __('messages.owner.stock_report.movement.back_button') }}
            </a>

            {{-- Header & Info Stok --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <p class="mb-0 text-muted small">
                                {{ __('messages.owner.stock_report.movement.info.stock_name') }} :</p>
                            <p class="fw-bold m-0">{{ $stockItem->stock_name }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-0 text-muted small">
                                {{ __('messages.owner.stock_report.movement.info.stock_code') }} :</p>
                            <p class="fw-bold m-0">{{ $stockItem->stock_code }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-0 text-muted small">{{ __('messages.owner.stock_report.movement.info.location') }}
                                :</p>
                            <p class="fw-bold m-0">
                                @if(request('partner_id') === 'owner' || request('partner_id') === null)
                                    {{ __('messages.owner.stock_report.filter.owner_warehouse') }}
                                @else
                                    {{ $stockItem->partner->name ?? 'N/A' }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-0 text-muted small">{{ __('messages.owner.stock_report.movement.info.period') }} :
                            </p>
                            <p class="fw-bold m-0">
                                {{ request('month') ? Carbon\Carbon::parse(request('month'))->translatedFormat('F Y') : __('messages.owner.stock_report.movement.info.all_time') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Detail Pergerakan Stok --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-0 small text-uppercase fw-bold">
                                {{ __('messages.owner.stock_report.movement.table.title') }}</h5>
                        </div>
                        <div class="col-md-4 text-right">
                            {{-- TAMBAHAN BARU: Tombol Export Excel --}}
                            <a href="{{ route('owner.user-owner.report.stocks.movement.export', array_merge(['stock' => $stockItem->stock_code], request()->all())) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i>
                                {{ __('messages.owner.stock_report.movement.table.export_excel') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-uppercase small">
                                    <th>{{ __('messages.owner.stock_report.movement.table.number') }}</th>
                                    <th>{{ __('messages.owner.stock_report.movement.table.date_time') }}</th>
                                    <th>{{ __('messages.owner.stock_report.movement.table.category') }}</th>
                                    <th>{{ __('messages.owner.stock_report.movement.table.location') }}</th>
                                    <th class="text-center">{{ __('messages.owner.stock_report.movement.table.quantity') }}
                                    </th>
                                    <th>{{ __('messages.owner.stock_report.movement.table.notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($movements as $index => $item)
                                    @php
                                        // Ambil kuantitas mentah (selalu positif)
                                        $rawQty = $item->quantity;

                                        // FIX: Tentukan arah berdasarkan tipe di Model StockMovement
                                        $isOut = optional($item->movement)->type === 'out'; // true jika tipe='out'
                                        $displayQty = abs($rawQty); // Nilai kuantitas adalah nilai mentah

                                        // Konversi ke display unit
                                        $conversion = optional($item->stock->displayUnit)->base_unit_conversion_value ?? 1;
                                        $displayQty = $displayQty / $conversion;

                                        $qtyClass = $isOut ? 'text-danger' : 'text-success';
                                        $qtySign = $isOut ? '-' : '+';
                                    @endphp
                                    <tr>
                                        <td>{{ $movements->firstItem() + $index }}</td>
                                        <td>
                                            <div class="fw-600">{{ optional($item->movement->created_at)->format('d M Y') }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ optional($item->movement->created_at)->format('H:i:s') }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $isOut ? 'bg-danger' : 'bg-success' }} small">
                                                {{ Str::title(str_replace('_', ' ', $item->movement->category)) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ optional($item->movement->partner)->name ?? __('messages.owner.stock_report.filter.owner_warehouse') }}
                                        </td>
                                        <td class="text-center fw-bold {{ $qtyClass }}">
                                            {{ $qtySign }}{{ number_format($displayQty, 2) }}
                                            <span
                                                class="text-muted small">{{ optional($item->stock->displayUnit)->unit_name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            {{ $item->movement->notes ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            {{ __('messages.owner.stock_report.movement.table.no_data') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                @if($movements->hasPages())
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                {{ __('messages.owner.stock_report.movement.table.showing') }}
                                {{ $movements->firstItem() ?? 0 }} {{ __('messages.owner.stock_report.movement.table.to') }}
                                {{ $movements->lastItem() ?? 0 }} {{ __('messages.owner.stock_report.movement.table.of') }}
                                {{ $movements->total() }} {{ __('messages.owner.stock_report.movement.table.entries') }}
                            </div>
                            <div>
                                {{ $movements->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                @endif

            </div>
    </section>
@endsection