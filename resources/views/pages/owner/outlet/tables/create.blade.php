@extends('layouts.owner')

@section('title', __('messages.owner.outlet.tables.create_title'))
@section('page_title', __('messages.owner.outlet.tables.create_title'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.outlet.tables.create_title') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.outlet.tables.create_subtitle') }}</p>
                </div>
                <a href="{{ route('owner.user-owner.tables.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.outlet.tables.back') }}
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon"><span class="material-symbols-outlined">error</span></div>
                    <div class="alert-content">
                        <strong>{{ __('messages.owner.outlet.tables.re_check_input') }}</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="modern-card">
                <form action="{{ route('owner.user-owner.tables.store') }}" method="POST" enctype="multipart/form-data"
                    id="tableForm">
                    @csrf

                    <div class="card-body-modern">
                        <div class="profile-section">

                            {{-- Image Upload --}}
                            <div class="profile-picture-wrapper">
                                <div class="profile-picture-container" id="tableImageContainer">
                                    <div class="upload-placeholder" id="uploadPlaceholder">
                                        <span class="material-symbols-outlined">image</span>
                                        <span class="upload-text">{{ __('messages.owner.outlet.tables.upload_text') }}</span>
                                    </div>
                                    <img id="imagePreview" class="profile-preview" alt="Table Preview">
                                </div>
                                <input type="file" name="images" id="tableImage" accept="image/*" style="display:none;">
                                <small class="text-muted d-block text-center mt-2">{{ __('messages.owner.outlet.tables.image_hint') }}</small>
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
                                    <h3 class="section-title">{{ __('messages.owner.outlet.tables.table_information') }}</h3>
                                </div>

                                <div class="row g-4">

                                    {{-- Outlet / Partner --}}
                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.layout.outlets') }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="select-wrapper">
                                                <select name="partner_id" id="partner_id"
                                                    class="form-control-modern @error('partner_id') is-invalid @enderror"
                                                    required>
                                                    <option value="">{{ __('messages.owner.outlet.tables.choose_outlet') }}</option>
                                                    @foreach ($outlets as $outlet)
                                                        <option value="{{ $outlet->id }}"
                                                            {{ old('partner_id') == $outlet->id ? 'selected' : '' }}>
                                                            {{ $outlet->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                                            </div>
                                            @error('partner_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Table No --}}
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.outlet.tables.table_no') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="table_no" id="table_no"
                                                class="form-control-modern @error('table_no') is-invalid @enderror"
                                                value="{{ old('table_no') }}"
                                                placeholder="{{ __('messages.owner.outlet.tables.enter_table_number') }}"
                                                required>
                                            @error('table_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.outlet.tables.status') }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="select-wrapper">
                                                <select name="status" id="status"
                                                    class="form-control-modern @error('status') is-invalid @enderror"
                                                    required>
                                                    <option value="">{{ __('messages.owner.outlet.tables.choose_status') }}</option>
                                                    <option value="available"
                                                        {{ old('status') == 'available' ? 'selected' : '' }}>
                                                        {{ __('messages.owner.outlet.tables.available') }}
                                                    </option>
                                                    <option value="not_available"
                                                        {{ old('status') == 'not_available' ? 'selected' : '' }}>
                                                        {{ __('messages.owner.outlet.tables.not_available') }}
                                                    </option>
                                                </select>
                                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                                            </div>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Class Type --}}
                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.outlet.tables.class_type') }} <span class="text-danger">*</span>
                                            </label>

                                            <div id="select_mode">
                                                <div class="select-wrapper">
                                                    <select name="table_class" id="table_class"
                                                        class="form-control-modern @error('table_class') is-invalid @enderror"
                                                        required>
                                                        <option value="">{{ __('messages.owner.outlet.tables.choose_or_add_class') }}</option>
                                                        @foreach ($table_classes as $class)
                                                            <option value="{{ $class }}"
                                                                {{ old('table_class') == $class ? 'selected' : '' }}>
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
                                                    {{ __('messages.owner.outlet.tables.add_class') }}
                                                </button>
                                            </div>

                                            <div id="input_mode" style="display:none;">
                                                <input type="text" name="new_table_class" id="new_table_class"
                                                    class="form-control-modern"
                                                    placeholder="{{ __('messages.owner.outlet.tables.enter_new_table_class') }}">
                                                <button type="button"
                                                    class="btn-modern btn-secondary-modern btn-sm-modern mt-3"
                                                    id="cancel_new_class">
                                                    {{ __('messages.owner.outlet.tables.cancel') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Description --}}
                                    <div class="col-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">{{ __('messages.owner.outlet.tables.description') }}</label>
                                            <textarea name="description" id="description"
                                                class="form-control-modern @error('description') is-invalid @enderror"
                                                rows="4"
                                                placeholder="{{ __('messages.owner.outlet.tables.enter_table_description') }}">{{ old('description') }}</textarea>
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
                        <a href="{{ route('owner.user-owner.tables.index') }}" class="btn-cancel-modern">{{ __('messages.owner.outlet.tables.cancel') }}</a>
                        <button type="submit" class="btn-submit-modern">{{ __('messages.owner.outlet.tables.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const imageContainer = document.getElementById('tableImageContainer');
            const imageInput = document.getElementById('tableImage');
            const imagePreview = document.getElementById('imagePreview');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');

            if (imageContainer && imageInput) {
                imageContainer.addEventListener('click', () => imageInput.click());

                imageInput.addEventListener('change', function(e) {
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
                    };
                    reader.readAsDataURL(file);
                });
            }

            const selectMode = document.getElementById('select_mode');
            const inputMode = document.getElementById('input_mode');
            const selectClass = document.getElementById('table_class');
            const newClassInput = document.getElementById('new_table_class');
            const btnAdd = document.getElementById('btn_add_new_class');
            const cancelBtn = document.getElementById('cancel_new_class');
            const form = document.getElementById('tableForm');
            let isInputMode = false;

            function switchToInputMode() {
                isInputMode = true;
                selectMode.style.display = 'none';
                inputMode.style.display = 'block';
                selectClass.required = false;
                newClassInput.required = true;
                setTimeout(() => newClassInput.focus(), 50);
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

            if (btnAdd) btnAdd.addEventListener('click', switchToInputMode);
            if (cancelBtn) cancelBtn.addEventListener('click', switchToSelectMode);

            if (form) {
                form.addEventListener('submit', function(e) {
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
                        selectClass.required = true;
                        newClassInput.required = false;
                    }
                });
            }

            const oldNewClass = '{{ old('new_table_class') }}';
            if (oldNewClass) {
                switchToInputMode();
                newClassInput.value = oldNewClass;
            }
        });
    </script>
@endpush