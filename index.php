<?php
// Koneksi ke database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=db_menu', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Koneksi gagal: ' . $e->getMessage());
}

// Query untuk mengambil data menu
$sql = "SELECT * FROM menu";
$stmt = $pdo->query($sql);
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HappyippieCake</title>
  <link rel="stylesheet" href="croot.css">
    <script type="module" src="https://cdn.jsdelivr.net/gh/domyid/tracker@main/index.js"></script>

    <!-- Meta Tags untuk Twitter (opsional) -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="CrootSS: CSS Mobile Friendly Responsive Component and Template" />
    <meta name="twitter:description" content="CSS Mobile Friendly Responsive Component and Template" />
    <meta name="twitter:image" content="https://www.do.my.id/crootss.jpeg" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
  <meta name="google-site-verification" content="FxJ_CP9CXLmS6uC-vBopCr_7V31N4wjJiNTdR8mYDPU" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XHQ9K68JXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-XHQ9K68JXX');
</script>
<body class="bg-gray-100">
  <!-- Header -->
  <header class="bg-white shadow-md">
    <header class="bg-white shadow-md fixed top-0 left-0 w-full z-50">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
      <div class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center space-x-1">
        <span>happyi</span>
        <span class="text-pink-500">ppieecake</span>
      </div>
      <!-- Navigation -->
      <nav class="hidden md:flex space-x-6">
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="#">Produk</a>
        <a href="#about-container" class="hover:text-pink-500 font-medium transition cursor-pointer" id="about-btn">About</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="maps.html">Location</a>
        <a class="text-red-600 hover:text-red-800 font-semibold transition duration-300" href="#">For Business</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="login.html">Login</a>
      </nav>

      <script>
        // Tombol About
        const aboutButton = document.getElementById('about-btn');
        
        // Fungsi untuk scroll ke About
        aboutButton.addEventListener('click', (e) => {
          e.preventDefault(); // Mencegah tindakan default anchor
          
          // Elemen target
          const aboutSection = document.getElementById('about-container');
          
          // Gulir ke elemen target dengan animasi halus
          aboutSection.scrollIntoView({
            behavior: 'smooth', // Gulir halus
            block: 'start',     // Posisi elemen setelah scroll
          });
        });
      </script>
      

      <!-- Mobile Menu Button -->
      <button id="mobile-menu-button" class="md:hidden text-gray-600 hover:text-pink-500 focus:outline-none transition duration-300">
        <i class="fas fa-bars"></i>
      </button>
    </div>
    <!-- Mobile Navigation -->
    <div id="mobile-menu" class="md:hidden hidden">
      <nav class="flex flex-col space-y-2 px-6 py-4">
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="#">Produk</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="about.html">About</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="maps.html">Location</a>
        <a class="text-red-600 hover:text-red-800 font-semibold transition duration-300" href="#">For Business</a>
      </nav>
    </div>
  </header>

  <!-- Main Content with Parallax Background -->
<main class="relative w-full py-28">
  <!-- Parallax Background -->
  <div class="absolute top-0 left-0 w-full h-full bg-fixed bg-center bg-cover"
      style="background-image: url('gambar/cake\ utama.jpg'); z-index: -1;"></div>

  <!-- Content Section -->
  <div class="container mx-auto px-4 lg:px-6 flex flex-col items-center relative">
      <div class="flex flex-col md:flex-row items-center space-y-6 md:space-y-0 md:space-x-6 bg-white bg-opacity-90 p-6 rounded-lg shadow-lg">
          <img alt="Two cakes"
              class="w-full md:w-1/3 lg:w-1/4 rounded-lg shadow-lg transform transition duration-500 hover:scale-105 mt-12"
              src="gambar/utama.jpg">
          <div class="text-center md:text-left">
              <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Kue Ulang Tahun, Wedding Cake, &amp;
                  More</h1>
              <p class="text-gray-600 mt-4">Pesan cake, pudding, wedding cake, dan lainnya dengan custom
                  design serta same day delivery.</p>
          </div>
      </div>
  </div>
