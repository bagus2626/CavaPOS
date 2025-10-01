@extends('layouts.partner')

@section('title', 'Product Detail')
@section('page_title', 'Product Detail')

@section('content')
<section class="content product-show">
  <div class="container-fluid">

    {{-- Tombol kembali --}}
    <a href="{{ route('partner.products.index') }}" class="btn btn-outline-choco mb-4 btn-pill">
      <i class="fas fa-arrow-left mr-2"></i> Back to Products
    </a>

    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-header brand-header rounded-top-4">
        <h3 class="card-title fw-bold mb-0">{{ $data->name }}</h3>
      </div>

      <div class="card-body">
        {{-- Galeri gambar --}}
        @if(!empty($data->pictures))
          @php $pictures = $data->pictures; @endphp
          <div class="product-gallery mb-4">
            @foreach($pictures as $pic)
              @php $src = asset($pic['path']); @endphp
              <a href="{{ $src }}" target="_blank" rel="noopener" class="gallery-item" title="{{ $pic['filename'] ?? $data->name }}">
                <img src="{{ $src }}" alt="{{ $pic['filename'] ?? $data->name }}" class="gallery-img">
              </a>
            @endforeach
          </div>
        @endif

        {{-- Deskripsi & jumlah --}}
        <div class="product-meta mb-3">
          <p class="lead mb-2"><strong>Description:</strong> {{ $data->description ?? '—' }}</p>
          <p class="lead mb-0"><strong>Jumlah:</strong>
            @if((int) $data->always_available_flag === 1)
              <span class="badge badge-soft-success">Always Available</span>
            @else
              {{ (int) $data->quantity }}
            @endif
          </p>
        </div>

        {{-- Parent Options --}}
        @foreach($data->parent_options as $parentOption)
          <div class="card mt-4 shadow-sm border-0 rounded-4">
            <div class="card-header sub-header rounded-top-4">
              <h4 class="card-title mb-0 fw-semibold">{{ $parentOption->name }}</h4>
            </div>
            <div class="card-body">
              @if(!empty($parentOption->description))
                <p class="text-muted mb-3">{{ $parentOption->description }}</p>
              @endif

              @if($parentOption->options->isNotEmpty())
                <div class="table-responsive rounded-3">
                  <table class="table table-hover align-middle product-options-table mb-0">
                    <thead>
                      <tr>
                        <th>Options</th>
                        <th class="text-center">Quantity</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($parentOption->options as $option)
                        <tr>
                          <td class="fw-600">{{ $option->name }}</td>
                          <td class="text-center">
                            @if((int) $option->always_available_flag === 1)
                              <span class="badge badge-soft-success">Always Available</span>
                            @else
                              {{ (int) $option->quantity }}
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <p class="text-muted mb-0">No Option found for this package.</p>
              @endif
            </div>
          </div>
        @endforeach

      </div>
    </div>
  </div>
</section>

<style>
    /* ==== Product Detail (page scope) ==== */
:root{
  /* fallback kalau theme global belum termuat */
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* Card & header */
.product-show .card{ border-radius: var(--radius); box-shadow: var(--shadow); }
.product-show .brand-header{
  background: linear-gradient(135deg, var(--choco), var(--soft-choco));
  color: #fff;
  border-bottom: 0;
}
.product-show .sub-header{
  background:#fff;
  border-bottom:1px solid #eef1f4;
}

/* Back button & brand buttons (selaras) */
.btn-pill{ border-radius:999px; }
.btn-outline-choco{
  color: var(--choco); border-color: var(--choco);
}
.btn-outline-choco:hover{
  color:#fff; background: var(--choco); border-color: var(--choco);
}

/* Galeri */
.product-gallery{
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: .75rem;
}
.gallery-item{ display:block; }
.gallery-img{
  width:100%;
  aspect-ratio: 4/3;
  object-fit: cover;
  border-radius: 12px;
  border:0;
  box-shadow: var(--shadow);
  transition: transform .18s ease, box-shadow .18s ease;
}
.gallery-item:hover .gallery-img{
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(0,0,0,.12);
}

/* Meta */
.product-meta .lead{ font-size:1.05rem; color:#374151; }

/* Tabel options */
.product-options-table{
  background:#fff; border-radius:10px; overflow:hidden;
}
.product-options-table thead th{
  background:#fff;
  border-bottom:2px solid #eef1f4 !important;
  color:#374151; font-weight:700;
}
.product-options-table tbody tr{ transition: background-color .12s ease; }
.product-options-table tbody tr:hover{ background: rgba(140,16,0,.04); }

/* Badges */
.badge-soft-success{
  background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;
  padding:.32rem .55rem; border-radius:999px; font-weight:600;
}

</style>
@endsection
