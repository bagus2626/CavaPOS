<header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-primary/10">
    <nav class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="font-poppins">
            
        </div>

        <!-- Desktop & Tablet Menu -->
        <ul class="hidden md:flex space-x-6 items-center">
            <li>
                <a href="#home" class="text-gray-700 hover:text-primary transition-colors font-medium">
                    {{ __('messages.home.home') }}
                </a>
            </li>
            <li>
                <a href="#fitur" class="text-gray-700 hover:text-primary transition-colors font-medium">
                    {{ __('messages.home.feature') }}
                </a>
            </li>
            <li>
                <a href="#kontak" class="text-gray-700 hover:text-primary transition-colors font-medium">
                    {{ __('messages.home.contact') }}
                </a>
            </li>
        </ul>

        <div class="flex items-center gap-4">
            <!-- Enhanced Language Toggle with Globe Icon -->
            <div class="relative hidden md:block">
                <button onclick="toggleLanguageDropdown()"
                    class="flex items-center gap-1 hover:border-primary transition-all duration-300 group">
                    <i class="fas fa-globe text-primary group-hover:rotate-12 transition-transform duration-300"></i>
                    <span class="font-semibold text-gray-700 text-sm" id="currentLangText">ID</span>
                    <i class="fas fa-chevron-down text-xs text-gray-500 group-hover:text-primary transition-colors duration-300"
                        id="langChevron"></i>
                </button>

                <!-- Dropdown Menu -->
                <div id="langDropdown"
                    class="hidden absolute top-full right-0 mt-2 bg-white rounded-xl shadow-lg border border-primary/10 overflow-hidden min-w-[180px] animate-fade-in-up">
                    <div class="p-1">
                        <button onclick="switchLanguage('id')"
                            class="lang-option w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-cream/30 transition-all duration-300 group">
                            <div class="w-7 h-7 rounded-full bg-primary flex items-center justify-center">
                                <span class="text-white font-bold text-xs">ID</span>
                            </div>
                            <div class="flex-1 text-left">
                                <div
                                    class="font-semibold text-gray-800 text-sm group-hover:text-primary transition-colors">
                                    Indonesia</div>
                            </div>
                            <i
                                class="fas fa-check text-primary opacity-0 lang-check-id transition-opacity duration-300 text-sm"></i>
                        </button>

                        <button onclick="switchLanguage('en')"
                            class="lang-option w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-cream/30 transition-all duration-300 group">
                            <div class="w-7 h-7 rounded-full bg-primary flex items-center justify-center">
                                <span class="text-white font-bold text-xs">EN</span>
                            </div>
                            <div class="flex-1 text-left">
                                <div
                                    class="font-semibold text-gray-800 text-sm group-hover:text-primary transition-colors">
                                    English</div>
                            </div>
                            <i
                                class="fas fa-check text-primary opacity-0 lang-check-en transition-opacity duration-300 text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search Bar - Visible on tablet and desktop -->
            <div class="relative hidden md:block">
                <input type="text" placeholder="Search....."
                    class="pl-10 pr-4 py-2 border border-primary/20 rounded-full bg-amber-50/50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary w-40 md:w-48 lg:w-64 transition-all duration-300"
                    data-lang-id-placeholder="Cari....." data-lang-en-placeholder="Search.....">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-primary"></i>
            </div>
        </div>

        <!-- Mobile Menu Button -->
        <button class="md:hidden text-primary" onclick="toggleMobileMenu()">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="mobile-menu md:hidden bg-white border-t border-primary/10 px-4 py-4">
        <ul class="space-y-3">
            <li><a href="#home" class="block text-gray-700 hover:text-primary transition-colors font-medium py-2"
                    onclick="toggleMobileMenu()" data-lang-id="Home" data-lang-en="Home">Home</a></li>
            <li><a href="#fitur" class="block text-gray-700 hover:text-primary transition-colors font-medium py-2"
                    onclick="toggleMobileMenu()" data-lang-id="Fitur" data-lang-en="Features">Fitur</a></li>
            <li><a href="#kontak" class="block text-gray-700 hover:text-primary transition-colors font-medium py-2"
                    onclick="toggleMobileMenu()" data-lang-id="Kontak" data-lang-en="Contact">Kontak</a></li>
        </ul>

        <!-- Mobile Language Toggle - Enhanced -->
        <div class="mt-4 pt-4 border-t border-primary/10">
            <div class="text-xs text-gray-500 mb-3 font-medium flex items-center gap-2">
                <i class="fas fa-globe text-primary"></i>
                <span data-lang-id="Pilih Bahasa" data-lang-en="Select Language">Pilih Bahasa</span>
            </div>
            <div class="flex gap-3">
                <button onclick="switchLanguage('id')"
                    class="lang-mobile-btn flex-1 flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-primary/20 hover:border-primary transition-all duration-300 active">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                        <span class="text-white font-bold text-xs">ID</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Indonesia</span>
                    <i class="fas fa-check text-primary text-sm lang-check-mobile-id"></i>
                </button>

                <button onclick="switchLanguage('en')"
                    class="lang-mobile-btn flex-1 flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-primary/20 hover:border-primary transition-all duration-300">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                        <span class="text-white font-bold text-xs">EN</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">English</span>
                    <i class="fas fa-check text-primary text-sm lang-check-mobile-en opacity-0"></i>
                </button>
            </div>
        </div>
    </div>
</header>
