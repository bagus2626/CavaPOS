@php
    $categories = ['All', 'Aplikasi Website', 'Aplikasi Mobile', 'Lainnya'];

    $portfolios = [
        [
            'title' => 'Website Tami Jaya',
            'desc' => 'Web app untuk pemesanan tiket bus realtime',
            'image' => 'images/web-01.png',
            'link' => 'https://tamijaya.com',
            'category' => 'Aplikasi Website',
        ],
        [
            'title' => 'Aplikasi mobile tamijaya',
            'desc' => 'Aplikasi untuk booking tiket bus secara mobile',
            'image' => 'images/mobile-01.jpeg',
            'link' => '#',
            'category' => 'Aplikasi Mobile',
        ],
        [
            'title' => 'Smesticon RAB',
            'desc' => 'Aplikasi untuk menghitung RAB proyek konstruksi',
            'image' => 'images/web-02.png',
            'link' => 'https://rabdev.livevcp.online/admin/login',
            'category' => 'Aplikasi Website',
        ],
        [
            'title' => 'Aplikasi mobile agen tamijaya',
            'desc' => 'Aplikasi untuk booking tiket bus secara mobile oleh agen',
            'image' => 'images/mobile-02.jpeg',
            'link' => '#',
            'category' => 'Aplikasi Mobile',
        ],
        [
            'title' => 'E-Laundry',
            'desc' => 'Aplikasi laundry online untuk memudahkan pemesanan',
            'image' => 'images/web-05.png',
            'link' => '#',
            'category' => 'Lainnya',
        ],
        [
            'title' => 'Landing Page',
            'desc' => 'Landing page untuk promosi produk',
            'image' => 'images/web-03.png',
            'link' => '#',
            'category' => 'Aplikasi Website',
        ],
    ];
@endphp
<div class="space"
    data-aos="fade-up"
    data-aos-delay="100"
    data-aos-duration="1000"
    data-aos-once="true"
    data-aos-anchor-placement="center-bottom"
>
    <div class="sun text-3xl md:text-4xl font-bold text-gray-800 mb-4 text-gray-600 dark:text-gray-200 text-center mt-20">
        Vastech solusi terbaik dalam mengembangkan bisnis anda
    </div>
    <section x-data="{
            activeTab: 'All',
            nextTab: null,
            isHiding: false,
            isShowing: true,

            changeTab(tab) {
                if (tab === this.activeTab || this.isHiding) return;

                // Simpan posisi scroll vertikal saat ini
                const scrollY = window.scrollY || window.pageYOffset;

                this.isShowing = false;      // Mulai animasi keluar portfolio lama
                this.isHiding = true;
                this.nextTab = tab;

                setTimeout(() => {
                    this.activeTab = this.nextTab;
                    this.isHiding = false;
                    this.isShowing = true;

                    setTimeout(() => {
                        window.scrollTo(0, scrollY);
                    }, 50);
                }, 400);
            }

        }"
        class="py-16 px-4 bg-white dark:bg-black md:h-screen"
    >
        <!-- Tabs -->
        <div class="flex flex-wrap justify-center gap-4 mb-10 bg-gray-100 dark:bg-gray-800 p-2 rounded-full w-fit mx-auto">
            @foreach ($categories as $category)
                <button
                    class="px-4 py-2 rounded-full font-medium text-sm transition-all"
                    :class="activeTab === '{{ $category }}' && !isHiding ? 'bg-indigo-600 dark:bg-black text-white' : 'text-gray-700 dark:text-gray-300'"
                    @click="changeTab('{{ $category }}')"
                >
                    {{ $category }}
                </button>
            @endforeach
        </div>

        <!-- Portfolio Grid -->
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 max-w-7xl mx-auto"
>
            @foreach ($portfolios as $item)
                <div
                    class="relative overflow-hidden rounded-xl group transition-all duration-300 bg-transparent dark:bg-gray-800 cursor-pointer"
                    x-show="(isShowing && (activeTab === 'All' || activeTab === '{{ $item['category'] }}'))"
                    x-transition:enter="transition transform ease-out duration-1000"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition transform ease-in duration-400"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    >
                    @if ($item['category'] === 'Aplikasi Mobile')
                        <div class="flex w-full h-64 overflow-hidden rounded-xl group transition-all duration-300">
                            <div class="flex-shrink-0 h-full overflow-hidden">
                                <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover object-center dark:grayscale dark:opacity-80" />
                            </div>
                            <div class="flex-grow h-full overflow-hidden">
                                <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover object-top dark:grayscale dark:opacity-80" />
                            </div>
                        </div>
                    @else
                        <img
                            src="{{ asset($item['image']) }}"
                            alt="{{ $item['title'] }}"
                            class="w-full h-64 object-cover object-top group-hover:scale-105 transition-all duration-300 dark:grayscale dark:opacity-80"
                        >
                    @endif

                    <!-- Overlay -->
                    <div
                        class="absolute inset-0 bg-black/60 backdrop-blur-sm text-white p-6
                            opacity-0 pointer-events-none
                            transition-opacity duration-700 ease-in-out
                            group-hover:opacity-100 group-hover:pointer-events-auto
                            flex flex-col justify-end space-y-2"
                    >
                        <h4
                            class="text-lg font-semibold opacity-0 translate-y-2
                                transition-all duration-500 delay-200 ease-out
                                group-hover:opacity-100 group-hover:translate-y-0"
                        >
                            {{ $item['title'] }}
                        </h4>

                        <p
                            class="text-sm opacity-0 translate-y-2
                                transition-all duration-500 delay-300 ease-out
                                group-hover:opacity-100 group-hover:translate-y-0"
                        >
                            {{ $item['desc'] }}
                        </p>

                        <a href="{{ $item['link'] }}" target="_blank"
                        class="inline-block bg-white text-indigo-600 font-medium text-sm px-3 py-1 rounded
                                opacity-0 translate-y-2
                                transition-all duration-500 delay-400 ease-out
                                group-hover:opacity-100 group-hover:translate-y-0
                                hover:bg-indigo-100 dark:text-gray-800 dark:hover:bg-gray-300"
                        >
                            Lihat Project
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        AOS.init({
            once: true,
            duration: 1200,
            easing: 'ease-in-out',
        });
    });
</script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.directive('auto-animate', (el, { expression }, { evaluate }) => {
            let autoAnimate = window.autoAnimate;
            if (typeof autoAnimate === 'function') {
                autoAnimate(el);
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@formkit/auto-animate@1.0.0-beta.3/dist/auto-animate.iife.js"></script>
