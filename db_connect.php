<?php
// Konfigurasi database
$servername = "127.0.0.1"; // Server database
$username = "root"; // Username MySQL (default XAMPP)
$password = ""; // Password MySQL (default kosong di XAMPP)
$dbname = "db_menu"; // Nama database

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
echo "Koneksi berhasil!";
?>