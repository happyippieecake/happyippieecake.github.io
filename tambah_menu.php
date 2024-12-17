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

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-500 via-pink-500 to-red-500">
    <form method="POST" enctype="multipart/form-data" 
          class="bg-white p-10 rounded-3xl shadow-2xl max-w-lg w-full transform transition-all duration-500 hover:scale-105 hover:shadow-3xl animate-pop-in">
        <h2 class="text-3xl font-extrabold text-center text-gray-800 mb-8">✨ Tambah Menu Baru ✨</h2>

        <div class="mb-6">
            <label for="name" class="block text-gray-700 font-bold mb-2">Nama Menu:</label>
            <input type="text" name="name" id="name" 
                   class="w-full p-4 border border-gray-300 rounded-xl text-gray-700 focus:ring-4 focus:ring-pink-400 focus:outline-none shadow-sm" 
                   placeholder="Masukkan nama menu" required>
        </div>

        <div class="mb-6">
            <label for="price" class="block text-gray-700 font-bold mb-2">Harga:</label>
            <input type="text" name="price" id="price" 
                   class="w-full p-4 border border-gray-300 rounded-xl text-gray-700 focus:ring-4 focus:ring-pink-400 focus:outline-none shadow-sm" 
                   placeholder="Masukkan harga menu" required>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-gray-700 font-bold mb-2">Deskripsi:</label>
            <textarea name="description" id="description" 
                      class="w-full p-4 border border-gray-300 rounded-xl text-gray-700 focus:ring-4 focus:ring-pink-400 focus:outline-none shadow-sm" 
                      placeholder="Deskripsi menu" required></textarea>
        </div>

        <div class="mb-6">
            <label for="category" class="block text-gray-700 font-bold mb-2">Kategori:</label>
            <input type="text" name="category" id="category" 
                   class="w-full p-4 border border-gray-300 rounded-xl text-gray-700 focus:ring-4 focus:ring-pink-400 focus:outline-none shadow-sm" 
                   placeholder="Masukkan kategori menu" required>
        </div>

        <div class="mb-8">
            <label for="image" class="block text-gray-700 font-bold mb-2">Upload Gambar:</label>
            <input type="file" name="image" id="image" 
                   class="w-full p-4 border border-gray-300 rounded-xl text-gray-700 focus:ring-4 focus:ring-pink-400 focus:outline-none shadow-sm" required>
        </div>

        <button type="submit" 
                class="w-full py-4 bg-gradient-to-r from-pink-500 to-red-500 text-white font-bold rounded-xl shadow-lg hover:opacity-90 transition-all duration-300 transform hover:scale-105">
            Tambah Menu
        </button>
    </form>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

body {
    margin: 0;
    padding: 0;
    font-family: 'Roboto', sans-serif;
    background: linear-gradient(120deg, #1c1b29, #3a3a59);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    color: #ffffff;
}

form {
    background: linear-gradient(145deg, #2a2a3d, #1e1e2d);
    border-radius: z0px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4), 0 4px 10px rgba(255, 255, 255, 0.1);
    padding: 2.5rem;
    max-width: 450px;
    width: 100%;
    animation: fade-in 0.7s ease-in-out;
    position: relative;
}

form::before {
    content: "";
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    background: linear-gradient(145deg, #ff8c00, #e52e71);
    z-index: -1;
    filter: blur(20px);
    border-radius: 25px;
}

h2 {
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: #ff8c00;
    position: relative;
}

h2::after {
    content: '';
    display: block;
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #e52e71, #ff8c00);
    margin: 0.5rem auto 0;
    border-radius: 10px;
}



label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #cccccc;
}

input[type="text"], 
input[type="file"], 
textarea {
    width: 100%;
    padding: 0.8rem 1rem;
    border: none;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    background: #1c1b29;
    color: #ffffff;
    font-size: 1rem;
    box-shadow: inset 0 4px 6px rgba(0, 0, 0, 0.3), inset 0 -2px 4px rgba(255, 255, 255, 0.1);
    transition: transform 0.2s ease, box-shadow 0.3s ease;
}

input:focus, 
textarea:focus {
    outline: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 140, 0, 0.5), inset 0 2px 6px rgba(255, 255, 255, 0.2);
}

textarea {
    min-height: 100px;
    resize: none;
}

button {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: 10px;
    background: linear-gradient(90deg, #ff8c00, #e52e71);
    color: #ffffff;
    font-weight: 700;
    font-size: 1.1rem;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(229, 46, 113, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.1);
}

button:hover {
    background: linear-gradient(90deg, #e52e71, #ff8c00);
    box-shadow: 0 8px 20px rgba(229, 46, 113, 0.5);
    transform: translateY(-3px);
}

button:active {
    transform: translateY(1px);
    box-shadow: 0 4px 10px rgba(229, 46, 113, 0.2);
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

</style>