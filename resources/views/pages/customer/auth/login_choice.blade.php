@extends('layouts.customer')

@section('content')
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100 p-4">
        {{-- Logo Outlet --}}
        @php
            $partnerId = session('customer.partner_id');
            if (!$partnerId && isset($partner_slug)) {
                $currentOutlet = \App\Models\User::where('slug', $partner_slug)->where('role', 'partner')->first();
                $partnerId = $currentOutlet?->id;
            }
            if ($partnerId && !isset($currentOutlet)) {
                $currentOutlet = \App\Models\User::find($partnerId);
            }
        @endphp

        @if (isset($currentOutlet) && $currentOutlet && $currentOutlet->logo)
            <div class=" flex flex-col items-center">
                <img src="{{ asset('storage/' . $currentOutlet->logo) }}" alt="{{ $currentOutlet->name }}"
                    class="h-40 w-40 md:h-40 md:w-40 object-contain rounded-lg ">
                    <span class="text-sm font-semibold text-gray-500 ">{{ __('messages.customer.login_choice.welcome_to') }}</span>
                <p class=" text-3xl md:text-3xl font-bold text-gray-800">{{ $currentOutlet->name }}</p>
            </div>
        @endif

        <div class="text-center w-full max-w-sm px-4 py-6 md:px-8 md:py-12">
            <h2 class="text-xl md:text-2xl font-bold">{{ __('messages.customer.login_choice.choose_how_to_log_in') }}</h2>
            <div class="mt-4 flex flex-col gap-3">
                <a class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 block w-full text-center"
                    href="{{ route('customer.login', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}">
                    {{ __('messages.customer.login_choice.login_with_email_or_register') }}
                </a>

                <a class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 block w-full text-center flex items-center justify-center"
                    href="{{ route('customer.google.redirect', ['provider' => 'google', 'partner_slug' => $partner_slug, 'table_code' => $table_code]) }}">
                    <div class="bg-white rounded-full flex items-center justify-center mr-2">
                        <img src="{{ asset('images/google-logo.png') }}" class="w-5 h-5" alt="Google">
                    </div>
                    <span>{{ __('messages.customer.login_choice.login_with_google') }}</span>
                </a>


                <form method="POST"
                    action="{{ route('customer.guest', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}"
                    class="w-full">
                    @csrf
                    <button class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 w-full" type="submit">
                        {{ __('messages.customer.login_choice.continue_with_guest') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Opsional: Footer kecil --}}
        <p class="mt-4 text-sm text-gray-500 text-center">
            {{ __('messages.customer.login_choice.login_instruction') }}
        </p>
    </div>
@endsection
