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
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Pacifico&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', Arial, sans-serif;}
    .glass {background:rgba(255,255,255,0.88);backdrop-filter:blur(8px);}
    .badge { font-size:13px; padding:3px 14px; border-radius:14px;}
    .table-row:hover { background:#fff6fa;}
    .btn-action {transition:.2s;}
    .btn-action:hover { filter:brightness(1.04) saturate(1.18);}
    .btn-danger {background: #fd5e53; color: #fff; padding: 4px 12px; border-radius: 8px; font-weight:bold;}
    .btn-danger:hover {background:#b33123;}
    .icon-status {display:inline-block; vertical-align:middle;}
  </style>
</head>
<body class="bg-gradient-to-br from-pink-100 via-white to-pink-200 min-h-screen">

  <!-- Navbar Admin -->
  <nav class="bg-pink-700/90 text-white shadow-lg backdrop-blur sticky top-0 z-40 mb-8">
    <div class="max-w-6xl mx-auto flex items-center justify-between py-4 px-6">
      <span class="text-2xl font-bold brand-font tracking-wide flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white opacity-85" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
        Data Pesanan
      </span>
      <ul class="flex gap-8 font-semibold items-center">
        <li><a href="dashboard.php" class="hover:text-yellow-300 transition">Dashboard</a></li>
        <li><a href="admin.php" class="hover:text-yellow-300 transition">CRUD Menu</a></li>
        <li><a href="data_pesanan.php" class="border-b-2 border-white pb-1 font-bold">Data Pesanan</a></li>
      </ul>
    </div>
  </nav>

  <div class="max-w-6xl mx-auto px-2 mb-14">

    <?php if($notif): ?>
      <div class="mb-4 bg-green-100 border-l-4 border-green-400 text-green-700 p-3 rounded shadow font-semibold"><?= $notif ?></div>
    <?php endif ?>

    <!-- Pesanan aktif -->
    <div class="glass shadow-xl rounded-2xl mb-14 border-t-4 border-pink-400 p-7">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-pink-700">Pesanan Aktif</h2>
        <span class="badge bg-yellow-100 text-yellow-700 font-semibold flex items-center gap-1">
          <svg class="icon-status w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">ircle cx="12" cy="12" r="10" fill="#"#fde047" /><path stroke-linecap="round" stroke-width="2" d="M8 12h4l3-4" stroke="#f59e42" /></svg>
          Pending
        </span>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-[15px]">
          <thead>
            <tr class="bg-gradient-to-r from-pink-100 to-pink-50 text-pink-700 uppercase text-xs tracking-wide">
              <th class="py-3 px-4 text-left rounded-tl-xl">Nama Pemesan</th>
              <th class="py-3 px-4 text-left">Menu</th>
              <th class="py-3 px-4 text-center">Jumlah</th>
              <th class="py-3 px-4 text-left">Alamat Pemesan</th>
              <th class="py-3 px-4 text-center">Status</th>
              <th class="py-3 px-4 text-center rounded-tr-xl">Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($pesanan as $p): ?>
            <tr class="table-row border-b last:border-b-0 hover:scale-[1.01] transition-all">
              <td class="py-3 px-4"><?= htmlspecialchars($p['nama_pemesan']) ?></td>
              <td class="py-3 px-4"><?= htmlspecialchars($p['menu_nama']) ?></td>
              <td class="py-3 px-4 text-center font-bold"><?= $p['jumlah'] ?></td>
              <td class="py-3 px-4"><?= htmlspecialchars($p['alamat']) ?></td>
              <td class="py-3 px-4 text-center">
                <span class="badge bg-yellow-100 text-yellow-700 font-semibold flex items-center gap-1 justify-center">
                  <svg class="icon-status w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">ircle cx="12" cy="12" r="10" fill="#"#fde047" /><path stroke-linecap="round" stroke-width="2" d="M8 12h4l3-4" stroke="#f59e42" /></svg>
                  Pending
                </span>
              </td>
              <td class="py-3 px-4 text-center">
                <a href="?selesai=<?= $p['id'] ?>" class="btn-action bg-gradient-to-tr from-green-500 to-green-400 hover:from-green-600 hover:to-green-400 text-white px-4 py-2 rounded-full shadow font-bold text-xs" 
                  onclick="return confirm('Tandai pesanan sudah selesai/dikirim?')"
                  title="Tandai Pesanan Selesai">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                  </svg> Selesai
                </a>
              </td>
            </tr>
          <?php endforeach ?>
          </tbody>
        </table>
        <?php if($pesanan->num_rows==0): ?>
        <div class="text-center text-gray-400 mt-5">Tidak ada pesanan aktif.</div>
        <?php endif ?>
      </div>
    </div>

    <!-- Riwayat pesanan selesai -->
    <div class="glass shadow-xl rounded-2xl border-t-4 border-green-400 p-7">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-green-700">Riwayat Pesanan Selesai</h2>
        <span class="badge bg-green-100 text-green-700 font-semibold flex items-center gap-1">
          <svg class="icon-status w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">ircle cx="12" cy="12" r="10" fill="#"#bbf7d0" /><path stroke-linecap="round" stroke-width="2" d="M9 12l2 2 4-4" stroke="#22a06b" /></svg>
          Selesai
        </span>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-[15px]">
          <thead>
            <tr class="bg-gradient-to-r from-green-100 to-green-50 text-green-900 uppercase text-xs tracking-wide">
              <th class="py-3 px-4 text-left rounded-tl-xl">Nama Pemesan</th>
              <th class="py-3 px-4 text-left">Menu</th>
              <th class="py-3 px-4 text-center">Jumlah</th>
              <th class="py-3 px-4 text-left">Alamat Pemesan</th>
              <th class="py-3 px-4 text-center">Status</th>
              <th class="py-3 px-4 text-center rounded-tr-xl">Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($riwayat as $p): ?>
            <tr class="table-row border-b last:border-b-0 hover:scale-[1.01] transition-all">
              <td class="py-3 px-4"><?= htmlspecialchars($p['nama_pemesan']) ?></td>
              <td class="py-3 px-4"><?= htmlspecialchars($p['menu_nama']) ?></td>
              <td class="py-3 px-4 text-center font-bold"><?= $p['jumlah'] ?></td>
              <td class="py-3 px-4"><?= htmlspecialchars($p['alamat']) ?></td>
              <td class="py-3 px-4 text-center">
                <span class="badge bg-green-100 text-green-700 font-semibold flex items-center gap-1 justify-center">
                  <svg class="icon-status w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">ircle cx="12" cy="12" r="10" fill="#"#bbf7d0" /><path stroke-linecap="round" stroke-width="2" d="M9 12l2 2 4-4" stroke="#22a06b" /></svg>
                  Selesai
                </span>
              </td>
              <td class="py-3 px-4 text-center">
                <a href="?hapus=<?= $p['id'] ?>" class="btn-action bg-gradient-to-tr from-red-500 to-red-400 hover:from-red-600 hover:to-red-400 text-white px-4 py-2 rounded-full shadow font-bold text-xs"
                  onclick="return confirm('Hapus riwayat pesanan ini?')"
                  title="Hapus pesanan">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline -mt-1 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg> Hapus
                </a>
              </td>
            </tr>
          <?php endforeach ?>
          </tbody>
        </table>
        <?php if($riwayat->num_rows==0): ?>
        <div class="text-center text-gray-400 mt-5">Belum ada pesanan selesai.</div>
        <?php endif ?>
      </div>
    </div>
  </div>
</body>
</html>
