<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to add a comment.'); window.location.href = '../view/signup.php';</script>";
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $topicId = $_POST['topic_id'];
    $content = trim($_POST['content']);
    $userId = $_SESSION['user_id'];

    // Validate input
    if (empty($content)) {
        echo "<script>alert('Please enter a comment.'); window.history.back();</script>";
        exit();
    }

    // Verify topic exists
    $checkQuery = "SELECT id FROM forumtopics WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $topicId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "<script>alert('Topic not found.'); window.location.href = '../view/profile.php';</script>";
        exit();
    }

    // Add the comment
    $query = "INSERT INTO forumcomments (topic_id, user_id, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $topicId, $userId, $content);

    if ($stmt->execute()) {
        echo "<script>window.location.href = '../view/view_topic.php?topic_id=" . $topicId . "';</script>";
    } else {
        echo "<script>alert('Error adding comment. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    // If accessed directly without POST data
    header("Location: ../view/profile.php");
    exit();
}

$conn->close();
?>
