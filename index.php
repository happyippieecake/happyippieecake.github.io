<?php
require_once __DIR__ . '/db_connect.php';
// Check if stok_tersedia column exists
$has_stok = $conn->query("SHOW COLUMNS FROM menu LIKE 'stok_tersedia'")->num_rows > 0;
// Only show available items if column exists, otherwise show all
if ($has_stok) {
    $menus = $conn->query("SELECT * FROM menu WHERE stok_tersedia = 1 OR stok_tersedia IS NULL ORDER BY id DESC LIMIT 3");
} else {
    $menus = $conn->query("SELECT * FROM menu ORDER BY id DESC LIMIT 3");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>x | Toko Kue Premium Cimahi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="styles.css">
  <script src="https://unpkg.com/lucide@latest"></script> 
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

  <!-- Desktop Navbar -->
  <nav class="fixed top-0 left-0 w-full z-50 glass-nav header-animate shadow-sm transition-all duration-300" id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        <!-- Logo -->
        <a href="index.php" class="flex-shrink-0 flex items-center gap-2 group">
           <span class="text-xl md:text-2xl font-bold text-pink-600 tracking-wide group-hover:text-pink-500 transition-colors">HappyippieCake</span>
        </a>

        <!-- Desktop Menu -->
        <div class="hidden lg:flex space-x-6 xl:space-x-8 items-center">
          <a href="#home" class="nav-link text-gray-600 hover:text-pink-600 font-medium transition-colors">Home</a>
          <a href="#about" class="nav-link text-gray-600 hover:text-pink-600 font-medium transition-colors">About</a>
          <a href="#menu" class="nav-link text-gray-600 hover:text-pink-600 font-medium transition-colors">Menu</a>
          <a href="#gallery" class="nav-link text-gray-600 hover:text-pink-600 font-medium transition-colors">Gallery</a>
          <a href="pesan.php" class="px-5 py-2.5 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-2">
            <i data-lucide="shopping-bag" class="w-4 h-4"></i> Order Now
          </a>
        </div>

        <!-- Mobile/Tablet Menu Button -->
        <div class="lg:hidden flex items-center">
          <button id="mobile-menu-btn" class="text-pink-600 hover:text-pink-800 focus:outline-none p-2 rounded-md transition-colors">
            <i data-lucide="menu" class="w-7 h-7"></i>
          </button>
        </div>
      </div>
    </div>
  </nav>

  <!-- Mobile Menu Overlay -->
  <div id="mobile-menu-overlay" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm hidden transition-opacity duration-300 opacity-0"></div>
  
  <!-- Mobile Menu Drawer -->
  <div id="mobile-menu-drawer" class="fixed top-0 right-0 h-full w-4/5 max-w-sm bg-white z-50 shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
      <div class="p-6 flex justify-between items-center border-b border-gray-100">
        <span class="text-xl font-bold text-pink-600 font-['Pacifico']">Menu</span>
        <button id="close-menu-btn" class="text-gray-500 hover:text-red-500 transition-colors">
          <i data-lucide="x" class="w-7 h-7"></i>
        </button>
      </div>
      <div class="flex-1 overflow-y-auto py-6 px-4 space-y-4">
        <a href="#home" class="mobile-link block px-4 py-3 rounded-xl text-lg font-medium text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors">
          <i data-lucide="home" class="w-5 h-5 inline-block mr-3 mb-1"></i> Home
        </a>
        <a href="#about" class="mobile-link block px-4 py-3 rounded-xl text-lg font-medium text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors">
          <i data-lucide="info" class="w-5 h-5 inline-block mr-3 mb-1"></i> About
        </a>
        <a href="#menu" class="mobile-link block px-4 py-3 rounded-xl text-lg font-medium text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors">
          <i data-lucide="cake" class="w-5 h-5 inline-block mr-3 mb-1"></i> Menu
        </a>
        <a href="#gallery" class="mobile-link block px-4 py-3 rounded-xl text-lg font-medium text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors">
          <i data-lucide="image" class="w-5 h-5 inline-block mr-3 mb-1"></i> Gallery
        </a>
        <a href="login.php" class="mobile-link block px-4 py-3 rounded-xl text-lg font-medium text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors">
          <i data-lucide="user" class="w-5 h-5 inline-block mr-3 mb-1"></i> Admin Login
        </a>
      </div>
      <div class="p-6 border-t border-gray-100 bg-gray-50">
        <a href="pesan.php" class="block w-full text-center py-3.5 bg-pink-500 hover:bg-pink-600 text-white rounded-xl font-bold shadow-lg transition-all transform hover:scale-[1.02]">
          Start Order
        </a>
      </div>
  </div>


  <!-- Hero Section -->
  <section id="home" class="flex items-center justify-center relative min-h-[60vh] bg-cover bg-no-repeat fade-in scroll-animate pt-32 pb-20" style="background-image: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1500&q=80');">
    <div class="absolute inset-0 bg-gradient-to-br from-pink-300/60 via-transparent to-pink-100/70"></div>
    <div class="relative text-center z-10 px-4 slide-in scroll-animate max-w-4xl mx-auto">
      <h1 class="text-5xl md:text-7xl mb-6 text-pink-600 font-bold brand-font drop-shadow-md animate-bounce leading-relaxed py-2">HappyippieCake</h1>
      <div class="flex justify-center mb-8">
        <span class="inline-block bg-white/80 rounded-full px-6 py-3 text-pink-800 text-base md:text-lg font-semibold shadow backdrop-blur brand-font whitespace-nowrap">Premium Cake • Custom & Fresh</span>
      </div>
      <p class="text-xl md:text-2xl text-white drop-shadow font-semibold mb-8 fade-in scroll-animate leading-snug">
        Toko kue premium untuk momen istimewa,<br class="hidden md:block"> fresh & custom setiap hari!
      </p>
      <a href="#menu" class="inline-block px-10 py-4 bg-pink-500 text-white text-lg rounded-full hover:bg-pink-600 shadow-xl font-bold transition brand-font tracking-wide border-2 border-white/60 fade-in scroll-animate transform hover:scale-105">
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
            <?php 
              $imgPath = $menu['gambar'];
              $imgPath = str_replace('\\', '/', $imgPath);
              if(empty($imgPath) || !file_exists($imgPath)) {
                  $imgPath = 'https://dummyimage.com/300x300/e2e8f0/94a3b8.png&text=No+Image';
              }
            ?>
            <img src="<?= htmlspecialchars($imgPath) ?>"
              alt="<?= htmlspecialchars($menu['nama']) ?>"
              loading="lazy"
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
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80" class="rounded-2xl shadow-xl object-cover h-40 w-full card-anim fade-in scroll-animate">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80" class="rounded-2xl shadow-xl object-cover h-40 w-full card-anim fade-in scroll-animate">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80" class="rounded-2xl shadow-xl object-cover h-40 w-full card-anim fade-in scroll-animate">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80" class="rounded-2xl shadow-xl object-cover h-40 w-full card-anim fade-in scroll-animate">
      </div>
    </div>
  </section>

  <!-- Modern Footer -->
  <footer class="bg-gradient-to-t from-pink-900 via-pink-800 to-pink-700 text-white relative pt-20 pb-10 mt-12 overflow-hidden">
    <!-- Decorative curve -->
    <div class="absolute top-0 left-0 w-full overflow-hidden leading-none z-0">
        <svg class="relative block w-full h-[60px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" class="fill-pink-50"></path>
        </svg>
    </div>

    <div class="container mx-auto px-6 relative z-10">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
        <!-- Brand -->
        <div class="col-span-1 md:col-span-1">
          <h2 class="text-3xl font-bold font-['Pacifico'] mb-4 text-pink-200">HappyippieCake</h2>
          <p class="text-pink-100/80 mb-6 leading-relaxed">
            Menghadirkan kebahagiaan di setiap moment spesial Anda dengan kue berkualitas tinggi dan desain yang memukau.
          </p>
          <div class="flex space-x-4">
            <a href="https://instagram.com" target="_blank" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
              <i data-lucide="instagram" class="w-5 h-5"></i>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="text-xl font-bold mb-6 border-b border-pink-500/30 pb-2 inline-block">Quick Links</h3>
          <ul class="space-y-3">
            <li><a href="#home" class="text-pink-100/80 hover:text-white transition-colors flex items-center gap-2"><i data-lucide="chevron-right" class="w-4 h-4"></i> Home</a></li>
            <li><a href="#about" class="text-pink-100/80 hover:text-white transition-colors flex items-center gap-2"><i data-lucide="chevron-right" class="w-4 h-4"></i> About Us</a></li>
            <li><a href="#menu" class="text-pink-100/80 hover:text-white transition-colors flex items-center gap-2"><i data-lucide="chevron-right" class="w-4 h-4"></i> Our Menu</a></li>
            <li><a href="#gallery" class="text-pink-100/80 hover:text-white transition-colors flex items-center gap-2"><i data-lucide="chevron-right" class="w-4 h-4"></i> Gallery</a></li>
          </ul>
        </div>

        <!-- Contact -->
        <div>
          <h3 class="text-xl font-bold mb-6 border-b border-pink-500/30 pb-2 inline-block">Hubungi Kami</h3>
          <ul class="space-y-4">
             <li class="flex items-start gap-3 text-pink-100/80">
                <i data-lucide="map-pin" class="w-5 h-5 mt-1 text-pink-300"></i>
                <span>Jl. Encep Kartawiria No. 12<br>Cimahi, Jawa Barat</span>
             </li>
             <li class="flex items-center gap-3 text-pink-100/80">
                <i data-lucide="phone" class="w-5 h-5 text-pink-300"></i>
                <span>+62 812-3456-7890</span>
             </li>
             <li class="flex items-center gap-3 text-pink-100/80">
                <i data-lucide="mail" class="w-5 h-5 text-pink-300"></i>
                <span>hello@happyippiecake.com</span>
             </li>
          </ul>
        </div>
      </div>

      <div class="border-t border-pink-600/30 pt-8 text-center text-pink-200/60 text-sm">
        <p>&copy; <?= date('Y') ?> HappyippieCake. All rights reserved. Created with ❤️ in Cimahi.</p>
      </div>
    </div>
  </footer>

  <script>
    // Initialize Lucide Icons
    lucide.createIcons();

    // Mobile Menu Logic
    const btn = document.getElementById('mobile-menu-btn');
    const closeBtn = document.getElementById('close-menu-btn');
    const drawer = document.getElementById('mobile-menu-drawer');
    const overlay = document.getElementById('mobile-menu-overlay');

    function toggleMenu() {
        if (drawer.classList.contains('translate-x-full')) {
            // Open
            drawer.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.remove('opacity-0'), 10);
        } else {
            // Close
            drawer.classList.add('translate-x-full');
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }

    btn.addEventListener('click', toggleMenu);
    closeBtn.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);

    // Close menu when clicking a link
    document.querySelectorAll('.mobile-link').forEach(link => {
        link.addEventListener('click', toggleMenu);
    });

    // Navbar Scroll Effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('shadow-md', 'bg-white/90');
        } else {
            navbar.classList.remove('shadow-md');
        }
    });
  </script>

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