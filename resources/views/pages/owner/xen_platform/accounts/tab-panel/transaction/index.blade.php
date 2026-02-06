<div class="row g-4">
    <div class="col-12">
        <div class="modern-card">
            <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                @include('pages.owner.xen_platform.accounts.account')
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="transactions-summary" id="transactions-summary-div">
            @include('pages.owner.xen_platform.accounts.summary')
        </div>
    </div>

    <div class="col-12">
        <div class="modern-card mb-4">
            <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                @include('pages.owner.xen_platform.accounts.tab-panel.transaction.filter')
            </div>
        </div>

        <div class="modern-card" id="show-data-transaction">
                @include('pages.owner.xen_platform.accounts.tab-panel.transaction.table')
        </div>
    </div>
</div>