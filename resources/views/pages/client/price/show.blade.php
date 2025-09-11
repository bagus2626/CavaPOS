<x-app-layout>

<div class="mx-auto px-4 py-10 max-w-7xl">
    <h1 class="text-3xl font-bold mb-6">{{ $product->name }}</h1>

    <p class="mb-4 text-gray-700 dark:text-gray-300">{{ $product->description }}</p>

    <h2 class="text-3xl font-bold">Kami Menawarkan Harga Yang Terbaik Untuk Anda</h2>
    <div class="flex items-center space-x-2 justify-end mb-8 mr-4">
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($product->packages as $index => $package)
            <x-price-card :package="$package" :index="$index" />
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
</x-app-layout>
