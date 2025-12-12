<x-app-layout>
    <x-guest-layout>
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-6 md:p-8 border-t-4 border-choco">

            {{-- Judul --}}
            <h2 class="text-2xl font-bold mb-4 text-choco">
                {{ __('messages.owner.auth.reset_password.title') }}
            </h2>

            {{-- Status Success --}}
            @if (session('status'))
                <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 p-2 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Deskripsi singkat --}}
            <p class="text-sm text-gray-900 leading-relaxed mb-4">
                {{ __('messages.owner.auth.reset_password.description') }}
            </p>

            {{-- Form Reset Password --}}
            <form method="POST" action="{{ route('owner.password.update') }}" class="mt-3 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                {{-- Password Baru --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('messages.owner.auth.reset_password.password_label') }}
                    </label>
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           class="mt-1 block w-full rounded-xl border border-gray-300 shadow-sm px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-choco focus:border-choco text-black">
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('messages.owner.auth.reset_password.password_confirmation_label') }}
                    </label>
                    <input id="password_confirmation"
                           type="password"
                           name="password_confirmation"
                           required
                           class="mt-1 block w-full rounded-xl border border-gray-300 shadow-sm px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-choco focus:border-choco text-black">
                </div>

                {{-- Tombol Simpan --}}
                <button type="submit"
                        class="w-full btn bg-choco btn-pill py-2 font-semibold shadow rounded-md text-white mt-2">
                    {{ __('messages.owner.auth.reset_password.submit_button') }}
                </button>
            </form>

            {{-- Link kembali ke login --}}
            <div class="mt-4">
                <a href="{{ route('owner.login') }}"
                   class="w-full block text-center text-sm text-gray-500 hover:text-choco transition-colors">
                    {{ __('messages.owner.auth.reset_password.back_to_login') }}
                </a>
            </div>
        </div>
    </x-guest-layout>
</x-app-layout>

<style>
.btn-pill {
    @apply rounded-full;
}
</style>
