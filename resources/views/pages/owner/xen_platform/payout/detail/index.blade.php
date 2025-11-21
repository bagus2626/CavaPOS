@extends('layouts.owner')

@section('title', __('messages.owner.xen_platform.payouts.withdrawal_detail'))
@section('page_title', __('messages.owner.xen_platform.payouts.withdrawal_detail'))

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        @include('pages.owner.xen_platform.payout.detail.content-body')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

