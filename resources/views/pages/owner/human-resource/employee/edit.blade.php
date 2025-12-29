@extends('layouts.owner')




@section('title', __('messages.owner.user_management.employees.edit_employee'))
@section('page_title', __('messages.owner.user_management.employees.edit_employee'))




@section('content')
    <div class="modern-employee-edit">
        <!-- Background Gradient -->
        <div class="page-background"></div>




        <div class="container-modern">
            <!-- Header Section -->
            <div class="page-header">
                <a href="{{ route('owner.user-owner.employees.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.user_management.employees.back_to_employees') }}
                </a>
                <div class="header-content">
                    <h1 class="page-title">Edit Employee</h1>
                    <p class="page-subtitle">Update employee information and access settings.</p>
                </div>
            </div>




            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                    </div>
                    <div class="alert-content">
                        <strong>{{ __('messages.owner.user_management.employees.alert_1') }}:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif




            @if ($errors->has('error'))
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                    </div>
                    <div class="alert-content">
                        {{ $errors->first('error') }}
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
                <input type="hidden" id="usernameCheckUrl" value="{{ route('owner.user-owner.employees.check-username') }}">
               
                <form action="{{ route('owner.user-owner.employees.update', $employee) }}" method="POST" enctype="multipart/form-data" id="employeeForm">
                    @csrf
                    @method('PUT')




                    <div class="card-body-modern">
                        <!-- Profile Section -->
                        <div class="profile-section">
                            <!-- Profile Picture Upload -->
                            <div class="profile-picture-wrapper">
                                <div class="profile-picture-container" id="profilePictureContainer">
                                    <div class="upload-placeholder" id="uploadPlaceholder" style="{{ $employee->image ? 'display: none;' : '' }}">
                                        <span class="material-symbols-outlined">add_a_photo</span>
                                        <span class="upload-text">Upload</span>
                                    </div>
                                    <img id="imagePreview" class="profile-preview {{ $employee->image ? 'active' : '' }}"
                                         src="{{ $employee->image ? asset('storage/' . $employee->image) : '' }}"
                                         alt="Profile Preview">
                                </div>
                                <button type="button" class="edit-picture-btn" id="editPictureBtn">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
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
                                    <div class="section-icon">
                                        <span class="material-symbols-outlined">person</span>
                                    </div>
                                    <h3 class="section-title">Personal Information</h3>
                                </div>




                                <div class="row g-4">
                                    <!-- Full Name -->
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.user_management.employees.employee_name') }}
                                            </label>
                                            <input type="text" name="name" id="name"
                                                   class="form-control-modern @error('name') is-invalid @enderror"
                                                   value="{{ old('name', $employee->name) }}"
                                                   placeholder="e.g. Jane Doe"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>




                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.user_management.employees.employee_email') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" name="email" id="email"
                                                   class="form-control-modern @error('email') is-invalid @enderror"
                                                   value="{{ old('email', $employee->email) }}"
                                                   placeholder="e.g. jane.doe@techcorp.com"
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>




                                    <!-- Role -->
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.user_management.employees.role') }}
                                            </label>
                                            <div class="select-wrapper">
                                                <select name="role" id="role"
                                                        class="form-control-modern @error('role') is-invalid @enderror"
                                                        required>
                                                    <option value="">Select a role...</option>
                                                    <option value="CASHIER" {{ old('role', $employee->role) == 'CASHIER' ? 'selected' : '' }}>
                                                        {{ __('messages.owner.user_management.employees.cashier') }}
                                                    </option>
                                                    <option value="KITCHEN" {{ old('role', $employee->role) == 'KITCHEN' ? 'selected' : '' }}>
                                                        {{ __('messages.owner.user_management.employees.kitchen') }}
                                                    </option>
                                                </select>
                                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                                            </div>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>




                                    <!-- Outlet / Branch -->
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.user_management.employees.outlet') }}
                                            </label>
                                            <div class="select-wrapper">
                                                <select name="partner" id="partner"
                                                        class="form-control-modern @error('partner') is-invalid @enderror"
                                                        required>
                                                    <option value="">Select location...</option>
                                                    @foreach ($partners as $partner)
                                                        <option value="{{ $partner->id }}" {{ old('partner_id', $employee->partner_id) == $partner->id ? 'selected' : '' }}>
                                                            {{ $partner->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="material-symbols-outlined select-arrow">location_on</span>
                                            </div>
                                            @error('partner')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>




                                    <!-- Status Active/Inactive -->
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern d-block">Status</label>
                                            <input type="hidden" name="is_active" value="0">
                                            <div class="status-switch">
                                                <label class="switch-modern">
                                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                                           {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                                                    <span class="slider-modern"></span>
                                                </label>
                                                <span class="status-label" id="statusLabel">
                                                    {{ old('is_active', $employee->is_active) ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




                        <!-- Divider -->
                        <div class="section-divider"></div>




                        <!-- Account Access Section -->
                        <div class="account-section">
                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">lock</span>
                                </div>
                                <h3 class="section-title">Account Access</h3>
                            </div>




                            <div class="row g-4">
                                <!-- Username -->
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.user_management.employees.username') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="username-group">
                                            <div class="username-input-wrapper">
                                                <span class="input-icon">
                                                    <span class="material-symbols-outlined">alternate_email</span>
                                                </span>
                                                <input type="text" name="username" id="username"
                                                       class="form-control-modern with-icon @error('username') is-invalid @enderror"
                                                       value="{{ old('username', $employee->user_name) }}"
                                                       placeholder="e.g. jdoe23"
                                                       data-exclude-id="{{ $employee->id }}"
                                                       required>
                                            </div>
                                            <button type="button" id="btnCheckUsername" class="btn-check-modern">
                                                <span class="label">Check</span>
                                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                            </button>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            {{ __('messages.owner.user_management.employees.muted_text_1') }}
                                        </small>
                                        <div id="usernameStatus" class="mt-2"></div>
                                        @error('username')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>




                                <div class="col-md-6"></div>




                                <!-- Password -->
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.user_management.employees.password_optional') }}
                                        </label>
                                        <div class="password-wrapper">
                                            <input type="password" name="password" id="password"
                                                   class="form-control-modern @error('password') is-invalid @enderror"
                                                   placeholder="Leave blank to keep current">
                                            <button type="button" class="password-toggle" id="togglePassword">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </button>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            {{ __('messages.owner.user_management.employees.muted_text_2') }}
                                        </small>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>




                                <!-- Confirm Password -->
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.user_management.employees.password_confirmation') }}
                                        </label>
                                        <div class="password-wrapper">
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                   class="form-control-modern @error('password_confirmation') is-invalid @enderror"
                                                   placeholder="Re-enter password if changed">
                                            <button type="button" class="password-toggle" id="togglePasswordConfirm">
                                                <span class="material-symbols-outlined">visibility_off</span>
                                            </button>
                                        </div>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




                    <!-- Card Footer -->
                    <div class="card-footer-modern">
                        <a href="{{ route('owner.user-owner.employees.index') }}" class="btn-cancel-modern">
                            {{ __('messages.owner.user_management.employees.cancel') }}
                        </a>
                        <button type="submit" class="btn-submit-modern">
                            <span class="material-symbols-outlined">save</span>
                            {{ __('messages.owner.user_management.employees.update') }}
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
                            Crop Employee Photo
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




    <style>
/* Import Material Icons & Fonts */
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200');
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');


/* Root Variables */
.modern-employee-edit {
    --primary: #ae1504;
    --primary-hover: #8a1103;
    --background: #f0f2f5;
    --card-bg: #ffffff;
    --text-primary: #0d141b;
    --text-secondary: #4c739a;
    --text-muted: #93adc8;
    --border-color: #e3e8ef;
    --shadow-soft: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
    --radius: 2rem;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    position: relative;
    min-height: 100vh;
    padding-bottom: 4rem;
}


/* Background Gradient - HIDDEN */
.modern-employee-edit .page-background {
    display: none;
}


/* Container */
.modern-employee-edit .container-modern {
    position: relative;
    z-index: 1;
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem 1rem;
}


/* Page Header */
.modern-employee-edit .page-header {
    margin-bottom: 2rem;
}


.modern-employee-edit .back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 1.5rem;
    transition: all 0.3s;
    font-size: 0.95rem;
}


.modern-employee-edit .back-button:hover {
    color: var(--primary);
    transform: translateX(-4px);
}


.modern-employee-edit .back-button .material-symbols-outlined {
    font-size: 20px;
}


.modern-employee-edit .header-content {
    text-align: left;
    margin-bottom: 2rem;
}


.modern-employee-edit .page-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
    line-height: 1.2;
}


.modern-employee-edit .page-subtitle {
    font-size: 1.125rem;
    color: var(--text-secondary);
    font-weight: 500;
    margin: 0;
}


/* Modern Alerts */
.modern-employee-edit .alert-modern {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    border-radius: 1rem;
    border: none;
    padding: 1rem 1.25rem;
    margin-bottom: 2rem;
}


.modern-employee-edit .alert-modern .alert-icon {
    flex-shrink: 0;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}


.modern-employee-edit .alert-modern .alert-icon .material-symbols-outlined {
    font-size: 20px;
}


.modern-employee-edit .alert-modern .alert-content {
    flex: 1;
}


.modern-employee-edit .alert-danger.alert-modern {
    background: #fff5f5;
    color: #991b1b;
}


.modern-employee-edit .alert-danger.alert-modern .alert-icon {
    background: #fee2e2;
    color: #991b1b;
}


.modern-employee-edit .alert-success.alert-modern {
    background: #f0fdf4;
    color: #166534;
}


.modern-employee-edit .alert-success.alert-modern .alert-icon {
    background: #dcfce7;
    color: #166534;
}


.modern-employee-edit .alert-info.alert-modern {
    background: #eff6ff;
    color: #1d4ed8;
}


.modern-employee-edit .alert-info.alert-modern .alert-icon {
    background: #dbeafe;
    color: #1d4ed8;
}


/* Modern Card */
.modern-employee-edit .modern-card {
    background: var(--card-bg);
    border-radius: var(--radius);
    box-shadow: var(--shadow-soft);
    overflow: hidden;
}


.modern-employee-edit .card-body-modern {
    padding: 3rem 3rem 2rem;
}


/* Profile Section */
.modern-employee-edit .profile-section {
    display: flex;
    gap: 3.5rem;
    align-items: flex-start;
}


.modern-employee-edit .profile-picture-wrapper {
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}


.modern-employee-edit .profile-picture-container {
    position: relative;
    width: 10rem;
    height: 10rem;
    border-radius: 2.5rem;
    background: #f8fafc;
    border: 4px solid #ffffff;
    box-shadow: 0 0 0 4px #f1f5f9, 0 10px 30px -5px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
}


.modern-employee-edit .profile-picture-container:hover {
    transform: scale(1.02);
    box-shadow: 0 0 0 4px #e0e7ff, 0 15px 40px -5px rgba(0, 0, 0, 0.15);
}


.modern-employee-edit .upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    color: var(--text-muted);
    transition: all 0.3s;
    pointer-events: none;
}


.modern-employee-edit .profile-picture-container:hover .upload-placeholder {
    color: var(--primary);
    transform: scale(1.05);
}


.modern-employee-edit .upload-placeholder .material-symbols-outlined {
    font-size: 2.5rem;
}


.modern-employee-edit .upload-text {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}


.modern-employee-edit .profile-preview {
    display: none;
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}


.modern-employee-edit .profile-preview.active {
    display: block;
}


.modern-employee-edit .edit-picture-btn {
    position: absolute;
    bottom: -0.5rem;
    right: -0.5rem;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 1rem;
    background: var(--primary);
    color: white;
    border: 4px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(174, 21, 4, 0.4);
}


.modern-employee-edit .edit-picture-btn:hover {
    background: var(--primary-hover);
    transform: scale(1.1);
}


.modern-employee-edit .edit-picture-btn .material-symbols-outlined {
    font-size: 16px;
}


/* Personal Info Fields */
.modern-employee-edit .personal-info-fields {
    flex: 1;
    min-width: 0;
}


.modern-employee-edit .section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.75rem;
}


.modern-employee-edit .section-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}