</main>

  <style>
    /* Styling Global */
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to bottom, #ebeaea, #efeded);
      color: #333;
      line-height: 1.6;
      overflow-x: hidden;
    }

    .about-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 2rem;
      text-align: center;
    }

    h1 {
      font-size: 2.5rem;
      color: #333;
      margin-bottom: 1rem;
      text-transform: uppercase;
    }

    p {
      font-size: 1rem;
      max-width: 800px;
      margin-bottom: 2rem;
      color: #555;
    }

    .about-image {
      width: 100%;
      max-width: 300px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      transform: scale(0.95);
      transition: transform 0.3s ease-in-out;
    }

    .about-image:hover {
      transform: scale(1);
    }

    .values {
      margin-top: 3rem;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 2rem;
    }

    .value-card {
      background: #fff;
      padding: 1.5rem;
      border-radius: 15px;
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 250px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .value-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
    }

    .value-card i {
      font-size: 3rem;
      color: #ff6363;
      margin-bottom: 1rem;
    }

    .value-card h3 {
      font-size: 1.2rem;
      margin-bottom: 0.5rem;
    }

    .value-card p {
      font-size: 0.9rem;
      color: #666;
    }

    /* Responsif */
    @media (max-width: 768px) {
      h1 {
        font-size: 2rem;
      }

      p {
        font-size: 0.9rem;
      }

      .values {
        flex-direction: column;
        gap: 1.5rem;
      }
    }

    footer {
    background-color: #2d2d2d; /* Warna latar belakang footer */
}

.map-section {
    display: flex;
    flex-direction: column;
    align-items: flex-start; /* Menyelaraskan konten peta ke kiri */
}

footer iframe {
    width: 100%;
    height: 200px; /* Tinggi iframe peta */
    border: none;
    border-radius: 10px;
    margin-top: 1rem;
}

/* Responsif */
@media (max-width: 768px) {
    footer {
        grid-template-columns: 1fr; /* Mengubah menjadi satu kolom pada layar kecil */
    }

    .map-section {
        order: 1; /* Memindahkan peta ke bagian bawah */
    }

    footer iframe {
        height: 150px; /* Tinggi iframe peta pada layar kecil */
    }
}
    
  </style>
</head>
<body>

  <div id="about-container" class="about-container">
    <h1>About Us</h1>
    <section class="bg-white-100 py-20">
      <div class="container mx-auto px-6 lg:px-20">
        <div class="flex flex-col md:flex-row items-center md:space-x-8">
          <!-- Left Content -->
          <div class="w-full md:w-1/2">
            <h3 class="text-4xl font-bold text-gray-800">HappyippieeCake</h3>
            <p class="mt-4 text-gray-600 leading-relaxed">
              Kami adalah tim yang berdedikasi untuk membuat momen spesial Anda lebih istimewa dengan kue dan dessert berkualitas tinggi. Dengan bahan-bahan terbaik dan desain yang kreatif, kami hadir untuk memenuhi semua kebutuhan Anda.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
              <div class="flex items-center space-x-4">
                <span class="text-pink-500 text-3xl">
                  <i class="fas fa-birthday-cake"></i>
                </span>
                <div>
                  <h4 class="text-lg font-semibold">Custom Cake</h4>
                  <p class="text-gray-600 text-sm">Desain yang sesuai dengan selera Anda.</p>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <span class="text-pink-500 text-3xl">
                  <i class="fas fa-truck"></i>
                </span>
                <div>
                  <h4 class="text-lg font-semibold">Same Day Delivery</h4>
                  <p class="text-gray-600 text-sm">Pesanan Anda sampai di hari yang sama.</p>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <span class="text-pink-500 text-3xl">
                  <i class="fas fa-heart"></i>
                </span>
                <div>
                  <h4 class="text-lg font-semibold">Bahan Berkualitas</h4>
                  <p class="text-gray-600 text-sm">Kami menggunakan bahan terbaik.</p>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <span class="text-pink-500 text-3xl">
                  <i class="fas fa-palette"></i>
                </span>
                <div class="flex flex-col">
                  <h4 class="text-lg font-semibold text-center">Desain Kreatif</h4>
                  <p class="text-gray-600 text-sm text-left">Setiap kue adalah karya seni.</p>
                </div>
              </div>
            </div>
          </div>
          <!-- Right Content -->
          <div class="w-full md:w-1/2 flex items-center justify-center mt-10 md:mt-0">
            <div class="relative">
              <img src="gambar/cake utama.jpg" alt="About Us" class="rounded-lg shadow-lg">
              <div class="absolute inset-0 bg-opacity-30 rounded-lg"></div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

