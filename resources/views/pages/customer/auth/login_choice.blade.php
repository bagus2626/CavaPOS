@extends('layouts.customer')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center bg-gray-100 p-4">
    <div class="text-center w-full max-w-sm px-4 py-6 md:px-8 md:py-12">
        <h2 class="text-xl md:text-2xl font-bold">Pilih cara masuk</h2>
        <div class="mt-4 flex flex-col gap-3">
            <a class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 block w-full text-center"
               href="{{ route('customer.login', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}">
                Login dengan Email/Daftar
            </a>

            <a class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 block w-full text-center flex items-center justify-center"
            href="{{ route('customer.google.redirect', ['provider' => 'google', 'partner_slug' => $partner_slug, 'table_code' => $table_code]) }}">
                <div class="bg-white rounded-full flex items-center justify-center mr-2">
                    <img src="{{ asset('images/google-logo.png') }}" class="w-5 h-5" alt="Google">
                </div>
                <span>Login dengan Google</span>
            </a>


            <form method="POST" action="{{ route('customer.guest', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}" class="w-full">
                @csrf
                <button class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 w-full" type="submit">
                    Lanjut sebagai Guest
                </button>
            </form>
        </div>
    </div>

    {{-- Opsional: Footer kecil --}}
    <p class="mt-4 text-sm text-gray-500 text-center">
        Masuk untuk mendapatkan pengalaman terbaik di menu kami
    </p>
</div>
@endsection
