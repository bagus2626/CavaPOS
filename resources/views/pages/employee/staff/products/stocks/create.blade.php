@extends('layouts.staff')

@section('title', __('messages.owner.products.stocks.create_title') ?? 'Add New Stock')
@section('page_title', __('messages.owner.products.stocks.create_page_title') ?? 'Create Stock')

@php
    $staffRoutePrefix = strtolower(auth('employee')->user()->role);
@endphp

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.stocks.create_title') ?? 'Create Stock' }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.products.stocks.create_subtitle') ?? 'Register a new raw material or linked stock item.' }}</p>
                </div>
                <a href="{{ route('employee.' . $staffRoutePrefix . '.stocks.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.products.stocks.back') ?? 'Back' }}
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                    </div>
                    <div class="alert-content">
                        <strong>{{ __('messages.owner.products.stocks.create_errors_title') ?? 'Please check your input:' }}</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
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

            <div class="modern-card">
                <form action="{{ route('employee.' . $staffRoutePrefix . '.stocks.store') }}" method="POST" id="stockForm">
                    @csrf

                    <div class="card-body-modern">
                        <div class="account-section">
                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">inventory</span>
                                </div>
                                <h3 class="section-title">{{ __('messages.owner.products.stocks.create_card_title') ?? 'Stock Information' }}</h3>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern d-block">
                                            {{ __('messages.owner.products.stocks.use_existing_switch_label') ?? 'Use Existing Product Name' }}
                                        </label>
                                        <input type="hidden" name="use_product_name" value="0">
                                        <div class="status-switch">
                                            <label class="switch-modern">
                                                <input type="checkbox"
                                                       id="use_product_name_switch"
                                                       name="use_product_name"
                                                       value="1"
                                                       {{ old('product_id') ? 'checked' : '' }}>
                                                <span class="slider-modern"></span>
                                            </label>
                                            <span class="status-label" id="useProductLabel">
                                                {{ old('product_id') ? (__('messages.owner.products.stocks.enabled') ?? 'Enabled') : (__('messages.owner.products.stocks.disabled') ?? 'Disabled') }}
                                            </span>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            {{ __('messages.owner.products.stocks.use_existing_switch_help') ?? 'Turn this on to use a product name that is already registered.' }}
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-12" id="productPickerWrap" style="{{ old('product_id') ? '' : 'display:none;' }}">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.choose_product_label') ?? 'Choose Product' }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="select-wrapper">
                                            <select id="product_picker" class="form-control-modern">
                                                <option value="">{{ __('messages.owner.products.stocks.choose_product_placeholder') ?? 'Select product...' }}</option>
                                                @foreach($master_products as $p)
                                                    <option value="{{ $p->id }}" 
                                                            data-name="{{ $p->name }}" 
                                                            {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                                        {{ $p->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            {{ __('messages.owner.products.stocks.choose_product_help') ?? 'The stock name will automatically match the selected product.' }}
                                        </small>
                                    </div>
                                </div>

                                <input type="hidden" 
                                       id="product_id" 
                                       name="product_id" 
                                       value="{{ old('product_id') }}" 
                                       {{ old('product_id') ? '' : 'disabled' }}>

                                <div class="col-md-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.stock_name_label') ?? 'Stock Item Name' }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control-modern @error('stock_name') is-invalid @enderror"
                                               id="stock_name"
                                               name="stock_name"
                                               value="{{ old('stock_name') }}"
                                               placeholder="{{ __('messages.owner.products.stocks.stock_name_placeholder') ?? 'e.g. Fresh Milk' }}"
                                               required
                                               maxlength="150"
                                               {{ old('product_id') ? 'readonly' : '' }}>
                                        @error('stock_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.display_unit_label') ?? 'Unit' }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="select-wrapper">
                                            <select id="unit_id"
                                                    name="unit_id"
                                                    class="form-control-modern @error('unit_id') is-invalid @enderror"
                                                    required>
                                                <option value="">{{ __('messages.owner.products.stocks.display_unit_placeholder') ?? 'Select Unit...' }}</option>
                                                @foreach($master_units as $unit)
                                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                        {{ $unit->unit_name }} ({{ $unit->group_label }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            {{ __('messages.owner.products.stocks.display_unit_help') ?? 'Select the unit of measurement for this item.' }}
                                        </small>
                                        @error('unit_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.stocks.description_label') ?? 'Description' }}
                                        </label>
                                        <textarea id="description"
                                                  name="description"
                                                  class="form-control-modern @error('description') is-invalid @enderror"
                                                  rows="3"
                                                  placeholder="{{ __('messages.owner.products.stocks.description_placeholder') ?? 'Optional description' }}">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer-modern">
                        <a href="{{ route('employee.' . $staffRoutePrefix . '.stocks.index') }}" class="btn-cancel-modern">
                            {{ __('messages.owner.products.stocks.cancel') ?? 'Cancel' }}
                        </a>
                        <button type="submit" class="btn-submit-modern">
                            {{ __('messages.owner.products.stocks.submit_button') ?? 'Save Stock' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
window.stockLang = {
    enabled: "{{ __('messages.owner.products.stocks.enabled') ?? 'Enabled' }}",
    disabled: "{{ __('messages.owner.products.stocks.disabled') ?? 'Disabled' }}"
};

(function () {
    const useSwitch = document.getElementById('use_product_name_switch');
    const pickerWrap = document.getElementById('productPickerWrap');
    const picker = document.getElementById('product_picker');
    const productId = document.getElementById('product_id');
    const stockName = document.getElementById('stock_name');
    const useProductLabel = document.getElementById('useProductLabel');

    function setUseProductUI(on) {
        pickerWrap.style.display = on ? '' : 'none';
        
        if (on) {
            productId.removeAttribute('disabled');
            stockName.setAttribute('readonly', 'readonly');

            if (picker && picker.value) {
                productId.value = picker.value;
                const opt = picker.selectedOptions[0];
                if (opt && opt.dataset.name) {
                    stockName.value = opt.dataset.name;
                }
            }
        } else {
            productId.setAttribute('disabled', 'disabled');
            productId.value = '';
            stockName.removeAttribute('readonly');
            stockName.value = '';
        }

        if (useProductLabel) {
            useProductLabel.textContent = on ? window.stockLang.enabled : window.stockLang.disabled;
        }
    }

    if (useSwitch) {
        useSwitch.addEventListener('change', () => setUseProductUI(useSwitch.checked));
        setUseProductUI(useSwitch.checked);
    }

    if (picker) {
        picker.addEventListener('change', () => {
            const id = picker.value;
            const opt = picker.selectedOptions[0];

            productId.value = id;

            if (id && useSwitch.checked) {
                if (opt && opt.dataset.name) {
                    stockName.value = opt.dataset.name;
                }
            }
        });
    }
})();
</script>
@endpush