@extends('layouts.owner')

@section('title', __('messages.owner.xen_platform.accounts.transaction_details'))
@section('page_title', __('messages.owner.xen_platform.accounts.transaction_details'))

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        @include('pages.owner.xen_platform.accounts.tab-panel.transaction.detail.content-body')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

