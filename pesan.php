<?php
$conn = new mysqli("localhost", "root", "", "happyippiecake");
$menus = $conn->query("SELECT * FROM menu ORDER BY nama ASC");
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $order = isset($_POST['order']) ? $_POST['order'] : [];
    if (!$nama || !$alamat || !$order) {
        $error = "Nama, alamat dan pesanan wajib diisi!";
    } else {
        $order_summary = "Pesanan HappyippieCake%0A";
        $order_summary .= "Nama: $nama%0AAlamat: $alamat%0AOrder:%0A";
        $total_harga = 0;
        foreach($order as $menu_id => $jumlah) {
            $menu_id = intval($menu_id); $jumlah = intval($jumlah);
            if ($jumlah > 0) {
                $conn->query("INSERT INTO pesanan (nama_pemesan, menu_id, jumlah, tanggal_pesan) VALUES ('$nama', $menu_id, $jumlah, CURDATE())");
                $menu = $conn->query("SELECT nama, harga FROM menu WHERE id=$menu_id")->fetch_assoc();
                $subtotal = $menu['harga']*$jumlah;
                $order_summary .= "- ".$menu['nama']." x $jumlah (@Rp".number_format($menu['harga'],0,',','.').") = Rp".number_format($subtotal,0,',','.')."%0A";
                $total_harga += $subtotal;
            }
        }
        $order_summary .= "Total: Rp".number_format($total_harga,0,',','.')."%0A";
        $wa_admin = '628123456789';
        $wa_url = "https://wa.me/$wa_admin?text=" . urlencode($order_summary);
        header("Location: $wa_url");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Pilih & Pesan Kue | HappyippieCake</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .modal-bg { background: rgba(243,197,217,0.97); z-index:99;}
    .modal-box { z-index:100;}
    .card-hover:hover { box-shadow: 0 8px 32px -8px #fd5e53;}
    .footer-link:hover { color:#fd5e53; transform:translateY(-2px);}
  </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-white to-pink-100 font-sans">

  <!-- Navbar Hamburger -->
  <nav class="w-full bg-white shadow sticky top-0 z-20">
    <div class="max-w-6xl mx-auto flex justify-between items-center py-3 px-4">
      <a href="index.php" class="text-2xl font-bold text-pink-500 font-serif">HappyippieCake</a>
      <button id="nav-toggle" class="md:hidden focus:outline-none text-pink-600 p-2" aria-label="open menu">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
      </button>
      <ul id="nav-menu" class="hidden md:flex gap-6 font-medium text-gray-700 md:static absolute top-[60px] left-0 w-full bg-white md:w-auto flex-col md:flex-row shadow md:shadow-none">
        <li><a href="index.php#home" class="nav-link block px-4 py-2">Home</a></li>
        <li><a href="index.php#about" class="nav-link block px-4 py-2">About</a></li>
        <li><a href="menu.php" class="nav-link block px-4 py-2">Menu</a></li>
        <li><a href="index.php#gallery" class="nav-link block px-4 py-2">Gallery</a></li>
      </ul>
    </div>
  </nav>
  <script>
    var navToggle = document.getElementById('nav-toggle');
    var navMenu = document.getElementById('nav-menu');
    navToggle.onclick = function() {
      navMenu.classList.toggle("hidden");
    };
    document.querySelectorAll('#nav-menu a').forEach(link => {
      link.addEventListener('click', function(){
        if(window.innerWidth < 768){
          navMenu.classList.add("hidden");
        }
      });
    });
  </script>

  <div class="max-w-6xl mx-auto py-10 px-2">
    <div class="text-center mb-8">
      <h1 class="text-5xl font-extrabold text-pink-600 mb-2 font-serif" style="font-family:'Pacifico',cursive;">HappyippieCake</h1>
      <p class="text-xl text-gray-700">Pilih & pesan kue istimewa untuk momen spesialmu 🎂</p>
    </div>
    <?php if($error): ?>
      <div class="mb-4 bg-red-100 border-l-4 border-red-400 text-red-700 p-3 rounded"><?= $error ?></div>
    <?php elseif($success): ?>
      <div class="mb-4 bg-green-100 border-l-4 border-green-400 text-green-700 p-3 rounded"><?= $success ?></div>
    <?php endif ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-2 gap-y-6 justify-items-center mb-8">
      <?php foreach($menus as $menu): ?>
        <div class="bg-white rounded-2xl shadow-xl card-hover transition flex flex-col w-64 sm:w-72 md:w-72">
          <img src="<?= htmlspecialchars($menu['gambar']) ?>" alt="<?= htmlspecialchars($menu['nama']) ?>" class="rounded-t-2xl h-40 sm:h-44 w-full object-cover"/>
          <div class="p-4 grow flex flex-col">
            <span class="font-bold text-lg mb-1 text-pink-600"><?= htmlspecialchars($menu['nama']) ?></span>
            <span class="text-gray-700 mb-3 text-sm"><?= htmlspecialchars($menu['deskripsi']) ?></span>
            <div class="flex justify-between items-center mt-auto">
              <span class="bg-pink-100 rounded font-bold text-pink-700 px-3 py-1 text-base">Rp<?= number_format($menu['harga'],0,',','.') ?></span>
              <button class="bg-pink-600 text-white rounded px-5 py-1 hover:bg-pink-700 open-modal font-semibold shadow transition"
                data-id="<?= $menu['id'] ?>"
                data-nama="<?= htmlspecialchars($menu['nama']) ?>"
                data-harga="<?= $menu['harga'] ?>"
                data-gambar="<?= htmlspecialchars($menu['gambar']) ?>"
              >Pesan</button>
            </div>
          </div>
        </div>
      <?php endforeach ?>
    </div>
    <div class="text-center mb-8">
      <span class="italic text-gray-400">* Klik "Pesan" untuk mulai pemesanan, bisa pilih lebih dari satu kue, isi detail, lalu order dengan WhatsApp</span>
    </div>
  </div>

  <!-- Modal Form -->
  <div id="modal" class="fixed inset-0 flex justify-center items-center modal-bg hidden">
    <div class="modal-box bg-white rounded-2xl p-8 w-full max-w-lg shadow-lg relative">
      <span class="absolute top-3 right-6 text-lg text-gray-400 cursor-pointer" onclick="closeModal()">&times;</span>
      <form method="post" onsubmit="return submitOrder();">
        <h2 class="font-bold text-xl text-pink-600 mb-4 text-center font-serif">Form Pemesanan Kue</h2>
        <div class="grid gap-4">
          <div>
            <label class="block mb-1 font-semibold text-pink-700">Nama</label>
            <input type="text" name="nama" class="w-full border-pink-200 rounded mb-1 p-2 font-semibold" required>
          </div>
          <div>
            <label class="block mb-1 font-semibold text-pink-700">Alamat Lengkap</label>
            <input type="text" name="alamat" class="w-full border-pink-200 rounded mb-1 p-2" required>
          </div>
        </div>
        <hr class="my-3">
        <div>
          <div class="font-semibold mb-2">Daftar Pesanan</div>
          <div id="order-list"></div>
          <button type="button" onclick="addOrderField()" class="mt-2 text-pink-600 underline">+ Tambah Menu Lain</button>
        </div>
        <div class="text-right font-bold mt-3">Total Harga: <span id="totalHarga">Rp0</span></div>
        <div class="mt-6">
          <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 py-2 rounded text-white font-semibold">Pesan Via WhatsApp</button>
        </div>
      </form>
    </div>
  </div>

  <footer class="bg-gradient-to-r from-pink-700 via-pink-500 to-pink-400 text-white py-8 shadow-xl mt-16">
    <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center">
      <div class="mb-4 md:mb-0">
        <span class="text-2xl font-bold font-serif" style="font-family:'Pacifico',cursive;">HappyippieCake</span>
        <p class="text-xs mt-1">&copy; 2025 HappyippieCake. All Rights Reserved.</p>
      </div>
      <div class="flex gap-6 items-center">
        <a href="https://instagram.com" target="_blank" class="footer-link text-xl" title="Instagram">IG</a>
        <a href="https://wa.me/628123456789" target="_blank" class="footer-link text-xl" title="WhatsApp">WA</a>
        <a href="#" class="footer-link text-xl" title="Facebook">FB</a>
      </div>
    </div>
  </footer>

  <script>
    // Semua menu
    const menus = [
      <?php
        $result = $conn->query("SELECT id, nama, harga, gambar FROM menu ORDER BY nama ASC");
        foreach($result as $row){
          echo "{id:".$row['id'].",nama:'".addslashes($row['nama'])."',harga:".$row['harga'].",gambar:'".addslashes($row['gambar'])."'},";
        }
      ?>
    ];

    // Modal logic
    let orderFields = [];
    function openModal(menu) {
      document.querySelector('#modal form').reset();
      orderFields = [menu];
      renderOrderFields();
      document.getElementById('modal').style.display = 'flex';
    }
    function closeModal() {
      document.getElementById('modal').style.display = 'none';
      orderFields = [];
    }
    function renderOrderFields() {
      let html = ''; let totalHarga = 0;
      orderFields.forEach((itm, idx) => {
        html += `<div class="flex gap-2 items-center mb-2 animate__animated animate__fadeInDown">
          <img src="${itm.gambar}" class="h-10 w-10 object-cover rounded">
          <select name="order[${itm.id}]" onchange="changeOrderMenu(${idx}, this.value)" class="border-pink-200 rounded px-2 py-1 bg-pink-50 text-pink-800 font-bold">
            ${menus.map(menu =>
              `<option value="${menu.id}" ${menu.id == itm.id ? 'selected':''}>${menu.nama}</option>`
            ).join('')}
          </select>
          <span class="mx-2 font-bold">&times;</span>
          <input type="number" min="1" max="20" value="${itm.jumlah||1}" onchange="changeOrderQty(${idx},this.value)" class="border-pink-200 rounded w-14 p-1 text-center font-bold">
          <span class="flex-grow"></span>
          <span>Rp${(itm.harga*(itm.jumlah||1)).toLocaleString()}</span>
          <button type="button" onclick="removeOrderField(${idx})" class="ml-2 text-red-500 text-lg" title="Hapus">&#x2716;</button>
        </div>`;
        totalHarga += itm.harga*(itm.jumlah||1);
      });
      document.getElementById('order-list').innerHTML = html;
      document.getElementById('totalHarga').innerText = 'Rp'+totalHarga.toLocaleString();
    }
    function addOrderField() {
      orderFields.push({id:menus[0].id, nama:menus[0].nama, harga:menus[0].harga, gambar:menus[0].gambar, jumlah:1});
      renderOrderFields();
    }
    function removeOrderField(idx) {
      orderFields.splice(idx,1); renderOrderFields();
    }
    function changeOrderQty(idx, val){
      orderFields[idx].jumlah = parseInt(val)||1; renderOrderFields();
    }
    function changeOrderMenu(idx, val){
      const menu = menus.find(m=>m.id==val);
      orderFields[idx].id = menu.id; orderFields[idx].nama = menu.nama; orderFields[idx].harga = menu.harga; orderFields[idx].gambar = menu.gambar;
      renderOrderFields();
    }
    document.querySelectorAll('.open-modal').forEach(btn => {
      btn.addEventListener('click', function(){
        const menu = menus.find(m=>m.id==this.dataset.id);
        openModal({...menu, jumlah:1});
      });
    });
    function submitOrder() {
      if (!orderFields.length) return false;
      for (let itm of orderFields) {
        let f = document.createElement('input');
        f.type = 'hidden';
        f.name = `order[${itm.id}]`;
        f.value = itm.jumlah||1;
        document.querySelector('#modal form').appendChild(f);
      }
      return true;
    }
  </script>
</body>
</html>
