@extends('layouts.partner')

@section('title', __('messages.partner.outlet.table_management.tables.table_list'))
@section('page_title', __('messages.partner.outlet.table_management.tables.table_list'))

@section('content')
@vite(['resources/css/app.css'])

<section class="content">
  <div class="container-fluid tables-index">
    <a href="{{ route('partner.store.tables.create') }}" class="btn btn-choco mb-3">
      <i class="fas fa-plus mr-1"></i> {{ __('messages.partner.outlet.table_management.tables.add_table') }}
    </a>

    @php
      $currentClass = request('table_class'); // dari query ?table_class=...
    @endphp

    <div class="mb-3">
      {{-- ALL --}}
      <a href="{{ route('partner.store.tables.index') }}"
        class="btn btn-outline-choco btn-sm rounded-pill filter-btn {{ $currentClass ? '' : 'active' }}">
          {{ __('messages.partner.outlet.table_management.tables.all') }}
      </a>

      {{-- PER CLASS --}}
      @foreach($table_classes as $table_class)
        <a href="{{ route('partner.store.tables.index', ['table_class' => $table_class]) }}"
          class="btn btn-outline-choco btn-sm rounded-pill filter-btn {{ $currentClass === $table_class ? 'active' : '' }}">
          {{ $table_class }}
        </a>
      @endforeach
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- bungkus display biar CSS page-scope --}}
    <div class="tables-index__table">
      @include('pages.partner.store.tables.display')
    </div>
    {{-- Pagination --}}
    <div class="tables-index__pagination mt-3">
        <div class="d-flex justify-content-end">
            {{ $tables->withQueryString()->links() }}
        </div>
    </div>
  </div>
</section>

<style>
    /* ==== Tables Index (page scope) ==== */
:root{
  /* fallback kalau theme belum ke-load */
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* tombol filter (outline brand) */
.tables-index .filter-btn{
  border-width:1.5px; letter-spacing:.2px; transition:.15s ease;
}
.tables-index .filter-btn.active{
  background:var(--choco); border-color:var(--choco); color:#fff;
  box-shadow:0 6px 14px rgba(140,16,0,.18);
}
.tables-index .filter-btn:not(.active){
  color:var(--choco); border-color:var(--choco); background:transparent;
}
.tables-index .filter-btn:not(.active):hover{
  background: rgba(140,16,0,.08);
}

/* tabel tampil rapi dan nyambung tema */
.tables-index__table .table{
  background:#fff; border-color:#eef1f4;
  border-radius: var(--radius); overflow:hidden;
}
.tables-index__table .table thead th{
  background:#fff; border-bottom:2px solid #eef1f4;
  color:#374151; font-weight:600;
}
.tables-index__table .table-hover tbody tr:hover{
  background: rgba(193,40,20,.06); /* soft-choco 6% */
}
.tables-index__table .text-muted{ color:#6b7280 !important; }

/* badge status seragam */
.badge-status{
  display:inline-flex; align-items:center; gap:.35rem;
  padding:.35rem .55rem; border-radius:999px;
  font-weight:600; font-size:.78rem;
}
.badge-status--active{ background:var(--choco); color:#fff; }
.badge-status--inactive{ background:#e5e7eb; color:#374151; }

/* thumbnail gambar (kalau ada foto meja) */
.tables-index__table .thumb-img{
  width:56px; height:56px; object-fit:cover;
  border-radius:12px; border:0; box-shadow:var(--shadow);
  transition: transform .15s ease, box-shadow .15s ease;
}
.tables-index__table a:hover .thumb-img{
  transform: scale(1.03); box-shadow:0 10px 24px rgba(0,0,0,.12);
}

/* empty row state */
.tables-index__table tr.empty-row td{
  color:#6b7280; background: #fafafa;
}

/* buttons brand (fallback kalau belum ada di theme) */
.btn-choco{ background:var(--choco); border-color:var(--choco); color:#fff; }
.btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); color:#fff; }
.btn-outline-choco{ color:var(--choco); border-color:var(--choco); background:#fff; }
.btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }

/* Danger lembut utk delete */
.btn-soft-danger{
  background:#fee2e2; color:#991b1b; border-color:#fecaca;
}
.btn-soft-danger:hover{
  background:#fecaca; color:#7f1d1d; border-color:#fca5a5;
}

/* ===== Pagination Choco Style (Tables Index) ===== */
.tables-index__pagination .pagination {
    margin-bottom: 0;
    gap: .25rem;
}

.tables-index__pagination .page-item .page-link {
    color: var(--choco);
    border-radius: 999px;
    padding: .35rem .75rem;
    font-weight: 600;
    font-size: .85rem;
    border: 1px solid #e5e7eb;
    background-color: #fff;
    transition: all .15s ease;
}

.tables-index__pagination .page-item .page-link:hover {
    background-color: var(--choco);
    color: #fff;
    border-color: var(--choco);
    box-shadow: 0 6px 14px rgba(140,16,0,.18);
}

.tables-index__pagination .page-item.active .page-link {
    background-color: var(--choco);
    border-color: var(--choco);
    color: #fff;
    box-shadow: 0 6px 14px rgba(140,16,0,.18);
}

.tables-index__pagination .page-item.disabled .page-link {
    color: #9ca3af;
    background-color: #f3f4f6;
    border-color: #e5e7eb;
    box-shadow: none;
    cursor: not-allowed;
}


</style>
@endsection

@push('scripts')
@vite(['resources/js/app.js'])

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
