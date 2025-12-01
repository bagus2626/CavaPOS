@extends('layouts.owner')

@section('title', __('messages.owner.products.outlet_products.product_list'))
@section('page_title', __('messages.owner.products.outlet_products.outlet_products'))

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
            <button class="btn btn-sm rounded-pill outlet-pill" data-outlet="{{ $o->id }}">
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
        @php
          $rows = ($productsByOutlet[$o->id] ?? collect());
        @endphp

        <div class="outlet-block card border-0 shadow-sm mb-4" id="outlet-block-{{ $o->id }}" data-outlet="{{ $o->id }}"
          style="{{ $index === 0 ? '' : 'display:none' }}">
          <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
              {{ $o->name }}
              <small class="text-muted">— {{ $rows->count() }}
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
                  <button class="btn btn-sm rounded-pill category-pill" data-category="{{ $c->id }}">
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
                    <th>{{ __('messages.owner.products.outlet_products.promo') }}</th>
                    <th class="text-end">{{ __('messages.owner.products.outlet_products.actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @php $no = 1; @endphp
                  @forelse($rows as $p)
                    <tr data-outlet="{{ $o->id }}" data-category="{{ $p->category_id }}">
                      <td>{{ $no++ }}</td>
                      <td class="d-flex align-items-center gap-2">
                          {{-- Wrapper agar bisa tempel badge di atas gambar --}}
                          <div class="position-relative" style="width:40px; height:40px;">
                              {{-- Tampilkan gambar jika ada --}}
                              @if(!empty($p->pictures) && isset($p->pictures[0]['path']))
                                  <img src="{{ asset($p->pictures[0]['path']) }}"
                                      alt="{{ $p->name ?? $p->product_name }}"
                                      style="width:40px; height:40px; object-fit:cover; border-radius:6px;">
                              @else
                                  {{-- Placeholder tanpa gambar --}}
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

                              {{-- HOT BADGE jika produk hot --}}
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
                          {{-- Nama produk --}}
                          <span class="ml-1">{{ $p->name ?? $p->product_name }}</span>

                      </td>
                      <td>{{ $p->category->category_name ?? '-' }}</td>
                      {{-- <td>
                        @if($p->always_available_flag === 1)
                        <span class="text-muted">{{ __('messages.owner.products.outlet_products.always_available') }}</span>
                        @else
                        {{ $p->quantity ?? 0 }}
                        @endif
                      </td> --}}

                      <td>
                        @php
                          $qtyAvailable = $p->quantity_available;
                          $isQtyZero = $qtyAvailable < 1 && $qtyAvailable !== 999999999;
                        @endphp

                        @if($p->stock_type == 'linked')
                          @if ($qtyAvailable === 999999999)
                            <span class="text-muted"
                              style="font-style: italic;">{{ __('messages.owner.products.outlet_products.always_available') }}</span>
                          @elseif ($isQtyZero)
                            {{-- Linked Stock: Bahan Habis --}}
                            <span class="badge bg-danger" title="Bahan baku tidak mencukupi!">Bahan Habis</span>
                          @else
                            {{-- Linked Stock: Tampilkan Porsi (Menggunakan floor karena porsi biasanya bulat) --}}
                            <strong>{{ number_format(floor($qtyAvailable), 0) }}</strong>
                            <span class="text-muted small">pcs</span>
                          @endif

                        @elseif((int) $p->always_available_flag === 1)
                          {{-- Direct Stock: Always Available --}}
                          <span class="text-muted">{{ __('messages.owner.products.outlet_products.always_available') }}</span>

                        @elseif($p->stock)
                          {{-- Direct Stock: Tampilkan Kuantitas Terkonversi + Unit Name --}}
                          @if ($isQtyZero)
                            <span class="badge bg-danger" title="Stok 0 atau belum diisi!">0</span>
                          @else
                            {{-- Tampilkan kuantitas terkonversi --}}
                            <strong>
                              {{ rtrim(rtrim(number_format($qtyAvailable, 2, ',', '.'), '0'), ',') }}
                            </strong>
                            {{-- Tampilkan unit name dari relasi stok --}}
                            <span class="text-muted small">{{ $p->stock->displayUnit->unit_name ?? 'unit' }}</span>
                          @endif

                        @else
                          <span class="badge bg-danger" title="Stok tidak ditemukan!">0</span>
                        @endif
                      </td>

                      <td>
                        @php
                          $active = (int) ($p->is_active ?? 1);
                        @endphp
                        <span class="badge bg-{{ $active ? 'success' : 'secondary' }}">
                          {{ $active ? 'Active' : 'Inactive' }}
                        </span>
                      </td>
                      <td>
                        @if($p->promotion)
                          <span class="badge bg-warning">
                            {{ $p->promotion->promotion_name }}
                          </span>
                        @else
                          <span class="text-muted">—</span>
                        @endif
                      </td>
                      <td class="text-end">
                        <a href="{{ route('owner.user-owner.outlet-products.edit', $p->id) }}"
                          class="btn btn-outline-dark btn-sm">{{ __('messages.owner.products.outlet_products.edit') }}</a>
                        <button class="btn btn-primary btn-sm" onclick="deleteProduct({{ $p->id }})">
                          {{ __('messages.owner.products.outlet_products.delete') }}
                        </button>
                      </td>
                    </tr>
                  @empty
                    <tr class="empty-row">
                      <td colspan="7" class="text-center text-muted">
                        {{ __('messages.owner.products.outlet_products.no_product_yet') }}
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
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
  </style>
@endsection


@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // ===== Outlet switch (pills paling atas) =====
      const outletPills = document.querySelectorAll('.outlet-pill');
      const blocks = document.querySelectorAll('.outlet-block');

      function showOutlet(outletId) {
        if (outletId === 'all') {
          blocks.forEach(b => b.style.display = '');
        } else {
          blocks.forEach(b => {
            b.style.display = (b.dataset.outlet === outletId) ? '' : 'none';
          });
        }
      }

      outletPills.forEach(btn => {
        btn.addEventListener('click', () => {
          outletPills.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          showOutlet(btn.dataset.outlet);
        });
      });

      // default: tampilkan semua atau yang pertama? (sekarang: “All Outlets” aktif)
      showOutlet('all');

      // ===== Filter kategori per outlet (tiap blok punya pill-nya sendiri) =====
      document.querySelectorAll('.outlet-block').forEach(block => {
        const tableBody = block.querySelector('tbody');
        const rows = block.querySelectorAll('tbody tr');
        const catPills = block.querySelectorAll('.category-pill');

        catPills.forEach(pill => {
          pill.addEventListener('click', () => {
            // aktifkan pill yg diklik
            catPills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');

            const categoryId = pill.dataset.category;
            let visibleCount = 0;

            // bersihkan row kosong lama
            const emptyRow = tableBody.querySelector('.empty-row');
            if (emptyRow) emptyRow.remove();

            rows.forEach(row => {
              const rowCat = row.getAttribute('data-category');
              if (categoryId === 'all' || rowCat === categoryId) {
                row.style.display = '';
                const noCell = row.querySelector('td');
                if (noCell) noCell.textContent = (++visibleCount);
              } else {
                row.style.display = 'none';
              }
            });

            if (visibleCount === 0) {
              const tr = document.createElement('tr');
              tr.classList.add('empty-row');
              tr.innerHTML = `<td colspan="7" class="text-center">{{ __('messages.owner.products.outlet_products.data_not_found') }}</td>`;
              tableBody.appendChild(tr);
            }
          });
        });
      });
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