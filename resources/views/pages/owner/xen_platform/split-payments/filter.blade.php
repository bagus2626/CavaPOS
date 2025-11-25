<form id="split-payments-filter-form">
    <div class="row">
        <div class="col-md-3 form-group">
            <label for="search_reference_id">{{ __('messages.owner.xen_platform.split_payments.transaction_reference_id') }}</label>
            <input type="text" name="search_reference_id" id="search_reference_id" class="form-control"
                   placeholder="{{ __('messages.owner.xen_platform.split_payments.search_reference_id') }}">
        </div>

        <div class="col-md-3 form-group">
            <label for="transaction_status">{{ __('messages.owner.xen_platform.split_payments.split_transaction_status') }}</label>
            <select name="transaction_status" id="transaction_status" class="form-control">
                <option value="">{{ __('messages.owner.xen_platform.split_payments.all_status') }}</option>
                @php $statuses = ['COMPLETED', 'FAILED']; @endphp
                @foreach($statuses as $status)
                    <option value="{{ $status }}">
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 form-group">
            <label for="filter-min-split">{{ __('messages.owner.xen_platform.split_payments.total_split_min') }} (IDR)</label>
            <input type="number" name="filter-min-split" id="filter-min-split" class="form-control"
                   placeholder="{{ __('messages.owner.xen_platform.split_payments.min_split_amount') }}">
        </div>

        <div class="col-md-3 form-group">
            <label for="filter-max-split">{{ __('messages.owner.xen_platform.split_payments.total_split_max') }} (IDR)</label>
            <input type="number" name="filter-max-split" id="filter-max-split" class="form-control"
                   placeholder="{{ __('messages.owner.xen_platform.split_payments.max_split_amount') }}">
        </div>

        <div class="col-md-3 form-group">
            <label>{{ __('messages.owner.xen_platform.split_payments.date_created') }}</label>
            <fieldset class="form-group position-relative has-icon-left m-0">
                <input type="text" class="form-control daterange-payments"
                       name="date_rules" id="daterange-payments"
                       placeholder="{{ __('messages.owner.xen_platform.split_payments.date_created') }}">
                <div class="form-control-position">
                    <i class="bx bx-calendar-check"></i>
                </div>
            </fieldset>
        </div>

        <div class="col-md-3 form-group">
            <label for="filter-business-name">{{ __('messages.owner.xen_platform.split_payments.business_name') }} (Source/Dest)</label>
            <input type="text" name="filter-business-name" id="filter-business-name" class="form-control"
                   placeholder="{{ __('messages.owner.xen_platform.split_payments.business_name') }}">
        </div>

        <input type="hidden" id="filter-limit" value="10">

        <div class="col-md-3 form-group d-flex align-items-end">
            <button id="apply-filter-btn" class="btn btn-primary btn-block"><i class="bx bx-filter-alt"></i> 
                {{ __('messages.owner.xen_platform.split_payments.apply_filter') }}
            </button>
        </div>

        <div class="col-md-3 d-flex form-group align-items-end">
            <button id="reset-filter-btn" type="button" class="btn btn-secondary btn-block" title="Reset Filter">
                <i class="bx bx-reset"></i> {{ __('messages.owner.xen_platform.split_payments.reset_and_reload_data') }}
            </button>
        </div>
    </div>
</form>