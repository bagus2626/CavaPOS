@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.movements_adjustment.title'))
@section('page_title', __('messages.owner.products.stocks.movements_adjustment.page_title'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <!-- Header Section -->
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.stocks.movements_adjustment.page_title') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.products.stocks.movements_adjustment.subtitle') }}</p>
                </div>
               <a href="{{ route('owner.user-owner.stocks.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.products.stocks.back') }}
                </a>
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
                <form action="{{ route('owner.user-owner.stocks.movements.store') }}" method="POST" id="stockMovementForm">
                    @csrf
                    <input type="hidden" name="movement_type" value="adjustment">

                    <div class="card-body-modern">
                        <!-- Transaction Details Section -->
                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">tune</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.products.stocks.movements_adjustment.card_transaction_title') }}</h3>
                        </div>

                        <div class="row g-4">
                            <!-- Location -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.stocks.movements_adjustment.location_from_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="location" id="location"
                                            class="form-control-modern @error('location') is-invalid @enderror"
                                            required>
                                            <option value="_owner">
                                                {{ __('messages.owner.products.stocks.movements_adjustment.location_owner_option') }}
                                            </option>
                                            @foreach ($partners as $partner)
                                                <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="material-symbols-outlined select-arrow">location_on</span>
                                    </div>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.stocks.movements_adjustment.category_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    
                                    <!-- Select Dropdown -->
                                    <div id="category_select_mode">
                                        <div class="select-wrapper">
                                            <select name="category" id="categorySelect"
                                                class="form-control-modern @error('category') is-invalid @enderror"
                                                required>
                                                <option value="">
                                                    {{ __('messages.owner.products.stocks.movements_adjustment.select_category_placeholder') }}
                                                </option>
                                                @if (isset($customCategories) && count($customCategories) > 0)
                                                    <optgroup label="Kategori Kustom">
                                                        @foreach ($customCategories as $cat)
                                                            <option value="{{ $cat }}">{{ $cat }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>

                                        @error('category')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                        <!-- Add New Category Button -->
                                        <button type="button" class="btn-modern btn-primary-modern btn-sm-modern mt-3" id="btn-add-category">
                                            <span class="material-symbols-outlined">add_circle</span>
                                            <span>Buat Kategori Baru</span>
                                        </button>
                                    </div>

                                    <!-- Input Text Mode (Hidden by default) -->
                                    <div id="category_input_mode" style="display: none;">
                                        <input type="text" name="category" id="categoryInput"
                                            class="form-control-modern"
                                            placeholder="{{ __('messages.owner.products.stocks.movements_adjustment.new_category_placeholder') }}"
                                            disabled>
                                        
                                        <button type="button" class="btn-modern btn-secondary-modern btn-sm-modern mt-3" id="btn-cancel-category">
                                            {{ __('messages.owner.products.stocks.movements_adjustment.cancel_button') ?? 'Batal' }}
                                        </button>
                                        
                                        {{-- <small class="text-muted d-block mt-2" id="categoryHelp">
                                            Masukkan nama kategori baru
                                        </small> --}}
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-md-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.stocks.movements_adjustment.notes_label') }}
                                    </label>
                                    <input type="text" name="notes" id="notes"
                                        class="form-control-modern @error('notes') is-invalid @enderror"
                                        placeholder="{{ __('messages.owner.products.stocks.movements_adjustment.notes_placeholder') }}">
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="section-divider"></div>

                        <!-- Items Section -->
                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">inventory_2</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.products.stocks.movements_adjustment.card_items_title') }}</h3>
                        </div>

                        <div id="item-repeater-container">
                            <!-- First Item Row -->
                            <div class="row repeater-item g-3 mb-3">
                                <div class="col-md-4">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.movements_adjustment.item_stock_label') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="select-wrapper">
                                            <select name="items[0][stock_id]" class="form-control-modern stock-select" required>
                                                <option value="">
                                                    {{ __('messages.owner.products.stocks.movements_adjustment.item_stock_placeholder') }}
                                                </option>
                                                @foreach ($stocks as $stock)
                                                    <option value="{{ $stock->id }}"
                                                        data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                                                        data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}"
                                                        data-current-qty="{{ $stock->display_quantity ?? 0 }}"
                                                        data-current-unit="{{ $stock->displayUnit->unit_name ?? __('messages.owner.products.stocks.movements_adjustment.current_stock_unit_default') }}">
                                                        {{ $stock->stock_name }}
                                                        ({{ $stock->partner->name ?? __('messages.owner.products.stocks.movements_adjustment.location_owner_option') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>
                                        <div class="current-stock-info text-muted small mt-2" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.movements_adjustment.quantity_label') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" name="items[0][new_quantity]"
                                            class="form-control-modern new-quantity-input" step="0.01"
                                            placeholder="Qty Baru" required>
                                        <input type="hidden" name="items[0][current_quantity]" class="current-quantity-hidden">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.movements_adjustment.unit_label') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control-modern unit-name-display" readonly
                                            placeholder="Unit" style="background-color: #f3f4f6;">
                                        <input type="hidden" name="items[0][unit_id]" class="unit-id-input">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.movements_adjustment.change') }}
                                        </label>
                                        <div class="adjustment-info adjustment-info-neutral" style="display: none;">
                                            <div class="adjustment-row">
                                                <span class="adjustment-label">{{ __('messages.owner.products.stocks.movements_adjustment.type_label') }}:</span>
                                                <span class="adjustment-type">-</span>
                                            </div>
                                            <div class="adjustment-row">
                                                <span class="adjustment-label">{{ __('messages.owner.products.stocks.movements_adjustment.quantity') }}:</span>
                                                <span class="adjustment-amount">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <!-- First row has no delete button -->
                                </div>
                            </div>
                        </div>

                        <button type="button" id="btn-add-item" class="btn-modern btn-sm-modern btn-secondary-modern" disabled>
                            <span class="material-symbols-outlined">add_circle</span>
                            {{ __('messages.owner.products.stocks.movements_adjustment.add_item_button') }}
                        </button>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer-modern">
                        <a href="{{ route('owner.user-owner.stocks.index') }}" class="btn-cancel-modern">
                            {{ __('messages.owner.user_management.employees.cancel') }}
                        </a>
                        <button type="submit" class="btn-submit-modern" id="btn-save" disabled>
                            {{ __('messages.owner.products.stocks.movements_adjustment.submit_button') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Item Template -->
    <template id="item-repeater-template">
        <div class="row repeater-item g-3 mb-3 position-relative">
            <div class="col-md-4">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        {{ __('messages.owner.products.stocks.movements_adjustment.item_stock_label') }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="select-wrapper">
                        <select name="items[__INDEX__][stock_id]" class="form-control-modern stock-select" required>
                            <option value="">
                                {{ __('messages.owner.products.stocks.movements_adjustment.item_stock_placeholder') }}
                            </option>
                            @foreach ($stocks as $stock)
                                <option value="{{ $stock->id }}" 
                                    data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                                    data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}"
                                    data-current-qty="{{ $stock->display_quantity ?? 0 }}"
                                    data-current-unit="{{ $stock->displayUnit->unit_name ?? __('messages.owner.products.stocks.movements_adjustment.current_stock_unit_default') }}">
                                    {{ $stock->stock_name }}
                                    ({{ $stock->partner->name ?? __('messages.owner.products.stocks.movements_adjustment.location_owner_option') }})
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                    </div>
                    <div class="current-stock-info text-muted small mt-2" style="display: none;"></div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        {{ __('messages.owner.products.stocks.movements_adjustment.quantity_label') }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="items[__INDEX__][new_quantity]"
                        class="form-control-modern new-quantity-input" step="0.01"
                        placeholder="Qty Baru" required>
                    <input type="hidden" name="items[__INDEX__][current_quantity]" class="current-quantity-hidden">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        {{ __('messages.owner.products.stocks.movements_adjustment.unit_label') }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control-modern unit-name-display" readonly
                        placeholder="Unit" style="background-color: #f3f4f6;">
                    <input type="hidden" name="items[__INDEX__][unit_id]" class="unit-id-input">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        {{ __('messages.owner.products.stocks.movements_adjustment.change') }}
                    </label>
                    <div class="adjustment-info adjustment-info-neutral" style="display: none;">
                        <div class="adjustment-row">
                            <span class="adjustment-label">Tipe:</span>
                            <span class="adjustment-type">-</span>
                        </div>
                        <div class="adjustment-row">
                            <span class="adjustment-label">Jumlah:</span>
                            <span class="adjustment-amount">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn-remove btn-remove-top">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('item-repeater-container');
            const template = document.getElementById('item-repeater-template');
            const locationSelect = document.getElementById('location');
            const form = document.getElementById('stockMovementForm');
            const saveButton = document.getElementById('btn-save');
            const btnAddItem = document.getElementById('btn-add-item');

            // Category elements
            const categorySelectMode = document.getElementById('category_select_mode');
            const categoryInputMode = document.getElementById('category_input_mode');
            const categorySelect = document.getElementById('categorySelect');
            const categoryInput = document.getElementById('categoryInput');
            const btnAddCategory = document.getElementById('btn-add-category');
            const btnCancelCategory = document.getElementById('btn-cancel-category');
            const categoryHelp = document.getElementById('categoryHelp');

            let itemIndex = 1;
            let isCategoryInputMode = false;

            const translations = {
                currentStockPrefix: "{{ __('messages.owner.products.stocks.movements_adjustment.current_stock_prefix') }}"
            };

            // Category logic
            btnAddCategory.addEventListener('click', function () {
                isCategoryInputMode = true;
                categorySelectMode.style.display = 'none';
                categoryInputMode.style.display = 'block';
                
                categorySelect.disabled = true;
                categorySelect.required = false;
                
                categoryInput.disabled = false;
                categoryInput.required = true;
                categoryInput.focus();
                
                checkFormValidity();
            });

            btnCancelCategory.addEventListener('click', function () {
                isCategoryInputMode = false;
                categoryInputMode.style.display = 'none';
                categorySelectMode.style.display = 'block';
                
                categoryInput.value = '';
                categoryInput.disabled = true;
                categoryInput.required = false;
                categoryInput.classList.remove('is-valid', 'is-invalid');

                const feedback = categoryInput.parentElement.querySelector('.invalid-feedback, .valid-feedback');
                if (feedback) feedback.remove();

                categorySelect.disabled = false;
                categorySelect.required = true;
                categorySelect.selectedIndex = 0;

                checkFormValidity();
            });

            // Category validation
            const existingCategories = Array.from(categorySelect.options)
                .filter(opt => opt.value !== '')
                .map(opt => opt.value.toLowerCase().trim());

            categoryInput.addEventListener('input', function () {
                const inputValue = this.value.trim();
                const inputLower = inputValue.toLowerCase();

                this.classList.remove('is-valid', 'is-invalid');
                const existingFeedback = this.parentElement.querySelector('.invalid-feedback, .valid-feedback');
                if (existingFeedback) existingFeedback.remove();

                if (inputValue === '') {
                    checkFormValidity();
                    return;
                }

                const validPattern = /^[a-zA-Z0-9\s_-]+$/;
                if (!validPattern.test(inputValue)) {
                    this.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback d-block';
                    feedback.textContent = '{{ __('messages.owner.products.stocks.movements_adjustment.category_invalid_characters') }}';
                    this.parentElement.appendChild(feedback);
                    checkFormValidity();
                    return;
                }

                if (existingCategories.includes(inputLower)) {
                    this.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback d-block';
                    feedback.textContent = '{{ __('messages.owner.products.stocks.movements_adjustment.category_already_exists') }}';
                    this.parentElement.appendChild(feedback);
                    checkFormValidity();
                    return;
                }

                this.classList.add('is-valid');
                const feedback = document.createElement('div');
                feedback.className = 'valid-feedback d-block';
                feedback.textContent = '{{ __('messages.owner.products.stocks.movements_adjustment.category_available') }}';
                this.parentElement.appendChild(feedback);
                checkFormValidity();
            });

            // Filter stocks by location
            function filterStocksByLocation() {
                const selectedLocation = locationSelect.value;
                const stockSelects = document.querySelectorAll('.stock-select');

                stockSelects.forEach(select => {
                    const currentVal = select.value;
                    let isCurrentValValid = false;

                    Array.from(select.options).forEach(option => {
                        if (option.value === "") return;
                        const stockLocation = option.getAttribute('data-location-id');
                        if (stockLocation === selectedLocation) {
                            option.hidden = false;
                            option.disabled = false;
                            if (option.value === currentVal) isCurrentValValid = true;
                        } else {
                            option.hidden = true;
                            option.disabled = true;
                        }
                    });

                    if (currentVal !== "" && !isCurrentValValid) {
                        select.value = "";
                        select.dispatchEvent(new Event('change'));
                    }
                });
            }

            // Update unit and quantity info
            function updateUnitDropdown(stockSelectElement) {
                const selectedOption = stockSelectElement.options[stockSelectElement.selectedIndex];
                const row = stockSelectElement.closest('.repeater-item');

                const unitNameDisplay = row.querySelector('.unit-name-display');
                const unitIdInput = row.querySelector('.unit-id-input');
                const infoBox = row.querySelector('.current-stock-info');
                const newQuantityInput = row.querySelector('.new-quantity-input');
                const currentQuantityHidden = row.querySelector('.current-quantity-hidden');

                if (infoBox) {
                    infoBox.textContent = '';
                    infoBox.style.display = 'none';
                }
                newQuantityInput.value = '';
                currentQuantityHidden.value = '';
                unitNameDisplay.value = '';
                unitIdInput.value = '';

                if (!selectedOption || !selectedOption.value) {
                    calculateAdjustment(row);
                    return;
                }

                const displayUnitId = selectedOption.getAttribute('data-display-unit-id');
                const currentQty = parseFloat(selectedOption.getAttribute('data-current-qty')) || 0;
                const currentUnit = selectedOption.getAttribute('data-current-unit');

                unitNameDisplay.value = currentUnit;
                unitIdInput.value = displayUnitId;
                currentQuantityHidden.value = currentQty;
                newQuantityInput.value = currentQty.toFixed(2);

                if (infoBox) {
                    infoBox.textContent = translations.currentStockPrefix + ' ' + currentQty.toFixed(2) + ' ' + currentUnit;
                    infoBox.style.display = 'block';
                }

                calculateAdjustment(row);
            }

            // Calculate adjustment
            function calculateAdjustment(row) {
                const newQuantityInput = row.querySelector('.new-quantity-input');
                const currentQuantityHidden = row.querySelector('.current-quantity-hidden');
                const adjustmentInfo = row.querySelector('.adjustment-info');
                const adjustmentType = row.querySelector('.adjustment-type');
                const adjustmentAmount = row.querySelector('.adjustment-amount');
                const unitNameDisplay = row.querySelector('.unit-name-display');

                const newQty = parseFloat(newQuantityInput.value) || 0;
                const currentQty = parseFloat(currentQuantityHidden.value) || 0;
                const difference = newQty - currentQty;

                if (newQuantityInput.value === '' || !unitNameDisplay.value) {
                    adjustmentInfo.style.display = 'none';
                    return;
                }

                const unitName = unitNameDisplay.value;

                adjustmentInfo.classList.remove('adjustment-info-success', 'adjustment-info-danger', 'adjustment-info-neutral');

                if (difference > 0) {
                    adjustmentInfo.classList.add('adjustment-info-success');
                    adjustmentType.textContent = '{{ __('messages.owner.products.stocks.movements_adjustment.type_increase') }}';
                    adjustmentAmount.textContent = '+' + difference.toFixed(2) + ' ' + unitName;
                } else if (difference < 0) {
                    adjustmentInfo.classList.add('adjustment-info-danger');
                    adjustmentType.textContent = '{{ __('messages.owner.products.stocks.movements_adjustment.type_decrease') }}';
                    adjustmentAmount.textContent = Math.abs(difference).toFixed(2) + ' ' + unitName;
                } else {
                    adjustmentInfo.classList.add('adjustment-info-neutral');
                    adjustmentType.textContent = '{{ __('messages.owner.products.stocks.movements_adjustment.type_no_change') }}';
                    adjustmentAmount.textContent = '0 ' + unitName;
                }

                adjustmentInfo.style.display = 'block';
            }

            // Form validation
            function isCategoryValid() {
                if (!categorySelect.disabled) {
                    return categorySelect.value !== '';
                }
                if (!categoryInput.disabled) {
                    return categoryInput.value.trim() !== '' && categoryInput.classList.contains('is-valid');
                }
                return false;
            }

            function areItemsValid() {
                const rows = document.querySelectorAll('.repeater-item');
                if (rows.length === 0) return false;

                for (let row of rows) {
                    const stock = row.querySelector('.stock-select');
                    const qty = row.querySelector('.new-quantity-input');
                    const unitId = row.querySelector('.unit-id-input');

                    if (!stock.value || !qty.value || !unitId.value) {
                        return false;
                    }
                }
                return true;
            }

            function checkFormValidity() {
                const requiredFields = form.querySelectorAll('[required]');
                let allRequiredFilled = true;

                requiredFields.forEach(field => {
                    if (!field.disabled && !field.value) {
                        allRequiredFilled = false;
                    }
                });

                const hasInvalid = form.querySelector('.is-invalid') !== null;

                if (allRequiredFilled && !hasInvalid && isCategoryValid() && areItemsValid()) {
                    saveButton.disabled = false;
                    btnAddItem.disabled = false;
                } else {
                    saveButton.disabled = true;
                    btnAddItem.disabled = true;
                }
            }

            // Event listeners
            locationSelect.addEventListener('change', function() {
                filterStocksByLocation();
                checkFormValidity();
            });

            btnAddItem.addEventListener('click', function () {
                let templateHTML = template.innerHTML.replace(/__INDEX__/g, itemIndex);
                const newRow = document.createElement('div');
                newRow.innerHTML = templateHTML;
                container.appendChild(newRow.firstElementChild);
                filterStocksByLocation();
                itemIndex++;
                setTimeout(checkFormValidity, 50);
            });

            container.addEventListener('click', function (e) {
                if (e.target.closest('.btn-remove')) {
                    e.target.closest('.repeater-item').remove();
                    setTimeout(checkFormValidity, 50);
                }
            });

            container.addEventListener('change', function (e) {
                if (e.target && e.target.classList.contains('stock-select')) {
                    updateUnitDropdown(e.target);
                }
            });

            container.addEventListener('input', function (e) {
                if (e.target && e.target.classList.contains('new-quantity-input')) {
                    const row = e.target.closest('.repeater-item');
                    calculateAdjustment(row);
                }
            });

            form.addEventListener('input', checkFormValidity);
            form.addEventListener('change', checkFormValidity);

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '{{ __('messages.owner.products.stocks.movements_adjustment.confirm_title') }}',
                    text: '{{ __('messages.owner.products.stocks.movements_adjustment.confirm_text') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __('messages.owner.products.stocks.movements_adjustment.confirm_button') }}',
                    cancelButtonText: '{{ __('messages.owner.products.stocks.movements_adjustment.cancel_button') }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            // Initialize
            filterStocksByLocation();
            const firstStockSelect = container.querySelector('.stock-select');
            if (firstStockSelect && firstStockSelect.value) {
                updateUnitDropdown(firstStockSelect);
            }
            checkFormValidity();
        });
    </script>

    @endsection