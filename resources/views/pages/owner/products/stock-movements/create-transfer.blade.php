@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.movements_transfer.title'))
@section('page_title', __('messages.owner.products.stocks.movements_transfer.page_title'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <!-- Header Section -->
            <div class="page-header">
                {{-- <a href="{{ route('owner.user-owner.stocks.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.products.stocks.back_to_list') }}
                </a> --}}
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.stocks.movements_transfer.page_title') }}</h1>
                    <p class="page-subtitle">Transfer stock between locations seamlessly.</p>
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
                <form action="{{ route('owner.user-owner.stocks.movements.store') }}" method="POST" id="stockMovementForm">
                    @csrf
                    <input type="hidden" name="movement_type" value="transfer" id="movement_type">
                    <input type="hidden" name="category" value="transfer">

                    <div class="card-body-modern">
                        <!-- Transaction Details Section -->
                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">swap_horiz</span>
                            </div>
                            <h3 class="section-title">{{ __('messages.owner.products.stocks.movements_transfer.card_transaction_title') }}</h3>
                        </div>

                        <div class="row g-4">
                            <!-- Source Location -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.stocks.movements_transfer.location_from_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="location_from" id="location_from"
                                            class="form-control-modern @error('location_from') is-invalid @enderror"
                                            required>
                                            <option value="_owner">
                                                {{ __('messages.owner.products.stocks.movements_transfer.location_owner_option') }}
                                            </option>
                                            @foreach ($partners as $partner)
                                                <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                                    </div>
                                    @error('location_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Destination Location -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.stocks.movements_transfer.location_to_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="location_to" id="location_to"
                                            class="form-control-modern @error('location_to') is-invalid @enderror"
                                            required>
                                            <option value="_owner">
                                                {{ __('messages.owner.products.stocks.movements_transfer.location_owner_option') }}
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

                            <!-- Notes -->
                            <div class="col-md-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.stocks.movements_transfer.notes_label') }}
                                    </label>
                                    <input type="text" name="notes" id="notes"
                                        class="form-control-modern @error('notes') is-invalid @enderror"
                                        placeholder="{{ __('messages.owner.products.stocks.movements_transfer.notes_placeholder') }}">
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
                            <h3 class="section-title">{{ __('messages.owner.products.stocks.movements_transfer.card_items_title') }}</h3>
                        </div>

                        <div id="item-repeater-container">
                            <!-- First Item Row -->
                            <div class="row repeater-item g-3 mb-3">
                                <div class="col-md-5">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.movements_transfer.item_stock_label') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="select-wrapper">
                                            <select name="items[0][stock_id]" class="form-control-modern stock-select" required>
                                                <option value="">
                                                    {{ __('messages.owner.products.stocks.movements_transfer.item_stock_placeholder') }}
                                                </option>
                                                @foreach ($stocks as $stock)
                                                    <option value="{{ $stock->id }}"
                                                        data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                                                        data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                                                        data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}"
                                                        data-current-qty="{{ number_format($stock->display_quantity ?? 0, 2) }}"
                                                        data-current-unit="{{ $stock->displayUnit->unit_name ?? __('messages.owner.products.stocks.movements_transfer.current_stock_unit_default') }}">
                                                        {{ $stock->stock_name }}
                                                        ({{ $stock->partner->name ?? __('messages.owner.products.stocks.movements_transfer.location_owner_option') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>
                                        <div class="current-stock-info text-muted small mt-2" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.movements_transfer.quantity_label') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" name="items[0][quantity]" class="form-control-modern quantity-input"
                                            step="0.01"
                                            placeholder="{{ __('messages.owner.products.stocks.movements_transfer.quantity_placeholder') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.movements_transfer.unit_label') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="select-wrapper">
                                            <select name="items[0][unit_id]" class="form-control-modern unit-select" required>
                                                <option value="">
                                                    {{ __('messages.owner.products.stocks.movements_transfer.unit_placeholder_no_stock') }}
                                                </option>
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
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
                            {{ __('messages.owner.products.stocks.movements_transfer.add_item_button') }}
                        </button>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer-modern">
                        <a href="{{ route('owner.user-owner.stocks.index') }}" class="btn-cancel-modern">
                            {{ __('messages.owner.user_management.employees.cancel') }}
                        </a>
                        <button type="submit" class="btn-submit-modern" id="btn-submit" disabled>
                            {{ __('messages.owner.products.stocks.movements_transfer.submit_button') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Item Template -->
    <template id="item-repeater-template">
        <div class="row repeater-item g-3 mb-3 position-relative">
            <div class="col-md-5">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        {{ __('messages.owner.products.stocks.movements_transfer.item_stock_label') }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="select-wrapper">
                        <select name="items[__INDEX__][stock_id]" class="form-control-modern stock-select" required>
                            <option value="">
                                {{ __('messages.owner.products.stocks.movements_transfer.item_stock_placeholder') }}
                            </option>
                            @foreach ($stocks as $stock)
                                <option value="{{ $stock->id }}" 
                                    data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                                    data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                                    data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}"
                                    data-current-qty="{{ number_format($stock->display_quantity ?? 0, 2) }}"
                                    data-current-unit="{{ $stock->displayUnit->unit_name ?? __('messages.owner.products.stocks.movements_transfer.current_stock_unit_default') }}">
                                    {{ $stock->stock_name }}
                                    ({{ $stock->partner->name ?? __('messages.owner.products.stocks.movements_transfer.location_owner_option') }})
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                    </div>
                    <div class="current-stock-info text-muted small mt-2" style="display: none;"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        {{ __('messages.owner.products.stocks.movements_transfer.quantity_label') }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="items[__INDEX__][quantity]" class="form-control-modern quantity-input" step="0.01"
                        placeholder="{{ __('messages.owner.products.stocks.movements_transfer.quantity_placeholder') }}"
                        required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        {{ __('messages.owner.products.stocks.movements_transfer.unit_label') }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="select-wrapper">
                        <select name="items[__INDEX__][unit_id]" class="form-control-modern unit-select" required>
                            <option value="">
                                {{ __('messages.owner.products.stocks.movements_transfer.unit_placeholder_no_stock') }}
                            </option>
                        </select>
                        <span class="material-symbols-outlined select-arrow">expand_more</span>
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
        const allUnits = @json($allUnits);

        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('item-repeater-container');
            const template = document.getElementById('item-repeater-template');
            const btnAddItem = document.getElementById('btn-add-item');
            const locationFrom = document.getElementById('location_from');
            const locationTo = document.getElementById('location_to');
            const form = document.getElementById('stockMovementForm');
            const submitBtn = document.getElementById('btn-submit');
            let itemIndex = 1;

            if (!container || !template || !btnAddItem || !locationFrom || !locationTo) return;
            if (typeof allUnits === 'undefined') {
                console.error('Error: Variabel `allUnits` tidak ditemukan.');
                return;
            }

            // Filter stocks by source location
            function filterStocksBySourceLocation() {
                const selectedSource = locationFrom.value;
                const stockSelects = document.querySelectorAll('.stock-select');

                stockSelects.forEach(select => {
                    const currentVal = select.value;
                    let isCurrentValValid = false;

                    Array.from(select.options).forEach(option => {
                        if (option.value === "") return;

                        const stockLocation = option.getAttribute('data-location-id');

                        if (stockLocation === selectedSource) {
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
                        updateRowInfo(select);
                    }
                });
            }

            // Update row info (units and current stock)
            function updateRowInfo(stockSelectElement) {
                const selectedOption = stockSelectElement.options[stockSelectElement.selectedIndex];
                const row = stockSelectElement.closest('.repeater-item');
                if (!row) return;

                const unitSelect = row.querySelector('.unit-select');
                const infoBox = row.querySelector('.current-stock-info');

                unitSelect.innerHTML =
                    `<option value="">{{ __('messages.owner.products.stocks.movements_transfer.unit_placeholder') }}</option>`;

                if (infoBox) {
                    infoBox.textContent = '';
                    infoBox.style.display = 'none';
                }

                if (!selectedOption || !selectedOption.value) return;

                const unitGroup = selectedOption.getAttribute('data-unit-group');
                const displayUnitId = selectedOption.getAttribute('data-display-unit-id');
                const currentQty = selectedOption.getAttribute('data-current-qty');
                const currentUnit = selectedOption.getAttribute('data-current-unit');

                if (unitGroup) {
                    const filteredUnits = allUnits.filter(unit => unit.group_label === unitGroup);
                    filteredUnits.forEach(unit => {
                        const option = new Option(unit.unit_name, unit.id);
                        option.setAttribute('data-conversion', unit.base_unit_conversion_value);
                        unitSelect.appendChild(option);
                    });
                    if (displayUnitId) {
                        unitSelect.value = displayUnitId;
                    }
                }

                if (currentQty && infoBox) {
                    infoBox.textContent =
                        '{{ __('messages.owner.products.stocks.movements_transfer.current_stock_prefix') }} ' +
                        currentQty + ' ' + currentUnit;
                    infoBox.style.display = 'block';
                }
            }

            // Update destination options (cannot be same as source)
            function updateToOptions() {
                const selectedFrom = locationFrom.value;
                const currentTo = locationTo.value;
                let firstValidValue = null;

                Array.from(locationTo.options).forEach(opt => {
                    if (opt.value === "") return;

                    if (opt.value === selectedFrom) {
                        opt.hidden = true;
                        opt.disabled = true;
                    } else {
                        opt.hidden = false;
                        opt.disabled = false;

                        if (!firstValidValue) {
                            firstValidValue = opt.value;
                        }
                    }
                });

                if (currentTo === selectedFrom || currentTo === "") {
                    if (firstValidValue) {
                        locationTo.value = firstValidValue;
                    }
                }
            }

            // Validate form
            function isFormValid() {
                if (!locationFrom.value || !locationTo.value) return false;
                if (locationFrom.value === locationTo.value) return false;

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
                btnAddItem.disabled = !valid;
            }

            // Event: Source location change
            locationFrom.addEventListener('change', function () {
                updateToOptions();
                filterStocksBySourceLocation();
                updateButtons();
            });

            // Event: Destination location change
            locationTo.addEventListener('change', updateButtons);

            // Event: Add item
            btnAddItem.addEventListener('click', function () {
                let templateHTML = template.innerHTML.replace(/__INDEX__/g, itemIndex);
                const newRow = document.createElement('div');
                newRow.innerHTML = templateHTML;
                container.appendChild(newRow.firstElementChild);

                filterStocksBySourceLocation();
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
                    updateRowInfo(e.target);
                }
            });

            // Event: Form submit
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                if (locationFrom.value === locationTo.value) {
                    Swal.fire(
                        '{{ __('messages.owner.products.stocks.movements_transfer.validation_same_location_title') }}',
                        '{{ __('messages.owner.products.stocks.movements_transfer.validation_same_location_text') }}',
                        'error'
                    );
                    return false;
                }

                Swal.fire({
                    title: '{{ __('messages.owner.products.stocks.movements_transfer.confirm_title') }}',
                    text: '{{ __('messages.owner.products.stocks.movements_transfer.confirm_text') }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#8c1000',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __('messages.owner.products.stocks.movements_transfer.confirm_button') }}',
                    cancelButtonText: '{{ __('messages.owner.products.stocks.movements_transfer.cancel_button') }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Monitor form changes
            form.addEventListener('input', updateButtons);
            form.addEventListener('change', updateButtons);

            // Initialize
            updateToOptions();
            filterStocksBySourceLocation();
            const firstStockSelect = container.querySelector('.stock-select');
            if (firstStockSelect && firstStockSelect.value) {
                updateRowInfo(firstStockSelect);
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