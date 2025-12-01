@extends('layouts.customer')

@section('title', __('messages.customer.orders.histories.order_history'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow p-6 md:p-8 border-t-4 border-choco">
        {{-- Header --}}
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">
                    {{ __('messages.customer.orders.histories.order_history') }}
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    {{ __('messages.customer.orders.histories.order_list_that_youve_made_in') }} <strong>{{ $partner->name }}</strong>.
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ __('messages.customer.orders.histories.recent_table') }}: {{ $table->table_no ?? '-' }} ({{ $table->table_code }})
                </p>
            </div>
        </div>

        {{-- List Riwayat --}}
        <div id="order-history-list" class="mt-6 space-y-4">
            @include('pages.customer.orders.partials._order-cards', [
                'orderHistory' => $orderHistory,
                'partner'      => $partner,
                'table'        => $table,
                'partner_slug' => $partner_slug,
                'table_code'   => $table_code,
            ])
        </div>

        {{-- Kalau sama sekali belum ada riwayat --}}
        @if($orderHistory->isEmpty())
            <div class="border rounded-xl p-4 bg-gray-50 text-sm text-gray-600 mt-4">
                {{ __('messages.customer.orders.histories.you_dont_have_histories') }}
            </div>
        @endif


        {{-- Load More --}}
        @if($orderHistory->hasMorePages())
            <div class="mt-6 text-center">
                <button id="load-more-orders"
                        type="button"
                        class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-60 disabled:cursor-not-allowed"
                        data-next-page="{{ $orderHistory->nextPageUrl() }}">
                    <span class="load-more-text">{{ __('messages.customer.orders.histories.load_more') ?? 'Load more' }}</span>
                    <svg class="ml-2 w-4 h-4 animate-spin hidden load-more-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3.5-3.5L12 0v4a8 8 0 00-8 8h4z"></path>
                    </svg>
                </button>
            </div>
        @endif


        {{-- Tombol kembali ke menu --}}
        <div class="mt-8">
            <a href="{{ route('customer.menu.index', [$partner_slug, $table_code]) }}"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-choco text-sm text-choco hover:bg-[#fee2e2]">
                {{ __('messages.customer.orders.histories.back_to_menu') }}
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const loadMoreBtn = document.getElementById('load-more-orders');
    const listEl = document.getElementById('order-history-list');

    if (!loadMoreBtn || !listEl) return;

    const textSpan = loadMoreBtn.querySelector('.load-more-text');
    const spinner  = loadMoreBtn.querySelector('.load-more-spinner');

    loadMoreBtn.addEventListener('click', function () {
        let nextUrl = loadMoreBtn.getAttribute('data-next-page');
        if (!nextUrl) return;

        loadMoreBtn.disabled = true;
        if (spinner) spinner.classList.remove('hidden');
        if (textSpan) textSpan.textContent = '{{ __('messages.customer.orders.histories.loading') ?? 'Loading…' }}';

        // Tambah flag AJAX biar controller bisa bedain
        fetch(nextUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('HTTP ' + res.status);
            }
            return res.json();
        })
        .then(data => {
            if (data.html) {
                // Append card baru ke dalam list
                const temp = document.createElement('div');
                temp.innerHTML = data.html;

                // Pindahkan anak-anaknya ke list
                Array.from(temp.children).forEach(child => {
                    listEl.appendChild(child);
                });
            }

            // Update next_page_url
            if (data.next_page_url) {
                loadMoreBtn.setAttribute('data-next-page', data.next_page_url);
                loadMoreBtn.disabled = false;
                if (spinner) spinner.classList.add('hidden');
                if (textSpan) textSpan.textContent = '{{ __('messages.customer.orders.histories.load_more') ?? 'Load more' }}';
            } else {
                // Tidak ada halaman berikut → sembunyikan tombol
                loadMoreBtn.classList.add('hidden');
            }
        })
        .catch(err => {
            console.error(err);
            loadMoreBtn.disabled = false;
            if (spinner) spinner.classList.add('hidden');
            if (textSpan) textSpan.textContent = '{{ __('messages.customer.orders.histories.load_more') ?? 'Load more' }}';

            // Optional: kasih alert sederhana
            @if(app()->environment('local'))
                alert('Gagal memuat data riwayat pesanan. Coba lagi.');
            @endif
        });
    });
});
</script>
@endpush
