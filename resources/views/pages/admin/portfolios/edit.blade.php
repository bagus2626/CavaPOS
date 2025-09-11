@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Portfolio</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 mb-4 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.portfolios.update', $portfolio) }}" method="POST" class="space-y-4" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block font-semibold">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $portfolio->title) }}"
                   class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-200">
        </div>

        <div>
            <label for="description" class="block font-semibold">Description</label>
            <textarea name="description" id="description" rows="4"
                      class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-200">{{ old('description', $portfolio->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label for="image" class="block font-semibold">Update Image</label>
            <input type="file" name="image" id="image"
                class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-200">

            <!-- Current Image Preview -->
            <div class="mt-4">
                <label class="block font-semibold mb-2">Current Image</label>
                @if($portfolio->hasMedia('images'))
                    <div class="flex items-center space-x-4">
                        <img src="{{ $portfolio->getFirstMediaUrl('images') }}"
                             alt="Current Portfolio Image"
                             class="w-32 h-32 object-cover rounded border border-gray-300">
                    </div>
                @else
                    <div class="w-32 h-32 bg-gray-200 rounded flex items-center justify-center text-gray-500">
                        No Image Uploaded
                    </div>
                @endif
            </div>

            <!-- New Image Preview -->
            <div class="mt-2">
                <label class="block font-semibold mb-2">New Image Preview</label>
                <img id="imagePreview" class="w-32 h-32 object-cover rounded hidden border border-gray-300" />
            </div>
        </div>

        <div>
            <label for="link" class="block font-semibold">Link</label>
            <input type="url" name="link" id="link" value="{{ old('link', $portfolio->link) }}"
                   class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-200">
        </div>

        <div>
            <label for="category" class="block font-semibold">Category</label>
            <input list="category-options" name="category" id="category"
                value="{{ old('category', $portfolio->category) }}"
                class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-200">
            <datalist id="category-options">
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}">
                @endforeach
            </datalist>
        </div>

        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('admin.portfolios.index') }}" class="text-gray-600 hover:underline">← Back to List</a>
            <div class="space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Update
                </button>
                <a href="{{ route('admin.portfolios.show', $portfolio) }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded hover:bg-gray-300">
                    Preview
                </a>
            </div>
        </div>
    </form>
</div>

<script>
    // Image preview functionality
    document.getElementById('image').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }

            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.classList.add('hidden');
        }
    });

    // Remove image checkbox functionality
    document.getElementById('remove_image')?.addEventListener('change', function(e) {
        const imageInput = document.getElementById('image');
        if (this.checked) {
            imageInput.disabled = true;
        } else {
            imageInput.disabled = false;
        }
    });
</script>
@endsection
