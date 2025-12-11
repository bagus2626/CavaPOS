<x-app-layout>
    <x-guest-layout>
        <div class="fixed inset-0 bg-gradient-to-br from-[#dc2626] via-[#b91c1c] to-[#991b1b] overflow-y-auto overflow-x-hidden">

            {{-- Background decorative elements --}}
            <div class="pointer-events-none fixed top-10 sm:top-20 left-10 sm:left-20 w-40 sm:w-64 h-40 sm:h-64 bg-white/5 rounded-full blur-3xl"></div>
            <div class="pointer-events-none fixed bottom-10 sm:bottom-20 right-10 sm:right-20 w-60 sm:w-96 h-60 sm:h-96 bg-white/5 rounded-full blur-3xl"></div>
            <div class="pointer-events-none fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[400px] sm:w-[600px] h-[400px] sm:h-[600px] bg-white/3 rounded-full blur-3xl"></div>

            {{-- Wrapper --}}
            <div class="min-h-full flex flex-col justify-center items-center py-4 sm:py-6 pt-8 sm:pt-14">

                {{-- Card Register --}}
                <div class="w-full max-w-lg px-4 sm:px-6 relative z-10">
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden relative">

                        {{-- Ornamen dalam card --}}
                        <div class="absolute top-0 right-0 w-20 sm:w-32 h-20 sm:h-32 opacity-5 pointer-events-none">
                            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="100" cy="0" r="80" fill="#dc2626" />
                            </svg>
                        </div>

                        {{-- Header Card Register --}}
                        <div class="px-5 sm:px-6 pt-4 sm:pt-5 pb-2 text-center relative">
                            <h1 class="text-xl sm:text-2xl font-bold text-[#991b1b]">
                                {{ __('messages.owner.auth.register.title') }}
                            </h1>
                            <p class="mt-0.5 text-[11px] sm:text-xs text-gray-600">
                                {!! __('messages.owner.auth.register.subtitle') !!}
                            </p>
                        </div>

                        {{-- Form --}}
                        <form method="POST" action="{{ route('owner.register.store') }}"
                              class="px-5 sm:px-6 pb-4 sm:pb-5 relative">
                            @csrf

                            {{-- Name --}}
                            <div class="mt-4">
                                <label for="name"
                                       class="block text-[11px] sm:text-xs font-semibold text-gray-700 mb-1">
                                    {{ __('messages.owner.auth.register.name_label') }}
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-2.5 sm:left-3 flex items-center text-[#b91c1c]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                        </svg>
                                    </span>
                                    <input id="name"
                                           type="text"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required autofocus autocomplete="name"
                                           placeholder="{{ __('messages.owner.auth.register.name_placeholder') }}"
                                           class="block w-full pl-8 sm:pl-9 pr-2.5 sm:pr-3 py-1.5 sm:py-2 text-xs sm:text-sm rounded-lg border-2 border-red-100 bg-red-50/30
                                                  focus:border-[#dc2626] focus:bg-white focus:ring-2 focus:ring-[#dc2626]/10 
                                                  transition-all duration-300 placeholder:text-gray-400 text-gray-800" />
                                </div>
                                @error('name')
                                    <span class="text-red-600 text-[10px] sm:text-xs mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mt-3">
                                <label for="email"
                                       class="block text-[11px] sm:text-xs font-semibold text-gray-700 mb-1">
                                    {{ __('messages.owner.auth.register.email_label') }}
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-2.5 sm:left-3 flex items-center text-[#b91c1c]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                        </svg>
                                    </span>
                                    <input id="email"
                                           type="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required autocomplete="username"
                                           placeholder="{{ __('messages.owner.auth.register.email_placeholder') }}"
                                           class="block w-full pl-8 sm:pl-9 pr-2.5 sm:pr-3 py-1.5 sm:py-2 text-xs sm:text-sm rounded-lg border-2 border-red-100 bg-red-50/30
                                                  focus:border-[#dc2626] focus:bg-white focus:ring-2 focus:ring-[#dc2626]/10 
                                                  transition-all duration-300 placeholder:text-gray-400 text-gray-800" />
                                </div>
                                @error('email')
                                    <span class="text-red-600 text-[10px] sm:text-xs mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Phone Number --}}
                            <div class="mt-3">
                                <label for="phone_number"
                                       class="block text-[11px] sm:text-xs font-semibold text-gray-700 mb-1">
                                    {{ __('messages.owner.auth.register.phone_label') }}
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-2.5 sm:left-3 flex items-center text-[#b91c1c]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 00-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z" />
                                        </svg>
                                    </span>
                                    <input id="phone_number"
                                           type="text"
                                           name="phone_number"
                                           value="{{ old('phone_number') }}"
                                           required autocomplete="tel"
                                           placeholder="{{ __('messages.owner.auth.register.phone_placeholder') }}"
                                           class="block w-full pl-8 sm:pl-9 pr-2.5 sm:pr-3 py-1.5 sm:py-2 text-xs sm:text-sm rounded-lg border-2 border-red-100 bg-red-50/30
                                                  focus:border-[#dc2626] focus:bg-white focus:ring-2 focus:ring-[#dc2626]/10 
                                                  transition-all duration-300 placeholder:text-gray-400 text-gray-800" />
                                </div>
                                @error('phone_number')
                                    <span class="text-red-600 text-[10px] sm:text-xs mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="mt-3">
                                <label for="password"
                                       class="block text-[11px] sm:text-xs font-semibold text-gray-700 mb-1">
                                    {{ __('messages.owner.auth.register.password_label') }}
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-2.5 sm:left-3 flex items-center text-[#b91c1c]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M17 9h-1V7a4 4 0 10-8 0v2H7a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2v-8a2 2 0 00-2-2Zm-6 7.732V17a1 1 0 112 0v-.268a2 2 0 10-2 0ZM9 7a3 3 0 116 0v2H9V7Z" />
                                        </svg>
                                    </span>
                                    <input id="password"
                                           type="password"
                                           name="password"
                                           required autocomplete="new-password"
                                           placeholder="{{ __('messages.owner.auth.register.password_placeholder') }}"
                                           class="block w-full pl-8 sm:pl-9 pr-9 sm:pr-10 py-1.5 sm:py-2 text-xs sm:text-sm rounded-lg border-2 border-red-100 bg-red-50/30
                                                  focus:border-[#dc2626] focus:bg-white focus:ring-2 focus:ring-[#dc2626]/10 
                                                  transition-all duration-300 placeholder:text-gray-400 text-gray-800" />
                                    <button type="button" id="togglePassword"
                                            class="absolute inset-y-0 right-2 sm:right-2.5 px-1.5 flex items-center text-[#b91c1c] hover:text-[#991b1b] transition-colors"
                                            aria-label="Show password">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 110-10 5 5 0 010 10Z" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="text-red-600 text-[10px] sm:text-xs mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="mt-3">
                                <label for="password_confirmation"
                                       class="block text-[11px] sm:text-xs font-semibold text-gray-700 mb-1">
                                    {{ __('messages.owner.auth.register.password_confirmation_label') }}
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-2.5 sm:left-3 flex items-center text-[#b91c1c]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M17 9h-1V7a4 4 0 10-8 0v2H7a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2v-8a2 2 0 00-2-2Zm-6 7.732V17a1 1 0 112 0v-.268a2 2 0 10-2 0ZM9 7a3 3 0 116 0v2H9V7Z" />
                                        </svg>
                                    </span>
                                    <input id="password_confirmation"
                                           type="password"
                                           name="password_confirmation"
                                           required autocomplete="new-password"
                                           placeholder="{{ __('messages.owner.auth.register.password_confirmation_placeholder') }}"
                                           class="block w-full pl-8 sm:pl-9 pr-9 sm:pr-10 py-1.5 sm:py-2 text-xs sm:text-sm rounded-lg border-2 border-red-100 bg-red-50/30
                                                  focus:border-[#dc2626] focus:bg-white focus:ring-2 focus:ring-[#dc2626]/10 
                                                  transition-all duration-300 placeholder:text-gray-400 text-gray-800" />
                                    <button type="button" id="togglePasswordConfirmation"
                                            class="absolute inset-y-0 right-2 sm:right-2.5 px-1.5 flex items-center text-[#b91c1b] hover:text-[#991b1b] transition-colors"
                                            aria-label="Show password confirmation">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 110-10 5 5 0 010 10Z" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <span class="text-red-600 text-[10px] sm:text-xs mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Submit --}}
                            <div class="mt-5 sm:mt-7">
                                <button type="submit"
                                        class="w-full py-2 sm:py-2.5 rounded-lg bg-gradient-to-r from-[#dc2626] via-[#b91c1c] to-[#991b1b]
                                               hover:from-[#b91c1c] hover:via-[#991b1b] hover:to-[#7f1d1d]
                                               shadow-lg hover:shadow-xl active:scale-[0.98]
                                               transition-all duration-300 font-bold text-white uppercase tracking-wide text-[11px] sm:text-xs">
                                    {{ __('messages.owner.auth.register.submit_button') }}
                                </button>
                            </div>

                            {{-- Footer --}}
                            <div class="px-6 sm:px-8 pb-4 sm:pb-6 text-center border-t border-red-100 pt-3 sm:pt-5 relative">
                                <p class="text-[10px] sm:text-[11px] text-gray-500 mb-1">
                                    {{ __('messages.owner.auth.register.already_have_account') }}
                                    <a href="{{ route('owner.login') }}"
                                       class="text-[#dc2626] hover:text-[#991b1b] font-semibold transition-colors">
                                        {{ __('messages.owner.auth.register.login_here') }}
                                    </a>
                                </p>
                                <p class="text-[9px] sm:text-[10px] text-gray-500">
                                    © {{ now()->year }} {{ config('app.name', 'Maemm') }}.
                                    {{ __('messages.owner.auth.register.copyright') }}
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Show/Hide Password & Input Validation Script --}}
        <script>
            (function() {
                // Validasi Nama Lengkap - hanya huruf dan spasi
                const nameInput = document.getElementById('name');
                if (nameInput) {
                    nameInput.addEventListener('input', function(e) {
                        this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
                    });
                    nameInput.addEventListener('keypress', function(e) {
                        const char = String.fromCharCode(e.which);
                        if (!/[a-zA-Z\s]/.test(char)) {
                            e.preventDefault();
                        }
                    });
                }

                // Validasi Nomor Telepon - hanya angka
                const phoneInput = document.getElementById('phone_number');
                if (phoneInput) {
                    phoneInput.addEventListener('input', function(e) {
                        this.value = this.value.replace(/\D/g, '');
                    });
                    phoneInput.addEventListener('keypress', function(e) {
                        const char = String.fromCharCode(e.which);
                        if (!/\d/.test(char)) {
                            e.preventDefault();
                        }
                    });
                }

                // Toggle password visibility
                function setupPasswordToggle(buttonId, inputId) {
                    const btn = document.getElementById(buttonId);
                    const input = document.getElementById(inputId);
                    if (!btn || !input) return;

                    btn.addEventListener('click', () => {
                        const isPwd = input.type === 'password';
                        input.type = isPwd ? 'text' : 'password';
                        btn.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');

                        if (isPwd) {
                            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46A11.804 11.804 0 0 0 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78 3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>
                            </svg>`;
                        } else {
                            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 110-10 5 5 0 010 10Z"/>
                            </svg>`;
                        }
                    });
                }

                setupPasswordToggle('togglePassword', 'password');
                setupPasswordToggle('togglePasswordConfirmation', 'password_confirmation');
            })();
        </script>
    </x-guest-layout>
</x-app-layout>
