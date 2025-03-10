<?php
include 'db.php';
session_start();

// Pastikan user sudah login dan terdapat user_id di session
if (!isset($_SESSION['user_id'])) {
    die("Anda harus login untuk memberikan ulasan.");
}

$user_id = $_SESSION['user_id'];
$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Anonymous';

// Ambil slug dari URL dengan sanitasi
$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';

// Query untuk mengambil data film berdasarkan slug
$stmt = $conn->prepare("SELECT * FROM movies WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();
$stmt->close();

// Periksa apakah film ditemukan
if (!$movie) {
    echo "Movie not found!";
    exit;
}

$movie_id = $movie['id']; // Ambil ID film

// Proses penambahan rating dan ulasan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'], $_POST['review'])) {
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);

    // Validasi rating
    if ($rating < 1 || $rating > 5) {
        $error_message = "Rating harus antara 1 hingga 5.";
    } elseif (empty($review)) {
        $error_message = "Ulasan tidak boleh kosong.";
    } else {
        // Simpan ke database dengan prepared statement, sertakan user_id
        $stmt = $conn->prepare("INSERT INTO ratings (movies_id, rating, review, username, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iissi", $movie_id, $rating, $review, $user_name, $user_id);
        $stmt->execute();
        $stmt->close();

        // Redirect ke halaman yang sama dengan fragmen untuk modal sukses
        header("Location: " . $_SERVER['REQUEST_URI'] . "#successModal");
        exit;
    }
}

