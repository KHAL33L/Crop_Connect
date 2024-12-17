<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to create a topic.'); window.location.href = '../view/signup.php';</script>";
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $userId = $_SESSION['user_id'];

    // Validate input
    if (empty($title)) {
        echo "<script>alert('Please fill in the title.'); window.history.back();</script>";
        exit();
    }

    // Prepare SQL statement
    $query = "INSERT INTO forumtopics (user_id, title, description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $userId, $title, $description);

    // Execute the query
    if ($stmt->execute()) {
        echo "<script>alert('Topic created successfully!'); window.location.href = '../view/profile.php';</script>";
    } else {
        echo "<script>alert('Error creating topic. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    // If accessed directly without POST data
    header("Location: ../view/profile.php");
    exit();
}

$conn->close();
?>
