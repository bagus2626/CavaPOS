<style>
  .benefit-1, .benefit-2, .benefit-3 {
  opacity: 0; /* sembunyikan dulu */
}

.benefit-1.animate {
  animation: fadeInLeft 1s ease forwards;
  animation-delay: 0.1s;
}

.benefit-2.animate {
  animation: fadeInUp 1s ease forwards;
  animation-delay: 0.3s;
}

.benefit-3.animate {
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

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(40px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
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


<section id="benefits" class="py-20 ">
    <div class="max-w-7xl mx-auto px-4 md:px-12 2xl:px-0">
        <div class="flex flex-col md:flex-row justify-between items-start gap-8">
            <x-benefit-box
                icon="icons/icon-headset.png"
                title="24/7 Support"
                description="Layanan kami dapat dihubungi kapanpun"
                tailwindClass="benefit-1"
            />
            <x-benefit-box
                icon="icons/icon-download.png"
                title="Update Berkala"
                description="Kami senantiasa akan melakukan pembaharuan secara berkala"
                tailwindClass="benefit-2"
            />
            <x-benefit-box
                icon="icons/icon-colaboration.png"
                title="Kolaborasi"
                description="Kami selalu mengedepankan kolaborasi dengan customer"
                tailwindClass="benefit-3"
            />
        </div>
    </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.benefit-1, .benefit-2, .benefit-3');

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

