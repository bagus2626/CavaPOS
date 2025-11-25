@extends('layouts.owner')

@section('title', 'Invoice Detail')
@section('page_title', 'Invoice Detail')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card  border-4 border">
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        @include('pages.owner.xen_platform.accounts.tab-panel.invoice.detail.content-body')
                    </div>
                </div>
            </div>
        </div>
        <section>
@endsection

