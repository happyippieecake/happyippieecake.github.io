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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {font-family: 'Montserrat',Arial,sans-serif;}
    .brand-font { font-family: 'Inter', system-ui, sans-serif; font-weight: 600;}
    .stat-card {transition:.3s;box-shadow:0 4px 32px 0 rgba(253,94,83,0.1);}
    .stat-card:hover { box-shadow: 0 8px 32px -6px #ef476f40, 0 2px 16px -4px #fd5e5340;}
    .glass {background:rgba(255,255,255,0.85);backdrop-filter:blur(6px);}
    .table-row:hover {background:#fff8fa;}
    .icon {display: inline-flex; border-radius:9999px; padding:8px; background:linear-gradient(135deg,#ffd6d6 20%,#ffe6ff 100%);}
  </style>
</head>
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen">

  <!-- Navbar Admin -->
  <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 mb-8">
    <div class="max-w-7xl mx-auto flex items-center justify-between py-4 px-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-pink-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-800 tracking-tight">Dashboard Overview</h1>
          <p class="text-xs text-gray-500 font-medium">Panel Admin HappyippieCake</p>
        </div>
      </div>
      <ul class="flex gap-1 bg-gray-100/50 p-1 rounded-xl">
        <li><a href="dashboard.php" class="px-4 py-2 rounded-lg text-sm font-bold text-pink-600 bg-white shadow-sm ring-1 ring-black/5">Dashboard</a></li>
        <li><a href="admin.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-white hover:text-pink-600 hover:shadow-sm transition-all">Menu</a></li>
        <li><a href="data_pesanan.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-white hover:text-pink-600 hover:shadow-sm transition-all">Pesanan</a></li>
      </ul>
    </div>
  </nav>

  <div class="max-w-7xl mx-auto px-6 pb-20">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
      
      <!-- Card: Total Pesanan -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-pink-50 rounded-full -mr-10 -mt-10 group-hover:bg-pink-100 transition-colors"></div>
        <div class="relative z-10">
          <div class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Total Pesanan</div>
          <div class="flex items-end gap-3 mb-1">
            <span class="text-4xl font-extrabold text-gray-800"><?= number_format($total_pesanan) ?></span>
            <span class="text-gray-400 text-sm mb-1 font-medium">Transaksi</span>
          </div>
          <div class="mt-4 flex items-center text-xs font-bold text-pink-600 bg-pink-50 w-max px-2 py-1 rounded-lg">
            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            All Time
          </div>
        </div>
      </div>

      <!-- Card: Penjualan Minggu Ini -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-green-50 rounded-full -mr-10 -mt-10 group-hover:bg-green-100 transition-colors"></div>
        <div class="relative z-10">
          <div class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Penjualan Minggu Ini</div>
          <div class="flex items-end gap-3 mb-1">
            <span class="text-4xl font-extrabold text-gray-800"><?= number_format($pesanan_minggu) ?></span>
            <span class="text-gray-400 text-sm mb-1 font-medium">Pcs Terjual</span>
          </div>
          <div class="mt-4 flex items-center text-xs font-bold text-green-600 bg-green-50 w-max px-2 py-1 rounded-lg">
            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 8v4l3 2"/></svg>
            Minggu Ini
          </div>
        </div>
      </div>

      <!-- Card: Penjualan Bulan Ini -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-10 -mt-10 group-hover:bg-blue-100 transition-colors"></div>
        <div class="relative z-10">
          <div class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Penjualan Bulan Ini</div>
          <div class="flex items-end gap-3 mb-1">
            <span class="text-4xl font-extrabold text-gray-800"><?= number_format($pesanan_bulan) ?></span>
            <span class="text-gray-400 text-sm mb-1 font-medium">Pcs Terjual</span>
          </div>
          <div class="mt-4 flex items-center text-xs font-bold text-blue-600 bg-blue-50 w-max px-2 py-1 rounded-lg">
            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Bulan Ini
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
        <div>
          <h2 class="text-lg font-bold text-gray-800">Top Menu Bulan Ini</h2>
          <p class="text-sm text-gray-500">Performa penjualan menu berdasarkan pesanan</p>
        </div>
        <button class="text-sm font-bold text-pink-600 hover:text-pink-700 bg-pink-50 hover:bg-pink-100 px-4 py-2 rounded-xl transition-colors">
          Download Report
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-50/50 border-b border-gray-100 text-gray-500 font-semibold uppercase tracking-wider text-xs">
            <tr>
              <th class="py-4 px-6 rounded-tl-3xl">Menu Item</th>
              <th class="py-4 px-6 w-1/3">Popularitas</th>
              <th class="py-4 px-6 text-right rounded-tr-3xl">Total Terjual</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <?php 
            $max_sales = 0;
            foreach($rekap as $r) {
                if($r['total'] > $max_sales) $max_sales = $r['total'];
            }
            // Reset pointer
            $rekap->data_seek(0);
            ?>
            <?php foreach($rekap as $r): 
              $percent = $max_sales > 0 ? ($r['total'] / $max_sales) * 100 : 0;
            ?>
            <tr class="hover:bg-gray-50/80 transition-colors">
              <td class="py-4 px-6 font-bold text-gray-700 capitalize brand-font tracking-wide">
                <?= htmlspecialchars($r['nama']) ?>
              </td>
              <td class="py-4 px-6 align-middle">
                <div class="flex items-center gap-3">
                   <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                     <div class="bg-pink-500 h-2 rounded-full" style="width: <?= $percent ?>%"></div>
                   </div>
                   <span class="text-xs font-bold text-gray-400 w-8"><?= number_format($percent,0) ?>%</span>
                </div>
              </td>
              <td class="py-4 px-6 text-right">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-pink-50 text-pink-700">
                  <?= $r['total'] ?> Pcs
                </span>
              </td>
            </tr>
            <?php endforeach ?>
            <?php if($rekap->num_rows == 0): ?>
              <tr><td colspan="3" class="text-center py-10 text-gray-400">Belum ada data penjualan bulan ini.</td></tr>
            <?php endif ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
