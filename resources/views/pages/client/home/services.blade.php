<!-- ===== Services Start ===== -->
<section class="py-16 px-4">
    <!-- Section Title Start -->
    <div
        x-data="{ sectionTitle: `Kami akan mengedepankan pelayanan terbaik untuk anda`, sectionTitleText: `` }"
        class="text-center mb-12"
    >
        <h2 x-text="sectionTitle" class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 text-gray-600 dark:text-gray-200"></h2>
        <p x-text="sectionTitleText" class="text-gray-600 max-w-xl mx-auto"></p>
    </div>
    <!-- Section Title End -->

    <!-- Services Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
        @php
            $services = [
                [
                    'icon' => 'icons/icon-time-management.png',
                    'title' => 'Timeline Management',
                    'desc' => 'Kami selalu berkerja menggunakan timeline yang telah disepakati'
                ],
                [
                    'icon' => 'icons/icon-layout.png',
                    'title' => 'Tampilan yang rapi',
                    'desc' => 'Kami akan selalu berusaha agar tampilan yang digunakan mudah di pahami'
                ],
                [
                    'icon' => 'icons/icon-discussion.png',
                    'title' => 'Diskusi',
                    'desc' => 'Kami akan selalu mendiskusikan project yang kami buat secara berkala'
                ],
                [
                    'icon' => 'icons/icon-time.png',
                    'title' => 'Ketepatan waktu',
                    'desc' => 'Kami akan selalu memastikan project dalam selesai dengan waktu yang telah di sepakati'
                ],
                [
                    'icon' => 'icons/icon-customization.png',
                    'title' => 'Kustomisasi',
                    'desc' => 'Layanan kami juga dapat melakukan kustomisasi sesuai dengan kebutuhan anda'
                ],
                [
                    'icon' => 'icons/icon-renewable.png',
                    'title' => 'Pembaharuan',
                    'desc' => 'Layanan kami akan melakukan update secara berkala guna melakukan optimasi'
                ]
            ];
        @endphp

        @foreach ($services as $index => $service)
            <div
            data-aos="fade-up"
            data-aos-delay="{{ $index * 200 }}"
            class="p-6 rounded-xl shadow hover:shadow-md transition-all text-center transform hover:scale-105 duration-300">
                <div class="w-fit bg-indigo-900 dark:bg-transparent rounded-full p-3 mx-auto mb-4">
                    <img src="{{ asset($service['icon']) }}" alt="{{ $service['title'] }}" class="w-10 h-10">
                </div>
                <h4 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">{{ $service['title'] }}</h4>
                <p class="text-gray-600 dark:text-gray-500 text-sm">{{ $service['desc'] }}</p>
            </div>
        @endforeach
    </div>
</section>
<script>
    AOS.init({
        duration: 1000, // durasi animasi
        once: true,    // animasi hanya sekali
    });
</script>
