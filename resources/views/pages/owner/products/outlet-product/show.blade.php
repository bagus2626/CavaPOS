@extends('layouts.owner')

@section('title', 'Product Detail')
@section('page_title', 'Product Detail')

@section('content')
<section class="content">
    <div class="container-fluid">

        {{-- Tombol kembali --}}
        <a href="{{ route('owner.user-owner.products.outlet-product.index') }}" class="btn btn-outline-secondary mb-4">
            ← Back to Products
        </a>

        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-header bg-secondary text-white rounded-top-4">
                <h3 class="card-title fw-bold mb-0">{{$data->name}}</h3>
            </div>
            <div class="card-body">

                {{-- Gambar produk --}}
                @if($data->pictures)
                    @php
                        $pictures = $data->pictures;
                    @endphp
                    <div class="mb-4 d-flex flex-wrap gap-3 justify-content-start">
                        @foreach($pictures as $pic)
                            <div class="product-img-wrapper" style="max-width: 220px;">
                                <img src="{{ asset($pic['path']) }}"
                                     alt="{{ $pic['filename'] }}"
                                     class="img-fluid rounded shadow-sm border"
                                     style="object-fit: cover; width: 100%; height: 150px;">
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Deskripsi --}}
                <p class="fs-5"><strong>Description:</strong> {{$data->description}}</p>

                {{-- Parent Options --}}
                @foreach($data->parent_options as $parentOption)
                    <div class="card mt-4 shadow-sm rounded-4 border-0">
                        <div class="card-header bg-secondary text-white rounded-top-4">
                            <h4 class="card-title mb-0 fw-semibold">{{ $parentOption->name }}</h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">{{ $parentOption->description }}</p>

                            @if($parentOption->options->isNotEmpty())
                                <div class="table-responsive rounded-3 shadow-sm border">
                                    <table class="table table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Options</th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($parentOption->options as $option)
                                                <tr>
                                                    <td>{{ $option->name }}</td>
                                                    <td>{{ $option->quantity }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No Option found for this package.</p>
                            @endif
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    /* Hover effect untuk card parent options */
    .card:hover {
        transform: translateY(-3px);
        transition: all 0.2s ease-in-out;
    }

    /* Gaya tombol back */
    .btn-outline-secondary {
        font-weight: 500;
        border-radius: 50px;
        padding: 0.5rem 1.2rem;
    }

    /* Styling gambar produk */
    .product-img-wrapper img {
        transition: transform 0.3s ease;
    }

    .product-img-wrapper img:hover {
        transform: scale(1.05);
    }

    /* Typography */
    p, table th, table td {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
</style>
@endpush
