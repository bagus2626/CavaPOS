{{-- Enhanced Floating Navigation --}}
<div id="customer-navbar" class="fixed top-0 left-0 right-0 z-50 flex w-full justify-center p-4 bg-transparent">
    <nav class="flex w-full max-w-[95vw] md:max-w-3xl lg:max-w-[1216px] items-center justify-between gap-4 rounded-full bg-white px-4 py-3 shadow-lg transition-all duration-300">

        {{-- Left Section: Back Button & User Name --}}
        <div class="flex items-center gap-3">
            {{-- Back Button --}}
            <a href="javascript:history.back()"
                class="group flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-full bg-[#ae1504] hover:bg-[#8a1103] text-white transition-all duration-200">
                <svg class="w-5 h-5 transition-transform group-hover:-translate-x-0.5" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14M5 12l4-4m-4 4 4 4" />
                </svg>
            </a>

            {{-- Divider --}}
            <div class="h-6 w-px bg-gray-300 hidden sm:block"></div>

            @php
                $authCustomer = auth('customer')->user();
                $guest = session('guest_customer');
                $displayName = $authCustomer?->name ?? ($guest->name ?? null);

                $ps = $partner_slug ?? null ?: request('partner_slug') ?: session('customer.partner_slug');
                $tc = $table_code ?? null ?: request('table_code') ?: session('customer.table_code');
                $logoutAction =
                    $ps && $tc
                        ? route('customer.logout', ['partner_slug' => $ps, 'table_code' => $tc])
                        : route('customer.logout.simple');
            @endphp

            {{-- Display User Name --}}
            @if ($displayName)
                <h1 class="text-lg font-bold text-gray-900">
                    @if ($displayName === 'Guest')
                        {{ __('messages.customer.navbar.guest') }}
                    @else
                        {{ $displayName }}
                    @endif
                </h1>
            @endif
        </div>

        {{-- Right Section: Utilities --}}
        <div class="flex items-center gap-2 md:gap-3">
            {{-- Language Selector (Desktop) --}}
            <div class="hidden md:block relative" id="cust-lang-desktop">
                <button type="button"
                    class="flex h-10 items-center gap-2 rounded-full border border-gray-300 bg-transparent px-4 text-sm font-bold text-gray-700 transition-all hover:bg-gray-100"
                    data-toggle="lang-dropdown" aria-haspopup="true" aria-expanded="false">
                    <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12.87 15.07l-2.54-2.51.03-.03c1.74-1.94 2.98-4.17 3.71-6.53H17V4h-7V2H8v2H1v1.99h11.17C11.5 7.92 10.44 9.75 9 11.35 8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5 3.11 3.11.76-2.04zM18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12zm-2.62 7l1.62-4.33L19.12 17h-3.24z" />
                    </svg>
                    <span>{{ app()->getLocale() === 'id' ? 'ID' : 'EN' }}</span>
                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div class="hidden absolute right-0 mt-2 w-40 rounded-xl border border-gray-200 bg-white shadow-xl overflow-hidden"
                    data-menu="lang-dropdown">
                    <button type="button"
                        class="w-full px-4 py-2.5 text-left text-sm hover:bg-gray-50 flex items-center gap-2 transition-colors
                        {{ app()->getLocale() === 'en' ? 'font-semibold text-red-500 bg-red-50' : 'text-gray-700' }}"
                        data-lang="en">
                        <span>🇬🇧</span><span>English</span>
                    </button>
                    <button type="button"
                        class="w-full px-4 py-2.5 text-left text-sm hover:bg-gray-50 flex items-center gap-2 transition-colors
                        {{ app()->getLocale() === 'id' ? 'font-semibold text-red-500 bg-red-50' : 'text-gray-700' }}"
                        data-lang="id">
                        <span>🇮🇩</span><span>Bahasa</span>
                    </button>
                </div>
            </div>

            {{-- Order History Button (Desktop Only) --}}
            @if ($ps && $tc && auth('customer')->check())
                <a href="{{ route('customer.orders.histories', ['partner_slug' => $ps, 'table_code' => $tc]) }}"
                    class="hidden md:flex h-10 items-center gap-2 rounded-full border border-gray-300 bg-transparent px-4 text-sm font-bold text-gray-700 transition-all hover:bg-gray-100"
                    title="{{ __('messages.customer.navbar.order_history') }}">
                    <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6l4 2m4-2a8 8 0 11-16 0 8 8 0 0116 0z" />
                    </svg>
                    <span class="hidden lg:inline">History</span>
                </a>
            @endif

            {{-- Logout Button (Desktop) --}}
            @auth('customer')
                <form class="hidden md:block my-auto" method="POST" action="{{ $logoutAction }}">
                    @csrf
                    <button type="submit"
                        class="group flex h-10 items-center gap-2 rounded-full bg-[#ae1504] px-5 text-sm font-bold text-white transition-all hover:bg-[#8a1103]">
                        <span class="truncate">{{ __('messages.customer.navbar.logout') }}</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            @endauth

            @if (session('guest_customer') && $ps && $tc)
                <form class="hidden md:block my-auto" method="POST"
                    action="{{ route('customer.guest-logout', ['partner_slug' => $ps, 'table_code' => $tc]) }}">
                    @csrf
                    <button type="submit"
                        class="group flex h-10 items-center gap-2 rounded-full bg-red-600 px-5 text-sm font-bold text-white transition-all hover:bg-red-700">
                        <span class="truncate">{{ __('messages.customer.navbar.logout') }}</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            @endif

            {{-- Mobile Menu Toggle --}}
            <button id="mobile-menu-button"
                class="flex md:hidden h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </nav>
