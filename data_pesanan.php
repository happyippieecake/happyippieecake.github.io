<?php
$conn = new mysqli("localhost","root","","happyippiecake");

// Hapus pesanan dari riwayat
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM pesanan WHERE id=$id");
    header('Location: data_pesanan.php?notif=Pesanan berhasil dihapus');
    exit;
}

// Tandai pesanan selesai
if(isset($_GET['selesai'])){
    $id = intval($_GET['selesai']);
    $conn->query("UPDATE pesanan SET status='selesai' WHERE id=$id");
    header('Location: data_pesanan.php?notif=Pesanan diarsipkan (selesai)');
    exit;
}
$notif = isset($_GET['notif']) ? $_GET['notif'] : '';

// Data pesanan aktif
$pesanan = $conn->query(
    "SELECT pesanan.id, pesanan.nama_pemesan, pesanan.jumlah, pesanan.alamat, pesanan.status, menu.nama as menu_nama
     FROM pesanan 
     JOIN menu ON pesanan.menu_id=menu.id
     WHERE pesanan.status = 'pending'
     ORDER BY pesanan.id DESC"
);

// Data pesanan selesai (riwayat)
$riwayat = $conn->query(
    "SELECT pesanan.id, pesanan.nama_pemesan, pesanan.jumlah, pesanan.alamat, pesanan.status, menu.nama as menu_nama
     FROM pesanan 
     JOIN menu ON pesanan.menu_id=menu.id
     WHERE pesanan.status = 'selesai'
     ORDER BY pesanan.id DESC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Pesanan | HappyippieCake</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', Arial, sans-serif;}
    .badge { font-size:13px; padding:3px 14px; border-radius:12px;}
    .table-row:hover { background:#fff6fa;}
    .btn-danger {background: #fd5e53; color: #fff; padding: 4px 12px; border-radius: 8px; font-weight:bold;}
    .btn-danger:hover {background:#b33123;}
  </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-white to-pink-100 min-h-screen">
  <nav class="bg-pink-600 text-white py-4 px-8 shadow-lg mb-6">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
      <span class="font-bold text-xl tracking-wide">Data Pesanan</span>
      <ul class="flex gap-8 font-semibold items-center">
        <li><a href="dashboard.php" class="hover:underline">Dashboard</a></li>
        <li><a href="admin.php" class="hover:underline">CRUD Menu</a></li>
        <li><a href="data_pesanan.php" class="border-b-2 border-white pb-1 font-bold">Data Pesanan</a></li>
      </ul>
    </div>
  </nav>
  <div class="max-w-5xl mx-auto px-2 mb-12">
    <?php if($notif): ?>
      <div class="mb-4 bg-green-100 border-l-4 border-green-400 text-green-700 p-3 rounded shadow font-semibold"><?= $notif ?></div>
    <?php endif ?>

    <!-- Pesanan aktif -->
    <div class="bg-white shadow-lg rounded-xl mb-12 border border-pink-100 p-6">
      <h2 class="text-2xl font-bold text-pink-700 mb-5">Pesanan Aktif</h2>
      <table class="min-w-full text-[15px]">
        <thead>
            <tr class="bg-pink-100 text-pink-700 uppercase text-xs tracking-wide">
                <th class="py-3 px-4 text-left">Nama Pemesan</th>
                <th class="py-3 px-4 text-left">Menu</th>
                <th class="py-3 px-4 text-center">Jumlah</th>
                <th class="py-3 px-4 text-left">Alamat Pemesan</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($pesanan as $p): ?>
            <tr class="table-row border-b">
                <td class="py-3 px-4"><?= htmlspecialchars($p['nama_pemesan']) ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($p['menu_nama']) ?></td>
                <td class="py-3 px-4 text-center font-bold"><?= $p['jumlah'] ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($p['alamat']) ?></td>
                <td class="py-3 px-4 text-center">
                    <span class="badge bg-yellow-100 text-yellow-700 font-semibold">Pending</span>
                </td>
                <td class="py-3 px-4 text-center">
                    <a href="?selesai=<?= $p['id'] ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded shadow text-xs font-bold" 
                       onclick="return confirm('Tandai pesanan sudah selesai/dikirim?')">
                        Tandai Selesai
                    </a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
      </table>
      <?php if($pesanan->num_rows==0): ?>
        <div class="text-center text-gray-400 mt-4">Tidak ada pesanan aktif.</div>
      <?php endif ?>
    </div>

    <!-- Riwayat pesanan selesai -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-100 p-6">
      <h2 class="text-xl font-bold text-pink-500 mb-5">Riwayat Pesanan Selesai</h2>
      <table class="min-w-full text-[15px]">
        <thead>
            <tr class="bg-green-50 text-green-900 uppercase text-xs tracking-wide">
                <th class="py-3 px-4 text-left">Nama Pemesan</th>
                <th class="py-3 px-4 text-left">Menu</th>
                <th class="py-3 px-4 text-center">Jumlah</th>
                <th class="py-3 px-4 text-left">Alamat Pemesan</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($riwayat as $p): ?>
            <tr class="table-row border-b last:border-b-0">
                <td class="py-3 px-4"><?= htmlspecialchars($p['nama_pemesan']) ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($p['menu_nama']) ?></td>
                <td class="py-3 px-4 text-center font-bold"><?= $p['jumlah'] ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($p['alamat']) ?></td>
                <td class="py-3 px-4 text-center">
                    <span class="badge bg-green-100 text-green-700 font-semibold">Selesai</span>
                </td>
                <td class="py-3 px-4 text-center">
                    <a href="?hapus=<?= $p['id'] ?>" class="btn-danger" onclick="return confirm('Hapus riwayat pesanan ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
      </table>
      <?php if($riwayat->num_rows==0): ?>
        <div class="text-center text-gray-400 mt-4">Belum ada pesanan selesai.</div>
      <?php endif ?>
    </div>
  </div>
</body>
</html>
