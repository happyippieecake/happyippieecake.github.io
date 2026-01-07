<?php
/**
 * API: Check for new orders
 * Returns unique pending orders for notification display
 */

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

$conn = new mysqli("localhost", "root", "", "happyippiecake");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get unique pending orders (grouped by order_id)
$result = $conn->query(
    "SELECT 
        pesanan.order_id,
        pesanan.nama_pemesan as nama,
        GROUP_CONCAT(menu.nama SEPARATOR ', ') as menu_items,
        SUM(pesanan.jumlah) as total_qty,
        SUM(menu.harga * pesanan.jumlah) as total,
        MAX(pesanan.id) as max_id,
        MAX(pesanan.tanggal_pesan) as waktu
     FROM pesanan 
     JOIN menu ON pesanan.menu_id = menu.id
     WHERE pesanan.status = 'pending'
     GROUP BY pesanan.order_id, pesanan.nama_pemesan
     ORDER BY max_id DESC
     LIMIT 5"
);

$orders = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = [
            'order_id' => $row['order_id'],
            'nama' => $row['nama'],
            'menu' => strlen($row['menu_items']) > 30 ? substr($row['menu_items'], 0, 30) . '...' : $row['menu_items'],
            'jumlah' => $row['total_qty'],
            'total' => $row['total'],
            'waktu' => date('H:i', strtotime($row['waktu']))
        ];
    }
}

// Count UNIQUE pending orders (by order_id)
$pending_result = $conn->query("SELECT COUNT(DISTINCT order_id) FROM pesanan WHERE status='pending'");
$pending_count = $pending_result ? $pending_result->fetch_row()[0] : 0;

// Get latest order ID for comparison
$latest_id = $conn->query("SELECT MAX(id) FROM pesanan")->fetch_row()[0] ?: 0;

echo json_encode([
    'pending_total' => (int)$pending_count,
    'latest_id' => (int)$latest_id,
    'orders' => $orders,
    'timestamp' => time()
]);

$conn->close();
