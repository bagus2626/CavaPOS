@extends('layouts.owner')

@section('title', __('messages.owner.user_management.employees.create_employees'))
@section('page_title', __('messages.owner.user_management.employees.create_new_employee'))

@section('content')
    <div class="container-fluid owner-emp-create px-4 py-3"> {{-- tambahkan class page-scope --}}
        <a href="{{ route('owner.user-owner.employees.index') }}" class="btn btn-outline-choco mb-3">
            <i class="fas fa-arrow-left mr-2"></i>{{ __('messages.owner.user_management.employees.back_to_employees') }}
        </a>
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('messages.owner.user_management.employees.create_new_employee') }}</h5>
            </div>
            <div class="card-body">
                {{-- Rangkuman semua error validasi --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>{{ __('messages.owner.user_management.employees.recheck_input') }}:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Error global dari withErrors(['error' => '...']) --}}
                @if ($errors->has('error'))
                    <div class="alert alert-danger">
                        {{ $errors->first('error') }}
                    </div>
                @endif

                {{-- Flash sukses/gagal (opsional) --}}
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('status'))
                    <div class="alert alert-info">{{ session('status') }}</div>
                @endif

                <input type="hidden" id="usernameCheckUrl"
                    value="{{ route('owner.user-owner.employees.check-username') }}">
                <form action="{{ route('owner.user-owner.employees.store') }}" method="POST" enctype="multipart/form-data"
                    id="employeeForm">
                    @csrf

                    <div class="row">
                        {{-- Employee name --}}
                        <div class="col-md-6 mb-3">
                            <label for="name"
                                class="form-label">{{ __('messages.owner.user_management.employees.employee_name') }}</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="role"
                                    class="form-label">{{ __('messages.owner.user_management.employees.role') }}</label>
                                <select name="role" id="role"
                                    class="form-control @error('role') is-invalid @enderror" required>
                                    <option value="">{{ __('messages.owner.user_management.employees.select_role') }}
                                    </option>
                                    <option value="CASHIER" {{ old('role') == 'CASHIER' ? 'selected' : '' }}>
                                        {{ __('messages.owner.user_management.employees.cashier') }}</option>
                                    <option value="KITCHEN" {{ old('role') == 'KITCHEN' ? 'selected' : '' }}>
                                        {{ __('messages.owner.user_management.employees.kitchen') }}</option>
                                    {{-- <option value="WAITER" {{ old('role') == 'WAITER' ? 'selected' : '' }}>
                                        {{ __('messages.owner.user_management.employees.waiter') }}</option> --}}
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Username --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username"
                                class="form-label">{{ __('messages.owner.user_management.employees.username') }} <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" name="username" id="username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    value="{{ old('username') }}" required minlength="3" maxlength="30"
                                    pattern="^[A-Za-z0-9._-]+$"
                                    placeholder="{{ __('messages.owner.user_management.employees.username_example') }}"
                                    autocomplete="username" autocapitalize="none" spellcheck="false">
                                <button type="button" id="btnCheckUsername" class="btn btn-outline-primary">
                                    <span class="label">{{ __('messages.owner.user_management.employees.check') }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                            <small
                                class="text-muted">{{ __('messages.owner.user_management.employees.muted_text_1') }}</small>

                            {{-- area status --}}
                            <div id="usernameStatus" class="form-text mt-1"></div>

                            @error('username')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="partner"
                                    class="form-label">{{ __('messages.owner.user_management.employees.outlet') }}</label>
                                <select name="partner" id="partner"
                                    class="form-control @error('partner') is-invalid @enderror" required>
                                    <option value="">
                                        {{ __('messages.owner.user_management.employees.select_status') }}</option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                    @endforeach
                                </select>
                                @error('partner')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Email --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="email"
                                    class="form-label">{{ __('messages.owner.user_management.employees.employee_email') }}
                                    <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $employee->email ?? '') }}" placeholder="email@email.com"
                                    required maxlength="254" autocomplete="email" autocapitalize="off" spellcheck="false"
                                    inputmode="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Image --}}
                        <div class="col-md-6 mb-3">
                            <label for="image"
                                class="form-label">{{ __('messages.owner.user_management.employees.upload_image') }}</label>
                            <input type="file" name="image" id="image"
                                class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, WEBP. Max 2 MB.</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- Preview --}}
                            <div id="imagePreviewWrapper" class="mt-2 d-none">
                                <div class="position-relative" style="width: 180px;">
                                    <img id="imagePreview" src="" alt="Image preview"
                                        class="img-thumbnail rounded w-100 h-auto">
                                    <button type="button" id="clearImageBtn"
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                        aria-label="Hapus gambar">
                                        &times;
                                    </button>
                                </div>
                                <small id="imageInfo" class="text-muted d-block mt-1"></small>
                            </div>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" minlength="8" required
                                    autocomplete="new-password"
                                    placeholder="{{ __('messages.owner.user_management.employees.muted_text_2') }}">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                    tabindex="-1">{{ __('messages.owner.user_management.employees.show') }}</button>
                            </div>
                            <small
                                class="text-muted">{{ __('messages.owner.user_management.employees.muted_text_2') }}</small>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation"
                                class="form-label">{{ __('messages.owner.user_management.employees.password_confirmation') }}
                                <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    minlength="8" required autocomplete="new-password"
                                    placeholder="{{ __('messages.owner.user_management.employees.password_placeholder_3') }}">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm"
                                    tabindex="-1">{{ __('messages.owner.user_management.employees.show') }}</button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('owner.user-owner.employees.index') }}" 
       class="btn btn-light border">{{ __('messages.owner.user_management.employees.cancel') }}</a>
    <button type="submit" 
            class="btn btn-choco">{{ __('messages.owner.user_management.employees.save') }}</button>
