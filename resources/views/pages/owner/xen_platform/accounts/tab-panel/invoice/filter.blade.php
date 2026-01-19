<form id="invoices-filter-form">
    <div class="row">
        
        {{-- 1. ADVANCED FILTER (Dropdown: Status & Client Type) --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <div class="dropdown">
                    <button class="form-control-modern d-flex justify-content-between align-items-center text-left" 
                            type="button" 
                            id="dropdownFilterInvoice" 
                            data-toggle="dropdown" 
                            aria-haspopup="true" 
                            aria-expanded="false"
                            style="background: #fff; border: 1px solid #e2e8f0; width: 100%;">
                        <span>
                            {{ __('messages.owner.xen_platform.accounts.filter') }} 
                            (<span id="invoice-filter-count">0</span>)
                        </span>
                        <span class="material-symbols-outlined" style="font-size: 1.2rem;">filter_list</span>
                    </button>

                    <div class="dropdown-menu p-3 shadow-lg" aria-labelledby="dropdownFilterInvoice" style="width: max-content; min-width: 100%; max-width: 90vw;">
                        <div class="d-flex justify-content-between mb-2">
                            <button class="btn btn-sm btn-outline-secondary" id="clear-all-invoice-filters" type="button">{{ __('messages.owner.xen_platform.accounts.clear_all') }}</button>
                            <button class="btn btn-sm btn-primary" id="close-filter-invoice-dropdown-btn" type="button" data-toggle="dropdown">{{ __('messages.owner.xen_platform.accounts.close') }}</button>
                        </div>

                        <div class="row" id="popup-invoice-filter-options">
                            {{-- Kolom 1: STATUS --}}
                            <div class="col-md-6 px-2 mb-3" style="min-width: 150px;">
                                <h6 class="font-weight-bold border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.75rem; color: #64748b;">{{ __('messages.owner.xen_platform.accounts.status_b') }}</h6>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-status-pending" data-filter-group="statuses" value="PENDING"><label class="custom-control-label" for="inv-status-pending">Pending</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-status-paid" data-filter-group="statuses" value="PAID"><label class="custom-control-label" for="inv-status-paid">Paid</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-status-settled" data-filter-group="statuses" value="SETTLED"><label class="custom-control-label" for="inv-status-settled">Settled</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input invoice-filter-checkbox" id="inv-status-expired" data-filter-group="statuses" value="EXPIRED"><label class="custom-control-label" for="inv-status-expired">Expired</label></div>
                            </div>

                            {{-- Kolom 2: CLIENT TYPE --}}
                            <div class="col-md-6 px-2 mb-3" style="min-width: 150px;">
                                <h6 class="font-weight-bold border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.75rem; color: #64748b;">{{ __('messages.owner.xen_platform.accounts.client_type_b') }}</h6>
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
            </div>
        </div>

        {{-- 2. DATE TYPE SELECTOR --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <div class="select-wrapper">
                    <select id="date_type_select" class="form-control-modern" onchange="document.getElementById('current-date-key').value = this.value">
                        <option value="created">{{ __('messages.owner.xen_platform.accounts.created_date') }}</option>
                        <option value="paid">{{ __('messages.owner.xen_platform.accounts.paid_date') }}</option>
                        <option value="expired">{{ __('messages.owner.xen_platform.accounts.expired_date') }}</option>
                    </select>
                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                </div>
            </div>
        </div>

        {{-- 3. DATE RANGE INPUT --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <div class="input-wrapper">
                    <span class="input-icon">
                        <span class="material-symbols-outlined">calendar_today</span>
                    </span>
                    <input type="text" 
                        class="form-control-modern with-icon daterange-invoice"
                        name="date_invoice" 
                        id="daterange-invoice"
                        placeholder="{{ __('messages.owner.xen_platform.accounts.select_date_range') }}">
                </div>
            </div>
        </div>

        {{-- 4. SEARCH TYPE SELECTOR (External ID - Dipisah) --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <div class="select-wrapper">
                    <select id="search_invoice_type_select" class="form-control-modern" onchange="document.getElementById('current-invoice-search-key').value = this.value">
                        <option value="external_id">{{ __('messages.owner.xen_platform.accounts.external_id') }}</option>
                    </select>
                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                </div>
            </div>
        </div>

        {{-- 5. SEARCH INPUT (Input Keyword - Dipisah) --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <div class="input-wrapper">
                    <span class="input-icon">
                        <span class="material-symbols-outlined">search</span>
                    </span>
                    <input type="text" 
                        class="form-control-modern with-icon" 
                        id="global-invoice-search-input" 
                        placeholder="{{ __('messages.owner.xen_platform.accounts.search_external_id') }}">
                </div>
            </div>
        </div>

        {{-- Hidden Inputs (Wajib ada untuk JS) --}}
        <input type="hidden" id="current-date-key" value="created">
        <input type="hidden" id="current-invoice-search-key" value="external_id">
        <input type="hidden" id="invoice-filter-limit" value="10">

        {{-- 6. BUTTON ACTIONS --}}
        <div class="col-12 col-md-3 col-lg-3 d-flex align-items-end">
            <div class="form-group" style="display: flex; gap: 0.5rem; width: 100%;">
                <button type="button" id="apply-invoice-filter-btn" class="btn-modern btn-primary-modern" style="flex: 1;">
                  {{ __('messages.owner.xen_platform.accounts.apply_filter') }}
                </button>
            </div>
        </div>
        <div class="col-12 col-md-3 col-lg-3 d-flex align-items-end">
            <div class="form-group" style="display: flex; gap: 0.5rem; width: 100%;">
                <button type="button" id="reset-invoice-filter-btn" class="btn-modern btn-secondary-modern" style="flex: 1;" title="Reset semua filter">
          {{ __('messages.owner.xen_platform.accounts.reset') }}
                </button>
            </div>
        </div>

    </div>
</form>