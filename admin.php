<?php
require_once __DIR__ . '/db_connect.php';

// Auto-add stok_tersedia column if it doesn't exist
$stok_column_exists = $conn->query("SHOW COLUMNS FROM menu LIKE 'stok_tersedia'")->num_rows > 0;
if (!$stok_column_exists) {
    $conn->query("ALTER TABLE menu ADD COLUMN stok_tersedia TINYINT(1) DEFAULT 1");
    $stok_column_exists = true; // Column now exists
}

// Auto-add kategori column if it doesn't exist
$kategori_column_exists = $conn->query("SHOW COLUMNS FROM menu LIKE 'kategori'")->num_rows > 0;
if (!$kategori_column_exists) {
    $conn->query("ALTER TABLE menu ADD COLUMN kategori VARCHAR(50) DEFAULT 'Lainnya'");
}

// Handle stock toggle
if (isset($_GET['toggle_stok'])) {
    $id = intval($_GET['toggle_stok']);
    // Get current value and toggle
    $result = $conn->query("SELECT stok_tersedia FROM menu WHERE id = $id");
    if ($row = $result->fetch_assoc()) {
        $new_value = ($row['stok_tersedia'] == 1 || $row['stok_tersedia'] === null) ? 0 : 1;
        $conn->query("UPDATE menu SET stok_tersedia = $new_value WHERE id = $id");
    }
    header('Location: admin.php?notif=Status+stok+berhasil+diubah');
    exit;
}

$menus = $conn->query("SELECT * FROM menu ORDER BY id DESC");
$notif = isset($_GET['notif']) ? $_GET['notif'] : '';

