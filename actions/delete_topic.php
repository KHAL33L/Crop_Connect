<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to delete a topic.'); window.location.href = '../view/signup.php';</script>";
    exit();
}

// Check if form was submitted with topic_id
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['topic_id'])) {
    $topicId = $_POST['topic_id'];
    $userId = $_SESSION['user_id'];

    // First verify that the topic belongs to the current user
    $checkQuery = "SELECT user_id FROM forumtopics WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $topicId);
    $stmt->execute();
    $result = $stmt->get_result();
    $topic = $result->fetch_assoc();

    if (!$topic || $topic['user_id'] != $userId) {
        echo "<script>alert('You can only delete your own topics.'); window.location.href = '../view/profile.php';</script>";
        exit();
    }

    // Delete associated comments first
    $deleteCommentsQuery = "DELETE FROM forumcomments WHERE topic_id = ?";
    $stmt = $conn->prepare($deleteCommentsQuery);
    $stmt->bind_param("i", $topicId);
    $stmt->execute();

    // Then delete the topic
    $deleteTopicQuery = "DELETE FROM forumtopics WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($deleteTopicQuery);
    $stmt->bind_param("ii", $topicId, $userId);

    if ($stmt->execute()) {
        echo "<script>alert('Topic deleted successfully!'); window.location.href = '../view/profile.php';</script>";
    } else {
        echo "<script>alert('Error deleting topic. Please try again.'); window.location.href = '../view/profile.php';</script>";
    }

    $stmt->close();
} else {
    // If accessed directly without POST data
    header("Location: ../view/profile.php");
    exit();
}

$conn->close();
?>
