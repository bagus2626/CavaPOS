{{-- resources/views/layouts/partials/navbar.blade.php --}}
<nav class="bg-white shadow-md fixed top-0 left-0 w-full z-50">
    <div class="max-w-12xl mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            {{-- Logo (Kiri) --}}
            <div class="flex-shrink-0">
                <a href="{{ route('employee.cashier.dashboard') }}">
                    <img src="{{ asset('images/cava-logo2-gradient.png') }}"
                        class="h-14 w-auto"
                        alt="Cavaa Logo">
                </a>
            </div>


            {{-- Menu Desktop (Kanan) --}}
            <div class="hidden md:flex items-center space-x-4">
                @auth('employee')
                    @php
                        $emp = auth('employee')->user();
                        $displayName = $emp?->name ?? $emp?->user_name ?? 'Pegawai';
                        $initials = collect(explode(' ', trim($displayName)))
                            ->map(fn($p)=>mb_strtoupper(mb_substr($p,0,1)))
                            ->take(2)->implode('');
                    @endphp


                    {{-- Notifikasi Desktop --}}
                    <div class="relative">
                        <button id="notif-btn-desktop"
                                class="relative rounded-full p-2 hover:bg-gray-100 border border-gray-300"
                                type="button">
                            <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span id="notif-badge-desktop"
                                class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[10px] font-bold flex items-center justify-center">
                                0
                            </span>
                        </button>


                        <div id="notif-panel-desktop"
                            class="hidden absolute right-0 mt-2 w-80 max-h-96 overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-xl z-50">
                            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                                <p class="font-semibold text-gray-800">Notifikasi</p>
                                <button id="notif-clear-desktop" class="text-xs text-red-600 hover:underline" type="button">Bersihkan</button>
                            </div>
                            <ul id="notif-list-desktop" class="divide-y divide-gray-100">
                                <li class="p-3 text-xs text-gray-500">Belum ada notifikasi.</li>
                            </ul>
                            <div class="px-4 py-2 border-t border-gray-200 text-right">
                                <a href="{{ route('employee.cashier.dashboard') }}"
                                class="text-xs text-blue-600 hover:underline">Buka Dashboard</a>
                            </div>
                        </div>
                    </div>


                    {{-- Profile Dropdown --}}
                    <div class="relative">
                        <button id="profile-btn-desktop"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg  "
                                type="button">
                            @if(!empty($emp->image))
                                <img src="{{ asset('storage/'.$emp->image) }}" class="h-8 w-8 rounded-full object-cover" alt="Avatar">
                            @else
                                <div class="h-8 w-8 rounded-full bg-soft-choco/20 flex items-center justify-center text-choco font-semibold text-xs">
                                    {{ $initials }}
                                </div>
                            @endif
                            <span class="text-sm font-semibold text-choco">Hi, {{ $displayName }}</span>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>


                        <div id="profile-panel-desktop"
                            class="hidden absolute right-0 mt-2 w-56 rounded-xl border border-gray-200 bg-white shadow-xl z-50">
                            {{-- User Info --}}
                            <div class="px-4 py-3 border-b border-gray-200">
                                <p class="text-xs text-gray-600">Masuk sebagai</p>
                                <p class="font-semibold text-gray-800">{{ $displayName }}</p>
                            </div>
                           
                            {{-- Menu Items --}}
                            <div class="py-2">
                                <a href="{{ route('employee.cashier.dashboard') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors {{ request()->routeIs('employee.cashier.dashboard') ? 'bg-red-50 text-red-600' : '' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <span class="text-sm font-medium">Dashboard</span>
                                </a>
                               
                                <a href="{{ route('employee.cashier.activity') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors {{ request()->routeIs('employee.cashier.activity') ? 'bg-red-50 text-red-600' : '' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                    <span class="text-sm font-medium">Activity</span>
                                </a>
                            </div>
                           
                            {{-- Logout --}}
                            <div class="border-t border-gray-200">
                                <form method="POST" action="{{ route('employee.logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 transition-colors rounded-b-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span class="text-sm font-medium">Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>


            {{-- Mobile Menu (Kanan) --}}
            <div class="md:hidden flex items-center gap-3">
                {{-- Notifikasi Mobile --}}
                <div class="relative">
                    <button id="notif-btn-mobile"
                            class="relative rounded-full p-2 hover:bg-gray-100 border border-gray-300"
                            type="button">
                        <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="notif-badge-mobile"
                            class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[10px] font-bold flex items-center justify-center">
                            0
                        </span>
                    </button>


                    <div id="notif-panel-mobile"
                        class="hidden absolute right-0 mt-2 w-80 max-h-96 overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-xl z-50">
                        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                            <p class="font-semibold text-gray-800">Notifikasi</p>
                            <button id="notif-clear-mobile" class="text-xs text-red-600 hover:underline" type="button">Bersihkan</button>
                        </div>
                        <ul id="notif-list-mobile" class="divide-y divide-gray-100">
                            <li class="p-3 text-xs text-gray-500">Belum ada notifikasi.</li>
                        </ul>
                        <div class="px-4 py-2 border-t border-gray-200 text-right">
                            <a href="{{ route('employee.cashier.dashboard') }}"
                            class="text-xs text-blue-600 hover:underline">Buka Dashboard</a>
                        </div>
                    </div>
                </div>


                {{-- Hamburger Menu --}}
                <button id="mobile-menu-button" class="p-2" type="button">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>


    {{-- Mobile Menu Dropdown --}}
    <div id="mobile-menu" class="hidden md:hidden border-t bg-white">
        @auth('employee')
            @php
                $emp = auth('employee')->user();
                $displayName = $emp?->name ?? $emp?->user_name ?? 'Pegawai';
            @endphp
            <div class="px-4 py-3 bg-gray-50">
                <p class="text-xs text-gray-600">Masuk sebagai</p>
                <p class="font-semibold text-gray-800">{{ $displayName }}</p>
            </div>


            <a href="{{ route('employee.cashier.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('employee.cashier.dashboard') ? 'bg-red-50 text-red-600' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('employee.cashier.activity') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('employee.cashier.activity') ? 'bg-red-50 text-red-600' : '' }}">
                Activity
            </a>


            <form method="POST" action="{{ route('employee.logout') }}" class="border-t">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
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
    // Mobile menu toggle
    const btn = document.getElementById('mobile-menu-button');
    const menu = document.getElementById('mobile-menu');
    if (btn && menu) {
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    }


    // Notification setup function
    function setupNotif(btnId, panelId, badgeId, listId, clearId) {
        const btn = document.getElementById(btnId);
        const panel = document.getElementById(panelId);
        const badge = document.getElementById(badgeId);
        const list = document.getElementById(listId);
        const clear = document.getElementById(clearId);


        if (!btn || !panel) return;


        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            panel.classList.toggle('hidden');
           
            // Close profile dropdown if open
            const profilePanel = document.getElementById('profile-panel-desktop');
            if (profilePanel && !profilePanel.classList.contains('hidden')) {
                profilePanel.classList.add('hidden');
            }
        });


        document.addEventListener('click', (e) => {
            if (!panel.contains(e.target) && !btn.contains(e.target)) {
                panel.classList.add('hidden');
            }
        });


        if (clear) {
            clear.addEventListener('click', () => {
                if (list) {
                    list.innerHTML = `<li class="p-3 text-xs text-gray-500">Belum ada notifikasi.</li>`;
                }
                if (badge) {
                    badge.classList.add('hidden');
                    badge.textContent = '0';
                }
            });
        }
    }


    // Profile dropdown setup
    function setupProfileDropdown() {
        const btn = document.getElementById('profile-btn-desktop');
        const panel = document.getElementById('profile-panel-desktop');


        if (!btn || !panel) return;


        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            panel.classList.toggle('hidden');
           
            // Close notification dropdown if open
            const notifPanel = document.getElementById('notif-panel-desktop');
            if (notifPanel && !notifPanel.classList.contains('hidden')) {
                notifPanel.classList.add('hidden');
            }
        });


        document.addEventListener('click', (e) => {
            if (!panel.contains(e.target) && !btn.contains(e.target)) {
                panel.classList.add('hidden');
            }
        });
    }


    // Initialize notifications
    setupNotif('notif-btn-desktop', 'notif-panel-desktop', 'notif-badge-desktop', 'notif-list-desktop', 'notif-clear-desktop');
    setupNotif('notif-btn-mobile', 'notif-panel-mobile', 'notif-badge-mobile', 'notif-list-mobile', 'notif-clear-mobile');
   
    // Initialize profile dropdown
    setupProfileDropdown();
});
</script>