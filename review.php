<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "customer_reviews");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses form jika ada data yang dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $review = $conn->real_escape_string($_POST['review']);
    $rating = (int)$_POST['rating'];

    $sql = "INSERT INTO reviews (name, review, rating) VALUES ('$name', '$review', $rating)";
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>Ulasan berhasil ditambahkan!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
}

// Ambil semua ulasan
$result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #444;
        }
        form {
            margin-bottom: 30px;
        }
        form input, form textarea, form select {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #218838;
        }
        .review {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .review:last-child {
            border-bottom: none;
        }
        .review h3 {
            margin: 0;
            color: #007bff;
        }
        .review p {
            margin: 5px 0;
        }
        .review .rating {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Customer Reviews</h1>

        <form method="POST">
            <input type="text" name="name" placeholder="Nama Anda" required>
            <textarea name="review" placeholder="Tulis ulasan Anda" rows="5" required></textarea>
            <select name="rating" required>
                <option value="">-- Pilih Rating --</option>
                <option value="1">1 - Sangat Buruk</option>
                <option value="2">2 - Buruk</option>
                <option value="3">3 - Cukup</option>
                <option value="4">4 - Baik</option>
                <option value="5">5 - Sangat Baik</option>
            </select>
            <button type="submit">Kirim Ulasan</button>
        </form>

        <h2>Ulasan Pelanggan</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="review">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p class="rating">Rating: <?php echo str_repeat('★', $row['rating']) . str_repeat('☆', 5 - $row['rating']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($row['review'])); ?></p>
                    <small>Ditulis pada: <?php echo $row['created_at']; ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Belum ada ulasan.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>
