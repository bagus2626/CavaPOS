<div class="row">
    <div class="col-md-3 form-group">
        <label for="filter-email">Email (Separate with commas)</label>
        <input type="text" id="filter-email" class="form-control" placeholder="test@mail.com, other@mail.com">
    </div>

    <div class="col-md-3 form-group">
        <label for="filter-business-name">Business Name</label>
        <input type="text" id="filter-business-name" class="form-control" placeholder="Business Name">
    </div>

    <div class="col-md-3">
        <label>Status (Multi-select)</label>
        <div class="form-group">
            <select id="filter-status" class="select2 form-control select-light-secondary"
                    data-placeholder="Select status..." multiple>
                <option value="INVITED">INVITED</option>
                <option value="REGISTERED">REGISTERED</option>
                <option value="AWAITING_DOCS">AWAITING_DOCS</option>
                <option value="LIVE">LIVE</option>
                <option value="SUSPENDED">SUSPENDED</option>
            </select>
        </div>
    </div>

    <div class="col-md-3 form-group">
        <label for="filter-type">Account Type</label>
        <select id="filter-type" class="select2 form-control select-light-secondary" data-placeholder="Select type..." multiple>
            <option value="MANAGED">MANAGED</option>
            <option value="OWNED">OWNED</option>
            <option value="CUSTOM">CUSTOM</option>
        </select>
    </div>

    <div class="col-md-3 form-group">
        <label for="filter-type">Created Date</label>
        <fieldset class="form-group position-relative has-icon-left m-0">
            <input type="text" class="form-control daterange-account-created"
                   name="created_date" id="daterange-account-created"
                   placeholder="Tanggal Transaksi">
            <div class="form-control-position">
                <i class="bx bx-calendar-check"></i>
            </div>
        </fieldset>
    </div>

    <input type="hidden" id="filter-limit" value="10">

    <div class="col-md-3 form-group d-flex align-items-end">
        <button id="apply-filter-btn" class="btn btn-primary btn-block"><i class="bx bx-filter-alt"></i> Terapkan Filter</button>
    </div>

    <div class="col-md-3 form-group d-flex align-items-end">
        <button type="button" class="btn btn-secondary btn-block" id="reset-filter-btn" onclick="resetFilters()"><i class="bx bx-refresh"></i> Reset & Reload Data</button>
    </div>
</div>

