@extends('layouts.owner')

@section('title', __('messages.owner.products.promotions.promotion_list'))
@section('page_title', __('messages.owner.products.promotions.all_promotions'))

@section('content')
    <div class="modern-container">
        <div class="container-modern">
            {{-- Page Header - Desktop Only --}}
            <div class="page-header only-desktop">
                <div class="header-content">
                    <h1 class="page-title">{{ __('messages.owner.products.promotions.all_promotions') }}</h1>
                    <p class="page-subtitle">{{ __('messages.owner.products.promotions.manage_promotions_subtitle') }}</p>
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
                    <form method="GET" action="{{ route('owner.user-owner.promotions.index') }}" id="promotionFilterForm">
                        <div class="table-controls">
                            <div class="search-filter-group">

                                <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                                    <span class="input-icon">
                                        <span class="material-symbols-outlined">search</span>
                                    </span>

                                    <input type="text" name="q" id="promotionSearchInput"
                                        value="{{ $q ?? request('q') }}" class="form-control-modern with-icon"
                                        placeholder="{{ __('messages.owner.products.promotions.search_placeholder') }}"
                                        oninput="searchFilter(this, 500)">
                                    <input type="hidden" name="page" id="pageInput" value="{{ request('page', 1) }}">
                                </div>

                                <div class="select-wrapper" style="min-width: 200px;">
                                    <select name="type" class="form-control-modern"
                                        onchange="document.getElementById('promotionFilterForm').submit()">
                                        <option value="">{{ __('messages.owner.products.promotions.all') }}</option>
                                        <option value="percentage" @selected(request('type') === 'percentage')>
                                            {{ __('messages.owner.products.promotions.percentage') }}
                                        </option>
                                        <option value="amount" @selected(request('type') === 'amount')>
                                            {{ __('messages.owner.products.promotions.reduced_fare') }}
                                        </option>
                                    </select>
                                    <span class="material-symbols-outlined select-arrow">expand_more</span>
                                </div>
                            </div>

                            <a href="{{ route('owner.user-owner.promotions.create') }}"
                                class="btn-modern btn-primary-modern">
                                <span class="material-symbols-outlined">add</span>
                                {{ __('messages.owner.products.promotions.add_promotion') ?? 'Add Promotion' }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>


            @include('pages.owner.products.promotion.display')

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
            document.querySelectorAll('.js-delete-promo-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const name = form.dataset.name || 'Promotion';

                    Swal.fire({
                        title: '{{ __('messages.owner.products.promotions.delete_confirmation_1') }}',
                        text: `{{ __('messages.owner.products.promotions.delete_confirmation_2') }}: "${name}"`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#b3311d',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '{{ __('messages.owner.products.promotions.delete_confirmation_3') }}',
                        cancelButtonText: '{{ __('messages.owner.products.promotions.cancel') }}',
                        reverseButtons: true
                    }).then((res) => {
                        if (res.isConfirmed) form.submit();
                    });
                });
            });
        });

        function searchFilter(el, delay = 500) {
            const form = document.getElementById('promotionFilterForm');
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
@endpush