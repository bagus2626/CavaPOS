<div class="modal fade text-left" id="modal-profile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel140" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title white" id="myModalLabel140">Account Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <form id="createAccountForm" method="POST" action=#">
            @csrf
            <input type="hidden" name="partner_id" id="partner_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Name</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="business-name" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label>Email</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="email" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label>Account ID</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="account-id" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label>Date Created</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="date-created" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label>Account Type</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="account-type" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label>Account Status</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="account-status" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Close</span>
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
