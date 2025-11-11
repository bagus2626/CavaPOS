<form id="disbursement-filter-form">
    <div class="row">
        <div class="col-md-4">
            <label for="filter-search">Cari</label>
            <input type="text" class="form-control" id="filter-search" name="search" placeholder="Ref ID, Nama Partner, Jumlah, Status, dll...">
        </div>

        {{-- Filter by Status --}}
        <div class="col-md-2">
            <label for="filter-status">Status</label>
            <select class="form-control" id="filter-status" name="status">
                <option value="">Semua Status</option>
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
        <div class="col-md-1 mt-2 align-items-end">
            <button type="submit" class="btn btn-info btn-block" title="Filter">
                <i class="bx bx-filter-alt"></i>
            </button>
        </div>
        <div class="col-md-1 mt-2 align-items-end">
            <button type="button" class="btn btn-secondary btn-block" id="reset-filter-btn" title="Reset Filter">
                <i class="bx bx-reset"></i>
            </button>
        </div>
    </div>
</form>
