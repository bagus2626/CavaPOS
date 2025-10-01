@extends('layouts.partner')

@section('title', 'Edit Table')
@section('page_title', 'Edit Table')

@section('content')
<div class="container">
  <a href="{{ route('partner.store.tables.index') }}" class="btn btn-outline-choco mb-3">
    <i class="fas fa-arrow-left mr-2"></i>Back to Tables
  </a>

  <div class="card shadow-sm rounded-xl">
    <div class="card-header">
      <h5 class="card-title mb-0">Edit Table</h5>
    </div>

    <div class="card-body">
      {{-- errors --}}
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

      <form action="{{ route('partner.store.tables.update', $table->id) }}" method="POST" enctype="multipart/form-data" id="tableEditForm">
        @csrf
        @method('PUT')

        <div class="row">
          {{-- Table No --}}
          <div class="col-md-6 mb-3">
            <label for="table_no" class="form-label">Table No</label>
            <input type="text" name="table_no" id="table_no"
                   class="form-control @error('table_no') is-invalid @enderror"
                   value="{{ old('table_no', $table->table_no) }}" required>
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
              <option value="available" {{ old('status', $table->status) == 'available' ? 'selected' : '' }}>Available</option>
              <option value="occupied"  {{ old('status', $table->status) == 'occupied'  ? 'selected' : '' }}>Occupied</option>
              <option value="reserved"  {{ old('status', $table->status) == 'reserved'  ? 'selected' : '' }}>Reserved</option>
            </select>
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          {{-- Table Class (datalist, bebas ketik) --}}
          <div class="col-md-6 mb-3">
            <label for="table_class" class="form-label">Table Class</label>
            <input list="table_class_list"
                   name="table_class"
                   id="table_class"
                   class="form-control @error('table_class') is-invalid @enderror"
                   value="{{ old('table_class', $table->table_class) }}"
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
            <small class="text-muted d-block">Upload baru akan <b>menimpa</b> gambar lama (maks 3 file, 2MB per file).</small>
            @error('images')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error('images.*')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            {{-- Preview gambar baru --}}
            <div id="imagesPreview" class="thumb-list mt-2"></div>

            {{-- Gambar lama + checkbox hapus --}}
            @if($table->images && count($table->images) > 0)
              <div class="mt-3">
                <label class="form-label">Current Images</label>
                <div class="thumb-list">
                  @foreach($table->images as $index => $img)
                    @php $src = asset($img['path']); @endphp
                    <div class="thumb-item">
                      <img src="{{ $src }}" alt="Table Image" class="thumb-img-sm">
                      <div class="form-check mt-2 delete-check">
                        <input class="form-check-input" type="checkbox"
                               name="delete_images[]"
                               value="{{ $img['filename'] }}"
                               id="delete_image_{{ $index }}">
                        <label class="form-check-label" for="delete_image_{{ $index }}">
                          Hapus gambar ini
                        </label>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif
          </div>
        </div>

        {{-- Description --}}
        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <textarea name="description" id="description" rows="3"
                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $table->description) }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end">
          <a href="{{ route('partner.store.tables.index') }}" class="btn btn-outline-choco me-2">Cancel</a>
          <button type="submit" class="btn btn-choco">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
    /* ==== Tables Edit (scoped) ==== */
:root{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* rounded-xl fallback */
.rounded-xl{ border-radius: 1rem; }

/* Card & header */
.card.shadow-sm{ border:0; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
.card-header{ background:#fff; border-bottom:1px solid #eef1f4; }
.card-title{ color:var(--ink); font-weight:700; letter-spacing:.2px; }

/* Labels & inputs */
.form-label{ font-weight:600; color:#374151; }
.form-control:focus, select.form-control:focus{
  border-color: var(--choco);
  box-shadow: 0 0 0 .2rem rgba(140,16,0,.15);
}

/* Select2 selaras tema */
.select2-container--bootstrap-5 .select2-selection{
  border-radius:10px; border-color:#e5e7eb;
}
.select2-container--bootstrap-5 .select2-results__option--highlighted{
  background: var(--soft-choco);
}

/* Alerts */
.alert{ border-left:4px solid var(--choco); border-radius:10px; }
.alert-danger{ background:#fff5f5; border-color:#fde2e2; color:#991b1b; }

/* Buttons brand (fallback jika belum ada global) */
.btn-choco{ background:var(--choco); border-color:var(--choco); color:#fff; }
.btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); color:#fff; }
.btn-outline-choco{ color:var(--choco); border-color:var(--choco); }
.btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }

/* Thumbnails (baru & lama) */
.thumb-list{ display:flex; flex-wrap:wrap; margin:-.4rem; }
.thumb-item{ margin:.4rem; text-align:center; background:#fff; border-radius:12px; padding:.4rem; transition:.15s ease; }
.thumb-img{
  width:100px; height:100px; object-fit:cover;
  border-radius:10px; border:0; box-shadow:var(--shadow);
}
.thumb-img-sm{
  width:120px; height:120px; object-fit:cover;
  border-radius:10px; border:0; box-shadow:var(--shadow);
}
.thumb-caption{
  font-size:.72rem; color:#6b7280; margin-top:.35rem;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;
}
.thumb-item:hover{ transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,.08); }

/* Checkbox hapus: beri aksen saat dicentang */
.delete-check .form-check-input:checked{
  background-color: var(--choco); border-color: var(--choco);
}
.thumb-item.marked-delete{
  outline: 2px dashed var(--soft-choco);
  outline-offset: 3px;
  background: #fff0ee;
}

</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Select2 untuk status (jika sudah di-load di layout)
  if (window.jQuery && $('.select2').length) {
    $('.select2').select2({ theme: 'bootstrap-5' });
  }

  // Preview & validasi gambar baru
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
        input.value = ''; previewWrap.innerHTML = ''; return;
      }
      for (const f of files) {
        if (!ALLOWED.includes(f.type)) { alert('Gunakan JPG, PNG, atau WEBP.'); input.value=''; previewWrap.innerHTML=''; return; }
        if (f.size > MAX_SIZE) { alert('Ukuran file melebihi 2 MB.'); input.value=''; previewWrap.innerHTML=''; return; }
      }
      previewWrap.innerHTML = '';
      files.forEach((file) => {
        const url = URL.createObjectURL(file);
        const el = document.createElement('div');
        el.className = 'thumb-item';
        el.innerHTML = `
          <img src="${url}" class="thumb-img" alt="${file.name}">
          <div class="thumb-caption">${file.name}</div>
        `;
        previewWrap.appendChild(el);
      });
    });
  }

  // Highlight kartu gambar lama saat checkbox di-klik
  document.querySelectorAll('.delete-check input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', function(){
      const card = this.closest('.thumb-item');
      if (!card) return;
      card.classList.toggle('marked-delete', this.checked);
    });
  });
});
</script>
@endpush
