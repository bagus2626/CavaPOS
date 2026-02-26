@extends('layouts.staff')
@section('title', 'Stock Movement Detail')

@section('content')
    @php $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager'); @endphp

    <div class="modern-container">
        <div class="container-modern">

            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">Stock Movement Detail</h1>
                    <p class="page-subtitle">Detailed movement history for this stock item</p>
                </div>
                <a href="{{ route('employee.' . $empRole . '.report.stocks.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back
                </a>
            </div>

            {{-- Stock Info --}}
            <div class="modern-card">
                <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-group">
                                <label class="info-label">Stock Name</label>
                                <p class="info-value">{{ $stockItem->stock_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <label class="info-label">Stock Code</label>
                                <p class="info-value mono">{{ $stockItem->stock_code }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <label class="info-label">Period</label>
                                <p class="info-value">
                                    {{ request('month') ? \Carbon\Carbon::parse(request('month'))->translatedFormat('F Y') : 'All Time' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Export Button --}}
            <div class="d-flex justify-content-end my-3">
                <a href="{{ route('employee.' . $empRole . '.report.stocks.movement.export', array_merge(['stock' => $stockItem->stock_code], request()->all())) }}"
                    class="btn-modern btn-sm-modern btn-success-modern">
                    <span class="material-symbols-outlined">download</span>
                    Export Excel
                </a>
            </div>

            {{-- Table --}}
            <div class="modern-card">
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">#</th>
                                <th>Date & Time</th>
                                <th>Category</th>
                                <th class="text-center">Quantity</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($movements as $index => $item)
                                @php
                                    $isOut = optional($item->movement)->type === 'out';
                                    $rawQty = abs($item->quantity);
                                    $conversion = optional($item->stock->displayUnit)->base_unit_conversion_value ?? 1;
                                    $displayQty = $rawQty / $conversion;
                                    $qtyClass = $isOut ? 'text-danger' : 'text-success';
                                    $qtySign = $isOut ? '-' : '+';
                                @endphp
                                <tr class="table-row">
                                    <td class="text-center text-muted">{{ $movements->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-600">{{ optional($item->movement->created_at)->format('d M Y') }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ optional($item->movement->created_at)->format('H:i:s') }}</div>
                                    </td>
                                    <td>
                                        <span class="badge-modern {{ $isOut ? 'badge-danger' : 'badge-success' }}">
                                            {{ Str::title(str_replace('_', ' ', $item->movement->category)) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-600 {{ $qtyClass }}">
                                            {{ $qtySign }}{{ number_format($displayQty, 2) }}
                                        </span>
                                        <span
                                            class="text-muted small">{{ optional($item->stock->displayUnit)->unit_name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 250px;"
                                            title="{{ $item->movement->notes }}">
                                            {{ $item->movement->notes ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="table-empty-state">
                                            <span class="material-symbols-outlined">inventory_2</span>
                                            <h4>No movement records found</h4>
                                            <p>No movement data for this stock item.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($movements->hasPages())
                    <div class="table-pagination">
                        <div class="text-muted small">
                            Showing {{ $movements->firstItem() }} to {{ $movements->lastItem() }} of
                            {{ $movements->total() }} entries
                        </div>
                        <div>{{ $movements->links() }}</div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @push('styles')
        <style>
            .info-group {
                margin-bottom: 0;
            }

            .info-label {
                font-size: .875rem;
                color: var(--text-muted, #6c757d);
                margin-bottom: .25rem;
                display: block;
                font-weight: 500;
            }

            .info-value {
                font-size: 1rem;
                font-weight: 600;
                color: var(--text-primary, #212529);
                margin: 0;
            }

            @media (max-width: 768px) {
                .info-group {
                    margin-bottom: 1rem;
                }
            }
        </style>
    @endpush
@endsection
