@extends('layouts.owner')
@section('title', __('messages.owner.payment_methods.add_payment_method'))
@section('page_title', __('messages.owner.payment_methods.add_payment_method'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.payment_methods.add_payment_method') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.payment_methods.create_payment_method_subtitle') }}</p>
                </div>
                <a href="{{ route('owner.user-owner.payment-methods.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.payment_methods.back') }}
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                    </div>
                    <div class="alert-content">
                        <strong>{{ __('messages.owner.payment_methods.alert_error') }}:</strong>
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

            <div class="modern-card">
                <form action="{{ route('owner.user-owner.payment-methods.store') }}" method="POST" enctype="multipart/form-data"
                    id="paymentMethodForm">
                    @csrf
                    <div class="card-body-modern">

                        <div class="profile-section">
                            

                            <div class="personal-info-fields">
                                <div class="section-header">
                                    <div class="section-icon section-icon-red">
                                        <span class="material-symbols-outlined">payment</span>
                                    </div>
                                    <h3 class="section-title">{{ __('messages.owner.payment_methods.info_title') }}</h3>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.payment_methods.payment_type') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select name="payment_type" id="payment_type"
                                                class="form-control-modern @error('payment_type') is-invalid @enderror"
                                                required>
                                                <option value="">
                                                    -- {{ __('messages.owner.payment_methods.select_payment_type') }} --
                                                </option>
                                                <option value="manual_tf" {{ old('payment_type') === 'manual_tf' ? 'selected' : '' }}>
                                                    {{ __('messages.owner.payment_methods.type_transfer') }}
                                                </option>
                                                <option value="manual_ewallet" {{ old('payment_type') === 'manual_ewallet' ? 'selected' : '' }}>
                                                    {{ __('messages.owner.payment_methods.type_ewallet') }}
                                                </option>
                                                <option value="manual_qris" {{ old('payment_type') === 'manual_qris' ? 'selected' : '' }}>
                                                    {{ __('messages.owner.payment_methods.type_qris') }}
                                                </option>
                                            </select>

                                            @error('payment_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.payment_methods.provider_name') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="provider_name" id="provider_name"
                                                class="form-control-modern @error('provider_name') is-invalid @enderror"
                                                value="{{ old('provider_name') }}"
                                                placeholder="{{ __('messages.owner.payment_methods.provider_name_placeholder') }}" required>
                                            @error('provider_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="providerFields">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.payment_methods.provider_account_name') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="provider_account_name" id="provider_account_name"
                                                class="form-control-modern @error('provider_account_name') is-invalid @enderror"
                                                value="{{ old('provider_account_name') }}"
                                                placeholder="{{ __('messages.owner.payment_methods.provider_account_name_placeholder') }}" required>
                                            @error('provider_account_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="accountNoField">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.payment_methods.provider_account_no') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="provider_account_no" id="provider_account_no"
                                                class="form-control-modern @error('provider_account_no') is-invalid @enderror"
                                                value="{{ old('provider_account_no') }}"
                                                placeholder="{{ __('messages.owner.payment_methods.provider_account_no_placeholder') }}">
                                            @error('provider_account_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="qris-picture-wrapper" id="qrisImageField">
                                        <div class="qris-picture-container" id="qrisPictureContainer">
                                            <div class="upload-placeholder" id="uploadPlaceholder">
                                                <span class="material-symbols-outlined">image</span>
                                                <span class="upload-text">{{ __('messages.owner.payment_methods.upload_text') }}</span>
                                            </div>
                                            <img id="imagePreview" class="profile-preview" alt="{{ __('messages.owner.payment_methods.image_preview_alt') }}">
                                            <button type="button" id="removeImageBtn" class="btn-remove btn-remove-top" style="display: none;">
                                                <span class="material-symbols-outlined">close</span>
                                            </button>
                                        </div>
                                        <input type="file" name="images" id="images" accept="image/*" style="display: none;">
                                        <small class="text-muted d-block text-center mt-2">{{ __('messages.owner.payment_methods.image_hint') }}</small>
                                        @error('images')
                                            <div class="text-danger text-center mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12" id="isActiveField">
                                        <div class="form-group-modern d-flex align-items-center justify-content-between">
                                            <label class="form-label-modern mb-0">
                                                {{ __('messages.owner.payment_methods.status') }}
                                            </label>

                                            {{-- hidden input: kalau switch OFF, tetap kirim 0 --}}
                                            <input type="hidden" name="is_active" value="0">

                                            <div class="d-flex align-items-center gap-3 ms-auto">
                                                <span id="isActiveLabel" class="status-badge status-active">
                                                    {{ __('messages.owner.payment_methods.enabled') }}
                                                </span>

                                                <label class="switch-modern mb-0 ml-2">
                                                    <input type="checkbox" name="is_active" value="1" checked id="isActiveSwitch">
                                                    <span class="slider-modern"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="additionalInfoField">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.payment_methods.additional_info') }}
                                            </label>
                                            <textarea name="additional_info" id="additional_info"
                                                class="form-control-modern @error('additional_info') is-invalid @enderror"
                                                rows="4"
                                                placeholder="{{ __('messages.owner.payment_methods.additional_info_placeholder') }}">{{ old('additional_info') }}</textarea>
                                            @error('additional_info')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer-modern">
                        <a href="{{ route('owner.user-owner.payment-methods.index') }}" class="btn-cancel-modern">
                            {{ __('messages.owner.payment_methods.cancel') }}
                        </a>
                        <button type="submit" class="btn-submit-modern">
                            {{ __('messages.owner.payment_methods.create_payment_method') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content modern-modal">
                    <div class="modal-header modern-modal-header">
                        <h5 class="modal-title">
                            <span class="material-symbols-outlined">crop</span>
                            {{ __('messages.owner.payment_methods.crop_modal_title') }}
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
                                <small>{{ __('messages.owner.payment_methods.crop_instruction') }}</small>
                            </div>
                        </div>
                        <div class="img-container-crop">
                            <img id="imageToCrop" style="max-width: 100%;" alt="Image to crop">
                        </div>
                    </div>
                    <div class="modal-footer modern-modal-footer">
                        <button type="button" class="btn-cancel-modern" data-dismiss="modal">
                            <span class="material-symbols-outlined">close</span>
                            {{ __('messages.owner.payment_methods.cancel') }}
                        </button>
                        <button type="button" id="cropBtn" class="btn-submit-modern">
                            <span class="material-symbols-outlined">check</span>
                            {{ __('messages.owner.payment_methods.crop_save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    /* untuk slider is_active */
    .switch-modern {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }

    .switch-modern input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider-modern {
        position: absolute;
        cursor: pointer;
        background-color: #d1d5db; /* gray */
        border-radius: 34px;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        transition: 0.3s;
    }

    .slider-modern::before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: 0.3s;
    }

    .switch-modern input:checked + .slider-modern {
        background-color: #ef4444; /* merah selaras owner */
    }

    .switch-modern input:checked + .slider-modern::before {
        transform: translateX(24px);
    }
        /* status label */
    .status-badge {
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .status-active {
        background-color: #fee2e2; /* red soft */
        color: #b91c1c;
    }

    .status-inactive {
        background-color: #e5e7eb; /* gray */
        color: #374151;
    }
    /* end of slider is_active */
</style>

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>


        document.addEventListener('DOMContentLoaded', function () {

            // Initialize Payment Method Image Cropper (1:1 Square)
            ImageCropper.init({
                id: 'category',
                inputId: 'images',
                previewId: 'imagePreview',
                modalId: 'cropModal',
                imageToCropId: 'imageToCrop',
                cropBtnId: 'cropBtn',
                containerId: 'qrisPictureContainer',

                aspectRatio: null,     // 🔥 FREE ASPECT RATIO

                viewMode: 1,
                dragMode: 'crop',
                autoCrop: true,
                autoCropArea: 1,
                cropBoxMovable: true,
                cropBoxResizable: true,
                responsive: true,
                background: false
            });

            // Initialize Remove Image Handler
            ImageRemoveHandler.init({
                removeBtnId: 'removeImageBtn',
                imageInputId: 'images',
                imagePreviewId: 'imagePreview',
                uploadPlaceholderId: 'uploadPlaceholder',
                confirmRemove: false // No confirmation for create page
            });

            // ==== Form Validation ====
            const form = document.getElementById('paymentMethodForm');
            const payment_type = document.getElementById('payment_type');

            if (form && payment_type) {
                form.addEventListener('submit', function (e) {
                    if (payment_type.value.trim() === '') {
                        e.preventDefault();
                        alert('{{ __('messages.owner.payment_methods.js_payment_type_required') }}');
                        payment_type.focus();
                        return false;
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const paymentType        = document.getElementById('payment_type');

            const providerFields     = document.getElementById('providerFields');
            const accountNoField     = document.getElementById('accountNoField');
            const additionalInfo     = document.getElementById('additionalInfoField');
            const qrisImageField     = document.getElementById('qrisImageField');
            const isActiveField      = document.getElementById('isActiveField');

            const accountNoInput     = document.getElementById('provider_account_no');
            const imageInput         = document.getElementById('images');

            if (isActiveField) {
                isActiveField.style.display = 'block';
            }

            function resetAll() {
                providerFields.style.display = 'none';
                accountNoField.style.display = 'none';
                additionalInfo.style.display = 'none';
                qrisImageField.style.display = 'none';

                // 🔥 remove required
                accountNoInput.removeAttribute('required');
                imageInput.removeAttribute('required');
            }

            function handlePaymentTypeChange() {
                resetAll();

                const value = paymentType.value;

                if (value === 'manual_tf' || value === 'manual_ewallet') {
                    providerFields.style.display = 'block';
                    accountNoField.style.display = 'block';
                    additionalInfo.style.display = 'block';

                    // 🔥 require account no
                    accountNoInput.setAttribute('required', 'required');
                }

                if (value === 'manual_qris') {
                    providerFields.style.display = 'block';
                    additionalInfo.style.display = 'block';
                    qrisImageField.style.display = 'block';

                    // 🔥 require image
                    imageInput.setAttribute('required', 'required');
                }
            }

            handlePaymentTypeChange();
            paymentType.addEventListener('change', handlePaymentTypeChange);
        });
    </script>
    
    {{-- untuk slider is_active --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const switchInput = document.getElementById('isActiveSwitch');
            const label       = document.getElementById('isActiveLabel');

            if (!switchInput || !label) return;

            function updateLabel() {
                if (switchInput.checked) {
                    label.textContent = "{{ __('messages.owner.payment_methods.enabled') }}";
                    label.classList.remove('status-inactive');
                    label.classList.add('status-active');
                } else {
                    label.textContent = "{{ __('messages.owner.payment_methods.disabled') }}";
                    label.classList.remove('status-active');
                    label.classList.add('status-inactive');
                }
            }

            updateLabel(); // initial
            switchInput.addEventListener('change', updateLabel);
        });
        </script>

@endpush