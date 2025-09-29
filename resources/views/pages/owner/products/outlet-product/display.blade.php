<div class="table-responsive rounded-xl">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Nama Produk</th>
                <th>Deskripsi</th>
                <th>Pilihan</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Gambar</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $index => $product)
                <tr data-category="{{ $product->category_id }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>
                        @if($product->parent_options->isEmpty())
                            <em>No packages</em>
                        @else
                            {{ $product->parent_options->pluck('name')->implode(', ') }}
                        @endif
                    </td>
                    <td>{{ $product->quantity}}</td>
                    <td>Rp. {{ number_format($product->price)}}</td>
                    <td>
                        @if(!empty($product->pictures) && is_array($product->pictures))
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($product->pictures as $picture)
                                    <a href="{{ asset($picture['path']) }}" target="_blank">
                                        <img src="{{ asset($picture['path']) }}"
                                            alt="{{ $picture['filename'] ?? 'Product Image' }}"
                                            class="img-thumbnail"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">No Images</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('owner.user-owner.outlet-products.show', $product->id) }}" class="btn btn-sm btn-info">Detail</a>
                        <a href="{{ route('owner.user-owner.outlet-products.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button onclick="deleteProduct({{ $product->id }})" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
