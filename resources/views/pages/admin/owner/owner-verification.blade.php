@extends('pages.admin.layouts.app')

@section('content-header')
    <div class="content-header-left col-12 mb-2 mt-1">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h5 class="content-header-title float-left pr-1 mb-0">Owner Verification</h5>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb p-0 mb-0">
                        <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active"><a href="{{ route('admin.owner-verification') }}">Owner
                                Verification</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')

    {{-- Statistics Section --}}
    <section id="widgets-Statistics">
        <div class="row">
            <div class="col-xl-3 col-md-3 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-warning mx-auto my-1">
                                <i class="bx bx-hourglass font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Pending</p>
                            <h2 class="mb-0">{{ $pendingCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-3 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-success mx-auto my-1">
                                <i class="bx bx-check-circle font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Approved</p>
                            <h2 class="mb-0">{{ $approvedCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-3 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-danger mx-auto my-1">
                                <i class="bx bx-x-circle font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">Rejected</p>
                            <h2 class="mb-0">{{ $rejectedCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-3 col-sm-6">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="badge-circle badge-circle-lg badge-circle-light-info mx-auto my-1">
                                <i class="bx bxs-user font-medium-5"></i>
                            </div>
                            <p class="text-muted mb-0 line-ellipsis">All</p>
                            <h2 class="mb-0">{{ $totalCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- Table Section --}}
    <div class="row" id="verification-table">
        <div class="col-12">
            @include('pages.admin.layouts.partials.alert')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Verification Management</h4>
                </div>

                <div class="card-content">
                    <div class="card-body">
                        <p>Business verification requests management table.</p>
                    </div>

                    <!-- Filter Status -->
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'pending' ? 'active' : '' }}" id="pending-tab"
                                    data-toggle="tab" href="#pending-content" aria-controls="pending" role="tab"
                                    aria-selected="{{ $status == 'pending' ? 'true' : 'false' }}">
                                    <span class="align-middle">Pending</span>
                                    @if ($pendingCount > 0)
                                        <span class="badge badge-pill badge-round badge-danger ml-1">{{ $pendingCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'approved' ? 'active' : '' }}" id="approved-tab"
                                    data-toggle="tab" href="#approved-content" aria-controls="approved" role="tab"
                                    aria-selected="{{ $status == 'approved' ? 'true' : 'false' }}">
                                    <span class="align-middle">Approved</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'rejected' ? 'active' : '' }}" id="rejected-tab"
                                    data-toggle="tab" href="#rejected-content" aria-controls="rejected" role="tab"
                                    aria-selected="{{ $status == 'rejected' ? 'true' : 'false' }}">
                                    <span class="align-middle">Rejected</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Pending Tab -->
                        <div class="tab-pane {{ $status == 'pending' ? 'show active' : '' }}" id="pending-content" role="tabpanel" aria-labelledby="pending-tab">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>NO</th>
                                            <th>OWNER INFO</th>
                                            <th>BUSINESS</th>
                                            <th>SUBMITTED</th>
                                            <th>STATUS</th>
                                            <th>ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingVerifications as $index => $verification)
                                            <tr>
                                                <td>{{ $pendingVerifications->firstItem() + $index }}</td>
                                                <td>
                                                    <span>{{ $verification->owner_name }}</span>
                                                    <small class="d-block">{{ $verification->owner_email }}</small>
                                                </td>
                                                <td>
                                                    <span>{{ $verification->business_name }}</span>
                                                    <small class="d-block">{{ $verification->businessCategory->name ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <span>{{ $verification->created_at ? $verification->created_at->format('d M Y') : '-' }}</span>
                                                    <small class="d-block">{{ $verification->created_at ? $verification->created_at->format('H:i') : '' }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-pill badge-warning">Pending</span>
                                                </td>
                                                <td>
                                                    <a href="#" onclick="viewDetails({{ $verification->id }})">
                                                        <i class="badge-circle badge-circle-light-secondary bx bx-show font-medium-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No pending verifications found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination for Pending -->
                            @if ($pendingVerifications->total() > 0)
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <div class="pagination-summary text-muted">
                                            Showing {{ $pendingVerifications->firstItem() }} - {{ $pendingVerifications->lastItem() }} from {{ $pendingVerifications->total() }} entries
                                        </div>
                                        <div class="pagination-links">
                                            {{ $pendingVerifications->appends(['status' => 'pending'])->links('vendor.pagination.custom-limited') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Approved Tab -->
                        <div class="tab-pane {{ $status == 'approved' ? 'show active' : '' }}" id="approved-content" role="tabpanel" aria-labelledby="approved-tab">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>NO</th>
                                            <th>OWNER INFO</th>
                                            <th>BUSINESS</th>
                                            <th>SUBMITTED</th>
                                            <th>APPROVED DATE</th>
                                            <th>XENDIT REGISTER</th>
                                            <th>SPLIT RULES</th>
                                            <th>STATUS</th>
                                            <th>ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($approvedVerifications as $index => $verification)
                                            <tr>
                                                <td>{{ $approvedVerifications->firstItem() + $index }}</td>
                                                <td>
                                                    <span>{{ $verification->owner_name }}</span>
                                                    <small class="d-block">{{ $verification->owner_email }}</small>
                                                </td>
                                                <td>
                                                    <span>{{ $verification->business_name }}</span>
                                                    <small class="d-block">{{ $verification->businessCategory->name ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <span>{{ $verification->created_at ? $verification->created_at->format('d M Y') : '-' }}</span>
                                                    <small class="d-block">{{ $verification->created_at ? $verification->created_at->format('H:i') : '' }}</small>
                                                </td>
                                                <td>
                                                    <span>{{ $verification->reviewed_at ? $verification->reviewed_at->format('d M Y') : '-' }}</span>
                                                    <small class="d-block">{{ $verification->reviewed_at ? $verification->reviewed_at->format('H:i') : '' }}</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $xenditStatus = $verification->owner->xenditSubAccount->status ?? null;
                                                        $badgeColors = [
                                                            'INVITED' => 'warning',
                                                            'REGISTERED' => 'success',
                                                            'AWAITING_DOCS' => 'primary',
                                                            'LIVE' => 'info',
                                                            'LIVE_TESTMODE' => 'info',
                                                            'SUSPENDED' => 'danger',
                                                        ];
                                                    @endphp

                                                    @if ($xenditStatus && isset($badgeColors[$xenditStatus]))
                                                        <span class="badge badge-light-{{ $badgeColors[$xenditStatus] }} badge-pill">{{ $xenditStatus }}</span>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                            onclick="openXenditRegistrationModal('{{ $verification->owner_id }}', '{{ $verification->owner_email }}', '{{ $verification->business_name }}')">
                                                            <i class="bx bx-id-card"></i> CREATE ACCOUNT
                                                        </button>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $hasSplitRule = $verification->owner && $verification->owner->latestSplitRule;
                                                    @endphp

                                                    @if ($hasSplitRule)
                                                        <i class="bx bx-check-circle text-success bx-md" title="Split Rule Created"></i>
                                                    @else
                                                        <i class="bx bx-x-circle text-danger bx-md" title="No Split Rule"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-pill badge-success">Approved</span>
                                                </td>
                                                <td>
                                                    <a href="#" onclick="viewDetails({{ $verification->id }})">
                                                        <i class="badge-circle badge-circle-light-secondary bx bx-show font-medium-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">No approved verifications found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination for Approved -->
                            @if ($approvedVerifications->total() > 0)
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <div class="pagination-summary text-muted">
                                            Showing {{ $approvedVerifications->firstItem() }} - {{ $approvedVerifications->lastItem() }} from {{ $approvedVerifications->total() }} entries
                                        </div>
                                        <div class="pagination-links">
                                            {{ $approvedVerifications->appends(['status' => 'approved'])->links('vendor.pagination.custom-limited') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Rejected Tab -->
                        <div class="tab-pane {{ $status == 'rejected' ? 'show active' : '' }}" id="rejected-content" role="tabpanel" aria-labelledby="rejected-tab">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>NO</th>
                                            <th>OWNER INFO</th>
                                            <th>BUSINESS</th>
                                            <th>SUBMITTED</th>
                                            <th>REJECTED DATE</th>
                                            <th>STATUS</th>
                                            <th>ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rejectedVerifications as $index => $verification)
                                            <tr>
                                                <td>{{ $rejectedVerifications->firstItem() + $index }}</td>
                                                <td>
                                                    <span>{{ $verification->owner_name }}</span>
                                                    <small class="d-block">{{ $verification->owner_email }}</small>
                                                </td>
                                                <td>
                                                    <span>{{ $verification->business_name }}</span>
                                                    <small class="d-block">{{ $verification->businessCategory->name ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <span>{{ $verification->created_at ? $verification->created_at->format('d M Y') : '-' }}</span>
                                                    <small class="d-block">{{ $verification->created_at ? $verification->created_at->format('H:i') : '' }}</small>
                                                </td>
                                                <td>
                                                    <span>{{ $verification->reviewed_at ? $verification->reviewed_at->format('d M Y') : '-' }}</span>
                                                    <small class="d-block">{{ $verification->reviewed_at ? $verification->reviewed_at->format('H:i') : '' }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-pill badge-danger">Rejected</span>
                                                </td>
                                                <td>
                                                    <a href="#" onclick="viewDetails({{ $verification->id }})">
                                                        <i class="badge-circle badge-circle-light-secondary bx bx-show font-medium-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No rejected verifications found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination for Rejected -->
                            @if ($rejectedVerifications->total() > 0)
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <div class="pagination-summary text-muted">
                                            Showing {{ $rejectedVerifications->firstItem() }} - {{ $rejectedVerifications->lastItem() }} from {{ $rejectedVerifications->total() }} entries
                                        </div>
                                        <div class="pagination-links">
                                            {{ $rejectedVerifications->appends(['status' => 'rejected'])->links('vendor.pagination.custom-limited') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('pages.admin.owner.modal')
@endsection

@push('page-scripts')
    <script>
        function viewDetails(id) {
            window.location.href = `/admin/owner-verification/${id}`;
        }

        function openXenditRegistrationModal(partnerId, partnerEmail, businessName) {
            $('#partner_id').val(partnerId);
            $('#partner_email').val(partnerEmail);
            $('#business_name').val(businessName);

            const modal = new bootstrap.Modal($('#xenditRegistrationModal')[0]);
            modal.show();
        }

        $(document).ready(function () {
            $('#createAccountForm').on('submit', function (e) {
                e.preventDefault();
                
                showPageLoader("Registering Xendit Account...");

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.owner-verification.register-xendit-account') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        hidePageLoader();

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message || 'Xendit account registered successfully!',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#xenditRegistrationModal').modal('hide');
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Failed to register Xendit account.',
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        hidePageLoader();

                        var errorMessage = 'An error occurred while processing your request.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.statusText) {
                            errorMessage = xhr.statusText;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });

                        console.error('AJAX Error:', error);
                    }
                });
            });
        });
    </script>
@endpush