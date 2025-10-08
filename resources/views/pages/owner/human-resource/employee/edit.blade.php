@extends('layouts.owner')

@section('title',  __('messages.owner.user_management.employees.edit_employee'))
@section('page_title',  __('messages.owner.user_management.employees.edit_employee'))

@section('content')
<div class="container owner-emp-edit"> {{-- tambahkan class page-scope --}}
    <a href="{{ route('owner.user-owner.employees.index') }}" class="btn btn-outline-choco mb-3">
        <i class="fas fa-arrow-left mr-2"></i>{{ __('messages.owner.user_management.employees.back_to_employees') }}
    </a>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('messages.owner.user_management.employees.edit_employee') }}</h5>
        </div>
        <div class="card-body">
            {{-- Error list --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>{{ __('messages.owner.user_management.employees.alert_1') }}</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if ($errors->has('error'))
                <div class="alert alert-danger">{{ $errors->first('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('owner.user-owner.employees.update', $employee) }}" method="POST" enctype="multipart/form-data" id="employeeForm">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Name --}}
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">{{ __('messages.owner.user_management.employees.employee_name') }}</label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $employee->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">{{ __('messages.owner.user_management.employees.role') }}</label>
                        <select name="role" id="role"
                                class="form-control @error('role') is-invalid @enderror" required>
                            <option value="">{{ __('messages.owner.user_management.employees.select_status') }}</option>
                            <option value="CASHIER" {{ old('role', $employee->role) === 'CASHIER' ? 'selected' : '' }}>{{ __('messages.owner.user_management.employees.cashier') }}</option>
                            <option value="KITCHEN" {{ old('role', $employee->role) === 'KITCHEN' ? 'selected' : '' }}>{{ __('messages.owner.user_management.employees.kitchen') }}</option>
                            <option value="WAITER"  {{ old('role', $employee->role) === 'WAITER'  ? 'selected' : '' }}>{{ __('messages.owner.user_management.employees.waiter') }}</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Username --}}
                <div class="row">
                    <input type="hidden" id="usernameCheckUrl" value="{{ route('owner.user-owner.employees.check-username') }}">
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">{{ __('messages.owner.user_management.employees.username') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input
                                type="text"
                                name="username"
                                id="username"
                                class="form-control @error('username') is-invalid @enderror"
                                value="{{ old('username', $employee->user_name) }}"
                                required
                                minlength="3"
                                maxlength="30"
                                pattern="^[A-Za-z0-9._-]+$"
                                placeholder="contoh: budi.setiawan"
                                autocomplete="user_name"
                                autocapitalize="none"
                                spellcheck="false"
                                data-exclude-id="{{ $employee->id }}"  {{-- penting untuk edit --}}
                            >
                            <button type="button" id="btnCheckUsername" class="btn btn-outline-primary">
                                <span class="label">Check</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                        <small class="text-muted">{{ __('messages.owner.user_management.employees.muted_text_1') }}</small>

                        {{-- area status --}}
                        <div id="usernameStatus" class="form-text mt-1"></div>

                        @error('username')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="partner" class="form-label">{{ __('messages.owner.user_management.employees.outlet') }}</label>
                            <select name="partner" id="partner"
                                    class="form-control @error('partner') is-invalid @enderror" required>
                                <option value="">{{ __('messages.owner.user_management.employees.select_status') }}</option>
                                @foreach ($partners as $partner)
                                    <option value="{{ $partner->id }}" {{ old('partner_id', $employee->partner_id) === $partner->id ? 'selected' : ''}}>{{ $partner->name }}</option>
                                @endforeach
                            </select>
                            @error('partner')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Aktif / Nonaktif --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                {{ old('is_active', $employee->is_active) ? 'Aktif' : 'Nonaktif' }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Email --}}
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">{{ __('messages.owner.user_management.employees.employee_email') }} <span class="text-danger">*</span></label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $employee->email) }}"
                            placeholder="name@company.com"
                            required
                            maxlength="254"
                            autocomplete="email"
                            autocapitalize="off"
                            spellcheck="false"
                            inputmode="email"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Image --}}
                    <div class="col-md-6 mb-3">
                        <label for="image" class="form-label">{{ __('messages.owner.user_management.employees.upload_image') }}</label>
                        <input
                            type="file"
                            name="image"
                            id="image"
                            class="form-control @error('image') is-invalid @enderror"
                            accept="image/*"
                        >
                        <small class="text-muted d-block">Format: JPG, PNG, WEBP. Max 2 MB.</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>

                        {{-- Preview lama / baru --}}
                        <div id="imagePreviewWrapper" class="mt-2 {{ $employee->image ? '' : 'd-none' }}">
                            <div class="position-relative" style="width: 180px;">
                                <img id="imagePreview"
                                     src="{{ $employee->image ? asset('storage/'.$employee->image) : '' }}"
                                     alt="Image preview"
                                     class="img-thumbnail rounded w-100 h-auto">
                                <button type="button"
                                        id="clearImageBtn"
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                        aria-label="{{ __('messages.owner.user_management.employees.delete_picture') }}">
                                    &times;
                                </button>
                            </div>
                            <small id="imageInfo" class="text-muted d-block mt-1">
                                {{ $employee->image ? basename($employee->image) : '' }}
                            </small>
                        </div>

                        {{-- penanda hapus gambar --}}
                        <input type="hidden" name="remove_image" id="remove_image" value="0">
                    </div>
                </div>

                {{-- Password (opsional) --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">{{ __('messages.owner.user_management.employees.password_optional') }}</label>
                        <div class="input-group">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                minlength="8"
                                autocomplete="new-password"
                                placeholder="Kosongkan jika tidak diubah"
                            >
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">{{ __('messages.owner.user_management.employees.show') }}</button>
                        </div>
                        <small class="text-muted">{{ __('messages.owner.user_management.employees.muted_text_2') }}</small>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">{{ __('messages.owner.user_management.employees.password_confirmation') }}</label>
                        <div class="input-group">
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                minlength="8"
                                autocomplete="new-password"
                                placeholder="Ulangi password jika diisi"
                            >
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm" tabindex="-1">{{ __('messages.owner.user_management.employees.show') }}</button>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex justify-content-end">
                    <a href="{{ route('owner.user-owner.employees.index') }}" class="btn btn-light border me-2">{{ __('messages.owner.user_management.employees.cancel') }}</a>
                    <button type="submit" class="btn btn-choco">{{ __('messages.owner.user_management.employees.update') }}</button>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
    <style>
/* ===== Owner › Employee Edit (page scope) ===== */
.owner-emp-edit{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* Card & header */
.owner-emp-edit .card{
  border:0; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden;
}
.owner-emp-edit .card-header{
  background:#fff; border-bottom:1px solid #eef1f4; padding:.85rem 1rem;
}
.owner-emp-edit .card-title{ color:var(--ink); font-weight:600; }

/* Alerts */
.owner-emp-edit .alert{ border-left:4px solid var(--choco); border-radius:10px; }
.owner-emp-edit .alert-danger{ background:#fff5f5; border-color:#fde2e2; color:#991b1b; }
.owner-emp-edit .alert-success{ background:#f0fdf4; border-color:#dcfce7; color:#166534; }

/* Labels & fields */
.owner-emp-edit .form-label{ font-weight:600; color:#374151; }
.owner-emp-edit .form-control:focus,
.owner-emp-edit select.form-control:focus{
  border-color:var(--choco);
  box-shadow:0 0 0 .2rem rgba(140,16,0,.15);
}

/* Input group “@username” & check button */
.owner-emp-edit .input-group-text{
  background:rgba(140,16,0,.08); color:var(--choco); border-color:rgba(140,16,0,.25);
}
.owner-emp-edit #btnCheckUsername{
  border-color:var(--choco); color:var(--choco); background:#fff;
}
.owner-emp-edit #btnCheckUsername:hover{
  background:var(--choco); color:#fff; border-color:var(--choco);
}
.owner-emp-edit #btnCheckUsername .spinner-border{ margin-left:.35rem; }

/* Username status badge */
.owner-emp-edit #usernameStatus .badge{ border-radius:999px; padding:.28rem .6rem; font-weight:600; }
.owner-emp-edit #usernameStatus .bg-success{ background:#ecfdf5 !important; color:#065f46 !important; border:1px solid #a7f3d0; }
.owner-emp-edit #usernameStatus .bg-danger{ background:#fee2e2 !important; color:#991b1b !important; border:1px solid #fecaca; }

/* Switch */
.owner-emp-edit .form-check-input:checked{ background-color:var(--choco); border-color:var(--choco); }
.owner-emp-edit .form-check-input:focus{ box-shadow:0 0 0 .2rem rgba(140,16,0,.15); border-color:var(--soft-choco); }

/* Image preview */
.owner-emp-edit #imagePreviewWrapper .img-thumbnail{
  border:0; border-radius:var(--radius); box-shadow:var(--shadow);
}
.owner-emp-edit #clearImageBtn{
  transform:translate(35%,-35%); border-radius:999px; width:28px; height:28px; padding:0; line-height:26px;
}

/* Buttons – brand */
.owner-emp-edit .btn-choco{ background:var(--choco); border-color:var(--choco); color:#fff; }
.owner-emp-edit .btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); }
.owner-emp-edit .btn-outline-choco{ color:var(--choco); border-color:var(--choco);}
.owner-emp-edit .btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }

