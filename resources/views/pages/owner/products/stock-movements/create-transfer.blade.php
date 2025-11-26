@extends('layouts.owner')

@section('title', 'Transfer Stock')
@section('page_title', 'Mencatat Transfer Stok')

@section('content')
    <section class="content">
        <div class="container-fluid owner-stocks">
            <form action="{{ route('owner.user-owner.stock-movements.store') }}" method="POST" id="stockMovementForm">
                @csrf
                <input type="hidden" name="movement_type" value="transfer" id="movement_type">

                {{-- CARD 1: DETAIL TRANSAKSI --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Detail Transaksi Transfer</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Lokasi Asal (FROM) --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location_from">Lokasi Asal (Dari) <span class="text-danger">*</span></label>
                                    <select name="location_from" id="location_from" class="form-control" required>
                                        <option value="_owner">Gudang Owner</option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Lokasi Tujuan (TO) --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location_to">Lokasi Tujuan (Ke) <span class="text-danger">*</span></label>
                                    <select name="location_to" id="location_to" class="form-control" required>
                                        <option value="_owner">Gudang Owner</option>
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
                                    <label for="notes">Catatan (Opsional)</label>
                                    <input type="text" name="notes" id="notes" class="form-control"
                                        placeholder="Misal: Transfer untuk restocking outlet atau Transfer nomor #TF-001">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: ITEM TRANSAKSI --}}
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Item yang Ditransfer</h3>
                    </div>
                    <div class="card-body">
                        <div id="item-repeater-container">
                            {{-- Item pertama --}}
                            <div class="row repeater-item mb-3">
                                <div class="col-md-5">
                                    <label>Item Stok <span class="text-danger">*</span></label>
                                    <select name="items[0][stock_id]" class="form-control stock-select" required>
                                        <option value="">Pilih item stok...</option>
                                        @foreach ($stocks as $stock)
                                            <option value="{{ $stock->id }}"
                                                data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                                                data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                                                data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}"
                                                data-current-qty="{{ number_format($stock->display_quantity ?? 0, 2) }}"
                                                data-current-unit="{{ $stock->displayUnit->unit_name ?? 'unit' }}">
                                                {{ $stock->stock_name }}
                                                ({{ $stock->partner->name ?? 'Gudang Owner' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="current-stock-info text-muted small mt-1" style="display: none;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label>Jumlah <span class="text-danger">*</span></label>
                                    <input type="number" name="items[0][quantity]" class="form-control quantity-input"
                                        step="0.01" placeholder="0.00" required>
                                    <div style="height: 21px;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label>Unit <span class="text-danger">*</span></label>
                                    <select name="items[0][unit_id]" class="form-control unit-select" required>
                                        <option value="">Pilih item</option>
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
                            <i class="fas fa-plus"></i> Tambah Item
                        </button>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4">
                    <a href="{{ route('owner.user-owner.stock-movements.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save"></i> Simpan Transfer
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- TEMPLATE --}}
    <template id="item-repeater-template">
        <div class="row repeater-item mb-3">
            <div class="col-md-5">
                <label>Item Stok <span class="text-danger">*</span></label>
                <select name="items[__INDEX__][stock_id]" class="form-control stock-select" required>
                    <option value="">Pilih item stok...</option>
                    @foreach ($stocks as $stock)
                        <option value="{{ $stock->id }}" data-location-id="{{ $stock->partner_id ?? '_owner' }}"
                            data-unit-group="{{ $stock->displayUnit->group_label ?? 'pcs' }}"
                            data-display-unit-id="{{ $stock->displayUnit->id ?? '' }}"
                            data-current-qty="{{ number_format($stock->display_quantity ?? 0, 2) }}"
                            data-current-unit="{{ $stock->displayUnit->unit_name ?? 'unit' }}">
                            {{ $stock->stock_name }}
                            ({{ $stock->partner->name ?? 'Gudang Owner' }})
                        </option>
                    @endforeach
                </select>
                <div class="current-stock-info text-muted small mt-1" style="display: none;"></div>
            </div>
            <div class="col-md-2">
                <label>Jumlah <span class="text-danger">*</span></label>
                <input type="number" name="items[__INDEX__][quantity]" class="form-control quantity-input" step="0.01"
                    placeholder="0.00" required>
                <div style="height: 21px;"></div>
            </div>
            <div class="col-md-2">
                <label>Unit <span class="text-danger">*</span></label>
                <select name="items[__INDEX__][unit_id]" class="form-control unit-select" required>
                    <option value="">Pilih item</option>
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

            if (!container || !template || !btnAddItem || !locationFrom || !locationTo) {
                return;
            }
            if (typeof allUnits === 'undefined') {
                console.error('Error: Variabel `allUnits` tidak ditemukan.');
                return;
            }

            let itemIndex = 1;

            // Filter Stok Berdasarkan Lokasi Asal
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

                if (currentQty && infoBox) {
                    infoBox.textContent = `Stok Tersedia: ${currentQty} ${currentUnit}`;
                    infoBox.style.display = 'block';
                }
            }

            // Update Opsi Lokasi Tujuan (Auto Select Default)
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

            // Saat Lokasi Asal (From) Berubah
            locationFrom.addEventListener('change', function () {
                updateToOptions();
                filterStocksBySourceLocation();
            });

            // Tombol Tambah Item 
            btnAddItem.addEventListener('click', function () {
                let templateHTML = template.innerHTML.replace(/__INDEX__/g, itemIndex);
                const newRow = document.createElement('div');
                newRow.innerHTML = templateHTML;
                container.appendChild(newRow.firstElementChild);
                filterStocksBySourceLocation();
                itemIndex++;
            });

            // Hapus Item
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

            updateToOptions();
            filterStocksBySourceLocation();

            // Update info baris pertama jika sudah terisi
            const firstStockSelect = container.querySelector('.stock-select');
            if (firstStockSelect && firstStockSelect.value) {
                updateRowInfo(firstStockSelect);
            }

            // Konfirmasi
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    if (locationFrom.value === locationTo.value) {
                        Swal.fire('Validasi Gagal', 'Lokasi asal dan tujuan tidak boleh sama!', 'error');
                        return false;
                    }

                    Swal.fire({
                        title: 'Konfirmasi Transfer',
                        text: "Pastikan data item dan lokasi sudah benar.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#8c1000',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Transfer!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });
    </script>
@endpush