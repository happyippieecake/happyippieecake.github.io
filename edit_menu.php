<?php
require_once __DIR__ . '/db_connect.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$menu = ['nama'=>'', 'deskripsi'=>'', 'harga'=>'', 'gambar'=>'', 'kategori'=>'Lainnya', 'stok_tersedia'=>1];
$error = ''; $success = '';

// Available categories
$kategori_options = ['Cake', 'Cookies', 'Brownies', 'Bread', 'Pastry', 'Lainnya'];

if (isset($_GET['delete'])) {
  $del_id = intval($_GET['delete']);
  $res = $conn->query("SELECT gambar FROM menu WHERE id=$del_id");
  if ($row = $res->fetch_assoc()) {
    if ($row['gambar'] && file_exists($row['gambar'])) {
      unlink($row['gambar']);
    }
  }
  $conn->query("DELETE FROM menu WHERE id=$del_id");
  header("Location: admin.php?notif=Menu+berhasil+dihapus");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama = trim($_POST['nama']);
  $deskripsi = trim($_POST['deskripsi']);
  $harga = trim($_POST['harga']);
  $kategori = isset($_POST['kategori']) ? trim($_POST['kategori']) : 'Lainnya';
  $stok_tersedia = isset($_POST['stok_tersedia']) ? 1 : 0;
  $gambar = isset($_POST['old_gambar']) ? $_POST['old_gambar'] : '';

  // Proses upload gambar jika ada file baru
  if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (!in_array($ext, $allowed)) {
      $error = "Ekstensi file tidak diizinkan!";
    } else {
      if (!is_dir('uploads')) mkdir('uploads');
      $namafile = uniqid('cake_').'.'.$ext;
      $dest = 'uploads/'.$namafile;
      if (move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
        // Hapus file lama jika update
        if ($id && $gambar && file_exists($gambar)) unlink($gambar);
        $gambar = $dest;
      } else {
        $error = "Upload gambar gagal!";
      }
    }
  }

  if (!$error) {
    if (!$nama || !$deskripsi || !$harga) {
      $error = "Semua field wajib diisi!";
    } elseif (!is_numeric($harga) || $harga < 1000) {
      $error = "Harga harus angka dan lebih dari 1.000";
    } else {
      // Check if new columns exist
      $has_kategori = $conn->query("SHOW COLUMNS FROM menu LIKE 'kategori'")->num_rows > 0;
      $has_stok = $conn->query("SHOW COLUMNS FROM menu LIKE 'stok_tersedia'")->num_rows > 0;
      
      if ($id) {
        // UPDATE
        if ($has_kategori && $has_stok) {
          $stmt = $conn->prepare("UPDATE menu SET nama=?, deskripsi=?, harga=?, gambar=?, kategori=?, stok_tersedia=? WHERE id=?");
          $stmt->bind_param("ssissii", $nama, $deskripsi, $harga, $gambar, $kategori, $stok_tersedia, $id);
        } else {
          $stmt = $conn->prepare("UPDATE menu SET nama=?, deskripsi=?, harga=?, gambar=? WHERE id=?");
          $stmt->bind_param("ssisi", $nama, $deskripsi, $harga, $gambar, $id);
        }
        $stmt->execute();
        $success = "Menu berhasil diupdate!";
      } else {
        // INSERT
        if ($has_kategori && $has_stok) {
          $stmt = $conn->prepare("INSERT INTO menu (nama, deskripsi, harga, gambar, kategori, stok_tersedia) VALUES (?,?,?,?,?,?)");
          $stmt->bind_param("ssissi", $nama, $deskripsi, $harga, $gambar, $kategori, $stok_tersedia);
        } else {
          $stmt = $conn->prepare("INSERT INTO menu (nama, deskripsi, harga, gambar) VALUES (?,?,?,?)");
          $stmt->bind_param("ssis", $nama, $deskripsi, $harga, $gambar);
        }
        $stmt->execute();
        $success = "Menu berhasil ditambahkan!";
      }
      header('Location: admin.php?notif=Menu+berhasil+'.($id?'diupdate':'ditambahkan'));
      exit;
    }
  }
  $menu = $_POST;
  $menu['gambar'] = $gambar;
} elseif ($id) {
  $result = $conn->query("SELECT * FROM menu WHERE id=$id");
  $menu = $result->fetch_assoc();
}

