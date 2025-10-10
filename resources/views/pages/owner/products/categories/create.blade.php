@extends('layouts.owner')

@section('content')
<div class="container owner-category-create mt-4"> {{-- PAGE SCOPE --}}
    <h1 class="page-title mb-3">{{ __('messages.owner.products.categories.add_category') }}</h1>

    <form method="POST"
          action="{{ route('owner.user-owner.categories.store') }}"
          enctype="multipart/form-data"
          class="form-card">
        @csrf

        <div class="form-group">
            <label for="category_name" class="form-label required">{{ __('messages.owner.products.categories.category_name') }}</label>
            <input type="text" name="category_name" id="category_name" class="form-control" required>
            @error('category_name') <div class="invalid-hint">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">{{ __('messages.owner.products.categories.description') }}</label>
            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
            @error('description') <div class="invalid-hint">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="images" class="form-label">{{ __('messages.owner.products.categories.picture') }}</label>
            <input type="file" name="images" id="images" class="form-control" accept="image/*" onchange="previewImage(event)">
            @error('images') <div class="invalid-hint">{{ $message }}</div> @enderror

            <div class="mt-2">
                <img id="image-preview" src="#" alt="Preview" class="thumb d-none">
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-primary">
                {{ __('messages.owner.products.categories.save') }}
            </button>
            <a href="{{ route('owner.user-owner.categories.index') }}" class="btn btn-outline-choco">
                {{ __('messages.owner.products.categories.back') }}
            </a>
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
/* ===== Owner › Category Create (page scope) ===== */
.owner-category-create{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* Title */
.owner-category-create .page-title{
  color:var(--ink); font-weight:500;
}

/* Card-ish form */
.owner-category-create .form-card{
  background:#fff; border:0; border-radius:var(--radius);
  box-shadow:var(--shadow); padding:1.25rem 1.25rem 1rem;
}

/* Labels & required mark */
.owner-category-create .form-label{ font-weight:600; color:#374151; }
.owner-category-create .required::after{ content:" *"; color:#dc3545; }

/* Inputs focus brand */
.owner-category-create .form-control:focus{
  border-color:var(--choco);
  box-shadow:0 0 0 .2rem rgba(140,16,0,.15);
}

/* Validation hint */
.owner-category-create .invalid-hint{ color:#b91c1c; margin-top:.25rem; }

/* Buttons */
.owner-category-create .btn-primary{
  background:var(--choco); border-color:var(--choco);
}
.owner-category-create .btn-primary:hover{
  background:var(--soft-choco); border-color:var(--soft-choco);
}
.owner-category-create .btn-outline-choco{
  color:var(--choco); border:1px solid var(--choco); background:#fff;
}
.owner-category-create .btn-outline-choco:hover{
  color:#fff; background:var(--choco); border-color:var(--choco);
}

/* Preview thumb */
.owner-category-create .thumb{
  width:200px; height:auto; max-height:200px; object-fit:cover;
  border-radius:12px; border:0; box-shadow:var(--shadow);
}

/* Small gaps utility */
.owner-category-create .gap-2{ gap:.5rem; }
</style>
@endsection
