<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "db_menu";

// Koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Cek error
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
