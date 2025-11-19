<div class="d-flex align-items-center w-100 justify-content-between">
    <div>
        <h3 class="mb-0 text-bold-500 d-flex align-items-center">
            {{ $account['public_profile']['business_name'] }}
            @php
                $status = $account['status'] ?? 'UNKNOWN';
                $badgeClasses = [
                    'INVITED'       => 'bg-info',
                    'REGISTERED'    => 'bg-primary',
                    'AWAITING_DOCS' => 'bg-warning',
                    'LIVE'          => 'bg-success',
                    'SUSPENDED'     => 'bg-danger',
                    'UNKNOWN'       => 'bg-secondary',
                ];
            @endphp
            <span class="badge {{ $badgeClasses[$status] ?? 'bg-secondary' }} badge-pill ml-2">{{ $status }}</span>
        </h3>

        <h5 class=" text-primary d-block mt-1">{{ $account['id'] }}</h5>
    </div>
</div>

