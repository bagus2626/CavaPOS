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
                                                <span class="badge badge-success badge-pill">Active</span>
                                            @else
                                                <span class="badge badge-danger badge-pill">Inactive</span>
                                            @endif
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
                                        <td colspan="7" class="text-center text-muted">
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('status-filter');
            const searchInput = document.getElementById('search-input');
            const filterBtn = document.getElementById('filter-btn');

            // Set filter values from URL on page load
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('status')) {
                statusFilter.value = urlParams.get('status');
            }

            if (urlParams.has('search')) {
                searchInput.value = urlParams.get('search');
            }

            // Apply filter when button clicked
            filterBtn.addEventListener('click', function() {
                applyFilters();
            });

            // Search when Enter key is pressed
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    applyFilters();
                }
            });

            function applyFilters() {
                const url = new URL(window.location.href);
                const params = new URLSearchParams(url.search);

                // Set status parameter
                const status = statusFilter.value;
                if (status && status !== 'all') {
                    params.set('status', status);
                } else {
                    params.delete('status');
                }

                // Set search parameter
                const search = searchInput.value.trim();
                if (search) {
                    params.set('search', search);
                } else {
                    params.delete('search');
                }

                // Reset to first page
                params.delete('page');

                // Redirect with new parameters
                window.location.href = url.pathname + '?' + params.toString();
            }
        });
    </script>
@endpush
