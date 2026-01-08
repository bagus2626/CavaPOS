@extends('layouts.owner')

@section('title', __('messages.owner.products.promotions.create_promotion'))
@section('page_title', __('messages.owner.products.promotions.create_new_promotion'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <!-- Header Section -->
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.promotions.create_new_promotion') }}</h1>
                    <p class="page-subtitle">Create attractive promotions to boost sales and customer satisfaction.</p>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                    </div>
                    <div class="alert-content">
                        <strong>{{ __('messages.owner.products.promotions.re_check_input') }}:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
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

            <!-- Main Card -->
            <div class="modern-card">
                <form action="{{ route('owner.user-owner.promotions.store') }}" method="POST" id="promotionForm">
                    @csrf
                    <div class="card-body-modern">

                        <!-- Promotion Information Section -->
                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">sell</span>
                            </div>
                            <h3 class="section-title">
                                {{ __('messages.owner.products.promotions.promotion_information') }}
                            </h3>
                        </div>

                        <div class="row g-4">
                            <!-- Promotion Name -->
                            <div class="col-md-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.promotions.promotion_name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="promotion_name" id="promotion_name"
                                        class="form-control-modern @error('promotion_name') is-invalid @enderror"
                                        value="{{ old('promotion_name') }}"
                                        placeholder="{{ __('messages.owner.products.promotions.promotion_name_placeholder') }}"
                                        maxlength="150" required>
                                    @error('promotion_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Promotion Type -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.promotions.promotion_type') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="select-wrapper">
                                        <select name="promotion_type" id="promotion_type"
                                            class="form-control-modern @error('promotion_type') is-invalid @enderror"
                                            required>
                                            <option value="">{{ __('messages.owner.products.promotions.select_type_dropdown') }}</option>
                                            <option value="percentage" {{ old('promotion_type') === 'percentage' ? 'selected' : '' }}>
                                                {{ __('messages.owner.products.promotions.percentage') }} (%)
                                            </option>
                                            <option value="amount" {{ old('promotion_type') === 'amount' ? 'selected' : '' }}>
                                                {{ __('messages.owner.products.promotions.reduced_fare') }} (Rp)
                                            </option>
                                        </select>
                                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                                    </div>
                                    @error('promotion_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Promotion Value -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.promotions.promotion_value') }}
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-wrapper" id="valueInputWrapper">
                                        <span class="input-icon" id="prefixAmount" style="display:none;">
                                            Rp
                                        </span>

                                        <input type="number"
                                            class="form-control-modern @error('promotion_value') is-invalid @enderror"
                                            id="promotion_value"
                                            name="promotion_value"
                                            value="{{ old('promotion_value') }}"
                                            inputmode="numeric"
                                            required>

                                        <span class="input-icon"
                                            id="suffixPercent"
                                            style="display:none; left:auto; right:1rem;">
                                            %
                                        </span>
                                    </div>

                                    <small class="text-muted d-block mt-1" id="valueHelp">
                                        @if(old('promotion_type') === 'amount')
                                            {{ __('messages.owner.products.promotions.reduced_fare_example') }}
                                        @else
                                            {{ __('messages.owner.products.promotions.percentage_example') }}
                                        @endif
                                    </small>

                                    @error('promotion_value')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        {{ __('messages.owner.products.promotions.description') }}
                                    </label>
                                    <textarea name="description" id="description"
                                        class="form-control-modern @error('description') is-invalid @enderror"
                                        rows="4"
                                        placeholder="{{ __('messages.owner.products.promotions.description_placeholder') }}">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="section-divider"></div>

                        <!-- Validity Period Section -->
                        <div class="account-section">
                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">schedule</span>
                                </div>
                                <h3 class="section-title">Validity Period</h3>
                            </div>

                            @php $usesExpiryInit = old('uses_expiry', 0); @endphp

                            <div class="row g-4">
                                <!-- Uses Expiry Toggle -->
                                <div class="col-md-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern d-block">
                                            {{ __('messages.owner.products.promotions.is_use_expiry') }}
                                        </label>

                                        <input type="hidden" name="uses_expiry" value="0">

                                        <div class="status-switch">
                                            <label class="switch-modern">
                                                <input type="checkbox"
                                                    id="uses_expiry"
                                                    name="uses_expiry"
                                                    value="1"
                                                    {{ $usesExpiryInit ? 'checked' : '' }}>
                                                <span class="slider-modern"></span>
                                            </label>

                                            <span class="status-label">
                                                {{ $usesExpiryInit ? 'Enabled' : 'Disabled' }}
                                            </span>
                                        </div>

                                        <small class="text-muted d-block mt-1">
                                            {{ __('messages.owner.products.promotions.activate_if_has_expiry') }}
                                        </small>
                                    </div>
                                </div>

                                <!-- Start Date -->
                                <div class="col-md-6" id="startDateGroup" style="{{ $usesExpiryInit ? '' : 'display:none;' }}">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.promotions.start_date') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local"
                                            class="form-control-modern @error('start_date') is-invalid @enderror"
                                            id="start_date"
                                            name="start_date"
                                            value="{{ old('start_date') }}">
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- End Date -->
                                <div class="col-md-6" id="endDateGroup" style="{{ $usesExpiryInit ? '' : 'display:none;' }}">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.products.promotions.end_date') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local"
                                            class="form-control-modern @error('end_date') is-invalid @enderror"
                                            id="end_date"
                                            name="end_date"
                                            value="{{ old('end_date') }}">
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted d-block mt-1">
                                            {{ __('messages.owner.products.promotions.end_date_alert') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Divider -->
                        <div class="section-divider"></div>

                        <div class="account-section">
                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">calendar_month</span>
                                </div>
                                <h3 class="section-title">
                                    {{ __('messages.owner.products.promotions.active_day') }}
                                </h3>
                            </div>

                            @php
                                $daysMap = [
                                    'sun' => __('messages.owner.products.promotions.sunday'),
                                    'mon' => __('messages.owner.products.promotions.monday'),
                                    'tue' => __('messages.owner.products.promotions.tuesday'),
                                    'wed' => __('messages.owner.products.promotions.wednesday'),
                                    'thu' => __('messages.owner.products.promotions.thursday'),
                                    'fri' => __('messages.owner.products.promotions.friday'),
                                    'sat' => __('messages.owner.products.promotions.saturday'),
                                ];

                                // CREATE: tidak ada $data
                                $selectedDays = old('active_days', []);
                                $isEveryDay = is_array($selectedDays) && count($selectedDays) === 7;
                            @endphp

                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern d-block">
                                            {{ __('messages.owner.products.promotions.every_day') }}
                                        </label>

                                        <div class="status-switch mb-3">
                                            <label class="switch-modern">
                                                <input type="checkbox"
                                                    id="every_day"
                                                    {{ $isEveryDay ? 'checked' : '' }}>
                                                <span class="slider-modern"></span>
                                            </label>

                                            <span class="status-label">
                                                {{ $isEveryDay ? 'All Days Selected' : 'Custom Days' }}
                                            </span>
                                        </div>

                                        <small class="text-muted d-block mb-3">
                                            {{ __('messages.owner.products.promotions.tick') }}
                                            <em>"{{ __('messages.owner.products.promotions.every_day') }}"</em>
                                            {{ __('messages.owner.products.promotions.to_activate_promo_everyday') }}
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div id="days_grid" class="row g-3">
                                        @foreach($daysMap as $key => $label)
                                            <div class="col-md-3 col-6">
                                                <div class="form-group-modern">
                                                    <label class="form-label-modern d-block">
                                                        {{ $label }}
                                                    </label>

                                                    <div class="status-switch">
                                                        <label class="switch-modern">
                                                            <input type="checkbox"
                                                                class="day-checkbox"
                                                                id="day_{{ $key }}"
                                                                name="active_days[]"
                                                                value="{{ $key }}"
                                                                {{ in_array($key, $selectedDays, true) ? 'checked' : '' }}>
                                                            <span class="slider-modern"></span>
                                                        </label>

                                                        <span class="status-label day-status-{{ $key }}">
                                                            {{ in_array($key, $selectedDays, true) ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @error('active_days')
                                        <div class="text-danger small mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="section-divider"></div>

                        <!-- Activation Status Section -->
                        <div class="account-section">
                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">toggle_on</span>
                                </div>
                                <h3 class="section-title">Status</h3>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern d-block">
                                            {{ __('messages.owner.products.promotions.activate_promotion') }}
                                        </label>

                                        <input type="hidden" name="is_active" value="0">

                                        <div class="status-switch">
                                            <label class="switch-modern">
                                                <input type="checkbox"
                                                    id="is_active"
                                                    name="is_active"
                                                    value="1"
                                                    {{ old('is_active', 1) ? 'checked' : '' }}>
                                                <span class="slider-modern"></span>
                                            </label>

                                            <span class="status-label" id="statusLabel">
                                                {{ old('is_active', 1) ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer-modern">
                        <a href="{{ route('owner.user-owner.promotions.index') }}" class="btn-cancel-modern">
                            {{ __('messages.owner.products.promotions.cancel') }}
                        </a>
                        <button type="submit" class="btn-submit-modern">

                            {{ __('messages.owner.products.promotions.create_promotion') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const typeSel   = document.getElementById('promotion_type');
    const valInput  = document.getElementById('promotion_value');
    const helpText  = document.getElementById('valueHelp');
    const prefixAmt = document.getElementById('prefixAmount');
    const suffixPct = document.getElementById('suffixPercent');

    function applyTypeUI() {
        const t = typeSel.value;

        if (t === 'percentage') {
            prefixAmt.style.display = 'none';
            suffixPct.style.display = '';
            valInput.min = '1';
            valInput.max = '100';
            valInput.step = '1';
            helpText.textContent = '{{ __('messages.owner.products.promotions.percentage_example') }}';
            if (valInput.value && (+valInput.value > 100)) valInput.value = 100;
        } else if (t === 'amount') {
            prefixAmt.style.display = '';
            suffixPct.style.display = 'none';
            valInput.removeAttribute('max');
            valInput.min = '0';
            valInput.step = '1';
            helpText.textContent = '{{ __('messages.owner.products.promotions.reduced_fare_example') }}';
        } else {
            prefixAmt.style.display = 'none';
            suffixPct.style.display = 'none';
            valInput.removeAttribute('min');
            valInput.removeAttribute('max');
            helpText.textContent = '';
        }
    }

    if (typeSel) {
        typeSel.addEventListener('change', applyTypeUI);
        applyTypeUI();
    }

    // Uses expiry toggle
    const usesExpiry = document.getElementById('uses_expiry');
    const dateWrap   = document.getElementById('dateRangeWrap');
    const startDate  = document.getElementById('start_date');
    const endDate    = document.getElementById('end_date');

    function toggleDateRange() {
        const show = usesExpiry.checked;
        dateWrap.classList.toggle('d-none', !show);
        [startDate, endDate].forEach(el => {
            if (!el) return;
            el.required = show;
        });
    }

    if (usesExpiry) {
        usesExpiry.addEventListener('change', toggleDateRange);
        toggleDateRange();
    }

    // Form validation
    const form = document.getElementById('promotionForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (usesExpiry && usesExpiry.checked && startDate.value && endDate.value) {
                const s = new Date(startDate.value);
                const ed = new Date(endDate.value);
                if (ed <= s) {
                    e.preventDefault();
                    alert('{{ __('messages.owner.products.promotions.end_date_alert') }}');
                    endDate.focus();
                }
            }
        });
    }

   // Active days logic (ENHANCED)
const everyDay = document.getElementById('every_day');
const dayCheckboxes = Array.from(document.querySelectorAll('.day-checkbox'));

if (everyDay && dayCheckboxes.length) {
    const everyDayStatusLabel = everyDay
        .closest('.status-switch')
        .querySelector('.status-label');

    function updateDayStatuses() {
        let checkedCount = 0;

        dayCheckboxes.forEach(cb => {
            const statusLabel = document.querySelector('.day-status-' + cb.value);

            if (cb.checked) {
                checkedCount++;
                if (statusLabel) statusLabel.textContent = 'Active';
            } else {
                if (statusLabel) statusLabel.textContent = 'Inactive';
            }
        });

        if (checkedCount === dayCheckboxes.length) {
            everyDay.checked = true;
            everyDayStatusLabel.textContent = 'All Days Selected';
        } else {
            everyDay.checked = false;
            everyDayStatusLabel.textContent = 'Custom Days';
        }
    }

    // Toggle Every Day → check/uncheck all
    everyDay.addEventListener('change', function () {
        dayCheckboxes.forEach(cb => cb.checked = everyDay.checked);
        updateDayStatuses();
    });

    // Toggle individual day
    dayCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateDayStatuses);
    });

    // Initial sync
    updateDayStatuses();
}

})();
</script>
@endpush