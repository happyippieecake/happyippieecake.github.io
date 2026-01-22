<?php
// Load environment variables from .env file
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

// Koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Cek error
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}