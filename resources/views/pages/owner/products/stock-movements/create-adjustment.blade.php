@extends('layouts.owner')

@section('title', 'Stock Adjustment')
@section('page_title', 'Mencatat Penyesuaian Stok (Adjustment)')

@section('content')
    <section class="content">
        <div class="container-fluid owner-stocks">
            <form action="{{ route('owner.user-owner.stocks.movements.store') }}" method="POST" id="stockMovementForm">
                @csrf
                <input type="hidden" name="movement_type" value="out">

                {{-- CARD 1: DETAIL TRANSAKSI --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Detail Transaksi Adjustment</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Lokasi Asal (FROM) --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location_from">Lokasi Asal (Keluar Dari) <span
                                            class="text-danger">*</span></label>
                                    <select name="location_from" id="location_from" class="form-control" required>
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
                                    <label for="category">Alasan Adjustment <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="damaged">Barang Rusak</option>
                                        <option value="expired">Barang Kedaluwarsa</option>
                                        <option value="internal_use">Pemakaian Internal</option>
                                        <option value="lost">Barang Hilang</option>
                                        <option value="audit_adjustment">Penyesuaian (Audit)</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Catatan --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Catatan (Opsional)</label>
                                    <input type="text" name="notes" id="notes" class="form-control"
                                        placeholder="Misal: Sawi busuk 2kg atau Kedaluwarsa tanggal 20 Nov 2024">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: ITEM TRANSAKSI --}}
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Item yang Dikurangi</h3>
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
                                    {{-- Kolom kosong untuk alignment --}}
                                </div>
                                <div class="col-md-1">
                                    {{-- Tombol Hapus tidak ada untuk item pertama --}}
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Tambah Item --}}
                        <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-plus"></i> Tambah Item
                        </button>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save"></i> Simpan Adjustment
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
                {{-- Kolom kosong untuk alignment --}}
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
            const locationSelect = document.getElementById('location_from');
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
        });
    </script>
@endsection