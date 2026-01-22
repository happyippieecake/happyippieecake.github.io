<?php
/**
 * Export Laporan Pesanan ke CSV
 * Supports filtering by date range and status
 */

require_once __DIR__ . '/db_connect.php';

// Get filter parameters
$filter_days = isset($_GET['days']) ? intval($_GET['days']) : 0;
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build query
$where_clauses = [];
$params = [];
$types = '';

if ($filter_days > 0) {
    $date_from = date('Y-m-d', strtotime("-{$filter_days} days"));
    $where_clauses[] = "pesanan.tanggal_pesan >= ?";
    $params[] = $date_from;
    $types .= 's';
}

if ($filter_status === 'pending') {
    $where_clauses[] = "pesanan.status = 'pending'";
} elseif ($filter_status === 'selesai') {
    $where_clauses[] = "pesanan.status = 'selesai'";
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$sql = "SELECT 
            COALESCE(pesanan.order_id, CONCAT('ORD-', pesanan.id)) as order_id,
            pesanan.nama_pemesan,
            pesanan.alamat,
            menu.nama as menu_nama,
            pesanan.jumlah,
            menu.harga,
            (menu.harga * pesanan.jumlah) as total,
            pesanan.tanggal_pesan,
            pesanan.status,
            COALESCE(payments.payment_method, 'WhatsApp') as metode_bayar,
            CASE WHEN payments.status = 'confirmed' THEN 'Lunas' ELSE 'Belum Bayar' END as status_bayar
        FROM pesanan
        JOIN menu ON pesanan.menu_id = menu.id
        LEFT JOIN payments ON pesanan.id = payments.pesanan_id
        $where_sql
        ORDER BY pesanan.id DESC";

if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

// Set headers for CSV download
$filename = 'Laporan_Pesanan_' . date('Y-m-d_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Add BOM for Excel UTF-8 compatibility
echo "\xEF\xBB\xBF";

// Open output stream
$output = fopen('php://output', 'w');

// Write header row
fputcsv($output, [
    'Order ID',
    'Nama Pelanggan',
    'Alamat',
    'Menu',
    'Qty',
    'Harga Satuan',
    'Total',
    'Tanggal Pesan',
    'Status Order',
    'Metode Bayar',
    'Status Bayar'
]);

// Write data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['order_id'],
        $row['nama_pemesan'],
        $row['alamat'],
        $row['menu_nama'],
        $row['jumlah'],
        $row['harga'],
        $row['total'],
        date('d/m/Y H:i', strtotime($row['tanggal_pesan'])),
        ucfirst($row['status']),
        $row['metode_bayar'],
        $row['status_bayar']
    ]);
}

fclose($output);
$conn->close();