</div>

{{-- Mobile Menu --}}
<div id="mobile-menu" class="hidden md:hidden fixed top-20 left-4 right-4 z-40">
    <div class="rounded-2xl bg-white shadow-xl overflow-hidden border border-gray-200">
        {{-- User Name Section --}}
        @if ($displayName)
            <div class="px-4 py-3 border-b border-gray-200">
                <p class="font-bold text-gray-900">
                    @if ($displayName === 'Guest')
                        {{ __('messages.customer.navbar.guest') }}
                    @else
                        {{ $displayName }}
                    @endif
                </p>
            </div>
        @endif

        {{-- Menu Links --}}
        <div class="p-2">
            {{-- Language Switcher Mobile --}}
            <div class="px-3 py-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">{{ __('messages.customer.navbar.language') }}</span>
                <div class="inline-flex rounded-lg overflow-hidden border border-gray-300">
                    <button type="button"
                        class="px-4 py-1.5 text-sm font-medium transition-colors
                            {{ app()->getLocale() === 'en' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                        data-lang="en">
                        EN
                    </button>
                    <button type="button"
                        class="px-4 py-1.5 text-sm font-medium transition-colors
                            {{ app()->getLocale() === 'id' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                        data-lang="id">
                        ID
                    </button>
                </div>
            </div>

            <div class="h-px bg-gray-200 my-1"></div>

            {{-- Menu Link --}}
            <a href="{{ route('customer.menu.index', ['partner_slug' => $ps, 'table_code' => $tc]) }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors group">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-red-500 transition-colors" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        d="M2,21H20V19H2M20,8H18V5H20M20,3H4V13A4,4 0 0,0 8,17H14A4,4 0 0,0 18,13V10H20A2,2 0 0,0 22,8V5C22,3.89 21.1,3 20,3Z" />
                </svg>
                <span class="font-medium">Menu</span>
            </a>

            {{-- Order History Link (Mobile) --}}
            @if ($ps && $tc && auth('customer')->check())
                <a href="{{ route('customer.orders.histories', ['partner_slug' => $ps, 'table_code' => $tc]) }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors group">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-red-500 transition-colors"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6l4 2m4-2a8 8 0 11-16 0 8 8 0 0116 0z" />
                    </svg>
                    <span class="font-medium">{{ __('messages.customer.navbar.order_history') }}</span>
                </a>
            @endif

            <div class="h-px bg-gray-200 my-1"></div>

            {{-- Logout --}}
            @auth('customer')
                <form method="POST" action="{{ $logoutAction }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors group">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="font-medium">{{ __('messages.customer.navbar.logout') }}</span>
                    </button>
                </form>
            @endauth

            @if (session('guest_customer') && $ps && $tc)
                <form method="POST"
                    action="{{ route('customer.guest-logout', ['partner_slug' => $ps, 'table_code' => $tc]) }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors group">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="font-medium">{{ __('messages.customer.navbar.logout_guest') }}</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- Hidden form untuk switch language --}}
<form id="cust-lang-form" action="{{ route('language.set') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="locale" id="cust-lang-input" value="{{ app()->getLocale() }}">
</form>

{{-- JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const btn = document.getElementById('mobile-menu-button');
        const menu = document.getElementById('mobile-menu');

        btn?.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (menu && !menu.contains(e.target) && !btn?.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // Desktop dropdown
        const desktopRoot = document.getElementById('cust-lang-desktop');
        const toggleBtn = desktopRoot?.querySelector('[data-toggle="lang-dropdown"]');
        const dropdown = desktopRoot?.querySelector('[data-menu="lang-dropdown"]');

        function closeDropdown() {
            if (dropdown) dropdown.classList.add('hidden');
            if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        }

        function openDropdown() {
            if (dropdown) dropdown.classList.remove('hidden');
            if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
        }

        toggleBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = dropdown?.classList.contains('hidden');
            isHidden ? openDropdown() : closeDropdown();
        });

        document.addEventListener('click', (e) => {
            if (!desktopRoot?.contains(e.target)) closeDropdown();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDropdown();
        });

        // Language switch
        const form = document.getElementById('cust-lang-form');
        const input = document.getElementById('cust-lang-input');

        function setLocaleAndSubmit(locale) {
            if (!form || !input) return;
            input.value = locale;
            form.submit();
        }

        // Desktop & Mobile language buttons
        document.querySelectorAll('[data-lang]').forEach(btn => {
            btn.addEventListener('click', () => setLocaleAndSubmit(btn.getAttribute('data-lang')));
        });
    });
</script>

<style>
    /* Smooth transitions */
    * {
        -webkit-tap-highlight-color: transparent;
    }

    /* Mobile menu animation */
    #mobile-menu {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Smooth hover effects */
    button,
    a {
        transition: all 0.2s ease-in-out;
    }
</style>
