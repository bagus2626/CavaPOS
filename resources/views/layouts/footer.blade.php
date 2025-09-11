<footer class=" w-full text-black dark:text-white pt-10 pb-4 px-6 md:px-20 bg-white border-t dark:border-none dark:bg-black">
    <div class="flex flex-col md:flex-row justify-between gap-8 mb-10">
        {{-- Kolom 1 --}}
        <div class="flex-1">
            <!-- Logo untuk Light Mode -->
            <img src="{{ asset('images/vastech-transparent-black.png') }}"
                class="block dark:hidden h-24 w-auto ml-7"
                alt="Logo Light Mode">

            <!-- Logo untuk Dark Mode -->
            <img src="{{ asset('images/vastech-transparent-white.png') }}"
                class="hidden dark:block h-24 w-auto ml-7"
                alt="Logo Dark Mode">
            <div class="flex-1 mt-4">
                <div>
                    <div class="flex space-x-4 bg-black p-2 rounded-full w-fit">
                        <a href="https://wa.me/6285183258575?text=Hi%20Vastech!%20saya%20ingin%20mengetahui%20lebih%20lanjut%20tentang%20produk%20digital%20yang%20ditawarkan"
                            class="transform transition-transform duration-300 hover:scale-125"
                            target="_blank">
                            <img src="{{ asset('icons/icon-whatsapp.png') }}" alt="Whatsapp" class="w-6 h-6">
                        </a>
                        <a href="https://www.instagram.com/vastech_id/"
                            class="transform transition-transform duration-300 hover:scale-125"
                            target="_blank">
                            <img src="{{ asset('icons/icon-instagram.png') }}" alt="Instagram" class="w-6 h-6">
                        </a>
                        <a href="https://www.linkedin.com/posts/vastech.dev_webdevelopment-softwarecompany-techinnovation-activity-7320650860521304064-aZI8?utm_source=share&utm_medium=member_ios&rcm=ACoAACQcU4UBV3ZbgcZzIloAbMQ-f3n9UKoq344"
                            class="transform transition-transform duration-300 hover:scale-125"
                            target="_blank">
                            <img src="{{ asset('icons/icon-linkedin.png') }}" alt="Linkedin" class="w-6 h-6">
                        </a>
                        <a href="#"
                            class="transform transition-transform duration-300 hover:scale-125"
                            onclick="sendEmail()" target="_blank">
                            <img src="{{ asset('icons/icon-email.png') }}" alt="Email" class="w-6 h-6">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom 2 --}}
        <div class="flex-1">
            <h3 class="text-lg font-semibold mb-3">Perusahaan</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ url('/about') }}" class="hover:underline">Tentang Kami</a></li>
                <li><a href="{{ url('/contact') }}" class="hover:underline">Kontak</a></li>
                <li><a href="{{ url('/careers') }}" class="hover:underline">Karier</a></li>
                <li><a href="{{ url('/terms') }}" class="hover:underline">Syarat & Ketentuan</a></li>
            </ul>
        </div>

        {{-- Kolom 3 --}}
        <div class="flex-1">
            <h3 class="text-lg font-semibold mb-3">Layanan</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ url('/search') }}" class="hover:underline">Pesan Tiket</a></li>
                <li><a href="{{ url('/faq') }}" class="hover:underline">Bantuan</a></li>
                <li><a href="{{ url('/partners') }}" class="hover:underline">Kemitraan</a></li>
            </ul>
        </div>
    </div>

    <div class="text-center text-xs border-t border-white/30 pt-4">
        &copy; {{ date('Y') }} Vastech. All rights reserved.
    </div>
</footer>
<script>//send email function
    function sendEmail() {
      var fullname = '';
      var email = '';
      var phone = '';
      var subject = 'Tanya tentang produk digital Vastech';
      var message = 'Halo Vastech, saya ingin menanyakan tentang produk digital yang ditawarkan.';

      var mailtoLink = 'mailto:development.vastech@gmail.com?subject=' + encodeURIComponent(subject) +
                       '&body=' + encodeURIComponent(
                         'Nama Lengkap: ' + fullname + '\n' +
                         'Email: ' + email + '\n' +
                         'No Telepon: ' + phone + '\n' +
                         'Pesan: ' + message
                       );

      window.location.href = mailtoLink;
    }
  </script>
