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

    // Proses upload gambar
    $targetDir = "gambar/"; // Folder tujuan
    $targetFile = $targetDir . basename($_FILES["image"]["name"]); // Nama file lengkap
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)); // Ekstensi file

    // Periksa apakah file benar-benar gambar
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File bukan gambar.";
        $uploadOk = 0;
    }

    // Periksa ukuran file (maks 2MB)
    if ($_FILES["image"]["size"] > 2000000) {
        echo "Ukuran file terlalu besar.";
        $uploadOk = 0;
    }

    // Batasi tipe file yang diizinkan
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
        $uploadOk = 0;
    }

    // Jika tidak ada masalah, pindahkan file ke folder tujuan
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Simpan nama file gambar ke database
            $image = basename($_FILES["image"]["name"]);

            // Query untuk menambahkan menu baru ke database
            $sql = "INSERT INTO menu (name, price, description, category, image)
                    VALUES ('$name', '$price', '$description', '$category', '$image')";

            if ($conn->query($sql) === TRUE) {
                echo "Menu baru berhasil ditambahkan!";
                header("Location: admin.php"); // Arahkan kembali ke halaman admin setelah sukses
            } else {
                echo "Error: " . $conn->error;
            }
        } else {
            echo "Ada masalah saat mengupload gambar.";
        }
    }
}

$conn->close();
?>

<form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-lg">
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
        <label for="image" class="block text-gray-700 font-semibold">Upload Image:</label>
        <input type="file" name="image" id="image" class="w-full p-2 border border-gray-300 rounded-md" required>
    </div>

    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-500 w-full">Add Menu</button>
</form>
