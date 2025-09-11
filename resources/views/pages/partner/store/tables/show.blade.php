@extends('layouts.partner')

@section('content')
<div class="container">
    <a href="{{ route('partner.store.tables.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left mr-2"></i>Back to Tables
    </a>
    <div class="card rounded-xl shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Detail Table #{{ $data->table_no }}</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Table No</div>
                <div class="col-md-8">{{ $data->table_no }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Class / Type</div>
                <div class="col-md-8">{{ $data->table_class }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Description</div>
                <div class="col-md-8">{{ $data->description ?? '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Status</div>
                <div class="col-md-8">
                    @if($data->status == 'available')
                        <span class="badge bg-success">Available</span>
                    @elseif($data->status == 'occupied')
                        <span class="badge bg-danger">Occupied</span>
                    @elseif($data->status == 'reserved')
                        <span class="badge bg-warning text-dark">Reserved</span>
                    @else
                        <span class="badge bg-secondary">{{ $data->status }}</span>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Pictures</div>
                <div class="col-md-8">
                    @if(!empty($data->images) && is_array($data->images))
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($data->images as $image)
                                <a href="{{ asset($image['path']) }}" target="_blank">
                                    <img src="{{ asset($image['path']) }}"
                                        alt="{{ $image['filename'] ?? 'Table Image' }}"
                                        class="img-thumbnail"
                                        style="width: 120px; height: 120px; object-fit: cover;">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">No Images</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('partner.store.tables.edit', $data->id) }}" class="btn btn-warning">Edit</a>
            <button onclick="deleteTable({{ $data->id }})" class="btn btn-danger">Delete</button>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
function deleteTable(productId) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda tidak dapat mengembalikan data tersebut!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batalkan'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/partner/store/tables/${productId}`;
            form.style.display = 'none';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
