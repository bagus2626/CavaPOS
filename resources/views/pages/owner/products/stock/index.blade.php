@extends('layouts.owner')

@section('title', __('messages.owner.products.stocks.stock_list'))
@section('page_title', __('messages.owner.products.stocks.all_stock'))

@section('content')
  <section class="content">
    <div class="container-fluid owner-stocks"> {{-- PAGE SCOPE --}}

      <div class="d-flex flex-wrap gap-2 mb-3 align-items-center justify-content-between">
        <a href="{{ route('owner.user-owner.stocks.create') }}" class="btn btn-primary">
          <i class="fas fa-plus me-1"></i>
          {{ __('messages.owner.products.stocks.add_stock_item') }}
        </a>

        <div class="btn-group">
          <a href="{{ route('owner.user-owner.stocks.movements.create-stock-in') }}" class="btn btn-outline-success">
            <i class="fas fa-arrow-down fa-fw me-1"></i> {{ __('messages.owner.products.stocks.stock_in') }}
          </a>
          <a href="{{ route('owner.user-owner.stocks.movements.create-transfer') }}" class="btn btn-outline-info">
            <i class="fas fa-exchange-alt fa-fw me-1"></i> {{ __('messages.owner.products.stocks.transfer') }}
          </a>
          <a href="{{ route('owner.user-owner.stocks.movements.create-adjustment') }}" class="btn btn-outline-danger">
            <i class="fas fa-arrow-up fa-fw me-1"></i> {{ __('messages.owner.products.stocks.adjustment') }}
          </a>
        </div>

      </div>


      {{-- Filter --}}
      <div class="mb-4 card card-default shadow-sm border-0">
        <div class="card-body">
          <form action="{{ route('owner.user-owner.stocks.index') }}" method="GET" class="row align-items-end">

            <div class="col-md-4">
              <div class="form-group mb-0">
                <label for="filter_location" class="fw-600">{{ __('messages.owner.products.stocks.view_stock_by_location') }}:</label>
                <select class="form-control" id="filter_location" name="filter_location" onchange="this.form.submit()">

                  {{-- Opsi Default: Gudang Owner --}}
                  <option value="owner" {{ $filterLocation == 'owner' ? 'selected' : '' }}>
                    {{ __('messages.owner.products.stocks.owner_warehouse') }}
                  </option>

                  {{-- Looping Partner/Outlet --}}
                  @foreach ($partners as $partner)
                    <option value="{{ $partner->id }}" {{ $filterLocation == $partner->id ? 'selected' : '' }}>
                      {{ $partner->name }} (Outlet)
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
          </form>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      {{-- Tabel dari partial --}}
      @include('pages.owner.products.stock.display')
    </div>
  </section>

  {{-- Brand styling --}}
  <style>
    /* ===== Owner › Promotions (page scope) ===== */
    .owner-stocks {
      --choco: #8c1000;
      --soft-choco: #c12814;
      --ink: #22272b;
      --radius: 12px;
      --shadow: 0 6px 20px rgba(0, 0, 0, .08);
    }

    /* Brand buttons */
    .owner-stocks .btn-primary {
      background: var(--choco);
      border-color: var(--choco);
    }

    .owner-stocks .btn-primary:hover {
      background: var(--soft-choco);
      border-color: var(--soft-choco);
    }

    /* Filter pills: outline saat idle, filled saat active */
    .owner-stocks .filter-btn {
      border: 1px solid var(--choco) !important;
      color: var(--choco) !important;
      border-radius: 999px;
      padding: .25rem .75rem;
      transition: all .15s ease;
    }

    .owner-stocks .filter-btn:hover {
      background: rgba(140, 16, 0, .06) !important;
      color: var(--choco) !important;
      border-color: var(--choco) !important;
    }

    .owner-stocks .filter-btn.active {
      background: var(--choco) !important;
      color: #fff !important;
      border-color: var(--choco) !important;
      box-shadow: 0 2px 8px rgba(140, 16, 0, .18);
    }

    /* Alerts */
    .owner-stocks .alert {
      border-left: 4px solid var(--choco);
      border-radius: 10px;
    }

    /* Table container (partial-friendly) */
    .owner-stocks .table-responsive {
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow-y: hidden;
      background: #fff;
    }

    .owner-stocks .table {
      margin-bottom: 0;
      background: #fff;
      border-collapse: separate;
      border-spacing: 0;
    }

    .owner-stocks thead th {
      background: #fff;
      border-bottom: 2px solid #eef1f4 !important;
      color: #374151;
      font-weight: 700;
      white-space: nowrap;
    }

    .owner-stocks tbody td {
      vertical-align: middle;
    }

    .owner-stocks tbody tr {
      transition: background-color .12s ease;
    }

    .owner-stocks tbody tr:hover {
      background: rgba(140, 16, 0, .04);
    }

    /* Soft badges (kalau partial menampilkan badge) */
    .owner-stocks .badge-soft-success {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
      padding: .32rem .55rem;
      border-radius: 999px;
      font-weight: 600;
    }

    .owner-stocks .badge-soft-warning {
      background: #fef3c7;
      color: #92400e;
      border: 1px solid #fde68a;
      padding: .32rem .55rem;
      border-radius: 999px;
      font-weight: 600;
    }

    .owner-stocks .badge-soft-secondary {
      background: #f3f4f6;
      color: #374151;
      border: 1px solid #e5e7eb;
      padding: .32rem .55rem;
      border-radius: 999px;
      font-weight: 600;
    }

    /* Action buttons kecil */
    .owner-stocks .btn-group-sm .btn,
    .owner-stocks .table .btn.btn-sm {
      border-radius: 10px;
      padding: .28rem .6rem;
      min-width: 68px;
    }

    /* Soft danger untuk delete */
    .owner-stocks .btn-soft-danger {
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    .owner-stocks .btn-soft-danger:hover {
      background: #fecaca;
      color: #7f1d1d;
      border-color: #fca5a5;
    }
  </style>

  {{-- Delete confirm + filter logic --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // function deletePromo(productId) {
    //   Swal.fire({
    //     title: '{{ __('messages.owner.products.stocks.delete_confirmation_1') }}',
    //     text: "{{ __('messages.owner.products.stocks.delete_confirmation_2') }}",
    //     icon: 'warning',
    //     showCancelButton: true,
    //     confirmButtonColor: '#8c1000', // brand choco
    //     cancelButtonColor: '#6b7280',
    //     confirmButtonText: '{{ __('messages.owner.products.stocks.delete_confirmation_3') }}',
    //     cancelButtonText: '{{ __('messages.owner.products.stocks.cancel') }}',
    //     reverseButtons: true
    //   }).then((result) => {
    //     if (result.isConfirmed) {
    //       const form = document.createElement('form');
    //       form.method = 'POST';
    //       form.action = `/owner/user-owner/stocks/${productId}`;
    //       form.style.display = 'none';

    //       const csrf = document.createElement('input');
    //       csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
    //       form.appendChild(csrf);

    //       const method = document.createElement('input');
    //       method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
    //       form.appendChild(method);

    //       document.body.appendChild(form);
    //       form.submit();
    //     }
    //   });
    // }

    document.addEventListener('DOMContentLoaded', function () {
      const filterButtons = document.querySelectorAll('.owner-stocks .filter-btn');

      filterButtons.forEach(button => {
        button.addEventListener('click', function () {
          const categoryId = this.getAttribute('data-category');

          // toggle active pill
          filterButtons.forEach(btn => btn.classList.remove('active'));
          this.classList.add('active');

          const tableBody = document.querySelector('.owner-stocks tbody');
          const tableRows = document.querySelectorAll('.owner-stocks tbody tr');

          let visibleCount = 0;

          tableRows.forEach((row) => {
            if (categoryId === 'all' || row.getAttribute('data-category') === categoryId) {
              row.style.display = '';
              const firstCell = row.querySelector('td');
              if (firstCell) firstCell.textContent = (++visibleCount);
            } else {
              row.style.display = 'none';
            }
          });

          // kosong state
          const emptyRow = tableBody?.querySelector('.empty-row');
          if (emptyRow) emptyRow.remove();

          if (visibleCount === 0 && tableBody) {
            const tr = document.createElement('tr');
            tr.classList.add('empty-row');
            tr.innerHTML = `<td colspan="8" class="text-center text-muted">{{ __('messages.owner.products.stocks.data_not_found') }}</td>`;
            tableBody.appendChild(tr);
          }
        });
      });
    });
  </script>
@endsection