// Ambil rata-rata rating dan jumlah ulasan
$stmt = $conn->prepare("SELECT AVG(rating) AS average_rating, COUNT(rating) AS rating_count FROM ratings WHERE movies_id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$rating_result = $stmt->get_result();
$rating_data = $rating_result->fetch_assoc();
$stmt->close();

// Ambil ulasan dari database
$stmt = $conn->prepare("SELECT id, rating, review, username FROM ratings WHERE movies_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$reviews_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $movie['title']; ?></title>
    <link rel="icon" href="logo.jpg" type="image/x-icon">
    <!-- Sertakan Font Awesome -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #1c1c1c;
            color: #ffffff;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        a {
            text-decoration: none;
            color: rgb(255, 0, 0);
        }
        a:hover {
            color: rgb(182, 182, 182);
        }
        /* Navbar */
        .navbar {
            background-color: #333;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar h1 {
            font-size: 2.5em;
            margin: 0;
            color: rgb(255, 0, 0);
        }
        /* Back Button */
        .back-button {
            display: inline-block;
            margin: 20px;
            padding: 10px 20px;
            background-color: rgb(255, 0, 0);
            color: white;
            border-radius: 5px;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: rgb(176, 0, 0);
        }
        /* Movie Detail Container */
        .movie-detail-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .movie-title {
            font-size: 2.5em;
            margin: 20px 0;
            color: rgb(255, 0, 0);
        }
        .movie-description {
            font-size: 1.2em;
            margin: 20px 0;
            color: #cccccc;
        }
        .movie-poster {
            width: 100%;
            max-width: 800px;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        /* Rating Section */
        .rating-section {
            margin-top: 30px;
            background-color: #2c2c2c;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .rating-section h2 {
            font-size: 2em;
            margin-bottom: 15px;
            color: rgb(255, 0, 0);
        }
        .rating-section form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .rating-section textarea {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #333;
            color: #ffffff;
            resize: vertical;
            min-height: 100px;
        }
        .rating-section button {
            padding: 10px 20px;
            background-color: rgb(255, 0, 0);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }
        .rating-section button:hover {
            background-color: rgb(176, 0, 0);
        }
        /* Star Rating Styles */
        .star-rating {
            display: inline-block;
            font-size: 24px;
            color: #ccc;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .star-rating .fa-star {
            margin-right: 5px;
            transition: color 0.2s;
        }
        .star-rating .fa-star.selected {
            color: #ffcc00;
        }
        /* Reviews Section */
        .reviews {
            margin-top: 30px;
        }
        .reviews h2 {
            font-size: 2em;
            margin-bottom: 15px;
            color: rgb(255, 0, 0);
        }
        .review-item {
            background-color: #2c2c2c;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .review-item p {
            margin: 5px 0;
        }
        .review-rating {
            font-weight: bold;
            color: rgb(175, 0, 0);
        }
        /* Styling untuk tombol hapus komentar */
        .delete-button {
            background-color: transparent;
            border: none;
            color: #ff4d4d;
            font-size: 0.9em;
            cursor: pointer;
            padding: 5px 10px;
            transition: color 0.3s ease;
        }
        .delete-button:hover {
            color: #ff0000;
        }
        /* Video Container */
        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 */
            height: 0;
            overflow: hidden;
            max-width: 1600px;
            margin: 0 auto;
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        /* Modal Styles (Pure CSS menggunakan :target) */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            display: none;
            z-index: 1000;
        }
        .modal:target {
            display: block;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 400px;
            margin: 15% auto;
            text-align: center;
            position: relative;
        }
        .close-button {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 24px;
            text-decoration: none;
            color: #333;
        }
        .success-message {
            color: rgb(255, 0, 0);
            font-weight: bold;
            margin: 20px 0;
        }
        .fa-trash{
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-button">Kembali ke Beranda</a>
    <div class="movie-detail-container">
        <div class="video-container">
            <iframe src="<?php echo $movie['url_video']; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;" allowfullscreen></iframe>
        </div>
        <h1 class="movie-title"><?php echo $movie['title']; ?></h1>
        <p class="movie-description"><?php echo $movie['description']; ?></p>
        <p class="movie-description">Author: <?php echo $movie['author']; ?></p>

        <!-- Rating Section -->
        <div class="rating-section">
            <h2>Rating</h2>
            <p>Rata-rata rating: <?php echo number_format($rating_data['average_rating'], 2); ?> (<?php echo $rating_data['rating_count']; ?> ulasan)</p>
            <form method="POST">
                <label for="rating">Beri Rating:</label>
                <!-- Star rating menggunakan ikon Font Awesome -->
                <div class="star-rating">
                    <!-- Input tersembunyi untuk menyimpan nilai rating -->
                    <input type="hidden" name="rating" id="rating" value="0">
                    <i class="fa fa-star" data-value="1"></i>
                    <i class="fa fa-star" data-value="2"></i>
                    <i class="fa fa-star" data-value="3"></i>
                    <i class="fa fa-star" data-value="4"></i>
                    <i class="fa fa-star" data-value="5"></i>
                </div>
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
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <p class="review-rating">
                            <?php 
                                $rating = intval($review['rating']);
                                // Tampilkan bintang penuh
                                for ($i = 1; $i <= $rating; $i++) {
                                    echo '<i class="fa fa-star" style="color: #ffcc00;"></i>';
                                }
                                // Tampilkan bintang kosong jika rating kurang dari 5
                                for ($i = $rating + 1; $i <= 5; $i++) {
                                    echo '<i class="fa fa-star-o" style="color: #ffcc00;"></i>';
                                }
                            ?>
                        </p>
                        <!-- Hanya tampilkan tombol hapus jika komentar ini dibuat oleh user yang sedang login -->
                        <?php if ($review['username'] === $user_name): ?>
                        <form method="POST" action="delete_review.php" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');" style="margin: 0;">
                            <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                            <button type="submit" class="delete-button"><i class="fa-solid fa-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </div>
                    <p><strong><?php echo htmlspecialchars($review['username']); ?>:</strong></p>
                    <p><?php echo htmlspecialchars($review['review']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Modal untuk notifikasi sukses (akan tampil jika URL mengandung #successModal) -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <a href="#" class="close-button">&times;</a>
            <p class="success-message">Rating dan ulasan berhasil ditambahkan.</p>
        </div>
    </div>

    <!-- JavaScript untuk interaksi star rating -->
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            const stars = document.querySelectorAll('.star-rating .fa-star');
            const ratingInput = document.getElementById('rating');

            function updateStars(rating) {
                stars.forEach(star => {
                    if (star.getAttribute('data-value') <= rating) {
                        star.classList.add('selected');
                    } else {
                        star.classList.remove('selected');
                    }
                });
            }

            stars.forEach(star => {
                // Ketika bintang diklik, simpan nilai rating dan perbarui tampilan
                star.addEventListener('click', function(){
                    const rating = this.getAttribute('data-value');
                    ratingInput.value = rating;
                    updateStars(rating);
                });

                // Saat hover, tampilkan preview rating
                star.addEventListener('mouseover', function(){
                    const rating = this.getAttribute('data-value');
                    updateStars(rating);
                });

                // Saat mouse keluar, kembalikan tampilan ke rating yang tersimpan
                star.addEventListener('mouseout', function(){
                    updateStars(ratingInput.value);
                });
            });
        });
    </script>
</body>
</html>
