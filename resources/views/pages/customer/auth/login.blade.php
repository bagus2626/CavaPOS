@extends('layouts.customer')

@section('title', 'Login Customer')

@section('content')
<div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6 md:p-8">
    <h2 class="text-2xl font-bold text-center mb-6">Login Customer</h2>

    {{-- Form login email/password --}}
    <form method="POST" action="{{ route('customer.login.submit', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @error('email')
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

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember" type="checkbox" name="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-900">Ingat saya</label>
            </div>
            <div class="text-sm">
                {{-- <a href="{{ route('customer.password.request') }}" class="text-blue-600 hover:text-blue-800">Lupa password?</a> --}}
            </div>
        </div>

        <div>
            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md transition">
                Login
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
        {{-- <a href="{{ route('customer.social.login', ['provider' => 'google']) }}" --}}
        <a href="{{ route('customer.social.login', ['provider' => 'google', 'partner_slug' => $partner_slug, 'table_code' => $table_code]) }}"
           class="w-full inline-flex justify-center items-center border border-gray-300 rounded-md py-2 px-4 mb-2 hover:bg-gray-100 transition">
            <img src="{{ asset('images/google-logo.png') }}" class="w-6 h-6 mr-2" alt="Google">
            Login dengan Google
        </a>

        <form method="POST" action="{{ route('customer.guest', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}" class="w-full">
            @csrf
            <button class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 w-full" type="submit">
                Lanjut sebagai Guest
            </button>
        </form>
    </div>

    {{-- Footer kecil --}}
    <p class="mt-6 text-center text-gray-500 text-sm">
        Belum punya akun? <a href="{{ route('customer.register', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}" class="text-blue-600 hover:text-blue-800">Daftar sekarang</a>
    </p>
</div>
@endsection
