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

// Query untuk menghapus data menu berdasarkan ID
$delete_sql = "DELETE FROM menu WHERE id = $id";

if ($conn->query($delete_sql) === TRUE) {
    echo "Menu berhasil dihapus!";
    header("Location: admin.php"); // Arahkan kembali ke halaman admin setelah menghapus
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
