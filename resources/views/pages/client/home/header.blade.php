<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-gray-200/50 dark:border-white/5">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <div class="flex items-center gap-3 group cursor-pointer">
                <div class="flex items-center justify-center">
                    <img src="{{ asset('images/cava-logo2-gradient.png') }}" alt="Cavaa Logo" class="h-12 w-auto">
                </div>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-8">
                <a class="nav-link text-sm font-medium text-slate-700 dark:text-white/70 hover:text-primary dark:hover:text-primary transition-colors relative" href="#home">
                    {{ __('messages.home.home') }}
                </a>
                <a class="nav-link text-sm font-medium text-slate-700 dark:text-white/70 hover:text-primary dark:hover:text-primary transition-colors relative" href="#features">
                    {{ __('messages.home.feature') }}
                </a>
                <a class="nav-link text-sm font-medium text-slate-700 dark:text-white/70 hover:text-primary dark:hover:text-primary transition-colors relative" href="#video">
                    Video
                </a>
                <a class="nav-link text-sm font-medium text-slate-700 dark:text-white/70 hover:text-primary dark:hover:text-primary transition-colors relative" href="#why-choose">
                    {{ __('messages.home.why_section') }}
                </a>
                <a class="nav-link text-sm font-medium text-slate-700 dark:text-white/70 hover:text-primary dark:hover:text-primary transition-colors relative" href="#contact">
                    {{ __('messages.home.contact') }}
                </a>
            </div>
           
            <!-- Auth Buttons & Language Switcher -->
            <div class="hidden md:flex items-center gap-3">
                <!-- Language Switcher -->
                <div class="relative">
                    <button id="languageButton" class="flex items-center gap-2 px-4 h-11 rounded-full text-sm font-medium text-slate-700 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/10 transition-all">
                        <span class="material-symbols-outlined text-xl">language</span>
                        <span id="currentLang">{{ strtoupper(session('app_locale', 'id')) }}</span>
                        <span class="material-symbols-outlined text-sm">expand_more</span>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="languageDropdown" class="hidden absolute right-0 mt-2 w-40 bg-white dark:bg-surface-dark rounded-lg shadow-lg border border-gray-200 dark:border-white/10 overflow-hidden z-50">
                        <button onclick="switchLanguage('id')" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-slate-700 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ session('app_locale', 'id') === 'id' ? 'bg-black/5 dark:bg-white/10' : '' }}">
                            <span class="text-xl">🇮🇩</span>
                            <span>Indonesia</span>
                        </button>
                        <button onclick="switchLanguage('en')" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-slate-700 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ session('app_locale', 'id') === 'en' ? 'bg-black/5 dark:bg-white/10' : '' }}">
                            <span class="text-xl">🇬🇧</span>
                            <span>English</span>
                        </button>
                    </div>
                </div>

                <a href="{{ route('owner.login') }}" class="flex items-center justify-center px-5 h-11 rounded-full text-sm font-bold text-slate-900 dark:text-white bg-transparent hover:bg-black/5 dark:hover:bg-white/10 transition-all">
                    Masuk
                </a>
                <a href="{{ route('owner.register') }}" class="px-6 h-11 rounded-full text-sm font-bold text-white bg-primary hover:bg-red-700 shadow-glow hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center justify-center">
                    Daftar
                </a>
            </div>
           
            <!-- Mobile Menu Button -->
            <button id="mobileMenuBtn" class="md:hidden p-2 text-slate-900 dark:text-white">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>
    </div>
</nav>
<!-- Mobile Menu -->
<div id="mobileMenu"
     class="hidden md:hidden fixed top-20 left-0 right-0 bg-white dark:bg-background-dark 
            border-t border-gray-200 dark:border-white/10 z-40">

    <div class="flex flex-col px-6 py-4 gap-4">
        <a class="nav-link" href="#home">{{ __('messages.home.home') }}</a>
        <a class="nav-link" href="#features">{{ __('messages.home.feature') }}</a>
        <a class="nav-link" href="#video">Video</a>
        <a class="nav-link" href="#why-choose">{{ __('messages.home.why_section') }}</a>
        <a class="nav-link" href="#contact">{{ __('messages.home.contact') }}</a>

        <hr class="my-2 border-gray-200 dark:border-white/10">

        <a href="{{ route('owner.login') }}" class="text-center font-semibold text-slate-700 dark:text-white">
            Masuk
        </a>
        <a href="{{ route('owner.register') }}"
           class="text-center px-4 py-3 rounded-full bg-primary text-white font-bold">
            Daftar
        </a>
    </div>
</div>


<style>
    /* Smooth scroll untuk seluruh halaman */
    html {
        scroll-behavior: smooth;
    }
    
    /* Indikator aktif untuk nav link */
    .nav-link.active {
        color: var(--primary-color, #ae1504) !important;
        font-weight: 700;
    }
    
    /* Garis bawah untuk link aktif s */
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -8px;   
        left: 0;
        right: 0;
        height: 2px;
        background: var(--primary-color, #ae1504);
        border-radius: 2px;
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            transform: scaleX(0);
        }
        to {
            transform: scaleX(1);
        }
    }
</style>

<script>
    // Script untuk smooth scroll dan menandai link yang aktif
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-link');
        
        // Smooth scroll saat link diklik
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                
                if (targetSection) {
                    const navHeight = 80; // Tinggi navbar
                    const targetPosition = targetSection.offsetTop - navHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Update active state
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
        
        // Fungsi untuk update active link saat scroll
        function updateActiveLink() {
            const scrollPosition = window.scrollY + 100;
            
            navLinks.forEach(link => {
                const section = document.querySelector(link.getAttribute('href'));
                if (section) {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.offsetHeight;
                    
                    if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                        navLinks.forEach(l => l.classList.remove('active'));
                        link.classList.add('active');
                    }
                }
            });
        }
        
        // Update saat scroll dengan throttle untuk performa
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (scrollTimeout) {
                window.cancelAnimationFrame(scrollTimeout);
            }
            scrollTimeout = window.requestAnimationFrame(function() {
                updateActiveLink();
            });
        });
        
        // Set active link saat halaman dimuat
        updateActiveLink();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('mobileMenuBtn');
        const menu = document.getElementById('mobileMenu');

        if (!btn || !menu) return;

        btn.addEventListener('click', function () {
            menu.classList.toggle('hidden');
        });

        // auto close ketika klik link
        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                menu.classList.add('hidden');
            });
        });
    });
</script>
