<?php
$conn = new mysqli("localhost", "root", "", "happyippiecake");

$total_pesanan = $conn->query("SELECT COUNT(*) FROM pesanan")->fetch_row()[0];
$minggu_awal = date('Y-m-d', strtotime('monday this week'));
$bulan_awal = date('Y-m-01');
$pesanan_minggu = $conn->query("SELECT SUM(jumlah) FROM pesanan WHERE tanggal_pesan >= '$minggu_awal'")->fetch_row()[0] ?: 0;
$pesanan_bulan = $conn->query("SELECT SUM(jumlah) FROM pesanan WHERE tanggal_pesan >= '$bulan_awal'")->fetch_row()[0] ?: 0;

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
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {font-family: 'Montserrat',Arial,sans-serif;}
    .stat-badge {font-size:26px;padding:16px 0;}
    .stat-label {font-size:16px;}
    .stat-card {transition:.25s;}
    .stat-card:hover {box-shadow:0 8px 32px -8px #fd5e53;}
    .table-row:hover {background:#fff6fa;}
  </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-white to-pink-100 min-h-screen">
  <!-- Navbar Admin -->
  <nav class="bg-pink-600 text-white py-4 px-8 shadow-lg">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
      <span class="font-bold text-xl tracking-wide">Dashboard Admin</span>
      <ul class="flex gap-8 font-semibold items-center">
        <li>
          <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard_admin.php' ? 'border-b-2 border-white pb-1 font-bold':'' ?>">Dashboard</a>
        </li>
        <li>
          <a href="admin.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin.php' ? 'border-b-2 border-white pb-1 font-bold':'' ?>">CRUD Menu</a>
        </li>
        <li>
          <a href="data_pesanan.php" class="<?= basename($_SERVER['PHP_SELF'])=='pesanan_admin.php' ? 'border-b-2 border-white pb-1 font-bold':'' ?>">Data Pesanan</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="max-w-5xl mx-auto mt-10 px-2">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mb-10">
      <div class="bg-white rounded-2xl shadow-xl stat-card text-center p-7 border border-pink-100">
        <div class="stat-label text-gray-600 mb-2">Total Pesanan</div>
        <div class="stat-badge font-bold text-4xl text-pink-600"><?= $total_pesanan ?></div>
        <div class="text-xs mt-2 text-gray-400">Keseluruhan sistem</div>
      </div>
      <div class="bg-white rounded-2xl shadow-xl stat-card text-center p-7 border border-green-100">
        <div class="stat-label text-gray-600 mb-2">Penjualan Minggu Ini</div>
        <div class="stat-badge font-bold text-4xl text-green-600"><?= $pesanan_minggu ?></div>
        <div class="text-xs mt-2 text-gray-400">Update minggu berjalan</div>
      </div>
      <div class="bg-white rounded-2xl shadow-xl stat-card text-center p-7 border border-blue-100">
        <div class="stat-label text-gray-600 mb-2">Penjualan Bulan Ini</div>
        <div class="stat-badge font-bold text-4xl text-blue-600"><?= $pesanan_bulan ?></div>
        <div class="text-xs mt-2 text-gray-400">Update bulan berjalan</div>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg mt-10 p-7 border border-pink-100">
      <div class="flex justify-between items-center mb-3">
        <div class="text-xl font-bold text-pink-700">Rekap Pesanan Bulan Ini</div>
        <span class="badge bg-pink-100 text-pink-700 px-3 py-1 rounded font-bold shadow-sm text-sm">Per Menu</span>
      </div>
      <table class="min-w-full text-[15px]">
        <thead>
          <tr class="bg-pink-50 text-pink-700 uppercase text-xs tracking-wide border-b border-pink-100">
            <th class="py-3 px-4 text-left">Menu</th>
            <th class="py-3 px-4 text-left">Jumlah Pesanan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($rekap as $r): ?>
          <tr class="table-row border-b last:border-b-0">
            <td class="py-3 px-4 font-semibold"><?= htmlspecialchars($r['nama']) ?></td>
            <td class="py-3 px-4 font-bold text-pink-600"><?= $r['total'] ?></td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
      <div class="pt-3 text-xs text-gray-400 font-medium text-right">Rekap otomatis update setiap transaksi</div>
    </div>
  </div>
</body>
</html>
