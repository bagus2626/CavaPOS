@extends('layouts.partner')

@section('content')
<div class="container mt-4">
    <h1>Add Category</h1>
    <form method="POST" action="{{ route('partner.categories.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="category_name">Category Name</label>
            <input type="text" name="category_name" class="form-control" required>
            @error('category_name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description">Category Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
            @error('description') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="images">Category Image</label>
            <input type="file" name="images" class="form-control" accept="image/*" onchange="previewImage(event)">
            @error('images') <div class="text-danger">{{ $message }}</div> @enderror

            <div class="mt-2">
                <img id="image-preview" src="#" alt="Preview" class="img-thumbnail d-none" style="max-height: 200px;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-2">Save</button>
        <a href="{{ route('partner.categories.index') }}" class="btn btn-secondary mt-2">Back</a>
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
@endsection
