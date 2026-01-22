<?php
// Load environment variables from .env file
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad(); // Use safeLoad to not throw exception if .env doesn't exist

// Database configuration from environment variables
$servername = $_ENV['DB_HOST'] ?? 'localhost';
$username   = $_ENV['DB_USERNAME'] ?? 'root';
$password   = $_ENV['DB_PASSWORD'] ?? '';
$database   = $_ENV['DB_NAME'] ?? 'happyippiecake';

// Koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Cek error
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}