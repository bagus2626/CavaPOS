@extends('layouts.owner')

@section('title', __('messages.owner.payment_methods.payment_method_list'))
@section('page_title', __('messages.owner.payment_methods.payment_methods'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            {{-- Page Header - Desktop Only --}}
            <div class="page-header only-desktop">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.payment_methods.payment_methods') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.payment_methods.subtitle') }}</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">check_circle</span>
                    </div>
                    <div class="alert-content">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-modern">
                    <div class="alert-icon">
                        <span class="material-symbols-outlined">error</span>
                    </div>
                    <div class="alert-content">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            {{-- Search & Filter Card - Desktop Only --}}
            <div class="modern-card mb-4 only-desktop">
                <div class="card-body-modern" style="padding: var(--spacing-lg) var(--spacing-xl);">
                    <form method="GET" action="{{ route('owner.user-owner.payment-methods.index') }}"
                        id="paymentFilterForm">
                        <div class="table-controls">
                            <div class="search-filter-group">

                                <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                                    <span class="input-icon">
                                        <span class="material-symbols-outlined">search</span>
                                    </span>

                                    <input type="text" name="search" id="paymentSearchInput"
                                        value="{{ $search ?? request('search') }}" class="form-control-modern with-icon"
                                        placeholder="{{ __('messages.owner.payment_methods.search_placeholder') }}"
                                        oninput="searchFilter(this, 500)">
                                    <input type="hidden" name="page" id="pageInput"
                                        value="{{ request('page', 1) }}">
                                </div>
                            </div>

                            <a href="{{ route('owner.user-owner.payment-methods.create') }}"
                                class="btn-modern btn-primary-modern">
                                <span class="material-symbols-outlined">add</span>
                                {{ __('messages.owner.payment_methods.add_payment_method') ?? 'Add Payment Method' }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>


            @include('pages.owner.payment-method.display')

        </div>
    </div>
@endsection

<style>
    /* Hide desktop elements on mobile */
    @media (max-width: 768px) {
        .only-desktop {
            display: none !important;
        }
    }

    /* Hide mobile elements on desktop */
    @media (min-width: 769px) {
        .only-mobile {
            display: none !important;
        }
    }
</style>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DELETE confirmation (for all delete forms)
            document.querySelectorAll('.js-delete-form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const name = form.dataset.name || 'data ini';

                    Swal.fire({
                        title: '{{ __('messages.owner.payment_methods.delete_confirmation_title') ?? 'Delete payment method?' }}',
                        text: `{{ __('messages.owner.payment_methods.delete_confirmation_text') ?? 'Payment method' }} "${name}" {{ __('messages.owner.payment_methods.delete_confirmation_suffix') ?? 'will be deleted.' }}`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __('messages.owner.payment_methods.confirm_delete') ?? 'Yes, delete' }}',
                        cancelButtonText: '{{ __('messages.owner.payment_methods.cancel') ?? 'Cancel' }}',
                        reverseButtons: true,
                        confirmButtonColor: '#b3311d',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });

        function searchFilter(el, delay = 500) {
            const form = document.getElementById('paymentFilterForm');
            const pageInput = document.getElementById('pageInput');
            if (!form || !el) return;

            // reset ke halaman 1 setiap kali user mengubah input
            if (pageInput) pageInput.value = 1;

            // clear timer sebelumnya
            if (el._debounceTimer) clearTimeout(el._debounceTimer);

            // debounce: submit 500ms setelah user berhenti mengetik
            el._debounceTimer = setTimeout(() => {
                form.submit();
            }, delay);
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let activeModal = null;

            function openModal(modal) {
                if (!modal) return;
                activeModal = modal;
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function closeModal(modal) {
                if (!modal) return;
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                if (activeModal === modal) activeModal = null;
            }

            // open from trigger
            document.querySelectorAll('.js-qris-open').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const selector = btn.getAttribute('data-modal');
                    openModal(document.querySelector(selector));
                });
            });

            // close when click backdrop or close button
            document.addEventListener('click', (e) => {
                const closeEl = e.target.closest('[data-close]');
                if (!closeEl) return;

                const modal = e.target.closest('.qris-modal');
                closeModal(modal);
            });

            // close on ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && activeModal) closeModal(activeModal);
            });
        });
    </script>
@endpush