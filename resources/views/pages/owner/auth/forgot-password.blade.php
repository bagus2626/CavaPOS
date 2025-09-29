<x-app-layout>
    <x-guest-layout>
        <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-lg p-6 md:p-8">
            <h2 class="text-2xl font-bold text-center mb-6">Lupa Password (Owner)</h2>

            @if (session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('owner.password.email') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-100">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('email')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
                    Kirim Link Reset
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-300">
                Ingat password? <a class="text-blue-600 hover:text-blue-800" href="{{ route('owner.login') }}">Kembali ke login</a>
            </p>
        </div>
    </x-guest-layout>
</x-app-layout>