</div>
                </form>
                <div class="modal fade" id="cropModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 650px">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-choco text-white border-0">
                                <h5 class="modal-title font-weight-bold">
                                    <i class="fas fa-crop mr-2"></i>Crop Employee Photo
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="alert alert-info border-0 mb-3">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <small>Drag to move, scroll to zoom, or use the corners to resize the crop area.</small>
                                </div>

                                <div class="img-container-crop">
                                    <img id="imageToCrop" style="max-width: 100%;">
                                </div>
                            </div>
                            <div class="modal-footer border-0 bg-light">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </button>
                                <button type="button" id="cropBtn" class="btn btn-choco btn-md px-4">
                                    <i class="fas fa-check mr-2"></i>Crop
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>

        /* ===== Owner › Employee Create (page scope) ===== */
        .owner-emp-create {
            --choco: #8c1000;
            --soft-choco: #c12814;
            --ink: #22272b;
            --paper: #f7f7f8;
            --radius: 12px;
            --shadow: 0 6px 20px rgba(0, 0, 0, .08);
        }

        /* Card polish */
        .owner-emp-create .card {
            border: 0;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .owner-emp-create .card-header {
            background: #fff;
            border-bottom: 1px solid #eef1f4;
            padding: .85rem 1rem;
        }

        .owner-emp-create .card-title {
            color: var(--ink);
            font-weight: 600;
        }

        /* Alerts (selaras brand) */
        .owner-emp-create .alert {
            border-left: 4px solid var(--choco);
            border-radius: 10px;
        }

        .owner-emp-create .alert-danger {
            background: #fff5f5;
            border-color: #fde2e2;
            color: #991b1b;
        }

        .owner-emp-create .alert-success {
            background: #f0fdf4;
            border-color: #dcfce7;
            color: #166534;
        }

        .owner-emp-create .alert-info {
            background: #eff6ff;
            border-color: #dbeafe;
            color: #1d4ed8;
        }

        /* Label & input */
        .owner-emp-create .form-label {
            font-weight: 600;
            color: #374151;
        }

        .owner-emp-create .form-control:focus,
        .owner-emp-create select.form-control:focus {
            border-color: var(--choco);
            box-shadow: 0 0 0 .2rem rgba(140, 16, 0, .15);
        }

        /* Input group token (@) & tombol di group */
        .owner-emp-create .input-group-text {
            background: rgba(140, 16, 0, .08);
            color: var(--choco);
            border-color: rgba(140, 16, 0, .25);
        }

        .owner-emp-create .input-group .btn {
            border-radius: 0 .5rem .5rem 0;
        }

        /* Brand buttons */
        .owner-emp-create .btn-choco {
            background: var(--choco);
            border-color: var(--choco);
            color: #fff;
        }

        .owner-emp-create .btn-choco:hover {
            background: var(--soft-choco);
            border-color: var(--soft-choco);
        }

        .owner-emp-create .btn-outline-choco {
            color: var(--choco);
            border-color: var(--choco);
        }

        .owner-emp-create .btn-outline-choco:hover {
            color: #fff;
            background: var(--choco);
            border-color: var(--choco);
        }

        /* Samakan tombol “Check username” dengan tema */
        .owner-emp-create #btnCheckUsername {
            border-color: var(--choco);
            color: var(--choco);
            background: #fff;
        }

        .owner-emp-create #btnCheckUsername:hover {
            background: var(--choco);
            color: #fff;
            border-color: var(--choco);
        }

        .owner-emp-create #btnCheckUsername .spinner-border {
            margin-left: .35rem;
        }

        /* Username availability area */
        .owner-emp-create #usernameStatus .badge {
            border-radius: 999px;
            padding: .28rem .6rem;
            font-weight: 600;
        }

        .owner-emp-create #usernameStatus .bg-success {
            background: #ecfdf5 !important;
            color: #065f46 !important;
            border: 1px solid #a7f3d0;
        }

        .owner-emp-create #usernameStatus .bg-danger {
            background: #fee2e2 !important;
            color: #991b1b !important;
            border: 1px solid #fecaca;
        }

        /* Switch & focus */
        .owner-emp-create .form-check-input:checked {
            background-color: var(--choco);
            border-color: var(--choco);
        }

        .owner-emp-create .form-check-input:focus {
            box-shadow: 0 0 0 .2rem rgba(140, 16, 0, .15);
            border-color: var(--soft-choco);
        }

        /* Gambar preview */
        .owner-emp-create #imagePreviewWrapper .img-thumbnail {
            border: 0;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .owner-emp-create #clearImageBtn {
            transform: translate(35%, -35%);
            border-radius: 999px;
            width: 28px;
            height: 28px;
            padding: 0;
            line-height: 26px;
        }

        /* Action row */
        .owner-emp-create .d-flex .btn {
            min-width: 120px;
        }

        /* Back button fallback (kalau belum ubah ke btn-outline-choco) */
        .owner-emp-create>.btn.btn-secondary {
            color: var(--choco);
            background: #fff;
            border-color: var(--choco);
        }

        .owner-emp-create>.btn.btn-secondary:hover {
            color: #fff;
            background: var(--choco);
            border-color: var(--choco);
        }

        /* Small helpers */
        .owner-emp-create .text-muted {
            color: #6b7280 !important;
        }

        /* ===== CROP MODAL - EMPLOYEE PHOTO (Circular) ===== */
