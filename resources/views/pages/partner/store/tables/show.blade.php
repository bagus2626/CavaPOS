@extends('layouts.partner')

@section('title', __('messages.partner.outlet.table_management.tables.detail_table'))
@section('page_title', __('messages.partner.outlet.table_management.tables.detail_table'))

@section('content')
  @php
    use Illuminate\Support\Str;

    // Data tabel
    $table = $data;

    // Status badge
    $status = strtolower($table->status);
    $statusConfig = [
        'available' => ['class' => 'badge-success', 'label' => __('messages.partner.outlet.table_management.tables.available')],
        'occupied' => ['class' => 'badge-danger', 'label' => __('messages.partner.outlet.table_management.tables.occupied')],
        'reserved' => ['class' => 'badge-warning', 'label' => __('messages.partner.outlet.table_management.tables.reserved')],
    ];
    $statusInfo = $statusConfig[$status] ?? ['class' => 'badge-secondary', 'label' => ucfirst($table->status)];

    // Gambar utama
    $firstImg = null;
    if (!empty($table->images) && is_array($table->images)) {
        $first = $table->images[0] ?? null;
        if ($first && !empty($first['path'])) {
            $firstImg = Str::startsWith($first['path'], ['http://', 'https://']) ? $first['path'] : asset($first['path']);
        }
    }
  @endphp

  <div class="modern-container">
    <div class="container-modern">

      {{-- Page Header --}}
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.partner.outlet.table_management.tables.detail_table') }}</h1>
          <p class="page-subtitle">{{ __('messages.partner.outlet.table_management.tables.view_complete_information') }}</p>
        </div>
      </div>

      {{-- Success Message --}}
      {{-- @if (session('success'))
        <div class="alert alert-success alert-modern">
          <div class="alert-icon">
            <span class="material-symbols-outlined">check_circle</span>
          </div>
          <div class="alert-content">
            {{ session('success') }}
          </div>
        </div>
      @endif --}}

      {{-- Error Message --}}
      {{-- @if (session('error'))
        <div class="alert alert-danger alert-modern">
          <div class="alert-icon">
            <span class="material-symbols-outlined">error</span>
          </div>
          <div class="alert-content">
            {{ session('error') }}
          </div>
        </div>
      @endif --}}

      {{-- Hero Card --}}
      <div class="modern-card">
        <div class="detail-hero-header">
          {{-- Table Image --}}
          <div class="detail-avatar">
            @if($firstImg)
              <img src="{{ $firstImg }}" alt="Table {{ $table->table_no }}" class="detail-avatar-image">
            @else
              <div class="detail-avatar-placeholder">
                <span class="material-symbols-outlined">table_restaurant</span>
              </div>
            @endif
          </div>

          {{-- Hero Info --}}
          <div class="detail-hero-info">
            <h3 class="detail-hero-name">{{ __('messages.partner.outlet.table_management.tables.table_no') }} {{ $table->table_no }}</h3>
            <p class="detail-hero-subtitle">
              {{ $table->table_code }}
            </p>
            <div class="detail-hero-badges">
              <span class="badge-modern badge-info">
                {{ $table->table_class }}
              </span>
              <span class="badge-modern {{ $statusInfo['class'] }}">
                {{ $statusInfo['label'] }}
              </span>
            </div>
          </div>
        </div>

        {{-- Gallery Thumbnails --}}
        @if(!empty($table->images) && is_array($table->images) && count($table->images) > 1)
          <div class="detail-gallery">
            @foreach($table->images as $img)
              @php
                $src = !empty($img['path'])
                    ? (Str::startsWith($img['path'], ['http://', 'https://']) ? $img['path'] : asset($img['path']))
                    : null;
              @endphp
              @if($src)
                <a href="{{ $src }}" target="_blank" rel="noopener" class="gallery-item">
                  <img src="{{ $src }}" alt="{{ $img['filename'] ?? 'Table Image' }}">
                </a>
              @endif
            @endforeach
          </div>
        @endif
      </div>

      {{-- Body Card --}}
      <div class="modern-card">
        <div class="card-body-modern">

          {{-- Table Information Section --}}
          <div class="section-header">
            <div class="section-icon section-icon-red">
              <span class="material-symbols-outlined">table_restaurant</span>
            </div>
            <h3 class="section-title">{{ __('messages.partner.outlet.table_management.tables.table_information') ?? 'Table Information' }}</h3>
          </div>

          <div class="detail-info-grid">
            <div class="detail-info-group">
              {{-- Table Number --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.outlet.table_management.tables.table_no') }}
                </div>
                <div class="detail-info-value">{{ $table->table_no ?? '—' }}</div>
              </div>

              {{-- Table Code --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.outlet.table_management.tables.table_code') ?? 'Table Code' }}
                </div>
                <div class="detail-info-value">{{ $table->table_code ?? '—' }}</div>
              </div>

              {{-- Status --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.outlet.table_management.tables.status') }}
                </div>
                <div class="detail-info-value">
                  <span class="badge-modern {{ $statusInfo['class'] }}">
                    {{ $statusInfo['label'] }}
                  </span>
                </div>
              </div>
            </div>

            <div class="detail-info-group">
              {{-- Table Class --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.outlet.table_management.tables.class_type') }}
                </div>
                <div class="detail-info-value">{{ $table->table_class ?? '—' }}</div>
              </div>

              {{-- Table URL --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.outlet.table_management.tables.table_url') ?? 'Table URL' }}
                </div>
                <div class="detail-info-value">
                  @if($table->table_url)
                    <a href="{{ url($table->table_url) }}" target="_blank" class="text-primary">
                      {{ url($table->table_url) }}
                    </a>
                  @else
                    —
                  @endif
                </div>
              </div>

              {{-- Created At --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.outlet.table_management.tables.created_at') ?? 'Created At' }}
                </div>
                <div class="detail-info-value">
                  {{ $table->created_at ? $table->created_at->format('d M Y, H:i') : '—' }}
                </div>
              </div>
            </div>
          </div>

          {{-- Description Section --}}
          @if(!empty($table->description))
            <div class="section-divider"></div>

            <div class="section-header">
              <div class="section-icon section-icon-red">
                <span class="material-symbols-outlined">description</span>
              </div>
              <h3 class="section-title">{{ __('messages.partner.outlet.table_management.tables.description') }}</h3>
            </div>

            <div class="detail-info-item">
              <div class="detail-info-value">
                {!! nl2br(e($table->description)) !!}
              </div>
            </div>
          @endif

        </div>
      </div>

      {{-- QR Code Section --}}
      {{-- @if($table->table_code)
        <div class="modern-card">
          <div class="card-body-modern">

            <div class="section-header">
              <div class="section-icon section-icon-red">
                <span class="material-symbols-outlined">qr_code_2</span>
              </div>
              <h3 class="section-title">{{ __('messages.partner.outlet.table_management.tables.qr_code') ?? 'QR Code' }}</h3>
            </div>

            <div class="detail-info-item">
              <div class="detail-info-value text-center">
                <div class="qr-code-container">
                  <img src="{{ route('partner.store.tables.barcode', $table->id) }}" 
                       alt="QR Code for Table {{ $table->table_no }}"
                       class="qr-code-image">
                  <div class="mt-3">
                    <a href="{{ route('partner.store.tables.barcode', $table->id) }}" 
                       download="table-{{ $table->table_no }}-qr.png"
                       class="btn-modern btn-primary-modern">
                      <span class="material-symbols-outlined">download</span>
                      {{ __('messages.partner.outlet.table_management.tables.download_qr') ?? 'Download QR Code' }}
                    </a>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      @endif --}}
    </div>
  </div>

  <style>
    /* QR Code Styling */
    .qr-code-container {
      display: inline-block;
      padding: 2rem;
      background: #fff;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-md);
    }

    .qr-code-image {
      max-width: 300px;
      width: 100%;
      height: auto;
      border-radius: var(--radius-md);
    }

    /* Detail Actions */
    .detail-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .detail-actions-right {
      display: flex;
      gap: 0.75rem;
      flex-wrap: wrap;
    }

    @media (max-width: 768px) {
      .detail-actions {
        flex-direction: column;
        align-items: stretch;
      }

      .detail-actions-right {
        flex-direction: column;
      }
    }
  </style>
@endsection

{{-- @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function deleteTable(tableId) {
      Swal.fire({
        title: '{{ __('messages.partner.outlet.table_management.tables.delete_confirmation') ?? 'Are you sure?' }}',
        text: '{{ __('messages.partner.outlet.table_management.tables.delete_warning') ?? 'You won\'t be able to revert this!' }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ae1504',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('messages.partner.outlet.table_management.tables.delete_confirm') ?? 'Yes, delete it!' }}',
        cancelButtonText: '{{ __('messages.partner.outlet.table_management.tables.cancel') ?? 'Cancel' }}'
      }).then((result) => {
        if (result.isConfirmed) {
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
        }
      });
    }
  </script>
@endpush --}}