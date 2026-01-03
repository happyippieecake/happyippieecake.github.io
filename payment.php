<?php
require_once 'db_connect.php';
require_once 'PaymentGateway.php';

$error = '';
$success = '';
$payment = null;
$orderData = null;

// Get order_id from URL
$orderId = isset($_GET['order_id']) ? $_GET['order_id'] : '';

if (!$orderId) {
    header('Location: pesan.php');
    exit;
}

// Get payment data
$gateway = new PaymentGateway($conn);
$payment = $gateway->getPaymentByOrderId($orderId);

if (!$payment) {
    $error = "Pesanan tidak ditemukan!";
}

// Get pesanan data
if ($payment) {
    $pesananId = $payment['pesanan_id'];
    $pesananResult = $conn->query("SELECT * FROM pesanan WHERE id = $pesananId");
    $orderData = $pesananResult->fetch_assoc();
}

// Handle payment method selection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['select_method'])) {
    $method = $_POST['payment_method'] ?? '';
    
    if (PaymentGateway::isValidPaymentMethod($method)) {
        // Update payment method
        $stmt = $conn->prepare("UPDATE payments SET payment_method = ? WHERE order_id = ?");
        $stmt->bind_param("ss", $method, $orderId);
        $stmt->execute();
        
        // Refresh payment data
        $payment = $gateway->getPaymentByOrderId($orderId);
        $success = "Metode pembayaran berhasil dipilih!";
    } else {
        $error = "Metode pembayaran tidak valid!";
    }
}

// Handle bukti transfer upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_bukti'])) {
    if (isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['bukti_transfer']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newName = 'bukti_' . $orderId . '_' . time() . '.' . $ext;
            $uploadPath = 'uploads/' . $newName;
            
            if (move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], $uploadPath)) {
                $gateway->uploadBuktiTransfer($payment['id'], $uploadPath);
                $success = "Bukti transfer berhasil diupload! Admin akan mengkonfirmasi pembayaran Anda.";
                $payment = $gateway->getPaymentByOrderId($orderId);
            } else {
                $error = "Gagal mengupload file!";
            }
        } else {
            $error = "Format file tidak didukung! Gunakan JPG, PNG, atau GIF.";
        }
    } else {
        $error = "Silakan pilih file bukti transfer!";
    }
}

