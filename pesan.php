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
  <header class="bg-white shadow">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
      <!-- Logo -->
      <div class="flex items-center space-x-2 text-xl font-bold">
        <span>happyi</span>
        <span class="text-pink-500">ppieecake</span>
      </div>

      <!-- Navigation -->
      <nav class="hidden md:flex space-x-6">
        <a class="text-gray-600 hover:text-gray-800 hover:underline" href="index.html">Produk</a>
        <a class="text-gray-600 hover:text-gray-800 hover:underline" href="index.html">About</a>
        <a class="text-gray-600 hover:text-gray-800 hover:underline" href="maps.html">Location</a>
        <a class="text-red-600 hover:text-red-800 font-semibold" href="#">For Business</a>
      </nav>

      <!-- Menu Icon (Mobile) -->
      <div class="md:hidden">
        <button id="menu-toggle" class="text-gray-600">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg max-h-0 overflow-hidden transition-all duration-300">
        <a class="block px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100" href="index.html">Produk</a>
        <a class="block px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100" href="#">Momen</a>
        <a class="block px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100" href="maps.html">Location</a>
        <a class="block px-4 py-2 text-red-600 hover:text-red-800" href="#">For Business</a>
    </div>

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
    font-family: Arial, sans-serif;
    margin: 20px;
    background-color: #f4f4f4;
    color: #333;
}
h1 {
    text-align: center;
    color: #000000;
    margin-bottom: 20px;
}
form {
    max-width: 400px;
    margin: auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
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
    border-color: #e43b9b;
    outline: none;
}
button {
    background-color: #e43b9b;
    color: white;
    font-size: 16px;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s;
}
button:hover {
    background-color: #ef3b9b;
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
            const message = `Halo, saya ingin memesan kue:\n\n` +
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
<footer class="bg-gray-800 text-white py-12 mt-12">
  <div class="container mx-auto px-4 lg:px-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <!-- Logo -->
      <div class="flex flex-col items-center lg:items-start">
        <img src="gambar/happycake.png" alt="Logo Happy Ippie Cake" class="object-contain mb-6" width="200" />
        <p class="text-sm text-gray-300 text-center lg:text-left mt-2">Your special moment, our sweet touch!</p>
      </div>

      <!-- Quick Links -->
      <div>
        <h3 class="text-lg font-bold mb-4">Quick Links</h3>
        <ul class="space-y-2">
          <a href="#about-container" class="font-medium transition cursor-pointer" id="about-btn">About</a>
          <li><a href="maps.html" class="text-gray-300 hover:text-white transition">Location</a></li>
          <li><a href="contact.html" class="text-gray-300 hover:text-white transition">Contact Us</a></li>
        </ul>
      </div>

      <!-- Contact Information -->
      <div>
        <h3 class="text-lg font-bold mb-4">Contact</h3>
        <ul class="space-y-3">
          <li><i class="fas fa-phone-alt"></i>
            <a href="tel:+6285722341788" class="text-gray-300 hover:text-white transition ml-2">+62 857-2234-1788</a>
          </li>
          <li><i class="fas fa-envelope"></i>
            <a href="mailto:info@happyippiecake.com" class="text-gray-300 hover:text-white transition ml-2">info@happyippiecake.com</a>
          </li>
          <li><i class="fas fa-map-marker-alt"></i>
            <span class="text-gray-300 ml-2">Jl. Nanjung No.25, RT.02/RW.13, Utama, Kec. Cimahi Sel., Kota Cimahi, Jawa Barat 40533</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="mt-8 border-t border-gray-700 pt-4 text-center text-sm">
      <p>&copy; 2024 HappyippieCake. All rights reserved.</p>
    </div>
  </div>
</footer>
</body>
</html>