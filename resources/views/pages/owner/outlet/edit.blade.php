@extends('layouts.owner')

@section('title', 'Edit Outlet')
@section('page_title', 'Ubah Data Outlet')

@section('content')
<div class="container owner-outlet-edit"> {{-- PAGE SCOPE --}}

    <a href="{{ route('owner.user-owner.outlets.index') }}" class="btn btn-outline-choco mb-3">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-store text-choco"></i>
                Edit Outlet: {{ $outlet->name }}
            </h5>
        </div>

        <div class="card-body pt-0">
            {{-- Alerts --}}
            @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-start gap-2">
                    <i class="fas fa-circle-exclamation mt-1"></i>
                    <div>
                        <strong>Periksa kembali input kamu:</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
            @endif
            @if (session('status'))
                <div class="alert alert-info"><i class="fas fa-circle-info me-2"></i>{{ session('status') }}</div>
            @endif

            <form action="{{ route('owner.user-owner.outlets.update', $outlet->id) }}" method="POST" enctype="multipart/form-data" id="employeeForm" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                {{-- SECTION: Info Dasar --}}
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon"><i class="fas fa-id-card"></i></span>
                        <h6 class="mb-0">Informasi Dasar</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label required">Nama Outlet</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $outlet->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- URL cek username --}}
                        <input type="hidden" id="usernameCheckUrl" value="{{ route('owner.user-owner.outlets.check-username') }}">

                        <div class="col-md-6">
                            <label for="username" class="form-label required">Username</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">@</span>
                                <input
                                    type="text"
                                    name="username"
                                    id="username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    value="{{ old('username', $outlet->username) }}"
                                    required minlength="3" maxlength="30"
                                    pattern="^[A-Za-z0-9._\-]+$"
                                    autocomplete="username" autocapitalize="none" spellcheck="false"
                                    data-exclude-id="{{ $outlet->id }}"
                                >
                                <button type="button" id="btnCheckUsername" class="btn btn-outline-choco">
                                    <span class="label">Check</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                                @error('username') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <small class="text-muted">3–30 karakter: huruf/angka, titik (.), underscore (_), dash (-).</small>

                            <div id="usernameStatus" class="form-text mt-1"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="slug" class="form-label required">Slug</label>
                            <div class="input-group has-validation">
                                <input
                                    type="text"
                                    name="slug"
                                    id="slug"
                                    class="form-control @error('slug') is-invalid @enderror"
                                    value="{{ old('slug', $outlet->slug) }}"
                                    required minlength="3" maxlength="30"
                                    pattern="^[A-Za-z0-9._\-]+$"
                                    placeholder="contoh: cava-coffee-malioboro"
                                    autocomplete="off" autocapitalize="none" spellcheck="false"
                                    disabled
                                >
                            </div>
                            <small class="text-muted">Slug tidak dapat diubah.</small>
                            <div id="slugStatus" class="form-text mt-1"></div>
                        </div>
                    </div>
                </div>

                {{-- SECTION: Alamat Outlet --}}
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon"><i class="fas fa-location-dot"></i></span>
                        <h6 class="mb-0">Alamat Outlet</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="province" class="form-label">Provinsi</label>
                            <div class="position-relative">
                                <select id="province" name="province"
                                        class="form-select w-100 @error('province') is-invalid @enderror" disabled
                                        data-selected-id="{{ old('province', $outlet->province_id) }}">
                                    <option value="">Memuat provinsi…</option>
                                </select>
                                <span class="loading-spinner d-none" id="spnProvince"></span>
                            </div>
                            <input type="hidden" id="province_name" name="province_name" value="{{ old('province_name', $outlet->province) }}">
                            @error('province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="city" class="form-label">Kota/Kabupaten</label>
                            <div class="position-relative">
                                <select id="city" name="city"
                                        class="form-select w-100 @error('city') is-invalid @enderror" disabled
                                        data-selected-id="{{ old('city', $outlet->city_id) }}">
                                    <option value="">Pilih provinsi dahulu</option>
                                </select>
                                <span class="loading-spinner d-none" id="spnCity"></span>
                            </div>
                            <input type="hidden" id="city_name" name="city_name" value="{{ old('city_name', $outlet->city) }}">
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="district" class="form-label">Kecamatan</label>
                            <div class="position-relative">
                                <select id="district" name="district"
                                        class="form-select w-100 @error('district') is-invalid @enderror" disabled
                                        data-selected-id="{{ old('district', $outlet->subdistrict_id) }}">
                                    <option value="">Pilih kota dahulu</option>
                                </select>
                                <span class="loading-spinner d-none" id="spnDistrict"></span>
                            </div>
                            <input type="hidden" id="district_name" name="district_name" value="{{ old('district_name', $outlet->subdistrict) }}">
                            @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="village" class="form-label">Kelurahan/Desa</label>
                            <div class="position-relative">
                                <select id="village" name="village"
                                        class="form-select w-100 @error('village') is-invalid @enderror" disabled
                                        data-selected-id="{{ old('village', $outlet->urban_village_id) }}">
                                    <option value="">Pilih kecamatan dahulu</option>
                                </select>
                                <span class="loading-spinner d-none" id="spnVillage"></span>
                            </div>
                            <input type="hidden" id="village_name" name="village_name" value="{{ old('village_name', $outlet->urban_village) }}">
                            @error('village') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label mt-2">Alamat Detail</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-road"></i></span>
                                <input type="text" id="address" name="address"
                                       class="form-control @error('address') is-invalid @enderror"
                                       value="{{ old('address', $outlet->address) }}" placeholder="Nama jalan, RT/RW, nomor, patokan">
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION: Kontak & Media --}}
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon"><i class="fas fa-envelope"></i></span>
                        <h6 class="mb-0">Kontak & Gambar</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label required">Email Outlet</label>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $outlet->email) }}"
                                   placeholder="name@company.com" required maxlength="254"
                                   autocomplete="email" autocapitalize="off" spellcheck="false" inputmode="email">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="image" class="form-label">Unggah Gambar (opsional)</label>
                            <input type="file" name="image" id="image"
                                   class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">JPG/PNG/WEBP, maks 2 MB. Biarkan kosong jika tidak mengubah gambar.</small>
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            {{-- Preview --}}
                            <div id="imagePreviewWrapper" class="mt-2 {{ $outlet->logo ? '' : 'd-none' }}">
                                <div class="position-relative preview-box">
                                    <img id="imagePreview" src="{{ $outlet->logo ? asset('storage/'.$outlet->logo) : '' }}" alt="Preview" class="img-thumbnail rounded w-100 h-auto">
                                    <button type="button" id="clearImageBtn" class="btn btn-sm btn-danger position-absolute top-0 end-0" aria-label="Hapus gambar">
                                        &times;
                                    </button>
                                </div>
                                <small id="imageInfo" class="text-muted d-block mt-1">{{ $outlet->logo ? basename($outlet->logo) : '' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION: Status Outlet --}}
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon"><i class="fas fa-toggle-on"></i></span>
                        <h6 class="mb-0">Status Outlet</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label d-block">Aktifkan Outlet</label>
                            <input type="hidden" name="is_active" value="0">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input toggle-ios @error('is_active') is-invalid @enderror"
                                    type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                                    {{ old('is_active', (int) $outlet->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <span id="isActiveLabel">{{ old('is_active', (int) $outlet->is_active) ? 'Aktif' : 'Nonaktif' }}</span>
                                </label>
                                @error('is_active') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <small class="text-muted">Jika dimatikan, outlet tidak tampil untuk pelanggan.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label d-block">Aktifkan QR Order</label>
                            <input type="hidden" name="is_qr_active" value="0">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input toggle-ios @error('is_qr_active') is-invalid @enderror"
                                    type="checkbox" role="switch" id="is_qr_active" name="is_qr_active" value="1"
                                    {{ old('is_qr_active', (int) $outlet->is_qr_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_qr_active">
                                    <span id="isQrActiveLabel">{{ old('is_qr_active', (int) $outlet->is_qr_active) ? 'Aktif' : 'Nonaktif' }}</span>
                                </label>
                                @error('is_qr_active') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <small class="text-muted">Mengontrol apakah pemesanan via QR untuk outlet ini aktif.</small>
                        </div>
                    </div>
                </div>

                {{-- SECTION: Keamanan --}}
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon"><i class="fas fa-lock"></i></span>
                        <h6 class="mb-0">Keamanan</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password (opsional)</label>
                            <div class="input-group has-validation">
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       minlength="8" autocomplete="new-password" placeholder="Kosongkan jika tidak diubah">
                                <button class="btn btn-outline-choco" type="button" id="togglePassword" tabindex="-1">Show</button>
                                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <div class="input-group has-validation">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control @error('password_confirmation') is-invalid @enderror"
                                       minlength="8" autocomplete="new-password" placeholder="Ulangi password jika diganti">
                                <button class="btn btn-outline-choco" type="button" id="togglePasswordConfirm" tabindex="-1">Show</button>
                                @error('password_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sticky Actions --}}
                <div class="form-actions sticky-actions mt-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('owner.user-owner.outlets.index') }}" class="btn btn-outline-choco">
                            <i class="fas fa-xmark me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-choco">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
/* ===== Owner › Outlet Edit (page scope) ===== */
.owner-outlet-edit{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
  --switch-w: 2.6rem;   /* lebar switch yang kamu mau */
  --switch-h: 1.4rem;   /* tinggi switch */
  --switch-gap: .65rem;
}

.owner-outlet-edit .form-check.form-switch{
  padding-left: calc(var(--switch-w) + var(--switch-gap));
}

.owner-outlet-edit .form-check.form-switch .form-check-input{
  width: var(--switch-w);
  height: var(--switch-h);
  margin-left: calc(-1 * (var(--switch-w) + var(--switch-gap)));
}

.owner-outlet-edit .form-check.form-switch .form-check-label{
  margin-left: .1rem;
}

/* Card & header */
.owner-outlet-edit .card{
  border:0; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden;
}
.owner-outlet-edit .card-header{
  background:#fff; border-bottom:1px solid #eef1f4;
}
.owner-outlet-edit .card-title{ color:var(--ink); font-weight:700; }

/* Brand helpers */
.owner-outlet-edit .text-choco{ color:var(--choco) !important; }
.owner-outlet-edit .btn-choco{ background:var(--choco); border-color:var(--choco); color:#fff; }
.owner-outlet-edit .btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); color:#fff; }
.owner-outlet-edit .btn-outline-choco{ color:var(--choco); border-color:var(--choco); background:#fff; }
.owner-outlet-edit .btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }

/* Alerts */
.owner-outlet-edit .alert{ border-left:4px solid var(--choco); border-radius:10px; }
.owner-outlet-edit .alert-danger{ background:#fff5f5; border-color:#fde2e2; color:#991b1b; }
.owner-outlet-edit .alert-success{ background:#f0fdf4; border-color:#dcfce7; color:#166534; }
.owner-outlet-edit .alert-info{ background:#eff6ff; border-color:#dbeafe; color:#1d4ed8; }

/* Labels & fields */
.owner-outlet-edit .form-label{ font-weight:600; color:#374151; }
.owner-outlet-edit .required::after{ content:" *"; color:#dc3545; }
.owner-outlet-edit .form-control:focus,
.owner-outlet-edit .form-select:focus{
  border-color:var(--choco);
  box-shadow:0 0 0 .2rem rgba(140,16,0,.15);
}

/* Input group cosmetics */
.owner-outlet-edit .input-group-text{
  background:rgba(140,16,0,.08); color:var(--choco);
  border-color:rgba(140,16,0,.25);
}
.owner-outlet-edit .input-group > .form-control{ border-right:0; }
.owner-outlet-edit .input-group .btn{ border-radius:0 .5rem .5rem 0; }

/* Sectioning */
.owner-outlet-edit .form-section{ padding: 1.15rem 0; border-top:1px solid #eef1f4; }
.owner-outlet-edit .form-section:first-of-type{ border-top:0; }
.owner-outlet-edit .section-header{ display:flex; align-items:center; gap:.6rem; margin-bottom:.85rem; }
.owner-outlet-edit .section-icon{
  width:36px; height:36px; border-radius:999px; display:grid; place-items:center;
  background:rgba(140,16,0,.08); color:var(--choco);
}

/* Select spinners */
.owner-outlet-edit .loading-spinner{
  position:absolute; right:.75rem; top:50%; transform:translateY(-50%);
  width:1rem; height:1rem; border:.15rem solid rgba(140,16,0,.2);
  border-top-color:var(--choco); border-radius:50%;
  animation: spin .8s linear infinite;
}
.owner-outlet-edit .d-none{ display:none !important; }
@keyframes spin{ to{ transform:translateY(-50%) rotate(360deg); } }

/* Switch (form-check-input) */
.owner-outlet-edit .form-check-input:checked{
  background-color:var(--choco); border-color:var(--choco);
}
.owner-outlet-edit .form-check-input:focus{
  box-shadow:0 0 0 .2rem rgba(140,16,0,.15);
}
/* iOS-ish bigger toggle */
.owner-outlet-edit .toggle-ios:focus{ outline:0; }

/* Image preview */
.owner-outlet-edit .preview-box{ width: 200px; border-radius: var(--radius); }
.owner-outlet-edit #imagePreviewWrapper .img-thumbnail{
  border:0; border-radius:var(--radius); box-shadow:var(--shadow);
}
.owner-outlet-edit #clearImageBtn{
  transform: translate(35%,-35%);
  border-radius:999px; width:28px; height:28px; padding:0; line-height:26px;
}

/* Sticky actions */
.owner-outlet-edit .sticky-actions{
  position: sticky; bottom: 0;
  background: linear-gradient(180deg, rgba(255,255,255,0) 0%, #fff 30%);
  padding-top:.75rem; margin-top:1rem;
}
.owner-outlet-edit .sticky-actions .btn{ border-radius:10px; min-width:120px; }

/* Small helpers */
.owner-outlet-edit .text-muted{ color:#6b7280 !important; }
</style>
@endsection




@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ==== Image preview (mirip create) ====
    const input    = document.getElementById('image');
    const wrapper  = document.getElementById('imagePreviewWrapper');
    const preview  = document.getElementById('imagePreview');
    const info     = document.getElementById('imageInfo');
    const clearBtn = document.getElementById('clearImageBtn');

    const MAX_SIZE = 2 * 1024 * 1024;
    const ALLOWED  = ['image/jpeg', 'image/png', 'image/webp'];

    function bytesToSize(bytes){const u=['B','KB','MB','GB'];let i=0,n=bytes;while(n>=1024&&i<u.length-1){n/=1024;i++;}return `${n.toFixed(n<10&&i>0?1:0)} ${u[i]}`;}
    function resetPreview(){ if (!wrapper) return; preview.src=''; info.textContent=''; wrapper.classList.add('d-none'); }

    if (input) {
        input.addEventListener('change', function () {
            const file = input.files?.[0];
            if (!file) { resetPreview(); return; }
            if (!ALLOWED.includes(file.type)) { alert('Tipe file tidak didukung. Gunakan JPG, PNG, atau WEBP.'); input.value=''; resetPreview(); return; }
            if (file.size > MAX_SIZE) { alert('Ukuran file melebihi 2 MB.'); input.value=''; resetPreview(); return; }
            const url = URL.createObjectURL(file);
            if (wrapper) wrapper.classList.remove('d-none');
            preview.src = url;
            if (info) info.textContent = `${file.name} • ${bytesToSize(file.size)}`;
        });
    }
    if (clearBtn) { clearBtn.addEventListener('click', function(){ if (input) input.value=''; resetPreview(); }); }

    // ==== Toggle password ====
    function bindToggle(btnId, inputId){const btn=document.getElementById(btnId),inp=document.getElementById(inputId); if(!btn||!inp) return; btn.addEventListener('click',()=>{const isPw=inp.type==='password'; inp.type=isPw?'text':'password'; btn.textContent=isPw?'Hide':'Show';});}
    bindToggle('togglePassword','password');
    bindToggle('togglePasswordConfirm','password_confirmation');

    // ==== Validasi konfirmasi (hanya jika password diisi) ====
    const form = document.getElementById('employeeForm');
    const pw   = document.getElementById('password');
    const pwc  = document.getElementById('password_confirmation');
    if (form) {
        form.addEventListener('submit', function(e){
            if (pw.value.length > 0 && pw.value.length < 8) { e.preventDefault(); alert('Password minimal 8 karakter.'); pw.focus(); return; }
            if (pw.value.length > 0 && pw.value !== pwc.value) { e.preventDefault(); alert('Konfirmasi password tidak sama.'); pwc.focus(); }
        });
    }

    // ==== Prefill alamat (chained select) ====
    const API_BASE = "https://www.emsifa.com/api-wilayah-indonesia/api";
    const provinceSelect = document.getElementById("province");
    const citySelect     = document.getElementById("city");
    const districtSelect = document.getElementById("district");
    const villageSelect  = document.getElementById("village");

    const provinceNameInput = document.getElementById("province_name");
    const cityNameInput     = document.getElementById("city_name");
    const districtNameInput = document.getElementById("district_name");
    const villageNameInput  = document.getElementById("village_name");

    const spnProvince = document.getElementById('spnProvince');
    const spnCity     = document.getElementById('spnCity');
    const spnDistrict = document.getElementById('spnDistrict');
    const spnVillage  = document.getElementById('spnVillage');

    const setLoading = (sel, spn, on, ph) => {
        if (on) { sel.innerHTML = `<option value="">${ph}</option>`; sel.disabled = true; spn?.classList.remove('d-none'); }
        else { spn?.classList.add('d-none'); sel.disabled = false; }
    };
    const resetSelect = (sel, msg) => { sel.innerHTML = `<option value="">${msg}</option>`; };
    const fillHiddenName = (selectEl, hidden) => { hidden.value = selectEl.options[selectEl.selectedIndex]?.text || ''; };

    // selected IDs dari server/old()
    const selectedProvince = provinceSelect?.dataset.selectedId || '';
    const selectedCity     = citySelect?.dataset.selectedId || '';
    const selectedDistrict = districtSelect?.dataset.selectedId || '';
    const selectedVillage  = villageSelect?.dataset.selectedId || '';

    // 1) provinces
    setLoading(provinceSelect, spnProvince, true, 'Memuat provinsi…');
    fetch(`${API_BASE}/provinces.json`)
      .then(r => r.json())
      .then(list => {
        resetSelect(provinceSelect, '-- Pilih Provinsi --');
        list.forEach(item => {
          const opt = document.createElement('option');
          opt.value = item.id; opt.textContent = item.name;
          if (selectedProvince && item.id == selectedProvince) opt.selected = true;
          provinceSelect.appendChild(opt);
        });
      })
      .catch(() => { resetSelect(provinceSelect, 'Gagal memuat provinsi'); alert('Gagal memuat daftar provinsi.'); })
      .finally(() => {
        setLoading(provinceSelect, spnProvince, false);
        if (selectedProvince) {
            fillHiddenName(provinceSelect, provinceNameInput);
            // 2) cities
            setLoading(citySelect, spnCity, true, 'Memuat kota…');
            fetch(`${API_BASE}/regencies/${selectedProvince}.json`)
              .then(r => r.json())
              .then(list => {
                resetSelect(citySelect, '-- Pilih Kota --');
                list.forEach(item => {
                  const opt = document.createElement('option');
                  opt.value = item.id; opt.textContent = item.name;
                  if (selectedCity && item.id == selectedCity) opt.selected = true;
                  citySelect.appendChild(opt);
                });
              })
              .finally(() => {
                setLoading(citySelect, spnCity, false);
                if (selectedCity) {
                    fillHiddenName(citySelect, cityNameInput);
                    // 3) districts
                    setLoading(districtSelect, spnDistrict, true, 'Memuat kecamatan…');
                    fetch(`${API_BASE}/districts/${selectedCity}.json`)
                      .then(r => r.json())
                      .then(list => {
                        resetSelect(districtSelect, '-- Pilih Kecamatan --');
                        list.forEach(item => {
                          const opt = document.createElement('option');
                          opt.value = item.id; opt.textContent = item.name;
                          if (selectedDistrict && item.id == selectedDistrict) opt.selected = true;
                          districtSelect.appendChild(opt);
                        });
                      })
                      .finally(() => {
                        setLoading(districtSelect, spnDistrict, false);
                        if (selectedDistrict) {
                            fillHiddenName(districtSelect, districtNameInput);
                            // 4) villages
                            setLoading(villageSelect, spnVillage, true, 'Memuat kelurahan…');
                            fetch(`${API_BASE}/villages/${selectedDistrict}.json`)
                              .then(r => r.json())
                              .then(list => {
                                resetSelect(villageSelect, '-- Pilih Kelurahan --');
                                list.forEach(item => {
                                  const opt = document.createElement('option');
                                  opt.value = item.id; opt.textContent = item.name;
                                  if (selectedVillage && item.id == selectedVillage) opt.selected = true;
                                  villageSelect.appendChild(opt);
                                });
                              })
                              .finally(() => {
                                setLoading(villageSelect, spnVillage, false);
                                if (selectedVillage) fillHiddenName(villageSelect, villageNameInput);
                              });
                        }
                      });
                }
              });
        }
      });

    // Perubahan manual (jika user ganti lagi)
    provinceSelect.addEventListener('change', function(){
        fillHiddenName(provinceSelect, provinceNameInput);
        resetSelect(citySelect,'-- Pilih Kota --'); resetSelect(districtSelect,'-- Pilih Kecamatan --'); resetSelect(villageSelect,'-- Pilih Kelurahan --');
        citySelect.disabled = districtSelect.disabled = villageSelect.disabled = true;

        if (!this.value) return;

        setLoading(citySelect, spnCity, true, 'Memuat kota…');
        fetch(`${API_BASE}/regencies/${this.value}.json`)
            .then(r=>r.json())
            .then(list=>{
                resetSelect(citySelect,'-- Pilih Kota --');
                list.forEach(item=>{
                    const opt=document.createElement('option'); opt.value=item.id; opt.textContent=item.name;
                    citySelect.appendChild(opt);
                });
            })
            .finally(()=> setLoading(citySelect, spnCity, false));
    });

    citySelect.addEventListener('change', function(){
        fillHiddenName(citySelect, cityNameInput);
        resetSelect(districtSelect,'-- Pilih Kecamatan --'); resetSelect(villageSelect,'-- Pilih Kelurahan --');
        districtSelect.disabled = villageSelect.disabled = true;
        if (!this.value) return;

        setLoading(districtSelect, spnDistrict, true, 'Memuat kecamatan…');
        fetch(`${API_BASE}/districts/${this.value}.json`)
            .then(r=>r.json())
            .then(list=>{
                resetSelect(districtSelect,'-- Pilih Kecamatan --');
                list.forEach(item=>{
                    const opt=document.createElement('option'); opt.value=item.id; opt.textContent=item.name;
                    districtSelect.appendChild(opt);
                });
            })
            .finally(()=> setLoading(districtSelect, spnDistrict, false));
    });

    districtSelect.addEventListener('change', function(){
        fillHiddenName(districtSelect, districtNameInput);
        resetSelect(villageSelect,'-- Pilih Kelurahan --');
        villageSelect.disabled = true;
        if (!this.value) return;

        setLoading(villageSelect, spnVillage, true, 'Memuat kelurahan…');
        fetch(`${API_BASE}/villages/${this.value}.json`)
            .then(r=>r.json())
            .then(list=>{
                resetSelect(villageSelect,'-- Pilih Kelurahan --');
                list.forEach(item=>{
                    const opt=document.createElement('option'); opt.value=item.id; opt.textContent=item.name;
                    villageSelect.appendChild(opt);
                });
            })
            .finally(()=> setLoading(villageSelect, spnVillage, false));
    });

    villageSelect.addEventListener('change', function(){
        fillHiddenName(villageSelect, villageNameInput);
    });

    // === Username availability check (USERS TABLE) for EDIT ===
    (function () {
        const inputUsername = document.getElementById('username');
        const btnCheck      = document.getElementById('btnCheckUsername');
        const statusEl      = document.getElementById('usernameStatus');
        const urlCheckEl    = document.getElementById('usernameCheckUrl');
        const urlCheck      = urlCheckEl ? urlCheckEl.value : '';

        if (!inputUsername || !btnCheck || !statusEl || !urlCheck) return;

        const spinner  = btnCheck.querySelector('.spinner-border');
        const btnLabel = btnCheck.querySelector('.label');

        function setUsernameLoading(isLoading) {
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
                statusEl.innerHTML = `<span class="badge bg-success">Available</span> <span class="text-success ms-1">${msg}</span>`;
                inputUsername.classList.remove('is-invalid');
                inputUsername.classList.add('is-valid');
            } else {
                statusEl.innerHTML = `<span class="badge bg-danger">Taken</span> <span class="text-danger ms-1">${msg}</span>`;
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

            // Validasi ringan di client – harus cocok dengan HTML pattern
            if (val.length < 3 || val.length > 30 || !/^[A-Za-z0-9._\-]+$/.test(val)) {
                showStatus(false, 'Format tidak valid.');
                return;
            }

            try {
                setUsernameLoading(true);
                const params = new URLSearchParams({ username: val });

                // kirim exclude_id (ID user/outlet) agar username miliknya sendiri dianggap valid
                const excludeId = inputUsername.dataset.excludeId || '';
                if (excludeId) params.append('exclude_id', excludeId);

                const res = await fetch(`${urlCheck}?${params.toString()}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                });

                if (res.status === 422) { showStatus(false, 'Format tidak valid.'); return; }

                const data = await res.json();
                if (typeof data.available !== 'undefined') {
                    data.available ? showStatus(true, 'Username tersedia 🎉')
                                   : showStatus(false, 'Username sudah dipakai.');
                } else {
                    showNeutral('Tidak bisa memeriksa saat ini.');
                }
            } catch (e) {
                showNeutral('Terjadi kesalahan jaringan.');
            } finally {
                setUsernameLoading(false);
            }
        }

        // klik tombol
        btnCheck.addEventListener('click', checkUsername);

        // auto-check dengan debounce saat mengetik
        let t;
        inputUsername.addEventListener('input', () => {
            showNeutral('');
            clearTimeout(t);
            t = setTimeout(checkUsername, 500);
        });
    })();

    (function() {
        const activeEl = document.getElementById('is_active');
        const activeLbl = document.getElementById('isActiveLabel');
        if (activeEl && activeLbl) {
            activeEl.addEventListener('change', () => {
                activeLbl.textContent = activeEl.checked ? 'Aktif' : 'Nonaktif';
            });
        }

        const qrEl = document.getElementById('is_qr_active');
        const qrLbl = document.getElementById('isQrActiveLabel');
        if (qrEl && qrLbl) {
            qrEl.addEventListener('change', () => {
                qrLbl.textContent = qrEl.checked ? 'Aktif' : 'Nonaktif';
            });
        }
    })();
});
</script>
@endpush
