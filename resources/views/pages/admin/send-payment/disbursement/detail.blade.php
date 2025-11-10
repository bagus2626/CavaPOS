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
                        <li class="breadcrumb-item active">Payout Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <section class="invoice-view-wrapper">
                    <div class="row">
                        <div class="col-xl-5 col-md-8 col-12">
                            <div class="card shadow border">
                                <div class="card-content">
                                    <div class="card-body pb-0 mx-25 mb-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <a href="{{ url()->previous() }}"
                                                   class="btn btn-outline-secondary mb-2">
                                                    <i class="bx bx-arrow-back me-1"></i> Kembali
                                                </a>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-6">
                                                <h6 class="invoice-from">Transaction Amount</h6>
                                                <h2 class="text-bold-700">
                                                    {{ $data['currency'] ?? 'IDR' }}
                                                    {{ number_format($data['amount'] ?? 0, 0, ',', '.') }}
                                                </h2>
                                            </div>
                                            <div class="col-6 d-flex justify-content-end align-items-center">
                                                <div class="d-flex align-items-center">

                                                    @php
                                                        $status = $data['status'] ?? 'UNKNOWN';

                                                        $statusData = [
                                                            'SUCCEEDED' => ['class' => 'badge-success', 'icon' => 'bx-check-circle'],
                                                            'REQUESTED' => ['class' => 'badge-warning', 'icon' => 'bx-time'],
                                                            'FAILED' => ['class' => 'badge-danger', 'icon' => 'bx-x-circle'],
                                                            'CANCELLED' => ['class' => 'badge-secondary', 'icon' => 'bx-minus-circle'],
                                                            'REVERSED' => ['class' => 'badge-secondary', 'icon' => 'bx-minus-circle'],
                                                            'ACCEPTED' => ['class' => 'badge-info', 'icon' => 'bx-help-circle'],
                                                        ];

                                                        $statusDisplay = $statusData[$status] ?? $statusData['UNKNOWN'];
                                                    @endphp

                                                    <div class="badge badge-pill {{ $statusDisplay['class'] }} badge-glow d-inline-flex align-items-center text-uppercase p-1">
                                                        <i class="bx {{ $statusDisplay['icon'] }} font-medium-1 mr-25"></i>
                                                        <span class="fw-bold">{{ $status }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row invoice-info">
                                            <div class="col-6">
                                                <div class="mb-0">
                                                    <span>Created Date</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="invoice-to">{{ \Carbon\Carbon::parse($data['created'])->setTimezone('Asia/Jakarta')->format('d M Y, h:i:s A') }}
                                                    (GMT +7)</h6>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row invoice-info">
                                            <div class="col-6">
                                                <div class="mb-0">
                                                    <span>External ID</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="invoice-to">{{ $data['reference_id'] }}</h6>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                            <div class="card invoice-print-area border">
                                <div class="card-content">
                                    <div class="card-body pb-0 mx-25">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>Payment Details</h5>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                    <div class="invoice-product-details table-responsive px-md-3">
                                        <table class="table mb-5">
                                            <tbody>
                                            <tr>
                                                <td>Name</td>
                                                <td class="text-bold-500">{{ $data['channel_properties']['account_holder_name'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Bank Code</td>
                                                <td class="text-bold-500">{{ $data['channel_code'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Bank Account Number</td>
                                                <td class="text-bold-500">{{ $data['channel_properties']['account_number'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Description</td>
                                                <td class="text-bold-500">{{ $data['description'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Bank Reference</td>
                                                <td class="text-bold-500">{{ $data['connector_reference'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Email Notification</td>
                                                <td class="text-bold-500">{{ $data['receipt_notification']['email_to'][0] ?? '-' }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-4 col-12">
                            <div class="card invoice-print-area border">
                                <div class="card-content">
                                    <div class="card-body pb-0 mx-25">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>Event History</h5>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                    <div class="invoice-product-details table-responsive px-md-3">
                                        <table class="table mb-5">
                                            <tbody>
                                            <tr>
                                                <td class="fw-semibold">Created</td>
                                                <td class="text-bold-500">
                                                    @if ($data['created'] ?? null)
                                                        {{ \Carbon\Carbon::parse($data['created'])->setTimezone('Asia/Jakarta')->format('d M Y, h:i:s A') }}
                                                        (GMT +7)
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Completed</td>
                                                <td class="text-bold-500">
                                                    @if ($data['estimated_arrival_time'] ?? null)
                                                        {{ \Carbon\Carbon::parse($data['estimated_arrival_time'])->setTimezone('Asia/Jakarta')->format('d M Y, h:i:s A') }}
                                                        (GMT +7)
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

