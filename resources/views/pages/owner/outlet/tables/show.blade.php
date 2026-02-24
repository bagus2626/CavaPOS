@extends('layouts.owner')

@section('title', 'Table Detail')
@section('page_title', 'Table Detail')

@section('content')
    @php
        use Illuminate\Support\Str;
        $table = $data;
        $status = strtolower($table->status);
        $statusMap = [
            'available'     => ['class' => 'badge-success', 'label' => 'Available'],
            'occupied'      => ['class' => 'badge-danger',  'label' => 'Occupied'],
            'reserved'      => ['class' => 'badge-warning', 'label' => 'Reserved'],
            'not_available' => ['class' => 'badge-danger',  'label' => 'Not Available'],
        ];
        $statusInfo = $statusMap[$status] ?? ['class' => 'badge-secondary', 'label' => ucfirst($table->status)];

        $firstImg = null;
        if (!empty($table->images) && is_array($table->images)) {
            $first = $table->images[0] ?? null;
            if ($first && !empty($first['path'])) {
                $firstImg = Str::startsWith($first['path'], ['http://', 'https://'])
                    ? $first['path']
                    : asset($first['path']);
            }
        }
    @endphp

    <div class="modern-container">
        <div class="container-modern">

            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">Table Detail</h1>
                    <p class="page-subtitle">View complete table information</p>
                </div>
                <a href="{{ route('owner.user-owner.tables.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span> Back to Tables
                </a>
            </div>

            {{-- Hero Card --}}
            <div class="modern-card">
                <div class="detail-hero-header">
                    <div class="detail-avatar">
                        @if ($firstImg)
                            <img src="{{ $firstImg }}" alt="Table {{ $table->table_no }}" class="detail-avatar-image">
                        @else
                            <div class="detail-avatar-placeholder">
                                <span class="material-symbols-outlined">table_restaurant</span>
                            </div>
                        @endif
                    </div>
                    <div class="detail-hero-info">
                        <h3 class="detail-hero-name">Table {{ $table->table_no }}</h3>
                        <p class="detail-hero-subtitle">{{ $table->table_code }}</p>
                        <p class="detail-hero-outlet">
                            <span class="material-symbols-outlined" style="font-size:1rem;vertical-align:middle;">store</span>
                            {{ $table->partner->name ?? '-' }}
                        </p>
                        <div class="detail-hero-badges">
                            <span class="badge-modern badge-info">{{ $table->table_class }}</span>
                            <span class="badge-modern {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- Gallery (jika lebih dari 1 gambar) --}}
                @if (!empty($table->images) && is_array($table->images) && count($table->images) > 1)
                    <div class="detail-gallery">
                        @foreach ($table->images as $img)
                            @php
                                $src = !empty($img['path'])
                                    ? (Str::startsWith($img['path'], ['http://', 'https://'])
                                        ? $img['path']
                                        : asset($img['path']))
                                    : null;
                            @endphp
                            @if ($src)
                                <a href="{{ $src }}" target="_blank" class="gallery-item">
                                    <img src="{{ $src }}" alt="{{ $img['filename'] ?? 'Table Image' }}">
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info Card --}}
            <div class="modern-card">
                <div class="card-body-modern">
                    <div class="section-header">
                        <div class="section-icon section-icon-red">
                            <span class="material-symbols-outlined">table_restaurant</span>
                        </div>
                        <h3 class="section-title">Table Information</h3>
                    </div>

                    <div class="detail-info-grid">
                        <div class="detail-info-group">
                            <div class="detail-info-item">
                                <div class="detail-info-label">Table No</div>
                                <div class="detail-info-value">{{ $table->table_no ?? '—' }}</div>
                            </div>
                            <div class="detail-info-item">
                                <div class="detail-info-label">Table Code</div>
                                <div class="detail-info-value">{{ $table->table_code ?? '—' }}</div>
                            </div>
                            <div class="detail-info-item">
                                <div class="detail-info-label">Outlet</div>
                                <div class="detail-info-value">{{ $table->partner->name ?? '—' }}</div>
                            </div>
                            <div class="detail-info-item">
                                <div class="detail-info-label">Created At</div>
                                <div class="detail-info-value">
                                    {{ $table->created_at?->format('d M Y, H:i') ?? '—' }}
                                </div>
                            </div>
                        </div>
                        <div class="detail-info-group">
                            <div class="detail-info-item">
                                <div class="detail-info-label">Class Type</div>
                                <div class="detail-info-value">{{ $table->table_class ?? '—' }}</div>
                            </div>
                            <div class="detail-info-item">
                                <div class="detail-info-label">Status</div>
                                <div class="detail-info-value">
                                    <span class="badge-modern {{ $statusInfo['class'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="detail-info-item">
                                <div class="detail-info-label">Table URL</div>
                                <div class="detail-info-value">
                                    @if ($table->table_url)
                                        <a href="{{ url($table->table_url) }}" target="_blank"
                                            class="text-primary" style="word-break:break-all;">
                                            {{ url($table->table_url) }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (!empty($table->description))
                        <div class="section-divider"></div>
                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <h3 class="section-title">Description</h3>
                        </div>
                        <div class="detail-info-item">
                            <div class="detail-info-value">{!! nl2br(e($table->description)) !!}</div>
                        </div>
                    @endif

                    <div class="action-buttons-group" style="margin-top:1.5rem;">
                        <a href="{{ route('owner.user-owner.tables.edit', $table->id) }}"
                            class="btn-action btn-action-edit">
                            <span class="material-symbols-outlined">edit</span> Edit
                        </a>
                        <form action="{{ route('owner.user-owner.tables.destroy', $table->id) }}" method="POST"
                            class="d-inline-block" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-action btn-action-delete" onclick="confirmDelete()">
                                <span class="material-symbols-outlined">delete</span> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- QR Code Card --}}
            @if ($table->table_code)
                <div class="modern-card">
                    <div class="card-body-modern">
                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">qr_code_2</span>
                            </div>
                            <h3 class="section-title">QR Code</h3>
                        </div>
                        <div class="qr-code-display">
                            <div class="qr-frame-inner">
                                <img src="{{ route('owner.user-owner.tables.generate-barcode', $table->id) }}"
                                    alt="QR Code Table {{ $table->table_no }}" class="qr-image">
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('owner.user-owner.tables.generate-barcode', $table->id) }}"
                                download="qrcode-table-{{ $table->table_no }}.png"
                                class="btn-action btn-action-edit" style="display:inline-flex;">
                                <span class="material-symbols-outlined">download</span> Download QR Code
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <style>
        .detail-hero-outlet {
            color: #6b7280;
            font-size: .9rem;
            margin: .25rem 0 .5rem;
            display: flex;
            align-items: center;
            gap: .25rem;
        }

        .qr-code-display {
            display: flex;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .qr-frame-inner {
            padding: 2rem;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .15);
        }

        .qr-image {
            width: 280px;
            height: 280px;
            display: block;
            border-radius: 8px;
        }

        .action-buttons-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
            border: 1px solid rgba(0, 0, 0, .1);
            cursor: pointer;
            font-size: .95rem;
            background: #fff;
        }

        .btn-action-edit:hover {
            background: #f8f9fa;
            transform: translateY(-1px);
        }

        .btn-action-delete {
            border-color: rgba(174, 21, 4, .25);
            color: #ae1504;
        }

        .btn-action-delete:hover {
            background: rgba(174, 21, 4, .05);
            transform: translateY(-1px);
        }

        @media (max-width:576px) {
            .action-buttons-group {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ae1504',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(result => {
                if (result.isConfirmed) document.getElementById('deleteForm').submit();
            });
        }
    </script>
@endpush