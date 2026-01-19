<form id="transactions-filter-form">
    <div class="row">
        
        {{-- 1. ADVANCED FILTER (Dropdown) --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                {{-- Dropdown Component Wrapper --}}
                <div class="dropdown">
                    <button class="form-control-modern d-flex justify-content-between align-items-center text-left" 
                            type="button" 
                            id="dropdownFilter" 
                            data-toggle="dropdown" 
                            aria-haspopup="true" 
                            aria-expanded="false"
                            style="background: #fff; border: 1px solid #e2e8f0; width: 100%;">
                        <span>
                            {{ __('messages.owner.xen_platform.accounts.filter') }} 
                            (<span id="filter-count">0</span>)
                        </span>
                        <span class="material-symbols-outlined" style="font-size: 1.2rem;">filter_list</span>
                    </button>

                    <div class="dropdown-menu p-3 shadow-lg" aria-labelledby="dropdownFilter" style="width: 800px; white-space: normal; max-width: 90vw;">
                        <div class="d-flex justify-content-between mb-2">
                            <button class="btn btn-sm btn-outline-secondary" id="clear-all-filters" type="button">{{ __('messages.owner.xen_platform.accounts.clear_all') }}</button>
                            <button class="btn btn-sm btn-primary" id="close-filter-dropdown-btn" type="button" data-toggle="dropdown">{{ __('messages.owner.xen_platform.accounts.close') }}</button>
                        </div>

                        <div class="row" id="popup-filter-options">
                            {{-- Kolom 1: STATUS --}}
                            <div class="col-md-3 px-2 mb-3">
                                <h6 class="font-weight-bold border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.75rem; color: #64748b;">Status</h6>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="status-pending" data-filter-group="statuses" value="PENDING"><label class="custom-control-label" for="status-pending">Pending</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="status-success" data-filter-group="statuses" value="SUCCESS"><label class="custom-control-label" for="status-success">Success</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="status-failed" data-filter-group="statuses" value="FAILED"><label class="custom-control-label" for="status-failed">Failed</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="status-voided" data-filter-group="statuses" value="VOIDED"><label class="custom-control-label" for="status-voided">Voided</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="status-reversed" data-filter-group="statuses" value="REVERSED"><label class="custom-control-label" for="status-reversed">Reversed</label></div>
                            </div>

                            {{-- Kolom 2: TYPE --}}
                            <div class="col-md-3 px-2 mb-3">
                                <h6 class="font-weight-bold border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.75rem; color: #64748b;">{{ __('messages.owner.xen_platform.accounts.type_b') }}</h6>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-disbursement" data-filter-group="types" value="DISBURSEMENT"><label class="custom-control-label" for="type-disbursement">Disbursement</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-payment" data-filter-group="types" value="PAYMENT"><label class="custom-control-label" for="type-payment">Payment</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-remittance-payout" data-filter-group="types" value="REMITTANCE_PAYOUT"><label class="custom-control-label" for="type-remittance-payout">Remit. Payout</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-transfer" data-filter-group="types" value="TRANSFER"><label class="custom-control-label" for="type-transfer">Transfer</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-refund" data-filter-group="types" value="REFUND"><label class="custom-control-label" for="type-refund">Refund</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-withdrawal" data-filter-group="types" value="WITHDRAWAL"><label class="custom-control-label" for="type-withdrawal">Withdrawal</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-topup" data-filter-group="types" value="TOPUP"><label class="custom-control-label" for="type-topup">Top Up</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-conversion" data-filter-group="types" value="CONVERSION"><label class="custom-control-label" for="type-conversion">Conversion</label></div>
                            </div>

                            {{-- Kolom 3: PAYMENT METHOD --}}
                            <div class="col-md-3 px-2 mb-3">
                                <h6 class="font-weight-bold border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.75rem; color: #64748b;">{{ __('messages.owner.xen_platform.accounts.payment_method_b') }}</h6>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-bank" data-filter-group="channel_categories" value="BANK"><label class="custom-control-label" for="pm-bank">Bank</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-cards" data-filter-group="channel_categories" value="CARDS"><label class="custom-control-label" for="pm-cards">Cards</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-cardless-credit" data-filter-group="channel_categories" value="CARDLESS_CREDIT"><label class="custom-control-label" for="pm-cardless-credit">Cardless Cr.</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-e-wallet" data-filter-group="channel_categories" value="EWALLET"><label class="custom-control-label" for="pm-e-wallet">E-Wallet</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-qr" data-filter-group="channel_categories" value="QR_CODE"><label class="custom-control-label" for="pm-qr">QR Code</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-va" data-filter-group="channel_categories" value="VIRTUAL_ACCOUNT"><label class="custom-control-label" for="pm-va">VA</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-xenplatform" data-filter-group="channel_categories" value="XENPLATFORM"><label class="custom-control-label" for="pm-xenplatform">Xenplatform</label></div>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-other" data-filter-group="channel_categories" value="OTHER"><label class="custom-control-label" for="pm-other">Other</label></div>
                            </div>

                            {{-- Kolom 4: CURRENCY --}}
                            <div class="col-md-3 px-2 mb-3">
                                <h6 class="font-weight-bold border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.75rem; color: #64748b;">{{ __('messages.owner.xen_platform.accounts.currency_b') }}</h6>
                                <div class="d-flex flex-wrap">
                                    <div class="custom-control custom-checkbox mr-2"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-idr" data-filter-group="currency" value="IDR"><label class="custom-control-label" for="currency-idr">IDR</label></div>
                                    <div class="custom-control custom-checkbox mr-2"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-usd" data-filter-group="currency" value="USD"><label class="custom-control-label" for="currency-usd">USD</label></div>
                                    <div class="custom-control custom-checkbox mr-2"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-sgd" data-filter-group="currency" value="SGD"><label class="custom-control-label" for="currency-sgd">SGD</label></div>
                                    <div class="custom-control custom-checkbox mr-2"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-php" data-filter-group="currency" value="PHP"><label class="custom-control-label" for="currency-php">PHP</label></div>
                                    <div class="custom-control custom-checkbox mr-2"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-myr" data-filter-group="currency" value="MYR"><label class="custom-control-label" for="currency-myr">MYR</label></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. DATE RANGE --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <div class="input-wrapper">
                    <span class="input-icon">
                        <span class="material-symbols-outlined">calendar_today</span>
                    </span>
                    <input type="text" 
                        class="form-control-modern with-icon daterange-transaction"
                        name="date_transaction" 
                        id="daterange-transaction"
                        placeholder="Transaction Date">
                </div>
            </div>
        </div>

        {{-- 3. SEARCH TYPE SELECTOR --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <div class="select-wrapper">
                    <select id="search_type_select" class="form-control-modern" onchange="document.getElementById('current-search-key').value = this.value">
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
                        id="global-search-input" 
                        placeholder="Search...">
                </div>
            </div>
        </div>

        {{-- Hidden Inputs --}}
        <input type="hidden" id="current-search-key" value="reference_id">
        <input type="hidden" id="filter-limit" value="10">

        {{-- 5. BUTTON ACTIONS --}}
        <div class="col-12 col-md-3 col-lg-3 d-flex align-items-end">
            <div class="form-group" style="display: flex; gap: 0.5rem; width: 100%;">
                <button type="button" id="apply-filter-btn" class="btn-modern btn-primary-modern" style="flex: 1;">
                   {{ __('messages.owner.xen_platform.accounts.apply_filter') }}
                </button>

            </div>
        </div>
        <div class="col-12 col-md-3 col-lg-3 d-flex align-items-end">
            <div class="form-group" style="display: flex; gap: 0.5rem; width: 100%;">
                <button type="button" id="reset-filter-btn" class="btn-modern btn-secondary-modern" style="flex: 1;" title="Reset semua filter">
                    {{ __('messages.owner.xen_platform.accounts.reset') }}
                </button>
            </div>
        </div>

    </div>
</form>