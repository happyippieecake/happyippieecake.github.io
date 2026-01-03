<?php
$conn = new mysqli("localhost","root","","happyippiecake");

// Hapus pesanan dari riwayat
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM pesanan WHERE id=$id");
    header('Location: data_pesanan.php?notif=Pesanan berhasil dihapus');
    exit;
}

// Tandai pesanan selesai (single)
if(isset($_GET['selesai'])){
    $id = intval($_GET['selesai']);
    $conn->query("UPDATE pesanan SET status='selesai' WHERE id=$id");
    // Also update payment status if exists
    $conn->query("UPDATE payments SET status='confirmed' WHERE pesanan_id=$id");
    header('Location: data_pesanan.php?notif=Pesanan diarsipkan (selesai)');
    exit;
}

// Tandai pesanan selesai (group)
if(isset($_GET['selesai_group'])){
    $ids = explode(',', $_GET['selesai_group']);
    foreach($ids as $id) {
        $id = intval($id);
        if($id > 0) {
            $conn->query("UPDATE pesanan SET status='selesai' WHERE id=$id");
            $conn->query("UPDATE payments SET status='confirmed' WHERE pesanan_id=$id");
        }
    }
    header('Location: data_pesanan.php?notif=Grup pesanan berhasil diproses');
    exit;
}
$notif = isset($_GET['notif']) ? $_GET['notif'] : '';

// Data pesanan aktif dengan info pembayaran
$pesanan = $conn->query(
    "SELECT pesanan.id, pesanan.nama_pemesan, pesanan.jumlah, pesanan.alamat, pesanan.status, 
            menu.nama as menu_nama, menu.harga,
            payments.amount, payments.payment_method, payments.bukti_transfer, payments.status as payment_status, payments.order_id
     FROM pesanan 
     JOIN menu ON pesanan.menu_id=menu.id
     LEFT JOIN payments ON pesanan.id=payments.pesanan_id
     WHERE pesanan.status = 'pending'
     ORDER BY pesanan.id DESC"
);

// Data pesanan selesai (riwayat) dengan info pembayaran
$riwayat = $conn->query(
    "SELECT pesanan.id, pesanan.nama_pemesan, pesanan.jumlah, pesanan.alamat, pesanan.status, 
            menu.nama as menu_nama, menu.harga,
            payments.amount, payments.payment_method, payments.bukti_transfer, payments.status as payment_status
     FROM pesanan 
     JOIN menu ON pesanan.menu_id=menu.id
     LEFT JOIN payments ON pesanan.id=payments.pesanan_id
     WHERE pesanan.status = 'selesai'
     ORDER BY pesanan.id DESC"
);

// Function to format payment method
function formatPaymentMethod($method) {
    $methods = [
        'bank_bca' => 'Bank BCA',
        'bank_mandiri' => 'Bank Mandiri', 
        'bank_bri' => 'Bank BRI',
        'qris' => 'QRIS/E-Wallet',
        'whatsapp' => 'WhatsApp'
    ];
    return $methods[$method] ?? $method;
}

