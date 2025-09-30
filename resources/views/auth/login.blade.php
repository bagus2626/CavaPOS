<x-app-layout>
  <x-guest-layout>
      {{-- decorative blobs (halus, non-intrusive) --}}
      <div class="pointer-events-none absolute -top-16 -left-16 w-56 h-56 rounded-full bg-choco/10 blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-16 -right-8 w-72 h-72 rounded-full bg-soft-choco/20 blur-3xl"></div>

      <div class="w-full max-w-md mx-auto px-4">
        <div class="bg-white/90 backdrop-blur rounded-2xl ring-1 ring-gray-100">
          {{-- Header / Brand --}}
          <div class="px-6 pt-6 pb-3 text-center">
            <h1 class="mt-3 text-xl font-bold text-gray-900">
              Selamat datang kembali
            </h1>
            <p class="mt-1 text-sm text-gray-500">
              Masuk untuk melanjutkan ke <span class="font-medium text-choco">{{ config('app.name', 'Cavaa') }}</span>
            </p>
          </div>

          {{-- Session Status --}}
          <div class="px-6">
            <x-auth-session-status class="mb-4" :status="session('status')" />
          </div>

          {{-- Form --}}
          <form method="POST" action="{{ route('login') }}" class="px-6 pb-6">
            @csrf

            {{-- Username --}}
            <div class="mt-2">
              <x-input-label for="username" :value="__('Username')" />
              <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                  {{-- user icon --}}
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/>
                  </svg>
                </span>
                <x-text-input
                  id="username"
                  class="block mt-1 w-full pl-10 h-11 rounded-xl border-gray-300 focus:border-choco focus:ring-choco/40"
                  type="text"
                  name="username"
                  :value="old('username')"
                  required
                  autofocus
                  autocomplete="username"
                />
              </div>
              <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div class="mt-4">
              <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                  <a href="{{ route('password.request') }}" class="text-sm text-choco hover:underline">
                    {{ __('Forgot your password?') }}
                  </a>
                @endif
              </div>

              <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                  {{-- lock icon --}}
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17 9h-1V7a4 4 0 10-8 0v2H7a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2v-8a2 2 0 00-2-2Zm-6 7.732V17a1 1 0 112 0v-.268a2 2 0 10-2 0ZM9 7a3 3 0 116 0v2H9V7Z"/>
                  </svg>
                </span>
                <x-text-input
                  id="password"
                  class="block mt-1 w-full pl-10 pr-11 h-11 rounded-xl border-gray-300 focus:border-choco focus:ring-choco/40"
                  type="password"
                  name="password"
                  required
                  autocomplete="current-password"
                />
                <button
                  type="button"
                  id="togglePassword"
                  class="absolute inset-y-0 right-2 px-2 flex items-center text-gray-500 hover:text-gray-700"
                  aria-label="Show password"
                >
                  {{-- eye icon --}}
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 110-10 5 5 0 010 10Z"/>
                  </svg>
                </button>
              </div>
              <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Remember Me --}}
            <div class="block mt-4">
              <label for="remember_me" class="inline-flex items-center select-none">
                <input id="remember_me" type="checkbox"
                       class="rounded border-gray-300 text-choco shadow-sm focus:ring-choco/40"
                       name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
              </label>
            </div>

            {{-- Submit --}}
            <div class="mt-6">
              <x-primary-button class="w-full h-11 justify-center rounded-xl bg-choco hover:bg-soft-choco focus:ring-choco/40">
                {{ __('Log in') }}
              </x-primary-button>
            </div>

            {{-- Divider & (opsional) SSO --}}
            <div class="mt-6 flex items-center gap-3">
              <div class="h-px bg-gray-200 flex-1"></div>
              {{-- <span class="text-xs text-gray-400 uppercase tracking-wider">atau</span> --}}
              <div class="h-px bg-gray-200 flex-1"></div>
            </div>

            {{-- Contoh tombol Google (aktifkan jika route tersedia) --}}
            @if (Route::has('oauth.google.redirect'))
              <a href="{{ route('oauth.google.redirect') }}"
                 class="mt-4 inline-flex w-full h-11 items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="" class="h-5 w-5">
                <span class="text-sm font-medium">Masuk dengan Google</span>
              </a>
            @endif
          </form>
        </div>

        {{-- Footer kecil --}}
        <p class="text-center text-xs text-gray-500 mt-4">
          © {{ now()->year }} {{ config('app.name', 'Cavaa') }}. All rights reserved.
        </p>
      </div>

    {{-- Show/Hide Password --}}
    <script>
      (function(){
        const btn = document.getElementById('togglePassword');
        const input = document.getElementById('password');
        if (!btn || !input) return;
        btn.addEventListener('click', () => {
          const isPwd = input.type === 'password';
          input.type = isPwd ? 'text' : 'password';
          btn.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');
        });
      })();
    </script>
  </x-guest-layout>
</x-app-layout>
