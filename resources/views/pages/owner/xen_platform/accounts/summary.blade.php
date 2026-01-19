
<div class="row">
    <!-- Total Incoming Amount -->
    <div class="col-12 col-sm-6 mb-3">
        <div class="modern-card stats-card stats-success">
            <div class="stats-icon">
                <span class="material-symbols-outlined">arrow_downward</span>
            </div>
            <div class="stats-content">
                <div class="stats-label">{{ __('messages.owner.xen_platform.accounts.total_incoming_amount') }}</div>
                <div class="stats-value">IDR {{ number_format($data['summary']['incoming_amount'] ?? 0) }}</div>
                <div class="stats-meta">
                    <span class="material-symbols-outlined">receipt</span>
                    {{ $data['summary']['incoming_count'] ?? 0 }} {{ __('messages.owner.xen_platform.accounts.transactions') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Total Outgoing Amount -->
    <div class="col-12 col-sm-6 mb-3">
        <div class="modern-card stats-card stats-danger">
            <div class="stats-icon">
                <span class="material-symbols-outlined">arrow_upward</span>
            </div>
            <div class="stats-content">
                <div class="stats-label">{{ __('messages.owner.xen_platform.accounts.total_outgoing_amount') }}</div>
                <div class="stats-value">IDR {{ number_format($data['summary']['outgoing_amount'] ?? 0) }}</div>
                <div class="stats-meta">
                    <span class="material-symbols-outlined">receipt</span>
                    {{ $data['summary']['outgoing_count'] ?? 0 }} {{ __('messages.owner.xen_platform.accounts.transactions') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>

.stats-meta {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.875rem;
    color: var(--color-text-secondary, #6b7280);
    margin-top: 0.5rem;
}

.stats-meta .material-symbols-outlined {
    font-size: 1rem;
}
</style>