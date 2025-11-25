<div class="row">
    <div class="col-12">
        <div class="card border-top border-4 border">
            <div class="card-header border-bottom d-flex justify-content-between">
                @include('pages.owner.xen_platform.accounts.account')
            </div>

            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="card border-top border-4 border">
                                <div class="card-header border-bottom">
                                    @include('pages.owner.xen_platform.accounts.tab-panel.balance.filter')
                                </div>

                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="table-responsive mt-1" id="show-data-balance">
                                            @include('pages.owner.xen_platform.accounts.tab-panel.balance.table')
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