.modern-employee-edit .section-icon-red {
    background: rgba(174, 21, 4, 0.1);
    color: var(--primary);
}


.modern-employee-edit .section-icon .material-symbols-outlined {
    font-size: 16px;
}


.modern-employee-edit .section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}


/* Form Groups */
.modern-employee-edit .form-group-modern {
    margin-bottom: 0;
}


.modern-employee-edit .form-label-modern {
    display: block;
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--text-secondary);
    margin-bottom: 0.75rem;
    margin-left: 0.5rem;
}


.modern-employee-edit .form-control-modern {
    width: 100%;
    height: 3.5rem;
    padding: 0 1.5rem;
    border-radius: 1rem;
    border: none;
    background: rgba(241, 245, 249, 0.5);
    color: var(--text-primary);
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}


.modern-employee-edit .form-control-modern:hover {
    background: rgba(241, 245, 249, 0.8);
}


.modern-employee-edit .form-control-modern:focus {
    outline: none;
    background: white;
    box-shadow: 0 0 0 3px rgba(174, 21, 4, 0.15);
}


.modern-employee-edit .form-control-modern::placeholder {
    color: var(--text-muted);
}


/* Select Wrapper */
.modern-employee-edit .select-wrapper {
    position: relative;
}


.modern-employee-edit .select-wrapper select {
    appearance: none;
    padding-right: 3rem;
    cursor: pointer;
}


.modern-employee-edit .select-arrow {
    position: absolute;
    right: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: var(--text-secondary);
    font-size: 24px;
}


/* Status Switch */
.modern-employee-edit .status-switch {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: rgba(241, 245, 249, 0.5);
    border-radius: 1rem;
    transition: all 0.3s;
}


.modern-employee-edit .status-switch:hover {
    background: rgba(241, 245, 249, 0.8);
}


.modern-employee-edit .switch-modern {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
}


.modern-employee-edit .switch-modern input {
    opacity: 0;
    width: 0;
    height: 0;
}


.modern-employee-edit .slider-modern {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: 0.4s;
    border-radius: 34px;
}


.modern-employee-edit .slider-modern:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}


.modern-employee-edit .switch-modern input:checked + .slider-modern {
    background-color: var(--primary);
}


.modern-employee-edit .switch-modern input:checked + .slider-modern:before {
    transform: translateX(24px);
}


.modern-employee-edit .status-label {
    font-weight: 600;
    color: var(--text-primary);
}


/* Username Group */
.modern-employee-edit .username-group {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
}


.modern-employee-edit .username-input-wrapper {
    position: relative;
    flex: 1;
}


.modern-employee-edit .input-icon {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    z-index: 1;
    pointer-events: none;
}


.modern-employee-edit .input-icon .material-symbols-outlined {
    font-size: 20px;
}


