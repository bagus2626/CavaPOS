<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Customer Page')</title>


    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">


    @vite('resources/css/app.css')
    @stack('head')
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen font-sans text-gray-800 overflow-x-hidden flex flex-col">
    {{-- Header --}}
    <header class="bg-white shadow-md w-full relative">
    </header>
   
    {{-- Navbar --}}
    @include('partials.employee-cashier-navbar')


    {{-- Main Content --}}
    <main class="flex-1 flex flex-col pt-16">
        @yield('content')
    </main>


    {{-- Footer --}}
    <footer class="bg-white w-full mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-3 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name', 'FoodBee') }}. All rights reserved.
        </div>
    </footer>


    @auth('employee')
        <script>
            window.CASHIER_PARTNER_ID = @json(auth('employee')->user()->partner_id);
            window.CASHIER_DASHBOARD_URL = @json(route('employee.cashier.dashboard'));
        </script>
    @endauth


    <!-- Load SweetAlert2 FIRST -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
    <!-- Then load other scripts -->
    @vite('resources/js/app.js')
    @vite('resources/js/echo.js')
    <script src="{{ asset('js/employee/cashier/cashier.js') }}"></script>


    <!-- SweetAlert Notifications -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: @json(session('error')),
                    confirmButtonColor: '#d33',
                    timer: 5000,
                    timerProgressBar: true
                });
            @endif


            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: @json($errors->first()),
                    confirmButtonColor: '#d33',
                    timer: 5000,
                    timerProgressBar: true
                });
            @endif


            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success')),
                    confirmButtonColor: '#3085d6',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
        });
    </script>


    @stack('scripts')
</body>
</html>

