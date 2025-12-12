{{-- resources/views/pages/owner/products/outlet-product/_table.blade.php --}}

<tbody>
@php $no = ($products->currentPage() - 1) * $products->perPage() + 1; @endphp

@forelse($products as $p)
  <tr data-outlet="{{ $outletId }}" data-category="{{ $p->category_id }}">
    <td>{{ $no++ }}</td>

    <td class="d-flex align-items-center gap-2">
      <div class="position-relative" style="width:40px; height:40px;">
        @if(!empty($p->pictures) && isset($p->pictures[0]['path']))
          <img src="{{ asset($p->pictures[0]['path']) }}"
               alt="{{ $p->name ?? $p->product_name }}"
               style="width:40px; height:40px; object-fit:cover; border-radius:6px;">
        @else
          <div style="
              width:40px; height:40px;
              background:#f3f4f6;
              border-radius:6px;
              display:flex;
              align-items:center;
              justify-content:center;
              font-size:12px;
              color:#9ca3af;
          ">
            <i class="fas fa-image"></i>
          </div>
        @endif

        @if($p->is_hot_product)
          <span style="
              position:absolute;
              top:-6px;
              right:-6px;
              background:#ff5722;
              color:white;
              padding:2px 6px;
              border-radius:8px;
              font-size:10px;
              font-weight:600;
              box-shadow:0 2px 6px rgba(0,0,0,0.2);
          ">
            HOT
          </span>
        @endif
      </div>

      <span class="ml-1">{{ $p->name ?? $p->product_name }}</span>
    </td>

    <td>{{ $p->category->category_name ?? '-' }}</td>

    {{-- STOCK --}}
    <td>
      @php
        $qtyAvailable = $p->quantity_available;
        $isQtyZero    = $qtyAvailable < 1 && $qtyAvailable !== 999999999;
      @endphp

      @if($p->stock_type == 'linked')
        @if ($qtyAvailable === 999999999)
          <span class="text-muted" style="font-style: italic;">
            {{ __('messages.owner.products.outlet_products.always_available') }}
          </span>
        @elseif ($isQtyZero)
          <span class="badge bg-danger" title="Bahan baku tidak mencukupi!">Bahan Habis</span>
        @else
          <strong>{{ number_format(floor($qtyAvailable), 0) }}</strong>
          <span class="text-muted small">pcs</span>
        @endif

      @elseif((int) $p->always_available_flag === 1)
        <span class="text-muted">
          {{ __('messages.owner.products.outlet_products.always_available') }}
        </span>

      @elseif($p->stock)
        @if ($isQtyZero)
          <span class="badge bg-danger" title="Stok 0 atau belum diisi!">0</span>
        @else
          <strong>
            {{ rtrim(rtrim(number_format($qtyAvailable, 2, ',', '.'), '0'), ',') }}
          </strong>
          <span class="text-muted small">{{ $p->stock->displayUnit->unit_name ?? 'unit' }}</span>
        @endif

      @else
        <span class="badge bg-danger" title="Stok tidak ditemukan!">0</span>
      @endif
    </td>

    {{-- STATUS --}}
    <td>
      @php
        $active = (int) ($p->is_active ?? 1);
      @endphp
      <span class="badge bg-{{ $active ? 'success' : 'secondary' }}">
        {{ $active ? 'Active' : 'Inactive' }}
      </span>
    </td>

    {{-- PRICE --}}
    <td>Rp {{ number_format($p->price) }}</td>

    {{-- PROMO --}}
    <td>
      @if($p->promotion)
        <span class="badge bg-warning">
          {{ $p->promotion->promotion_name }}
        </span>
      @else
        <span class="text-muted">—</span>
      @endif
    </td>

    {{-- ACTIONS --}}
    <td class="text-end">
      <a href="{{ route('owner.user-owner.outlet-products.edit', $p->id) }}"
         class="btn btn-outline-dark btn-sm">
        {{ __('messages.owner.products.outlet_products.edit') }}
      </a>
      <button class="btn btn-primary btn-sm" onclick="deleteProduct({{ $p->id }})">
        {{ __('messages.owner.products.outlet_products.delete') }}
      </button>
    </td>
  </tr>
@empty
  <tr class="empty-row">
    <td colspan="8" class="text-center text-muted">
      {{ __('messages.owner.products.outlet_products.no_product_yet') }}
    </td>
  </tr>
@endforelse
</tbody>

<div class="pagination-wrapper mt-2">
  @if($products->hasPages())
    {{ $products->appends([
        'outlet_id'   => $outletId,
        'category_id' => $categoryId,
    ])->links() }}
  @endif
</div>
