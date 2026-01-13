<!-- Footer -->
<footer id="contact" class="w-full relative pt-20 pb-10 px-6 lg:px-8 border-t border-gray-200 dark:border-gray-800 overflow-hidden" style="background-color: #b91d1d;">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 lg:gap-8 mb-16">
            <!-- Brand Column -->
            <div class="lg:col-span-4 md:col-span-2 flex flex-col gap-6 opacity-0 translate-y-8 transition-all duration-700 delay-100" data-scroll-animate>
                <div class="flex flex-col gap-3">
                    <a href="#" class="inline-block">
                        <img src="{{ asset('images/cava-logo2.png') }}" alt="Cavaa Logo" class="h-20 w-auto">
                    </a>
                    <p class="text-white text-base leading-relaxed max-w-sm">
                        {{ __('messages.home.footer_description') }}
                    </p>
                </div>
               
                <!-- Social Media Icons -->
                <div class="flex items-center gap-3">
                    <a class="group flex items-center justify-center w-11 h-11 rounded-lg bg-white/10 border border-white/20 hover:bg-white hover:border-white transition-all duration-300" href="#">
                        <i class="fa-brands fa-instagram text-xl text-white group-hover:text-[#b91d1d] transition-colors"></i>
                    </a>
                    <a class="group flex items-center justify-center w-11 h-11 rounded-lg bg-white/10 border border-white/20 hover:bg-white hover:border-white transition-all duration-300" href="#">
                        <i class="fa-brands fa-linkedin-in text-xl text-white group-hover:text-[#b91d1d] transition-colors"></i>
                    </a>
                    <a class="group flex items-center justify-center w-11 h-11 rounded-lg bg-white/10 border border-white/20 hover:bg-white hover:border-white transition-all duration-300" href="#">
                        <i class="fa-brands fa-x-twitter text-xl text-white group-hover:text-[#b91d1d] transition-colors"></i>
                    </a>
                    <a class="group flex items-center justify-center w-11 h-11 rounded-lg bg-white/10 border border-white/20 hover:bg-white hover:border-white transition-all duration-300" href="#">
                        <i class="fa-brands fa-facebook-f text-xl text-white group-hover:text-[#b91d1d] transition-colors"></i>
                    </a>
                </div>
            </div>
           
            <!-- Perusahaan Links -->
            <div class="lg:col-span-2 md:col-span-1 opacity-0 translate-y-8 transition-all duration-700 delay-200" data-scroll-animate>
                <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-6">{{ __('messages.home.footer_company') }}</h3>
                <ul class="flex flex-col gap-4">
                    <li><a class="text-white/80 hover:text-white transition-all duration-300 hover:translate-x-1 inline-block" href="#">{{ __('messages.home.footer_about_us') }}</a></li>
                    <li><a class="text-white/80 hover:text-white transition-all duration-300 hover:translate-x-1 inline-block" href="#">{{ __('messages.home.footer_career') }}</a></li>
                    <li><a class="text-white/80 hover:text-white transition-all duration-300 hover:translate-x-1 inline-block" href="#">{{ __('messages.home.footer_contact') }}</a></li>
                </ul>
            </div>
           
            <!-- Layanan Links -->
            <div class="lg:col-span-2 md:col-span-1 opacity-0 translate-y-8 transition-all duration-700 delay-300" data-scroll-animate>
                <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-6">{{ __('messages.home.footer_services') }}</h3>
                <ul class="flex flex-col gap-4">
                    <li><a class="text-white/80 hover:text-white transition-all duration-300 hover:translate-x-1 inline-block" href="#">{{ __('messages.home.footer_support') }}</a></li>
                    <li><a class="text-white/80 hover:text-white transition-all duration-300 hover:translate-x-1 inline-block" href="#">{{ __('messages.home.footer_training') }}</a></li>
                    <li><a class="text-white/80 hover:text-white transition-all duration-300 hover:translate-x-1 inline-block" href="#">{{ __('messages.home.footer_consultation') }}</a></li>
                </ul>
            </div>
           
            <!-- Newsletter -->
            <div class="lg:col-span-4 md:col-span-2 flex flex-col gap-6 opacity-0 translate-y-8 transition-all duration-700 delay-500" data-scroll-animate>
                <div class="flex flex-col gap-2">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white">{{ __('messages.home.footer_stay_connected') }}</h3>
                    <p class="text-white/80 text-sm">{{ __('messages.home.footer_newsletter_desc') }}</p>
                </div>
               
                <form class="flex flex-col gap-3" onsubmit="event.preventDefault();">
                    <div class="relative w-full">
                        <input class="w-full h-12 pl-11 pr-4 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white transition-all placeholder:text-white/60" placeholder="{{ __('messages.home.footer_email_placeholder') }}" type="email"/>
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-white/60 select-none">mail</span>
                    </div>
                   
                    <button class="h-12 w-full sm:w-auto px-8 rounded-lg bg-white hover:bg-white/90 font-semibold shadow-lg transition-all active:scale-[0.98] flex items-center justify-center gap-2" style="color: #b91d1d;" type="submit">
                        <span>{{ __('messages.home.footer_subscribe') }}</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </button>
                </form>
            </div>
        </div>
       
        <!-- Bottom Bar -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 pt-8 border-t border-white/20 opacity-0 translate-y-8 transition-all duration-700 delay-700" data-scroll-animate>
            <p class="text-white/80 text-sm text-center md:text-left">{{ __('messages.home.footer_copyright') }}</p>
            <div class="flex flex-wrap justify-center gap-6">
                <a class="text-sm text-white/80 hover:text-white transition-colors" href="#">{{ __('messages.home.footer_privacy_policy') }}</a>
                <a class="text-sm text-white/80 hover:text-white transition-colors" href="#">{{ __('messages.home.footer_terms_conditions') }}</a>
            </div>
        </div>
    </div>
</footer>

<script>
// Scroll Animation untuk Footer
document.addEventListener('DOMContentLoaded', function() {
    const animateElements = document.querySelectorAll('[data-scroll-animate]');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('!opacity-100', '!translate-y-0');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    animateElements.forEach(element => {
        observer.observe(element);
    });
});
</script>