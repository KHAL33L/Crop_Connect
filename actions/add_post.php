<?php 
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imageUrl = null;

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imagePath = $targetDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    $query = "INSERT INTO posts (user_id, title, content, image_url) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $userId, $title, $content, $imageUrl);
    $stmt->execute();

    header("Location: ../view/profile.php");
    exit();
}
?>
