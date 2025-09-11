@extends('layouts.partner')

@section('title', 'Create Employees')
@section('page_title', 'Create New Employee')

@section('content')
<div class="container">
    <a href="{{ route('partner.user-management.employees.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left mr-2"></i>Back to Employees
    </a>
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Create New Employee</h5>
        </div>
        <div class="card-body">
            {{-- Rangkuman semua error validasi --}}
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

            <form action="{{ route('partner.user-management.employees.store') }}" method="POST" enctype="multipart/form-data" id="employeeForm">
                @csrf

                <div class="row">
                    {{-- Employee name --}}
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Employee Name</label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role"
                                    class="form-control @error('role') is-invalid @enderror" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="CASHIER" {{ old('role') == 'CASHIER' ? 'selected' : '' }}>Kasir</option>
                                <option value="KITCHEN" {{ old('role') == 'KITCHEN' ? 'selected' : '' }}>Kitchen</option>
                                <option value="WAITER"  {{ old('role') == 'WAITER'  ? 'selected' : '' }}>Waiter</option>
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
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input
                                type="text"
                                name="username"
                                id="username"
                                class="form-control @error('username') is-invalid @enderror"
                                value="{{ old('username') }}"
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
                </div>

                <div class="row">
                    {{-- Email --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="email" class="form-label">Employee Email <span class="text-danger">*</span></label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $employee->email ?? '') }}"
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
                        <small class="text-muted">Format: JPG, PNG, WEBP. Maks 2 MB.</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- Preview --}}
                        <div id="imagePreviewWrapper" class="mt-2 d-none">
                            <div class="position-relative" style="width: 180px;">
                                <img id="imagePreview" src="" alt="Image preview"
                                     class="img-thumbnail rounded w-100 h-auto">
                                <button type="button"
                                        id="clearImageBtn"
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
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                minlength="8"
                                required
                                autocomplete="new-password"
                                placeholder="Min. 8 karakter"
                            >
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">Show</button>
                        </div>
                        <small class="text-muted">Minimal 8 karakter, disarankan kombinasi huruf & angka.</small>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                minlength="8"
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi password"
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
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===== Image preview =====
    const input    = document.getElementById('image');
    const wrapper  = document.getElementById('imagePreviewWrapper');
    const preview  = document.getElementById('imagePreview');
    const info     = document.getElementById('imageInfo');
    const clearBtn = document.getElementById('clearImageBtn');

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
            if (!file) { resetPreview(); return; }

            if (!ALLOWED.includes(file.type)) {
                alert('Tipe file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
                input.value = '';
                resetPreview();
                return;
            }
            if (file.size > MAX_SIZE) {
                alert('Ukuran file melebihi 2 MB.');
                input.value = '';
                resetPreview();
                return;
            }

            const url = URL.createObjectURL(file);
            preview.src = url;
            info.textContent = `${file.name} • ${bytesToSize(file.size)}`;
            wrapper.classList.remove('d-none');
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
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
            inp.type   = isPw ? 'text' : 'password';
            btn.textContent = isPw ? 'Hide' : 'Show';
        });
    }
    bindToggle('togglePassword', 'password');
    bindToggle('togglePasswordConfirm', 'password_confirmation');

    // ===== Client check: confirm password must match =====
    const form = document.getElementById('employeeForm');
    const pw   = document.getElementById('password');
    const pwc  = document.getElementById('password_confirmation');

    if (form && pw && pwc) {
        form.addEventListener('submit', function (e) {
            if (pw.value.length < 8) {
                e.preventDefault();
                alert('Password minimal 8 karakter.');
                pw.focus();
                return;
            }
            if (pw.value !== pwc.value) {
                e.preventDefault();
                alert('Konfirmasi password tidak sama.');
                pwc.focus();
            }
        });
    }
});
</script>
@endpush
