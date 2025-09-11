@extends('layouts.partner')

@section('title', 'Employee List')
@section('page_title', 'All Employees')

@section('content')

<section class="content">
    <div class="container-fluid">
        <a href="{{ route('partner.user-management.employees.create') }}" class="btn btn-primary mb-3">Add Employee</a>
        <div class="mb-3">
            <button class="btn btn-outline-secondary btn-sm filter-btn rounded-pill active" data-category="all">All</button>
            @foreach($roles as $role)
                <button class="btn btn-outline-secondary btn-sm filter-btn rounded-pill" data-category="{{ $role }}">
                    {{ $role }}
                </button>

            @endforeach
        </div>


        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @include('pages.partner.human-resource.employee.display')
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Tombol diklik:', this.textContent);
            const categoryId = this.getAttribute('data-category');

            // hapus class active dari semua tombol
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const tableBody = document.querySelector('tbody');
            const tableRows = document.querySelectorAll('tbody tr');

            let visibleCount = 0; // hitung row yang tampil

            tableRows.forEach((row, index) => {
                if(categoryId === 'all' || row.getAttribute('data-category') === categoryId) {
                    row.style.display = '';
                    visibleCount++;
                    row.querySelector('td').textContent = visibleCount; // update nomor urut di kolom pertama
                } else {
                    row.style.display = 'none';
                }
            });

            // hapus row "data tidak ditemukan" dulu kalau ada
            const emptyRow = tableBody.querySelector('.empty-row');
            if(emptyRow) emptyRow.remove();

            // jika tidak ada row yang tampil, tampilkan pesan
            if(visibleCount === 0) {
                const tr = document.createElement('tr');
                tr.classList.add('empty-row');
                tr.innerHTML = `<td colspan="5" class="text-center">Data tidak ditemukan</td>`;
                tableBody.appendChild(tr);
            }
        });
    });
});
</script>
@endpush
