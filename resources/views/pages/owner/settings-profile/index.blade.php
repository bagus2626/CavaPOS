@extends('layouts.owner')

@section('title', __('messages.owner.settings.settings.profile_settings'))
@section('page_title', __('messages.owner.settings.settings.profile_settings'))

@section('content')
@php
  use Illuminate\Support\Str;

  $img = $owner && $owner->image
      ? (Str::startsWith($owner->image, ['http://','https://'])
          ? $owner->image
          : asset('storage/'.$owner->image))
      : null;

  $isActive = (int) ($owner->is_active ?? 0) === 1;
@endphp

<div class="modern-container">
  <div class="container-modern">
    
    {{-- Page Header --}}
    <div class="page-header">
      <div class="header-content">
        <h1 class="page-title">{{ __('messages.owner.settings.settings.profile_settings') }}</h1>
        <p class="page-subtitle">View your profile information and account settings.</p>
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
          @if($img)
            <img src="{{ $img }}" alt="{{ $owner->name }}" class="detail-avatar-image">
          @else
            <div class="detail-avatar-placeholder">
              {{ Str::upper(Str::substr($owner->name ?? 'U', 0, 1)) }}
            </div>
          @endif
        </div>

        {{-- Hero Info --}}
        <div class="detail-hero-info">
          <h3 class="detail-hero-name">{{ $owner->name }}</h3>
          <p class="detail-hero-subtitle">{{ $owner->email }}</p>
          <div class="detail-hero-badges">
            <span class="badge-modern badge-info">
              {{ __('messages.owner.settings.settings.owner_account') }}
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

    {{-- Body Card --}}
    <div class="modern-card">
      <div class="card-body-modern">
        
        {{-- Personal Information Section --}}
        <div class="section-header">
          <div class="section-icon section-icon-red">
            <span class="material-symbols-outlined">person</span>
          </div>
          <h3 class="section-title">{{ __('messages.owner.settings.settings.personal_information') }}</h3>
        </div>
        
        <div class="detail-info-grid">
          <div class="detail-info-group">
            {{-- Full Name --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.settings.settings.full_name') }}
              </div>
              <div class="detail-info-value">{{ $owner->name ?? '—' }}</div>
            </div>

            {{-- Email --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.settings.settings.email_address') }}
              </div>
              <div class="detail-info-value">
                @if(!empty($owner->email))
                  <a href="mailto:{{ $owner->email }}">{{ $owner->email }}</a>
                @else
                  —
                @endif
              </div>
            </div>
          </div>

          <div class="detail-info-group">
            {{-- Phone Number --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.settings.settings.phone_number') }}
              </div>
              <div class="detail-info-value">{{ $owner->phone_number ?? '—' }}</div>
            </div>

            {{-- Account Type --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.settings.settings.account_type') }}
              </div>
              <div class="detail-info-value">
                <span class="badge-modern badge-info">
                  {{ __('messages.owner.settings.settings.owner_account') }}
                </span>
              </div>
            </div>
          </div>
        </div>

        {{-- Section Divider --}}
        <div class="section-divider"></div>

        {{-- System Information Section --}}
        <div class="section-header">
          <div class="section-icon section-icon-red">
            <span class="material-symbols-outlined">info</span>
          </div>
          <h3 class="section-title">{{ __('messages.owner.settings.settings.another_information') }}</h3>
        </div>
        
        <div class="detail-info-grid">
          <div class="detail-info-group">
            {{-- Member Since --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.settings.settings.member_since') }}
              </div>
              <div class="detail-info-value">
                {{ \Carbon\Carbon::parse($owner->created_at)->format('d M Y') }}
              </div>
            </div>
          </div>

          <div class="detail-info-group">
            {{-- Last Update --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.settings.settings.last_update') }}
              </div>
              <div class="detail-info-value">
                {{ \Carbon\Carbon::parse($owner->updated_at)->diffForHumans() }}
              </div>
            </div>
          </div>
        </div>

      </div>

      {{-- Card Footer with Action Button --}}
      <div class="card-footer-modern">
        <div></div>
        <a href="{{ route('owner.user-owner.settings.edit') }}" class="btn-submit-modern"></i>Edit Profile
        </a>
      </div>
    </div>

  </div>
</div>

@endsection