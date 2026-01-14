{{-- <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">

  <div class="filter-tabs-container">
    <ul class="nav nav-tabs" id="stockFilterTabs">
      <li class="nav-item">
        <a class="nav-link active" href="#" data-filter-type="all">{{ __('messages.owner.products.stocks.all_stock') }}</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" data-filter-type="linked">{{ __('messages.owner.products.stocks.raw_materials') }}</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" data-filter-type="direct">{{ __('messages.owner.products.stocks.products') }}</a>
      </li>
    </ul>
  </div>

</div> --}}

{{-- <div class="sub-filter-container mb-3 ms-3" id="stockPartnerFilterContainer" style="display: none;">
  <ul class="nav nav-tabs" id="stockPartnerFilterTabs">
    <li class="nav-item">
      <a class="nav-link font-weight-normal active" id="filter-partner-product-tab" href="#"
        data-filter-partner-type="product">{{ __('messages.owner.products.stocks.main_product') }}</a>
    </li>
    <li class="nav-item">
      <a class="nav-link font-weight-normal" id="filter-partner-option-tab" href="#"
        data-filter-partner-type="option">{{ __('messages.owner.products.stocks.product_opt') }}</a>
    </li>
  </ul>
</div> --}}

<!-- Table Card -->
<div class="modern-card">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">#</th>
                    <th>{{ __('messages.owner.products.stocks.stock_code') }}</th>
                    <th>{{ __('messages.owner.products.stocks.stock_name') }}</th>
                    <th>{{ __('messages.owner.products.stocks.stock_quantity') }}</th>
                    <th>{{ __('messages.owner.products.stocks.unit') }}</th>
                    <th>{{ __('messages.owner.products.stocks.last_price_unit') }}</th>
                    <th class="text-center" style="width: 160px;">
                        {{ __('messages.owner.products.stocks.actions') }}
                    </th>
                </tr>
            </thead>

            <tbody id="stockTableBody">
                @forelse ($stocks as $index => $stock)
                    <tr class="table-row"
                        data-type="{{ $stock->type }}"
                        data-stock_type="{{ $stock->stock_type }}"
                        data-partner-type="{{ $stock->partner_product_id && !$stock->partner_product_option_id ? 'product' : ($stock->partner_product_id && $stock->partner_product_option_id ? 'option' : 'none') }}">

                        <!-- Number -->
                        <td class="text-center text-muted">
                            {{ $stocks->firstItem() + $index }}
                        </td>

                        <!-- Stock Code -->
                        <td class="mono fw-600">
                            {{ $stock->stock_code }}
                        </td>

                        <!-- Stock Name -->
                        <td>
                            <span class="fw-600">{{ $stock->stock_name }}</span>
                        </td>

                        <!-- Quantity -->
                        <td>
                            {{ number_format($stock->display_quantity, 2, ',', '.') }}
                        </td>

                        <!-- Unit -->
                        <td>
                            @if($stock->displayUnit)
                                <span class="badge-modern badge-info">
                                    {{ $stock->displayUnit->unit_name }}
                                </span>
                            @else
                                <span class="text-muted small">
                                    ({{ __('messages.owner.products.stocks.base_unit') }})
                                </span>
                            @endif
                        </td>

                        <!-- Last Price -->
                        <td>
                            <span class="fw-600">
                                {{ $stock->last_price_per_unit }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <div class="table-actions">
                                <button onclick="deleteStock({{ $stock->id }})"
                                    class="btn-table-action delete"
                                    title="{{ __('messages.owner.products.stocks.delete') }}">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">inventory_2</span>
                                <h4>{{ __('messages.owner.products.stocks.no_stock_found') }}</h4>
                                <p>{{ __('messages.owner.products.stocks.add_first_stock') ?? 'Add your first stock to get started' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($stocks->hasPages())
        <div class="table-pagination">
            {{ $stocks->links() }}
        </div>
    @endif
</div>



<script>
  function deleteStock(stockId) {
    Swal.fire({
      title: '{{ __('messages.owner.products.promotions.delete_confirmation_1') }}',
      text: "{{ __('messages.owner.products.promotions.delete_confirmation_2') }}",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#8c1000',
      cancelButtonColor: '#6b7280',
      confirmButtonText: '{{ __('messages.owner.products.promotions.delete_confirmation_3') }}',
      cancelButtonText: '{{ __('messages.owner.products.promotions.cancel') }}',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/owner/user-owner/stocks/delete-stock/${stockId}`;
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
      let visibleCount = 0;
      
      tableRows.forEach(row => {
        // Skip empty state row
        if (row.querySelector('td[colspan]')) {
          return;
        }

        const rowType = row.getAttribute('data-type');
        const stockType = row.getAttribute('data-stock_type');
        const rowPartnerType = row.getAttribute('data-partner-type');

        let show = false;

        if (currentMainFilter === 'all') {
          show = true;
        } else if (currentMainFilter === 'linked') {
          show = (stockType === 'linked');
        } else if (currentMainFilter === 'direct') {
          if (stockType === 'direct') {
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
        if (show) visibleCount++;
      });

      // Show/hide empty state if no visible rows
      const emptyRow = document.querySelector('.owner-stocks-table tbody tr td[colspan]');
      if (emptyRow && emptyRow.parentElement) {
        emptyRow.parentElement.style.display = visibleCount === 0 ? '' : 'none';
      }
    }

    // Event listener untuk tab filter utama
    mainFilterTabs.forEach(tab => {
      tab.addEventListener('click', function (e) {
        e.preventDefault();

        mainFilterTabs.forEach(t => {
          t.classList.remove('active');
        });
        this.classList.add('active');

        currentMainFilter = this.getAttribute('data-filter-type');

        if (currentMainFilter === 'direct') {
          partnerFilterContainer.style.display = '';
          currentPartnerFilter = 'product';

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