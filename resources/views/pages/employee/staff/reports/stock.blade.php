@extends('layouts.staff')
@section('title', __('messages.owner.stock_report.title'))

@section('content')
    @php $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager'); @endphp

    <div class="modern-container">
        <div class="container-modern">

            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.stock_report.page_title') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.stock_report.subtitle') }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="modern-card mb-4">
                <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                    <form method="GET" action="{{ route('employee.' . $empRole . '.report.stocks.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-modern">{{ __('messages.owner.stock_report.filter.stock_type') }}</label>
                                    <div class="select-wrapper">
                                        <select name="stock_type" class="form-control-modern">
                                            <option value="">{{ __('messages.owner.stock_report.filter.all_types') }}</option>
                                            <option value="direct" {{ request('stock_type') == 'direct' ? 'selected' : '' }}>
                                                {{ __('messages.owner.stock_report.filter.direct') }}
                                            </option>
                                            <option value="linked" {{ request('stock_type') == 'linked' ? 'selected' : '' }}>
                                                {{ __('messages.owner.stock_report.filter.linked') }}
                                            </option>
                                        </select>
                                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-modern">{{ __('messages.owner.stock_report.filter.month') }}</label>
                                    <input type="month" name="month" class="form-control-modern"
                                        value="{{ request('month') }}" max="{{ date('Y-m') }}">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="submit" class="btn-modern btn-primary-modern">
                                        {{ __('messages.owner.stock_report.filter.apply_filter') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Search & Export --}}
            <div class="modern-card mb-4">
                <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                    <div class="table-controls">
                        <div class="search-filter-group">
                            <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                                <span class="input-icon">
                                    <span class="material-symbols-outlined">search</span>
                                </span>
                                <input type="text" id="searchInput" class="form-control-modern with-icon"
                                    placeholder="{{ __('messages.owner.stock_report.table.search_placeholder') }}">
                            </div>
                        </div>
                        <a href="{{ route('employee.' . $empRole . '.report.stocks.export', request()->all()) }}"
                            class="btn-modern btn-sm-modern btn-success-modern">
                            <span class="material-symbols-outlined">download</span>
                            {{ __('messages.owner.stock_report.table.export_excel') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="modern-card">
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">{{ __('messages.owner.stock_report.table.number') }}</th>
                                <th>{{ __('messages.owner.stock_report.table.stock_name_code') }}</th>
                                <th class="text-end"><span class="text-success">{{ __('messages.owner.stock_report.table.total_in') }}</span></th>
                                <th class="text-end"><span class="text-danger">{{ __('messages.owner.stock_report.table.total_out') }}</span></th>
                                <th class="text-center" style="width: 120px;">{{ __('messages.owner.stock_report.table.action') }}</th>
                            </tr>
                        </thead>
                        <tbody id="stockTableBody">
                            @forelse ($stocks as $index => $stock)
                                <tr class="table-row stock-row" data-stock-name="{{ strtolower($stock->stock_name) }}"
                                    data-stock-code="{{ strtolower($stock->stock_code) }}">
                                    <td class="text-center text-muted">{{ $stocks->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-600">{{ $stock->stock_name }}</div>
                                        <div class="text-muted small mono">{{ $stock->stock_code }}</div>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-600 text-success">{{ number_format($stock->lifetime_in, 2) }}</span>
                                        <span class="text-muted small">{{ $stock->displayUnit->unit_name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-600 text-danger">{{ number_format($stock->lifetime_out, 2) }}</span>
                                        <span class="text-muted small">{{ $stock->displayUnit->unit_name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="table-actions">
                                            <a href="{{ route('employee.' . $empRole . '.report.stocks.movement', ['stock' => $stock->stock_code, 'month' => request('month'), 'stock_type' => request('stock_type')]) }}"
                                                class="btn-table-action view"
                                                title="{{ __('messages.owner.stock_report.table.detail_button') }}">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow">
                                    <td colspan="5" class="text-center">
                                        <div class="table-empty-state">
                                            <span class="material-symbols-outlined">inventory_2</span>
                                            <h4>{{ __('messages.owner.stock_report.table.no_data') }}</h4>
                                            <p>Try adjusting your filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <tr id="noResultRow" style="display: none;">
                                <td colspan="5" class="text-center">
                                    <div class="table-empty-state">
                                        <span class="material-symbols-outlined">search_off</span>
                                        <h4>{{ __('messages.owner.stock_report.table.no_result') }}</h4>
                                        <p>No results for "<span id="searchKeyword"></span>"</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if ($stocks->hasPages())
                    <div class="table-pagination" id="paginationSection">
                        <div class="text-muted small">
                            Showing {{ $stocks->firstItem() }} to {{ $stocks->lastItem() }} of {{ $stocks->total() }}
                            entries
                        </div>
                        <div id="paginationLinks">{{ $stocks->links() }}</div>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let searchTimeout;
            const $searchInput = $('#searchInput');
            const $stockRows = $('.stock-row');
            const $noResultRow = $('#noResultRow');
            const $emptyRow = $('#emptyRow');
            const $paginationLinks = $('#paginationLinks');

            $searchInput.on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    const term = $searchInput.val().toLowerCase().trim();
                    let count = 0;

                    $stockRows.each(function() {
                        const $row = $(this);
                        const match = $row.data('stock-name').includes(term) || $row.data(
                            'stock-code').includes(term);
                        $row.toggle(term === '' || match);
                        if (match || term === '') count++;
                    });

                    if (term === '') {
                        $noResultRow.hide();
                        $paginationLinks.show();
                    } else if (count === 0) {
                        $noResultRow.show();
                        $('#searchKeyword').text($searchInput.val());
                        $paginationLinks.hide();
                    } else {
                        $noResultRow.hide();
                        $paginationLinks.hide();
                    }
                }, 300);
            });

            $searchInput.on('keypress', function(e) {
                if (e.which === 13) e.preventDefault();
            });
        });
    </script>
@endpush