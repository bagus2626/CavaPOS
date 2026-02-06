@extends('layouts.owner')

@section('title', __('messages.owner.xen_platform.payouts.withdrawal_detail'))
@section('page_title', __('messages.owner.xen_platform.payouts.withdrawal_detail'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">Invoice Detail</h1>
                    <p class="page-subtitle">View complete invoice information</p> 
                </div>
                <a href="{{ url()->previous() }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.xen_platform.accounts.back') }}
                </a>
            </div>

            @include('pages.owner.xen_platform.payout.detail.content-body')
        </div>
    </div>
@endsection

