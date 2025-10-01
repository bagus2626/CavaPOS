@extends('layouts.owner')

@section('title', 'Edit Promotion')
@section('page_title', 'Edit Promotion')

@section('content')
<section class="content">
    <div class="container-fluid owner-promo-edit">
        <div class="row">
            <div class="col-12">

                <a href="{{ route('owner.user-owner.promotions.index') }}" class="btn btn-secondary mb-3">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Promotions
                </a>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Periksa kembali input kamu:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Promotion Information</h3>
                    </div>

                    <form action="{{ route('owner.user-owner.promotions.update', $data->id) }}"
                          method="POST" id="promotionForm" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            {{-- Promotion Name --}}
                            <div class="form-group">
                                <label for="promotion_name" class="required">Promotion Name</label>
                                <input type="text"
                                       class="form-control @error('promotion_name') is-invalid @enderror"
                                       id="promotion_name"
                                       name="promotion_name"
                                       value="{{ old('promotion_name', $data->promotion_name) }}"
                                       placeholder="e.g. Weekend Discount"
                                       required
                                       maxlength="150">
                                @error('promotion_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Type & Value --}}
                            @php $type = old('promotion_type', $data->promotion_type); @endphp
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="promotion_type" class="required">Promotion Type</label>
                                        <select id="promotion_type"
                                                name="promotion_type"
                                                class="form-control @error('promotion_type') is-invalid @enderror"
                                                required>
                                            <option value="">-- Select Type --</option>
                                            <option value="percentage" {{ $type === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                            <option value="amount" {{ $type === 'amount' ? 'selected' : '' }}>Amount (Rp)</option>
                                        </select>
                                        @error('promotion_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group mb-0">
                                        <label for="promotion_value" class="required">Promotion Value</label>

                                        <div class="input-group">
                                            <div class="input-group-prepend" id="prefixAmount" style="display:none;">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="number"
                                                   class="form-control text-right @error('promotion_value') is-invalid @enderror"
                                                   id="promotion_value"
                                                   name="promotion_value"
                                                   value="{{ old('promotion_value', $data->promotion_value) }}"
                                                   inputmode="numeric"
                                                   required>
                                            <div class="input-group-append" id="suffixPercent" style="display:none;">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            @error('promotion_value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>

                                        <small class="form-text text-muted mt-1" id="valueHelp">
                                            {{ $type === 'amount' ? 'Masukkan nominal rupiah (contoh: 10000).' : 'Masukkan persen (1–100).' }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            {{-- Uses Expiry --}}
                            @php $usesExpiryInit = old('uses_expiry', ($data->start_date && $data->end_date) ? 1 : 0); @endphp
                            <div class="form-group mb-1">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="uses_expiry"
                                           name="uses_expiry"
                                           value="1"
                                           {{ $usesExpiryInit ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="uses_expiry">
                                        Menggunakan tanggal berlaku (expired)?
                                    </label>
                                </div>
                                <small class="text-muted">Aktifkan jika promosi memiliki periode mulai & berakhir.</small>
                            </div>

                            {{-- Date Range --}}
                            <div id="dateRangeWrap" class="{{ $usesExpiryInit ? '' : 'd-none' }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="start_date" class="required">Start Date</label>
                                            <input type="datetime-local"
                                                   class="form-control @error('start_date') is-invalid @enderror"
                                                   id="start_date"
                                                   name="start_date"
                                                   value="{{ old('start_date', optional($data->start_date)->format('Y-m-d\TH:i')) }}">
                                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="end_date" class="required">End Date</label>
                                            <input type="datetime-local"
                                                   class="form-control @error('end_date') is-invalid @enderror"
                                                   id="end_date"
                                                   name="end_date"
                                                   value="{{ old('end_date', optional($data->end_date)->format('Y-m-d\TH:i')) }}">
                                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">End Date harus lebih besar dari Start Date.</small>
                            </div>

                            <hr>

                            {{-- Is Active --}}
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="is_active"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active', $data->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Aktifkan promosi</label>
                                </div>
                                <small class="text-muted">Jika dinonaktifkan, promosi tidak akan diterapkan.</small>
                            </div>

                            {{-- Active Days --}}
                            <hr>
                            <div class="form-group">
                                <label class="d-block">Hari Aktif</label>
                                @php
                                    $daysMap = ['mon'=>'Senin','tue'=>'Selasa','wed'=>'Rabu','thu'=>'Kamis','fri'=>'Jumat','sat'=>'Sabtu','sun'=>'Minggu'];
                                    $selectedDays = old('active_days', $data->active_days ?: []);
                                    $isEveryDay = is_array($selectedDays) && count($selectedDays) === 7;
                                @endphp

                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="every_day" {{ $isEveryDay ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="every_day">Setiap Hari</label>
                                </div>

                                <div id="days_grid" class="d-flex flex-wrap gap-2">
                                    @foreach($daysMap as $key => $label)
                                        <div class="custom-control custom-checkbox mr-3 mb-2">
                                            <input type="checkbox"
                                                   class="custom-control-input day-checkbox"
                                                   id="day_{{ $key }}"
                                                   name="active_days[]"
                                                   value="{{ $key }}"
                                                   {{ in_array($key, $selectedDays, true) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="day_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                <small class="text-muted d-block">
                                    Centang <em>"Setiap Hari"</em> atau pilih satu/lebih hari secara manual.
                                </small>

                                @error('active_days') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                @error('active_days.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Description --}}
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description"
                                          name="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          rows="3"
                                          placeholder="Tulis keterangan promosi (opsional)">{{ old('description', $data->description) }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <a href="{{ route('owner.user-owner.promotions.index') }}" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Update Promotion
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
/* ===== Owner › Promotion Edit (page scope) ===== */
.owner-promo-edit{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#fff;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

.btn-secondary{
  background:var(--choco); border-color:var(--choco);
}
.btn-secondary:hover{
  background:var(--soft-choco); border-color:var(--soft-choco);
}

/* Card */
.owner-promo-edit .card{
  border:0; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; background:var(--paper);
}
.owner-promo-edit .card-header{ background:#fff; border-bottom:1px solid #eef1f4; }
.owner-promo-edit .card-title{ color:var(--ink); font-weight:700; }

/* Alerts */
.owner-promo-edit .alert{ border-left:4px solid var(--choco); border-radius:10px; }

/* Buttons – brand choco */
.owner-promo-edit .btn-primary{ background:var(--choco); border-color:var(--choco); }
.owner-promo-edit .btn-primary:hover{ background:var(--soft-choco); border-color:var(--soft-choco); }
.owner-promo-edit .btn-outline-secondary{ border-color:#cbd5e1; color:#374151; background:#fff; }
.owner-promo-edit .btn-outline-secondary:hover{ color:#fff; background:#6b7280; border-color:#6b7280; }

/* Fields */
.owner-promo-edit .form-group label{ font-weight:600; color:#374151; }
.owner-promo-edit .required::after{ content:" *"; color:#dc3545; }
.owner-promo-edit .form-control:focus{
  border-color:var(--choco);
  box-shadow:0 0 0 .2rem rgba(140,16,0,.15);
}

/* Input group cosmetics (Rp / %) */
.owner-promo-edit .input-group-text{
  background:rgba(140,16,0,.08); color:var(--choco);
  border-color:rgba(140,16,0,.25);
}

/* Switch (Bootstrap 4 custom switches) -> choco + tidak keluar bingkai */
.owner-promo-edit .custom-control-input:focus ~ .custom-control-label::before{
  box-shadow:0 0 0 .2rem rgba(140,16,0,.15);
  border-color:var(--choco);
}
.owner-promo-edit .custom-control-input:checked ~ .custom-control-label::before{
  background-color:var(--choco); border-color:var(--choco);
}
/* ukuran & posisi knob switch */
.owner-promo-edit .custom-switch .custom-control-label{
  padding-left: 1rem;
  min-height: 1.8rem;
}
.owner-promo-edit .custom-switch .custom-control-label::before{
  width:2.6rem; height:1.4rem; left:-2rem; top:.12rem; border-radius:1.4rem;
}
.owner-promo-edit .custom-switch .custom-control-label::after{
  width:1rem; height:1rem; left:calc(-2rem + .2rem); top:.33rem; border-radius:50%;
}
/* RESET global transform agar checkbox normal */
.owner-promo-edit .custom-control-input:checked ~ .custom-control-label::after{ transform:none; }
/* KHUSUS switch: geser knob */
.owner-promo-edit .custom-switch .custom-control-input:checked ~ .custom-control-label::after{ transform:translateX(1.2rem); }

/* Date range wrapper */
.owner-promo-edit #dateRangeWrap{
  background:#fff; border:1px solid #eef1f4; border-left:4px solid var(--choco);
  border-radius:10px; padding: .85rem; margin-top:.5rem;
}

/* Days grid */
.owner-promo-edit #days_grid .custom-control{ min-width: 140px; }

/* Footer buttons radius */
.owner-promo-edit .card-footer .btn{ border-radius:10px; }

/* Small text */
.owner-promo-edit .text-muted{ color:#6b7280 !important; }

/* Input-group radius logic: samakan dengan Create */
.owner-promo-edit .input-group:not(.has-prefix):not(.has-suffix) > .form-control{
  border-radius:.25rem !important;
}
.owner-promo-edit .input-group.has-prefix:not(.has-suffix) > .form-control{
  border-top-right-radius:.25rem !important; border-bottom-right-radius:.25rem !important;
}
.owner-promo-edit .input-group.has-prefix .input-group-prepend .input-group-text{
  border-top-left-radius:.25rem; border-bottom-left-radius:.25rem;
}
.owner-promo-edit .input-group.has-suffix:not(.has-prefix) > .form-control{
  border-top-left-radius:.25rem !important; border-bottom-left-radius:.25rem !important;
}
.owner-promo-edit .input-group.has-suffix .input-group-append .input-group-text{
  border-top-right-radius:.25rem; border-bottom-right-radius:.25rem;
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    const typeSel   = document.getElementById('promotion_type');
    const valInput  = document.getElementById('promotion_value');
    const helpText  = document.getElementById('valueHelp');
    const prefixAmt = document.getElementById('prefixAmount');
    const suffixPct = document.getElementById('suffixPercent');

    function applyTypeUI() {
        const t = typeSel.value;
        const ig = document.getElementById('promotion_value').closest('.input-group');

        if (t === 'percentage') {
            prefixAmt.style.display = 'none';
            suffixPct.style.display = '';
            ig.classList.remove('has-prefix');
            ig.classList.add('has-suffix');

            valInput.min = '1'; valInput.max = '100'; valInput.step = '1';
            helpText.textContent = 'Masukkan persen (1–100).';

            if (valInput.value && (+valInput.value > 100)) valInput.value = 100;

        } else if (t === 'amount') {
            prefixAmt.style.display = '';
            suffixPct.style.display = 'none';
            ig.classList.add('has-prefix');
            ig.classList.remove('has-suffix');

            valInput.removeAttribute('max'); valInput.min = '0'; valInput.step = '1';
            helpText.textContent = 'Masukkan nominal rupiah (contoh: 10000).';

        } else {
            prefixAmt.style.display = 'none';
            suffixPct.style.display = 'none';
            ig.classList.remove('has-prefix','has-suffix');

            valInput.removeAttribute('min'); valInput.removeAttribute('max'); valInput.removeAttribute('step');
            helpText.textContent = '';
        }
    }
    typeSel?.addEventListener('change', applyTypeUI);
    applyTypeUI();

    // Uses expiry
    const usesExpiry = document.getElementById('uses_expiry');
    const dateWrap   = document.getElementById('dateRangeWrap');
    const startDate  = document.getElementById('start_date');
    const endDate    = document.getElementById('end_date');

    function toggleDateRange() {
        const show = usesExpiry.checked;
        dateWrap.classList.toggle('d-none', !show);
        [startDate, endDate].forEach(el => { if (el){ el.required = show; } });
    }
    usesExpiry?.addEventListener('change', toggleDateRange);
    toggleDateRange();

    // Guard end > start
    const form = document.getElementById('promotionForm');
    form?.addEventListener('submit', function (e) {
        if (usesExpiry?.checked && startDate.value && endDate.value) {
            const s = new Date(startDate.value);
            const ed = new Date(endDate.value);
            if (ed <= s) {
                e.preventDefault();
                alert('End Date harus lebih besar dari Start Date.');
                endDate.focus();
            }
        }
    });

    // Active days
    const everyDay = document.getElementById('every_day');
    const dayCheckboxes = Array.from(document.querySelectorAll('.day-checkbox'));
    function setAllDays(checked){ dayCheckboxes.forEach(cb => cb.checked = checked); }
    function syncEveryDayFromDays(){
        everyDay.checked = (dayCheckboxes.length > 0 && dayCheckboxes.every(cb => cb.checked));
    }
    everyDay?.addEventListener('change', () => setAllDays(everyDay.checked));
    dayCheckboxes.forEach(cb => cb.addEventListener('change', syncEveryDayFromDays));
    syncEveryDayFromDays();
})();
</script>
@endsection
