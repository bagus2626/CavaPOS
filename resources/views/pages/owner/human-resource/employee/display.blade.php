<div class="table-responsive rounded-xl">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Outlet</th>
                <th>Nama Pegawai</th>
                <th>username</th>
                <th>email</th>
                <th>role</th>
                <th>Picture</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $index => $employee)
                <tr data-category="{{ $employee->partner_id }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $employee->partner->name }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->user_name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->role }}</td>
                    <td>
                        @php
                            // Jika $employee->image relatif (mis. "employees/xxx.webp"), buat URL publik via storage
                            $img = $employee->image
                            ? (Str::startsWith($employee->image, ['http://', 'https://'])
                                ? $employee->image
                                : asset('storage/'.$employee->image))
                            : null;
                        @endphp

                        @if($img)
                            <a href="{{ $img }}" target="_blank" rel="noopener">
                            <img
                                src="{{ $img }}"
                                alt="{{ $employee->name }}"
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
                        @if((int) $employee->is_active === 1)
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
                        <a href="{{ route('owner.user-owner.employees.show', $employee->id) }}" class="btn btn-sm btn-info">Detail</a>
                        <a href="{{ route('owner.user-owner.employees.edit', $employee->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button onclick="deleteEmployee({{ $employee->id }})" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


@push('scripts')
<script>
function deleteEmployee(employeeId) {
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
            form.action = `/owner/user-owner/employees/${employeeId}`;
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
