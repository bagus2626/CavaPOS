@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.movements_create_in.title'))
@section('page_title', __('messages.owner.products.stocks.movements_create_in.page_title'))

@section('content')
    <section class="content">
        <div class="container-fluid owner-stocks">
            <a href="{{ route('owner.user-owner.stocks.index') }}" class="btn btn-primary mb-3">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('messages.owner.products.stocks.back_to_list') }}
            </a>
            <form action="{{ route('owner.user-owner.stocks.movements.store') }}" method="POST" id="stockMovementForm">
                @csrf
                <input type="hidden" name="movement_type" value="in">

                {{-- CARD 1: DETAIL TRANSAKSI --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('messages.owner.products.stocks.movements_create_in.card_transaction_title') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Lokasi Tujuan (TO) --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location_to">
                                        {{ __('messages.owner.products.stocks.movements_create_in.location_to_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="location_to" id="location_to" class="form-control" required>
                                        <option value="_owner">
                                            {{ __('messages.owner.products.stocks.movements_create_in.location_owner_option') }}
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
                                    <label for="category">
                                        {{ __('messages.owner.products.stocks.movements_create_in.category_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="purchase">
                                            {{ __('messages.owner.products.stocks.movements_create_in.category_purchase') }}
                                        </option>
                                        <option value="transfer_in">
                                            {{ __('messages.owner.products.stocks.movements_create_in.category_transfer_in') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            {{-- Catatan --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">
                                        {{ __('messages.owner.products.stocks.movements_create_in.notes_label') }}
                                    </label>
                                    <input type="text" name="notes" id="notes" class="form-control"
                                        placeholder="{{ __('messages.owner.products.stocks.movements_create_in.notes_placeholder') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: ITEM TRANSAKSI --}}
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('messages.owner.products.stocks.movements_create_in.card_items_title') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="item-repeater-container">
                            {{-- Item pertama --}}
                            <div class="row repeater-item align-items-end mb-3">
                                <div class="col-md-4">
                                    <label>
                                        {{ __('messages.owner.products.stocks.movements_create_in.item_stock_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="items[0][stock_id]" class="form-control stock-select" required>
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
                                </div>
                                <div class="col-md-2">
                                    <label>
                                        {{ __('messages.owner.products.stocks.movements_create_in.quantity_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="items[0][quantity]" class="form-control quantity-input"
                                        step="0.01"
                                        placeholder="{{ __('messages.owner.products.stocks.movements_create_in.quantity_placeholder') }}"
                                        required>
                                </div>
                                <div class="col-md-2">
                                    <label>
                                        {{ __('messages.owner.products.stocks.movements_create_in.unit_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="items[0][unit_id]" class="form-control unit-select" required>
                                        <option value="">
                                            {{ __('messages.owner.products.stocks.movements_create_in.unit_placeholder_no_stock') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>
                                        {{ __('messages.owner.products.stocks.movements_create_in.unit_price_label') }}
                                    </label>
                                    <input type="number" name="items[0][unit_price]" class="form-control" step="0.01"
                                        placeholder="{{ __('messages.owner.products.stocks.movements_create_in.unit_price_placeholder') }}">
                                </div>
                                <div class="col-md-1">
                                    {{-- Baris pertama tidak punya tombol hapus --}}
                                </div>
                            </div>
                        </div>

                        <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-primary mt-2" disabled>
                            <i class="fas fa-plus"></i>
                            {{ __('messages.owner.products.stocks.movements_create_in.add_item_button') }}
                        </button>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4 mb-4">
                    <button type="submit" class="btn btn-success" id="btn-submit" disabled>
                        <i class="fas fa-save"></i>
                        {{ __('messages.owner.products.stocks.movements_create_in.submit_button') }}
                    </button>

                </div>
            </form>
        </div>
    </section>

    <template id="item-repeater-template">
        <div class="row repeater-item align-items-end mb-3">
            <div class="col-md-4">
                <label>
                    {{ __('messages.owner.products.stocks.movements_create_in.item_stock_label') }}
                    <span class="text-danger">*</span>
                </label>
                <select name="items[__INDEX__][stock_id]" class="form-control stock-select" required>
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
            </div>
            <div class="col-md-2">
                <label>
                    {{ __('messages.owner.products.stocks.movements_create_in.quantity_label') }}
                    <span class="text-danger">*</span>
                </label>
                <input type="number" name="items[__INDEX__][quantity]" class="form-control quantity-input" step="0.01"
                    placeholder="{{ __('messages.owner.products.stocks.movements_create_in.quantity_placeholder') }}"
                    required>
            </div>
            <div class="col-md-2">
                <label>
                    {{ __('messages.owner.products.stocks.movements_create_in.unit_label') }}
                    <span class="text-danger">*</span>
                </label>
                <select name="items[__INDEX__][unit_id]" class="form-control unit-select" required>
                    <option value="">
                        {{ __('messages.owner.products.stocks.movements_create_in.unit_placeholder_no_stock') }}
                    </option>
                </select>
            </div>
            <div class="col-md-3">
                <label>
                    {{ __('messages.owner.products.stocks.movements_create_in.unit_price_label_with_unit') }}
                </label>
                <input type="number" name="items[__INDEX__][unit_price]" class="form-control" step="0.01"
                    placeholder="{{ __('messages.owner.products.stocks.movements_create_in.unit_price_placeholder') }}">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                    <i class="fas fa-trash"></i>
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
            let itemIndex = 1;

            // --- FILTER STOK BERDASARKAN LOKASI ---
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

            // --- UPDATE UNIT DROPDOWN ---
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

            // --- EVENT LISTENERS ---

            // 1. Saat Lokasi Tujuan Berubah
            locationSelect.addEventListener('change', function () {
                filterStocksByLocation();
            });

            // 2. Tambah Item (Repeater)
            document.getElementById('btn-add-item').addEventListener('click', function () {
                let templateHTML = template.innerHTML.replace(/__INDEX__/g, itemIndex);
                const newRow = document.createElement('div');
                newRow.innerHTML = templateHTML;
                container.appendChild(newRow.firstElementChild);

                filterStocksByLocation();

                itemIndex++;
            });

            // 3. Hapus Item
            container.addEventListener('click', function (e) {
                if (e.target && (e.target.classList.contains('btn-remove-item') || e.target.closest('.btn-remove-item'))) {
                    e.target.closest('.repeater-item').remove();
                }
            });

            // 4. Saat Stok Dipilih (Update Unit)
            container.addEventListener('change', function (e) {
                if (e.target && e.target.classList.contains('stock-select')) {
                    updateUnitDropdown(e.target);
                }
            });

            // Filter saat halaman pertama kali dimuat
            filterStocksByLocation();

            // Update unit untuk baris pertama
            const firstStockSelect = container.querySelector('.stock-select');
            if (firstStockSelect && firstStockSelect.value) {
                updateUnitDropdown(firstStockSelect);
            }

            // --- KONFIRMASI SUBMIT ---
            const form = document.getElementById('stockMovementForm');
            if (form) {
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
            }

            // Aktifkan tombol tambah item dan submit
            const submitBtn = document.getElementById('btn-submit');
            const addItemBtn = document.getElementById('btn-add-item');

            function isFormValid() {
                // lokasi tujuan wajib
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

            function updateButtons() {
                const valid = isFormValid();

                submitBtn.disabled = !valid;
                addItemBtn.disabled = !valid;
            }

            // Pantau perubahan form
            form.addEventListener('input', updateButtons);
            form.addEventListener('change', updateButtons);

            // Saat tambah item
            addItemBtn.addEventListener('click', () => {
                setTimeout(updateButtons, 50);
            });

            // Saat hapus item
            container.addEventListener('click', e => {
                if (e.target.closest('.btn-remove-item')) {
                    setTimeout(updateButtons, 50);
                }
            });

            // Init awal
            updateButtons();

        });
    </script>
@endsection