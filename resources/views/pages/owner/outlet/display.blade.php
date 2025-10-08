@php
  use Illuminate\Support\Str;
@endphp

<div class="table-responsive owner-outlets-table">
  <table class="table table-hover align-middle">
    <thead>
      <tr>
        <th>#</th>
        <th>{{ __('messages.owner.outlet.all_outlets.outlet_name') }}</th>
        <th>{{ __('messages.owner.outlet.all_outlets.username') }}</th>
        <th>{{ __('messages.owner.outlet.all_outlets.email') }}</th>
        <th>{{ __('messages.owner.outlet.all_outlets.logo') }}</th>
        <th>{{ __('messages.owner.outlet.all_outlets.status') }}</th>
        <th>QRIS</th>
        <th class="text-nowrap">{{ __('messages.owner.outlet.all_outlets.actions') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($outlets as $index => $outlet)
        <tr>
          <td class="text-muted">{{ $index + 1 }}</td>

          <td class="fw-600">{{ $outlet->name }}</td>

          <td class="mono">{{ $outlet->username }}</td>

          <td>
            <a href="mailto:{{ $outlet->email }}" class="link-ink">{{ $outlet->email }}</a>
          </td>

          <td class="col-photo">
            @php
              $img = $outlet->logo
                ? (Str::startsWith($outlet->logo, ['http://','https://'])
                    ? $outlet->logo
                    : asset('storage/'.$outlet->logo))
                : null;
            @endphp

            @if($img)
              <a href="{{ $img }}" target="_blank" rel="noopener">
                <img src="{{ $img }}" alt="{{ $outlet->name }}" class="avatar-48" loading="lazy">
              </a>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>

          <td class="col-status">
            @if((int) $outlet->is_active === 1)
              <span class="badge badge-soft-success d-inline-flex align-items-center gap-1">
                <i class="fas fa-check-circle"></i> {{ __('messages.owner.outlet.all_outlets.active') }}
              </span>
            @else
              <span class="badge badge-soft-secondary d-inline-flex align-items-center gap-1">
                <i class="fas fa-minus-circle"></i> {{ __('messages.owner.outlet.all_outlets.inactive') }}
              </span>
            @endif
          </td>

          <td class="col-status">
            @if((int) $outlet->is_qr_active === 1)
              <span class="badge badge-soft-success d-inline-flex align-items-center gap-1">
                <i class="fas fa-check-circle"></i> {{ __('messages.owner.outlet.all_outlets.active') }}
              </span>
            @else
              <span class="badge badge-soft-secondary d-inline-flex align-items-center gap-1">
                <i class="fas fa-minus-circle"></i> {{ __('messages.owner.outlet.all_outlets.inactive') }}
              </span>
            @endif
          </td>

          <td class="col-actions">
            <div class="action-btns d-inline-flex">
                <a href="{{ route('owner.user-owner.outlets.edit', $outlet->id) }}" class="btn btn-outline-choco mr-1">{{ __('messages.owner.outlet.all_outlets.edit') }}</a>
                <button onclick="deleteOutlet({{ $outlet->id }})" class="btn btn-soft-danger">{{ __('messages.owner.outlet.all_outlets.delete') }}</button>
            </div>
          </td>

        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<style>
/* ===== Owner › Outlets List (page scope) ===== */
.owner-outlets-table{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}
.owner-outlets-table .table{
  border-collapse: separate;
  border-spacing: 0;
  background:#fff;
  overflow:hidden;
  border-radius: var(--radius);
}
.owner-outlets-table thead th{
  background:#fff;
  border-bottom:2px solid #eef1f4 !important;
  color:#374151; font-weight:700;
  white-space: nowrap;
}
.owner-outlets-table tbody td{
  vertical-align: middle;
}
.owner-outlets-table tbody tr{
  transition: background-color .12s ease;
}
.owner-outlets-table tbody tr:hover{
  background: rgba(140,16,0,.04);
}

/* Teks & util */
.fw-600{ font-weight:600; }
.mono{ font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace; color:#374151; }
.link-ink{ color:#374151; text-decoration:none; }
.link-ink:hover{ color:var(--choco); }

/* Avatar/logo */
.owner-outlets-table .avatar-48{
  width:48px; height:48px; object-fit:cover;
  border-radius:12px; border:0; box-shadow: var(--shadow);
}

/* Badges (soft) */
.badge-soft-success{
  background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;
  padding:.32rem .55rem; border-radius:999px; font-weight:600;
}
.badge-soft-secondary{
  background:#f3f4f6; color:#374151; border:1px solid #e5e7eb;
  padding:.32rem .55rem; border-radius:999px; font-weight:600;
}

/* Actions */
.owner-outlets-table .col-actions{ white-space: nowrap; }
.owner-outlets-table .btn-group-sm .btn{
  border-radius:10px; padding:.28rem .6rem; min-width:68px;
}
.btn-outline-choco{
  color: var(--choco); border-color: var(--choco); background:#fff;
}
.btn-outline-choco:hover{
  color:#fff; background:var(--choco); border-color:var(--choco);
}
.btn-soft-danger{
  background:#fee2e2; color:#991b1b; border-color:#fecaca;
}
.btn-soft-danger:hover{
  background:#fecaca; color:#7f1d1d; border-color:#fca5a5;
}
</style>





@push('scripts')
<script>
function deleteOutlet(outletId) {
  Swal.fire({
    title: '{{ __('messages.owner.outlet.all_outlets.delete_confirmation_1') }}',
    text: '{{ __('messages.owner.outlet.all_outlets.delete_confirmation_2') }}',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: '{{ __('messages.owner.outlet.all_outlets.delete_confirmation_3') }}',
    cancelButtonText: '{{ __('messages.owner.outlet.all_outlets.cancel') }}'
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/owner/user-owner/outlets/${outletId}`;
      form.style.display = 'none';
      form.innerHTML = `
        @csrf
        <input type="hidden" name="_method" value="DELETE">
      `;
      document.body.appendChild(form);
      form.submit();
    }
  });
}
</script>
<script>
function generateBarcode(tableId) {
    axios.get(`/partner/store/tables/generate-barcode/${tableId}`, {
        responseType: 'blob' // supaya bisa download image barcode
    })
    .then(response => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `barcode-table-${tableId}.png`);
        document.body.appendChild(link);
        link.click();
    })
    .catch(error => {
        console.error('Generate barcode failed:', error);
    });
}

</script>
@endpush
