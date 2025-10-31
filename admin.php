<?php
$conn = new mysqli("localhost", "root", "", "happyippiecake");
$menus = $conn->query("SELECT * FROM menu ORDER BY id DESC");
$notif = isset($_GET['notif']) ? $_GET['notif'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Admin Menu | HappyippieCake</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Montserrat',Arial,sans-serif;}
    .brand-font { font-family: 'Pacifico', cursive;}
    .btn { transition:.2s; }
    .btn:hover { filter:brightness(1.10) saturate(1.25);}
    .badge { font-size:14px; padding:2px 10px; border-radius:8px;}
    .table-row:hover { background:#fff6fa;}
    .menu-img { border-radius:12px; box-shadow:0 2px 16px #fd5e5340; border:2px solid #fde4ec;}
    .card-glass { background:rgba(255,255,255,0.85); backdrop-filter:blur(6px);}
    .icon-cake { display:inline-block; background:linear-gradient(135deg,#ffe6e6 60%,#ffd6e6 100%); border-radius:9999px; padding:8px; margin-right:8px;}
    .rounded-full { border-radius:9999px;}
  </style>
</head>
<body class="bg-gradient-to-br from-pink-100 via-white to-pink-200 min-h-screen">

  <!-- Navbar Admin -->
  <nav class="bg-pink-700/90 text-white shadow-lg backdrop-blur sticky top-0 z-30">
    <div class="max-w-6xl mx-auto flex items-center justify-between py-4 px-6">
      <span class="text-2xl font-bold brand-font tracking-wide flex items-center gap-2">
        <span class="icon-cake"><svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M8 17a4 4 0 108 0M12 3v3m-7 6a9.06 9.06 0 003.72 7.44M21 12a9.06 9.06 0 01-3.72 7.44"/></svg></span>
        CRUD Menu Admin
      </span>
      <ul class="flex gap-8 font-semibold items-center">
        <li><a href="dashboard.php" class="hover:text-yellow-300 transition">Dashboard</a></li>
        <li><a href="admin.php" class="border-b-2 border-white pb-1 font-bold hover:text-yellow-300 transition">CRUD Menu</a></li>
        <li><a href="data_pesanan.php" class="hover:text-yellow-300 transition">Data Pesanan</a></li>
      </ul>
    </div>
  </nav>

  <div class="max-w-5xl mx-auto mt-10 px-2">
    <?php if($notif): ?>
      <div class="mb-4 bg-green-100 border-l-4 border-green-400 text-green-700 p-3 rounded shadow font-semibold"><?= $notif ?></div>
    <?php endif ?>
    <div class="flex flex-wrap justify-between items-center mb-7 gap-3">
      <div class="card-glass rounded-xl flex items-center p-5 shadow font-bold text-pink-700 text-xl tracking-wide gap-2">
        <span class="icon-cake"><svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M8 17a4 4 0 108 0M12 3v3m-7 6a9.06 9.06 0 003.72 7.44M21 12a9.06 9.06 0 01-3.72 7.44"/></svg></span>
        Daftar Menu Cake
      </div>
      <a href="edit_menu.php" class="btn bg-gradient-to-tr from-pink-500 to-pink-400 text-white px-7 py-3 font-bold rounded-full shadow hover:from-pink-600 hover:to-pink-400 transition text-lg tracking-wide">+ Tambah Menu Baru</a>
    </div>
    <div class="card-glass bg-white shadow-lg rounded-2xl overflow-x-auto border border-pink-200 p-2">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="bg-gradient-to-r from-pink-100 to-pink-50 text-pink-700 uppercase text-xs tracking-wide">
            <th class="py-3 px-4 text-left rounded-tl-xl">Nama Cake</th>
            <th class="py-3 px-4 text-left">Deskripsi</th>
            <th class="py-3 px-4 text-center">Harga</th>
            <th class="py-3 px-4 text-center">Gambar</th>
            <th class="py-3 px-4 text-center rounded-tr-xl">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($menus as $m): ?>
          <tr class="table-row border-b last:border-b-0 hover:scale-[1.01] transition-all">
            <td class="py-3 px-4 font-semibold text-pink-700"><?= htmlspecialchars($m['nama'])?></td>
            <td class="py-3 px-4 text-gray-900"><?= htmlspecialchars($m['deskripsi'])?></td>
            <td class="py-3 px-4 text-center font-bold text-pink-600">Rp<?= number_format($m['harga'],0,',','.')?></td>
            <td class="py-3 px-4 text-center">
              <?php if($m['gambar'] && file_exists($m['gambar'])): ?>
                <img src="<?= $m['gambar']?>" class="menu-img w-16 h-16 object-cover mx-auto border" />
              <?php else: ?>
                <span class="badge bg-gray-100 text-gray-400 font-semibold">Belum ada</span>
              <?php endif ?>
            </td>
            <td class="py-3 px-4 text-center flex gap-2 justify-center">
              <a href="edit_menu.php?id=<?= $m['id']?>" class="btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded-full shadow transition font-semibold">Edit</a>
              <a href="edit_menu.php?delete=<?= $m['id']?>" onclick="return confirm('Yakin mau hapus menu?')" class="btn bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded-full shadow transition font-semibold">Hapus</a>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
      <div class="p-3 text-xs text-gray-400 font-medium text-right">Menampilkan <?= $menus->num_rows ?> menu</div>
    </div>
  </div>
</body>
</html>
