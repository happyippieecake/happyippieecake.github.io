<?php
$conn = new mysqli("localhost", "root", "", "happyippiecake");

// Ambil semua data menu
$menus = $conn->query("SELECT * FROM menu ORDER BY id DESC");

// Tambah notifikasi
$notif = isset($_GET['notif']) ? $_GET['notif'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Admin Menu | HappyippieCake</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<nav class="bg-pink-600 text-white py-4 px-8 shadow">
  <div class="max-w-6xl mx-auto flex items-center justify-between">
    <span class="font-bold text-xl">CRUD Menu Admin</span>
    <ul class="flex gap-8 font-medium items-center">
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="admin.php" class="border-b-2 border-white pb-1 font-bold">CRUD Menu</a></li>
      <li><a href="pesanan_admin.php">Data Pesanan</a></li>
    </ul>
  </div>
</nav>
<div class="max-w-4xl mx-auto mt-8">
  <?php if($notif): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-400 text-green-700 p-3 rounded"><?= $notif ?></div>
  <?php endif ?>
  <a href="edit_menu.php" class="mb-4 inline-block bg-pink-600 text-white px-6 py-2 rounded shadow hover:bg-pink-700">+ Tambah Menu Baru</a>
  <div class="bg-white shadow rounded overflow-x-auto">
    <table class="min-w-full">
      <thead>
        <tr class="bg-pink-100">
          <th class="py-2 px-4">Nama</th>
          <th class="py-2 px-4">Deskripsi</th>
          <th class="py-2 px-4">Harga</th>
          <th class="py-2 px-4">Gambar</th>
          <th class="py-2 px-4">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($menus as $m): ?>
        <tr>
          <td class="py-1 px-4"><?= htmlspecialchars($m['nama'])?></td>
          <td class="py-1 px-4"><?= htmlspecialchars($m['deskripsi'])?></td>
          <td class="py-1 px-4">Rp<?= number_format($m['harga'],0,',','.')?></td>
          <td class="py-1 px-4">
            <?php if($m['gambar'] && file_exists($m['gambar'])): ?>
              <img src="<?= $m['gambar']?>" class="h-12 rounded shadow" />
            <?php else: ?>
              <span class="italic text-gray-400">-</span>
            <?php endif ?>
          </td>
          <td class="py-1 px-4">
            <a href="edit_menu.php?id=<?= $m['id']?>" class="text-blue-600 underline mr-2">Edit</a>
            <a href="edit_menu.php?delete=<?= $m['id']?>" onclick="return confirm('Yakin hapus menu?')" class="text-red-600 underline">Hapus</a>
          </td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
