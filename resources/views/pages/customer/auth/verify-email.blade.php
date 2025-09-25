@extends('layouts.customer')

@section('title','Verifikasi Email')

@section('content')
<div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6 md:p-8">
    <h2 class="text-2xl font-bold text-center mb-4">Verifikasi Email</h2>

    @if (session('status') === 'verification-link-sent')
        <div class="mb-4 text-sm text-green-600">
            Link verifikasi baru telah dikirim ke email Anda.
        </div>
    @elseif (session('status'))
        <div class="mb-4 text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <p class="text-sm text-gray-600 mb-6">
        Kami telah mengirimkan link verifikasi ke email Anda. Jika belum menerima, klik tombol di bawah untuk mengirim ulang.
    </p>

    <form method="POST" action="{{ route('customer.verification.send') }}" class="space-y-3">
        @csrf
        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
            Kirim Ulang Link Verifikasi
        </button>
    </form>

    <form method="POST" action="{{ route('customer.logout', ['partner_slug'=>request('partner_slug'),'table_code'=>request('table_code')]) }}" class="mt-3">
        @csrf
        <button type="submit" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md">
            Logout
        </button>
    </form>
</div>
@endsection
