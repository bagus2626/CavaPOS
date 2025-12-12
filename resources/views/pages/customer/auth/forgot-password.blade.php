@extends('layouts.customer')

@section('title',__('messages.customer.forgot_password.forgot_password'))

@section('content')
<div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6 md:p-8">
    <h2 class="text-2xl font-bold text-center mb-6">{{ __('messages.customer.forgot_password.forgot_password') }}</h2>

    @if (session('status'))
        <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
    @endif

    <p class="text-sm text-gray-600 mb-4">
        {{ __('messages.customer.forgot_password.forgot_password_instruction') }}
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
            {{ __('messages.customer.forgot_password.send_reset_link') }}
        </button>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('customer.login', compact('partner_slug','table_code')) }}" class="text-sm text-blue-600 hover:text-blue-800">
            {{ __('messages.customer.forgot_password.back_to_login') }}
        </a>
    </div>
</div>
@endsection
