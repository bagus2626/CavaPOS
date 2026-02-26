    @extends('layouts.staff')
    @section('title', 'Edit Employee')

    @section('content')
        @php $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager'); @endphp

        <div class="modern-container">
            <div class="container-modern">

                <div class="page-header">
                    <div class="header-content">
                        <h1 class="page-title">Edit Employee</h1>
                        <p class="page-subtitle">Update employee information</p>
                    </div>
                    <a href="{{ route('employee.' . $empRole . '.employees.index') }}" class="back-button">
                        <span class="material-symbols-outlined">arrow_back</span>
                        Back
                    </a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-modern">
                        <div class="alert-icon"><span class="material-symbols-outlined">error</span></div>
                        <div class="alert-content">
                            <strong>Please check your input:</strong>
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
                                            <span class="upload-text">Upload Photo</span>
                                        </div>
                                        <img id="imagePreview"
                                            class="profile-preview {{ $employee->image ? 'active' : '' }}"
                                            src="{{ $employee->image ? asset('storage/' . $employee->image) : '' }}"
                                            alt="Profile">
                                        <button type="button" id="removeImageBtn" class="btn-remove btn-remove-top"
                                            style="{{ $employee->image ? 'display: block;' : 'display: none;' }}">
                                            <span class="material-symbols-outlined">close</span>
                                        </button>
                                    </div>
                                    <input type="file" name="image" id="image" accept="image/*"
                                        style="display: none;">
                                    <input type="hidden" name="remove_image" id="remove_image" value="0">
                                    <small class="text-muted d-block text-center mt-2">JPG, PNG, max 2MB</small>
                                    @error('image')
                                        <div class="text-danger text-center mt-1 small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="personal-info-fields">
                                    <div class="section-header">
                                        <div class="section-icon section-icon-red">
                                            <span class="material-symbols-outlined">person</span>
                                        </div>
                                        <h3 class="section-title">Personal Information</h3>
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">Full Name</label>
                                                <input type="text" name="name"
                                                    class="form-control-modern @error('name') is-invalid @enderror"
                                                    value="{{ old('name', $employee->name) }}"
                                                    placeholder="Enter full name" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">Email <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" name="email"
                                                    class="form-control-modern @error('email') is-invalid @enderror"
                                                    value="{{ old('email', $employee->email) }}"
                                                    placeholder="email@example.com" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">Role</label>
                                                <div class="select-wrapper">
                                                    <select name="role"
                                                        class="form-control-modern @error('role') is-invalid @enderror"
                                                        required>
                                                        <option value="">-- Select Role --</option>
                                                        <option value="CASHIER"
                                                            {{ old('role', $employee->role) == 'CASHIER' ? 'selected' : '' }}>
                                                            Cashier</option>
                                                        <option value="KITCHEN"
                                                            {{ old('role', $employee->role) == 'KITCHEN' ? 'selected' : '' }}>
                                                            Kitchen</option>
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
                                                <label class="form-label-modern">Outlet</label>
                                                <div class="select-wrapper">
                                                    <select name="partner"
                                                        class="form-control-modern @error('partner') is-invalid @enderror"
                                                        required>
                                                        <option value="">-- Select Outlet --</option>
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
                                                <label class="form-label-modern d-block">Status</label>
                                                <input type="hidden" name="is_active" value="0">
                                                <div class="status-switch">
                                                    <label class="switch-modern">
                                                        <input type="checkbox" id="is_active" name="is_active"
                                                            value="1"
                                                            {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                                                        <span class="slider-modern"></span>
                                                    </label>
                                                    <span class="status-label" id="statusLabel">
                                                        {{ old('is_active', $employee->is_active) ? 'Active' : 'Inactive' }}
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
                                    <h3 class="section-title">Account Access</h3>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">Username <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-wrapper position-relative">
                                                <span class="input-icon">
                                                    <span class="material-symbols-outlined">alternate_email</span>
                                                </span>
                                                <input type="text" name="username" id="username"
                                                    class="form-control-modern with-icon @error('username') is-invalid @enderror"
                                                    value="{{ old('username', $employee->user_name) }}"
                                                    placeholder="Enter username" data-exclude-id="{{ $employee->id }}"
                                                    autocomplete="off" required>
                                                <div id="usernameLoading" class="position-absolute d-none"
                                                    style="right: 15px; top: 50%; transform: translateY(-50%); z-index: 5;">
                                                    <div class="spinner-border spinner-border-sm text-secondary"
                                                        role="status">
                                                        <span class="sr-only">Loading...</span>
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
                                            <label class="form-label-modern">Password (optional)</label>
                                            <div class="password-wrapper">
                                                <input type="password" name="password" id="password"
                                                    class="form-control-modern @error('password') is-invalid @enderror"
                                                    placeholder="Leave blank to keep current">
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
                                            <label class="form-label-modern">Confirm Password</label>
                                            <div class="password-wrapper">
                                                <input type="password" name="password_confirmation"
                                                    id="password_confirmation"
                                                    class="form-control-modern @error('password_confirmation') is-invalid @enderror"
                                                    placeholder="Re-enter new password">
                                                <button type="button" class="password-toggle"
                                                    id="togglePasswordConfirm">
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
                            <a href="{{ rroute('employee.' . $empRole . '.employees.index') }}"
                                class="btn-cancel-modern">Cancel</a>
                            <button type="submit" class="btn-submit-modern">Update Employee</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Crop Modal --}}
            <div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content modern-modal">
                        <div class="modal-header modern-modal-header">
                            <h5 class="modal-title"><span class="material-symbols-outlined">crop</span> Crop Photo</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="img-container-crop">
                                <img id="imageToCrop" style="max-width: 100%;" alt="Crop">
                            </div>
                        </div>
                        <div class="modal-footer modern-modal-footer">
                            <button type="button" class="btn-cancel-modern" data-dismiss="modal">
                                <span class="material-symbols-outlined">close</span> Cancel
                            </button>
                            <button type="button" id="cropBtn" class="btn-submit-modern">
                                <span class="material-symbols-outlined">check</span> Save Crop
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
                        statusLabel.textContent = this.checked ? 'Active' : 'Inactive';
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
                            alert('Password must be at least 8 characters.');
                            pw.focus();
                            return;
                        }
                        if (pw.value.length > 0 && pw.value !== pwc.value) {
                            e.preventDefault();
                            alert('Passwords do not match.');
                            pwc.focus();
                            return;
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
                        statusEl.innerHTML = ok ?
                            `<span class="badge bg-success">Available</span> <span class="text-success">${msg}</span>` :
                            `<span class="badge bg-danger">Taken</span> <span class="text-danger">${msg}</span>`;
                        inputUsername.classList.toggle('is-invalid', !ok);
                        inputUsername.classList.toggle('is-valid', ok);
                    }

                    function showNeutral() {
                        statusEl.textContent = '';
                        inputUsername.classList.remove('is-valid', 'is-invalid');
                    }
                    async function checkUsername() {
                        const val = (inputUsername.value || '').trim();
                        if (!val) {
                            setLoading(false);
                            showNeutral();
                            return;
                        }
                        if (val.length < 3 || val.length > 30 || !/^[A-Za-z0-9._-]+$/.test(val)) {
                            setLoading(false);
                            showStatus(false, 'Invalid format');
                            return;
                        }
                        try {
                            const params = new URLSearchParams({
                                username: val
                            });
                            const excludeId = inputUsername.dataset.excludeId || '';
                            if (excludeId) params.append('exclude_id', excludeId);
                            const res = await fetch(`${urlCheck}?${params}`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();
                            if (typeof data.available !== 'undefined') {
                                showStatus(data.available, data.available ? 'Username available' :
                                    'Username already taken');
                            }
                        } catch (e) {
                            console.error(e);
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
