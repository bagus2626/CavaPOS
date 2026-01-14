<!-- Video Demo Section -->
<section id="video" class="relative min-h-screen w-full flex flex-col justify-center overflow-hidden py-12 sm:py-16 lg:py-24 border-b border-white-200 dark:border-gray-800 bg-white">
    <!-- Background -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute top-20 right-0 w-[500px] h-[500px] bg-red-100/30 dark:bg-primary/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-40 left-0 w-[600px] h-[600px] bg-gray-200/30 dark:bg-primary/5 rounded-full blur-3xl"></div>
    </div>
   
    <div class="layout-container flex h-full grow flex-col z-10 px-4 sm:px-6 md:px-10 lg:px-20 xl:px-40">
        <!-- Text Content s -->
        <div class="max-w-[960px] mx-auto text-center mb-8 sm:mb-12 opacity-0 translate-y-10" data-scroll-animate>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider mb-3 sm:mb-4 border border-primary/20">
                <span class="material-symbols-outlined text-sm">play_circle</span> {{ __('messages.home.video_demo_badge') }}
            </div>
           
            <h1 class="text-slate-900 dark:text-white tracking-tight text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-[56px] font-extrabold leading-[1.1] mb-4 sm:mb-6 px-4">
                {{ __('messages.home.video_demo_title') }}
            </h1>
           
            <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg md:text-xl font-normal leading-relaxed max-w-2xl mx-auto px-4">
                {{ __('messages.home.video_demo_description') }}
            </p>
        </div>
       
        <!-- Video Player Area -->
        <div class="relative w-full max-w-[1080px] mx-auto group opacity-0 translate-y-10" data-scroll-animate data-scroll-delay="200">
            <div class="absolute -inset-1 bg-gradient-to-r from-primary/30 to-pink-600/30 rounded-[1.5rem] sm:rounded-[2rem] lg:rounded-[2.5rem] blur opacity-30 group-hover:opacity-60 transition duration-1000 group-hover:duration-200"></div>
           
            <div id="video-container" class="relative rounded-[1.5rem] sm:rounded-[2rem] overflow-hidden shadow-2xl shadow-primary/10 bg-gray-900 border border-gray-100 dark:border-gray-800 aspect-video">
                <!-- YouTube Video Embed -->
                <iframe
                    id="youtube-video"
                    class="w-full h-full"
                    src=""
                    title="{{ __('messages.home.video_demo_badge') }}"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                </iframe>
               
                <!-- Video Details Bottom Bar - Responsif with fade out and hover -->
                <div id="video-info-bar" class="absolute bottom-0 left-0 w-full p-4 sm:p-6 lg:p-8 bg-gradient-to-t from-black/80 via-black/40 to-transparent flex flex-col sm:flex-row sm:items-end justify-between gap-3 sm:gap-4 pointer-events-none transition-opacity duration-500 opacity-100">
                    <div>
                        <p class="text-white/60 text-xs sm:text-sm font-medium uppercase tracking-wider mb-1">{{ __('messages.home.video_demo_label') }}</p>
                        <h3 id="video-title" class="text-white text-base sm:text-xl lg:text-2xl font-bold">{{ __('messages.home.video_demo_loading') }}</h3>
                    </div>
                    <div class="text-white/80 text-xs sm:text-sm font-medium flex items-center gap-1 bg-black/40 px-2 sm:px-3 py-1 rounded-full backdrop-blur-sm w-fit">
                        <span class="material-symbols-outlined text-sm sm:text-base">schedule</span>
                        <span id="video-duration">--:--</span>
                    </div>
                </div>
            </div>
        </div>
       
        <!-- Bottom CTA -->
        <div class="mt-8 sm:mt-12 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 px-4 opacity-0 translate-y-10" data-scroll-animate data-scroll-delay="400">
            <a id="youtube-link" href="" target="_blank" class="w-full sm:w-auto flex min-w-[200px] cursor-pointer items-center justify-center rounded-full h-12 sm:h-14 px-6 sm:px-8 bg-primary hover:bg-red-700 hover:-translate-y-0.5 transition-all duration-200 text-white text-sm sm:text-base font-bold shadow-lg shadow-primary/20">
                <span class="mr-2 material-symbols-outlined">play_circle</span>
                <span class="truncate">{{ __('messages.home.video_demo_watch_youtube') }}</span>
            </a>
        </div>
    </div>
</section>

<style>
    /* Animasi untuk elemen yang muncul saat scroll */
    [data-scroll-animate] {
        transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }
    
    [data-scroll-animate].animate-in {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }
    
    /* Hover effect untuk video container */
    #video-container:hover #video-info-bar {
        opacity: 0;
    }
</style>

<script>
    // Intersection Observer untuk animasi scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = entry.target.dataset.scrollDelay || 0;
                setTimeout(() => {
                    entry.target.classList.add('animate-in');
                }, delay);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe semua elemen dengan data-scroll-animate
    document.addEventListener('DOMContentLoaded', () => {
        const animateElements = document.querySelectorAll('[data-scroll-animate]');
        animateElements.forEach(el => observer.observe(el));
    });
</script>