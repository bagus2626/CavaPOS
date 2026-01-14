@extends('layouts.owner')
@section('page_title', __('messages.owner.verification.status.page_title'))

@section('content')
@php
  $isPending = $verification->status === 'pending';
  $isRejected = $verification->status === 'rejected';
@endphp

<div class="modern-container">
  <div class="container-modern">
    
    {{-- Page Header --}}
    <div class="page-header">
      <div class="header-content">
        <h1 class="page-title">{{ __('messages.owner.verification.status.header_title') }}</h1>
        <p class="page-subtitle">{{ __('messages.owner.verification.status.header_desc') }}</p>
      </div>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
      <div class="alert alert-success alert-modern">
        <div class="alert-icon">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div class="alert-content">
          <p>{{ __('messages.owner.verification.status.swal_success_p1') }}</p>
          <p>
            {{ __('messages.owner.verification.status.swal_success_p2') }}
            <strong>{{ auth()->guard('owner')->user()->email }}</strong>
          </p>
          <p>
            <span class="material-symbols-outlined">warning</span>
            {{ __('messages.owner.verification.status.swal_success_warning') }}
          </p>
        </div>
      </div>
    @endif

    {{-- Status Cards --}}
    <div class="row mb-4">
      @if($isPending)
        {{-- Pending Status Card --}}
        <div class="col-12 col-sm-6 col-lg-4 mb-3">
          <div class="modern-card stats-card">
            <div class="stats-icon">
              <span class="material-symbols-outlined">schedule</span>
            </div>
            <div class="stats-content">
              <div class="stats-label">Status</div>
              <div class="stats-value">{{ __('messages.owner.verification.status.pending') }}</div>
            </div>
          </div>
        </div>

        {{-- Submitted Date Card --}}
        <div class="col-12 col-sm-6 col-lg-4 mb-3">
          <div class="modern-card stats-card">
            <div class="stats-icon">
              <span class="material-symbols-outlined">calendar_today</span>
            </div>
            <div class="stats-content">
              <div class="stats-label">{{ __('messages.owner.verification.status.submitted_at') }}</div>
              <div class="stats-value">{{ $verification->created_at->format('d M Y') }}</div>
            </div>
          </div>
        </div>

        {{-- Estimation Card --}}
        <div class="col-12 col-sm-6 col-lg-4 mb-3">
          <div class="modern-card stats-card">
            <div class="stats-icon">
              <span class="material-symbols-outlined">hourglass_empty</span>
            </div>
            <div class="stats-content">
              <div class="stats-label">{{ __('messages.owner.verification.status.process_estimation') }}</div>
              <div class="stats-value">{{ __('messages.owner.verification.status.estimation_time') }}</div>
            </div>
          </div>
        </div>

      @elseif($isRejected)
        {{-- Rejected Status Card --}}
        <div class="col-12 col-sm-6 col-lg-4 mb-3">
          <div class="modern-card stats-card">
            <div class="stats-icon">
              <span class="material-symbols-outlined">cancel</span>
            </div>
            <div class="stats-content">
              <div class="stats-label">Status</div>
              <div class="stats-value">{{ __('messages.owner.verification.status.rejected') }}</div>
            </div>
          </div>
        </div>

        {{-- Submitted Date Card --}}
        <div class="col-12 col-sm-6 col-lg-4 mb-3">
          <div class="modern-card stats-card">
            <div class="stats-icon">
              <span class="material-symbols-outlined">calendar_today</span>
            </div>
            <div class="stats-content">
              <div class="stats-label">{{ __('messages.owner.verification.status.submitted_at') }}</div>
              <div class="stats-value">{{ $verification->created_at->format('d M Y') }}</div>
            </div>
          </div>
        </div>

        {{-- Reviewed Date Card --}}
        <div class="col-12 col-sm-6 col-lg-4 mb-3">
          <div class="modern-card stats-card">
            <div class="stats-icon">
              <span class="material-symbols-outlined">event_busy</span>
            </div>
            <div class="stats-content">
              <div class="stats-label">{{ __('messages.owner.verification.status.reviewed_at') }}</div>
              <div class="stats-value">
                {{ $verification->reviewed_at->format('d M Y')}}
              </div>
            </div>
          </div>
        </div>
      @endif
    </div>

    {{-- Rejection Reason Alert --}}
    @if($isRejected && $verification->rejection_reason)
      <div class="alert alert-danger alert-modern">
        <div class="alert-icon">
          <span class="material-symbols-outlined">warning</span>
        </div>
        <div class="alert-content">
          <p class="fw-bold">
            {{ __('messages.owner.verification.status.rejection_title') }}
          </p>
          <p>{{ $verification->rejection_reason }}</p>
        </div>
      </div>
    @endif


    {{-- Owner Data Card --}}
    <div class="modern-card">
      <div class="card-body-modern">
        
        {{-- Section Header --}}
        <div class="section-header">
          <div class="section-icon section-icon-red">
            <span class="material-symbols-outlined">person</span>
          </div>
          <h3 class="section-title">{{ __('messages.owner.verification.status.owner_data') }}</h3>
        </div>
        
        {{-- Owner Info Grid --}}
        <div class="detail-info-grid">
          <div class="detail-info-group">
            {{-- Owner Name --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.verification.owner_name') }}
              </div>
              <div class="detail-info-value">{{ $verification->owner_name }}</div>
            </div>

            {{-- Owner Phone --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.verification.owner_phone') }}
              </div>
              <div class="detail-info-value">{{ $verification->owner_phone }}</div>
            </div>

            {{-- KTP Image --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.verification.status.view_ktp') }}
              </div>
              <div class="detail-info-value">
                <img src="{{ route('owner.user-owner.verification.ktp-image') }}" 
                    alt="{{ __('messages.owner.verification.status.view_ktp') }}" 
                    style="max-width: 100%; border-radius: var(--radius-sm); box-shadow: var(--shadow-soft); border: 2px solid var(--border-color); display: block;">
              </div>
            </div>
          </div>

          <div class="detail-info-group">
            {{-- Owner Email --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.verification.owner_email') }}
              </div>
              <div class="detail-info-value">
                <a href="mailto:{{ $verification->owner_email }}">{{ $verification->owner_email }}</a>
              </div>
            </div>

            {{-- KTP Number --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.verification.ktp_number') }}
              </div>
              <div class="detail-info-value">
                {{ $verification->ktp_number_decrypted ?? __('messages.owner.verification.status.ktp_hidden') }}
              </div>
            </div>
          </div>
        </div>

        {{-- Section Divider --}}
        <div class="section-divider"></div>

        {{-- Business Information Section --}}
        <div class="section-header">
          <div class="section-icon section-icon-red">
            <span class="material-symbols-outlined">storefront</span>
          </div>
          <h3 class="section-title">{{ __('messages.owner.verification.status.business_data') }}</h3>
        </div>

        {{-- Business Info Grid --}}
        <div class="detail-info-grid">
          <div class="detail-info-group">
            {{-- Business Name --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.verification.business_name') }}
              </div>
              <div class="detail-info-value">{{ $verification->business_name }}</div>
            </div>

            {{-- Business Category --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.verification.business_category') }}
              </div>
              <div class="detail-info-value">
                {{ optional($verification->businessCategory)->name ?? '—' }}
              </div>
            </div>

            {{-- Business Phone --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.verification.business_phone') }}
              </div>
              <div class="detail-info-value">{{ $verification->business_phone }}</div>
            </div>

            {{-- Business Logo --}}
            @if($verification->business_logo_path)
              <div class="detail-info-item">
                <div class="detail-info-label">
                  {{ __('messages.owner.verification.status.view_logo') }}
                </div>
                <div class="detail-info-value">
                  <img src="{{ asset('storage/' . $verification->business_logo_path) }}" 
                      alt="{{ __('messages.owner.verification.status.view_logo') }}" 
                      style="max-width: 100%; border-radius: var(--radius-sm); box-shadow: var(--shadow-soft); border: 2px solid var(--border-color); display: block;">
                </div>
              </div>
            @endif
          </div>

          <div class="detail-info-group">
            {{-- Business Email --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.verification.business_email') }}
              </div>
              <div class="detail-info-value">
                @if($verification->business_email)
                  <a href="mailto:{{ $verification->business_email }}">{{ $verification->business_email }}</a>
                @else
                  —
                @endif
              </div>
            </div>

            {{-- Business Address --}}
            <div class="detail-info-item">
              <div class="detail-info-label">
                {{ __('messages.owner.verification.business_address') }}
              </div>
              <div class="detail-info-value">{{ $verification->business_address }}</div>
            </div>
          </div>
        </div>

      </div>

      <!-- Card Footer -->
      @if($isRejected)
        <div class="card-footer-modern">
          {{-- Resubmit Button --}}
          <a href="{{ route('owner.user-owner.verification.index') }}" 
            class="btn-modern btn-primary-modern btn-lg-modern">
            {{ __('messages.owner.verification.status.btn_resubmit') }}
          </a>
        </div>
      @endif

    </div>

  </div>
</div>

@push('scripts')
<script>
// Auto refresh for pending status
@if($isPending)
let refreshInterval = setInterval(function() {
  fetch(window.location.href, {
    method: 'GET',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.status && data.status !== 'pending') {
      clearInterval(refreshInterval);
      location.reload();
    }
  })
  .catch(error => {
    console.log('Auto refresh error:', error);
  });
}, 30000);
@endif
</script>
@endpush
@endsection