.modern-employee-edit .form-control-modern.with-icon {
    padding-left: 3rem;
}


.modern-employee-edit .btn-check-modern {
    height: 3.5rem;
    padding: 0 1.75rem;
    border-radius: 9999px;
    border: 2px solid #f1f5f9;
    background: white;
    color: var(--primary);
    font-weight: 700;
    font-size: 0.875rem;
    white-space: nowrap;
    transition: all 0.3s;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}


.modern-employee-edit .btn-check-modern:hover:not(:disabled) {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(174, 21, 4, 0.25);
}


.modern-employee-edit .btn-check-modern:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}


/* Password Wrapper */
.modern-employee-edit .password-wrapper {
    position: relative;
}


.modern-employee-edit .password-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}


.modern-employee-edit .password-toggle:hover {
    background: #f1f5f9;
    color: var(--primary);
}


.modern-employee-edit .password-toggle .material-symbols-outlined {
    font-size: 20px;
}


/* Section Divider */
.modern-employee-edit .section-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, var(--border-color), transparent);
    margin: 3rem 0;
}


/* Username Status */
.modern-employee-edit #usernameStatus .badge {
    display: inline-block;
    padding: 0.4rem 0.85rem;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 0.75rem;
}


.modern-employee-edit #usernameStatus .bg-success {
    background: #ecfdf5 !important;
    color: #065f46 !important;
    border: 1px solid #a7f3d0;
}


.modern-employee-edit #usernameStatus .bg-danger {
    background: #fee2e2 !important;
    color: #991b1b !important;
    border: 1px solid #fecaca;
}


.modern-employee-edit #usernameStatus .text-success {
    color: #065f46 !important;
}


.modern-employee-edit #usernameStatus .text-danger {
    color: #991b1b !important;
}


/* Card Footer */
.modern-employee-edit .card-footer-modern {
    padding: 1.5rem 3rem;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}


.modern-employee-edit .btn-cancel-modern {
    height: 3rem;
    padding: 0 2rem;
    border-radius: 9999px;
    border: 1px solid var(--border-color);
    background: white;
    color: var(--text-secondary);
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    text-decoration: none;
    transition: all 0.3s;
    cursor: pointer;
}


