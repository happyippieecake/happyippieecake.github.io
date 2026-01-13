<?php
$conn = new mysqli("localhost","root","","happyippiecake");

// Hapus pesanan dari riwayat
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM pesanan WHERE id=$id");
    header('Location: data_pesanan.php?notif=Pesanan berhasil dihapus');
    exit;
}

// Tandai pesanan selesai (single)
if(isset($_GET['selesai'])){
    $id = intval($_GET['selesai']);
    $conn->query("UPDATE pesanan SET status='selesai' WHERE id=$id");
    $conn->query("UPDATE payments SET status='confirmed' WHERE pesanan_id=$id");
    header('Location: data_pesanan.php?notif=Pesanan diarsipkan (selesai)');
    exit;
}

// Tandai pesanan selesai (group)
if(isset($_GET['selesai_group'])){
    $ids = explode(',', $_GET['selesai_group']);
    foreach($ids as $id) {
        $id = intval($id);
        if($id > 0) {
            $conn->query("UPDATE pesanan SET status='selesai' WHERE id=$id");
            $conn->query("UPDATE payments SET status='confirmed' WHERE pesanan_id=$id");
        }
    }
    header('Location: data_pesanan.php?notif=Grup pesanan berhasil diproses');
    exit;
}

$notif = isset($_GET['notif']) ? $_GET['notif'] : '';

// Function to format payment method
function formatPaymentMethod($method) {
    $methods = [
        'bank_bca' => 'Bank BCA',
        'bank_mandiri' => 'Bank Mandiri', 
        'bank_bri' => 'Bank BRI',
        'qris' => 'QRIS/E-Wallet',
        'whatsapp' => 'WhatsApp'
    ];
    return $methods[$method] ?? $method;
}

