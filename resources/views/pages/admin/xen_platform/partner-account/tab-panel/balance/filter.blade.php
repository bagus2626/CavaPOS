<form id="balances-filter-form">
        <div class="d-flex align-items-center flex-wrap">

            {{-- 1. Dropdown Filter --}}
            <div class="dropdown mr-2">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownFilterBalance" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Filter (<span id="balance-filter-count">0</span>)
                </button>

                <div class="dropdown-menu p-3" aria-labelledby="dropdownFilterBalance" style="width: 500px; white-space: normal;">

                    <div class="d-flex justify-content-between mb-2">
                        <button class="btn btn-sm btn-outline-secondary" id="clear-all-balance-filters" type="button">Clear All</button>
                        <button class="btn btn-sm btn-primary" id="close-filter-balance-dropdown-btn" type="button" data-toggle="dropdown">Tutup</button>
                    </div>

                    <div class="row" id="popup-balance-filter-options">
                        {{-- Kolom 3: TYPE --}}
                        <div class="col-sm-2 px-2" style="flex-basis: 40%; max-width: 40%;">
                            <h6 class="font-weight-bold border-bottom pb-1">TYPE</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-disbursement" data-filter-group="types" value="DISBURSEMENT"><label class="custom-control-label" for="bal-type-disbursement">Disbursement</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-payment" data-filter-group="types" value="PAYMENT"><label class="custom-control-label" for="bal-type-payment">Payment</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-remittance-payout" data-filter-group="types" value="REMITTANCE_PAYOUT"><label class="custom-control-label" for="bal-type-remittance-payout">Remittance Payout</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-transfer" data-filter-group="types" value="TRANSFER"><label class="custom-control-label" for="bal-type-transfer">Transfer</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-refund" data-filter-group="types" value="REFUND"><label class="custom-control-label" for="bal-type-refund">Refund</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-withdrawal" data-filter-group="types" value="WITHDRAWAL"><label class="custom-control-label" for="bal-type-withdrawal">Withdrawal</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-topup" data-filter-group="types" value="TOPUP"><label class="custom-control-label" for="bal-type-topup">Top Up</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-type-conversion" data-filter-group="types" value="CONVERSION"><label class="custom-control-label" for="bal-type-conversion">Conversion</label></div>
                        </div>

                        {{-- Kolom 4: PAYMENT METHOD --}}
                        <div class="col-sm-3 px-2" style="flex-basis: 50%; max-width: 50%;">
                            <h6 class="font-weight-bold border-bottom pb-1">PAYMENT METHOD</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-bank" data-filter-group="channel_categories" value="BANK"><label class="custom-control-label" for="bal-pm-bank">Bank</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-cards" data-filter-group="channel_categories" value="CARDS"><label class="custom-control-label" for="bal-pm-cards">Cards</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-cardless-credit" data-filter-group="channel_categories" value="CARDLESS_CREDIT"><label class="custom-control-label" for="bal-pm-cardless-credit">Cardless Credit</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-cash" data-filter-group="channel_categories" value="CASH"><label class="custom-control-label" for="bal-pm-cash">Cash</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-direct-debit" data-filter-group="channel_categories" value="DIRECT_DEBIT"><label class="custom-control-label" for="bal-pm-direct-debit">Direct Debit</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-e-wallet" data-filter-group="channel_categories" value="EWALLET"><label class="custom-control-label" for="bal-pm-e-wallet">E-Wallet</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-paylater" data-filter-group="channel_categories" value="PAYLATER"><label class="custom-control-label" for="bal-pm-paylater">PayLater</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-qr" data-filter-group="channel_categories" value="QR_CODE"><label class="custom-control-label" for="bal-pm-qr">QR Code</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-retail-outlet" data-filter-group="channel_categories" value="RETAIL_OUTLET"><label class="custom-control-label" for="bal-pm-retail-outlet">Retail Outlet</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-va" data-filter-group="channel_categories" value="VIRTUAL_ACCOUNT"><label class="custom-control-label" for="bal-pm-va">Virtual Account</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-xenplatform" data-filter-group="channel_categories" value="XENPLATFORM"><label class="custom-control-label" for="bal-pm-xenplatform">Xenplatform</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input balance-filter-checkbox" id="bal-pm-other" data-filter-group="channel_categories" value="OTHER"><label class="custom-control-label" for="bal-pm-other">Other</label></div>
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