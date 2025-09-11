@extends('layouts.partner')

@section('title', 'Edit Employee')
@section('page_title', 'Edit Employee')

@section('content')
<div class="container">
    <a href="{{ route('partner.user-management.employees.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left mr-2"></i>Back to Employees
    </a>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Employee</h5>
        </div>
        <div class="card-body">
            {{-- Error list --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Periksa kembali input kamu:</strong>
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

            <form action="{{ route('partner.user-management.employees.update', $employee) }}" method="POST" enctype="multipart/form-data" id="employeeForm">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Name --}}
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Employee Name</label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $employee->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role"
                                class="form-control @error('role') is-invalid @enderror" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="CASHIER" {{ old('role', $employee->role) === 'CASHIER' ? 'selected' : '' }}>Kasir</option>
                            <option value="KITCHEN" {{ old('role', $employee->role) === 'KITCHEN' ? 'selected' : '' }}>Kitchen</option>
                            <option value="WAITER"  {{ old('role', $employee->role) === 'WAITER'  ? 'selected' : '' }}>Waiter</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Username --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
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
                                autocomplete="username"
                                autocapitalize="none"
                                spellcheck="false"
                            >
                        </div>
                        <small class="text-muted">3–30 karakter, boleh huruf/angka, titik (.), underscore (_), dan dash (-).</small>
                        @error('username')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
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
                        <label for="email" class="form-label">Employee Email <span class="text-danger">*</span></label>
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
                        <label for="image" class="form-label">Upload Image</label>
                        <input
                            type="file"
                            name="image"
                            id="image"
                            class="form-control @error('image') is-invalid @enderror"
                            accept="image/*"
                        >
                        <small class="text-muted d-block">Format: JPG, PNG, WEBP. Maks 2 MB.</small>
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
                                        aria-label="Hapus gambar">
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
                        <label for="password" class="form-label">Password (opsional)</label>
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
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">Show</button>
                        </div>
                        <small class="text-muted">Isi untuk mengganti password. Minimal 8 karakter.</small>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
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
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm" tabindex="-1">Show</button>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex justify-content-end">
                    <a href="{{ route('partner.user-management.employees.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>
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
                alert('Tipe file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
                input.value = '';
                return;
            }
            if (file.size > MAX_SIZE) {
                alert('Ukuran file melebihi 2 MB.');
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
                alert('Password minimal 8 karakter.');
                pw.focus();
                return;
            }
            if (pw.value.length > 0 && pw.value !== pwc.value) {
                e.preventDefault();
                alert('Konfirmasi password tidak sama.');
                pwc.focus();
            }
        });
    }
});
</script>
@endpush
