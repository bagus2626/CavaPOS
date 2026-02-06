@extends('layouts.customer')

@section('title', 'Manual Payment')

@section('content')
@php
    /** @var \App\Models\Transaction\OrderPayment|null $payment */
    /** @var \App\Models\Owner\OwnerManualPayment|null $ownerManual */

    $payment = $payment ?? ($order->payment ?? null);
    $ownerManual = $ownerManual ?? ($payment?->ownerManualPayment);
    $type = $ownerManual->payment_type ?? null; // manual_tf | manual_ewallet | manual_qris
    $totalNumeric = (int) ($order->total_order_value ?? 0); // contoh: 15000
    $totalFormatted = 'Rp ' . number_format($totalNumeric, 0, ',', '.'); // contoh: Rp 15.000
@endphp

<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow p-6 md:p-8 border-t-4 border-[#ae1504]">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">
                    {{ __('messages.customer.orders.manual_payment.manual_payment') }}
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $headline ?? 'Silakan selesaikan pembayaran Anda' }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $subtitle ?? 'Upload bukti pembayaran agar pesanan bisa diproses.' }}
                </p>
            </div>

            <div class="text-right text-sm">
                <div class="font-mono text-xs uppercase text-gray-500">
                    {{ __('messages.customer.orders.manual_payment.order_code') }}
                </div>
                <div class="font-semibold tracking-widest">
                    {{ $order->booking_order_code }}
                </div>
                <div class="mt-1 text-xs text-gray-500">
                    {{ $order->created_at?->format('d M Y H:i') }}
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mt-4 text-xs text-green-700 bg-green-50 border border-green-200 rounded-md p-3">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 text-xs text-red-700 bg-red-50 border border-red-200 rounded-md p-3">
                <div class="font-semibold mb-1">{{ __('messages.customer.orders.manual_payment.upload_failed') }}:</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Info pemesan & meja --}}
        <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-gray-500">{{ __('Nama') }}</div>
                <div class="font-semibold">{{ $order->customer_name }}</div>
            </div>
            <div>
                <div class="text-gray-500">{{ __('Meja') }}</div>
                <div class="font-semibold">{{ $table->table_no ?? '-' }}</div>
            </div>
        </div>

        {{-- Card Instruksi Pembayaran --}}
        <div class="mt-8">
            <h2 class="text-sm font-semibold text-gray-800 mb-3">
                {{ __('messages.customer.orders.manual_payment.payment_detail') }}
            </h2>

            <div class="border rounded-xl p-4 bg-slate-50">
                <div class="w-full">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                        {{ __('messages.customer.orders.manual_payment.payment_method') }}
                    </p>
                    <p class="text-sm font-semibold text-gray-900">
                        @if ($type === 'manual_tf')
                            Transfer
                        @elseif ($type === 'manual_ewallet')
                        E-Wallet
                        @elseif ($type === 'manual_qris')
                        {{ __('messages.customer.orders.manual_payment.static_qr') }}
                        @endif
                    </p>

                    <p class="mt-3 text-xs text-gray-500 uppercase tracking-wide">
                        {{ __('Status') }}
                    </p>
                    <p class="text-sm font-semibold
                        @if(($payment->payment_status ?? '') === 'PAID') text-emerald-700
                        @elseif(($payment->payment_status ?? '') === 'PENDING') text-amber-600
                        @else text-red-600 @endif">
                        {{ $payment->payment_status ?? '-' }}
                    </p>
                    <div class="mt-4 flex justify-center md:justify-end">
                        <button
                            type="button"
                            onclick="cancelOrder({{ $order->id }}, '{{ $partner->slug }}', '{{ $table->table_code }}', '{{ $customer->id }}')"
                            class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-red-50 px-4 py-2 text-xs md:text-sm font-semibold text-red-700 hover:bg-red-100 hover:border-red-400 transition"
                        >
                            {{ __('messages.customer.orders.detail.cancel_order') ?? 'Cancel Order' }}
                        </button>
                        <form id="cancelOrderForm" method="POST" style="display:none;">
                            @csrf

                            <input type="hidden" name="order_id">
                            <input type="hidden" name="partner_slug">
                            <input type="hidden" name="table_code">
                            <input type="hidden" name="customer_id">
                        </form>
                    </div>
                </div>


                {{-- Switch by manual payment type --}}
                <div class="mt-5 pt-4 border-t space-y-4">
                    @if(in_array($type, ['manual_tf','manual_ewallet']))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div class="bg-white rounded-lg border p-3">
                                <div class="text-[11px] text-gray-500 uppercase tracking-wide">Provider</div>
                                <div class="font-semibold text-gray-900 mt-0.5">
                                    {{ $ownerManual->provider_name ?? '-' }}
                                </div>
                                <div class="text-[11px] text-gray-500 uppercase tracking-wide">{{ __('messages.customer.orders.manual_payment.destination_account_name') }}</div>
                                <div class="font-semibold text-gray-900 mt-0.5">
                                    {{ $ownerManual->provider_account_name ?? '-' }}
                                </div>
                                <div class="text-[11px] text-gray-500 uppercase tracking-wide">{{ __('messages.customer.orders.manual_payment.destination_account_no') }}</div>
                                <div class="mt-0.5 flex items-center justify-between gap-3">
                                    <div class="font-mono font-semibold text-gray-900">
                                        {{ $ownerManual->provider_account_no ?? '-' }}
                                    </div>
                                    @if(!empty($ownerManual->provider_account_no))
                                        <button
                                            type="button"
                                            onclick="copyText('{{ $ownerManual->provider_account_no }}')"
                                            class="px-3 py-1.5 text-xs rounded-lg border bg-white hover:bg-gray-50"
                                        >
                                            {{ __('messages.customer.orders.manual_payment.copy') }}
                                        </button>
                                    @endif
                                </div>
                                <div class="text-[11px] text-gray-500 uppercase tracking-wide">{{ __('messages.customer.orders.manual_payment.bill_amount') }}</div>
                                <div class="mt-0.5 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-gray-900">
                                            {{ $totalFormatted }}
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        onclick="copyText('{{ $totalNumeric }}', '{{ __('messages.customer.orders.manual_payment.copied') }}')"
                                        class="shrink-0 px-3 py-1.5 text-xs rounded-lg border bg-white hover:bg-gray-50"
                                    >
                                        {{ __('messages.customer.orders.manual_payment.copy') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Additional info (expand) --}}
                        @if(!empty($ownerManual->additional_info))
                            <details class="bg-white rounded-lg border p-3">
                                <summary class="cursor-pointer text-sm font-semibold text-gray-800">
                                    {{ __('messages.customer.orders.manual_payment.additional_info') }}
                                </summary>
                                <div class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                                    {{ $ownerManual->additional_info }}
                                </div>
                            </details>
                        @endif

                    @elseif($type === 'manual_qris')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div class="bg-white rounded-lg border p-3">
                                <div class="text-[11px] text-gray-500 uppercase tracking-wide">Provider</div>
                                <div class="font-semibold text-gray-900 mt-0.5">
                                    {{ $ownerManual->provider_name ?? 'QRIS' }}
                                </div>
                                <div class="text-[11px] text-gray-500 uppercase tracking-wide">{{ __('messages.customer.orders.manual_payment.qr_name') }}</div>
                                <div class="font-semibold text-gray-900 mt-0.5">
                                    {{ $ownerManual->provider_account_name ?? '-' }}
                                </div>
                                <div class="text-[11px] text-gray-500 uppercase tracking-wide">{{ __('messages.customer.orders.manual_payment.bill_amount') }}</div>
                                <div class="mt-0.5 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-gray-900">
                                            {{ $totalFormatted }}
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        onclick="copyText('{{ $totalNumeric }}', '{{ __('messages.customer.orders.manual_payment.copied') }}')"
                                        class="shrink-0 px-3 py-1.5 text-xs rounded-lg border bg-white hover:bg-gray-50"
                                    >
                                        {{ __('messages.customer.orders.manual_payment.copy') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- QRIS image --}}
                        @php
                            $qrisUrl = !empty($ownerManual->qris_image_url)
                                ? asset('storage/' . $ownerManual->qris_image_url)
                                : null;
                        @endphp

                        <div class="bg-white rounded-lg border p-4">
                            {{-- header bar --}}
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-[11px] text-gray-500 uppercase tracking-wide">QRIS</div>
                                    <div class="text-sm font-semibold text-gray-900">{{ __('messages.customer.orders.manual_payment.scan_to_pay') }}</div>
                                </div>

                                @if($qrisUrl)
                                    <a
                                        href="{{ $qrisUrl }}"
                                        download
                                        class="px-3 py-1.5 text-xs rounded-lg border bg-white hover:bg-gray-50 whitespace-nowrap"
                                    >
                                        {{ __('messages.customer.orders.manual_payment.download_qr') }}
                                    </a>
                                @endif
                            </div>

                            {{-- image --}}
                            @if($qrisUrl)
                                <div class="mt-6 flex justify-center">
                                    <div class="w-full max-w-[420px]">
                                        <div class="bg-white rounded-2xl border-2 border-gray-300 shadow-sm p-4 md:p-6">
                                            <img
                                                src="{{ $qrisUrl }}"
                                                alt="QRIS"
                                                class="w-full h-auto object-contain mx-auto"
                                                loading="lazy"
                                            >
                                        </div>
                                        <p class="mt-3 text-xs text-gray-500 text-center">
                                            {{ __('messages.customer.orders.manual_payment.make_sure_payment_amount') }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="mt-3 text-sm text-red-600">
                                    {{ __('messages.customer.orders.manual_payment.qris_image_not_available_yet') }}
                                </div>
                            @endif

                            {{-- additional info (accordion) --}}
                            @if(!empty($ownerManual->additional_info))
                                <details class="mt-4 bg-white rounded-lg border p-3">
                                    <summary class="cursor-pointer text-sm font-semibold text-gray-800 flex items-center justify-between">
                                        <span>{{ __('messages.customer.orders.manual_payment.additional_info') }}</span>
                                        <svg class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </summary>
                                    <div class="mt-3 text-sm text-gray-700 whitespace-pre-line">
                                        {{ $ownerManual->additional_info }}
                                    </div>
                                </details>
                            @endif

                        </div>

                    @else
                        <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3">
                            {{ __('messages.customer.orders.manual_payment.manual_payment_type_not_found') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Upload bukti bayar (untuk semua tipe) --}}
        <div class="mt-8">
            <h2 class="text-sm font-semibold text-gray-800 mb-3">
                {{ __('Upload Bukti Pembayaran') }}
            </h2>

            <form
                id="manualPaymentUploadForm"
                action="{{ route('customer.orders.manual-payment.upload', [
                    'partner_slug' => $partner->slug,
                    'table_code' => $table->table_code,
                    'order_id' => $order->id,
                ]) }}"
                method="POST"
                enctype="multipart/form-data"
                class="border rounded-xl p-4 bg-white"
            >
                @csrf

                <label class="block text-xs text-gray-600 mb-2">
                    {{ __('messages.customer.orders.manual_payment.payment_proof_requirement') }}
                </label>

                <input
                    type="file"
                    id="paymentProofInput"
                    name="payment_proof"
                    accept="image/*"
                    required
                    class="block w-full text-sm border rounded-lg p-2 bg-gray-50"
                >

                <label class="block text-xs text-gray-600 mt-3 mb-2">
                    {{ __('messages.customer.orders.manual_payment.note') }}
                </label>

                <textarea
                    name="payment_note"
                    rows="3"
                    class="w-full text-sm border rounded-lg p-2"
                    placeholder="{{ __('messages.customer.orders.manual_payment.example_placeholder') }}"
                ></textarea>

                <button
                    type="submit"
                    id="submitManualPaymentBtn"
                    disabled
                    class="mt-4 w-full md:w-auto px-5 py-2 rounded-lg
                        bg-[#ae1504] text-white text-sm font-semibold transition
                        hover:bg-[#8a1103]
                        disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#ae1504]"
                >
                    {{ __('messages.customer.orders.manual_payment.send_proof_of_payment') }}
                </button>

                <p class="mt-3 text-[11px] text-gray-500">
                    {{ __('messages.customer.orders.manual_payment.after_uploading') }}
                </p>
            </form>
        </div>
    </div>
</div>

{{-- Toast Copy --}}
<div
    id="copy-toast"
    class="fixed bottom-4 right-4 z-[9999] hidden items-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-white shadow-lg"
>
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    <span id="copy-toast-text" class="text-sm font-semibold">{{ __('messages.customer.orders.manual_payment.copied') }}</span>
</div>

@endsection
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('manualPaymentUploadForm');
    if (!form) return;

    const fileInput = document.getElementById('paymentProofInput');
    const submitBtn = document.getElementById('submitManualPaymentBtn');
    if (!fileInput || !submitBtn) return;

    // initial
    submitBtn.disabled = true;

    function disableBtn() {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
    function enableBtn() {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    // ✅ VALIDASI FILE + enable/disable tombol
    fileInput.addEventListener('change', () => {
        const file = fileInput.files && fileInput.files[0];

        if (!file) {
            disableBtn();
            return;
        }

        if (!file.type || !file.type.startsWith('image/')) {
            fileInput.value = ''; // batalin file
            disableBtn();

            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Format tidak didukung',
                    text: 'Silakan upload file gambar (JPG/PNG/WebP).',
                    confirmButtonColor: '#ae1504',
                });
            } else {
                alert('Silakan upload file gambar (JPG/PNG/WebP).');
            }
            return;
        }

        enableBtn();
    });

    // ✅ SUBMIT CONFIRMATION (Swal)
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const file = fileInput.files && fileInput.files[0];
        if (!file) {
            disableBtn();
            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Bukti pembayaran belum dipilih',
                    text: 'Silakan pilih file bukti pembayaran terlebih dahulu.',
                    confirmButtonColor: '#ae1504',
                });
            }
            return;
        }

        // safety: pastikan masih image
        if (!file.type || !file.type.startsWith('image/')) {
            fileInput.value = '';
            disableBtn();
            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Format tidak didukung',
                    text: 'Silakan upload file gambar (JPG/PNG/WebP).',
                    confirmButtonColor: '#ae1504',
                });
            }
            return;
        }

        // kalau Swal tidak ada, langsung submit
        if (!window.Swal) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Mengirim...';
            form.submit();
            return;
        }

        Swal.fire({
            icon: 'question',
            title: "{{ __('messages.customer.orders.manual_payment.submit_confirmation_1') }}",
            text: "{{ __('messages.customer.orders.manual_payment.submit_confirmation_2') }}",
            showCancelButton: true,
            confirmButtonText: "{{ __('messages.customer.orders.manual_payment.submit_yes') }}",
            cancelButtonText: "{{ __('messages.customer.orders.manual_payment.cancel') }}",
            confirmButtonColor: '#ae1504',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                submitBtn.textContent = 'Mengirim...';
                form.submit();
            }
        });
    });
});
</script>

