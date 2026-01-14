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
        <a href="{{ route('partner.store.tables.index') }}" class="back-button">
           <span class="material-symbols-outlined">arrow_back</span>
           {{ __('messages.partner.outlet.table_management.tables.back_to_tables') }}
        </a>
      </div>

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

            <div class="detail-info-group">
              {{-- Table Class --}}
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.partner.outlet.table_management.tables.class_type') }}
                </div>
                <div class="detail-info-value">{{ $table->table_class ?? '—' }}</div>
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
      @if($table->table_code)
        <div class="modern-card">
          <div class="card-body-modern">

            <div class="section-header">
              <div class="section-icon section-icon-red">
                <span class="material-symbols-outlined">qr_code_2</span>
              </div>
              <h3 class="section-title">{{ __('messages.partner.outlet.table_management.tables.qr_code') ?? 'QR Code' }}</h3>
            </div>

            <div class="qr-code-display">
              <div class="qr-frame">
                <div class="qr-frame-inner">
                  <img src="{{ route('partner.store.tables.generate-barcode', $table->id) }}" 
                       alt="QR Code for Table {{ $table->table_no }}"
                       class="qr-image">
                </div>
              </div>
            </div>

          </div>
        </div>
      @endif

  <style>
    /* QR Code Display */
    .qr-code-display {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem 1rem;
      min-height: 400px;
    }

    .qr-frame-inner {
      position: relative;
      padding: 2rem;
      background: #ffffff;
      border-radius: 16px;
      border: 4px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
      overflow: hidden;
    }

    .qr-image {
      position: relative;
      width: 320px;
      height: 320px;
      display: block;
      image-rendering: pixelated;
      image-rendering: -moz-crisp-edges;
      image-rendering: crisp-edges;
      border-radius: 8px;
      z-index: 1;
    }

    /* Corner Accents */
    .qr-frame-inner::after {
      content: '';
      position: absolute;
      width: 40px;
      height: 40px;
      border: 5px solid #ae1504;
      border-right: none;
      border-bottom: none;
      top: 10px;
      left: 10px;
      border-radius: 8px 0 0 0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .qr-code-display {
        padding: 1.5rem 1rem;
        min-height: 350px;
      }

      .qr-frame {
        padding: 1.5rem;
      }

      .qr-frame-inner {
        padding: 1.5rem;
      }

      .qr-image {
        width: 260px;
        height: 260px;
      }
    }

    @media (max-width: 480px) {
      .qr-code-display {
        padding: 1rem;
        min-height: 300px;
      }

      .qr-frame {
        padding: 1.25rem;
        border-radius: 20px;
      }

      .qr-frame::before {
        border-radius: 22px;
      }

      .qr-frame-inner {
        padding: 1.25rem;
        border-radius: 12px;
      }

      .qr-image {
        width: 200px;
        height: 200px;
      }

      .qr-frame-inner::after {
        width: 30px;
        height: 30px;
        border-width: 2px;
      }
    }

    /* Print Optimization */
    @media print {
      .qr-frame {
        box-shadow: none !important;
        background: white !important;
        animation: none !important;
      }

      .qr-frame::before,
      .qr-frame::after,
      .qr-frame-inner::before,
      .qr-frame-inner::after {
        display: none !important;
      }

      .qr-frame-inner {
        box-shadow: none !important;
      }
    }
  </style>
@endsection