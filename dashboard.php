<?php
$conn = new mysqli("localhost", "root", "", "happyippiecake");

// Get current dates
$hari_ini = date('Y-m-d');
$bulan_awal = date('Y-m-01');

// Total Revenue (All Time) - from payments
$total_revenue = $conn->query("SELECT SUM(amount) FROM payments WHERE status='confirmed'")->fetch_row()[0] ?: 0;

// Total Orders (All Time)
$total_orders = $conn->query("SELECT COUNT(*) FROM pesanan")->fetch_row()[0] ?: 0;

// Total Customers (Unique)
$total_customers = $conn->query("SELECT COUNT(DISTINCT nama_pemesan) FROM pesanan")->fetch_row()[0] ?: 0;

// Perlu Diproses (Pending Orders)
$pending_orders = $conn->query("SELECT COUNT(*) FROM pesanan WHERE status='pending'")->fetch_row()[0] ?: 0;

// Hari Ini Stats
$orders_hari_ini = $conn->query("SELECT COUNT(*) FROM pesanan WHERE DATE(tanggal_pesan)='$hari_ini'")->fetch_row()[0] ?: 0;
$revenue_hari_ini = $conn->query("SELECT SUM(p.amount) FROM payments p JOIN pesanan ps ON p.pesanan_id=ps.id WHERE DATE(ps.tanggal_pesan)='$hari_ini' AND p.status='confirmed'")->fetch_row()[0] ?: 0;

// Bulan Ini Stats  
$orders_bulan_ini = $conn->query("SELECT COUNT(*) FROM pesanan WHERE tanggal_pesan >= '$bulan_awal'")->fetch_row()[0] ?: 0;
$revenue_bulan_ini = $conn->query("SELECT SUM(p.amount) FROM payments p JOIN pesanan ps ON p.pesanan_id=ps.id WHERE ps.tanggal_pesan >= '$bulan_awal' AND p.status='confirmed'")->fetch_row()[0] ?: 0;

// Order Terbaru (5 latest pending)
$recent_orders = $conn->query(
    "SELECT pesanan.id, pesanan.nama_pemesan, pesanan.tanggal_pesan, pesanan.order_id,
            menu.nama as menu_nama, menu.harga, pesanan.jumlah,
            payments.status as payment_status
     FROM pesanan 
     JOIN menu ON pesanan.menu_id=menu.id
     LEFT JOIN payments ON pesanan.id=payments.pesanan_id
     WHERE pesanan.status='pending'
     ORDER BY pesanan.id DESC
     LIMIT 5"
);

// Menu Terlaris (Top 5)
$top_menu = $conn->query(
    "SELECT menu.id, menu.nama, menu.harga, SUM(pesanan.jumlah) as total_sold,
            SUM(menu.harga * pesanan.jumlah) as revenue
     FROM pesanan 
     JOIN menu ON pesanan.menu_id = menu.id
     GROUP BY menu.id
     ORDER BY total_sold DESC
     LIMIT 5"
);

function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | HappyippieCake Admin</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin_styles.css">
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
          <a href="dashboard.php" class="active">
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
      <!-- Header -->
      <div class="page-header">
        <div>
          <h1>Dashboard</h1>
          <p>Selamat datang di panel admin HappyippieCake</p>
        </div>
        <button class="btn-refresh" onclick="location.reload()">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Refresh
        </button>
      </div>

      <!-- Stat Cards -->
      <div class="stat-cards">
        <!-- Total Revenue -->
        <div class="stat-card green">
          <span class="badge">All Time</span>
          <div class="icon">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="label">Total Revenue</div>
          <div class="value"><?= formatRupiah($total_revenue) ?></div>
        </div>

        <!-- Total Orders -->
        <div class="stat-card teal">
          <span class="badge">All Time</span>
          <div class="icon">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <div class="label">Total Orders</div>
          <div class="value"><?= number_format($total_orders) ?></div>
        </div>

        <!-- Total Customers -->
        <div class="stat-card orange">
          <span class="badge">Unique</span>
          <div class="icon">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
          <div class="label">Total Customers</div>
          <div class="value"><?= number_format($total_customers) ?></div>
        </div>

        <!-- Perlu Diproses -->
        <div class="stat-card purple">
          <span class="badge">Pending</span>
          <div class="icon">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="label">Perlu Diproses</div>
          <div class="value"><?= number_format($pending_orders) ?></div>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="summary-cards">
        <!-- Hari Ini -->
        <div class="summary-card">
          <div class="header">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            Hari Ini
          </div>
          <div class="stats">
            <div class="stat-item">
              <div class="label">Orders</div>
              <div class="value"><?= number_format($orders_hari_ini) ?></div>
            </div>
            <div class="stat-item">
              <div class="label">Revenue</div>
              <div class="value"><?= formatRupiah($revenue_hari_ini) ?></div>
            </div>
          </div>
        </div>

        <!-- Bulan Ini -->
        <div class="summary-card">
          <div class="header">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            Bulan Ini
          </div>
          <div class="stats">
            <div class="stat-item">
              <div class="label">Orders</div>
              <div class="value"><?= number_format($orders_bulan_ini) ?></div>
            </div>
            <div class="stat-item">
              <div class="label">Revenue</div>
              <div class="value"><?= formatRupiah($revenue_bulan_ini) ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Cards -->
      <div class="content-cards">
        <!-- Order Terbaru -->
        <div class="content-card">
          <div class="card-header">
            <h3>Order Terbaru</h3>
            <a href="data_pesanan.php">
              Lihat Semua
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </a>
          </div>
          <div class="card-body">
            <?php if($recent_orders->num_rows == 0): ?>
              <p style="color: #94a3b8; text-align: center; padding: 32px 0;">Belum ada pesanan pending</p>
            <?php else: ?>
              <?php while($order = $recent_orders->fetch_assoc()): ?>
              <div class="order-item">
                <div class="order-info">
                  <h4>#<?= $order['order_id'] ?: $order['id'] ?></h4>
                  <p><?= htmlspecialchars($order['nama_pemesan']) ?> • <?= date('d M Y H:i', strtotime($order['tanggal_pesan'])) ?></p>
                </div>
                <div class="order-meta">
                  <div class="price"><?= formatRupiah($order['harga'] * $order['jumlah']) ?></div>
                  <span class="status <?= $order['payment_status'] == 'confirmed' ? 'completed' : 'pending' ?>">
                    <?= $order['payment_status'] == 'confirmed' ? 'Paid' : 'Pending' ?>
                  </span>
                </div>
              </div>
              <?php endwhile; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- Menu Terlaris -->
        <div class="content-card">
          <div class="card-header">
            <h3>Menu Terlaris</h3>
            <a href="admin.php">
              Kelola Menu
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </a>
          </div>
          <div class="card-body">
            <?php if($top_menu->num_rows == 0): ?>
              <p style="color: #94a3b8; text-align: center; padding: 32px 0;">Belum ada data penjualan</p>
            <?php else: ?>
              <?php $rank = 1; while($menu = $top_menu->fetch_assoc()): ?>
              <div class="menu-item">
                <div class="rank <?= $rank == 2 ? 'second' : ($rank >= 3 ? 'third' : '') ?>"><?= $rank ?></div>
                <div class="menu-info">
                  <h4><?= htmlspecialchars($menu['nama']) ?></h4>
                  <p>Terjual: <?= $menu['total_sold'] ?> • Revenue: <?= formatRupiah($menu['revenue']) ?></p>
                </div>
                <div class="menu-price"><?= formatRupiah($menu['harga']) ?></div>
              </div>
              <?php $rank++; endwhile; ?>
            <?php endif; ?>
          </div>
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
