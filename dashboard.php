<?php
$conn = new mysqli("localhost", "root", "", "happyippiecake");

// Total pesanan keseluruhan
$total_pesanan = $conn->query("SELECT COUNT(*) FROM pesanan")->fetch_row()[0];

// Periode tanggal
$minggu_awal = date('Y-m-d', strtotime('monday this week'));
$bulan_awal = date('Y-m-01');

// Total jumlah pesanan tiap minggu
$pesanan_minggu = $conn->query("SELECT SUM(jumlah) FROM pesanan WHERE tanggal_pesan >= '$minggu_awal'")->fetch_row()[0] ?: 0;

// Total jumlah pesanan tiap bulan
$pesanan_bulan = $conn->query("SELECT SUM(jumlah) FROM pesanan WHERE tanggal_pesan >= '$bulan_awal'")->fetch_row()[0] ?: 0;

// Rekap detail pesanan bulan ini, per menu
$rekap = $conn->query(
  "SELECT menu.nama, SUM(pesanan.jumlah) as total
   FROM pesanan 
   JOIN menu ON pesanan.menu_id = menu.id
   WHERE pesanan.tanggal_pesan >= '$bulan_awal'
   GROUP BY menu_id
   ORDER BY total DESC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin | HappyippieCake</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50">
  <nav class="bg-pink-600 text-white py-4 px-8 shadow">
  <div class="max-w-6xl mx-auto flex items-center justify-between">
    <span class="font-bold text-xl">Dashboard Admin</span>
    <ul class="flex gap-8 font-medium items-center">
      <li>
        <a href="dashboard_admin.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard_admin.php' ? 'border-b-2 border-white pb-1 font-bold':'' ?>">Dashboard</a>
      </li>
      <li>
        <a href="admin.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin.php' ? 'border-b-2 border-white pb-1 font-bold':'' ?>">CRUD Menu</a>
      </li>
      <li>
        <a href="pesanan_admin.php" class="<?= basename($_SERVER['PHP_SELF'])=='pesanan_admin.php' ? 'border-b-2 border-white pb-1 font-bold':'' ?>">Data Pesanan</a>
      </li>
      <!-- Tambah nav lagi jika perlu (user, laporan, dst) -->
      <!-- <li><a href="logout.php">Logout</a></li> -->
    </ul>
  </div>
</nav>

  <div class="max-w-4xl mx-auto mt-8">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-xl p-6 shadow text-center">
        <div class="text-lg font-medium mb-1">Pesanan Total</div>
        <div class="text-3xl font-bold text-pink-600"><?= $total_pesanan ?></div>
      </div>
      <div class="bg-white rounded-xl p-6 shadow text-center">
        <div class="text-lg font-medium mb-1">Penjualan Minggu Ini</div>
        <div class="text-3xl font-bold text-pink-600"><?= $pesanan_minggu ?></div>
      </div>
      <div class="bg-white rounded-xl p-6 shadow text-center">
        <div class="text-lg font-medium mb-1">Penjualan Bulan Ini</div>
        <div class="text-3xl font-bold text-pink-600"><?= $pesanan_bulan ?></div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 mt-8">
      <div class="text-lg font-bold text-pink-700 mb-3">Rekap Pesanan Bulan Ini Per Menu</div>
      <table class="min-w-full">
        <thead>
          <tr class="bg-pink-100">
            <th class="py-2 px-4 text-left">Menu</th>
            <th class="py-2 px-4 text-left">Jumlah Pesanan</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($rekap as $r): ?>
          <tr>
            <td class="py-2 px-4"><?= htmlspecialchars($r['nama']) ?></td>
            <td class="py-2 px-4"><?= $r['total'] ?></td>
          </tr>
        <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
