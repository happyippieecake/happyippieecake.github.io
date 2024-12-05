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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <!-- Container -->
    <div class="flex justify-center items-center min-h-screen">
        <div class="w-full max-w-lg bg-white shadow-lg rounded-lg p-8">
            <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Edit Menu</h1>
            <form method="POST" class="space-y-4">
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-600">Nama:</label>
                    <input type="text" name="name" value="<?= $row['name'] ?>" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Price Field -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-600">Harga:</label>
                    <input type="text" name="price" value="<?= $row['price'] ?>" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Description Field -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-600">Deskripsi:</label>
                    <textarea name="description" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= $row['description'] ?></textarea>
                </div>

                <!-- Category Field -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-600">Kategori:</label>
                    <input type="text" name="category" value="<?= $row['category'] ?>" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Image Field -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-600">Gambar (URL):</label>
                    <input type="text" name="image" value="<?= $row['image'] ?>" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Update Menu
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>
