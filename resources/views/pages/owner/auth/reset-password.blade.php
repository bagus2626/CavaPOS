<x-app-layout>
    <x-guest-layout>
        <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-lg p-6 md:p-8">
            <h2 class="text-2xl font-bold text-center mb-6">Reset Password (Owner)</h2>

            @if (session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('owner.password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-100">Password Baru</label>
                    <input id="password" type="password" name="password" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('password')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-100">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
                    Simpan Password
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-300">
                Sudah ingat? <a class="text-blue-600 hover:text-blue-800" href="{{ route('owner.login') }}">Kembali ke login</a>
            </p>
        </div>
    </x-guest-layout>
</x-app-layout>
