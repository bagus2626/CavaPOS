{{-- resources/views/employee/cashier/dashboard-new.blade.php --}}
@extends('layouts.employee-cashier')


@section('title', 'Cashier Dashboard - ' . $partner->name)


@section('content')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <div class="flex-1 flex flex-col h-full overflow-hidden relative">
        {{-- Sidebar Tabs --}}
        <div class="fixed left-0 top-0 h-screen w-28 bg-white border-r border-gray-200 shadow-sm flex flex-col pt-20 z-40">
            @php
                $tabs = [
                    'pembelian' => ['label' => 'Pembelian', 'icon' => 'shopping_cart'],
                    'pembayaran' => ['label' => 'Pembayaran', 'icon' => 'payments'],
                    'proses' => ['label' => 'Proses', 'icon' => 'sync'],
                    'selesai' => ['label' => 'Selesai', 'icon' => 'check_circle'],
                ];
            @endphp


            <div class="flex flex-col gap-3 px-2 py-2">
                @foreach ($tabs as $key => $data)
                    <button type="button" data-tab="{{ $key }}"
                        class="tab-btn group relative flex flex-col items-center justify-center px-2 py-4 rounded-2xl transition-all duration-200 {{ $loop->first ? 'active bg-red-50 text-[#ae1504]' : 'text-gray-500' }}">
                        <span class="material-icons-round text-[32px] mb-2">{{ $data['icon'] }}</span>
                        <span class="text-[10px] font-semibold text-center leading-snug break-words w-full">{{ $data['label'] }}</span>


                        @if (isset($tabCounts[$key]) && $tabCounts[$key] > 0)
                            <span id="tab-badge-{{ $key }}"
                                class="absolute top-2 right-2 inline-flex items-center justify-center min-w-[22px] h-[22px] rounded-full bg-[#ae1504] text-white text-[11px] font-bold px-1.5 shadow-md">
                                {{ $tabCounts[$key] > 9 ? '9+' : $tabCounts[$key] }}
                            </span>
                        @endif


                        <div class="indicator-bar absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-12 bg-[#ae1504] rounded-r-full transition-opacity duration-200 {{ $loop->first ? 'opacity-100' : 'opacity-0' }}">
                        </div>
                    </button>
                @endforeach
            </div>
        </div>


        {{-- Main Content dengan margin untuk sidebar --}}
        <div class="ml-28 flex-1 overflow-y-auto px-6 pb-6 pt-6" id="mainScrollContainer">
            {{-- Tab Content --}}
            <div id="tabContent" class="relative w-full max-w-full rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden mb-7">
                <div id="tabLoading" class="hidden p-10 text-center text-gray-500">Memuat…</div>
            </div>
        </div>
    </div>


    @if ($needPaymentOrder && request('auto_pay') == '1')
        <script>
            window.NEED_PAYMENT_ORDER_ID = @json($needPaymentOrder->id);
        </script>
    @endif


    @include('pages.employee.cashier.dashboard.modals.cash')
    @include('pages.employee.cashier.dashboard.modals.detail')
    @include('pages.employee.cashier.dashboard.modals.scanner')
@endsection


@push('styles')
    <style>
        /* Material Icons */
        .material-icons-round {
            font-family: 'Material Icons Round';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
            -moz-osx-font-smoothing: grayscale;
            font-feature-settings: 'liga';
        }


        /* Sidebar Tab Button States */
        .tab-btn {
            position: relative;
            min-height: 88px;
            max-width: 100%;
        }


        .tab-btn span:not(.material-icons-round):not([id^="tab-badge"]) {
            max-width: 100%;
            word-break: break-word;
            hyphens: auto;
        }


        /* Active state */
        .tab-btn.active {
            background-color: #fef2f2 !important;
            color: #ae1504 !important;
        }


        .tab-btn.active .indicator-bar {
            opacity: 1 !important;
        }


        /* Hover state - only for non-active buttons */
        .tab-btn:not(.active):hover {
            background-color: #f9fafb;
            color: #111827;
            transform: scale(1.02);
        }


        /* Default state */
        .tab-btn:not(.active) {
            color: #6b7280;
        }


        /* Badge pulse animation only for active tabs */
        .tab-btn.active span[id^="tab-badge"] {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }


        @keyframes pulse {


            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }


            50% {
                opacity: .85;
                transform: scale(1.08);
            }
        }


        /* Responsive - Hide sidebar on mobile */
        @media (max-width: 768px) {
            .fixed.left-0.w-28 {
                display: none;
            }


            .ml-28 {
                margin-left: 0;
            }
        }
    </style>