$bankAccounts = PaymentGateway::getBankAccounts();
$qrisData = PaymentGateway::getQrisData();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Pembayaran | HappyippieCake</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', Arial, sans-serif;}
    .brand-font { font-family: 'Inter', system-ui, sans-serif; font-weight: 600;}
    .glass { background: rgba(255,255,255,0.92); backdrop-filter: blur(10px); }
    .payment-card { transition: all 0.3s ease; border: 2px solid transparent; }
    .payment-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px -8px rgba(253,94,83,0.25); }
    .payment-card.selected { border-color: #ec4899; background: linear-gradient(135deg, #fdf2f8, #fce7f3); }
    .copy-btn:active { transform: scale(0.95); }
    .qris-glow { animation: glow 2s ease-in-out infinite alternate; }
    @keyframes glow {
      from { box-shadow: 0 0 20px rgba(236, 72, 153, 0.3); }
      to { box-shadow: 0 0 40px rgba(236, 72, 153, 0.6); }
    }
  </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-white to-pink-100 min-h-screen">

  <!-- Navbar -->
  <nav class="w-full bg-white shadow sticky top-0 z-20">
    <div class="max-w-6xl mx-auto flex justify-between items-center py-3 px-4">
      <a href="index.php" class="text-2xl font-bold text-pink-500 brand-font">HappyippieCake</a>
      <ul class="flex gap-6 font-medium text-gray-700">
        <li><a href="index.php" class="hover:text-pink-500 transition">Home</a></li>
        <li><a href="pesan.php" class="hover:text-pink-500 transition">Menu</a></li>
      </ul>
    </div>
  </nav>

  <div class="max-w-4xl mx-auto py-10 px-4">
    
    <!-- Header -->
    <div class="text-center mb-10">
      <h1 class="text-4xl font-bold text-pink-600 brand-font mb-3">Pembayaran</h1>
      <p class="text-gray-600">Selesaikan pembayaran untuk pesanan Anda</p>
    </div>

    <?php if($error): ?>
      <div class="mb-6 bg-red-100 border-l-4 border-red-400 text-red-700 p-4 rounded-lg shadow"><?= htmlspecialchars($error) ?></div>
    <?php endif ?>

    <?php if($success): ?>
      <div class="mb-6 bg-green-100 border-l-4 border-green-400 text-green-700 p-4 rounded-lg shadow"><?= htmlspecialchars($success) ?></div>
    <?php endif ?>

    <?php if($payment): ?>
    
    <!-- Order Summary -->
    <div class="glass rounded-2xl shadow-xl p-6 mb-8 border-t-4 border-pink-400">
      <h2 class="text-xl font-bold text-pink-700 mb-4 flex items-center gap-2">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Ringkasan Pesanan
      </h2>
      <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
          <span class="text-gray-500">Order ID:</span>
          <span class="font-bold text-pink-600 ml-2"><?= htmlspecialchars($payment['order_id']) ?></span>
        </div>
        <div>
          <span class="text-gray-500">Status:</span>
          <span class="ml-2 px-3 py-1 rounded-full text-xs font-bold 
            <?= $payment['status'] == 'confirmed' ? 'bg-green-100 text-green-700' : 
               ($payment['bukti_transfer'] ? 'bg-blue-100 text-blue-700' : 
               ($payment['status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700')) ?>">
            <?= $payment['status'] == 'confirmed' ? 'Confirmed' : ($payment['bukti_transfer'] ? 'Sudah Bayar' : ucfirst($payment['status'])) ?>
          </span>
        </div>
        <div>
          <span class="text-gray-500">Total Pembayaran:</span>
          <span class="font-bold text-2xl text-pink-600 ml-2"><?= PaymentGateway::formatRupiah($payment['amount']) ?></span>
        </div>
        <?php if($orderData): ?>
        <div>
          <span class="text-gray-500">Nama Pemesan:</span>
          <span class="font-semibold ml-2"><?= htmlspecialchars($orderData['nama_pemesan'] ?? '-') ?></span>
        </div>
        <?php endif ?>
      </div>
    </div>

    <?php if($payment['status'] == 'pending'): ?>
    
    <!-- Unified Payment Method Selection -->
    <div class="glass rounded-2xl shadow-xl p-6 mb-8">
      <h2 class="text-xl font-bold text-pink-700 mb-6 flex items-center gap-2">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        Pilih Metode Pembayaran
      </h2>

      <!-- Payment Type Tabs -->
      <div class="flex gap-2 mb-6">
        <button type="button" onclick="switchPaymentTab('bank')" id="tab-bank" 
          class="flex-1 py-3 px-4 rounded-xl font-semibold transition-all text-center
          <?= strpos($payment['payment_method'] ?? '', 'bank') !== false ? 'bg-blue-500 text-white shadow-lg' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
          <svg class="w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
          Transfer Bank
        </button>
        <button type="button" onclick="switchPaymentTab('qris')" id="tab-qris"
          class="flex-1 py-3 px-4 rounded-xl font-semibold transition-all text-center
          <?= ($payment['payment_method'] ?? '') == 'qris' ? 'bg-purple-500 text-white shadow-lg' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
          <svg class="w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
          E-Wallet / QRIS
        </button>
      </div>

      <form method="post" id="methodForm">
        <input type="hidden" name="select_method" value="1">
        
        <!-- Bank Transfer Panel -->
        <div id="panel-bank" class="<?= ($payment['payment_method'] ?? '') == 'qris' ? 'hidden' : '' ?>">
          <div class="grid grid-cols-3 gap-4">
            <?php foreach($bankAccounts as $key => $bank): ?>
            <label class="payment-card glass rounded-xl p-4 cursor-pointer block <?= $payment['payment_method'] == 'bank_'.$key ? 'selected' : '' ?>">
              <input type="radio" name="payment_method" value="bank_<?= $key ?>" class="sr-only" 
                <?= $payment['payment_method'] == 'bank_'.$key ? 'checked' : '' ?>>
              <div class="text-center h-full flex flex-col items-center justify-center">
                <div class="h-8 w-full flex items-center justify-center mb-2">
                  <img src="gambar/logo_<?= $key ?>.png" alt="<?= strtoupper($key) ?>" class="h-full object-contain">
                </div>
                <div class="font-semibold text-gray-800 text-sm"><?= $bank['bank_name'] ?></div>
              </div>
            </label>
            <?php endforeach ?>
          </div>
        </div>

        <!-- QRIS Panel -->
        <div id="panel-qris" class="<?= ($payment['payment_method'] ?? '') != 'qris' ? 'hidden' : '' ?>">
          <label class="payment-card glass rounded-xl p-4 cursor-pointer block <?= $payment['payment_method'] == 'qris' ? 'selected' : '' ?>">
            <input type="radio" name="payment_method" value="qris" class="sr-only"
              <?= $payment['payment_method'] == 'qris' ? 'checked' : '' ?>>
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 flex items-center justify-center bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
              </div>
              <div>
                <div class="font-semibold text-gray-800">QRIS - Semua E-Wallet</div>
                <div class="text-sm text-gray-500">GoPay, OVO, DANA, ShopeePay, LinkAja</div>
              </div>
            </div>
          </label>
        </div>

        <button type="submit" class="mt-6 w-full bg-gradient-to-r from-pink-500 to-pink-600 text-white py-4 rounded-xl font-bold hover:from-pink-600 hover:to-pink-700 shadow-lg transition flex items-center justify-center gap-2">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          Konfirmasi Metode Pembayaran
        </button>
      </form>
    </div>

    <!-- Payment Instructions -->
    <?php if($payment['payment_method']): ?>
    <div class="glass rounded-2xl shadow-xl p-6 mb-8 border-t-4 border-green-400">
      <h2 class="text-xl font-bold text-green-700 mb-4">Instruksi Pembayaran</h2>
      
      <?php if(strpos($payment['payment_method'], 'bank_') === 0): ?>
        <?php 
        $selectedBank = str_replace('bank_', '', $payment['payment_method']);
        $bankInfo = $bankAccounts[$selectedBank] ?? null;
        ?>
        <?php if($bankInfo): ?>
        <div class="bg-gradient-to-r from-pink-50 to-white rounded-xl p-6 mb-6">
          <div class="text-center mb-4">
            <div class="inline-block bg-white px-6 py-3 rounded-xl shadow-md border border-gray-100">
              <img src="gambar/logo_<?= $selectedBank ?>.png" alt="<?= strtoupper($selectedBank) ?>" class="h-12 object-contain">
            </div>
          </div>
          <div class="text-center space-y-3">
            <div>
              <span class="text-gray-500 text-sm">Nomor Rekening:</span>
              <div class="flex items-center justify-center gap-3 mt-1">
                <span class="text-3xl font-bold text-gray-800 tracking-wider" id="accNumber"><?= $bankInfo['account_number'] ?></span>
                <button onclick="copyToClipboard('<?= $bankInfo['account_number'] ?>')" 
                  class="copy-btn bg-pink-100 hover:bg-pink-200 text-pink-600 px-3 py-2 rounded-lg transition" title="Salin">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
              </div>
            </div>
            <div>
              <span class="text-gray-500 text-sm">Atas Nama:</span>
              <div class="font-semibold text-lg text-gray-800"><?= $bankInfo['account_name'] ?></div>
            </div>
            <div>
              <span class="text-gray-500 text-sm">Jumlah Transfer:</span>
              <div class="font-bold text-2xl text-pink-600"><?= PaymentGateway::formatRupiah($payment['amount']) ?></div>
            </div>
          </div>
        </div>
        
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-6">
          <p class="text-yellow-800 text-sm">
            <strong>Penting:</strong> Transfer sesuai nominal yang tertera agar pembayaran dapat diverifikasi otomatis.
          </p>
        </div>
        <?php endif ?>
        
      <?php elseif($payment['payment_method'] == 'qris'): ?>
        <!-- E-Wallet Selection -->
        <div class="mb-6">
          <h3 class="font-semibold text-gray-700 mb-4 text-center">Pilih E-Wallet untuk Pembayaran</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <!-- GoPay -->
            <button type="button" onclick="selectEwallet('gopay')" id="btn-gopay" class="ewallet-btn bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-green-500 transition focus:outline-none flex flex-col items-center justify-center">
              <div class="h-8 w-full flex items-center justify-center mb-2">
                <img src="gambar/logo_gopay.png" alt="GoPay" class="h-full object-contain">
              </div>
              <div class="font-semibold text-gray-700">GoPay</div>
            </button>
            <!-- OVO -->
            <button type="button" onclick="selectEwallet('ovo')" id="btn-ovo" class="ewallet-btn bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-purple-500 transition focus:outline-none flex flex-col items-center justify-center">
              <div class="h-8 w-full flex items-center justify-center mb-2">
                <img src="gambar/logo_ovo.png" alt="OVO" class="h-full object-contain">
              </div>
              <div class="font-semibold text-gray-700">OVO</div>
            </button>
            <!-- DANA -->
            <button type="button" onclick="selectEwallet('dana')" id="btn-dana" class="ewallet-btn bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-blue-500 transition focus:outline-none flex flex-col items-center justify-center">
              <div class="h-8 w-full flex items-center justify-center mb-2">
                <img src="gambar/logo_dana.png" alt="DANA" class="h-full object-contain">
              </div>
              <div class="font-semibold text-gray-700">DANA</div>
            </button>
            <!-- ShopeePay -->
            <button type="button" onclick="selectEwallet('shopeepay')" id="btn-shopeepay" class="ewallet-btn bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-orange-500 transition focus:outline-none flex flex-col items-center justify-center">
              <div class="h-8 w-full flex items-center justify-center mb-2">
                <img src="gambar/logo_shopeepay.png" alt="ShopeePay" class="h-full object-contain">
              </div>
              <div class="font-semibold text-gray-700">ShopeePay</div>
            </button>
          </div>
        </div>

        <!-- QR Code Display Area -->
        <div id="qr-display" class="text-center hidden">
          <div class="inline-block bg-white p-6 rounded-2xl shadow-xl qris-glow mb-4">
            <div class="w-64 h-64 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center relative overflow-hidden">
              <!-- Placeholder QR - akan diganti dengan gambar QR asli -->
              <div id="qr-placeholder" class="text-center">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <p id="qr-ewallet-name" class="text-gray-600 font-bold text-lg">QR Code</p>
                <p class="text-xs text-gray-400">Scan untuk bayar</p>
              </div>
            </div>
          </div>
          <p id="qr-instruction" class="text-gray-600 mb-2">Scan QR Code dengan aplikasi <span id="selected-ewallet-name" class="font-bold text-pink-600">e-wallet</span> Anda</p>
          <p class="font-bold text-2xl text-pink-600 mb-2"><?= PaymentGateway::formatRupiah($payment['amount']) ?></p>
          <p class="text-sm text-gray-500">Pembayaran akan otomatis terverifikasi</p>
        </div>

        <!-- Initial instruction when no e-wallet selected -->
        <div id="ewallet-initial" class="text-center py-8">
          <svg class="w-16 h-16 mx-auto text-pink-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
          </svg>
          <p class="text-gray-500 text-lg">Pilih e-wallet di atas untuk melihat QR Code pembayaran</p>
        </div>
      <?php endif ?>
    </div>

    <!-- Upload Bukti Transfer -->
    <div class="glass rounded-2xl shadow-xl p-6 mb-8">
      <h2 class="text-xl font-bold text-pink-700 mb-4">Upload Bukti Transfer</h2>
      
      <?php if($payment['bukti_transfer']): ?>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
          <p class="text-green-700 font-semibold mb-2">✓ Bukti transfer sudah diupload</p>
          <img src="<?= htmlspecialchars($payment['bukti_transfer']) ?>" alt="Bukti Transfer" class="max-w-xs rounded-lg shadow">
        </div>
        <p class="text-gray-600 text-sm">Admin akan segera memverifikasi pembayaran Anda.</p>
      <?php else: ?>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="upload_bukti" value="1">
          <div class="border-2 border-dashed border-pink-300 rounded-xl p-8 text-center hover:border-pink-500 transition">
            <svg class="w-12 h-12 mx-auto text-pink-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-600 mb-4">Pilih atau drag file bukti transfer</p>
            <input type="file" name="bukti_transfer" accept="image/*" class="mb-4" required>
            <br>
            <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-full font-semibold transition">
              Upload Bukti Transfer
            </button>
          </div>
        </form>
      <?php endif ?>
    </div>
    <?php endif ?>

    <?php elseif($payment['status'] == 'confirmed'): ?>
    <!-- Payment Confirmed -->
    <div class="glass rounded-2xl shadow-xl p-8 text-center border-t-4 border-green-400">
      <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-6">
        <svg class="w-12 h-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-green-700 mb-3">Pembayaran Dikonfirmasi!</h2>
      <p class="text-gray-600 mb-6">Terima kasih! Pesanan Anda sedang diproses.</p>
      <a href="index.php" class="inline-block bg-gradient-to-r from-pink-500 to-pink-600 text-white px-8 py-3 rounded-full font-bold hover:from-pink-600 hover:to-pink-700 shadow-lg transition">
        Kembali ke Home
      </a>
    </div>
    <?php endif ?>

    <?php endif ?>
  </div>

  <!-- Footer -->
  <footer class="bg-gradient-to-t from-pink-700 via-pink-500 to-pink-400 text-white pt-8 pb-4 mt-16">
    <div class="max-w-6xl mx-auto px-4 text-center">
      <span class="text-2xl font-bold brand-font">HappyippieCake</span>
      <p class="mt-2 text-white/80 text-sm">&copy; 2025 HappyippieCake. All Rights Reserved.</p>
    </div>
  </footer>

  <script>
    // Handle payment method selection visual
    document.querySelectorAll('.payment-card').forEach(card => {
      card.addEventListener('click', function() {
        document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
      });
    });

    // E-Wallet data with colors
    const ewalletData = {
      gopay: { name: 'GoPay', color: 'from-green-400 to-teal-500', borderColor: 'border-green-500', bgColor: 'bg-green-50' },
      ovo: { name: 'OVO', color: 'from-purple-600 to-purple-800', borderColor: 'border-purple-500', bgColor: 'bg-purple-50' },
      dana: { name: 'DANA', color: 'from-blue-400 to-blue-600', borderColor: 'border-blue-500', bgColor: 'bg-blue-50' },
      shopeepay: { name: 'ShopeePay', color: 'from-orange-500 to-red-500', borderColor: 'border-orange-500', bgColor: 'bg-orange-50' }
    };

    // Handle e-wallet selection
    function selectEwallet(ewallet) {
      const data = ewalletData[ewallet];
      if (!data) return;

      // Remove selection from all buttons
      document.querySelectorAll('.ewallet-btn').forEach(btn => {
        btn.classList.remove('border-green-500', 'border-purple-500', 'border-blue-500', 'border-orange-500', 'bg-green-50', 'bg-purple-50', 'bg-blue-50', 'bg-orange-50');
        btn.classList.add('border-gray-200');
      });

      // Add selection to clicked button
      const selectedBtn = document.getElementById('btn-' + ewallet);
      if (selectedBtn) {
        selectedBtn.classList.remove('border-gray-200');
        selectedBtn.classList.add(data.borderColor, data.bgColor);
      }

      // Update QR display
      document.getElementById('qr-ewallet-name').textContent = data.name + ' QR';
      document.getElementById('selected-ewallet-name').textContent = data.name;
      
      // Show QR display, hide initial message
      document.getElementById('qr-display').classList.remove('hidden');
      document.getElementById('ewallet-initial').classList.add('hidden');
    }

    // Copy to clipboard function
    function copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(() => {
        alert('Nomor rekening berhasil disalin!');
      }).catch(err => {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Nomor rekening berhasil disalin!');
      });
    }
    
    // Switch between Bank and QRIS payment tabs
    function switchPaymentTab(tab) {
      const bankPanel = document.getElementById('panel-bank');
      const qrisPanel = document.getElementById('panel-qris');
      const bankTab = document.getElementById('tab-bank');
      const qrisTab = document.getElementById('tab-qris');
      
      if (tab === 'bank') {
        // Show bank panel
        bankPanel.classList.remove('hidden');
        qrisPanel.classList.add('hidden');
        // Style tabs
        bankTab.className = 'flex-1 py-3 px-4 rounded-xl font-semibold transition-all text-center bg-blue-500 text-white shadow-lg';
        qrisTab.className = 'flex-1 py-3 px-4 rounded-xl font-semibold transition-all text-center bg-gray-100 text-gray-600 hover:bg-gray-200';
      } else {
        // Show QRIS panel
        bankPanel.classList.add('hidden');
        qrisPanel.classList.remove('hidden');
        // Style tabs
        bankTab.className = 'flex-1 py-3 px-4 rounded-xl font-semibold transition-all text-center bg-gray-100 text-gray-600 hover:bg-gray-200';
        qrisTab.className = 'flex-1 py-3 px-4 rounded-xl font-semibold transition-all text-center bg-purple-500 text-white shadow-lg';
        // Auto-select QRIS radio
        const qrisRadio = document.querySelector('input[value="qris"]');
        if (qrisRadio) qrisRadio.checked = true;
      }
    }
  </script>
</body>
</html>
