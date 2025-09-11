<nav class="bg-white shadow-md fixed top-0 left-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16 relative">
            {{-- Tombol Back --}}
            <a href="javascript:history.back()"
               class="absolute left-4 top-1/2 -translate-y-1/2 hover:bg-soft-choco p-2 rounded-full shadow-md border border-choco border">
                <svg class="w-6 h-6 text-choco dark:text-choco" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/>
                </svg>

            </a>

            {{-- Brand --}}
            <div class="flex-1 flex justify-center">
                <a href="{{ url('/') }}" class="text-xl font-bold text-choco">
                    {{ config('app.name', 'FoodBee') }}
                </a>
            </div>

            {{-- Menu Desktop --}}
            <div class="hidden md:flex items-center space-x-6">
                <a href="#" class="text-gray-700 hover:text-blue-500">Home</a>
                <a href="#" class="text-gray-700 hover:text-blue-500">Menu</a>
                <a href="#" class="text-gray-700 hover:text-blue-500">Contact</a>
                @auth('customer') {{-- jika customer login --}}
                    <form class="my-auto" method="POST" action="{{ route('customer.logout', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-choco hover:bg-red-100">
                            Logout
                        </button>
                    </form>
                @endauth
                @if(session('guest_customer'))
                    <form class="my-auto" method="POST" action="{{ route('customer.guest-logout', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-choco hover:bg-red-100 rounded-lg">
                            Logout (Guest)
                        </button>
                    </form>
                @endif
            </div>

            {{-- Mobile toggle --}}
            <div class="flex items-center md:hidden absolute right-4">
                <button id="mobile-menu-button" class="text-gray-700 focus:outline-none">
                    <!-- Icon burger -->
                    <svg class="w-6 h-6 text-choco" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="hidden md:hidden">
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Home</a>
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Menu</a>
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Contact</a>
        @auth('customer') {{-- jika customer login --}}
            <form method="POST" action="{{ route('customer.logout', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}">
                @csrf
                <button type="submit"
                    class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-100">
                    Logout
                </button>
            </form>
        @endauth
        @if(session('guest_customer'))
            <form method="POST" action="{{ route('customer.guest-logout', ['partner_slug' => $partner_slug, 'table_code' => $table_code]) }}">
                @csrf
                <button type="submit"
                    class="w-full text-left px-4 py-2 text-choco hover:bg-red-100">
                    Logout (Guest)
                </button>
            </form>
        @endif
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('mobile-menu-button');
        const menu = document.getElementById('mobile-menu');
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    });
</script>
