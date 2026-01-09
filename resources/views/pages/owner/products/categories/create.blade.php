@extends('layouts.owner')
@section('title', __('messages.owner.products.categories.add_category'))
@section('page_title', __('messages.owner.products.categories.add_category'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <!-- Header Section -->
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.categories.add_category') }}</h1>
                    <p class="page-subtitle">Create a new category to organize your products effectively.</p>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                    </div>
                    <div class="alert-content">
                        <strong>{{ __('messages.owner.user_management.employees.recheck_input') }}:</strong>
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
                <form action="{{ route('owner.user-owner.categories.store') }}" method="POST" enctype="multipart/form-data"
                    id="categoryForm">
                    @csrf
                    <div class="card-body-modern">

                        <!-- Profile Section -->
                        <div class="profile-section">
                            <!-- Category Picture Upload -->
                            <div class="profile-picture-wrapper">
                                <div class="profile-picture-container" id="profilePictureContainer">
                                    <div class="upload-placeholder" id="uploadPlaceholder">
                                        <span class="material-symbols-outlined">image</span>
                                        <span class="upload-text">Upload</span>
                                    </div>
                                    <img id="imagePreview" class="profile-preview" alt="Category Preview">
                                </div>
                                <input type="file" name="images" id="images" accept="image/*" style="display: none;">
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
                                                value="{{ old('category_name') }}"
                                                placeholder="e.g. Beverages, Snacks, Main Course" required>
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
                                                rows="4"
                                                placeholder="Brief description of this category...">{{ old('description') }}</textarea>
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
                            {{ __('messages.owner.products.categories.cancel') }}
                        </a>
                        <button type="submit" class="btn-submit-modern">
                            {{ __('messages.owner.products.categories.create_category') }}
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
                            Crop Category Image
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script src="{{ asset('js/image-cropper.js') }}"></script>

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
                aspectRatio: 1, // Square crop
                outputWidth: 800,
                outputHeight: 800
            });

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