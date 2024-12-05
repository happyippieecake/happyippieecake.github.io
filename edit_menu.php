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

    <header class="bg-gray-700 text-white py-4 text-center">
        <h1 class="text-2xl font-semibold">Edit Menu</h1>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <form method="POST" class="bg-white p-6 rounded-lg shadow-lg">
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Name:</label>
                <input type="text" name="name" id="name" value="<?= $row['name'] ?>" class="w-full p-2 border border-gray-300 rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-gray-700">Price:</label>
                <input type="text" name="price" id="price" value="<?= $row['price'] ?>" class="w-full p-2 border border-gray-300 rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description:</label>
                <textarea name="description" id="description" class="w-full p-2 border border-gray-300 rounded-md" required><?= $row['description'] ?></textarea>
            </div>

            <div class="mb-4">
                <label for="category" class="block text-gray-700">Category:</label>
                <input type="text" name="category" id="category" value="<?= $row['category'] ?>" class="w-full p-2 border border-gray-300 rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="image" class="block text-gray-700">Image:</label>
                <input type="text" name="image" id="image" value="<?= $row['image'] ?>" class="w-full p-2 border border-gray-300 rounded-md" required>
            </div>

            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-500">Update Menu</button>
        </form>
    </div>

</body>
</html>

<?php
$conn->close();
?>
