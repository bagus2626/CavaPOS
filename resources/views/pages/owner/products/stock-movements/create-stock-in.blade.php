@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.movements_create_in.title'))
@section('page_title', __('messages.owner.products.stocks.movements_create_in.page_title'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.stocks.movements_create_in.page_title') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.products.stocks.movements_create_in.subtitle') }}</p>
                </div>
               <a href="{{ route('owner.user-owner.stocks.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.products.stocks.back') }}
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                    </div>
                    <div class="alert-content">
                        {{-- Pastikan key recheck_input tersedia di user_management atau buat global --}}
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
                <form action="{{ route('owner.user-owner.stocks.movements.store') }}" method="POST" id="stockMovementForm">
                    @csrf
                    <input type="hidden" name="movement_type" value="in">

                    <div class="card-body-modern">
                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">receipt_long</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.products.stocks.movements_create_in.card_transaction_title') }}</h3>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.stocks.movements_create_in.location_to_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="location_to" id="location_to"
                                            class="form-control-modern @error('location_to') is-invalid @enderror"
                                            required>
                                            <option value="_owner">
                                                {{ __('messages.owner.products.stocks.movements_create_in.location_owner_option') }}
                                            </option>
                                            @foreach ($partners as $partner)
                                                <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="material-symbols-outlined select-arrow">location_on</span>
                                    </div>
                                    @error('location_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.stocks.movements_create_in.category_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="category" id="category"
                                            class="form-control-modern @error('category') is-invalid @enderror"
                                            required>
                                            <option value="purchase">
                                                {{ __('messages.owner.products.stocks.movements_create_in.category_purchase') }}
                                            </option>
                                        </select>
                                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                                    </div>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.stocks.movements_create_in.notes_label') }}
                                    </label>
                                    <input type="text" name="notes" id="notes"
                                        class="form-control-modern @error('notes') is-invalid @enderror"
                                        placeholder="{{ __('messages.owner.products.stocks.movements_create_in.notes_placeholder') }}">
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">inventory_2</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.products.stocks.movements_create_in.card_items_title') }}</h3>
                        </div>

                        <div id="item-repeater-container">
                            <div class="row repeater-item g-3 mb-3">
                                <div class="col-md-4">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.movements_create_in.item_stock_label') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="select-wrapper">
                                            <select name="items[0][stock_id]" class="form-control-modern stock-select" required>
                                                <option value="">
                                                    {{ __('messages.owner.products.stocks.movements_create_in.item_stock_placeholder') }}
                                                </option>
                                                @foreach ($stocks as $stock)
                                                    <option value="{{ $stock->id }}"
                                                        data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                                                        data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                                                        data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}">
                                                        {{ $stock->stock_name }}
                                                        ({{ $stock->partner->name ?? __('messages.owner.products.stocks.movements_create_in.location_owner_option') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.movements_create_in.quantity_label') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" name="items[0][quantity]" class="form-control-modern quantity-input"
                                            step="0.01"
                                            placeholder="{{ __('messages.owner.products.stocks.movements_create_in.quantity_placeholder') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.movements_create_in.unit_label') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="select-wrapper">
                                            <select name="items[0][unit_id]" class="form-control-modern unit-select" required>
                                                <option value="">
                                                    {{ __('messages.owner.products.stocks.movements_create_in.unit_placeholder_no_stock') }}
                                                </option>
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.movements_create_in.unit_price_label') }}
                                        </label>
                                        <input type="number" name="items[0][unit_price]" class="form-control-modern" step="0.01"
                                            placeholder="{{ __('messages.owner.products.stocks.movements_create_in.unit_price_placeholder') }}">
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    </div>
                            </div>
                        </div>

                        <button type="button" id="btn-add-item" class="btn-modern btn-sm-modern btn-secondary-modern" disabled>
                            <span class="material-symbols-outlined">add_circle</span>
                            {{ __('messages.owner.products.stocks.movements_create_in.add_item_button') }}
                        </button>
                    </div>

                    <div class="card-footer-modern">
                        <a href="{{ route('owner.user-owner.stocks.index') }}" class="btn-cancel-modern">
                            {{ __('messages.owner.products.stocks.movements_create_in.cancel') }}
                        </a>
                        <button type="submit" class="btn-submit-modern" id="btn-submit" disabled>
                            {{ __('messages.owner.products.stocks.movements_create_in.submit_button') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="item-repeater-template">
        <div class="row repeater-item g-3 mb-3 position-relative">
            <div class="col-md-4">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        {{ __('messages.owner.products.stocks.movements_create_in.item_stock_label') }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="select-wrapper">
                        <select name="items[__INDEX__][stock_id]" class="form-control-modern stock-select" required>
                            <option value="">
                                {{ __('messages.owner.products.stocks.movements_create_in.item_stock_placeholder') }}
                            </option>
                            @foreach ($stocks as $stock)
                                <option value="{{ $stock->id }}" data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                                    data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                                    data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}">
                                    {{ $stock->stock_name }}
                                    ({{ $stock->partner->name ?? __('messages.owner.products.stocks.movements_create_in.location_owner_option') }})
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        {{ __('messages.owner.products.stocks.movements_create_in.quantity_label') }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="items[__INDEX__][quantity]" class="form-control-modern quantity-input" step="0.01"
                        placeholder="{{ __('messages.owner.products.stocks.movements_create_in.quantity_placeholder') }}"
                        required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        {{ __('messages.owner.products.stocks.movements_create_in.unit_label') }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="select-wrapper">
                        <select name="items[__INDEX__][unit_id]" class="form-control-modern unit-select" required>
                            <option value="">
                                {{ __('messages.owner.products.stocks.movements_create_in.unit_placeholder_no_stock') }}
                            </option>
                        </select>
                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        {{ __('messages.owner.products.stocks.movements_create_in.unit_price_label_with_unit') }}
                    </label>
                    <input type="number" name="items[__INDEX__][unit_price]" class="form-control-modern" step="0.01"
                        placeholder="{{ __('messages.owner.products.stocks.movements_create_in.unit_price_placeholder') }}">
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
        const allUnits = @json($allUnits);

        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('item-repeater-container');
            const template = document.getElementById('item-repeater-template');
            const locationSelect = document.getElementById('location_to');
            const form = document.getElementById('stockMovementForm');
            const submitBtn = document.getElementById('btn-submit');
            const addItemBtn = document.getElementById('btn-add-item');
            let itemIndex = 1;

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

                            if (option.value === currentVal) {
                                isCurrentValValid = true;
                            }
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

            // Update unit dropdown
            function updateUnitDropdown(stockSelectElement) {
                const selectedOption = stockSelectElement.options[stockSelectElement.selectedIndex];
                const row = stockSelectElement.closest('.repeater-item');
                const unitSelect = row.querySelector('.unit-select');

                if (!selectedOption || !selectedOption.value) {
                    unitSelect.innerHTML =
                        `<option value="">{{ __('messages.owner.products.stocks.movements_create_in.unit_placeholder_no_stock') }}</option>`;
                    return;
                }

                const unitGroup = selectedOption.getAttribute('data-unit-group');
                const displayUnitId = selectedOption.getAttribute('data-display-unit-id');

                unitSelect.innerHTML =
                    `<option value="">{{ __('messages.owner.products.stocks.movements_create_in.unit_placeholder') }}</option>`;

                if (unitGroup) {
                    const filteredUnits = allUnits.filter(unit => unit.group_label === unitGroup);
                    filteredUnits.forEach(unit => {
                        const option = new Option(unit.unit_name, unit.id);
                        unitSelect.appendChild(option);
                    });
                    if (displayUnitId) {
                        unitSelect.value = displayUnitId;
                    }
                }
            }

            // Validate form
            function isFormValid() {
                if (!locationSelect.value) return false;

                const items = document.querySelectorAll('.repeater-item');
                if (items.length === 0) return false;

                let valid = true;

                items.forEach(row => {
                    const stock = row.querySelector('.stock-select');
                    const qty = row.querySelector('.quantity-input');
                    const unit = row.querySelector('.unit-select');

                    if (
                        !stock || !stock.value ||
                        !qty || !qty.value || Number(qty.value) <= 0 ||
                        !unit || !unit.value
                    ) {
                        valid = false;
                    }
                });

                return valid;
            }

            // Update button states
            function updateButtons() {
                const valid = isFormValid();
                submitBtn.disabled = !valid;
                addItemBtn.disabled = !valid;
            }

            // Event: Location change
            locationSelect.addEventListener('change', function () {
                filterStocksByLocation();
            });

            // Event: Add item
            addItemBtn.addEventListener('click', function () {
                let templateHTML = template.innerHTML.replace(/__INDEX__/g, itemIndex);
                const newRow = document.createElement('div');
                newRow.innerHTML = templateHTML;
                container.appendChild(newRow.firstElementChild);

                filterStocksByLocation();
                itemIndex++;
                setTimeout(updateButtons, 50);
            });

            // Event: Remove item
            container.addEventListener('click', function (e) {
                if (e.target.closest('.btn-remove')) {
                    e.target.closest('.repeater-item').remove();
                    setTimeout(updateButtons, 50);
                }
            });

            // Event: Stock change
            container.addEventListener('change', function (e) {
                if (e.target && e.target.classList.contains('stock-select')) {
                    updateUnitDropdown(e.target);
                }
            });

            // Event: Form submit
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '{{ __('messages.owner.products.stocks.movements_create_in.confirm_title') }}',
                    text: '{{ __('messages.owner.products.stocks.movements_create_in.confirm_text') }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#8c1000',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __('messages.owner.products.stocks.movements_create_in.confirm_button') }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            // Monitor form changes
            form.addEventListener('input', updateButtons);
            form.addEventListener('change', updateButtons);

            // Initialize
            filterStocksByLocation();
            const firstStockSelect = container.querySelector('.stock-select');
            if (firstStockSelect && firstStockSelect.value) {
                updateUnitDropdown(firstStockSelect);
            }
            updateButtons();
        });
    </script>

    <style>

        .repeater-item {
            padding: 1rem;
            background: #f9fafb;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
        }
    </style>
@endsection