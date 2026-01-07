<?php
/**
 * Migration: Add kategori and stok_tersedia columns to menu table
 * Run this once to update the database schema
 */

$conn = new mysqli("localhost", "root", "", "happyippiecake");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$errors = [];
$success = [];

// 1. Add kategori column
$check = $conn->query("SHOW COLUMNS FROM menu LIKE 'kategori'");
if ($check->num_rows == 0) {
    $sql = "ALTER TABLE menu ADD COLUMN kategori VARCHAR(50) DEFAULT 'Lainnya'";
    if ($conn->query($sql)) {
        $success[] = "Kolom 'kategori' berhasil ditambahkan";
    } else {
        $errors[] = "Gagal menambahkan kolom 'kategori': " . $conn->error;
    }
} else {
    $success[] = "Kolom 'kategori' sudah ada";
}

// 2. Add stok_tersedia column  
$check = $conn->query("SHOW COLUMNS FROM menu LIKE 'stok_tersedia'");
if ($check->num_rows == 0) {
    $sql = "ALTER TABLE menu ADD COLUMN stok_tersedia TINYINT(1) DEFAULT 1";
    if ($conn->query($sql)) {
        $success[] = "Kolom 'stok_tersedia' berhasil ditambahkan";
    } else {
        $errors[] = "Gagal menambahkan kolom 'stok_tersedia': " . $conn->error;
    }
} else {
    $success[] = "Kolom 'stok_tersedia' sudah ada";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Database Migration</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 40px; background: #f1f5f9; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 32px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h1 { color: #1e293b; margin-bottom: 24px; }
        .success { background: #dcfce7; color: #16a34a; padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; }
        .error { background: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; }
        .back-link { display: inline-block; margin-top: 24px; color: #0d9488; text-decoration: none; font-weight: 600; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Database Migration</h1>
        
        <?php if (empty($errors)): ?>
            <div class="success">✅ Migration berhasil dijalankan!</div>
        <?php endif; ?>
        
        <?php foreach ($success as $msg): ?>
            <div class="success">✅ <?= htmlspecialchars($msg) ?></div>
        <?php endforeach; ?>
        
        <?php foreach ($errors as $msg): ?>
            <div class="error">❌ <?= htmlspecialchars($msg) ?></div>
        <?php endforeach; ?>
        
        <a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a>
    </div>
</body>
</html>
