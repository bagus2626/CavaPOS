<div class="row">
    {{-- Main Content - Single Column --}}
    <div class="col-lg-12">
        
        {{-- Balance Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">account_balance_wallet</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.balance') }}</h3>
                </div>
                
                <div class="detail-info-item">
                    <div class="detail-info-value">
                        <h2 class="font-weight-bold text-success" style="font-size: 2rem; margin: 0;">
                            IDR {{ number_format($data['balance']) }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile Card --}}
        <div class="modern-card">
            <div class="card-body-modern">
                <div class="section-header">
                    <div class="section-icon section-icon-red">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <h3 class="section-title">{{ __('messages.owner.xen_platform.accounts.profile') }}</h3>
                </div>

                <div class="data-table-wrapper">
                    <table class="table-details">
                        <tbody>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.name') }}</td>
                                <td class="value-col">: {{ $data['public_profile']['business_name'] }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">Email</td>
                                <td class="value-col">: {{ $data['email'] }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.account_id') }}</td>
                                <td class="value-col">: {{ $data['id'] }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.date_created') }}</td>
                                <td class="value-col">: {{ $data['created'] }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">{{ __('messages.owner.xen_platform.accounts.account_type') }}</td>
                                <td class="value-col">: 
                                    @php
                                        $accountType = $data['type'] ?? 'UNKNOWN';
                                        $typeBadge = [
                                            'MANAGED' => 'badge-primary',
                                            'OWNED' => 'badge-info',
                                            'CUSTOM' => 'badge-warning',
                                            'UNKNOWN' => 'badge-secondary',
                                        ];
                                        $badgeClass = $typeBadge[$accountType] ?? 'badge-secondary';
                                    @endphp
                                    <span class="badge-modern {{ $badgeClass }}">
                                        {{ $accountType }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col">Status</td>
                                <td class="value-col">: 
                                    @php
                                        $accountStatus = $data['status'] ?? 'UNKNOWN';
                                        $statusBadge = [
                                            'INVITED' => 'badge-info',
                                            'REGISTERED' => 'badge-primary',
                                            'AWAITING_DOCS' => 'badge-warning',
                                            'LIVE' => 'badge-success',
                                            'SUSPENDED' => 'badge-danger',
                                            'UNKNOWN' => 'badge-secondary',
                                        ];
                                        $badgeClass = $statusBadge[$accountStatus] ?? 'badge-secondary';
                                    @endphp
                                    <span class="badge-modern {{ $badgeClass }}">
                                        {{ $accountStatus }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>