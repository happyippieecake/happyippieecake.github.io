<?php
// Koneksi ke database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=db_menu', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Koneksi gagal: ' . $e->getMessage());
}

// Query untuk mengambil data menu
$sql = "SELECT * FROM menu_items";
$stmt = $pdo->query($sql);
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <style>
        /* Styling card */
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 20px;
        }
        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .card h3 {
            margin: 15px 0 5px;
            font-size: 1.2rem;
        }
        .card p {
            margin: 5px 15px 10px;
            color: #555;
        }
        .card .price {
            color: #e74c3c;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .card a {
            display: block;
            margin: 15px;
            padding: 10px 15px;
            background-color: #e74c3c;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .card a:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<div class="card-container">
    <?php foreach ($menus as $menu): ?>
        <div class="card">
            <img src="<?php echo htmlspecialchars($menu['image_url']); ?>" alt="<?php echo htmlspecialchars($menu['name']); ?>">
            <h3><?php echo htmlspecialchars($menu['name']); ?></h3>
            <p><?php echo htmlspecialchars($menu['description']); ?></p>
            <p class="price">Rp. <?php echo number_format($menu['price'], 0, ',', '.'); ?></p>
            <a href="pesan.php?id=<?php echo $menu['id']; ?>">Pesan Sekarang</a>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
