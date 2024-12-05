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

// Proses form jika telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $image = $_POST['image'];

    // Query untuk menambahkan menu baru ke database
    $sql = "INSERT INTO menu (name, price, description, category, image)
            VALUES ('$name', '$price', '$description', '$category', '$image')";

    if ($conn->query($sql) === TRUE) {
        echo "Menu baru berhasil ditambahkan!";
        header("Location: admin.php"); // Arahkan kembali ke halaman admin setelah sukses
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <!-- Header -->
    <header class="bg-gray-700 text-white py-4 text-center">
        <h1 class="text-2xl font-semibold">Add New Menu</h1>
    </header>

    <!-- Form to Add New Menu -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <form method="POST" class="bg-white p-6 rounded-lg shadow-lg">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-semibold">Name:</label>
                <input type="text" name="name" id="name" class="w-full p-2 border border-gray-300 rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-gray-700 font-semibold">Price:</label>
                <input type="text" name="price" id="price" class="w-full p-2 border border-gray-300 rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-semibold">Description:</label>
                <textarea name="description" id="description" class="w-full p-2 border border-gray-300 rounded-md" required></textarea>
            </div>

            <div class="mb-4">
                <label for="category" class="block text-gray-700 font-semibold">Category:</label>
                <input type="text" name="category" id="category" class="w-full p-2 border border-gray-300 rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="image" class="block text-gray-700 font-semibold">Image URL:</label>
                <input type="text" name="image" id="image" class="w-full p-2 border border-gray-300 rounded-md" required>
            </div>

            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-500 w-full">Add Menu</button>
        </form>
    </div>

</body>
</html>
