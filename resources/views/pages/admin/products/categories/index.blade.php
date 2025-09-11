@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h1>Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">+ Add Category</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th class="w-[5%]">#</th>
                <th class="w-[20%]">Name</th>
                <th class="w-[35%]">Description</th>
                <th class="w-[20%]">Picture</th>
                <th class="w-[10%]">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                    <td>{{ $category->category_name }}</td>
                    <td>{{ $category->description }}</td>
                    {{-- <td><pre>{{ var_dump($category->images) }}</pre></td> --}}

                    <td>
                        @if($category->images && isset($category->images['path']))
                            <a href="#" data-toggle="modal" data-target="#imageModal{{ $category->id }}">
                                <img src="{{ asset($category->images['path']) }}"
                                    alt="{{ $category->category_name }}"
                                    width="120" class="img-thumbnail" style="cursor: zoom-in;">
                            </a>
                            @include('pages.admin.products.categories.modal')
                        @else
                            <span class="text-muted">Belum ada gambar</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this category?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $categories->links() }}
    </div>
</div>
@endsection
