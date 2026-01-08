@extends('layouts.owner')

@section('title', 'Detail Outlet')
@section('page_title', 'Detail Outlet')

@section('content')
    @php
        use Illuminate\Support\Str;

        // Gambar logo (relatif → storage)
        $logo = $outlet && $outlet->logo
            ? (Str::startsWith($outlet->logo, ['http://', 'https://'])
                ? $outlet->logo
                : asset('storage/' . $outlet->logo))
            : null;

        // Gambar background
        $background = $outlet && $outlet->background_picture
            ? (Str::startsWith($outlet->background_picture, ['http://', 'https://'])
                ? $outlet->background_picture
                : asset('storage/' . $outlet->background_picture))
            : null;

        $isActive = (int) ($outlet->is_active ?? 0) === 1;
        $isQrActive = (int) ($outlet->is_qr_active ?? 0) === 1;
        $isCashierActive = (int) ($outlet->is_cashier_active ?? 0) === 1;
        $isWifiShown = (int) ($outlet->is_wifi_shown ?? 0) === 1;

        // QR Mode Label
        $qrModeLabels = [
            'disabled' => 'Disabled',
            'barcode_only' => 'Barcode Only',
            'cashier_only' => 'Cashier Only',
            'both' => 'Both'
        ];
        $qrModeLabel = $qrModeLabels[$outlet->qr_mode] ?? 'Unknown';
    @endphp

    <div class="modern-container">
        <div class="container-modern">

            {{-- Page Header with Back Button --}}
            <div class="page-header">
                {{-- <a href="{{ route('owner.user-owner.outlets.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke Daftar Outlet
                </a> --}}
                <div class="header-content">
                    <h1 class="page-title">Detail Outlet</h1>
                    <p class="page-subtitle">Lihat informasi lengkap tentang outlet ini.</p>
                </div>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert alert-success alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">check_circle</span>
                    </div>
                    <div class="alert-content">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- Error Message --}}
            @if (session('error'))
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                    </div>
                    <div class="alert-content">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            {{-- Hero Card --}}
            <div class="modern-card">
                <div class="detail-hero-header">
                    {{-- Avatar --}}
                    <div class="detail-avatar">
                        @if($logo)
                            <img src="{{ $logo }}" alt="{{ $outlet->name }}" class="detail-avatar-image">
                        @else
                            <div class="detail-avatar-placeholder">
                                {{ Str::upper(Str::substr($outlet->name ?? 'O', 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    {{-- Hero Info --}}
                    <div class="detail-hero-info">
                        <h3 class="detail-hero-name">{{ $outlet->name }}</h3>
                        <p class="detail-hero-subtitle">
                            {{ $outlet->city ?? 'Lokasi tidak tersedia' }}
                        </p>
                        <div class="detail-hero-badges">
                            <span class="detail-badge-role">
                                Partner Code: {{ $outlet->partner_code ?? '—' }}
                            </span>
                            @if($isActive)
                                <span class="badge-modern badge-success">
                                    Aktif
                                </span>
                            @else
                                <span class="badge-modern badge-danger">
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Body Card --}}
            <div class="modern-card">
                <div class="card-body-modern">

                    {{-- Basic Information Section --}}
                    <div class="section-header">
                        <div class="section-icon section-icon-red">
                            <span class="material-symbols-outlined">store</span>
                        </div>
                        <h3 class="section-title">Informasi Dasar</h3>
                    </div>

                    <div class="detail-info-grid">
                        <div class="detail-info-group">
                            {{-- Outlet Name --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Nama Outlet</div>
                                <div class="detail-info-value">{{ $outlet->name ?? '—' }}</div>
                            </div>

                            {{-- Email --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Email</div>
                                <div class="detail-info-value">
                                    @if(!empty($outlet->email))
                                        <a href="mailto:{{ $outlet->email }}">{{ $outlet->email }}</a>
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>

                            {{-- Username --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Username</div>
                                <div class="detail-info-value">{{ $outlet->username ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="detail-info-group">
                            {{-- Slug --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Slug</div>
                                <div class="detail-info-value">{{ $outlet->slug ?? '—' }}</div>
                            </div>

                            {{-- Partner Code --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Kode Partner</div>
                                <div class="detail-info-value">
                                    <strong>{{ $outlet->partner_code ?? '—' }}</strong>
                                </div>
                            </div>

                            {{-- Package --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Paket</div>
                                <div class="detail-info-value">{{ $outlet->package ?? 'Tidak ada paket' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Section Divider --}}
                    <div class="section-divider"></div>

                    {{-- Location Information Section --}}
                    <div class="section-header">
                        <div class="section-icon section-icon-red">
                            <span class="material-symbols-outlined">location_on</span>
                        </div>
                        <h3 class="section-title">Informasi Lokasi</h3>
                    </div>

                    <div class="detail-info-grid">
                        <div class="detail-info-group">
                            {{-- Province --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Provinsi</div>
                                <div class="detail-info-value">{{ $outlet->province ?? '—' }}</div>
                            </div>

                            {{-- City --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Kota/Kabupaten</div>
                                <div class="detail-info-value">{{ $outlet->city ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="detail-info-group">
                            {{-- Subdistrict --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Kecamatan</div>
                                <div class="detail-info-value">{{ $outlet->subdistrict ?? '—' }}</div>
                            </div>

                            {{-- Urban Village --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Kelurahan/Desa</div>
                                <div class="detail-info-value">{{ $outlet->urban_village ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Address Full Width --}}
                    <div class="detail-info-item" style="margin-top: 1rem;">
                        <div class="detail-info-label">Alamat Lengkap</div>
                        <div class="detail-info-value">{{ $outlet->address ?? '—' }}</div>
                    </div>

                    {{-- Section Divider --}}
                    <div class="section-divider"></div>

                    {{-- Features & Settings Section --}}
                    <div class="section-header">
                        <div class="section-icon section-icon-red">
                            <span class="material-symbols-outlined">settings</span>
                        </div>
                        <h3 class="section-title">Fitur & Pengaturan</h3>
                    </div>

                    <div class="detail-info-grid">
                        <div class="detail-info-group">

                            {{-- QR Active --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">QR Aktif</div>
                                <div class="detail-info-value">
                                    @if($isQrActive)
                                        <span class="badge-modern badge-success">Aktif</span>
                                    @else
                                        <span class="badge-modern badge-danger">Tidak Aktif</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Cashier Active --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Kasir Aktif</div>
                                <div class="detail-info-value">
                                    @if($isCashierActive)
                                        <span class="badge-modern badge-success">Aktif</span>
                                    @else
                                        <span class="badge-modern badge-danger">Tidak Aktif</span>
                                    @endif
                                </div>
                            </div>

                            {{-- WiFi Shown --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">WiFi Ditampilkan</div>
                                <div class="detail-info-value">
                                    @if($isWifiShown)
                                        <span class="badge-modern badge-success">Aktif</span>
                                    @else
                                        <span class="badge-modern badge-danger">Tidak Aktif</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="detail-info-group">
                            {{-- WiFi Username --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Username WiFi</div>
                                <div class="detail-info-value">{{ $outlet->user_wifi ?? '—' }}</div>
                            </div>

                            {{-- WiFi Password --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Password WiFi</div>
                                <div class="detail-info-value">{{ $outlet->pass_wifi ?? '—' }}</div>
                            </div>

                            
                        </div>
                    </div>

                    {{-- Section Divider --}}
                    <div class="section-divider"></div>

                    {{-- Contact & Social Media Section --}}
                    @if($outlet->profileOutlet)
                        <div class="section-header">
                            <div class="section-icon section-icon-red">
                                <span class="material-symbols-outlined">contact_page</span>
                            </div>
                            <h3 class="section-title">Kontak & Media Sosial</h3>
                        </div>

                        <div class="detail-info-grid">
                            <div class="detail-info-group">
                                {{-- Contact Person --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">Contact Person</div>
                                    <div class="detail-info-value">{{ $outlet->profileOutlet->contact_person ?? '—' }}</div>
                                </div>

                                {{-- Contact Phone --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">Nomor Telepon</div>
                                    <div class="detail-info-value">
                                        @if($outlet->profileOutlet->contact_phone)
                                            <a
                                                href="tel:{{ $outlet->profileOutlet->contact_phone }}">{{ $outlet->profileOutlet->contact_phone }}</a>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>

                                {{-- WhatsApp --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">WhatsApp</div>
                                    <div class="detail-info-value">
                                        @if($outlet->profileOutlet->whatsapp)
                                            <a href="https://wa.me/{{ $outlet->profileOutlet->whatsapp }}"
                                                target="_blank">{{ $outlet->profileOutlet->whatsapp }}</a>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>

                                {{-- Website --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">Website</div>
                                    <div class="detail-info-value">
                                        @if($outlet->profileOutlet->website)
                                            <a href="{{ $outlet->profileOutlet->website }}"
                                                target="_blank">{{ $outlet->profileOutlet->website }}</a>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="detail-info-group">
                                {{-- Instagram --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">Instagram</div>
                                    <div class="detail-info-value">{{ $outlet->profileOutlet->instagram ?? '—' }}</div>
                                </div>

                                {{-- Facebook --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">Facebook</div>
                                    <div class="detail-info-value">{{ $outlet->profileOutlet->facebook ?? '—' }}</div>
                                </div>

                                {{-- Twitter --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">Twitter</div>
                                    <div class="detail-info-value">{{ $outlet->profileOutlet->twitter ?? '—' }}</div>
                                </div>

                                {{-- TikTok --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">TikTok</div>
                                    <div class="detail-info-value">{{ $outlet->profileOutlet->tiktok ?? '—' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Google Maps Full Width --}}
                        @if($outlet->profileOutlet->gmaps_url)
                            <div class="detail-info-item" style="margin-top: 1rem;">
                                <div class="detail-info-label">Google Maps</div>
                                <div class="detail-info-value">
                                    <a href="{{ $outlet->profileOutlet->gmaps_url }}"
                                        target="_blank">{{ $outlet->profileOutlet->gmaps_url }}</a>
                                </div>
                            </div>
                        @endif

                        {{-- Section Divider --}}
                        <div class="section-divider"></div>
                    @endif

                    {{-- System Information Section --}}
                    <div class="section-header">
                        <div class="section-icon section-icon-red">
                            <span class="material-symbols-outlined">info</span>
                        </div>
                        <h3 class="section-title">Informasi Sistem</h3>
                    </div>

                    <div class="detail-info-grid">
                        <div class="detail-info-group">
                            {{-- Created At --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Dibuat Pada</div>
                                <div class="detail-info-value">
                                    {{ optional($outlet->created_at)->format('d M Y, H:i') ?? '—' }}
                                </div>
                            </div>
                        </div>

                        <div class="detail-info-group">
                            {{-- Updated At --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">Terakhir Diupdate</div>
                                <div class="detail-info-value">
                                    {{ optional($outlet->updated_at)->format('d M Y, H:i') ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection