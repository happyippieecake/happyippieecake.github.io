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

// Query untuk mengambil semua data menu
$sql = "SELECT * FROM menu";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <!-- Header -->
    <header class="bg-gray-700 text-white py-4 text-center">
        <h1 class="text-2xl font-semibold">Admin - Manage Menu</h1>
    </header>

    <!-- Back and Add Menu Links -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
            <a href="index.html" class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-500">Back to Index</a>
            <a href="tambah_menu.php" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-500">Add New Menu</a>
        </div>
    </div>

    <!-- Menu Table -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <table class="min-w-full bg-white shadow-lg rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-200 text-gray-800">
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Price</th>
                    <th class="px-4 py-2 text-left">Description</th>
                    <th class="px-4 py-2 text-left">Category</th>
                    <th class="px-4 py-2 text-left">Image</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='hover:bg-gray-50'>";
                        echo "<td class='px-4 py-2'>" . $row["id"] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row["name"] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row["price"] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row["description"] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row["category"] . "</td>";
                        echo "<td class='px-4 py-2'><img src='images/" . $row["image"] . "' alt='" . $row["name"] . "' class='w-16 h-16 object-cover rounded-md'></td>";
                        echo "<td class='px-4 py-2 text-center'>";
                        echo "<a href='edit_menu.php?id=" . $row["id"] . "' class='bg-yellow-500 text-white py-1 px-3 rounded-md hover:bg-yellow-400'>Edit</a>";
                        echo "<a href='delete_menu.php?id=" . $row["id"] . "' class='bg-red-600 text-white py-1 px-3 rounded-md hover:bg-red-500 ml-2'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='px-4 py-2 text-center italic text-gray-600'>No menu items available.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
$conn->close();
?>
