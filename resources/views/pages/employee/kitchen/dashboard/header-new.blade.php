<header class="flex items-center justify-between p-4 bg-card-light dark:bg-card-dark border-b border-border-light dark:border-border-dark">
    <div class="flex items-center space-x-2 md:space-x-4">
        <img alt="CAVAA Logo" class="h-8 md:h-12" src="{{ asset('images/cava-logo3-gradient.png') }}"/>
    </div>
    <div class="flex items-center space-x-2 md:space-x-4">
    <span class="hidden md:inline text-text-secondary-light dark:text-text-secondary-dark text-sm typography-enhanced" id="headerTime">Loading...</span>
    <div class="flex items-center space-x-1 md:space-x-2">
        <span class="material-icons text-text-secondary-light dark:text-text-secondary-dark text-base md:text-xl">restaurant</span>
        <div>
            <p class="font-semibold text-text-light dark:text-text-dark typography-heading text-sm md:text-base">{{ Auth::guard('employee')->user()->name ?? 'Kitchen Staff' }}</p>
            <p class="hidden md:block text-xs text-text-secondary-light dark:text-text-secondary-dark typography-enhanced">Kitchen Team</p>
            </div>
        </div>
        
        <!-- Settings Dropdown dengan Logout -->
        <div class="relative" x-data="{ open: false }">
            <button 
                class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-text-secondary-light dark:text-text-secondary-dark transition-colors"
                @click="open = !open"
            >
                <span class="material-icons">settings</span>
            </button>
            
            <!-- Dropdown Menu -->
            <div 
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50 card-elegant"
                x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                @click.outside="open = false"
                style="display: none;"
            >
                <div class="py-1">
                    
                    <!-- Divider -->
                    <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                    
                    <!-- Logout Option -->
                    <form action="{{ route('employee.logout') }}" method="POST">
                        @csrf
                        <button 
                            type="submit" 
                            class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors typography-enhanced"
                        >
                            <span class="material-icons text-sm mr-2">logout</span>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function updateHeaderTime() {
        const timeElement = document.getElementById('headerTime');
        if (!timeElement) return;
        
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
        timeElement.textContent = timeString;
    }
    
    // Only run if element exists
    if (document.getElementById('headerTime')) {
        updateHeaderTime();
        setInterval(updateHeaderTime, 60000);
    }
</script>

<!-- AlpineJS untuk dropdown functionality -->
<script src="//unpkg.com/alpinejs" defer></script>