function imgPreview($src) {
  if (!$src || !file_exists($src)) return 'https://dummyimage.com/200x150/e2e8f0/94a3b8.png&text=No+Image';
  return $src;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $id ? 'Edit Menu' : 'Tambah Menu' ?> | HappyippieCake Admin</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin_styles.css">
  <style>
    .form-card {
      background: white;
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      padding: 32px;
      max-width: 600px;
    }
    .form-title {
      font-size: 20px;
      font-weight: 700;
      color: #1e293b;
      margin: 0 0 24px;
    }
    .image-preview {
      width: 100%;
      max-width: 300px;
      height: 200px;
      border-radius: 12px;
      object-fit: cover;
      background: #f1f5f9;
      border: 2px dashed #e2e8f0;
      margin-bottom: 16px;
    }
    .file-input-wrapper {
      position: relative;
      overflow: hidden;
      display: inline-block;
    }
    .file-input-wrapper input[type=file] {
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0;
      cursor: pointer;
      width: 100%;
      height: 100%;
    }
    .form-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
      padding-top: 24px;
      border-top: 1px solid #e2e8f0;
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
      <!-- Header -->
      <div class="page-header">
        <div>
          <h1><?= $id ? 'Edit Menu' : 'Tambah Menu Baru' ?></h1>
          <p><?= $id ? 'Update informasi menu yang sudah ada' : 'Tambahkan menu kue baru ke katalog' ?></p>
        </div>
        <a href="admin.php" class="btn btn-secondary">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Kembali
        </a>
      </div>

      <?php if($error): ?>
        <div class="alert alert-error">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <?= $error ?>
        </div>
      <?php endif; ?>

      <div class="form-card">
        <h2 class="form-title">Informasi Menu</h2>
        
        <form method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label>Nama Menu</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($menu['nama']) ?>" placeholder="Contoh: Chocolate Layer Cake" required>
          </div>
          
          <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan detail menu kue ini..." required><?= htmlspecialchars($menu['deskripsi']) ?></textarea>
          </div>
          
          <div class="form-group">
            <label>Harga (Rupiah)</label>
            <input type="number" name="harga" min="1000" class="form-control" value="<?= htmlspecialchars($menu['harga']) ?>" placeholder="Contoh: 150000" required>
          </div>
          
          <div class="form-group">
            <label>Foto Menu</label>
            <div style="margin-bottom: 12px;">
              <img id="gambar-preview" src="<?= imgPreview($menu['gambar']) ?>" class="image-preview" alt="Preview">
            </div>
            <div class="file-input-wrapper">
              <button type="button" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Pilih Gambar
              </button>
              <input type="file" name="gambar" accept="image/*" onchange="previewImage(this)">
            </div>
            <?php if($menu['gambar']): ?>
              <input type="hidden" name="old_gambar" value="<?= htmlspecialchars($menu['gambar']) ?>">
            <?php endif; ?>
            <p style="font-size: 12px; color: #94a3b8; margin-top: 8px;">Format: JPG, PNG, GIF, WebP. Maks 2MB</p>
          </div>
          
          <!-- Category -->
          <div class="form-group">
            <label>Kategori</label>
            <select name="kategori" class="form-control">
              <?php foreach($kategori_options as $kat): ?>
                <option value="<?= $kat ?>" <?= (isset($menu['kategori']) && $menu['kategori'] == $kat) ? 'selected' : '' ?>><?= $kat ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <!-- Stock Toggle -->
          <div class="form-group">
            <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
              <input type="checkbox" name="stok_tersedia" value="1" <?= (!isset($menu['stok_tersedia']) || $menu['stok_tersedia']) ? 'checked' : '' ?> style="width: 20px; height: 20px; accent-color: #0d9488;">
              <span>Tersedia untuk dijual</span>
            </label>
            <p style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Hapus centang untuk menandai menu sebagai "Habis"</p>
          </div>
          
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <?= $id ? 'Update Menu' : 'Simpan Menu' ?>
            </button>
            <a href="admin.php" class="btn btn-secondary">Batal</a>
          </div>
        </form>
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

    function previewImage(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('gambar-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
</body>
</html>
