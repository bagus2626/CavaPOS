<form id="transactions-filter-form">
        <div class="d-flex align-items-center flex-wrap">
            {{-- 1. Dropdown Filter --}}
            <div class="dropdown mr-2">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownFilter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Filter (<span id="filter-count">0</span>)
                </button>

                <div class="dropdown-menu p-3" aria-labelledby="dropdownFilter" style="width: 800px; white-space: normal;">

                    <div class="d-flex justify-content-between mb-2">
                        <button class="btn btn-sm btn-outline-secondary" id="clear-all-filters" type="button">Clear All</button>
                        <button class="btn btn-sm btn-primary" id="close-filter-dropdown-btn" type="button" data-toggle="dropdown">Tutup</button>
                    </div>

                    <div class="row" id="popup-filter-options">
                        {{-- Kolom 1: STATUS --}}
                        <div class="col-sm-2_5 px-2 px-2" style="flex-basis: 20%; max-width: 20%;">
                            <h6 class="font-weight-bold border-bottom pb-1">STATUS</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="status-pending" data-filter-group="statuses" value="PENDING"><label class="custom-control-label" for="status-pending">Pending</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="status-success" data-filter-group="statuses" value="SUCCESS"><label class="custom-control-label" for="status-success">Success</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="status-failed" data-filter-group="statuses" value="FAILED"><label class="custom-control-label" for="status-failed">Failed</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="status-voided" data-filter-group="statuses" value="VOIDED"><label class="custom-control-label" for="status-voided">Voided</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="status-reversed" data-filter-group="statuses" value="REVERSED"><label class="custom-control-label" for="status-reversed">Reversed</label></div>
                        </div>

                        {{-- Kolom 3: TYPE --}}
                        <div class="col-sm-2 px-2" style="flex-basis: 25%; max-width: 25%;">
                            <h6 class="font-weight-bold border-bottom pb-1">TYPE</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-disbursement" data-filter-group="types" value="DISBURSEMENT"><label class="custom-control-label" for="type-disbursement">Disbursement</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-payment" data-filter-group="types" value="PAYMENT"><label class="custom-control-label" for="type-payment">Payment</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-remittance-payout" data-filter-group="types" value="REMITTANCE_PAYOUT"><label class="custom-control-label" for="type-remittance-payout">Remittance Payout</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-transfer" data-filter-group="types" value="TRANSFER"><label class="custom-control-label" for="type-transfer">Transfer</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-refund" data-filter-group="types" value="REFUND"><label class="custom-control-label" for="type-refund">Refund</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-withdrawal" data-filter-group="types" value="WITHDRAWAL"><label class="custom-control-label" for="type-withdrawal">Withdrawal</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-topup" data-filter-group="types" value="TOPUP"><label class="custom-control-label" for="type-topup">Top Up</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="type-conversion" data-filter-group="types" value="CONVERSION"><label class="custom-control-label" for="type-conversion">Conversion</label></div>
                        </div>

                        {{-- Kolom 4: PAYMENT METHOD --}}
                        <div class="col-sm-3 px-2" style="flex-basis: 30%; max-width: 30%;">
                            <h6 class="font-weight-bold border-bottom pb-1">PAYMENT METHOD</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-bank" data-filter-group="channel_categories" value="BANK"><label class="custom-control-label" for="pm-bank">Bank</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-cards" data-filter-group="channel_categories" value="CARDS"><label class="custom-control-label" for="pm-cards">Cards</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-cardless-credit" data-filter-group="channel_categories" value="CARDLESS_CREDIT"><label class="custom-control-label" for="pm-cardless-credit">Cardless Credit</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-cash" data-filter-group="channel_categories" value="CASH"><label class="custom-control-label" for="pm-cash">Cash</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-direct-debit" data-filter-group="channel_categories" value="DIRECT_DEBIT"><label class="custom-control-label" for="pm-direct-debit">Direct Debit</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-e-wallet" data-filter-group="channel_categories" value="EWALLET"><label class="custom-control-label" for="pm-e-wallet">E-Wallet</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-paylater" data-filter-group="channel_categories" value="PAYLATER"><label class="custom-control-label" for="pm-paylater">PayLater</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-qr" data-filter-group="channel_categories" value="QR_CODE"><label class="custom-control-label" for="pm-qr">QR Code</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-retail-outlet" data-filter-group="channel_categories" value="RETAIL_OUTLET"><label class="custom-control-label" for="pm-retail-outlet">Retail Outlet</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-va" data-filter-group="channel_categories" value="VIRTUAL_ACCOUNT"><label class="custom-control-label" for="pm-va">Virtual Account</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-xenplatform" data-filter-group="channel_categories" value="XENPLATFORM"><label class="custom-control-label" for="pm-xenplatform">Xenplatform</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="pm-other" data-filter-group="channel_categories" value="OTHER"><label class="custom-control-label" for="pm-other">Other</label></div>
                        </div>

                        {{-- Kolom 5: CURRENCY --}}
                        <div class="col-sm-2 px-2" style="flex-basis: 25%; max-width: 25%;">
                            <h6 class="font-weight-bold border-bottom pb-1">CURRENCY</h6>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-idr" data-filter-group="currency" value="IDR"><label class="custom-control-label" for="currency-idr">IDR</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-php" data-filter-group="currency" value="PHP"><label class="custom-control-label" for="currency-php">PHP</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-usd" data-filter-group="currency" value="USD"><label class="custom-control-label" for="currency-usd">USD</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-vnd" data-filter-group="currency" value="VND"><label class="custom-control-label" for="currency-vnd">VND</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-thb" data-filter-group="currency" value="THB"><label class="custom-control-label" for="currency-thb">THB</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-myr" data-filter-group="currency" value="MYR"><label class="custom-control-label" for="currency-myr">MYR</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-sgd" data-filter-group="currency" value="SGD"><label class="custom-control-label" for="currency-sgd">SGD</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-eur" data-filter-group="currency" value="EUR"><label class="custom-control-label" for="currency-eur">EUR</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-gbp" data-filter-group="currency" value="GBP"><label class="custom-control-label" for="currency-gbp">GBP</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-hkd" data-filter-group="currency" value="HKD"><label class="custom-control-label" for="currency-hkd">HKD</label></div>
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input filter-checkbox" id="currency-aud" data-filter-group="currency" value="AUD"><label class="custom-control-label" for="currency-aud">AUD</label></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Date Range Picker --}}
            <div class="mr-2" style="min-width: 250px;">
                <fieldset class="form-group position-relative has-icon-left m-0">
                    <input type="text" class="form-control daterange-transaction"
                           name="date_transaction" id="daterange-transaction"
                           placeholder="Tanggal Transaksi">
                    <div class="form-control-position">
                        <i class="bx bx-calendar-check"></i>
                    </div>
                </fieldset>
            </div>

            {{-- 3. Search Box --}}
            <div class="input-group mr-2 flex-grow-1" style="max-width: 500px;">
                <div class="input-group-prepend">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="search-type-toggle">Reference</button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item search-type-select" href="#" data-search-key="reference_id">Reference</a>
                        <a class="dropdown-item search-type-select" href="#" data-search-key="account_identifier">Account</a>
                        <a class="dropdown-item search-type-select" href="#" data-search-key="amount">Amount</a>
                        <a class="dropdown-item search-type-select" href="#" data-search-key="product_id">Product ID</a>
                    </div>
                </div>
                <input type="text" class="form-control" placeholder="Search..." aria-label="Search" id="global-search-input">
            </div>

            {{-- 4. Tombol Terapkan SEMUA Filter (Aksi Final) --}}
            <div class="d-flex w-md-auto">
                <button id="apply-filter-btn" class="btn btn-primary mr-1 flex-grow-1" type="button">
                    <i class="bx bx-filter"></i> Terapkan
                </button>
                <button id="reset-filter-btn" class="btn btn-outline-secondary" type="button" title="Reset semua filter">
                    <i class="bx bx-reset"></i>
                </button>
            </div>

            <input type="hidden" id="current-search-key" value="reference_id">
            <input type="hidden" id="filter-limit" value="10">

        </div>
</form>