// Function to format currency
if (!function_exists('formatRupiah')) {
    function formatRupiah($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

// Count stats
$pending_count = $conn->query("SELECT COUNT(*) FROM pesanan WHERE status='pending'")->fetch_row()[0];
$completed_count = $conn->query("SELECT COUNT(*) FROM pesanan WHERE status='selesai'")->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Orders | HappyippieCake Admin</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin_styles.css">
  <style>
    .order-tabs {
      display: flex;
      gap: 8px;
      margin-bottom: 24px;
    }
    .order-tab {
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      border: none;
      background: #f1f5f9;
      color: #64748b;
      transition: all 0.2s;
    }
    .order-tab.active {
      background: #0d9488;
      color: white;
    }
    .order-tab .count {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 20px;
      height: 20px;
      padding: 0 6px;
      border-radius: 10px;
      font-size: 11px;
      margin-left: 8px;
    }
    .order-tab.active .count {
      background: rgba(255,255,255,0.2);
    }
    .order-tab:not(.active) .count {
      background: #e2e8f0;
    }
    .order-section {
      display: none;
    }
    .order-section.active {
      display: block;
    }
    .customer-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #0d9488, #14b8a6);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 14px;
      flex-shrink: 0;
      text-transform: uppercase;
    }
    .order-detail-item {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #f1f5f9;
    }
    .order-detail-item:last-child {
      border-bottom: none;
    }
    .payment-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
    }
    .payment-badge.bank { background: #dbeafe; color: #2563eb; }
    .payment-badge.qris { background: #f3e8ff; color: #7c3aed; }
    .payment-badge.wa { background: #dcfce7; color: #16a34a; }
    .empty-orders {
      text-align: center;
      padding: 60px 20px;
      color: #94a3b8;
    }
    .empty-orders svg {
      width: 64px;
      height: 64px;
      margin-bottom: 16px;
      opacity: 0.5;
    }
    /* Search & Filter Styles */
    .search-filter-bar { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
    .search-input { flex: 1; min-width: 200px; padding: 10px 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; }
    .search-input:focus { outline: none; border-color: #0d9488; box-shadow: 0 0 0 3px rgba(13,148,136,0.1); }
    .filter-select { padding: 10px 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; background: white; cursor: pointer; }
    .filter-select:focus { outline: none; border-color: #0d9488; }
    .btn-export { display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; background: #059669; color: white; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; }
    .btn-export:hover { background: #047857; }
    .highlight { background: #fef08a; }
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
          <a href="data_pesanan.php" class="active">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span class="nav-text">Orders</span>
          </a>
        </li>
        <li>
          <a href="payment_admin.php">
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
      <?php if($notif): ?>
        <div class="alert alert-success">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <?= htmlspecialchars($notif) ?>
        </div>
      <?php endif; ?>

      <!-- Header -->
      <div class="page-header">
        <div>
          <h1>Order Management</h1>
          <p>Kelola semua pesanan masuk</p>
        </div>
        <div style="display:flex; gap:12px;">
          <a href="export_laporan.php" class="btn-export">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export CSV
          </a>
          <button class="btn-refresh" onclick="location.reload()">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Refresh
          </button>
        </div>
      </div>

      <!-- Search & Filter Bar -->
      <div class="search-filter-bar">
        <input type="text" class="search-input" id="searchInput" placeholder="🔍 Cari nama pelanggan..." oninput="filterOrders()">
        <select class="filter-select" id="filterDate" onchange="filterOrders()">
          <option value="all">Semua Tanggal</option>
          <option value="7">7 Hari Terakhir</option>
          <option value="30">30 Hari Terakhir</option>
        </select>
        <select class="filter-select" id="filterStatus" onchange="filterOrders()">
          <option value="all">Semua Status</option>
          <option value="paid">Sudah Bayar</option>
          <option value="pending">Belum Bayar</option>
        </select>
      </div>

      <!-- Tabs -->
      <div class="order-tabs">
        <button class="order-tab active" onclick="showTab('pending')">
          Perlu Diproses
          <span class="count"><?= $pending_count ?></span>
        </button>
        <button class="order-tab" onclick="showTab('completed')">
          Riwayat Selesai
          <span class="count"><?= $completed_count ?></span>
        </button>
      </div>

      <?php
      // Pending orders
      $pesanan = $conn->query(
          "SELECT pesanan.id, pesanan.nama_pemesan, pesanan.jumlah, pesanan.alamat, pesanan.status, pesanan.order_id as link_order_id, pesanan.tanggal_pesan,
                  menu.nama as menu_nama, menu.harga,
                  payments.amount, payments.payment_method, payments.bukti_transfer, payments.status as payment_status, payments.order_id
           FROM pesanan 
           JOIN menu ON pesanan.menu_id=menu.id
           LEFT JOIN payments ON pesanan.id=payments.pesanan_id
           WHERE pesanan.status = 'pending'
           ORDER BY pesanan.id DESC"
      );

      // Group orders
      $grouped_pesanan = [];
      while($row = $pesanan->fetch_assoc()) {
          if (!empty($row['link_order_id'])) {
              $key = $row['link_order_id'];
          } else {
              $key = 'single_'.$row['id'];
          }
          
          if(!isset($grouped_pesanan[$key])) {
              $grouped_pesanan[$key] = [
                  'nama_pemesan' => $row['nama_pemesan'],
                  'alamat' => $row['alamat'],
                  'tanggal' => $row['tanggal_pesan'],
                  'items' => [],
                  'payment_info' => null,
                  'ids' => [],
                  'display_order_id' => $row['link_order_id'] ?? $row['order_id']
              ];
          }
          
          $grouped_pesanan[$key]['items'][] = [
              'menu_nama' => $row['menu_nama'],
              'jumlah' => $row['jumlah'],
              'harga' => $row['harga'],
              'subtotal' => $row['harga'] * $row['jumlah']
          ];
          $grouped_pesanan[$key]['ids'][] = $row['id'];
          
          if($row['payment_method'] && !$grouped_pesanan[$key]['payment_info']) {
              $grouped_pesanan[$key]['payment_info'] = [
                  'amount' => $row['amount'],
                  'method' => $row['payment_method'],
                  'bukti' => $row['bukti_transfer'],
                  'status' => $row['payment_status'],
                  'order_id' => $row['order_id']
              ];
              if(empty($grouped_pesanan[$key]['display_order_id'])) {
                 $grouped_pesanan[$key]['display_order_id'] = $row['order_id'];
              }
          }
      }

      // Completed orders
      $riwayat = $conn->query(
          "SELECT pesanan.id, pesanan.nama_pemesan, pesanan.jumlah, pesanan.alamat, pesanan.tanggal_pesan,
                  menu.nama as menu_nama, menu.harga,
                  payments.payment_method, payments.status as payment_status
           FROM pesanan 
           JOIN menu ON pesanan.menu_id=menu.id
           LEFT JOIN payments ON pesanan.id=payments.pesanan_id
           WHERE pesanan.status = 'selesai'
           ORDER BY pesanan.id DESC
           LIMIT 50"
      );
      ?>

      <!-- Pending Orders Section -->
      <div id="section-pending" class="order-section active">
        <div class="content-card" style="margin-bottom: 0;">
          <?php if(empty($grouped_pesanan)): ?>
            <div class="empty-orders">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
              <p>Tidak ada pesanan yang perlu diproses saat ini</p>
            </div>
          <?php else: ?>
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Pelanggan</th>
                  <th>Detail Pesanan</th>
                  <th style="text-align:center">Pembayaran</th>
                  <th style="text-align:center">Status</th>
                  <th style="text-align:right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($grouped_pesanan as $group): 
                    $total_tagihan = 0;
                    foreach($group['items'] as $itm) $total_tagihan += $itm['subtotal'];
                    $pay = $group['payment_info'];
                ?>
                <tr>
                  <td>
                    <div style="display:flex; gap:12px; align-items:flex-start;">
                      <div class="customer-avatar"><?= substr($group['nama_pemesan'], 0, 2) ?></div>
                      <div>
                        <div style="font-weight:600; color:#1e293b; margin-bottom:4px;"><?= htmlspecialchars($group['nama_pemesan']) ?></div>
                        <div style="font-size:12px; color:#64748b; display:flex; align-items:center; gap:4px;">
                          <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                          <?= htmlspecialchars($group['alamat']) ?>
                        </div>
                        <?php if($group['display_order_id']): ?>
                          <div style="margin-top:6px;"><span class="badge badge-info" style="font-size:10px;">#<?= $group['display_order_id'] ?></span></div>
                        <?php endif ?>
                      </div>
                    </div>
                  </td>
                  <td>
                    <?php foreach($group['items'] as $item): ?>
                    <div class="order-detail-item">
                      <div>
                        <div style="font-weight:600; color:#0d9488;"><?= htmlspecialchars($item['menu_nama']) ?></div>
                        <div style="font-size:12px; color:#64748b;"><?= $item['jumlah'] ?> pcs × <?= formatRupiah($item['harga']) ?></div>
                      </div>
                      <div style="font-weight:600;"><?= formatRupiah($item['subtotal']) ?></div>
                    </div>
                    <?php endforeach ?>
                    <div style="padding-top:8px; margin-top:8px; border-top:2px dashed #e2e8f0; display:flex; justify-content:space-between; font-weight:700;">
                      <span>Total</span>
                      <span style="color:#0d9488;"><?= formatRupiah($total_tagihan) ?></span>
                    </div>
                  </td>
                  <td style="text-align:center;">
                    <?php if($pay): ?>
                      <span class="payment-badge <?= strpos($pay['method'], 'bank') !== false ? 'bank' : ($pay['method'] == 'qris' ? 'qris' : 'wa') ?>">
                        <?= formatPaymentMethod($pay['method']) ?>
                      </span>
                      <?php if($pay['bukti']): ?>
                        <a href="<?= htmlspecialchars($pay['bukti']) ?>" target="_blank" style="display:block; margin-top:8px; font-size:12px; color:#2563eb;">
                          Lihat Bukti
                        </a>
                      <?php endif ?>
                    <?php else: ?>
                      <span style="color:#94a3b8; font-size:12px;">Via WhatsApp</span>
                    <?php endif ?>
                  </td>
                  <td style="text-align:center;">
                    <?php if($pay && ($pay['status'] == 'confirmed' || $pay['bukti'])): ?>
                      <span class="badge badge-success">Sudah Bayar</span>
                    <?php else: ?>
                      <span class="badge badge-pending">Pending</span>
                    <?php endif ?>
                  </td>
                  <td style="text-align:right;">
                    <?php $ids_str = implode(',', $group['ids']); ?>
                    <a href="?selesai_group=<?= $ids_str ?>" onclick="return confirm('Selesaikan semua item dalam pesanan ini?')" class="btn btn-primary btn-sm">
                      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                      Selesai
                    </a>
                  </td>
                </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          <?php endif ?>
        </div>
      </div>

      <!-- Completed Orders Section -->
      <div id="section-completed" class="order-section">
        <div class="content-card" style="margin-bottom: 0;">
          <?php if($riwayat->num_rows == 0): ?>
            <div class="empty-orders">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p>Belum ada riwayat pesanan selesai</p>
            </div>
          <?php else: ?>
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Pelanggan</th>
                  <th>Menu</th>
                  <th style="text-align:right">Total</th>
                  <th style="text-align:center">Tanggal</th>
                  <th style="text-align:right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($riwayat as $p): ?>
                <tr>
                  <td>
                    <div style="font-weight:600; color:#1e293b;"><?= htmlspecialchars($p['nama_pemesan']) ?></div>
                    <div style="font-size:12px; color:#94a3b8;"><?= htmlspecialchars($p['alamat']) ?></div>
                  </td>
                  <td>
                    <span style="color:#1e293b;"><?= htmlspecialchars($p['menu_nama']) ?></span>
                    <span style="color:#94a3b8;"> (<?= $p['jumlah'] ?>x)</span>
                  </td>
                  <td style="text-align:right; font-weight:600;"><?= formatRupiah($p['harga'] * $p['jumlah']) ?></td>
                  <td style="text-align:center;">
                    <span class="badge badge-success">Selesai</span>
                    <div style="font-size:11px; color:#94a3b8; margin-top:4px;"><?= date('d M Y', strtotime($p['tanggal_pesan'])) ?></div>
                  </td>
                  <td style="text-align:right;">
                    <a href="?hapus=<?= $p['id'] ?>" onclick="return confirm('Hapus riwayat permanen?')" class="btn btn-danger btn-sm">
                      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                      Hapus
                    </a>
                  </td>
                </tr>
                <?php endforeach ?>
              </tbody>
            </table>
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

    function showTab(tab) {
      document.querySelectorAll('.order-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.order-section').forEach(s => s.classList.remove('active'));
      
      event.target.classList.add('active');
      document.getElementById('section-' + tab).classList.add('active');
    }

    // Filter orders function
    function filterOrders() {
      const search = document.getElementById('searchInput').value.toLowerCase();
      const dateFilter = document.getElementById('filterDate').value;
      const statusFilter = document.getElementById('filterStatus').value;
      
      const rows = document.querySelectorAll('#section-pending tbody tr, #section-completed tbody tr');
      
      rows.forEach(row => {
        const name = row.querySelector('td:first-child')?.textContent.toLowerCase() || '';
        const dateCell = row.querySelector('.badge-info, [style*="font-size:11px"]')?.textContent || '';
        const statusBadge = row.querySelector('.badge-success, .badge-pending')?.textContent.toLowerCase() || '';
        
        let showRow = true;
        
        // Search filter
        if (search && !name.includes(search)) {
          showRow = false;
        }
        
        // Status filter
        if (statusFilter === 'paid' && !statusBadge.includes('bayar') && !statusBadge.includes('selesai') && !statusBadge.includes('lunas')) {
          showRow = false;
        }
        if (statusFilter === 'pending' && (statusBadge.includes('bayar') || statusBadge.includes('selesai'))) {
          showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
      });
      
      // Update export link with filters
      const exportLink = document.querySelector('.btn-export');
      if (exportLink) {
        let url = 'export_laporan.php?';
        if (dateFilter !== 'all') url += 'days=' + dateFilter + '&';
        if (statusFilter === 'paid') url += 'status=selesai';
        else if (statusFilter === 'pending') url += 'status=pending';
        exportLink.href = url;
      }
    }
  </script>
</body>
</html>
