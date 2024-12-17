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
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Header -->
    <header>
        <h1>Admin - Manage Menu</h1>
    </header>

    <!-- Back and Add Menu Links -->
    <div class="container">
        <div style="display: flex; justify-content: space-between;">
            <a href="index.html" class="button">Back to Index</a>
            <a href="tambah_menu.php" class="button">Add New Menu</a>
        </div>
    </div>

    <!-- Menu Table -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Actions</th>
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
                        echo "<td class='actions'>";
                        echo "<a href='edit_menu.php?id=" . $row["id"] . "'>Edit</a>";
                        echo "<a href='delete_menu.php?id=" . $row["id"] . "'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No menu items available.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2024 Admin Menu Management</p>
    </footer>

</body>
</html>

<?php
$conn->close();
?>


<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

body {
    margin: 0;
    padding: 0;
    font-family: 'Roboto', sans-serif;
    background: linear-gradient(120deg, #1c1b29, #3a3a59);
    color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    flex-direction: column;
}

header {
    background: #2e2e3b;
    color: #ff8c00;
    padding: 20px;
    width: 100%;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
    text-align: center;
}

header h1 {
    font-size: 2rem;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
}

a {
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin-top: 20px;
}

.button {
    padding: 10px 20px;
    border-radius: 8px;
    color: #ffffff;
    font-weight: 600;
    background: linear-gradient(90deg, #ff8c00, #e52e71);
    transition: all 0.3s ease-in-out;
}

.button:hover {
    background: linear-gradient(90deg, #e52e71, #ff8c00);
    transform: scale(1.05);
}

.table-wrapper {
    margin-top: 30px;
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    background: #1c1b29;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    text-align: left;
    color: #cccccc;
    font-weight: 500;
}

th {
    background: linear-gradient(90deg, #3a3a59, #2e2e3b);
}

tbody tr:hover {
    background-color: #2e2e3b;
    transform: scale(1.02);
    transition: all 0.2s ease-in-out;
}

button {
    padding: 10px 20px;
    border-radius: 8px;
    background: linear-gradient(90deg, #ff8c00, #e52e71);
    color: #ffffff;
    font-weight: bold;
    cursor: pointer;
    border: none;
    transition: all 0.3s ease;
}

button:hover {
    background: linear-gradient(90deg, #e52e71, #ff8c00);
    transform: scale(1.05);
}

button:active {
    transform: translateY(1px);
    box-shadow: 0 4px 10px rgba(229, 46, 113, 0.2);
}

img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.actions a {
    padding: 6px 12px;
    margin-right: 10px;
    border-radius: 6px;
    color: white;
    font-weight: bold;
    transition: all 0.2s ease;
}

.actions a:hover {
    transform: scale(1.05);
}

.actions a:nth-child(1) {
    background-color: #ff8c00;
}

.actions a:nth-child(2) {
    background-color: #e52e71;
}

.actions a:nth-child(1):hover {
    background-color: #e52e71;
}

.actions a:nth-child(2):hover {
    background-color: #ff8c00;
}

footer {
    margin-top: 50px;
    padding: 10px;
    width: 100%;
    background: #2e2e3b;
    text-align: center;
    color: #ff8c00;
}

</style>