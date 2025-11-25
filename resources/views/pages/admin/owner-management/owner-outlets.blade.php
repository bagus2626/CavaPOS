@extends('pages.admin.layouts.app')

@section('content-header')
    <div class="content-header-left col-12 mb-2 mt-1">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h5 class="content-header-title float-left pr-1 mb-0">Owner Management</h5>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb p-0 mb-0">
                        <li class="breadcrumb-item"><a href="#"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.owner-list.index') }}">Owner List</a>
                        </li>
                        <li class="breadcrumb-item active">Owner Outlet</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Owner Info Header -->
    <section id="owner-header">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h2 class="text-dark font-weight-bold mb-1">{{ $owner->name }}</h2>
                                <div class="text-dark-50">
                                    <div class="d-flex align-items-center mb-50">
                                        <i class="bx bx-envelope"></i>
                                        <span class="ml-50">{{ $owner->email }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-50">
                                        <i class="bx bx-phone"></i>
                                        <span class="ml-50">{{ $owner->phone_number ?? 'N/A' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-50">
                                        <i class="bx bx-calendar-check"></i>
                                        <span class="ml-50">Joined at {{ $owner->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                @if ($owner->is_active)
                                    <span class="badge badge-success badge-pill">Active Owner</span>
                                @else
                                    <span class="badge badge-danger badge-pill">Inactive Owner</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Widgets Statistics start -->
    <section id="widgets-Statistics">
        <div class="row">
            <div class="col-xl-4 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto my-1">
                                <i class="bx bx-store font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Total Outlets</p>
                            <h2 class="mb-0">{{ number_format($totalOutlets ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto my-1">
                                <i class="bx bx-check-circle font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Active Outlets</p>
                            <h2 class="mb-0">{{ number_format($activeOutlets ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-danger mx-auto my-1">
                                <i class="bx bx-x-circle font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Inactive Outlets</p>
                            <h2 class="mb-0">{{ number_format($inactiveOutlets ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Widgets Statistics End -->

    <!-- Deactivation Reason Modal -->
    <div class="modal fade text-left" id="deactivationOutletModal" tabindex="-1" role="dialog"
        aria-labelledby="deactivationOutletModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title text-white" id="deactivationOutletModalLabel">
                        Deactivate Outlet Account
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="alert border-danger">
                        <div class="alert-body d-flex align-items-center">
                            <i class="bx bx-error"></i>
                            You are about to deactivate: <strong id="outletNameDisplay"></strong>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deactivationOutletReason" class="font-weight-bold">
                            Deactivation Reason <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="deactivationOutletReason" rows="4"
                            placeholder="Please explain why you are deactivating this outlet account..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmOutletDeactivation" disabled>
                        Confirm Deactivation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Activation Confirmation Modal -->
    <div class="modal fade text-left" id="activationOutletModal" tabindex="-1" role="dialog"
        aria-labelledby="activationOutletModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title text-white" id="activationOutletModalLabel">
                        Activate Outlet Account
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="alert border-info">
                        <div class="alert-body d-flex align-items-center">
                            <i class="bx bx-info-circle"></i>
                            You are about to activate: <strong id="outletNameDisplayActivation"></strong>
                        </div>
                    </div>

                    <p class="mb-0">Are you sure you want to activate this outlet account?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="confirmOutletActivation">
                        Confirm Activation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Outlets Table start -->
    <div class="row" id="outlets-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Outlets List</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-muted mb-0">View all outlets owned by {{ $owner->name }}.</p>
                            <div class="d-flex align-items-center">
                                <label class="text-muted mb-0 mr-1">Search:</label>
                                <input type="search" id="outletSearch" class="form-control">
                            </div>
                        </div>
                    </div>
                    <!-- table -->
                    <div class="table-responsive">
                        <table class="table mb-0" id="outletsTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>NO</th>
                                    <th>OUTLET NAME</th>
                                    <th>USERNAME</th>
                                    <th>LOCATION</th>
                                    <th>ADDRESS</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                    <th>DETAIL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outlets as $index => $outlet)
                                    @include('pages.admin.owner-management.partials.outlet-row')
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            No outlets found for this owner
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-body">
                        <div id="paginationContainer">
                            @if ($outlets->total() > 0)
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <div class="pagination-summary text-muted">
                                        Showing {{ $outlets->firstItem() }} - {{ $outlets->lastItem() }} from
                                        {{ $outlets->total() }} entries
                                    </div>
                                    <div class="pagination-links">
                                        {{ $outlets->appends(request()->query())->links('vendor.pagination.custom-limited') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Outlets Table end -->
@endsection

@push('styles')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #outletSearch {
            width: 250px;
        }

        /* Smooth loading overlay */
        .table-loading-overlay {
            position: relative;
        }

        .table-loading-overlay::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            display: none;
            z-index: 10;
        }

        .table-loading-overlay.loading::after {
            display: block;
        }

        .table-loading-overlay.loading tbody {
            opacity: 0.4;
            pointer-events: none;
        }

        /* Smooth fade transition */
        #outletsTable tbody {
            transition: opacity 0.2s ease-in-out;
        }

        /* Loading spinner */
        .search-loading {
            display: none;
            margin-left: 8px;
        }

        .search-loading.active {
            display: inline-block;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let searchTimeout;
            let currentRequest = null;
            const ownerId = {{ $owner->id }};

            // Outlet status toggles
            let currentOutletId = null;
            let currentOwnerId = null;
            let currentToggle = null;
            const deactivationOutletReasonTextarea = document.getElementById('deactivationOutletReason');
            const confirmOutletDeactivationBtn = document.getElementById('confirmOutletDeactivation');

            // Initialize tooltips
            if (typeof $('[data-toggle="tooltip"]').tooltip === 'function') {
                $('[data-toggle="tooltip"]').tooltip();
            }

            // Enable/disable confirm button based on textarea input
            if (deactivationOutletReasonTextarea && confirmOutletDeactivationBtn) {
                deactivationOutletReasonTextarea.addEventListener('input', function() {
                    confirmOutletDeactivationBtn.disabled = this.value.trim().length === 0;
                });
            }

            // Handle all outlet status toggles (using event delegation)
            $(document).on('change', '.outlet-status-toggle', function(e) {
                const isActive = this.checked;
                const outletId = $(this).data('outlet-id');
                const ownerId = $(this).data('owner-id');
                const outletName = $(this).data('outlet-name');

                currentOutletId = outletId;
                currentOwnerId = ownerId;
                currentToggle = this;

                if (!isActive) {
                    e.preventDefault();
                    this.checked = true;
                    document.getElementById('outletNameDisplay').textContent = outletName;
                    document.getElementById('deactivationOutletReason').value = '';
                    confirmOutletDeactivationBtn.disabled = true;
                    $('#deactivationOutletModal').modal('show');
                } else {
                    e.preventDefault();
                    this.checked = false;
                    document.getElementById('outletNameDisplayActivation').textContent = outletName;
                    $('#activationOutletModal').modal('show');
                }
            });

            // Handle deactivation confirmation
            if (confirmOutletDeactivationBtn) {
                confirmOutletDeactivationBtn.addEventListener('click', function() {
                    const reason = deactivationOutletReasonTextarea.value.trim();
                    if (reason.length === 0) return;

                    if (currentOutletId && currentOwnerId && currentToggle) {
                        updateOutletStatus(currentOwnerId, currentOutletId, false, reason, currentToggle);
                        $('#deactivationOutletModal').modal('hide');
                    }
                });
            }

            // Handle activation confirmation
            document.getElementById('confirmOutletActivation').addEventListener('click', function() {
                if (currentOutletId && currentOwnerId && currentToggle) {
                    updateOutletStatus(currentOwnerId, currentOutletId, true, null, currentToggle);
                    $('#activationOutletModal').modal('hide');
                }
            });

            // Reset on modal close
            $('#deactivationOutletModal, #activationOutletModal').on('hidden.bs.modal', function() {
                currentOutletId = null;
                currentOwnerId = null;
                currentToggle = null;
            });

            // Update outlet status via AJAX
            function updateOutletStatus(ownerId, outletId, isActive, reason, toggleElement) {
                toggleElement.disabled = true;

                fetch(`/admin/owner-list/${ownerId}/outlets/${outletId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            is_active_admin: isActive ? 1 : 0,
                            deactivation_reason: reason
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toggleElement.checked = isActive;
                            toggleElement.disabled = false;

                            const row = toggleElement.closest('tr');
                            const statusCell = row.querySelector('td:nth-child(6)');
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
                                        $(badge).tooltip();
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

            // Function to load outlets
            function loadOutlets(searchValue = '', page = 1) {
                // Abort previous request if still running
                if (currentRequest) {
                    currentRequest.abort();
                }

                currentRequest = $.ajax({
                    url: "{{ route('admin.owner-list.outlets', $owner->id) }}",
                    type: 'GET',
                    data: {
                        search: searchValue,
                        page: page,
                        ajax: 1
                    },
                    beforeSend: function() {
                        // Show loading indicator
                        $('.search-loading').addClass('active');
                        $('#tableContainer').addClass('loading');
                    },
                    success: function(response) {
                        // Smooth fade effect
                        $('#outletsTable tbody').css('opacity', '0');

                        setTimeout(function() {
                            $('#outletsTable tbody').html(response.table);

                            // Update pagination
                            if (response.pagination) {
                                $('#paginationContainer').html(response.pagination);
                            } else {
                                $('#paginationContainer').html('');
                            }

                            // Fade in
                            $('#outletsTable tbody').css('opacity', '1');

                            // Update URL without reload
                            let url = new URL(window.location.href);
                            if (searchValue) {
                                url.searchParams.set('search', searchValue);
                            } else {
                                url.searchParams.delete('search');
                            }
                            if (page > 1) {
                                url.searchParams.set('page', page);
                            } else {
                                url.searchParams.delete('page');
                            }
                            window.history.pushState({}, '', url);
                        }, 150);
                    },
                    error: function(xhr) {
                        // Only show error if not aborted
                        if (xhr.statusText !== 'abort') {
                            $('#outletsTable tbody').html(
                                '<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>'
                            );
                        }
                    },
                    complete: function() {
                        // Hide loading indicator
                        $('.search-loading').removeClass('active');
                        $('#tableContainer').removeClass('loading');
                        currentRequest = null;
                    }
                });
            }

            // Search on keyup with debounce
            $('#outletSearch').on('keyup', function() {
                clearTimeout(searchTimeout);
                let searchValue = $(this).val();

                // Longer delay for better UX
                searchTimeout = setTimeout(function() {
                    loadOutlets(searchValue, 1);
                }, 600);
            });

            // Handle pagination clicks
            $(document).on('click', '#paginationContainer .pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let page = new URL(url).searchParams.get('page') || 1;
                let searchValue = $('#outletSearch').val();

                // Smooth scroll to top of table
                $('html, body').animate({
                    scrollTop: $("#outlets-table").offset().top - 100
                }, 400);

                loadOutlets(searchValue, page);
            });

            // Set search input value from URL parameter on page load
            let urlParams = new URLSearchParams(window.location.search);
            let searchParam = urlParams.get('search');
            if (searchParam) {
                $('#outletSearch').val(searchParam);
            }
        });
    </script>
@endpush
