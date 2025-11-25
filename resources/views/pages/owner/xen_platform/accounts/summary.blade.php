<h4 class="fw-bold mb-1">{{ __('messages.owner.xen_platform.accounts.transactions') }}</h4>
<div class="row">
    <!-- Total Incoming Amount -->
    <div class="col-md-6">
        <div class="card border rounded-3">
            <div class="card-body d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-arrow-down fa-lg text-success fs-3 me-2"></i>
                    <div>
                        <small class="text-muted d-block">{{ __('messages.owner.xen_platform.accounts.total_incoming_amount') }}</small>
                        <h2 class="mb-0 fw-bold text-dark">
                            IDR {{ number_format($data['summary']['incoming_amount'] ?? 0) }}</h2>
                    </div>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">{{ __('messages.owner.xen_platform.accounts.transactions_count') }}</small>
                    <span class="text-muted fw-semibold">{{ $data['summary']['incoming_count'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Outgoing Amount -->
    <div class="col-md-6">
        <div class="card border rounded-3">
            <div class="card-body d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-arrow-up fa-lg text-danger fs-3 me-2"></i>
                    <div>
                        <small class="text-muted d-block">{{ __('messages.owner.xen_platform.accounts.total_outgoing_amount') }}</small>
                        <h2 class="mb-0 fw-bold text-dark">IDR {{ number_format($data['summary']['outgoing_amount'] ?? 0) }}</h2>
                    </div>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">{{ __('messages.owner.xen_platform.accounts.transactions_count') }}</small>
                    <span class="text-muted fw-semibold">{{ $data['summary']['outgoing_count'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>