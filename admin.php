<?php
// Konfigurasi database
$servername = "localhost"; // Server database
$username = "happyipp_fauzi"; // Username MySQL (default XAMPP)
$password = "Fauzi2801*"; // Password MySQL (default kosong di XAMPP)
$dbname = "happyipp_db_menu"; // Nama database

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
      <script type="module" src="https://cdn.jsdelivr.net/gh/domyid/tracker@main/index.js"></script>
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
            <a href="index.php" class="button">Back to Index</a>
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
                    $menus = array();
                    while ($row = $result->fetch_assoc()) {
                        $menus[] = $row;
                    }
                    // Handle move to top (GET param)
                    if (isset($_GET['move_top'])) {
                        $moveId = intval($_GET['move_top']);
                        foreach ($menus as $i => $menu) {
                            if ($menu['id'] == $moveId) {
                                // Pindahkan ke atas
                                $moveMenu = $menus[$i];
                                unset($menus[$i]);
                                array_unshift($menus, $moveMenu);
                                break;
                            }
                        }
                    }
                    foreach ($menus as $row) {
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
                        echo "<a href='admin.php?move_top=" . $row["id"] . "' style='background:#fbbf24;color:#fff;margin-left:5px;'>Pindah ke Atas</a>";
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
    background: linear-gradient(120deg, #fff0f6, #ffdeeb, #fcc2d7);
    color: #881337;
    min-height: 100vh;
}

header {
    background: #fff0f6;
    color: #9d174d;
    padding: 32px 0 24px 0;
    width: 100%;
    box-shadow: 0 8px 32px rgba(236, 72, 153, 0.08);
    text-align: center;
    border-bottom-left-radius: 32px;
    border-bottom-right-radius: 32px;
}

header h1 {
    font-size: 2.3rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 0;
    background: linear-gradient(90deg, #f472b6, #ec4899);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.container {
    width: 95%;
    max-width: 1200px;
    margin: 32px auto 0 auto;
}

.button {
    padding: 12px 28px;
    border-radius: 12px;
    color: #fff;
    font-weight: 600;
    background: linear-gradient(90deg, #f472b6, #ec4899);
    box-shadow: 0 2px 8px rgba(236, 72, 153, 0.12);
    border: none;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    cursor: pointer;
    margin-bottom: 0;
}
.button:hover {
    background: linear-gradient(90deg, #ec4899, #db2777);
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 6px 18px rgba(236, 72, 153, 0.18);
}

.table-wrapper {
    margin-top: 32px;
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(236, 72, 153, 0.10);
    border-collapse: collapse;
    overflow: hidden;
}

th, td {
    padding: 16px 14px;
    text-align: left;
    color: #881337;
    font-weight: 500;
    border-bottom: 1px solid #fbcfe8;
}

th {
    background: linear-gradient(90deg, #fbcfe8, #f9a8d4);
    color: #9d174d;
    font-size: 1.1rem;
    font-weight: 700;
    border-bottom: 2px solid #fbcfe8;
}

tbody tr {
    transition: box-shadow 0.2s, transform 0.2s;
}
tbody tr:hover {
    background-color: #fff0f6;
    box-shadow: 0 4px 16px rgba(236, 72, 153, 0.10);
    transform: scale(1.01);
}

img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 12px;
    border: 2px solid #fbcfe8;
    box-shadow: 0 2px 8px rgba(236, 72, 153, 0.10);
}

.actions a {
    display: inline-block;
    padding: 8px 16px;
    margin-right: 8px;
    border-radius: 8px;
    color: #fff;
    font-weight: 600;
    font-size: 0.95rem;
    background: linear-gradient(90deg, #f472b6, #ec4899);
    box-shadow: 0 2px 8px rgba(236, 72, 153, 0.10);
    text-decoration: none;
    transition: all 0.2s cubic-bezier(.25,.8,.25,1);
}
.actions a:hover {
    background: linear-gradient(90deg, #ec4899, #db2777);
    transform: translateY(-2px) scale(1.07);
    box-shadow: 0 6px 18px rgba(236, 72, 153, 0.18);
}
.actions a:last-child {
    background: linear-gradient(90deg, #fbbf24, #f59e42);
    color: #fff;
}
.actions a:last-child:hover {
    background: linear-gradient(90deg, #f59e42, #fbbf24);
    color: #fff;
}

footer {
    margin-top: 48px;
    padding: 16px 0;
    width: 100%;
    background: #fff0f6;
    text-align: center;
    color: #9d174d;
    font-size: 1rem;
    border-top-left-radius: 32px;
    border-top-right-radius: 32px;
    box-shadow: 0 -8px 32px rgba(236, 72, 153, 0.08);
}
</style>