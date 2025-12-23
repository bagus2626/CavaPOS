@extends('pages.admin.layouts.app')

@section('content-header')
    <div class="content-header-left col-12 mb-2 mt-1">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h5 class="content-header-title float-left pr-1 mb-0">Verification Detail</h5>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb p-0 mb-0">
                        <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.owner-verification') }}">Owner Verification</a>
                        </li>
                        <li class="breadcrumb-item active"><a
                                href="{{ route('admin.owner-verification.show', $verification->id) }}">Detail</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')


    {{-- Alert Section --}}
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
    {{-- End Alert Section --}}

    <section class="invoice-view-wrapper">
        <div class="row">
            <!-- invoice view page -->
            <div class="col-xl-9 col-md-8 col-12">
                <div class="card invoice-print-area">
                    <div class="card-content">
                        <div class="card-body pb-0 mx-25">
                            <!-- header section -->
                            <div class="row my-2">
                                <div class="col-6">
                                    <h3 class="text-primary">Owner Verification</h3>
                                </div>
                            </div>
                            <div class="col-6 d-flex p-0">
                                <div>
                                    <div class="mb-50"> <small class="text-muted">Date Submitted:</small> <span
                                            class="">{{ $verification->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div> <small class="text-muted">Status:</small>
                                        @if($verification->status == 'pending') <span
                                            class="badge badge-warning mt-50">Pending</span>
                                        @elseif($verification->status == 'approved') <span
                                            class="badge badge-success mt-50">Approved</span>
                                        @elseif($verification->status == 'rejected') <span
                                            class="badge badge-danger mt-50">Rejected</span>
                                        @endif </div>
                                </div>
                            </div>

                            <hr>
                            <!-- invoice address and contact -->
                            <div class="row invoice-info">
                                <div class="col-6 mt-1">
                                    <h6 class="invoice-from">Owner Information</h6>
                                    <div class="mb-1">
                                        <small class="text-muted">Name:</small>
                                        <span class="d-block">{{ $verification->owner_name }}</span>
                                    </div>
                                    <div class="mb-1">
                                        <small class="text-muted">Email:</small>
                                        <span class="d-block">{{ $verification->owner_email }}</span>
                                    </div>
                                    <div class="mb-1">
                                        <small class="text-muted">Phone:</small>
                                        <span class="d-block">{{ $verification->owner_phone ?? '-' }}</span>
                                    </div>
                                    <div class="mb-1">
                                        <small class="text-muted">ID Number (KTP):</small>
                                        <span
                                            class="d-block">{{ $verification->ktp_number_decrypted ?? '****************' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 mt-1">
                                    <h6 class="invoice-to">Business Information</h6>
                                    <div class="mb-1">
                                        <small class="text-muted">Business Name:</small>
                                        <span class="d-block">{{ $verification->business_name }}</span>
                                    </div>
                                    <div class="mb-1">
                                        <small class="text-muted">Category:</small>
                                        <span class="d-block">{{ $verification->businessCategory->name ?? '-' }}</span>
                                    </div>
                                    @if($verification->business_address)
                                        <div class="mb-1">
                                            <small class="text-muted">Address:</small>
                                            <span class="d-block">{{ $verification->business_address }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                        </div>
                        <!-- product details table-->
                        <div class="invoice-product-details table-responsive mx-md-25">
                            <table class="table table-borderless mb-0">
                                <thead>
                                    <tr class="border-0">
                                        <th scope="col">Document Type</th>
                                        <th scope="col">Description</th>
                                        <th scope="col" class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($verification->ktp_photo_path)
                                        <tr>
                                            <td>ID Card (KTP)</td>
                                            <td>Identity Verification Document</td>
                                            <td class="text-primary text-right">
                                                <a href="{{ route('admin.owner-verification.ktp-image', $verification->id) }}"
                                                    target="_blank">
                                                    <i class="bx bx-show font-medium-1"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($verification->business_logo_path)
                                        <tr>
                                            <td>Business Logo</td>
                                            <td>Company Branding Asset</td>
                                            <td class="text-primary text-right">
                                                <a href="{{ asset('storage/' . $verification->business_logo_path) }}"
                                                    target="_blank">
                                                    <i class="bx bx-show font-medium-1"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--  action  -->
            <div class="col-xl-3 col-md-4 col-12">
                @if($verification->status == 'pending')
                    <div class="card invoice-action-wrapper shadow-none border">
                        <div class="card-body">
                            <div class="invoice-action-btn mb-1">
                                <button class="btn btn-success btn-block" onclick="showApproveModal({{ $verification->id }})">
                                    <span>Approve</span>
                                </button>
                            </div>
                            <div class="invoice-action-btn">
                                <button class="btn btn-danger btn-block" onclick="showRejectModal({{ $verification->id }})">
                                    <span>Reject</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if($verification->status != 'pending')
                    <div class="card invoice-action-wrapper shadow-none border">
                        <div class="card-body">
                            <h6 class="mb-2">Review Information</h6>
                            @if($verification->reviewed_at)
                                <div class="mb-1">
                                    <small class="text-muted">Reviewed Date:</small>
                                    <span class="d-block">{{ $verification->reviewed_at->format('d M Y, H:i') }}</span>
                                </div>
                            @endif
                            @if($verification->reviewed_by)
                                <div class="mb-1">
                                    <small class="text-muted">Reviewed By:</small>
                                    <span class="d-block">{{ $verification->reviewedBy->name ?? 'Admin' }}</span>
                                </div>
                            @endif
                            @if($verification->status == 'rejected' && $verification->rejection_reason)
                                <div class="mb-1">
                                    <small class="text-muted">Rejection Reason:</small>
                                    <div class="alert alert-danger mt-1">
                                        <p class="mb-0">{{ $verification->rejection_reason }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Approval</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this verification?</p>
                    <div class="alert bg-rgba-warning" id="approveTimer">
                        <p class="mb-0 text-center">Please wait <span id="approveCountdown">5</span> seconds</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">Cancel</button>
                    <form id="approveForm" method="POST"
                        action="{{ route('admin.owner-verification.approve', $verification->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" id="approveConfirmBtn" class="btn btn-success" disabled>Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Verification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="rejectForm" onsubmit="return handleRejectSubmit(event)">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rejectionReason">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" id="rejectionReason" rows="4" required class="form-control"
                                placeholder="Enter reason..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Next</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Confirm Modal -->
    <div class="modal fade" id="rejectConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Rejection</h5>
                    <button type="button" class="close" onclick="closeRejectConfirmModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this verification?</p>
                    <div class="alert bg-rgba-danger">
                        <p class="mb-0" id="confirmRejectionReason"></p>
                    </div>
                    <div class="alert bg-rgba-warning" id="rejectTimer">
                        <p class="mb-0 text-center">Please wait <span id="rejectCountdown">5</span> seconds</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary"
                        onclick="closeRejectConfirmModal()">Cancel</button>
                    <form id="rejectConfirmForm" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="rejection_reason" id="finalRejectionReason">
                        <button type="submit" id="rejectConfirmBtn" class="btn btn-danger" disabled>Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let approveTimer = null;
        let rejectTimer = null;

        function showApproveModal(id) {
            $('#approveConfirmModal').modal('show');
            const confirmBtn = document.getElementById('approveConfirmBtn');
            const timerContainer = document.getElementById('approveTimer');
            const countdownSpan = document.getElementById('approveCountdown');

            if (approveTimer) clearInterval(approveTimer);

            // --- RESET STATE AWAL ---
            confirmBtn.disabled = true;
            let seconds = 5;

            // Setel ulang tampilan timer
            timerContainer.className = 'alert bg-rgba-warning'; // Kembalikan kelas warna warning
            timerContainer.innerHTML = '<p class="mb-0 text-center">Please wait <span id="approveCountdown">5</span> seconds</p>';
            const newCountdownSpan = document.getElementById('approveCountdown');
            newCountdownSpan.textContent = seconds;
            // --- END RESET STATE AWAL ---

            approveTimer = setInterval(() => {
                seconds--;
                document.getElementById('approveCountdown').textContent = seconds; // Update countdown
                // Pastikan kita mendapatkan elemen yang baru jika DOM direplace
                const currentCountdown = document.getElementById('approveCountdown');

                if (seconds <= 0) {
                    clearInterval(approveTimer);
                    confirmBtn.disabled = false;
                    // Ganti pesan setelah hitung mundur selesai
                    timerContainer.className = 'alert bg-rgba-success'; // Ubah kelas warna menjadi success
                    timerContainer.innerHTML = '<p class="mb-0 text-center">You can now confirm</p>';
                }
            }, 1000);
        }

        // Tidak perlu mengubah event 'hidden.bs.modal' untuk Approve karena reset dilakukan di awal show modal.

        function showRejectModal(id) {
            $('#rejectModal').modal('show');
            document.getElementById('rejectForm').dataset.verificationId = id;
        }

        function handleRejectSubmit(event) {
            event.preventDefault();
            const reason = document.getElementById('rejectionReason').value.trim();

            if (!reason) {
                alert('Please provide a rejection reason');
                return false;
            }

            const verificationId = event.target.dataset.verificationId;
            $('#rejectModal').modal('hide');
            showRejectConfirmModal(verificationId, reason);
            return false;
        }

        function showRejectConfirmModal(id, reason) {
            document.getElementById('confirmRejectionReason').textContent = reason;
            document.getElementById('finalRejectionReason').value = reason;
            document.getElementById('rejectConfirmForm').action = `{{ url('admin/owner-verification') }}/${id}/reject`; // Gunakan url() untuk menghindari masalah routing

            $('#rejectConfirmModal').modal('show');

            const confirmBtn = document.getElementById('rejectConfirmBtn');
            const timerContainer = document.getElementById('rejectTimer');

            if (rejectTimer) clearInterval(rejectTimer);

            // --- RESET STATE AWAL ---
            confirmBtn.disabled = true;
            let seconds = 5;

            // Setel ulang tampilan timer
            timerContainer.className = 'alert bg-rgba-warning'; // Kembalikan kelas warna warning
            timerContainer.innerHTML = '<p class="mb-0 text-center">Please wait <span id="rejectCountdown">5</span> seconds</p>';
            const newCountdownSpan = document.getElementById('rejectCountdown');
            newCountdownSpan.textContent = seconds;
            // --- END RESET STATE AWAL ---

            rejectTimer = setInterval(() => {
                seconds--;
                document.getElementById('rejectCountdown').textContent = seconds;

                if (seconds <= 0) {
                    clearInterval(rejectTimer);
                    confirmBtn.disabled = false;
                    // Ganti pesan setelah hitung mundur selesai
                    timerContainer.className = 'alert bg-rgba-danger'; // Ubah kelas warna menjadi danger
                    timerContainer.innerHTML = '<p class="mb-0 text-center">You can now confirm</p>';
                }
            }, 1000);
        }
        // Tidak perlu mengubah closeRejectConfirmModal karena ia sudah memanggil clearInterval

        function closeRejectConfirmModal() {
            $('#rejectConfirmModal').modal('hide');
            if (rejectTimer) clearInterval(rejectTimer);
        }

        $('#approveConfirmModal').on('hidden.bs.modal', function () {
            if (approveTimer) clearInterval(approveTimer);
        });

        $('#rejectModal').on('hidden.bs.modal', function () {
            document.getElementById('rejectForm').reset();
        });

        $('#rejectConfirmModal').on('hidden.bs.modal', function () {
            if (rejectTimer) clearInterval(rejectTimer);
        });
    </script>
@endsection