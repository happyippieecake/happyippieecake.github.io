<?php
require_once 'db_connect.php';

// Change tanggal_pesan to DATETIME
$sql = "ALTER TABLE pesanan MODIFY COLUMN tanggal_pesan DATETIME NOT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Column tanggal_pesan updated to DATETIME successfully.\n";
} else {
    echo "Error updating column: " . $conn->error . "\n";
}

// Optional: specific fix for recent orders to set them to NOW() if they are 00:00:00 
// This is risky if we overwrite actual past dates, so I'll skip data modification 
// and only fix structure. Users just have to accept old data is 00:00.
?>
