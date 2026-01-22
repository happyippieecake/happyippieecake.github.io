<?php
require_once 'db_connect.php';
require_once 'PaymentGateway.php';

// Check if stok_tersedia column exists
$has_stok = $conn->query("SHOW COLUMNS FROM menu LIKE 'stok_tersedia'")->num_rows > 0;
// Only show available items if column exists, otherwise show all
if ($has_stok) {
    $menus = $conn->query("SELECT * FROM menu WHERE stok_tersedia = 1 OR stok_tersedia IS NULL ORDER BY nama ASC");
} else {
    $menus = $conn->query("SELECT * FROM menu ORDER BY nama ASC");
}
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $order = isset($_POST['order']) ? $_POST['order'] : [];
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'whatsapp';
    
    if (!$nama || !$alamat || !$order) {
        $error = "Nama, alamat dan pesanan wajib diisi!";
    } else {
        $total_harga = 0;
        $first_pesanan_id = null;
        
        // Generate generic Order ID for this transaction
        $orderId = PaymentGateway::generateOrderId();
        
        // Calculate total and insert orders
        foreach($order as $menu_id => $jumlah) {
            $menu_id = intval($menu_id); 
            $jumlah = intval($jumlah);
            if ($jumlah > 0) {
                // Insert with order_id
                $stmt = $conn->prepare("INSERT INTO pesanan (nama_pemesan, alamat, menu_id, jumlah, tanggal_pesan, status, order_id) VALUES (?, ?, ?, ?, CURDATE(), 'pending', ?)");
                $stmt->bind_param("ssiis", $nama, $alamat, $menu_id, $jumlah, $orderId);
                $stmt->execute();
                
                if (!$first_pesanan_id) {
                    $first_pesanan_id = $conn->insert_id;
                }
                
                $menu = $conn->query("SELECT nama, harga FROM menu WHERE id=$menu_id")->fetch_assoc();
                $subtotal = $menu['harga'] * $jumlah;
                $total_harga += $subtotal;
            }
        }
        
        // Check payment method
        if ($payment_method == 'whatsapp') {
            // WhatsApp Inquiry Flow - for consultation only
            $inquiry_msg = "Halo Admin HappyippieCake, saya mau tanya/konsultasi tentang pesanan ini:\n\n";
            $inquiry_msg .= "Item yg diminati:\n";
            foreach($order as $menu_id => $jumlah) {
                $menu_id = intval($menu_id); 
                $jumlah = intval($jumlah);
                if ($jumlah > 0) {
                    $menu = $conn->query("SELECT nama FROM menu WHERE id=$menu_id")->fetch_assoc();
                    $inquiry_msg .= "- " . $menu['nama'] . " ($jumlah pcs)\n";
                }
            }
            $inquiry_msg .= "\nApakah stok ready atau bisa custom request?";
            $wa_admin = '6285722341788';
            $wa_url = "https://wa.me/$wa_admin?text=" . urlencode($inquiry_msg);
            header("Location: $wa_url");
            exit;
        } else {
            // Payment Gateway flow (Bank Transfer / QRIS)
            $gateway = new PaymentGateway($conn);
            // $orderId already generated above
            
            // Create payment record
            $paymentId = $gateway->createPayment($orderId, $first_pesanan_id, $total_harga, $payment_method);
            
            if ($paymentId) {
                // Redirect to payment page
                header("Location: payment.php?order_id=" . urlencode($orderId));
                exit;
            } else {
                $error = "Gagal membuat pembayaran. Silakan coba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Pilih & Pesan Kue | HappyippieCake</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', Arial, sans-serif; }
    .brand-font { font-family: 'Inter', system-ui, sans-serif; font-weight: 600; }
    .modal-bg { 
      background: linear-gradient(135deg, rgba(236,72,153,0.95) 0%, rgba(219,39,119,0.98) 50%, rgba(190,24,93,0.95) 100%);
      z-index:99;
      backdrop-filter: blur(8px);
    }
    .modal-box { 
      z-index:100;
      animation: slideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes slideIn {
      from { opacity: 0; transform: translateY(30px) scale(0.95); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .card-hover:hover { box-shadow: 0 8px 32px -8px #fd5e53; }
    .footer-link:hover { color:#fd5e53; transform:translateY(-2px); }
    .input-field {
      border: 2px solid #fce7f3;
      transition: all 0.3s ease;
    }
    .input-field:focus {
      border-color: #ec4899;
      box-shadow: 0 0 0 4px rgba(236,72,153,0.1);
      outline: none;
    }
    .payment-option {
      transition: all 0.3s ease;
      border: 2px solid #fce7f3;
    }
    .payment-option:hover {
      border-color: #f9a8d4;
      background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
      transform: translateX(4px);
    }
    .payment-option.selected {
      border-color: #ec4899;
      background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
      box-shadow: 0 4px 15px rgba(236,72,153,0.2);
    }
    .order-item {
      background: linear-gradient(135deg, #fdf2f8 0%, #fff 100%);
      border: 1px solid #fce7f3;
      transition: all 0.3s ease;
    }
    .order-item:hover {
      box-shadow: 0 4px 12px rgba(236,72,153,0.15);
    }
    .glass-card {
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(20px);
      box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.5);
    }
    .scrollbar-pink::-webkit-scrollbar { width: 6px; }
    .scrollbar-pink::-webkit-scrollbar-track { background: #fdf2f8; border-radius: 10px; }
    .scrollbar-pink::-webkit-scrollbar-thumb { background: #f9a8d4; border-radius: 10px; }
    .scrollbar-pink::-webkit-scrollbar-thumb:hover { background: #ec4899; }
    /* Prevent body scroll when modal is open */
    body.modal-open {
      overflow: hidden;
      position: fixed;
      width: 100%;
      height: 100%;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-white to-pink-100 font-sans">

  <!-- Navbar Hamburger -->
  <nav class="w-full bg-white shadow sticky top-0 z-20">
    <div class="max-w-6xl mx-auto flex justify-between items-center py-3 px-4">
      <a href="index.php" class="text-3xl font-bold text-pink-500 brand-font tracking-wider">HappyippieCake</a>
      <button id="nav-toggle" class="md:hidden focus:outline-none text-pink-600 p-2" aria-label="open menu">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
      </button>
      <ul id="nav-menu" class="hidden md:flex gap-6 font-medium text-gray-700 md:static absolute top-[60px] left-0 w-full bg-white md:w-auto flex-col md:flex-row shadow md:shadow-none">
        <li><a href="index.php#home" class="nav-link block px-4 py-2">Home</a></li>
        <li><a href="index.php#about" class="nav-link block px-4 py-2">About</a></li>
        <li><a href="pesan.php" class="nav-link block px-4 py-2">Menu</a></li>
        <li><a href="index.php#gallery" class="nav-link block px-4 py-2">Gallery</a></li>
      </ul>
    </div>
  </nav>
  <script>
    var navToggle = document.getElementById('nav-toggle');
    var navMenu = document.getElementById('nav-menu');
    navToggle.onclick = function() {
      navMenu.classList.toggle("hidden");
    };
    document.querySelectorAll('#nav-menu a').forEach(link => {
      link.addEventListener('click', function(){
        if(window.innerWidth < 768){
          navMenu.classList.add("hidden");
        }
      });
    });
  </script>

  <div class="max-w-6xl mx-auto py-10 px-2">
    <div class="text-center mb-8">
      <h1 class="text-5xl font-extrabold text-pink-600 mb-2 brand-font">HappyippieCake</h1>
      <p class="text-xl text-pink-700 brand-font tracking-wide mt-2">Pilih & pesan kue istimewa untuk momen spesialmu 🎂</p>
    </div>
    <?php if($error): ?>
      <div class="mb-4 bg-red-100 border-l-4 border-red-400 text-red-700 p-3 rounded"><?= $error ?></div>
    <?php elseif($success): ?>
      <div class="mb-4 bg-green-100 border-l-4 border-green-400 text-green-700 p-3 rounded"><?= $success ?></div>
    <?php endif ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-2 gap-y-6 justify-items-center mb-8">
      <?php foreach($menus as $menu): ?>
        <div class="bg-white rounded-2xl shadow-xl card-hover transition flex flex-col w-64 sm:w-72 md:w-72">
          <img src="<?= htmlspecialchars($menu['gambar']) ?>" alt="<?= htmlspecialchars($menu['nama']) ?>" class="rounded-t-2xl h-40 sm:h-44 w-full object-cover"/>
          <div class="p-4 grow flex flex-col">
            <span class="font-bold text-xl mb-1 text-pink-600 brand-font tracking-wide"><?= htmlspecialchars($menu['nama']) ?></span>
            <span class="text-gray-700 mb-3 text-sm"><?= htmlspecialchars($menu['deskripsi']) ?></span>
            <div class="flex justify-between items-center mt-auto">
              <span class="bg-pink-100 rounded font-bold text-pink-700 px-3 py-1 text-base">Rp<?= number_format($menu['harga'],0,',','.') ?></span>
              <button class="bg-pink-600 text-white rounded px-5 py-1 hover:bg-pink-700 open-modal font-semibold shadow transition brand-font tracking-wide"
                data-id="<?= $menu['id'] ?>"
                data-nama="<?= htmlspecialchars($menu['nama']) ?>"
                data-harga="<?= $menu['harga'] ?>"
                data-gambar="<?= htmlspecialchars($menu['gambar']) ?>"
              >Pesan</button>
            </div>
          </div>
        </div>
      <?php endforeach ?>
    </div>
    <div class="text-center mb-8">
      <span class="italic text-gray-400">* Klik "Pesan" untuk mulai pemesanan, bisa pilih lebih dari satu kue, isi detail, lalu order dengan WhatsApp</span>
    </div>
  </div>

  <!-- Modal Form - Full Screen -->
  <div id="modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="min-h-screen w-full bg-white">
      
      <!-- Header -->
      <div class="bg-gradient-to-r from-pink-600 via-rose-500 to-pink-700 px-6 py-4 sticky top-0 z-10 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
          <div class="flex items-center gap-4">
            <span class="text-3xl brand-font text-white">HappyippieCake</span>
            <span class="hidden md:inline text-pink-200">|</span>
            <span class="hidden md:inline text-white font-medium">Checkout</span>
          </div>
          <button onclick="closeModal()" class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-all text-white">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Main Content -->
      <form method="post" onsubmit="return submitOrder();" class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          
          <!-- Left Column - Order Details (2 cols wide) -->
          <div class="lg:col-span-2 space-y-6">
            
            <!-- Customer Info Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-pink-500 to-rose-500 px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Informasi Pemesan
                </h3>
              </div>
              <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block mb-2 font-semibold text-gray-600 text-sm">Nama Lengkap *</label>
                  <input type="text" name="nama" placeholder="Masukkan nama lengkap" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-800 font-medium focus:border-pink-500 focus:outline-none transition" required>
                </div>
                <div>
                  <label class="block mb-2 font-semibold text-gray-600 text-sm">Alamat Pengiriman *</label>
                  <input type="text" name="alamat" placeholder="Masukkan alamat lengkap" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:border-pink-500 focus:outline-none transition" required>
                </div>
              </div>
            </div>

            <!-- Order Items Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                  </svg>
                  Keranjang Pesanan
                </h3>
                <button type="button" onclick="addOrderField()" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-white text-sm font-semibold transition flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  Tambah Item
                </button>
              </div>
              <div class="p-6">
                <div id="order-list" class="space-y-4"></div>
              </div>
              <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                <div class="flex justify-between items-center">
                  <span class="text-gray-600 font-medium">Total Pembayaran</span>
                  <span id="totalHarga" class="text-3xl font-bold text-pink-600">Rp0</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Payment Methods -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden sticky top-24">
              <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                  </svg>
                  Metode Pembayaran
                </h3>
              </div>
              <div class="p-4 space-y-3">
                
                <!-- Bank Transfer Section -->
                <div class="pt-2">
                  <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Transfer Bank</p>
                  <div class="grid grid-cols-3 gap-2">
                    <!-- BCA -->
                    <label class="block cursor-pointer">
                      <input type="radio" name="payment_method" value="bank_bca" class="peer hidden" checked>
                      <div class="peer-checked:border-blue-500 peer-checked:bg-blue-50 border-2 border-gray-200 rounded-xl p-3 transition-all hover:border-gray-300 text-center h-full flex flex-col items-center justify-center">
                        <div class="h-8 w-full flex items-center justify-center mb-2">
                          <img src="gambar/logo_bca.png" alt="BCA" class="h-full object-contain">
                        </div>
                        <div class="text-xs font-semibold text-gray-700">Bank BCA</div>
                      </div>
                    </label>
                    <!-- Mandiri -->
                    <label class="block cursor-pointer">
                      <input type="radio" name="payment_method" value="bank_mandiri" class="peer hidden">
                      <div class="peer-checked:border-blue-500 peer-checked:bg-blue-50 border-2 border-gray-200 rounded-xl p-3 transition-all hover:border-gray-300 text-center h-full flex flex-col items-center justify-center">
                        <div class="h-8 w-full flex items-center justify-center mb-2">
                          <img src="gambar/logo_mandiri.png" alt="Mandiri" class="h-full object-contain">
                        </div>
                        <div class="text-xs font-semibold text-gray-700">Mandiri</div>
                      </div>
                    </label>
                    <!-- BRI -->
                    <label class="block cursor-pointer">
                      <input type="radio" name="payment_method" value="bank_bri" class="peer hidden">
                      <div class="peer-checked:border-blue-500 peer-checked:bg-blue-50 border-2 border-gray-200 rounded-xl p-3 transition-all hover:border-gray-300 text-center h-full flex flex-col items-center justify-center">
                        <div class="h-8 w-full flex items-center justify-center mb-2">
                          <img src="gambar/logo_bri.png" alt="BRI" class="h-full object-contain">
                        </div>
                        <div class="text-xs font-semibold text-gray-700">Bank BRI</div>
                      </div>
                    </label>
                  </div>
                </div>

                <!-- E-Wallet Section -->
                <div class="pt-2">
                  <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">E-Wallet / QRIS</p>
                  
                  <!-- Scan QRIS Button -->
                  <div id="qris-btn" class="block cursor-pointer" onclick="toggleEwalletOptions()">
                    <input type="radio" name="payment_method" value="qris" class="hidden" id="qris-radio">
                    <div id="qris-card" class="border-2 border-gray-200 rounded-xl p-4 transition-all hover:border-gray-300 flex items-center gap-3">
                      <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shadow-lg flex-shrink-0 border border-gray-100 overflow-hidden">
                        <img src="gambar/logo_gopay.png" class="w-full h-full object-contain p-1" alt="QRIS">
                      </div>
                      <div class="flex-1">
                        <div class="font-bold text-gray-800">Scan QRIS</div>
                        <div id="qris-subtitle" class="text-xs text-gray-500">GoPay, OVO, DANA, ShopeePay</div>
                      </div>
                      <svg id="qris-arrow" class="w-5 h-5 text-gray-400 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                      </svg>
                    </div>
                  </div>
                  
                  <!-- E-Wallet Options (Hidden by default) -->
                  <div id="ewallet-options" class="hidden mt-3 grid grid-cols-4 gap-2 p-3 bg-gray-50 rounded-xl border border-gray-200">
                    <!-- GoPay -->
                    <button type="button" onclick="selectEwalletOption('GoPay')" class="ewallet-opt-btn flex flex-col items-center p-2 rounded-lg hover:bg-white transition flex-1">
                      <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shadow mb-1 border border-gray-100 p-1">
                        <img src="gambar/logo_gopay.png" class="w-full h-full object-contain" alt="GoPay">
                      </div>
                      <span class="text-xs font-medium text-gray-600">GoPay</span>
                    </button>
                    <!-- OVO -->
                    <!-- OVO -->
                    <button type="button" onclick="selectEwalletOption('OVO')" class="ewallet-opt-btn flex flex-col items-center p-2 rounded-lg hover:bg-white transition flex-1">
                      <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shadow mb-1 border border-gray-100 p-1">
                        <img src="gambar/logo_ovo.png" class="w-full h-full object-contain" alt="OVO">
                      </div>
                      <span class="text-xs font-medium text-gray-600">OVO</span>
                    </button>
                    <!-- DANA -->
                    <!-- DANA -->
                    <button type="button" onclick="selectEwalletOption('DANA')" class="ewallet-opt-btn flex flex-col items-center p-2 rounded-lg hover:bg-white transition flex-1">
                      <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shadow mb-1 border border-gray-100 p-1">
                        <img src="gambar/logo_dana.png" class="w-full h-full object-contain" alt="DANA">
                      </div>
                      <span class="text-xs font-medium text-gray-600">DANA</span>
                    </button>
                    <!-- ShopeePay -->
                    <!-- ShopeePay -->
                    <button type="button" onclick="selectEwalletOption('ShopeePay')" class="ewallet-opt-btn flex flex-col items-center p-2 rounded-lg hover:bg-white transition flex-1">
                      <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shadow mb-1 border border-gray-100 p-1">
                        <img src="gambar/logo_shopeepay.png" class="w-full h-full object-contain" alt="ShopeePay">
                      </div>
                      <span class="text-xs font-medium text-gray-600">ShopeePay</span>
                    </button>
                  </div>
                </div>

              </div>
              
              <!-- Submit Button -->
              <div class="p-4 bg-gray-50 border-t border-gray-100">
                <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 py-4 rounded-xl text-white font-bold text-lg shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  Buat Pesanan
                </button>
                
                <!-- WhatsApp Consultation Button -->
                <button type="button" onclick="openWhatsAppConsultation()" class="w-full mt-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 py-3 rounded-xl text-white font-bold text-base shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                  Tanya/Konsultasi via WA
                </button>
                
                <p class="text-center text-xs text-gray-400 mt-3 flex items-center justify-center gap-1">
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                  Transaksi aman & terenkripsi
                </p>
              </div>
            </div>
          </div>
          
        </div>
      </form>
    </div>
  </div>

  <!-- Footer Modern -->
  <footer class="bg-gradient-to-t from-pink-700 via-pink-500 to-pink-400 text-white pt-10 pb-5 shadow-xl mt-20">
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
        <a href="https://wa.me/6285722341788" target="_blank" class="footer-link" title="WhatsApp">
          <svg class="inline" width="26" height="26" fill="currentColor"><path d="M2 12A10 10 0 0012 22h.043C8.81 22 5.997 20.094 4.257 17.489a.995.995 0 01.156-1.221l1.134-1.12A1.004 1.004 0 016.5 15.05c.9.62 1.867 1.059 2.913 1.285 1.046.227 2.137.228 3.195.002a7.993 7.993 0 001.372-.38c.322-.113.684-.011.883.245l1.127 1.087a.997.997 0 01.157 1.221C18.004 20.106 15.19 22 12.043 22H12A10 10 0 002 12zm10-8a8 8 0 110 16A8 8 0 012 12a8 8 0 0110-8zm-1 9.5a1 1 0 00-1 1V16a1 1 0 102 0v-3.5a1 1 0 00-1-1zm0-2a1 1 0 100 2 1 1 0 000-2z"/></svg>
        </a>
        <a href="#" class="footer-link" title="Facebook">
          <svg class="inline" width="26" height="26" fill="currentColor"><path d="M22.675 0H1.325C.595 0 0 .595 0 1.326V22.675c0 .73.595 1.325 1.325 1.325h11.495v-9.294H9.691V11.09h3.129V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.243l-1.917.001c-1.504 0-1.797.715-1.797 1.762v2.312h3.587l-.467 3.613h-3.12v9.294h6.116c.73 0 1.325-.595 1.325-1.326V1.325C24 .595 23.405 0 22.675 0"/></svg>
        </a>
      </div>
    </div>
    <div class="text-center text-lg pt-4 text-white/80 font-light brand-font">Serving Joy & Elegance in Every Slice</div>
  </footer>
  <script>
    // Semua menu
    const menus = [
      <?php
        $result = $conn->query("SELECT id, nama, harga, gambar FROM menu ORDER BY nama ASC");
        foreach($result as $row){
          echo "{id:".$row['id'].",nama:'".addslashes($row['nama'])."',harga:".$row['harga'].",gambar:'".addslashes($row['gambar'])."'},";
        }
      ?>
    ];

    // Modal logic
    let orderFields = [];
    let scrollPosition = 0;
    
    function openModal(menu) {
      // Save scroll position and prevent body scroll
      scrollPosition = window.pageYOffset;
      document.body.classList.add('modal-open');
      document.body.style.top = `-${scrollPosition}px`;
      
      document.querySelector('#modal form').reset();
      orderFields = [menu];
      renderOrderFields();
      document.getElementById('modal').classList.remove('hidden');
    }
    
    function closeModal() {
      // Restore body scroll
      document.body.classList.remove('modal-open');
      document.body.style.top = '';
      window.scrollTo(0, scrollPosition);
      
      document.getElementById('modal').classList.add('hidden');
      orderFields = [];
    }
    function renderOrderFields() {
      let html = ''; let totalHarga = 0;
      orderFields.forEach((itm, idx) => {
        html += `<div class="bg-gray-50 rounded-xl p-4 flex flex-col md:flex-row items-start md:items-center gap-4 border border-gray-200 hover:border-pink-300 transition">
          <img src="${itm.gambar}" class="w-20 h-20 rounded-xl object-cover shadow-md flex-shrink-0" alt="${itm.nama}">
          <div class="flex-1 w-full md:w-auto">
            <select name="order[${itm.id}]" onchange="changeOrderMenu(${idx}, this.value)" class="w-full bg-white border-2 border-gray-200 rounded-lg px-4 py-3 text-gray-800 font-semibold focus:border-pink-500 focus:outline-none transition">
              ${menus.map(menu =>
                `<option value="${menu.id}" ${menu.id == itm.id ? 'selected':''}>${menu.nama} - Rp${menu.harga.toLocaleString()}</option>`
              ).join('')}
            </select>
          </div>
          <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
            <div class="flex items-center bg-white rounded-lg border-2 border-gray-200 overflow-hidden">
              <button type="button" onclick="changeOrderQty(${idx}, Math.max(1, ${itm.jumlah||1}-1))" class="w-10 h-10 text-pink-600 hover:bg-pink-50 transition font-bold text-lg">−</button>
              <input type="number" min="1" max="20" value="${itm.jumlah||1}" onchange="changeOrderQty(${idx},this.value)" class="w-14 h-10 text-center font-bold text-lg border-x border-gray-200 focus:outline-none">
              <button type="button" onclick="changeOrderQty(${idx}, Math.min(20, ${itm.jumlah||1}+1))" class="w-10 h-10 text-pink-600 hover:bg-pink-50 transition font-bold text-lg">+</button>
            </div>
            <span class="font-bold text-pink-600 text-lg min-w-[100px] text-right">Rp${(itm.harga*(itm.jumlah||1)).toLocaleString()}</span>
            <button type="button" onclick="removeOrderField(${idx})" class="w-10 h-10 rounded-full bg-red-100 hover:bg-red-500 text-red-500 hover:text-white flex items-center justify-center transition" title="Hapus">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>`;
        totalHarga += itm.harga*(itm.jumlah||1);
      });
      document.getElementById('order-list').innerHTML = html;
      document.getElementById('totalHarga').innerText = 'Rp'+totalHarga.toLocaleString();
    }
    function addOrderField() {
      orderFields.push({id:menus[0].id, nama:menus[0].nama, harga:menus[0].harga, gambar:menus[0].gambar, jumlah:1});
      renderOrderFields();
    }
    function removeOrderField(idx) {
      orderFields.splice(idx,1); renderOrderFields();
    }
    function changeOrderQty(idx, val){
      orderFields[idx].jumlah = parseInt(val)||1; renderOrderFields();
    }
    function changeOrderMenu(idx, val){
      const menu = menus.find(m=>m.id==val);
      orderFields[idx].id = menu.id; orderFields[idx].nama = menu.nama; orderFields[idx].harga = menu.harga; orderFields[idx].gambar = menu.gambar;
      renderOrderFields();
    }
    document.querySelectorAll('.open-modal').forEach(btn => {
      btn.addEventListener('click', function(){
        const menu = menus.find(m=>m.id==this.dataset.id);
        openModal({...menu, jumlah:1});
      });
    });
    function submitOrder() {
      if (!orderFields.length) return false;
      for (let itm of orderFields) {
        let f = document.createElement('input');
        f.type = 'hidden';
        f.name = `order[${itm.id}]`;
        f.value = itm.jumlah||1;
        document.querySelector('#modal form').appendChild(f);
      }
      return true;
    }
    
    // Toggle e-wallet options visibility
    function toggleEwalletOptions() {
      const options = document.getElementById('ewallet-options');
      const arrow = document.getElementById('qris-arrow');
      const qrisRadio = document.getElementById('qris-radio');
      const qrisCard = document.getElementById('qris-card');
      
      // Check the radio button
      qrisRadio.checked = true;
      
      // Style the card as selected
      qrisCard.classList.add('border-purple-500', 'bg-purple-50');
      
      // Toggle visibility
      if (options.classList.contains('hidden')) {
        options.classList.remove('hidden');
        arrow.style.transform = 'rotate(180deg)';
      } else {
        options.classList.add('hidden');
        arrow.style.transform = 'rotate(0deg)';
      }
    }
    
    // Select specific e-wallet option
    function selectEwalletOption(ewallet) {
      // Visual feedback - highlight selected e-wallet
      document.querySelectorAll('.ewallet-opt-btn').forEach(btn => {
        btn.classList.remove('bg-white', 'ring-2', 'ring-purple-400');
      });
      event.currentTarget.classList.add('bg-white', 'ring-2', 'ring-purple-400');
      
      // Update subtitle to show selected e-wallet
      document.getElementById('qris-subtitle').textContent = 'Bayar dengan ' + ewallet;
    }
    
    // Open WhatsApp for consultation
    function openWhatsAppConsultation() {
      let inquiry_msg = "Halo Admin HappyippieCake, saya mau tanya/konsultasi tentang pesanan ini:\n\nItem yg diminati:\n";
      orderFields.forEach(itm => {
        inquiry_msg += "- " + itm.nama + " (" + (itm.jumlah||1) + " pcs)\n";
      });
      inquiry_msg += "\nApakah stok ready atau bisa custom request?";
      const wa_admin = '6285722341788';
      window.open("https://wa.me/" + wa_admin + "?text=" + encodeURIComponent(inquiry_msg), '_blank');
    }
  </script>
</body>
</html>
