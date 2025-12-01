@extends('layouts.owner')

@section('title', 'Buat Transaksi Stok')
@section('page_title', 'Buat Transaksi Stok Baru')

@section('content')
    <section class="content">
        <div class="container-fluid owner-stocks">
            <form action="{{ route('owner.user-owner.movements.store') }}" method="POST" id="stockMovementForm">
                @csrf
                {{-- Hidden input untuk Tipe Transaksi --}}
                <input type="hidden" name="movement_type" id="movement_type" value="{{ $type }}">

                {{-- CARD 1: DETAIL TRANSAKSI --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold" id="form-title">Mencatat Stok Baru</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            {{-- Lokasi Asal (FROM) --}}
                            <div class="col-md-4" id="location_from_group">
                                <div class="form-group">
                                    <label for="location_from">Lokasi Asal (Dari)</label>
                                    <select name="location_from" id="location_from" class="form-control">
                                        <option value="_owner">Gudang Owner</option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Lokasi Tujuan (TO) --}}
                            <div class="col-md-4" id="location_to_group">
                                <div class="form-group">
                                    <label for="location_to">Lokasi Tujuan (Ke)</label>
                                    <select name="location_to" id="location_to" class="form-control">
                                        <option value="_owner">Gudang Owner</option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Kategori Transaksi (Dinamis) --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category">Kategori/Alasan Transaksi</label>
                                    <select name="category" id="category" class="form-control" required>
                                        {{-- Opsi akan diisi oleh JavaScript --}}
                                    </select>
                                </div>
                            </div>

                            {{-- Catatan --}}
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="notes">Catatan (Opsional)</label>
                                    <input type="text" name="notes" id="notes" class="form-control"
                                        placeholder="Misal: Invoice #12345 atau Sawi busuk 2kg">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: ITEM TRANSAKSI (REPEATER) --}}
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Item</h3>
                    </div>
                    <div class="card-body">
                        <div id="item-repeater-container">
                            {{-- Item pertama (index 0) --}}
                            <div class="row repeater-item align-items-end mb-3">
                                <div class="col-md-4">
                                    <label>Item Stok</label>
                                    <select name="items[0][stock_id]" class="form-control stock-select" required>
                                        <option value="">Pilih item stok...</option>
                                        @foreach ($stocks as $stock)
                                            <option value="{{ $stock->id }}"
                                                data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                                                data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                                                data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}"
                                                data-current-qty="{{ $stock->display_quantity ?? 0 }}"
                                                data-current-unit="{{ $stock->displayUnit->unit_name ?? 'unit' }}">
                                                {{ $stock->stock_name }}
                                                ({{ $stock->partner->name ?? 'Gudang Owner' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="current-stock-info text-muted small mt-1" style="display: none;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label>Jumlah</label>
                                    <input type="number" name="items[0][quantity]" class="form-control quantity-input"
                                        step="0.01" placeholder="0.00" required>
                                    <div style="height: 21px;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label>Unit</label>
                                    <select name="items[0][unit_id]" class="form-control unit-select" required>
                                        <option value="">Pilih item</option>
                                    </select>
                                    <div style="height: 21px;"></div>
                                </div>
                                <div class="col-md-3 unit-price-group">
                                    <label>Harga Beli / Unit</label>
                                    <input type="number" name="items[0][unit_price]" class="form-control" step="0.01"
                                        placeholder="0.00">
                                    <div style="height: 21px;"></div>
                                </div>
                                <div class="col-md-1">
                                    {{-- Tombol Hapus tidak ada untuk item pertama --}}
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Tambah Item --}}
                        <button type="button" id="btn-add-item" class="text-[#8c1000] mt-2">
                            <i class="fas fa-plus"></i> Tambah Item
                        </button>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4">
                    <a href="{{ route('owner.user-owner.stocks.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </section>

    <template id="item-repeater-template">
        <div class="row repeater-item align-items-end mb-3">
            <div class="col-md-4">
                <label>Item Stok</label>
                <select name="items[__INDEX__][stock_id]" class="form-control stock-select" required>
                    <option value="">Pilih item stok...</option>
                    @foreach ($stocks as $stock)
                        <option value="{{ $stock->id }}" data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                            data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                            data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}"
                            data-current-qty="{{ $stock->display_quantity ?? 0 }}"
                            data-current-unit="{{ $stock->displayUnit->unit_name ?? 'unit' }}">
                            {{ $stock->stock_name }}
                            ({{ $stock->partner->name ?? 'Gudang Owner' }})
                        </option>
                    @endforeach
                </select>
                <div class="current-stock-info text-muted small mt-1" style="display: none;"></div>
            </div>
            <div class="col-md-2">
                <label>Jumlah</label>
                <input type="number" name="items[__INDEX__][quantity]" class="form-control quantity-input" step="0.01"
                    placeholder="0.00" required>
                <div style="height: 21px;"></div>
            </div>
            <div class="col-md-2">
                <label>Unit</label>
                <select name="items[__INDEX__][unit_id]" class="form-control unit-select" required>
                    <option value="">Pilih item</option>
                </select>
                <div style="height: 21px;"></div>
            </div>
            <div class="col-md-3 unit-price-group">
                <label>Harga Beli / Unit</label>
                <input type="number" name="items[__INDEX__][unit_price]" class="form-control" step="0.01"
                    placeholder="0.00">
                <div style="height: 21px;"></div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </template>

    <style>
        .btn-primary:active,
        .btn-primary:focus {
            background-color: #8C1000 !important;
            border-color: #8C1000 !important;
        }
    </style>

    <script>
        const allUnits = @json($allUnits);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const movementType = document.getElementById('movement_type').value;
            const formTitle = document.getElementById('form-title');
            const categorySelect = document.getElementById('category');

            // Grup Field
            const fromGroup = document.getElementById('location_from_group');
            const toGroup = document.getElementById('location_to_group');
            const locationFrom = document.getElementById('location_from');
            const locationTo = document.getElementById('location_to');

            // Opsi Kategori
            const categories = {
                in: [
                    { value: 'purchase', text: 'Pembelian dari Supplier' },
                    { value: 'transfer_in', text: 'Transfer Masuk' },
                    { value: 'customer_return', text: 'Retur dari Pelanggan' },
                    { value: 'audit_adjustment', text: 'Penyesuaian (Audit)' }
                ],
                out: [
                    { value: 'damaged', text: 'Barang Rusak' },
                    { value: 'expired', text: 'Barang Kedaluwarsa' },
                    { value: 'internal_use', text: 'Pemakaian Internal' },
                    { value: 'lost', text: 'Barang Hilang' },
                    { value: 'audit_adjustment', text: 'Penyesuaian (Audit)' }
                ],
                transfer: [
                    { value: 'transfer', text: 'Transfer Antar Gudang' }
                ]
            };

            // Fungsi untuk update UI berdasarkan Tipe
            function updateFormUI(type) {
                // 1. Kosongkan & Isi Kategori
                categorySelect.innerHTML = '';
                categories[type].forEach(cat => {
                    const option = new Option(cat.text, cat.value);
                    categorySelect.appendChild(option);
                });

                // 2. Tampilkan/Sembunyikan Field
                if (type === 'in') {
                    formTitle.textContent = 'Mencatat Stok Masuk (Stock In)';
                    fromGroup.style.display = 'none';
                    toGroup.style.display = 'block';
                    toggleUnitPrice(true);
                } else if (type === 'out') {
                    formTitle.textContent = 'Mencatat Stok Keluar (Adjustment)';
                    fromGroup.style.display = 'block';
                    toGroup.style.display = 'none';
                    toggleUnitPrice(false);
                } else if (type === 'transfer') {
                    formTitle.textContent = 'Mencatat Transfer Stok';
                    fromGroup.style.display = 'block';
                    toGroup.style.display = 'block';
                    toggleUnitPrice(false);
                }
            }

            // Fungsi untuk menampilkan/menyembunyikan kolom harga
            function toggleUnitPrice(show) {
                document.querySelectorAll('.unit-price-group').forEach(group => {
                    group.style.display = show ? 'block' : 'none';
                });
            }

            // Filter Stok Berdasarkan Lokasi
            function filterStocksByLocation() {
                let selectedLocation = '';

                if (movementType === 'in') {
                    selectedLocation = locationTo.value;
                } else if (movementType === 'out') {
                    selectedLocation = locationFrom.value;
                } else if (movementType === 'transfer') {
                    selectedLocation = locationFrom.value;
                }

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
                        updateRowInfo(select);
                    }
                });
            }

            // Update Unit & Info Kuantitas
            function updateRowInfo(stockSelectElement) {
                const selectedOption = stockSelectElement.options[stockSelectElement.selectedIndex];
                const row = stockSelectElement.closest('.repeater-item');
                if (!row) return;

                const unitSelect = row.querySelector('.unit-select');
                const infoBox = row.querySelector('.current-stock-info');

                unitSelect.innerHTML = '<option value="">Pilih unit...</option>';
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

                if (currentQty && infoBox && (movementType === 'out' || movementType === 'transfer')) {
                    infoBox.textContent = `Stok Tersedia: ${currentQty} ${currentUnit}`;
                    infoBox.style.display = 'block';
                }
            }

            // --- LOGIKA REPEATER ---
            const container = document.getElementById('item-repeater-container');
            const template = document.getElementById('item-repeater-template');
            let itemIndex = 1;

            document.getElementById('btn-add-item').addEventListener('click', function () {
                let templateHTML = template.innerHTML.replace(/__INDEX__/g, itemIndex);
                const newRow = document.createElement('div');
                newRow.innerHTML = templateHTML;
                container.appendChild(newRow.firstElementChild);

                toggleUnitPrice(movementType === 'in');
                filterStocksByLocation();

                itemIndex++;
            });

            // Event listener untuk tombol hapus
            container.addEventListener('click', function (e) {
                if (e.target && (e.target.classList.contains('btn-remove-item') || e.target.closest('.btn-remove-item'))) {
                    e.target.closest('.repeater-item').remove();
                }
            });

            // Saat Stok Dipilih (Change)
            container.addEventListener('change', function (e) {
                if (e.target && e.target.classList.contains('stock-select')) {
                    updateRowInfo(e.target);
                }
            });

            // Event listener untuk perubahan lokasi
            if (locationFrom) {
                locationFrom.addEventListener('change', filterStocksByLocation);
            }
            if (locationTo) {
                locationTo.addEventListener('change', filterStocksByLocation);
            }

            // Panggil fungsi update UI saat halaman dimuat
            updateFormUI(movementType);
            filterStocksByLocation();

            // Update info baris pertama
            const firstStockSelect = container.querySelector('.stock-select');
            if (firstStockSelect && firstStockSelect.value) {
                updateRowInfo(firstStockSelect);
            }
        });
    </script>
@endsection