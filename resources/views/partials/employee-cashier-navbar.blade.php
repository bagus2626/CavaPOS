{{-- resources/views/layouts/partials/navbar.blade.php --}}
<nav class="bg-white shadow-md fixed top-0 left-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16 relative">
            {{-- Tombol Back --}}
            {{-- <a href="javascript:history.back()"
               class="absolute left-4 top-1/2 -translate-y-1/2 hover:bg-soft-choco/20 p-2 rounded-full shadow-sm border border-choco/30">
                <svg class="w-6 h-6 text-choco" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/>
                </svg>
            </a> --}}

            {{-- Brand --}}
            <div class="flex-1 flex justify-center">
                <a href="{{ route('employee.cashier.dashboard') }}">
                        <!-- Logo untuk Light Mode -->
                        <img src="{{ asset('images/cava-logo2-choco.png') }}"
                            class="block h-14 w-auto"
                            alt="Logo Light Mode">
                </a>
            </div>

            {{-- Menu Desktop --}}
            <div class="hidden md:flex items-center space-x-6">
                <a href="#" class="text-gray-700 hover:text-choco">Home</a>
                <a href="#" class="text-gray-700 hover:text-choco">Menu</a>
                <a href="#" class="text-gray-700 hover:text-choco">Contact</a>

                @auth('employee')
                    @php
                        $emp = auth('employee')->user();
                        $displayName = $emp?->name ?? $emp?->user_name ?? 'Pegawai';
                        $initials = collect(explode(' ', trim($displayName)))
                            ->map(fn($p)=>mb_strtoupper(mb_substr($p,0,1)))
                            ->take(2)->implode('');
                    @endphp

                    {{-- Nama Pegawai --}}
                    <div class="flex items-center gap-3">
                        @if(!empty($emp->image))
                            <img src="{{ asset('storage/'.$emp->image) }}" class="h-8 w-8 rounded-full object-cover" alt="Avatar">
                        @else
                            <div class="h-8 w-8 rounded-full bg-soft-choco/20 flex items-center justify-center text-choco font-semibold">
                                {{ $initials }}
                            </div>
                        @endif
                        <span class="text-sm font-semibold text-choco">Hi, {{ $displayName }}</span>
                    </div>

                    {{-- Notifikasi (Mobile) --}}
                    <div class="relative">
                        <button id="notif-btn-desktop"
                                class="relative rounded-full p-2 hover:bg-soft-choco/20 border border-choco/30 focus:ring-2 focus:ring-soft-choco/30"
                                aria-haspopup="true" aria-expanded="false">
                            {{-- Icon bell --}}
                            <svg class="w-6 h-6 text-choco" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            {{-- Badge --}}
                            <span id="notif-badge-desktop"
                                class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[10px] font-bold flex items-center justify-center">
                                0
                            </span>
                        </button>

                        {{-- Dropdown panel --}}
                        <div id="notif-panel-desktop"
                            class="hidden absolute right-0 mt-2 w-80 max-h-96 overflow-y-auto rounded-xl border border-choco/20 bg-white shadow-lg">
                            <div class="px-4 py-3 border-b border-choco/10 flex items-center justify-between">
                                <p class="font-semibold text-choco">Notifikasi</p>
                                <button id="notif-clear-desktop" class="text-xs text-rose-600 hover:underline">Bersihkan</button>
                            </div>
                            <ul id="notif-list-desktop" class="divide-y divide-choco/10">
                                {{-- Item notifikasi akan disisipkan via JS --}}
                                <li class="p-3 text-xs text-gray-500">Belum ada notifikasi.</li>
                            </ul>
                            <div class="px-4 py-2 border-t border-choco/10 text-right">
                                <a href="{{ route('employee.cashier.dashboard') }}"
                                class="text-xs text-choco hover:underline">Buka Dashboard</a>
                            </div>
                        </div>
                    </div>


                    {{-- Logout --}}
                    <form class="my-auto" method="POST"  action="{{ route('employee.logout') }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 text-choco hover:bg-red-100 rounded-lg border border-choco/20">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>

            {{-- Notifikasi (Mobile) --}}
            <div class="md:hidden absolute right-14">
                <button id="notif-btn-mobile"
                        class="relative rounded-full p-2 hover:bg-soft-choco/20 border border-choco/30 focus:ring-2 focus:ring-soft-choco/30"
                        aria-haspopup="true" aria-expanded="false">
                    {{-- Icon bell --}}
                    <svg class="w-6 h-6 text-choco" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    {{-- Badge --}}
                    <span id="notif-badge-mobile"
                        class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[10px] font-bold flex items-center justify-center">
                        0
                    </span>
                </button>

                {{-- Dropdown panel --}}
                <div id="notif-panel-mobile"
                    class="hidden absolute right-0 mt-2 w-80 max-h-96 overflow-y-auto rounded-xl border border-choco/20 bg-white shadow-lg">
                    <div class="px-4 py-3 border-b border-choco/10 flex items-center justify-between">
                        <p class="font-semibold text-choco">Notifikasi</p>
                        <button id="notif-clear-mobile" class="text-xs text-rose-600 hover:underline">Bersihkan</button>
                    </div>
                    <ul id="notif-list-mobile" class="divide-y divide-choco/10">
                        {{-- Item notifikasi akan disisipkan via JS --}}
                        <li class="p-3 text-xs text-gray-500">Belum ada notifikasi.</li>
                    </ul>
                    <div class="px-4 py-2 border-t border-choco/10 text-right">
                        <a href="{{ route('employee.cashier.dashboard') }}"
                        class="text-xs text-choco hover:underline">Buka Dashboard</a>
                    </div>
                </div>
            </div>


            {{-- Mobile toggle --}}
            <div class="flex items-center md:hidden absolute right-4">
                <button id="mobile-menu-button" class="text-gray-700 focus:outline-none">
                    <svg class="w-6 h-6 text-choco" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="hidden md:hidden border-t bg-white">
        @auth('employee')
            @php
                $emp = auth('employee')->user();
                $displayName = $emp?->name ?? $emp?->user_name ?? 'Pegawai';
            @endphp
            <div class="px-4 py-3 bg-soft-choco/10">
                <p class="text-sm text-gray-600">Masuk sebagai</p>
                <p class="font-semibold text-choco">{{ $displayName }}</p>
            </div>
        @endauth

        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-soft-choco/10">Home</a>
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-soft-choco/10">Menu</a>
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-soft-choco/10">Contact</a>

        @auth('employee')
            <form method="POST" action="{{ route('employee.logout') }}" class="border-t">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-100">
                    Logout
                </button>
            </form>
        @endauth
    </div>
