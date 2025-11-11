@extends('pages.admin.layouts.app')
@section('content-header')
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h5 class="content-header-title float-left pr-1 mb-0">XenPlatform</h5>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb p-0 mb-0">
                        <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item">Partner</li>
                        <li class="breadcrumb-item active">Invoice Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="card  border-4 border">
        <div class="card-content">
            <div class="card-body card-dashboard">
                @include('pages.admin.xen_platform.partner-account.tab-panel.invoice.detail.content-body')
            </div>
        </div>
    </div>
@endsection

