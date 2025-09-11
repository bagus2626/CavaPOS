@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Create New Portfolio</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 mb-4 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.portfolios.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="title" class="block font-semibold">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}"
                   class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-200">
        </div>

        <div>
            <label for="description" class="block font-semibold">Description</label>
            <textarea name="description" id="description" rows="4"
                      class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-200">{{ old('description') }}</textarea>
        </div>

        <div class="mb-4">
            <label for="image" class="block font-semibold">Upload Image</label>
            <input type="file" name="image" id="image"
                class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-200">

            <!-- Tempat Preview -->
            <div class="mt-2">
                <img id="imagePreview" class="w-32 h-32 object-cover rounded hidden border border-gray-300" />
            </div>
        </div>

        <div>
            <label for="link" class="block font-semibold">Link</label>
            <input type="url" name="link" id="link" value="{{ old('link') }}"
                   class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-200">
        </div>

        <div>
            <label for="category" class="block font-semibold">Category</label>
            <input list="category-options" name="category" id="category"
                value="{{ old('category') }}"
                class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-200">
            <datalist id="category-options">
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}">
                @endforeach
            </datalist>
        </div>


        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('admin.portfolios.index') }}" class="text-gray-600 hover:underline">← Back to List</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Save
            </button>
        </div>
    </form>
</div>
<script>
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
</script>
@endsection

