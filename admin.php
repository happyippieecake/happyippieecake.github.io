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
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat',Arial,sans-serif;}
    .btn { transition:.2s; }
    .btn:hover { filter:brightness(1.08) saturate(1.2);}
    .badge { font-size:14px; padding:2px 10px; border-radius:10px;}
    .table-row:hover { background:#fff7f8; }
    .menu-img { border-radius:12px; box-shadow:0 2px 8px #eab4cb55; }
  </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-white to-pink-100 min-h-screen">

  <!-- Navbar Admin -->
  <nav class="bg-pink-600 text-white py-4 px-8 shadow-lg">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
      <span class="font-bold text-xl tracking-wide">CRUD Menu Admin</span>
      <ul class="flex gap-8 font-semibold items-center">
        <li><a href="dashboard.php" class="hover:underline opacity-85">Dashboard</a></li>
        <li><a href="admin.php" class="border-b-2 border-white pb-1 font-bold">CRUD Menu</a></li>
        <li><a href="data_pesanan.php" class="hover:underline opacity-85">Data Pesanan</a></li>
      </ul>
    </div>
  </nav>

  <div class="max-w-5xl mx-auto mt-10 px-2">
    <?php if($notif): ?>
      <div class="mb-4 bg-green-100 border-l-4 border-green-400 text-green-700 p-3 rounded shadow font-semibold"><?= $notif ?></div>
    <?php endif ?>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-pink-700 tracking-wide">Daftar Menu Cake</h2>
      <a href="edit_menu.php" class="btn bg-pink-600 text-white px-6 py-2 font-bold rounded-lg shadow hover:bg-pink-700 transition">+ Tambah Menu Baru</a>
    </div>
    <div class="bg-white shadow-lg rounded-xl overflow-x-auto border border-pink-100">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="bg-pink-100 text-pink-700 uppercase text-xs tracking-wide">
            <th class="py-3 px-4 text-left">Nama Cake</th>
            <th class="py-3 px-4 text-left">Deskripsi</th>
            <th class="py-3 px-4 text-center">Harga</th>
            <th class="py-3 px-4 text-center">Gambar</th>
            <th class="py-3 px-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($menus as $m): ?>
          <tr class="table-row border-b last:border-b-0">
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
            <td class="py-3 px-4 text-center">
              <a href="edit_menu.php?id=<?= $m['id']?>" class="btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded mr-2">Edit</a>
              <a href="edit_menu.php?delete=<?= $m['id']?>" onclick="return confirm('Yakin mau hapus menu?')" class="btn bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded">Hapus</a>
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
