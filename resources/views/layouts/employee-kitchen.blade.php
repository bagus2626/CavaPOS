<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Customer Page')</title>

    <!-- Optional: Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    @stack('head')
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-100 min-h-screen font-sans text-gray-800">

    {{-- Header --}}
    <header class="bg-white shadow-md w-full relative">

    </header>
    {{-- Navbar --}}
    @include('partials.employee-kitchen-navbar')

    {{-- Main Content --}}
    <main class="flex-1 flex flex-col justify-center items-center pt-16">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white w-full">
        <div class="max-w-screen-xl mx-auto px-4 py-3 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} {{ config('app.name', 'FoodBee') }}. All rights reserved.
        </div>
    </footer>

    @auth('employee')
        <script>
            window.KITCHEN_PARTNER_ID = @json(auth('employee')->user()->partner_id);
            window.KITCHEN_DASHBOARD_URL = @json(route('employee.cashier.dashboard'));
        </script>
    @endauth


    <!-- Scripts -->
    <script src="{{ asset('js/employee/kitchen/kitchen.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
