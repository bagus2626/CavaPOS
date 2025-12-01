<div class="d-flex flex-wrap align-items-center justify-content-between mb-2">

  <div class="filter-tabs-container">
    <ul class="nav nav-tabs" id="stockFilterTabs">
      <li class="nav-item">
        <a class="nav-link active" href="#" data-filter-type="all">Semua Stok</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" data-filter-type="master">Bahan Baku</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" data-filter-type="partner">Produk</a>
      </li>
    </ul>
  </div>

  <div class="ms-auto pt-2 pt-md-0">
    <a href="{{ route('owner.user-owner.stocks.movements.index') }}" class="btn btn-outline-primary btn-movements">
      <i class="fas fa-history me-1"></i>
      Riwayat Pergerakan
    </a>
  </div>
</div>

<div class="sub-filter-container mb-3 ms-3" id="stockPartnerFilterContainer" style="display: none;">
  <ul class="nav nav-tabs" id="stockPartnerFilterTabs">
    <li class="nav-item">
      <a class="nav-link font-weight-normal active" id="filter-partner-product-tab" href="#"
        data-filter-partner-type="product">Produk
        Utama</a>
    </li>
    <li class="nav-item">
      <a class="nav-link font-weight-normal" id="filter-partner-option-tab" href="#"
        data-filter-partner-type="option">Opsi Produk</a>
    </li>
  </ul>
</div>

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
        {{-- <th>Description</th> --}}
        <th class="text-nowrap">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($stocks as $index => $stock)

        <tr data-type="{{ $stock->type }}"
          data-partner-type="{{ $stock->partner_product_id && !$stock->partner_product_option_id ? 'product' : ($stock->partner_product_id && $stock->partner_product_option_id ? 'option' : 'none') }}">
          <td class="text-muted">{{ $index + 1 }}</td>
          <td class="mono">{{ $stock->stock_code }}</td>
          <td class="fw-600">{{ $stock->stock_name }}</td>
          <td>{{ number_format($stock->display_quantity, 2) }}</td>

          <td>
            @if($stock->displayUnit)
              {{ $stock->displayUnit->unit_name }}
            @else
              <span class="text-muted small">(Unit Dasar)</span>
            @endif
          </td>

          <td>{{ $stock->last_price_per_unit }}</td>
          {{-- <td>{{ $stock->description ?? '-' }}</td> --}}
          <td class="text-nowrap">
            <a href="{{ route('owner.user-owner.stocks.show', $stock->id) }}"
              class="btn btn-sm btn-outline-choco me-1">Detail</a>
            <a href="{{ route('owner.user-owner.stocks.edit', $stock->id) }}"
              class="btn btn-sm btn-outline-choco me-1">Edit</a>
            <button onclick="deleteStock({{ $stock->id }})" class="btn btn-sm btn-soft-danger">Delete</button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<style>
  /* ... (Semua CSS Anda yang lama tetap di sini) ... */

  .owner-stocks .owner-stocks-table {
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
    overflow-y: hidden;
    background: #fff;
  }

  /* ... ... */
  .owner-stocks .btn-soft-danger:hover {
    background: #fecaca;
    color: #7f1d1d;
    border-color: #fca5a5;
  }


  .owner-stocks .nav-tabs {
    border-bottom: 2px solid #eef1f4;
  }

  .owner-stocks .nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6b7280;
    /* abu-abu */
    font-weight: 600;
    padding: 0.75rem 1rem;
  }

  .owner-stocks .nav-tabs .nav-link.active {
    border-color: #8c1000;
    /* var(--choco) */
    color: #8c1000;
    /* var(--choco) */
    background-color: transparent;
  }

  .owner-stocks .nav-tabs .nav-link:hover {
    border-color: #e5e7eb;
    /* abu-abu muda */
  }
</style>
<script>
  { { --Fungsi deleteStock Anda tetap sama(tidak diubah)-- } }
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

{{-- Script untuk Tab Filter --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const mainFilterTabs = document.querySelectorAll('#stockFilterTabs .nav-link');
    const partnerFilterTabs = document.querySelectorAll('#stockPartnerFilterTabs .nav-link');
    const partnerFilterContainer = document.getElementById('stockPartnerFilterContainer');
    const tableRows = document.querySelectorAll('.owner-stocks-table tbody tr');

    let currentMainFilter = 'all';
    let currentPartnerFilter = 'product';

    function applyFilters() {
      tableRows.forEach(row => {
        const rowType = row.getAttribute('data-type');
        const rowPartnerType = row.getAttribute('data-partner-type');

        let show = false;

        if (currentMainFilter === 'all') {
          show = true;
        } else if (currentMainFilter === 'master') {
          show = (rowType === 'master');
        } else if (currentMainFilter === 'partner') {
          if (rowType === 'partner') {
            if (currentPartnerFilter === 'product') {
              show = (rowPartnerType === 'product');
            } else if (currentPartnerFilter === 'option') {
              show = (rowPartnerType === 'option');
            }
          } else {
            show = false;
          }
        }
        row.style.display = show ? '' : 'none';
      });
    }

    // Event listener untuk tab filter utama
    mainFilterTabs.forEach(tab => {
      tab.addEventListener('click', function (e) {
        e.preventDefault();

        // Tukar kelas 'active' untuk filter utama
        mainFilterTabs.forEach(t => {
          t.classList.remove('active');
        });
        this.classList.add('active');

        currentMainFilter = this.getAttribute('data-filter-type');

        if (currentMainFilter === 'partner') {
          partnerFilterContainer.style.display = '';

          // Reset filter partner ke 'product'
          currentPartnerFilter = 'product';

          // Reset style tombol sub-filter
          partnerFilterTabs.forEach(t => {
            t.classList.remove('active');
          });
          document.getElementById('filter-partner-product-tab').classList.add('active');

        } else {
          partnerFilterContainer.style.display = 'none';
        }

        applyFilters();
      });
    });

    // Event listener untuk tab filter sekunder (partner)
    partnerFilterTabs.forEach(tab => {
      tab.addEventListener('click', function (e) {
        e.preventDefault();

        partnerFilterTabs.forEach(t => {
          t.classList.remove('active');
        });
        this.classList.add('active');

        currentPartnerFilter = this.getAttribute('data-filter-partner-type');
        applyFilters();
      });
    });

    applyFilters();
  });
</script>