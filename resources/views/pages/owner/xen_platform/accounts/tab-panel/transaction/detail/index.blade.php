@extends('layouts.owner')

@section('title', __('messages.owner.xen_platform.accounts.transaction_details'))
@section('page_title', __('messages.owner.xen_platform.accounts.transaction_details'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            {{-- Page Header --}}
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.xen_platform.accounts.transaction_details') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.xen_platform.accounts.view_complete_info') }}</p>
                </div>
                <a href="{{ url()->previous() }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.xen_platform.accounts.back') }}
                </a>
            </div>

            @include('pages.owner.xen_platform.accounts.tab-panel.transaction.detail.content-body')
        </div>
    </div>
@endsection