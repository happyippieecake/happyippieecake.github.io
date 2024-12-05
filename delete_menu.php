<?php
// Konfigurasi database
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "db_menu";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID menu dari URL
$id = $_GET['id'];

// Query untuk menghapus menu
$delete_sql = "DELETE FROM menu WHERE id=$id";
if ($conn->query($delete_sql) === TRUE) {
    echo "Menu berhasil dihapus!";
    header("Location: admin.php"); // Kembali ke halaman admin setelah menghapus menu
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
