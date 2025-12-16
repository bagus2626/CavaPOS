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
                <input type="hidden" name="movement_type" value="out">

                {{-- CARD 1: DETAIL TRANSAKSI --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.owner.products.stocks.movements_adjustment.card_transaction_title') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Lokasi Asal (FROM) --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location_from">{{ __('messages.owner.products.stocks.movements_adjustment.location_from_label') }} <span class="text-danger">*</span></label>
                                    <select name="location_from" id="location_from" class="form-control" required>
                                        <option value="_owner">{{ __('messages.owner.products.stocks.movements_adjustment.location_owner_option') }}</option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Kategori Transaksi --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">{{ __('messages.owner.products.stocks.movements_adjustment.category_label') }} <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="damaged">{{ __('messages.owner.products.stocks.movements_adjustment.category_damaged') }}</option>
                                        <option value="expired">{{ __('messages.owner.products.stocks.movements_adjustment.category_expired') }}</option>
                                        <option value="internal_use">{{ __('messages.owner.products.stocks.movements_adjustment.category_internal_use') }}</option>
                                        <option value="lost">{{ __('messages.owner.products.stocks.movements_adjustment.category_lost') }}</option>
                                        <option value="audit_adjustment">{{ __('messages.owner.products.stocks.movements_adjustment.category_audit_adjustment') }}</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Catatan --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">{{ __('messages.owner.products.stocks.movements_adjustment.notes_label') }}</label>
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
                        <h3 class="card-title">{{ __('messages.owner.products.stocks.movements_adjustment.card_items_title') }}</h3>
                    </div>
                    <div class="card-body">
                        <div id="item-repeater-container">
                            {{-- Item pertama --}}
                            <div class="row repeater-item mb-3">
                                <div class="col-md-5">
                                    <label>{{ __('messages.owner.products.stocks.movements_adjustment.item_stock_label') }} <span class="text-danger">*</span></label>
                                    <select name="items[0][stock_id]" class="form-control stock-select" required>
                                        <option value="">{{ __('messages.owner.products.stocks.movements_adjustment.item_stock_placeholder') }}</option>
                                        @foreach ($stocks as $stock)
                                            <option value="{{ $stock->id }}" data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                                                data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                                                data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}"
                                                data-current-qty="{{ number_format($stock->display_quantity ?? 0, 2) }}"
                                                data-current-unit="{{ $stock->displayUnit->unit_name ?? __('messages.owner.products.stocks.movements_adjustment.current_stock_unit_default') }}">
                                                {{ $stock->stock_name }}
                                                ({{ $stock->partner->name ?? __('messages.owner.products.stocks.movements_adjustment.location_owner_option') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="current-stock-info text-muted small mt-1" style="display: none;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('messages.owner.products.stocks.movements_adjustment.quantity_label') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="items[0][quantity]" class="form-control quantity-input"
                                        step="0.01" placeholder="{{ __('messages.owner.products.stocks.movements_adjustment.quantity_placeholder') }}" required>
                                    <div style="height: 21px;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('messages.owner.products.stocks.movements_adjustment.unit_label') }} <span class="text-danger">*</span></label>
                                    <select name="items[0][unit_id]" class="form-control unit-select" required>
                                        <option value="">{{ __('messages.owner.products.stocks.movements_adjustment.unit_placeholder_no_stock') }}</option>
                                    </select>
                                    <div style="height: 21px;"></div>
                                </div>
                                <div class="col-md-2">
                                    {{-- Kolom kosong untuk alignment --}}
                                </div>
                                <div class="col-md-1 d-flex align-items-center" style="padding-top: 32px;">
                                    {{-- Tombol Hapus tidak ada untuk item pertama --}}
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Tambah Item --}}
                        <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-plus"></i> {{ __('messages.owner.products.stocks.movements_adjustment.add_item_button') }}
                        </button>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4 mb-4">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save"></i> {{ __('messages.owner.products.stocks.movements_adjustment.submit_button') }}
                    </button>
                </div>
            </form>
        </div>
    </section>

    <template id="item-repeater-template">
        <div class="row repeater-item mb-3">
            <div class="col-md-5">
                <label>{{ __('messages.owner.products.stocks.movements_adjustment.item_stock_label') }} <span class="text-danger">*</span></label>
                <select name="items[__INDEX__][stock_id]" class="form-control stock-select" required>
                    <option value="">{{ __('messages.owner.products.stocks.movements_adjustment.item_stock_placeholder') }}</option>
                    @foreach ($stocks as $stock)
                        <option value="{{ $stock->id }}" data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                            data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                            data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}"
                            data-current-qty="{{ number_format($stock->display_quantity ?? 0, 2) }}"
                            data-current-unit="{{ $stock->displayUnit->unit_name ?? __('messages.owner.products.stocks.movements_adjustment.current_stock_unit_default') }}">
                            {{ $stock->stock_name }}
                            ({{ $stock->partner->name ?? __('messages.owner.products.stocks.movements_adjustment.location_owner_option') }})
                        </option>
                    @endforeach
                </select>
                <div class="current-stock-info text-muted small mt-1" style="display: none;"></div>
            </div>
            <div class="col-md-2">
                <label>{{ __('messages.owner.products.stocks.movements_adjustment.quantity_label') }} <span class="text-danger">*</span></label>
                <input type="number" name="items[__INDEX__][quantity]" class="form-control quantity-input" step="0.01"
                    placeholder="{{ __('messages.owner.products.stocks.movements_adjustment.quantity_placeholder') }}" required>
                <div style="height: 21px;"></div>
            </div>
            <div class="col-md-2">
                <label>{{ __('messages.owner.products.stocks.movements_adjustment.unit_label') }} <span class="text-danger">*</span></label>
                <select name="items[__INDEX__][unit_id]" class="form-control unit-select" required>
                    <option value="">{{ __('messages.owner.products.stocks.movements_adjustment.unit_placeholder_no_stock') }}</option>
                </select>
                <div style="height: 21px;"></div>
            </div>
            <div class="col-md-2">
                {{-- Kolom kosong untuk alignment --}}
            </div>
            <div class="col-md-1 d-flex align-items-center" style="padding-top: 32px;">
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
            const locationSelect = document.getElementById('location_from');
            let itemIndex = 1;

            // Translation strings untuk JavaScript
            const translations = {
                selectItemFirst: "{{ __('messages.owner.products.stocks.movements_adjustment.unit_placeholder_no_stock') }}",
                selectUnit: "{{ __('messages.owner.products.stocks.movements_adjustment.unit_placeholder') }}",
                currentStockPrefix: "{{ __('messages.owner.products.stocks.movements_adjustment.current_stock_prefix') }}"
            };

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
                const infoBox = row.querySelector('.current-stock-info');

                // Reset info box
                if (infoBox) {
                    infoBox.textContent = '';
                    infoBox.style.display = 'none';
                }

                if (!selectedOption || !selectedOption.value) {
                    unitSelect.innerHTML = '<option value="">' + translations.selectItemFirst + '</option>';
                    return;
                }

                const unitGroup = selectedOption.getAttribute('data-unit-group');
                const displayUnitId = selectedOption.getAttribute('data-display-unit-id');
                const currentQty = selectedOption.getAttribute('data-current-qty');
                const currentUnit = selectedOption.getAttribute('data-current-unit');

                unitSelect.innerHTML = '<option value="">' + translations.selectUnit + '</option>';

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

                // Show current stock info
                if (currentQty && infoBox) {
                    infoBox.textContent = translations.currentStockPrefix + ' ' + currentQty + ' ' + currentUnit;
                    infoBox.style.display = 'block';
                }
            }

            // --- EVENT LISTENERS ---

            // 1. Saat Lokasi Asal Berubah
            locationSelect.addEventListener('change', function () {
                filterStocksByLocation();
            });

            // 2. Tambah Item
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
                        title: '{{ __('messages.owner.products.stocks.movements_adjustment.confirm_title') }}',
                        text: '{{ __('messages.owner.products.stocks.movements_adjustment.confirm_text') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
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
    </script>
@endsection