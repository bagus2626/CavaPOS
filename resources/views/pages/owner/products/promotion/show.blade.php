@extends('layouts.owner')

@section('title', __('messages.owner.products.promotions.promotion_detail'))
@section('page_title', __('messages.owner.products.promotions.promotion_detail'))

@section('content')
@php
    use Illuminate\Support\Str;

    $promo = $data ?? $promotion ?? null;
    $isActive = (int) ($promo->is_active ?? 0) === 1;

    $daysMap = [
        'sun' => __('messages.owner.products.promotions.sunday'),
        'mon' => __('messages.owner.products.promotions.monday'),
        'tue' => __('messages.owner.products.promotions.tuesday'),
        'wed' => __('messages.owner.products.promotions.wednesday'),
        'thu' => __('messages.owner.products.promotions.thursday'),
        'fri' => __('messages.owner.products.promotions.friday'),
        'sat' => __('messages.owner.products.promotions.saturday'),
    ];
    $days = is_array($promo->active_days) ? $promo->active_days : [];
    $isEveryDay = count($days) === 7;

    $startLabel = optional($promo->start_date)->translatedFormat('d F Y H:i');
    $endLabel   = optional($promo->end_date)->translatedFormat('d F Y H:i');

    $typeLabel = $promo->promotion_type === 'percentage' ? 'Percentage' : 'Amount';
    $valueLabel = $promo->promotion_type === 'percentage'
        ? ($promo->promotion_value . '%')
        : ('Rp ' . number_format((float) $promo->promotion_value, 0, ',', '.'));
@endphp

<div class="modern-container">
    <div class="container-modern">
        
        {{-- Page Header --}}
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">{{ __('messages.owner.products.promotions.promotion_detail') }}</h1>
                <p class="page-subtitle">View complete information about this promotion.</p>
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
                {{-- Icon --}}
                <div class="detail-avatar">
                    <div class="detail-avatar-placeholder">
                        <span class="material-symbols-outlined" style="font-size: 48px;">local_offer</span>
                    </div>
                </div>

                {{-- Hero Info --}}
                <div class="detail-hero-info">
                    <h3 class="detail-hero-name">{{ $promo->promotion_name }}</h3>
                    <p class="detail-hero-subtitle">
                        {{ $promo->promotion_code ?? 'No code' }}
                    </p>
                    <div class="detail-hero-badges">

                        @if($isActive)
                            <span class="badge-modern badge-success">
                                {{ __('messages.owner.products.promotions.active') }}
                            </span>
                        @else
                            <span class="badge-modern badge-secondary">
                                {{ __('messages.owner.products.promotions.inactive') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Body Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                
                {{-- Promotion Information Section --}}
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">campaign</span>
                    </div>
                    <h3 class="section-title">Promotion Information</h3>
                </div>
                
                <div class="detail-info-grid">
                    <div class="detail-info-group">
                        {{-- Promo Code --}}
                        <div class="detail-info-item">
                            <div class="detail-info-label">
                                {{ __('messages.owner.products.promotions.promo_code') }}
                            </div>
                            <div class="detail-info-value">{{ $promo->promotion_code ?? '—' }}</div>
                        </div>

                        {{-- Promo Name --}}
                        <div class="detail-info-item">
                            <div class="detail-info-label">
                                {{ __('messages.owner.products.promotions.promo_name') }}
                            </div>
                            <div class="detail-info-value">{{ $promo->promotion_name ?? '—' }}</div>
                        </div>

                        {{-- Promo Type --}}
                        <div class="detail-info-item">
                            <div class="detail-info-label">
                                {{ __('messages.owner.products.promotions.promo_type') }}
                            </div>
                            <div class="detail-info-value">
                                <span class="badge-modern badge-info">{{ $typeLabel }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="detail-info-group">
                        {{-- Value --}}
                        <div class="detail-info-item">
                            <div class="detail-info-label">
                                {{ __('messages.owner.products.promotions.value') }}
                            </div>
                            <div class="detail-info-value text-success">
                                <span>{{ $valueLabel }}</span>
                            </div>
                        </div>

                        {{-- Uses Expiry --}}
                        <div class="detail-info-item">
                            <div class="detail-info-label">
                                {{ __('messages.owner.products.promotions.is_use_expiry') }}
                            </div>
                            <div class="detail-info-value">
                                @if((int)($promo->uses_expiry ?? 0) === 1)
                                    <span class="badge-modern badge-primary">{{ __('messages.owner.products.promotions.yes') }}</span>
                                @else
                                    <span class="badge-modern badge-danger">{{ __('messages.owner.products.promotions.no') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Active Days --}}
                        <div class="detail-info-item">
                            <div class="detail-info-label">
                                {{ __('messages.owner.products.promotions.active_day') }}
                            </div>
                            <div class="detail-info-value">
                                @if($isEveryDay)
                                    <span class="badge-modern badge-success">{{ __('messages.owner.products.promotions.every_day') }}</span>
                                @elseif(!empty($days))
                                    <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                        @foreach($days as $d)
                                            <span class="">
                                                {{ $daysMap[$d] ?? $d }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span>—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Divider --}}
                <div class="section-divider"></div>

                {{-- Validity Period Section --}}
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">schedule</span>
                    </div>
                    <h3 class="section-title">Validity Period</h3>
                </div>
                
                <div class="detail-info-grid">
                    <div class="detail-info-group">
                        {{-- Start Date --}}
                        <div class="detail-info-item">
                            <div class="detail-info-label">
                                {{ __('messages.owner.products.promotions.start_date') ?? 'Start Date' }}
                            </div>
                            <div class="detail-info-value">
                                @if((int)($promo->uses_expiry ?? 0) === 1 && $promo->start_date)
                                    {{ $startLabel }}
                                @else
                                    <span>—</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="detail-info-group">
                        {{-- End Date --}}
                        <div class="detail-info-item">
                            <div class="detail-info-label">
                                {{ __('messages.owner.products.promotions.end_date') ?? 'End Date' }}
                            </div>
                            <div class="detail-info-value">
                                @if((int)($promo->uses_expiry ?? 0) === 1 && $promo->end_date)
                                    {{ $endLabel }}
                                @else
                                    <span>—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description Section --}}
                @if(!empty($promo->description))
                    <div class="section-divider"></div>
                    
                    <div class="section-header">
                        <div class="section-icon section-icon-red">
                            <span class="material-symbols-outlined">description</span>
                        </div>
                        <h3 class="section-title">{{ __('messages.owner.products.promotions.description') }}</h3>
                    </div>
                    
                    <div class="detail-info-item">
                        <div class="detail-info-value">
                            {!! nl2br(e($promo->description)) !!}
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection