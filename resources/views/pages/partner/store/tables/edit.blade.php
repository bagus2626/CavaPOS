@extends('layouts.partner')

@section('title', 'Edit Table')
@section('page_title', 'Edit Table')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <a href="{{ route('partner.store.tables.index') }}" class="btn btn-outline-choco mb-3">
                <i
                    class="fas fa-arrow-left mr-2"></i>{{ __('messages.partner.outlet.table_management.tables.back_to_tables') }}
            </a>

            <div class="card shadow-sm rounded-xl">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('messages.partner.outlet.table_management.tables.edit_table') }}</h5>
                </div>

                <div class="card-body">
                    {{-- errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>{{ __('messages.partner.outlet.table_management.tables.re_check_input') }}</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('partner.store.tables.update', $table->id) }}" method="POST"
                        enctype="multipart/form-data" id="tableEditForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- Table No --}}
                            <div class="col-md-6 mb-3">
                                <label for="table_no"
                                    class="form-label">{{ __('messages.partner.outlet.table_management.tables.table_no') }}</label>
                                <input type="text" name="table_no" id="table_no"
                                    class="form-control @error('table_no') is-invalid @enderror"
                                    value="{{ old('table_no', $table->table_no) }}" required>
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
                                        {{ __('messages.partner.outlet.table_management.tables.choose_status') }}</option>
                                    <option value="available"
                                        {{ old('status', $table->status) == 'available' ? 'selected' : '' }}>
                                        {{ __('messages.partner.outlet.table_management.tables.available') }}</option>
                                    <option value="occupied"
                                        {{ old('status', $table->status) == 'occupied' ? 'selected' : '' }}>
                                        {{ __('messages.partner.outlet.table_management.tables.occupied') }}</option>
                                    <option value="reserved"
                                        {{ old('status', $table->status) == 'reserved' ? 'selected' : '' }}>
                                        {{ __('messages.partner.outlet.table_management.tables.reserved') }}</option>
                                    <option value="not_available"
                                        {{ old('status', $table->status) == 'not_available' ? 'selected' : '' }}>
                                        {{ __('messages.partner.outlet.table_management.tables.not_available') }}</option>
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

                                        @if (!empty($table_classes) && count($table_classes) > 0)
                                            @foreach ($table_classes as $class)
                                                <option value="{{ $class }}"
                                                    {{ old('table_class', $table->table_class) == $class ? 'selected' : '' }}>
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
                                <input type="file" name="images[]" id="images"
                                    class="form-control @error('images') is-invalid @enderror" accept="image/*" multiple>
                                <small
                                    class="text-muted d-block">{{ __('messages.partner.outlet.table_management.tables.new_upload') }}
                                    <b>{{ __('messages.partner.outlet.table_management.tables.replace') }}</b>
                                    {{ __('messages.partner.outlet.table_management.tables.old_picture') }}</small>
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- Preview gambar baru --}}
                                <div id="imagesPreview" class="thumb-list mt-2"></div>

                                {{-- Gambar lama + checkbox hapus --}}
                                @if ($table->images && count($table->images) > 0)
                                    <div class="mt-3">
                                        <label
                                            class="form-label">{{ __('messages.partner.outlet.table_management.tables.current_images') }}</label>
                                        <div class="thumb-list">
                                            @foreach ($table->images as $index => $img)
                                                @php $src = asset($img['path']); @endphp
                                                <div class="thumb-item">
                                                    <img src="{{ $src }}" alt="Table Image" class="thumb-img-sm">
                                                    <div class="form-check mt-2 delete-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="delete_images[]" value="{{ $img['filename'] }}"
                                                            id="delete_image_{{ $index }}">
                                                        <label class="form-check-label"
                                                            for="delete_image_{{ $index }}">
                                                            {{ __('messages.partner.outlet.table_management.tables.delete_this_picture') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description"
                                class="form-label">{{ __('messages.partner.outlet.table_management.tables.description') }}</label>
                            <textarea name="description" id="description" rows="3"
                                class="form-control @error('description') is-invalid @enderror">{{ old('description', $table->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('partner.store.tables.index') }}"
                                class="btn btn-outline-choco mr-2">{{ __('messages.partner.outlet.table_management.tables.cancel') }}</a>
                            <button type="submit"
                                class="btn btn-choco">{{ __('messages.partner.outlet.table_management.tables.update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* ==== Tables Edit (scoped) ==== */
        :root {
            --choco: #8c1000;
            --soft-choco: #c12814;
            --ink: #22272b;
            --paper: #f7f7f8;
            --radius: 12px;
            --shadow: 0 6px 20px rgba(0, 0, 0, .08);
        }

        /* rounded-xl fallback */
        .rounded-xl {
            border-radius: 1rem;
        }

        /* Card & header */
        .card.shadow-sm {
            border: 0;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #eef1f4;
        }

        .card-title {
            color: var(--ink);
            font-weight: 700;
            letter-spacing: .2px;
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

        /* Select2 selaras tema */
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

        /* Buttons brand (fallback jika belum ada global) */
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

        /* Thumbnails (baru & lama) */
        .thumb-list {
            display: flex;
            flex-wrap: wrap;
            margin: -.4rem;
        }

        .thumb-item {
            margin: .4rem;
            text-align: center;
            background: #fff;
            border-radius: 12px;
            padding: .4rem;
            transition: .15s ease;
        }

        .thumb-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            border: 0;
            box-shadow: var(--shadow);
        }

        .thumb-img-sm {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            border: 0;
            box-shadow: var(--shadow);
        }

        .thumb-caption {
            font-size: .72rem;
            color: #6b7280;
            margin-top: .35rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px;
        }

        .thumb-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, .08);
        }

        /* Checkbox hapus: beri aksen saat dicentang */
        .delete-check .form-check-input:checked {
            background-color: var(--choco);
            border-color: var(--choco);
        }

        .thumb-item.marked-delete {
            outline: 2px dashed var(--soft-choco);
            outline-offset: 3px;
            background: #fff0ee;
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
            border-color: var(--choco);
            color: var(--choco);
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
            border-color: var(--choco);
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
        document.addEventListener('DOMContentLoaded', function() {
            // Select2 untuk status (jika sudah di-load di layout)
            if (window.jQuery && $('.select2').length) {
                $('.select2').select2({
                    theme: 'bootstrap-5'
                });
            }

            // Preview & validasi gambar baru
            const input = document.getElementById('images');
            const previewWrap = document.getElementById('imagesPreview');
            const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];
            const MAX_SIZE = 2 * 1024 * 1024; // 2MB
            const MAX_FILES = 3;

            if (input && previewWrap) {
                input.addEventListener('change', () => {
                    const files = Array.from(input.files || []);
                    if (files.length > MAX_FILES) {
                        alert(`Maksimal ${MAX_FILES} gambar.`);
                        input.value = '';
                        previewWrap.innerHTML = '';
                        return;
                    }
                    for (const f of files) {
                        if (!ALLOWED.includes(f.type)) {
                            alert('Gunakan JPG, PNG, atau WEBP.');
                            input.value = '';
                            previewWrap.innerHTML = '';
                            return;
                        }
                        if (f.size > MAX_SIZE) {
                            alert('Ukuran file melebihi 2 MB.');
                            input.value = '';
                            previewWrap.innerHTML = '';
                            return;
                        }
                    }
                    previewWrap.innerHTML = '';
                    files.forEach((file) => {
                        const url = URL.createObjectURL(file);
                        const el = document.createElement('div');
                        el.className = 'thumb-item';
                        el.innerHTML = `
          <img src="${url}" class="thumb-img" alt="${file.name}">
          <div class="thumb-caption">${file.name}</div>
        `;
                        previewWrap.appendChild(el);
                    });
                });
            }

            // Highlight kartu gambar lama saat checkbox di-klik
            document.querySelectorAll('.delete-check input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', function() {
                    const card = this.closest('.thumb-item');
                    if (!card) return;
                    card.classList.toggle('marked-delete', this.checked);
                });
            });

            // === Toggle Input Mode untuk Table Class (sama seperti create) ===
            const selectMode = document.getElementById('select_mode');
            const inputMode = document.getElementById('input_mode');
            const selectClass = $('#table_class');
            const newClassInput = document.getElementById('new_table_class');
            const btnAddNewClass = document.getElementById('btn_add_new_class');
            const cancelBtn = document.getElementById('cancel_new_class');
            const form = document.getElementById('tableEditForm');

            let isInputMode = false;

            // Initialize Select2 untuk table_class
            if (selectClass.length) {
                selectClass.select2({
                    theme: 'bootstrap-5',
                    placeholder: '{{ __('messages.partner.outlet.table_management.tables.placeholder_1') }}',
                    allowClear: true
                });
            }

            // Handle klik tombol "Tambah Kelas Baru"
            if (btnAddNewClass) {
                btnAddNewClass.addEventListener('click', function() {
                    switchToInputMode();
                });
            }

            // Fungsi untuk switch ke input mode
            function switchToInputMode() {
                isInputMode = true;
                selectMode.style.display = 'none';
                inputMode.style.display = 'block';

                // Disable select dan set required ke input
                selectClass.prop('required', false);
                newClassInput.required = true;

                // Focus ke input dengan smooth animation
                setTimeout(() => {
                    newClassInput.focus();
                    inputMode.style.opacity = '1';
                }, 50);

                inputMode.style.opacity = '0';
                inputMode.style.transition = 'opacity 0.2s ease';
            }

            // Fungsi untuk kembali ke select mode
            function switchToSelectMode() {
                isInputMode = false;
                inputMode.style.display = 'none';
                selectMode.style.display = 'block';

                // Enable select dan remove required dari input
                selectClass.prop('required', true);
                newClassInput.required = false;
                newClassInput.value = '';

                // Restore nilai original dari database
                selectClass.val('{{ old('table_class', $table->table_class) }}').trigger('change');
            }

            // Handle tombol batal
            if (cancelBtn) {
                cancelBtn.addEventListener('click', switchToSelectMode);
            }

            // Handle form submit
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (isInputMode) {
                        const newClassName = newClassInput.value.trim();

                        if (!newClassName) {
                            e.preventDefault();
                            alert(
                                'Silakan masukkan nama kelas baru atau klik "Batal" untuk memilih kelas yang ada');
                            newClassInput.focus();
                            return false;
                        }

                        // Buat option baru dan set sebagai selected
                        const newOption = new Option(newClassName, newClassName, true, true);
                        selectClass.append(newOption).trigger('change');

                        // Re-enable select sebelum submit
                        selectClass.prop('required', true);
                        newClassInput.required = false;

                        console.log('New class created:', newClassName);
                    }
                });
            }

            // Handle initial state jika ada error dan old() value
            const oldNewClass = '{{ old('new_table_class') }}';
            if (oldNewClass) {
                switchToInputMode();
                newClassInput.value = oldNewClass;
            }
        });
    </script>
@endpush
