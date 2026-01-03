<?php
session_start();
require_once 'db_connect.php';
require_once 'PaymentGateway.php';

// Check admin login (optional - uncomment if needed)
// if (!isset($_SESSION['admin_login']) || !$_SESSION['admin_login']) {
//     header('Location: login.php');
//     exit;
// }

$gateway = new PaymentGateway($conn);
$notif = '';

// Handle confirm payment
if (isset($_GET['confirm'])) {
    $paymentId = intval($_GET['confirm']);
    if ($gateway->confirmPayment($paymentId)) {
        $notif = 'Pembayaran berhasil dikonfirmasi!';
    } else {
        $notif = 'Gagal mengkonfirmasi pembayaran!';
    }
}

// Handle cancel payment
if (isset($_GET['cancel'])) {
    $paymentId = intval($_GET['cancel']);
    if ($gateway->cancelPayment($paymentId)) {
        $notif = 'Pembayaran berhasil dibatalkan!';
    } else {
        $notif = 'Gagal membatalkan pembayaran!';
    }
}

// Get pending and confirmed payments
$pendingPayments = $gateway->getPendingPayments();
$confirmedPayments = $gateway->getConfirmedPayments();

// Get payment method labels
$paymentMethods = PaymentGateway::getPaymentMethods();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Admin Pembayaran | HappyippieCake</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', Arial, sans-serif;}
    .brand-font { font-family: 'Inter', system-ui, sans-serif; font-weight: 600;}
    .glass { background: rgba(255,255,255,0.88); backdrop-filter: blur(8px); }
    .badge { font-size: 12px; padding: 4px 12px; border-radius: 20px; }
    .table-row:hover { background: #fff6fa; }
    .btn-action { transition: 0.2s; }
    .btn-action:hover { filter: brightness(1.1); transform: translateY(-1px); }
  </style>
</head>
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen">

  <!-- Navbar Admin -->
  <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 mb-8">
    <div class="max-w-7xl mx-auto flex items-center justify-between py-4 px-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-pink-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-800 tracking-tight">Manajemen Pembayaran</h1>
          <p class="text-xs text-gray-500 font-medium">Panel Admin HappyippieCake</p>
        </div>
      </div>
      <ul class="flex gap-1 bg-gray-100/50 p-1 rounded-xl">
        <li><a href="dashboard.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-white hover:text-pink-600 hover:shadow-sm transition-all">Dashboard</a></li>
        <li><a href="admin.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-white hover:text-pink-600 hover:shadow-sm transition-all">Menu</a></li>
        <li><a href="data_pesanan.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-white hover:text-pink-600 hover:shadow-sm transition-all">Pesanan</a></li>
        <li><a href="payment_admin.php" class="px-4 py-2 rounded-lg text-sm font-bold text-pink-600 bg-white shadow-sm ring-1 ring-black/5">Pembayaran</a></li>
      </ul>
    </div>
  </nav>

  <div class="max-w-7xl mx-auto px-6 mb-14">

    <?php if($notif): ?>
      <div id="notif-toast" class="fixed top-24 right-6 z-50 flex items-center gap-3 bg-white border border-green-100 p-4 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] animate-fade-in-down">
        <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center text-green-500">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
          <h4 class="font-bold text-gray-800 text-sm">Berhasil!</h4>
          <p class="text-sm text-gray-500"><?= htmlspecialchars($notif) ?></p>
        </div>
        <button onclick="document.getElementById('notif-toast').remove()" class="ml-4 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
      </div>
      <script>setTimeout(() => document.getElementById('notif-toast')?.remove(), 4000);</script>
    <?php endif ?>

    <!-- Pending Payments -->
    <div class="mb-12">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-800">Menunggu Konfirmasi</h2>
          <p class="text-gray-500 text-sm mt-1">Verifikasi bukti transfer dari pelanggan</p>
        </div>
        <?php if(count($pendingPayments) > 0): ?>
        <span class="px-4 py-2 bg-amber-50 text-amber-700 rounded-full text-sm font-bold border border-amber-100 flex items-center gap-2">
          <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
          </span>
          <?= count($pendingPayments) ?> Perlu Tindakan
        </span>
        <?php endif ?>
      </div>

      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <?php if(count($pendingPayments) > 0): ?>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead class="bg-gray-50/50 border-b border-gray-100 text-gray-500 font-semibold uppercase tracking-wider text-xs">
              <tr>
                <th class="py-5 px-6 rounded-tl-3xl">Order Info</th>
                <th class="py-5 px-6">Pelanggan</th>
                <th class="py-5 px-6 text-center">Jumlah</th>
                <th class="py-5 px-6 text-center">Metode</th>
                <th class="py-5 px-6 text-center">Bukti</th>
                <th class="py-5 px-6 text-right rounded-tr-3xl">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <?php foreach($pendingPayments as $p): ?>
              <tr class="hover:bg-amber-50/30 transition-colors group">
                <td class="py-5 px-6 font-mono text-xs font-bold text-pink-600">
                  <?= htmlspecialchars($p['order_id']) ?>
                  <div class="text-[10px] text-gray-400 font-sans mt-1 font-normal"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?></div>
                </td>
                <td class="py-5 px-6 font-medium text-gray-800">
                  <?= htmlspecialchars($p['nama_pemesan'] ?? '-') ?>
                </td>
                <td class="py-5 px-6 text-center font-bold text-gray-800">
                  <?= PaymentGateway::formatRupiah($p['amount']) ?>
                </td>
                <td class="py-5 px-6 text-center">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                    <?= $paymentMethods[$p['payment_method']] ?? $p['payment_method'] ?>
                  </span>
                </td>
                <td class="py-5 px-6 text-center">
                  <?php if($p['bukti_transfer']): ?>
                    <a href="<?= htmlspecialchars($p['bukti_transfer']) ?>" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-pink-600 hover:text-pink-800 hover:bg-pink-50 px-3 py-1.5 rounded-lg transition-colors border border-pink-100">
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>  
                      Lihat Bukti
                    </a>
                  <?php else: ?>
                    <span class="text-xs text-gray-400 italic">Belum upload</span>
                  <?php endif ?>
                </td>
                <td class="py-5 px-6 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <a href="?confirm=<?= $p['id'] ?>" 
                      onclick="return confirm('Konfirmasi pembayaran ini valid?')"
                      class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-sm transition-all text-xs font-bold" title="Konfirmasi">
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                      Terima
                    </a>
                    <a href="?cancel=<?= $p['id'] ?>" 
                      onclick="return confirm('Batalkan pembayaran ini?')"
                      class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-100" title="Tolak / Batal">
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
          <div class="py-16 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
              <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-gray-400 font-medium">Tidak ada pembayaran menunggu konfirmasi.</p>
          </div>
        <?php endif ?>
      </div>
    </div>

    <!-- Confirmed Payments -->
    <div>
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
          <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Riwayat Transaksi Sukses
        </h2>
      </div>
      
      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden opacity-90 hover:opacity-100 transition-opacity">
        <?php if(count($confirmedPayments) > 0): ?>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b border-gray-100 text-gray-400 font-medium text-xs uppercase">
              <tr>
                <th class="py-4 px-6">Order ID</th>
                <th class="py-4 px-6">Pelanggan</th>
                <th class="py-4 px-6 text-center">Jumlah</th>
                <th class="py-4 px-6 text-center">Metode</th>
                <th class="py-4 px-6 text-center">Waktu Konfirmasi</th>
                <th class="py-4 px-6 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <?php foreach($confirmedPayments as $p): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="py-4 px-6 font-mono text-xs text-gray-500">
                  <?= htmlspecialchars($p['order_id']) ?>
                </td>
                <td class="py-4 px-6 font-medium text-gray-700">
                  <?= htmlspecialchars($p['nama_pemesan'] ?? '-') ?>
                </td>
                <td class="py-4 px-6 text-center font-bold text-gray-600">
                  <?= PaymentGateway::formatRupiah($p['amount']) ?>
                </td>
                <td class="py-4 px-6 text-center">
                  <span class="text-xs text-gray-500">
                    <?= $paymentMethods[$p['payment_method']] ?? $p['payment_method'] ?>
                  </span>
                </td>
                <td class="py-4 px-6 text-center text-xs text-gray-400">
                  <?= $p['confirmed_at'] ? date('d/m/Y H:i', strtotime($p['confirmed_at'])) : '-' ?>
                </td>
                <td class="py-4 px-6 text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                    Lunas
                  </span>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
          <div class="py-10 text-center text-gray-400 text-sm">Belum ada riwayat transaksi.</div>
        <?php endif ?>
      </div>
    </div>

  </div>
</body>
</html>
