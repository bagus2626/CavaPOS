@extends('layouts.owner')

@section('title', __('messages.owner.products.categories.edit_category'))
@section('page_title', __('messages.owner.products.categories.edit_category'))

@section('content')
<section class="content">
<div class="container-fluid owner-category-edit"> {{-- PAGE SCOPE --}}

    <form method="POST"
          action="{{ route('owner.user-owner.categories.update', $category) }}"
          enctype="multipart/form-data"
          class="form-card">
        @csrf
        @method('PUT')

        {{-- Category Name --}}
        <div class="form-group">
            <label for="category_name" class="form-label required">{{ __('messages.owner.products.categories.category_name') }}</label>
            <input type="text"
                   name="category_name"
                   id="category_name"
                   class="form-control"
                   value="{{ old('category_name', $category->category_name) }}"
                   required>
            @error('category_name') <div class="invalid-hint">{{ $message }}</div> @enderror
        </div>

        {{-- Description --}}
        <div class="form-group">
            <label for="description" class="form-label">{{ __('messages.owner.products.categories.description') }}</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
            @error('description') <div class="invalid-hint">{{ $message }}</div> @enderror
        </div>

        {{-- Existing Image --}}
        @if($category->images && isset($category->images['path']))
        <div class="form-group">
            <div id="existing-image-container">
                <div class="position-relative d-inline-block">
                    <img id="existing-image-preview"
                        src="{{ asset($category->images['path']) }}"
                        alt="{{ $category->category_name }}"
                        class="thumb">
                    <button type="button" 
                            class="btn btn-danger btn-sm position-absolute"
                            style="top: 5px; right: 5px;"
                            onclick="removeExistingImage()"
                            title="Hapus gambar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <input type="hidden" name="keep_existing_image" id="keep_existing_image" value="1">
            </div>
        </div>
        @endif

        {{-- Image --}}
        <div class="form-group">
            <input type="file" name="images" id="images" class="form-control" accept="image/*" onchange="previewNewImage(event)">
            @error('images') <div class="invalid-hint">{{ $message }}</div> @enderror

            <div class="mt-2">
                <img id="new-image-preview" src="#" alt="Preview" class="thumb d-none">
            </div>
        </div>

        <div class="card-footer text-right">
            <a href="{{ route('owner.user-owner.categories.index') }}" class="btn btn-outline-choco mr-2">
              {{ __('messages.owner.products.categories.back') }}
            </a>
            <button type="submit" class="btn btn-primary mr-1">
              {{ __('messages.owner.products.categories.update') }}
            </button>
        </div>
    </form>
</div>
</section>

<script>
function previewNewImage(event) {
    const input = event.target;
    const preview = document.getElementById('new-image-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '#';
        preview.classList.add('d-none');
    }
}

// Fungsi untuk menghapus gambar existing
function removeExistingImage() {
    
    // Sembunyikan container existing image
    const container = document.getElementById('existing-image-container');
    if (container) {
        container.style.display = 'none';
    }
    
    // Set hidden input untuk menandai bahwa gambar akan dihapus
    const keepInput = document.getElementById('keep_existing_image');
    if (keepInput) {
        keepInput.value = '0';
    }
    
    // Reset file input jika ada
    const fileInput = document.getElementById('images');
    if (fileInput) {
        fileInput.value = '';
    }
    
    // Sembunyikan preview gambar baru jika ada
    const newPreview = document.getElementById('new-image-preview');
    if (newPreview) {
        newPreview.src = '#';
        newPreview.classList.add('d-none');
    }
}
</script>

<style>
/* ===== Owner › Category Edit (page scope) ===== */
.owner-category-edit{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* Title */
.owner-category-edit .page-title{
  color:var(--ink); font-weight:500;
}

/* Card-ish form */
.owner-category-edit .form-card{
  background:#fff; border:0; border-radius:var(--radius);
  box-shadow:var(--shadow); padding:1.25rem 1.25rem 1rem;
}

/* Labels & required mark */
.owner-category-edit .form-label{ font-weight:600; color:#374151; }
.owner-category-edit .required::after{ content:" *"; color:#dc3545; }

/* Inputs focus brand */
.owner-category-edit .form-control:focus{
  border-color:var(--choco);
  box-shadow:0 0 0 .2rem rgba(140,16,0,.15);
}

/* Validation hint */
.owner-category-edit .invalid-hint{ color:#b91c1c; margin-top:.25rem; }

/* Buttons */
.owner-category-edit .btn-primary{
  background:var(--choco); border-color:var(--choco);
}
.owner-category-edit .btn-primary:hover{
  background:var(--soft-choco); border-color:var(--soft-choco);
}
.owner-category-edit .btn-outline-choco{
  color:var(--choco); border:1px solid var(--choco); background:#fff;
}
.owner-category-edit .btn-outline-choco:hover{
  color:#fff; background:var(--choco); border-color:var(--choco);
}

/* Preview thumb */
.owner-category-edit .thumb{
  width:200px; height:auto; max-height:200px; object-fit:cover;
  border-radius:12px; border:0; box-shadow:var(--shadow);
}

/* Small gaps utility */
.owner-category-edit .gap-2{ gap:.5rem; }

/* Position relative untuk button delete */
.owner-category-edit .position-relative {
    position: relative;
}

.owner-category-edit .position-absolute {
    position: absolute;
}

.owner-category-edit .d-inline-block {
    display: inline-block;
}
</style>
@endsection
