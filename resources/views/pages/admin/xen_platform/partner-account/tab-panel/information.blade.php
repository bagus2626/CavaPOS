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
                        <li class="breadcrumb-item active">Account Information</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Account Information</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'profile' ? 'active' : '' }}"
                                   data-tab="profile" data-toggle="tab" href="#profile" role="tab">
                                    Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'activity' ? 'active' : '' }}"
                                   data-tab="activity" data-toggle="tab" href="#activity" role="tab">
                                    Transactions
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'invoices' ? 'active' : '' }}"
                                   data-tab="invoices" data-toggle="tab" href="#invoices" role="tab">
                                    Invoices
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'balance' ? 'active' : '' }}"
                                   data-tab="balance" data-toggle="tab" href="#balance" role="tab">
                                    Balance
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'payout' ? 'active' : '' }}"
                                   data-tab="payout" data-toggle="tab" href="#payout" role="tab">
                                    Payout
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content mt-2">
                            <div class="tab-pane fade {{ $tab === 'profile' ? 'show active' : '' }}" id="profile"
                                 data-loaded="{{ $tab === 'profile' ? 'true' : 'false' }}">

                                @if($tab === 'profile')
                                    @include('pages.admin.xen_platform.partner-account.tab-panel.profile.index', ['data' => $data])
                                @else
                                    <div class="text-center">
                                        <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                                            <i class="bx bx-loader bx-spin bx-lg"></i>
                                            <div class="fw-medium mt-1">Loading data...</div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade {{ $tab === 'activity' ? 'show active' : '' }}" id="activity"
                                 data-loaded="{{ $tab === 'activity' ? 'true' : 'false' }}">

                                @if($tab === 'activity')
                                    @include('pages.admin.xen_platform.partner-account.tab-panel.transaction.index', ['data' => $data])
                                @else
                                    <div class="text-center">
                                        <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                                            <i class="bx bx-loader bx-spin bx-lg"></i>
                                            <div class="fw-medium mt-1">Loading data...</div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade {{ $tab === 'invoices' ? 'show active' : '' }}" id="invoices"
                                 data-loaded="{{ $tab === 'invoices' ? 'true' : 'false' }}">

                                @if($tab === 'invoices')
                                    @include('pages.admin.xen_platform.partner-account.tab-panel.invoice.index', ['data' => $data])
                                @else
                                    <div class="text-center">
                                        <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                                            <i class="bx bx-loader bx-spin bx-lg"></i>
                                            <div class="fw-medium mt-1">Loading data...</div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade {{ $tab === 'balance' ? 'show active' : '' }}" id="balance"
                                 data-loaded="{{ $tab === 'balance' ? 'true' : 'false' }}">

                                @if($tab === 'balance')
                                    @include('pages.admin.xen_platform.partner-account.tab-panel.balance.index', ['data' => $data])
                                @else
                                    <div class="text-center">
                                        <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                                            <i class="bx bx-loader bx-spin bx-lg"></i>
                                            <div class="fw-medium mt-1">Loading data...</div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade {{ $tab === 'payout' ? 'show active' : '' }}" id="payout"
                                 data-loaded="{{ $tab === 'payout' ? 'true' : 'false' }}">

                                @if($tab === 'payout')
                                    @include('pages.admin.xen_platform.partner-account.tab-panel.payout.index', ['data' => $data])
                                @else
                                    <div class="text-center">
                                        <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                                            <i class="bx bx-loader bx-spin bx-lg"></i>
                                            <div class="fw-medium mt-1">Loading data...</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-scripts')
    <script>
        const activePane = document.querySelector('.tab-pane.active');
        var tabName = activePane.id ?? 'profile';

        document.addEventListener("DOMContentLoaded", function () {
            const accountId = "{{ $accountId }}";
            const baseUrl = "/admin/xen_platform/partner-account";

            const initTabFunctions = {
                invoices: initInvoiceTab,
                activity: initTransactionTab,
                balance: initBalanceTab,
                payout: initPayoutTab,
            };

            document.querySelectorAll('.nav-link').forEach(tabLink => {
                tabLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    showPageLoader("Mohon tunggu...");

                    tabName = this.dataset.tab;
                    const targetPane = document.querySelector(`#${tabName}`);
                    const isLoaded = targetPane.dataset.loaded === 'true';


                    const newUrl = `${window.location.pathname}?tab=${tabName}`;
                    window.history.pushState(null, '', newUrl);

                    if (!isLoaded) {
                        targetPane.innerHTML = `
                          <div class="text-center">
                             <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                                  <i class="bx bx-loader bx-spin bx-lg"></i>
                                   <div class="fw-medium mt-1">Loading data...</div>
                              </div>
                        </div>
                        `;

                        fetch(`${baseUrl}/${accountId}/tab/${tabName}`)
                            .then(res => {
                                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                                return res.text();
                            })
                            .then(html => {
                                targetPane.innerHTML = html;
                                targetPane.dataset.loaded = 'true';

                                if (initTabFunctions[tabName]) {
                                    initTabFunctions[tabName](accountId);
                                }
                            })
                            .catch(error => {
                                console.error('Error loading tab data:', error);
                                targetPane.innerHTML = '<div class="p-5 text-center text-danger">Gagal memuat data. Silakan coba lagi.</div>';
                            })
                            .finally(() => hidePageLoader());

                    }else{
                        hidePageLoader();
                    }
                });
            });

            if (initTabFunctions[tabName] && typeof initTabFunctions[tabName] === 'function') {
                initTabFunctions[tabName](accountId);
            }
        });
    </script>
    <script src="{{ asset('script/admin/invoice.js') }}"></script>
    <script src="{{ asset('script/admin/transaction.js') }}"></script>
    <script src="{{ asset('script/admin/balance.js') }}"></script>
    <script src="{{ asset('script/admin/payout.js') }}"></script>
@endpush