{{-- helper copy --}}
<script>
    let copyToastTimer = null;

    function showCopyToast(message = '{{ __("messages.customer.orders.manual_payment.copied") }}') {
        const toast = document.getElementById('copy-toast');
        const textEl = document.getElementById('copy-toast-text');
        if (!toast || !textEl) return;

        textEl.textContent = message;

        toast.classList.remove('hidden');
        toast.classList.add('flex');

        clearTimeout(copyToastTimer);
        copyToastTimer = setTimeout(() => {
            toast.classList.add('hidden');
            toast.classList.remove('flex');
        }, 2000);
    }

    function fallbackCopy(text, message) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();

        try {
            document.execCommand('copy');
            showCopyToast(message);
        } catch (err) {
            console.error('Gagal menyalin:', err);
        }

        document.body.removeChild(textarea);
    }

    function copyText(text, message = '{{ __("messages.customer.orders.manual_payment.copied") }}') {
        if (!text) return;

        if (!navigator.clipboard) {
            fallbackCopy(text, message);
            return;
        }

        navigator.clipboard.writeText(text)
            .then(() => showCopyToast(message))
            .catch(() => fallbackCopy(text, message));
    }
</script>
<script>
    function cancelOrder(orderId, partnerSlug, tableCode, customerId) {
        if (!orderId) return;

        Swal.fire({
            title: "{{ __('messages.customer.orders.detail.cancel_order_confirm_1') }}",
            text: "{{ __('messages.customer.orders.detail.cancel_order_confirm_2') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ __('messages.customer.orders.detail.cancel_order_confirm_yes') }}",
            cancelButtonText: "{{ __('messages.customer.orders.detail.cancel_order_confirm_no') }}",
            confirmButtonColor: '#d33',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('cancelOrderForm');
                form.action = `/customer/cancel-order/${orderId}`;
                form.querySelector('input[name="order_id"]').value = orderId;
                form.querySelector('input[name="partner_slug"]').value = partnerSlug;
                form.querySelector('input[name="table_code"]').value = tableCode;
                form.querySelector('input[name="customer_id"]').value = customerId;
                form.submit();
            }
        });
    }
</script>
