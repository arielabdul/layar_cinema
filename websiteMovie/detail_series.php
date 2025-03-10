<?php
include 'db.php';

// Start session to capture the logged-in user's name
session_start();

// Assuming user data (e.g., username) is stored in session after login
$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Anonymous';

// Ambil slug dari URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// Query untuk mengambil data film berdasarkan slug
$query = "SELECT * FROM series WHERE slug = '$slug'";
$result = mysqli_query($conn, $query);

// Periksa apakah data ditemukan
if (mysqli_num_rows($result) > 0) {
    $movie = mysqli_fetch_assoc($result);
} else {
    echo "Movie not found!";
    exit;
}

// Tambahkan rating dan ulasan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'], $_POST['review'])) {
    $rating = intval($_POST['rating']);
    $review = mysqli_real_escape_string($conn, $_POST['review']);

    if ($rating >= 1 && $rating <= 5) {
        // Insert the rating, review, and username into the database
        $stmt = $conn->prepare("INSERT INTO ratings (series_id, rating, review, username) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $slug, $rating, $review, $user_name);
        $stmt->execute();
        $stmt->close();
        echo "Rating dan ulasan berhasil ditambahkan.";
    } else {
        echo "Rating harus antara 1 hingga 5.";
    }
}

// Ambil rata-rata rating
$rating_query = "SELECT AVG(rating) AS average_rating, COUNT(rating) AS rating_count FROM ratings WHERE series_id = '$slug'";
$rating_result = mysqli_query($conn, $rating_query);
$rating_data = mysqli_fetch_assoc($rating_result);

// Ambil semua ulasan termasuk username
$reviews_query = "SELECT rating, review, username FROM ratings WHERE series_id = '$slug' ORDER BY id DESC";
$reviews_result = mysqli_query($conn, $reviews_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $movie['title']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1c1c1c;
            color: white;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #333;
            padding: 15px;
            text-align: center;
        }

        .navbar h1 {
            font-size: 2em;
            margin: 0;
        }

        .movie-detail-container {
            padding: 20px;
            text-align: center;
        }

        .movie-poster {
            width: 100%;
            max-width: 800px;
            border-radius: 10px;
            cursor: pointer;
            margin: 20px 0;
        }

        .movie-title {
            font-size: 2.5em;
            margin: 10px 0;
        }

        .movie-description {
            font-size: 1.2em;
            max-width: 800px;
            margin: 20px auto;
            line-height: 1.5;
        }

        .back-button {
            display: inline-block;
            margin-top: 30px;
            padding: 15px 30px;
            background-color: #4dbf00;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
        }

        .back-button:hover {
            background-color: #3a9e00;
        }

        .rating-section {
            margin-top: 30px;
        }

        .rating-section form {
            margin-top: 15px;
        }

        .rating-section select, .rating-section textarea {
            padding: 10px;
            font-size: 1em;
            width: 100%;
            max-width: 500px;
            margin-bottom: 10px;
        }

        .rating-section button {
            padding: 10px 20px;
            background-color: #4dbf00;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .rating-section button:hover {
            background-color: #3a9e00;
        }

        .reviews {
            margin-top: 30px;
            text-align: left;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .review-item {
            background-color: #2c2c2c;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .review-item p {
            margin: 5px 0;
        }

        .review-rating {
            font-weight: bold;
            color: #4dbf00;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Trailer Cinema</h1>
    </div>
    <div class="movie-detail-container">
        <h1 class="movie-title"><?php echo $movie['title']; ?></h1>
        <p class="movie-description"><?php echo $movie['description']; ?></p>
        <p class="movie-description">Author: <?php echo $movie['author']; ?></p>

        <!-- Rating Section -->
        <div class="rating-section">
            <h2>Rating</h2>
            <p>Rata-rata rating: <?php echo number_format($rating_data['average_rating'], 2); ?> (<?php echo $rating_data['rating_count']; ?> ulasan)</p>
            <form method="POST">
                <label for="rating">Beri Rating:</label>
                <select name="rating" id="rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                <label for="review">Ulasan:</label>
                <textarea name="review" id="review" rows="4" placeholder="Tulis ulasan Anda..."></textarea>
                <button type="submit">Kirim</button>
            </form>
        </div>

        <!-- Reviews Section -->
        <div class="reviews">
            <h2>Ulasan</h2>
            <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                <div class="review-item">
                    <p class="review-rating">Rating: <?php echo $review['rating']; ?>/5</p>
                    <p><strong><?php echo htmlspecialchars($review['username']); ?>:</strong></p>
                    <p><?php echo htmlspecialchars($review['review']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>

        <a href="index.php" class="back-button">Back to Home</a> 
    </div>

    <script>
        function openModal() {
            document.getElementById("myModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }
    </script>
</body>
</html>
