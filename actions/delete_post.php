<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Get the post ID from the POST request
$postId = $_POST['post_id'];
$userId = $_SESSION['user_id'];

// Verify if the post exists and belongs to the logged-in user
$query = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $postId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    echo "Post not found or you do not have permission to delete it.";
    exit();
}

// Delete the post from the database
$deleteQuery = "DELETE FROM posts WHERE id = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("i", $postId);
$stmt->execute();

// Redirect to the profile page
header("Location: ../view/profile.php");
exit();
?>
