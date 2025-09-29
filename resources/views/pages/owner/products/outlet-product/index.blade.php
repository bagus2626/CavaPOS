@extends('layouts.owner')

@section('title', 'Product List')
@section('page_title', 'Outlet Products')

@section('content')

<section class="content">
    <div class="container-fluid">

        {{-- ====== OUTLET PILLS (TOP) ====== --}}
        <div class="mb-3">
            <div class="d-flex flex-wrap gap-2">
                {{-- Opsional: tombol "All Outlets" --}}
                <button class="btn btn-outline-dark btn-sm rounded-pill outlet-pill active"
                        data-outlet="all">All Outlets</button>

                @foreach($outlets as $o)
                    <button class="btn btn-outline-primary btn-sm rounded-pill outlet-pill"
                            data-outlet="{{ $o->id }}">
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

            <div class="outlet-block card border-0 shadow-sm mb-4"
                 id="outlet-block-{{ $o->id }}"
                 data-outlet="{{ $o->id }}"
                 style="{{ $index === 0 ? '' : 'display:none' }}">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        {{ $o->name }}
                        <small class="text-muted">— {{ $rows->count() }} items</small>
                    </h5>
                    <button class="btn btn-primary btn-add-product"
                            data-toggle="modal"
                            data-target="#addProductModal"
                            data-outlet="{{ $o->id }}">
                        Add Product
                    </button>
                </div>

                <div class="card-body">

                    {{-- Category filter (per outlet) --}}
                    <div class="mb-3">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-secondary btn-sm rounded-pill category-pill active"
                                    data-category="all">All</button>
                            @foreach($categories as $c)
                                <button class="btn btn-outline-secondary btn-sm rounded-pill category-pill"
                                        data-category="{{ $c->id }}">
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
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Promo</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse($rows as $p)
                                    <tr data-outlet="{{ $o->id }}"
                                        data-category="{{ $p->category_id }}">
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $p->name ?? $p->product_name }}</td>
                                        <td>{{ $p->category->category_name ?? '-' }}</td>
                                        <td>
                                          @if($p->always_available_flag === 1)
                                            <span class="text-muted">Always Available</span>
                                          @else
                                            {{ $p->quantity ?? 0 }}
                                          @endif
                                        </td>
                                        <td>
                                            @php
                                                $active = (int)($p->is_active ?? 1);
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
                                               class="btn btn-sm btn-warning">Edit</a>
                                            <button class="btn btn-sm btn-danger" onclick="deleteProduct({{ $p->id }})">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="6" class="text-center text-muted">Belum ada produk</td>
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
@endsection

@push('styles')
<style>
  /* Grid container untuk kotak checkbox */
  #qp_master_product_box.mp-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: .5rem;
  }

  /* Tile/label yang clickable */
  .mp-item {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .6rem .8rem;
    border: 1px solid #e5e7eb;            /* gray-200 */
    border-radius: 9999px;                 /* pill */
    background: #fff;
    cursor: pointer;
    transition: box-shadow .15s ease, border-color .15s ease, background-color .15s ease;
    user-select: none;
  }
  .mp-item:hover {
    border-color: #cfe2ff;                 /* soft primary */
    box-shadow: 0 2px 10px rgba(13,110,253,.10);
  }

  /* Checkbox custom: bulat */
  .mp-check {
    appearance: none;
    -webkit-appearance: none;
    width: 1.15rem;
    height: 1.15rem;
    border: 2px solid #6c757d;             /* bootstrap secondary */
    border-radius: 9999px;
    display: inline-grid;
    place-content: center;
    outline: none;
    transition: border-color .12s ease, background-color .12s ease;
    background: #fff;
    flex: 0 0 auto;
  }
  .mp-check::before {
    content: "";
    width: .6rem;
    height: .6rem;
    border-radius: 9999px;
    transform: scale(0);
    transition: transform .12s ease;
    background: #0d6efd;                   /* bootstrap primary */
  }
  .mp-check:checked {
    border-color: #0d6efd;
    background: #fff;
  }
  .mp-check:checked::before {
    transform: scale(1);
  }
  .mp-check:focus-visible {
    outline: 2px solid rgba(13,110,253,.35);
    outline-offset: 2px;
  }

  /* Teks & thumb */
  .mp-text {
    line-height: 1.2;
    color: #111827;                         /* gray-900 */
    font-weight: 500;
  }
  .mp-thumb {
    width: 28px;
    height: 28px;
    border-radius: 9999px;
    object-fit: cover;
    border: 1px solid #e5e7eb;
    background: #f8f9fa;
    flex: 0 0 auto;
  }

  /* Select All baris kecil */
  #qp_check_all { transform: translateY(1px); }
</style>
@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    const rows      = block.querySelectorAll('tbody tr');
    const catPills  = block.querySelectorAll('.category-pill');

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
          tr.innerHTML = `<td colspan="6" class="text-center">Data tidak ditemukan</td>`;
          tableBody.appendChild(tr);
        }
      });
    });
  });
});
</script>

<script>
$(function () {
  const $modal  = $('#addProductModal');
  const $form   = $('#outletProductQuickAddForm');
  const $outlet = $('#qp_outlet_id');
  const $cat    = $('#qp_category_id');

  // elemen baru untuk checkbox list
  const $mpBox      = $('#qp_master_product_box');
  const $mpSelectAll= $('#qp_check_all');
  const $mpError    = $('#qp_mp_error');

  const $qty    = $('#qp_quantity');
  const $status = $('#qp_is_active');

  // cegah autofill
  $form.attr('autocomplete', 'off');
  $form.find('input, select').attr('autocomplete', 'off');

  function hardResetFields({ keepOutlet = true } = {}) {
    if ($form[0]) $form[0].reset();

    // reset kategori
    $cat.val(''); // kalau pakai select2, pakai $cat.val(null).trigger('change.select2')

    // reset box checkbox
    $mpBox.html('<div class="text-muted small">Select a category first.</div>');
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
      $mpBox.html('<div class="text-muted small">No master products found for this filter.</div>');
      return;
    }

    const frag = $(document.createDocumentFragment());
    items.forEach(item => {
      const id    = String(item.id);
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

    const outletId = $(this).data('outlet') || '';

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
    $mpBox.html('<div class="text-muted small">Loading…</div>');
    $mpSelectAll.prop('disabled', true).prop('checked', false);
    $mpError.hide();

    try {
      const url = new URL("{{ route('owner.user-owner.outlet-products.get-master-products') }}", window.location.origin);

      // ALWAYS send category_id (use 'all' if that's the selected value)
      url.searchParams.set('category_id', categoryId || 'all');

      // ALWAYS send outlet_id
      if (outletId) url.searchParams.set('outlet_id', outletId);

      const res  = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
      const data = await res.json();

      renderMasterProductCheckboxes(data);
    } catch {
      $mpBox.html('<div class="text-danger small">Failed to load master products.</div>');
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
            title: 'Yakin ingin menghapus produk ini?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
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
                    title: 'Berhasil!',
                    text: 'Produk berhasil dihapus.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
                location.reload();
            } else {
                const data = await res.json();
                Swal.fire({
                    title: 'Gagal!',
                    text: data.message || res.statusText,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        } catch (err) {
            console.error(err);
            Swal.fire({
                title: 'Kesalahan!',
                text: 'Terjadi kesalahan saat menghapus.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }
</script>



@endpush
