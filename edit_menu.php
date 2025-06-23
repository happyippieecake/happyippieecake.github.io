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
      <script type="module" src="https://cdn.jsdelivr.net/gh/domyid/tracker@main/index.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
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

    .container {
        padding: 20px;
        width: 100%;
        max-width: 450px; /* Menyesuaikan lebar form */
    }

    form {
        background: linear-gradient(145deg, #2a2a3d, #1e1e2d);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4), 0 4px 10px rgba(255, 255, 255, 0.1);
        padding: 1.5rem;
        margin: 0 auto;
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

    h1 {
        text-align: center;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #ff8c00;
        position: relative;
    }

    h1::after {
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
        padding: 0.75rem;
        border: none;
        border-radius: 10px;
        margin-bottom: 1rem;
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
</head>
<body>
    <div class="container">
        <h1>Edit Menu</h1>
        <form method="POST" enctype="multipart/form-data">
            <!-- Name Field -->
            <div class="mb-4">
                <label for="name">Nama:</label>
                <input type="text" name="name" value="<?= $row['name'] ?>" required>
            </div>

            <!-- Price Field -->
            <div class="mb-4">
                <label for="price">Harga:</label>
                <input type="text" name="price" value="<?= $row['price'] ?>" required>
            </div>

            <!-- Description Field -->
            <div class="mb-4">
                <label for="description">Deskripsi:</label>
                <textarea name="description" required><?= $row['description'] ?></textarea>
            </div>

            <!-- Category Field -->
            <div class="mb-4">
                <label for="category">Kategori:</label>
                <input type="text" name="category" value="<?= $row['category'] ?>" required>
            </div>

            <!-- Image Field -->
            <div class="mb-4">
                <label for="image">Upload Image:</label>
                <input type="file" name="image" id="image" required>
            </div>

            <!-- Submit Button -->
            <button type="submit">Update Menu</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
