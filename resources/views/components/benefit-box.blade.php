@props([
    'icon',
    'title',
    'tailwindClass',
    'description'
])

<div class="w-full md:w-1/3 flex flex-col items-center text-center gap-4 p-6 {{ $tailwindClass }} }}">
    <div class="bg-indigo-900 dark:bg-black p-4 rounded-full shadow-md">
        <img src="{{ asset($icon) }}" alt="{{ $title }}" class="w-10 h-10" />
    </div>
    <div>
        <h4 class="text-lg font-bold text-gray-800 dark:text-white">{{ $title }}</h4>
        <p class="text-gray-600 dark:text-gray-300">{{ $description }}</p>
    </div>
</div>
