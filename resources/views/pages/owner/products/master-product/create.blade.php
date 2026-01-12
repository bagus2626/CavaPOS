@extends('layouts.owner')

@section('title', __('messages.owner.products.master_products.create_product'))
@section('page_title', __('messages.owner.products.master_products.create_new_master_product'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <!-- Header Section -->
            <div class="page-header">
                {{-- <a href="{{ route('owner.user-owner.master-products.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.products.master_products.back_to_products') }}
                </a> --}}
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.master_products.create_new_master_product') }}
                    </h1>
                    <p class="page-subtitle">Add a new product to your menu catalog</p>
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
                <form action="{{ route('owner.user-owner.master-products.store') }}" method="POST"
                    enctype="multipart/form-data" id="productForm">
                    @csrf
                    <div class="card-body-modern">

                        <!-- Product Image & Basic Info Section -->
                        <div class="profile-section">
                            <!-- Product Image Upload -->
                            <div class="profile-picture-wrapper">
                                <div class="profile-picture-container" id="productImageContainer">
                                    <div class="upload-placeholder" id="uploadPlaceholder">
                                        <span class="material-symbols-outlined">image</span>
                                        <span class="upload-text">Upload</span>
                                    </div>
                                    <img id="imagePreview" class="profile-preview" alt="Product Preview">
                                    <button type="button" id="removeImageBtn" class="btn-remove btn-remove-top" style="display: none;">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>                                    
                                </div>
                                <input type="file" name="images[]" id="productImage" accept="image/*" style="display: none;"
                                    required>
                                <small class="text-muted d-block text-center mt-2">JPG, PNG, WEBP. Max 2 MB</small>
                                @error('images')
                                    <div class="text-danger text-center mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Basic Product Information -->
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
                                    <!-- Product Name -->
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

                                    <!-- Category -->
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
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('product_category') == $category->id ? 'selected' : '' }}>
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

                                    <!-- Price -->
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

                                    <!-- Promotion -->
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
                                                    @foreach($promotions as $promotion)
                                                        <option value="{{ $promotion->id }}" {{ old('promotion_id') == $promotion->id ? 'selected' : '' }}>
                                                            {{ $promotion->promotion_name }}
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

                                    <!-- Description -->
                                    <div class="col-12">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.products.master_products.description') }}
                                            </label>
                                            <textarea name="description" id="description"
                                                class="form-control-modern @error('description') is-invalid @enderror"
                                                rows="4"
                                                placeholder="{{ __('messages.owner.products.master_products.product_description_placeholder') }}">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="section-divider"></div>

                        <!-- Menu Options Section -->
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
                            <!-- Dynamic option forms will be added here -->
                        </div>
                    </div>
                    
                    <!-- Card Footer -->
                    <div class="card-footer-modern">
                        <button type="button" class="btn-cancel-modern"
                            onclick="window.location.href='{{ route('owner.user-owner.master-products.index') }}'">
                            {{ __('messages.owner.products.master_products.cancel') }}
                        </button>
                        <button type="submit" class="btn-submit-modern">
                            {{ __('messages.owner.products.master_products.create_product') }}
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
                            Crop Product Image
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
            priceInput.addEventListener('input', function (e) {
                let value = this.value.replace(/[^,\d]/g, '');
                this.value = new Intl.NumberFormat('id-ID').format(value);
            });
        }

        // ==== Menu Options System ====
        let menuIndex = 0;

        window.addMenuOption = function () {
            menuIndex++;
            const container = document.getElementById('menu-options-container');

            const html = `
            <div class="menu-option-card" data-menu-index="${menuIndex}">
                <div class="menu-option-header">
                    <div class="menu-option-title">
                        <span class="material-symbols-outlined">tune</span>
                        <h4>Category Option ${menuIndex}</h4>
                    </div>
                    <button type="button" class="btn-remove" onclick="removeMenuOption(this)">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="row g-3">
                    <!-- Menu Name -->
                    <div class="col-md-4">
                        <div class="form-group-modern">
                            <label class="form-label-modern">Menu Name</label>
                            <input type="text" name="menu_options[${menuIndex}][name]" 
                                class="form-control-modern" 
                                placeholder="Enter menu name" 
                                required>
                        </div>
                    </div>

                    <!-- Menu Description -->
                    <div class="col-md-4">
                        <div class="form-group-modern">
                            <label class="form-label-modern">Menu Description</label>
                            <input type="text" name="menu_options[${menuIndex}][description]" 
                                class="form-control-modern" 
                                placeholder="Brief description">
                        </div>
                    </div>

                    <!-- Provision Type -->
                    <div class="col-md-2">
                        <div class="form-group-modern">
                            <label class="form-label-modern">Pilihan</label>
                            <div class="select-wrapper">
                                <select name="menu_options[${menuIndex}][provision]" 
                                    class="form-control-modern provision-select" 
                                    data-index="${menuIndex}"
                                    required>
                                    <option value="">Select provision</option>
                                    <option value="OPTIONAL">Optional</option>
                                    <option value="OPTIONAL MAX">Optional Max</option>
                                    <option value="MAX">Max</option>
                                    <option value="EXACT">Exact</option>
                                    <option value="MIN">Min</option>
                                </select>
                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                            </div>
                        </div>
                    </div>

                    <!-- Provision Value -->
                    <div class="col-md-2 provision-value-col" id="provision-value-${menuIndex}">
                        <div class="form-group-modern">
                            <label class="form-label-modern">Amount</label>
                            <input type="number" 
                                name="menu_options[${menuIndex}][provision_value]" 
                                class="form-control-modern" 
                                min="0" 
                                value="0" 
                                required>
                        </div>
                    </div>
                </div>

                <!-- Options List -->
                <div class="options-list" id="options-container-${menuIndex}"></div>

                <!-- Add Option Button -->
                <button type="button" class="btn-modern btn-secondary-modern btn-sm-modern" onclick="addOption(${menuIndex})">
                    <span class="material-symbols-outlined">add</span>
                    Add Option
                </button>
            </div>
        `;

            container.insertAdjacentHTML('beforeend', html);
        };

        window.removeMenuOption = function (button) {
            button.closest('.menu-option-card').remove();
        };

        window.addOption = function (menuIndex) {
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
                            <label class="form-label-modern">Option Name</label>
                            <input type="text" 
                                name="menu_options[${menuIndex}][options][${optionIndex}][name]" 
                                class="form-control-modern" 
                                placeholder="e.g. Small, Medium, Large" 
                                required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group-modern">
                            <label class="form-label-modern">Price</label>
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
                            <label class="form-label-modern">Description</label>
                            <input type="text" 
                                name="menu_options[${menuIndex}][options][${optionIndex}][description]" 
                                class="form-control-modern" 
                                placeholder="Optional description">
                        </div>
                    </div>
                </div>
            </div>
        `;

            container.insertAdjacentHTML('beforeend', html);
        };

        window.removeOption = function (button) {
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
        document.getElementById('menu-options-container')?.addEventListener('change', function (e) {
            if (e.target.matches('.provision-select')) {
                const index = e.target.dataset.index;
                toggleProvisionValue(index, e.target.value);
            }
        });
    });
</script>
@endpush