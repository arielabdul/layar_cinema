<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Ambil username dari review yang akan dihapus
    $stmt = $conn->prepare("SELECT username FROM ratings WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($review_username);
    
    if ($stmt->fetch()) {
        $stmt->close();
        
        // Pastikan user yang login sama dengan pemilik review
        if ($review_username === $_SESSION['username']) {
            $stmt_delete = $conn->prepare("DELETE FROM ratings WHERE id = ?");
            $stmt_delete->bind_param("i", $id);
            
            if ($stmt_delete->execute()) {
                // Redirect kembali ke halaman sebelumnya
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                echo "Gagal menghapus review.";
            }
            $stmt_delete->close();
        } else {
            echo "Anda tidak memiliki izin untuk menghapus komentar ini.";
        }
    } else {
        echo "Review tidak ditemukan.";
        $stmt->close();
    }
}
?>
