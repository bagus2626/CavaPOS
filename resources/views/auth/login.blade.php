<x-app-layout>
    <x-guest-layout>
        <div
            class="fixed inset-0 flex flex-col justify-center items-center bg-gradient-to-br from-[#dc2626] via-[#b91c1c] to-[#991b1b] overflow-hidden">

            {{-- === Dekorasi Background === --}}
            <div
                class="pointer-events-none absolute top-10 sm:top-20 left-10 sm:left-20 w-40 sm:w-64 h-40 sm:h-64 bg-white/5 rounded-full blur-3xl">
            </div>
            <div
                class="pointer-events-none absolute bottom-10 sm:bottom-20 right-10 sm:right-20 w-60 sm:w-96 h-60 sm:h-96 bg-white/5 rounded-full blur-3xl">
            </div>
            <div
                class="pointer-events-none absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[400px] sm:w-[600px] h-[400px] sm:h-[600px] bg-white/3 rounded-full blur-3xl">
            </div>

            {{-- Ornamen kiri atas --}}
            <div class="pointer-events-none absolute top-0 left-0 w-24 sm:w-40 h-24 sm:h-40 opacity-10 hidden sm:block">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path fill="#ffffff"
                        d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,79.6,-45.8C87.4,-32.6,90,-16.3,88.5,-0.9C87,14.6,81.4,29.2,73.1,42.3C64.8,55.4,53.8,67,40.4,74.2C27,81.4,11.2,84.2,-4.8,81.8C-20.8,79.4,-41.6,71.8,-56.8,60.1C-72,48.4,-81.6,32.6,-85.4,15.4C-89.2,-1.8,-87.2,-20.4,-79.8,-36.2C-72.4,-52,-59.6,-65,-45.2,-71.8C-30.8,-78.6,-15.4,-79.2,0.3,-79.8C16,-80.4,32,-81,44.7,-76.4Z"
                        transform="translate(100 100)" />
                </svg>
            </div>

            {{-- Ornamen kanan bawah --}}
            <div
                class="pointer-events-none absolute bottom-0 right-0 w-32 sm:w-48 h-32 sm:h-48 opacity-10 hidden sm:block">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path fill="#ffffff"
                        d="M39.5,-65.6C52.3,-58.2,64.5,-50.1,71.8,-38.4C79.1,-26.7,81.5,-11.4,80.2,3.5C78.9,18.4,74,32.9,65.4,44.8C56.8,56.7,44.5,66,30.8,71.5C17.1,77,1.9,78.7,-13.5,76.3C-28.9,73.9,-44.5,67.4,-56.8,57.2C-69.1,47,-78.1,33.1,-81.7,17.7C-85.3,2.3,-83.5,-14.6,-76.9,-28.7C-70.3,-42.8,-58.9,-54.1,-45.8,-61.3C-32.7,-68.5,-18.9,-71.6,-5.2,-70.1C8.5,-68.6,26.7,-73,39.5,-65.6Z"
                        transform="translate(100 100)" />
                </svg>
            </div>

            {{-- Ornamen geometris kiri --}}
            <div class="pointer-events-none absolute left-4 sm:left-10 top-1/3 opacity-10 hidden md:block">
                <div class="w-16 sm:w-24 h-16 sm:h-24 border-2 sm:border-4 border-white rounded-full"></div>
                <div
                    class="w-10 sm:w-16 h-10 sm:h-16 border-2 sm:border-4 border-white rounded-full absolute top-3 sm:top-4 left-3 sm:left-4">
                </div>
            </div>

            {{-- Ornamen geometris kanan --}}
            <div class="pointer-events-none absolute right-4 sm:right-10 top-1/2 opacity-10 hidden md:block">
                <div class="w-14 sm:w-20 h-14 sm:h-20 border-2 sm:border-4 border-white transform rotate-45"></div>
                <div
                    class="w-8 sm:w-12 h-8 sm:h-12 border-2 sm:border-4 border-white transform rotate-45 absolute top-3 sm:top-4 left-3 sm:left-4">
                </div>
            </div>

            {{-- Pattern dots --}}
            <div class="pointer-events-none absolute inset-0 opacity-5 hidden lg:block">
                <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-white rounded-full"></div>
                <div class="absolute top-1/3 left-1/3 w-2 h-2 bg-white rounded-full"></div>
                <div class="absolute top-2/3 right-1/4 w-2 h-2 bg-white rounded-full"></div>
                <div class="absolute top-3/4 right-1/3 w-2 h-2 bg-white rounded-full"></div>
                <div class="absolute top-1/2 left-1/5 w-1 h-1 bg-white rounded-full"></div>
                <div class="absolute top-1/4 right-1/5 w-1 h-1 bg-white rounded-full"></div>
            </div>

            {{-- === Logo === --}}
            {{-- <div class="mb-4 sm:mb-8 relative z-10 px-4">
                <a href="/">
                    <img src="{{ asset('images/cava-logo2.png') }}" class="h-14 w-auto drop-shadow-lg" alt="Cavaa Logo">
                </a>
            </div> --}}

            {{-- === Card Login === --}}
            <div class="w-full max-w-lg px-4 sm:px-6 relative z-10">
                <div class="bg-white/95 backdrop-blur-sm rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden relative">

                    {{-- Ornamen dalam card --}}
                    <div class="absolute top-0 left-0 w-20 sm:w-32 h-20 sm:h-32 opacity-5 pointer-events-none">
                        <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="0" cy="0" r="80" fill="#dc2626" />
                        </svg>
                    </div>
                    <div class="absolute bottom-0 right-0 w-20 sm:w-32 h-20 sm:h-32 opacity-5 pointer-events-none">
                        <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="100" cy="100" r="80" fill="#991b1b" />
                        </svg>
                    </div>

                    {{-- Header Card Login --}}
                    <div class="px-6 sm:px-8 pt-6 sm:pt-10 pb-4 sm:pb-8 text-center relative">
                        <h1 class="text-xl sm:text-2xl font-bold text-[#991b1b]">{{ __('messages.partner.auth.login.title') }}</h1>
                        <p class="mt-2 sm:mt-3 text-xs sm:text-sm text-gray-600">
                            {{ __('messages.partner.auth.login.subtitle') }}
                            <span class="font-semibold text-[#b91c1c]">{{ __('messages.partner.auth.login.partner_dashboard') }}</span>
                        </p>
                    </div>

                    {{-- Session Status --}}
                    <div class="px-6 sm:px-8">
                        <x-auth-session-status class="mb-4" :status="session('status')" />
                    </div>

                    {{-- === Form Login === --}}
                    <form method="POST" action="{{ route('login') }}" class="px-6 sm:px-8 pb-6 sm:pb-8 relative">
                        @csrf

                        {{-- Username --}}
                        <div class="mt-2">
                            <label for="username"
                                class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                                {{ __('messages.partner.auth.login.username') }}
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 sm:left-4 flex items-center text-[#b91c1c]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z" />
                                    </svg>
                                </span>
                                <input id="username" name="username" type="text" value="{{ old('username') }}"
                                    placeholder="{{ __('messages.partner.auth.login.username_placeholder') }}" required autofocus autocomplete="username"
                                    class="block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-2.5 sm:py-3 text-sm sm:text-base rounded-lg sm:rounded-xl border-2 border-red-100 bg-red-50/30
                                           focus:border-[#dc2626] focus:bg-white focus:ring-4 focus:ring-[#dc2626]/10 transition-all duration-300
                                           placeholder:text-gray-400 text-gray-800" />
                            </div>
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        {{-- Password --}}
                        <div class="mt-4 sm:mt-5">
                            <div class="flex items-center justify-between mb-1.5 sm:mb-2">
                                <label for="password" class="block text-xs sm:text-sm font-semibold text-gray-700">
                                    {{ __('messages.partner.auth.login.password') }}
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                        class="text-xs sm:text-sm text-[#dc2626] hover:text-[#991b1b] transition-colors font-semibold">
                                        {{ __('messages.partner.auth.login.forgot_password') }}
                                    </a>
                                @endif
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 sm:left-4 flex items-center text-[#b91c1c]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M17 9h-1V7a4 4 0 10-8 0v2H7a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2v-8a2 2 0 00-2-2Zm-6 7.732V17a1 1 0 112 0v-.268a2 2 0 10-2 0ZM9 7a3 3 0 116 0v2H9V7Z" />
                                    </svg>
                                </span>
                                <input id="password" name="password" type="password" placeholder="{{ __('messages.partner.auth.login.password_placeholder') }}"
                                    required autocomplete="current-password"
                                    class="block w-full pl-10 sm:pl-12 pr-10 sm:pr-12 py-2.5 sm:py-3 text-sm sm:text-base rounded-lg sm:rounded-xl border-2 border-red-100 bg-red-50/30
                                           focus:border-[#dc2626] focus:bg-white focus:ring-4 focus:ring-[#dc2626]/10 transition-all duration-300
                                           placeholder:text-gray-400 text-gray-800" />
                                <button type="button" id="togglePassword" aria-label="Show password"
                                    class="absolute inset-y-0 right-3 sm:right-4 px-2 flex items-center text-[#b91c1c] hover:text-[#991b1b] transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 110-10 5 5 0 010 10Z" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        {{-- Remember Me --}}
                        <div class="block mt-4 sm:mt-5">
                            <label for="remember_me" class="inline-flex items-center select-none cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember"
                                    class="rounded border-red-200 text-[#dc2626] shadow-sm focus:ring-[#dc2626] transition-all cursor-pointer w-4 h-4">
                                <span class="ms-2 text-xs sm:text-sm text-gray-700 font-medium">{{ __('messages.partner.auth.login.remember_me') }}</span>
                            </label>
                        </div>

                        {{-- Submit --}}
                        <div class="mt-5 sm:mt-7">
                            <button type="submit"
                                class="w-full py-3 sm:py-3.5 rounded-lg sm:rounded-xl bg-gradient-to-r from-[#dc2626] via-[#b91c1c] to-[#991b1b]
                                           hover:from-[#b91c1c] hover:via-[#991b1b] hover:to-[#7f1d1d]
                                           shadow-lg hover:shadow-xl active:scale-[0.98]
                                           transition-all duration-300 font-bold text-white uppercase tracking-wide text-xs sm:text-sm">
                                {{ __('messages.partner.auth.login.login_button') }}
                            </button>
                        </div>

                        {{-- Google SSO --}}
                        @if (Route::has('oauth.google.redirect'))
                            <a href="{{ route('oauth.google.redirect') }}"
                                class="mt-4 sm:mt-5 inline-flex w-full py-3 sm:py-3.5 items-center justify-center gap-2 sm:gap-3 rounded-lg sm:rounded-xl
                                      border-2 border-red-100 bg-white hover:bg-red-50/50
                                      text-gray-700 transition-all duration-300 shadow-sm hover:shadow-md hover:border-red-200">
                                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
                                    alt="" class="h-4 w-4 sm:h-5 sm:w-5">
                                <span class="text-xs sm:text-sm font-semibold">Masuk dengan Google</span>
                            </a>
                        @endif
                    </form>

                    {{-- Footer --}}
                    <div class="px-6 sm:px-8 pb-4 sm:pb-8 text-center border-t border-red-100 pt-4 sm:pt-6 relative">
                        <p class="text-[10px] sm:text-xs text-gray-500">
                            © {{ now()->year }} {{ config('app.name', 'Maemm') }}. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- === Script Show/Hide Password === --}}
        <script>
            (function() {
                const btn = document.getElementById('togglePassword');
                const input = document.getElementById('password');
                if (!btn || !input) return;

                btn.addEventListener('click', () => {
                    const isPwd = input.type === 'password';
                    input.type = isPwd ? 'text' : 'password';
                    btn.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');

                    // Ganti ikon
                    btn.innerHTML = isPwd ?
                        `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46A11.804 11.804 0 0 0 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78 3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/></svg>` :
                        `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 110-10 5 5 0 010 10Z"/></svg>`;
                });
            })();
        </script>
    </x-guest-layout>
</x-app-layout>
