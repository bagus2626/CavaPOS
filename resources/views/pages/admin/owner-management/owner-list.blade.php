@extends('pages.admin.layouts.app')

@section('content-header')
    <div class="content-header-left col-12 mb-2 mt-1">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h5 class="content-header-title float-left pr-1 mb-0">Owner Management</h5>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb p-0 mb-0">
                        <li class="breadcrumb-item"><a href="#"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active">Owner List
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Widgets Statistics start -->
    <section id="widgets-Statistics">
        <div class="row">
            <!-- Total Owners Card -->
            <div class="col-xl-4 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto my-1">
                                <i class="bx bx-group font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Total Owners</p>
                            <h2 class="mb-0">{{ number_format($totalOwners ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Owners Card -->
            <div class="col-xl-4 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto my-1">
                                <i class="bx bx-check-circle font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Active Owners</p>
                            <h2 class="mb-0">{{ number_format($activeOwners ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inactive Owners Card -->
            <div class="col-xl-4 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-danger mx-auto my-1">
                                <i class="bx bx-x-circle font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Inactive Owners</p>
                            <h2 class="mb-0">{{ number_format($inactiveOwners ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Widgets Statistics End -->

    <!-- Filter Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <!-- Account Status -->
                            <div class="col-md-3 col-12">
                                <label for="status-filter" class="d-block">Account Status</label>
                                <select class="form-control" id="status-filter">
                                    <option value="all" selected>All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <!-- Search -->
                            <div class="col-md-6 col-12">
                                <label for="search-input" class="d-block">Search</label>
                                <input type="text" class="form-control" id="search-input"
                                    placeholder="Search by name, email, or phone...">
                            </div>

                            <!-- Apply Filter Button -->
                            <div class="col-md-3 col-12 d-flex align-items-end">
                                <button class="btn btn-dark btn-block" id="filter-btn">
                                    <i class="bx bx-filter-alt mr-50"></i>
                                    Apply Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deactivation Reason Modal -->
    <div class="modal fade text-left" id="deactivationModal" tabindex="-1" role="dialog"
        aria-labelledby="deactivationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title text-white" id="deactivationModalLabel">
                        Deactivate Owner Account
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="alert border-danger">
                        <div class="alert-body d-flex align-items-center">
                            <i class="bx bx-error"></i>
                            You are about to deactivate: <strong id="ownerNameDisplay"></strong>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deactivationReason" class="font-weight-bold">
                            Deactivation Reason <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="deactivationReason" rows="4"
                            placeholder="Please explain why you are deactivating this owner account..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeactivation" disabled>
                        Confirm Deactivation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Activation Confirmation Modal -->
    <div class="modal fade text-left" id="activationModal" tabindex="-1" role="dialog"
        aria-labelledby="activationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title text-white" id="activationModalLabel">
                        Activate Owner Account
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="alert border-info">
                        <div class="alert-body d-flex align-items-center">
                            <i class="bx bx-info-circle"></i>
                            You are about to activate: <strong id="ownerNameDisplayActivation"></strong>
                        </div>
                    </div>

                    <p class="mb-0">Are you sure you want to activate this owner account?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="confirmActivation">
                        Confirm Activation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Owner List Table start -->
    <div class="row" id="owners-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Owner List</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <p class="text-muted mb-0">Manage and view all owner accounts</p>
                    </div>
                    <!-- table -->
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>NO</th>
                                    <th>OWNER NAME</th>
                                    <th>CONTACT INFORMATION</th>
                                    <th>TOTAL OUTLET</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th> {{-- KOLOM BARU --}}
                                    <th>JOINED DATE</th>
                                    <th>OUTLET</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($owners as $index => $owner)
                                    <tr>
                                        <td class="text-left">
                                            <span class="text-bold-500">{{ $owners->firstItem() + $index }}</span>
                                        </td>
                                        <td>
                                            <h6 class="mb-0 text-bold-500">{{ $owner->name }}</h6>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="d-flex align-items-center text-muted mb-25">
                                                    <i class="bx bx-envelope mr-50"></i>
                                                    <span>{{ $owner->email }}</span>
                                                </div>
                                                <div class="d-flex align-items-center text-muted">
                                                    <i class="bx bx-phone mr-50"></i>
                                                    <span>{{ $owner->phone_number ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-bold-500">{{ $owner->users_count }}</span>
                                            <span class="text-muted">
                                                {{ $owner->users_count == 1 ? 'Outlet' : 'Outlets' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($owner->is_active)
                                                <span class="badge badge-success badge-pill">
                                                    Active
                                                </span>
                                            @else
                                                <span class="badge badge-danger badge-pill"
                                                    @if ($owner->deactivation_reason) data-toggle="tooltip" 
                                                        data-placement="top"
                                                        title="{{ $owner->deactivation_reason }}" @endif>
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="custom-control custom-switch custom-switch-success">
                                                    <input type="checkbox"
                                                        class="custom-control-input owner-status-toggle"
                                                        id="ownerSwitch{{ $owner->id }}"
                                                        data-owner-id="{{ $owner->id }}"
                                                        data-owner-name="{{ $owner->name }}"
                                                        {{ $owner->is_active ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="ownerSwitch{{ $owner->id }}"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="text-muted d-block">{{ $owner->created_at->format('d M Y') }}</span>
                                            <span class="text-muted">{{ $owner->created_at->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.owner-list.outlets', $owner->id) }}"
                                                title="View Outlets">
                                                <i
                                                    class="badge-circle badge-circle-light-secondary bx bx-show font-medium-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            No owners found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($owners->total() > 0)
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <div class="pagination-summary text-muted">
                                    Showing {{ $owners->firstItem() }} - {{ $owners->lastItem() }} from
                                    {{ $owners->total() }} entries
                                </div>
                                <div class="pagination-links">
                                    {{ $owners->appends(request()->query())->links('vendor.pagination.custom-limited') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Owner List Table end -->
@endsection

@push('styles')
    <style>
        /* Fix tooltip flickering */
        .badge[data-toggle="tooltip"] {
            cursor: pointer;
        }

        .tooltip {
            pointer-events: none;
        }

        .tooltip-inner {
            max-width: 300px;
            text-align: left;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter Code
            const statusFilter = document.getElementById('status-filter');
            const searchInput = document.getElementById('search-input');
            const filterBtn = document.getElementById('filter-btn');
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('status')) {
                statusFilter.value = urlParams.get('status');
            }

            if (urlParams.has('search')) {
                searchInput.value = urlParams.get('search');
            }

            filterBtn.addEventListener('click', function() {
                applyFilters();
            });

            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    applyFilters();
                }
            });

            function applyFilters() {
                const url = new URL(window.location.href);
                const params = new URLSearchParams(url.search);

                const status = statusFilter.value;
                if (status && status !== 'all') {
                    params.set('status', status);
                } else {
                    params.delete('status');
                }

                const search = searchInput.value.trim();
                if (search) {
                    params.set('search', search);
                } else {
                    params.delete('search');
                }

                params.delete('page');
                window.location.href = url.pathname + '?' + params.toString();
            }

            // Owner Status Toggle
            let currentOwnerId = null;
            let currentToggle = null;
            const deactivationReasonTextarea = document.getElementById('deactivationReason');
            const confirmDeactivationBtn = document.getElementById('confirmDeactivation');

            // Initialize tooltips
            if (typeof $('[data-toggle="tooltip"]').tooltip === 'function') {
                $('[data-toggle="tooltip"]').tooltip({
                    trigger: 'hover',
                    boundary: 'window',
                    container: 'body'
                });
            }

            // Enable/disable confirm button based on textarea input
            if (deactivationReasonTextarea && confirmDeactivationBtn) {
                deactivationReasonTextarea.addEventListener('input', function() {
                    confirmDeactivationBtn.disabled = this.value.trim().length === 0;
                });
            }

            // Handle all owner status toggles
            document.querySelectorAll('.owner-status-toggle').forEach(function(toggle) {
                toggle.addEventListener('change', function(e) {
                    const isActive = this.checked;
                    const ownerId = this.dataset.ownerId;
                    const ownerName = this.dataset.ownerName;

                    currentOwnerId = ownerId;
                    currentToggle = this;

                    if (!isActive) {
                        e.preventDefault();
                        this.checked = true;
                        document.getElementById('ownerNameDisplay').textContent = ownerName;
                        document.getElementById('deactivationReason').value = '';
                        confirmDeactivationBtn.disabled = true;
                        $('#deactivationModal').modal('show');
                    } else {
                        e.preventDefault();
                        this.checked = false;
                        document.getElementById('ownerNameDisplayActivation').textContent =
                            ownerName;
                        $('#activationModal').modal('show');
                    }
                });
            });

            // Handle deactivation confirmation
            confirmDeactivationBtn.addEventListener('click', function() {
                const reason = deactivationReasonTextarea.value.trim();
                if (reason.length === 0) return;

                if (currentOwnerId && currentToggle) {
                    updateOwnerStatus(currentOwnerId, false, reason, currentToggle);
                    $('#deactivationModal').modal('hide');
                }
            });

            // Handle activation confirmation
            document.getElementById('confirmActivation').addEventListener('click', function() {
                if (currentOwnerId && currentToggle) {
                    updateOwnerStatus(currentOwnerId, true, null, currentToggle);
                    $('#activationModal').modal('hide');
                }
            });

            // Reset on modal close
            $('#deactivationModal, #activationModal').on('hidden.bs.modal', function() {
                currentOwnerId = null;
                currentToggle = null;
            });

            // Update owner status via AJAX
            function updateOwnerStatus(ownerId, isActive, reason, toggleElement) {
                toggleElement.disabled = true;

                fetch(`/admin/owner-list/${ownerId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            is_active: isActive ? 1 : 0,
                            deactivation_reason: reason
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toggleElement.checked = isActive;
                            toggleElement.disabled = false;

                            const row = toggleElement.closest('tr');
                            const statusCell = row.querySelector('td:nth-child(5)');
                            const badge = statusCell.querySelector('.badge');

                            if (isActive) {
                                badge.className = 'badge badge-success badge-pill';
                                badge.textContent = 'Active';
                                badge.removeAttribute('data-toggle');
                                badge.removeAttribute('data-placement');
                                badge.removeAttribute('title');
                                badge.removeAttribute('data-original-title');

                                if (typeof $(badge).tooltip === 'function') {
                                    $(badge).tooltip('dispose');
                                }
                            } else {
                                badge.className = 'badge badge-danger badge-pill';
                                badge.textContent = 'Inactive';

                                if (reason) {
                                    badge.setAttribute('data-toggle', 'tooltip');
                                    badge.setAttribute('data-placement', 'top');
                                    badge.setAttribute('title', reason);

                                    if (typeof $(badge).tooltip === 'function') {
                                        $(badge).tooltip({
                                            trigger: 'hover',
                                            boundary: 'window',
                                            container: 'body'
                                        });
                                    }
                                }
                            }

                            // Show success message
                            toastr.info(data.message || 'Status updated successfully');
                        } else {
                            toggleElement.checked = !isActive;
                            toggleElement.disabled = false;
                            alert(data.message || 'Failed to update status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toggleElement.checked = !isActive;
                        toggleElement.disabled = false;
                        alert('An error occurred while updating status');
                    });
            }
        });
    </script>
@endpush
