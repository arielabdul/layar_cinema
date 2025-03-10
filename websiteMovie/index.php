<?php
session_start();
include 'db.php';

// Perbolehkan akses jika user sudah login atau admin sudah login
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: user-login.php");
    exit();
}

// Periksa apakah ada parameter pencarian
$search = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    
    // Query film movies berdasarkan pencarian
    $query = "SELECT * FROM movies WHERE kategori = 'movies' AND title LIKE '%$search%'";
    
    // Ubah query film series agar juga difilter berdasarkan judul
    $series = "SELECT * FROM movies WHERE kategori = 'series' AND title LIKE '%$search%'";
} else {
    $query = "SELECT * FROM movies WHERE kategori = 'movies'";
    $series = "SELECT * FROM movies WHERE kategori = 'series'";
}

$result = mysqli_query($conn, $query);
$result_series = mysqli_query($conn, $series);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Layar Cinema</title>
  <link rel="stylesheet" href="design.css">
  <link rel="icon" href="logo.jpg" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.2.0/fonts/remixicon.css">
  <style>
    /* ==== KODE ASLI UNTUK DESKTOP ==== */
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
      z-index: 1000;
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

    /* Logo */
    .logo-container {
      flex: 3;
    }

    .logo {
      font-size: 30px;
      color: rgb(255, 0, 0);
    }

    /* Menu Navigasi */
    .menu-container {
      flex: 8 ;
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

    /* Sidebar (tetap ada) */
    .sidebar {
      width: 50px;
      height: 100%;
      background-color: rgb(0, 0, 0);
      position: fixed;
      top: 0px;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-top: 60px;
    }

    .left-menu-icon {
      color: white;
      margin-top: 80px;
      font-size: 20px;
      margin-bottom: 40px;
      cursor: pointer;
    }

    .sidebar:hover {
      color: rgb(255, 0, 0);
    }

    /* Container utama */
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
      margin-top: -60px;
      margin-left: -50px;
    }

    .featured-desc {
      width: 500px;
      color: lightgray;
      margin: -35px 0;
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

    /* ==================footer================== */
    .footer {
      background-color: black;
      padding: 40px;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }
    .footer .logo {
      margin-left: 50px;
      font-size: 40px;
      font-weight: bold;
      color: red;
    }
    .footer .desc {
      margin-left: 50px;
      max-width: 300px;
      font-size: 14px;
    }
    .footer .column {
      margin: 20px;
    }
    .footer .column h3 {
      font-size: 16px;
      margin-bottom: 10px;
    }
    .footer .column a {
      display: block;
      color: red;
      text-decoration: none;
      font-size: 14px;
      margin-bottom: 5px;
    }
    .footer .column a:hover {
      text-decoration: underline;
    }

    .and-text {
      background-color: black;
      text-align: center;
      padding: 20px;
    }

    .and-text p {
      color: #fff;
      text-transform: capitalize;
    }

    /* =====================================================================
       TAMBAHAN AGAR HAMBURGER MUNCUL HANYA DI MOBILE & MENU JADI SIDEBAR
       TANPA MENGUBAH TAMPILAN DESKTOP
       ===================================================================== */

    /* Hamburger icon: hidden by default (desktop) */
    .hamburger {
      display: none;
      font-size: 24px;
      color: #fff;
      margin-left: 10px;
      cursor: pointer;
    }

    /* Mobile Sidebar */
    .mobile-sidebar {
      width: 200px;
      height: 100%;
      background-color: #000;
      position: fixed;
      top: 0;
      left: 0;
      transform: translateX(-100%);
      transition: transform 0.3s ease;
      z-index: 2001; 
      padding-top: 40px;
    }
    .mobile-sidebar.active {
      transform: translateX(0);
    }
    .mobile-menu {
      list-style: none;
      text-align: center;
      width: 100%;
    }
    .mobile-menu li {
      margin: 15px 0;
    }
    .mobile-menu li a {
      text-decoration: none;
      color: #fff;
      font-size: 16px;
      transition: 0.2s;
    }
    .mobile-menu li a:hover {
      color: red;
    }
    .mobile-menu .active a {
      font-weight: bold;
      color: red;
    }

    /* Responsive (max-width: 767px) */
    @media (max-width: 767px) {
      /* Sembunyikan menu horizontal di navbar */
      .menu-container {
        display: none;
      }
      .hamburger {
        display: inline-block;
      }
      .logo {
        font-size: 24px;
      }
      .search-container-navbar form {
        width: 100%;
        max-width: 200px;
      }
      .featured-content {
        height: 40vh;
        padding: 20px;
      }
      .featured-title {
        width: 150px;
        margin: 0 auto 10px;
        display: block;
      }
      .featured-desc {
        width: 100%;
        text-align: center;
        margin: 0;
        font-size: 14px;
      }
      /* Footer disembunyikan di mobile */
      .footer {
        display: none; /* HIDE FOOTER IN MOBILE */
      }
    }
  </style>
</head>
<body> 

  <!-- NAVBAR -->
  <div class="navbar">
    <div class="navbar-container">
      <!-- Logo -->
      <div class="logo-container">
        <h1 class="logo">Layar Cinema |</h1>
      </div>
      <!-- Menu Navigasi (Desktop) -->
      <div class="menu-container">
        <ul class="menu-list">
          <li class="menu-list-item active"><a href="#">Home</a></li>
          <li class="menu-list-item"><a href="index-movie.php">Movies</a></li>
          <li class="menu-list-item"><a href="index-series.php">Series</a></li>
        </ul>
      </div>
      <!-- Search (tetap sama), + hamburger (hidden di desktop) -->
      <div class="search-container-navbar">
        <form method="GET" action="search-index.php">
          <input type="text" name="search" placeholder="Cari berdasarkan judul" required value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
          <button type="submit"><i class="fas fa-search"></i></button>
        </form>
        <!-- Hamburger icon (muncul hanya di mobile) -->
        <div class="hamburger" onclick="toggleMobileSidebar()">
          <i class="fas fa-bars"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="sidebar">
        <a href="logout.php"><i class="left-menu-icon fas fa-sign-out-alt"></i></a>
    </div>

  <!-- MOBILE SIDEBAR (muncul saat hamburger diklik di mobile) -->
  <div class="mobile-sidebar" id="mobileSidebar">
    <ul class="mobile-menu">
      <li class="active"><a href="#">Home</a></li>
      <li><a href="index-movie.php">Movies</a></li>
      <li><a href="index-series.php">Series</a></li>
    </ul>
  </div>

  <!-- Container Utama -->
  <div class="container">
    <div class="content-container">
      <!-- Konten Featured -->
      <div class="featured-content" style="background: linear-gradient(to bottom, rgba(0,0,0,0), #151515), url('img/f-1.jpg');">
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
                  <img src="<?php echo $movie['url_poster']; ?>" alt="<?php echo $movie['title']; ?>" class="movie-list-item-img">
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
                  <img src="<?php echo $row_series['url_poster']; ?>" alt="<?php echo $row_series['title']; ?>" class="movie-list-item-img">
                </a>
              </div>
            <?php } ?>
          </div>
          <i class="fas fa-chevron-right arrow"></i>
        </div>
      </div>
    </div>

    <!-- Footer (Hanya tampil di desktop/tablet, disembunyikan di mobile) -->
    <footer class="footer">
      <div>
        <div class="logo">Layar Cinema</div>
        <p class="desc">
          Website ini dirancang sebagai platform bagi para pecinta film untuk melihat trailer terbaru dan mengetahui rating film sebelum menontonnya. 
          Dengan tampilan yang user-friendly dan fitur pencarian yang intuitif, pengguna dapat dengan mudah menemukan film
          favorit mereka berdasarkan genre, tahun rilis, atau popularitas.
        </p>
      </div>
      <div class="column">
        <h3>Original Series</h3>
        <a href="https://tv.apple.com/">Apple TV+</a>
        <a href="https://www.disneyplus.com/">Disney+</a>
        <a href="https://www.max.com/">HBO</a>
        <a href="https://www.netflix.com/">Netflix</a>
        <a href="https://www.idlix.com/">IDLIX</a>
      </div>
      <div class="column">
        <h3>Category</h3>
        <a href="index-movie.php">Movie</a>
        <a href="index-series.php">Series</a>
      </div>
      <div class="column">
        <h3>IDLIX</h3>
        <a href="https://vip.idlixvip.asia/tag/dc-movies/">DCEU Movie</a>
        <a href="https://vip.idlixvip.asia/tag/marvel/">MCU Movie</a>
        <a href="https://vip.idlixvip.asia/tag/disney/">Disney+ Movie and Series</a>
        <a href="https://vip.idlixvip.asia/tag/netflix/">Netflix Movie and Series</a>
      </div>
    </footer>
    <div class="and-text">
      <p>Chopyright @ 2025 by Layar Cinema. All Rights Reserved.</p>
    </div>
  </div>
  
  <script>
    // Fungsi toggle untuk mobile sidebar
    function toggleMobileSidebar() {
      const mobileSidebar = document.getElementById("mobileSidebar");
      mobileSidebar.classList.toggle("active");
    }

    // Slider Arrow Functionality
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
