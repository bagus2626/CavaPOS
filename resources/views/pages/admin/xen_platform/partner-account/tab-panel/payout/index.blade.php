<div class="row">
    <div class="col-12">
        <div class="card border-top border-4 border">
            <div class="card-header border-bottom d-flex justify-content-between">
                @include('pages.admin.xen_platform.partner-account.tab-panel.account')
            </div>

            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="card border-top border-4 border">
                                <div class="card-header border-bottom">
                                    @include('pages.admin.xen_platform.partner-account.tab-panel.payout.filter')
                                </div>

                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="table-responsive mt-1" id="show-data-payout">
                                            @include('pages.admin.xen_platform.partner-account.tab-panel.payout.table')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>