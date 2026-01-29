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
<body class="bg-gray-100 font-sans text-gray-800 flex flex-col min-h-screen">

    {{-- Header --}}
    {{-- <header class="bg-white shadow-md w-full relative">

    </header> --}}
    {{-- Navbar --}}
    {{-- @include('partials.customer-navbar') --}}
    @include('partials.customer-navbar', [
        'partner_slug' => $partner_slug,
        'table_code' => $table_code
    ])

    {{-- Main Content --}}
    <main class="flex-1 flex flex-col @yield('main-justify', 'justify-center') items-center @yield('main-class', 'pt-[64px]')">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="w-full bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p class="text-sm text-gray-600 font-medium">
                    &copy; {{ date('Y') }} 
                    <span class="font-bold text-[#ae1504]">
                        {{ config('app.name', 'FoodBee') }}.
                    </span>
                    <span class="text-sm text-gray-600 font-medium">All rights reserved.</span>
                </p>
            </div>
        </div>
    </footer>


    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: @json(session('success')),
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: @json(session('error')),
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: @json($errors->first()),
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                });
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
