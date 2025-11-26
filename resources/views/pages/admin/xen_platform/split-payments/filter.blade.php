<form id="split-payments-filter-form">
    <div class="row">
        <div class="col-md-3 form-group">
            <label for="search_reference_id">Transaction Reference ID</label>
            <input type="text" name="search_reference_id" id="search_reference_id" class="form-control"
                   placeholder="Search Reference ID...">
        </div>

        <div class="col-md-3 form-group">
            <label for="transaction_status">Split Transaction Status</label>
            <select name="transaction_status" id="transaction_status" class="form-control">
                <option value="">All Status</option>
                @php $statuses = ['COMPLETED', 'FAILED']; @endphp
                @foreach($statuses as $status)
                    <option value="{{ $status }}">
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 form-group">
            <label for="filter-min-split">Total Split Min (IDR)</label>
            <input type="number" name="filter-min-split" id="filter-min-split" class="form-control"
                   placeholder="Min Split Amount">
        </div>

        <div class="col-md-3 form-group">
            <label for="filter-max-split">Total Split Max (IDR)</label>
            <input type="number" name="filter-max-split" id="filter-max-split" class="form-control"
                   placeholder="Max Split Amount">
        </div>

        <div class="col-md-3 form-group">
            <label>Date Created</label>
            <fieldset class="form-group position-relative has-icon-left m-0">
                <input type="text" class="form-control daterange-payments"
                       name="date_rules" id="daterange-payments"
                       placeholder="Date created">
                <div class="form-control-position">
                    <i class="bx bx-calendar-check"></i>
                </div>
            </fieldset>
        </div>

        <div class="col-md-3 form-group">
            <label for="filter-business-name">Business Name (Source/Dest)</label>
            <input type="text" name="filter-business-name" id="filter-business-name" class="form-control"
                   placeholder="Business Name">
        </div>

        <input type="hidden" id="filter-limit" value="10">

        <div class="col-md-3 form-group d-flex align-items-end">
            <button id="apply-filter-btn" class="btn btn-primary btn-block"><i class="bx bx-filter-alt"></i> Apply
                Filter
            </button>
        </div>

        <div class="col-md-3 d-flex form-group align-items-end">
            <button id="reset-filter-btn" type="button" class="btn btn-secondary btn-block" title="Reset Filter">
                <i class="bx bx-reset"></i> Reset & Reload Data
            </button>
        </div>
    </div>
</form>