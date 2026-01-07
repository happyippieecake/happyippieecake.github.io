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
$notifType = 'success';

// Handle confirm payment
if (isset($_GET['confirm'])) {
    $paymentId = intval($_GET['confirm']);
    if ($gateway->confirmPayment($paymentId)) {
        $notif = 'Pembayaran berhasil dikonfirmasi!';
    } else {
        $notif = 'Gagal mengkonfirmasi pembayaran!';
        $notifType = 'error';
    }
}

// Handle cancel payment
if (isset($_GET['cancel'])) {
    $paymentId = intval($_GET['cancel']);
    if ($gateway->cancelPayment($paymentId)) {
        $notif = 'Pembayaran berhasil dibatalkan!';
    } else {
        $notif = 'Gagal membatalkan pembayaran!';
        $notifType = 'error';
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
  <title>Payments | HappyippieCake Admin</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin_styles.css">
  <style>
    /* Payment-specific styles */
    .payment-table { width: 100%; border-collapse: collapse; }
    .payment-table th {
      text-align: left;
      padding: 16px 20px;
      font-size: 12px;
      font-weight: 600;
      color: var(--text-secondary);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
    }
    .payment-table td {
      padding: 16px 20px;
      font-size: 14px;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
    }
    .payment-table tr:hover { background: #fefce8; }
    
    .order-id {
      font-family: 'Monaco', 'Consolas', monospace;
      font-size: 12px;
      font-weight: 600;
      color: var(--accent);
    }
    .order-date {
      font-size: 11px;
      color: var(--text-secondary);
      margin-top: 4px;
    }
    
    .method-badge {
      display: inline-flex;
      align-items: center;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      background: #dbeafe;
      color: #2563eb;
    }
    
    .btn-view-bukti {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      color: #ec4899;
      background: #fdf2f8;
      border: 1px solid #fbcfe8;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-view-bukti:hover {
      background: #fce7f3;
      border-color: #f9a8d4;
    }
    
    .btn-confirm {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      color: white;
      background: #16a34a;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-confirm:hover { background: #15803d; transform: translateY(-1px); }
    
    .btn-cancel {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      border-radius: 8px;
      color: #ef4444;
      background: transparent;
      border: 1px solid #fee2e2;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-cancel:hover { background: #fee2e2; border-color: #fecaca; }
    
    .pending-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
      background: #fef3c7;
      color: #b45309;
      border: 1px solid #fde68a;
    }
    .pending-badge .dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #f59e0b;
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
    
    .empty-state {
      text-align: center;
      padding: 48px 24px;
      color: var(--text-secondary);
    }
    .empty-state svg {
      width: 48px;
      height: 48px;
      margin-bottom: 16px;
      opacity: 0.5;
    }
    
    .section-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    .section-title h2 {
      font-size: 18px;
      font-weight: 600;
      color: var(--text-primary);
      margin: 0;
    }
    .section-title p {
      font-size: 13px;
      color: var(--text-secondary);
      margin: 4px 0 0;
    }
    
    .status-lunas {
      display: inline-flex;
      align-items: center;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      background: #dcfce7;
      color: #16a34a;
    }
  </style>
</head>
<body>
  <div class="admin-layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <div class="brand-icon">HC</div>
        <span class="brand-text">HappyippieCake</span>
      </div>
      
      <ul class="sidebar-nav">
        <li>
          <a href="dashboard.php">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="admin.php">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span class="nav-text">Menu</span>
          </a>
        </li>
        <li>
          <a href="data_pesanan.php">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span class="nav-text">Orders</span>
          </a>
        </li>
        <li>
          <a href="payment_admin.php" class="active">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <span class="nav-text">Payments</span>
          </a>
        </li>
        <li>
          <a href="index.php" target="_blank">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            <span class="nav-text">Lihat Toko</span>
          </a>
        </li>
      </ul>

      <button class="sidebar-toggle" onclick="toggleSidebar()">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
        </svg>
        <span class="nav-text">Collapse</span>
      </button>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Header -->
      <div class="page-header">
        <div>
          <h1>Payments</h1>
          <p>Kelola pembayaran dan konfirmasi transaksi</p>
        </div>
        <button class="btn-refresh" onclick="location.reload()">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Refresh
        </button>
      </div>

      <?php if($notif): ?>
        <div class="alert alert-<?= $notifType ?>" style="margin-bottom: 24px;">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <?php if($notifType == 'success'): ?>
              <path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            <?php else: ?>
              <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            <?php endif ?>
          </svg>
          <?= htmlspecialchars($notif) ?>
        </div>
      <?php endif ?>

      <!-- Pending Payments Section -->
      <div class="content-card" style="margin-bottom: 32px;">
        <div class="card-header">
          <div>
            <h3 style="font-size: 16px;">Menunggu Konfirmasi</h3>
            <p style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">Verifikasi bukti transfer dari pelanggan</p>
          </div>
          <?php if(count($pendingPayments) > 0): ?>
          <span class="pending-badge">
            <span class="dot"></span>
            <?= count($pendingPayments) ?> Perlu Tindakan
          </span>
          <?php endif ?>
        </div>
        <div class="card-body" style="padding: 0;">
          <?php if(count($pendingPayments) > 0): ?>
          <div style="overflow-x: auto;">
            <table class="payment-table">
              <thead>
                <tr>
                  <th>Order Info</th>
                  <th>Pelanggan</th>
                  <th style="text-align: center;">Jumlah</th>
                  <th style="text-align: center;">Metode</th>
                  <th style="text-align: center;">Bukti</th>
                  <th style="text-align: right;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($pendingPayments as $p): ?>
                <tr>
                  <td>
                    <div class="order-id"><?= htmlspecialchars($p['order_id']) ?></div>
                    <div class="order-date"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?></div>
                  </td>
                  <td style="font-weight: 500;"><?= htmlspecialchars($p['nama_pemesan'] ?? '-') ?></td>
                  <td style="text-align: center; font-weight: 600;"><?= PaymentGateway::formatRupiah($p['amount']) ?></td>
                  <td style="text-align: center;">
                    <span class="method-badge"><?= $paymentMethods[$p['payment_method']] ?? $p['payment_method'] ?></span>
                  </td>
                  <td style="text-align: center;">
                    <?php if($p['bukti_transfer'] && file_exists($p['bukti_transfer'])): ?>
                      <a href="<?= htmlspecialchars($p['bukti_transfer']) ?>" target="_blank" class="btn-view-bukti">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Lihat Bukti
                      </a>
                    <?php elseif($p['bukti_transfer']): ?>
                      <span style="color: #ef4444; font-size: 12px; font-style: italic;" title="<?= htmlspecialchars($p['bukti_transfer']) ?>">⚠️ File hilang</span>
                    <?php else: ?>
                      <span style="color: var(--text-secondary); font-size: 12px; font-style: italic;">Belum upload</span>
                    <?php endif ?>
                  </td>
                  <td style="text-align: right;">
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                      <a href="?confirm=<?= $p['id'] ?>" 
                        onclick="return confirm('Konfirmasi pembayaran ini valid?')"
                        class="btn-confirm">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        Terima
                      </a>
                      <a href="?cancel=<?= $p['id'] ?>" 
                        onclick="return confirm('Batalkan pembayaran ini?')"
                        class="btn-cancel" title="Tolak / Batalkan">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          </div>
          <?php else: ?>
          <div class="empty-state">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>Tidak ada pembayaran menunggu konfirmasi.</p>
          </div>
          <?php endif ?>
        </div>
      </div>

      <!-- Confirmed Payments Section -->
      <div class="content-card">
        <div class="card-header">
          <h3 style="display: flex; align-items: center; gap: 8px; font-size: 16px;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#16a34a">
              <path stroke-linecap="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Riwayat Transaksi Sukses
          </h3>
        </div>
        <div class="card-body" style="padding: 0;">
          <?php if(count($confirmedPayments) > 0): ?>
          <div style="overflow-x: auto;">
            <table class="payment-table">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Pelanggan</th>
                  <th style="text-align: center;">Jumlah</th>
                  <th style="text-align: center;">Metode</th>
                  <th style="text-align: center;">Waktu Konfirmasi</th>
                  <th style="text-align: center;">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($confirmedPayments as $p): ?>
                <tr>
                  <td>
                    <div class="order-id" style="color: var(--text-secondary);"><?= htmlspecialchars($p['order_id']) ?></div>
                  </td>
                  <td style="font-weight: 500;"><?= htmlspecialchars($p['nama_pemesan'] ?? '-') ?></td>
                  <td style="text-align: center; font-weight: 600;"><?= PaymentGateway::formatRupiah($p['amount']) ?></td>
                  <td style="text-align: center;">
                    <span style="font-size: 12px; color: var(--text-secondary);"><?= $paymentMethods[$p['payment_method']] ?? $p['payment_method'] ?></span>
                  </td>
                  <td style="text-align: center; font-size: 12px; color: var(--text-secondary);">
                    <?= $p['confirmed_at'] ? date('d/m/Y H:i', strtotime($p['confirmed_at'])) : '-' ?>
                  </td>
                  <td style="text-align: center;">
                    <span class="status-lunas">Lunas</span>
                  </td>
                </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          </div>
          <?php else: ?>
          <div class="empty-state">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <p>Belum ada riwayat transaksi.</p>
          </div>
          <?php endif ?>
        </div>
      </div>
    </main>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('collapsed');
      const mainContent = document.querySelector('.main-content');
      if(document.getElementById('sidebar').classList.contains('collapsed')) {
        mainContent.style.marginLeft = '72px';
      } else {
        mainContent.style.marginLeft = '260px';
      }
    }
  </script>
</body>
</html>
