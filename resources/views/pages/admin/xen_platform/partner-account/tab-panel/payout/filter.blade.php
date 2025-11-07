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

        {{-- Filter Date From --}}
        <div class="col-md-2">
            <label for="filter-date-from">Tanggal Awal</label>
            <input type="date" class="form-control" id="filter-date-from" name="date_from">
        </div>

        {{-- Filter Date To --}}
        <div class="col-md-2">
            <label for="filter-date-to">Tanggal Akhir</label>
            <input type="date" class="form-control" id="filter-date-to" name="date_to">
        </div>

        {{-- Action Buttons --}}
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-info btn-block" title="Filter">
                <i class="bx bx-filter-alt"></i>
            </button>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-secondary btn-block" id="reset-filter-btn" title="Reset Filter">
                <i class="bx bx-reset"></i>
            </button>
        </div>
    </div>
</form>
