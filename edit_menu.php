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

// Query untuk mengambil data menu berdasarkan ID
$sql = "SELECT * FROM menu WHERE id = $id";
$result = $conn->query($sql);

// Jika data ditemukan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Menu tidak ditemukan!";
    exit;
}

// Periksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $image = $_POST['image'];

    // Query untuk mengupdate data menu
    $update_sql = "UPDATE menu SET name='$name', price='$price', description='$description', category='$category', image='$image' WHERE id=$id";
    
    if ($conn->query($update_sql) === TRUE) {
        echo "Menu berhasil diupdate!";
        header("Location: admin.php"); // Arahkan kembali ke halaman admin setelah update
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu</title>
</head>
<body>
    <h1>Edit Menu</h1>
    <form method="POST">
        <label for="name">Nama:</label>
        <input type="text" name="name" value="<?= $row['name'] ?>" required><br><br>
        
        <label for="price">Harga:</label>
        <input type="text" name="price" value="<?= $row['price'] ?>" required><br><br>
        
        <label for="description">Deskripsi:</label>
        <textarea name="description" required><?= $row['description'] ?></textarea><br><br>
        
        <label for="category">Kategori:</label>
        <input type="text" name="category" value="<?= $row['category'] ?>" required><br><br>
        
        <label for="image">Gambar:</label>
        <input type="text" name="image" value="<?= $row['image'] ?>" required><br><br>
        
        <button type="submit">Update Menu</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
