<?php
$conn = new mysqli("localhost", "root", "", "happyippiecake");
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$menu = ['nama'=>'', 'deskripsi'=>'', 'harga'=>'', 'gambar'=>''];
$error = ''; $success = '';

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
      if ($id) {
        $stmt = $conn->prepare("UPDATE menu SET nama=?, deskripsi=?, harga=?, gambar=? WHERE id=?");
        $stmt->bind_param("ssisi", $nama, $deskripsi, $harga, $gambar, $id);
        $stmt->execute();
        $success = "Menu berhasil diupdate!";
      } else {
        $stmt = $conn->prepare("INSERT INTO menu (nama, deskripsi, harga, gambar) VALUES (?,?,?,?)");
        $stmt->bind_param("ssis", $nama, $deskripsi, $harga, $gambar);
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
  if (!$src || !file_exists($src)) return 'https://dummyimage.com/200x150/f3c5d9/fff.png&text=No+Image';
  return $src;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Menu | HappyippieCake</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', Arial, sans-serif;}
    .brand-font { font-family: 'Inter', system-ui, sans-serif; font-weight: 600;}
  </style>
</head>
<body class="bg-gray-50">
  <div class="max-w-lg mx-auto mt-12 bg-white p-8 rounded-xl shadow">
    <h1 class="text-2xl mb-6 font-bold text-pink-600"><?= $id ? 'Edit Menu Kue' : 'Tambah Menu Baru'?></h1>
    <?php if($error): ?>
      <div class="mb-4 bg-red-100 border-l-4 border-red-400 text-red-700 p-3 rounded"><?= $error ?></div>
    <?php elseif($success): ?>
      <div class="mb-4 bg-green-100 border-l-4 border-green-400 text-green-700 p-3 rounded"><?= $success ?></div>
    <?php endif; ?>
    <form method="post" class="space-y-4" enctype="multipart/form-data">
      <div>
        <label class="block mb-1 font-semibold text-pink-700">Nama Kue</label>
        <input type="text" name="nama" class="w-full border border-pink-200 rounded p-2 focus:ring-pink-400" value="<?= htmlspecialchars($menu['nama']) ?>" required>
      </div>
      <div>
        <label class="block mb-1 font-semibold text-pink-700">Deskripsi</label>
        <textarea name="deskripsi" class="w-full border border-pink-200 rounded p-2 focus:ring-pink-400" rows="3" required><?= htmlspecialchars($menu['deskripsi']) ?></textarea>
      </div>
      <div>
        <label class="block mb-1 font-semibold text-pink-700">Harga (Rupiah)</label>
        <input type="number" name="harga" min="1000" class="w-full border border-pink-200 rounded p-2 focus:ring-pink-400" value="<?= htmlspecialchars($menu['harga']) ?>" required>
      </div>
      <div>
        <label class="block mb-1 font-semibold text-pink-700">Upload Gambar</label>
        <input type="file" name="gambar" accept="image/*" class="w-full border border-pink-200 rounded p-2 focus:ring-pink-400">
        <?php if($menu['gambar']): ?>
          <input type="hidden" name="old_gambar" value="<?= htmlspecialchars($menu['gambar']) ?>">
        <?php endif; ?>
      </div>
      <div class="mb-2">
        <span class="block mb-1 font-semibold text-gray-600">Preview Gambar:</span>
        <img id="gambar-preview" src="<?= imgPreview($menu['gambar']) ?>" class="rounded-lg shadow object-cover max-h-40 w-auto">
      </div>
      <div class="flex items-center">
        <button type="submit" class="bg-pink-600 hover:bg-pink-700 px-5 py-2 rounded text-white font-medium"><?= $id ? 'Update' : 'Tambah' ?></button>
        <a href="admin.php" class="ml-5 text-gray-700 underline">Kembali ke Admin</a>
      </div>
    </form>
    <div class="text-xs text-gray-400 mt-4">* Hanya file gambar (jpg/jpeg/png/webp/gif). Maksimal 2MB.</div>
  </div>
</body>
</html>
