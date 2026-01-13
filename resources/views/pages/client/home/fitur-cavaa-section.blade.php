<!-- Features Section -->
<section id="features" class="w-full py-12 sm:py-16 lg:py-20 px-4 sm:px-6 lg:px-10 flex justify-center bg-background-light dark:bg-background-dark border-b border-gray-200 dark:border-gray-800 overflow-hidden">
    <div class="max-w-[1200px] w-full flex flex-col gap-10 sm:gap-12">
        <!-- Section Header -->
        <div class="text-center flex flex-col items-center gap-3 sm:gap-4 scroll-reveal">
            <h2 class="text-primary text-xs sm:text-sm font-bold tracking-wider uppercase bg-primary/10 px-3 py-1 rounded-full w-fit">{{ __('messages.home.features_section_title') }}</h2>
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black tracking-tight text-slate-900 dark:text-white leading-[1.15] max-w-[600px] px-4">
                {{ __('messages.home.features_section_heading') }}
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base md:text-lg max-w-[600px] px-4">
                {{ __('messages.home.features_section_description') }}
            </p>
        </div>
       
        <!-- Features Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
            <!-- Card 1 - Dashboard -->
            <div class="feature-card-wrapper scroll-reveal" data-delay="0">
                <div class="feature-card group">
                    <div class="card-front">
                        <div class="icon-container mb-4 sm:mb-6 flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-xl bg-[#ae1504]/10 text-[#ae1504] group-hover:bg-[#ae1504] group-hover:text-white transition-all duration-500 group-hover:rotate-12 group-hover:scale-110">
                            <span class="material-symbols-outlined text-2xl sm:text-3xl">dashboard</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-2 sm:mb-3 group-hover:text-primary transition-colors duration-300">{{ __('messages.home.feature_dashboard_title') }}</h3>
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors duration-300 flex-grow">
                            {{ __('messages.home.feature_dashboard_desc') }}
                        </p>
                        <div class="mt-4 pt-4">
                            <span class="text-xs text-primary font-semibold">{{ __('messages.home.click_to_learn_more') }} →</span>
                        </div>
                    </div>
                    
                    <div class="card-back">
                        <div class="flex items-center justify-between mb-4">
                            <span class="material-symbols-outlined text-3xl">dashboard</span>
                        </div>
                        <h3 class="text-xl font-bold mb-4">{{ __('messages.home.key_benefits') }}</h3>
                        <ul class="space-y-2 text-sm flex-grow overflow-y-auto">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_dashboard_benefit_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_dashboard_benefit_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_dashboard_benefit_3') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_dashboard_benefit_4') }}</span>
                            </li>
                        </ul>
                        <div class="mt-4">
                            <span class="text-xs opacity-80">{{ __('messages.home.click_to_flip_back') }}</span>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Card 2 - POS System -->
            <div class="feature-card-wrapper scroll-reveal" data-delay="100">
                <div class="feature-card group">
                    <div class="card-front">
                        <div class="icon-container mb-4 sm:mb-6 flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-xl bg-[#ae1504]/10 text-[#ae1504] group-hover:bg-[#ae1504] group-hover:text-white transition-all duration-500 group-hover:rotate-12 group-hover:scale-110">
                            <span class="material-symbols-outlined text-2xl sm:text-3xl">point_of_sale</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-2 sm:mb-3 group-hover:text-primary transition-colors duration-300">{{ __('messages.home.feature_pos_title') }}</h3>
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors duration-300 flex-grow">
                            {{ __('messages.home.feature_pos_desc') }}
                        </p>
                        <div class="mt-4 pt-4">
                            <span class="text-xs text-primary font-semibold">{{ __('messages.home.click_to_learn_more') }} →</span>
                        </div>
                    </div>
                    
                    <div class="card-back">
                        <div class="flex items-center justify-between mb-4">
                            <span class="material-symbols-outlined text-3xl">point_of_sale</span>
                        </div>
                        <h3 class="text-xl font-bold mb-4">{{ __('messages.home.key_benefits') }}</h3>
                        <ul class="space-y-2 text-sm flex-grow overflow-y-auto">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_pos_benefit_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_pos_benefit_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_pos_benefit_3') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_pos_benefit_4') }}</span>
                            </li>
                        </ul>
                        <div class="mt-4">
                            <span class="text-xs opacity-80">{{ __('messages.home.click_to_flip_back') }}</span>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Card 3 - Inventory -->
            <div class="feature-card-wrapper scroll-reveal" data-delay="200">
                <div class="feature-card group">
                    <div class="card-front">
                        <div class="icon-container mb-4 sm:mb-6 flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-xl bg-[#ae1504]/10 text-[#ae1504] group-hover:bg-[#ae1504] group-hover:text-white transition-all duration-500 group-hover:rotate-12 group-hover:scale-110">
                            <span class="material-symbols-outlined text-2xl sm:text-3xl">inventory_2</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-2 sm:mb-3 group-hover:text-primary transition-colors duration-300">{{ __('messages.home.feature_inventory_title') }}</h3>
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors duration-300 flex-grow">
                            {{ __('messages.home.feature_inventory_desc') }}
                        </p>
                        <div class="mt-4 pt-4">
                            <span class="text-xs text-primary font-semibold">{{ __('messages.home.click_to_learn_more') }} →</span>
                        </div>
                    </div>
                    
                    <div class="card-back">
                        <div class="flex items-center justify-between mb-4">
                            <span class="material-symbols-outlined text-3xl">inventory_2</span>
                        </div>
                        <h3 class="text-xl font-bold mb-4">{{ __('messages.home.key_benefits') }}</h3>
                        <ul class="space-y-2 text-sm flex-grow overflow-y-auto">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_inventory_benefit_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_inventory_benefit_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_inventory_benefit_3') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_inventory_benefit_4') }}</span>
                            </li>
                        </ul>
                        <div class="mt-4">
                            <span class="text-xs opacity-80">{{ __('messages.home.click_to_flip_back') }}</span>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Card 4 - Sales Analytics -->
            <div class="feature-card-wrapper scroll-reveal" data-delay="300">
                <div class="feature-card group">
                    <div class="card-front">
                        <div class="icon-container mb-4 sm:mb-6 flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-xl bg-[#ae1504]/10 text-[#ae1504] group-hover:bg-[#ae1504] group-hover:text-white transition-all duration-500 group-hover:rotate-12 group-hover:scale-110">
                            <span class="material-symbols-outlined text-2xl sm:text-3xl">trending_up</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-2 sm:mb-3 group-hover:text-primary transition-colors duration-300">{{ __('messages.home.feature_sales_title') }}</h3>
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors duration-300 flex-grow">
                            {{ __('messages.home.feature_sales_desc') }}
                        </p>
                        <div class="mt-4 pt-4">
                            <span class="text-xs text-primary font-semibold">{{ __('messages.home.click_to_learn_more') }} →</span>
                        </div>
                    </div>
                    
                    <div class="card-back">
                        <div class="flex items-center justify-between mb-4">
                            <span class="material-symbols-outlined text-3xl">trending_up</span>
                        </div>
                        <h3 class="text-xl font-bold mb-4">{{ __('messages.home.key_benefits') }}</h3>
                        <ul class="space-y-2 text-sm flex-grow overflow-y-auto">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_sales_benefit_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_sales_benefit_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_sales_benefit_3') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_sales_benefit_4') }}</span>
                            </li>
                        </ul>
                        <div class="mt-4">
                            <span class="text-xs opacity-80">{{ __('messages.home.click_to_flip_back') }}</span>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Card 5 - Customer Management -->
            <div class="feature-card-wrapper scroll-reveal" data-delay="400">
                <div class="feature-card group">
                    <div class="card-front">
                        <div class="icon-container mb-4 sm:mb-6 flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-xl bg-[#ae1504]/10 text-[#ae1504] group-hover:bg-[#ae1504] group-hover:text-white transition-all duration-500 group-hover:rotate-12 group-hover:scale-110">
                            <span class="material-symbols-outlined text-2xl sm:text-3xl">groups</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-2 sm:mb-3 group-hover:text-primary transition-colors duration-300">{{ __('messages.home.feature_customer_title') }}</h3>
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors duration-300 flex-grow">
                            {{ __('messages.home.feature_customer_desc') }}
                        </p>
                        <div class="mt-4 pt-4">
                            <span class="text-xs text-primary font-semibold">{{ __('messages.home.click_to_learn_more') }} →</span>
                        </div>
                    </div>
                    
                    <div class="card-back">
                        <div class="flex items-center justify-between mb-4">
                            <span class="material-symbols-outlined text-3xl">groups</span>
                        </div>
                        <h3 class="text-xl font-bold mb-4">{{ __('messages.home.key_benefits') }}</h3>
                        <ul class="space-y-2 text-sm flex-grow overflow-y-auto">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_customer_benefit_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_customer_benefit_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_customer_benefit_3') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_customer_benefit_4') }}</span>
                            </li>
                        </ul>
                        <div class="mt-4">
                            <span class="text-xs opacity-80">{{ __('messages.home.click_to_flip_back') }}</span>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Card 6 - Multi-Device -->
            <div class="feature-card-wrapper scroll-reveal" data-delay="500">
                <div class="feature-card group">
                    <div class="card-front">
                        <div class="icon-container mb-4 sm:mb-6 flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-xl bg-[#ae1504]/10 text-[#ae1504] group-hover:bg-[#ae1504] group-hover:text-white transition-all duration-500 group-hover:rotate-12 group-hover:scale-110">
                            <span class="material-symbols-outlined text-2xl sm:text-3xl">devices</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-2 sm:mb-3 group-hover:text-primary transition-colors duration-300">{{ __('messages.home.feature_multidevice_title') }}</h3>
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors duration-300 flex-grow">
                            {{ __('messages.home.feature_multidevice_desc') }}
                        </p>
                        <div class="mt-4 pt-4">
                            <span class="text-xs text-primary font-semibold">{{ __('messages.home.click_to_learn_more') }} →</span>
                        </div>
                    </div>
                    
                    <div class="card-back">
                        <div class="flex items-center justify-between mb-4">
                            <span class="material-symbols-outlined text-3xl">devices</span>
                        </div>
                        <h3 class="text-xl font-bold mb-4">{{ __('messages.home.key_benefits') }}</h3>
                        <ul class="space-y-2 text-sm flex-grow overflow-y-auto">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_multidevice_benefit_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_multidevice_benefit_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_multidevice_benefit_3') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span>{{ __('messages.home.feature_multidevice_benefit_4') }}</span>
                            </li>
                        </ul>
                        <div class="mt-4">
                            <span class="text-xs opacity-80">{{ __('messages.home.click_to_flip_back') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Flip Card Container - FIXED HEIGHT */
    .feature-card-wrapper {
        perspective: 1000px;
        height: 380px;
    }

    .feature-card {
        position: relative;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
        transition: transform 0.6s cubic-bezier(0.4, 0.0, 0.2, 1);
        cursor: pointer;
    }

    .feature-card.flipped {
        transform: rotateY(180deg);
    }

    .card-front,
    .card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        border-radius: 1rem;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
    }

    /* FRONT CARD - Warna Original */
    .card-front {
        background: white;
        border: 1px solid #e5e7eb;
        transition: all 0.5s ease;
        overflow: hidden;
    }

    .card-front:hover {
        border-color: #ae1504;
        box-shadow: 0 25px 50px -12px rgba(255, 71, 96, 0.2);
    }

    /* BACK CARD - GRADIENT MERAH */
    .card-back {
        background: #ae1504;
        color: white;
        transform: rotateY(180deg);
        overflow: hidden;
    }

    /* Scroll Reveal Animations */
    .scroll-reveal {
        opacity: 0;
        transform: translateY(50px) scale(0.95);
        transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }

    .scroll-reveal.revealed {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    /* Alternating Card Animations */
    .feature-card-wrapper:nth-child(odd) .feature-card {
        transform: translateX(-100px) translateY(50px) rotate(-5deg);
    }

    .feature-card-wrapper:nth-child(even) .feature-card {
        transform: translateX(100px) translateY(50px) rotate(5deg);
    }

    .feature-card-wrapper.revealed .feature-card {
        transform: translateX(0) translateY(0) rotate(0deg) !important;
    }

    .feature-card-wrapper.revealed .feature-card.flipped {
        transform: rotateY(180deg) !important;
    }

    /* Enhanced Card Hover Effects */
.card-front::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(239, 68, 68, 0.1), transparent);
    transition: left 0.6s ease;
    pointer-events: none;
}

    .feature-card:not(.flipped):hover .card-front::before {
        left: 100%;
    }

    /* Icon Pulse Effect on Hover */
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .feature-card:not(.flipped):hover .icon-container {
        animation: pulse 1s ease-in-out infinite;
    }

    /* Smooth Scroll Behavior */
    html {
        scroll-behavior: smooth;
    }

    /* Stagger Animation Delays */
    .scroll-reveal.revealed {
        transition-delay: calc(var(--delay) * 1ms);
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .feature-card-wrapper {
            height: 340px;
        }
        
        .card-front,
        .card-back {
            padding: 1.5rem;
        }
    }

    @media (min-width: 640px) {
        .card-front,
        .card-back {
            border-radius: 1.5rem;
        }
    }
</style>

<script>
    // Intersection Observer for Scroll Animations
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = entry.target.dataset.delay || 0;
                    entry.target.style.setProperty('--delay', delay);
                    
                    setTimeout(() => {
                        entry.target.classList.add('revealed');
                    }, delay);
                }
            });
        }, observerOptions);

        // Observe all scroll-reveal elements
        const scrollElements = document.querySelectorAll('.scroll-reveal');
        scrollElements.forEach(el => observer.observe(el));

        // Flip Card Functionality
        const featureCards = document.querySelectorAll('.feature-card');
        
        featureCards.forEach(card => {
            card.addEventListener('click', function(e) {
                // Check if clicked element or its parent is the close button
                if (e.target.classList.contains('close-btn') || 
                    e.target.closest('.close-btn') ||
                    e.target.textContent === 'close') {
                    this.classList.remove('flipped');
                    return;
                }
                
                this.classList.toggle('flipped');
            });
        });
    });
</script>