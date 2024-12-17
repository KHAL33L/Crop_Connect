<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to edit a comment.'); window.location.href = '../view/signup.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $commentId = $_POST['comment_id'];
    $topicId = $_POST['topic_id'];
    $content = trim($_POST['content']);
    $userId = $_SESSION['user_id'];

    // Validate input
    if (empty($content)) {
        echo "<script>alert('Comment cannot be empty.'); window.history.back();</script>";
        exit();
    }

    // Verify the comment exists and belongs to the user
    $checkQuery = "SELECT id FROM forumcomments WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $commentId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('You can only edit your own comments.'); window.history.back();</script>";
        exit();
    }

    // Update the comment
    $updateQuery = "UPDATE forumcomments SET content = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $content, $commentId);

    if ($stmt->execute()) {
        echo "<script>window.location.href = '../view/view_topic.php?topic_id=" . $topicId . "';</script>";
    } else {
        echo "<script>alert('Error updating comment. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    header("Location: ../view/forum.php");
    exit();
}

$conn->close();
?>