</nav>
<button id="enable-sound"
        class="hidden fixed bottom-4 right-4 z-50 px-3 py-2 rounded-lg bg-amber-500 text-white shadow-lg">
  Enable sound
</button>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('mobile-menu-button');
        const menu = document.getElementById('mobile-menu');
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    });
</script>
{{-- <script>
document.addEventListener('DOMContentLoaded', function () {
  const enableBtn = document.getElementById('enable-sound');
  if (!enableBtn) return;

  const testAudio = new Audio('/sounds/bell-notification-337658.mp3');

  // Coba autoplay saat load. Jika ditolak, tampilkan tombol.
  testAudio.play()
    .then(() => {
      // Autoplay diizinkan → tombol tetap hidden (memang sengaja)
    })
    .catch(() => {
      // Autoplay ditolak → munculkan tombol agar user memberi izin
      enableBtn.classList.remove('hidden');
    });

  // Klik tombol = minta izin sekali, lalu sembunyikan
  enableBtn.addEventListener('click', () => {
    (new Audio('/sounds/bell-notification-337658.mp3')).play()
      .then(() => enableBtn.classList.add('hidden'))
      .catch(() => {/* tetap tampil kalau masih gagal */});
  });
});
</script> --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  // util
  function setupNotif(btnId, panelId, badgeId, listId, clearId) {
    const btn   = document.getElementById(btnId);
    const panel = document.getElementById(panelId);
    const badge = document.getElementById(badgeId);
    const list  = document.getElementById(listId);
    const clear = document.getElementById(clearId);

    if (!btn || !panel) return;

    // toggle panel
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      panel.classList.toggle('hidden');
      btn.setAttribute('aria-expanded', panel.classList.contains('hidden') ? 'false' : 'true');
    });

    // click di luar => tutup
    document.addEventListener('click', (e) => {
      if (!panel.contains(e.target) && !btn.contains(e.target)) {
        panel.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
      }
    });

    // tombol clear (opsional)
    clear?.addEventListener('click', () => {
      if (list) {
        list.innerHTML = `<li class="p-3 text-xs text-gray-500">Belum ada notifikasi.</li>`;
      }
      if (badge) {
        badge.classList.add('hidden');
        badge.textContent = '0';
      }
    });
  }

  // inisialisasi desktop & mobile (pakai ID baru yang unik)
  setupNotif('notif-btn-desktop','notif-panel-desktop','notif-badge-desktop','notif-list-desktop','notif-clear-desktop');
  setupNotif('notif-btn-mobile','notif-panel-mobile','notif-badge-mobile','notif-list-mobile','notif-clear-mobile');
});
</script>
