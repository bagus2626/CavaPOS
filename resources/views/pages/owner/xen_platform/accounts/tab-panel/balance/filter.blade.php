<form id="balances-filter-form">
    <div class="row">
        
        {{-- 1. ADVANCED FILTER (Dropdown: Type & Payment Method) --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <div class="dropdown">
                    <button class="form-control-modern d-flex justify-content-between align-items-center text-left" 
                            type="button" 
                            id="dropdownFilterBalance" 
                            data-toggle="dropdown" 
                            aria-haspopup="true" 
                            aria-expanded="false"
                            style="background: #fff; border: 1px solid #e2e8f0; width: 100%;">
                        <span>
                            {{ __('messages.owner.xen_platform.accounts.filter') }} 
                            (<span id="balance-filter-count">0</span>)
                        </span>
                        <span class="material-symbols-outlined" style="font-size: 1.2rem;">filter_list</span>
                    </button>

                    <div class="dropdown-menu p-3 shadow-lg" aria-labelledby="dropdownFilterBalance" style="width: max-content; min-width: 100%; max-width: 90vw;">
                        <div class="d-flex justify-content-between mb-2">
                            <button class="btn btn-sm btn-outline-secondary" id="clear-all-balance-filters" type="button">{{ __('messages.owner.xen_platform.accounts.clear_all') }}</button>
                            <button class="btn btn-sm btn-primary" id="close-filter-balance-dropdown-btn" type="button" data-toggle="dropdown">{{ __('messages.owner.xen_platform.accounts.close') }}</button>
                        </div>

                        <div class="row" id="popup-balance-filter-options">
                            {{-- Kolom 1: TYPE --}}
                            <div class="col-md-6 px-2 mb-3" style="min-width: 180px;">
                                <h6 class="font-weight-bold border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.75rem; color: #64748b;">{{ __('messages.owner.xen_platform.accounts.type_b') }}</h6>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-disbursement" data-filter-group="types" value="DISBURSEMENT"><label class="custom-control-label" for="bal-type-disbursement">Disbursement</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-payment" data-filter-group="types" value="PAYMENT"><label class="custom-control-label" for="bal-type-payment">Payment</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-remittance-payout" data-filter-group="types" value="REMITTANCE_PAYOUT"><label class="custom-control-label" for="bal-type-remittance-payout">Remit. Payout</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-transfer" data-filter-group="types" value="TRANSFER"><label class="custom-control-label" for="bal-type-transfer">Transfer</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-refund" data-filter-group="types" value="REFUND"><label class="custom-control-label" for="bal-type-refund">Refund</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-withdrawal" data-filter-group="types" value="WITHDRAWAL"><label class="custom-control-label" for="bal-type-withdrawal">Withdrawal</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-topup" data-filter-group="types" value="TOPUP"><label class="custom-control-label" for="bal-type-topup">Top Up</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-conversion" data-filter-group="types" value="CONVERSION"><label class="custom-control-label" for="bal-type-conversion">Conversion</label></div>
                            </div>

                            {{-- Kolom 2: PAYMENT METHOD --}}
                            <div class="col-md-6 px-2 mb-3" style="min-width: 180px;">
                                <h6 class="font-weight-bold border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.75rem; color: #64748b;">{{ __('messages.owner.xen_platform.accounts.payment_method_b') }}</h6>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-bank" data-filter-group="channel_categories" value="BANK"><label class="custom-control-label" for="bal-pm-bank">Bank</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-cards" data-filter-group="channel_categories" value="CARDS"><label class="custom-control-label" for="bal-pm-cards">Cards</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-cardless-credit" data-filter-group="channel_categories" value="CARDLESS_CREDIT"><label class="custom-control-label" for="bal-pm-cardless-credit">Cardless Cr.</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-cash" data-filter-group="channel_categories" value="CASH"><label class="custom-control-label" for="bal-pm-cash">Cash</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-direct-debit" data-filter-group="channel_categories" value="DIRECT_DEBIT"><label class="custom-control-label" for="bal-pm-direct-debit">Direct Debit</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-e-wallet" data-filter-group="channel_categories" value="EWALLET"><label class="custom-control-label" for="bal-pm-e-wallet">E-Wallet</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-paylater" data-filter-group="channel_categories" value="PAYLATER"><label class="custom-control-label" for="bal-pm-paylater">PayLater</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-qr" data-filter-group="channel_categories" value="QR_CODE"><label class="custom-control-label" for="bal-pm-qr">QR Code</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-retail-outlet" data-filter-group="channel_categories" value="RETAIL_OUTLET"><label class="custom-control-label" for="bal-pm-retail-outlet">Retail Outlet</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-va" data-filter-group="channel_categories" value="VIRTUAL_ACCOUNT"><label class="custom-control-label" for="bal-pm-va">VA</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-xenplatform" data-filter-group="channel_categories" value="XENPLATFORM"><label class="custom-control-label" for="bal-pm-xenplatform">Xenplatform</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-other" data-filter-group="channel_categories" value="OTHER"><label class="custom-control-label" for="bal-pm-other">Other</label></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. DATE RANGE INPUT --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <div class="input-wrapper">
                    <span class="input-icon">
                        <span class="material-symbols-outlined">calendar_today</span>
                    </span>
                    <input type="text" 
                        class="form-control-modern with-icon daterange-balance"
                        name="date_balance" 
                        id="daterange-balance"
                        placeholder="{{ __('messages.owner.xen_platform.accounts.transaction_date') }}">
                </div>
            </div>
        </div>

        {{-- 3. SEARCH TYPE SELECTOR --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <div class="select-wrapper">
                    <select id="search_balance_type_select" class="form-control-modern" onchange="document.getElementById('current-balance-search-key').value = this.value">
                        <option value="reference_id">{{ __('messages.owner.xen_platform.accounts.reference') }}</option>
                        <option value="account_identifier">{{ __('messages.owner.xen_platform.accounts.account') }}</option>
                        <option value="amount">{{ __('messages.owner.xen_platform.accounts.amount') }}</option>
                        <option value="product_id">{{ __('messages.owner.xen_platform.accounts.product_id') }}</option>
                    </select>
                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                </div>
            </div>
        </div>

        {{-- 4. SEARCH INPUT --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <div class="input-wrapper">
                    <span class="input-icon">
                        <span class="material-symbols-outlined">search</span>
                    </span>
                    <input type="text" 
                        class="form-control-modern with-icon" 
                        id="global-balance-search-input" 
                        placeholder="{{ __('messages.owner.xen_platform.accounts.search_placeholder') }}">
                </div>
            </div>
        </div>

        {{-- Hidden Inputs (Wajib ada untuk JS) --}}
        <input type="hidden" id="current-balance-search-key" value="reference_id">
        <input type="hidden" id="balance-filter-limit" value="10">

        {{-- 5. BUTTON ACTIONS --}}
        <div class="col-12 col-md-3 col-lg-3 d-flex align-items-end mt-2">
            <div class="form-group" style="display: flex; gap: 0.5rem; width: 100%;">
                <button type="button" id="apply-balance-filter-btn" class="btn-modern btn-primary-modern" style="flex: 1;">
                    {{ __('messages.owner.xen_platform.accounts.apply_filter') }}
                </button>
            </div>
        </div>
        <div class="col-12 col-md-3 col-lg-3 d-flex align-items-end mt-2">
            <div class="form-group" style="display: flex; gap: 0.5rem; width: 100%;">
                <button type="button" id="reset-balance-filter-btn" class="btn-modern btn-secondary-modern" style="flex: 1;" title="Reset semua filter">
                    {{ __('messages.owner.xen_platform.accounts.reset') }}
                </button>
            </div>
        </div>

    </div>
</form>