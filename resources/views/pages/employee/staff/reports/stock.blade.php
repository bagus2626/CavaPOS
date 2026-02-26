@extends('layouts.staff')
@section('title', 'Stock Report')

@section('content')
    @php $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager'); @endphp

    <div class="modern-container">
        <div class="container-modern">

            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">Stock Report</h1>
                    <p class="page-subtitle">Monitor stock movement in your outlet</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="modern-card mb-4">
                <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                    <form method="GET" action="{{ route('employee.' . $empRole . '.report.stocks.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-modern">Stock Type</label>
                                    <div class="select-wrapper">
                                        <select name="stock_type" class="form-control-modern">
                                            <option value="">All Types</option>
                                            <option value="direct"
                                                {{ request('stock_type') == 'direct' ? 'selected' : '' }}>Direct</option>
                                            <option value="linked"
                                                {{ request('stock_type') == 'linked' ? 'selected' : '' }}>Linked</option>
                                        </select>
                                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-modern">Month</label>
                                    <input type="month" name="month" class="form-control-modern"
                                        value="{{ request('month') }}" max="{{ date('Y-m') }}">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="submit" class="btn-modern btn-primary-modern">
                                        Apply Filter
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
                                    placeholder="Search stock name or code...">
                            </div>
                        </div>
                        <a href="{{ route('employee.' . $empRole . '.report.stocks.export', request()->all()) }}"
                            class="btn-modern btn-sm-modern btn-success-modern">
                            <span class="material-symbols-outlined">download</span>
                            Export Excel
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
                                <th class="text-center" style="width: 60px;">#</th>
                                <th>Stock Name / Code</th>
                                <th class="text-end"><span class="text-success">Total In</span></th>
                                <th class="text-end"><span class="text-danger">Total Out</span></th>
                                <th class="text-center" style="width: 120px;">Action</th>
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
                                        <span
                                            class="fw-600 text-success">{{ number_format($stock->lifetime_in, 2) }}</span>
                                        <span class="text-muted small">{{ $stock->displayUnit->unit_name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span
                                            class="fw-600 text-danger">{{ number_format($stock->lifetime_out, 2) }}</span>
                                        <span class="text-muted small">{{ $stock->displayUnit->unit_name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="table-actions">
                                            <a href="{{ route('employee.' . $empRole . '.report.stocks.movement', ['stock' => $stock->stock_code, 'month' => request('month'), 'stock_type' => request('stock_type')]) }}"
                                                class="btn-table-action view" title="View Movement">
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
                                            <h4>No stock data found</h4>
                                            <p>Try adjusting your filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <tr id="noResultRow" style="display: none;">
                                <td colspan="5" class="text-center">
                                    <div class="table-empty-state">
                                        <span class="material-symbols-outlined">search_off</span>
                                        <h4>No results found</h4>
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