.owner-emp-create #cropModal .modal-dialog {
    max-width: 650px;
    width: 90%;
    margin: 1.75rem auto;
}

.owner-emp-create #cropModal .modal-content {
    border-radius: 20px;
    overflow: hidden;
}

.owner-emp-create #cropModal .modal-header {
    background-color: var(--choco);
    border-radius: 20px 20px 0 0;
}

.owner-emp-create #cropModal .modal-footer {
    border-radius: 0 0 20px 20px;
}

.owner-emp-create #cropModal .modal-body {
    padding: 1.5rem;
    background: #f8f9fa;
}

.owner-emp-create #cropModal .alert-info {
    border-radius: 10px;
    background: #e0f2fe;
    border: 1px solid #bae6fd;
    color: #075985;
}

.owner-emp-create .img-container-crop {
    width: 100%;
    height: 450px;
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.owner-emp-create .img-container-crop img {
    max-width: 100%;
    max-height: 100%;
    display: block;
}

/* Cropper styling - CIRCULAR for employee photo */
.owner-emp-create #cropModal .cropper-view-box,
.owner-emp-create #cropModal .cropper-face {
    border-radius: 50% !important;
}

.owner-emp-create #cropModal .cropper-container {
    background-color: #f8f9fa;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .owner-emp-create #cropModal .modal-dialog {
        max-width: 95%;
        width: 95%;
        margin: 1rem auto;
    }

    .owner-emp-create .img-container-crop {
        height: 350px;
    }

    .owner-emp-create #cropModal .modal-body {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .owner-emp-create #cropModal .modal-dialog {
        margin: 0.5rem auto;
        width: 98%;
    }

    .owner-emp-create #cropModal .modal-content {
        border-radius: 16px;
    }

    .owner-emp-create .img-container-crop {
        height: 280px;
    }

    .owner-emp-create #cropModal .modal-body {
        padding: 0.75rem;
    }
}

/* Spacing untuk action buttons */
.owner-emp-create .d-flex.gap-2 > .btn {
    min-width: 100px;
    padding: 0.5rem 1.5rem;
}

