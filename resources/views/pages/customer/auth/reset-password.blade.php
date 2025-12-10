@extends('layouts.customer')

@section('title',__('messages.customer.reset_password.reset_password'))

@section('content')
<div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6 md:p-8">
    <h2 class="text-2xl font-bold text-center mb-6">{{ __('messages.customer.reset_password.reset_password') }}</h2>

    <form method="POST" action="{{ route('customer.password.update') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="partner_slug" value="{{ $partner_slug }}">
        <input type="hidden" name="table_code" value="{{ $table_code }}">

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">{{ __('messages.customer.reset_password.new_password') }}</label>
            <input id="password" type="password" name="password" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @error('password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('messages.customer.reset_password.password_confirmation') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
            {{ __('messages.customer.reset_password.reset_password') }}
        </button>
    </form>
</div>
@endsection
