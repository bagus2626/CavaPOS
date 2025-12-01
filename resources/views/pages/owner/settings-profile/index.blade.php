@extends('layouts.owner')

@section('title', __('messages.owner.settings.settings.profile_settings'))

@section('page_title', __('messages.owner.settings.settings.profile_settings'))

@section('content')
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <!-- Left Column - Profile Card -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body text-center p-4">
                            <div class="position-relative d-inline-block mb-3">
                                @if ($owner->image)
                                    <img id="profilePhotoPreview" src="{{ asset('storage/' . $owner->image) }}"
                                        class="img-fluid rounded-circle shadow-sm"
                                        style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #f8f9fa;">
                                @else
                                    <div id="profilePhotoPreview"
                                        class="rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                        style="width: 120px; height: 120px; border: 4px solid #f8f9fa; background-color: #9ca3af;">
                                        <svg style="width: 60px; height: 60px; color: #ffffff;" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                        </svg>
                                    </div>
                                @endif

                                <button type="button"
                                    class="btn btn-primary btn-sm rounded-circle position-absolute shadow"
                                    style="bottom: 5px; right: 5px; width: 36px; height: 36px; padding: 0;"
                                    onclick="$('#profilePhotoInput').click()">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>

                            <input type="file" id="profilePhotoInput" accept="image/jpeg,image/png,image/jpg"
                                style="display: none;">

                            <h4 class="font-weight-bold mb-1">{{ $owner->name }}</h4>
                            <p class="text-muted mb-3">{{ $owner->email }}</p>

                            @if ($owner->image)
                                <button type="button" id="btnDeletePhoto" class="btn btn-outline-danger btn-sm mb-3">
                                    <i
                                        class="fas fa-trash mr-1"></i>{{ __('messages.owner.settings.settings.remove_photo') }}
                                </button>
                            @endif

                            <div class="border-top pt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">
                                        <i
                                            class="fas fa-calendar-alt mr-1"></i>{{ __('messages.owner.settings.settings.member_since') }}
                                    </small>
                                    <small class="font-weight-bold">
                                        {{ \Carbon\Carbon::parse($owner->created_at)->format('d M Y') }}
                                    </small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i
                                            class="fas fa-clock mr-1"></i>{{ __('messages.owner.settings.settings.last_update') }}
                                    </small>
                                    <small class="font-weight-bold">
                                        {{ \Carbon\Carbon::parse($owner->updated_at)->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i
                                    class="fas fa-shield-alt text-choco mr-2"></i>{{ __('messages.owner.settings.settings.security') }}
                            </h6>
                        </div>

                        <div class="card-body">
                            <button type="button" class="btn btn-outline-primary btn-block" data-toggle="modal"
                                data-target="#changePasswordModal">
                                <i class="fas fa-key mr-2"></i>{{ __('messages.owner.settings.settings.change_password') }}
                            </button>
                            <small class="text-muted d-block mt-2 text-center">
                                <i
                                    class="fas fa-info-circle mr-1"></i>{{ __('messages.owner.settings.settings.keep_account_secure') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Form -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-black">
                            <h5 class="mb-0 font-weight-bold">
                                <i
                                    class="fas fa-user-edit mr-2 text-choco"></i>{{ __('messages.owner.settings.settings.personal_information') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form id="profileForm">
                                @csrf

                                <div class="row">
                                    <!-- Full Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label font-weight-semibold">
                                                <i class="fas fa-user text-choco mr-1"></i>
                                                {{ __('messages.owner.settings.settings.full_name') }} <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control form-control-md" id="name"
                                                name="name" value="{{ old('name', $owner->name ?? '') }}"
                                                placeholder="{{ __('messages.owner.settings.settings.full_name_placeholder') }}"
                                                required>
                                            <small
                                                class="form-text text-muted">{{ __('messages.owner.settings.settings.this_name_displayed') }}</small>
                                        </div>
                                    </div>

                                    <!-- Email Address -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label font-weight-semibold">
                                                <i class="fas fa-envelope text-choco mr-1"></i>
                                                {{ __('messages.owner.settings.settings.email_address') }}<span
                                                    class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="form-control form-control-md" id="email"
                                                name="email" value="{{ old('email', $owner->email ?? '') }}"
                                                placeholder="{{ __('messages.owner.settings.settings.email_placeholder') }}"
                                                required>
                                            <small
                                                class="form-text text-muted">{{ __('messages.owner.settings.settings.never_share_email') }}</small>
                                        </div>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label font-weight-semibold">
                                                <i class="fas fa-phone text-choco mr-1"></i>
                                                {{ __('messages.owner.settings.settings.phone_number') }}<span
                                                    class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control form-control-md" id="phone_number"
                                                name="phone_number"
                                                value="{{ old('phone_number', $owner->phone_number ?? '') }}"
                                                placeholder="{{ __('messages.owner.settings.settings.phone_placeholder') }}"
                                                required>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-shield-alt mr-1"></i>
                                                {{ __('messages.owner.settings.settings.use_active_phone') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Another Information Section -->
                                <div class="border-top pt-4 mt-3">
                                    <h6 class="font-weight-bold mb-3">
                                        <i class="fas fa-info-circle text-choco mr-2"></i>
                                        {{ __('messages.owner.settings.settings.another_information') }}
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0 mb-3">
                                                <div class="card-body py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-box bg-white rounded-circle p-2 mr-3"
                                                            style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                                                            <i
                                                                class="fas fa-{{ $owner->is_active ? 'fingerprint' : 'fingerprint' }} text-{{ $owner->is_active ? 'success' : 'danger' }}"></i>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted d-block">Status</small>
                                                            <span
                                                                class="font-weight-bold text-{{ $owner->is_active ? 'success' : 'danger' }}">
                                                                {{ $owner->is_active ? __('messages.owner.settings.settings.active') : __('messages.owner.settings.settings.inactive') }}
                                                            </span>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card bg-light border-0 mb-3">
                                                <div class="card-body py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-box bg-white rounded-circle p-2 mr-3">
                                                            <i class="fas fa-user-shield text-choco"></i>
                                                        </div>
                                                        <div>
                                                            <small
                                                                class="text-muted d-block">{{ __('messages.owner.settings.settings.account_type') }}</small>
                                                            <span
                                                                class="font-weight-bold">{{ __('messages.owner.settings.settings.owner_account') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <button type="button" id="btnCancel" class="btn btn-outline-secondary px-4">
                                        <i class="fas fa-undo mr-2"></i>{{ __('messages.owner.settings.settings.reset') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-md px-5">
                                        <i
                                            class="fas fa-save mr-2"></i>{{ __('messages.owner.settings.settings.save_changes') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-choco text-white border-0">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-lock mr-2"></i>{{ __('messages.owner.settings.settings.change_password') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="changePasswordForm">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-warning border-0 bg-light-warning mb-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-triangle fa-2x mr-3 text-warning"></i>
                                <div>
                                    <strong>{{ __('messages.owner.settings.settings.security_notice') }}:</strong><br>
                                    <small>{{ __('messages.owner.settings.settings.security_notice_text') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label font-weight-semibold">
                                <i class="fas fa-lock text-choco mr-1"></i>
                                {{ __('messages.owner.settings.settings.current_password') }}<span
                                    class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-md">
                                <input type="password" class="form-control" name="current_password" id="currentPassword"
                                    required
                                    placeholder="{{ __('messages.owner.settings.settings.current_password_placeholder') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label font-weight-semibold">
                                <i class="fas fa-key text-choco mr-1"></i>
                                {{ __('messages.owner.settings.settings.new_password') }}<span
                                    class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-md">
                                <input type="password" class="form-control" name="new_password" id="newPassword"
                                    required
                                    placeholder="{{ __('messages.owner.settings.settings.new_password_placeholder') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i
                                    class="fas fa-info-circle mr-1"></i>{{ __('messages.owner.settings.settings.password_hint') }}
                            </small>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label font-weight-semibold">
                                <i class="fas fa-check-circle text-choco mr-1"></i>
                                {{ __('messages.owner.settings.settings.confirm_new_password') }} <span
                                    class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-md">
                                <input type="password" class="form-control" name="new_password_confirmation"
                                    id="confirmPassword" required
                                    placeholder="{{ __('messages.owner.settings.settings.confirm_password_placeholder') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>{{ __('messages.owner.settings.settings.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary btn-md px-4">
                            <i class="fas fa-check mr-2"></i>{{ __('messages.owner.settings.settings.change_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Crop Photo Modal asd -->
    <div class="modal fade" id="cropPhotoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 600px">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-choco text-white border-0">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-crop mr-2"></i>{{ __('messages.owner.settings.settings.crop_profile_photo') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 mb-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        <small>{{ __('messages.owner.settings.settings.crop_instruction') }}</small>
                    </div>

                    <div class="img-container">
                        <img id="imageToCrop" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>{{ __('messages.owner.settings.settings.cancel') }}
                    </button>
                    <button type="button" id="cropAndUploadBtn" class="btn btn-primary btn-md px-4">
                        <i class="fas fa-check mr-2"></i>{{ __('messages.owner.settings.settings.crop') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <style>
        /* Card Styling */
        .card {
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        /* Gradient Background */
        .bg-gradient-choco {
            background: linear-gradient(135deg, #8c1000 0%, #6d0c00 100%);
        }

        /* Form Controls */
        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control-lg {
            padding: 0.85rem 1.2rem;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: #8c1000;
            box-shadow: 0 0 0 0.2rem rgba(140, 16, 0, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #8c1000 0%, #6d0c00 100%);
            border: none;
            box-shadow: 0 2px 8px rgba(140, 16, 0, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #6d0c00 0%, #5a0a00 100%);
            box-shadow: 0 4px 12px rgba(140, 16, 0, 0.4);
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            color: #8c1000;
            border-color: #8c1000;
        }

        .btn-outline-primary:hover {
            background-color: #8c1000;
            border-color: #8c1000;
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-outline-secondary {
            border-color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            transform: translateY(-2px);
        }

        /* Text Colors */
        .text-choco {
            color: #8c1000;
        }

        .bg-choco {
            background-color: #8c1000;
        }

        /* Alert Styles */
        .bg-light-info {
            background-color: #d1ecf1 !important;
            color: #0c5460;
        }

        .bg-light-warning {
            background-color: #fff3cd !important;
            color: #856404;
        }

        /* Icon Box */
        .icon-box {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Progress Bar */
        .progress {
            border-radius: 10px;
            overflow: hidden;
        }

        /* Input Group */
        .input-group-append .btn {
            border-left: 0;
        }

        .input-group .form-control:focus+.input-group-append .btn {
            border-color: #8c1000;
        }

        /* Modal */
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }

        /* Profile Photo Hover */
        .position-relative .btn-primary {
            opacity: 0.95;
            transition: all 0.3s ease;
        }

        .position-relative:hover .btn-primary {
            opacity: 1;
            transform: scale(1.05);
        }

        /* Font Weight */
        .font-weight-semibold {
            font-weight: 600;
        }

        /* Card Header */
        .card-header {
            border-radius: 12px 12px 0 0 !important;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeInUp 0.5s ease;
        }

        /* Cropper.js Styles asd */
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        $(document).ready(function() {
            // Store original values
            const originalValues = {
                name: $('#name').val(),
                email: $('#email').val(),
                phone_number: $('#phone_number').val()
            };



            let cropper = null;
            let currentImageFile = null;

            // Profile Photo Upload Handler
            $('#profilePhotoInput').on('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    $(this).val('');
                    return;
                }

                // Validate file size (max 5MB)
                if (file.size > 2 * 1024 * 1024) {
                    $(this).val('');
                    return;
                }

                currentImageFile = file;

                const reader = new FileReader();
                reader.onload = function(event) {
                    const imageElement = document.getElementById('imageToCrop');
                    imageElement.src = event.target.result;
                    $('#cropPhotoModal').modal('show');
                };
                reader.readAsDataURL(file);
            });

            // Initialize cropper when modal is fully shown
            $('#cropPhotoModal').off('shown.bs.modal').on('shown.bs.modal', function() {
                // Destroy existing cropper if any
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }

                const imageElement = document.getElementById('imageToCrop');

                // Wait a bit for modal animation to complete
                setTimeout(function() {
                    cropper = new Cropper(imageElement, {
                        aspectRatio: 1,
                        viewMode: 2,
                        dragMode: 'move',
                        autoCropArea: 0.8,
                        restore: true,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        background: true,
                        responsive: true,
                        zoomOnTouch: true,
                        zoomOnWheel: true,
                        checkOrientation: true,
                        modal: true,
                        // minContainerWidth: 200,
                        // minContainerHeight: 200,
                    });
                }, 300);
            });

            // Cleanup cropper when modal is hidden
            $('#cropPhotoModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                $('#profilePhotoInput').val('');
                currentImageFile = null;
            });

            // Crop and Upload Button
            $('#cropAndUploadBtn').off('click').on('click', function() {
                if (!cropper) {
                    return;
                }

                const btn = $(this);
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

                try {
                    // Get cropped canvas
                    const canvas = cropper.getCroppedCanvas({
                        width: 500,
                        height: 500,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high',
                        fillColor: '#fff'
                    });

                    if (!canvas) {
                        throw new Error('Failed to get cropped canvas');
                    }

                    // Convert canvas to blob
                    canvas.toBlob(function(blob) {
                        if (!blob) {
                            btn.prop('disabled', false).html(originalText);
                            return;
                        }

                        // Create FormData
                        const formData = new FormData();
                        formData.append('image', blob, currentImageFile ? currentImageFile.name :
                            'profile.jpg');
                        formData.append('_token', '{{ csrf_token() }}');

                        // Upload cropped image
                        $.ajax({
                            url: '{{ route('owner.user-owner.settings.update-photo') }}',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);

                                    // Update preview with timestamp to force reload
                                    const timestamp = new Date().getTime();
                                    $('#profilePhotoPreview').attr('src', response
                                        .image_url + '?t=' + timestamp);

                                    // Update navbar image if exists
                                    $('.user-image, .img-circle').attr('src', response
                                        .image_url + '?t=' + timestamp);

                                    // Show delete button if not exists
                                    if (!$('#btnDeletePhoto').length) {
                                        $('.position-relative').after(
                                            '<button type="button" id="btnDeletePhoto" class="btn btn-outline-danger btn-sm mb-3">' +
                                            '<i class="fas fa-trash mr-1"></i>Remove Photo' +
                                            '</button>'
                                        );
                                    }

                                    // Close modal
                                    $('#cropPhotoModal').modal('hide');

                                    // Optional: Reload after delay
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1500);
                                }
                            },
                            error: function(xhr) {
                                let errorMessage =
                                    'Failed to upload photo. Please try again.';

                                if (xhr.status === 422 && xhr.responseJSON && xhr
                                    .responseJSON.errors) {
                                    const errors = xhr.responseJSON.errors;
                                    if (errors.image) {
                                        errorMessage = errors.image[0];
                                    }
                                }

                                toastr.error(errorMessage);
                            },
                            complete: function() {
                                btn.prop('disabled', false).html(originalText);
                            }
                        });
                    }, 'image/jpeg', 0.92);

                } catch (error) {
                    btn.prop('disabled', false).html(originalText);
                }
            });

            // Delete Photo Handler
            $(document).off('click', '#btnDeletePhoto').on('click', '#btnDeletePhoto', function() {
                Swal.fire({
                    title: 'Remove Photo?',
                    text: 'Are you sure you want to remove your profile photo?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#8c1000',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('owner.user-owner.settings.delete-photo') }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1500);
                                }
                            },
                        });
                    }
                });
            });

            // Toggle Password Visibility
            $('#toggleCurrentPassword').on('click', function() {
                const input = $('#currentPassword');
                const icon = $(this).find('i');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            $('#toggleNewPassword').on('click', function() {
                const input = $('#newPassword');
                const icon = $(this).find('i');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            $('#toggleConfirmPassword').on('click', function() {
                const input = $('#confirmPassword');
                const icon = $(this).find('i');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Cancel/Reset Button
            $('#btnCancel').on('click', function() {
                $('#name').val(originalValues.name);
                $('#email').val(originalValues.email);
                $('#phone_number').val(originalValues.phone_number);
            });

            // Profile Form Submission
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();

                const name = $('#name').val().trim();
                const email = $('#email').val().trim();
                const phoneNumber = $('#phone_number').val().trim();

                if (!name) {
                    return;
                }

                if (!email) {
                    return;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    return;
                }

                if (!phoneNumber) {
                    return;
                }

                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');

                $.ajax({
                    url: '{{ route('owner.user-owner.settings.update-personal-info') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            originalValues.name = name;
                            originalValues.email = email;
                            originalValues.phone_number = phoneNumber;
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.values(errors).forEach(error => {
                                toastr.error(error[0]);
                            });
                        } else {
                            return;
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Change Password Form
            $('#changePasswordForm').on('submit', function(e) {
                e.preventDefault();

                const currentPassword = $('#currentPassword').val();
                const newPassword = $('#newPassword').val();
                const confirmPassword = $('#confirmPassword').val();

                if (!currentPassword) {
                    return;
                }

                if (!newPassword) {
                    return;
                }

                if (newPassword.length < 8) {
                    return;
                }

                if (newPassword !== confirmPassword) {
                    return;
                }

                if (currentPassword === newPassword) {
                    return;
                }

                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin mr-2"></i>Changing...');

                $.ajax({
                    url: '{{ route('owner.user-owner.settings.change-password') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            submitBtn.prop('disabled', false).html(originalText);
                            toastr.success(response.message);
                            $('#changePasswordModal').modal('hide');
                            $('#changePasswordForm')[0].reset();
                            $('#toggleCurrentPassword i, #toggleNewPassword i, #toggleConfirmPassword i')
                                .removeClass('fa-eye-slash').addClass('fa-eye');
                            $('#currentPassword, #newPassword, #confirmPassword').attr('type',
                                'password');
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            if (errors) {
                                Object.values(errors).forEach(error => {
                                    toastr.error(error[0]);
                                });
                            } else if (xhr.responseJSON.message) {
                                toastr.error(xhr.responseJSON.message);
                            }
                        } else {
                            return;
                        }
                    }
                });
            });

            // Reset password form when modal is closed
            $('#changePasswordModal').on('hidden.bs.modal', function() {
                $('#changePasswordForm')[0].reset();
                $('#toggleCurrentPassword i, #toggleNewPassword i, #toggleConfirmPassword i')
                    .removeClass('fa-eye-slash').addClass('fa-eye');
                $('#currentPassword, #newPassword, #confirmPassword').attr('type', 'password');
            });
        });
    </script>
@endpush
