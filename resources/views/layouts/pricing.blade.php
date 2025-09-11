@php
    $packages = [
        [
            'name' => 'Lite',
            'price_monthly' => 'Rp 199.000',
            'price_yearly' => 'Rp 1.999.000',
            'features' => [
                'Basic UI Design',
                '1 Platform (Android atau iOS)',
                'Email Support',
            ],
        ],
        [
            'name' => 'Premium',
            'price_monthly' => 'Rp 499.000',
            'price_yearly' => 'Rp 4.999.000',
            'features' => [
                'UI/UX Design',
                '2 Platform (Android & iOS)',
                'Push Notification',
                'Priority Support',
            ],
        ],
        [
            'name' => 'Business',
            'price_monthly' => 'Rp 999.000',
            'price_yearly' => 'Rp 9.999.000',
            'features' => [
                'Custom Design',
                '2 Platform',
                'Real-time Database',
                'API Integration',
                'Dedicated Support',
            ],
        ],
        [
            'name' => 'Custom',
            'price_monthly' => 'Hubungi Kami',
            'price_yearly' => 'Hubungi Kami',
            'features' => [
                'Sesuai Kebutuhan Anda',
                'Konsultasi Gratis',
                'Skalabilitas Tinggi',
            ],
        ],
    ];
@endphp


    <div class="max-w-6xl mx-auto py-12 px-4">
        <h2 class="text-3xl font-bold">Kami Menawarkan Harga Yang Terbaik Untuk Anda</h2>
        <div class="flex justify-end items-center mb-6">
            {{-- Toggle Bulanan/Tahunan --}}
            <div class="flex items-center space-x-2">
                <span>Bulanan</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="billingToggle" class="sr-only peer">
                    <!-- background switch -->
                    <div class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-blue-600 transition-colors duration-300"></div>
                    <!-- bola switch -->
                    <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md transform transition-transform duration-300 peer-checked:translate-x-5"></div>
                </label>

                <span>Tahunan</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($packages as $index => $package)
                <div class="border dark:border-gray-700 rounded-xl p-6 shadow-md flex flex-col items-start space-y-4"
                        data-aos="fade-up"
                        data-aos-delay={{ $index * 100 }}
                        data-aos-duration="1000"
                        data-aos-once="true"
                        data-aos-anchor-placement="center-bottom">
                    <h3 class="text-xl font-semibold">{{ $package['name'] }}</h3>
                    <p class="text-3xl font-bold">
                        <span class="monthly-price">{{ $package['price_monthly'] }}</span>
                        <span class="yearly-price hidden">{{ $package['price_yearly'] }}</span>
                        <span class="text-base font-normal text-gray-500">/bulan</span>
                    </p>

                    <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-400">
                        @foreach ($package['features'] as $feature)
                            <li>✅ {{ $feature }}</li>
                        @endforeach
                    </ul>

                    <a href="#" class="mt-auto inline-block bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 transition">
                        Coba Sekarang
                    </a>
                </div>

            @endforeach
        </div>
    </div>

    <script>
        const toggle = document.getElementById('billingToggle');
        toggle.addEventListener('change', () => {
            const monthlyPrices = document.querySelectorAll('.monthly-price');
            const yearlyPrices = document.querySelectorAll('.yearly-price');

            monthlyPrices.forEach(el => el.classList.toggle('hidden'));
            yearlyPrices.forEach(el => el.classList.toggle('hidden'));
        });
    </script>

