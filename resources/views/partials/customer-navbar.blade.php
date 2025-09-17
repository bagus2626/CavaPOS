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
                <a href="#" class="text-gray-700 hover:text-blue-500">{{ __('messages.customer.navbar.home') }}</a>
                <a href="#" class="text-gray-700 hover:text-blue-500">{{ __('messages.customer.navbar.menu') }}</a>
                <a href="#" class="text-gray-700 hover:text-blue-500">{{ __('messages.customer.navbar.contact') }}</a>
                <div class="relative" id="cust-lang-desktop">
                    {{-- switch language --}}
                <button
                    type="button"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-gray-200 bg-white text-gray-700 shadow-sm hover:bg-gray-50
                        dark:bg-choco dark:text-white dark:soft-choco dark:hover:bg-choco/80"
                    data-toggle="lang-dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    <!-- Globe icon -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2a10 10 0 100 20 10 10 0 000-20Zm0 2c1.49 0 2.86.41 4.04 1.12A13.8 13.8 0 0014 8H10a13.8 13.8 0 00-2.04-2.88A7.97 7.97 0 0112 4Z"/>
                    </svg>
                    <span class="font-medium">
                        {{ app()->getLocale() === 'id' ? 'Bahasa' : 'English' }}
                    </span>
                    <!-- Caret -->
                    <svg class="w-4 h-4 opacity-70" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- Dropdown -->
                <div
                    class="hidden absolute right-0 mt-2 w-40 rounded-md border border-gray-200 bg-white shadow-lg dark:bg-soft-choco dark:border-choco"
                    data-menu="lang-dropdown"
                >
                    <button
                        type="button"
                        class="w-full px-3 py-2 text-left text-sm hover:bg-choco dark:hover:bg-choco flex items-center gap-2
                            {{ app()->getLocale() === 'en' ? 'font-semibold text-gray-200 dark:text-white' : 'text-gray-700 dark:text-gray-200' }}"
                        data-lang="en"
                    >
                        <span>🇬🇧</span><span>English</span>
                    </button>
                    <button
                        type="button"
                        class="w-full px-3 py-2 text-left text-sm hover:bg-choco dark:hover:bg-choco flex items-center gap-2
                            {{ app()->getLocale() === 'id' ? 'font-semibold text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-200' }}"
                        data-lang="id"
                    >
                        <span>🇮🇩</span><span>Bahasa</span>
                    </button>
                </div>
            </div>
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
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">{{ __('messages.customer.navbar.home') }}</a>
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">{{ __('messages.customer.navbar.menu') }}</a>
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">{{ __('messages.customer.navbar.contact') }}</a>
        {{-- LANG SWITCH (MOBILE) --}}
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-700 dark:text-gray-500">Language</span>
                <div class="inline-flex rounded-md overflow-hidden border borderchoco dark:border-choco">
                    <button
                        type="button"
                        class="px-3 py-1.5 text-sm
                            {{ app()->getLocale()==='en' ? 'bg-choco text-white dark:bg-white dark:text-choco' : 'bg-white text-soft-choco hover:bg-gray-50 dark:bg-choco dark:text-white dark:hover:bg-choco/25' }}"
                        data-lang="en"
                    >
                        EN
                    </button>
                    <button
                        type="button"
                        class="px-3 py-1.5 text-sm
                            {{ app()->getLocale()==='id' ? 'bg-choco text-white dark:bg-white dark:text-choco' : 'bg-white text-soft-choco hover:bg-gray-50 dark:bg-choco dark:text-white dark:hover:bg-choco/25' }}"
                        data-lang="id"
                    >
                        ID
                    </button>
                </div>
            </div>
        </div>

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
    {{-- hidden form untuk switch language --}}
    <form id="cust-lang-form" action="{{ route('language.set') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="locale" id="cust-lang-input" value="{{ app()->getLocale() }}">
</form>

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
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // === Desktop dropdown ===
    const desktopRoot  = document.getElementById('cust-lang-desktop');
    const toggleBtn    = desktopRoot?.querySelector('[data-toggle="lang-dropdown"]');
    const dropdown     = desktopRoot?.querySelector('[data-menu="lang-dropdown"]');

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

    // === Submit helper ===
    const form  = document.getElementById('cust-lang-form');
    const input = document.getElementById('cust-lang-input');

    function setLocaleAndSubmit(locale) {
      if (!form || !input) return;
      input.value = locale;
      form.submit();
    }

    // Desktop items
    desktopRoot?.querySelectorAll('[data-lang]').forEach(btn => {
      btn.addEventListener('click', () => setLocaleAndSubmit(btn.getAttribute('data-lang')));
    });

    // Mobile items (pakai selector umum di #mobile-menu)
    document.querySelectorAll('#mobile-menu [data-lang]').forEach(btn => {
      btn.addEventListener('click', () => setLocaleAndSubmit(btn.getAttribute('data-lang')));
    });
  });
</script>

