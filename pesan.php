<?php
include 'db_connect.php';

$id = $_GET['id']; // Mendapatkan ID menu dari URL
$sql = "SELECT * FROM menu WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$menu = $result->fetch_assoc();

if (!$menu) {
    die("Menu tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script type="module" src="https://cdn.jsdelivr.net/gh/domyid/tracker@main/index.js"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>happyippieecake</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
    }
  </style>
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
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="index.html">Produk</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="index.html">About</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="maps.html">Location</a>
        <a class="text-red-600 hover:text-red-800 font-semibold transition duration-300" href="#">For Business</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="login.html">Login</a>
      </nav>
      
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
    
    <script>
        const menuToggle = document.getElementById("menu-toggle");
        const mobileMenu = document.getElementById("mobile-menu");
    
        menuToggle.addEventListener("click", () => {
            if (mobileMenu.classList.contains("hidden")) {
                mobileMenu.classList.remove("hidden");
                mobileMenu.style.maxHeight = mobileMenu.scrollHeight + "px";
            } else {
                mobileMenu.style.maxHeight = "0px";
                setTimeout(() => mobileMenu.classList.add("hidden"), 300);
            }
        });
    </script>
    

    <?php if ($menu): ?>
        <main class="container mx-auto p-4 lg:p-8">
            <div class="flex flex-col lg:flex-row items-center space-y-6 lg:space-y-0 lg:space-x-8">
                <!-- Image Section -->
                <img src="gambar/<?php echo htmlspecialchars($menu['image']); ?>" alt="<?php echo htmlspecialchars($menu['name']); ?>"
                    class="w-full max-w-md object-cover rounded-lg shadow-lg">

                <!-- Product Details -->
                <div class="text-center lg:text-left">
                    <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($menu['name']); ?></h2>
                    <p class="text-xl text-red-600 font-semibold mt-2">Rp <?php echo number_format($menu['price'], 0, ',', '.'); ?></p>
                    <ul class="list-disc list-inside text-left space-y-2 mt-4">
                        <li><?php echo htmlspecialchars($menu['description']); ?></li>
                    </ul>
                </div>
            </div>
        </main>
    <?php else: ?>
        <p>Menu tidak ditemukan. <a href="index.php">Kembali ke halaman utama</a></p>
    <?php endif; ?>
  <!-- Main Content -->

  <title>Formulir Pesanan</title>
  <style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    form {
        max-width: 400px;
        margin: auto;
        background: #ffffff; /* Warna latar belakang form */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Bayangan yang lebih halus */
        transition: transform 0.3s; /* Efek transisi */
    }

    form:hover {
        transform: scale(1.02); /* Efek zoom saat hover */
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #555; /* Warna label */
    }

    input, button {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #cccccc;
        border-radius: 5px;
        transition: border-color 0.3s;
    }

    input:focus {
        border-color: #e43b9b; /* Warna border saat fokus */
        outline: none;
    }

    button {
        background-color: #e43b9b; /* Warna tombol */
        color: white;
        font-size: 16px;
        cursor: pointer;
        border: none;
        transition: background-color 0.3s, transform 0.3s; /* Transisi untuk hover */
    }

    button:hover {
        background-color: #ef3b9b; /* Warna tombol saat hover */
        transform: translateY(-2px); /* Efek angkat saat hover */
    }

    @media (max-width: 480px) {
        h1 {
            font-size: 24px;
        }
        form {
            padding: 15px;
        }
        input, button {
            padding: 8px;
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
  <h1>Formulir Pesanan</h1>
  <form id="orderForm">
      <label for="name">Nama:</label>
      <input type="text" id="name" name="name" placeholder="Masukkan nama Anda" required>

      <label for="product">Produk:</label>
      <input type="text" id="product" name="product" placeholder="Masukkan nama produk" required>

      <label for="quantity">Jumlah:</label>
      <input type="number" id="quantity" name="quantity" placeholder="Masukkan jumlah produk" required>

      <label for="address">Alamat:</label>
      <input type="text" id="address" name="address" placeholder="Masukkan alamat Anda" required>

      <label for="ucapan">Ucapan:</label>
      <input type="text" id="ucapan" name="ucapan" placeholder="Masukkan ucapan Anda" required>

      <button type="button" onclick="sendToWhatsApp()">Kirim Pesanan</button>
  </form>

<script>
    function sendToWhatsApp() {
        const name = document.getElementById('name').value;
        const product = document.getElementById('product').value;
        const quantity = document.getElementById('quantity').value;
        const address = document.getElementById('address').value;
        const ucapan = document.getElementById('ucapan').value;

        if (name && product && quantity && address && ucapan) {
            // Membuat pesan
            const message = `Halo, saya ingin memesan kue di toko Happyippiecake ini:\n\n` +
                            `Nama: ${name}\n` +
                            `Produk: ${product}\n` +
                            `Jumlah: ${quantity}\n` +
                            `Alamat: ${address} \n` +
                            `ucapan: ${ucapan}`;
            
            // Nomor WhatsApp tujuan (gunakan format internasional tanpa tanda +)
            const phoneNumber = "6285722341788"; // Ganti dengan nomor Anda
            const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
            
            // Membuka WhatsApp
            window.open(url, '_blank');
        } else {
            alert('Harap isi semua data pada formulir.');
        }
    }
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
