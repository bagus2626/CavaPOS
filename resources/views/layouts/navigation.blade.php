<nav
    x-data="{ open: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => {
        scrolled = window.scrollY > 10;
    })"
    :class="scrolled
        ? 'bg-white/50 dark:bg-black/30 backdrop-blur-md shadow-md'
        : 'bg-[#c9c9c9cc] dark:bg-[#5d5d5de8]'"
    class="fixed w-full top-0 z-50 transition-all duration-300"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center gap-4">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <!-- Logo untuk Light Mode -->
                        <img src="{{ asset('images/vastech-transparent-black.png') }}"
                            class="block dark:hidden h-14 w-auto"
                            alt="Logo Light Mode">

                        <!-- Logo untuk Dark Mode -->
                        <img src="{{ asset('images/vastech-transparent-white.png') }}"
                            class="hidden dark:block h-14 w-auto"
                            alt="Logo Dark Mode">
                    </a>

                </div>

                <!-- Authenticated Navigation Links -->
                @auth
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
                @endauth
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('home') }}#about-content">
                        {{ __('Fitur') }}
                    </x-nav-link>

                    <x-nav-link :href="route('portfolio')" :active="request()->routeIs('portfolio')">
                        {{ __('Portfolio') }}
                    </x-nav-link>

                    {{-- harga --}}
                    @php
                        $isPriceActive = request()->routeIs('price') || request()->routeIs('price.show');
                        $classes = $isPriceActive
                            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-gray-800 dark:border-gray-200 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out dark:text-gray-100'
                            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out dark:text-gray-300';
                    @endphp
                    @php
                        use App\Models\Product\Product;
                        $products = Product::with('packages.specificationValues.specification')->get();
                    @endphp
                    <li class="relative list-none">
                        <a href="#" id="priceToggle" onclick="toggleDropdown(event)" class="{{ $classes }}">
                            Harga
                            <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0l-4.24-4.25a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </a>

                        <!-- Dropdown -->
                        <div id="priceDropdown" class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 dark:bg-gray-800" style="display: none; max-height: 300px; overflow-y: auto;">
                            @if ($products->isEmpty())
                                <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-300">Tidak ada produk</div>
                            @else
                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-100">
                                    @foreach ($products as $product)
                                        <li>
                                            <a href="{{ url('/price/' . $product->slug) }}" class="dropdown-item px-3 py-2 block text-dark dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700">
                                                {{ $product->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                    </li>
                    {{-- harga --}}


                    <x-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                        {{ __('Contact') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Dark Mode Switch -->
                    <button
                        @click="$store.darkMode.toggle()"
                        class="relative inline-flex items-center h-6 w-11 rounded-full transition-colors duration-300 ease-in-out focus:outline-none mr-5"
                        :class="$store.darkMode.on ? 'bg-gray-600' : 'bg-gray-300'"
                    >
                        <!-- Matahari -->
                        <img
                            src="/icons/icon-lightmode.png"
                            alt="Light Mode"
                            class="absolute left-1 top-1 w-4 h-4 transition-opacity duration-300 ease-in-out"
                            :class="$store.darkMode.on ? 'opacity-0' : 'opacity-100'"
                        >

                        <!-- Bulan -->
                        <img
                            src="/icons/icon-darkmode.png"
                            alt="Dark Mode"
                            class="absolute right-1 top-1 w-4 h-4 transition-opacity duration-300 ease-in-out"
                            :class="$store.darkMode.on ? 'opacity-100' : 'opacity-0'"
                        >
                    </button>

                @auth
                    <!-- User Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-gray-500 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-white focus:outline-none">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth

                @guest
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                Register
                            </a>
                        @endif
                    </div>
                @endguest
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                              stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                              stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="sm:hidden hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @endauth

            @guest
            <!-- Dark Mode Switch -->
                <div x-data class="flex items-center ms-4 mr-4 mb-5 bg-gray-100 dark:bg-gray-800 px-3 py-2 justify-center rounded-md">
                    <span class="text-sm text-gray-600 dark:text-gray-300 me-2">Light</span>

                    <button
                        @click="$store.darkMode.toggle()"
                        class="relative inline-flex items-center h-6 w-11 rounded-full transition-colors duration-300 ease-in-out focus:outline-none"
                        :class="$store.darkMode.on ? 'bg-gray-600' : 'bg-gray-300'"
                    >
                        <span
                            class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-300 ease-in-out"
                            :class="$store.darkMode.on ? 'translate-x-6' : 'translate-x-1'"
                        ></span>
                    </button>

                    <span class="text-sm text-gray-600 dark:text-gray-300 ms-2">Dark</span>
                </div>
                <div class="space-y-1 px-4">
                    <a href="{{ route('login') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        Login
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="block text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            Register
                        </a>
                    @endif
                </div>
            @endguest
        </div>
    </div>
</nav>
<script>
    function toggleDropdown(event) {
        event.preventDefault();
        const dropdown = document.getElementById('priceDropdown');
        if (!dropdown) return;

        // toggle tampil/hidden dropdown
        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    }

    // Optional: tutup dropdown kalau klik di luar
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('priceDropdown');
        const toggle = document.getElementById('priceToggle');
        if (!dropdown || !toggle) return;

        if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>
