@extends('layouts.owner')

@section('title', __('messages.owner.products.outlet_products.product_list'))
@section('page_title', __('messages.owner.products.outlet_products.outlet_products'))

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        // ===== Outlet switch (pills paling atas) =====
        const outletPills = document.querySelectorAll('.outlet-pill');
        const blocks      = document.querySelectorAll('.outlet-block');

        const categoryState = {};

        function showOutlet(outletId) {
            if (outletId === 'all') {
                blocks.forEach(b => b.style.display = '');
            } else {
                blocks.forEach(b => {
                    b.style.display = (b.dataset.outlet === outletId) ? '' : 'none';
                });
            }
        }

        // ==== AJAX loader untuk produk per outlet + kategori + halaman ====
        async function loadOutletProducts(outletId, categoryId = 'all', page = 1) {
            const tbody = document.getElementById(`outlet-table-body-${outletId}`);
            const pager = document.getElementById(`outlet-pagination-${outletId}`);

            if (!tbody) return;

            tbody.innerHTML = `
                <tr>
                  <td colspan="8" class="text-center text-muted py-3">
                    <i class="fas fa-spinner fa-spin"></i> Loading...
                  </td>
                </tr>`;

            const url = new URL("{{ route('owner.user-owner.outlet-products.list') }}", window.location.origin);
            url.searchParams.set('outlet_id', outletId);
            url.searchParams.set('category_id', categoryId || 'all');
            url.searchParams.set('page', page);

            const res  = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            // console.log(data.html);

            // Pakai <table> sebagai wrapper agar <tbody> diparse dengan benar
            const wrapper = document.createElement('table');
            wrapper.innerHTML = data.html.trim();

            const newTbody = wrapper.querySelector('tbody');
            const newPager = wrapper.querySelector('.pagination-wrapper');

            tbody.innerHTML = newTbody ? newTbody.innerHTML : '';

            if (pager && newPager) {
              pager.innerHTML = newPager.innerHTML;

              // Ambil SEMUA link pagination
              pager.querySelectorAll('a.page-link').forEach(a => {
                  a.addEventListener('click', (e) => {
                      e.preventDefault();

                      // Ambil nomor page dari query string ?page=...
                      const urlObj   = new URL(a.href);
                      const nextPage = urlObj.searchParams.get('page') || 1;

                      const currentCat = categoryState[outletId] || 'all';
                      loadOutletProducts(outletId, currentCat, nextPage);
                  });
              });
          }
        }

        // ==== Inisialisasi per outlet-block ====
        document.querySelectorAll('.outlet-block').forEach(block => {
            const outletId = block.dataset.outlet;
            const catPills = block.querySelectorAll('.category-pill');

            // default kategori: 'all'
            categoryState[outletId] = 'all';

            // klik kategori untuk outlet ini
            catPills.forEach(pill => {
                pill.addEventListener('click', () => {
                    // set active pill di outlet tersebut
                    catPills.forEach(p => p.classList.remove('active'));
                    pill.classList.add('active');

                    const categoryId = pill.dataset.category;

                    // simpan pilihan
                    categoryState[outletId] = categoryId;

                    // load halaman 1 dengan kategori baru
                    loadOutletProducts(outletId, categoryId, 1);
                });
            });

            // pertama kali load data untuk outlet ini (kategori 'all')
            loadOutletProducts(outletId, 'all', 1);
        });

        // ==== Klik outlet pill atas ====
        outletPills.forEach(btn => {
            btn.addEventListener('click', () => {
                // styling pill
                outletPills.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const outletId = btn.dataset.outlet;
                showOutlet(outletId);

                // kalau "All Outlets" tidak perlu panggil load (sudah diload per outlet di atas)
                if (outletId === 'all') return;

                // gunakan kategori yang sudah pernah dipilih, kalau belum => 'all'
                const selectedCat = categoryState[outletId] || 'all';

                // set kategori pill aktif sesuai state
                const targetBlock = document.querySelector(`.outlet-block[data-outlet="${outletId}"]`);
                if (targetBlock) {
                    const catPills = targetBlock.querySelectorAll('.category-pill');
                    catPills.forEach(pill => {
                        pill.classList.toggle('active', pill.dataset.category === selectedCat);
                    });
                }

                // load data dengan kategori terakhir untuk outlet ini
                loadOutletProducts(outletId, selectedCat, 1);
            });
        });

        // default tampilan: "All Outlets"
        showOutlet('all');
    });

  </script>

  <script>
    $(function () {
      const $modal = $('#addProductModal');
      const $form = $('#outletProductQuickAddForm');
      const $outlet = $('#qp_outlet_id');
      const $cat = $('#qp_category_id');

      // elemen baru untuk checkbox list
      const $mpBox = $('#qp_master_product_box');
      const $mpSelectAll = $('#qp_check_all');
      const $mpError = $('#qp_mp_error');

      const $qty = $('#qp_quantity');
      const $status = $('#qp_is_active');

      // cegah autofill
      $form.attr('autocomplete', 'off');
      $form.find('input, select').attr('autocomplete', 'off');

      function hardResetFields({ keepOutlet = true } = {}) {
        if ($form[0]) $form[0].reset();

        // reset kategori
        $cat.val(''); // kalau pakai select2, pakai $cat.val(null).trigger('change.select2')

        // reset box checkbox
        $mpBox.html('<div class="text-muted small">{{ __('messages.owner.products.outlet_products.select_category_first') }}</div>');
        $mpSelectAll.prop('disabled', true).prop('checked', false);
        $mpError.hide();

        $qty.val('0');
        $status.val('1');

        if (!keepOutlet) $outlet.val('');
      }

      function getDefaultCategoryId() {
        const current = $cat.val();
        if (current) return current;
        const firstNonEmpty = $cat.find('option').filter(function () {
          return $(this).val() && $(this).val() !== '';
        }).first().val();
        return firstNonEmpty || '';
      }

      // Render checkbox list
      function renderMasterProductCheckboxes(items) {
        $mpBox.empty();
        $mpSelectAll.prop('disabled', true).prop('checked', false);

        if (!Array.isArray(items) || items.length === 0) {
          $mpBox.html('<div class="text-muted small">{{ __('messages.owner.products.outlet_products.no_master_product_filter') }}</div>');
          return;
        }

        const frag = $(document.createDocumentFragment());
        items.forEach(item => {
          const id = String(item.id);
          const label = item.name ?? ('#' + id);
          const row = $(`
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="master_product_ids[]" value="${id}" id="mp_${id}">
                <label class="form-check-label" for="mp_${id}">${label}</label>
              </div>
            `);
          frag.append(row);
        });

        $mpBox.append(frag);
        $mpSelectAll.prop('disabled', false).prop('checked', false);
      }

      // Select all toggle
      $mpSelectAll.on('change', function () {
        const checked = $(this).is(':checked');
        $mpBox.find('input[type="checkbox"][name="master_product_ids[]"]').prop('checked', checked);
      });

      // Validasi minimal 1 terpilih di sisi frontend
      $form.on('submit', function (e) {
        const anyChecked = $mpBox.find('input[name="master_product_ids[]"]:checked').length > 0;
        if (!anyChecked) {
          e.preventDefault();
          $mpError.show(); // tampilkan pesan error
          $mpBox.addClass('border-danger');
          setTimeout(() => $mpBox.removeClass('border-danger'), 1500);
        } else {
          $mpError.hide();
        }
      });

      // KLIK Add Product → Reset + set outlet + auto fetch
      $(document).on('click', '.btn-add-product', function (e) {
        e.preventDefault();

        // const outletId = $(this).data('outlet') || '';
        const outletId = $(this).attr('data-outlet') || '';


        if ($modal.hasClass('show')) {
          $modal.one('hidden.bs.modal', function () {
            hardResetFields({ keepOutlet: true });

            $outlet.val(outletId);
            const catId = getDefaultCategoryId();
            if (catId) {
              $cat.val(catId).trigger('change'); // akan memanggil loadMasterProducts
            }

            $modal.modal('show');
          });
          $modal.modal('hide');
        } else {
          hardResetFields({ keepOutlet: true });

          $outlet.val(outletId);
          const catId = getDefaultCategoryId();
          if (catId) {
            $cat.val(catId).trigger('change');
          }

          $modal.modal('show');
        }
      });

      // Tetap reset saat ditutup manual
      $modal.on('hidden.bs.modal', function () {
        hardResetFields({ keepOutlet: true });
      });

      // === FETCH: load master products (by category + outlet)
      async function loadMasterProducts(categoryId, outletId) {
        $mpBox.html('<div class="text-muted small">{{ __('messages.owner.products.outlet_products.loading') }}</div>');
        $mpSelectAll.prop('disabled', true).prop('checked', false);
        $mpError.hide();

        try {
          const url = new URL("{{ route('owner.user-owner.outlet-products.get-master-products') }}", window.location.origin);

          // ALWAYS send category_id (use 'all' if that's the selected value)
          url.searchParams.set('category_id', categoryId || 'all');

          // ALWAYS send outlet_id
          if (outletId) url.searchParams.set('outlet_id', outletId);

          const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
          const data = await res.json();
          // console.log(data.html);

          renderMasterProductCheckboxes(data);
        } catch {
          $mpBox.html('<div class="text-danger small">{{ __('messages.owner.products.outlet_products.failed_load_master_products') }}</div>');
        }
      }


      // Kategori berubah → fetch
      $cat.on('change', function () {
        loadMasterProducts(this.value, $outlet.val());
      });
    });
  </script>

  <script>
    async function deleteProduct(id) {
      console.log(id);

      // Konfirmasi dengan SweetAlert2
      const result = await Swal.fire({
        title: '{{ __('messages.owner.products.outlet_products.delete_confirmation_1') }}',
        text: "{{ __('messages.owner.products.outlet_products.delete_confirmation_2') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __('messages.owner.products.outlet_products.delete_confirmation_3') }}',
        cancelButtonText: '{{ __('messages.owner.products.outlet_products.cancel') }}'
      });

      if (!result.isConfirmed) return; // jika batal, keluar

      try {
        // Gunakan route() supaya URL sesuai dengan route name Laravel
        const url = "{{ route('owner.user-owner.outlet-products.destroy', ':id') }}".replace(':id', id);

        // _Method spoofing untuk DELETE
        const formData = new FormData();
        formData.append('_method', 'DELETE');

        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
          },
          body: formData
        });

        if (res.ok) {
          await Swal.fire({
            title: '{{ __('messages.owner.products.outlet_products.success') }}',
            text: '{{ __('messages.owner.products.outlet_products.delete_success') }}',
            icon: 'success',
            confirmButtonText: 'OK'
          });
          location.reload();
        } else {
          const data = await res.json();
          Swal.fire({
            title: '{{ __('messages.owner.products.outlet_products.failed') }}',
            text: data.message || res.statusText,
            icon: 'error',
            confirmButtonText: 'OK'
          });
        }
      } catch (err) {
        console.error(err);
        Swal.fire({
          title: '{{ __('messages.owner.products.outlet_products.error') }}',
          text: '{{ __('messages.owner.products.outlet_products.delete_error') }}',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    }
  </script>
@endpush

@section('content')

  <section class="content">
    <div class="container-fluid owner-outlet-products">

      {{-- ====== OUTLET PILLS (TOP) ====== --}}
      <div class="mb-3">
        <div class="d-flex flex-wrap gap-2">
          {{-- Opsional: tombol "All Outlets" --}}
          <button class="btn btn-sm rounded-pill outlet-pill active"
            data-outlet="all">{{ __('messages.owner.products.outlet_products.all_outlets') }}</button>

          @foreach($outlets as $o)
            <button class="btn btn-sm rounded-pill outlet-pill ml-1" data-outlet="{{ $o->id }}">
              {{ $o->name }}
            </button>
          @endforeach
        </div>
      </div>
      @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
      @endif


      {{-- ====== BLOK PER OUTLET ====== --}}
      @foreach($outlets as $index => $o)
        <div class="outlet-block card border-0 shadow-sm mb-4" id="outlet-block-{{ $o->id }}" data-outlet="{{ $o->id }}"
          style="{{ $index === 0 ? '' : 'display:none' }}">
          <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
              {{ $o->name }}
                {{ __('messages.owner.products.outlet_products.items') }}</small>
            </h5>
            <button class="btn btn-primary btn-add-product" data-toggle="modal" data-target="#addProductModal"
              data-outlet="{{ $o->id }}">
              {{ __('messages.owner.products.outlet_products.add_product') }}
            </button>
          </div>

          <div class="card-body">

            {{-- Category filter (per outlet) --}}
            <div class="mb-3">
              <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-sm rounded-pill category-pill active"
                  data-category="all">{{ __('messages.owner.products.outlet_products.all') }}</button>
                @foreach($categories as $c)
                  <button class="btn btn-sm rounded-pill category-pill ml-1" data-category="{{ $c->id }}">
                    {{ $c->category_name }}
                  </button>
                @endforeach
              </div>
            </div>

            {{-- Tabel produk untuk outlet ini --}}
            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>{{ __('messages.owner.products.outlet_products.product') }}</th>
                    <th>{{ __('messages.owner.products.outlet_products.category') }}</th>
                    <th>{{ __('messages.owner.products.outlet_products.stock') }}</th>
                    <th>{{ __('messages.owner.products.outlet_products.status') }}</th>
                    <th>{{ __('messages.owner.products.outlet_products.price') }}</th>
                    <th>{{ __('messages.owner.products.outlet_products.promo') }}</th>
                    <th class="text-end">{{ __('messages.owner.products.outlet_products.actions') }}</th>
                  </tr>
                </thead>
                <tbody id="outlet-table-body-{{ $o->id }}">
                  <tr>
                      <td colspan="8" class="text-center text-muted py-4">
                          Loading...
                      </td>
                  </tr>
              </tbody>
              </table>
              <div id="outlet-pagination-{{ $o->id }}"></div>
            </div>

          </div>
        </div>
      @endforeach

      @include('pages.owner.products.outlet-product.modal')

    </div>
  </section>

  <style>
    /* ===== Owner › Outlet Products (page scope) ===== */
    .owner-outlet-products {
      --choco: #8c1000;
      --soft-choco: #c12814;
      --ink: #22272b;
      --radius: 12px;
      --shadow: 0 6px 20px rgba(0, 0, 0, .08);
    }

    /* Cards */
    .owner-outlet-products .card {
      border: 0;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
    }

    .owner-outlet-products .card-header {
      background: #fff;
      border-bottom: 1px solid #eef1f4;
    }

    .owner-outlet-products .card-header h5 {
      color: var(--ink);
      font-weight: 700;
    }

    .owner-outlet-products .card-header small {
      font-weight: 500;
    }

    /* Brand buttons (fallback) */
    .owner-outlet-products .btn-primary {
      background: var(--choco);
      border-color: var(--choco);
    }

    .owner-outlet-products .btn-primary:hover {
      background: var(--soft-choco);
      border-color: var(--soft-choco);
    }

    .owner-outlet-products .btn-outline-dark {
      color: var(--choco);
      border-color: var(--choco);
    }

    .owner-outlet-products .btn-outline-dark:not(.outlet-pill):hover {
      color: #fff;
      background: var(--choco);
      border-color: var(--choco);
    }

    /* “Add Product” button */
    .owner-outlet-products .btn-add-product {
      border-radius: 10px;
      min-width: 140px;
    }

    /* Top outlet pills */
    /* ===== Outlet pills: outline saat idle, filled saat active ===== */
    .owner-outlet-products .outlet-pill {
      /* brand */
      --choco: #8c1000;
      /* base (idle) */
      border: 1px solid var(--choco) !important;
      color: var(--choco) !important;
      border-radius: 999px;
      padding: .25rem .75rem;
      transition: all .15s ease;
    }

    /* Hover saat idle: tipis saja */
    .owner-outlet-products .outlet-pill:hover {
      background: rgba(140, 16, 0, .06) !important;
      color: var(--choco) !important;
      border-color: var(--choco) !important;
    }

    /* Active/pressed: filled choco */
    .owner-outlet-products .outlet-pill.active,
    .owner-outlet-products .outlet-pill[aria-pressed="true"] {
      background: var(--choco) !important;
      color: #fff !important;
      border-color: var(--choco) !important;
      box-shadow: 0 2px 8px rgba(140, 16, 0, .18);
    }

    /* Matikan warna bawaan .btn-outline-primary/.btn-outline-dark pada outlet-pill */
    .owner-outlet-products .outlet-pill.btn-outline-primary,
    .owner-outlet-products .outlet-pill.btn-outline-dark {
      /* pastikan tidak kebawa palette bootstrap */
      --bs-btn-color: var(--choco);
      --bs-btn-border-color: var(--choco);
      --bs-btn-hover-color: #fff;
      --bs-btn-hover-bg: var(--choco);
      --bs-btn-hover-border-color: var(--choco);
    }


    /* Category pills per card */
    .owner-outlet-products .category-pill {
      border-radius: 999px;
      border: 1px solid #8c10008e;
      color: #8c1000;
      background: #fff;
      padding: .22rem .7rem;
      transition: all .15s ease;
    }

    .owner-outlet-products .category-pill:hover {
      border-color: var(--choco);
      color: var(--choco);
    }

    .owner-outlet-products .category-pill.active {
      background: rgba(140, 16, 0, .08);
      color: var(--choco);
      border-color: var(--choco);
    }

    /* Table */
    .owner-outlet-products .table {
      background: #fff;
      margin-bottom: 0;
    }

    .owner-outlet-products thead th {
      background: #fff;
      border-bottom: 2px solid #eef1f4 !important;
      color: #374151;
      font-weight: 700;
      white-space: nowrap;
    }

    .owner-outlet-products tbody td {
      vertical-align: middle;
    }

    .owner-outlet-products tbody tr {
      transition: background-color .12s ease;
    }

    .owner-outlet-products tbody tr:hover {
      background: rgba(140, 16, 0, .04);
    }

    /* Status & promo badges – soft */
    .owner-outlet-products .badge {
      border-radius: 999px;
      font-weight: 600;
      padding: .32rem .55rem;
    }

    .owner-outlet-products .badge.bg-success {
      background: #ecfdf5 !important;
      color: #065f46 !important;
      border: 1px solid #a7f3d0;
    }

    .owner-outlet-products .badge.bg-secondary {
      background: #f3f4f6 !important;
      color: #374151 !important;
      border: 1px solid #e5e7eb;
    }

    .owner-outlet-products .badge.bg-warning {
      background: #fef3c7 !important;
      color: #92400e !important;
      border: 1px solid #fde68a;
    }

    /* Actions */
    .owner-outlet-products td.text-end .btn {
      border-radius: 10px;
      padding: .28rem .6rem;
      min-width: 70px;
    }

    /* Empty row */
    .owner-outlet-products .empty-row td {
      background: #fafafa;
      color: #6b7280;
      font-style: italic;
    }

    /* Small helpers */
    .owner-outlet-products .text-muted {
      color: #6b7280 !important;
    }

    /* Responsive tweaks */
    @media (max-width: 576px) {
      .owner-outlet-products .card-header h5 {
        font-size: 1rem;
      }

      .owner-outlet-products .btn-add-product {
        min-width: 120px;
        padding: .3rem .6rem;
      }
    }

        /* ===== Pagination: choco style ===== */
    .owner-outlet-products .pagination-wrapper {
      margin-top: 0.75rem;
    }

    .owner-outlet-products .pagination {
      margin-bottom: 0;
      justify-content: center; /* geser ke kanan, kalau mau tengah pakai center */
      gap: 0.25rem;
    }

    .owner-outlet-products .page-item .page-link {
      border-radius: 999px;
      padding: 0.25rem 0.65rem;
      font-size: 0.8rem;
      border: 1px solid rgba(140, 16, 0, 0.25); /* choco tipis */
      color: var(--choco);
      background-color: #fff;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
      transition: all 0.15s ease;
    }

    .owner-outlet-products .page-item .page-link:hover {
      background-color: rgba(140, 16, 0, 0.06);
      color: var(--choco);
      border-color: rgba(140, 16, 0, 0.5);
    }

    /* Active page */
    .owner-outlet-products .page-item.active .page-link {
      background-color: var(--choco);
      border-color: var(--choco);
      color: #fff;
      box-shadow: 0 2px 8px rgba(140, 16, 0, 0.25);
    }

    /* Disabled (prev/next) */
    .owner-outlet-products .page-item.disabled .page-link {
      background-color: #f3f4f6;
      border-color: #e5e7eb;
      color: #9ca3af;
      box-shadow: none;
    }
    .owner-outlet-products .card {
        overflow: visible !important;
    }
  </style>
@endsection

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
