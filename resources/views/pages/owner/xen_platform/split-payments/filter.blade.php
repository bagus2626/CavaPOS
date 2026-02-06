<form id="split-payments-filter-form">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <label for="search_reference_id" class="form-label-modern">
                    {{ __('messages.owner.xen_platform.split_payments.transaction_reference_id') }}
                </label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <span class="material-symbols-outlined">tag</span>
                    </span>
                    <input type="text" 
                        name="search_reference_id" 
                        id="search_reference_id" 
                        class="form-control-modern with-icon"
                        placeholder="{{ __('messages.owner.xen_platform.split_payments.search_reference_id') }}">
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <label for="transaction_status" class="form-label-modern">
                    {{ __('messages.owner.xen_platform.split_payments.split_transaction_status') }}
                </label>
                <div class="select-wrapper">
                    <select name="transaction_status" id="transaction_status" class="form-control-modern">
                        <option value="">{{ __('messages.owner.xen_platform.split_payments.all_status') }}</option>
                        @php $statuses = ['COMPLETED', 'FAILED']; @endphp
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <label for="filter-min-split" class="form-label-modern">
                    {{ __('messages.owner.xen_platform.split_payments.total_split_min') }} (IDR)
                </label>
                <div class="input-wrapper">
                    <span class="input-icon">Rp</span>
                    <input type="number" 
                        name="filter-min-split" 
                        id="filter-min-split" 
                        class="form-control-modern with-icon"
                        placeholder="{{ __('messages.owner.xen_platform.split_payments.min_split_amount') }}">
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <label for="filter-max-split" class="form-label-modern">
                    {{ __('messages.owner.xen_platform.split_payments.total_split_max') }} (IDR)
                </label>
                <div class="input-wrapper">
                    <span class="input-icon">Rp</span>
                    <input type="number" 
                        name="filter-max-split" 
                        id="filter-max-split" 
                        class="form-control-modern with-icon"
                        placeholder="{{ __('messages.owner.xen_platform.split_payments.max_split_amount') }}">
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <label for="daterange-payments" class="form-label-modern">
                    {{ __('messages.owner.xen_platform.split_payments.date_created') }}
                </label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <span class="material-symbols-outlined">calendar_today</span>
                    </span>
                    <input type="text" 
                        class="form-control-modern with-icon daterange-payments"
                        name="date_rules" 
                        id="daterange-payments"
                        placeholder="{{ __('messages.owner.xen_platform.split_payments.date_created') }}">
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="form-group">
                <label for="filter-business-name" class="form-label-modern">
                    {{ __('messages.owner.xen_platform.split_payments.business_name') }} (Source/Dest)
                </label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <span class="material-symbols-outlined">business</span>
                    </span>
                    <input type="text" 
                        name="filter-business-name" 
                        id="filter-business-name" 
                        class="form-control-modern with-icon"
                        placeholder="{{ __('messages.owner.xen_platform.split_payments.business_name') }}">
                </div>
            </div>
        </div>

        <input type="hidden" id="filter-limit" value="10">

        <div class="col-12 col-md-12 col-lg-6 d-flex align-items-end">
            <div class="form-group" style="display: flex; gap: 0.5rem; width: 100%;">
                <button type="submit" id="apply-filter-btn" class="btn-modern btn-primary-modern" style="flex: 1;">
                    {{ __('messages.owner.xen_platform.split_payments.apply_filter') }}
                </button>
                <button type="button" id="reset-filter-btn" class="btn-modern btn-secondary-modern" style="flex: 1;" title="Reset Filter">
                    {{ __('messages.owner.xen_platform.split_payments.reset_and_reload_data') }}
                </button>
            </div>
        </div>
    </div>
</form>