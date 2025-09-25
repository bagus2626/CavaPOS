<x-app-layout>
    <x-guest-layout>
        <div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-lg p-6 md:p-8">
            <h2 class="text-xl font-semibold mb-3">Verifikasi Email</h2>

            @if (session('status') === 'verification-link-sent')
                <div class="mb-4 text-sm text-green-600">
                    Tautan verifikasi baru telah dikirim ke email Anda.
                </div>
            @elseif (session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <p class="text-sm text-gray-600 dark:text-gray-300">
                Kami telah mengirim tautan verifikasi ke alamat email Anda. 
                Jika belum menerima, Anda bisa meminta untuk mengirim ulang.
            </p>

            <form method="POST" action="{{ route('owner.verification.send') }}" class="mt-4">
                @csrf
                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
                    Kirim Ulang Tautan Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('owner.logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full text-center text-sm text-gray-500 hover:text-gray-700">
                    Keluar
                </button>
            </form>
        </div>
    </x-guest-layout>
</x-app-layout>
