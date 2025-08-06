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
  <header class="bg-white shadow-md fixed top-0 left-0 w-full z-50">
<div class="w-full flex justify-between items-center py-4 px-6">
      <div class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center space-x-1">
        <span>happyi</span>
        <span class="text-pink-500">ppieecake</span>
      </div>
      <!-- Navigation -->
      <nav class="hidden md:flex space-x-6">
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="index.php">Produk</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="index.php">About</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="maps.html">Location</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300" href="login.html">Login</a>
      </nav>
      <!-- Mobile Menu Button -->
<div class="flex justify-end md:hidden">
  <button id="mobile-menu-button" class="text-gray-600 hover:text-pink-500 focus:outline-none bg-transparent p-0 transition duration-300">
    <i class="fas fa-bars text-xl"></i>
  </button>
</div>


      </div>
    </div>
    <!-- Mobile Navigation -->
    <div id="mobile-menu" class="md:hidden hidden w-full absolute left-0 top-16 z-50">
      <nav class="flex flex-col space-y-3 px-6 py-4 bg-white">
        <a class="text-gray-600 hover:text-pink-500 transition duration-300 py-2 border-b border-gray-100" href="index.php">Produk</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300 py-2 border-b border-gray-100" href="index.php">About</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300 py-2 border-b border-gray-100" href="maps.html">Location</a>
        <a class="text-gray-600 hover:text-pink-500 transition duration-300 py-2" href="login.html">Login</a>
      </nav>
    </div>
  </header>
    
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
          mobileMenuButton.onclick = function(e) {
            e.stopPropagation();
            mobileMenu.classList.toggle('hidden');
            const icon = this.querySelector('i');
            if (mobileMenu.classList.contains('hidden')) {
              icon.classList.replace('fa-times', 'fa-bars');
            } else {
              icon.classList.replace('fa-bars', 'fa-times');
            }
          };

          document.addEventListener('click', function(e) {
            if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target)) {
              if (!mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                mobileMenuButton.querySelector('i').classList.replace('fa-times', 'fa-bars');
              }
            }
          });

          const mobileLinks = mobileMenu.querySelectorAll('a');
          mobileLinks.forEach(function(link) {
            link.onclick = function() {
              mobileMenu.classList.add('hidden');
              mobileMenuButton.querySelector('i').classList.replace('fa-times', 'fa-bars');
            };
          });
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

      <label for="product">Produk 1:</label>
      <select id="product" name="product" class="mb-2" onchange="updateTotal()" required>
        <option value="<?php echo htmlspecialchars($menu['name']); ?>" data-price="<?php echo htmlspecialchars($menu['price']); ?>" selected><?php echo htmlspecialchars($menu['name']); ?> (Rp <?php echo number_format($menu['price'], 0, ',', '.'); ?>)</option>
        <?php
        $sql2 = "SELECT * FROM menu WHERE id != ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        while ($row = $result2->fetch_assoc()): ?>
          <option value="<?php echo htmlspecialchars($row['name']); ?>" data-price="<?php echo htmlspecialchars($row['price']); ?>"><?php echo htmlspecialchars($row['name']); ?> (Rp <?php echo number_format($row['price'], 0, ',', '.'); ?>)</option>
        <?php endwhile; ?>
      </select>

      <label for="quantity">Jumlah Produk 1:</label>
      <input type="number" id="quantity" name="quantity" placeholder="Masukkan jumlah produk" min="1" value="1" required oninput="updateTotal()">

      <label for="product2">Produk 2 (opsional):</label>
      <select id="product2" name="product2" class="mb-2" onchange="updateTotal()">
        <option value="" data-price="0">-- Pilih Produk Kedua --</option>
        <?php
        $sql3 = "SELECT * FROM menu";
        $result3 = $conn->query($sql3);
        while ($row = $result3->fetch_assoc()): ?>
          <option value="<?php echo htmlspecialchars($row['name']); ?>" data-price="<?php echo htmlspecialchars($row['price']); ?>"><?php echo htmlspecialchars($row['name']); ?> (Rp <?php echo number_format($row['price'], 0, ',', '.'); ?>)</option>
        <?php endwhile; ?>
      </select>

      <label for="quantity2">Jumlah Produk 2:</label>
      <input type="number" id="quantity2" name="quantity2" placeholder="Masukkan jumlah produk kedua" min="1" value="1" oninput="updateTotal()">

      <label for="total">Total Harga:</label>
      <input type="text" id="total" name="total" value="<?php echo htmlspecialchars($menu['price']); ?>" readonly>

      <label for="address">Alamat:</label>
      <input type="text" id="address" name="address" placeholder="Masukkan alamat Anda" required>

      <label for="ucapan">Ucapan:</label>
      <input type="text" id="ucapan" name="ucapan" placeholder="Masukkan ucapan Anda" required>

      <label for="notelfon">No Hp:</label>
      <input type="text" id="notelfon" name="notelfon" placeholder="Masukkan notelfon anda" required>

      <button type="button" onclick="sendToWhatsApp()">Kirim Pesanan</button>
  </form>

<script>
    function updateTotal() {
        // Produk 1
        var productSelect = document.getElementById('product');
        var price1 = parseInt(productSelect.options[productSelect.selectedIndex].getAttribute('data-price')) || 0;
        var quantity1 = parseInt(document.getElementById('quantity').value) || 1;
        // Produk 2
        var product2Select = document.getElementById('product2');
        var price2 = 0;
        var quantity2 = 0;
        if (product2Select.value !== "") {
            price2 = parseInt(product2Select.options[product2Select.selectedIndex].getAttribute('data-price')) || 0;
            quantity2 = parseInt(document.getElementById('quantity2').value) || 1;
        }
        var total = (price1 * quantity1) + (price2 * quantity2);
        document.getElementById('total').value = total.toLocaleString('id-ID');
    }

    function sendToWhatsApp() {
        const name = document.getElementById('name').value;
        const product = document.getElementById('product').value;
        const quantity = document.getElementById('quantity').value;
        const product2 = document.getElementById('product2').value;
        const quantity2 = document.getElementById('quantity2').value;
        const total = document.getElementById('total').value;
        const address = document.getElementById('address').value;
        const ucapan = document.getElementById('ucapan').value;
        const notelfon = document.getElementById('notelfon').value;

        if (name && product && quantity && address && ucapan && notelfon) {
            let pesanProduk = `Produk 1: ${product} (Jumlah: ${quantity})`;
            if (product2) {
                pesanProduk += `\nProduk 2: ${product2} (Jumlah: ${quantity2})`;
            }
            // Membuat pesan
            const message = `Halo, saya ingin memesan kue di toko Happyippiecake:\n\n` +
                            `Nama: ${name}\n` +
                            `${pesanProduk}\n` +
                            `Total Harga: Rp ${total}\n` +
                            `Alamat: ${address}\n` +
                            `Ucapan: ${ucapan}\n` +
                            `No Hp: ${notelfon}`;
            const phoneNumber = "6285722341788";
            const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
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
