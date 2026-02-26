@extends('layouts.staff')

@section('title', __('messages.owner.products.master_products.create_product'))

@php
    // Dapatkan role employee (manager atau supervisor) untuk prefix route
    $staffRoutePrefix = strtolower(auth('employee')->user()->role);
@endphp

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <div class="page-header">

                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.master_products.create_product') }}
                    </h1>
                    <p class="page-subtitle">{{ __('messages.owner.products.master_products.add_product_subtitle') }}</p>
                </div>
                <a href="{{ route('employee.' . $staffRoutePrefix . '.products.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Product
                </a>
            </div>

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

            <div class="modern-card">
                {{-- MENGARAH KE STORE-CUSTOM MILIK STAFF --}}
                <form action="{{ route('employee.' . $staffRoutePrefix . '.products.store-custom') }}" method="POST"
                    enctype="multipart/form-data" id="productForm">
                    @csrf
                    <div class="card-body-modern">

                        <div class="profile-section">
                            <div class="profile-picture-wrapper">
                                <div class="profile-picture-container" id="productImageContainer">
                                    <div class="upload-placeholder" id="uploadPlaceholder">
                                        <span class="material-symbols-outlined">image</span>
                                        <span
                                            class="upload-text">{{ __('messages.owner.products.master_products.upload_text') }}</span>
                                    </div>
                                    <img id="imagePreview" class="profile-preview" alt="Product Preview">
                                    <button type="button" id="removeImageBtn" class="btn-remove btn-remove-top"
                                        style="display: none;">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                                <input type="file" name="images[]" id="productImage" accept="image/*"
                                    style="display: none;" required>
                                <small
                                    class="text-muted d-block text-center mt-2">{{ __('messages.owner.products.master_products.image_upload_help') }}</small>
                                @error('images')
                                    <div class="text-danger text-center mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="personal-info-fields">
                                <div class="section-header">
                                    <div class="section-icon section-icon-red">
                                        <span class="material-symbols-outlined">inventory_2</span>
                                    </div>
                                    <h3 class="section-title">
                                        {{ __('messages.owner.products.master_products.product_information') }}
                                    </h3>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.products.master_products.product_name') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="name" id="name"
                                                class="form-control-modern @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}"
                                                placeholder="{{ __('messages.owner.products.master_products.product_name_placeholder') }}"
                                                required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.products.master_products.category') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="select-wrapper">
                                                <select name="product_category" id="product_category"
                                                    class="form-control-modern @error('product_category') is-invalid @enderror"
                                                    required>
                                                    <option value="">
                                                        {{ __('messages.owner.products.master_products.select_category') }}
                                                    </option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ old('product_category') == $category->id ? 'selected' : '' }}>
                                                            {{ $category->category_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                                            </div>
                                            @error('product_category')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.products.master_products.price') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-wrapper">
                                                <p class="input-icon">Rp</p>
                                                <input type="text" name="price" id="price"
                                                    class="form-control-modern with-icon @error('price') is-invalid @enderror"
                                                    value="{{ old('price') }}"
                                                    placeholder="{{ __('messages.owner.products.master_products.product_price_placeholder') }}"
                                                    required>
                                            </div>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.products.master_products.promo') }}
                                            </label>
                                            <div class="select-wrapper">
                                                <select name="promotion_id" id="promotion_id"
                                                    class="form-control-modern @error('promotion_id') is-invalid @enderror">
                                                    <option value="">
                                                        {{ __('messages.owner.products.master_products.select_promotion') }}
                                                    </option>
                                                    @foreach ($promotions as $promotion)
                                                        <option value="{{ $promotion->id }}"
                                                            {{ old('promotion_id') == $promotion->id ? 'selected' : '' }}>
                                                            {{ $promotion->promotion_name }}
                                                            (@if ($promotion->promotion_type === 'percentage')
                                                                {{ number_format($promotion->promotion_value, 0, ',', '.') }}%
                                                                Off
                                                            @else
                                                                Rp
                                                                {{ number_format($promotion->promotion_value, 0, ',', '.') }}
                                                                Off
                                                            @endif)
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                                            </div>
                                            @error('promotion_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.products.master_products.description') }}
                                            </label>
                                            <textarea name="description" id="description" class="form-control-modern @error('description') is-invalid @enderror"
                                                rows="4" placeholder="{{ __('messages.owner.products.master_products.product_description_placeholder') }}">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        <div class="section-header-with-action">
                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">restaurant_menu</span>
                                </div>
                                <h3 class="section-title">{{ __('messages.owner.products.master_products.options') }}</h3>
                            </div>
                            <button type="button" class="btn-modern btn-secondary-modern btn-sm-modern"
                                onclick="addMenuOption()">
                                <span class="material-symbols-outlined">add</span>
                                {{ __('messages.owner.products.master_products.add_menu_option') }}
                            </button>
                        </div>

                        <div id="menu-options-container" class="menu-options-container">
                        </div>
                    </div>

                    <div class="card-footer-modern">
                        <button type="button" class="btn-cancel-modern"
                            onclick="window.location.href='{{ route('employee.' . $staffRoutePrefix . '.products.index') }}'">
                            {{ __('messages.owner.products.master_products.cancel') }}
                        </button>
                        <button type="submit" class="btn-submit-modern">
                            {{ __('messages.owner.products.master_products.create_product') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content modern-modal">
                    <div class="modal-header modern-modal-header">
                        <h5 class="modal-title">
                            <span class="material-symbols-outlined">crop</span>
                            {{ __('messages.owner.products.master_products.crop_modal_title') }}
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
                                <small>{{ __('messages.owner.products.master_products.crop_modal_instruction') }}</small>
                            </div>
                        </div>
                        <div class="img-container-crop">
                            <img id="imageToCrop" style="max-width: 100%;" alt="Image to crop">
                        </div>
                    </div>
                    <div class="modal-footer modern-modal-footer">
                        <button type="button" class="btn-cancel-modern" data-dismiss="modal">
                            <span class="material-symbols-outlined">close</span>
                            {{ __('messages.owner.products.master_products.cancel') }}
                        </button>
                        <button type="button" id="cropBtn" class="btn-submit-modern">
                            <span class="material-symbols-outlined">check</span>
                            {{ __('messages.owner.products.master_products.crop_save_btn') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Initialize Product Image Cropper (1:1 Square)
            ImageCropper.init({
                id: 'product',
                inputId: 'productImage',
                previewId: 'imagePreview',
                modalId: 'cropModal',
                imageToCropId: 'imageToCrop',
                cropBtnId: 'cropBtn',
                containerId: 'productImageContainer',
                aspectRatio: 1, // Square crop
                outputWidth: 800,
                outputHeight: 800
            });

            // Initialize Remove Image Handler
            ImageRemoveHandler.init({
                removeBtnId: 'removeImageBtn',
                imageInputId: 'productImage',
                imagePreviewId: 'imagePreview',
                uploadPlaceholderId: 'uploadPlaceholder',
                confirmRemove: false // No confirmation for create page
            });

            // ==== Price Formatting ====
            const priceInput = document.getElementById('price');
            if (priceInput) {
                priceInput.addEventListener('input', function(e) {
                    let value = this.value.replace(/[^,\d]/g, '');
                    this.value = new Intl.NumberFormat('id-ID').format(value);
                });
            }

            // ==== Menu Options System ====
            let menuIndex = 0;

            window.addMenuOption = function() {
                menuIndex++;
                const container = document.getElementById('menu-options-container');

                const html = `
            <div class="menu-option-card" data-menu-index="${menuIndex}">
                <div class="menu-option-header">
                    <div class="menu-option-title">
                        <span class="material-symbols-outlined">tune</span>
                        <h4>{{ __('messages.owner.products.master_products.category_option_header') }} ${menuIndex}</h4>
                    </div>
                    <button type="button" class="btn-remove" onclick="removeMenuOption(this)">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group-modern">
                            <label class="form-label-modern">{{ __('messages.owner.products.master_products.menu_name_label') }}</label>
                            <input type="text" name="menu_options[${menuIndex}][name]" 
                                class="form-control-modern" 
                                placeholder="{{ __('messages.owner.products.master_products.menu_name_placeholder') }}" 
                                required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group-modern">
                            <label class="form-label-modern">{{ __('messages.owner.products.master_products.menu_description_label') }}</label>
                            <input type="text" name="menu_options[${menuIndex}][description]" 
                                class="form-control-modern" 
                                placeholder="{{ __('messages.owner.products.master_products.menu_description_placeholder') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group-modern">
                            <label class="form-label-modern">{{ __('messages.owner.products.master_products.provision_type_label') }}</label>
                            <div class="select-wrapper">
                                <select name="menu_options[${menuIndex}][provision]" 
                                    class="form-control-modern provision-select" 
                                    data-index="${menuIndex}"
                                    required>
                                    <option value="">{{ __('messages.owner.products.master_products.select_provision_placeholder') }}</option>
                                    <option value="OPTIONAL">{{ __('messages.owner.products.master_products.provision_optional') }}</option>
                                    <option value="OPTIONAL MAX">{{ __('messages.owner.products.master_products.provision_optional_max') }}</option>
                                    <option value="MAX">{{ __('messages.owner.products.master_products.provision_max') }}</option>
                                    <option value="EXACT">{{ __('messages.owner.products.master_products.provision_exact') }}</option>
                                    <option value="MIN">{{ __('messages.owner.products.master_products.provision_min') }}</option>
                                </select>
                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 provision-value-col" id="provision-value-${menuIndex}">
                        <div class="form-group-modern">
                            <label class="form-label-modern">{{ __('messages.owner.products.master_products.amount_label') }}</label>
                            <input type="number" 
                                name="menu_options[${menuIndex}][provision_value]" 
                                class="form-control-modern" 
                                min="0" 
                                value="0" 
                                required>
                        </div>
                    </div>
                </div>

                <div class="options-list" id="options-container-${menuIndex}"></div>

                <button type="button" class="btn-modern btn-secondary-modern btn-sm-modern" onclick="addOption(${menuIndex})">
                    <span class="material-symbols-outlined">add</span>
                    {{ __('messages.owner.products.master_products.add_option_btn') }}
                </button>
            </div>
        `;

                container.insertAdjacentHTML('beforeend', html);
            };

            window.removeMenuOption = function(button) {
                button.closest('.menu-option-card').remove();
            };

            window.addOption = function(menuIndex) {
                const container = document.getElementById('options-container-' + menuIndex);
                const optionIndex = container.children.length + 1;

                const html = `
            <div class="option-item">
                <div class="option-item-header">
                    <span class="option-number">${optionIndex}</span>
                    <button type="button" class="btn-remove" onclick="removeOption(this)">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="form-group-modern">
                            <label class="form-label-modern">{{ __('messages.owner.products.master_products.option_name_label') }}</label>
                            <input type="text" 
                                name="menu_options[${menuIndex}][options][${optionIndex}][name]" 
                                class="form-control-modern" 
                                placeholder="{{ __('messages.owner.products.master_products.option_name_placeholder') }}" 
                                required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group-modern">
                            <label class="form-label-modern">{{ __('messages.owner.products.master_products.option_price_label') }}</label>
                            <div class="input-wrapper">
                                <span class="input-icon">Rp</span>
                                <input type="number" 
                                    name="menu_options[${menuIndex}][options][${optionIndex}][price]" 
                                    class="form-control-modern with-icon" 
                                    min="0" 
                                    placeholder="0" 
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-modern">
                            <label class="form-label-modern">{{ __('messages.owner.products.master_products.option_description_label') }}</label>
                            <input type="text" 
                                name="menu_options[${menuIndex}][options][${optionIndex}][description]" 
                                class="form-control-modern" 
                                placeholder="{{ __('messages.owner.products.master_products.option_description_placeholder') }}">
                        </div>
                    </div>
                </div>
            </div>
        `;

                container.insertAdjacentHTML('beforeend', html);
            };

            window.removeOption = function(button) {
                button.closest('.option-item').remove();
            };

            // ==== Provision Toggle Logic ====
            function toggleProvisionValue(index, provisionValue) {
                const valueCol = document.getElementById(`provision-value-${index}`);
                if (!valueCol) return;

                const input = valueCol.querySelector('input[name$="[provision_value]"]');
                const hide = (provisionValue === 'OPTIONAL');

                if (hide) {
                    valueCol.style.display = 'none';
                    if (input) {
                        input.disabled = true;
                        input.required = false;
                        input.value = 0;
                    }
                } else {
                    valueCol.style.display = '';
                    if (input) {
                        input.disabled = false;
                        input.required = true;
                    }
                }
            }

            // Event delegation for dynamically added provisions
            document.getElementById('menu-options-container')?.addEventListener('change', function(e) {
                if (e.target.matches('.provision-select')) {
                    const index = e.target.dataset.index;
                    toggleProvisionValue(index, e.target.value);
                }
            });
        });
    </script>
@endpush