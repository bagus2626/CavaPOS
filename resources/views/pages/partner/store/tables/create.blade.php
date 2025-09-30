@extends('layouts.partner')

@section('title', 'Create Tables')
@section('page_title', 'Create New Table')

@push('styles')
  @vite('resources/css/pages/tables-create.css')
@endpush

@section('content')
<div class="container">
  <a href="{{ route('partner.store.tables.index') }}" class="btn btn-outline-choco mb-3">
    <i class="fas fa-arrow-left mr-2"></i>Back to Tables
  </a>

  <div class="card shadow-sm">
    <div class="card-header">
      <h5 class="card-title mb-0">Create New Table</h5>
    </div>

    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          <strong>Periksa kembali input:</strong>
          <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('partner.store.tables.store') }}" method="POST" enctype="multipart/form-data" id="tableForm">
        @csrf

        <div class="row">
          {{-- Table No --}}
          <div class="col-md-6 mb-3">
            <label for="table_no" class="form-label">Table No</label>
            <input type="text" name="table_no" id="table_no"
                   class="form-control @error('table_no') is-invalid @enderror"
                   value="{{ old('table_no') }}" required>
            @error('table_no')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Status --}}
          <div class="col-md-6 mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status"
                    class="form-control select2 @error('status') is-invalid @enderror" required>
              <option value="">-- Pilih Status --</option>
              <option value="available" {{ old('status')=='available'?'selected':'' }}>Available</option>
              <option value="occupied"  {{ old('status')=='occupied' ?'selected':'' }}>Occupied</option>
              <option value="reserved"  {{ old('status')=='reserved' ?'selected':'' }}>Reserved</option>
            </select>
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          {{-- Table Class (datalist) --}}
          <div class="col-md-6 mb-3">
                <label for="table_class" class="form-label">Table Class</label>
                <input list="table_class_list"
                        name="table_class"
                        id="table_class"
                        class="form-control @error('table_class') is-invalid @enderror"
                        value="{{ old('table_class') }}"
                        placeholder="Ketik atau pilih class..." required>

                <datalist id="table_class_list">
                    @if(!empty($table_classes))
                    @foreach($table_classes as $class)
                        <option value="{{ $class }}"></option>
                    @endforeach
                    @endif
                </datalist>

                @error('table_class')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
          </div>

          {{-- Images --}}
          <div class="col-md-6 mb-3">
            <label for="images" class="form-label">Upload Images (max 3)</label>
            <input type="file" name="images[]" id="images"
                   class="form-control @error('images') is-invalid @enderror"
                   accept="image/*" multiple>
            <small class="text-muted d-block">JPG, PNG, WEBP • Maks 2 MB per file • Maks 3 file.</small>
            @error('images')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error('images.*')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror>

            {{-- Preview --}}
            <div id="imagesPreview" class="thumb-list mt-2"></div>
          </div>
        </div>

        {{-- Description --}}
        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <textarea name="description" id="description" rows="3"
                    class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end form-actions">
          <a href="{{ route('partner.store.tables.index') }}" class="btn btn-outline-choco me-2">Cancel</a>
          <button type="submit" class="btn btn-choco">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
    /* ==== Tables Create (page scope) ==== */
:root{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* Card & headings */
.card.shadow-sm{ border:0; border-radius:var(--radius); box-shadow:var(--shadow); }
.card-header{ background:#fff; border-bottom:1px solid #eef1f4; }
.card-title{ color:var(--ink); font-weight:600; }

/* Labels & inputs */
.form-label{ font-weight:600; color:#374151; }
.form-control:focus, select.form-control:focus{
  border-color: var(--choco);
  box-shadow: 0 0 0 .2rem rgba(140,16,0,.15);
}

/* Select2 theme alignment */
.select2-container--bootstrap-5 .select2-selection{
  border-radius:10px; border-color:#e5e7eb;
}
.select2-container--bootstrap-5 .select2-results__option--highlighted{
  background: var(--soft-choco);
}

/* Alerts */
.alert{ border-left:4px solid var(--choco); border-radius:10px; }
.alert-danger{ background:#fff5f5; border-color:#fde2e2; color:#991b1b; }

/* Actions */
.form-actions .btn{ min-width:120px; }

/* Brand buttons (fallback jika belum di theme global) */
.btn-choco{ background:var(--choco); border-color:var(--choco); color:#fff; }
.btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); color:#fff; }
.btn-outline-choco{ color:var(--choco); border-color:var(--choco); background:#fff; }
.btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }

/* Thumbs (preview images) */
.thumb-list{ display:flex; flex-wrap:wrap; margin:-.35rem; }
.thumb-item{ width:100px; margin:.35rem; text-align:center; }
.thumb-img{
  width:100px; height:100px; object-fit:cover;
  border-radius:12px; border:0; box-shadow:var(--shadow);
  transition: transform .15s ease, box-shadow .15s ease;
}
.thumb-item:hover .thumb-img{ transform: scale(1.03); box-shadow:0 10px 24px rgba(0,0,0,.12); }
.thumb-caption{
  font-size:.72rem; color:#6b7280; margin-top:.35rem;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}

</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Select2 (status)
  if (window.jQuery && $('.select2').length) {
    $('.select2').select2({ theme: 'bootstrap-5' });
  }

  // Preview & validasi gambar (maks 3, tipe & size)
  const input = document.getElementById('images');
  const previewWrap = document.getElementById('imagesPreview');
  const ALLOWED = ['image/jpeg','image/png','image/webp'];
  const MAX_SIZE = 2 * 1024 * 1024; // 2MB
  const MAX_FILES = 3;

  if (input && previewWrap) {
    input.addEventListener('change', () => {
      const files = Array.from(input.files || []);
      if (files.length > MAX_FILES) {
        alert(`Maksimal ${MAX_FILES} gambar.`);
        input.value = '';
        previewWrap.innerHTML = '';
        return;
      }
      for (const f of files) {
        if (!ALLOWED.includes(f.type)) { alert('Gunakan JPG, PNG, atau WEBP.'); input.value=''; previewWrap.innerHTML=''; return; }
        if (f.size > MAX_SIZE) { alert('Ukuran file melebihi 2 MB.'); input.value=''; previewWrap.innerHTML=''; return; }
      }
      previewWrap.innerHTML = '';
      files.forEach((file) => {
        const url = URL.createObjectURL(file);
        const a = document.createElement('div');
        a.className = 'thumb-item';
        a.innerHTML = `
          <img src="${url}" class="thumb-img" alt="${file.name}">
          <div class="thumb-caption">${file.name}</div>
        `;
        previewWrap.appendChild(a);
      });
    });
  }
});
</script>
@endpush