<!-- Product Section -->
<div class="container mx-auto px-4 lg:px-6">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-xl md:text-2xl font-bold text-gray-800">Pilihan Produk</h2>
    <a href="#" class="text-pink-500 hover:text-pink-700 font-medium transition"></a>
  </div>

  <div class="bg-gray-100 py-8">
    <div class="container mx-auto">
      <h2 class="text-center text-2xl font-bold mb-6">Produk Kami</h2>

      <!-- Scrollable container for all products -->
      <div class="overflow-x-auto">
        <div class="flex space-x-4 pb-4" style="width: max-content;">
          <?php foreach ($menus as $menu): ?>
            <!-- Card -->
            <div class="w-64 bg-white shadow-lg rounded-lg p-4 group flex-shrink-0 transition-transform transform hover:scale-105 hover:shadow-2xl">
              <div class="relative">
                <img src="gambar/<?php echo htmlspecialchars($menu['image']); ?>" alt="<?php echo htmlspecialchars($menu['name']); ?>" class="w-full h-48 object-cover rounded-lg transition-transform transform group-hover:scale-110">
                <span class="absolute top-2 right-2 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold px-2 py-1 rounded shadow animate-pulse">SALE</span>
              </div>
              <h3 class="text-gray-800 font-semibold mt-4 group-hover:text-pink-500 transition"><?php echo htmlspecialchars($menu['name']); ?></h3>
              <div class="mt-2">
                <p class="text-red-600 line-through mb-1">Rp. <?php echo number_format($menu['price'], 0, ',', '.'); ?></p>
                <p class="text-gray-800 font-bold">Rp. <?php echo number_format($menu['price'], 0, ',', '.'); ?></p>
              </div>
              <a class="mt-4 block bg-gradient-to-r from-pink-400 to-red-500 text-white text-center py-2 rounded shadow hover:from-pink-600 hover:to-red-600 transition duration-300" href="pesan.php?id=<?= $menu['id']; ?>" target="_blank">
                Pesan Sekarang
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>


 <!-- Chat Button -->
 <div class="fixed bottom-4 right-4 animate-bounce">
  <a 
    href="https://www.instagram.com/happyippieecake?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" 
    target="_blank" 
    class="flex items-center space-x-2 bg-pink-500 text-white px-4 py-2 rounded-full shadow-lg hover:bg-pink-600 transition duration-300">
    <i class="fab fa-instagram text-lg"></i>
    <span class="font-medium">Follow Us</span>
  </a>
</div>


  <script>
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuButton.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  </script>

<!-- Footer -->
<footer class="bg-gray-800 text-white py-6 mt-12">
  <div class="container mx-auto px-4 lg:px-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"> <!-- Mengubah jumlah kolom menjadi 4 -->
      <!-- Logo -->
      <div class="flex flex-col items-center lg:items-start">
        <img src="gambar/happycake.png" alt="Logo Happy Ippie Cake" class="object-contain mb-4" width="150" />
        <p class="text-xs text-gray-300 text-center lg:text-left mt-2">Your special moment, our sweet touch!</p>
      </div>

      <div>
        <h3 class="text-md font-semibold mb-2">Quick Links</h3>
        <ul class="space-y-1">
          <li><a href="#about-container" class="text-gray-300 hover:text-white transition">About</a></li>
          <li><a href="maps.html" class="text-gray-300 hover:text-white transition">Location</a></li>
          <li>
            <a href="https://wa.me/6285722341788" target="_blank" class="text-gray-300 hover:text-white transition"> Contact Us </a>
          </li>
          <li><a href="review.php" class="text-gray-300 hover:text-white transition">Review</a></li>
        </ul>
      </div>
      
      <!-- Contact Information -->
      <div>
        <h3 class="text-md font-semibold mb-2">Contact</h3>
        <ul class="space-y-2">
          <li><i class="fas fa-phone-alt"></i>
            <a href="tel:+6285722341788" class="text-gray-300 hover:text-white transition ml-2">+62 857-2234-1788</a>
          </li>
          <li><i class="fas fa-envelope"></i>
            <a href="mailto:info@happyippiecake.com" class="text-gray-300 hover:text-white transition ml-2">info@happyippiecake.com</a>
          </li>
        </ul>
      </div>

      <!-- Maps Section -->
      <div class="map-section">
        <h3 class="text-md font-semibold mb-2">Our Location</h3>
        <iframe 
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d495.1070132257533!2d107.53570984005971!3d-6.907804161619513!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e569a8e9d7bb%3A0x142ecf9a09a7f3e7!2shappyippieecake!5e0!3m2!1sid!2sid!4v1732411075230!5m2!1sid!2sid"
          allowfullscreen="" 
          loading="lazy">
        </iframe>
      </div>
    </div>
    <div class="mt-4 border-t border-gray-700 pt-2 text-center text-xs">
      &copy; 2024 HappyippieCake. All rights reserved.
    </div>
  </div>
</footer>


</body>
</html>