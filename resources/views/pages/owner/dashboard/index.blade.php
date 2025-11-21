@extends('layouts.owner')

@section('title', 'Owner Dashboard')

@section('page_title', 'Dashboard Owner')

@section('content')
<!-- Main Content -->
<section class="content">
    <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row mb-3">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.owner.dashboard.total_users') }}</span>
                        <span class="info-box-number">{{ $data['total_accounts'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.owner.dashboard.total_orders') }} ({{ now()->year }})</span>
                        <span class="info-box-number">{{ $data['total_orders'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.owner.dashboard.total_sales') }} ({{ now()->year }})</span>
                        <span class="info-box-number">Rp. {{ number_format($data['orders_gross_income']) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-star"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.owner.dashboard.products') }}</span>
                        <span class="info-box-number">{{ $data['total_products'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- @yield('content') --}}

        <!-- Example content -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.owner.dashboard.recent_orders') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.owner.dashboard.order_id') }}</th>
                                        <th>Outlet</th>
                                        <th>{{ __('messages.owner.dashboard.customer') }}</th>
                                        <th>Status</th>
                                        <th>{{ __('messages.owner.dashboard.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['last_orders'] as $order)
                                        <tr>
                                            <td><a href="#">{{ $order->booking_order_code ?? '-' }}</a></td>
                                            <td>{{ $order->partner_name ?? '-' }}</td>
                                            <td>{{ $order->customer_name ?? '-' }}</td>
                                            <td>
                                                <span class="badge 
                                                    @if ($order->order_status === 'PAID') badge-primary
                                                    @elseif ($order->order_status === 'SERVED') badge-success
                                                    @elseif ($order->order_status === 'PENDING') badge-warning
                                                    @elseif ($order->order_status === 'UNPAID') badge-warning
                                                    @elseif ($order->order_status === 'PROCESSED') badge-primary
                                                    @elseif ($order->order_status === 'CANCELLED') badge-danger
                                                    @else badge-secondary
                                                    @endif
                                                ">
                                                    {{ $order->order_status }}
                                                </span>
                                            </td>
                                            <td>Rp. {{ number_format($order->total_order_value ?? 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.owner.dashboard.my_timeline') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="time-label">
                                <span class="bg-red">Today</span>
                            </div>
                            <div>
                                <i class="fas fa-envelope bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> 12:05</span>
                                    <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>
                                    <div class="timeline-body">
                                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                        weebly ning heekya handango imeem plugg dopplr jibjab, movity
                                        jajah plickers sifteo edmodo ifttt zimbra.
                                    </div>
                                    <div class="timeline-footer">
                                        <a class="btn btn-primary btn-sm">Read more</a>
                                        <a class="btn btn-danger btn-sm">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-user bg-green"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> 5 mins ago</span>
                                    <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request</h3>
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-comments bg-yellow"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> 27 mins ago</span>
                                    <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                                    <div class="timeline-body">
                                        Take me to your leader!
                                        Switzerland is small and neutral!
                                        We are more like Germany, ambitious and misunderstood!
                                    </div>
                                    <div class="timeline-footer">
                                        <a class="btn btn-warning btn-sm">View comment</a>
                                    </div>
                                </div>
                            </div>
                            <div class="time-label">
                                <span class="bg-green">Yesterday</span>
                            </div>
                            <div>
                                <i class="fas fa-camera bg-purple"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> 2 days ago</span>
                                    <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>
                                    <div class="timeline-body">
                                        <img src="https://via.placeholder.com/150" alt="..." class="mr-1 mb-1" style="width:100px">
                                        <img src="https://via.placeholder.com/150" alt="..." class="mr-1 mb-1" style="width:100px">
                                        <img src="https://via.placeholder.com/150" alt="..." class="mr-1 mb-1" style="width:100px">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-video bg-maroon"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> 5 days ago</span>
                                    <h3 class="timeline-header"><a href="#">Mr. Doe</a> shared a video</h3>
                                    <div class="timeline-body">
                                        <div class="embed-responsive embed-responsive-16by9">
                                            <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/tMWkeBIohBs" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                    <div class="timeline-footer">
                                        <a href="#" class="btn btn-sm bg-maroon">See comments</a>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection