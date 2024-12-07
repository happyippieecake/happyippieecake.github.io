<div class="card-container">
    <?php foreach ($menus as $menu): ?>
        <div class="card">
            <!-- Tambahkan jalur ke folder 'gambar/' -->
            <img src="gambar/<?php echo htmlspecialchars($menu['image']); ?>" alt="<?php echo htmlspecialchars($menu['name']); ?>">
            <h3><?php echo htmlspecialchars($menu['name']); ?></h3>
            <p><?php echo htmlspecialchars($menu['description']); ?></p>
            <p class="price">Rp. <?php echo number_format($menu['price'], 0, ',', '.'); ?></p>
            <a href="pesan.php?id=<?php echo $menu['id']; ?>">Pesan Sekarang</a>
        </div>
    <?php endforeach; ?>
</div>