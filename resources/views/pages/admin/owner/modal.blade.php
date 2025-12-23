<div class="modal fade text-left" id="xenditRegistrationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel140" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title white" id="myModalLabel140">Xendit Account Registration</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <form id="createAccountForm" method="POST">
                @csrf
                <input type="hidden" name="partner_id" id="partner_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Partner Email : </label>
                        <input class="form-control" type="text" id="partner_email" name="partner_email" readonly required>
                    </div>

                    <div class="form-group">
                        <label>Business Name : </label>
                        <input class="form-control" type="text" placeholder="Silahkan masukan nama bisnis" id="business_name"
                               name="business_name" readonly required>
                    </div>

                    <div class="form-group">
                        <label for="account_type" class="form-label">Account Type</label>
                        <select name="account_type" id="account_type" class="form-control" required>
                            <option value="">-- Select Type --</option>
                            <option value="OWNED">OWNED</option>
                            {{-- <option value="MANAGED">MANAGED</option> --}}
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </button>

                    <button type="submit" class="btn btn-secondary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Submit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>