<div class="d-flex align-items-center w-100 justify-content-between">
    <div>
        <h3 class="mb-0 text-bold-500 d-flex align-items-center">
            {{ $account['public_profile']['business_name'] }}
            @php
                $status = $account['status'] ?? 'UNKNOWN';
                $badgeClasses = [
                    'INVITED'       => 'badge-light-info',
                    'REGISTERED'    => 'badge-light-primary',
                    'AWAITING_DOCS' => 'badge-light-warning',
                    'LIVE'          => 'badge-light-success',
                    'SUSPENDED'     => 'badge-light-danger',
                    'UNKNOWN'       => 'badge-light-secondary',
                ];
            @endphp
            <span class="badge {{ $badgeClasses[$status] ?? 'badge-light-secondary' }} badge-pill ml-1">{{ $status }}</span>
        </h3>

        <h5 class=" text-primary d-block mt-1">{{ $account['id'] }}</h5>
    </div>
</div>

