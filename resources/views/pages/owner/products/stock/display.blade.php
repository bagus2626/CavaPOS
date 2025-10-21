<div class="table-responsive owner-stocks-table">
  <table class="table table-hover align-middle">
    <thead>
      <tr>
        <th>#</th>
        <th>Stock Code</th>
        <th>Stock Name</th>
        <th>Stock/Quantity</th>
        <th>Unit</th>
        <th>Last Price/Unit</th>
        <th>Description</th>
        <th class="text-nowrap">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($stocks as $index => $stock)

        <tr data-category="{{ $stock->type }}">
          <td class="text-muted">{{ $index + 1 }}</td>
          <td class="mono">{{ $stock->stock_code }}</td>
          <td class="fw-600">{{ $stock->stock_name }}</td>
          <td>{{ $stock->quantity }}</td>
          <td>{{ $stock->unit }}</td>
          <td>{{ $stock->last_price_per_unit }}</td>
          <td>{{ $stock->description ?? '-' }}</td>
          <td class="text-nowrap">
            <a href="{{ route('owner.user-owner.stocks.show', $stock->id) }}" class="btn btn-sm btn-outline-choco me-1">Detail</a>
            <a href="{{ route('owner.user-owner.stocks.edit', $stock->id) }}" class="btn btn-sm btn-outline-choco me-1">Edit</a>
            <button onclick="deleteStock({{ $stock->id }})" class="btn btn-sm btn-soft-danger">Delete</button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<style>
/* ===== Promotions table (scoped to the parent page .owner-promotions) ===== */
.owner-stocks .owner-stocks-table{
  border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,.08); overflow-y:hidden; background:#fff;
}
.owner-stocks .table{ margin-bottom:0; background:#fff; }
.owner-stocks thead th{
  background:#fff; border-bottom:2px solid #eef1f4 !important;
  color:#374151; font-weight:700; white-space:nowrap;
}
.owner-stocks tbody td{ vertical-align:middle; }
.owner-stocks tbody tr{ transition: background-color .12s ease; }
.owner-stocks tbody tr:hover{ background: rgba(140,16,0,.04); }

/* text utils */
.owner-stocks .fw-600{ font-weight:600; }
.owner-stocks .mono{
  font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono",monospace;
  color:#374151;
}

/* Type badge */
.owner-stocks .badge.badge-type{
  background:#eff6ff; color:#1d4ed8; border:1px solid #dbeafe;
  border-radius:999px; padding:.28rem .55rem; font-weight:600;
}

/* Soft status badges (override bs contextual badges, only inside scope) */
.owner-stocks .badge-success{
  background:#ecfdf5 !important; color:#065f46 !important; border:1px solid #a7f3d0; border-radius:999px;
}
.owner-stocks .badge-secondary{
  background:#f3f4f6 !important; color:#374151 !important; border:1px solid #e5e7eb; border-radius:999px;
}
.owner-stocks .badge-warning{
  background:#fef3c7 !important; color:#92400e !important; border:1px solid #fde68a; border-radius:999px;
}
.owner-stocks .badge-danger{
  background:#fee2e2 !important; color:#991b1b !important; border:1px solid #fecaca; border-radius:999px;
}

/* Action buttons */
.owner-stocks .btn-outline-choco{
  color:#8c1000; border:1px solid #8c1000; background:#fff;
}
.owner-stocks .btn-outline-choco:hover{
  color:#fff; background:#8c1000; border-color:#8c1000;
}
.owner-stocks .btn-soft-danger{
  background:#fee2e2; color:#991b1b; border:1px solid #fecaca;
}
.owner-stocks .btn-soft-danger:hover{
  background:#fecaca; color:#7f1d1d; border-color:#fca5a5;
}
</style>
<script>
  function deleteStock(stockId) {
  Swal.fire({
    title: '{{ __('messages.owner.products.promotions.delete_confirmation_1') }}',
    text: "{{ __('messages.owner.products.promotions.delete_confirmation_2') }}",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#8c1000', // brand choco
    cancelButtonColor: '#6b7280',
    confirmButtonText: '{{ __('messages.owner.products.promotions.delete_confirmation_3') }}',
    cancelButtonText: '{{ __('messages.owner.products.promotions.cancel') }}',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/owner/user-owner/stocks/${stockId}`;
      form.style.display = 'none';

      const csrf = document.createElement('input');
      csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
      form.appendChild(csrf);

      const method = document.createElement('input');
      method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
      form.appendChild(method);

      document.body.appendChild(form);
      form.submit();
    }
  });
}
</script>