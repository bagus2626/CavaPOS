@extends('layouts.owner')

@section('title', 'Transaction Detail')
@section('page_title', 'Transaction Detail')

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

