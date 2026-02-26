@extends('layouts.staff')

@section('title', __('messages.owner.products.promotions.edit_promotion'))

@section('content')
    @php $empRole = strtolower(auth('employee')->user()->role ?? 'manager'); @endphp
    <div class="modern-container">
        <div class="container-modern">
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.promotions.edit_promotion') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.products.promotions.edit_promotion_subtitle') }}</p>
                </div>
                <a href="{{ route("employee.{$empRole}.promotions.index") }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.products.promotions.back') }}
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon"><span class="material-symbols-outlined">error</span></div>
                    <div class="alert-content">
                        <strong>{{ __('messages.owner.products.promotions.re_check_input') }}</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="modern-card">
                <form action="{{ route("employee.{$empRole}.promotions.update", $data->id) }}" method="POST" id="promotionForm">
                    @csrf
                    @method('PUT')

                    <div class="card-body-modern">

                        {{-- Promotion Information --}}
                        <div class="account-section">
                            <div class="section-header">
                                <div class="section-icon section-icon-red"><span class="material-symbols-outlined">local_offer</span></div>
                                <h3 class="section-title">{{ __('messages.owner.products.promotions.promotion_information') }}</h3>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.products.promotions.promotion_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control-modern @error('promotion_name') is-invalid @enderror"
                                            name="promotion_name" value="{{ old('promotion_name', $data->promotion_name) }}"
                                            placeholder="{{ __('messages.owner.products.promotions.promotion_name_placeholder') }}"
                                            required maxlength="150">
                                        @error('promotion_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                @php $type = old('promotion_type', $data->promotion_type); @endphp

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.products.promotions.promotion_type') }} <span class="text-danger">*</span></label>
                                        <div class="select-wrapper">
                                            <select id="promotion_type" name="promotion_type"
                                                class="form-control-modern @error('promotion_type') is-invalid @enderror" required>
                                                <option value="">{{ __('messages.owner.products.promotions.select_type_dropdown') }}</option>
                                                <option value="percentage" {{ $type === 'percentage' ? 'selected' : '' }}>{{ __('messages.owner.products.promotions.percentage') }} (%)</option>
                                                <option value="amount" {{ $type === 'amount' ? 'selected' : '' }}>{{ __('messages.owner.products.promotions.reduced_fare') }} (Rp)</option>
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>
                                        @error('promotion_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.products.promotions.promotion_value') }} <span class="text-danger">*</span></label>
                                        <div class="input-wrapper" id="valueInputWrapper">
                                            <span class="input-icon" id="prefixAmount" style="display:none;">Rp</span>
                                            <input type="number" class="form-control-modern @error('promotion_value') is-invalid @enderror"
                                                id="promotion_value" name="promotion_value"
                                                value="{{ old('promotion_value', $data->promotion_value) }}" inputmode="numeric" required>
                                            <span class="input-icon" id="suffixPercent" style="display:none; left:auto; right:1rem;">%</span>
                                        </div>
                                        <small class="text-muted d-block mt-1" id="valueHelp">
                                            {{ $type === 'amount' ? __('messages.owner.products.promotions.reduced_fare_example') : __('messages.owner.products.promotions.percentage_example') }}
                                        </small>
                                        @error('promotion_value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.products.promotions.description') }}</label>
                                        <textarea name="description" class="form-control-modern @error('description') is-invalid @enderror"
                                            rows="3" placeholder="{{ __('messages.owner.products.promotions.description_placeholder') }}">{{ old('description', $data->description) }}</textarea>
                                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        {{-- Validity Period --}}
                        <div class="account-section">
                            <div class="section-header">
                                <div class="section-icon section-icon-red"><span class="material-symbols-outlined">schedule</span></div>
                                <h3 class="section-title">{{ __('messages.owner.products.promotions.validity_period') }}</h3>
                            </div>

                            @php $usesExpiryInit = old('uses_expiry', ($data->start_date && $data->end_date) ? 1 : 0); @endphp

                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern d-block">{{ __('messages.owner.products.promotions.is_use_expiry') }}</label>
                                        <input type="hidden" name="uses_expiry" value="0">
                                        <div class="status-switch">
                                            <label class="switch-modern">
                                                <input type="checkbox" id="uses_expiry" name="uses_expiry" value="1" {{ $usesExpiryInit ? 'checked' : '' }}>
                                                <span class="slider-modern"></span>
                                            </label>
                                            <span class="status-label">{{ $usesExpiryInit ? __('messages.owner.products.promotions.enabled') : __('messages.owner.products.promotions.disabled') }}</span>
                                        </div>
                                        <small class="text-muted d-block mt-1">{{ __('messages.owner.products.promotions.activate_if_has_expiry') }}</small>
                                    </div>
                                </div>

                                <div class="col-md-6" id="startDateGroup" style="{{ $usesExpiryInit ? '' : 'display:none;' }}">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.products.promotions.start_date') }} <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control-modern @error('start_date') is-invalid @enderror"
                                            id="start_date" name="start_date"
                                            value="{{ old('start_date', optional($data->start_date)->format('Y-m-d\TH:i')) }}">
                                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-6" id="endDateGroup" style="{{ $usesExpiryInit ? '' : 'display:none;' }}">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.products.promotions.end_date') }} <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control-modern @error('end_date') is-invalid @enderror"
                                            id="end_date" name="end_date"
                                            value="{{ old('end_date', optional($data->end_date)->format('Y-m-d\TH:i')) }}">
                                        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        <small class="text-muted d-block mt-1">{{ __('messages.owner.products.promotions.end_date_alert') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        {{-- Active Days --}}
                        <div class="account-section">
                            <div class="section-header">
                                <div class="section-icon section-icon-red"><span class="material-symbols-outlined">calendar_month</span></div>
                                <h3 class="section-title">{{ __('messages.owner.products.promotions.active_day') }}</h3>
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
                                $selectedDays = old('active_days', $data->active_days ?: []);
                                $isEveryDay = is_array($selectedDays) && count($selectedDays) === 7;
                            @endphp

                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern d-block">{{ __('messages.owner.products.promotions.every_day') }}</label>
                                        <div class="status-switch mb-3">
                                            <label class="switch-modern">
                                                <input type="checkbox" id="every_day" {{ $isEveryDay ? 'checked' : '' }}>
                                                <span class="slider-modern"></span>
                                            </label>
                                            <span class="status-label">{{ $isEveryDay ? __('messages.owner.products.promotions.all_days_selected') : __('messages.owner.products.promotions.custom_days') }}</span>
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
                                        @foreach ($daysMap as $key => $label)
                                            <div class="col-md-3 col-6">
                                                <div class="form-group-modern">
                                                    <label class="form-label-modern d-block">{{ $label }}</label>
                                                    <div class="status-switch">
                                                        <label class="switch-modern">
                                                            <input type="checkbox" class="day-checkbox"
                                                                id="day_{{ $key }}" name="active_days[]" value="{{ $key }}"
                                                                {{ in_array($key, $selectedDays, true) ? 'checked' : '' }}>
                                                            <span class="slider-modern"></span>
                                                        </label>
                                                        <span class="status-label day-status-{{ $key }}">
                                                            {{ in_array($key, $selectedDays, true) ? __('messages.owner.products.promotions.active_status') : __('messages.owner.products.promotions.inactive_status') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('active_days')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        {{-- Status --}}
                        <div class="account-section">
                            <div class="section-header">
                                <div class="section-icon section-icon-red"><span class="material-symbols-outlined">toggle_on</span></div>
                                <h3 class="section-title">{{ __('messages.owner.products.promotions.status') }}</h3>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern d-block">{{ __('messages.owner.products.promotions.activate_promotion') }}</label>
                                        <input type="hidden" name="is_active" value="0">
                                        <div class="status-switch">
                                            <label class="switch-modern">
                                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                                    {{ old('is_active', $data->is_active) ? 'checked' : '' }}>
                                                <span class="slider-modern"></span>
                                            </label>
                                            <span class="status-label" id="statusLabel">
                                                {{ old('is_active', $data->is_active) ? __('messages.owner.products.promotions.active_status') : __('messages.owner.products.promotions.inactive_status') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer-modern">
                        <a href="{{ route("employee.{$empRole}.promotions.index") }}" class="btn-cancel-modern">
                            {{ __('messages.owner.products.promotions.cancel') }}
                        </a>
                        <button type="submit" class="btn-submit-modern">
                            {{ __('messages.owner.products.promotions.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    window.promoLang = {
        enabled: "{{ __('messages.owner.products.promotions.enabled') }}",
        disabled: "{{ __('messages.owner.products.promotions.disabled') }}",
        allDaysSelected: "{{ __('messages.owner.products.promotions.all_days_selected') }}",
        customDays: "{{ __('messages.owner.products.promotions.custom_days') }}",
        activeStatus: "{{ __('messages.owner.products.promotions.active_status') }}",
        inactiveStatus: "{{ __('messages.owner.products.promotions.inactive_status') }}",
        percentageExample: "{{ __('messages.owner.products.promotions.percentage_example') }}",
        reducedFareExample: "{{ __('messages.owner.products.promotions.reduced_fare_example') }}",
        endDateAlert: "{{ __('messages.owner.products.promotions.end_date_alert') }}"
    };

    (function () {
        const typeSel = document.getElementById('promotion_type');
        const valInput = document.getElementById('promotion_value');
        const helpText = document.getElementById('valueHelp');
        const prefixAmt = document.getElementById('prefixAmount');
        const suffixPct = document.getElementById('suffixPercent');

        function applyTypeUI() {
            const t = typeSel.value;
            if (t === 'percentage') {
                prefixAmt.style.display = 'none'; suffixPct.style.display = 'flex';
                valInput.classList.remove('with-icon');
                valInput.style.paddingRight = '3rem'; valInput.style.paddingLeft = 'var(--spacing-lg)';
                valInput.min = '1'; valInput.max = '100'; valInput.step = '1';
                helpText.textContent = window.promoLang.percentageExample;
                if (valInput.value && (+valInput.value > 100)) valInput.value = 100;
            } else if (t === 'amount') {
                prefixAmt.style.display = 'flex'; suffixPct.style.display = 'none';
                valInput.classList.add('with-icon');
                valInput.style.paddingLeft = '3rem'; valInput.style.paddingRight = 'var(--spacing-lg)';
                valInput.removeAttribute('max'); valInput.min = '0'; valInput.step = '1';
                helpText.textContent = window.promoLang.reducedFareExample;
            } else {
                prefixAmt.style.display = 'none'; suffixPct.style.display = 'none';
                valInput.classList.remove('with-icon');
                valInput.style.paddingLeft = 'var(--spacing-lg)'; valInput.style.paddingRight = 'var(--spacing-lg)';
                valInput.removeAttribute('min'); valInput.removeAttribute('max'); valInput.removeAttribute('step');
                helpText.textContent = '';
            }
        }
        typeSel?.addEventListener('change', applyTypeUI);
        applyTypeUI();

        const usesExpiry = document.getElementById('uses_expiry');
        const startDateGroup = document.getElementById('startDateGroup');
        const endDateGroup = document.getElementById('endDateGroup');
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        function toggleDateRange() {
            const show = usesExpiry.checked;
            startDateGroup.style.display = show ? '' : 'none';
            endDateGroup.style.display = show ? '' : 'none';
            if (startDate) startDate.required = show;
            if (endDate) endDate.required = show;
            const statusLabel = usesExpiry.closest('.status-switch')?.querySelector('.status-label');
            if (statusLabel) statusLabel.textContent = show ? window.promoLang.enabled : window.promoLang.disabled;
        }
        usesExpiry?.addEventListener('change', toggleDateRange);
        toggleDateRange();

        document.getElementById('promotionForm')?.addEventListener('submit', function (e) {
            if (usesExpiry?.checked && startDate.value && endDate.value) {
                if (new Date(endDate.value) <= new Date(startDate.value)) {
                    e.preventDefault();
                    alert(window.promoLang.endDateAlert);
                    endDate.focus();
                }
            }
        });

        const everyDay = document.getElementById('every_day');
        const dayCheckboxes = Array.from(document.querySelectorAll('.day-checkbox'));
        const everyDayStatusLabel = everyDay?.closest('.status-switch')?.querySelector('.status-label');

        function syncEveryDayState() {
            let checkedCount = 0;
            dayCheckboxes.forEach(cb => {
                const label = document.querySelector('.day-status-' + cb.value);
                if (cb.checked) { checkedCount++; if (label) label.textContent = window.promoLang.activeStatus; }
                else { if (label) label.textContent = window.promoLang.inactiveStatus; }
            });
            const allChecked = checkedCount === dayCheckboxes.length;
            everyDay.checked = allChecked;
            if (everyDayStatusLabel) everyDayStatusLabel.textContent = allChecked ? window.promoLang.allDaysSelected : window.promoLang.customDays;
        }

        everyDay?.addEventListener('change', function () {
            dayCheckboxes.forEach(cb => cb.checked = everyDay.checked);
            syncEveryDayState();
        });
        dayCheckboxes.forEach(cb => cb.addEventListener('change', syncEveryDayState));
        syncEveryDayState();

        const isActiveToggle = document.getElementById('is_active');
        isActiveToggle?.addEventListener('change', function () {
            document.getElementById('statusLabel').textContent = this.checked ? window.promoLang.activeStatus : window.promoLang.inactiveStatus;
        });
    })();
</script>
@endpush