@extends('layouts.owner')

@section('title', __('messages.owner.stock_report.title'))
@section('page_title', __('messages.owner.stock_report.page_title'))

@section('content')
    <section class="content">
        <div class="container-fluid">

            <!-- FILTER SECTION -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('owner.user-owner.report.stocks.index') }}">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label>{{ __('messages.owner.stock_report.filter.stock_type') }}</label>
                                    <select name="stock_type" class="form-control">
                                        <option value="">{{ __('messages.owner.stock_report.filter.all_types') }}</option>
                                        <option value="direct" {{ request('stock_type') == 'direct' ? 'selected' : '' }}>{{ __('messages.owner.stock_report.filter.direct') }}</option>
                                        <option value="linked" {{ request('stock_type') == 'linked' ? 'selected' : '' }}>{{ __('messages.owner.stock_report.filter.linked') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label>{{ __('messages.owner.stock_report.filter.partner') }}</label>
                                    <select name="partner_id" class="form-control">
                                        
                                        <option value="owner" {{ request('partner_id', 'owner') == 'owner' ? 'selected' : '' }}>
                                            {{ __('messages.owner.stock_report.filter.owner_warehouse') }}
                                        </option>
                                        
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}" {{ request('partner_id') == $partner->id ? 'selected' : '' }}>
                                                {{ $partner->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label>{{ __('messages.owner.stock_report.filter.month') }}</label>
                                    <input type="month" name="month" class="form-control"
                                        value="{{ request('month') }}" max="{{ date('Y-m') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        {{ __('messages.owner.stock_report.filter.apply_filter') }}
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <!-- STOCK DETAIL TABLE -->
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-4 col-12 mb-2 mb-md-0">
                            <h5 class="mb-0 small text-uppercase fw-bold">{{ __('messages.owner.stock_report.table.detail_stock') }}</h5>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="position-relative" style="max-width: 300px;">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                    </div>
                                    <input type="text" 
                                           id="searchInput" 
                                           class="form-control" 
                                           placeholder="{{ __('messages.owner.stock_report.table.search_placeholder') }}"
                                           style="padding-right: 5px;">
                                    <button class="btn btn-link position-absolute" 
                                            type="button" 
                                            id="clearSearch" 
                                            style="display: none; right: 5px; top: 50%; transform: translateY(-50%); z-index: 10; padding: 0; width: 20px; height: 20px; text-decoration: none; color: #6c757d;">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 justify-content-md-end d-flex">
                        <a href="{{ route('owner.user-owner.report.stocks.export', request()->all()) }}" 
                        class="btn btn-success btn-sm mr-2">
                            <i class="fas fa-file-excel"></i> {{ __('messages.owner.stock_report.table.export_excel') }}
                        </a>                            
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="text-uppercase small">
                                    <th>{{ __('messages.owner.stock_report.table.number') }}</th>
                                    <th>{{ __('messages.owner.stock_report.table.stock_name_code') }}</th>
                                    <th>{{ __('messages.owner.stock_report.table.location') }}</th>
                                    <th class="text-end text-success">{{ __('messages.owner.stock_report.table.total_in') }}</th>
                                    <th class="text-end text-danger">{{ __('messages.owner.stock_report.table.total_out') }}</th>
                                    <th class="text-nowrap">{{ __('messages.owner.stock_report.table.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stocks as $index => $stock)
                                    <tr class="stock-row"
                                        data-stock-name="{{ strtolower($stock->stock_name) }}"
                                        data-stock-code="{{ strtolower($stock->stock_code) }}"
                                        data-location="{{ strtolower($stock->partner->name ?? __('messages.owner.stock_report.filter.owner_warehouse')) }}">
                                        <td>{{ $stocks->firstItem() + $index }}</td>
                                        <td class="fw-bold">
                                            {{ $stock->stock_name }}
                                            <br><span class="text-muted small">{{ $stock->stock_code }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $stock->partner->name ?? __('messages.owner.stock_report.filter.owner_warehouse') }}
                                            </span>
                                        </td>
                                        
                                        {{-- IN/OUT --}}
                                        <td class="text-end fw-bold text-success">
                                            {{ number_format($stock->lifetime_in, 2) }}
                                            <span class="text-muted small">{{ $stock->displayUnit->unit_name ?? 'N/A' }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-danger">
                                            {{ number_format($stock->lifetime_out, 2) }}
                                            <span class="text-muted small">{{ $stock->displayUnit->unit_name ?? 'N/A' }}</span>
                                        </td>

                                        {{-- TOMBOL DETAIL --}}
                                     <td>
                                        <a href="{{ 
                                            route('owner.user-owner.report.stocks.movement', [
                                                'stock' => $stock->stock_code, 
                                                'partner_id' => request('partner_id'),
                                                'month' => request('month'),
                                                'stock_type' => request('stock_type'),
                                            ]) 
                                        }}" class="btn btn-sm btn-primary rounded-lg">{{ __('messages.owner.stock_report.table.detail_button') }}</a>
                                    </td>
                                    </tr>
                                @empty
                                    <tr id="emptyRow">
                                        <td colspan="6" class="text-center text-muted py-4">{{ __('messages.owner.stock_report.table.no_data') }}</td>
                                    </tr>
                                @endforelse
                                <tr id="noResultRow" style="display: none;">
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-search fa-2x mb-2"></i>
                                        <p class="mb-0">{{ __('messages.owner.stock_report.table.no_result') }} "<span id="searchKeyword"></span>"</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- PAGINATION --}}
                @if($stocks->hasPages())
                <div class="card-footer bg-white" id="paginationSection">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <span id="showingInfo">
                                {{ __('messages.owner.stock_report.table.showing') }} {{ $stocks->firstItem() ?? 0 }} {{ __('messages.owner.stock_report.table.to') }} {{ $stocks->lastItem() ?? 0 }} {{ __('messages.owner.stock_report.table.of') }} {{ $stocks->total() }} {{ __('messages.owner.stock_report.table.entries') }}
                            </span>
                            <span id="searchResultInfo" style="display: none;">
                                {{ __('messages.owner.stock_report.table.found') }} <span id="resultCount">0</span> {{ __('messages.owner.stock_report.table.results') }}
                            </span>
                        </div>
                        <div id="paginationLinks">
                            {{ $stocks->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;
    const $searchInput = $('#searchInput');
    const $clearButton = $('#clearSearch');
    const $stockRows = $('.stock-row');
    const $noResultRow = $('#noResultRow');
    const $emptyRow = $('#emptyRow');
    const $showingInfo = $('#showingInfo');
    const $searchResultInfo = $('#searchResultInfo');
    const $paginationLinks = $('#paginationLinks');

    function toggleClearButton() {
        $clearButton.toggle($searchInput.val().length > 0);
    }

    function performSearch() {
        const searchTerm = $searchInput.val().toLowerCase().trim();
        let visibleCount = 0;

        $stockRows.each(function() {
            const $row = $(this);
            const stockName = $row.data('stock-name');
            const stockCode = $row.data('stock-code');
            const location = $row.data('location');
            
            if (searchTerm === '' || 
                stockName.includes(searchTerm) || 
                stockCode.includes(searchTerm) || 
                location.includes(searchTerm)) {
                $row.show();
                visibleCount++;
            } else {
                $row.hide();
            }
        });

        if (searchTerm === '') {
            $noResultRow.hide();
            if ($emptyRow.length) $emptyRow.toggle($stockRows.length === 0);
            $showingInfo.show();
            $searchResultInfo.hide();
            $paginationLinks.show();
        } else {
            if (visibleCount > 0) {
                $noResultRow.hide();
                if ($emptyRow.length) $emptyRow.hide();
                $('#resultCount').text(visibleCount);
                $showingInfo.hide();
                $searchResultInfo.show();
            } else {
                $noResultRow.show();
                if ($emptyRow.length) $emptyRow.hide();
                $('#searchKeyword').text($searchInput.val());
                $showingInfo.hide();
                $searchResultInfo.hide();
            }
            $paginationLinks.hide();
        }
    }

    $searchInput.on('input', function() {
        toggleClearButton();
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 300);
    });

    $clearButton.on('click', function() {
        $searchInput.val('').focus();
        toggleClearButton();
        performSearch();
    });

    $searchInput.on('keypress', function(e) {
        if (e.which === 13) e.preventDefault();
    });

    toggleClearButton();
});
</script>
@endpush

@push('styles')
<style>
    #clearSearch:hover {
        color: #dc3545 !important;
    }
    
    /* Responsive untuk search box */
    @media (min-width: 768px) {
        .ml-auto {
            margin-left: auto !important;
        }
    }
    
    @media (max-width: 767px) {
        .card-header .position-relative {
            max-width: 100% !important;
        }
    }
</style>
@endpush