@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.movements_transfer.title'))
@section('page_title', __('messages.owner.products.stocks.movements_transfer.page_title'))

@section('content')
    <section class="content">
        <div class="container-fluid owner-stocks">
            <a href="{{ route('owner.user-owner.stocks.index') }}" class="btn btn-primary mb-3">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('messages.owner.products.stocks.back_to_list') }}
            </a>
            <form action="{{ route('owner.user-owner.stocks.movements.store') }}" method="POST" id="stockMovementForm">
                @csrf
                <input type="hidden" name="movement_type" value="transfer" id="movement_type">

                {{-- CARD 1: DETAIL TRANSAKSI --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('messages.owner.products.stocks.movements_transfer.card_transaction_title') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Lokasi Asal (FROM) --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location_from">
                                        {{ __('messages.owner.products.stocks.movements_transfer.location_from_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="location_from" id="location_from" class="form-control" required>
                                        <option value="_owner">
                                            {{ __('messages.owner.products.stocks.movements_transfer.location_owner_option') }}
                                        </option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Lokasi Tujuan (TO) --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location_to">
                                        {{ __('messages.owner.products.stocks.movements_transfer.location_to_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="location_to" id="location_to" class="form-control" required>
                                        <option value="_owner">
                                            {{ __('messages.owner.products.stocks.movements_transfer.location_owner_option') }}
                                        </option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Kategori (Hidden untuk transfer, selalu 'transfer') --}}
                            <input type="hidden" name="category" value="transfer">

                            {{-- Catatan --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">
                                        {{ __('messages.owner.products.stocks.movements_transfer.notes_label') }}
                                    </label>
                                    <input type="text" name="notes" id="notes" class="form-control"
                                        placeholder="{{ __('messages.owner.products.stocks.movements_transfer.notes_placeholder') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: ITEM TRANSAKSI --}}
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('messages.owner.products.stocks.movements_transfer.card_items_title') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="item-repeater-container">
                            {{-- Item pertama --}}
                            <div class="row repeater-item mb-3">
                                <div class="col-md-5">
                                    <label>
                                        {{ __('messages.owner.products.stocks.movements_transfer.item_stock_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="items[0][stock_id]" class="form-control stock-select" required>
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
                                    <div class="current-stock-info text-muted small mt-1" style="display: none;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label>
                                        {{ __('messages.owner.products.stocks.movements_transfer.quantity_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="items[0][quantity]" class="form-control quantity-input"
                                        step="0.01"
                                        placeholder="{{ __('messages.owner.products.stocks.movements_transfer.quantity_placeholder') }}"
                                        required>
                                    <div style="height: 21px;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label>
                                        {{ __('messages.owner.products.stocks.movements_transfer.unit_label') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="items[0][unit_id]" class="form-control unit-select" required>
                                        <option value="">
                                            {{ __('messages.owner.products.stocks.movements_transfer.unit_placeholder_no_stock') }}
                                        </option>
                                    </select>
                                    <div style="height: 21px;"></div>
                                </div>
                                <div class="col-md-2 unit-price-group" style="display: none;">
                                    {{-- Kolom harga disembunyikan untuk transfer --}}
                                </div>
                                <div class="col-md-1 d-flex align-items-center" style="padding-top: 32px;">
                                    {{-- Tombol Hapus tidak ada untuk item pertama --}}
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Tambah Item --}}
                        <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-plus"></i>
                            {{ __('messages.owner.products.stocks.movements_transfer.add_item_button') }}
                        </button>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4 mb-4">
                    <button type="submit" class="btn btn-info" id="btn-submit" disabled>
                        <i class="fas fa-save"></i>
                        {{ __('messages.owner.products.stocks.movements_transfer.submit_button') }}
                    </button>

                </div>
            </form>
        </div>
    </section>

    {{-- TEMPLATE --}}
    <template id="item-repeater-template">
        <div class="row repeater-item mb-3">
            <div class="col-md-5">
                <label>
                    {{ __('messages.owner.products.stocks.movements_transfer.item_stock_label') }}
                    <span class="text-danger">*</span>
                </label>
                <select name="items[__INDEX__][stock_id]" class="form-control stock-select" required>
                    <option value="">
                        {{ __('messages.owner.products.stocks.movements_transfer.item_stock_placeholder') }}
                    </option>
                    @foreach ($stocks as $stock)
                        <option value="{{ $stock->id }}" data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                            data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                            data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}"
                            data-current-qty="{{ number_format($stock->display_quantity ?? 0, 2) }}"
                            data-current-unit="{{ $stock->displayUnit->unit_name ?? __('messages.owner.products.stocks.movements_transfer.current_stock_unit_default') }}">
                            {{ $stock->stock_name }}
                            ({{ $stock->partner->name ?? __('messages.owner.products.stocks.movements_transfer.location_owner_option') }})
                        </option>
                    @endforeach
                </select>
                <div class="current-stock-info text-muted small mt-1" style="display: none;"></div>
            </div>
            <div class="col-md-2">
                <label>
                    {{ __('messages.owner.products.stocks.movements_transfer.quantity_label') }}
                    <span class="text-danger">*</span>
                </label>
                <input type="number" name="items[__INDEX__][quantity]" class="form-control quantity-input" step="0.01"
                    placeholder="{{ __('messages.owner.products.stocks.movements_transfer.quantity_placeholder') }}"
                    required>
                <div style="height: 21px;"></div>
            </div>
            <div class="col-md-2">
                <label>
                    {{ __('messages.owner.products.stocks.movements_transfer.unit_label') }}
                    <span class="text-danger">*</span>
                </label>
                <select name="items[__INDEX__][unit_id]" class="form-control unit-select" required>
                    <option value="">
                        {{ __('messages.owner.products.stocks.movements_transfer.unit_placeholder_no_stock') }}
                    </option>
                </select>
                <div style="height: 21px;"></div>
            </div>
            <div class="col-md-2 unit-price-group" style="display: none;">
                {{-- Kolom harga disembunyikan untuk transfer --}}
            </div>
            <div class="col-md-1 d-flex align-items-center" style="padding-top: 32px;">
                <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
    {{-- Kirim data unit dari PHP ke JavaScript --}}
    <script>
        const allUnits = @json($allUnits);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('item-repeater-container');
            const template = document.getElementById('item-repeater-template');
            const btnAddItem = document.getElementById('btn-add-item');
            const locationFrom = document.getElementById('location_from');
            const locationTo = document.getElementById('location_to');
            const form = document.getElementById('stockMovementForm');

            if (!container || !template || !btnAddItem || !locationFrom || !locationTo) return;
            if (typeof allUnits === 'undefined') {
                console.error('Error: Variabel `allUnits` tidak ditemukan.');
                return;
            }

            let itemIndex = 1;

            // Filter stok berdasarkan lokasi asal
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

            // Update unit dropdown & info stok
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

            // Update opsi lokasi tujuan (tidak boleh sama dengan asal)
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

            // Saat lokasi asal berubah
            locationFrom.addEventListener('change', function () {
                updateToOptions();
                filterStocksBySourceLocation();
            });

            // Tombol tambah item
            btnAddItem.addEventListener('click', function () {
                let templateHTML = template.innerHTML.replace(/__INDEX__/g, itemIndex);
                const newRow = document.createElement('div');
                newRow.innerHTML = templateHTML;
                container.appendChild(newRow.firstElementChild);

                filterStocksBySourceLocation();
                itemIndex++;
            });

            // Hapus item
            container.addEventListener('click', function (e) {
                if (e.target && (e.target.classList.contains('btn-remove-item') || e.target.closest('.btn-remove-item'))) {
                    e.target.closest('.repeater-item').remove();
                }
            });

            // Saat stok dipilih
            container.addEventListener('change', function (e) {
                if (e.target && e.target.classList.contains('stock-select')) {
                    updateRowInfo(e.target);
                }
            });

            // Inisialisasi awal
            updateToOptions();
            filterStocksBySourceLocation();

            const firstStockSelect = container.querySelector('.stock-select');
            if (firstStockSelect && firstStockSelect.value) {
                updateRowInfo(firstStockSelect);
            }

            // Konfirmasi submit
            if (form) {
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
            }

            // Aktifkan tombol submit jika ada perubahan pada form
            const submitBtn = document.getElementById('btn-submit');

            function isFormValid() {
                // lokasi harus beda
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

            function updateSubmitButton() {
                submitBtn.disabled = !isFormValid();
            }

            // 🔁 Pantau perubahan form
            form.addEventListener('input', updateSubmitButton);
            form.addEventListener('change', updateSubmitButton);

            // Saat tambah / hapus item
            btnAddItem.addEventListener('click', () => {
                setTimeout(updateSubmitButton, 50);
            });

            container.addEventListener('click', e => {
                if (e.target.closest('.btn-remove-item')) {
                    setTimeout(updateSubmitButton, 50);
                }
            });

            // Inisialisasi awal
            updateSubmitButton();

        });
    </script>
@endpush