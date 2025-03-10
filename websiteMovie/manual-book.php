<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contoh Card</title>
  <style>
    body {
      background: #f0f0f0;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      max-width: 300px;
      width: 100%;
    }
    .card img {
      width: 100%;
      height: auto;
    }
    .card-body {
      padding: 15px;
    }
    .card-title {
      font-size: 1.2em;
      margin: 0;
      padding-bottom: 10px;
      border-bottom: 1px solid #eaeaea;
    }
    .card-text {
      margin: 15px 0;
      font-size: 0.9em;
      color: #555;
    }
    .card-button {
      display: inline-block;
      background: #3498db;
      color: #fff;
      padding: 10px 15px;
      border-radius: 4px;
      text-decoration: none;
      transition: background 0.3s ease;
    }
    .card-button:hover {
      background: #2980b9;
    }
  </style>
</head>
<body>
  <div class="card">
    <img src="https://via.placeholder.com/300x200" alt="Gambar Card">
    <div class="card-body">
      <h2 class="card-title">Judul Card</h2>
      <p class="card-text">Ini adalah contoh card dengan HTML dan CSS. Anda bisa mengganti isi dan tampilan sesuai kebutuhan.</p>
      <a href="#" class="card-button">Baca Selengkapnya</a>
    </div>
  </div>
</body>
</html>
