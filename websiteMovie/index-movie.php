<?php
session_start();
include 'db.php';

// Pastikan admin sudah login
if (!isset($_SESSION['user_id'])) {
  header('Location: user-login.php');
  exit();
}

// Ambil data dari database
$query = "SELECT * FROM movies WHERE kategori = 'movies'";
$result = mysqli_query($conn, $query);

// Periksa apakah ada parameter pencarian
$search = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    
    // Query film movies berdasarkan pencarian
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
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.2.0/fonts/remixicon.css">
</head>

<style>
    * {
    margin: 0;
  }
  
  body {
    font-family: "Roboto", sans-serif;
    background-color: #151515;
  }
  
  .navbar {
    position: fixed
    top: 0;
    left: 0;
    width: 100%;
    height: 50px;
    z-index: 1000;
    background-color:rgb(0, 0, 0);
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
    color:rgb(255, 0, 0);
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

  /* Form Pencarian di dalam Navbar (di samping kanan menu) */
  .search-container-navbar {
    flex: 3;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-left: 20px;
}

.search-container-navbar form {
    position: relative;
    width: 300px;        /* Atur lebar sesuai kebutuhan */
    height: 40px;        /* Ubah nilai ini untuk mengatur tinggi search bar */
    background-color: #fff;
    border-radius: 25px;
    display: flex;
    align-items: center;
    padding: 0 10px;     /* Padding vertikal dapat disesuaikan jika diperlukan */
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.search-container-navbar form input[type="text"] {
    width: 100%;
    border: none;
    outline: none;
    border-radius: 25px;
    font-size: 14px;
    padding: 10px 15px;  /* Padding di dalam input mempengaruhi tinggi tampilan teks */
    color: #333;
}

.search-container-navbar form button {
    background: none;
    border: none;
    color: #ff0000;
    cursor: pointer;
    font-size: 18px;
}

  .profile-container {
    flex: 2;
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }
  
  .profile-text-container {
    margin: 0 20px;
  }
  
  .profile-picture {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
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

  .sidebar:hover {
    color: rgb(255, 0, 0);
  }
  
  .left-menu-icon {
            color: white;
            margin-top: 80px;
            font-size: 20px;
            margin-bottom: 40px;
            cursor: pointer;
        }
  
  .container {
    
    background-color: #151515;
    min-height: calc(100vh - 50px);
    color: white;
  }
  
  .content-container {
    margin-left: 50px;
  }
  
  .featured-content {
    height: 50vh;
    padding: 50px;
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
    background-color:rgba(0, 0, 0, 0.86);
    color: white;
    padding: 10px 20px;
    border-radius: 10px;
    border: none;
    outline: none;
    font-weight: bold;
  }
  
  .movie-list-container {
    padding: 0 20px;
  }
  
  .movie-list-wrapper {
  /* Hilangkan overflow untuk menampilkan semua baris */
  /* overflow: hidden; */
}

.movie-list {
  display: flex;
  flex-wrap: wrap; /* Mengizinkan item pindah ke baris berikutnya */
  justify-content: center; /* Opsional: agar item rata tengah */
  height: auto; /* Agar tinggi container menyesuaikan dengan jumlah item */
  transition: all 0.5s ease-in-out;
}

.movie-list-item {
  flex: 0 0 100px; /* Atur lebar tetap untuk tiap item */
  margin: 10px;    /* Spasi antar item */
}


  
  .movie-list-item:hover .movie-list-item-img {
    transform: scale(1.2);
    margin: 0 30px;
    opacity: 0.5;
  }
  
  .movie-list-item:hover .movie-list-item-title,
  .movie-list-item:hover .movie-list-item-desc,
  .movie-list-item:hover .movie-list-item-button {
    opacity: 1;
  }
  
  .movie-list-item-img {
    margin-top: 20px;
    transition: all 1s ease-in-out;
    width: 152px;
    height: 200px;
    object-fit: cover;
    border-radius: 20px;
  }
  
  .movie-list-item-title {
    background-color: #333;
    padding: 0 10px;
    font-size: 32px;
    font-weight: bold;
    position: absolute;
    top: 10%;
    left: 50px;
    opacity: 0;
    transition: 1s all ease-in-out;
  }
  
  .movie-list-item-desc {
    background-color: #333;
    padding: 10px;
    font-size: 14px;
    position: absolute;
    top: 30%;
    left: 50px;
    width: 230px;
    opacity: 0;
    transition: 1s all ease-in-out;
  }
  
  .movie-list-item-button {
    padding: 10px;
    background-color: #4dbf00;
    color: white;
    border-radius: 10px;
    outline: none;
    border: none;
    cursor: pointer;
    position: absolute;
    bottom: 20px;
    left: 50px;
    opacity: 0;
    transition: 1s all ease-in-out;
  }
  
  .movie-list-item-button a {
    text-decoration: none;
    color: #fff;
    padding: 5px 10px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
  }
  
  .arrow {
    font-size: 120px;
    position: absolute;
    top: 90px;
    right: 0;
    color: lightgray;
    opacity: 0.5;
    cursor: pointer;
  }
  
  .container.active {
    background-color: white;
  }
  
  .movie-list-title.active {
    color: black;
  }
  
  .navbar-container.active {
    background-color: white;
  
    color: black;
  }
  
  .sidebar.active{
      background-color: rgb(255, 255, 255);
  }
  
  .left-menu-icon.active{
      color: black;
  }
  
  .toggle.active{
      background-color: black;
  }
  
  .toggle-ball.active{
      background-color: rgb(255, 255, 255);
      transform: translateX(-20px);
  }

  .title{
    color: rgba(255, 0, 0, 0.51);
      font-size: 15px;
      text-align: center;
      font-family: "Bungee", serif;
      margin-top: 25px;
      /* background: rgb(42, 42, 42); */
      padding: 10px;
      border-bottom: 5px solid rgba(208, 208, 208, 0.51);

  }
  
  @media only screen and (max-width: 940px){
      .menu-container{
          display: none;
      }
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
        @media (max-width: 768px) {
            .footer {
                flex-direction: column;
                text-align: center;
            }
            .footer .column {
                margin-bottom: 20px;
            }
        }

        .and-text{
        background-color: black;
        text-align: center;
        padding: 20px;
    }

    .and-text p{
        color: #fff;
        text-transform: capitalize
    }
        /* ============================================== */
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
                 <form method="GET" action="search-movie.php">
           <input type="text" name="search" placeholder="Cari berdasarkan judul" required value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
           <button type="submit"><i class="fas fa-search"></i></button>
                 </form>
          </div>
            <!-- <div class="profile-container">
                <div class="toggle">
                    <i class="fas fa-moon toggle-icon"></i>
                    <i class="fas fa-sun toggle-icon"></i>
                    <div class="toggle-ball"></div>
                </div>
            </div> -->
        </div>
    </div>
    <div class="sidebar">
       
        <a href="logout.php"><i class="left-menu-icon fas fa-sign-out-alt"></i></a>
    </div>
<div class="container">
    <div class="content-container">
           <div class="title">
            <h1>--- FILM MOVIES ---</h1>
           </div> 

    <div class="movie-list-container">
                <div class="movie-list-wrapper">
                    <div class="movie-list">
                        <?php while ($query = mysqli_fetch_assoc($result)) { ?>
                            <div class="movie-list-item">
                                <a href="detail.php?slug=<?php echo $query['slug']; ?>">
                                    <img src="<?php echo $query['url_poster']; ?>" alt="<?php echo $query['title']; ?>" class="movie-list-item-img">
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
    </div>
    <footer class="footer">
        <div>
            <div class="logo">Layar Cinema</div>
            <p class="desc">Website ini dirancang sebagai platform bagi para pecinta film untuk melihat trailer terbaru dan mengetahui rating film sebelum menontonnya. Dengan tampilan yang user-friendly dan fitur pencarian yang intuitif, pengguna dapat dengan mudah menemukan film
                 favorit mereka berdasarkan genre, tahun rilis, atau popularitas.</p>
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
            <a href="https://vip.idlixvip.asia/tag/netflix/">Netflix Movie and Series</a>e
        </div>
    </footer>
    <div class="and-text">
        <p>Chopyright @ 2025 by Layar Cinema. All Rights Reserved.</p>
    </div>
</div>
    
</body>
<script src="app.js"></script>
</html>