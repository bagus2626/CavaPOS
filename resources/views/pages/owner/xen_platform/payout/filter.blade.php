<form id="disbursement-filter-form">
    <div class="row">
        <div class="col-md-4">
            <label for="filter-search">{{ __('messages.owner.xen_platform.payouts.search') }}</label>
            <input type="text" class="form-control" id="filter-search" name="search"
                   placeholder="{{ __('messages.owner.xen_platform.payouts.search_placeholder') }}">
        </div>

        {{-- Filter by Status --}}
        <div class="col-md-2">
            <label for="filter-status">Status</label>
            <select class="form-control" id="filter-status" name="status">
                <option value="">{{ __('messages.owner.xen_platform.payouts.all_status') }}</option>
                <option value="REQUESTED">REQUESTED</option>
                <option value="REVERSED">REVERSED</option>
                <option value="ACCEPTED">ACCEPTED</option>
                <option value="SUCCEEDED">SUCCEEDED</option>
                <option value="FAILED">FAILED</option>
                <option value="CANCELLED">CANCELLED</option>
            </select>
        </div>

        {{-- Filter Date Range --}}
        <div class="col-md-3 form-group">
            <label>{{ __('messages.owner.xen_platform.payouts.date_created') }}</label>
            <fieldset class="form-group position-relative has-icon-left m-0">
                <input type="text" class="form-control daterange-payout"
                       name="date_payout" id="daterange-payout"
                       placeholder="{{ __('messages.owner.xen_platform.payouts.date_created') }}">
                <div class="form-control-position">
                    <i class="bx bx-calendar-check"></i>
                </div>
            </fieldset>
        </div>

        {{-- Action Buttons --}}
        <div class="col-3">
            <div class="row mt-2">
                <div class="d-flex w-md-auto mt-4 ml-1">
                    <button type="submit" class="btn btn-primary btn-block" title="Filter">
                        <i class="fas fa-filter"></i> {{ __('messages.owner.xen_platform.payouts.apply_filter') }}
                    </button>
                </div>
                <div class="d-flex w-md-auto mt-4 ml-1">
                    <button type="button" class="btn btn-secondary btn-block" id="reset-filter-btn" title="Reset Filter">
                        <i class="fas fa-sync-alt"></i> {{ __('messages.owner.xen_platform.payouts.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>