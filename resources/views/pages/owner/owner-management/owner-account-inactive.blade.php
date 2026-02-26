@extends('layouts.owner')

@section('title', 'Account Inactive')

@section('page_title', 'Account Inactive')

@section('content')
@php
    $deactivationData = session('owner_deactivation_data', []);
    $reason = $deactivationData['reason'] ?? null;
@endphp

<div class="content">
    <div class="container-modern">
        <div class="row">
            <div class="col">
                <div class="card card-outline">
                    <div class="card-body">
                        <div class="text-center py-5" style="min-height: 450px; display: flex; flex-direction: column; justify-content: center;">
                            <i class="fas fa-user-slash fa-5x text-warning mb-4"></i>
                            <h4 class="mb-4">Akun Anda Telah Dinonaktifkan</h4>
                            
                            @if($reason)
                            <p class="text-muted mb-3">
                                <strong>Alasan Penonaktifan:</strong> {{ $reason }}
                            </p>
                            @else
                            <p class="text-muted mb-4">
                                Akun Anda tidak dapat mengakses fitur panel owner sampai akun Anda diaktifkan kembali.
                            </p>
                            @endif

                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-info-circle"></i>
                                Silakan hubungi admin untuk informasi lebih lanjut.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.disabled-link {
    pointer-events: none;
    opacity: 0.6;
    cursor: not-allowed;
}

.disabled-header {
    opacity: 0.6;
}
</style>
@endpush