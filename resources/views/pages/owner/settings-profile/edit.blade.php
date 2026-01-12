@extends('layouts.owner')
@section('title', 'Edit Profile')
@section('page_title', 'Edit Profile')

@section('content')
<div class="modern-container">
    <div class="container-modern">
        
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">Edit Profile</h1>
                <p class="page-subtitle">Update your profile information and photo.</p>
            </div>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger alert-modern">
                <div class="alert-icon">
                    <span class="material-symbols-outlined">error</span>
                </div>
                <div class="alert-content">
                    <strong>Whoops!</strong> There were some problems with your input.
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-modern">
                <div class="alert-icon">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
                <div class="alert-content">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Main Card -->
        <div class="modern-card">
            <form action="{{ route('owner.user-owner.settings.update-personal-info') }}" method="POST"
                enctype="multipart/form-data" id="profileForm">
                @csrf
                
                <div class="card-body-modern">
                    <!-- Profile Section -->
                    <div class="profile-section">
                        <!-- Profile Picture Upload -->
                        <div class="profile-picture-wrapper">
                            <div class="profile-picture-container" id="profilePictureContainer">
                                <div class="upload-placeholder" id="uploadPlaceholder"
                                    style="{{ $owner->image ? 'display: none;' : '' }}">
                                    <span class="material-symbols-outlined">add_a_photo</span>
                                    <span class="upload-text">Upload</span>
                                </div>
                                <img id="imagePreview" class="profile-preview {{ $owner->image ? 'active' : '' }}"
                                    src="{{ $owner->image ? asset('storage/' . $owner->image) : '' }}"
                                    alt="Profile Preview">
                            </div>
                            <input type="file" name="image" id="image" accept="image/*" style="display: none;">
                            <input type="hidden" name="remove_image" id="remove_image" value="0">
                            <small class="text-muted d-block text-center mt-2">JPG, PNG, WEBP. Max 2 MB</small>
                            @error('image')
                                <div class="text-danger text-center mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Personal Information Fields -->
                        <div class="personal-info-fields">
                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">person</span>
                                </div>
                                <h3 class="section-title">Personal Information</h3>
                            </div>
                            <div class="row g-4">
                                <!-- Full Name -->
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.settings.settings.full_name') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" id="name"
                                            class="form-control-modern @error('name') is-invalid @enderror"
                                            value="{{ old('name', $owner->name) }}" 
                                            placeholder="e.g. John Doe"
                                            required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Email (Read-only) -->
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.settings.settings.email_address') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                            class="form-control-modern" 
                                            value="{{ $owner->email }}" 
                                            readonly 
                                            disabled
                                            style="background-color: #f8f9fa; cursor: not-allowed;">
                                    </div>
                                </div>
                                
                                <!-- Phone Number -->
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.settings.settings.phone_number') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="phone_number" id="phone_number"
                                            class="form-control-modern @error('phone_number') is-invalid @enderror"
                                            value="{{ old('phone_number', $owner->phone_number) }}" 
                                            placeholder="e.g. +62812345678"
                                            required>
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Account Type (Read-only) -->
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.settings.settings.account_type') }}
                                        </label>
                                        <input type="text" 
                                            class="form-control-modern" 
                                            value="{{ __('messages.owner.settings.settings.owner_account') }}" 
                                            readonly 
                                            disabled
                                            style="background-color: #f8f9fa; cursor: not-allowed;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="section-divider"></div>

                    <!-- Change Password Section (Optional) -->
                    <div class="account-section">
                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">lock</span>
                            </div>
                            <h3 class="section-title">Change Password (Optional)</h3>
                        </div>

                        <div class="alert alert-info alert-modern mb-4">
                            <div class="alert-icon">
                                <span class="material-symbols-outlined">info</span>
                            </div>
                            <div class="alert-content">
                                <small><strong>Leave blank</strong> if you don't want to change your password. If you want to change password, all fields below must be filled.</small>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Current Password -->
                            <div class="col-md-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.settings.settings.current_password') }}
                                    </label>
                                    <div class="password-wrapper">
                                        <input type="password" name="current_password" id="current_password"
                                            class="form-control-modern @error('current_password') is-invalid @enderror"
                                            placeholder="Enter your current password">
                                        <button type="button" class="password-toggle" id="toggleCurrentPassword">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.settings.settings.new_password') }}
                                    </label>
                                    <div class="password-wrapper">
                                        <input type="password" name="new_password" id="new_password"
                                            class="form-control-modern @error('new_password') is-invalid @enderror"
                                            placeholder="Min. 8 characters">
                                        <button type="button" class="password-toggle" id="toggleNewPassword">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </button>
                                    </div>
                                    @error('new_password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Confirm New Password -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.settings.settings.confirm_new_password') }}
                                    </label>
                                    <div class="password-wrapper">
                                        <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                            class="form-control-modern"
                                            placeholder="Re-enter new password">
                                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card Footer -->
                <div class="card-footer-modern">
                    <a href="{{ route('owner.user-owner.settings.index') }}" class="btn-cancel-modern">
                        Cancel
                    </a>
                    <button type="submit" class="btn-submit-modern">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Crop Modal -->
    <div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content modern-modal">
                <div class="modal-header modern-modal-header">
                    <h5 class="modal-title">
                        <span class="material-symbols-outlined">crop</span>
                        Crop Profile Photo
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info alert-modern mb-3">
                        <div class="alert-icon">
                            <span class="material-symbols-outlined">info</span>
                        </div>
                        <div class="alert-content">
                            <small>Drag to move, scroll to zoom, or use the corners to resize the crop area.</small>
                        </div>
                    </div>
                    <div class="img-container-crop">
                        <img id="imageToCrop" style="max-width: 100%;" alt="Image to crop">
                    </div>
                </div>
                <div class="modal-footer modern-modal-footer">
                    <button type="button" class="btn-cancel-modern" data-dismiss="modal">
                        <span class="material-symbols-outlined">close</span>
                        Cancel
                    </button>
                    <button type="button" id="cropBtn" class="btn-submit-modern">
                        <span class="material-symbols-outlined">check</span>
                        Crop & Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Image Cropper (Square 1:1)
            ImageCropper.init({
                id: 'profile',
                inputId: 'image',
                previewId: 'imagePreview',
                modalId: 'cropModal',
                imageToCropId: 'imageToCrop',
                cropBtnId: 'cropBtn',
                editBtnId: 'editPictureBtn',
                containerId: 'profilePictureContainer',
                removeInputId: 'remove_image',
                aspectRatio: 1,
                outputWidth: 800,
                outputHeight: 800
            });

            // Password toggle handlers
            function bindPasswordToggle(btnId, inputId) {
                const btn = document.getElementById(btnId);
                const inp = document.getElementById(inputId);
                if (!btn || !inp) return;

                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const isPw = inp.type === 'password';
                    inp.type = isPw ? 'text' : 'password';
                    const icon = btn.querySelector('.material-symbols-outlined');
                    if (icon) {
                        icon.textContent = isPw ? 'visibility_off' : 'visibility';
                    }
                });
            }

            bindPasswordToggle('toggleCurrentPassword', 'current_password');
            bindPasswordToggle('toggleNewPassword', 'new_password');
            bindPasswordToggle('toggleConfirmPassword', 'new_password_confirmation');

            // Form validation
            const form = document.getElementById('profileForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    const name = document.getElementById('name').value.trim();
                    const phone = document.getElementById('phone_number').value.trim();
                    const currentPw = document.getElementById('current_password').value;
                    const newPw = document.getElementById('new_password').value;
                    const confirmPw = document.getElementById('new_password_confirmation').value;

                    // Validasi basic info
                    if (!name) {
                        e.preventDefault();
                        alert('Please enter your full name.');
                        return false;
                    }

                    if (!phone) {
                        e.preventDefault();
                        alert('Please enter your phone number.');
                        return false;
                    }

                    // Validasi password (OPSIONAL)
                    // Jika salah satu field password diisi, semua harus diisi
                    if (currentPw || newPw || confirmPw) {
                        if (!currentPw) {
                            e.preventDefault();
                            alert('Please enter your current password.');
                            document.getElementById('current_password').focus();
                            return false;
                        }

                        if (!newPw) {
                            e.preventDefault();
                            alert('Please enter your new password.');
                            document.getElementById('new_password').focus();
                            return false;
                        }

                        if (newPw.length < 8) {
                            e.preventDefault();
                            alert('New password must be at least 8 characters.');
                            document.getElementById('new_password').focus();
                            return false;
                        }

                        if (!confirmPw) {
                            e.preventDefault();
                            alert('Please confirm your new password.');
                            document.getElementById('new_password_confirmation').focus();
                            return false;
                        }

                        if (newPw !== confirmPw) {
                            e.preventDefault();
                            alert('Password confirmation does not match.');
                            document.getElementById('new_password_confirmation').focus();
                            return false;
                        }

                        if (currentPw === newPw) {
                            e.preventDefault();
                            alert('New password must be different from current password.');
                            document.getElementById('new_password').focus();
                            return false;
                        }
                    }
                });
            }
        });
    </script>
@endpush