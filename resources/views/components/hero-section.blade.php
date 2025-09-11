{{-- resources/views/components/hero-section.blade.php --}}
<style>
  .hero-section {
    animation: fadeInLeft 2s ease forwards;
    animation-delay: 0.2s;
    opacity: 0; /* Supaya awalnya tersembunyi */
  }


  @keyframes fadeInLeft {
    from {
      opacity: 0;
      transform: translateX(-140px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

</style>

<div class="px-4 md:px-12 2xl:px-0 py-20  hero-section">
    <div class="max-w-7xl mx-auto">
        <div class="w-full md:w-2/3">
            <h1 class="text-3xl md:text-5xl font-extrabold text-gray-200 dark:text-white mb-6 leading-tight">
                Kami menyediakan jasa dalam pembuatan aplikasi
            </h1>

            <p class="text-lg text-gray-200 dark:text-gray-300 mb-8">
                Dari sistem informasi, aplikasi mobile, hingga solusi berbasis web—kami siap membantu Anda membawa ide menjadi kenyataan.
            </p>

            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <a href="#" onclick="sendEmail()"
                   class="inline-block px-6 py-3 text-white bg-indigo-900 dark:bg-black hover:bg-indigo-700 dark:hover:bg-gray-800 rounded-lg text-base font-semibold transition">
                    Mulai
                </a>

                <div class="text-sm text-gray-200 dark:text-gray-300">
                    <a href="mailto:development.vastech@gmail.com"
                       class="block font-medium text-indigo-600 dark:text-white hover:underline">
                        Hubungi kami
                    </a>
                    <span class="block">Apabila terdapat pertanyaan lebih lanjut</span>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function sendEmail() {
        window.location.href = "mailto:development.vastech@gmail.com";
    }
</script>