.owner-emp-create .d-flex.gap-2 {
    gap: 0.75rem !important;
}
    </style>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== Image preview =====
            const input = document.getElementById('image');
            const wrapper = document.getElementById('imagePreviewWrapper');
            const preview = document.getElementById('imagePreview');
            const info = document.getElementById('imageInfo');
            const clearBtn = document.getElementById('clearImageBtn');

            const MAX_SIZE = 2 * 1024 * 1024; // 2 MB
            const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];

            let cropper = null;
            let currentFile = null;

            function bytesToSize(bytes) {
                const units = ['B', 'KB', 'MB', 'GB'];
                let i = 0,
                    num = bytes;
                while (num >= 1024 && i < units.length - 1) {
                    num /= 1024;
                    i++;
                }
                return `${num.toFixed(num < 10 && i > 0 ? 1 : 0)} ${units[i]}`;
            }

            function resetPreview() {
                preview.src = '';
                info.textContent = '';
                wrapper.classList.add('d-none');
            }

            if (input) {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) {
                        resetPreview();
                        return;
                    }
                    if (!ALLOWED.includes(file.type)) {
                        alert('File not supported. Use JPG, PNG, atau WEBP.');
                        input.value = '';
                        resetPreview();
                        return;
                    }
                    if (file.size > MAX_SIZE) {
                        alert('File size more than 2 MB.');
                        input.value = '';
                        resetPreview();
                        return;
                    }

                    currentFile = file;

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('imageToCrop').src = event.target.result;
                        $('#cropModal').modal('show'); // Buka modal crop
                    };
                    reader.readAsDataURL(file);
                });
            }

            // TAMBAHAN BARU - Initialize Cropper when modal shown
            $('#cropModal').on('shown.bs.modal', function() {
                if (cropper) cropper.destroy(); // Hapus instance lama jika ada
                const imageElement = document.getElementById('imageToCrop');

                setTimeout(function() {
                    cropper = new Cropper(imageElement, {
                        aspectRatio: 1, // Rasio 1:1 (kotak)
                        viewMode: 2, // Mode tampilan
                        dragMode: 'move', // Bisa drag gambar
                        autoCropArea: 0.9, // Area crop awal 90%
                        restore: true, // Restore state saat resize
                        guides: true, // Tampilkan garis bantu
                        center: true, // Titik tengah
                        cropBoxMovable: true, // Crop box bisa dipindah
                        cropBoxResizable: true, // Crop box bisa diresize
                        responsive: true // Responsif
                    });
                }, 300); // Delay 300ms untuk animasi modal
            });

            // TAMBAHAN BARU - Destroy cropper when modal hidden
            $('#cropModal').on('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                currentFile = null;
            });

            // TAMBAHAN BARU - Crop button handler
            document.getElementById('cropBtn')?.addEventListener('click', function() {
                if (!cropper) {
                    alert('Cropper not initialized. Please try again.');
                    return;
                }

                const btn = this;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                // Deteksi tipe file asli (PNG/WEBP untuk transparansi)
                const isTransparent = currentFile.type === 'image/png' || currentFile.type === 'image/webp';
                const outputType = isTransparent ? currentFile.type : 'image/jpeg';
                const quality = isTransparent ? 1 : 0.92;

                // Generate canvas dari area yang di-crop
                const canvas = cropper.getCroppedCanvas({
                    width: 800,
                    height: 800,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                // Convert canvas ke blob/file
                canvas.toBlob(function(blob) {
                    const croppedFile = new File([blob], currentFile.name, {
                        type: outputType,
                        lastModified: Date.now()
                    });

                    // Replace file input dengan file hasil crop
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    input.files = dataTransfer.files;

                    // Update preview
                    const url = URL.createObjectURL(blob);
                    wrapper.classList.remove('d-none');
                    preview.src = url;
                    info.textContent =
                        `${croppedFile.name} • ${(croppedFile.size / 1024).toFixed(1)} KB (Cropped)`;

                    // Tutup modal dan reset button
                    $('#cropModal').modal('hide');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }, outputType, quality);
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    if (input) input.value = '';
                    resetPreview();
                });
            }

            // ===== Password show/hide =====
            function bindToggle(btnId, inputId) {
                const btn = document.getElementById(btnId);
                const inp = document.getElementById(inputId);
                if (!btn || !inp) return;
                btn.addEventListener('click', () => {
                    const isPw = inp.type === 'password';
                    inp.type = isPw ? 'text' : 'password';
                    btn.textContent = isPw ? 'Hide' : 'Show';
                });
            }
            bindToggle('togglePassword', 'password');
            bindToggle('togglePasswordConfirm', 'password_confirmation');

            // ===== Client check: confirm password must match =====
            const form = document.getElementById('employeeForm');
            const pw = document.getElementById('password');
            const pwc = document.getElementById('password_confirmation');

            if (form && pw && pwc) {
                form.addEventListener('submit', function(e) {
                    if (pw.value.length < 8) {
                        e.preventDefault();
                        alert('Password minimal 8 characters.');
                        pw.focus();
                        return;
                    }
                    if (pw.value !== pwc.value) {
                        e.preventDefault();
                        alert('Password confirmation is not the same.');
                        pwc.focus();
                    }
                });
            }

            // === Username availability check ===
            const inputUsername = document.getElementById('username');
            const btnCheck = document.getElementById('btnCheckUsername');
            const statusEl = document.getElementById('usernameStatus');
            const urlCheck = document.getElementById('usernameCheckUrl')?.value;

            if (!inputUsername || !btnCheck || !statusEl || !urlCheck) return;

            const spinner = btnCheck.querySelector('.spinner-border');
            const btnLabel = btnCheck.querySelector('.label');

            function setLoading(isLoading) {
                if (isLoading) {
                    btnCheck.disabled = true;
                    spinner.classList.remove('d-none');
                    btnLabel.textContent = 'Checking...';
                } else {
                    btnCheck.disabled = false;
                    spinner.classList.add('d-none');
                    btnLabel.textContent = 'Check';
                }
            }

            function showStatus(ok, msg) {
                statusEl.className = 'form-text mt-1';
                if (ok) {
                    statusEl.innerHTML =
                        `<span class="badge bg-success">{{ __('messages.owner.user_management.employees.available') }}</span> <span class="text-success ms-1">${msg}</span>`;
                    inputUsername.classList.remove('is-invalid');
                    inputUsername.classList.add('is-valid');
                } else {
                    statusEl.innerHTML =
                        `<span class="badge bg-danger">{{ __('messages.owner.user_management.employees.taken') }}</span> <span class="text-danger ms-1">${msg}</span>`;
                    inputUsername.classList.remove('is-valid');
                    inputUsername.classList.add('is-invalid');
                }
            }

            function showNeutral(msg) {
                statusEl.className = 'form-text mt-1 text-muted';
                statusEl.textContent = msg || '';
                inputUsername.classList.remove('is-valid', 'is-invalid');
            }

            async function checkUsername() {
                const val = (inputUsername.value || '').trim();

                // validasi ringan di client
                if (!val) {
                    showNeutral('');
                    return;
                }
                if (val.length < 3 || val.length > 30 || !/^[A-Za-z0-9._-]+$/.test(val)) {
                    showStatus(false, 'Format tidak valid.');
                    return;
                }

                try {
                    setLoading(true);
                    const params = new URLSearchParams({
                        username: val
                    });
                    // kalau reuse untuk edit: tambahkan exclude_id
                    const excludeId = inputUsername.dataset.excludeId || '';
                    if (excludeId) params.append('exclude_id', excludeId);

                    const res = await fetch(`${urlCheck}?${params.toString()}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        },
                    });

                    if (res.status === 422) {
                        // validasi server gagal → tampilkan pesan singkat
                        showStatus(false, 'Format not valid.');
                        return;
                    }

                    const data = await res.json();
                    if (data && typeof data.available !== 'undefined') {
                        if (data.available) showStatus(true,
                            '{{ __('messages.owner.user_management.employees.username_available') }} 🎉');
                        else showStatus(false,
                            '{{ __('messages.owner.user_management.employees.username_used') }}');
                    } else {
                        showNeutral('Tidak bisa memeriksa saat ini.');
                    }
                } catch (e) {
                    showNeutral('Terjadi kesalahan jaringan.');
                } finally {
                    setLoading(false);
                }
            }

            // tombol click
            btnCheck.addEventListener('click', checkUsername);

            // debounce saat mengetik
            let t;
            inputUsername.addEventListener('input', () => {
                showNeutral('');
                clearTimeout(t);
                t = setTimeout(checkUsername, 500); // 0.5 detik setelah berhenti mengetik
            });
        });
    </script>
@endpush
