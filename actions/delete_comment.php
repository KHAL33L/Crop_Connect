<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to delete a comment.'); window.location.href = '../view/signup.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $commentId = $_POST['comment_id'];
    $topicId = $_POST['topic_id'];
    $userId = $_SESSION['user_id'];

    // Verify the comment exists and belongs to the user
    $checkQuery = "SELECT id FROM forumcomments WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $commentId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('You can only delete your own comments.'); window.history.back();</script>";
        exit();
    }

    // Delete the comment
    $deleteQuery = "DELETE FROM forumcomments WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $commentId);

    if ($stmt->execute()) {
        echo "<script>window.location.href = '../view/view_topic.php?topic_id=" . $topicId . "';</script>";
    } else {
        echo "<script>alert('Error deleting comment. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    header("Location: ../view/forum.php");
    exit();
}

$conn->close();
?>
