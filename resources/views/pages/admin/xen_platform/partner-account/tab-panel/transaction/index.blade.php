<div class="row">
    <div class="col-12">
        <div class="card border-top border-4 border">
            <div class="card-header border-bottom d-flex justify-content-between">
                @include('pages.admin.xen_platform.partner-account.tab-panel.account')
            </div>

            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="row">
                        <div class="col-12">
                            <div class="transactions-summary mt-2" id="transactions-summary-div">
                                @include('pages.admin.xen_platform.partner-account.tab-panel.summary')
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card border-top border-4 border">
                                <div class="card-header border-bottom">
                                    @include('pages.admin.xen_platform.partner-account.tab-panel.transaction.filter')
                                </div>

                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="table-responsive mt-1" id="show-data-transaction">
                                            @include('pages.admin.xen_platform.partner-account.tab-panel.transaction.table')
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