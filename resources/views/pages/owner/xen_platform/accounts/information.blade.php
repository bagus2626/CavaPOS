@extends('layouts.owner')

@section('title', 'Xendit Account')
@section('page_title', 'Xendit Account')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('messages.owner.xen_platform.accounts.account_information') }}</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link {{ $tab === 'profile' ? 'active' : '' }}"
                                           data-tab="profile" data-toggle="tab" href="#profile" role="tab">
                                            {{ __('messages.owner.xen_platform.accounts.profile') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $tab === 'activity' ? 'active' : '' }}"
                                           data-tab="activity" data-toggle="tab" href="#activity" role="tab">
                                            {{ __('messages.owner.xen_platform.accounts.transactions') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $tab === 'invoices' ? 'active' : '' }}"
                                           data-tab="invoices" data-toggle="tab" href="#invoices" role="tab">
                                            {{ __('messages.owner.xen_platform.accounts.invoices') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $tab === 'balance' ? 'active' : '' }}"
                                           data-tab="balance" data-toggle="tab" href="#balance" role="tab">
                                            {{ __('messages.owner.xen_platform.accounts.balance') }}
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-2">
                                    <div class="tab-pane fade {{ $tab === 'profile' ? 'show active' : '' }}"
                                         id="profile"
                                         data-loaded="{{ $tab === 'profile' ? 'true' : 'false' }}">

                                        @if($tab === 'profile')
                                            @include('pages.owner.xen_platform.accounts.tab-panel.profile.index', ['data' => $data])
                                        @else
                                            <div class="text-center">
                                                <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                                                    <i class="fas fa-spinner fa-spin fa-lg"></i>
                                                    <div class="text-bold-500 mt-3">{{ __('messages.owner.xen_platform.accounts.loading_data') }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="tab-pane fade {{ $tab === 'activity' ? 'show active' : '' }}"
                                         id="activity"
                                         data-loaded="{{ $tab === 'activity' ? 'true' : 'false' }}">

                                        @if($tab === 'activity')
                                            @include('pages.owner.xen_platform.accounts.tab-panel.transaction.index', ['data' => $data])
                                        @else
                                            <div class="text-center">
                                                <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                                                    <i class="fas fa-spinner fa-spin fa-lg"></i>
                                                    <div class="text-bold-500 mt-3">{{ __('messages.owner.xen_platform.accounts.loading_data') }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="tab-pane fade {{ $tab === 'invoices' ? 'show active' : '' }}"
                                         id="invoices"
                                         data-loaded="{{ $tab === 'invoices' ? 'true' : 'false' }}">

                                        @if($tab === 'invoices')
                                            @include('pages.owner.xen_platform.accounts.tab-panel.invoice.index', ['data' => $data])
                                        @else
                                            <div class="text-center">
                                                <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                                                    <i class="fas fa-spinner fa-spin fa-lg"></i>
                                                    <div class="text-bold-500 mt-3">{{ __('messages.owner.xen_platform.accounts.loading_data') }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="tab-pane fade {{ $tab === 'balance' ? 'show active' : '' }}"
                                         id="balance"
                                         data-loaded="{{ $tab === 'balance' ? 'true' : 'false' }}">

                                        @if($tab === 'balance')
                                            @include('pages.owner.xen_platform.accounts.tab-panel.balance.index', ['data' => $data])
                                        @else
                                            <div class="text-center">
                                                <div class="d-flex flex-column align-items-center justify-content-center py-4 text-secondary">
                                                    <i class="fas fa-spinner fa-spin fa-lg"></i>
                                                    <div class="text-bold-500 mt-3">{{ __('messages.owner.xen_platform.accounts.loading_data') }}</div>
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
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const activePane = document.querySelector('.tab-pane.active');
        var tabName = activePane.id ?? 'profile';

        document.addEventListener("DOMContentLoaded", function () {
            const accountId = "{{ $accountId }}";
            const baseUrl = "/owner/user-owner/xen_platform/accounts";

            const initTabFunctions = {
                invoices: initInvoiceTab,
                activity: initTransactionTab,
                balance: initBalanceTab,
            };

            document.querySelectorAll('.nav.nav-tabs .nav-link').forEach(tabLink => {
                tabLink.addEventListener('click', function (e) {
                    console.log('sfsf')
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
                                 <i class="fas fa-spinner fa-spin fa-lg"></i>
                                 <div class="text-bold-500 mt-3">Loading data...</div>
                              </div>
                        </div>
                        `;

                        fetch(`${baseUrl}/tab/${tabName}`)
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
    <script src="{{ asset('script/owner/invoice.js') }}"></script>
    <script src="{{ asset('script/owner/transaction.js') }}"></script>
    <script src="{{ asset('script/owner/balance.js') }}"></script>
@endpush