@extends('layouts.staff')
@section('title', __('messages.owner.products.categories.add_category'))
@section('page_title', __('messages.owner.products.categories.add_category'))

@php
    $empRole = strtolower(auth('employee')->user()->role ?? 'manager');
@endphp

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.categories.add_category') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.products.categories.create_subtitle') }}</p>
                </div>
                <a href="{{ route("employee.{$empRole}.categories.index") }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.products.categories.back') }}
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon"><span class="material-symbols-outlined">error</span></div>
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
                    <div class="alert-icon"><span class="material-symbols-outlined">check_circle</span></div>
                    <div class="alert-content">{{ session('success') }}</div>
                </div>
            @endif

            <div class="modern-card">
                <form action="{{ route("employee.{$empRole}.categories.store") }}" method="POST"
                    enctype="multipart/form-data" id="categoryForm">
                    @csrf
                    <div class="card-body-modern">
                        <div class="profile-section">
                            <div class="profile-picture-wrapper">
                                <div class="profile-picture-container" id="profilePictureContainer">
                                    <div class="upload-placeholder" id="uploadPlaceholder">
                                        <span class="material-symbols-outlined">image</span>
                                        <span class="upload-text">{{ __('messages.owner.products.categories.upload_text') }}</span>
                                    </div>
                                    <img id="imagePreview" class="profile-preview"
                                        alt="{{ __('messages.owner.products.categories.image_preview_alt') }}">
                                    <button type="button" id="removeImageBtn" class="btn-remove btn-remove-top"
                                        style="display: none;">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                                <input type="file" name="images" id="images" accept="image/*" style="display: none;">
                                <small class="text-muted d-block text-center mt-2">
                                    {{ __('messages.owner.products.categories.image_hint') }}
                                </small>
                                @error('images')
                                    <div class="text-danger text-center mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="personal-info-fields">
                                <div class="section-header">
                                    <div class="section-icon section-icon-red">
                                        <span class="material-symbols-outlined">category</span>
                                    </div>
                                    <h3 class="section-title">{{ __('messages.owner.products.categories.category_info_title') }}</h3>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.products.categories.category_name') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="category_name" id="category_name"
                                                class="form-control-modern @error('category_name') is-invalid @enderror"
                                                value="{{ old('category_name') }}"
                                                placeholder="{{ __('messages.owner.products.categories.placeholder_name') }}"
                                                required>
                                            @error('category_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.products.categories.description') }}
                                            </label>
                                            <textarea name="description" id="description"
                                                class="form-control-modern @error('description') is-invalid @enderror"
                                                rows="4"
                                                placeholder="{{ __('messages.owner.products.categories.placeholder_description') }}">{{ old('description') }}</textarea>
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
                        <a href="{{ route("employee.{$empRole}.categories.index") }}" class="btn-cancel-modern">
                            {{ __('messages.owner.products.categories.cancel') }}
                        </a>
                        <button type="submit" class="btn-submit-modern">
                            {{ __('messages.owner.products.categories.create_category') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Crop Modal --}}
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
                            <div class="alert-icon"><span class="material-symbols-outlined">info</span></div>
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

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            ImageCropper.init({
                id: 'category',
                inputId: 'images',
                previewId: 'imagePreview',
                modalId: 'cropModal',
                imageToCropId: 'imageToCrop',
                cropBtnId: 'cropBtn',
                containerId: 'profilePictureContainer',
                aspectRatio: 1,
                outputWidth: 800,
                outputHeight: 800
            });

            ImageRemoveHandler.init({
                removeBtnId: 'removeImageBtn',
                imageInputId: 'images',
                imagePreviewId: 'imagePreview',
                uploadPlaceholderId: 'uploadPlaceholder',
                confirmRemove: false
            });

            const form         = document.getElementById('categoryForm');
            const categoryName = document.getElementById('category_name');

            if (form && categoryName) {
                form.addEventListener('submit', function (e) {
                    if (categoryName.value.trim() === '') {
                        e.preventDefault();
                        alert('{{ __('messages.owner.products.categories.js_name_required') }}');
                        categoryName.focus();
                        return false;
                    }
                });
            }
        });
    </script>
@endpush