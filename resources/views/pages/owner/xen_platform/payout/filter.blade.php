<form id="disbursement-filter-form">
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="filter-search" class="form-label-modern">
                    {{ __('messages.owner.xen_platform.payouts.search') }}
                </label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <span class="material-symbols-outlined">search</span>
                    </span>
                    <input type="text" 
                        class="form-control-modern with-icon" 
                        id="filter-search" 
                        name="search"
                        placeholder="{{ __('messages.owner.xen_platform.payouts.search_placeholder') }}">
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="filter-status" class="form-label-modern">
                    Status
                </label>
                <div class="select-wrapper">
                    <select class="form-control-modern" id="filter-status" name="status">
                        <option value="">{{ __('messages.owner.xen_platform.payouts.all_status') }}</option>
                        <option value="REQUESTED">REQUESTED</option>
                        <option value="REVERSED">REVERSED</option>
                        <option value="ACCEPTED">ACCEPTED</option>
                        <option value="SUCCEEDED">SUCCEEDED</option>
                        <option value="FAILED">FAILED</option>
                        <option value="CANCELLED">CANCELLED</option>
                    </select>
                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="daterange-payout" class="form-label-modern">
                    {{ __('messages.owner.xen_platform.payouts.date_created') }}
                </label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <span class="material-symbols-outlined">calendar_today</span>
                    </span>
                    <input type="text" 
                        class="form-control-modern with-icon daterange-payout"
                        name="date_payout" 
                        id="daterange-payout"
                        placeholder="{{ __('messages.owner.xen_platform.payouts.date_created') }}">
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 d-flex align-items-end">
            <div class="form-group" style="display: flex; gap: 0.5rem; width: 100%;">
                <button type="submit" class="btn-modern btn-primary-modern" style="flex: 1;" title="Filter">
                    {{ __('messages.owner.xen_platform.payouts.apply_filter') }}
                </button>
                <button type="button" class="btn-modern btn-secondary-modern" style="flex: 1;" id="reset-filter-btn" title="Reset Filter">
                    {{ __('messages.owner.xen_platform.payouts.reset') }}
                </button>
            </div>
        </div>
    </div>
</form>