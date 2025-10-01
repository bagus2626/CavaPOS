@php
  use Illuminate\Support\Str;
@endphp

<div class="table-responsive owner-products-table">
  <table class="table table-hover align-middle">
    <thead>
      <tr>
        <th>#</th>
        <th>Nama Produk</th>
        <th class="col-desc">Deskripsi</th>
        <th>Pilihan</th>
        <th>Jumlah</th>
        <th>Harga</th>
        <th>Gambar</th>
        <th>Promo</th>
        <th class="text-nowrap">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($products as $index => $product)
        <tr data-category="{{ $product->category_id }}">
          <td class="text-muted">{{ $index + 1 }}</td>

          <td class="fw-600">{{ $product->name }}</td>

          <td class="col-desc">
            <div class="desc-clamp">{{ $product->description }}</div>
          </td>

          <td>
            @if($product->parent_options->isEmpty())
              <em class="text-muted">No packages</em>
            @else
              {{ $product->parent_options->pluck('name')->implode(', ') }}
            @endif
          </td>

          <td>{{ $product->quantity }}</td>

          <td>Rp {{ number_format($product->price) }}</td>

          <td class="col-photo">
            @if(!empty($product->pictures) && is_array($product->pictures))
              <div class="d-flex flex-wrap gap-2">
                @foreach($product->pictures as $picture)
                  @php $src = asset($picture['path']); @endphp
                  <a href="{{ $src }}" target="_blank" rel="noopener">
                    <img src="{{ $src }}"
                         alt="{{ $picture['filename'] ?? 'Product Image' }}"
                         class="thumb-56" loading="lazy">
                  </a>
                @endforeach
              </div>
            @else
              <span class="text-muted">No Images</span>
            @endif
          </td>

          <td>
            @if($product->promotion)
              <span class="badge badge-soft-warning">
                {{ $product->promotion->promotion_name }}
              </span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>

          <td class="col-actions">
            <div class="btn-group btn-group-sm">
              <a href="{{ route('owner.user-owner.master-products.show', $product->id) }}" class="btn btn-outline-secondary">Detail</a>
              <a href="{{ route('owner.user-owner.master-products.edit', $product->id) }}"  class="btn btn-outline-choco">Edit</a>
              <button onclick="deleteProduct({{ $product->id }})" class="btn btn-soft-danger">Delete</button>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<style>
/* ===== Master Products › Tabel (scoped) ===== */
.owner-products-table{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}
.owner-products-table .table{
  border-collapse:separate; border-spacing:0;
  background:#fff; margin-bottom:0;
}
.owner-products-table thead th{
  background:#fff; color:#374151; font-weight:700;
  border-bottom:2px solid #eef1f4 !important;
  white-space:nowrap;
}
.owner-products-table tbody td{ vertical-align:middle; }
.owner-products-table tbody tr{ transition: background-color .12s ease; }
.owner-products-table tbody tr:hover{ background: rgba(140,16,0,.04); }

/* Deskripsi: clamp 2 baris */
.owner-products-table .col-desc{ max-width: 460px; }
.owner-products-table .desc-clamp{
  display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;
  overflow:hidden; color:#4b5563;
}

/* Thumb gambar */
.owner-products-table .thumb-56{
  width:56px; height:56px; object-fit:cover;
  border-radius:12px; border:0; box-shadow: var(--shadow);
}

/* Badges lembut */
.owner-products-table .badge-soft-warning{
  background:#fffbeb; color:#92400e; border:1px solid #fcd34d;
  padding:.32rem .55rem; border-radius:999px; font-weight:600;
}

/* Actions simetris */
.owner-products-table .col-actions{ white-space:nowrap; }
.owner-products-table .btn-group.btn-group-sm{ gap:.4rem; }
.owner-products-table .btn-group.btn-group-sm > .btn{
  border-radius:10px !important; padding:.28rem .6rem; min-width:72px; line-height:1.25;
}
.owner-products-table .btn-outline-choco{
  color: var(--choco); border-color: var(--choco); background:#fff;
}
.owner-products-table .btn-outline-choco:hover{
  color:#fff; background:var(--choco); border-color:var(--choco);
}
.owner-products-table .btn-soft-danger{
  background:#fee2e2; color:#991b1b; border-color:#fecaca; border-radius:10px;
}
.owner-products-table .btn-soft-danger:hover{
  background:#fecaca; color:#7f1d1d; border-color:#fca5a5;
}

/* Responsive: sempitkan deskripsi */
@media (max-width: 576px){
  .owner-products-table .col-desc{ max-width:260px; }
}
</style>
