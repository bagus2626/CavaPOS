@extends('layouts.owner')

@section('title', __('messages.owner.products.categories.edit_category'))
@section('page_title', __('messages.owner.products.categories.edit_category'))

@section('content')
    <section class="content">
        <div class="container-fluid owner-category-edit"> {{-- PAGE SCOPE --}}

            <form method="POST" action="{{ route('owner.user-owner.categories.update', $category) }}"
                enctype="multipart/form-data" class="form-card">
                @csrf
                @method('PUT')

                {{-- Category Name --}}
                <div class="form-group">
                    <label for="category_name"
                        class="form-label required">{{ __('messages.owner.products.categories.category_name') }}</label>
                    <input type="text" name="category_name" id="category_name" class="form-control"
                        value="{{ old('category_name', $category->category_name) }}" required>
                    @error('category_name') <div class="invalid-hint">{{ $message }}</div> @enderror
                </div>

                {{-- Description --}}
                <div class="form-group">
                    <label for="description"
                        class="form-label">{{ __('messages.owner.products.categories.description') }}</label>
                    <textarea name="description" id="description" class="form-control"
                        rows="3">{{ old('description', $category->description) }}</textarea>
                    @error('description') <div class="invalid-hint">{{ $message }}</div> @enderror
                </div>

                {{-- Image Section --}}
                <div class="form-group">
                    <label for="images" class="form-label">{{ __('messages.owner.products.categories.picture') }}</label>
                    <input type="file" name="images" id="images" class="form-control" accept="image/*"
                        onchange="previewImage(event)">

                    <small class="text-muted d-block mt-1">
                        {{ __('messages.owner.products.categories.note_image') }}
                    </small>

                    <div id="error-images" class="invalid-hint">
                        @error('images'){{ $message }} @enderror
                    </div>

                    {{-- Wadah Tunggal untuk Gambar --}}
                    <div class="mt-2" id="image-display-container"
                        style="{{ ($category->images && isset($category->images['path'])) ? '' : 'display: none;' }}">
                        <div class="position-relative d-inline-block">
                            <img id="main-image-preview"
                                src="{{ ($category->images && isset($category->images['path'])) ? asset($category->images['path']) : '#' }}"
                                alt="Preview" class="thumb">

                            <button type="button" class="btn btn-danger btn-sm position-absolute"
                                style="top: 5px; right: 5px;" onclick="clearImage()" title="Hapus gambar">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        {{-- Hidden input untuk memberi tahu backend apakah gambar lama dihapus --}}
                        <input type="hidden" name="keep_existing_image" id="keep_existing_image" value="1">
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('owner.user-owner.categories.index') }}" class="btn btn-outline-choco mr-2">
                        {{ __('messages.owner.products.categories.back') }}
                    </a>
                    <button type="submit" class="btn btn-primary mr-1">
                        {{ __('messages.owner.products.categories.update') }}
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script>
        function previewImage(event) {
            const input = event.target;
            const mainPreview = document.getElementById('main-image-preview');
            const container = document.getElementById('image-display-container');
            const errorDisplay = document.getElementById('error-images');
            const keepInput = document.getElementById('keep_existing_image');
            const maxSize = 2 * 1024 * 1024; // 2MB

            errorDisplay.textContent = '';

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validasi Ukuran
                if (file.size > maxSize) {
                    errorDisplay.textContent = '{{ __('messages.owner.products.categories.error_size_image') }}';
                    input.value = '';
                    // Jika ukuran salah, jangan ganggu gambar lama dulu atau sembunyikan jika sudah telanjur ganti
                    return;
                }

                // Baca file baru
                const reader = new FileReader();
                reader.onload = function (e) {
                    mainPreview.src = e.target.result;
                    container.style.display = 'block';

                    // Jika user upload baru, kita anggap tidak lagi memakai gambar lama secara default
                    // Tapi backend Anda menangani: jika ada file baru, gambar lama otomatis dihapus.
                    if (keepInput) keepInput.value = '0';
                };
                reader.readAsDataURL(file);
            }
        }

        function clearImage() {
            const fileInput = document.getElementById('images');
            const mainPreview = document.getElementById('main-image-preview');
            const container = document.getElementById('image-display-container');
            const keepInput = document.getElementById('keep_existing_image');
            const errorDisplay = document.getElementById('error-images');

            // 1. Reset input file
            if (fileInput) fileInput.value = '';

            // 2. Tandai di backend bahwa gambar lama harus dihapus
            if (keepInput) keepInput.value = '0';

            // 3. Sembunyikan container dan reset src
            if (container) container.style.display = 'none';
            if (mainPreview) mainPreview.src = '#';
            if (errorDisplay) errorDisplay.textContent = '';
        }
    </script>

    <style>
        /* ===== Owner › Category Edit (page scope) ===== */
        .owner-category-edit {
            --choco: #8c1000;
            --soft-choco: #c12814;
            --ink: #22272b;
            --radius: 12px;
            --shadow: 0 6px 20px rgba(0, 0, 0, .08);
        }

        /* Title */
        .owner-category-edit .page-title {
            color: var(--ink);
            font-weight: 500;
        }

        /* Card-ish form */
        .owner-category-edit .form-card {
            background: #fff;
            border: 0;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.25rem 1.25rem 1rem;
        }

        /* Labels & required mark */
        .owner-category-edit .form-label {
            font-weight: 600;
            color: #374151;
        }

        .owner-category-edit .required::after {
            content: " *";
            color: #dc3545;
        }

        /* Inputs focus brand */
        .owner-category-edit .form-control:focus {
            border-color: var(--choco);
            box-shadow: 0 0 0 .2rem rgba(140, 16, 0, .15);
        }

        /* Validation hint */
        .owner-category-edit .invalid-hint {
            color: #b91c1c;
            margin-top: .25rem;
        }

        /* Buttons */
        .owner-category-edit .btn-primary {
            background: var(--choco);
            border-color: var(--choco);
        }

        .owner-category-edit .btn-primary:hover {
            background: var(--soft-choco);
            border-color: var(--soft-choco);
        }

        .owner-category-edit .btn-outline-choco {
            color: var(--choco);
            border: 1px solid var(--choco);
            background: #fff;
        }

        .owner-category-edit .btn-outline-choco:hover {
            color: #fff;
            background: var(--choco);
            border-color: var(--choco);
        }

        /* Preview thumb */
        .owner-category-edit .thumb {
            width: 200px;
            height: auto;
            max-height: 200px;
            object-fit: cover;
            border-radius: 12px;
            border: 0;
            box-shadow: var(--shadow);
        }

        /* Small gaps utility */
        .owner-category-edit .gap-2 {
            gap: .5rem;
        }

        /* Position relative untuk button delete */
        .owner-category-edit .position-relative {
            position: relative;
        }

        .owner-category-edit .position-absolute {
            position: absolute;
        }

        .owner-category-edit .d-inline-block {
            display: inline-block;
        }
    </style>
@endsection