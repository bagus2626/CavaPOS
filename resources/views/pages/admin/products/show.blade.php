@extends('layouts.admin')

@section('title', 'Product Detail')

@section('page_title', 'Product Detail')

@section('content')
<section class="content">
    <div class="container-fluid">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mb-3">← Back to Products</a>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">{{ $product->name }}</h3>
            </div>
            <div class="card-body">
                <p><strong>Description:</strong> {{ $product->description }}</p>

                @foreach($product->packages as $package)
                    <div class="card mt-4">
                        <div class="card-header bg-info text-white">
                            <h4 class="card-title mb-0">
                                {{ $package->name }} - (Monthly Rp {{ number_format($package->monthly_price, 0, ',', '.') }}) - (Annually Rp {{ number_format($package->annual_price, 0, ',', '.') }})
                            </h4>
                        </div>
                        <div class="card-body">
                            <p>{{ $package->description }}</p>

                            @if($package->specificationValues->isNotEmpty())
                                <table class="table table-bordered table-striped mt-3">
                                    <thead>
                                        <tr>
                                            <th>Specification</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($package->specificationValues as $specValue)
                                            <tr>
                                                <td>{{ $specValue->specification->name }}</td>
                                                <td>{{ $specValue->value }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">No specifications found for this package.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
