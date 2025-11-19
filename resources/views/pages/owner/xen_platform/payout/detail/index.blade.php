@extends('layouts.owner')

@section('title', 'Payout Detail')
@section('page_title', 'Payout Detail')

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

