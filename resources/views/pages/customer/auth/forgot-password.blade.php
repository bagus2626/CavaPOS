@extends('layouts.customer')

@section('title','Lupa Password')

@section('content')
<div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6 md:p-8">
    <h2 class="text-2xl font-bold text-center mb-6">Lupa Password</h2>

    @if (session('status'))
        <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
    @endif

    <p class="text-sm text-gray-600 mb-4">
        Masukkan email yang terdaftar. Kami akan mengirimkan tautan untuk reset password.
    </p>

    <form method="POST" action="{{ route('customer.password.email', compact('partner_slug','table_code')) }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
            Kirim Link Reset
        </button>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('customer.login', compact('partner_slug','table_code')) }}" class="text-sm text-blue-600 hover:text-blue-800">
            Kembali ke Login
        </a>
    </div>
</div>
@endsection
