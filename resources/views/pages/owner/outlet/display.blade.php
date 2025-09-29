<div class="table-responsive rounded-xl">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Nama Outlet</th>
                <th>username</th>
                <th>email</th>
                <th>Picture</th>
                <th>Status</th>
                <th>QRIS</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($outlets as $index => $outlet)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $outlet->name }}</td>
                    <td>{{ $outlet->username }}</td>
                    <td>{{ $outlet->email }}</td>
                    <td>
                        @php
                            // Jika $employee->image relatif (mis. "employees/xxx.webp"), buat URL publik via storage
                            $img = $outlet->logo
                            ? (Str::startsWith($outlet->logo, ['http://', 'https://'])
                                ? $outlet->logo
                                : asset('storage/'.$outlet->logo))
                            : null;
                        @endphp

                        @if($img)
                            <a href="{{ $img }}" target="_blank" rel="noopener">
                            <img
                                src="{{ $img }}"
                                alt="{{ $outlet->name }}"
                                width="56" height="56"
                                loading="lazy"
                                class="rounded"
                                style="object-fit:cover; width:56px; height:56px;"
                            >
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if((int) $outlet->is_active === 1)
                            <span class="badge bg-success d-inline-flex align-items-center gap-1">
                                <i class="fas fa-check-circle"></i> Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary d-inline-flex align-items-center gap-1">
                                <i class="fas fa-minus-circle"></i> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td>
                        @if((int) $outlet->is_qr_active === 1)
                            <span class="badge bg-success d-inline-flex align-items-center gap-1">
                                <i class="fas fa-check-circle"></i> Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary d-inline-flex align-items-center gap-1">
                                <i class="fas fa-minus-circle"></i> Nonaktif
                            </span>
                        @endif
                    </td>

                    <td>
                        {{-- <a href="{{ route('partner.user-management.employees.show', $outlet->id) }}" class="btn btn-sm btn-info">Detail</a> --}}
                        <a href="{{ route('owner.user-owner.outlets.edit', $outlet->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button onclick="deleteOutlet({{ $outlet->id }})" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


@push('scripts')
<script>
function deleteOutlet(employeeId) {
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
            form.action = `/owner/user-owner/outlets/${employeeId}`;
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
