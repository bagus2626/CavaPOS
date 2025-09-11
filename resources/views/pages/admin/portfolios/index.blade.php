@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Portfolios</h1>

    <button
        onclick="window.location.href='{{ route('admin.portfolios.create') }}'"
        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block transition duration-200"
    >
        + Add New
    </button>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">#</th>
                    <th class="p-2">Image</th>
                    <th class="p-2">Title</th>
                    <th class="p-2">Category</th>
                    <th class="p-2">Link</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($portfolios as $item)
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-2">{{ $loop->iteration }}</td>
                    <td class="p-2">
                        @if($item->hasMedia('images'))
                            <img src="{{ $item->getFirstMediaUrl('images') }}"
                                 alt="{{ $item->title }}"
                                 class="w-16 h-16 object-cover rounded">
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-gray-500">
                                No Image
                            </div>
                        @endif
                    </td>
                    <td class="p-2">{{ $item->title }}</td>
                    <td class="p-2">{{ $item->category }}</td>
                    <td class="p-2">
                        @if($item->link)
                            <a href="{{ $item->link }}" target="_blank" class="text-blue-500 hover:underline">View</a>
                        @else
                            <span class="text-gray-400">No Link</span>
                        @endif
                    </td>
                    <td class="p-2 text-right space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.portfolios.edit', $item) }}"
                           class="text-yellow-500 hover:text-yellow-600">Edit</a>
                        <form action="{{ route('admin.portfolios.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $portfolios->links() }}
    </div>
</div>
@endsection
