<?php
// Load environment variables from .env file
<<<<<<< HEAD
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        // Parse key=value
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Database configuration from environment variables
$servername = getenv('DB_HOST') ?: 'localhost';
$username   = getenv('DB_USERNAME') ?: 'root';
$password   = getenv('DB_PASSWORD') ?: '';
$database   = getenv('DB_NAME') ?: 'happyippiecake';
=======
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad(); // Use safeLoad to not throw exception if .env doesn't exist

// Database configuration from environment variables
$servername = $_ENV['DB_HOST'] ?? 'localhost';
$username   = $_ENV['DB_USERNAME'] ?? 'root';
$password   = $_ENV['DB_PASSWORD'] ?? '';
$database   = $_ENV['DB_DATABASE'] ?? 'happyippiecake';
>>>>>>> 50a0037281ce780f4a6865c822869ac193020056

// Koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Cek error
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}