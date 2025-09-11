<section class="py-20 2xl:py-36">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row items-center gap-12">
      <!-- About Images -->
      <div class="flex-1 w-full animate-fade-in-left" id="about-content">
        <div class="hidden sm:flex flex-wrap justify-center gap-4">
          <img src="{{ asset('images/vastech-office2.png') }}" alt="Office 2"
               class="w-full sm:w-[48%] rounded-lg shadow-lg grayscale hover:grayscale-0 transition duration-500 object-cover" />
          <img src="{{ asset('images/vastech-office1.png') }}" alt="Office 1"
               class="w-full sm:w-[48%] rounded-lg shadow-lg grayscale hover:grayscale-0 transition duration-500 object-cover" />
        </div>
        <div class="mt-4 flex justify-center">
          <img src="{{ asset('images/vastech-office3.png') }}" alt="Office 3"
               class="w-full sm:w-[65%] rounded-lg shadow-lg grayscale hover:grayscale-0 transition duration-500 object-cover" />
        </div>
      </div>
      <!-- About Content -->
      <div class="flex-1 text-center md:text-left animate-fade-in-right">
        <h4 class="text-indigo-500 dark:text-white font-semibold uppercase tracking-wide mb-3">Mengapa Vastech</h4>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-600 dark:text-gray-300 mb-6 leading-tight">
          Kami selalu mengedepankan pelayanan untuk customer
        </h2>
        <p class="text-gray-400 dark:text-gray-00 mb-8">
          Kami menyediakan pelayanan dan solusi teknologi terbaik yang disesuaikan dengan kebutuhan bisnis Anda.
        </p>
        <a href="#" data-fslightbox
           class="inline-flex items-center gap-3 px-4 py-3 rounded-full bg-indigo-600 dark:bg-gray-300 hover:dark:bg-gray-700 text-white dark:text-black font-semibold shadow-md hover:bg-indigo-700 transition duration-300">
          <span class="flex items-center justify-center w-8 h-8 bg-indigo-700 dark:bg-black rounded-full">
            <img src="{{ asset('icons/icon-play.svg') }}" alt="Play" class="w-4 h-4" />
          </span>
          Tonton Demo
        </a>
      </div>

    </div>
  </div>
</section>

{{-- Animations --}}
<style>
  .animate-fade-in-left, .benefit-2, .benefit-3 {
  opacity: 0; /* sembunyikan dulu */
}

.animate-fade-in-left.animate {
  animation: fadeInLeft 1s ease forwards;
  animation-delay: 0.1s;
}


.animate-fade-in-right.animate {
  animation: fadeInRight 1s ease forwards;
  animation-delay: 0.5s;
}

/* keyframes tetap sama seperti yang kamu buat */


  @keyframes fadeInLeft {
    from {
      opacity: 0;
      transform: translateX(-40px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes fadeInRight {
    from {
      opacity: 0;
      transform: translateX(40px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.animate-fade-in-left, .animate-fade-in-right');

    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          // Hapus opacity 0 dan trigger animasi dengan menambahkan kelas animasi
          entry.target.style.opacity = 0;
          entry.target.classList.add('animate');
          observer.unobserve(entry.target); // Optional: stop observing after anim triggered
        }
      });
    }, { threshold: 0.1 }); // trigger saat 10% elemen terlihat

    items.forEach(item => {
      // set opacity 0 awal (bisa juga di CSS)
      item.style.opacity = 0;
      observer.observe(item);
    });
  });
</script>
