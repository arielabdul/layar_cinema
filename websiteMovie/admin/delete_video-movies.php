<?php
session_start();
include '../db.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location:../user-login.php");
    exit();
}
$movie_id = $_GET['id']; // Pastikan ini berasal dari parameter yang valid

// Hapus terlebih dahulu semua rating terkait
$stmt = $conn->prepare("DELETE FROM ratings WHERE movies_id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$stmt->close();

// Setelah itu, baru hapus filmnya
$stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$stmt->close();

header("Location: ../admin-crud-movies.php"); // Redirect setelah penghapusan berhasil
exit();


?>