.modern-employee-edit .btn-cancel-modern:hover {
    background: #f8fafc;
    color: var(--text-primary);
    border-color: var(--text-muted);
    transform: translateY(-2px);
}


.modern-employee-edit .btn-submit-modern {
    height: 3rem;
    padding: 0 2rem;
    border-radius: 9999px;
    border: none;
    background: var(--primary);
    color: white;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 10px 25px -10px rgba(174, 21, 4, 0.4);
}


.modern-employee-edit .btn-submit-modern:hover {
    background: var(--primary-hover);
    box-shadow: 0 15px 35px -10px rgba(174, 21, 4, 0.5);
    transform: translateY(-2px);
}


.modern-employee-edit .btn-submit-modern .material-symbols-outlined,
.modern-employee-edit .btn-cancel-modern .material-symbols-outlined {
    font-size: 18px;
}


/* Modal Styles */
.modern-employee-edit .modern-modal {
    border-radius: 1.25rem;
    border: none;
    overflow: hidden;
}


.modern-employee-edit .modern-modal-header {
    background: var(--primary);
    color: white;
    padding: 1.25rem 1.5rem;
    border: none;
}


.modern-employee-edit .modern-modal-header .modal-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 700;
    font-size: 1.125rem;
}


.modern-employee-edit .modern-modal-header .modal-title .material-symbols-outlined {
    font-size: 24px;
}


.modern-employee-edit .modern-modal-header .close {
    color: white;
    opacity: 1;
    text-shadow: none;
    font-size: 2rem;
    font-weight: 300;
    line-height: 1;
    padding: 0;
    margin: 0;
}


.modern-employee-edit .modern-modal-header .close:hover {
    opacity: 0.8;
}


.modern-employee-edit .modern-modal-footer {
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    padding: 1rem 1.5rem;
}


.modern-employee-edit .img-container-crop {
    width: 100%;
    height: 450px;
    background: #ffffff;
    border-radius: 0.75rem;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e5e7eb;
}


.modern-employee-edit .img-container-crop img {
    max-width: 100%;
    display: block;
}


/* Cropper Circular */
.modern-employee-edit #cropModal .cropper-view-box,
.modern-employee-edit #cropModal .cropper-face {
    border-radius: 50% !important;
}


.modern-employee-edit #cropModal .cropper-container {
    background-color: #f8f9fa;
}


/* Responsive */
@media (max-width: 992px) {
    .modern-employee-edit .container-modern {
        padding: 1.5rem 1rem;
    }


    .modern-employee-edit .page-header {
        margin-bottom: 1.5rem;
    }


    .modern-employee-edit .back-button {
        margin-bottom: 1rem;
    }


    .modern-employee-edit .header-content {
        margin-bottom: 1.5rem;
    }


    .modern-employee-edit .profile-section {
        flex-direction: column;
        align-items: center;
        gap: 2rem;
    }


    .modern-employee-edit .personal-info-fields {
        width: 100%;
    }


    .modern-employee-edit .section-header {
        justify-content: center;
    }
}


@media (max-width: 768px) {
    .modern-employee-edit .container-modern {
        padding: 1rem 1rem;
    }


    .modern-employee-edit .page-header {
        margin-bottom: 1.5rem;
    }


    .modern-employee-edit .back-button {
        margin-bottom: 1rem;
    }


    .modern-employee-edit .header-content {
        margin-bottom: 1.5rem;
    }


    .modern-employee-edit .page-title {
        font-size: 2rem;
    }


    .modern-employee-edit .card-body-modern {
        padding: 2rem 1.5rem;
    }


    .modern-employee-edit .card-footer-modern {
        padding: 1rem 1.5rem;
        flex-direction: column-reverse;
    }


    .modern-employee-edit .btn-cancel-modern,
    .modern-employee-edit .btn-submit-modern {
        width: 100%;
        justify-content: center;
    }


    .modern-employee-edit .username-group {
        flex-direction: column;
    }


    .modern-employee-edit .btn-check-modern {
        width: 100%;
        justify-content: center;
    }


    .modern-employee-edit .img-container-crop {
        height: 300px;
    }


    .modern-employee-edit .profile-picture-container {
        width: 8rem;
        height: 8rem;
    }


    .modern-employee-edit .alert-modern {
        margin-bottom: 1.5rem;
    }
}


