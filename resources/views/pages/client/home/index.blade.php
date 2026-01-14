<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>CAVAA - Modern POS Solution</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#b91d1d",
                        "primary-hover": "#991b1b",
                        "background-light": "#f8f6f6",
                        "background-dark": "#201212",
                        "surface-light": "#ffffff",
                        "surface-dark": "#2d1b1b",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "1rem",
                        "lg": "2rem",
                        "xl": "3rem",
                        "full": "9999px"
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'glow': '0 0 15px rgba(185, 29, 29, 0.15)',
                    }
                },
            },
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Glass Card Effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .dark .glass-card {
            background: rgba(32, 18, 18, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Feature Card Hover */
        .feature-card:hover .icon-container {
            background-color: #b91d1d;
            color: white;
            transform: scale(1.1);
        }

        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px -10px rgba(185, 29, 29, 0.15);
            border-color: rgba(185, 29, 29, 0.3);
        }

        .icon-container {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Animations */
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        /* Fade In Animations */
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

        /* WhatsApp Floating Button */
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 30px;
            right: 30px;
            background-color: #25D366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 32px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .whatsapp-float:hover {
            background-color: #128C7E;
            transform: scale(1.1);
            box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.4);
        }

        .whatsapp-float i {
            margin-top: 2px;
        }

        /* Animasi pulse untuk WhatsApp button */
        @keyframes pulse-wa {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }

        .whatsapp-float {
            animation: pulse-wa 2s infinite;
        }

        .whatsapp-float:hover {
            animation: none;
        }

        /* Responsive - ukuran lebih kecil di mobile s */
        @media screen and (max-width: 768px) {
            .whatsapp-float {
                width: 50px;
                height: 50px;
                bottom: 20px;
                right: 20px;
                font-size: 28px;
            }
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display selection:bg-primary/20 selection:text-primary">

    <!-- Header -->
    @include('pages.client.home.header')

    <!-- Hero Section -->
    @include('pages.client.home.hero-section')

    <!-- Fitur CAVAA Section -->
    @include('pages.client.home.fitur-cavaa-section')

    <!-- Video Demo Section -->
    @include('pages.client.home.video-demo-section')

    <!-- Features Section -->
    @include('pages.client.home.features-section')

    <!-- CTA Section -->

    <!-- Testimonial Section -->
    @include('pages.client.home.contact-section')

    <!-- Footer -->
    @include('pages.client.home.footer')

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/6285177152837?text=Halo%20CAVAA,%20saya%20ingin%20bertanya%20tentang%20produk%20Anda" 
       target="_blank"
       class="whatsapp-float"
       aria-label="Chat WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script>
        // Language Switcher
        const languageButton = document.getElementById('languageButton');
        const languageDropdown = document.getElementById('languageDropdown');
        const currentLang = document.getElementById('currentLang');

        // Toggle dropdown language
        languageButton?.addEventListener('click', (e) => {
            e.stopPropagation();
            const dropdown = document.getElementById('languageDropdown');

            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            languageDropdown?.classList.add('hidden');
        });

        // Fungsi untuk switch language - TAMBAHKAN INI
        function switchLanguage(locale) {
            const dropdown = document.getElementById('languageDropdown');

            if (dropdown) {
                dropdown.classList.add('hidden');
            }

            // Cek apakah bahasa sudah sama
            const currentLangValue = currentLang?.textContent?.toLowerCase() || 'id';
            if (locale === currentLangValue) {
                return;
            }

            // Redirect langsung ke set-language
            window.location.href = `/set-language?locale=${locale}`;
        }

        // Load saved language on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Update tampilan berdasarkan session
            const sessionLang = '{{ session('app_locale', 'id') }}';
            if (currentLang) {
                currentLang.textContent = sessionLang.toUpperCase();
            }
        });

        // ============================================
        // GANTI VIDEO DI SINI - SATU-SATUNYA TEMPAT!
        // ============================================
        const VIDEO_URL = 'https://youtu.be/Oextk-If8HQ?si=hhX57jBYTfF6Wnav';
        // ============================================

        // PENGATURAN FADE OUT (dalam milidetik)
        const FADE_OUT_DELAY = 5000; // 5 detik (ubah angka ini untuk mengatur waktu)

        let infoBarHidden = false;

        // Function to extract YouTube video ID from any URL format
        function getYouTubeVideoId(url) {
            const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
            const match = url.match(regExp);
            return (match && match[7].length === 11) ? match[7] : null;
        }


        // Function to hide video info bar
        function hideVideoInfoBar() {
            const infoBar = document.getElementById('video-info-bar');
            setTimeout(() => {
                infoBar.style.opacity = '0';
                infoBarHidden = true;
            }, FADE_OUT_DELAY);
        }


        // Function to show video info bar on hover
        function setupHoverEffect() {
            const videoContainer = document.getElementById('video-container');
            const infoBar = document.getElementById('video-info-bar');

            videoContainer.addEventListener('mouseenter', () => {
                if (infoBarHidden) {
                    infoBar.style.opacity = '1';
                }
            });

            videoContainer.addEventListener('mouseleave', () => {
                if (infoBarHidden) {
                    infoBar.style.opacity = '0';
                }
            });
        }


        // Initialize video on page load
        document.addEventListener('DOMContentLoaded', function() {
            const videoId = getYouTubeVideoId(VIDEO_URL);

            if (!videoId) {
                console.error('Invalid YouTube URL');
                document.getElementById('video-title').textContent = 'Video tidak valid';
                return;
            }

            // Set iframe src with proper embed format
            const iframe = document.getElementById('youtube-video');
            iframe.src = `https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1&enablejsapi=1`;

            // Set button link
            document.getElementById('youtube-link').href = VIDEO_URL;

            // Setup hover effect
            setupHoverEffect();

            // Load video info
            loadVideoInfo(videoId);
        });


        // Function to fetch video info
        async function loadVideoInfo(videoId) {
            try {
                // Using YouTube oEmbed API (no key required)
                const oEmbedResponse = await fetch(
                    `https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=${videoId}&format=json`);
                const oEmbedData = await oEmbedResponse.json();

                // Update title
                document.getElementById('video-title').textContent = oEmbedData.title;

                // Load YouTube Player API for duration
                loadYouTubePlayerAPI(videoId);

            } catch (error) {
                console.error('Error loading video info:', error);
                document.getElementById('video-title').textContent = 'Video Demo CAVAA';
                document.getElementById('video-duration').textContent = 'N/A';
                // Still hide even on error
                hideVideoInfoBar();
            }
        }


        // Load YouTube Player API to get duration
        function loadYouTubePlayerAPI(videoId) {
            if (!window.YT) {
                const tag = document.createElement('script');
                tag.src = "https://www.youtube.com/iframe_api";
                const firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            }

            window.onYouTubeIframeAPIReady = function() {
                const player = new YT.Player('youtube-video', {
                    events: {
                        'onReady': function(event) {
                            const duration = event.target.getDuration();
                            const minutes = Math.floor(duration / 60);
                            const seconds = Math.floor(duration % 60);
                            document.getElementById('video-duration').textContent =
                                `${minutes}:${seconds.toString().padStart(2, '0')} min`;

                            // Hide info bar after delay
                            hideVideoInfoBar();
                        }
                    }
                });
            };
        }
    </script>
</body>

</html>