@extends('layouts.owner')

@section('title', __('messages.owner.user_management.employees.employee_detail'))
@section('page_title', __('messages.owner.user_management.employees.employee_detail'))

@section('content')
@php
  use Illuminate\Support\Str;

  // Fleksibel: dukung $data atau $employee dari controller
  $emp = $data ?? $employee ?? null;

  // Gambar (relatif → storage)
  $img = $emp && $emp->image
      ? (Str::startsWith($emp->image, ['http://','https://'])
          ? $emp->image
          : asset('storage/'.$emp->image))
      : null;

  $isActive = (int) ($emp->is_active ?? 0) === 1;
@endphp

<div class="modern-container">
  <div class="container-modern">
    
    {{-- Page Header with Back Button --}}
    <div class="page-header">
      <div class="header-content">
        <h1 class="page-title">{{ __('messages.owner.user_management.employees.employee_detail') }}</h1>
        <p class="page-subtitle">View complete information about this employee.</p>
      </div>
      <a href="{{ route('owner.user-owner.employees.index') }}" class="back-button">
        <span class="material-symbols-outlined">arrow_back</span>
        {{ __('messages.owner.user_management.employees.back') }}
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
          @if($img)
            <img src="{{ $img }}" alt="{{ $emp->name }}" class="detail-avatar-image">
          @else
            <div class="detail-avatar-placeholder">
              {{ Str::upper(Str::substr($emp->name ?? 'U', 0, 1)) }}
            </div>
          @endif
        </div>

        {{-- Hero Info --}}
        <div class="detail-hero-info">
          <h3 class="detail-hero-name">{{ $emp->name }}</h3>
          <p class="detail-hero-subtitle">
            {{ optional($emp->partner)->name ?? 'No outlet assigned' }}
          </p>
          <div class="detail-hero-badges">
            <span class="badge-modern badge-info">
              {{ $emp->role ?? '—' }}
            </span>
            @if($isActive)
              <span class="badge-modern badge-success">
                {{ __('messages.owner.user_management.employees.active') }}
              </span>
            @else
              <span class="badge-modern badge-danger">
                {{ __('messages.owner.user_management.employees.non_active') }}
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
          <h3 class="section-title">Personal Information</h3>
        </div>
        
        <div class="detail-info-grid">
          <div class="detail-info-group">
            {{-- Full Name --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.user_management.employees.employee_name') }}
              </div>
              <div class="detail-info-value">{{ $emp->name ?? '—' }}</div>
            </div>

            {{-- Email --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.user_management.employees.email') }}
              </div>
              <div class="detail-info-value">
                @if(!empty($emp->email))
                  <a href="mailto:{{ $emp->email }}">{{ $emp->email }}</a>
                @else
                  —
                @endif
              </div>
            </div>
          </div>

          <div class="detail-info-group">
            {{-- Outlet/Branch --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.user_management.employees.outlet') }}
              </div>
              <div class="detail-info-value">{{ optional($emp->partner)->name ?? '—' }}</div>
            </div>

            {{-- Username --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.user_management.employees.username') }}
              </div>
              <div class="detail-info-value">{{ $emp->user_name ?? '—' }}</div>
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
          <h3 class="section-title">System Information</h3>
        </div>
        
        <div class="detail-info-grid">
          <div class="detail-info-group">
            {{-- Created At --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.user_management.employees.created') }}
              </div>
              <div class="detail-info-value">
                {{ optional($emp->created_at)->format('d M Y, H:i') ?? '—' }}
              </div>
            </div>
          </div>

          <div class="detail-info-group">
            {{-- Updated At --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.user_management.employees.updated') }}
              </div>
              <div class="detail-info-value">
                {{ optional($emp->updated_at)->format('d M Y, H:i') ?? '—' }}
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection