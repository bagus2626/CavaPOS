<!-- Contact Section -->
<section id="contact" class="relative w-full py-12 sm:py-16 lg:py-20 bg-white dark:bg-[#1a1111] overflow-hidden">
    <!-- Animated Background Elements s -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-primary/5 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-purple-500/5 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- <!-- Garis Pembatas Atas -->
        <div class="relative mb-12 sm:mb-16 lg:mb-20">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t-2 border-gray-200 dark:border-gray-800"></div>
            </div>
            <div class="relative flex justify-center w-full">
                <span class="bg-white dark:bg-[#1a1111] px-6 text-sm font-semibold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">contact_support</span>
                    {{ __('messages.home.contact_badge') }}
                </span>
            </div>
        </div> --}}

        <!-- Section Header -->
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider mb-6">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                {{ strtoupper(__('messages.home.contact_badge')) }}
            </div>
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.1] text-gray-900 dark:text-white mb-6">
                {!! __('messages.home.contact_title') !!}
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed max-w-2xl mx-auto">
                {{ __('messages.home.contact_description') }}
            </p>
        </div>

        <!-- Main Contact Cards -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-16 lg:mb-20">
            <!-- WhatsApp -->
            <a href="https://wa.me/6285177152837?text=Halo%20CAVAA,%20saya%20ingin%20bertanya%20tentang%20produk%20Anda" 
               target="_blank"
               class="group relative overflow-hidden rounded-2xl p-8 bg-white dark:bg-[#1a1111] border border-gray-100 dark:border-[#3a2222] shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-16 h-16 rounded-xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fab fa-whatsapp text-green-600 dark:text-green-400 text-4xl"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold">
                            {{ __('messages.home.contact_whatsapp_badge') }}
                        </span>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-2">{{ __('messages.home.contact_whatsapp_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ __('messages.home.contact_whatsapp_desc') }}</p>
                    <div class="flex items-center gap-2 text-primary font-bold text-lg">
                        <span>{{ __('messages.home.contact_whatsapp_btn') }}</span>
                        <span class="material-symbols-outlined text-[20px] group-hover:translate-x-2 transition-transform">arrow_forward</span>
                    </div>
                </div>
            </a>

            <!-- Phone -->
            <a href="tel:+6285177152837"
               class="group relative overflow-hidden rounded-2xl p-8 bg-white dark:bg-[#1a1111] border border-gray-100 dark:border-[#3a2222] shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="relative z-10">
                    <div class="w-16 h-16 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-phone-alt text-blue-600 dark:text-blue-400 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-2">{{ __('messages.home.contact_phone_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ __('messages.home.contact_phone_desc') }}</p>
                    <div class="flex items-center gap-2 text-primary font-bold text-lg">
                        <span>{{ __('messages.home.contact_phone_btn') }}</span>
                        <span class="material-symbols-outlined text-[20px] group-hover:translate-x-2 transition-transform">arrow_forward</span>
                    </div>
                </div>
            </a>

            <!-- Email -->
            <a href="mailto:info@cavaa.id"
               class="group relative overflow-hidden rounded-2xl p-8 bg-white dark:bg-[#1a1111] border border-gray-100 dark:border-[#3a2222] shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 sm:col-span-2 lg:col-span-1">
                <div class="relative z-10">
                    <div class="w-16 h-16 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-envelope text-primary text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-2">{{ __('messages.home.contact_email_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ __('messages.home.contact_email_desc') }}</p>
                    <div class="flex items-center gap-2 text-primary font-bold text-lg">
                        <span>{{ __('messages.home.contact_email_btn') }}</span>
                        <span class="material-symbols-outlined text-[20px] group-hover:translate-x-2 transition-transform">arrow_forward</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Bottom CTA -->
        <div class="relative rounded-2xl bg-white dark:bg-[#1a1111] border border-gray-100 dark:border-[#3a2222] shadow-2xl p-10 lg:p-12">
            <div class="relative z-10 text-center max-w-4xl mx-auto">
                <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-2xl bg-primary/10 flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-headset text-primary text-4xl lg:text-5xl"></i>
                </div>
                
                <h3 class="text-3xl lg:text-4xl xl:text-5xl font-extrabold text-gray-900 dark:text-white mb-6 leading-[1.15]">
                    {!! __('messages.home.contact_cta_title') !!}
                </h3>
                
                <p class="text-base lg:text-xl text-gray-600 dark:text-gray-300 mb-10 leading-relaxed">
                    {{ __('messages.home.contact_cta_description') }}
                </p>

                <div class="flex flex-wrap justify-center gap-4 lg:gap-6 mb-10">
                    <div class="flex items-center gap-3 px-6 py-3 rounded-xl bg-gray-50 dark:bg-[#2a1a1a] border border-gray-100 dark:border-[#3a2222]">
                        <span class="material-symbols-outlined text-primary">schedule</span>
                        <span class="text-gray-900 dark:text-white font-semibold text-sm">{{ __('messages.home.contact_cta_response_time') }}</span>
                    </div>
                    <div class="flex items-center gap-3 px-6 py-3 rounded-xl bg-gray-50 dark:bg-[#2a1a1a] border border-gray-100 dark:border-[#3a2222]">
                        <span class="material-symbols-outlined text-primary">support_agent</span>
                        <span class="text-gray-900 dark:text-white font-semibold text-sm">{{ __('messages.home.contact_cta_professional_team') }}</span>
                    </div>
                    <div class="flex items-center gap-3 px-6 py-3 rounded-xl bg-gray-50 dark:bg-[#2a1a1a] border border-gray-100 dark:border-[#3a2222]">
                        <span class="material-symbols-outlined text-primary">card_giftcard</span>
                        <span class="text-gray-900 dark:text-white font-semibold text-sm">{{ __('messages.home.contact_cta_free_consultation') }}</span>
                    </div>
                </div>

                <div>
                    <a href="https://wa.me/6285177152837?text=Halo%20CAVAA,%20saya%20ingin%20konsultasi%20gratis" 
                       target="_blank"
                       class="group inline-flex items-center gap-3 px-8 lg:px-10 py-4 lg:py-5 bg-primary hover:bg-[#991b1b] text-white font-bold text-base lg:text-lg rounded-full transition-all duration-300 shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-1 hover:scale-105">
                        <i class="fab fa-whatsapp text-2xl"></i>
                        <span>{{ __('messages.home.contact_cta_btn') }}</span>
                        <span class="material-symbols-outlined text-[20px] group-hover:translate-x-2 transition-transform">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Animasi Scroll Contact Section */
#contact .opacity-0 {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}

#contact .animate-in {
    opacity: 1 !important;
    transform: translateY(0) !important;
}

/* Animasi Float untuk Background */
@keyframes float {
    0%, 100% {
        transform: translateY(0) translateX(0);
    }
    25% {
        transform: translateY(-20px) translateX(10px);
    }
    50% {
        transform: translateY(-10px) translateX(-10px);
    }
    75% {
        transform: translateY(-30px) translateX(5px);
    }
}

.animate-float {
    animation: float 20s ease-in-out infinite;
}
        </style>

    <script>
        // Animasi Scroll untuk Contact Section
document.addEventListener('DOMContentLoaded', function() {
    // Konfigurasi Intersection Observer
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Target elemen yang akan dianimasi
    const contactSection = document.querySelector('#contact');
    
    if (contactSection) {
        // Animasi untuk header
        const header = contactSection.querySelector('.text-center.mb-12');
        if (header) {
            header.classList.add('opacity-0', 'translate-y-10');
            observer.observe(header);
        }

        // Animasi untuk contact cards
        const cards = contactSection.querySelectorAll('.grid > a');
        cards.forEach((card, index) => {
            card.classList.add('opacity-0', 'translate-y-10');
            card.style.transitionDelay = `${index * 150}ms`;
            observer.observe(card);
        });

        // Animasi untuk bottom CTA
        const cta = contactSection.querySelector('.relative.rounded-2xl.bg-white');
        if (cta) {
            cta.classList.add('opacity-0', 'translate-y-10');
            cta.style.transitionDelay = '450ms';
            observer.observe(cta);
        }
    }
});
        </script>
</section>