@extends('layouts.staff')
@section('title', 'Edit Profile')

@section('content')
@php
    $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager');
@endphp

<div class="modern-container">
    <div class="container-modern">

        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">Edit Profile</h1>
                <p class="page-subtitle">Update your account information</p>
            </div>
            <a href="{{ route('employee.' . $empRole . '.settings.index') }}" class="back-button">
                <span class="material-symbols-outlined">arrow_back</span>
                Back
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-modern">
                <div class="alert-icon"><span class="material-symbols-outlined">error</span></div>
                <div class="alert-content">
                    <strong>Please check your input:</strong>
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
                <div class="alert-icon"><span class="material-symbols-outlined">check_circle</span></div>
                <div class="alert-content">{{ session('success') }}</div>
            </div>
        @endif

        <div class="modern-card">
            <form action="{{ route('employee.' . $empRole . '.settings.update-personal-info') }}"
                method="POST" enctype="multipart/form-data" id="profileForm">
                @csrf

                <div class="card-body-modern">

                    {{-- Profile Picture & Personal Info --}}
                    <div class="profile-section">
                        <div class="profile-picture-wrapper">
                            <div class="profile-picture-container" id="profilePictureContainer">
                                <div class="upload-placeholder" id="uploadPlaceholder"
                                    style="{{ $employee->image ? 'display: none;' : '' }}">
                                    <span class="material-symbols-outlined">add_a_photo</span>
                                    <span class="upload-text">Upload Photo</span>
                                </div>
                                <img id="imagePreview"
                                    class="profile-preview {{ $employee->image ? 'active' : '' }}"
                                    src="{{ $employee->image ? asset('storage/' . $employee->image) : '' }}"
                                    alt="Profile Preview">
                                <button type="button" id="removeImageBtn" class="btn-remove btn-remove-top"
                                    style="{{ $employee->image ? 'display: block;' : 'display: none;' }}">
                                    <span class="material-symbols-outlined">close</span>
                                </button>
                            </div>
                            <input type="file" name="image" id="image" accept="image/*" style="display: none;">
                            <input type="hidden" name="remove_image" id="remove_image" value="0">
                            <small class="text-muted d-block text-center mt-2">JPG, PNG, WebP, max 2MB</small>
                            @error('image')
                                <div class="text-danger text-center mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="personal-info-fields">
                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">person</span>
                                </div>
                                <h3 class="section-title">Personal Information</h3>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name"
                                            class="form-control-modern @error('name') is-invalid @enderror"
                                            value="{{ old('name', $employee->name) }}"
                                            placeholder="Enter your full name" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Email</label>
                                        <input type="email" class="form-control-modern"
                                            value="{{ $employee->email }}" readonly disabled
                                            style="background-color: #f8f9fa; cursor: not-allowed;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Username</label>
                                        <input type="text" class="form-control-modern"
                                            value="{{ $employee->user_name }}" readonly disabled
                                            style="background-color: #f8f9fa; cursor: not-allowed;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Role</label>
                                        <input type="text" class="form-control-modern"
                                            value="{{ $employee->role }}" readonly disabled
                                            style="background-color: #f8f9fa; cursor: not-allowed;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    {{-- Change Password --}}
                    <div class="account-section">
                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">lock</span>
                            </div>
                            <h3 class="section-title">Change Password (Optional)</h3>
                        </div>

                        <div class="alert alert-info alert-modern mb-4">
                            <div class="alert-icon"><span class="material-symbols-outlined">info</span></div>
                            <div class="alert-content">
                                <small>Leave password fields empty if you don't want to change your password.</small>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Current Password</label>
                                    <div class="password-wrapper">
                                        <input type="password" name="current_password" id="current_password"
                                            class="form-control-modern @error('current_password') is-invalid @enderror"
                                            placeholder="Enter current password">
                                        <button type="button" class="password-toggle" id="toggleCurrentPassword">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">New Password</label>
                                    <div class="password-wrapper">
                                        <input type="password" name="new_password" id="new_password"
                                            class="form-control-modern @error('new_password') is-invalid @enderror"
                                            placeholder="Enter new password (min. 8 characters)">
                                        <button type="button" class="password-toggle" id="toggleNewPassword">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </button>
                                    </div>
                                    @error('new_password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Confirm New Password</label>
                                    <div class="password-wrapper">
                                        <input type="password" name="new_password_confirmation"
                                            id="new_password_confirmation"
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

                <div class="card-footer-modern">
                    <a href="{{ route('employee.' . $empRole . '.settings.index') }}" class="btn-cancel-modern">
                        Cancel
                    </a>
                    <button type="submit" class="btn-submit-modern">Save Changes</button>
                </div>
            </form>
        </div>

    </div>

    {{-- Crop Modal --}}
    <div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content modern-modal">
                <div class="modal-header modern-modal-header">
                    <h5 class="modal-title">
                        <span class="material-symbols-outlined">crop</span> Crop Photo
                    </h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body p-4">
                    <div class="img-container-crop">
                        <img id="imageToCrop" style="max-width: 100%;" alt="Crop">
                    </div>
                </div>
                <div class="modal-footer modern-modal-footer">
                    <button type="button" class="btn-cancel-modern" data-dismiss="modal">
                        <span class="material-symbols-outlined">close</span> Cancel
                    </button>
                    <button type="button" id="cropBtn" class="btn-submit-modern">
                        <span class="material-symbols-outlined">check</span> Save Crop
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
        ImageCropper.init({
            id: 'profile',
            inputId: 'image',
            previewId: 'imagePreview',
            modalId: 'cropModal',
            imageToCropId: 'imageToCrop',
            cropBtnId: 'cropBtn',
            containerId: 'profilePictureContainer',
            removeInputId: 'remove_image',
            aspectRatio: 1,
            outputWidth: 800,
            outputHeight: 800
        });

        ImageRemoveHandler.init({
            removeBtnId: 'removeImageBtn',
            imageInputId: 'image',
            imagePreviewId: 'imagePreview',
            uploadPlaceholderId: 'uploadPlaceholder',
            removeInputId: 'remove_image',
            confirmRemove: false
        });

        function bindPasswordToggle(btnId, inputId) {
            const btn = document.getElementById(btnId);
            const inp = document.getElementById(inputId);
            if (!btn || !inp) return;
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const isPw = inp.type === 'password';
                inp.type = isPw ? 'text' : 'password';
                const icon = btn.querySelector('.material-symbols-outlined');
                if (icon) icon.textContent = isPw ? 'visibility_off' : 'visibility';
            });
        }

        bindPasswordToggle('toggleCurrentPassword', 'current_password');
        bindPasswordToggle('toggleNewPassword', 'new_password');
        bindPasswordToggle('toggleConfirmPassword', 'new_password_confirmation');

        const form = document.getElementById('profileForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                const name = document.getElementById('name').value.trim();
                const currentPw = document.getElementById('current_password').value;
                const newPw = document.getElementById('new_password').value;
                const confirmPw = document.getElementById('new_password_confirmation').value;

                if (!name) {
                    e.preventDefault();
                    alert('Please enter your full name.');
                    return false;
                }

                if (currentPw || newPw || confirmPw) {
                    if (!currentPw) {
                        e.preventDefault();
                        alert('Please enter your current password.');
                        document.getElementById('current_password').focus();
                        return false;
                    }
                    if (!newPw) {
                        e.preventDefault();
                        alert('Please enter a new password.');
                        document.getElementById('new_password').focus();
                        return false;
                    }
                    if (newPw.length < 8) {
                        e.preventDefault();
                        alert('New password must be at least 8 characters.');
                        document.getElementById('new_password').focus();
                        return false;
                    }
                    if (newPw !== confirmPw) {
                        e.preventDefault();
                        alert('Passwords do not match.');
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