<section class="py-16 lg:py-20 bg-white relative overflow-hidden">        
        <div class="max-w-6xl mx-auto px-4 text-center relative z-10">
            <div class="bg-gradient-to-br from-primary via-primary-dark to-dark-brown rounded-2xl p-8 md:p-12 lg:p-16 relative overflow-hidden shadow-2xl">
                <!-- Background Pattern -->
                <div class="absolute inset-0 bg-hero-pattern"></div>
                <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-accent/20 to-transparent transform rotate-12"></div>
                
                <div class="relative z-10">
                    <h2 class="font-poppins text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-4 lg:mb-6 leading-tight">
                        {{ __('messages.home.cta_section') }}
                    </h2>
                    <p class="text-base lg:text-lg text-white/90 mb-6 lg:mb-8 max-w-3xl mx-auto leading-relaxed">
                        {{ __('messages.home.cta_section_desc') }}
                    </p>
                    <a href="{{ route('owner.login') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-6 lg:px-8 py-3 lg:py-4 bg-gradient-to-r from-accent to-accent-light text-white font-semibold rounded-full shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                        <i class="fas fa-rocket mr-2"></i>
                        {{ __('messages.home.cta_section_button') }}
                    </a>
                </div>
            </div>
        </div>
    </section>