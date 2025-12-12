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
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $currentLocale = session('app_locale', app()->getLocale());
    @endphp
    <body class="font-sans antialiased bg-gray-100 dark:bg-black text-gray-900 dark:text-white">
        {{-- === Custom Header Section === --}}
        <header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-primary/10">
            <nav class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
                {{-- Logo --}}
                <div class="font-poppins">
                    <img src="{{ asset('images/cava-logo3-gradient.png') }}" 
                        alt="CAVAA Logo" 
                        class="h-8 md:h-10 object-contain" />
                </div>

                {{-- Menu + Search + Language + Mobile Button --}}
                <div class="flex items-center gap-4">
                    <!-- Desktop & Tablet Menu -->
                    <ul class="hidden md:flex space-x-6 items-center">
                        <li><a href="#home" class="text-gray-700 hover:text-primary transition-colors font-medium">Home</a></li>
                        <li><a href="#fitur" class="text-gray-700 hover:text-primary transition-colors font-medium">Fitur</a></li>
                        <li><a href="#kontak" class="text-gray-700 hover:text-primary transition-colors font-medium">Kontak</a></li>
                    </ul>

                    <!-- Search Bar -->
                    <div class="relative hidden md:block">
                        <input type="text" placeholder="Search....." 
                            class="pl-10 pr-4 py-2 border border-primary/20 rounded-full bg-amber-50/50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary w-40 md:w-48 lg:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-primary"></i>
                    </div>

                    <!-- Language Switcher (Desktop) -->
                    <div class="hidden md:flex items-center space-x-1">
                        @php
                            $currentLocale = session('app_locale', app()->getLocale());
                        @endphp

                        <form action="{{ route('language.set') }}" method="POST">
                            @csrf
                            <input type="hidden" name="locale" value="id">
                            <button type="submit"
                                class="px-3 py-1 text-xs font-semibold rounded-full border
                                    {{ $currentLocale === 'id'
                                        ? 'bg-choco text-white border-choco'
                                        : 'bg-tansparent text-gray-700 border-choco/40 hover:bg-soft-choco/10' }}">
                                ID
                            </button>
                        </form>

                        <form action="{{ route('language.set') }}" method="POST">
                            @csrf
                            <input type="hidden" name="locale" value="en">
                            <button type="submit"
                                class="px-3 py-1 text-xs font-semibold rounded-full border
                                    {{ $currentLocale === 'en'
                                        ? 'bg-choco text-white border-choco'
                                        : 'bg-transparent text-gray-700 border-choco/40 hover:bg-soft-choco/10' }}">
                                EN
                            </button>
                        </form>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button class="md:hidden text-choco" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </nav>

            <!-- Mobile Menu -->
            <div id="mobileMenu" class="mobile-menu md:hidden bg-white border-t border-primary/10 px-4 py-4 hidden">
                <ul class="space-y-3">
                    <li><a href="#home" class="block text-gray-700 hover:text-primary transition-colors font-medium py-2" onclick="toggleMobileMenu()">Home</a></li>
                    <li><a href="#fitur" class="block text-gray-700 hover:text-primary transition-colors font-medium py-2" onclick="toggleMobileMenu()">Fitur</a></li>
                    <li><a href="#kontak" class="block text-gray-700 hover:text-primary transition-colors font-medium py-2" onclick="toggleMobileMenu()">Kontak</a></li>
                </ul>

                <!-- Language Switcher (Mobile) -->
                <div class="mt-4 pt-3 border-t border-primary/10">
                    <p class="text-xs text-gray-400 mb-2">Language</p>

                    <div class="flex gap-2">
                        <form action="{{ route('language.set') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="locale" value="id">
                            <button type="submit"
                                class="w-full px-3 py-1 text-xs font-semibold rounded-full border
                                    {{ $currentLocale === 'id'
                                        ? 'bg-choco text-white border-choco'
                                        : 'bg-transparent text-gray-700 border-choco/40 hover:bg-soft-choco/10' }}">
                                Indonesia
                            </button>
                        </form>

                        <form action="{{ route('language.set') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="locale" value="en">
                            <button type="submit"
                                class="w-full px-3 py-1 text-xs font-semibold rounded-full border
                                    {{ $currentLocale === 'en'
                                        ? 'bg-choco text-white border-choco'
                                        : 'bg-transparent text-gray-700 border-choco/40 hover:bg-soft-choco/10' }}">
                                English
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>
    
    {{-- <body class="font-sans antialiased bg-gray-100 dark:bg-black text-gray-900 dark:text-white">
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
            @endisset --}}

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
        {{-- @include('layouts.footer') --}}

        <!-- Fancybox JS -->
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox/fancybox.umd.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
        <script src="https://unpkg.com/@formkit/auto-animate@1" defer></script>
        <script>
            function toggleMobileMenu() {
                const menu = document.getElementById('mobileMenu');

                if (!menu) return;

                // toggle kelas "hidden" saja sudah cukup, karena default-nya kamu kasih class "hidden"
                menu.classList.toggle('hidden');
            }
        </script>
        @stack('scripts')
    </body>
</html>
