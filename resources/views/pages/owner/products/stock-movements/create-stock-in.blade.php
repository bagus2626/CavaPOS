@extends('layouts.owner')

@section('title', 'Stock In')
@section('page_title', 'Mencatat Stok Masuk (Stock In)')

@section('content')
    <section class="content">
        <div class="container-fluid owner-stocks">
            <form action="{{ route('owner.user-owner.stocks.movements.store') }}" method="POST" id="stockMovementForm">
                @csrf
                <input type="hidden" name="movement_type" value="in">

                {{-- CARD 1: DETAIL TRANSAKSI --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Detail Transaksi Stock In</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Lokasi Tujuan (TO) --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location_to">Lokasi Tujuan (Masuk Ke) <span
                                            class="text-danger">*</span></label>
                                    <select name="location_to" id="location_to" class="form-control" required>
                                        <option value="_owner">Gudang Owner</option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- Kategori Transaksi --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">Kategori Transaksi <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="purchase">Pembelian dari Supplier</option>
                                        <option value="transfer_in">Transfer Masuk</option>
                                    </select>
                                </div>
                            </div>
                            {{-- Catatan --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Catatan (Opsional)</label>
                                    <input type="text" name="notes" id="notes" class="form-control"
                                        placeholder="Misal: Invoice #12345 atau Purchase Order #PO-001">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: ITEM TRANSAKSI --}}
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Item yang Masuk</h3>
                    </div>
                    <div class="card-body">
                        <div id="item-repeater-container">
                            {{-- Item pertama --}}
                            <div class="row repeater-item align-items-end mb-3">
                                <div class="col-md-4">
                                    <label>Item Stok <span class="text-danger">*</span></label>
                                    <select name="items[0][stock_id]" class="form-control stock-select" required>
                                        <option value="">Pilih item stok...</option>
                                        @foreach ($stocks as $stock)
                                            <option value="{{ $stock->id }}"
                                                data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                                                data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                                                data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}">
                                                {{ $stock->stock_name }}
                                                ({{ $stock->partner->name ?? 'Gudang Owner' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Jumlah <span class="text-danger">*</span></label>
                                    <input type="number" name="items[0][quantity]" class="form-control quantity-input"
                                        step="0.01" placeholder="0.00" required>
                                </div>
                                <div class="col-md-2">
                                    <label>Unit <span class="text-danger">*</span></label>
                                    <select name="items[0][unit_id]" class="form-control unit-select" required>
                                        <option value="">Pilih item dulu</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Harga Beli</label>
                                    <input type="number" name="items[0][unit_price]" class="form-control" step="0.01"
                                        placeholder="0.00">
                                </div>
                                <div class="col-md-1">
                                    {{-- Tombol Hapus tidak ada --}}
                                </div>
                            </div>
                        </div>

                        <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-plus"></i> Tambah Item
                        </button>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Stock In
                    </button>
                </div>
            </form>
        </div>
    </section>

    <template id="item-repeater-template">
        <div class="row repeater-item align-items-end mb-3">
            <div class="col-md-4">
                <label>Item Stok <span class="text-danger">*</span></label>
                <select name="items[__INDEX__][stock_id]" class="form-control stock-select" required>
                    <option value="">Pilih item stok...</option>
                    @foreach ($stocks as $stock)
                        <option value="{{ $stock->id }}" data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                            data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                            data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}">
                            {{ $stock->stock_name }}
                            ({{ $stock->partner->name ?? 'Gudang Owner' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Jumlah <span class="text-danger">*</span></label>
                <input type="number" name="items[__INDEX__][quantity]" class="form-control quantity-input" step="0.01"
                    placeholder="0.00" required>
            </div>
            <div class="col-md-2">
                <label>Unit <span class="text-danger">*</span></label>
                <select name="items[__INDEX__][unit_id]" class="form-control unit-select" required>
                    <option value="">Pilih item dulu</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Harga Beli / Unit</label>
                <input type="number" name="items[__INDEX__][unit_price]" class="form-control" step="0.01"
                    placeholder="0.00">
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

                if (!selectedOption || !selectedOption.value) {
                    const row = stockSelectElement.closest('.repeater-item');
                    const unitSelect = row.querySelector('.unit-select');
                    unitSelect.innerHTML = '<option value="">Pilih item dulu</option>';
                    return;
                }

                const unitGroup = selectedOption.getAttribute('data-unit-group');
                const displayUnitId = selectedOption.getAttribute('data-display-unit-id');

                const row = stockSelectElement.closest('.repeater-item');
                const unitSelect = row.querySelector('.unit-select');

                unitSelect.innerHTML = '<option value="">Pilih unit...</option>';

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
                        title: 'Konfirmasi Stock In',
                        text: "Pastikan data lokasi dan item sudah benar.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#8c1000',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Simpan!',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            }
        });
    </script>
@endsection