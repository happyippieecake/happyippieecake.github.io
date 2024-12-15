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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background-color:rgb(255, 255, 255); /* Light Pink Background */
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        h1, h2 {
            text-align: center;
            color: #e91e63; /* Pink Color */
        }
        .success {
            text-align: center;
            color: #28a745;
            font-weight: bold;
        }
        .error {
            text-align: center;
            color: #dc3545;
            font-weight: bold;
        }
        form {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f8bbd0; /* Light Pink */
        }
        form input, form textarea, form select {
            width: 98%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            transition: border-color 0.3s;
        }
        form input:focus, form textarea:focus, form select:focus {
            border-color: #e91e63; /* Pink border on focus */
        }
        form button {
            background-color: #e91e63; /* Pink Button */
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        form button:hover {
            background-color: #c2185b; /* Darker Pink on hover */
        }
        .reviews {
            margin-top: 30px;
        }
        .review {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fce4ec; /* Light Pink Background for reviews */
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, background-color 0.3s;
        }
        .review:hover {
            transform: translateY(-5px);
            background-color: #f8bbd0; /* Slightly darker pink on hover */
        }
        .review h3 {
            margin: 0 0 10px 0;
            color: #e91e63; /* Pink Color for Name */
            font-size: 20px;
        }
        .review p {
            margin: 5px 0;
            font-size: 16px;
            line-height: 1.5;
        }
        .review .rating {
            color: #ffc107;
            font-size: 1.3em;
            margin-top: 5px;
        }
        .review small {
            display: block;
            margin-top: 15px;
            font-size: 14px;
            color: #777;
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
        <div class="reviews">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="review">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="rating">Rating: <?php echo str_repeat('<i class="fas fa-star"></i>', $row['rating']) . str_repeat('<i class="far fa-star"></i>', 5 - $row['rating']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($row['review'])); ?></p>
                        <small>Ditulis pada: <?php echo $row['created_at']; ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Belum ada ulasan.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
