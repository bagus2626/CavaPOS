<div class="border dark:border-gray-700 rounded-xl p-6 shadow-md flex flex-col items-start space-y-4"
        data-aos="fade-up"
        data-aos-delay={{ $index * 100 }}
        data-aos-duration="1000"
        data-aos-once="true"
        data-aos-anchor-placement="center-bottom">
    <h3 class="text-xl font-semibold">{{ $package['name'] }}</h3>
    <p class="text-3xl font-bold">
        @if ($package['name'] == 'Custom')
            <span>Hubungi Kami</span>
        @else
            <span class="monthly-price">{{ $package['monthly_price'] }}</span>
            <span class="yearly-price hidden">{{ $package['annual_price'] }}</span>
            <span class="text-base font-normal text-gray-500">/bulan</span>
        @endif
    </p>

    <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-400">
        @foreach ($package->specificationValues as $specVal)
            <li>✅ {{ $specVal->specification->name }}: {{ $specVal->value }}</li>
        @endforeach
    </ul>


    <a href="#" class="mt-auto inline-block bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 transition">
        Coba Sekarang
    </a>
</div>
