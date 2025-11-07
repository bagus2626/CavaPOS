<h4 class="fw-bold mb-1">Transactions</h4>
<div class="row">
    <!-- Total Incoming Amount -->
    <div class="col-md-6">
        <div class="card border rounded-3">
            <div class="card-body d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex align-items-center">
                    <i class="bx bx-down-arrow-alt text-success fs-3 me-2"></i>
                    <div>
                        <small class="text-muted d-block">Total Incoming Amount</small>
                        <h2 class="mb-0 fw-bold text-dark">
                            IDR {{ number_format($data['summary']['incoming_amount'] ?? 0) }}</h2>
                    </div>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Transactions Count</small>
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
                    <i class="bx bx-up-arrow-alt text-danger fs-3 me-2"></i>
                    <div>
                        <small class="text-muted d-block">Total Outgoing Amount</small>
                        <h2 class="mb-0 fw-bold text-dark">IDR {{ number_format($data['summary']['outgoing_amount'] ?? 0) }}</h2>
                    </div>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Transactions Count</small>
                    <span class="text-muted fw-semibold">{{ $data['summary']['outgoing_count'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>