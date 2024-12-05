<?php
// Konfigurasi database
$servername = "127.0.0.1";
$username = "root";  // Default username untuk MySQL di XAMPP
$password = "";      // Default password MySQL (kosong)
$dbname = "db_menu";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mendapatkan data menu
$sql = "SELECT * FROM menu";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Menu</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            padding: 20px;
            background-color: #6c757d;
            color: white;
            margin: 0;
        }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #f8f9fa;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        img {
            width: 50px;
            height: auto;
            border-radius: 5px;
        }
        .action-buttons button {
            padding: 6px 12px;
            margin: 3px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .action-buttons button:hover {
            background-color: #0056b3;
        }
        .action-buttons button:active {
            background-color: #003366;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #6c757d;
        }
        @media screen and (max-width: 768px) {
            table {
                width: 100%;
                font-size: 12px;
            }
            h1 {
                font-size: 18px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <h1>Halaman Admin - Manage Menu</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Gambar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["price"] . "</td>";
                    echo "<td>" . $row["description"] . "</td>";
                    echo "<td>" . $row["category"] . "</td>";
                    echo "<td><img src='images/" . $row["image"] . "' alt='" . $row["name"] . "'></td>";
                    echo "<td class='action-buttons'>";
                    echo "<button onclick='editMenu(" . $row["id"] . ")'>Edit</button>";
                    echo "<button onclick='deleteMenu(" . $row["id"] . ")'>Hapus</button>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='no-data'>Tidak ada data menu.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        function editMenu(id) {
            window.location.href = "edit_menu.php?id=" + id;
        }

        function deleteMenu(id) {
            if (confirm("Apakah Anda yakin ingin menghapus menu ini?")) {
                window.location.href = "delete_menu.php?id=" + id;
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
