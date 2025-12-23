<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CAVAA - Landing Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        'primary': '#b91c1c',
                        'primary-light': '#dc2626',
                        'primary-dark': '#991b1b',
                        'accent': '#d97706',
                        'accent-light': '#f59e0b',
                        'cream': '#fef3c7',
                        'cream-dark': '#fde68a',
                        'dark-brown': '#451a03',
                    },
                    backgroundImage: {
                        'hero-pattern': "url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"dots\" width=\"20\" height=\"20\" patternUnits=\"userSpaceOnUse\"><circle cx=\"10\" cy=\"10\" r=\"1.5\" fill=\"white\" opacity=\"0.1\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23dots)\"/></svg>')",
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease forwards',
                        'slide-in-right': 'slideInRight 0.8s ease forwards',
                    }
                }
            }
        }
    </script>
    <style>
        /* Prevent horizontal scroll */
        html,
        body {
            overflow-x: hidden;
            max-width: 100vw;
        }

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

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Mobile menu toggle */
        .mobile-menu {
            display: none;
        }

        .mobile-menu.active {
            display: block;
        }
    </style>
</head>

<body class="font-inter text-gray-900 bg-white overflow-x-hidden">

    <!-- Header -->
    @include('pages.client.home.header')

    <!-- Hero Section -->
    @include('pages.client.home.hero-section')

    <!-- Video Demo Section -->
    @include('pages.client.home.video-demo-section')

    <!-- Features Section -->
    @include('pages.client.home.features-section')

    <!-- Fitur CAVAA Section -->
    @include('pages.client.home.fitur-cavaa-section')

    <!-- CTA Section -->
    @include('pages.client.home.cta-section')

    <!-- Footer -->
    @include('pages.client.home.footer')

    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('active');
        }

        function playDemo() {
            alert('Demo video akan segera dimuat!');
        }

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const headerHeight = document.querySelector('header').offsetHeight;
                    // Reduced offset to position better at section background
                    const targetPosition = target.offsetTop - headerHeight - 5; // 5px minimal padding

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.querySelectorAll('.grid > div, .space-y-16 > div').forEach(el => {
            observer.observe(el);
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobileMenu');
            const menuButton = event.target.closest('button');
            const isClickInsideMenu = mobileMenu.contains(event.target);

            if (!isClickInsideMenu && !menuButton && mobileMenu.classList.contains('active')) {
                mobileMenu.classList.remove('active');
            }
        });

        // Close mobile menu on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                const mobileMenu = document.getElementById('mobileMenu');
                mobileMenu.classList.remove('active');
            }
        });

        // Fungsi toggle dropdown language
        function toggleLanguageDropdown() {
            const dropdown = document.getElementById('langDropdown');
            const chevron = document.getElementById('langChevron');

            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                dropdown.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        }

        // Tutup dropdown jika klik di luar
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('langDropdown');
            const langButton = event.target.closest('button[onclick="toggleLanguageDropdown()"]');

            if (!langButton && !dropdown?.contains(event.target)) {
                dropdown?.classList.add('hidden');
                const chevron = document.getElementById('langChevron');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });

        // Fungsi untuk switch language
        function switchLanguage(locale) {
            const dropdown = document.getElementById('langDropdown');
            const chevron = document.getElementById('langChevron');
            const mobileMenu = document.getElementById('mobileMenu');
            
            if (dropdown) {
                dropdown.classList.add('hidden');
            }
            if (chevron) {
                chevron.style.transform = 'rotate(0deg)';
            }
            if (mobileMenu) {
                mobileMenu.classList.remove('active');
            }
            
            // Cek apakah bahasa sudah sama
            const currentLang = '{{ app()->getLocale() }}';
            if (locale === currentLang) {
                return;
            }

            // Redirect langsung ke set-language
            window.location.href = `/set-language?locale=${locale}`;
        }

        // Update tampilan berdasarkan bahasa yang aktif
        function updateLanguageUI() {
            const currentLang = '{{ app()->getLocale() }}';

            // Update text pada desktop
            const currentLangText = document.getElementById('currentLangText');
            if (currentLangText) {
                currentLangText.textContent = currentLang.toUpperCase();
            }

            // Update checkmark pada dropdown desktop
            document.querySelectorAll('.lang-check-id, .lang-check-en').forEach(el => {
                el.style.opacity = '0';
            });

            const activeCheck = document.querySelector(`.lang-check-${currentLang}`);
            if (activeCheck) {
                activeCheck.style.opacity = '1';
            }

            // Update checkmark pada mobile
            document.querySelectorAll('.lang-check-mobile-id, .lang-check-mobile-en').forEach(el => {
                el.style.opacity = '0';
            });

            const activeMobileCheck = document.querySelector(`.lang-check-mobile-${currentLang}`);
            if (activeMobileCheck) {
                activeMobileCheck.style.opacity = '1';
            }

            // Update border pada mobile buttons
            document.querySelectorAll('.lang-mobile-btn').forEach(btn => {
                btn.classList.remove('border-primary', 'bg-cream/20');
                btn.classList.add('border-primary/20');
            });

            const activeMobileBtn = document.querySelector(
                `button[onclick="switchLanguage('${currentLang}')"].lang-mobile-btn`);
            if (activeMobileBtn) {
                activeMobileBtn.classList.remove('border-primary/20');
                activeMobileBtn.classList.add('border-primary', 'bg-cream/20');
            }
        }

        // Jalankan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            updateLanguageUI();

            // Update placeholder search bar berdasarkan bahasa
            const searchInput = document.querySelector('input[type="text"][placeholder]');
            if (searchInput) {
                const currentLang = '{{ app()->getLocale() }}';
                const placeholder = currentLang === 'id' ?
                    searchInput.getAttribute('data-lang-id-placeholder') :
                    searchInput.getAttribute('data-lang-en-placeholder');
                if (placeholder) {
                    searchInput.placeholder = placeholder;
                }
            }
        });
    </script>
</body>

</html>
