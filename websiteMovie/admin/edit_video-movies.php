
<?php
session_start();
include '../db.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location:../user-login.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : '';

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $slug = $_POST['slug'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $kategori = $_POST['kategori'];
    $poster = $_POST['url_poster'];
    $video = $_POST['url_video'];

   $image = $_POST['image'];
    $sql = "UPDATE movies SET title = ?, slug = ?, author = ?, description = ?, kategori = ?, url_poster = ?, url_video = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $title, $slug, $author, $description, $kategori, $poster, $video, $id);
    if ($stmt->execute()) {
        header('Location: ../admin-crud-movies.php');
        exit();
    } else {
        $error = "Gagal mengedit video.";
    }

}

// Ambil data video
$sql = "SELECT * FROM movies WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$video = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Video</title>
    <link rel="icon" href="../logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
</head>
<style>
    /* style.css */

body {
    font-family: Arial, sans-serif;
    background: #f4f4f9;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.container {
    background: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 20px;
    max-width: 400px;
    width: 90%;
    box-sizing: border-box;
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-direction: column;
}

label {
    font-weight: bold;
    margin-bottom: 5px;
    color: #555;
}

input[type="text"], 
textarea {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    font-size: 14px;
    margin-bottom: 15px;
    width: 100%;
    box-sizing: border-box;
    transition: border-color 0.3s ease-in-out;
}

input[type="text"]:focus, 
textarea:focus {
    border-color: #007BFF;
    outline: none;
}

textarea {
    resize: none;
    height: 80px;
}

button {
    background: #007BFF;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 10px ;
    font-size: 16px;
    cursor: pointer;
    margin-top: 10px;
    transition: background-color 0.3s ease;
}

button:hover {
    background: #0056b3;
}

p {
    text-align: center;
    color: #e74c3c;
    font-size: 14px;
}

</style>
<body>

<div class="container">
    <h2>Tambah Video Baru</h2>

    <?php if (isset($error)) { echo "<p s   tyle='color:red;'>$error</p>"; } ?>

    <form action="" method="POST">
    <input type="hidden" name="id" value="<?php echo $video['id']; ?>">
        <label for="title">Judul Video:</label>
        <input type="text" name="title" value="<?php echo $video['title']; ?>" required>

        <label for="slug">Slug:</label>
        <input type="text" name="slug" value="<?php echo $video['slug']; ?>" required>

        <label for="author">Author:</label>
        <input type="text" name="author" value="<?php echo $video['author']; ?>" required>

        <label for="description">Deskripsi:</label>
        <input type="text" name="description" value="<?php echo $video['description']; ?>" required></input>

        <label for="kategori">Kategori:</label>
        <select name="kategori" id="kategori">
            <option value="movies">Pilih Kategori</option>
            <option value="movies" <?php echo ($video['kategori'] == 'movies') ? 'selected' : 'Pilih Kategori'; ?>>Movies</option>
            <option value="series" <?php echo ($video['kategori'] == 'series') ? 'selected' : 'Pilih Kategori'; ?>>Series</option>
        </select>

        <label for="poster">URL Poster:</label>
        <input type="text" name="url_poster" value="<?php echo $video['url_poster']; ?>"required>

        <label for="poster">URL Trailer:</label>
        <input type="text" name="url_video" value="<?php echo $video['url_video']; ?>"required>

        <button type="submit" name="submit">Tambah Video</button>
        <button type="button" onclick="window.history.back()">Kembali</button>
    </form>
</div>

</body>
</html>
