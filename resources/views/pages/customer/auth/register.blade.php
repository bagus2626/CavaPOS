@extends('layouts.customer')

@section('title', 'Register Customer')

@section('content')
<div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6 md:p-8">
    <h2 class="text-2xl font-bold text-center mb-6">Daftar Customer</h2>

    {{-- Form registrasi --}}
    <form method="POST" action="{{ route('customer.register.submit', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @error('name')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @error('email')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">No HP</label>
            <input id="phone" type="number" name="phone" value="{{ old('phone') }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @error('phone')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" type="password" name="password" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @error('password')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md transition">
                Daftar
            </button>
        </div>
    </form>

    {{-- Divider --}}
    <div class="flex items-center my-6">
        <hr class="flex-grow border-gray-300">
        <span class="mx-2 text-gray-400 text-sm">atau</span>
        <hr class="flex-grow border-gray-300">
    </div>

    {{-- Login Social --}}
    <div class="space-y-3">
        <a href="#"
           class="w-full inline-flex justify-center items-center border border-gray-300 rounded-md py-2 px-4 mb-2 hover:bg-gray-100 transition">
            <img src="{{ asset('images/google-logo.png') }}" class="w-6 h-6 mr-2" alt="Google">
            Daftar dengan Google
        </a>

        <a href="{{ route('customer.login', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}"
           class="w-full bg-gray-700 hover:bg-gray-800 text-white font-semibold py-2 px-4 rounded-md transition text-center block">
            Sudah punya akun? Login
        </a>
    </div>
</div>
@endsection
