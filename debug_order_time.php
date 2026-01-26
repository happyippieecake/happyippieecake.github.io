<?php
require_once 'db_connect.php';

$result = $conn->query("SELECT id, tanggal_pesan FROM pesanan ORDER BY id DESC LIMIT 1");
if ($row = $result->fetch_assoc()) {
    echo "Raw tanggal_pesan: " . $row['tanggal_pesan'] . "\n";
    echo "Dumped: ";
    var_dump($row['tanggal_pesan']);
} else {
    echo "No orders found.";
}

$columns = $conn->query("SHOW COLUMNS FROM pesanan LIKE 'tanggal_pesan'");
$col = $columns->fetch_assoc();
echo "Column Type: " . $col['Type'] . "\n";
?>
