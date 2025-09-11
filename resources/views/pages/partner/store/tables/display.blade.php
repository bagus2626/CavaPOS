<div class="table-responsive rounded-xl">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Table No</th>
                <th>Class/Type</th>
                <th>Description</th>
                <th>Status</th>
                <th>Picture</th>
                <th>Barcode</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tables as $index => $table)
                <tr data-category="{{ $table->table_class }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $table->table_no }}</td>
                    <td>{{ $table->table_class }}</td>
                    <td>{{ $table->description }}</td>
                    <td>{{ $table->status }}</td>
                    <td>
                        @if(!empty($table->images) && is_array($table->images))
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($table->images as $image)
                                    <a href="{{ asset($image['path']) }}" target="_blank">
                                        <img src="{{ asset($image['path']) }}"
                                            alt="{{ $image['filename'] ?? 'Table Image' }}"
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
                        <button onclick="generateBarcode({{ $table->id }})" class="btn btn-sm btn-primary">Table Barcode</button>
                    </td>

                    <td>
                        <a href="{{ route('partner.store.tables.show', $table->id) }}" class="btn btn-sm btn-info">Detail</a>
                        <a href="{{ route('partner.store.tables.edit', $table->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button onclick="deleteTable({{ $table->id }})" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


@push('scripts')
<script>
function deleteTable(tableId) {
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
            form.action = `/partner/store/tables/${tableId}`;
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
<script>
function generateBarcode(tableId) {
    axios.get(`/partner/store/tables/generate-barcode/${tableId}`, {
        responseType: 'blob' // supaya bisa download image barcode
    })
    .then(response => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `barcode-table-${tableId}.png`);
        document.body.appendChild(link);
        link.click();
    })
    .catch(error => {
        console.error('Gagal generate barcode:', error);
    });
}

</script>
@endpush
