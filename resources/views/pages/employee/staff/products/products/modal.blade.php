@php
    // Dapatkan role employee (manager atau supervisor) untuk prefix route
    $staffRoutePrefix = strtolower(auth('employee')->user()->role);
@endphp

{{-- 1. MODAL PILIHAN (ACTION CHOOSER) MENGGUNAKAN EXISTING CLASS --}}
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern-modal">
            <div class="modal-header modern-modal-header">
                <h5 class="modal-title" id="addProductModalLabel">
                    <span class="material-symbols-outlined">add_circle</span>
                    Tambah Produk
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding: var(--spacing-lg);">

                {{-- Opsi 1: Dari Master Product (Menggunakan style modern-card & Bootstrap utilities) --}}
                <a href="javascript:void(0)" class="modern-card p-3 mb-3 d-flex align-items-center text-decoration-none"
                    data-dismiss="modal" data-toggle="modal" data-target="#addFromMasterModal"
                    style="border: 1px solid var(--border-color); cursor: pointer;">

                    <div class="flex-grow-1 text-left text-dark">
                        <h6 class="mb-1 fw-bold">
                            Pilih dari Master Product
                        </h6>
                        {{-- <span class="text-muted small d-block" style="line-height: 1.4;">Ambil produk yang sudah
                            didaftarkan oleh owner.</span> --}}
                    </div>
                    <span class="material-symbols-outlined text-muted ml-2">chevron_right</span>
                </a>

                {{-- Opsi 2: Buat Sendiri / Custom Product --}}
                <a href="{{ route('employee.' . $staffRoutePrefix . '.products.create') }}"
                    class="modern-card p-3 d-flex align-items-center text-decoration-none"
                    style="border: 1px solid var(--border-color); cursor: pointer;">

                    <div class="flex-grow-1 text-left text-dark">
                        <h6 class="mb-1 fw-bold">
                            Buat Produk Sendiri
                        </h6>
                        {{-- <span class="text-muted small d-block" style="line-height: 1.4;">Buat produk baru yang
                            khusus
                            hanya ada di outlet ini.</span> --}}
                    </div>
                    <span class="material-symbols-outlined text-muted ml-2">chevron_right</span>
                </a>
            </div>
        </div>
    </div>
</div>


