@extends('layouts.owner')
@section('title', __('messages.owner.products.categories.edit_category'))
@section('page_title', __('messages.owner.products.categories.edit_category'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <!-- Header Section -->
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.categories.edit_category') }}</h1>
                    <p class="page-subtitle">Update category information and settings.</p>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                    </div>
                    <div class="alert-content">
                        <strong>{{ __('messages.owner.products.categories.alert_error') }}:</strong>
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

            <!-- Main Card -->
            <div class="modern-card">
                <form method="POST" action="{{ route('owner.user-owner.categories.update', $category) }}"
                    enctype="multipart/form-data" id="categoryForm">
                    @csrf
                    @method('PUT')

                    <div class="card-body-modern">
                        <!-- Profile Section -->
                        <div class="profile-section">
                            <!-- Category Picture Upload -->
                            <div class="profile-picture-wrapper">
                                <div class="profile-picture-container" id="profilePictureContainer">
                                    <div class="upload-placeholder" id="uploadPlaceholder"
                                        style="{{ ($category->images && isset($category->images['path'])) ? 'display: none;' : '' }}">
                                        <span class="material-symbols-outlined">add_a_photo</span>
                                        <span class="upload-text">Upload</span>
                                    </div>
                                    <img id="imagePreview"
                                        class="profile-preview {{ ($category->images && isset($category->images['path'])) ? 'active' : '' }}"
                                        src="{{ ($category->images && isset($category->images['path'])) ? asset($category->images['path']) : '' }}"
                                        alt="Category Preview">
                                </div>
                                <input type="file" name="images" id="images" accept="image/*" style="display: none;">
                                <input type="hidden" name="keep_existing_image" id="keep_existing_image" value="1">
                                <small class="text-muted d-block text-center mt-2">JPG, PNG, WEBP. Max 2 MB</small>
                                @error('images')
                                    <div class="text-danger text-center mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category Information Fields -->
                            <div class="personal-info-fields">
                                <div class="section-header">
                                    <div class="section-icon section-icon-red">
                                        <span class="material-symbols-outlined">category</span>
                                    </div>
                                    <h3 class="section-title">Category Information</h3>
                                </div>
                                <div class="row g-4">
                                    <!-- Category Name -->
                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.products.categories.category_name') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="category_name" id="category_name"
                                                class="form-control-modern @error('category_name') is-invalid @enderror"
                                                value="{{ old('category_name', $category->category_name) }}"
                                                placeholder="e.g. Beverages" required>
                                            @error('category_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.products.categories.description') }}
                                            </label>
                                            <textarea name="description" id="description"
                                                class="form-control-modern @error('description') is-invalid @enderror"
                                                rows="3"
                                                placeholder="Enter category description...">{{ old('description', $category->description) }}</textarea>
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
                        <a href="{{ route('owner.user-owner.categories.index') }}" class="btn-cancel-modern">
                            {{ __('messages.owner.products.categories.back') }}
                        </a>
                        <button type="submit" class="btn-submit-modern">
                            {{ __('messages.owner.products.categories.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    script src="{{ asset('js/image-cropper.js') }}"></script>
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            // Initialize Category Image Cropper (1:1 Square)
            ImageCropper.init({
                id: 'category',
                inputId: 'images',
                previewId: 'imagePreview',
                modalId: 'cropModal',
                imageToCropId: 'imageToCrop',
                cropBtnId: 'cropBtn',
                containerId: 'profilePictureContainer',
                removeInputId: 'keep_existing_image',
                aspectRatio: 1, // Square crop
                outputWidth: 800,
                outputHeight: 800
            });

            // ==== Right-click to Remove Image ====
            const profileContainer = document.getElementById('profilePictureContainer');
            const mainPreview = document.getElementById('imagePreview');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');
            const fileInput = document.getElementById('images');
            const keepInput = document.getElementById('keep_existing_image');

            if (profileContainer) {
                profileContainer.addEventListener('contextmenu', function (e) {
                    e.preventDefault();

                    if (mainPreview && mainPreview.classList.contains('active')) {
                        if (confirm('Remove this image?')) {
                            mainPreview.src = '';
                            mainPreview.classList.remove('active');

                            if (uploadPlaceholder) {
                                uploadPlaceholder.style.display = 'flex';
                            }

                            if (fileInput) {
                                fileInput.value = '';
                            }

                            if (keepInput) {
                                keepInput.value = '0';
                            }
                        }
                    }
                });
            }

            // ==== Form Validation ====
            const form = document.getElementById('categoryForm');
            const categoryName = document.getElementById('category_name');

            if (form && categoryName) {
                form.addEventListener('submit', function (e) {
                    if (categoryName.value.trim() === '') {
                        e.preventDefault();
                        alert('Category name is required.');
                        categoryName.focus();
                        return false;
                    }
                });
            }
        });
    </script>
@endpush