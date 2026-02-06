@extends('layouts.owner')
@section('title', __('messages.owner.payment_methods.edit_payment_method') ?? 'Edit Payment Method')
@section('page_title', __('messages.owner.payment_methods.edit_payment_method') ?? 'Edit Payment Method')

@section('content')
<div class="modern-container">
  <div class="container-modern">
    <div class="page-header">
      <div class="header-content">
        <h1 class="page-title">{{ __('messages.owner.payment_methods.edit_payment_method') ?? 'Edit Payment Method' }}</h1>
        <p class="page-subtitle">{{ __('messages.owner.payment_methods.update_payment_method_subtitle') ?? '' }}</p>
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

    <div class="modern-card">
      <form action="{{ route('owner.user-owner.payment-methods.update', $paymentMethod) }}"
            method="POST" enctype="multipart/form-data" id="paymentMethodForm">
        @csrf
        @method('PUT')

        {{-- untuk hapus qris image --}}
        <input type="hidden" name="remove_qris" id="remove_qris" value="0">

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
                      class="form-control-modern @error('payment_type') is-invalid @enderror" required>
                      <option value="">-- {{ __('messages.owner.payment_methods.select_payment_type') }} --</option>
                      <option value="manual_tf" {{ old('payment_type', $paymentMethod->payment_type) === 'manual_tf' ? 'selected' : '' }}>
                        {{ __('messages.owner.payment_methods.type_transfer') }}
                      </option>
                      <option value="manual_ewallet" {{ old('payment_type', $paymentMethod->payment_type) === 'manual_ewallet' ? 'selected' : '' }}>
                        {{ __('messages.owner.payment_methods.type_ewallet') }}
                      </option>
                      <option value="manual_qris" {{ old('payment_type', $paymentMethod->payment_type) === 'manual_qris' ? 'selected' : '' }}>
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
                      value="{{ old('provider_name', $paymentMethod->provider_name) }}"
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
                      value="{{ old('provider_account_name', $paymentMethod->provider_account_name) }}"
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
                      value="{{ old('provider_account_no', $paymentMethod->provider_account_no) }}"
                      placeholder="{{ __('messages.owner.payment_methods.provider_account_no_placeholder') }}">
                    @error('provider_account_no')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                {{-- QRIS IMAGE --}}
                <div class="qris-picture-wrapper" id="qrisImageField">
                  <div class="qris-picture-container" id="qrisPictureContainer">
                    <div class="upload-placeholder" id="uploadPlaceholder" style="{{ $paymentMethod->qris_image_url ? 'display:none;' : '' }}">
                      <span class="material-symbols-outlined">image</span>
                      <span class="upload-text">{{ __('messages.owner.products.categories.upload_text') }}</span>
                    </div>

                    <img id="imagePreview"
                         class="profile-preview"
                         alt="{{ __('messages.owner.products.categories.image_preview_alt') }}"
                         style="{{ $paymentMethod->qris_image_url ? 'display:block;' : 'display:none;' }}"
                         src="{{ $paymentMethod->qris_image_url ? asset('storage/' . $paymentMethod->qris_image_url) : '' }}">

                    <button type="button" id="removeImageBtn"
                            class="btn-remove btn-remove-top"
                            style="{{ $paymentMethod->qris_image_url ? 'display:block;' : 'display:none;' }}">
                      <span class="material-symbols-outlined">close</span>
                    </button>
                  </div>

                  <input type="file" name="images" id="images" accept="image/*" style="display: none;">
                  <small class="text-muted d-block text-center mt-2">{{ __('messages.owner.products.categories.image_hint') }}</small>
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
                      <span id="isActiveLabel"
                            class="status-badge {{ old('is_active', $paymentMethod->is_active) ? 'status-active' : 'status-inactive' }}">
                        {{ old('is_active', $paymentMethod->is_active)
                            ? __('messages.owner.payment_methods.enabled')
                            : __('messages.owner.payment_methods.disabled') }}
                      </span>

                      <label class="switch-modern mb-0 ml-2">
                        <input type="checkbox"
                              name="is_active"
                              value="1"
                              id="isActiveSwitch"
                              {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}>
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
                      placeholder="{{ __('messages.owner.payment_methods.additional_info_placeholder') }}">{{ old('additional_info', $paymentMethod->additional_info) }}</textarea>
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
            {{ __('messages.owner.payment_methods.update_payment_method') ?? 'Update' }}
          </button>
        </div>

      </form>
    </div>
  </div>

  {{-- Modal Crop --}}
  <div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content modern-modal">
        <div class="modal-header modern-modal-header">
          <h5 class="modal-title">
            <span class="material-symbols-outlined">crop</span>
            {{ __('messages.owner.products.categories.crop_modal_title') }}
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
              <small>{{ __('messages.owner.products.categories.crop_instruction') }}</small>
            </div>
          </div>
          <div class="img-container-crop">
            <img id="imageToCrop" style="max-width: 100%;" alt="Image to crop">
          </div>
        </div>
        <div class="modal-footer modern-modal-footer">
          <button type="button" class="btn-cancel-modern" data-dismiss="modal">
            <span class="material-symbols-outlined">close</span>
            {{ __('messages.owner.products.categories.cancel') }}
          </button>
          <button type="button" id="cropBtn" class="btn-submit-modern">
            <span class="material-symbols-outlined">check</span>
            {{ __('messages.owner.products.categories.crop_save') }}
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
      background-color: #d1d5db;
      border-radius: 34px;
      top: 0; left: 0; right: 0; bottom: 0;
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
      background-color: #ef4444;
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
      background-color: #fee2e2;
      color: #b91c1c;
  }
  .status-inactive {
      background-color: #e5e7eb;
      color: #374151;
  }
