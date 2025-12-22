@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.movements_adjustment.title'))
@section('page_title', __('messages.owner.products.stocks.movements_adjustment.page_title'))

@section('content')
    <section class="content">
        <div class="container-fluid owner-stocks">
            <a href="{{ route('owner.user-owner.stocks.index') }}" class="btn btn-primary mb-3">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('messages.owner.products.stocks.back_to_list') }}
            </a>

            <form action="{{ route('owner.user-owner.stocks.movements.store') }}" method="POST" id="stockMovementForm">
                @csrf
                <input type="hidden" name="movement_type" value="adjustment">

                {{-- CARD 1: DETAIL TRANSAKSI --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('messages.owner.products.stocks.movements_adjustment.card_transaction_title') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Lokasi --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label
                                        for="location">{{ __('messages.owner.products.stocks.movements_adjustment.location_from_label') }}
                                        <span class="text-danger">*</span></label>
                                    <select name="location" id="location" class="form-control" required>
                                        <option value="_owner">
                                            {{ __('messages.owner.products.stocks.movements_adjustment.location_owner_option') }}
                                        </option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Kategori Transaksi --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label
                                        for="category">{{ __('messages.owner.products.stocks.movements_adjustment.category_label') }}
                                        <span class="text-danger">*</span></label>

                                    <div class="input-group">
                                        {{-- 1. Select Dropdown --}}
                                        <select name="category" id="categorySelect" class="form-control" required>
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

                                        {{-- 2. Input Text (Hidden default) --}}
                                        <input type="text" name="category" id="categoryInput" class="form-control"
                                            placeholder="{{ __('messages.owner.products.stocks.movements_adjustment.new_category_placeholder') }}"
                                            style="display: none;" disabled>

                                        {{-- 3. Tombol Toggle --}}
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="btn-add-category"
                                                title="Buat Kategori Baru">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" type="button" id="btn-cancel-category"
                                                title="Batal" style="display: none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Catatan --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label
                                        for="notes">{{ __('messages.owner.products.stocks.movements_adjustment.notes_label') }}</label>
                                    <input type="text" name="notes" id="notes" class="form-control"
                                        placeholder="{{ __('messages.owner.products.stocks.movements_adjustment.notes_placeholder') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: ITEM TRANSAKSI --}}
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('messages.owner.products.stocks.movements_adjustment.card_items_title') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="item-repeater-container">
                            {{-- Item pertama --}}
                            <div class="row repeater-item mb-3">
                                <div class="col-md-4">
                                    <label>{{ __('messages.owner.products.stocks.movements_adjustment.item_stock_label') }}
                                        <span class="text-danger">*</span></label>
                                    <select name="items[0][stock_id]" class="form-control stock-select" required>
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
                                    <div class="current-stock-info text-muted small mt-1" style="display: none;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('messages.owner.products.stocks.movements_adjustment.quantity_label') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="number" name="items[0][new_quantity]"
                                        class="form-control new-quantity-input" step="0.01" placeholder="Qty Baru" required>
                                    <input type="hidden" name="items[0][current_quantity]" class="current-quantity-hidden">
                                    <div style="height: 21px;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('messages.owner.products.stocks.movements_adjustment.unit_label') }} <span
                                            class="text-danger">*</span></label>

                                    {{-- MODIFIKASI: Input Readonly untuk Tampilan --}}
                                    <input type="text" class="form-control unit-name-display" readonly placeholder="Unit"
                                        style="background-color: #e9ecef;">

                                    {{-- MODIFIKASI: Input Hidden untuk ID Unit yang dikirim ke Controller --}}
                                    <input type="hidden" name="items[0][unit_id]" class="unit-id-input">

                                    <div style="height: 21px;"></div>
                                </div>
                                <div class="col-md-3">
                                    <label>{{ __('messages.owner.products.stocks.movements_adjustment.change') }}</label>
                                    <div class="adjustment-info alert alert-info"
                                        style="display: none; padding: 8px; margin: 0;">
                                        <small><strong>{{ __('messages.owner.products.stocks.movements_adjustment.type_label') }}
                                                :</strong> <span class="adjustment-type">-</span></small><br>
                                        <small><strong>{{ __('messages.owner.products.stocks.movements_adjustment.quantity') }}
                                                :</strong> <span class="adjustment-amount">-</span></small>
                                    </div>
                                    <div style="height: 21px;"></div>
                                </div>
                                <div class="col-md-1 d-flex align-items-center" style="padding-top: 32px;">
                                    {{-- Tombol Hapus tidak ada untuk item pertama --}}
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Tambah Item --}}
                        <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-plus"></i>
                            {{ __('messages.owner.products.stocks.movements_adjustment.add_item_button') }}
                        </button>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4 mb-4">
                    <button type="submit" class="btn btn-danger" id="btn-save" disabled>
                        <i class="fas fa-save"></i>
                        {{ __('messages.owner.products.stocks.movements_adjustment.submit_button') }}
                    </button>
                </div>
            </form>
        </div>
    </section>

    <template id="item-repeater-template">
        <div class="row repeater-item mb-3">
            <div class="col-md-4">
                <label>{{ __('messages.owner.products.stocks.movements_adjustment.item_stock_label') }} <span
                        class="text-danger">*</span></label>
                <select name="items[__INDEX__][stock_id]" class="form-control stock-select" required>
                    <option value="">{{ __('messages.owner.products.stocks.movements_adjustment.item_stock_placeholder') }}
                    </option>
                    @foreach ($stocks as $stock)
                        <option value="{{ $stock->id }}" data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                            data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}"
                            data-current-qty="{{ $stock->display_quantity ?? 0 }}"
                            data-current-unit="{{ $stock->displayUnit->unit_name ?? __('messages.owner.products.stocks.movements_adjustment.current_stock_unit_default') }}">
                            {{ $stock->stock_name }}
                            ({{ $stock->partner->name ?? __('messages.owner.products.stocks.movements_adjustment.location_owner_option') }})
                        </option>
                    @endforeach
                </select>
                <div class="current-stock-info text-muted small mt-1" style="display: none;"></div>
            </div>
            <div class="col-md-2">
                <label>{{ __('messages.owner.products.stocks.movements_adjustment.quantity_label') }} <span
                        class="text-danger">*</span></label>
                <input type="number" name="items[__INDEX__][new_quantity]" class="form-control new-quantity-input"
                    step="0.01" placeholder="Qty Baru" required>
                <input type="hidden" name="items[__INDEX__][current_quantity]" class="current-quantity-hidden">
                <div style="height: 21px;"></div>
            </div>
            <div class="col-md-2">
                <label>{{ __('messages.owner.products.stocks.movements_adjustment.unit_label') }} <span
                        class="text-danger">*</span></label>

                {{-- MODIFIKASI: Template untuk Unit Readonly --}}
                <input type="text" class="form-control unit-name-display" readonly placeholder="Unit"
                    style="background-color: #e9ecef;">
                <input type="hidden" name="items[__INDEX__][unit_id]" class="unit-id-input">

                <div style="height: 21px;"></div>
            </div>
            <div class="col-md-3">
                <label>{{ __('messages.owner.products.stocks.movements_adjustment.change') }}</label>
                <div class="adjustment-info alert alert-info" style="display: none; padding: 8px; margin: 0;">
                    <small><strong>Tipe:</strong> <span class="adjustment-type">-</span></small><br>
                    <small><strong>Jumlah:</strong> <span class="adjustment-amount">-</span></small>
                </div>
                <div style="height: 21px;"></div>
            </div>
            <div class="col-md-1 d-flex align-items-center" style="padding-top: 32px;">
                <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                    <i class="fas fa-trash"></i>
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

            // Elemen Kategori
            const categorySelect = document.getElementById('categorySelect');
            const categoryInput = document.getElementById('categoryInput');
            const btnAddCategory = document.getElementById('btn-add-category');
            const btnCancelCategory = document.getElementById('btn-cancel-category');
            const categoryHelp = document.getElementById('categoryHelp');

            let itemIndex = 1;

            const translations = {
                currentStockPrefix: "{{ __('messages.owner.products.stocks.movements_adjustment.current_stock_prefix') }}"
            };

            // --- 1. LOGIKA KATEGORI ---
            btnAddCategory.addEventListener('click', function () {
                categorySelect.style.display = 'none';
                categorySelect.disabled = true;

                categoryInput.style.display = 'block';
                categoryInput.disabled = false;
                categoryInput.required = true;
                categoryInput.focus();

                btnAddCategory.style.display = 'none';
                btnCancelCategory.style.display = 'inline-block';
                categoryHelp.style.display = 'block';
            });

            btnCancelCategory.addEventListener('click', function () {
                categoryInput.value = '';
                categoryInput.style.display = 'none';
                categoryInput.disabled = true;
                categoryInput.required = false;

                categorySelect.style.display = 'block';
                categorySelect.disabled = false;
                categorySelect.selectedIndex = 0;

                btnCancelCategory.style.display = 'none';
                btnAddCategory.style.display = 'inline-block';
                categoryHelp.style.display = 'none';
            });

            // --- 2. FILTER STOK ---
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

            // --- 3. UPDATE INFO UNIT & QUANTITY ---
            // MODIFIKASI: Sekarang fungsi ini mengunci unit sesuai stok terpilih
            function updateUnitDropdown(stockSelectElement) {
                const selectedOption = stockSelectElement.options[stockSelectElement.selectedIndex];
                const row = stockSelectElement.closest('.repeater-item');

                // Elemen-elemen dalam baris
                const unitNameDisplay = row.querySelector('.unit-name-display');
                const unitIdInput = row.querySelector('.unit-id-input');
                const infoBox = row.querySelector('.current-stock-info');
                const newQuantityInput = row.querySelector('.new-quantity-input');
                const currentQuantityHidden = row.querySelector('.current-quantity-hidden');

                // Reset nilai
                if (infoBox) {
                    infoBox.textContent = '';
                    infoBox.style.display = 'none';
                }
                newQuantityInput.value = '';
                currentQuantityHidden.value = '';
                unitNameDisplay.value = '';
                unitIdInput.value = '';

                // Jika tidak ada stok dipilih
                if (!selectedOption || !selectedOption.value) {
                    return;
                }

                // Ambil data dari option
                const displayUnitId = selectedOption.getAttribute('data-display-unit-id');
                const currentQty = parseFloat(selectedOption.getAttribute('data-current-qty')) || 0;
                const currentUnit = selectedOption.getAttribute('data-current-unit');

                // Set Unit (Terkunci)
                unitNameDisplay.value = currentUnit; // Tampilkan nama unit
                unitIdInput.value = displayUnitId;   // Set ID unit untuk dikirim ke controller

                // Set Quantity Saat Ini
                currentQuantityHidden.value = currentQty;
                newQuantityInput.value = currentQty.toFixed(2);

                // Tampilkan Info
                if (infoBox) {
                    infoBox.textContent = translations.currentStockPrefix + ' ' + currentQty.toFixed(2) + ' ' + currentUnit;
                    infoBox.style.display = 'block';
                }

                // Hitung Penyesuaian
                calculateAdjustment(row);
            }

            // --- 4. HITUNG SELISIH ---
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

                if (difference > 0) {
                    adjustmentInfo.className = 'adjustment-info alert alert-success';
                    adjustmentType.textContent = '{{ __('messages.owner.products.stocks.movements_adjustment.type_increase') }}';
                    adjustmentAmount.textContent = '+' + difference.toFixed(2) + ' ' + unitName;
                } else if (difference < 0) {
                    adjustmentInfo.className = 'adjustment-info alert alert-danger';
                    adjustmentType.textContent = '{{ __('messages.owner.products.stocks.movements_adjustment.type_decrease') }}';
                    adjustmentAmount.textContent = '-' + Math.abs(difference).toFixed(2) + ' ' + unitName;
                } else {
                    adjustmentInfo.className = 'adjustment-info alert alert-secondary';
                    adjustmentType.textContent = '{{ __('messages.owner.products.stocks.movements_adjustment.type_no_change') }}';
                    adjustmentAmount.textContent = '0 ' + unitName;
                }

                adjustmentInfo.style.display = 'block';
                adjustmentInfo.style.padding = '8px';
                adjustmentInfo.style.margin = '0';
            }

            // --- EVENT LISTENERS ---
            locationSelect.addEventListener('change', filterStocksByLocation);

            document.getElementById('btn-add-item').addEventListener('click', function () {
                let templateHTML = template.innerHTML.replace(/__INDEX__/g, itemIndex);
                const newRow = document.createElement('div');
                newRow.innerHTML = templateHTML;
                container.appendChild(newRow.firstElementChild);
                filterStocksByLocation();
                itemIndex++;
            });

            container.addEventListener('click', function (e) {
                if (e.target && (e.target.classList.contains('btn-remove-item') || e.target.closest('.btn-remove-item'))) {
                    e.target.closest('.repeater-item').remove();
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

            // Init
            filterStocksByLocation();
            const firstStockSelect = container.querySelector('.stock-select');
            if (firstStockSelect && firstStockSelect.value) {
                updateUnitDropdown(firstStockSelect);
            }

            if (form) {
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
            }
        });


        // --- VALIDASI KATEGORI BARU ---
        document.addEventListener('DOMContentLoaded', function () {
            const categoryInput = document.getElementById('categoryInput');
            const categorySelect = document.getElementById('categorySelect');

            // Ambil daftar kategori yang sudah ada dari select options
            const existingCategories = Array.from(categorySelect.options)
                .filter(opt => opt.value !== '')
                .map(opt => opt.value.toLowerCase().trim());

            // Real-time validation saat user mengetik
            categoryInput.addEventListener('input', function () {
                const inputValue = this.value.trim();
                const inputLower = inputValue.toLowerCase();

                // Remove previous validation states
                this.classList.remove('is-valid', 'is-invalid');

                // Hapus feedback message sebelumnya
                const existingFeedback = this.parentElement.querySelector('.invalid-feedback, .valid-feedback');
                if (existingFeedback) {
                    existingFeedback.remove();
                }

                // Validasi kosong
                if (inputValue === '') {
                    return;
                }

                // Validasi karakter khusus
                const validPattern = /^[a-zA-Z0-9\s_-]+$/;
                if (!validPattern.test(inputValue)) {
                    this.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback d-block';
                    feedback.textContent = '{{ __('messages.owner.products.stocks.movements_adjustment.category_invalid_characters') }}';
                    this.parentElement.appendChild(feedback);
                    return;
                }

                // Validasi duplikasi (case-insensitive)
                if (existingCategories.includes(inputLower)) {
                    this.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback d-block';
                    feedback.textContent = '{{ __('messages.owner.products.stocks.movements_adjustment.category_already_exists') }}';
                    this.parentElement.appendChild(feedback);
                    return;
                }

                // Valid
                this.classList.add('is-valid');
                const feedback = document.createElement('div');
                feedback.className = 'valid-feedback d-block';
                feedback.textContent = '{{ __('messages.owner.products.stocks.movements_adjustment.category_available') }}';
                this.parentElement.appendChild(feedback);
            });

        });

        // --- ENABLE/DISABLE TOMBOL SIMPAN BERDASARKAN VALIDASI FORM ---
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('stockMovementForm');
            const saveButton = document.getElementById('btn-save');
            const categoryInput = document.getElementById('categoryInput');
            const categorySelect = document.getElementById('categorySelect');

            function isCategoryValid() {
                // Jika pakai select
                if (!categorySelect.disabled) {
                    return categorySelect.value !== '';
                }

                // Jika pakai input custom
                if (!categoryInput.disabled) {
                    return categoryInput.value.trim() !== '' &&
                        categoryInput.classList.contains('is-valid');
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

                if (
                    allRequiredFilled &&
                    !hasInvalid &&
                    isCategoryValid() &&
                    areItemsValid()
                ) {
                    saveButton.disabled = false;
                } else {
                    saveButton.disabled = true;
                }
            }

            // Pantau semua perubahan penting
            form.addEventListener('input', checkFormValidity);
            form.addEventListener('change', checkFormValidity);

            // Jalankan saat awal
            checkFormValidity();
        });

    </script>
@endsection