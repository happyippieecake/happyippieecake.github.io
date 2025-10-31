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
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {font-family: 'Montserrat',Arial,sans-serif;}
    .brand-font { font-family: 'Pacifico', cursive;}
    .stat-card {transition:.3s;box-shadow:0 4px 32px 0 rgba(253,94,83,0.1);}
    .stat-card:hover { box-shadow: 0 8px 32px -6px #ef476f40, 0 2px 16px -4px #fd5e5340;}
    .glass {background:rgba(255,255,255,0.85);backdrop-filter:blur(6px);}
    .table-row:hover {background:#fff8fa;}
    .icon {display: inline-flex; border-radius:9999px; padding:8px; background:linear-gradient(135deg,#ffd6d6 20%,#ffe6ff 100%);}
  </style>
</head>
<body class="bg-gradient-to-br from-pink-100 via-white to-pink-200 min-h-screen">
  <!-- Navbar Admin -->
  <nav class="bg-pink-700/90 text-white shadow-lg backdrop-blur sticky top-0 z-30">
    <div class="max-w-6xl mx-auto flex items-center justify-between py-4 px-6">
      <span class="text-2xl font-bold brand-font tracking-wide flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white opacity-85" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
        Dashboard Admin
      </span>
      <ul class="flex gap-8 font-semibold items-center">
        <li>
          <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard_admin.php' ? 'border-b-2 border-white pb-1 font-bold':'' ?> hover:text-yellow-300 transition">Dashboard</a>
        </li>
        <li>
          <a href="admin.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin.php' ? 'border-b-2 border-white pb-1 font-bold':'' ?> hover:text-yellow-300 transition">CRUD Menu</a>
        </li>
        <li>
          <a href="data_pesanan.php" class="<?= basename($_SERVER['PHP_SELF'])=='pesanan_admin.php' ? 'border-b-2 border-white pb-1 font-bold':'' ?> hover:text-yellow-300 transition">Data Pesanan</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="max-w-6xl mx-auto mt-10 px-2">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mb-10">
      <div class="glass rounded-2xl stat-card text-center p-7 border-t-4 border-pink-400 shadow-xl relative overflow-hidden">
        <div class="icon absolute -top-5 -right-5"><svg class="w-8 h-8 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A4 4 0 013 15v4a1 1 0 001 1h16a1 1 0 001-1v-4a4 4 0 00-8.248-1.832z"/></svg></div>
        <div class="stat-label text-gray-600 mb-2 text-lg">Total Pesanan</div>
        <div class="stat-badge font-bold text-4xl text-pink-600"><?= $total_pesanan ?></div>
        <div class="text-xs mt-2 text-gray-400">Keseluruhan sistem</div>
      </div>
      <div class="glass rounded-2xl stat-card text-center p-7 border-t-4 border-green-400 shadow-xl relative overflow-hidden">
        <div class="icon absolute -top-5 -right-5"><svg class="w-8 h-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 8v4l3 2"/></svg></div>
        <div class="stat-label text-gray-600 mb-2 text-lg">Penjualan Minggu Ini</div>
        <div class="stat-badge font-bold text-4xl text-green-600"><?= $pesanan_minggu ?></div>
        <div class="text-xs mt-2 text-gray-400">Update minggu berjalan</div>
      </div>
      <div class="glass rounded-2xl stat-card text-center p-7 border-t-4 border-blue-400 shadow-xl relative overflow-hidden">
        <div class="icon absolute -top-5 -right-5"><svg class="w-8 h-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M17 9V7a5 5 0 00-10 0v2a5 5 0 0010 0z"/></svg></div>
        <div class="stat-label text-gray-600 mb-2 text-lg">Penjualan Bulan Ini</div>
        <div class="stat-badge font-bold text-4xl text-blue-600"><?= $pesanan_bulan ?></div>
        <div class="text-xs mt-2 text-gray-400">Update bulan berjalan</div>
      </div>
    </div>

    <div class="glass rounded-2xl shadow-lg mt-10 p-7 border-t-4 border-pink-400">
      <div class="flex flex-wrap justify-between items-center mb-3 gap-2">
        <div class="text-xl font-bold text-pink-700">Rekap Pesanan Bulan Ini</div>
        <span class="badge bg-pink-100 text-pink-700 px-3 py-1 rounded-full font-bold shadow-sm text-sm">Per Menu</span>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-[15px] rounded-xl shadow-inner">
          <thead>
            <tr class="bg-gradient-to-r from-pink-100 to-pink-50 text-pink-700 uppercase text-xs tracking-wide border-b border-pink-200">
              <th class="py-3 px-4 text-left rounded-tl-lg">Menu</th>
              <th class="py-3 px-4 text-left">Jumlah Pesanan</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rekap as $r): ?>
            <tr class="table-row border-b last:border-b-0 transition-all hover:bg-pink-50/80">
              <td class="py-3 px-4 font-semibold capitalize"><?= htmlspecialchars($r['nama']) ?></td>
              <td class="py-3 px-4 font-bold text-pink-600"><?= $r['total'] ?></td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
      <div class="pt-3 text-xs text-gray-400 font-medium text-right">Rekap otomatis update setiap transaksi</div>
    </div>
  </div>
</body>
</html>
