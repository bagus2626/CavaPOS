@extends('layouts.owner')

@section('title', 'Edit Table')
@section('page_title', 'Edit Table')

@section('content')
<div class="modern-container">
    <div class="container-modern">
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">Edit Table</h1>
                <p class="page-subtitle">Update table information</p>
            </div>
            <a href="{{ route('owner.user-owner.tables.index') }}" class="back-button">
                <span class="material-symbols-outlined">arrow_back</span> Back to Tables
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-modern">
                <div class="alert-icon"><span class="material-symbols-outlined">error</span></div>
                <div class="alert-content">
                    <strong>Please re-check your input:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="modern-card">
            <form action="{{ route('owner.user-owner.tables.update', $table->id) }}" method="POST"
                enctype="multipart/form-data" id="tableEditForm">
                @csrf
                @method('PUT')

                <div class="card-body-modern">
                    <div class="profile-section">

                        {{-- Image Upload --}}
                        <div class="profile-picture-wrapper">
                            @php
                                $existingImage = is_array($table->images) ? ($table->images[0]['path'] ?? null) : null;
                            @endphp
                            <div class="profile-picture-container" id="tableImageContainer">
                                <div class="upload-placeholder" id="uploadPlaceholder"
                                    style="{{ $existingImage ? 'display:none;' : '' }}">
                                    <span class="material-symbols-outlined">image</span>
                                    <span class="upload-text">Upload</span>
                                </div>
                                <img id="imagePreview"
                                    class="profile-preview {{ $existingImage ? 'active' : '' }}"
                                    src="{{ $existingImage ? asset($existingImage) : '' }}"
                                    alt="Table Preview">
                            </div>
                            <input type="file" name="images" id="tableImage" accept="image/*" style="display:none;">
                            <input type="hidden" name="keep_existing_image" id="keep_existing_image" value="1">
                            <small class="text-muted d-block text-center mt-2">JPG, PNG, WEBP. Max 2 MB</small>
                            @error('images')
                                <div class="text-danger text-center mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Form Fields --}}
                        <div class="personal-info-fields">
                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">table_restaurant</span>
                                </div>
                                <h3 class="section-title">Table Information</h3>
                            </div>

                            <div class="row g-4">

                                {{-- Table No --}}
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            Table No <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="table_no"
                                            class="form-control-modern @error('table_no') is-invalid @enderror"
                                            value="{{ old('table_no', $table->table_no) }}" required>
                                        @error('table_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            Status <span class="text-danger">*</span>
                                        </label>
                                        <div class="select-wrapper">
                                            <select name="status"
                                                class="form-control-modern @error('status') is-invalid @enderror"
                                                required>
                                                <option value="">Choose Status</option>
                                                <option value="available"
                                                    {{ old('status', $table->status) == 'available' ? 'selected' : '' }}>
                                                    Available
                                                </option>
                                                <option value="not_available"
                                                    {{ old('status', $table->status) == 'not_available' ? 'selected' : '' }}>
                                                    Not Available
                                                </option>
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Outlet (read-only info) --}}
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Outlet</label>
                                        <input type="text" class="form-control-modern"
                                            value="{{ $table->partner->name ?? '-' }}" readonly
                                            style="background:#f9fafb; cursor:not-allowed;">
                                        <small class="text-muted">Outlet cannot be changed after creation.</small>
                                    </div>
                                </div>

                                {{-- Class Type --}}
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            Class Type <span class="text-danger">*</span>
                                        </label>

                                        <div id="select_mode">
                                            <div class="select-wrapper">
                                                <select name="table_class" id="table_class"
                                                    class="form-control-modern @error('table_class') is-invalid @enderror"
                                                    required>
                                                    <option value="">Choose class</option>
                                                    @foreach ($table_classes as $class)
                                                        <option value="{{ $class }}"
                                                            {{ old('table_class', $table->table_class) == $class ? 'selected' : '' }}>
                                                            {{ $class }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                                            </div>
                                            @error('table_class')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <button type="button"
                                                class="btn-modern btn-primary-modern btn-sm-modern mt-3"
                                                id="btn_add_new_class">
                                                <span class="material-symbols-outlined">add_circle</span>
                                                Add New Class
                                            </button>
                                        </div>

                                        <div id="input_mode" style="display:none;">
                                            <input type="text" name="new_table_class" id="new_table_class"
                                                class="form-control-modern" placeholder="Enter new class name">
                                            <button type="button"
                                                class="btn-modern btn-secondary-modern btn-sm-modern mt-3"
                                                id="cancel_new_class">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="col-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Description</label>
                                        <textarea name="description"
                                            class="form-control-modern @error('description') is-invalid @enderror"
                                            rows="4"
                                            placeholder="Optional table description">{{ old('description', $table->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer-modern">
                    <a href="{{ route('owner.user-owner.tables.index') }}" class="btn-cancel-modern">Cancel</a>
                    <button type="submit" class="btn-submit-modern">Update Table</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Image Preview ────────────────────────────────────────────────────────
    const imageContainer    = document.getElementById('tableImageContainer');
    const imageInput        = document.getElementById('tableImage');
    const imagePreview      = document.getElementById('imagePreview');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const keepInput         = document.getElementById('keep_existing_image');

    if (imageContainer && imageInput) {
        imageContainer.addEventListener('click', () => imageInput.click());

        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                alert('Max file size is 2MB.');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = ev => {
                imagePreview.src = ev.target.result;
                imagePreview.classList.add('active');
                uploadPlaceholder.style.display = 'none';
                if (keepInput) keepInput.value = '0';
            };
            reader.readAsDataURL(file);
        });
    }

    // ── Table Class Toggle ───────────────────────────────────────────────────
    const selectMode    = document.getElementById('select_mode');
    const inputMode     = document.getElementById('input_mode');
    const selectClass   = document.getElementById('table_class');
    const newClassInput = document.getElementById('new_table_class');
    const btnAdd        = document.getElementById('btn_add_new_class');
    const cancelBtn     = document.getElementById('cancel_new_class');
    const form          = document.getElementById('tableEditForm');
    let isInputMode     = false;

    function switchToInputMode() {
        isInputMode = true;
        selectMode.style.display = 'none';
        inputMode.style.display  = 'block';
        selectClass.required     = false;
        newClassInput.required   = true;
        setTimeout(() => newClassInput.focus(), 50);
    }

    function switchToSelectMode() {
        isInputMode = false;
        inputMode.style.display  = 'none';
        selectMode.style.display = 'block';
        selectClass.required     = true;
        newClassInput.required   = false;
        newClassInput.value      = '';
        selectClass.value        = '{{ old('table_class', $table->table_class) }}';
    }

    if (btnAdd)    btnAdd.addEventListener('click', switchToInputMode);
    if (cancelBtn) cancelBtn.addEventListener('click', switchToSelectMode);

    if (form) {
        form.addEventListener('submit', function (e) {
            if (isInputMode) {
                const val = newClassInput.value.trim();
                if (!val) {
                    e.preventDefault();
                    newClassInput.focus();
                    return;
                }
                if (!selectClass.querySelector(`option[value="${val}"]`)) {
                    selectClass.appendChild(new Option(val, val, true, true));
                } else {
                    selectClass.value = val;
                }
                selectClass.required   = true;
                newClassInput.required = false;
            }
        });
    }

    // Restore input mode jika ada old('new_table_class')
    const oldNewClass = '{{ old('new_table_class') }}';
    if (oldNewClass) {
        switchToInputMode();
        newClassInput.value = oldNewClass;
    }
});
</script>
@endpush