@media (max-width: 576px) {
    .modern-employee-edit .container-modern {
        padding: 1rem 0.75rem;
    }


    .modern-employee-edit .page-header {
        margin-bottom: 1rem;
    }


    .modern-employee-edit .back-button {
        margin-bottom: 0.75rem;
    }


    .modern-employee-edit .header-content {
        margin-bottom: 1rem;
    }


    .modern-employee-edit .page-title {
        font-size: 1.75rem;
    }


    .modern-employee-edit .page-subtitle {
        font-size: 1rem;
    }


    .modern-employee-edit .alert-modern {
        margin-bottom: 1rem;
    }
}
</style>
@endsection
@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const input = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('uploadPlaceholder');
    const editBtn = document.getElementById('editPictureBtn');
    const container = document.getElementById('profilePictureContainer');
    const removeEl = document.getElementById('remove_image');
const MAX_SIZE = 2 * 1024 * 1024; // 2 MB
const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];




let cropper = null;
let currentFile = null;




// Status switch handler
const isActive = document.getElementById('is_active');
const statusLabel = document.getElementById('statusLabel');
if (isActive && statusLabel) {
    isActive.addEventListener('change', function() {
        statusLabel.textContent = this.checked ? 'Aktif' : 'Nonaktif';
    });
}




// Click handlers to trigger file input
if (editBtn && input) {
    editBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        input.click();
    });
}




if (container && input) {
    container.addEventListener('click', function(e) {
        e.preventDefault();
        input.click();
    });
}




// Image upload handler
if (input) {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;




        // Validate file type
        if (!ALLOWED.includes(file.type)) {
            alert('File not supported. Please use JPG, PNG, or WEBP.');
            this.value = '';
            return;
        }




        // Validate file size
        if (file.size > MAX_SIZE) {
            alert('File size exceeds 2 MB. Please choose a smaller file.');
            this.value = '';
            return;
        }




        currentFile = file;
        const reader = new FileReader();
       
        reader.onload = function(event) {
            const imgToCrop = document.getElementById('imageToCrop');
            if (imgToCrop) {
                imgToCrop.src = event.target.result;
                $('#cropModal').modal('show');
            }
        };
       
        reader.onerror = function() {
            alert('Error reading file. Please try again.');
            input.value = '';
        };
       
        reader.readAsDataURL(file);




        if (removeEl) removeEl.value = '0';
    });
}




// Initialize Cropper when modal shown
$('#cropModal').on('shown.bs.modal', function() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
   
    const imageElement = document.getElementById('imageToCrop');
    if (!imageElement || !imageElement.src) return;




    setTimeout(function() {
        cropper = new Cropper(imageElement, {
            aspectRatio: 1,
            viewMode: 2,
            dragMode: 'move',
            autoCropArea: 0.9,
            restore: true,
            guides: true,
            center: true,
            highlight: true,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
            responsive: true,
            ready: function() {
                console.log('Cropper ready');
            }
        });
    }, 350);
});




// Destroy cropper when modal hidden
$('#cropModal').on('hidden.bs.modal', function() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
});




