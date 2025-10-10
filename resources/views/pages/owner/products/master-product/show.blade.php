@extends('layouts.owner')

@section('title',  __('messages.owner.products.master_products.product_detail'))
@section('page_title',  __('messages.owner.products.master_products.master_product_detail'))

@section('content')
@php
  use Illuminate\Support\Str;

  // fleksibel: dukung $product atau $data
  $prod = $product ?? $data ?? null;

  // kategori (opsional)
  $catName = optional($prod->category)->category_name ??  __('messages.owner.products.master_products.uncategorized');

  // gambar utama (ambil dari pictures[0] jika ada)
  $firstImg = null;
  if (!empty($prod->pictures) && is_array($prod->pictures)) {
      $first = $prod->pictures[0] ?? null;
      if ($first && !empty($first['path'])) {
          $firstImg = Str::startsWith($first['path'], ['http://','https://'])
              ? $first['path']
              : asset($first['path']);
      }
  } elseif (!empty($prod->image)) {
      $firstImg = Str::startsWith($prod->image, ['http://','https://'])
          ? $prod->image
          : asset('storage/'.$prod->image);
  }

  // promo (dukung relasi / field berbeda)
  $promo = $prod->promotion ?? $prod->promo ?? null;
  $promoType  = $promo->promotion_type  ?? null;   // 'percentage' atau 'nominal'
  $promoValue = $promo->promotion_value ?? null;
  $promoName  = $promo->promotion_name  ?? null;

  // harga final setelah promo
  $basePrice = (float) ($prod->price ?? 0);
  $finalPrice = $basePrice;
  if ($promoType && $promoValue !== null) {
      if ($promoType === 'percentage') {
          $finalPrice = max(0, $basePrice - ($basePrice * ((float)$promoValue/100)));
      } else {
          $finalPrice = max(0, $basePrice - (float)$promoValue);
      }
  }
@endphp

<div class="container owner-prod-show">
  {{-- Toolbar --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('owner.user-owner.master-products.index') }}" class="btn btn-outline-choco">
      <i class="fas fa-arrow-left me-2"></i>{{ __('messages.owner.products.master_products.back_to_products') }}
    </a>

    <div class="btn-group">
      <a href="{{ route('owner.user-owner.master-products.edit', $prod->id) }}" class="btn btn-choco">
        <i class="fas fa-pen me-1"></i> {{ __('messages.owner.products.master_products.edit') }}
      </a>
      <button class="btn btn-soft-danger"
              onclick="ownerConfirmDeletion(`{{ route('owner.user-owner.master-products.destroy', $prod->id) }}`)">
        <i class="fas fa-trash-alt me-1"></i> {{ __('messages.owner.products.master_products.delete') }}
      </button>
    </div>
  </div>

  <div class="card shadow-sm product-card">
    {{-- Hero --}}
    <div class="product-hero">
      <div class="product-avatar">
        @if($firstImg)
          <img src="{{ $firstImg }}" alt="{{ $prod->name }}">
        @else
          <div class="product-avatar__placeholder">
            {{ Str::upper(Str::substr($prod->name ?? 'P', 0, 1)) }}
          </div>
        @endif
      </div>

      <div class="product-hero__meta">
        <h3 class="product-name mb-1">{{ $prod->name }}</h3>

        <div class="d-flex align-items-center gap-2 flex-wrap">
          <span class="badge badge-chip">
            <i class="fas fa-tag me-1"></i>{{ $catName }}
          </span>

          @if($promo)
            <span class="badge badge-promo">
              <i class="fas fa-bolt me-1"></i>{{ $promoName ?? 'Promo' }}
            </span>
          @endif
        </div>

        <div class="price-wrap mt-2">
          @if($promo && $finalPrice < $basePrice)
            <div class="d-flex align-items-baseline gap-2 flex-wrap">
              <span class="price-final">Rp {{ number_format($finalPrice, 0, ',', '.') }}</span>
              <span class="price-strike text-muted">Rp {{ number_format($basePrice, 0, ',', '.') }}</span>
              <span class="badge badge-saving">
                @if($promoType === 'percentage')
                  -{{ number_format($promoValue, 0, ',', '.') }}%
                @else
                  -Rp {{ number_format($promoValue, 0, ',', '.') }}
                @endif
              </span>
            </div>
          @else
            <span class="price-final">Rp {{ number_format($basePrice, 0, ',', '.') }}</span>
          @endif
        </div>
      </div>
    </div>

    {{-- Body --}}
    <div class="card-body">
      <div class="row gy-3">
        {{-- Description --}}
        <div class="col-12">
          <div class="section-title">{{ __('messages.owner.products.master_products.description') }}</div>
          <div class="desc-body">
            @if(!empty($prod->description))
              {!! $prod->description !!}
            @else
              <span class="text-muted">{{ __('messages.owner.products.master_products.no_description') }}</span>
            @endif
          </div>
        </div>

        {{-- Gallery --}}
        <div class="col-12">
          <div class="section-title">{{ __('messages.owner.products.master_products.images') }}</div>
          <div class="thumb-grid">
            @if(!empty($prod->pictures) && is_array($prod->pictures))
              @foreach($prod->pictures as $p)
                @php
                  $src = !empty($p['path'])
                        ? (Str::startsWith($p['path'], ['http://','https://']) ? $p['path'] : asset($p['path']))
                        : null;
                @endphp
                @if($src)
                  <a href="{{ $src }}" target="_blank" rel="noopener" class="thumb-item">
                    <img src="{{ $src }}" alt="{{ $p['filename'] ?? 'Product Image' }}">
                  </a>
                @endif
              @endforeach
            @else
              <span class="text-muted">{{ __('messages.owner.products.master_products.no_images') }}</span>
            @endif
          </div>
        </div>

        {{-- Meta --}}
        <div class="col-md-6">
          <div class="section-title">Meta</div>
          <dl class="meta-list">
            <dt>{{ __('messages.owner.products.master_products.category') }}</dt>
            <dd>{{ $catName }}</dd>

            <dt>{{ __('messages.owner.products.master_products.base_price') }}</dt>
            <dd>Rp {{ number_format($basePrice, 0, ',', '.') }}</dd>

            <dt>{{ __('messages.owner.products.master_products.final_price') }}</dt>
            <dd>Rp {{ number_format($finalPrice, 0, ',', '.') }}</dd>
          </dl>
        </div>

        <div class="col-md-6">
          <div class="section-title">{{ __('messages.owner.products.master_products.promotion') }}</div>
          <dl class="meta-list">
            <dt>{{ __('messages.owner.products.master_products.status') }}</dt>
            <dd>
              @if($promo)
                <span class="badge badge-promo">{{ __('messages.owner.products.master_products.active') }}</span>
              @else
                <span class="badge badge-soft-secondary">{{ __('messages.owner.products.master_products.none') }}</span>
              @endif
            </dd>

            @if($promo)
              <dt>{{ __('messages.owner.products.master_products.promo_name') }}</dt>
              <dd>{{ $promoName }}</dd>

              <dt>{{ __('messages.owner.products.master_products.type') }}</dt>
              <dd class="text-capitalize">{{ $promoType }}</dd>

              <dt>{{ __('messages.owner.products.master_products.value') }}</dt>
              <dd>
                @if($promoType === 'percentage')
                  {{ number_format($promoValue, 0, ',', '.') }}%
                @else
                  Rp {{ number_format($promoValue, 0, ',', '.') }}
                @endif
              </dd>
            @endif
          </dl>
        </div>

        {{-- Options --}}
        <div class="col-12">
          <div class="section-title">{{ __('messages.owner.products.master_products.options') }}</div>
          @php
            $parents = $prod->parent_options ?? collect();
          @endphp

          @if($parents && count($parents))
            <div class="options-stack">
              @foreach($parents as $parent)
                <div class="option-card">
                  <div class="option-head">
                    <div class="lh-sm">
                      <div class="option-title">{{ $parent->name }}</div>
                      @if(!empty($parent->description))
                        <div class="option-sub">{{ $parent->description }}</div>
                      @endif
                    </div>
                    <div class="option-badges">
                      <span class="badge badge-chip">
                        @if ($parent->provision)
                          @if ($parent->provision === 'OPTIONAL')
                            {{ __('messages.owner.products.master_products.optional') }}
                          @elseif ($parent->provision === 'OPTIONAL MAX')
                            {{ __('messages.owner.products.master_products.optional_max') }}
                          @elseif ($parent->provision === 'MAX')
                            {{ __('messages.owner.products.master_products.max_provision') }}
                          @elseif ($parent->provision === 'EXACT')
                            {{ __('messages.owner.products.master_products.exact_provision') }}
                          @elseif ($parent->provision === 'MIN')
                            {{ __('messages.owner.products.master_products.min_provision') }}
                          @endif
                        @endif
                        @if(!empty($parent->provision_value) && $parent->provision !== 'OPTIONAL')
                          : {{ $parent->provision_value }}
                        @endif
                      </span>
                    </div>
                  </div>

                  @if(!empty($parent->options) && count($parent->options))
                    <div class="table-responsive">
                      <table class="table table-sm align-middle mb-0">
                        <thead>
                          <tr>
                            <th style="width:38%">{{ __('messages.owner.products.master_products.options') }}</th>
                            <th style="width:18%">{{ __('messages.owner.products.master_products.price') }}</th>
                            <th>{{ __('messages.owner.products.master_products.description') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($parent->options as $opt)
                            <tr>
                              <td class="fw-600">{{ $opt->name }}</td>
                              <td>Rp {{ number_format((float)$opt->price, 0, ',', '.') }}</td>
                              <td class="text-muted">{{ $opt->description ?: '—' }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  @else
                    <div class="text-muted">{{ __('messages.owner.products.master_products.no_child_options') }}</div>
                  @endif
                </div>
              @endforeach
            </div>
          @else
            <span class="text-muted">{{ __('messages.owner.products.master_products.no_options') }}</span>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* ===== Owner › Product Show (page scope) ===== */
.owner-prod-show{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

/* Card */
.owner-prod-show .product-card{
  border:0; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden;
}

/* Hero */
.owner-prod-show .product-hero{
  display:flex; align-items:center; gap:12px;
  padding:12px 16px; background:#fff; border-bottom:1px solid #eef1f4;
}
.owner-prod-show .product-avatar{
  width:64px; height:64px; border-radius:12px; overflow:hidden; flex:0 0 auto;
  box-shadow:var(--shadow); background:#fff;
}
.owner-prod-show .product-avatar img{
  width:100%; height:100%; object-fit:cover; display:block;
}
.owner-prod-show .product-avatar__placeholder{
  width:100%; height:100%; display:flex; align-items:center; justify-content:center;
  font-weight:700; font-size:22px; color:#fff;
  background:linear-gradient(135deg, var(--choco), var(--soft-choco));
}
.owner-prod-show .product-hero__meta{ min-width:0; }
.owner-prod-show .product-name{ font-weight:700; color:var(--ink); margin:0; }

/* Price */
.owner-prod-show .price-wrap{ line-height:1; }
.owner-prod-show .price-final{
  font-size:1.25rem; font-weight:800; color:#111827;
}
.owner-prod-show .price-strike{
  text-decoration: line-through;
}
.owner-prod-show .badge-saving{
  background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; border-radius:999px;
  padding:.2rem .5rem; font-weight:700;
}

/* Badges */
.owner-prod-show .badge-chip{
  background:#fff1ef; color:#8c1000; border:1px solid #f7c9c2; border-radius:999px;
  padding:.28rem .6rem; font-weight:600;
}
.owner-prod-show .badge-promo{
  background:#fef3c7; color:#92400e; border:1px solid #fde68a; border-radius:999px;
  padding:.28rem .6rem; font-weight:700;
}
.owner-prod-show .badge-soft-secondary{
  background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:999px;
  padding:.28rem .6rem; font-weight:600;
}

/* Sections */
.owner-prod-show .section-title{
  font-weight:700; color:#374151; margin-bottom:.5rem;
}

/* Description */
.owner-prod-show .desc-body :where(p,ul,ol){ margin-bottom:.5rem; }
.owner-prod-show .desc-body img{ max-width:100%; height:auto; border-radius:10px; }

/* Meta list */
.owner-prod-show .meta-list{ margin:0 0 1rem; }
.owner-prod-show .meta-list dt{ font-weight:700; color:#374151; margin-top:.35rem; }
.owner-prod-show .meta-list dd{ margin-bottom:.75rem; color:#4b5563; }

/* Gallery */
.owner-prod-show .thumb-grid{
  display:flex; flex-wrap:wrap; gap:.6rem;
}
.owner-prod-show .thumb-item img{
  width:96px; height:96px; object-fit:cover; display:block;
  border-radius:12px; border:0; box-shadow:var(--shadow);
}

/* Options */
.owner-prod-show .options-stack{ display:flex; flex-direction:column; gap:.75rem; }
.owner-prod-show .option-card{
  border:1px solid #eef1f4; border-radius:12px; padding:.75rem;
  background:#fff;
}
.owner-prod-show .option-head{
  display:flex; justify-content:space-between; align-items:flex-start; gap:.75rem; margin-bottom:.5rem;
}
.owner-prod-show .option-title{ font-weight:700; color:#111827; }
.owner-prod-show .option-sub{ color:#6b7280; font-size:.95rem; }

/* Buttons */
.owner-prod-show .btn-choco{ background:var(--choco); border-color:var(--choco); color:#fff; }
.owner-prod-show .btn-choco:hover{ background:var(--soft-choco); border-color:var(--soft-choco); }
.owner-prod-show .btn-outline-choco{ color:var(--choco); border-color:var(--choco); }
.owner-prod-show .btn-outline-choco:hover{ color:#fff; background:var(--choco); border-color:var(--choco); }
.owner-prod-show .btn-soft-danger{ background:#fee2e2; color:#991b1b; border-color:#fecaca; }
.owner-prod-show .btn-soft-danger:hover{ background:#fecaca; color:#7f1d1d; border-color:#fca5a5; }

/* Table */
.owner-prod-show table thead th{
  background:#fff; border-bottom:2px solid #eef1f4!important; color:#374151; font-weight:700;
}
</style>
@endsection




@push('scripts')
<script>
  function ownerConfirmDeletion(url, opts = {}) {
    const base = {
      title: '{{ __('messages.owner.products.master_products.delete_confirmation_1') }}',
      text: '{{ __('messages.owner.products.master_products.delete_confirmation_2') }}',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: '{{ __('messages.owner.products.master_products.delete_confirmation_3') }}',
      cancelButtonText: '{{ __('messages.owner.products.master_products.cancel') }}'
    };
    const swal = window.$swal || window.Swal;
    if (!swal) {
      if (confirm(base.title + '\n' + base.text)) ownerPostDelete(url);
      return;
    }
    swal.fire(Object.assign(base, opts)).then(r => { if (r.isConfirmed) ownerPostDelete(url); });
  }

  function ownerPostDelete(url) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.style.display = 'none';
    form.innerHTML = `
      @csrf
      <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
  }
</script>
@endpush
