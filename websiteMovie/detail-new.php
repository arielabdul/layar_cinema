<?php
session_start();
include 'db.php';

// Perbolehkan akses jika user atau admin sudah login
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: user-login.php");
    exit();
}

// Periksa apakah ada parameter pencarian
$search = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    // Mengamankan input pencarian dari karakter berbahaya
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    
    // Query untuk kategori 'movies' yang difilter berdasarkan title
    $query = "SELECT * FROM movies WHERE kategori = 'movies' AND title LIKE '%$search%'";
    
    // Query untuk kategori 'series' yang difilter berdasarkan title
    $series = "SELECT * FROM movies WHERE kategori = 'series' AND title LIKE '%$search%'";
} else {
    // Jika tidak ada pencarian, ambil semua data sesuai kategori
    $query = "SELECT * FROM movies WHERE kategori = 'movies'";
    $series = "SELECT * FROM movies WHERE kategori = 'series'";
}

$result = mysqli_query($conn, $query);
$result_series = mysqli_query($conn, $series);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="design.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Cinema</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.2.0/fonts/remixicon.css">
    <style>
        /* Reset margin */
        * {
            margin: 0;
        }
  
        body {
            font-family: "Roboto", sans-serif;
        }
  
        .navbar {
            width: 100%;
            height: 50px;
            background-color: rgb(0, 0, 0);
            position: sticky;
            z-index: 99999;
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
            flex: 2;
        }
  
        .logo {
            font-size: 30px;
            color: rgb(255, 0, 0);
        }
  
        .menu-container {
            flex: 6;
        }
  
        .menu-list {
            display: flex;
            list-style: none;
        }
  
        .menu-list-item {
            margin-right: 30px;
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
  
        .profile-container {
            flex: 2;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
  
        .toggle {
            width: 40px;
            height: 20px;
            background-color: white;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            position: relative;
        }
  
        .toggle-icon {
            color: goldenrod;
        }
  
        .toggle-ball {
            width: 18px;
            height: 18px;
            background-color: black;
            position: absolute;
            right: 1px;
            border-radius: 50%;
            cursor: pointer;
            transition: 1s ease all;
        }
  
        .sidebar {
            width: 50px;
            height: 100%;
            background-color: rgb(0, 0, 0);
            position: fixed;
            top: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 60px;
        }
        .left-menu-icon {
            color: white;
            font-size: 20px;
            margin-bottom: 40px;
            cursor: pointer;
        }
  
        .sidebar:hover {
            color: rgb(255, 0, 0);
        }
  
        .container {
            background-color: #151515;
            min-height: calc(100vh - 50px);
            color: white;
        }
  
        .content-container {
            margin-left: 50px;
            padding: 20px;
        }
  
        .featured-content {
            height: 50vh;
            padding: 50px;
            background-size: cover;
            background-position: center;
        }
  
        .featured-title {
            width: 200px;
        }
  
        .featured-desc {
            width: 500px;
            color: lightgray;
            margin: 30px 0;
        }
  
        .featured-button {
            background-color: rgba(0, 0, 0, 0.86);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            outline: none;
            font-weight: bold;
        }
  
        .movie-list-container {
            padding: 0 20px;
            margin-top: 40px;
        }
  
        .movie-list-wrapper {
            position: relative;
            overflow: hidden;
            width: 100%;
        }
  
        .movie-list {
            display: flex;
            align-items: center;
            height: 300px;
            transform: translateX(0);
            transition: all 1s ease-in-out;
        }
  
        .movie-list-item {
            margin-right: 30px;
            position: relative;
        }
  
        .movie-list-item:hover .movie-list-item-img {
            transform: scale(1.2);
            margin: 0 30px;
            opacity: 0.5;
        }
  
        .movie-list-item-img {
            transition: all 1s ease-in-out;
            width: 200px;
            height: 270px;
            object-fit: cover;
            border-radius: 20px;
        }
  
        .arrow-left, .arrow {
            font-size: 100px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: lightgray;
            opacity: 0.5;
            cursor: pointer;
            transition: 0.3s;
            z-index: 10;
            padding: 10px;
            border-radius: 50%;
        }
  
        .arrow-left {
            left: 5px;
        }
  
        .arrow {
            right: 5px;
        }
  
        .arrow-left:hover, .arrow:hover {
            opacity: 1;
        }
  
        /* === Tampilan Search UI (menggunakan class .search_user) === */
        .search_user {
            position: relative;
            margin: 20px 50px;
            background: rgba(0,0,0,0.8);
            padding: 15px;
            border-radius: 10px;
        }
  
        .search_user input[type="text"] {
            width: calc(100% - 50px);
            padding: 10px;
            border: none;
            outline: none;
            font-size: 16px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
  
        .search_user img {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }
  
        .search_user .search {
            margin-top: 15px;
        }
  
        .search_user .card {
            display: flex;
            align-items: center;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            color: white;
        }
  
        .search_user .card img {
            width: 50px;
            height: 70px;
            border-radius: 5px;
            margin-right: 15px;
        }
  
        .search_user .cont h3 {
            margin: 0;
            font-size: 18px;
        }
    </style>
</head>
<body> 

    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-container">
            <div class="logo-container">
                <h1 class="logo">Layar Cinema</h1>
            </div>
            <div class="menu-container">
                <ul class="menu-list">
                    <li class="menu-list-item active"><a href="">Home</a></li>
                    <li class="menu-list-item"><a href="index-movie.php">Movies</a></li>
                    <li class="menu-list-item"><a href="index-series.php">Series</a></li>
                </ul>
            </div>
            <div class="profile-container">
                <div class="toggle">
                    <i class="fas fa-moon toggle-icon"></i>
                    <i class="fas fa-sun toggle-icon"></i>
                    <div class="toggle-ball"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar dengan ikon pencarian dan logout -->
    <div class="sidebar">
        <!-- Klik ikon search untuk menampilkan/menghilangkan search UI -->
        <a href="#" id="toggleSearch"><i class="left-menu-icon fas fa-search"></i></a>
        <a href="logout.php"><i class="left-menu-icon fas fa-sign-out-alt"></i></a>
    </div>

    <!-- Container Utama -->
    <div class="container">
        <div class="content-container">
            <!-- Tampilan Search UI (disembunyikan secara default) -->
            <div class="search_user" id="searchUser" style="display: none;">
                <!-- Form pencarian (dengan metode GET) -->
                <form method="GET" action="index.php">
                    <input type="text" name="search" placeholder="Search..." id="search_input" value="<?php echo htmlspecialchars($search); ?>">
                </form>
                <img src="img/user.jpg" alt="User">
                <div class="search">
                    <?php 
                    // Jika ada parameter pencarian, tampilkan hasilnya sebagai "card"
                    if (!empty($search)) {
                        $result_search = mysqli_query($conn, "SELECT * FROM movies WHERE title LIKE '%$search%'");
                        while ($row = mysqli_fetch_assoc($result_search)) { ?>
                            <a href="detail.php?slug=<?php echo $row['slug']; ?>" class="card">
                                <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>">
                                <div class="cont">
                                    <h3><?php echo $row['title']; ?></h3>
                                </div>
                            </a>
                    <?php }
                    } ?>
                </div>
            </div>

            <!-- Konten Featured -->
            <div class="featured-content"
                style="background: linear-gradient(to bottom, rgba(0,0,0,0), #151515), url('img/f-1.jpg');">
                <img class="featured-title" src="img/pt.png" alt="Featured Title">
                <p class="featured-desc">
                    Website Layar Cinema adalah sebuah aplikasi web yang dirancang untuk mempermudah pengelolaan untuk melihat rating dan trailer film serta menyediakan informasi tentang sinema bioskop secara online. 
                    Aplikasi ini menawarkan pengalaman sinematik yang modern dan mudah digunakan.
                </p>
            </div>

            <!-- List Film Movies -->
            <div class="movie-list-container">
                <h1 class="movie-list-title">Film Movies</h1>
                <div class="movie-list-wrapper">
                    <i class="fas fa-chevron-left arrow-left"></i>
                    <div class="movie-list">
                        <?php while ($movie = mysqli_fetch_assoc($result)) { ?>
                            <div class="movie-list-item">
                                <a href="detail.php?slug=<?php echo $movie['slug']; ?>">
                                    <img src="<?php echo $movie['image']; ?>" alt="<?php echo $movie['title']; ?>" class="movie-list-item-img">
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                    <i class="fas fa-chevron-right arrow"></i>
                </div>
            </div>

            <!-- List Film Series -->
            <div class="movie-list-container">
                <h1 class="movie-list-title">Film Series</h1>
                <div class="movie-list-wrapper">
                    <i class="fas fa-chevron-left arrow-left"></i>
                    <div class="movie-list">
                        <?php while ($row_series = mysqli_fetch_assoc($result_series)) { ?>
                            <div class="movie-list-item">
                                <a href="detail.php?slug=<?php echo $row_series['slug']; ?>">
                                    <img src="<?php echo $row_series['image']; ?>" alt="<?php echo $row_series['title']; ?>" class="movie-list-item-img">
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                    <i class="fas fa-chevron-right arrow"></i>
                </div>
            </div>

        </div>
    </div>
    
    <script>
    // Toggle tampilan search UI ketika ikon search di sidebar diklik
    document.getElementById('toggleSearch').addEventListener('click', function(e) {
      e.preventDefault(); // Mencegah navigasi
      var searchUser = document.getElementById('searchUser');
      if (searchUser.style.display === "none" || searchUser.style.display === "") {
        searchUser.style.display = "block";
      } else {
        searchUser.style.display = "none";
      }
    });

    // Slider Arrow Functionality untuk menggeser daftar film
    document.addEventListener("DOMContentLoaded", function () {
      const arrowsRight = document.querySelectorAll(".arrow");
      const arrowsLeft = document.querySelectorAll(".arrow-left");
      const movieLists = document.querySelectorAll(".movie-list");

      arrowsRight.forEach((arrow, i) => {
          arrow.addEventListener("click", () => {
              const wrapperWidth = movieLists[i].parentElement.offsetWidth;
              const listWidth = movieLists[i].scrollWidth;
              const maxScroll = listWidth - wrapperWidth;

              let currentTransform = parseInt(
                  movieLists[i].style.transform.replace("translateX(", "").replace("px)", "")
              ) || 0;
              
              let newTransform = currentTransform - 230;
              if (Math.abs(newTransform) > maxScroll) {
                  newTransform = -maxScroll;
              }
              movieLists[i].style.transform = `translateX(${newTransform}px)`;
          });
      });

      arrowsLeft.forEach((arrow, i) => {
          arrow.addEventListener("click", () => {
              let currentTransform = parseInt(
                  movieLists[i].style.transform.replace("translateX(", "").replace("px)", "")
              ) || 0;
              
              let newTransform = currentTransform + 230;
              if (newTransform > 0) {
                  newTransform = 0;
              }
              movieLists[i].style.transform = `translateX(${newTransform}px)`;
          });
      });
    });
    </script>
</body>
</html>
