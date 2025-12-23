@extends('layouts.partner')

@section('title', __('messages.partner.outlet.table_management.tables.create_tables'))
@section('page_title', __('messages.partner.outlet.table_management.tables.create_new_table'))

@section('content')
    <section class="content">
        <div class="container-fluid">
            <a href="{{ route('partner.store.tables.index') }}" class="btn btn-outline-choco mb-3">
                <i
                    class="fas fa-arrow-left mr-2"></i>{{ __('messages.partner.outlet.table_management.tables.back_to_tables') }}
            </a>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('messages.partner.outlet.table_management.tables.create_new_table') }}
                    </h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>P{{ __('messages.partner.outlet.table_management.tables.re_check_input') }}</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('partner.store.tables.store') }}" method="POST" enctype="multipart/form-data"
                        id="tableForm">
                        @csrf

                        <div class="row">
                            {{-- Table No --}}
                            <div class="col-md-6 mb-3">
                                <label for="table_no"
                                    class="form-label">{{ __('messages.partner.outlet.table_management.tables.table_no') }}</label>
                                <input type="text" name="table_no" id="table_no"
                                    class="form-control @error('table_no') is-invalid @enderror"
                                    value="{{ old('table_no') }}" required>
                                @error('table_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6 mb-3">
                                <label for="status"
                                    class="form-label">{{ __('messages.partner.outlet.table_management.tables.status') }}</label>
                                <select name="status" id="status"
                                    class="form-control select2 @error('status') is-invalid @enderror" required>
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
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- Table Class with Toggle between Select and Input --}}
                            <div class="col-md-6 mb-3">
                                <label for="table_class" class="form-label">
                                    {{ __('messages.partner.outlet.table_management.tables.class_type') }}
                                </label>

                                {{-- SELECT MODE (default) --}}
                                <div id="select_mode">
                                    <select name="table_class" id="table_class"
                                        class="form-control select2 @error('table_class') is-invalid @enderror" required>
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

                                    @error('table_class')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror

                                    {{-- Tombol Tambah Kelas Baru --}}
                                    <button type="button" class="btn-add-class mt-2" id="btn_add_new_class">
                                        <i class="fas fa-plus-circle"></i>
                                        <span>{{ __('messages.partner.outlet.table_management.tables.add_class') }}</span>
                                    </button>
                                </div>

                                {{-- INPUT MODE (hidden by default) --}}
                                <div id="input_mode" style="display: none;">
                                    <div class="new-class-input-wrapper">
                                        <input type="text" name="new_table_class" id="new_table_class"
                                            class="form-control new-class-input">
                                        <button type="button" class="btn btn-cancel-new-class" id="cancel_new_class">
                                            {{ __('messages.partner.outlet.table_management.tables.cancel') }}
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-0">
                                        {{ __('messages.partner.outlet.table_management.tables.muted_text_2') }}
                                    </small>
                                </div>
                            </div>

                            {{-- Images --}}
                            <div class="col-md-6 mb-3">
                                <label for="images"
                                    class="form-label">{{ __('messages.partner.outlet.table_management.tables.upload_images') }}</label>
                                <input type="file" name="images" id="images"
                                    class="form-control @error('images') is-invalid @enderror" accept="image/*">
                                <small class="text-muted d-block">Gunakan JPG, PNG, atau WEBP. Maksimal 2MB.</small>
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- Preview --}}
                                <div id="imagesPreview" class="thumb-list mt-2"></div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description"
                                class="form-label">{{ __('messages.partner.outlet.table_management.tables.description') }}</label>
                            <textarea name="description" id="description" rows="3"
                                class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="d-flex justify-content-end form-actions">
                            <a href="{{ route('partner.store.tables.index') }}"
                                class="btn btn-outline-choco mr-2">{{ __('messages.partner.outlet.table_management.tables.cancel') }}</a>
                            <button type="submit"
                                class="btn btn-choco">{{ __('messages.partner.outlet.table_management.tables.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* ==== Tables Create (page scope) ==== */
        :root {
            --choco: #8c1000;
            --soft-choco: #c12814;
            --ink: #22272b;
            --paper: #f7f7f8;
            --radius: 12px;
            --shadow: 0 6px 20px rgba(0, 0, 0, .08);
        }

        /* Card & headings */
        .card.shadow-sm {
            border: 0;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #eef1f4;
        }

        .card-title {
            color: var(--ink);
            font-weight: 600;
        }

        /* Labels & inputs */
        .form-label {
            font-weight: 600;
            color: #374151;
        }

        .form-control:focus,
        select.form-control:focus {
            border-color: var(--choco);
            box-shadow: 0 0 0 .2rem rgba(140, 16, 0, .15);
        }

        /* Select2 theme alignment */
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 10px;
            border-color: #e5e7eb;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background: var(--soft-choco);
        }

        /* Alerts */
        .alert {
            border-left: 4px solid var(--choco);
            border-radius: 10px;
        }

        .alert-danger {
            background: #fff5f5;
            border-color: #fde2e2;
            color: #991b1b;
        }

        /* Actions */
        .form-actions .btn {
            min-width: 120px;
        }

        /* Brand buttons (fallback jika belum di theme global) */
        .btn-choco {
            background: var(--choco);
            border-color: var(--choco);
            color: #fff;
        }

        .btn-choco:hover {
            background: var(--soft-choco);
            border-color: var(--soft-choco);
            color: #fff;
        }

        .btn-outline-choco {
            color: var(--choco);
            border-color: var(--choco);
        }

        .btn-outline-choco:hover {
            color: #fff;
            background: var(--choco);
            border-color: var(--choco);
        }

        /* Thumbs (preview images) */
        .thumb-list {
            display: flex;
            flex-wrap: wrap;
            margin: -.35rem;
        }

        .thumb-item {
            width: 100px;
            margin: .35rem;
            text-align: center;
        }

        .thumb-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            border: 0;
            box-shadow: var(--shadow);
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .thumb-item:hover .thumb-img {
            transform: scale(1.03);
            box-shadow: 0 10px 24px rgba(0, 0, 0, .12);
        }

        .thumb-caption {
            font-size: .72rem;
            color: #6b7280;
            margin-top: .35rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Tombol Tambah Kelas Baru */
        .btn-add-class {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: transparent;
            border: 1.5px dashed #d1d5db;
            border-radius: 8px;
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-add-class:hover {
            background: #f9fafb;
            border-color: var(--choco, #8c1000);
            color: var(--choco, #8c1000);
        }

        .btn-add-class i {
            font-size: 1rem;
        }

        /* Input Mode Wrapper */
        .new-class-input-wrapper {
            display: flex;
            gap: 0.75rem;
            align-items: stretch;
        }

        .new-class-input {
            flex: 1;
            border-radius: 10px;
            border: 1.5px solid #d1d5db;
            padding: 0.625rem 1rem;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
        }

        .new-class-input:focus {
            border-color: var(--choco, #8c1000);
            box-shadow: 0 0 0 0.2rem rgba(140, 16, 0, 0.1);
            outline: none;
        }

        .new-class-input::placeholder {
            color: #9ca3af;
            font-style: italic;
        }

        /* Tombol Batal */
        .btn-cancel-new-class {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            /* ini yang membuat tinggi setara input */
            background: #fff;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-cancel-new-class:hover {
            background: var(--choco);
            border-color: var(--choco);
            color: #fff;
        }

        .btn-cancel-new-class i {
            font-size: 0.875rem;
        }

        /* Mode transitions */
        #select_mode,
        #input_mode {
            transition: opacity 0.2s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .new-class-input-wrapper {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-cancel-new-class {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && $('.select2').length) {
                $('.select2').select2({
                    theme: 'bootstrap-5'
                });
            }

            const input = document.getElementById('images');
            const previewWrap = document.getElementById('imagesPreview');
            const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];
            const MAX_SIZE = 2 * 1024 * 1024; // 2MB

            if (input && previewWrap) {
                input.addEventListener('change', function () {
                    const file = this.files[0];

                    if (!file) {
                        previewWrap.innerHTML = '';
                        return;
                    }

                    if (!ALLOWED.includes(file.type)) {
                        alert('Gunakan format gambar JPG, PNG, atau WEBP.');
                        this.value = '';
                        previewWrap.innerHTML = '';
                        return;
                    }

                    if (file.size > MAX_SIZE) {
                        alert('Ukuran file tidak boleh melebihi 2 MB.');
                        this.value = '';
                        previewWrap.innerHTML = '';
                        return;
                    }

                    previewWrap.innerHTML = '';
                    const url = URL.createObjectURL(file);
                    const thumbItem = document.createElement('div');
                    thumbItem.style.maxWidth = '250px';
                    thumbItem.innerHTML = `
                            <div class="card shadow-sm border-0 mt-2">
                                <img src="${url}" class="card-img-top rounded" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2 bg-light text-center">
                                    <small class="text-truncate d-block" style="max-width: 230px;">${file.name}</small>
                                </div>
                            </div>
                        `;
                    previewWrap.appendChild(thumbItem);
                });
            }

            const selectMode = document.getElementById('select_mode');
            const inputMode = document.getElementById('input_mode');
            const selectClass = $('#table_class');
            const newClassInput = document.getElementById('new_table_class');
            const btnAddNewClass = document.getElementById('btn_add_new_class');
            const cancelBtn = document.getElementById('cancel_new_class');
            const form = document.getElementById('tableForm');

            let isInputMode = false;

            if (selectClass.length) {
                selectClass.select2({
                    theme: 'bootstrap-5',
                    placeholder: '{{ __('messages.partner.outlet.table_management.tables.placeholder_1') }}',
                    allowClear: true
                });
            }

            function switchToInputMode() {
                isInputMode = true;
                selectMode.style.display = 'none';
                inputMode.style.display = 'block';
                selectClass.prop('required', false);
                newClassInput.required = true;

                setTimeout(() => {
                    newClassInput.focus();
                    inputMode.style.opacity = '1';
                }, 50);

                inputMode.style.opacity = '0';
                inputMode.style.transition = 'opacity 0.2s ease';
            }

            function switchToSelectMode() {
                isInputMode = false;
                inputMode.style.display = 'none';
                selectMode.style.display = 'block';
                selectClass.prop('required', true);
                newClassInput.required = false;
                newClassInput.value = '';
                selectClass.val('').trigger('change');
            }

            if (btnAddNewClass) {
                btnAddNewClass.addEventListener('click', switchToInputMode);
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', switchToSelectMode);
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

                        const newOption = new Option(newClassName, newClassName, true, true);
                        selectClass.append(newOption).trigger('change');

                        selectClass.prop('required', true);
                        newClassInput.required = false;
                    }
                });
            }

            const initialValue = selectClass.val();
            if (!initialValue && '{{ old('new_table_class') }}') {
                switchToInputMode();
                newClassInput.value = '{{ old('new_table_class') }}';
            }
        });
    </script>
@endpush