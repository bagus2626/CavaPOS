<section id="home" class="min-h-screen pt-20 pb-16 bg-gradient-to-br from-primary via-primary-dark to-dark-brown relative overflow-hidden flex items-center">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-hero-pattern"></div>
        <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-accent/20 to-transparent transform rotate-12"></div>
        
        <div class="max-w-6xl mx-auto px-4 relative z-10 w-full">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <!-- Text Content -->
                <div class="text-white text-center lg:text-left">
                    <h1 class="font-poppins text-3xl sm:text-4xl lg:text-5xl font-bold mb-4 lg:mb-6 leading-tight">
                        {{ __('messages.home.hero_section') }}
                    </h1>
                    <p class="text-base lg:text-lg mb-6 lg:mb-8 text-white/90 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        {{ __('messages.home.hero_section_desc') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 lg:gap-4 mb-6 lg:mb-8 justify-center lg:justify-start">
                        <a href="#demo" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-accent to-accent-light text-white font-semibold rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                            <i class="fas fa-play mr-2"></i>
                            {{ __('messages.home.hero_section_button_start') }}
                        </a>
                        <a href="#kontak" class="inline-flex items-center justify-center px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-semibold rounded-full border border-white/30 hover:bg-white/30 transition-all duration-300">
                            <i class="fas fa-phone mr-2"></i>
                            {{ __('messages.home.hero_section_button_contact') }}
                        </a>
                    </div>
                    <div class="grid grid-cols-3 gap-4 sm:gap-8 max-w-md mx-auto lg:mx-0 lg:flex lg:max-w-none">
                        <div class="text-center lg:text-left">
                            <div class="text-2xl lg:text-3xl font-bold text-cream">1,500+</div>
                            <div class="text-xs lg:text-sm text-white/80">Bisnis Aktif</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div class="text-2xl lg:text-3xl font-bold text-cream">85K+</div>
                            <div class="text-xs lg:text-sm text-white/80">Transaksi Harian</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div class="text-2xl lg:text-3xl font-bold text-cream">99.8%</div>
                            <div class="text-xs lg:text-sm text-white/80">Kepuasan Pelanggan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hero Image - Hidden on mobile and tablet, visible on desktop -->
        <div class="absolute top-1/2 right-52 transform -translate-y-1/2 hidden lg:block">
            <img src="images/cava-barista.png" 
                 alt="CAVAA Dashboard Interface" 
                 class="w-[520px] h-auto object-contain">
        </div>
    </section> 