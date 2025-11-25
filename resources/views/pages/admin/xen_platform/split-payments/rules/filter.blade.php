<form id="split-rules-filter-form">
        <div class="row">
            <div class="col-md-3 form-group">
                <label for="search_rules_id_or_name">Split Rules (ID/Name)</label>
                <input type="text" name="search_rules_id_or_name" id="search_rules_id_or_name" class="form-control"
                       placeholder="Cari split rule ID/Name...">
            </div>

            <div class="col-md-3 form-group">
                <label for="filter-business-name">Business Name (Source/Dest)</label>
                <input type="text" name="filter_business_name" id="filter_business_name_rules" class="form-control"
                       placeholder="Nama Bisnis">
            </div>

            <div class="col-md-3 form-group">
                <label>Date Created</label>
                <fieldset class="form-group position-relative has-icon-left m-0">
                    <input type="text" class="form-control daterange-rules"
                           name="date_rules" id="daterange-rules"
                           placeholder="Date created">
                    <div class="form-control-position">
                        <i class="bx bx-calendar-check"></i>
                    </div>
                </fieldset>
            </div>

            <div class="col-md-3 d-flex">
                <div class="col-md-6 form-group d-flex align-items-end px-0">
                    <button id="apply-rules-filter-btn" class="btn btn-primary btn-block"><i class="bx bx-filter-alt"></i>
                        Apply Filter
                    </button>
                </div>

                <div class="col-md-6 d-flex form-group align-items-end">
                    <button id="reset-rules-filter-btn" type="button" class="btn btn-secondary btn-block" title="Reset Filter">
                        <i class="bx bx-reset"></i> Reset
                    </button>
                </div>
            </div>
        </div>
</form>
