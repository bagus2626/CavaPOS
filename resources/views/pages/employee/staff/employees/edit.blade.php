@extends('layouts.staff')
@section('title', __('messages.owner.user_management.employees.edit_employee'))

@section('content')
    @php $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager'); @endphp

    <div class="modern-container">
        <div class="container-modern">

            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.user_management.employees.edit_page_title') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.user_management.employees.edit_subtitle') }}</p>
                </div>
                <a href="{{ route('employee.' . $empRole . '.employees.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.user_management.employees.back') }}
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon"><span class="material-symbols-outlined">error</span></div>
                    <div class="alert-content">
                        <strong>{{ __('messages.owner.user_management.employees.alert_1') }}:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if ($errors->has('error'))
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon"><span class="material-symbols-outlined">error</span></div>
                    <div class="alert-content">{{ $errors->first('error') }}</div>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-modern">
                    <div class="alert-icon"><span class="material-symbols-outlined">check_circle</span></div>
                    <div class="alert-content">{{ session('success') }}</div>
                </div>
            @endif

            <div class="modern-card">
                <input type="hidden" id="usernameCheckUrl"
                    value="{{ route('employee.' . $empRole . '.employees.check-username') }}">
                <form action="{{ route('employee.' . $empRole . '.employees.update', $employee) }}" method="POST"
                    enctype="multipart/form-data" id="employeeForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body-modern">

                        <div class="profile-section">
                            <div class="profile-picture-wrapper">
                                <div class="profile-picture-container" id="profilePictureContainer">
                                    <div class="upload-placeholder" id="uploadPlaceholder"
                                        style="{{ $employee->image ? 'display: none;' : '' }}">
                                        <span class="material-symbols-outlined">add_a_photo</span>
                                        <span class="upload-text">{{ __('messages.owner.user_management.employees.upload_text') }}</span>
                                    </div>
                                    <img id="imagePreview"
                                        class="profile-preview {{ $employee->image ? 'active' : '' }}"
                                        src="{{ $employee->image ? asset('storage/' . $employee->image) : '' }}"
                                        alt="{{ __('messages.owner.user_management.employees.profile_preview_alt') }}">
                                    <button type="button" id="removeImageBtn" class="btn-remove btn-remove-top"
                                        style="{{ $employee->image ? 'display: block;' : 'display: none;' }}">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                                <input type="file" name="image" id="image" accept="image/*" style="display: none;">
                                <input type="hidden" name="remove_image" id="remove_image" value="0">
                                <small class="text-muted d-block text-center mt-2">{{ __('messages.owner.user_management.employees.upload_hint') }}</small>
                                @error('image')
                                    <div class="text-danger text-center mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="personal-info-fields">
                                <div class="section-header">
                                    <div class="section-icon section-icon-red">
                                        <span class="material-symbols-outlined">person</span>
                                    </div>
                                    <h3 class="section-title">{{ __('messages.owner.user_management.employees.personal_info_section') }}</h3>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">{{ __('messages.owner.user_management.employees.employee_name') }}</label>
                                            <input type="text" name="name"
                                                class="form-control-modern @error('name') is-invalid @enderror"
                                                value="{{ old('name', $employee->name) }}"
                                                placeholder="{{ __('messages.owner.user_management.employees.placeholder_name') }}"
                                                required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">
                                                {{ __('messages.owner.user_management.employees.employee_email') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" name="email"
                                                class="form-control-modern @error('email') is-invalid @enderror"
                                                value="{{ old('email', $employee->email) }}"
                                                placeholder="{{ __('messages.owner.user_management.employees.placeholder_email') }}"
                                                required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">{{ __('messages.owner.user_management.employees.role') }}</label>
                                            <div class="select-wrapper">
                                                <select name="role"
                                                    class="form-control-modern @error('role') is-invalid @enderror"
                                                    required>
                                                    <option value="">{{ __('messages.owner.user_management.employees.select_role_default') }}</option>
                                                    <option value="CASHIER" {{ old('role', $employee->role) == 'CASHIER' ? 'selected' : '' }}>
                                                        {{ __('messages.owner.user_management.employees.cashier') }}
                                                    </option>
                                                    <option value="KITCHEN" {{ old('role', $employee->role) == 'KITCHEN' ? 'selected' : '' }}>
                                                        {{ __('messages.owner.user_management.employees.kitchen') }}
                                                    </option>
                                                </select>
                                                <span class="material-symbols-outlined select-arrow">expand_more</span>
                                            </div>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">{{ __('messages.owner.user_management.employees.outlet') }}</label>
                                            <div class="select-wrapper">
                                                <select name="partner"
                                                    class="form-control-modern @error('partner') is-invalid @enderror"
                                                    required>
                                                    <option value="">{{ __('messages.owner.user_management.employees.select_location_default') }}</option>
                                                    @foreach ($partners as $partner)
                                                        <option value="{{ $partner->id }}"
                                                            {{ old('partner_id', $employee->partner_id) == $partner->id ? 'selected' : '' }}>
                                                            {{ $partner->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="material-symbols-outlined select-arrow">location_on</span>
                                            </div>
                                            @error('partner')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern d-block">{{ __('messages.owner.user_management.employees.status_label') }}</label>
                                            <input type="hidden" name="is_active" value="0">
                                            <div class="status-switch">
                                                <label class="switch-modern">
                                                    <input type="checkbox" id="is_active" name="is_active"
                                                        value="1"
                                                        {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                                                    <span class="slider-modern"></span>
                                                </label>
                                                <span class="status-label" id="statusLabel">
                                                    {{ old('is_active', $employee->is_active) ? __('messages.owner.user_management.employees.active') : __('messages.owner.user_management.employees.non_active') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        <div class="account-section">
                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">lock</span>
                                </div>
                                <h3 class="section-title">{{ __('messages.owner.user_management.employees.account_access_section') }}</h3>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.user_management.employees.username') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-wrapper position-relative">
                                            <span class="input-icon">
                                                <span class="material-symbols-outlined">alternate_email</span>
                                            </span>
                                            <input type="text" name="username" id="username"
                                                class="form-control-modern with-icon @error('username') is-invalid @enderror"
                                                value="{{ old('username', $employee->user_name) }}"
                                                placeholder="{{ __('messages.owner.user_management.employees.placeholder_username') }}"
                                                data-exclude-id="{{ $employee->id }}"
                                                autocomplete="off" required>
                                            <div id="usernameLoading" class="position-absolute d-none"
                                                style="right: 15px; top: 50%; transform: translateY(-50%); z-index: 5;">
                                                <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                                    <span class="sr-only">{{ __('messages.owner.user_management.employees.loading') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="usernameStatus" class="mt-2"></div>
                                        @error('username')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.user_management.employees.password_optional') }}</label>
                                        <div class="password-wrapper">
                                            <input type="password" name="password" id="password"
                                                class="form-control-modern @error('password') is-invalid @enderror"
                                                placeholder="{{ __('messages.owner.user_management.employees.password_placeholder_1') }}">
                                            <button type="button" class="password-toggle" id="togglePassword">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.user_management.employees.password_confirmation') }}</label>
                                        <div class="password-wrapper">
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation"
                                                class="form-control-modern @error('password_confirmation') is-invalid @enderror"
                                                placeholder="{{ __('messages.owner.user_management.employees.password_placeholder_edit') }}">
                                            <button type="button" class="password-toggle" id="togglePasswordConfirm">
                                                <span class="material-symbols-outlined">visibility_off</span>
                                            </button>
                                        </div>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer-modern">
                        <a href="{{ route('employee.' . $empRole . '.employees.index') }}" class="btn-cancel-modern">
                            {{ __('messages.owner.user_management.employees.cancel') }}
                        </a>
                        <button type="submit" class="btn-submit-modern">
                            {{ __('messages.owner.user_management.employees.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Crop Modal --}}
        <div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content modern-modal">
                    <div class="modal-header modern-modal-header">
                        <h5 class="modal-title">
                            <span class="material-symbols-outlined">crop</span>
                            {{ __('messages.owner.user_management.employees.crop_modal_title') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-info alert-modern mb-3">
                            <div class="alert-icon">
                                <span class="material-symbols-outlined">info</span>
                            </div>
                            <div class="alert-content">
                                <small>{{ __('messages.owner.user_management.employees.crop_instruction') }}</small>
                            </div>
                        </div>
                        <div class="img-container-crop">
                            <img id="imageToCrop" style="max-width: 100%;" alt="Image to crop">
                        </div>
                    </div>
                    <div class="modal-footer modern-modal-footer">
                        <button type="button" class="btn-cancel-modern" data-dismiss="modal">
                            <span class="material-symbols-outlined">close</span>
                            {{ __('messages.owner.user_management.employees.cancel') }}
                        </button>
                        <button type="button" id="cropBtn" class="btn-submit-modern">
                            <span class="material-symbols-outlined">check</span>
                            {{ __('messages.owner.user_management.employees.crop_save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const isActiveToggle = document.getElementById('is_active');
            const statusLabel = document.getElementById('statusLabel');
            if (isActiveToggle && statusLabel) {
                isActiveToggle.addEventListener('change', function() {
                    statusLabel.textContent = this.checked ?
                        '{{ __('messages.owner.user_management.employees.active') }}' :
                        '{{ __('messages.owner.user_management.employees.non_active') }}';
                });
            }

            ImageCropper.init({
                id: 'profile',
                inputId: 'image',
                previewId: 'imagePreview',
                modalId: 'cropModal',
                imageToCropId: 'imageToCrop',
                cropBtnId: 'cropBtn',
                containerId: 'profilePictureContainer',
                removeInputId: 'remove_image',
                aspectRatio: 1,
                outputWidth: 800,
                outputHeight: 800
            });

            ImageRemoveHandler.init({
                removeBtnId: 'removeImageBtn',
                imageInputId: 'image',
                imagePreviewId: 'imagePreview',
                uploadPlaceholderId: 'uploadPlaceholder',
                removeInputId: 'remove_image',
                confirmRemove: false
            });

            function bindPasswordToggle(btnId, inputId) {
                const btn = document.getElementById(btnId);
                const inp = document.getElementById(inputId);
                if (!btn || !inp) return;
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isPw = inp.type === 'password';
                    inp.type = isPw ? 'text' : 'password';
                    const icon = btn.querySelector('.material-symbols-outlined');
                    if (icon) icon.textContent = isPw ? 'visibility_off' : 'visibility';
                });
            }

            bindPasswordToggle('togglePassword', 'password');
            bindPasswordToggle('togglePasswordConfirm', 'password_confirmation');

            const form = document.getElementById('employeeForm');
            const pw = document.getElementById('password');
            const pwc = document.getElementById('password_confirmation');
            if (form && pw && pwc) {
                form.addEventListener('submit', function(e) {
                    if (pw.value.length > 0 && pw.value.length < 8) {
                        e.preventDefault();
                        alert('{{ __('messages.owner.user_management.employees.js_password_length') }}');
                        pw.focus();
                        return false;
                    }
                    if (pw.value.length > 0 && pw.value !== pwc.value) {
                        e.preventDefault();
                        alert('{{ __('messages.owner.user_management.employees.js_password_mismatch') }}');
                        pwc.focus();
                        return false;
                    }
                });
            }

            const inputUsername = document.getElementById('username');
            const statusEl = document.getElementById('usernameStatus');
            const loadingEl = document.getElementById('usernameLoading');
            const urlCheck = document.getElementById('usernameCheckUrl')?.value;

            if (inputUsername && statusEl && urlCheck) {
                function setLoading(v) {
                    if (!loadingEl) return;
                    v ? loadingEl.classList.remove('d-none') : loadingEl.classList.add('d-none');
                    if (v) statusEl.innerHTML = '';
                }

                function showStatus(ok, msg) {
                    if (ok) {
                        statusEl.innerHTML =
                            `<span class="badge bg-success">{{ __('messages.owner.user_management.employees.badge_available') }}</span> <span class="text-success ms-2">${msg}</span>`;
                        inputUsername.classList.remove('is-invalid');
                        inputUsername.classList.add('is-valid');
                    } else {
                        statusEl.innerHTML =
                            `<span class="badge bg-danger">{{ __('messages.owner.user_management.employees.badge_taken') }}</span> <span class="text-danger ms-2">${msg}</span>`;
                        inputUsername.classList.remove('is-valid');
                        inputUsername.classList.add('is-invalid');
                    }
                }

                function showNeutral() {
                    statusEl.textContent = '';
                    inputUsername.classList.remove('is-valid', 'is-invalid');
                }

                async function checkUsername() {
                    const val = (inputUsername.value || '').trim();
                    if (!val) { setLoading(false); showNeutral(); return; }
                    if (val.length < 3 || val.length > 30 || !/^[A-Za-z0-9._-]+$/.test(val)) {
                        setLoading(false);
                        showStatus(false, '{{ __('messages.owner.user_management.employees.js_username_invalid_format') }}');
                        return;
                    }
                    try {
                        const params = new URLSearchParams({ username: val });
                        const excludeId = inputUsername.dataset.excludeId || '';
                        if (excludeId) params.append('exclude_id', excludeId);
                        const res = await fetch(`${urlCheck}?${params.toString()}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.status === 422) {
                            showStatus(false, '{{ __('messages.owner.user_management.employees.js_invalid_format_short') }}');
                            return;
                        }
                        const data = await res.json();
                        if (typeof data.available !== 'undefined') {
                            showStatus(data.available,
                                data.available ?
                                '{{ __('messages.owner.user_management.employees.username_available') }}' :
                                '{{ __('messages.owner.user_management.employees.username_used') }}'
                            );
                        } else {
                            showNeutral();
                        }
                    } catch (e) {
                        console.error('Username check error:', e);
                    } finally {
                        setLoading(false);
                    }
                }

                let timer;
                inputUsername.addEventListener('input', function() {
                    clearTimeout(timer);
                    this.value.trim() ? setLoading(true) : (setLoading(false), showNeutral());
                    timer = setTimeout(checkUsername, 600);
                });
            }
        });
    </script>
@endpush