@extends('layouts.owner')

@section('title', __('messages.owner.outlet.all_outlets.detail_title'))
@section('page_title', __('messages.owner.outlet.all_outlets.detail_title'))

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
            'disabled' => __('messages.owner.outlet.all_outlets.mode_disabled'),
            'barcode_only' => __('messages.owner.outlet.all_outlets.mode_barcode_only'),
            'cashier_only' => __('messages.owner.outlet.all_outlets.mode_cashier_only'),
            'both' => __('messages.owner.outlet.all_outlets.mode_both')
        ];
        $qrModeLabel = $qrModeLabels[$outlet->qr_mode] ?? 'Unknown';
    @endphp

    <div class="modern-container">
        <div class="container-modern">

            {{-- Page Header with Back Button --}}
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.outlet.all_outlets.detail_title') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.outlet.all_outlets.detail_subtitle') }}</p>
                </div>
                <a href="{{ route('owner.user-owner.outlets.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.outlet.all_outlets.back') }}
                </a>
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
                            {{ $outlet->city ?? __('messages.owner.outlet.all_outlets.location_unavailable') }}
                        </p>
                        <div class="detail-hero-badges">
                            <span class="detail-badge-role">
                                {{ __('messages.owner.outlet.all_outlets.partner_code') }}: {{ $outlet->partner_code ?? '—' }}
                            </span>
                            @if($isActive)
                                <span class="badge-modern badge-success">
                                    {{ __('messages.owner.outlet.all_outlets.active') }}
                                </span>
                            @else
                                <span class="badge-modern badge-danger">
                                    {{ __('messages.owner.outlet.all_outlets.inactive') }}
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
                        <h3 class="section-title">{{ __('messages.owner.outlet.all_outlets.base_information') }}</h3>
                    </div>

                    <div class="detail-info-grid">
                        <div class="detail-info-group">
                            {{-- Outlet Name --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.outlet_name') }}</div>
                                <div class="detail-info-value">{{ $outlet->name ?? '—' }}</div>
                            </div>

                            {{-- Email --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.email') }}</div>
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
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.username') }}</div>
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
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.partner_code') }}</div>
                                <div class="detail-info-value">
                                    <strong>{{ $outlet->partner_code ?? '—' }}</strong>
                                </div>
                            </div>

                            {{-- Package --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.package') }}</div>
                                <div class="detail-info-value">{{ $outlet->package ?? __('messages.owner.outlet.all_outlets.no_package') }}</div>
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
                        <h3 class="section-title">{{ __('messages.owner.outlet.all_outlets.location_information') }}</h3>
                    </div>

                    <div class="detail-info-grid">
                        <div class="detail-info-group">
                            {{-- Province --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.province') }}</div>
                                <div class="detail-info-value">{{ $outlet->province ?? '—' }}</div>
                            </div>

                            {{-- City --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.city') }}</div>
                                <div class="detail-info-value">{{ $outlet->city ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="detail-info-group">
                            {{-- Subdistrict --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.district') }}</div>
                                <div class="detail-info-value">{{ $outlet->subdistrict ?? '—' }}</div>
                            </div>

                            {{-- Urban Village --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.village') }}</div>
                                <div class="detail-info-value">{{ $outlet->urban_village ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Address Full Width --}}
                    <div class="detail-info-item" style="margin-top: 1rem;">
                        <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.full_address') }}</div>
                        <div class="detail-info-value">{{ $outlet->address ?? '—' }}</div>
                    </div>

                    {{-- Section Divider --}}
                    <div class="section-divider"></div>

                    {{-- Features & Settings Section --}}
                    <div class="section-header">
                        <div class="section-icon section-icon-red">
                            <span class="material-symbols-outlined">settings</span>
                        </div>
                        <h3 class="section-title">{{ __('messages.owner.outlet.all_outlets.features_settings') }}</h3>
                    </div>

                    <div class="detail-info-grid">
                        <div class="detail-info-group">

                            {{-- QR Active --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.qr_active_status') }}</div>
                                <div class="detail-info-value">
                                    @if($isQrActive)
                                        <span class="badge-modern badge-success">{{ __('messages.owner.outlet.all_outlets.active') }}</span>
                                    @else
                                        <span class="badge-modern badge-danger">{{ __('messages.owner.outlet.all_outlets.inactive') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Cashier Active --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.cashier_active_status') }}</div>
                                <div class="detail-info-value">
                                    @if($isCashierActive)
                                        <span class="badge-modern badge-success">{{ __('messages.owner.outlet.all_outlets.active') }}</span>
                                    @else
                                        <span class="badge-modern badge-danger">{{ __('messages.owner.outlet.all_outlets.inactive') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- WiFi Shown --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.wifi_shown_status') }}</div>
                                <div class="detail-info-value">
                                    @if($isWifiShown)
                                        <span class="badge-modern badge-success">{{ __('messages.owner.outlet.all_outlets.active') }}</span>
                                    @else
                                        <span class="badge-modern badge-danger">{{ __('messages.owner.outlet.all_outlets.inactive') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="detail-info-group">
                            {{-- WiFi Username --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.wifi_username_label') }}</div>
                                <div class="detail-info-value">{{ $outlet->user_wifi ?? '—' }}</div>
                            </div>

                            {{-- WiFi Password --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.wifi_password') }}</div>
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
                            <h3 class="section-title">{{ __('messages.owner.outlet.all_outlets.contact_and_social_media') }}</h3>
                        </div>

                        <div class="detail-info-grid">
                            <div class="detail-info-group">
                                {{-- Contact Person --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.contact_name') }}</div>
                                    <div class="detail-info-value">{{ $outlet->profileOutlet->contact_person ?? '—' }}</div>
                                </div>

                                {{-- Contact Phone --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.phone_number') }}</div>
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
                                    <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.whatsapp') }}</div>
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
                                    <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.website') }}</div>
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
                                    <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.instagram') }}</div>
                                    <div class="detail-info-value">{{ $outlet->profileOutlet->instagram ?? '—' }}</div>
                                </div>

                                {{-- Facebook --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.facebook') }}</div>
                                    <div class="detail-info-value">{{ $outlet->profileOutlet->facebook ?? '—' }}</div>
                                </div>

                                {{-- Twitter --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.twitter') }}</div>
                                    <div class="detail-info-value">{{ $outlet->profileOutlet->twitter ?? '—' }}</div>
                                </div>

                                {{-- TikTok --}}
                                <div class="detail-info-item">
                                    <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.tiktok') }}</div>
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
                        <h3 class="section-title">{{ __('messages.owner.outlet.all_outlets.system_information') }}</h3>
                    </div>

                    <div class="detail-info-grid">
                        <div class="detail-info-group">
                            {{-- Created At --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.created_at') }}</div>
                                <div class="detail-info-value">
                                    {{ optional($outlet->created_at)->format('d M Y, H:i') ?? '—' }}
                                </div>
                            </div>
                        </div>

                        <div class="detail-info-group">
                            {{-- Updated At --}}
                            <div class="detail-info-item">
                                <div class="detail-info-label">{{ __('messages.owner.outlet.all_outlets.last_updated') }}</div>
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