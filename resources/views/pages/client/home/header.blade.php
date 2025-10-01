<header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-primary/10">
        <nav class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="font-poppins">
                <img src="{{ asset('images/cava-logo3-gradient.png') }}" 
                    alt="CAVAA Logo" 
                    class="h-8 md:h-10 object-contain" />
            </div>

            
            <!-- Desktop & Tablet Menu -->
            <ul class="hidden md:flex space-x-6 items-center">
                <li><a href="#home" class="text-gray-700 hover:text-primary transition-colors font-medium">Home</a></li>
                <li><a href="#fitur" class="text-gray-700 hover:text-primary transition-colors font-medium">Fitur</a></li>
                <li><a href="#kontak" class="text-gray-700 hover:text-primary transition-colors font-medium">Kontak</a></li>
            </ul>
            
            <!-- Search Bar - Visible on tablet and desktop -->
            <div class="relative hidden md:block">
                <input type="text" placeholder="Search....." 
                       class="pl-10 pr-4 py-2 border border-primary/20 rounded-full bg-amber-50/50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary w-40 md:w-48 lg:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-primary"></i>
            </div>
            
            <!-- Mobile Menu Button -->
            <button class="md:hidden text-primary" onclick="toggleMobileMenu()">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </nav>
        
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="mobile-menu md:hidden bg-white border-t border-primary/10 px-4 py-4">
            <ul class="space-y-3">
                <li><a href="#home" class="block text-gray-700 hover:text-primary transition-colors font-medium py-2" onclick="toggleMobileMenu()">Home</a></li>
                <li><a href="#fitur" class="block text-gray-700 hover:text-primary transition-colors font-medium py-2" onclick="toggleMobileMenu()">Fitur</a></li>
                <li><a href="#kontak" class="block text-gray-700 hover:text-primary transition-colors font-medium py-2" onclick="toggleMobileMenu()">Kontak</a></li>
            </ul>
        </div>
    </header>