<x-app-layout>
    <x-guest-layout>
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-6 md:p-8 border-t-4 border-choco">

            {{-- Judul --}}
            <h2 class="text-2xl font-bold mb-4 text-choco text-center">
                {{ __('messages.owner.auth.forgot_password.title') }}
            </h2>

            {{-- Status --}}
            @if (session('status'))
                <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 p-2 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Deskripsi --}}
            <p class="text-sm text-gray-900 leading-relaxed mb-4 text-center">
                {{ __('messages.owner.auth.forgot_password.description') }}
            </p>

            {{-- Form --}}
            <form method="POST" action="{{ route('owner.password.email') }}" class="space-y-4 mt-4">
                @csrf

                {{-- Input Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('messages.owner.auth.forgot_password.email_label') }}
                    </label>

                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required autofocus
                           class="mt-1 block w-full rounded-xl border border-gray-300 shadow-sm px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-choco focus:border-choco text-black">

                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tombol Submit --}}
                <button type="submit"
                        class="w-full btn bg-choco btn-pill py-2 font-semibold text-white shadow rounded-md">
                    {{ __('messages.owner.auth.forgot_password.submit_button') }}
                </button>
            </form>

            {{-- Link ke Login --}}
            <div class="mt-6 text-center">
                <a href="{{ route('owner.login') }}"
                   class="text-sm text-gray-500 hover:text-choco transition-colors">
                    {{ __('messages.owner.auth.forgot_password.back_to_login') }}
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