// Get unique categories - only if column exists
$categories = ['Semua'];
$kategori_column_exists = $conn->query("SHOW COLUMNS FROM menu LIKE 'kategori'")->num_rows > 0;
if ($kategori_column_exists) {
    $cat_result = $conn->query("SELECT DISTINCT kategori FROM menu WHERE kategori IS NOT NULL AND kategori != ''");
    if ($cat_result) {
        while ($cat = $cat_result->fetch_row()) {
            if ($cat[0]) $categories[] = $cat[0];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Menu | HappyippieCake Admin</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin_styles.css">
  <style>
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }
    .menu-card {
      background: white;
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      overflow: hidden;
      transition: all 0.2s;
    }
    .menu-card:hover {
      box-shadow: 0 10px 40px rgba(0,0,0,0.08);
      transform: translateY(-2px);
    }
    .menu-card-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      background: #f1f5f9;
    }
    .menu-card-body {
      padding: 20px;
    }
    .menu-card-title {
      font-size: 16px;
      font-weight: 700;
      color: #1e293b;
      margin: 0 0 8px;
    }
    .menu-card-desc {
      font-size: 13px;
      color: #64748b;
      margin: 0 0 16px;
      line-height: 1.5;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .menu-card-price {
      font-size: 18px;
      font-weight: 700;
      color: #0d9488;
    }
    .menu-card-actions {
      display: flex;
      gap: 8px;
      margin-top: 16px;
      padding-top: 16px;
      border-top: 1px solid #f1f5f9;
    }
    .no-image {
      width: 100%;
      height: 180px;
      background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #94a3b8;
    }
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      background: white;
      border-radius: 16px;
      border: 1px solid #e2e8f0;
    }
    .empty-state svg {
      width: 64px;
      height: 64px;
      color: #cbd5e1;
      margin-bottom: 16px;
    }
    .empty-state h3 {
      font-size: 18px;
      color: #64748b;
      margin: 0 0 8px;
    }
    .empty-state p {
      font-size: 14px;
      color: #94a3b8;
      margin: 0 0 24px;
    }
    /* Category & Stock Styles */
    .category-tabs { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
    .category-tab { padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; border: 1px solid #e2e8f0; background: white; color: #64748b; transition: all 0.2s; }
    .category-tab:hover { border-color: #0d9488; color: #0d9488; }
    .category-tab.active { background: #0d9488; color: white; border-color: #0d9488; }
    .menu-card-badge { position: absolute; top: 12px; left: 12px; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: rgba(13,148,136,0.9); color: white; }
    .menu-card-stock { position: absolute; top: 12px; right: 12px; }
    .stock-toggle { display: flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; }
    .stock-toggle.available { background: #dcfce7; color: #16a34a; }
    .stock-toggle.unavailable { background: #fee2e2; color: #dc2626; }
    .stock-toggle:hover { opacity: 0.8; }
    .menu-card { position: relative; }
    .out-of-stock { opacity: 0.6; }
    .out-of-stock .menu-card-img, .out-of-stock .no-image { filter: grayscale(50%); }
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
          <a href="admin.php" class="active">
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
          <h1>Menu Management</h1>
          <p>Kelola katalog produk kue Anda</p>
        </div>
        <a href="edit_menu.php" class="btn btn-primary">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Tambah Menu Baru
        </a>
      </div>

      <!-- Category Tabs -->
      <div class="category-tabs">
        <?php foreach($categories as $cat): ?>
        <button class="category-tab <?= $cat === 'Semua' ? 'active' : '' ?>" onclick="filterCategory('<?= htmlspecialchars($cat) ?>')">
          <?= htmlspecialchars($cat) ?>
        </button>
        <?php endforeach; ?>
      </div>

      <!-- Menu Grid -->
      <?php if($menus->num_rows == 0): ?>
        <div class="empty-state">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
          </svg>
          <h3>Belum Ada Menu</h3>
          <p>Mulai tambahkan menu kue pertama Anda</p>
          <a href="edit_menu.php" class="btn btn-primary">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Menu Pertama
          </a>
        </div>
      <?php else: ?>
        <div class="menu-grid">
          <?php foreach($menus as $m): ?>
          <div class="menu-card">
            <?php if($m['gambar'] && file_exists($m['gambar'])): ?>
              <img src="<?= $m['gambar']?>" class="menu-card-img" alt="<?= htmlspecialchars($m['nama'])?>">
            <?php else: ?>
              <div class="no-image">
                <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
            <?php endif ?>
            <!-- Category Badge -->
            <?php if(isset($m['kategori']) && $m['kategori']): ?>
              <span class="menu-card-badge"><?= htmlspecialchars($m['kategori']) ?></span>
            <?php endif ?>
            <!-- Stock Toggle -->
            <div class="menu-card-stock">
              <a href="?toggle_stok=<?= $m['id'] ?>" class="stock-toggle <?= (isset($m['stok_tersedia']) && !$m['stok_tersedia']) ? 'unavailable' : 'available' ?>" onclick="return confirm('Ubah status stok menu ini?')">
                <?= (isset($m['stok_tersedia']) && !$m['stok_tersedia']) ? '❌ Habis' : '✅ Tersedia' ?>
              </a>
            </div>
            <div class="menu-card-body">
              <h3 class="menu-card-title"><?= htmlspecialchars($m['nama'])?></h3>
              <p class="menu-card-desc"><?= htmlspecialchars($m['deskripsi'])?></p>
              <div class="menu-card-price">Rp<?= number_format($m['harga'],0,',','.')?></div>
              <div class="menu-card-actions">
                <a href="edit_menu.php?id=<?= $m['id']?>" class="btn btn-secondary btn-sm">
                  <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                  Edit
                </a>
                <a href="edit_menu.php?delete=<?= $m['id']?>" onclick="return confirm('Yakin mau hapus menu ini?')" class="btn btn-danger btn-sm">
                  <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  Hapus
                </a>
              </div>
            </div>
          </div>
          <?php endforeach ?>
        </div>

        <script>
          function filterCategory(cat) {
            document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            
            document.querySelectorAll('.menu-card').forEach(card => {
              const cardCat = card.querySelector('.menu-card-badge')?.textContent || '';
              if (cat === 'Semua' || cardCat === cat) {
                card.style.display = '';
              } else {
                card.style.display = 'none';
              }
            });
          }
        </script>

        <div style="margin-top: 24px; padding: 16px 20px; background: white; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; font-size: 14px; color: #64748b;">
          <span>Menampilkan <?= $menus->num_rows ?> produk</span>
          <span>Terakhir diperbarui: <?= date('d M Y') ?></span>
        </div>
      <?php endif ?>
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
