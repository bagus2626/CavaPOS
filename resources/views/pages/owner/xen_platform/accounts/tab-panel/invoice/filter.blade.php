<form id="invoices-filter-form">
   <div class="d-flex align-items-center flex-wrap">
            {{-- 1. Dropdown Filter (Status, Client Type, Payment Channels) --}}
            <div class="dropdown mr-2">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownFilterInvoice" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ __('messages.owner.xen_platform.accounts.filter') }} (<span id="invoice-filter-count">0</span>)
                </button>

                <div class="dropdown-menu p-3" aria-labelledby="dropdownFilterInvoice" style="max-width: 95vw; width: 600px;">
                    <div class="d-flex justify-content-between mb-2">
                        <button class="btn btn-sm btn-outline-secondary" id="clear-all-invoice-filters" type="button">{{ __('messages.owner.xen_platform.accounts.clear_all') }}</button>
                        <button class="btn btn-sm btn-primary" id="close-filter-invoice-dropdown-btn" type="button" data-toggle="dropdown">{{ __('messages.owner.xen_platform.accounts.close') }}</button>
                    </div>

                    <div class="row" id="popup-invoice-filter-options">
                        {{-- Kolom 1: STATUS --}}
                        <div class="col-6 col-lg-4 px-2">
                            <h6 class="font-weight-bold border-bottom pb-1">{{ __('messages.owner.xen_platform.accounts.status_b') }}</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-status-pending" data-filter-group="statuses" value="PENDING"><label class="custom-control-label" for="inv-status-pending">Pending</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-status-paid" data-filter-group="statuses" value="PAID"><label class="custom-control-label" for="inv-status-paid">Paid</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-status-settled" data-filter-group="statuses" value="SETTLED"><label class="custom-control-label" for="inv-status-settled">Settled</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-status-expired" data-filter-group="statuses" value="EXPIRED"><label class="custom-control-label" for="inv-status-expired">Expired</label></div>
                        </div>

                        {{-- Kolom 2: CLIENT TYPE --}}
                        <div class="col-6 col-lg-4 px-2">
                            <h6 class="font-weight-bold border-bottom pb-1">{{ __('messages.owner.xen_platform.accounts.client_type_b') }}</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-type-api" data-filter-group="client_types" value="API_GATEWAY"><label class="custom-control-label" for="inv-type-api">API Gateway</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-type-dashboard" data-filter-group="client_types" value="DASHBOARD"><label class="custom-control-label" for="inv-type-dashboard">Dashboard</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-type-integration" data-filter-group="client_types" value="INTEGRATION"><label class="custom-control-label" for="inv-type-integration">Integration</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-type-on-demand" data-filter-group="client_types" value="ON_DEMAND"><label class="custom-control-label" for="inv-type-on-demand">On Demand</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-type-recurring" data-filter-group="client_types" value="RECURRING"><label class="custom-control-label" for="inv-type-recurring">Recurring</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-type-mobile" data-filter-group="client_types" value="MOBILE"><label class="custom-control-label" for="inv-type-mobile">Mobile</label></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Date Range Picker --}}
            <div class="mr-2" style="min-width: 400px;">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="date-type-toggle">{{ __('messages.owner.xen_platform.accounts.created_date') }}</button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item date-type-select" href="#" data-date-key="created">{{ __('messages.owner.xen_platform.accounts.created_date') }}</a>
                            <a class="dropdown-item date-type-select" href="#" data-date-key="paid">{{ __('messages.owner.xen_platform.accounts.paid_date') }}</a>
                            <a class="dropdown-item date-type-select" href="#" data-date-key="expired">{{ __('messages.owner.xen_platform.accounts.expired_date') }}</a>
                        </div>
                    </div>
                    <input type="text" class="form-control daterange-invoice"
                           name="date_invoice" id="daterange-invoice"
                           placeholder="{{ __('messages.owner.xen_platform.accounts.select_date_range') }}">
                    <div class="form-control-position">
                        <i class="bx bx-calendar-check"></i>
                    </div>
                </div>
                <input type="hidden" id="current-date-key" value="created">
            </div>

            {{-- 3. Search Box --}}
            <div class="input-group mr-2 flex-grow-1" style="max-width: 500px;">
                <div class="input-group-prepend">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="search-invoice-type-toggle">{{ __('messages.owner.xen_platform.accounts.external_id') }}</button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item search-invoice-type-select" href="#" data-search-key="external_id">{{ __('messages.owner.xen_platform.accounts.external_id') }}</a>
                    </div>
                </div>
                <input type="text" class="form-control" placeholder="{{ __('messages.owner.xen_platform.accounts.search_external_id') }}" aria-label="Search" id="global-invoice-search-input">
                <input type="hidden" id="current-invoice-search-key" value="external_id">
            </div>

            {{-- 4. Tombol Aksi --}}
            <div class="d-flex w-md-auto">
                <button id="apply-invoice-filter-btn" class="btn btn-primary mr-1 flex-grow-1" type="button">
                    <i class="fas fa-filter"></i> {{ __('messages.owner.xen_platform.accounts.apply_filter') }}
                </button>
                <button id="reset-invoice-filter-btn" class="btn btn-secondary" type="button" title="Reset semua filter">
                    <i class="fas fa-sync-alt"></i> {{ __('messages.owner.xen_platform.accounts.reset') }}
                </button>
            </div>

            <input type="hidden" id="invoice-filter-limit" value="10">
        </div>
</form>