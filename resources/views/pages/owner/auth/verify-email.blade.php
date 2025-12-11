<x-app-layout>
    <x-guest-layout>
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-6 md:p-8 border-t-4 border-choco">

            {{-- Judul --}}
            <h2 class="text-2xl font-bold mb-4 text-choco">
                {{ __('messages.owner.auth.verify_email.email_verification') }}
            </h2>

            {{-- Status Success --}}
            @if (session('status') === 'verification-link-sent')
                <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 p-2 rounded-lg">
                    {{ __('messages.owner.auth.verify_email.verification_link_information_1') }}
                </div>
            @elseif (session('status'))
                <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 p-2 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Deskripsi --}}
            <p class="text-sm text-gray-900 leading-relaxed">
                {{ __('messages.owner.auth.verify_email.verification_link_information_2') }}
            </p>

            {{-- Tombol Kirim Ulang --}}
            <form method="POST" action="{{ route('owner.verification.send') }}" class="mt-5">
                @csrf
                <button type="submit"
                    class="w-full btn bg-choco btn-pill py-2 font-semibold shadow rounded-md">
                    {{ __('messages.owner.auth.verify_email.resend_verification_link') }}
                </button>
            </form>

            {{-- Tombol Logout --}}
            <form method="POST" action="{{ route('owner.logout') }}" class="mt-4">
                @csrf
                <button type="submit"
                    class="w-full text-center text-sm text-gray-500 hover:text-choco transition-colors">
                    {{ __('messages.owner.auth.verify_email.exit') }}
                </button>
            </form>
        </div>
    </x-guest-layout>
</x-app-layout>

<style>

.btn-pill {
    @apply rounded-full;
}

</style>