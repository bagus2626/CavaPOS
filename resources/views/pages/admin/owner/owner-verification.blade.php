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
                        <li class="breadcrumb-item active"><a href="{{ route('admin.owner-verification') }}">Owner Verification</a>
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
                    <div class="card-body border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.owner-verification', ['status' => 'pending']) }}"
                                    class="btn {{ $status == 'pending' ? 'btn-warning' : 'btn-outline-warning' }} btn-sm">
                                    Pending
                                    @if($pendingCount > 0 && $status != 'pending')
                                        <span class="badge badge-pill badge-round badge-danger ml-1">{{ $pendingCount }}</span>
                                        {{-- <span class="badge badge-pill badge-danger badge-up badge-round">2</span> --}}
                                    @endif
                                </a>

                                <a href="{{ route('admin.owner-verification', ['status' => 'approved']) }}"
                                    class="btn {{ $status == 'approved' ? 'btn-success' : 'btn-outline-success' }} btn-sm">
                                    Approved
                                </a>
                                <a href="{{ route('admin.owner-verification', ['status' => 'rejected']) }}"
                                    class="btn {{ $status == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }} btn-sm">
                                    Rejected
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>NO</th>
                                    <th>OWNER INFO</th>
                                    <th>BUSINESS</th>
                                    <th>SUBMITTED</th>
                                    @if($status == 'approved')
                                        <th>APPROVED DATE</th>
                                        <th>XENDIT REGISTER</th>
                                        <th>SPLIT RULES</th>
                                    @elseif($status == 'rejected')
                                        <th>REJECTED DATE</th>
                                    @endif
                                    <th>STATUS</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($verifications as $index => $verification)
                                    <tr>
                                        <td>{{ $verifications->firstItem() + $index }}</td>
                                        <td>
                                            <span>{{ $verification->owner_name }}</span>
                                            <small class="d-block" >{{ $verification->owner_email }}</small>
                                        </td>
                                        <td>
                                            <span>{{ $verification->business_name }}</span>
                                            <small class="d-block">{{ $verification->businessCategory->name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span
                                            >{{ $verification->created_at ? $verification->created_at->format('d M Y') : '-' }}</span>
                                            <small
                                                class="d-block">{{ $verification->created_at ? $verification->created_at->format('H:i') : '' }}</small>
                                        </td>
                                        @if($status == 'approved')
                                            <td>
                                                <span
                                                >{{ $verification->reviewed_at ? $verification->reviewed_at->format('d M Y') : '-' }}</span>
                                                <small
                                                    class="d-block">{{ $verification->reviewed_at ? $verification->reviewed_at->format('H:i') : '' }}</small>
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

                                                @if($hasSplitRule)
                                                    <i class="bx bx-check-circle text-success bx-md" title="Split Rule Created"></i>
                                                @else
                                                    <i class="bx bx-x-circle text-danger bx-md" title="No Split Rule"></i>
                                                @endif
                                            </td>

                                        @elseif($status == 'rejected')
                                            <td>
                                                <span
                                                >{{ $verification->reviewed_at ? $verification->reviewed_at->format('d M Y') : '-' }}</span>
                                                <small
                                                    class="d-block">{{ $verification->reviewed_at ? $verification->reviewed_at->format('H:i') : '' }}</small>
                                            </td>
                                        @endif
                                        <td>
                                            @if($verification->status == 'pending')
                                                <span class="badge badge-pill badge-warning">Pending</span>
                                            @elseif($verification->status == 'approved')
                                                <span class="badge badge-pill badge-success">Approved</span>
                                            @elseif($verification->status == 'rejected')
                                                <span class="badge badge-pill badge-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" onclick="viewDetails({{ $verification->id }})">
                                                <i
                                                    class="badge-circle badge-circle-light-secondary bx bx-show font-medium-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $status == 'pending' ? '6' : '7' }}" class="text-center">
                                            No {{ $status }} verifications found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($verifications->hasPages())
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    Showing {{ $verifications->firstItem() }} to {{ $verifications->lastItem() }} of
                                    {{ $verifications->total() }} entries
                                </span>
                                <nav>
                                    {{ $verifications->links() }}
                                </nav>
                            </div>
                        </div>
                    @endif
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
            // Redirect ke halaman detail atau buka modal
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
            $('#createAccountForm').on('submit', function () {
                showPageLoader("Registering Xendit Account...");
            });
        });

    </script>
@endpush