/* Fallback reskin default buttons (jaga kompatibilitas) */
.owner-emp-edit .btn-primary{ background:var(--choco); border-color:var(--choco); }
.owner-emp-edit .btn-primary:hover{ background:var(--soft-choco); border-color:var(--soft-choco); }
.owner-emp-edit .btn-secondary{ color:var(--choco); background:#fff; border-color:var(--choco); }
.owner-emp-edit .btn-secondary:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }

/* Action row */
.owner-emp-edit .d-flex .btn{ min-width:120px; }

/* Small helpers */
.owner-emp-edit .text-muted{ color:#6b7280 !important; }
</style>

</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===== Switch label aktif/nonaktif =====
    const isActive = document.getElementById('is_active');
    if (isActive) {
        isActive.addEventListener('change', function () {
            const label = document.querySelector('label[for="is_active"] + .form-check .form-check-label') ||
                          this.closest('.form-check').querySelector('.form-check-label');
            if (label) label.textContent = this.checked ? 'Aktif' : 'Nonaktif';
        });
    }

    // ===== Image preview =====
    const input    = document.getElementById('image');
    const wrapper  = document.getElementById('imagePreviewWrapper');
    const preview  = document.getElementById('imagePreview');
    const info     = document.getElementById('imageInfo');
    const clearBtn = document.getElementById('clearImageBtn');
    const removeEl = document.getElementById('remove_image');

    const MAX_SIZE = 2 * 1024 * 1024; // 2 MB
    const ALLOWED  = ['image/jpeg', 'image/png', 'image/webp'];

    function bytesToSize(bytes) {
        const units = ['B','KB','MB','GB'];
        let i = 0, num = bytes;
        while (num >= 1024 && i < units.length - 1) { num /= 1024; i++; }
        return `${num.toFixed(num < 10 && i > 0 ? 1 : 0)} ${units[i]}`;
    }

    function resetPreview() {
        preview.src = '';
        info.textContent = '';
        wrapper.classList.add('d-none');
    }

    if (input) {
        input.addEventListener('change', function () {
            const file = input.files && input.files[0];
            if (!file) { return; }

            if (!ALLOWED.includes(file.type)) {
                alert('File not supported. Use JPG, PNG, atau WEBP.');
                input.value = '';
                return;
            }
            if (file.size > MAX_SIZE) {
                alert('FIle size more than 2 MB.');
                input.value = '';
                return;
            }

            const url = URL.createObjectURL(file);
            preview.src = url;
            info.textContent = `${file.name} • ${bytesToSize(file.size)}`;
            wrapper.classList.remove('d-none');

            // User memilih gambar baru => batal hapus gambar lama
            if (removeEl) removeEl.value = '0';
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (input) input.value = '';
            resetPreview();
            if (removeEl) removeEl.value = '1'; // tandai agar server menghapus gambar lama
        });
    }

    // ===== Password show/hide =====
    function bindToggle(btnId, inputId) {
        const btn = document.getElementById(btnId);
        const inp = document.getElementById(inputId);
        if (!btn || !inp) return;
        btn.addEventListener('click', () => {
            const isPw = inp.type === 'password';
            inp.type   = isPw ? 'text' : 'password';
            btn.textContent = isPw ? 'Hide' : 'Show';
        });
    }
    bindToggle('togglePassword', 'password');
    bindToggle('togglePasswordConfirm', 'password_confirmation');

    // ===== Optional: validasi client-side password match jika diisi =====
    const form = document.getElementById('employeeForm');
    const pw   = document.getElementById('password');
    const pwc  = document.getElementById('password_confirmation');

    if (form && pw && pwc) {
        form.addEventListener('submit', function (e) {
            if (pw.value.length > 0 && pw.value.length < 8) {
                e.preventDefault();
                alert('{{ __('messages.owner.user_management.employees.muted_text_2') }}');
                pw.focus();
                return;
            }
            if (pw.value.length > 0 && pw.value !== pwc.value) {
                e.preventDefault();
                alert('{{ __('messages.owner.user_management.employees.password_not_same') }}');
                pwc.focus();
            }
        });
    }

    // === Username availability check (EDIT) ===
    const inputUsername = document.getElementById('username');
    const btnCheck      = document.getElementById('btnCheckUsername');
    const statusEl      = document.getElementById('usernameStatus');
    const urlCheck      = document.getElementById('usernameCheckUrl')?.value;

    if (!inputUsername || !btnCheck || !statusEl || !urlCheck) return;

    const spinner   = btnCheck.querySelector('.spinner-border');
    const btnLabel  = btnCheck.querySelector('.label');

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
            statusEl.innerHTML = `<span class="badge bg-success">{{ __('messages.owner.user_management.employees.available') }}</span> <span class="text-success ms-1">${msg}</span>`;
            inputUsername.classList.remove('is-invalid');
            inputUsername.classList.add('is-valid');
        } else {
            statusEl.innerHTML = `<span class="badge bg-danger">{{ __('messages.owner.user_management.employees.taken') }}</span> <span class="text-danger ms-1">${msg}</span>`;
            inputUsername.classList.remove('is-valid');
            inputUsername.classList.add('is-invalid');
        }
    }

    function showNeutral(msg) {
        statusEl.className = 'form-text mt-1 text-muted';
        statusEl.textContent = msg || '';
        inputUsername.classList.remove('is-valid','is-invalid');
    }

    async function checkUsername() {
        const val = (inputUsername.value || '').trim();
        if (!val) { showNeutral(''); return; }

        // client-side rules
        if (val.length < 3 || val.length > 30 || !/^[A-Za-z0-9._-]+$/.test(val)) {
            showStatus(false, 'Format tidak valid.');
            return;
        }

        try {
            setLoading(true);
            const params = new URLSearchParams({ username: val });

            // kunci: exclude id employee yang sedang di-edit
            const excludeId = inputUsername.dataset.excludeId || '';
            if (excludeId) params.append('exclude_id', excludeId);

            const res = await fetch(`${urlCheck}?${params.toString()}`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
            });

            if (res.status === 422) { showStatus(false, 'Format not valid.'); return; }

            const data = await res.json();
            if (data && typeof data.available !== 'undefined') {
                if (data.available) showStatus(true, '{{ __('messages.owner.user_management.employees.username_available') }} 🎉');
                else showStatus(false, '{{ __('messages.owner.user_management.employees.username_used') }}');
            } else {
                showNeutral('Tidak bisa memeriksa saat ini.');
            }
        } catch (e) {
            showNeutral('Terjadi kesalahan jaringan.');
        } finally {
            setLoading(false);
        }
    }

    btnCheck.addEventListener('click', checkUsername);

    // debounce saat mengetik
    let t;
    inputUsername.addEventListener('input', () => {
        showNeutral('');
        clearTimeout(t);
        t = setTimeout(checkUsername, 500);
    });
});
</script>
@endpush
