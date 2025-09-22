<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Cavaa') }}</title>

        <link rel="icon" href="icons/favicon-32x32.png" sizes="32x32" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
        <!-- Fancybox CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox/fancybox.css"/>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-black text-gray-900 dark:text-white">
        @if (request()->is('partner*'))
            @include('layouts.guest-partner-navigation')
        @else
            @include('layouts.navigation')
        @endif
        <div class="min-h-screen mt-20 bg-gray-100 dark:bg-black">
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- sweet alert --}}
            @if (session('success'))
                <script>
                    Swal.fire({
                        title: "Berhasil!",
                        text: "{{ session('success') }}",
                        icon: "success",
                        confirmButtonText: "OK"
                    });
                </script>
            @endif

            @if (session('error'))
                <script>
                    Swal.fire({
                        title: "Gagal!",
                        text: "{{ session('error') }}",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                </script>
            @endif

            @if ($errors->any())
                <script>
                    Swal.fire({
                        title: "Validasi Gagal!",
                        html: `{!! implode('<br>', $errors->all()) !!}`,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                </script>
            @endif
            {{-- sweet alert --}}


            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @include('layouts.footer')

        <!-- Fancybox JS -->
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox/fancybox.umd.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
        <script src="https://unpkg.com/@formkit/auto-animate@1" defer></script>
        @stack('scripts')
    </body>
</html>
