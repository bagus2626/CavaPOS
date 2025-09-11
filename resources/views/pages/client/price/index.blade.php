{{-- resources/views/welcome.blade.php --}}
<x-app-layout>
<div class="max-w-6xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold mb-6">Daftar Harga Produk</h1>

    <div class="grid md:grid-cols-3 gap-6">
        @foreach ($products as $product)
            <a href="{{ route('price.show', $product->slug) }}"
               class="p-6 border rounded-lg hover:shadow-lg bg-white dark:bg-gray-800 transition-all">
                <h2 class="text-xl font-semibold mb-2">{{ $product->name }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($product->description, 100) }}</p>
            </a>
        @endforeach
    </div>
</div>
</x-app-layout>
