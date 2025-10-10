@extends('layouts.partner')

@section('title', __('messages.partner.user_management.employees.employee_list'))
@section('page_title', __('messages.partner.user_management.employees.all_employees'))

@section('content')

<section class="content">
    <div class="container-fluid">
        {{-- <a href="{{ route('partner.user-management.employees.create') }}" class="btn btn-primary mb-3">Add Employee</a> --}}
        <div class="mb-3">
            <button class="btn btn-outline-choco btn-sm filter-btn rounded-pill active" data-category="all">All</button>
            @foreach($roles as $role)
                <button class="btn btn-outline-choco btn-sm filter-btn rounded-pill" data-category="{{ $role }}">
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
<style>
    /* Filter pill */
.filter-btn{
  border-width:1.5px;
  letter-spacing:.2px;
  transition: .15s ease;
}
.filter-btn.active{
  background: var(--choco);
  border-color: var(--choco);
  color:#fff;
  box-shadow: 0 6px 14px rgba(140,16,0,.18);
}

/* Saat non-aktif tetap nyambung ke palet brand */
.filter-btn:not(.active){
  color: var(--choco);
  border-color: var(--choco);
  background: transparent;}
.filter-btn:not(.active):hover{
  background: rgba(140,16,0,.08);
}

/* Tabel numbering & baris kosong */
tbody tr{ transition: .12s ease; }
tbody tr:hover{ background: rgba(193,40,20,.06); }
tbody tr.empty-row td{ color:#6b7280; }

/* Header section cardy */
.content > .container-fluid > .mb-3{ /* area tombol filter */
  padding: .35rem .35rem .1rem;
}

</style>
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
