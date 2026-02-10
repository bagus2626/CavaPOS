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

    $typeLabel = $promo->promotion_type === 'percentage' 
        ? __('messages.owner.products.promotions.percentage') 
        : __('messages.owner.products.promotions.reduced_fare');

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
                <p class="page-subtitle">{{ __('messages.owner.products.promotions.view_complete_promo_info') }}</p>
            </div>
            <a href="{{ route('owner.user-owner.promotions.index') }}" class="back-button">
                <span class="material-symbols-outlined">arrow_back</span>
                {{ __('messages.owner.products.promotions.back') }}
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
                        {{ $promo->promotion_code ?? __('messages.owner.products.promotions.no_code') }}
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
                    <h3 class="section-title">{{ __('messages.owner.products.promotions.promotion_information') }}</h3>
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
                    <h3 class="section-title">{{ __('messages.owner.products.promotions.validity_period') }}</h3>
                </div>
                
                <div class="detail-info-grid">
                    <div class="detail-info-group">
                        {{-- Start Date --}}
                        <div class="detail-info-item">
                            <div class="detail-info-label">
                                {{ __('messages.owner.products.promotions.start_date') }}
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
                                {{ __('messages.owner.products.promotions.end_date') }}
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

                {{-- Action Buttons --}}
<div class="action-buttons-group">
    <a href="{{ route('owner.user-owner.promotions.edit', $promo->id) }}" class="btn-action btn-action-edit">
        <span class="material-symbols-outlined">edit</span>
        {{ __('messages.owner.products.promotions.edit') }}
    </a>
    
    <form action="{{ route('owner.user-owner.promotions.destroy', $promo->id) }}" method="POST" class="d-inline-block" id="deleteForm">
        @csrf
        @method('DELETE')
        <button type="button" class="btn-action btn-action-delete" onclick="confirmDelete()">
            <span class="material-symbols-outlined">delete</span>
            {{ __('messages.owner.products.promotions.delete') }}
        </button>
    </form>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete() {
    Swal.fire({
        title: '{{ __('messages.owner.products.promotions.delete_confirmation_1') }}',
        text: '{{ __('messages.owner.products.promotions.delete_confirmation_2') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ae1504',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('messages.owner.products.promotions.delete_confirmation_3') }}',
        cancelButtonText: '{{ __('messages.owner.products.promotions.cancel') }}'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
}
</script>
@endpush

<style>
/* Action Buttons */
.action-buttons-group {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
  flex-wrap: wrap;
}

.btn-action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
  border: 1px solid rgba(0,0,0,.10);
  cursor: pointer;
  font-size: 0.95rem;
  background: #fff;
}

.btn-action .material-symbols-outlined {
  font-size: 1.25rem;
}

.btn-action-edit {
  color: #333;
  border-color: rgba(0,0,0,.10);
}

.btn-action-edit:hover {
  background: #f8f9fa;
  color: #333;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,.08);
}

.btn-action-delete {
  border-color: rgba(174,21,4,.25);
  color: #ae1504;
}

.btn-action-delete:hover {
  background: rgba(174,21,4,.05);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(174,21,4,.15);
}

@media (max-width: 576px) {
  .action-buttons-group {
    flex-direction: column;
  }
  
  .btn-action {
    width: 100%;
  }
}
</style>