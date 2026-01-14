@php
  use Illuminate\Support\Str;
@endphp

<!-- Table Card -->
<div class="modern-card">
  <div class="data-table-wrapper">
    <table class="data-table">
      <thead>
        <tr>
          <th class="text-center" style="width: 60px;">#</th>
          <th>{{ __('messages.partner.outlet.table_management.tables.table_no') }}</th>
          <th>{{ __('messages.partner.outlet.table_management.tables.class_type') }}</th>
          <th>{{ __('messages.partner.outlet.table_management.tables.description') }}</th>
          <th class="text-center">{{ __('messages.partner.outlet.table_management.tables.status') }}</th>
          <th class="text-center">{{ __('messages.partner.outlet.table_management.tables.picture') }}</th>
          <th class="text-center">
            Barcode
            <a href="{{ route('partner.store.tables.generate-all-barcode') }}" 
              target="_blank"
              class="table-link"
              title="Download All Barcodes (PDF)">
              <span class="material-symbols-outlined" style="font-size: 1.25rem; vertical-align: middle;">picture_as_pdf</span>
            </a>
          </th>
          <th class="text-center" style="width: 180px;">
            {{ __('messages.partner.outlet.table_management.tables.actions') }}
          </th>
        </tr>
      </thead>
      <tbody id="tableTableBody">
        @forelse ($tables as $index => $table)
          @php
            $status = strtoupper((string) $table->status);
          @endphp
          <tr data-category="{{ $table->table_class }}" class="table-row">
            <!-- Number -->
            <td class="text-center text-muted">{{ $tables->firstItem() + $index }}</td>

            <!-- Table Number -->
            <td>
              <div class="cell-with-icon">
                <span class="fw-600">{{ $table->table_no }}</span>
              </div>
            </td>

            <!-- Class Type -->
            <td>
              <span class="text-secondary">
                {{ $table->table_class }}
              </span>
            </td>

            <!-- Description -->
            <td>
              <span class="text-secondary">{{ $table->description ?: '-' }}</span>
            </td>

            <!-- Status -->
            <td class="text-center">
              @if ($table->status === 'available')
                <span class="badge-modern badge-success">
                  {{ __('messages.partner.outlet.table_management.tables.available') }}
                </span>
              @elseif ($table->status === 'occupied')
                <span class="badge-modern badge-warning">
                  {{ __('messages.partner.outlet.table_management.tables.occupied') }}
                </span>
              @elseif ($table->status === 'reserved')
                <span class="badge-modern badge-info">
                  {{ __('messages.partner.outlet.table_management.tables.reserved') }}
                </span>
              @elseif($table->status === 'not_available')
                <span class="badge-modern badge-danger">
                  {{ __('messages.partner.outlet.table_management.tables.not_available') }}
                </span>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>

            <!-- Picture -->
            <td class="text-center">
              @if(!empty($table->images))
                @php 
                  // Handle both old format (single object) and new format (array of objects)
                  $images = is_array($table->images) ? $table->images : [$table->images];
                  
                  // Filter out non-array items (in case of string or other types)
                  $images = array_filter($images, function($img) {
                      return is_array($img) && isset($img['path']);
                  });
                @endphp
                
                @if(count($images) > 0)
                  <div class="table-images-cell">
                    @foreach($images as $image)
                      @php 
                        $src = asset($image['path']); 
                      @endphp
                      <a href="{{ $src }}" target="_blank" rel="noopener" class="table-image-link">
                        <img src="{{ $src }}" alt="{{ $image['filename'] ?? 'Table' }}" class="table-thumbnail" loading="lazy">
                      </a>
                    @endforeach
                  </div>
                @else
                  <span class="text-muted">{{ __('messages.partner.outlet.table_management.tables.no_images') }}</span>
                @endif
              @else
                <span class="text-muted">{{ __('messages.partner.outlet.table_management.tables.no_images') }}</span>
              @endif
            </td>

            <!-- Barcode -->
            <td class="text-center">
              <button onclick="generateBarcode({{ $table->id }})" 
                class="btn-table-action primary" 
                title="{{ __('messages.partner.outlet.table_management.tables.table_barcode') }}">
                <span class="material-symbols-outlined">qr_code</span>
              </button>
            </td>

            <!-- Actions -->
            <td class="text-center">
              <div class="table-actions">
                <a href="{{ route('partner.store.tables.show', $table->id) }}"
                  class="btn-table-action view"
                  title="Detail">
                  <span class="material-symbols-outlined">visibility</span>
                </a>
                <a href="{{ route('partner.store.tables.edit', $table->id) }}"
                  class="btn-table-action edit"
                  title="{{ __('messages.partner.outlet.table_management.tables.edit') }}">
                  <span class="material-symbols-outlined">edit</span>
                </a>
                <button onclick="deleteTable({{ $table->id }})" 
                  class="btn-table-action delete"
                  title="{{ __('messages.partner.outlet.table_management.tables.delete') }}">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center">
              <div class="table-empty-state">
                <span class="material-symbols-outlined">table_restaurant</span>
                <h4>{{ __('messages.partner.outlet.table_management.tables.no_tables') ?? 'No tables found' }}</h4>
                <p>{{ __('messages.partner.outlet.table_management.tables.add_first_table') ?? 'Add your first table to get started' }}</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  @if($tables->hasPages())
    <div class="table-pagination">
      {{ $tables->withQueryString()->links() }}
    </div>
  @endif
</div>

<style>
/* Table Images Cell */
.table-images-cell {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  flex-wrap: wrap;
}

.table-image-link {
  display: block;
  transition: transform 0.15s ease;
}

.table-image-link:hover {
  transform: scale(1.05);
}

.table-thumbnail {
  width: 56px;
  height: 56px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid #e5e7eb;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* Primary button action style */
.btn-table-action.primary {
  background: rgba(140, 16, 0, 0.1);
  color: var(--choco, #8c1000);
}

.btn-table-action.primary:hover {
  background: var(--choco, #8c1000);
  color: #fff;
}

/* Override badge warning untuk occupied */
.badge-modern.badge-warning {
  background: #fff7ed;
  color: #9a3412;
  border: 1px solid #fed7aa;
}
</style>

@push('scripts')
<script>
// Delete Table Function
function deleteTable(tableId) {
  const swal = window.$swal || window.Swal;
  swal.fire({
    title: '{{ __('messages.partner.outlet.table_management.tables.delete_confirm_1') }}',
    text: '{{ __('messages.partner.outlet.table_management.tables.delete_confirm_2') }}',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ae1504',
    cancelButtonColor: '#6c757d',
    confirmButtonText: '{{ __('messages.partner.outlet.table_management.tables.delete_confirm_3') }}',
    cancelButtonText: '{{ __('messages.partner.outlet.table_management.tables.delete_confirm_4') }}'
  }).then((result) => {
    if (!result.isConfirmed) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/partner/store/tables/${tableId}`;
    form.style.display = 'none';
    form.innerHTML = `
      @csrf
      <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
  });
}

// Generate Barcode Function
function generateBarcode(tableId) {
  axios.get(`/partner/store/tables/generate-barcode/${tableId}`, { responseType: 'blob' })
  .then(res => {
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `barcode-table-${tableId}.png`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  })
  .catch(err => {
    console.error('Gagal generate barcode:', err);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to generate barcode'
    });
  });
}
</script>
@endpush