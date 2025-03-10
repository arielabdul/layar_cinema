<?php 
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $fullname = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Validasi input
    if (empty($fullname) || empty($email) || empty($password)) {
        echo "<p style='color: red;'>Semua kolom harus diisi.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color: red;'>Email tidak valid.</p>";
    } else {
        // Hash password untuk keamanan
        $hashed_password = $password;

        // Query untuk memasukkan data ke tabel users
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $fullname, $email, $hashed_password);

            if ($stmt->execute()) {
              // echo "<p style='color: green;'>Registrasi berhasil! Mengarahkan ke halaman login...</p>";
              header('Location: user-login.php'); // Redirect ke halaman login setelah 2 detik
              exit();
          } else {
              echo "<p style='color: red;'>Terjadi kesalahan: " . $stmt->error . "</p>";
          }

            $stmt->close();
        } else {
            echo "<p style='color: red;'>Terjadi kesalahan: " . $conn->error . "</p>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap");

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Roboto", sans-serif;
    }

    body {
      background: #000;
    }

    body::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0.5;
      width: 100%;
      height: 100%;
      background: url("img/bg.jpg");
      background-position: center;
    }

    nav {
      position: fixed;
      padding: 25px 60px;
      z-index: 1;
    }


    nav a img {
      width: 167px;
    }

    .form-wrapper {
      position: absolute;
      left: 50%;
      top: 50%;
      border-radius: 4px;
      padding: 70px;
      width: 450px;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.75);
    }

    .form-wrapper h2 {
      color: #fff;
      font-size: 2rem;
    }

    .form-wrapper form {
      margin: 25px 0 65px;
    }

    form .form-control {
      height: 50px;
      position: relative;
      margin-bottom: 16px;
    }

    .form-control input {
      height: 100%;
      width: 100%;
      background: #333;
      border: none;
      outline: none;
      border-radius: 4px;
      color: #fff;
      font-size: 1rem;
      padding: 0 20px;
    }

    .form-control input:is( :focus, :valid) {
      background: #444;
      padding: 16px 20px 0;
    }

    .form-control label {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1rem;
      pointer-events: none;
      color: #8c8c8c;
      transition: all 0.1s ease;
    }

    .form-control input:is( :focus, :valid)~label {
      font-size: 0.75rem;
      transform: translateY(-130%);
    }

    form button {
      width: 100%;
      padding: 16px 0;
      font-size: 1rem;
      background: #e50914;
      color: #fff;
      font-weight: 500;
      border-radius: 4px;
      border: none;
      outline: none;
      margin: 25px 0 10px;
      cursor: pointer;
      transition: 0.1s ease;
    }

    form button:hover {
      background: #c40812;
    }

    .form-wrapper a {
      text-decoration: none;
    }

    .form-wrapper a:hover {
      text-decoration: underline;
    }

    .form-wrapper :where(label, p, small, a) {
      color: #b3b3b3;
    }

    form .form-help {
      display: flex;
      justify-content: space-between;
    }

    form .remember-me {
      display: flex;
    }

    form .remember-me input {
      margin-right: 5px;
      accent-color: #b3b3b3;
    }

    form .form-help :where(label, a) {
      font-size: 0.9rem;
    }

    .form-wrapper p a {
      color: #fff;
    }

    .form-wrapper small {
      display: block;
      margin-top: 15px;
      color: #b3b3b3;
    }

    .form-wrapper small a {
      color: #0071eb;
    }

    @media (max-width: 740px) {
      body::before {
        display: none;
      }

      nav,
      .form-wrapper {
        padding: 20px;
      }

      nav a img {
        width: 140px;
      }

      .form-wrapper {
        width: 100%;
        top: 43%;
      }

      .form-wrapper form {
        margin: 25px 0 40px;
      }
    }

    
        /* -- External Social Link CSS Styles -- */

        #source-link {
            top: 120px;
        }

        #source-link>i {
            color: rgb(94, 106, 210);
        }

        #yt-link {
            top: 65px;
        }

        #yt-link>i {
            color: rgb(219, 31, 106);

        }

        #Fund-link {
            top: 10px;
        }

        #Fund-link>i {
            color: rgb(255, 251, 0);

        }

        .meta-link {
            align-items: center;
            backdrop-filter: blur(3px);
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            display: inline-flex;
            gap: 5px;
            left: 10px;
            padding: 10px 20px;
            position: fixed;
            text-decoration: none;
            transition: background-color 600ms, border-color 600ms;
            z-index: 10000;
        }

        .meta-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .meta-link>i,
        .meta-link>span {
            height: 20px;
            line-height: 20px;
        }

        .meta-link>span {
            color: white;
            font-family: "Rubik", sans-serif;
            transition: color 600ms;
        }
  </style>
</head>

<body>

  <!-- <nav>
    <a href="#"><img src="logo.svg" alt="logo"></a>
  </nav> -->
  <div class="form-wrapper">
    <h2>Register</h2>
    <form action="" method="POST">
      <div class="form-control">
        <input type="text" name="username" required>
        <label>Full Name</label>
      </div>
      <div class="form-control">
        <input type="email" name="email" required>
        <label>Email</label>
      </div>
      <div class="form-control">
        <input type="password" name="password" required>
        <label>Password</label>
      </div>
      <button type="submit">Register</button>
    </form>
    <p>Siap Menonton? <a href="user-login.php">Masuk Sekarang</a></p>
  </div>
</body>

</html>
