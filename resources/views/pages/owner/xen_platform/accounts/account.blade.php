<div class="detail-hero-info">
    <h3 class="detail-hero-name">{{ $account['public_profile']['business_name'] }}</h3>
    <p class="detail-hero-subtitle">
        {{ $account['id'] }}
    </p>
    <div class="detail-hero-badges">
        @php
            $status = $account['status'] ?? 'UNKNOWN';
            $badgeClasses = [
                'INVITED'       => 'badge-info',
                'REGISTERED'    => 'badge-primary',
                'AWAITING_DOCS' => 'badge-warning',
                'LIVE'          => 'badge-success',
                'SUSPENDED'     => 'badge-danger',
                'UNKNOWN'       => 'badge-secondary',
            ];
        @endphp
        <span class="badge-modern {{ $badgeClasses[$status] ?? 'badge-secondary' }}">
            {{ $status }}
        </span>
    </div>
</div>