// Crop button handler
const cropBtn = document.getElementById('cropBtn');
if (cropBtn) {
    cropBtn.addEventListener('click', function() {
        if (!cropper) {
            alert('Cropper not initialized. Please try again.');
            return;
        }




        if (!currentFile) {
            alert('No file selected. Please try again.');
            return;
        }




        const btn = this;
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';




        try {
            const isTransparent = currentFile.type === 'image/png' || currentFile.type === 'image/webp';
            const outputType = isTransparent ? currentFile.type : 'image/jpeg';
            const quality = isTransparent ? 1 : 0.92;




            const canvas = cropper.getCroppedCanvas({
                width: 800,
                height: 800,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
                fillColor: isTransparent ? 'transparent' : '#fff'
            });




            if (!canvas) {
                throw new Error('Failed to create canvas');
            }




            canvas.toBlob(function(blob) {
                if (!blob) {
                    alert('Failed to process image. Please try again.');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    return;
                }




                const croppedFile = new File([blob], currentFile.name, {
                    type: outputType,
                    lastModified: Date.now()
                });




                // Update file input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                input.files = dataTransfer.files;




                // Update preview
                const url = URL.createObjectURL(blob);
                if (preview) {
                    preview.src = url;
                    preview.classList.add('active');
                }
                if (placeholder) {
                    placeholder.style.display = 'none';
                }




                // Close modal
                $('#cropModal').modal('hide');
               
                // Reset button
                btn.disabled = false;
                btn.innerHTML = originalHTML;




                console.log('Image cropped successfully');
            }, outputType, quality);




        } catch (error) {
            console.error('Crop error:', error);
            alert('Error processing image: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    });
}




// Password toggle handlers
function bindPasswordToggle(btnId, inputId) {
    const btn = document.getElementById(btnId);
    const inp = document.getElementById(inputId);
    if (!btn || !inp) return;
   
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const isPw = inp.type === 'password';
        inp.type = isPw ? 'text' : 'password';
        const icon = btn.querySelector('.material-symbols-outlined');
        if (icon) {
            icon.textContent = isPw ? 'visibility_off' : 'visibility';
        }
    });
}




bindPasswordToggle('togglePassword', 'password');
bindPasswordToggle('togglePasswordConfirm', 'password_confirmation');




// Form validation
const form = document.getElementById('employeeForm');
const pw = document.getElementById('password');
const pwc = document.getElementById('password_confirmation');




if (form && pw && pwc) {
    form.addEventListener('submit', function(e) {
        if (pw.value.length > 0 && pw.value.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters.');
            pw.focus();
            return false;
        }
        if (pw.value.length > 0 && pw.value !== pwc.value) {
            e.preventDefault();
            alert('Password confirmation does not match.');
            pwc.focus();
            return false;
        }
    });
}




// Username availability check
const inputUsername = document.getElementById('username');
const btnCheck = document.getElementById('btnCheckUsername');
const statusEl = document.getElementById('usernameStatus');
const urlCheck = document.getElementById('usernameCheckUrl')?.value;




if (!inputUsername || !btnCheck || !statusEl || !urlCheck) return;




const spinner = btnCheck.querySelector('.spinner-border');
const btnLabel = btnCheck.querySelector('.label');




function setLoading(isLoading) {
    if (isLoading) {
        btnCheck.disabled = true;
        if (spinner) spinner.classList.remove('d-none');
        if (btnLabel) btnLabel.textContent = 'Checking...';
    } else {
        btnCheck.disabled = false;
        if (spinner) spinner.classList.add('d-none');
        if (btnLabel) btnLabel.textContent = 'Check';
    }
}




function showStatus(ok, msg) {
    if (ok) {
        statusEl.innerHTML = `<span class="badge bg-success">Available</span> <span class="text-success ms-2">${msg}</span>`;
        inputUsername.classList.remove('is-invalid');
        inputUsername.classList.add('is-valid');
    } else {
        statusEl.innerHTML = `<span class="badge bg-danger">Taken</span> <span class="text-danger ms-2">${msg}</span>`;
        inputUsername.classList.remove('is-valid');
        inputUsername.classList.add('is-invalid');
    }
}




function showNeutral(msg) {
    statusEl.textContent = msg || '';
    statusEl.className = 'mt-2 text-muted';
    inputUsername.classList.remove('is-valid', 'is-invalid');
}




async function checkUsername() {
    const val = (inputUsername.value || '').trim();




    if (!val) {
        showNeutral('');
        return;
    }
   
    if (val.length < 3 || val.length > 30 || !/^[A-Za-z0-9._-]+$/.test(val)) {
        showStatus(false, 'Invalid format. Use 3-30 characters (letters, numbers, ._-)');
        return;
    }




    try {
        setLoading(true);
        const params = new URLSearchParams({ username: val });
        const excludeId = inputUsername.dataset.excludeId || '';
        if (excludeId) params.append('exclude_id', excludeId);




        const res = await fetch(`${urlCheck}?${params.toString()}`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' },
        });




        if (res.status === 422) {
            showStatus(false, 'Invalid format.');
            return;
        }




        const data = await res.json();
        if (data && typeof data.available !== 'undefined') {
            if (data.available) {
                showStatus(true, 'This username is available! 🎉');
            } else {
                showStatus(false, 'This username is already taken.');
            }
        } else {
            showNeutral('Unable to check availability at this time.');
        }
    } catch (e) {
        console.error('Username check error:', e);
        showNeutral('Network error. Please try again.');
    } finally {
        setLoading(false);
    }
}




btnCheck.addEventListener('click', function(e) {
    e.preventDefault();
    checkUsername();
});




let debounceTimer;
inputUsername.addEventListener('input', function() {
    showNeutral('');
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(checkUsername, 600);
});
});
</script>
@endpush







