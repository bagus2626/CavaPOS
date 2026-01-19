@extends('layouts.partner')

@section('title', __('messages.partner.outlet.table_management.tables.create_tables'))
@section('page_title', __('messages.partner.outlet.table_management.tables.create_new_table'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <!-- Header Section -->
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.partner.outlet.table_management.tables.create_new_table') }}</h1>
                    <p class="page-subtitle">{{ __('messages.partner.outlet.table_management.tables.add_new_table') }}</p>
                </div>
                <a href="{{ route('partner.store.tables.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.partner.outlet.table_management.tables.back_to_tables') }}
                </a>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                    </div>
                    <div class="alert-content">
                        <strong>{{ __('messages.partner.outlet.table_management.tables.re_check_input') }}</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
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
                <form action="{{ route('partner.store.tables.store') }}" method="POST" enctype="multipart/form-data"
                    id="tableForm">
                    @csrf
                    <div class="card-body-modern">

                        <!-- Table Image & Basic Info Section -->
                        <div class="profile-section">
                            <!-- Table Image Upload -->
                            <div class="profile-picture-wrapper">
                                <div class="profile-picture-container" id="tableImageContainer">
                                    <div class="upload-placeholder" id="uploadPlaceholder">
                                        <span class="material-symbols-outlined">image</span>
                                        <span class="upload-text">Upload</span>
                                    </div>
                                    <img id="imagePreview" class="profile-preview" alt="Table Preview">
                                </div>
                                <input type="file" name="images" id="tableImage" accept="image/*" style="display: none;">
                                <small class="text-muted d-block text-center mt-2">JPG, PNG, WEBP. Max 2 MB</small>
                                @error('images')
                                    <div class="text-danger text-center mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Basic Table Information -->
                            <div class="personal-info-fields">
                                <div class="section-header">
                                    <div class="section-icon section-icon-red">
                                        <span class="material-symbols-outlined">table_restaurant</span>
                                    </div>
                                    <h3 class="section-title">
                                        {{ __('messages.partner.outlet.table_management.tables.table_information') }}
                                    </h3>
                                </div>
                                <div class="row g-4">
                                    <!-- Table No -->
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.partner.outlet.table_management.tables.table_no') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="table_no" id="table_no"
                                                class="form-control-modern @error('table_no') is-invalid @enderror"
                                                value="{{ old('table_no') }}"
                                                placeholder="{{ __('messages.partner.outlet.table_management.tables.enter_table_number') }}"
                                                required>
                                            @error('table_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.partner.outlet.table_management.tables.status') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="select-wrapper">
                                                <select name="status" id="status"
                                                    class="form-control-modern @error('status') is-invalid @enderror" required>
                                                    <option value="">
                                                        {{ __('messages.partner.outlet.table_management.tables.choose_status') }}
                                                    </option>
                                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>
                                                        {{ __('messages.partner.outlet.table_management.tables.available') }}
                                                    </option>
                                                    <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>
                                                        {{ __('messages.partner.outlet.table_management.tables.occupied') }}
                                                    </option>
                                                    <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>
                                                        {{ __('messages.partner.outlet.table_management.tables.reserved') }}
                                                    </option>
                                                    <option value="not_available" {{ old('status') == 'not_available' ? 'selected' : '' }}>
                                                        {{ __('messages.partner.outlet.table_management.tables.not_available') }}
                                                    </option>
                                                </select>
                                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                                            </div>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Table Class with Toggle -->
                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.partner.outlet.table_management.tables.class_type') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <!-- SELECT MODE (default) -->
                                            <div id="select_mode">
                                                <div class="select-wrapper">
                                                    <select name="table_class" id="table_class"
                                                        class="form-control-modern @error('table_class') is-invalid @enderror" required>
                                                        <option value="">
                                                            {{ __('messages.partner.outlet.table_management.tables.placeholder_1') }}
                                                        </option>
                                                        @if (!empty($table_classes) && $table_classes->count() > 0)
                                                            @foreach ($table_classes as $class)
                                                                <option value="{{ $class }}" {{ old('table_class') == $class ? 'selected' : '' }}>
                                                                    {{ $class }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                                                </div>

                                                @error('table_class')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror

                                                <!-- Add New Class Button -->
                                                <button type="button" class="btn-modern btn-primary-modern btn-sm-modern mt-3" id="btn_add_new_class">
                                                    <span class="material-symbols-outlined">add_circle</span>
                                                    <span>{{ __('messages.partner.outlet.table_management.tables.add_class') }}</span>
                                                </button>
                                            </div>

                                            <!-- INPUT MODE (hidden by default) -->
                                            <div id="input_mode" style="display: none;">
                                                <input type="text" name="new_table_class" id="new_table_class"
                                                    class="form-control-modern"
                                                    placeholder="{{ __('messages.partner.outlet.table_management.tables.enter_new_table_class') }}">
                                                
                                                <button type="button" class="btn-modern btn-secondary-modern btn-sm-modern mt-3" id="cancel_new_class">
                                                    {{ __('messages.partner.outlet.table_management.tables.cancel') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="col-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.partner.outlet.table_management.tables.description') }}
                                            </label>
                                            <textarea name="description" id="description"
                                                class="form-control-modern @error('description') is-invalid @enderror"
                                                rows="4"
                                                placeholder="{{ __('messages.partner.outlet.table_management.tables.enter_table_description') }}">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Footer -->
                    <div class="card-footer-modern">
                        <a href="{{ route('partner.store.tables.index') }}" class="btn-cancel-modern">
                            {{ __('messages.partner.outlet.table_management.tables.cancel') }}
                        </a>
                        <button type="submit" class="btn-submit-modern">
                            {{ __('messages.partner.outlet.table_management.tables.save') }}
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
                            Crop Table Image
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {

    // Initialize Table Image Cropper (1:1 Square)
    try {
        ImageCropper.init({
            id: 'table',
            inputId: 'tableImage',
            previewId: 'imagePreview',
            modalId: 'cropModal',
            imageToCropId: 'imageToCrop',
            cropBtnId: 'cropBtn',
            containerId: 'tableImageContainer',
            aspectRatio: 1,
            outputWidth: 800,
            outputHeight: 800
        });
    } catch (error) {
        console.error('ImageCropper error:', error);
    }

    // ==== Table Class Toggle System ====
    const selectMode = document.getElementById('select_mode');
    const inputMode = document.getElementById('input_mode');
    const selectClass = document.getElementById('table_class');
    const newClassInput = document.getElementById('new_table_class');
    const btnAddNewClass = document.getElementById('btn_add_new_class');
    const cancelBtn = document.getElementById('cancel_new_class');
    const form = document.getElementById('tableForm');

    // Safety check
    if (!btnAddNewClass || !selectMode || !inputMode || !selectClass || !newClassInput) {
        return;
    }

    let isInputMode = false;

    function switchToInputMode() {
        isInputMode = true;
        selectMode.style.display = 'none';
        inputMode.style.display = 'block';
        selectClass.required = false;
        newClassInput.required = true;

        inputMode.style.opacity = '0';
        inputMode.style.transition = 'opacity 0.2s ease';
        
        setTimeout(() => {
            newClassInput.focus();
            inputMode.style.opacity = '1';
        }, 50);
    }

    function switchToSelectMode() {
        isInputMode = false;
        inputMode.style.display = 'none';
        selectMode.style.display = 'block';
        selectClass.required = true;
        newClassInput.required = false;
        newClassInput.value = '';
        selectClass.value = '';
    }

    // Add event listeners
    btnAddNewClass.addEventListener('click', function(e) {
        e.preventDefault();
        switchToInputMode();
    });

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            switchToSelectMode();
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            if (isInputMode) {
                const newClassName = newClassInput.value.trim();

                if (!newClassName) {
                    e.preventDefault();
                    alert('Silakan masukkan nama kelas baru atau klik "Batal".');
                    newClassInput.focus();
                    return false;
                }

                // Add new option to select before submit
                const newOption = document.createElement('option');
                newOption.value = newClassName;
                newOption.text = newClassName;
                newOption.selected = true;
                selectClass.appendChild(newOption);

                selectClass.required = true;
                newClassInput.required = false;
            }
        });
    }

    // Handle old input on page reload
    const initialValue = selectClass.value;
    if (!initialValue && '{{ old('new_table_class') }}') {
        switchToInputMode();
        newClassInput.value = '{{ old('new_table_class') }}';
    }
});
    </script>
@endpush