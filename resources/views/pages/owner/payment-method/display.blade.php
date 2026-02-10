@php
    use Illuminate\Support\Str;
@endphp

<link rel="stylesheet" href="{{ asset('css/mobile-owner.css') }}">
<div class="modern-card">

    {{-- =======================
    DESKTOP: TABLE
  ======================= --}}
    <div class="data-table-wrapper only-desktop">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">#</th>
                    <th>{{ __('messages.owner.payment_methods.payment_type') }}</th>
                    <th>{{ __('messages.owner.payment_methods.provider') }}</th>
                    <th class="text-center" style="width: 150px;">{{ __('messages.owner.payment_methods.picture') }}</th>
                    <th>{{ __('messages.owner.payment_methods.additional_info') }}</th>
                    <th>{{ __('messages.owner.payment_methods.status') }}</th>
                    <th class="text-center" style="width: 180px;">{{ __('messages.owner.payment_methods.actions') }}</th>
                </tr>
            </thead>

            <tbody id="paymentMethodTableBody">
                @forelse($paymentMethods as $index => $paymentMethod)
                    <tr class="table-row">
                        <td class="text-center text-muted">
                            {{ $paymentMethods->firstItem() + $index }}
                        </td>

                        <td>
                            @if ($paymentMethod->payment_type === 'manual_tf')
                                <span
                                    class="badge bg-primary text-white">{{ __('messages.owner.payment_methods.type_transfer') }}</span>
                            @elseif ($paymentMethod->payment_type === 'manual_ewallet')
                                <span
                                    class="badge bg-success text-white">{{ __('messages.owner.payment_methods.type_ewallet') }}</span>
                            @elseif ($paymentMethod->payment_type === 'manual_qris')
                                <span
                                    class="badge bg-info text-white">{{ __('messages.owner.payment_methods.type_qris') }}</span>
                            @endif
                        </td>

                        <td>
                            <span class="text-secondary text-ellipsis-1" title="{{ $paymentMethod->provider_name }}">
                                {{ $paymentMethod->provider_name }}
                                @if ($paymentMethod->provider_account_name)
                                    <br><small class="text-muted">{{ $paymentMethod->provider_account_name }}</small>
                                @endif
                                @if ($paymentMethod->provider_account_no)
                                    <br><small class="text-muted">{{ $paymentMethod->provider_account_no }}</small>
                                @endif
                            </span>
                        </td>

                        <td class="text-center">
                            @if ($paymentMethod->qris_image_url)
                                <a href="javascript:void(0)" class="js-qris-open"
                                    data-modal="#imageModal{{ $paymentMethod->id }}">
                                    <img src="{{ asset('storage/' . $paymentMethod->qris_image_url) }}"
                                        alt="{{ $paymentMethod->provider_account_name }}" class="table-image"
                                        loading="lazy">
                                </a>
                            @else
                                <span class="text-muted" style="font-size: 0.875rem;">-</span>
                            @endif
                        </td>

                        <td>
                            <span class="text-secondary text-ellipsis-1" title="{{ $paymentMethod->additional_info }}">
                                {{ $paymentMethod->additional_info ?? '-' }}
                            </span>
                        </td>

                        <td>
                            <span
                                class="status-badge-table {{ $paymentMethod->is_active ? 'status-active-soft' : 'status-inactive-soft' }}">
                                {{ $paymentMethod->is_active ? __('messages.owner.payment_methods.enabled') : __('messages.owner.payment_methods.disabled') }}
                            </span>
                        </td>

                        <td class="text-center">
                            <div class="table-actions">
                                <a href="{{ route('owner.user-owner.payment-methods.edit', $paymentMethod) }}"
                                    class="btn-table-action edit"
                                    title="{{ __('messages.owner.payment_methods.edit') }}">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>

                                <form action="{{ route('owner.user-owner.payment-methods.destroy', $paymentMethod) }}"
                                    method="POST" class="d-inline js-delete-form"
                                    data-name="{{ $paymentMethod->provider_name }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-table-action delete"
                                        title="{{ __('messages.owner.payment_methods.delete') }}">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">payment</span>
                                <h4>{{ __('messages.owner.payment_methods.no_results_found') ?? 'No payment methods found' }}
                                </h4>
                                <p>{{ __('messages.owner.payment_methods.add_first_payment_method') ?? 'Add your first payment method to get started' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- =======================
    MOBILE: HEADER + SEARCH + CARDS
  ======================= --}}
    <div class="only-mobile">
        {{-- Mobile Header with Avatar & Search --}}
        <div class="mobile-header-section">
            <div class="mobile-header-card">
                <div class="mobile-header-content">
                    <div class="mobile-header-left">
                        <h2 class="mobile-header-title">Payment Methods</h2>
                        <p class="mobile-header-subtitle">{{ $paymentMethods->total() }} Total Methods</p>
                    </div>
                    <div class="mobile-header-right">
                        @if (auth()->user()->image)
                            @php
                                $userImg = Str::startsWith(auth()->user()->image, ['http://', 'https://'])
                                    ? auth()->user()->image
                                    : asset('storage/' . auth()->user()->image);
                            @endphp
                            <img src="{{ $userImg }}" alt="Profile" class="mobile-header-avatar">
                        @else
                            <div class="mobile-header-avatar-placeholder">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Mobile Search Form --}}
                <div class="mobile-search-wrapper">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="mobile-search-box">
                            <span class="mobile-search-icon">
                                <span class="material-symbols-outlined">search</span>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="mobile-search-input"
                                placeholder="{{ __('messages.owner.payment_methods.search_placeholder') }}"
                                oninput="searchFilter(this, 600)">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Mobile Payment List (MENGGUNAKAN CLASS DARI CSS UNIVERSAL) --}}
        <div class="mobile-employee-list">
            @forelse ($paymentMethods as $paymentMethod)
                <div class="employee-card-wrapper">
                    {{-- Swipe Actions Background --}}
                    <div class="swipe-actions">
                        <a href="{{ route('owner.user-owner.payment-methods.edit', $paymentMethod) }}"
                            class="swipe-action edit">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                        <button type="button"
                            onclick="deletePaymentMethod({{ $paymentMethod->id }}, '{{ $paymentMethod->provider_name }}')"
                            class="swipe-action delete">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>

                    {{-- Card Content --}}
                    <div class="employee-card-link" style="cursor: default;">
                        <div class="employee-card-clickable">
                            <div class="employee-card__left">
                                <div class="employee-card__avatar">
                                    @if ($paymentMethod->qris_image_url)
                                        <img src="{{ asset('storage/' . $paymentMethod->qris_image_url) }}"
                                            alt="{{ $paymentMethod->provider_name }}" loading="lazy"
                                            onclick="openImageModal('{{ asset('storage/' . $paymentMethod->qris_image_url) }}', '{{ $paymentMethod->provider_name }}')">
                                    @else
                                        <div class="user-avatar-placeholder">
                                            <span class="material-symbols-outlined">payment</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="employee-card__info">
                                    <div class="employee-card__name">{{ $paymentMethod->provider_name }}</div>
                                    <div class="employee-card__details">
                                        @if ($paymentMethod->payment_type === 'manual_tf')
                                            <span class="detail-text">Bank Transfer</span>
                                        @elseif ($paymentMethod->payment_type === 'manual_ewallet')
                                            <span class="detail-text">E-Wallet</span>
                                        @elseif ($paymentMethod->payment_type === 'manual_qris')
                                            <span class="detail-text">QRIS</span>
                                        @endif

                                        @if ($paymentMethod->provider_account_no)
                                            <span class="detail-separator">•</span>
                                            <span class="detail-text mono">{{ $paymentMethod->provider_account_no }}</span>
                                        @endif
                                    </div>
                                    <div style="margin-top: 6px;">
                                        <span
                                            class="badge-modern {{ $paymentMethod->is_active ? 'badge-success' : 'badge-danger' }}"
                                            style="font-size: 11px; padding: 4px 10px;">
                                            {{ $paymentMethod->is_active ? __('messages.owner.payment_methods.enabled') : __('messages.owner.payment_methods.disabled') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="employee-card__right" style="opacity: 0;">
                                <span class="material-symbols-outlined chevron">chevron_right</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="table-empty-state">
                    <span class="material-symbols-outlined">payment</span>
                    <h4>{{ __('messages.owner.payment_methods.no_results_found') ?? 'No payment methods found' }}</h4>
                    <p>{{ __('messages.owner.payment_methods.add_first_payment_method') ?? 'Add your first payment method to get started' }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- MODALS --}}
    @foreach ($paymentMethods as $paymentMethod)
        @if ($paymentMethod->qris_image_url)
            @include('pages.owner.payment-method.modal', ['paymentMethod' => $paymentMethod])
        @endif
    @endforeach

    {{-- =======================
    PAGINATION
  ======================= --}}
    @if ($paymentMethods->hasPages())
        <div class="table-pagination">
            {{ $paymentMethods->links() }}
        </div>
    @endif

</div>

{{-- Floating Add Button (Mobile Only) --}}
<a href="{{ route('owner.user-owner.payment-methods.create') }}" class="btn-add-employee-mobile">
    <span class="material-symbols-outlined">add</span>
</a>

{{-- Mobile Image Modal --}}
<div id="mobileImageModal" class="qris-modal" aria-hidden="true">
    <div class="qris-modal__backdrop" data-close></div>
    <div class="qris-modal__dialog">
        <div class="qris-modal__header">
            <h3 class="qris-modal__title" id="modalImageTitle">QRIS Image</h3>
            <button type="button" class="qris-modal__close" data-close>
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="qris-modal__body">
            <img id="modalImageSrc" src="" alt="QRIS" class="qris-modal__img">
        </div>
    </div>
</div>

<script>
    function openImageModal(imageSrc, title) {
        const modal = document.getElementById('mobileImageModal');
        const img = document.getElementById('modalImageSrc');
        const titleEl = document.getElementById('modalImageTitle');

        if (modal && img && titleEl) {
            img.src = imageSrc;
            titleEl.textContent = title || 'QRIS Image';
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const backdrop = document.querySelector('.qris-modal__backdrop');
        const closeBtn = document.querySelector('.qris-modal__close');
        const modal = document.getElementById('mobileImageModal');

        function closeModal() {
            if (modal) {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeModal);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) {
                closeModal();
            }
        });
    });

    let searchTimeout;

    function searchFilter(input, delay) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            input.form.submit();
        }, delay);
    }

    function deletePaymentMethod(id, name) {
        Swal.fire({
            title: '{{ __('messages.owner.payment_methods.delete_confirmation_title') ?? 'Delete payment method?' }}',
            text: `{{ __('messages.owner.payment_methods.delete_confirmation_text') ?? 'Payment method' }} "${name}" {{ __('messages.owner.payment_methods.delete_confirmation_suffix') ?? 'will be deleted.' }}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#b3311d',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '{{ __('messages.owner.payment_methods.confirm_delete') ?? 'Yes, delete' }}',
            cancelButtonText: '{{ __('messages.owner.payment_methods.cancel') ?? 'Cancel' }}'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/owner/user-owner/payment-methods/${id}`;
                form.style.display = 'none';
                form.innerHTML = `
          @csrf
          <input type="hidden" name="_method" value="DELETE">
        `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

<style>
    .mono {
        font-family: 'Courier New', monospace;
    }

    .text-ellipsis-1 {
        max-width: 260px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    .qris-modal {
        position: fixed;
        inset: 0;
        display: none;
        z-index: 2000;
    }

    .qris-modal.is-open {
        display: block;
    }

    .qris-modal__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, .55);
    }

    .qris-modal__dialog {
        position: relative;
        width: min(720px, calc(100% - 24px));
        max-height: calc(100vh - 120px);
        margin: 90px auto 30px;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        z-index: 1;
    }

    @media (max-width: 576px) {
        .qris-modal__dialog {
            margin: 70px auto 20px;
            max-height: calc(100vh - 90px);
        }
    }

    .qris-modal__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-bottom: 1px solid rgba(0, 0, 0, .08);
    }

    .qris-modal__title {
        margin: 0;
        font-size: 16px;
    }

    .qris-modal__close {
        border: none;
        background: transparent;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
    }

    .qris-modal__body {
        padding: 12px;
        text-align: center;
        overflow: auto;
    }

    .qris-modal__img {
        max-height: 80vh;
        width: auto;
        max-width: 100%;
        object-fit: contain;
    }
</style>