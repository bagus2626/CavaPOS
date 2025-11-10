<form id="balances-filter-form">
        <div class="d-flex align-items-center flex-wrap">

            {{-- 1. Dropdown Filter --}}
            <div class="dropdown mr-2">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownFilterBalance" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Filter (<span id="balance-filter-count">0</span>)
                </button>

                <div class="dropdown-menu p-3" aria-labelledby="dropdownFilterBalance" style="width: 950px; white-space: normal;">

                    <div class="d-flex justify-content-between mb-2">
                        <button class="btn btn-sm btn-outline-secondary" id="clear-all-balance-filters" type="button">Clear All</button>
                        <button class="btn btn-sm btn-primary" id="close-filter-balance-dropdown-btn" type="button" data-toggle="dropdown">Tutup</button>
                    </div>

                    <div class="row" id="popup-balance-filter-options">
                        {{-- Kolom 1: STATUS --}}
                        <div class="col-sm-2_5 px-2 px-2" style="flex-basis: 20%; max-width: 20%;">
                            <h6 class="font-weight-bold border-bottom pb-1">STATUS</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-status-successful" data-filter-group="statuses" value="SUCCESSFUL"><label class="custom-control-label" for="bal-status-successful">Successful</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-status-failed" data-filter-group="statuses" value="FAILED"><label class="custom-control-label" for="bal-status-failed">Failed</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-status-refund-pending" data-filter-group="statuses" value="REFUND_PENDING"><label class="custom-control-label" for="bal-status-refund-pending">Refund Pending</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-status-refunded" data-filter-group="statuses" value="REFUNDED"><label class="custom-control-label" for="bal-status-refunded">Refunded</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-status-partially-refunded" data-filter-group="statuses" value="PARTIALLY_REFUNDED"><label class="custom-control-label" for="bal-status-partially-refunded">Partially Refunded</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-status-voided" data-filter-group="statuses" value="VOIDED"><label class="custom-control-label" for="bal-status-voided">Voided</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-status-reversed" data-filter-group="statuses" value="REVERSED"><label class="custom-control-label" for="bal-status-reversed">Reversed</label></div>
                        </div>

                        {{-- Kolom 2: CLIENT TYPE --}}
                        <div class="col-sm-2_5 px-2" style="flex-basis: 20%; max-width: 20%;">
                            <h6 class="font-weight-bold border-bottom pb-1">CLIENT TYPE</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-api" data-filter-group="client_types" value="API_GATEWAY"><label class="custom-control-label" for="bal-type-api">API Gateway</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-dashboard" data-filter-group="client_types" value="DASHBOARD"><label class="custom-control-label" for="bal-type-dashboard">Dashboard</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-integration" data-filter-group="client_types" value="INTEGRATION"><label class="custom-control-label" for="bal-type-integration">Integration</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-recurring" data-filter-group="client_types" value="RECURRING"><label class="custom-control-label" for="bal-type-recurring">Recurring</label></div>
                        </div>

                        {{-- Kolom 3: PAYMENT CHANNELS --}}
                        <div class="col-sm-2 px-2" style="flex-basis: 30%; max-width: 30%;">
                            <h6 class="font-weight-bold border-bottom pb-1">PAYMENT CHANNELS</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-channel-va" data-filter-group="payment_channels" value="VIRTUAL_ACCOUNT"><label class="custom-control-label" for="bal-channel-va">Virtual Account</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-channel-ewallet" data-filter-group="payment_channels" value="EWALLET"><label class="custom-control-label" for="bal-channel-ewallet">E-Wallet</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-channel-retail" data-filter-group="payment_channels" value="RETAIL_OUTLET"><label class="custom-control-label" for="bal-channel-retail">Retail Outlet</label></div>
                        </div>

                        {{-- Kolom 4: PAYMENT METHOD --}}
                        <div class="col-sm-3 px-2" style="flex-basis: 30%; max-width: 30%;">
                            <h6 class="font-weight-bold border-bottom pb-1">PAYMENT METHOD (Lainnya)</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="pm-direct-debit-bal" data-filter-group="channel_categories" value="DIRECT_DEBIT"><label class="custom-control-label" for="pm-direct-debit-bal">Direct Debit</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="pm-qr-bal" data-filter-group="channel_categories" value="QR_CODE"><label class="custom-control-label" for="pm-qr-bal">QR Code</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="pm-paylater-bal" data-filter-group="channel_categories" value="PAYLATER"><label class="custom-control-label" for="pm-paylater-bal">PayLater</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="pm-cards-bal" data-filter-group="channel_categories" value="CARDS"><label class="custom-control-label" for="pm-cards-bal">Cards</label></div>
                        </div>

                        {{-- Kolom 5: CURRENCY --}}
                        <div class="col-sm-2 px-2" style="flex-basis: 15%; max-width: 15%;">
                            <h6 class="font-weight-bold border-bottom pb-1">CURRENCY</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-currency-idr" data-filter-group="currency" value="IDR"><label class="custom-control-label" for="bal-currency-idr">IDR</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-currency-other" data-filter-group="currency" value="NON_IDR"><label class="custom-control-label" for="bal-currency-other">Others (Non IDR)</label></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Date Range Picker --}}
            <div class="mr-2" style="min-width: 250px;">
                <fieldset class="form-group position-relative has-icon-left m-0">
                    <input type="text" class="form-control daterange-balance"
                           name="date_balance" id="daterange-balance"
                           placeholder="Tanggal Mutasi">
                    <div class="form-control-position">
                        <i class="bx bx-calendar-check"></i>
                    </div>
                </fieldset>
            </div>

            {{-- 3. Search Box --}}
            <div class="input-group mr-2 flex-grow-1" style="max-width: 500px;">
                <div class="input-group-prepend">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="search-balance-type-toggle">Reference</button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item search-balance-type-select" href="#" data-search-key="reference_id">Reference</a>
                        <a class="dropdown-item search-balance-type-select" href="#" data-search-key="account_identifier">Account</a>
                        <a class="dropdown-item search-balance-type-select" href="#" data-search-key="amount">Amount</a>
                        <a class="dropdown-item search-balance-type-select" href="#" data-search-key="product_id">Product ID</a>
                    </div>
                </div>
                <input type="text" class="form-control" placeholder="Search..." aria-label="Search" id="global-balance-search-input">
            </div>

            {{-- 4. Tombol Terapkan SEMUA Filter (Aksi Final) --}}
            <div class="d-flex w-md-auto">
                <button id="apply-balance-filter-btn" class="btn btn-primary mr-1 flex-grow-1" type="button">
                    <i class="bx bx-filter"></i> Terapkan
                </button>
                <button id="reset-balance-filter-btn" class="btn btn-outline-secondary" type="button" title="Reset semua filter">
                    <i class="bx bx-reset"></i>
                </button>
            </div>

            <input type="hidden" id="current-balance-search-key" value="reference_id">
            <input type="hidden" id="balance-filter-limit" value="10">

        </div>
</form>