<div class="row">
    <div class="col-lg-4">
        <div class="card border shadow-lg">
            <div class="card-header">
                <h6 class="mb-0">Profile</h6>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <table class="table table-hover" id="table-split-rules">
                        <tr>
                            <td class="w-20p">Name</td>
                            <td class="w-15p text-right text-bold-500">{{$data['public_profile']['business_name']}}</td>
                        </tr>
                        <tr>
                            <td class="w-20p">Email</td>
                            <td class="w-15p text-right text-bold-500">{{$data['email']}}</td>
                        </tr>
                        <tr>
                            <td class="w-20p">Account ID</td>
                            <td class="w-15p text-right text-bold-500">{{$data['id']}}</td>
                        </tr>
                        <tr>
                            <td class="w-20p">Date Created</td>
                            <td class="w-15p text-right text-bold-500">{{$data['created']}}</td>
                        </tr>
                        <tr>
                            <td class="w-20p">Account Type</td>
                            <td class="text-right">
                                @switch($data['type'])
                                    @case('MANAGED')
                                        <span class="badge bg-primary badge-pill">{{ $data['type'] }}</span>
                                        @break

                                    @case('OWNED')
                                        <span class="badge bg-info badge-pill">{{ $data['type'] }}</span>
                                        @break

                                    @case('CUSTOM')
                                        <span class="badge bg-warning badge-pill">{{ $data['type'] }}</span>
                                        @break

                                    @default
                                        <span class="badge bg-secondary badge-pill">{{ $data['type'] ?? 'UNKNOWN' }}</span>
                                @endswitch
                            </td>


                        </tr>
                        <tr>
                            <td class="w-20p">Status</td>
                            <td class="text-right">
                                @switch($data['status'])
                                    @case('INVITED')
                                        <span class="badge bg-info badge-pill">{{ $data['status'] }}</span>
                                        @break

                                    @case('REGISTERED')
                                        <span class="badge bg-primary badge-pill">{{ $data['status'] }}</span>
                                        @break

                                    @case('AWAITING_DOCS')
                                        <span class="badge bg-warning badge-pill">{{ $data['status'] }}</span>
                                        @break

                                    @case('LIVE')
                                        <span class="badge bg-success badge-pill">{{ $data['status'] }}</span>
                                        @break

                                    @case('SUSPENDED')
                                        <span class="badge bg-danger badge-pill">{{ $data['status'] }}</span>
                                        @break

                                    @default
                                        <span class="badge bg-secondary badge-pill">{{ $data['status'] ?? 'UNKNOWN' }}</span>
                                @endswitch
                            </td>

                        </tr>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border shadow-lg">
            <div class="card-header">
                <h6 class="mb-0">Balance</h6>
            </div>
            <div class="card-content">
                <div class="card-body" id="split-rule-detail">
                    <h1>IDR {{number_format($data['balance'])}}</h1>
                </div>
            </div>
        </div>
    </div>
</div>