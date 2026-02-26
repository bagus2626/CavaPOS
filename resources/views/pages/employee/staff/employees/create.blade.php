@extends('layouts.staff')
@section('title', 'Create Employee')

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            @php $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager'); @endphp

            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">Create New Employee</h1>
                    <p class="page-subtitle">Add a new employee to your outlet</p>
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

            <div class="modern-card">
                <input type="hidden" id="usernameCheckUrl"
                    value="{{ route('employee.' . $empRole . '.employees.check-username') }}">
                <form action="{{ route('employee.' . $empRole . '.employees.store') }}" method="POST"
                    enctype="multipart/form-data" id="employeeForm">
                    @csrf
                    <div class="card-body-modern">

                        <div class="profile-section">
                            <div class="profile-picture-wrapper">
                                <div class="profile-picture-container" id="profilePictureContainer">
                                    <div class="upload-placeholder" id="uploadPlaceholder">
                                        <span class="material-symbols-outlined">image</span>
                                        <span class="upload-text">Upload Photo</span>
                                    </div>
                                    <img id="imagePreview" class="profile-preview" alt="Profile Preview">
                                    <button type="button" id="removeImageBtn" class="btn-remove btn-remove-top"
                                        style="display: none;">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                                <input type="file" name="image" id="image" accept="image/*" style="display: none;">
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
                                                value="{{ old('name') }}" placeholder="Enter full name" required>
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
                                                value="{{ old('email') }}" placeholder="email@example.com" required>
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
                                                        {{ old('role') == 'CASHIER' ? 'selected' : '' }}>Cashier</option>
                                                    <option value="KITCHEN"
                                                        {{ old('role') == 'KITCHEN' ? 'selected' : '' }}>Kitchen</option>
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
                                                        <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="material-symbols-outlined select-arrow">location_on</span>
                                            </div>
                                            @error('partner')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                                value="{{ old('username') }}" placeholder="Enter username"
                                                autocomplete="off" required>
                                            <div id="usernameLoading" class="position-absolute d-none"
                                                style="right: 15px; top: 50%; transform: translateY(-50%); z-index: 5;">
                                                <div class="spinner-modern text-secondary" role="status">
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
                                        <label class="form-label-modern">Password <span
                                                class="text-danger">*</span></label>
                                        <div class="password-wrapper">
                                            <input type="password" name="password" id="password"
                                                class="form-control-modern @error('password') is-invalid @enderror"
                                                placeholder="Enter password" required>
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
                                        <label class="form-label-modern">Confirm Password <span
                                                class="text-danger">*</span></label>
                                        <div class="password-wrapper">
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation"
                                                class="form-control-modern @error('password_confirmation') is-invalid @enderror"
                                                placeholder="Re-enter password" required>
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
                        <a href="{{ route('employee.' . $empRole . '.employees.index') }}" class="btn-cancel-modern">Cancel</a>
                        <button type="submit" class="btn-submit-modern">Create Employee</button>
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
                            <span class="material-symbols-outlined">crop</span> Crop Photo
                        </h5>
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
            ImageCropper.init({
                id: 'profile',
                inputId: 'image',
                previewId: 'imagePreview',
                modalId: 'cropModal',
                imageToCropId: 'imageToCrop',
                cropBtnId: 'cropBtn',
                containerId: 'profilePictureContainer',
                aspectRatio: 1,
                outputWidth: 800,
                outputHeight: 800
            });

            ImageRemoveHandler.init({
                removeBtnId: 'removeImageBtn',
                imageInputId: 'image',
                imagePreviewId: 'imagePreview',
                uploadPlaceholderId: 'uploadPlaceholder',
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
                    if (pw.value.length < 8) {
                        e.preventDefault();
                        alert('Password must be at least 8 characters.');
                        pw.focus();
                        return;
                    }
                    if (pw.value !== pwc.value) {
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
                        `<span class="text-success">${msg}</span>` :
                        `<span class="text-danger">${msg}</span>`;
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
                        showStatus(false, 'Invalid format (3-30 chars, letters/numbers/._- only)');
                        return;
                    }
                    try {
                        const params = new URLSearchParams({
                            username: val
                        });
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
