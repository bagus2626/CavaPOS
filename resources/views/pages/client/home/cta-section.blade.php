<!-- CTA Section with Scroll Animations -->
<section class="relative w-full py-12 sm:py-16 lg:py-20 bg-white overflow-hidden" id="cta-section">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-primary/5 rounded-full blur-3xl scroll-animate"
            data-scroll="float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl scroll-animate"
            data-scroll="float-delayed"></div>
        <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-purple-500/5 rounded-full blur-3xl scroll-animate"
            data-scroll="pulse"></div>
    </div>

    <!-- Main CTA Card -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-[#1a1111] rounded-lg shadow-2xl overflow-hidden border border-gray-100 dark:border-[#3a2222] hover:shadow-3xl transition-shadow duration-500 scroll-animate"
            data-scroll="scale-in">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0 min-h-[600px]">
                <!-- Content Side -->
                <div class="lg:col-span-7 flex flex-col justify-center p-8 md:p-12 lg:p-20 order-2 lg:order-1">
                    <div class="flex flex-col items-start gap-6 max-w-2xl mx-auto lg:mx-0">
                        <!-- Badge with animation -->
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider scroll-animate"
                            data-scroll="slide-left" data-delay="100">
                            <span class="material-symbols-outlined text-sm">bolt</span>
                            <span>{{ __('messages.home.cta_badge') }}</span>
                        </div>

                        <!-- Heading with stagger animation -->
                        <h2
                            class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.1] text-gray-900 dark:text-white">
                            <span class="inline-block scroll-animate" data-scroll="fade-up"
                                data-delay="200">{{ __('messages.home.cta_title_part1') }}</span>
                            <span class="inline-block text-primary scroll-animate" data-scroll="fade-up"
                                data-delay="300">{{ __('messages.home.cta_title_part2') }}</span>
                        </h2>

                        <!-- Description -->
                        <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed scroll-animate"
                            data-scroll="fade-up" data-delay="400">
                            {{ __('messages.home.cta_description_part1') }} <span
                                class="font-bold text-gray-900 dark:text-white">{{ __('messages.home.cta_description_teams') }}</span>
                            {{ __('messages.home.cta_description_part2') }}
                        </p>

                        <!-- Feature Checks with stagger -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full py-2">
                            <div class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 scroll-animate hover:translate-x-2 transition-transform duration-300"
                                data-scroll="slide-left" data-delay="500">
                                <span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                                {{ __('messages.home.cta_check1') }}
                            </div>
                            <div class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 scroll-animate hover:translate-x-2 transition-transform duration-300"
                                data-scroll="slide-left" data-delay="600">
                                <span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                                {{ __('messages.home.cta_check2') }}
                            </div>
                            <div class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 scroll-animate hover:translate-x-2 transition-transform duration-300"
                                data-scroll="slide-left" data-delay="700">
                                <span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                                {{ __('messages.home.cta_check3') }}
                            </div>
                            <div class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 scroll-animate hover:translate-x-2 transition-transform duration-300"
                                data-scroll="slide-left" data-delay="800">
                                <span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                                {{ __('messages.home.cta_check4') }}
                            </div>
                        </div>

                        <!-- Buttons with hover effects -->
                        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto mt-4 scroll-animate"
                            data-scroll="zoom-in" data-delay="900">
                            <a href="{{ route('owner.login') }}"
                                class="group relative flex items-center justify-center gap-2 h-14 px-8 rounded-full bg-primary hover:bg-[#991b1b] text-white text-base font-bold transition-all duration-300 shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-1 hover:scale-105 overflow-hidden">
                                <span
                                    class="absolute inset-0 w-0 bg-gradient-to-r from-transparent via-white/20 to-transparent group-hover:w-full transition-all duration-700 skew-x-12"></span>
                                <span class="relative">{{ __('messages.home.cta_btn_primary') }}</span>
                                <span
                                    class="material-symbols-outlined text-[20px] transition-transform group-hover:translate-x-2">arrow_forward</span>
                            </a>
                            <button
                                class="group flex items-center justify-center h-14 px-8 rounded-full border-2 border-gray-200 dark:border-gray-700 hover:border-primary dark:hover:border-primary text-gray-700 dark:text-white font-semibold transition-all duration-300 bg-transparent hover:bg-primary/5 hover:-translate-y-1">
                                <span
                                    class="group-hover:scale-110 transition-transform duration-300">{{ __('messages.home.cta_btn_secondary') }}</span>
                            </button>
                        </div>

                        <!-- Trust Indicator with animation -->
                        <div class="flex items-center gap-4 mt-6 pt-6 border-t border-gray-100 dark:border-gray-800 w-full scroll-animate"
                            data-scroll="fade-up" data-delay="1000">
                            <div class="flex -space-x-3">
                                <img alt=""
                                    class="w-10 h-10 rounded-full border-2 border-white dark:border-[#1a1111] scroll-animate hover:scale-110 hover:z-10 transition-transform duration-300"
                                    data-scroll="pop" data-delay="1100"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuATPE3iJboXGYQntdVDMWiwQw1jwaf2dJPUn9Rhbe1D2n4_q1T_CuFvNu9G5fyBOUhrwgJx33AgyhGKyjUIqWTJ2GbOpnnxzp1VuT92JxT4fD7AG83j3GGwvIiezGDMTYP-L9FSkIsf8VjvPKEzqa5shKwHY9C5kCMvsivzCjv64HyJHVC0bIzc_oUzvoiYhjrQg5jhsVcfv23Yo3ZumR44oeOsLdO4fu75eoG-9MoKBnh1oYxaAb7T7sFQVs73nZn0vISJ9zOiFiE" />
                                <img alt=""
                                    class="w-10 h-10 rounded-full border-2 border-white dark:border-[#1a1111] scroll-animate hover:scale-110 hover:z-10 transition-transform duration-300"
                                    data-scroll="pop" data-delay="1200"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDXNJ4SJ6Xgrad6mDofi-SpnX5G1dUMZBwLHZkQcT7aHu405v2OE9YGRJEToF5Ij_GgETV1xCiWSzhZcf9VueOPs6bBH5SvWhp0QcJD0Zn-C4su68k5gFnEKi024lSMzu7GBiyyP2d_wtY_l8pdTLyM9JKvlBfeeR5YBYLKEPGmumQR9dqo7pTGb4htn0N6dDAeKQlii_azakSEZHEG7LFiNkkaiCrTbE2Ax3IN7Iuz1nSbzuOHpo3uaeTYbtzmMKkS5GjdZRjcx7k" />
                                <img alt=""
                                    class="w-10 h-10 rounded-full border-2 border-white dark:border-[#1a1111] scroll-animate hover:scale-110 hover:z-10 transition-transform duration-300"
                                    data-scroll="pop" data-delay="1300"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAf6m9nK99ZKt6zEkHCHvRdYbmmBfmz5zwWelKantnMPyaLRXWjnv-ShCaGjjnRAuift6-es7YOzlKjwGcsMOvu7eMZz7clL_ajQrJo0BEdrrT_5DaHDg_nyhhXHUcngN8pSxtuu5WPzk5rb3wXICKLKwHWkbsW8YAoq6Ka8GZ0M6AoP6VsfA3gR3feWdjMZwpAQKqigVNSiJLsF5VxZvfUT1YrXg9k0rER2j-kcwQXdWvLBLfK4ZvnH9l8xBgMyoR1Vjw_CVRvVKE" />
                                <div class="w-10 h-10 rounded-full border-2 border-white dark:border-[#1a1111] bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300 scroll-animate hover:scale-110 hover:z-10 transition-transform duration-300"
                                    data-scroll="pop" data-delay="1400">{{ __('messages.home.cta_trust_more') }}</div>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex text-yellow-400 text-[16px] gap-0.5">
                                    <span class="material-symbols-outlined fill-current scroll-animate"
                                        data-scroll="star-rotate" data-delay="1100"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined fill-current scroll-animate"
                                        data-scroll="star-rotate" data-delay="1200"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined fill-current scroll-animate"
                                        data-scroll="star-rotate" data-delay="1300"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined fill-current scroll-animate"
                                        data-scroll="star-rotate" data-delay="1400"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined fill-current scroll-animate"
                                        data-scroll="star-rotate" data-delay="1500"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                </div>
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.home.cta_trust_text') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visual Side -->
                <div class="lg:col-span-5 relative order-1 lg:order-2 h-64 lg:h-auto overflow-hidden">
                    <div class="absolute inset-0 bg-gray-200 dark:bg-gray-800 scroll-animate" data-scroll="fade-in">
                        <img alt="Abstract Background" class="w-full h-full object-cover animate-ken-burns"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCxMyJU-T1I0etdxYd8GEL8K4mqtiN3b1vvUWI7VgfQRQrfYUBdnHQXqqv2oGoRs5cuVw4cJrV1dizylYHIcWVjEpj11Qg3AI0Ym_b2tJfGqq7vnnpV18YkTICLRZj_Vgv6SB05wha0w-w_zKLR35QlNgpV6DqTTvqB9pHLWL_w3ysRMyxM20NDMj-ImD2yXdRwd3_WPg0yfULz4IRq08NYrVQ5rHQiMBegguTjknCPgyRn0Y7tlZQIo-_3c1XDY1znz_zFAPLBsXs" />
                        <!-- Gradient Overlay -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-primary/40 to-transparent mix-blend-overlay animate-gradient-shift">
                        </div>
                        <div class="absolute inset-0 bg-black/10"></div>
                    </div>

                    <!-- Floating glassmorphism card -->
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[80%] aspect-[4/3] hidden lg:block scroll-animate"
                        data-scroll="float-card" data-delay="600">
                        <div
                            class="absolute inset-0 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl flex items-center justify-center p-8 transform rotate-3 hover:rotate-0 transition-all duration-700 hover:scale-105 group">
                            <div class="w-full h-full flex flex-col gap-4">
                                <div class="flex items-center gap-4 mb-4 scroll-animate" data-scroll="slide-right"
                                    data-delay="800">
                                    <div
                                        class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-white animate-spin-slow">
                                        <span class="material-symbols-outlined">analytics</span>
                                    </div>
                                    <div class="h-4 w-32 bg-white/20 rounded-full animate-shimmer"></div>
                                </div>
                                <div class="h-32 w-full bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-4 flex items-end gap-2 scroll-animate"
                                    data-scroll="fade-up" data-delay="1000">
                                    <div class="w-1/5 bg-primary/40 rounded-t-sm scroll-animate"
                                        data-scroll="bar-grow" data-delay="1200" style="--bar-height: 40%;"></div>
                                    <div class="w-1/5 bg-primary/60 rounded-t-sm scroll-animate"
                                        data-scroll="bar-grow" data-delay="1300" style="--bar-height: 60%;"></div>
                                    <div class="w-1/5 bg-primary/30 rounded-t-sm scroll-animate"
                                        data-scroll="bar-grow" data-delay="1400" style="--bar-height: 30%;"></div>
                                    <div class="w-1/5 bg-primary rounded-t-sm scroll-animate" data-scroll="bar-grow"
                                        data-delay="1500" style="--bar-height: 80%;"></div>
                                    <div class="w-1/5 bg-primary/50 rounded-t-sm scroll-animate"
                                        data-scroll="bar-grow" data-delay="1600" style="--bar-height: 50%;"></div>
                                </div>
                                <div class="flex gap-2 mt-auto scroll-animate" data-scroll="slide-right"
                                    data-delay="1700">
                                    <div class="h-8 w-24 rounded-full bg-white/20 animate-pulse-slow"></div>
                                    <div
                                        class="h-8 w-24 rounded-full bg-white/5 border border-white/10 animate-pulse-slow animation-delay-300">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<style>
    /* Keyframe Animations */
    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    @keyframes float-delayed {

        0%,
        100% {
            transform: translateY(0px) translateX(0px);
        }

        50% {
            transform: translateY(-30px) translateX(10px);
        }
    }

    @keyframes pulse-slow {

        0%,
        100% {
            opacity: 0.3;
            transform: scale(1);
        }

        50% {
            opacity: 0.5;
            transform: scale(1.05);
        }
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    @keyframes ken-burns {
        0% {
            transform: scale(1);
        }

        100% {
            transform: scale(1.1);
        }
    }

    @keyframes gradient-shift {

        0%,
        100% {
            opacity: 0.4;
        }

        50% {
            opacity: 0.6;
        }
    }

    @keyframes spin-slow {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Animation Classes */
    .animate-shimmer {
        position: relative;
        overflow: hidden;
    }

    .animate-shimmer::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }

    .animate-ken-burns {
        animation: ken-burns 20s ease-in-out infinite alternate;
    }

    .animate-gradient-shift {
        animation: gradient-shift 3s ease-in-out infinite;
    }

    .animate-spin-slow {
        animation: spin-slow 3s linear infinite;
    }

    .animate-pulse-slow {
        animation: pulse-slow 4s ease-in-out infinite;
    }

    .animation-delay-300 {
        animation-delay: 0.3s;
    }

    /* Scroll Animation States */
    .scroll-animate {
        opacity: 0;
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .scroll-animate.scroll-revealed {
        opacity: 1;
    }

    /* Scroll Animation Types */
    .scroll-animate[data-scroll="fade-up"] {
        transform: translateY(40px);
    }

    .scroll-animate[data-scroll="fade-up"].scroll-revealed {
        transform: translateY(0);
    }

    .scroll-animate[data-scroll="slide-left"] {
        transform: translateX(-50px);
    }

    .scroll-animate[data-scroll="slide-left"].scroll-revealed {
        transform: translateX(0);
    }

    .scroll-animate[data-scroll="slide-right"] {
        transform: translateX(50px);
    }

    .scroll-animate[data-scroll="slide-right"].scroll-revealed {
        transform: translateX(0);
    }

    .scroll-animate[data-scroll="scale-in"] {
        transform: scale(0.85);
    }

    .scroll-animate[data-scroll="scale-in"].scroll-revealed {
        transform: scale(1);
    }

    .scroll-animate[data-scroll="zoom-in"] {
        transform: scale(0.8);
    }

    .scroll-animate[data-scroll="zoom-in"].scroll-revealed {
        transform: scale(1);
    }

    .scroll-animate[data-scroll="pop"] {
        transform: scale(0);
    }

    .scroll-animate[data-scroll="pop"].scroll-revealed {
        transform: scale(1);
    }

    .scroll-animate[data-scroll="star-rotate"] {
        transform: scale(0) rotate(-180deg);
    }

    .scroll-animate[data-scroll="star-rotate"].scroll-revealed {
        transform: scale(1) rotate(0deg);
    }

    .scroll-animate[data-scroll="float-card"] {
        transform: translate(-50%, -50%) translateY(60px);
        opacity: 0;
    }

    .scroll-animate[data-scroll="float-card"].scroll-revealed {
        transform: translate(-50%, -50%) translateY(0);
        opacity: 1;
    }

    .scroll-animate[data-scroll="bar-grow"] {
        height: 0;
    }

    .scroll-animate[data-scroll="bar-grow"].scroll-revealed {
        height: var(--bar-height, 50%);
    }

    .scroll-animate[data-scroll="float"] {
        animation: none;
    }

    .scroll-animate[data-scroll="float"].scroll-revealed {
        animation: float 6s ease-in-out infinite;
    }

    .scroll-animate[data-scroll="float-delayed"] {
        animation: none;
    }

    .scroll-animate[data-scroll="float-delayed"].scroll-revealed {
        animation: float-delayed 8s ease-in-out infinite;
    }

    .scroll-animate[data-scroll="pulse"] {
        animation: none;
    }

    .scroll-animate[data-scroll="pulse"].scroll-revealed {
        animation: pulse-slow 4s ease-in-out infinite;
    }

    .scroll-animate[data-scroll="fade-in"] {
        opacity: 0;
    }

    .scroll-animate[data-scroll="fade-in"].scroll-revealed {
        opacity: 1;
    }
</style>

<script>
    // Scroll Animation Handler - Hanya untuk CTA Section
    (function() {
        const ctaSection = document.getElementById('cta-section');
        if (!ctaSection) return;

        const animateElements = ctaSection.querySelectorAll('.scroll-animate');

        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const delay = element.getAttribute('data-delay') || 0;

                    setTimeout(() => {
                        element.classList.add('scroll-revealed');
                    }, parseInt(delay));

                    // Optional: unobserve after animation
                    // observer.unobserve(element);
                }
            });
        }, observerOptions);

        animateElements.forEach(element => {
            observer.observe(element);
        });
    })();
</script>
