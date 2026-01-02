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
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen">

  <!-- Navbar Admin -->
  <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 mb-8">
    <div class="max-w-7xl mx-auto flex items-center justify-between py-4 px-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-pink-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-800 tracking-tight">Manajemen Menu</h1>
          <p class="text-xs text-gray-500 font-medium">Panel Admin HappyippieCake</p>
        </div>
      </div>
      <ul class="flex gap-1 bg-gray-100/50 p-1 rounded-xl">
        <li><a href="dashboard.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-white hover:text-pink-600 hover:shadow-sm transition-all">Dashboard</a></li>
        <li><a href="admin.php" class="px-4 py-2 rounded-lg text-sm font-bold text-pink-600 bg-white shadow-sm ring-1 ring-black/5">Menu</a></li>
        <li><a href="data_pesanan.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-white hover:text-pink-600 hover:shadow-sm transition-all">Pesanan</a></li>
      </ul>
    </div>
  </nav>

  <div class="max-w-7xl mx-auto px-6 pb-20">
    <?php if($notif): ?>
      <div id="notif-toast" class="fixed top-24 right-6 z-50 flex items-center gap-3 bg-white border border-green-100 p-4 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] animate-fade-in-down">
        <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center text-green-500">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
          <h4 class="font-bold text-gray-800 text-sm">Berhasil!</h4>
          <p class="text-sm text-gray-500"><?= $notif ?></p>
        </div>
        <button onclick="document.getElementById('notif-toast').remove()" class="ml-4 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
      </div>
      <script>setTimeout(() => document.getElementById('notif-toast')?.remove(), 4000);</script>
    <?php endif ?>

    <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">Daftar Produk</h2>
        <p class="text-gray-500 text-sm mt-1">Kelola katalog produk cake Anda</p>
      </div>
      <a href="edit_menu.php" class="flex items-center gap-2 bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-gray-200 transition-all hover:scale-105 active:scale-95 group">
        <div class="bg-white/20 p-1 rounded-lg group-hover:bg-white/30 transition">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
        </div>
        Tambah Menu Baru
      </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-50/50 border-b border-gray-100 text-gray-500 font-semibold uppercase tracking-wider text-xs">
            <tr>
              <th class="py-5 px-6 rounded-tl-3xl">Produk</th>
              <th class="py-5 px-6 w-1/3">Deskripsi</th>
              <th class="py-5 px-6 text-center">Harga</th>
              <th class="py-5 px-6 text-right rounded-tr-3xl">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <?php foreach($menus as $m): ?>
            <tr class="hover:bg-gray-50/80 transition-colors group">
              <td class="py-5 px-6 align-top">
                <div class="flex items-start gap-4">
                  <?php if($m['gambar'] && file_exists($m['gambar'])): ?>
                    <div class="w-20 h-20 rounded-xl overflow-hidden shadow-sm border border-gray-100 shrink-0">
                      <img src="<?= $m['gambar']?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="<?= htmlspecialchars($m['nama'])?>" />
                    </div>
                  <?php else: ?>
                    <div class="w-20 h-20 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                      <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                  <?php endif ?>
                  <div>
                    <h3 class="font-bold text-gray-800 text-lg group-hover:text-pink-600 transition-colors"><?= htmlspecialchars($m['nama'])?></h3>
                    <span class="inline-block mt-1 px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold uppercase tracking-wider rounded">Cake</span>
                  </div>
                </div>
              </td>
              <td class="py-5 px-6 align-top">
                <p class="text-gray-500 text-sm leading-relaxed line-clamp-2"><?= htmlspecialchars($m['deskripsi'])?></p>
              </td>
              <td class="py-5 px-6 text-center align-top">
                <span class="inline-block font-bold text-gray-800 bg-gray-50 px-3 py-1 rounded-lg border border-gray-100">
                  Rp<?= number_format($m['harga'],0,',','.')?>
                </span>
              </td>
              <td class="py-5 px-6 text-right align-top">
                <div class="flex items-center justify-end gap-2">
                  <a href="edit_menu.php?id=<?= $m['id']?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors border border-transparent hover:border-blue-100" title="Edit">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </a>
                  <a href="edit_menu.php?delete=<?= $m['id']?>" onclick="return confirm('Yakin mau hapus menu ini? Tindakan ini tidak dapat dibatalkan.')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-100" title="Hapus">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
        <?php if($menus->num_rows == 0): ?>
          <div class="py-16 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
              <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <p class="text-gray-400 font-medium">Belum ada menu yang ditambahkan.</p>
            <a href="edit_menu.php" class="text-pink-600 font-bold hover:underline mt-2 inline-block">Tambah Menu Pertama</a>
          </div>
        <?php endif ?>
      </div>
      <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 text-xs text-gray-500 flex justify-between items-center">
        <span>Menampilkan <?= $menus->num_rows ?> produk</span>
        <span>Terakhir diperbarui: <?= date('d M Y') ?></span>
      </div>
    </div>
  </div>
</body>
</html>