{{-- 2. MODAL FORM DARI MASTER PRODUCT (Kode Anda sebelumnya) --}}
<div class="modal fade" id="addFromMasterModal" tabindex="-1" aria-labelledby="addFromMasterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form id="outletProductQuickAddForm" method="POST"
            action="{{ route('employee.' . $staffRoutePrefix . '.products.store') }}"
            class="modal-content modern-modal">
            @csrf

            <div class="modal-header modern-modal-header">

                <h5 class="modal-title" id="addFromMasterModalLabel" style="flex: 1;">
                    <span class="material-symbols-outlined">inventory</span>
                    Pilih dari Master Product
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding: var(--spacing-lg);">
                {{-- ====== Info ====== --}}
                <div class="mb-4 mt-2" style="display:flex; gap:12px; align-items:flex-start; padding: var(--spacing-md);
                    background: var(--info-light, #e3f2fd); border-left: 4px solid var(--info, #2196F3);
                    border-radius: var(--radius-sm);">
                    <span class="material-symbols-outlined"
                        style="color: var(--info, #2196F3); flex-shrink:0; font-size:22px;">
                        tips_and_updates
                    </span>
                    <div style="font-size: 0.95rem; line-height: 1.45;">
                        <strong style="display:block; color: var(--info, #2196F3); margin-bottom: 4px;">
                            {{ __('messages.owner.products.outlet_products.how_to_add_product') }}
                        </strong>

                        <ol style="margin: 0; padding-left: 18px; color: var(--text-secondary);">
                            {!! __('messages.owner.products.outlet_products.step_information') !!}
                        </ol>
                    </div>
                </div>

                {{-- Category --}}
                <div class="form-group-modern">
                    <label for="qp_category_id" class="form-label-modern">
                        <span class="material-symbols-outlined"
                            style="font-size: 16px; vertical-align: middle; margin-right: 4px;">category</span>
                        {{ __('messages.owner.products.outlet_products.category') }}
                        <span style="color: var(--danger);">*</span>
                    </label>
                    <div class="select-wrapper">
                        <select id="qp_category_id" name="category_id" class="form-control-modern" required>
                            <option value="all">
                                {{ __('messages.owner.products.outlet_products.all_category_dropdown') }}
                            </option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                    </div>
                    <div class="invalid-feedback">
                        {{ __('messages.owner.products.outlet_products.select_category_first') }}
                    </div>
                </div>

                {{-- Master Product --}}
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <span class="material-symbols-outlined"
                            style="font-size: 16px; vertical-align: middle; margin-right: 4px;">inventory</span>
                        {{ __('messages.owner.products.outlet_products.master_products') }}
                        <span style="color: var(--danger);">*</span>
                    </label>

                    <div class="checkbox-modern"
                        style="margin-bottom: var(--spacing-sm); padding: var(--spacing-sm) var(--spacing-md); background: var(--input-bg); border-radius: var(--radius-sm);">
                        <input class="form-check-input" type="checkbox" id="qp_check_all" disabled
                            style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                        <label class="form-check-label" for="qp_check_all" style="cursor: pointer; font-weight: 600;">
                            {{ __('messages.owner.products.outlet_products.select_all') }}
                        </label>
                    </div>

                    <div id="qp_master_product_box"
                        style="max-height: 280px; overflow: auto; padding: var(--spacing-md); background: var(--card-bg); border: 2px solid var(--border-color); border-radius: var(--radius-sm);">
                        <div style="color: var(--text-muted); font-size: 0.875rem;">
                            {{ __('messages.owner.products.outlet_products.select_category_first') }}
                        </div>
                    </div>

                    <div class="invalid-feedback d-block" id="qp_mp_error" style="display:none;">
                        {{ __('messages.owner.products.outlet_products.at_least_one_master') }}
                    </div>
                </div>

                {{-- STOCK TYPE --}}
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <span class="material-symbols-outlined"
                            style="font-size: 16px; vertical-align: middle; margin-right: 4px;">settings</span>
                        {{ __('messages.owner.products.outlet_products.stock_management') }}
                        <span style="color: var(--danger);">*</span>
                    </label>

                    <div class="radio-modern"
                        style="padding: var(--spacing-md); background: var(--card-bg); border: 2px solid var(--border-color); border-radius: var(--radius-sm); transition: all var(--transition-base); cursor: pointer;"
                        onclick="document.getElementById('stock_type_direct').click()">
                        <input type="radio" name="stock_type" id="stock_type_direct" value="direct" checked required
                            style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                        <label for="stock_type_direct" style="cursor: pointer; flex: 1; margin: 0;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <strong
                                    style="color: var(--text-primary);">{{ __('messages.owner.products.outlet_products.direct_stock_input') }}</strong>
                            </div>
                        </label>
                    </div>

                    <div class="radio-modern"
                        style="padding: var(--spacing-md); background: var(--card-bg); border: 2px solid var(--border-color); border-radius: var(--radius-sm); margin-top: var(--spacing-sm); transition: all var(--transition-base); cursor: pointer;"
                        onclick="document.getElementById('stock_type_linked').click()">
                        <input type="radio" name="stock_type" id="stock_type_linked" value="linked" required
                            style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                        <label for="stock_type_linked" style="cursor: pointer; flex: 1; margin: 0;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <strong
                                    style="color: var(--text-primary);">{{ __('messages.owner.products.outlet_products.link_to_raw_materials') }}</strong>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Quantity --}}
                <div class="form-group-modern" id="qp_quantity_group">
                    <label for="qp_quantity" class="form-label-modern">
                        <span class="material-symbols-outlined"
                            style="font-size: 16px; vertical-align: middle; margin-right: 4px;">package</span>
                        {{ __('messages.owner.products.outlet_products.stock') }}
                        <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="number" min="0" step="1" id="qp_quantity" name="quantity" class="form-control-modern"
                        value="0" placeholder="0" required>
                    <small
                        style="color: var(--text-muted); font-size: 0.875rem; display: block; margin-top: 0.25rem; margin-left: var(--spacing-xs);">
                        {{ __('messages.owner.products.outlet_products.enter_initial_stock') }}
                    </small>
                </div>

                {{-- Info message --}}
                <div class="d-none" id="linked_stock_info"
                    style="display: flex; align-items: flex-start; gap: var(--spacing-md); padding: var(--spacing-md); background: var(--info-light, #e3f2fd); border-left: 4px solid var(--info, #2196F3); border-radius: var(--radius-sm); margin-top: var(--spacing-md);">
                    <span class="material-symbols-outlined"
                        style="color: var(--info, #2196F3); flex-shrink: 0; font-size: 20px;">info</span>
                    <div style="font-size: 0.9rem;">
                        <strong style="color: var(--info, #2196F3); display: block; margin-bottom: 0.25rem;">
                            {{ __('messages.owner.products.outlet_products.note_linked_stock') }}
                        </strong>
                        <span style="color: var(--text-secondary);">
                            {{ __('messages.owner.products.outlet_products.linked_stock_info') }}
                        </span>
                    </div>
                </div>

                {{-- Status --}}
                <div class="form-group-modern">
                    <label for="qp_is_active" class="form-label-modern">
                        <span class="material-symbols-outlined"
                            style="font-size: 16px; vertical-align: middle; margin-right: 4px;">toggle_on</span>
                        {{ __('messages.owner.products.outlet_products.status') }}
                    </label>
                    <div class="select-wrapper">
                        <select id="qp_is_active" name="is_active" class="form-control-modern">
                            <option value="1">{{ __('messages.owner.products.outlet_products.active') }}</option>
                            <option value="0">{{ __('messages.owner.products.outlet_products.inactive') }}</option>
                        </select>
                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer modern-modal-footer">
                <button type="button" class="btn-cancel-modern" data-dismiss="modal">
                    {{ __('messages.owner.products.outlet_products.cancel') }}
                </button>
                <button type="submit" class="btn-submit-modern">
                    <span class="label">{{ __('messages.owner.products.outlet_products.save') }}</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Stock Type Styles (Ini bawaan asli dari kode Anda, dipertahankan) --}}
