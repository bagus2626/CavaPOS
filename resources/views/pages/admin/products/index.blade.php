@extends('layouts.admin')

@section('title', 'Product List')
@section('page_title', 'All Products')

@section('content')
<section class="content">
    <div class="container-fluid">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Add Product</a>

        <div id="productsList">
            @foreach ($products as $product)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="product-info" style="flex: 1;">
                                <h5 class="mb-1">{{ $product->name }}</h5>
                                <p class="mb-1 text-truncate" style="max-width: 600px;">{{ $product->description }}</p>
                                <p class="mb-1 text-truncate" style="max-width: 600px;">slug: /{{ $product->slug }}</p>
                            </div>

                            <div class="d-flex">
                                <button class="btn btn-sm btn-outline-primary mr-2 toggle-details"
                                        data-target="#details-{{ $product->id }}"
                                        aria-label="Toggle details">
                                    <i class="fas fa-chevron-down"></i>
                                </button>

                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle"
                                            type="button"
                                            id="dropdownMenuButton{{ $product->id }}"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton{{ $product->id }}">
                                        <a class="dropdown-item" href="{{ route('admin.products.show', $product->id) }}">Detail</a>
                                        <a class="dropdown-item" href="{{ route('admin.products.edit', $product->id) }}">Edit</a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="details-{{ $product->id }}" class="product-details mt-3" style="display: none;">
                            @if($product->packages->isEmpty())
                                <p><em>No packages available for this product.</em></p>
                            @else
                                <ul class="list-unstyled mb-0" style="max-width: 600px; overflow-x:auto;">
                                    @foreach ($product->packages as $pkg)
                                        <li class="mb-1">
                                            <strong>{{ $pkg->name }}</strong> —
                                            Rp{{ number_format($pkg->monthly_price, 0, ',', '.') }} / month,
                                            Rp{{ number_format($pkg->annual_price, 0, ',', '.') }} / year

                                            @if($pkg->specificationValues->isNotEmpty())
                                                <ul class="mb-0 pl-3">
                                                    @foreach ($pkg->specificationValues as $val)
                                                        <li>{{ $val->specification->name }}: {{ $val->value }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-details').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const detailsSection = document.querySelector(targetId);
            const icon = this.querySelector('i');

            if (detailsSection.style.display === 'none') {
                detailsSection.style.display = 'block';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                detailsSection.style.display = 'none';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        });
    });
});
</script>
