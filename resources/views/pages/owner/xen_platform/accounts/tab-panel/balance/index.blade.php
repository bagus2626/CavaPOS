<div class="row g-4">
    <!-- Account Section -->
    <div class="col-12">
        <div class="modern-card">
            <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                @include('pages.owner.xen_platform.accounts.account')
            </div>
        </div>
    </div>

    <!-- Balance Filter -->
    <div class="col-12">
        <div class="modern-card mb-4">
            <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                @include('pages.owner.xen_platform.accounts.tab-panel.balance.filter')
            </div>
        </div>

        <!-- Balance Table -->
        <div class="modern-card">
            <div class="data-table-wrapper" id="show-data-balance">
                @include('pages.owner.xen_platform.accounts.tab-panel.balance.table')
            </div>
        </div>
    </div>
</div>