@extends('layouts.staff')
@section('title', __('messages.owner.settings.settings.profile_settings'))

@section('content')
@php
    use Illuminate\Support\Str;
    $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager');
    $img = $employee && $employee->image
        ? (Str::startsWith($employee->image, ['http://', 'https://'])
            ? $employee->image
            : asset('storage/' . $employee->image))
        : null;
    $isActive = (int) ($employee->is_active ?? 0) === 1;
@endphp

<div class="modern-container">
    <div class="container-modern">

        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">{{ __('messages.owner.settings.settings.profile_settings') }}</h1>
                <p class="page-subtitle">{{ __('messages.owner.settings.settings.subtitle') }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-modern">
                <div class="alert-icon"><span class="material-symbols-outlined">check_circle</span></div>
                <div class="alert-content">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-modern">
                <div class="alert-icon"><span class="material-symbols-outlined">error</span></div>
                <div class="alert-content">{{ session('error') }}</div>
            </div>
        @endif

        {{-- Hero Card --}}
        <div class="modern-card">
            <div class="detail-hero-header">
                <div class="detail-avatar">
                    @if($img)
                        <img src="{{ $img }}" alt="{{ $employee->name }}" class="detail-avatar-image">
                    @else
                        <div class="detail-avatar-placeholder">
                            {{ Str::upper(Str::substr($employee->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="detail-hero-info">
                    <h3 class="detail-hero-name">{{ $employee->name }}</h3>
                    <p class="detail-hero-subtitle">{{ $employee->email }}</p>
                    <div class="detail-hero-badges">
                        <span class="badge-modern badge-info">
                            {{ $employee->role ?? '—' }}
                        </span>
                        @if($isActive)
                            <span class="badge-modern badge-success">
                                {{ __('messages.owner.settings.settings.active') }}
                            </span>
                        @else
                            <span class="badge-modern badge-danger">
                                {{ __('messages.owner.settings.settings.inactive') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Card --}}
        <div class="modern-card">
            <div class="card-body-modern">

                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.settings.settings.personal_information') }}</h3>
                </div>

                <div class="detail-info-grid">
                    <div class="detail-info-group">
                        <div class="detail-info-item">
                            <div class="detail-info-label">{{ __('messages.owner.settings.settings.full_name') }}</div>
                            <div class="detail-info-value">{{ $employee->name ?? '—' }}</div>
                        </div>
                        <div class="detail-info-item">
                            <div class="detail-info-label">{{ __('messages.owner.settings.settings.email_address') }}</div>
                            <div class="detail-info-value">
                                @if(!empty($employee->email))
                                    <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a>
                                @else —
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="detail-info-group">
                        <div class="detail-info-item">
                            <div class="detail-info-label">{{ __('messages.owner.user_management.employees.username') }}</div>
                            <div class="detail-info-value">{{ $employee->user_name ?? '—' }}</div>
                        </div>
                        <div class="detail-info-item">
                            <div class="detail-info-label">{{ __('messages.owner.user_management.employees.outlet') }}</div>
                            <div class="detail-info-value">{{ optional($employee->partner)->name ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">info</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.settings.settings.another_information') }}</h3>
                </div>

                <div class="detail-info-grid">
                    <div class="detail-info-group">
                        <div class="detail-info-item">
                            <div class="detail-info-label">{{ __('messages.owner.settings.settings.member_since') }}</div>
                            <div class="detail-info-value">
                                {{ \Carbon\Carbon::parse($employee->created_at)->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="detail-info-group">
                        <div class="detail-info-item">
                            <div class="detail-info-label">{{ __('messages.owner.settings.settings.last_update') }}</div>
                            <div class="detail-info-value">
                                {{ \Carbon\Carbon::parse($employee->updated_at)->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer-modern">
                <div></div>
                <a href="{{ route('employee.' . $empRole . '.settings.edit') }}" class="btn-submit-modern">
                    {{ __('messages.owner.settings.settings.edit_profile') }}
                </a>
            </div>
        </div>

    </div>
</div>
@endsection