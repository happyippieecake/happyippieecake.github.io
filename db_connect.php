<?php
// Konfigurasi database
$servername = "localhost"; // Server database
$username = "root"; // Username MySQL default XAMPP
$password = ""; // Password default XAMPP (kosong)
$dbname = "db_menu"; // Nama database

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
echo "Koneksi berhasil!";
?>
    