@extends('layouts.owner')

@section('content')
<div class="container owner-category-edit mt-4"> {{-- PAGE SCOPE --}}
    <h1 class="page-title mb-3">Edit Category</h1>

    <form method="POST"
          action="{{ route('owner.user-owner.categories.update', $category) }}"
          enctype="multipart/form-data"
          class="form-card">
        @csrf
        @method('PUT')

        {{-- Category Name --}}
        <div class="form-group">
            <label for="category_name" class="form-label required">Category Name</label>
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
            <label for="description" class="form-label">Category Description</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
            @error('description') <div class="invalid-hint">{{ $message }}</div> @enderror
        </div>

        {{-- Image --}}
        <div class="form-group">
            <label for="images" class="form-label">Category Image</label>
            <input type="file" name="images" id="images" class="form-control" accept="image/*" onchange="previewImage(event)">
            @error('images') <div class="invalid-hint">{{ $message }}</div> @enderror

            <div class="mt-2">
                @if($category->images && isset($category->images['path']))
                    <img id="image-preview"
                         src="{{ asset($category->images['path']) }}"
                         alt="{{ $category->category_name }}"
                         class="thumb">
                @else
                    <img id="image-preview" src="#" alt="Preview" class="thumb d-none">
                @endif
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('owner.user-owner.categories.index') }}" class="btn btn-outline-choco">Back</a>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
  const input = event.target;
  const preview = document.getElementById('image-preview');
  if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
          preview.src = e.target.result;
          preview.classList.remove('d-none');
      };
      reader.readAsDataURL(input.files[0]);
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
</style>
@endsection