<style>
    /* Highlight border saat radio button di-check */
    .radio-modern:has(input[type="radio"]:checked) {
        border-color: var(--primary) !important;
        background-color: var(--primary-light, rgba(174, 21, 4, 0.05)) !important;
        box-shadow: 0 0 0 3px rgba(174, 21, 4, 0.1);
    }

    .radio-modern:hover {
        border-color: var(--primary);
        background-color: var(--input-bg-hover);
    }

    /* Checkbox dalam master product box */
    #qp_master_product_box .checkbox-modern {
        padding: var(--spacing-sm);
        margin-bottom: var(--spacing-xs);
        border-radius: var(--radius-sm);
        transition: background var(--transition-fast);
    }

    #qp_master_product_box .checkbox-modern:hover {
        background: var(--input-bg-hover);
    }
</style>

{{-- Stock Type Toggle Logic --}}
<script>
    (function () {
        const directRadio = document.getElementById('stock_type_direct');
        const linkedRadio = document.getElementById('stock_type_linked');
        const qtyGroup = document.getElementById('qp_quantity_group');
        const qtyInput = document.getElementById('qp_quantity');
        const linkedInfo = document.getElementById('linked_stock_info');

        function syncStockTypeUI() {
            if (!directRadio || !linkedRadio || !qtyGroup || !qtyInput || !linkedInfo) return;

            if (linkedRadio.checked) {
                qtyGroup.classList.add('d-none');
                qtyInput.required = false;
                qtyInput.value = '0';
                linkedInfo.classList.remove('d-none');
            } else {
                qtyGroup.classList.remove('d-none');
                qtyInput.required = true;
                linkedInfo.classList.add('d-none');
            }
        }

        directRadio?.addEventListener('change', syncStockTypeUI);
        linkedRadio?.addEventListener('change', syncStockTypeUI);

        // Reset ke default saat modal master product dibuka
        $('#addFromMasterModal').on('shown.bs.modal', function () {
            if (directRadio) directRadio.checked = true;
            syncStockTypeUI();
        });

        syncStockTypeUI();
    })();
</script>