// Function to format currency
function formatRupiah($amount) {
    return 'Rp' . number_format($amount, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Pesanan | HappyippieCake</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', Arial, sans-serif;}
    .brand-font { font-family: 'Inter', system-ui, sans-serif; font-weight: 600;}
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
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen">

  <!-- Navbar Admin -->
  <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 mb-8">
    <div class="max-w-7xl mx-auto flex items-center justify-between py-4 px-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-pink-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-800 tracking-tight">Data Pesanan</h1>
          <p class="text-xs text-gray-500 font-medium">Panel Admin HappyippieCake</p>
        </div>
      </div>
      <ul class="flex gap-1 bg-gray-100/50 p-1 rounded-xl">
        <li><a href="dashboard.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-white hover:text-pink-600 hover:shadow-sm transition-all">Dashboard</a></li>
        <li><a href="admin.php" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-white hover:text-pink-600 hover:shadow-sm transition-all">Menu</a></li>
        <li><a href="data_pesanan.php" class="px-4 py-2 rounded-lg text-sm font-bold text-pink-600 bg-white shadow-sm ring-1 ring-black/5">Pesanan</a></li>
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

    <?php
    // Data pesanan aktif dengan info pembayaran
    $pesanan = $conn->query(
        "SELECT pesanan.id, pesanan.nama_pemesan, pesanan.jumlah, pesanan.alamat, pesanan.status, pesanan.order_id as link_order_id,
                menu.nama as menu_nama, menu.harga,
                payments.amount, payments.payment_method, payments.bukti_transfer, payments.status as payment_status, payments.order_id
         FROM pesanan 
         JOIN menu ON pesanan.menu_id=menu.id
         LEFT JOIN payments ON pesanan.id=payments.pesanan_id
         WHERE pesanan.status = 'pending'
         ORDER BY pesanan.id DESC"
    );

    // Grouping Logic PHP
    $grouped_pesanan = [];
    while($row = $pesanan->fetch_assoc()) {
        if (!empty($row['link_order_id'])) {
            $key = $row['link_order_id'];
        } else {
            // Fallback for old data
            $key = md5($row['nama_pemesan'] . $row['alamat'] . date('Y-m-d', strtotime($row['created_at'] ?? 'now'))); 
        }
        
        if(!isset($grouped_pesanan[$key])) {
            $grouped_pesanan[$key] = [
                'nama_pemesan' => $row['nama_pemesan'],
                'alamat' => $row['alamat'],
                'items' => [],
                'payment_info' => null,
                'ids' => [],
                'display_order_id' => $row['link_order_id'] ?? $row['order_id'] // Use link_order_id first, then payment order_id
            ];
        }
        
        $grouped_pesanan[$key]['items'][] = [
            'menu_nama' => $row['menu_nama'],
            'jumlah' => $row['jumlah'],
            'harga' => $row['harga'],
            'subtotal' => $row['harga'] * $row['jumlah']
        ];
        $grouped_pesanan[$key]['ids'][] = $row['id'];
        
        // If this row has payment info, attach it to the group
        if($row['payment_method'] && !$grouped_pesanan[$key]['payment_info']) {
            $grouped_pesanan[$key]['payment_info'] = [
                'amount' => $row['amount'],
                'method' => $row['payment_method'],
                'bukti' => $row['bukti_transfer'],
                'status' => $row['payment_status'],
                'order_id' => $row['order_id']
            ];
            // If we didn't have a display ID from column, use the one from payment
            if(empty($grouped_pesanan[$key]['display_order_id'])) {
               $grouped_pesanan[$key]['display_order_id'] = $row['order_id'];
            }
        }
    }
    ?>

    <!-- Pesanan Aktif Section -->
    <div class="mb-10">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-800">Pesanan Masuk</h2>
          <p class="text-gray-500 text-sm mt-1">Kelola pesanan yang perlu diproses</p>
        </div>
        <span class="px-4 py-2 bg-yellow-50 text-yellow-700 rounded-full text-sm font-bold border border-yellow-100 flex items-center gap-2">
          <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
          </span>
          Pending Process
        </span>
      </div>

      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead class="bg-gray-50/50 border-b border-gray-100 text-gray-500 font-semibold uppercase tracking-wider text-xs">
              <tr>
                <th class="py-5 px-6 rounded-tl-3xl">Pelanggan</th>
                <th class="py-5 px-6">Detail Pesanan</th>
                <th class="py-5 px-6 text-center">Metode Bayar</th>
                <th class="py-5 px-6 text-center">Status Bayar</th>
                <th class="py-5 px-6 text-right rounded-tr-3xl">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
            <?php if(empty($grouped_pesanan)): ?>
              <tr>
                <td colspan="5" class="py-20 text-center">
                  <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                  </div>
                  <p class="text-gray-400 font-medium">Tidak ada pesanan aktif saat ini</p>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach($grouped_pesanan as $group): 
                  $total_tagihan = 0;
                  foreach($group['items'] as $itm) $total_tagihan += $itm['subtotal'];
                  $pay = $group['payment_info'];
              ?>
              <tr class="hover:bg-gray-50/80 transition-colors group">
                <td class="py-5 px-6 align-top">
                  <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-500 font-bold text-sm shrink-0 uppercase">
                      <?= substr($group['nama_pemesan'], 0, 2) ?>
                    </div>
                    <div>
                      <div class="font-bold text-gray-800 text-base mb-1"><?= htmlspecialchars($group['nama_pemesan']) ?></div>
                      <div class="flex items-center gap-1.5 text-gray-500 text-xs mb-2">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <?= htmlspecialchars($group['alamat']) ?>
                      </div>
                      <?php if($pay && $pay['order_id']): ?>
                        <div class="text-[10px] font-mono text-pink-500 bg-pink-50 px-2 py-0.5 rounded inline-block">Order #<?= $pay['order_id'] ?></div>
                      <?php endif ?>
                    </div>
                  </div>
                </td>
                
                <td class="py-5 px-6 align-top">
                  <div class="space-y-3">
                    <?php foreach($group['items'] as $item): ?>
                    <div class="flex justify-between items-start border-b border-gray-100 pb-2 last:border-0 last:pb-0">
                      <div>
                        <div class="font-bold text-pink-700 text-sm brand-font tracking-wide"><?= htmlspecialchars($item['menu_nama']) ?></div>
                        <div class="text-xs text-gray-500"><?= $item['jumlah'] ?> pcs × <?= formatRupiah($item['harga']) ?></div>
                      </div>
                      <div class="font-semibold text-gray-700 text-sm"><?= formatRupiah($item['subtotal']) ?></div>
                    </div>
                    <?php endforeach ?>
                    <div class="pt-2 border-t border-dashed border-gray-200 flex justify-between items-center text-base font-bold text-gray-900">
                      <span>Total Tagihan</span>
                      <span><?= formatRupiah($total_tagihan) ?></span>
                    </div>
                  </div>
                </td>

                <td class="py-5 px-6 align-top text-center">
                  <?php if($pay): ?>
                    <div class="flex flex-col items-center gap-2">
                      <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border
                        <?php 
                          if(strpos($pay['method'], 'bank') !== false) echo 'bg-blue-50 text-blue-700 border-blue-100';
                          elseif($pay['method'] == 'qris') echo 'bg-purple-50 text-purple-700 border-purple-100';
                          else echo 'bg-gray-50 text-gray-700 border-gray-200';
                        ?>">
                        <?= formatPaymentMethod($pay['method']) ?>
                      </span>
                      
                      <?php if($pay['bukti']): ?>
                        <a href="<?= htmlspecialchars($pay['bukti']) ?>" target="_blank" 
                           class="text-xs text-blue-600 hover:text-blue-800 font-medium underline flex items-center gap-1 group-hover:bg-blue-50 px-2 py-1 rounded transition">
                          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                          Cek Bukti
                        </a>
                      <?php else: ?>
                        <span class="text-xs text-gray-400 italic">Belum upload</span>
                      <?php endif ?>
                    </div>
                  <?php else: ?>
                    <span class="text-gray-400 text-xs flex items-center justify-center gap-1">
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                      Order via WA
                    </span>
                  <?php endif ?>
                </td>

                <td class="py-5 px-6 align-top text-center">
                  <?php if($pay && ($pay['status'] == 'confirmed' || $pay['bukti'])): ?>
                    <div class="inline-flex flex-col items-center">
                      <div class="w-8 h-8 <?php echo $pay['status']=='confirmed'?'bg-green-100 text-green-600':'bg-blue-100 text-blue-600'; ?> rounded-full flex items-center justify-center mb-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      </div>
                      <span class="text-[10px] font-bold <?php echo $pay['status']=='confirmed'?'text-green-600':'text-blue-600'; ?> uppercase tracking-wide">
                        <?= $pay['status']=='confirmed' ? 'Paid' : 'Sudah Bayar' ?>
                      </span>
                    </div>
                  <?php else: ?>
                    <div class="inline-flex flex-col items-center">
                      <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mb-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      </div>
                      <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wide">Pending</span>
                    </div>
                  <?php endif ?>
                </td>

                <td class="py-5 px-6 align-middle text-right">
                  <div class="flex items-center justify-end gap-2">                    
                    <!-- We iterate through all IDs in the group to mark them done -->
                    <?php $ids_str = implode(',', $group['ids']); ?>
                    <a href="?selesai_group=<?= $ids_str ?>" 
                       onclick="return confirm('Selesaikan semua item dalam pesanan ini?')"
                       class="group/btn flex items-center gap-2 pl-4 pr-5 py-2.5 bg-gray-900 text-white rounded-full hover:bg-green-600 hover:shadow-lg hover:shadow-green-200 transition-all duration-300">
                       <span class="font-bold text-xs tracking-wide group-hover/btn:text-white">Proses Semua</span>
                       <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center group-hover/btn:bg-white group-hover/btn:text-green-600 transition-colors">
                         <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                       </div>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach ?>
            <?php endif ?>
            </tbody>
          </table>
        </div>
      </div>
    </div><!-- Riwayat Section -->
    <div>
      <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Riwayat Selesai
      </h2>
      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden opacity-80 hover:opacity-100 transition-opacity">
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b border-gray-100 text-gray-400 font-medium text-xs uppercase">
              <tr>
                <th class="py-4 px-6">Pelanggan</th>
                <th class="py-4 px-6">Menu</th>
                <th class="py-4 px-6 text-right">Total</th>
                <th class="py-4 px-6 text-center">Waktu</th>
                <th class="py-4 px-6 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
            <?php foreach($riwayat as $p): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="py-4 px-6 font-medium text-gray-700">
                  <?= htmlspecialchars($p['nama_pemesan']) ?>
                  <span class="block text-xs text-gray-400 font-normal"><?= htmlspecialchars($p['alamat']) ?></span>
                </td>
                <td class="py-4 px-6">
                  <span class="text-gray-600 brand-font"><?= htmlspecialchars($p['menu_nama']) ?></span>
                  <span class="text-gray-400 text-xs ml-1">(<?= $p['jumlah'] ?>x)</span>
                </td>
                <td class="py-4 px-6 text-right font-bold text-gray-600">
                  <?= formatRupiah($p['harga'] * $p['jumlah']) ?>
                </td>
                <td class="py-4 px-6 text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Selesai
                  </span>
                </td>
                <td class="py-4 px-6 text-right">
                  <a href="?hapus=<?= $p['id'] ?>" 
                     onclick="return confirm('Hapus riwayat permanen?')"
                     class="text-gray-400 hover:text-red-500 transition-colors p-2 hover:bg-red-50 rounded-full inline-block" title="Hapus">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </a>
                </td>
              </tr>
            <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</body>
</html>
