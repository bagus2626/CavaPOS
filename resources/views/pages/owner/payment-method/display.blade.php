<!-- Table Card -->
<div class="modern-card">
    <div class="data-table-wrapper">
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
                                <span class="badge bg-primary text-white">{{ __('messages.owner.payment_methods.type_transfer') }}</span>
                            @elseif ($paymentMethod->payment_type === 'manual_ewallet')
                                <span class="badge bg-success text-white">{{ __('messages.owner.payment_methods.type_ewallet') }}</span>
                            @elseif ($paymentMethod->payment_type === 'manual_qris')
                                <span class="badge bg-info text-white">{{ __('messages.owner.payment_methods.type_qris') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-secondary text-ellipsis-1"
                                title="{{ $paymentMethod->provider_name }}">
                                {{ $paymentMethod->provider_name }} 
                                @if ($paymentMethod->provider_account_name)
                                    <br>
                                    <small class="text-muted">{{ $paymentMethod->provider_account_name }}</small>
                                    @if ($paymentMethod->provider_account_no)
                                        <br>
                                        <small class="text-muted">{{ $paymentMethod->provider_account_no }}</small>
                                    @endif
                                @endif
                            </span>
                        </td>
                        <td class="text-center">
                            @if($paymentMethod->qris_image_url)
                                <a href="javascript:void(0)"
                                class="js-qris-open"
                                data-modal="#imageModal{{ $paymentMethod->id }}">
                                    <img src="{{ asset('storage/' . $paymentMethod->qris_image_url) }}"
                                        alt="{{ $paymentMethod->provider_account_name }}"
                                        class="table-image"
                                        loading="lazy">
                                </a>

                                
                            @else
                                <span class="text-muted" style="font-size: 0.875rem;">
                                    -
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-secondary text-ellipsis-1"
                                title="{{ $paymentMethod->additional_info }}">
                                {{ $paymentMethod->additional_info ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge-table
                                {{ $paymentMethod->is_active ? 'status-active-soft' : 'status-inactive-soft' }}">
                                {{ $paymentMethod->is_active
                                    ? __('messages.owner.payment_methods.enabled')
                                    : __('messages.owner.payment_methods.disabled') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="{{ route('owner.user-owner.payment-methods.edit', $paymentMethod) }}"
                                    class="btn-table-action edit"
                                    title="{{ __('messages.owner.payment_methods.edit') }}">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>

                                <form action="{{ route('owner.user-owner.payment-methods.destroy', $paymentMethod) }}" method="POST"
                                    class="d-inline js-delete-form" data-name="{{ $paymentMethod->provider_name }}"
                                    style="display: inline;">
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
                        <td colspan="5" class="text-center">
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
    @foreach($paymentMethods as $paymentMethod)
        @if($paymentMethod->qris_image_url)
            @include('pages.owner.payment-method.modal', ['paymentMethod' => $paymentMethod])
        @endif
    @endforeach


    <!-- Pagination -->
    @if($paymentMethods->hasPages())
        <div class="table-pagination">
            {{ $paymentMethods->links() }}
        </div>
    @endif
</div>
<style>
    .text-ellipsis-1 {
        max-width: 300px;          /* atur sesuai kolom */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    /* status badge table */
    .status-badge-table {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 999px;
        line-height: 1;
    }

    /* ACTIVE: soft green */
    .status-active-soft {
        background-color: #dcfce7; /* green-100 */
        color: #166534;           /* green-700 */
    }

    /* DISABLED: soft gray */
    .status-inactive-soft {
        background-color: #f3f4f6; /* gray-100 */
        color: #4b5563;           /* gray-600 */
    }

</style>