@endpush


@push('scripts')
    <!-- Pastikan library scanner tersedia -->
     <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="{{ asset('js/employee/cashier/dashboard/detail.js') }}"></script>
    
    <script>
        window.CASHIER_PARTNER_ID = "{{ $partner->id }}";
        window.CASHIER_METRICS_URL = "{{ route('employee.cashier.metrics') }}";


        // Update waktu real-time
        setInterval(() => {
            const timeEl = document.getElementById('current-time');
            if (timeEl) {
                const now = new Date();
                timeEl.textContent = now.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }
        }, 1000);
    </script>
    <script>
        (function() {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContent = document.getElementById('tabContent');
            const tabLoading = document.getElementById('tabLoading');


            const url = new URL(window.location.href);
            const shouldAutoPay = url.searchParams.get('auto_pay') === '1';
            const urlTabParam = url.searchParams.get('tab');
            const urlOpenOrder = url.searchParams.get('open_order');
            const needPayId = window.NEED_PAYMENT_ORDER_ID || null;


            function setActive(btn) {
                tabBtns.forEach(b => {
                    // Remove active state
                    b.classList.remove('active', 'bg-red-50', 'text-[#ae1504]');
                    b.classList.add('text-gray-500');


                    // Hide indicator
                    const indicator = b.querySelector('.indicator-bar');
                    if (indicator) {
                        indicator.classList.remove('opacity-100');
                        indicator.classList.add('opacity-0');
                    }
                });


                // Add active state to clicked button
                btn.classList.remove('text-gray-500');
                btn.classList.add('active', 'bg-red-50', 'text-[#ae1504]');


                // Show indicator
                const indicator = btn.querySelector('.indicator-bar');
                if (indicator) {
                    indicator.classList.remove('opacity-0');
                    indicator.classList.add('opacity-100');
                }
            }


            function setActiveByKey(key) {
                const btn = document.querySelector(`.tab-btn[data-tab="${key}"]`);
                if (btn) setActive(btn);
            }


            async function loadTab(tab, afterLoaded) {
                tabLoading.classList.remove('hidden');
                [...tabContent.children].forEach(el => {
                    if (el.id !== 'tabLoading') el.remove();
                });


                try {
                    const qs = new URLSearchParams(window.location.search);
                    qs.set('_', Date.now());


                    const url = "{{ route('employee.cashier.tab', '__TAB__') }}"
                        .replace('__TAB__', tab) + '?' + qs.toString();


                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const html = await res.text();
                    const frag = document.createRange().createContextualFragment(html);
                    tabContent.appendChild(frag);


                    if (typeof afterLoaded === 'function') afterLoaded();


                    if (needPayId && shouldAutoPay && tab === 'pembayaran') {
                        requestAnimationFrame(() => {
                            const btnBayar = tabContent.querySelector(
                                `[data-cash-btn][data-order-id="${needPayId}"]`);
                            if (btnBayar) btnBayar.click();
                        });
                    }


                } catch (e) {
                    tabContent.appendChild(Object.assign(document.createElement('div'), {
                        className: 'p-6 text-[#b91d1d]',
                        textContent: 'Gagal memuat data. Coba lagi.'
                    }));
                } finally {
                    tabLoading.classList.add('hidden');
                }
            }


            if (tabBtns.length) {
                const savedTabKey = localStorage.getItem("activeTab");
                const defaultKey = tabBtns[0].dataset.tab;
                const initialKey = (needPayId && shouldAutoPay) ? "pembayaran" : (urlTabParam || savedTabKey ||
                    defaultKey);
                const initialBtn = document.querySelector(`.tab-btn[data-tab="${initialKey}"]`) || tabBtns[0];


                setActive(initialBtn);
                loadTab(initialBtn.dataset.tab, () => {
                    if (initialBtn.dataset.tab === 'pembelian' && typeof window.initPembelianTab ===
                        'function') {
                        window.initPembelianTab();
                    }
                    if (urlOpenOrder) highlightOrderCard(urlOpenOrder);
                });
            }


            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    setActive(btn);
                    loadTab(btn.dataset.tab, () => {
                        if (btn.dataset.tab === 'pembelian' && typeof window
                            .initPembelianTab === 'function') {
                            window.initPembelianTab();
                            console.log('initPembelianTab');
                            // location.reload();
                        }
                    });
                    localStorage.setItem("activeTab", btn.dataset.tab);
                });
            });


            window.CASHIER = {
                setActiveTab: (key) => setActiveByKey(key),
                loadTab: (key, afterLoaded) => loadTab(key, afterLoaded)
            };
        })();


        function highlightOrderCard(orderId) {
            if (!orderId) return;
            const tabContentEl = document.getElementById('tabContent');
            if (!tabContentEl) return;


            const selector = `#order-item-${orderId}, [data-order-id="${orderId}"]`;
            const el = tabContentEl.querySelector(selector);


            if (!el) {
                console.warn('Order card tidak ditemukan untuk ID', orderId);
                return;
            }


            el.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            el.classList.add('ring-4', 'ring-amber-400', 'ring-offset-2', 'ring-offset-white', 'shadow-lg');


            setTimeout(() => {
                el.classList.remove('ring-4', 'ring-amber-400', 'ring-offset-2', 'ring-offset-white', 'shadow-lg');
            }, 2000);
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const scanBtn = document.getElementById('scanBarcodeBtn');
            const modal = document.getElementById('barcodeModal');
            const closeBtn = document.getElementById('closeScanner');


            if (scanBtn && modal && closeBtn) {
                let html5QrCode = null;


                function openScanner() {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');


                    // Pastikan elemen scanner ada
                    if (!document.getElementById('barcodeScanner')) {
                        const scannerDiv = document.createElement('div');
                        scannerDiv.id = 'barcodeScanner';
                        modal.querySelector('.modal-body').appendChild(scannerDiv);
                    }


                    try {
                        if (!html5QrCode) {
                            html5QrCode = new Html5Qrcode("barcodeScanner");
                        }


                        const config = {
                            fps: 10,
                            qrbox: {
                                width: 250,
                                height: 250
                            }
                        };


                        html5QrCode.start({
                                facingMode: "environment"
                            },
                            config,
                            (decodedText) => {
                                console.log('Barcode/QR ditemukan:', decodedText);
                                stopScanner();


                                // Gunakan nilai decodedText sesuai kebutuhan
                                const searchInput = document.getElementById('searchInput');
                                if (searchInput) {
                                    searchInput.value = decodedText;
                                    searchInput.closest('form')?.submit();
                                }
                            },
                            (errorMessage) => {
                                // Bisa diabaikan atau untuk debugging
                            }
                        );
                    } catch (err) {
                        console.error("Camera error:", err);
                        alert("Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.");
                        stopScanner();
                    }
                }


                function stopScanner() {
                    try {
                        if (html5QrCode && html5QrCode.isScanning) {
                            html5QrCode.stop();
                        }
                    } catch (e) {
                        console.warn("Error stopping scanner:", e);
                    } finally {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                }


                scanBtn.addEventListener('click', openScanner);
                closeBtn.addEventListener('click', stopScanner);


                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        stopScanner();
                    }
                });
            }
        });
    </script>
@endpush