<!-- Why Choose Us Section -->
<section id="why-choose" class="w-full relative border-b border-gray-200 dark:border-gray-800">
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-20 right-0 w-[300px] sm:w-[400px] lg:w-[500px] h-[300px] sm:h-[400px] lg:h-[500px] bg-red-100/50 dark:bg-primary/5 rounded-full blur-3xl opacity-60 transition-transform duration-1000" id="blob1"></div>
        <div class="absolute bottom-40 left-0 w-[400px] sm:w-[500px] lg:w-[600px] h-[400px] sm:h-[500px] lg:h-[600px] bg-gray-200/50 dark:bg-primary/5 rounded-full blur-3xl opacity-40 transition-transform duration-1000" id="blob2"></div>
    </div>
   
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 md:px-10 py-12 sm:py-16 md:py-24">
        <!-- Section Header -->
        <div class="flex flex-col items-center text-center mb-12 sm:mb-16 md:mb-20 lg:mb-32 opacity-0 translate-y-8 transition-all duration-700" data-scroll-animate>
            <span class="text-primary font-bold text-xs sm:text-sm tracking-widest uppercase mb-2 sm:mb-3">{{ __('messages.home.why_choose_badge') }}</span>
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight px-4">
                {{ __('messages.home.why_choose_title') }} <span class="relative inline-block text-primary">{{ __('messages.home.why_choose_title_highlight') }}<span class="absolute bottom-1 left-0 w-full h-2 bg-red-200/40 -z-10 rounded-lg"></span></span>?
            </h2>
            <p class="mt-3 sm:mt-4 text-slate-600 dark:text-slate-400 max-w-2xl text-base sm:text-lg px-4">
                {{ __('messages.home.why_choose_description') }}
            </p>
        </div>
       
        <!-- Feature 1 -->
        <div class="flex flex-col lg:flex-row items-center gap-8 sm:gap-10 lg:gap-20 mb-16 sm:mb-20 md:mb-24 lg:mb-32 group">
            <div class="flex-1 flex flex-col gap-4 sm:gap-6 order-2 lg:order-1 opacity-0 -translate-x-12 transition-all duration-700 delay-100" data-scroll-animate>
                <div class="size-10 sm:size-12 rounded-xl sm:rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-1 sm:mb-2 animate-icon">
                    <span class="material-symbols-outlined text-2xl sm:text-3xl">inventory_2</span>
                </div>
                <h3 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white leading-tight">
                    {{ __('messages.home.why_feature1_title_part1') }} <br class="hidden sm:block"/> <span class="text-primary">{{ __('messages.home.why_feature1_title_part2') }}</span>
                </h3>
                <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg leading-relaxed">
                    {{ __('messages.home.why_feature1_description') }}
                </p>
            </div>
           
            <div class="flex-1 relative order-1 lg:order-2 w-full opacity-0 translate-x-12 transition-all duration-700 delay-200" data-scroll-animate>
                <div class="relative rounded-lg overflow-hidden shadow-2xl shadow-gray-200/50 dark:shadow-none bg-white dark:bg-gray-800 p-1.5 sm:p-2 transform transition-transform duration-500 group-hover:scale-[1.02] hover:rotate-1">
                    <div class="rounded-lg sm:rounded-xl overflow-hidden aspect-[4/3] relative bg-gray-100">
                        <img alt="Dashboard screen showing analytics graphs and data tables" class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110" src="{{ asset('images/orang-mencatat.jpg') }}"/>
                    </div>
                   
                    <div class="absolute -bottom-4 sm:-bottom-6 -left-4 sm:-left-6 bg-white dark:bg-surface-dark p-3 sm:p-4 rounded-lg sm:rounded-xl shadow-xl flex items-center gap-2 sm:gap-3 animate-float">
                        <div class="bg-green-100 text-green-600 p-1.5 sm:p-2 rounded-full">
                            <span class="material-symbols-outlined text-lg sm:text-xl">trending_up</span>
                        </div>
                        <div>
                            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium">{{ __('messages.home.why_feature1_stat_label') }}</p>
                            <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">{{ __('messages.home.why_feature1_stat_value') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <!-- Feature 2 -->
        <div class="flex flex-col lg:flex-row items-center gap-8 sm:gap-10 lg:gap-20 mb-16 sm:mb-20 md:mb-24 lg:mb-32 group">
            <div class="flex-1 relative w-full opacity-0 -translate-x-12 transition-all duration-700 delay-100" data-scroll-animate>
                <div class="relative rounded-lg overflow-hidden shadow-2xl shadow-gray-200/50 dark:shadow-none bg-white dark:bg-gray-800 p-1.5 sm:p-2 transform transition-transform duration-500 group-hover:scale-[1.02] hover:-rotate-1">
                    <div class="rounded-lg sm:rounded-xl overflow-hidden aspect-[4/3] relative bg-gray-100">
                        <img alt="Person using a sleek digital payment terminal" class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110" src="{{ asset('images/masin-debit.jpg') }}"/>
                    </div>
                   
                    <div class="absolute top-4 sm:top-6 -right-4 sm:-right-6 bg-white dark:bg-surface-dark p-2 pr-4 sm:p-3 sm:pr-6 rounded-l-full shadow-xl flex items-center gap-2 sm:gap-3 animate-slide-left">
                        <div class="bg-blue-100 text-blue-600 p-1.5 sm:p-2 rounded-full">
                            <span class="material-symbols-outlined text-lg sm:text-xl">bolt</span>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">{{ __('messages.home.why_feature2_badge') }}</p>
                        </div>
                    </div>
                </div>
            </div>
           
            <div class="flex-1 flex flex-col gap-4 sm:gap-6 opacity-0 translate-x-12 transition-all duration-700 delay-200" data-scroll-animate>
                <div class="size-10 sm:size-12 rounded-xl sm:rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-1 sm:mb-2 animate-icon">
                    <span class="material-symbols-outlined text-2xl sm:text-3xl">point_of_sale</span>
                </div>
                <h3 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white leading-tight">
                    {{ __('messages.home.why_feature2_title_part1') }} <br class="hidden sm:block"/> <span class="text-primary">{{ __('messages.home.why_feature2_title_part2') }}</span>
                </h3>
                <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg leading-relaxed">
                    {{ __('messages.home.why_feature2_description') }}
                </p>
            </div>
        </div>
       
        <!-- Feature 3 -->
        <div class="flex flex-col lg:flex-row items-center gap-8 sm:gap-10 lg:gap-20 group">
            <div class="flex-1 flex flex-col gap-4 sm:gap-6 order-2 lg:order-1 opacity-0 -translate-x-12 transition-all duration-700 delay-100" data-scroll-animate>
                <div class="size-10 sm:size-12 rounded-xl sm:rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-1 sm:mb-2 animate-icon">
                    <span class="material-symbols-outlined text-2xl sm:text-3xl">rocket_launch</span>
                </div>
                <h3 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white leading-tight">
                    {{ __('messages.home.why_feature3_title_part1') }} <br class="hidden sm:block"/> <span class="text-primary">{{ __('messages.home.why_feature3_title_part2') }}</span>
                </h3>
                <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg leading-relaxed">
                    {{ __('messages.home.why_feature3_description') }}
                </p>
            </div>
           
            <div class="flex-1 relative order-1 lg:order-2 w-full opacity-0 translate-x-12 transition-all duration-700 delay-200" data-scroll-animate>
                <div class="relative rounded-lg overflow-hidden shadow-2xl shadow-gray-200/50 dark:shadow-none bg-white dark:bg-gray-800 p-1.5 sm:p-2 transform transition-transform duration-500 group-hover:scale-[1.02] hover:rotate-1">
                    <div class="rounded-lg sm:rounded-xl overflow-hidden aspect-[4/3] relative bg-gray-100">
                        <img alt="Business people discussing growth strategy" class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110" src="{{ asset('images/suasana-meeting.jpg') }}"/>
                    </div>
                   
                    <div class="absolute bottom-4 sm:bottom-6 right-4 sm:right-6 bg-white dark:bg-surface-dark p-3 sm:p-4 rounded-lg sm:rounded-xl shadow-xl flex items-center gap-3 sm:gap-4 max-w-[160px] sm:max-w-[200px] animate-float-delayed">
                        <div class="flex -space-x-2 sm:-space-x-3 overflow-hidden">
                            <img alt="User Avatar 1" class="inline-block h-7 w-7 sm:h-8 sm:w-8 rounded-full ring-2 ring-white" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDVuPtSMBYT7iRqtwiZioDLcOoyTh7LhMuQOHSkozDIReihd-ay3OKXks6e3oW5Jb0N1FLH0CpgvEolf-83nOFtudSWmMOByI89iAFxG0DKdVunQM7GW3nEbl-novnANY2f9yqPBpFqikK1STj0eo7LD-MUtwJMxn-H1ya_SByGUzuuc0NMbdMmlOmaA2PL5eptJYbFLTQkVfvG8HvxHH8Jc9HvOt8OwO4F75bmKpDI0aPge-t-7d994Xvmi6pPWnGQ3H8rWoJL9g"/>
                            <img alt="User Avatar 2" class="inline-block h-7 w-7 sm:h-8 sm:w-8 rounded-full ring-2 ring-white" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBUo_NVQcbHfwYzgRWJJlKIE0pZpt5mr6EuPBpXhCrSkuNy8vs23LSYwsi6ehgroFUe05AV_4-_DUjZj2GyhvtKsDMAKD23_fF2BYXzwhq1xVG3u1bhKUXQTzVP-j3RGPrOHuydhshfOzRk9-6S4Vv11eSRG0880mP30lW7vFZ6XKIzjkOj0U0GmlfN9ADS_5DSh_yb0_H08vnIAXm1HfRxpj2Jtgs-AhSssh0vk1bfwfV_rexHUfWYa_jsfNrCcL7Atu5Klm3xtw"/>
                        </div>
                        <div>
                            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">{{ __('messages.home.why_feature3_stat_label') }}</p>
                            <p class="text-[10px] sm:text-xs font-bold text-slate-900 dark:text-white">{{ __('messages.home.why_feature3_stat_value') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}

@keyframes slide-left {
    0%, 100% { transform: translateX(0px); }
    50% { transform: translateX(-5px); }
}

@keyframes icon-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 4s ease-in-out infinite;
}

.animate-slide-left {
    animation: slide-left 2s ease-in-out infinite;
}

.animate-icon {
    animation: icon-pulse 2s ease-in-out infinite;
}

[data-scroll-animate].visible {
    opacity: 1 !important;
    transform: translateX(0) translateY(0) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll Animation Observer
    const observerOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    // Observe all elements with data-scroll-animate
    document.querySelectorAll('[data-scroll-animate]').forEach(el => {
        observer.observe(el);
    });

    // Parallax effect for background blobs
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const blob1 = document.getElementById('blob1');
        const blob2 = document.getElementById('blob2');
        
        if (blob1 && blob2) {
            blob1.style.transform = `translateY(${scrolled * 0.3}px) translateX(${scrolled * 0.1}px)`;
            blob2.style.transform = `translateY(${-scrolled * 0.2}px) translateX(${-scrolled * 0.15}px)`;
        }
    });
});
</script>