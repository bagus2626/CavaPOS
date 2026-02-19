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
                        <div class="mobile-header-avatar-placeholder">
                            <span class="material-symbols-outlined">payment</span>
                        </div>
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

        {{-- Mobile Payment List --}}
        <div class="mobile-employee-list">
            @forelse ($paymentMethods as $paymentMethod)
                <div class="payment-card-modern">
                    <div class="card-top-section">
                        <div class="card-icon-wrapper">
                            @if ($paymentMethod->qris_image_url)
                                <div class="qr-thumbnail"
                                    onclick="openImageModal('{{ asset('storage/' . $paymentMethod->qris_image_url) }}', '{{ $paymentMethod->provider_name }}')">
                                    <img src="{{ asset('storage/' . $paymentMethod->qris_image_url) }}" alt="">
                                    <div class="qr-overlay">
                                        <span class="material-symbols-outlined">fullscreen</span>
                                    </div>
                                </div>
                            @else
                                <div class="card-icon-circle">
                                    @if ($paymentMethod->payment_type === 'manual_tf')
                                        <span class="material-symbols-outlined">account_balance</span>
                                    @elseif ($paymentMethod->payment_type === 'manual_ewallet')
                                        <span class="material-symbols-outlined">account_balance_wallet</span>
                                    @else
                                        <span class="material-symbols-outlined">qr_code_scanner</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="card-status-indicator">
                            <div class="status-pill {{ $paymentMethod->is_active ? 'active' : 'inactive' }}">
                                <span class="status-dot"></span>
                                <span
                                    class="status-text">{{ $paymentMethod->is_active ? __('messages.owner.payment_methods.enabled') : __('messages.owner.payment_methods.disabled') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-content-section">
                        <h3 class="payment-provider-name">{{ $paymentMethod->provider_name }}</h3>

                        <div class="payment-type-row">
                            <span class="type-label {{ $paymentMethod->payment_type }}">
                                @if ($paymentMethod->payment_type === 'manual_tf')
                                    Bank Transfer
                                @elseif ($paymentMethod->payment_type === 'manual_ewallet')
                                    E-Wallet
                                @else
                                    QRIS Payment
                                @endif
                            </span>
                        </div>

                        @if ($paymentMethod->provider_account_no)
                            <div class="account-info-row">
                                <span class="material-symbols-outlined">numbers</span>
                                <span class="account-text">{{ $paymentMethod->provider_account_no }}</span>
                            </div>
                        @endif

                        {{-- TAMBAHKAN INI: Additional Info --}}
                        @if ($paymentMethod->additional_info)
                            <div class="additional-info-row">
                                <span class="material-symbols-outlined">info</span>
                                <span class="additional-text">{{ $paymentMethod->additional_info }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer-actions">
                        <a href="{{ route('owner.user-owner.payment-methods.edit', $paymentMethod) }}"
                            class="footer-btn primary">
                            <span class="material-symbols-outlined">edit_square</span>
                            <span>Edit</span>
                        </a>
                        <div class="btn-divider"></div>
                        <button
                            onclick="deletePaymentMethod({{ $paymentMethod->id }}, '{{ $paymentMethod->provider_name }}')"
                            class="footer-btn danger">
                            <span class="material-symbols-outlined">delete_forever</span>
                            <span>Delete</span>
                        </button>
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

    /* Modern Payment Card */
    .payment-card-modern {
        background: #ffffff;
        border-radius: 20px;
        margin-bottom: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .payment-card-modern:active {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .card-top-section {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 16px 16px 0;
    }

    .card-icon-wrapper {
        flex-shrink: 0;
    }

    .qr-thumbnail {
        position: relative;
        width: 64px;
        height: 64px;
        border-radius: 14px;
        overflow: hidden;
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .qr-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .qr-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .qr-thumbnail:active .qr-overlay {
        opacity: 1;
    }

    .qr-overlay .material-symbols-outlined {
        color: white;
        font-size: 28px;
    }

    .card-icon-circle {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, #b3311d 0%, #f65c5c 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(241, 99, 99, 0.3);
    }

    .card-icon-circle .material-symbols-outlined {
        color: white;
        font-size: 32px;
    }

    .card-status-indicator {
        flex-shrink: 0;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-pill.active {
        background: linear-gradient(135deg, #d4f4dd 0%, #c8f0d4 100%);
        color: #16a34a;
    }

    .status-pill.inactive {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #dc2626;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-text {
        line-height: 1;
    }

    .card-content-section {
        padding: 12px 16px 16px;
    }

    .payment-provider-name {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 10px 0;
        letter-spacing: -0.02em;
    }

    .payment-type-row {
        margin-bottom: 10px;
    }

    .type-label {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    .type-label.manual_tf {
        background: #dbeafe;
        color: #1e40af;
    }

    .type-label.manual_ewallet {
        background: #dcfce7;
        color: #16a34a;
    }

    .type-label.manual_qris {
        background: #fef3c7;
        color: #d97706;
    }

    .account-info-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    .account-info-row .material-symbols-outlined {
        font-size: 20px;
        color: #64748b;
    }

    .account-text {
        font-size: 14px;
        font-family: 'Courier New', monospace;
        color: #334155;
        font-weight: 500;
    }

    .card-footer-actions {
        display: flex;
        align-items: stretch;
        border-top: 1px solid #f1f5f9;
    }

    .footer-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px;
        border: none;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
    }

    .footer-btn.primary {
        color: #3b82f6;
    }

    .footer-btn.primary:active {
        background: #eff6ff;
    }

    .footer-btn.danger {
        color: #ef4444;
    }

    .footer-btn.danger:active {
        background: #fef2f2;
    }

    .footer-btn .material-symbols-outlined {
        font-size: 20px;
    }

    .btn-divider {
        width: 1px;
        background: #f1f5f9;
    }

    .additional-info-row {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 10px 12px;
        background: #fefce8;
        border-radius: 10px;
        border: 1px solid #fde047;
        margin-top: 8px;
    }

    .additional-info-row .material-symbols-outlined {
        font-size: 20px;
        color: #ca8a04;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .additional-text {
        font-size: 13px;
        color: #713f12;
        line-height: 1.5;
        word-break: break-word;
    }
</style>
