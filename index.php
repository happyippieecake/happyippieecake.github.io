<?php
$conn = new mysqli("localhost", "root", "", "happyippiecake");
$menus = $conn->query("SELECT * FROM menu ORDER BY id DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>HappyippieCake | Toko Kue Premium Cimahi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <style>
    body { font-family: 'Montserrat', Arial, sans-serif;}
    .active-link { border-bottom: 2px solid #fd5e53;}
    .footer-link:hover { color:#fd5e53; transform:translateY(-2px);}
    .brand-font { font-family: 'Pacifico',cursive;}
    .card-anim { transition: .33s cubic-bezier(.42,.41,.53,.87); box-shadow:0 4px 32px -8px #fd5e531c,0 0 0 4px #fff3f4;}
    .card-anim:hover { box-shadow:0 8px 40px -10px #fd5e53a5,0 0 0 4px #fde4ec; transform: scale(1.04) translateY(-3px); border:1px solid #fd5e53;}
    .fade-in, .slide-in {opacity:0;pointer-events:none;}
    .fade-in.visible, .slide-in.visible {opacity:1;pointer-events:auto;}
    .fade-in {transform: translateY(24px); transition: opacity 0.7s, transform 0.7s;}
    .fade-in.visible {transform: none;}
    .slide-in {transform: scale(.96) translateY(15px); transition: opacity 0.8s cubic-bezier(.43,.64,.38,1.46), transform 0.8s;}
    .slide-in.visible {transform: none;}
    .menu-anim {transition: .2s cubic-bezier(.34,1.56,.64,1);}
    .menu-anim:hover {transform: scale(1.03) rotate(-1deg);}
  </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-white to-pink-100">

  <!-- Navbar Hamburger -->
  <nav class="w-full bg-white shadow sticky top-0 z-20 fade-in scroll-animate">
    <div class="max-w-6xl mx-auto flex justify-between items-center py-3 px-4">
      <a href="index.php" class="text-3xl font-bold text-pink-500 brand-font tracking-wider">HappyippieCake</a>
      <button id="nav-toggle" class="md:hidden focus:outline-none text-pink-600 p-2" aria-label="open menu">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
      </button>
      <ul id="nav-menu" class="hidden md:flex gap-8 font-semibold text-gray-700 md:static absolute top-[60px] left-0 w-full bg-white md:w-auto flex-col md:flex-row shadow md:shadow-none">
        <li><a href="#home" class="nav-link active-link block px-4 py-2 transition">Home</a></li>
        <li><a href="#about" class="nav-link block px-4 py-2 transition">About</a></li>
        <li><a href="pesan.php" class="nav-link block px-4 py-2 transition">Menu</a></li>
        <li><a href="#gallery" class="nav-link block px-4 py-2 transition">Gallery</a></li>
        <li><a href="login.php" class="nav-link block px-4 py-2 transition">Admin</a></li>
      </ul>
    </div>
  </nav>
  <script>
    var navToggle = document.getElementById('nav-toggle');
    var navMenu = document.getElementById('nav-menu');
    navToggle.onclick = function() { navMenu.classList.toggle("hidden"); };
    document.querySelectorAll('#nav-menu a').forEach(link => {
      link.addEventListener('click', function(){
        if(window.innerWidth < 768){
          navMenu.classList.add("hidden");
        }
      });
    });
  </script>

  <!-- Hero Section -->
  <section id="home" class="flex items-center justify-center relative min-h-[60vh] bg-cover bg-no-repeat fade-in scroll-animate" style="background-image: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1500&q=80');">
    <div class="absolute inset-0 bg-gradient-to-br from-pink-300/60 via-transparent to-pink-100/70"></div>
    <div class="relative text-center z-10 px-2 slide-in scroll-animate">
      <h1 class="text-5xl md:text-7xl mb-4 text-pink-600 font-bold brand-font drop-shadow-md animate-bounce">HappyippieCake</h1>
      <div class="flex justify-center mb-4">
        <span class="inline-block bg-white/80 rounded-full px-4 py-2 text-pink-800 text-base font-semibold shadow backdrop-blur brand-font">Premium Cake • Custom & Fresh • Cimahi</span>
      </div>
      <p class="text-xl md:text-2xl text-white drop-shadow font-semibold mb-6 fade-in scroll-animate">
        Toko kue premium untuk momen istimewa,<br class="hidden md:block"> fresh & custom setiap hari!
      </p>
      <a href="#menu" class="px-10 py-4 bg-pink-500 text-white text-lg rounded-full hover:bg-pink-600 shadow-xl font-bold transition brand-font tracking-wide border-2 border-white/60 fade-in scroll-animate">
        Lihat Menu Kue
      </a>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="py-12 bg-pink-50 fade-in scroll-animate">
    <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row items-center gap-12 slide-in scroll-animate">
      <img src="gambar/utama.jpg" alt="HappyippieCake Team" class="w-72 h-72 md:w-80 md:h-80 object-cover rounded-3xl shadow-2xl mb-6 md:mb-0 ring-4 ring-pink-200 card-anim" />
      <div class="md:w-1/2">
        <h2 class="text-4xl font-bold text-pink-600 mb-6 brand-font">Tentang HappyippieCake</h2>
        <p class="mb-5 text-gray-700 text-lg">Sejak 2018, HappyippieCake hadir dengan kue custom bertema unik, bahan premium, dan sentuhan artistik. Setiap cake dikerjakan detail, bisa desain sesuai impian dan konsultasi tema <span class="text-pink-500 font-semibold">gratis!</span></p>
        <ul class="list-disc pl-7 text-pink-700 space-y-2 text-lg font-semibold">
          <li>Kue fresh dari oven setiap hari</li>
          <li>Desain custom sesuai keinginan</li>
          <li>Kualitas premium dan bergaransi</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Menu Section: Best Seller/Recent -->
  <section id="menu" class="py-16 bg-white relative z-0 fade-in scroll-animate">
    <div class="max-w-6xl mx-auto px-4">
      <h2 class="text-4xl font-bold text-center mb-12 text-pink-600 brand-font tracking-wide slide-in scroll-animate">Menu Cake Spesial</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-12 justify-items-center">
        <?php foreach ($menus as $menu): ?>
        <div class="bg-white rounded-3xl ring-2 ring-pink-100 shadow-2xl card-anim transition-all flex flex-col w-72 hover:-translate-y-2 hover:z-10 relative overflow-hidden group menu-anim fade-in scroll-animate">
          <div class="relative">
            <img src="<?= htmlspecialchars($menu['gambar']) ?>"
              alt="<?= htmlspecialchars($menu['nama']) ?>"
              class="rounded-t-3xl h-48 w-full object-cover transition-all group-hover:scale-105 card-anim" />
            <div class="absolute top-3 right-3 bg-white/80 px-3 py-1 rounded-full text-pink-700 font-bold text-xs shadow slide-in scroll-animate">#BestSeller</div>
          </div>
          <div class="p-5 grow flex flex-col">
            <span class="font-bold text-xl mb-2 text-pink-600 brand-font"><?= htmlspecialchars($menu['nama']) ?></span>
            <span class="text-gray-700 mb-3 text-[15px] font-medium"><?= htmlspecialchars($menu['deskripsi']) ?></span>
            <div class="flex justify-between items-center mt-auto">
              <span class="bg-pink-100 rounded-full font-bold text-pink-700 px-4 py-1 text-base shadow-sm">Rp<?= number_format($menu['harga'],0,',','.') ?></span>
              <form action="pesan.php" method="GET">
                <input type="hidden" name="menu" value="<?= $menu['id'] ?>">
                <button type="submit" class="bg-gradient-to-tr from-pink-500 to-pink-400 text-white rounded-full px-6 py-2 font-semibold hover:from-pink-600 hover:to-pink-400 shadow transition brand-font text-base menu-anim">Pesan</button>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach ?>
      </div>
      <div class="text-center mt-14 fade-in scroll-animate">
        <a href="pesan.php" class="inline-block px-12 py-3 rounded-full bg-gradient-to-tr from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-400 text-white shadow-xl font-bold text-xl transition brand-font border-2 border-white/60">Lihat Semua Menu &gt;&gt;</a>
      </div>
    </div>
  </section>

  <!-- Gallery Section -->
  <section id="gallery" class="pb-16 pt-6 bg-white fade-in scroll-animate">
    <div class="max-w-6xl mx-auto px-4">
      <h2 class="text-4xl font-bold text-center mb-10 text-pink-600 brand-font tracking-wide slide-in scroll-animate">Galeri Karya Cake Terbaik</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSjS7XjbQ7Eu32S9YAmeju-4QM6whprsavSTQ&s" class="rounded-2xl shadow-xl object-cover h-40 w-full card-anim fade-in scroll-animate">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80" class="rounded-2xl shadow-xl object-cover h-40 w-full card-anim fade-in scroll-animate">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80" class="rounded-2xl shadow-xl object-cover h-40 w-full card-anim fade-in scroll-animate">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80" class="rounded-2xl shadow-xl object-cover h-40 w-full card-anim fade-in scroll-animate">
      </div>
    </div>
  </section>

  <!-- Footer Modern -->
  <footer class="bg-gradient-to-t from-pink-700 via-pink-500 to-pink-400 text-white pt-10 pb-5 shadow-xl mt-20 fade-in scroll-animate">
    <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-8">
      <div class="mb-4 md:mb-0 text-center md:text-left">
        <span class="text-3xl font-bold brand-font tracking-wider">HappyippieCake</span>
        <p class="mt-1 text-base text-white/80">Cakes for Every Story &amp; Memory</p>
        <p class="text-xs mt-1">&copy; 2025 HappyippieCake. All Rights Reserved.</p>
      </div>
      <div class="flex gap-8 items-center text-xl">
        <a href="https://instagram.com" target="_blank" class="footer-link" title="Instagram">
          <svg class="inline" width="26" height="26" fill="currentColor"><path d="M7.75 2C4.126 2 1 5.126 1 8.75v6.5C1 18.874 4.126 22 7.75 22h8.5c3.624 0 6.75-3.126 6.75-6.75v-6.5C23 5.126 19.874 2 16.25 2h-8.5zm0 2h8.5c2.623 0 4.75 2.127 4.75 4.75v6.5c0 2.623-2.127 4.75-4.75 4.75h-8.5A4.755 4.755 0 013 13.25v-6.5A4.755 4.755 0 017.75 4zm4.25 2.5a4.25 4.25 0 100 8.5 4.25 4.25 0 000-8.5zm0 2a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5zM18.625 6a.875.875 0 110 1.75.875.875 0 010-1.75z"/></svg>
        </a>
        <a href="https://wa.me/628123456789" target="_blank" class="footer-link" title="WhatsApp">
          <svg class="inline" width="26" height="26" fill="currentColor"><path d="M2 12A10 10 0 0012 22h.043C8.81 22 5.997 20.094 4.257 17.489a.995.995 0 01.156-1.221l1.134-1.12A1.004 1.004 0 016.5 15.05c.9.62 1.867 1.059 2.913 1.285 1.046.227 2.137.228 3.195.002a7.993 7.993 0 001.372-.38c.322-.113.684-.011.883.245l1.127 1.087a.997.997 0 01.157 1.221C18.004 20.106 15.19 22 12.043 22H12A10 10 0 002 12zm10-8a8 8 0 110 16A8 8 0 012 12a8 8 0 0110-8zm-1 9.5a1 1 0 00-1 1V16a1 1 0 102 0v-3.5a1 1 0 00-1-1zm0-2a1 1 0 100 2 1 1 0 000-2z"/></svg>
        </a>
        <a href="#" class="footer-link" title="Facebook">
          <svg class="inline" width="26" height="26" fill="currentColor"><path d="M22.675 0H1.325C.595 0 0 .595 0 1.326V22.675c0 .73.595 1.325 1.325 1.325h11.495v-9.294H9.691V11.09h3.129V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.243l-1.917.001c-1.504 0-1.797.715-1.797 1.762v2.312h3.587l-.467 3.613h-3.12v9.294h6.116c.73 0 1.325-.595 1.325-1.326V1.325C24 .595 23.405 0 22.675 0"/></svg>
        </a>
      </div>
    </div>
    <div class="text-center text-lg pt-4 text-white/80 font-light brand-font">Serving Joy & Elegance in Every Slice</div>
  </footer>

  <!-- Scroll animation with Intersection Observer -->
  <script>
    // reveal .scroll-animate on scroll
    let observer = new IntersectionObserver((entries, obs)=>{
      entries.forEach(entry=>{
        if(entry.isIntersecting){
          entry.target.classList.add('visible');
          obs.unobserve(entry.target);
        }
      });
    },{ threshold:.15 });
    document.querySelectorAll('.scroll-animate').forEach(el=>observer.observe(el));
  </script>
</body>
</html>
