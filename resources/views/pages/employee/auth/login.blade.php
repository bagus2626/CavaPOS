@extends('layouts.employee-cashier')

@section('title', 'Employee Login')

@section('content')
{{-- Spacer untuk navbar fixed --}}
<div class="pt-20"></div>

<div class="min-h-[70vh] flex items-center justify-center px-4 py-10">
  <div class="w-full max-w-md">
    <div class="bg-white border border-choco/20 shadow-xl rounded-2xl p-6 sm:p-8">
      {{-- Header --}}
      <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full border border-choco/30 bg-soft-choco/20 mb-3">
          <svg class="w-6 h-6 text-choco" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0ZM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7Z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-semibold text-choco">Login Employee</h1>
        <p class="mt-1 text-sm text-gray-600">Masuk menggunakan akun pegawai Anda.</p>
      </div>

      {{-- Flash --}}
      @if (session('status'))
        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-3 text-sm text-blue-800">
          {{ session('status') }}
        </div>
      @endif

      {{-- Errors --}}
      @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
          <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('employee.login.submit') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
          <label for="email" class="block text-sm font-medium text-gray-800">Email</label>
          <div class="mt-1 relative">
            <input
              id="email"
              name="email"
              type="email"
              value="{{ old('email') }}"
              required
              autocomplete="email"
              placeholder="name@company.com"
              class="block w-full rounded-lg border-gray-300 shadow-sm placeholder-gray-400
                     focus:border-choco focus:ring-2 focus:ring-choco/40
                     @error('email') ring-2 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
            >
          </div>
          @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-800 mt-2">Password</label>
            <div class="mt-1 relative">
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="block w-full rounded-lg border-gray-300 shadow-sm placeholder-gray-400 pr-12
                        focus:border-choco focus:ring-2 focus:ring-choco/40
                        @error('password') ring-2 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                >

                {{-- Toggle password (ikon mata) --}}
                <button
                    type="button"
                    id="togglePw"
                    class="absolute inset-y-0 right-2 my-auto inline-flex items-center justify-center
                        w-9 h-9 rounded-md text-choco/80 hover:text-choco hover:bg-soft-choco/40 focus:outline-none
                        focus:ring-2 focus:ring-choco/40"
                    aria-label="Tampilkan password"
                    aria-pressed="false"
                >
                    {{-- Eye (password tersembunyi) --}}
                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 5 12 5c4.64 0 8.577 2.51 9.964 6.678.07.21.07.434 0 .644C20.577 16.49 16.64 19 12 19c-4.64 0-8.577-2.51-9.964-6.678z"/>
                    <circle cx="12" cy="12" r="3.5"></circle>
                    </svg>

                    {{-- Eye-off (password terlihat) --}}
                    <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.584 10.587A3.5 3.5 0 0012 15.5a3.5 3.5 0 003.5-3.5c0-.424-.078-.83-.221-1.203M9.88 4.6A9.9 9.9 0 0112 4.5c4.64 0 8.577 2.51 9.964 6.678.07.21.07.434 0 .644-.588 1.68-1.64 3.137-2.99 4.258M5.73 6.16C4.2 7.19 2.98 8.64 2.036 11.678a1.012 1.012 0 000 .644C3.423 16.49 7.36 19 12 19c1.18 0 2.31-.17 3.36-.49"/>
                    </svg>
                </button>
                </div>
          @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        {{-- Remember + (optional) Forgot --}}
        <div class="flex items-center justify-between mt-2">
          <label class="inline-flex items-center gap-2 text-sm text-gray-800">
            <input id="remember" name="remember" type="checkbox"
                   class="h-4 w-4 rounded border-gray-300 text-choco focus:ring-choco/40">
            <span>Remember me</span>
          </label>

          @if (Route::has('employee.password.request'))
            <a href="{{ route('employee.password.request') }}"
               class="text-sm text-choco hover:underline hover:decoration-choco">Forgot password?</a>
          @endif
        </div>

        {{-- Submit --}}
        <button
          type="submit"
          class="w-full inline-flex items-center justify-center rounded-lg bg-choco px-4 py-2.5 text-white font-semibold shadow-sm
                 hover:bg-soft-choco focus:outline-none focus:ring-2 focus:ring-choco/40 focus:ring-offset-2 transition mt-4">
          Login
        </button>
      </form>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const pw = document.getElementById('password');
    const btn = document.getElementById('togglePw');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    if (btn && pw) {
      btn.addEventListener('click', () => {
        // toggle type
        pw.type = (pw.type === 'password') ? 'text' : 'password';

        // toggle ikon
        const isText = pw.type === 'text';
        eyeOpen.classList.toggle('hidden', isText);
        eyeClosed.classList.toggle('hidden', !isText);

        // aksesibilitas
        btn.setAttribute('aria-pressed', String(isText));
        btn.setAttribute('aria-label', isText ? 'Sembunyikan password' : 'Tampilkan password');
      });
    }
  });
</script>
@endpush
