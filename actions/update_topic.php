<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to update a topic.'); window.location.href = '../view/signup.php';</script>";
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $topicId = $_POST['topic_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['description']);
    $userId = $_SESSION['user_id'];

    // Validate input
    // if (empty($title) || empty($content)) {
    //     echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
    //     exit();
    // }

    // Verify ownership
    $checkQuery = "SELECT user_id FROM forumtopics WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $topicId);
    $stmt->execute();
    $result = $stmt->get_result();
    $topic = $result->fetch_assoc();

    if (!$topic || $topic['user_id'] != $userId) {
        echo "<script>alert('You can only edit your own topics.'); window.location.href = '../view/profile.php';</script>";
        exit();
    }

    // Update the topic
    $updateQuery = "UPDATE forumtopics SET title = ?, description = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssii", $title, $content, $topicId, $userId);

    if ($stmt->execute()) {
        echo "<script>alert('Topic updated successfully!'); window.location.href = '../view/profile.php';</script>";
    } else {
        echo "<script>alert('Error updating topic. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    // If accessed directly without POST data
    header("Location: ../view/profile.php");
    exit();
}

$conn->close();
?>
