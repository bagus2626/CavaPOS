<!-- Hero Section -->
<style>
    /* Fade In Animations s */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.8s ease-out forwards;
    }

    .animate-fadeInLeft {
        animation: fadeInLeft 0.8s ease-out forwards;
    }

    .animate-fadeInRight {
        animation: fadeInRight 0.8s ease-out forwards;
    }

    .animation-delay-200 {
        animation-delay: 0.2s;
        opacity: 0;
    }

    .animation-delay-400 {
        animation-delay: 0.4s;
        opacity: 0;
    }

    .animation-delay-600 {
        animation-delay: 0.6s;
        opacity: 0;
    }

    .animation-delay-800 {
        animation-delay: 0.8s;
        opacity: 0;
    }

    /* Floating Animation */
    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    .animate-float {
        animation: float 6s ease-in-out infinite;
    }

    /* Pulse Scale Animation */
    @keyframes pulseScale {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    .animate-pulseScale {
        animation: pulseScale 2s ease-in-out infinite;
    }

    /* Gradient Animation */
    @keyframes gradientShift {

        0%,
        100% {
            opacity: 0.6;
            transform: translate(0, 0) scale(1);
        }

        33% {
            opacity: 0.4;
            transform: translate(20px, -30px) scale(1.1);
        }

        66% {
            opacity: 0.5;
            transform: translate(-15px, 20px) scale(0.95);
        }
    }

    .animate-gradientShift {
        animation: gradientShift 15s ease-in-out infinite;
    }

    /* Shimmer Effect */
    @keyframes shimmer {
        0% {
            background-position: -200% center;
        }

        100% {
            background-position: 200% center;
        }
    }

    .btn-shimmer {
        background: #ae1504;
        background-size: 200% 100%;
    }
</style>

<section id="home"
    class="relative pt-20 pb-12 sm:pt-24 sm:pb-16 lg:pt-32 lg:pb-24 xl:pt-40 xl:pb-32 overflow-hidden border-b border-gray-200 dark:border-gray-800 bg-white">
    <!-- Abstract Background Shapes -->
    <div
        class="absolute top-0 right-0 -z-10 w-[400px] sm:w-[600px] lg:w-[800px] h-[400px] sm:h-[600px] lg:h-[800px] bg-red-100/50 dark:bg-red-900/10 rounded-full blur-3xl opacity-60 translate-x-1/3 -translate-y-1/4 animate-gradientShift">
    </div>
    <div
        class="absolute bottom-0 left-0 -z-10 w-[300px] sm:w-[450px] lg:w-[600px] h-[300px] sm:h-[450px] lg:h-[600px] bg-gray-200/50 dark:bg-gray-800/20 rounded-full blur-3xl opacity-40 -translate-x-1/3 translate-y-1/4 animate-gradientShift animation-delay-4000">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 xl:gap-16 items-center">
            <!-- Text Content -->
            <div class="flex flex-col gap-6 sm:gap-8 max-w-2xl mx-auto lg:mx-0 text-center lg:text-left z-10">
                <div class="space-y-4 sm:space-y-6">
                    <div
                        class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full bg-red-50 dark:bg-red-900/20 text-primary text-xs font-bold uppercase tracking-wider w-fit mx-auto lg:mx-0 animate-fadeInUp">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                        </span>
                        {{ __('messages.home.hero_system_number_one') }}
                    </div>

                    <h1
                        class="text-3xl sm:text-4xl md:text-5xl lg:text-5xl xl:text-6xl font-black leading-[1.15] tracking-tight text-slate-900 dark:text-white animate-fadeInUp animation-delay-200">
                        {{ __('messages.home.hero_title_modern') }} <span
                            class="text-primary relative inline-block">{{ __('messages.home.hero_title_solution') }}
                            <svg class="absolute w-full h-2 sm:h-3 -bottom-1 left-0 text-primary/20 fill-current -z-10"
                                preserveAspectRatio="none" viewBox="0 0 100 20">
                                <path d="M0 15 Q 50 25 100 15 L 100 20 L 0 20 Z"></path>
                            </svg>
                        </span> <br class="hidden sm:block" />{{ __('messages.home.hero_title_for_business') }}
                    </h1>

                    <p
                        class="text-base sm:text-lg text-slate-600 dark:text-slate-400 font-normal leading-relaxed max-w-lg mx-auto lg:mx-0 animate-fadeInUp animation-delay-400">
                        {{ __('messages.home.hero_description') }}
                    </p>
                </div>

                <div
                    class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 justify-center lg:justify-start animate-fadeInUp animation-delay-600">
                    <a href="#features"
                        class="w-full sm:w-auto px-6 sm:px-8 h-12 sm:h-14 rounded-full btn-shimmer text-white text-sm sm:text-base font-bold tracking-wide hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2 group">
                        {{ __('messages.home.hero_btn_start') }}
                        <span
                            class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </a>

                    <a href="#contact"
                        class="w-full sm:w-auto px-6 sm:px-8 h-12 sm:h-14 rounded-full border border-gray-300 dark:border-white/20 text-slate-900 dark:text-white text-sm sm:text-base font-bold tracking-wide hover:bg-gray-50 dark:hover:bg-white/5 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-primary">call</span>
                        {{ __('messages.home.hero_btn_contact') }}
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div
                    class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 sm:gap-6 pt-2 sm:pt-4 text-xs sm:text-sm text-slate-600 dark:text-slate-400 font-medium animate-fadeInUp animation-delay-800">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg sm:text-xl">check_circle</span>
                        <span>{{ __('messages.home.hero_trust_no_credit_card') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg sm:text-xl">check_circle</span>
                        <span>{{ __('messages.home.hero_trust_free_trial') }}</span>
                    </div>
                </div>
            </div>

            <!-- Visual Content -->
            <div
                class="relative w-full h-full min-h-[400px] sm:min-h-[500px] lg:min-h-[550px] flex items-center justify-center lg:justify-end mt-8 lg:mt-0">
                <!-- Main Hero Image Wrapper - Laptop Mockup -->
                <div
                    class="relative w-full max-w-[500px] sm:max-w-[650px] lg:max-w-[750px] animate-fadeInRight animate-float">
                    <!-- Laptop Frame -->
                    <div
                        class="relative bg-gray-800 rounded-t-xl sm:rounded-t-2xl p-2 sm:p-3 shadow-2xl shadow-red-900/10 dark:shadow-black/50 transition-transform duration-500 hover:scale-[1.01]">
                        <!-- Screen Bezel -->
                        <div class="bg-black rounded-lg p-1 sm:p-1.5">
                            <!-- Screen Content -->
                            <div class="relative w-full aspect-[16/10] rounded-md overflow-hidden bg-white">
                                <img alt="Dashboard POS System"
                                    class="w-full h-full object-contain object-center bg-white"
                                    src="{{ asset('images/dashboard-owner.png') }}" />

                                <!-- Embedded Label -->
                                <div class="absolute bottom-3 sm:bottom-4 left-3 sm:left-4 z-20">
                                    <div
                                        class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full bg-white/90 backdrop-blur-sm border border-gray-200 shadow-lg text-xs sm:text-sm font-semibold animate-pulseScale">
                                        <span class="size-2 rounded-full bg-green-400 animate-pulse"></span>
                                        <span class="text-gray-800">{{ __('messages.home.hero_status_online') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Webcam Notch -->
                        <div
                            class="absolute top-2 left-1/2 -translate-x-1/2 w-16 sm:w-20 h-1 sm:h-1.5 bg-gray-900 rounded-full">
                        </div>
                    </div>

                    <!-- Laptop Base -->
                    <div
                        class="relative h-3 sm:h-4 bg-gradient-to-b from-gray-700 to-gray-800 rounded-b-xl sm:rounded-b-2xl">
                        <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-t from-gray-900/50 to-transparent">
                        </div>
                    </div>

                    <!-- Laptop Shadow -->
                    <div
                        class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-[90%] h-4 bg-black/10 blur-xl rounded-full">
                    </div>
                </div>

                <!-- Floating Stats Cards -->
                <div
                    class="hidden sm:block absolute top-[10%] -left-4 lg:-left-12 z-30 transform hover:-translate-y-2 transition-transform duration-300 animate-fadeInLeft animation-delay-600">
                    <div
                        class="glass-card p-3 sm:p-4 lg:p-5 rounded-xl sm:rounded-2xl shadow-soft flex items-center gap-3 sm:gap-4 min-w-[160px] sm:min-w-[200px]">
                        <div
                            class="size-10 sm:size-12 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-xl sm:text-2xl">storefront</span>
                        </div>
                        <div>
                            <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-none">10k+
                            </p>
                            <p class="text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-400 mt-1">
                                {{ __('messages.home.hero_stats_active_business') }}</p>
                        </div>
                    </div>
                </div>

                <div
                    class="hidden sm:block absolute bottom-[15%] -right-4 lg:-right-8 z-30 transform hover:-translate-y-2 transition-transform duration-300 delay-100 animate-fadeInRight animation-delay-800">
                    <div
                        class="glass-card p-3 sm:p-4 lg:p-5 rounded-xl sm:rounded-2xl shadow-soft flex items-center gap-3 sm:gap-4 min-w-[180px] sm:min-w-[220px]">
                        <div
                            class="size-10 sm:size-12 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                            <span class="material-symbols-outlined text-xl sm:text-2xl">payments</span>
                        </div>
                        <div>
                            <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-none">50k+
                            </p>
                            <p class="text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-400 mt-1">
                                {{ __('messages.home.hero_stats_daily_transactions') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Smooth scroll untuk semua anchor links
                    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                        anchor.addEventListener('click', function(e) {
                            e.preventDefault();
                            const target = document.querySelector(this.getAttribute('href'));
                            if (target) {
                                target.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }
                        });
                    });
                });
            </script>
</section>
