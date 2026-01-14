@extends('layouts.owner')
@section('page_title', __('messages.owner.verification.page_title'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <!-- Header Section -->
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.verification.header_title') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.verification.header_desc') }}</p>
                </div>
            </div>

            <!-- Rejection Alert -->
            @if($owner->verification_status === 'rejected' && $latestVerification && $latestVerification->rejection_reason)
            <div class="alert-danger alert-modern">
                <div class="alert-icon">
                    <span class="material-symbols-outlined">warning</span>
                </div>
                <div class="alert-content">
                    <h4 class="fw-bold">
                        {{ __('messages.owner.verification.rejected_title') }}
                    </h4>
                    <div>
                        <p class="fw-bold">
                            {{ __('messages.owner.verification.rejection_reason') }}
                        </p>
                        <p>{{ $latestVerification->rejection_reason }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Form Card -->
            <div class="modern-card">
                <form action="{{ route('owner.user-owner.verification.store') }}" method="POST" enctype="multipart/form-data" id="verificationForm">
                    @csrf
                    <div class="card-body-modern">
                        
                        <!-- Personal Information Section -->
                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                            <div>
                                <h3 class="section-title mb-1">{{ __('messages.owner.verification.personal_title') }}</h3>
                            </div>
                        </div>

                        <div class="row g-4 mt-2">
                            <!-- Owner Name -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.verification.owner_name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="owner_name" required
                                        class="form-control-modern @error('owner_name') is-invalid @enderror"
                                        placeholder="{{ __('messages.owner.verification.owner_name_placeholder') }}" 
                                        minlength="3"
                                        value="{{ old('owner_name', $latestVerification->owner_name ?? $owner->name ?? '') }}">
                                    @error('owner_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">{{ __('messages.owner.verification.err_name_min') }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Owner Phone -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.verification.owner_phone') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" name="owner_phone" required
                                        class="form-control-modern @error('owner_phone') is-invalid @enderror"
                                        placeholder="08xxxxxxxxxx" 
                                        pattern="^(08|62)\d{8,12}$" 
                                        minlength="10" 
                                        maxlength="15"
                                        value="{{ old('owner_phone', $latestVerification->owner_phone ?? $owner->phone_number ?? '') }}">
                                    @error('owner_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">{{ __('messages.owner.verification.err_phone_format') }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Owner Email (Disabled) -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.verification.owner_email') }}
                                    </label>
                                    <input type="text" 
                                        value="{{ $owner->email }}" 
                                        disabled
                                        class="form-control-modern bg-light text-muted">
                                </div>
                            </div>

                            <!-- KTP Number -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.verification.ktp_number') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="ktp_number" required 
                                        maxlength="16" 
                                        minlength="16"
                                        class="form-control-modern @error('ktp_number') is-invalid @enderror"
                                        placeholder="{{ __('messages.owner.verification.ktp_number_placeholder') }}" 
                                        pattern="\d{16}"
                                        value="{{ old('ktp_number', $latestVerification->ktp_number_decrypted ?? '') }}">
                                    @error('ktp_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">{{ __('messages.owner.verification.err_ktp_format') }}</div>
                                    @enderror
                                </div>
                            </div>

                          <!-- KTP Photo Upload -->
                            <div class="col-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.verification.ktp_photo') }}
                                        @if($latestVerification)
                                            <span class="text-muted">{{ __('messages.owner.verification.ktp_optional') }}</span>
                                        @else
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    <div class="profile-picture-wrapper" style="display: inline-block;">
                                        <div class="profile-picture-container" id="ktpPictureContainer">
                                            @if($latestVerification && $latestVerification->ktp_photo_path)
                                                <!-- Show existing KTP image -->
                                                <img src="{{ route('owner.user-owner.verification.ktp-image') }}" 
                                                    class="profile-preview" 
                                                    alt="KTP Current"
                                                    id="currentKtpImage"
                                                    style="display: block;">
                                                <!-- Overlay for hover effect -->
                                                <div class="upload-overlay">
                                                    <span class="material-symbols-outlined">edit</span>
                                                    <span class="upload-text">Ganti Foto</span>
                                                </div>
                                            @else
                                                <!-- Show upload placeholder for first time -->
                                                <div class="upload-placeholder" id="uploadPlaceholderKTP">
                                                    <span class="material-symbols-outlined">badge</span>
                                                    <span class="upload-text">Upload KTP</span>
                                                </div>
                                            @endif
                                            <!-- New preview will be shown here when user selects new image -->
                                            <img id="imagePreviewKTP" class="profile-preview" alt="KTP Preview" style="display: none;">
                                        </div>
                                    </div>
                                    <input type="file" name="ktp_photo" id="ktp_photo" 
                                        {{ $latestVerification ? '' : 'required' }}
                                        accept="image/jpeg,image/jpg,image/png" hidden>
                                    <small class="text-muted d-block mt-2">
                                        {{ __('messages.owner.verification.ktp_upload_hint') }}
                                    </small>
                                    @error('ktp_photo')
                                        <div class="text-danger small text-center mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="section-divider"></div>

                        <!-- Business Information Section -->
                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">store</span>
                            </div>
                            <div>
                                <h3 class="section-title mb-1">{{ __('messages.owner.verification.business_title') }}</h3>
                            </div>
                        </div>

                        <div class="row g-4 mt-2">
                            <!-- Business Name -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.verification.business_name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="business_name" required minlength="3"
                                        class="form-control-modern @error('business_name') is-invalid @enderror"
                                        placeholder="{{ __('messages.owner.verification.business_name_placeholder') }}"
                                        value="{{ old('business_name', $latestVerification->business_name ?? '') }}">
                                    @error('business_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">{{ __('messages.owner.verification.err_name_min') }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Business Category -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.verification.business_category') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="business_category_id" required
                                            class="form-control-modern @error('business_category_id') is-invalid @enderror">
                                            <option value="">{{ __('messages.owner.verification.business_category_select') }}</option>
                                            @foreach($businessCategories as $category)
                                                <option value="{{ $category->id }}" 
                                                    {{ old('business_category_id', $latestVerification->business_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                                    </div>
                                    @error('business_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Business Address -->
                            <div class="col-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.verification.business_address') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="business_address" required rows="3" minlength="10"
                                        class="form-control-modern @error('business_address') is-invalid @enderror"
                                        placeholder="{{ __('messages.owner.verification.business_address_placeholder') }}">{{ old('business_address', $latestVerification->business_address ?? '') }}</textarea>
                                    @error('business_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">{{ __('messages.owner.verification.err_address_min') }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Business Phone -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.verification.business_phone') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" name="business_phone" required
                                        class="form-control-modern @error('business_phone') is-invalid @enderror"
                                        placeholder="08xxxxxxxxxx" 
                                        pattern="^(08|62)\d{8,12}$" 
                                        minlength="10" 
                                        maxlength="15"
                                        value="{{ old('business_phone', $latestVerification->business_phone ?? '') }}">
                                    @error('business_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">{{ __('messages.owner.verification.err_phone_format') }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Business Email -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.verification.business_email') }}
                                        <span class="text-muted">{{ __('messages.owner.verification.business_email_optional') }}</span>
                                    </label>
                                    <input type="email" name="business_email"
                                        class="form-control-modern @error('business_email') is-invalid @enderror"
                                        placeholder="bisnis@email.com" 
                                        pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                        value="{{ old('business_email', $latestVerification->business_email ?? '') }}">
                                    @error('business_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">{{ __('messages.owner.verification.err_email_format') }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Business Logo Upload -->
                            <div class="col-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.verification.business_logo') }}
                                        <span class="text-muted">{{ __('messages.owner.verification.business_email_optional') }}</span>
                                    </label>

                                    <div class="profile-picture-wrapper" style="display: inline-block;">
                                        <div class="profile-picture-container" id="logoPictureContainer">
                                            @if($latestVerification && $latestVerification->business_logo_path)
                                                <!-- Show existing Logo -->
                                                <img src="{{ Storage::url($latestVerification->business_logo_path) }}" 
                                                    class="profile-preview" 
                                                    alt="Logo Current"
                                                    id="currentLogoImage"
                                                    style="display: block;">
                                                <!-- Overlay for hover effect -->
                                                <div class="upload-overlay">
                                                    <span class="material-symbols-outlined">edit</span>
                                                    <span class="upload-text">Ganti Logo</span>
                                                </div>
                                            @else
                                                <!-- Show upload placeholder for first time -->
                                                <div class="upload-placeholder" id="uploadPlaceholderLogo">
                                                    <span class="material-symbols-outlined">add_business</span>
                                                    <span class="upload-text">Upload Logo</span>
                                                </div>
                                            @endif
                                            <!-- New preview will be shown here when user selects new image -->
                                            <img id="imagePreviewLogo" class="profile-preview" alt="Logo Preview" style="display: none;">
                                        </div>
                                    </div>
                                    <input type="file" name="business_logo" id="business_logo"
                                        accept="image/jpeg,image/jpg,image/png" hidden>
                                    <small class="text-muted d-block mt-2">
                                        {{ __('messages.owner.verification.logo_upload_hint') }}
                                    </small>
                                    @error('business_logo')
                                        <div class="text-danger small text-center mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="section-divider"></div>

                        <!-- Important Information Box -->
                        <div class="alert-info alert-modern">
                            <div class="alert-icon">
                                <span class="material-symbols-outlined">info</span>
                            </div>
                            <div class="alert-content">
                                <h4 style="margin: 0 0 0.75rem 0; font-size: 1.125rem; font-weight: 700;">
                                    {{ __('messages.owner.verification.important_info') }}
                                </h4>
                                <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.875rem; line-height: 1.7;">
                                    <li style="margin-bottom: 0.375rem;">{{ __('messages.owner.verification.info_1') }}</li>
                                    <li style="margin-bottom: 0.375rem;">{{ __('messages.owner.verification.info_2') }}</li>
                                    <li style="margin-bottom: 0.375rem;">{{ __('messages.owner.verification.info_3') }}</li>
                                    <li>{{ __('messages.owner.verification.info_4') }}</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="form-check-modern mt-4">
                            <input type="checkbox" id="terms" name="terms" required class="form-check-input-modern">
                            <label for="terms" class="form-check-label-modern">
                                {{ __('messages.owner.verification.terms_agreement') }} 
                                <a href="#" class="text-danger">{{ __('messages.owner.verification.terms_link') }}</a> 
                                {{ __('messages.owner.verification.agreement_suffix') }} 
                                <a href="#" class="text-danger">{{ __('messages.owner.verification.privacy_link') }}</a>
                            </label>
                        </div>
                        @error('terms')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer-modern">
                        <button type="button" class="btn-cancel-modern" onclick="window.history.back()">
                            Batal
                        </button>
                        <button type="submit" id="submitBtn" class="btn-submit-modern" disabled>
                            {{ $latestVerification ? __('messages.owner.verification.btn_resend') : __('messages.owner.verification.btn_send') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('verificationForm');
            const submitBtn = document.getElementById('submitBtn');
            const termsCheckbox = document.getElementById('terms');

            // Image preview handler for KTP
            const ktpInput = document.getElementById('ktp_photo');
            const ktpPreview = document.getElementById('imagePreviewKTP');
            const ktpPlaceholder = document.getElementById('uploadPlaceholderKTP');
            const ktpContainer = document.getElementById('ktpPictureContainer');

            // Image preview handler for Logo
            const logoInput = document.getElementById('business_logo');
            const logoPreview = document.getElementById('imagePreviewLogo');
            const logoPlaceholder = document.getElementById('uploadPlaceholderLogo');
            const logoContainer = document.getElementById('logoPictureContainer');

            // Setup KTP upload
            if (ktpContainer && ktpInput) {
                ktpContainer.addEventListener('click', function(e) {
                    e.preventDefault();
                    ktpInput.click();
                });

                ktpInput.addEventListener('change', function(e) {
                    handleImageUpload(this, ktpPreview, ktpPlaceholder, 1 * 1024 * 1024); // 1MB max
                });
            }

            // Setup Logo upload
            if (logoContainer && logoInput) {
                logoContainer.addEventListener('click', function(e) {
                    e.preventDefault();
                    logoInput.click();
                });

                logoInput.addEventListener('change', function(e) {
                    handleImageUpload(this, logoPreview, logoPlaceholder, 2 * 1024 * 1024); // 2MB max
                });
            }

            // Handle image upload
            function handleImageUpload(input, preview, placeholder, maxSize) {
                const file = input.files[0];
                
                if (!file) {
                    checkFormValidity();
                    return;
                }

                // Validate file size
                if (file.size > maxSize) {
                    const sizeLimit = maxSize === 1048576 ? '1MB' : '2MB';
                    alert(`{{ __('messages.owner.verification.err_file_image') }} (Max ${sizeLimit})`);
                    input.value = '';
                    input.classList.add('is-invalid');
                    checkFormValidity();
                    return;
                }

                // Validate file type
                if (!file.type.match('image/(jpeg|jpg|png)')) {
                    alert('{{ __('messages.owner.verification.err_file_image') }}');
                    input.value = '';
                    input.classList.add('is-invalid');
                    checkFormValidity();
                    return;
                }

                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                    checkFormValidity();
                };
                reader.readAsDataURL(file);
            }

            // Get all required inputs
            const requiredInputs = form.querySelectorAll('input[required], select[required], textarea[required]');

            // Add input listeners for real-time validation
            requiredInputs.forEach(input => {
                // Validate on input
                input.addEventListener('input', function() {
                    validateField(this);
                    checkFormValidity();
                });

                // Validate on blur
                input.addEventListener('blur', function() {
                    validateField(this);
                    checkFormValidity();
                });

                // Validate on change (for select and file inputs)
                input.addEventListener('change', function() {
                    validateField(this);
                    checkFormValidity();
                });
            });

            // Terms checkbox listener
            termsCheckbox.addEventListener('change', checkFormValidity);

            // Validate optional fields (business email and logo)
            const businessEmailInput = document.querySelector('input[name="business_email"]');
            if (businessEmailInput) {
                businessEmailInput.addEventListener('input', function() {
                    validateField(this);
                    checkFormValidity();
                });
                businessEmailInput.addEventListener('blur', function() {
                    validateField(this);
                    checkFormValidity();
                });
            }

            // Validate business logo if uploaded
            if (logoInput) {
                logoInput.addEventListener('change', function() {
                    checkFormValidity();
                });
            }

            // Initial check
            checkFormValidity();

            // Validate individual field
            function validateField(field) {
                // For optional fields that are empty, skip validation but remove classes
                if (!field.hasAttribute('required') && field.value.trim() === '') {
                    field.classList.remove('is-invalid', 'is-valid');
                    return true;
                }

                let isValid = true;

                // Check basic HTML5 validity
                if (!field.checkValidity()) {
                    isValid = false;
                }

                // Additional custom validations
                if (field.name === 'owner_name' || field.name === 'business_name') {
                    if (field.value.trim().length < 3) {
                        isValid = false;
                    }
                }

                if (field.name === 'owner_phone' || field.name === 'business_phone') {
                    const phonePattern = /^(08|62)\d{8,12}$/;
                    if (!phonePattern.test(field.value.trim())) {
                        isValid = false;
                    }
                }

                if (field.name === 'ktp_number') {
                    if (field.value.length !== 16 || !/^\d{16}$/.test(field.value)) {
                        isValid = false;
                    }
                }

                if (field.name === 'business_address') {
                    if (field.value.trim().length < 10) {
                        isValid = false;
                    }
                }

                // Validate business email if filled (optional but must be valid if filled)
                if (field.name === 'business_email' && field.value.trim() !== '') {
                    const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
                    if (!emailPattern.test(field.value.trim())) {
                        isValid = false;
                    }
                }

                // Apply validation classes
                if (isValid) {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                } else {
                    field.classList.remove('is-valid');
                    field.classList.add('is-invalid');
                }

                return isValid;
            }

            // Check overall form validity
            function checkFormValidity() {
                let isFormValid = true;

                // Check all required fields
                requiredInputs.forEach(input => {
                    if (!input.checkValidity()) {
                        isFormValid = false;
                    }

                    // Additional validations
                    if (input.name === 'owner_name' || input.name === 'business_name') {
                        if (input.value.trim().length < 3) {
                            isFormValid = false;
                        }
                    }

                    if (input.name === 'owner_phone' || input.name === 'business_phone') {
                        const phonePattern = /^(08|62)\d{8,12}$/;
                        if (!phonePattern.test(input.value.trim())) {
                            isFormValid = false;
                        }
                    }

                    if (input.name === 'ktp_number') {
                        if (input.value.length !== 16 || !/^\d{16}$/.test(input.value)) {
                            isFormValid = false;
                        }
                    }

                    if (input.name === 'business_address') {
                        if (input.value.trim().length < 10) {
                            isFormValid = false;
                        }
                    }
                });

                // Check optional business email if filled
                if (businessEmailInput && businessEmailInput.value.trim() !== '') {
                    const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
                    if (!emailPattern.test(businessEmailInput.value.trim())) {
                        isFormValid = false;
                    }
                }

                // Check optional business logo if uploaded
                if (logoInput && logoInput.files.length > 0) {
                    const file = logoInput.files[0];
                    // Check file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        isFormValid = false;
                    }
                    // Check file type
                    if (!file.type.match('image/(jpeg|jpg|png)')) {
                        isFormValid = false;
                    }
                }

                // Check required KTP photo if no previous verification
                const hasOldKtp = document.getElementById('old_ktp_preview');
                if (!hasOldKtp && ktpInput && ktpInput.files.length === 0) {
                    isFormValid = false;
                }

                // Check KTP photo if uploaded
                if (ktpInput && ktpInput.files.length > 0) {
                    const file = ktpInput.files[0];
                    // Check file size (max 1MB)
                    if (file.size > 1 * 1024 * 1024) {
                        isFormValid = false;
                    }
                    // Check file type
                    if (!file.type.match('image/(jpeg|jpg|png)')) {
                        isFormValid = false;
                    }
                }

                // Check terms checkbox
                if (!termsCheckbox.checked) {
                    isFormValid = false;
                }

                // Enable/disable submit button
                submitBtn.disabled = !isFormValid;

                return isFormValid;
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Final validation before submit
                let isFormCompletelyValid = true;
                
                requiredInputs.forEach(input => {
                    if (!validateField(input)) {
                        isFormCompletelyValid = false;
                    }
                });

                // Validate optional business email if filled
                if (businessEmailInput && businessEmailInput.value.trim() !== '') {
                    if (!validateField(businessEmailInput)) {
                        isFormCompletelyValid = false;
                    }
                }

                // Validate business logo if uploaded
                if (logoInput && logoInput.files.length > 0) {
                    const file = logoInput.files[0];
                    if (file.size > 2 * 1024 * 1024 || !file.type.match('image/(jpeg|jpg|png)')) {
                        isFormCompletelyValid = false;
                        alert('Logo bisnis tidak valid. Pastikan format JPG/PNG dan ukuran max 2MB.');
                        return;
                    }
                }

                // Validate KTP photo
                if (ktpInput && ktpInput.files.length > 0) {
                    const file = ktpInput.files[0];
                    if (file.size > 1 * 1024 * 1024 || !file.type.match('image/(jpeg|jpg|png)')) {
                        isFormCompletelyValid = false;
                        alert('Foto KTP tidak valid. Pastikan format JPG/PNG dan ukuran max 1MB.');
                        return;
                    }
                }

                if (!termsCheckbox.checked) {
                    isFormCompletelyValid = false;
                }

                if (!isFormCompletelyValid) {
                    alert('{{ __('messages.owner.verification.err_form_invalid') }}');
                    // Focus on first invalid field
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                    }
                    return;
                }

                // SweetAlert confirmation (if you have SweetAlert2)
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: "{{ __('messages.owner.verification.swal_confirm_title') }}",
                        text: "{{ __('messages.owner.verification.swal_confirm_text') }}",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: "{{ __('messages.owner.verification.swal_confirm_btn') }}",
                        cancelButtonText: "{{ __('messages.owner.verification.swal_cancel_btn') }}",
                        confirmButtonColor: '#8c1000',
                        cancelButtonColor: '#6c757d',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __('messages.owner.verification.btn_loading') }}';
                            form.submit();
                        }
                    });
                } else {
                    // Fallback without SweetAlert
                    if (confirm("{{ __('messages.owner.verification.swal_confirm_text') }}")) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __('messages.owner.verification.btn_loading') }}';
                        form.submit();
                    }
                }
            });

            // KTP number - only digits
            const ktpNumberInput = document.querySelector('input[name="ktp_number"]');
            if (ktpNumberInput) {
                ktpNumberInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/\D/g, '').substring(0, 16);
                    validateField(this);
                    checkFormValidity();
                });
            }

            // Phone numbers - only digits
            document.querySelectorAll('input[type="tel"]').forEach(function(input) {
                input.addEventListener('input', function(e) {
                    this.value = this.value.replace(/\D/g, '');
                    validateField(this);
                    checkFormValidity();
                });
            });
        });
    </script>
@endpush