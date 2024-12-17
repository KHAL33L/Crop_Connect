<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    $query = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $username, $email, $userId);
    $stmt->execute();

    header("Location: profile.php");
    exit();
}
?>