</style>

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Cropper init (tetap free aspect)
  ImageCropper.init({
    id: 'qris',
    inputId: 'images',
    previewId: 'imagePreview',
    modalId: 'cropModal',
    imageToCropId: 'imageToCrop',
    cropBtnId: 'cropBtn',
    containerId: 'qrisPictureContainer',
    aspectRatio: null,

    viewMode: 1,
    dragMode: 'crop',
    autoCrop: true,
    autoCropArea: 1,
    cropBoxMovable: true,
    cropBoxResizable: true,
    responsive: true,
    background: false
  });

  // Remove button: set remove_qris = 1 dan reset preview
  const removeBtn = document.getElementById('removeImageBtn');
  const removeFlag = document.getElementById('remove_qris');
  const imagePreview = document.getElementById('imagePreview');
  const uploadPlaceholder = document.getElementById('uploadPlaceholder');
  const imageInput = document.getElementById('images');

  if (removeBtn) {
    removeBtn.addEventListener('click', function () {
      // tandai untuk dihapus saat submit
      removeFlag.value = '1';

      // reset preview
      imagePreview.src = '';
      imagePreview.style.display = 'none';
      uploadPlaceholder.style.display = 'flex';
      removeBtn.style.display = 'none';
      imageInput.value = '';
    });
  }

  // Toggle field berdasarkan payment type (sama seperti create)
  const paymentType = document.getElementById('payment_type');
  const providerFields = document.getElementById('providerFields');
  const accountNoField = document.getElementById('accountNoField');
  const additionalInfo = document.getElementById('additionalInfoField');
  const qrisImageField = document.getElementById('qrisImageField');

  const accountNoInput = document.getElementById('provider_account_no');

  function resetAll() {
    providerFields.style.display = 'none';
    accountNoField.style.display = 'none';
    additionalInfo.style.display = 'none';
    qrisImageField.style.display = 'none';

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
      accountNoInput.setAttribute('required', 'required');

      // jika pindah dari qris ke non-qris -> set flag remove (biar backend hapus)
      removeFlag.value = '1';
    }

    if (value === 'manual_qris') {
      providerFields.style.display = 'block';
      additionalInfo.style.display = 'block';
      qrisImageField.style.display = 'block';
      imageInput.setAttribute('required', 'required');

      // jangan auto remove kalau memang mau keep existing
      // (kalau sudah ada gambar lama, user tidak wajib upload ulang)
      imageInput.removeAttribute('required');
    }
  }

  handlePaymentTypeChange();
  paymentType.addEventListener('change', handlePaymentTypeChange);
});
</script>
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
