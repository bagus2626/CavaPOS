@extends('layouts.partner')

@section('content')
<div class="container mt-4">
    <h1>Edit Category</h1>
    <form method="POST" action="{{ route('partner.categories.update', $category) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Category Name --}}
        <div class="form-group">
            <label for="category_name">Category Name</label>
            <input type="text" name="category_name" class="form-control"
                   value="{{ old('category_name', $category->category_name) }}" required>
            @error('category_name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Description --}}
        <div class="form-group">
            <label for="description">Category Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
            @error('description') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Image --}}
        <div class="form-group">
            <label for="images">Category Image</label>
            <input type="file" name="images" class="form-control" accept="image/*" onchange="previewImage(event)">
            @error('images') <div class="text-danger">{{ $message }}</div> @enderror

            <div class="mt-2">
                @if($category->images && isset($category->images['path']))
                    <img id="image-preview"
                         src="{{ asset($category->images['path']) }}"
                         alt="{{ $category->category_name }}"
                         class="img-thumbnail"
                         style="max-height: 200px;">
                @else
                    <img id="image-preview" src="#" alt="Preview" class="img-thumbnail d-none" style="max-height: 200px;">
                @endif
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-2">Update</button>
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
