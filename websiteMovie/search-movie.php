<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: user-login.php");
    exit();
}

$search = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    // Query hanya untuk film dengan kategori 'movies'
    $query = "SELECT * FROM movies WHERE kategori = 'movies' AND title LIKE '%$search%'";
} else {
    $query = "SELECT * FROM movies WHERE kategori = 'movies'";
}

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="design.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Cinema</title>
    <link rel="icon" href="logo.jpg" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Bungee&family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.2.0/fonts/remixicon.css">
</head>

<style>
    /* CSS yang sudah ada */
    * {
        margin: 0;
    }
    body {
        font-family: "Roboto", sans-serif;
        background-color: #151515;
    }
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 50px;
        z-index: 1000;
        background-color: rgb(0, 0, 0);
        position: sticky;
        top: 0;
    }
    .navbar-container {
        display: flex;
        align-items: center;
        padding: 0 50px;
        height: 100%;
        color: white;
        font-family: "Sen", sans-serif;
    }
    .logo-container {
        flex: 3;
    }
    .logo {
        font-size: 30px;
        color: rgb(255, 0, 0);
    }
    .menu-container {
        flex: 8;
    }
    .menu-list {
        display: flex;
        list-style: none;
    }
    .menu-list-item {
        margin-right: 80px;
    }
    .menu-list-item a {
        text-decoration: none;
        cursor: pointer;
        outline: none;
        border: none;
        color: white;
        transition: all 0.2s ease-in-out;
    }
    .menu-list-item a:hover {
        color: rgb(255, 0, 0);
    }
    .menu-list-item.active {
        font-weight: bold;
    }
    /* Form Pencarian di dalam Navbar */
    .search-container-navbar {
        flex: 3;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-left: 20px;
    }
    .search-container-navbar form {
        position: relative;
        width: 300px;
        height: 40px;
        background-color: #fff;
        border-radius: 25px;
        display: flex;
        align-items: center;
        padding: 0 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .search-container-navbar form input[type="text"] {
        width: 100%;
        border: none;
        outline: none;
        border-radius: 25px;
        font-size: 14px;
        padding: 10px 15px;
        color: #333;
    }
    .search-container-navbar form button {
        background: none;
        border: none;
        color: #ff0000;
        cursor: pointer;
        font-size: 18px;
    }
    /* Not Found Container */
    .not-found-container {
        text-align: center;
        color: #bbb;
        font-family: Arial, sans-serif;
        padding: 20px;
    }
    .not-found-container h2 {
        font-size: 24px;
        font-weight: normal;
        color: #999;
    }
    .not-found-container h2 strong {
        color: #999;
    }
    .not-found-container .help-section {
        text-align: left;
        max-width: 700px;
        margin: 20px auto;
    }
    .not-found-container .help-section h3 {
        margin-left: 17px;
        font-size: 24px;
        font-weight: bold;
        color: #999;
    }
    .not-found-container .help-section ul {
        margin-left: 30px;
        list-style-type: square;
        padding-left: 20px;
    }
    .not-found-container .help-section ul li {
        font-size: 14px;
        color: #999;
        margin-bottom: 10px;
    }
    .not-found-container .help-section ul li strong {
        color: #ccc;
    }
    /* Container */
    .container {
        background-color: #151515;
        min-height: calc(100vh - 50px);
        color: white;
        padding-top: 60px;
    }
    .search-bar {
        padding: 10px;
        border-radius: 5px;
        display: flex;
        align-items: center;
    }
    .search-bar input {
        flex-grow: 1;
        background: transparent;
        border: none;
        color: white;
        padding: 5px;
    }
    .search-bar button {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
    }
    .search-bar h4 {
        font-size: 20px;
    }
    .movie-card {
        background: #1e1e1e;
        padding: 15px;
        gap: 15px;
        display: flex;
        align-items: center;
        border: 2px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.5);
    }
    .movie-card img {
        width: 100px;
        height: auto;
        border-radius: 5px;
    }
    .movie-card .ms-3 h5 {
        font-size: 20px; 
        color: white;     
        margin-bottom: 5px; 
        transition: all 0.2s ease-in-out;
    }
    .movie-card .ms-3 h5:hover {
        color: rgb(255, 0, 0);
    }
    .movie-card .ms-3 p {
        font-size: 14px; 
        color: gray;     
        margin: 2px 0;    
    }
    .category-badge {
        padding: 0px 8px;
        border-radius: 5px;
        font-size: 13px;
    }
    .movie {
        background-color: green;
    }
    .tv {
        background-color: red;
    }
    .season {
        background-color: purple;
    }
    .title {
        color: rgba(255, 0, 0, 0.51);
        font-size: 15px;
        text-align: center;
        font-family: "Bungee", serif;
        margin-top: 25px;
        padding: 10px;
        border-bottom: 5px solid rgba(208, 208, 208, 0.51);
    }
    @media only screen and (max-width: 940px) {
        .menu-container {
            display: none;
        }
    }
</style>
<body>
    <div class="navbar">
        <div class="navbar-container">
            <div class="logo-container">
                <h1 class="logo">Layar Cinema |</h1>
            </div>
            <div class="menu-container">
                <ul class="menu-list">
                    <li class="menu-list-item active"><a href="index.php">Home</a></li>
                    <li class="menu-list-item"><a href="index-movie.php">Movies</a></li>
                    <li class="menu-list-item"><a href="index-series.php">Series</a></li>
                </ul>
            </div>
            <div class="search-container-navbar">
                <form method="GET" action="search-index.php">
                    <input type="text" name="search" placeholder="Cari berdasarkan judul" required value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>
    <div class="container">
        <form method="GET" class="search-bar mb-4">
            <h4>Result Found: <?php echo htmlspecialchars($search); ?></h4>
        </form>
        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-12 mb-3">
                        <div class="movie-card">
                            <!-- Hanya poster (gambar) yang dapat diklik -->
                            <a href="detail.php?slug=<?php echo $row['slug']; ?>" style="text-decoration:none; color:inherit;">
                                <img src="<?php echo $row['url_poster']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="movie-list-item-img">
                            </a>
                            <div class="ms-3">
                                <!-- Hanya judul yang dapat diklik -->
                                <a href="detail.php?slug=<?php echo $row['slug']; ?>" style="text-decoration:none; color:inherit;">
                                    <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                                </a>
                                <p>Author: <?php echo $row['author']; ?></p>
                                <p class="category-badge <?php echo strtolower($row['kategori']); ?>">
                                    <?php echo strtoupper($row['kategori']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="not-found-container">
                <h2>Maaf, saat ini Film "<strong><?php echo htmlspecialchars($search); ?></strong>" sedang tidak tersedia.</h2>
                <div class="help-section">
                    <h3>Bantuan:</h3>
                    <ul>
                        <li>Format pencarian yang benar ialah <strong>JUDUL + TAHUN RILIS FILM</strong> (jika tidak tahu tahun rilis, silahkan cek Google atau IMDb terlebih dahulu).</li>
                        <li>Pastikan penulisan judul sudah benar, perhatikan text atau typo saat mencari.</li>
                        <li>Jika masih tidak ada hasil, gunakan halaman request untuk me-request film yang diinginkan.</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="app.js"></script>
</body>
</html>
