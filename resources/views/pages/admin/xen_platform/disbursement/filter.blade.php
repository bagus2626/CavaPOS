<form id="disbursement-filter-form">
    <div class="row">
        <div class="col-md-4">
            <label for="filter-search">Search</label>
            <input type="text" class="form-control" id="filter-search" name="search"
                   placeholder="Ref ID, Partner Name, Amount, Status, etc...">
        </div>

        {{-- Filter by Status --}}
        <div class="col-md-2">
            <label for="filter-status">Status</label>
            <select class="form-control" id="filter-status" name="status">
                <option value="">All Status</option>
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
            <label>Date Created</label>
            <fieldset class="form-group position-relative has-icon-left m-0">
                <input type="text" class="form-control daterange-payout"
                       name="date_payout" id="daterange-payout"
                       placeholder="Date created">
                <div class="form-control-position">
                    <i class="bx bx-calendar-check"></i>
                </div>
            </fieldset>
        </div>

        {{-- Action Buttons --}}
        <div class="col-3">
            <div class="row">
                <div class="d-flex w-md-auto mt-2 ml-1">
                    <button type="submit" class="btn btn-primary btn-block" title="Filter">
                        <i class="bx bx-filter-alt"></i> Apply Filter
                    </button>
                </div>
                <div class="d-flex w-md-auto mt-2 ml-1">
                    <button type="button" class="btn btn-secondary btn-block" id="reset-filter-btn"
                            title="Reset Filter">
                        <i class="bx bx-reset"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>